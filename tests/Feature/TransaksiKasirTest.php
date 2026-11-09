<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * Backend penyimpanan transaksi kasir (POS) & detail varian.
 *
 * Endpoint: POST /kasir/api/transaksi
 */
class TransaksiKasirTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->d = $this->buatDataChikumi();
        $this->kasir = $this->buatKasir();
    }

    private function kirim(array $body)
    {
        return $this->actingAs($this->kasir)->postJson('/kasir/api/transaksi', $body);
    }

    public function test_transaksi_cash_menyimpan_item_dan_detail_varian(): void
    {
        $res = $this->kirim([
            'payment_method' => 'cash',
            'amount_paid' => 50000,
            'items' => [[
                'product_size_id' => $this->d['regular']->id,
                'quantity' => 2,
                'variant_ids' => [$this->d['mozzarella']->id, $this->d['bbq']->id],
            ]],
        ]);

        $res->assertCreated()
            ->assertJsonPath('data.total', 40000)
            ->assertJsonPath('data.dibayar', 50000)
            ->assertJsonPath('data.kembalian', 10000)
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonCount(1, 'data.item')
            ->assertJsonCount(2, 'data.item.0.varian');

        $this->assertDatabaseHas('pos_transactions', [
            'cashier_id' => $this->kasir->id,
            'total_amount' => 40000,
            'payment_method' => 'cash',
            'amount_paid' => 50000,
            'change_amount' => 10000,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('pos_transaction_items', [
            'product_size_id' => $this->d['regular']->id,
            'unit_price' => 20000,
            'quantity' => 2,
            'subtotal' => 40000,
        ]);
        $this->assertDatabaseHas('pos_item_variants', ['variant_id' => $this->d['mozzarella']->id, 'variant_price' => 3000]);
        $this->assertDatabaseHas('pos_item_variants', ['variant_id' => $this->d['bbq']->id, 'variant_price' => 2000]);

        $this->assertSame(18, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'user_id' => $this->kasir->id,
            'type' => 'out',
            'quantity' => 2,
            'source' => 'pos_transaction',
        ]);
    }

    public function test_dua_baris_ukuran_sama_dijumlah_stoknya(): void
    {
        // 3 x 15.000 + 2 x (15.000 + 3.000) = 81.000
        $this->kirim([
            'payment_method' => 'cash',
            'amount_paid' => 81000,
            'items' => [
                ['product_size_id' => $this->d['regular']->id, 'quantity' => 3],
                ['product_size_id' => $this->d['regular']->id, 'quantity' => 2, 'variant_ids' => [$this->d['mozzarella']->id]],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.total', 81000)
            ->assertJsonCount(2, 'data.item');

        $this->assertSame(15, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 5,
            'source' => 'pos_transaction',
        ]);
    }

    public function test_uang_kurang_ditolak_dan_stok_tidak_berubah(): void
    {
        $this->kirim([
            'payment_method' => 'cash',
            'amount_paid' => 1000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 1]],
        ])->assertStatus(422);

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseCount('pos_transactions', 0);
    }

    public function test_stok_kurang_ditolak(): void
    {
        $this->kirim([
            'payment_method' => 'cash',
            'amount_paid' => 1000000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 21]],
        ])->assertStatus(422);

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseCount('pos_transactions', 0);
    }

    public function test_pilihan_varian_tidak_valid_ditolak(): void
    {
        $this->kirim([
            'payment_method' => 'cash',
            'amount_paid' => 100000,
            'items' => [[
                'product_size_id' => $this->d['regular']->id,
                'quantity' => 1,
                'variant_ids' => [$this->d['mozzarella']->id, $this->d['cokelat']->id],
            ]],
        ])->assertStatus(422)->assertJsonValidationErrors('variant_ids');

        $this->assertDatabaseCount('pos_transactions', 0);
    }

    public function test_non_cash_kembalian_nol(): void
    {
        $this->kirim([
            'payment_method' => 'non_cash',
            'amount_paid' => 15000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 1]],
        ])->assertCreated()
            ->assertJsonPath('data.kembalian', 0)
            ->assertJsonPath('data.metode_pembayaran', 'non_cash');
    }

    public function test_pelanggan_tidak_boleh_menyimpan_transaksi(): void
    {
        $this->actingAs($this->buatPelanggan())
            ->postJson('/kasir/api/transaksi', [])
            ->assertForbidden();
    }
}
