<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ProductSize;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-118] Uji penerimaan PPL-10 (CHIK-39) — pengurangan stok otomatis
 * pada transaksi lunas & pesanan dibayar, plus riwayat stok.
 *
 * Alur yang diuji pada commit main:
 * - POS   : POST /kasir/api/transaksi (TransactionController::store)
 * - Online: POST /customer/api/pesanan/checkout, POST /payment/callback,
 *           GET /customer/api/pesanan/{id}/status-pembayaran,
 *           PATCH /customer/api/pesanan/{id}/batal
 * - Riwayat: GET /owner/stok (komponen components/owner/riwayat-stok)
 */
class PenguranganStokTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $kasir;

    private User $pelanggan;

    private User $owner;

    private int $urutan = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();
        $this->owner = User::create([
            'name' => 'Owner Stok',
            'email' => 'owner.stok@gmail.com',
            'phone_number' => '081200000077',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);

        config([
            'services.duitku.merchant_code' => 'D0001',
            'services.duitku.api_key' => 'rahasia-stok',
            'services.duitku.is_production' => false,
        ]);
    }

    private function buatSkemaPesanan(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id');
            $table->string('delivery_method');
            $table->text('shipping_address')->nullable();
            $table->unsignedBigInteger('shipping_origin_id')->nullable();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->timestamp('shipping_confirmed_at')->nullable();
            $table->unsignedBigInteger('shipping_confirmed_by')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->string('status')->default('unpaid');
            $table->string('snap_token')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('product_size_id');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id');
            $table->foreignId('variant_id');
            $table->decimal('variant_price', 12, 2);
        });
    }

    // ---------- Helper POS ----------

    private function jualKasir(array $body)
    {
        return $this->actingAs($this->kasir)->postJson('/kasir/api/transaksi', $body);
    }

    private function stok(int $sizeId): int
    {
        return (int) ProductSize::find($sizeId)->stock;
    }

    // ---------- Helper pesanan online ----------

    private function pesanOnline(int $quantity, string $metode = 'qris'): Order
    {
        $this->actingAs($this->pelanggan)->postJson('/customer/api/keranjang', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => $quantity,
            'variant_ids' => [],
        ])->assertCreated();

        $res = $this->actingAs($this->pelanggan)->postJson('/customer/api/pesanan/checkout', [
            'delivery_method' => 'pickup',
            'payment_method' => $metode,
        ])->assertCreated();

        return Order::findOrFail($res->json('data.id'));
    }

    private function signature(string $raw): string
    {
        return hash_hmac('sha256', $raw, 'rahasia-stok');
    }

    private function callbackSukses(Order $order)
    {
        $amount = (string) (int) $order->grand_total;

        return $this->post('/payment/callback', [
            'merchantCode' => 'D0001',
            'amount' => $amount,
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'REF-STOK',
            'signature' => $this->signature('D0001'.$amount.$order->order_number),
        ]);
    }

    // ================= A. POS: transaksi lunas =================

    public function test_transaksi_tunai_mengurangi_stok_dan_mencatat_riwayat(): void
    {
        $res = $this->jualKasir([
            'payment_method' => 'cash',
            'amount_paid' => 40000,
            'items' => [[
                'product_size_id' => $this->d['regular']->id,
                'quantity' => 2,
                'variant_ids' => [$this->d['mozzarella']->id, $this->d['bbq']->id],
            ]],
        ])->assertCreated();

        $id = $res->json('data.id');

        $this->assertSame(18, $this->stok($this->d['regular']->id));
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'user_id' => $this->kasir->id,
            'type' => 'out',
            'quantity' => 2,
            'source' => 'pos_transaction',
            'reference_id' => $id,
        ]);
    }

    public function test_transaksi_non_tunai_mengurangi_stok(): void
    {
        $this->jualKasir([
            'payment_method' => 'non_cash',
            'amount_paid' => 22000,
            'items' => [['product_size_id' => $this->d['large']->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->assertSame(2, $this->stok($this->d['large']->id));
        $this->assertSame(1, StockHistory::where('source', 'pos_transaction')->count());
    }

    public function test_stok_kurang_tidak_mengurangi_stok_dan_tanpa_riwayat(): void
    {
        $this->jualKasir([
            'payment_method' => 'cash',
            'amount_paid' => 500000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 21]],
        ])->assertStatus(422);

        $this->assertSame(20, $this->stok($this->d['regular']->id));
        $this->assertDatabaseCount('stock_histories', 0);
        $this->assertDatabaseCount('pos_transactions', 0);
    }

    public function test_dua_baris_ukuran_sama_digabung_jadi_satu_riwayat(): void
    {
        $this->jualKasir([
            'payment_method' => 'cash',
            'amount_paid' => 81000,
            'items' => [
                ['product_size_id' => $this->d['regular']->id, 'quantity' => 3],
                ['product_size_id' => $this->d['regular']->id, 'quantity' => 2, 'variant_ids' => [$this->d['mozzarella']->id]],
            ],
        ])->assertCreated();

        $this->assertSame(15, $this->stok($this->d['regular']->id));
        $this->assertSame(1, StockHistory::where('source', 'pos_transaction')->count());
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 5,
        ]);
    }

    // ================= B. Pesanan online: dipotong saat checkout =================

    public function test_checkout_online_mengurangi_stok_saat_pesanan_belum_dibayar(): void
    {
        $order = $this->pesanOnline(2);

        // Perilaku sekarang: stok sudah dipotong padahal pesanan masih unpaid.
        $this->assertSame('unpaid', $order->fresh()->status);
        $this->assertSame(18, $this->stok($this->d['regular']->id));

        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'user_id' => $this->pelanggan->id,
            'type' => 'out',
            'quantity' => 2,
            'source' => 'online_order',
            'reference_id' => $order->id,
        ]);
    }

    public function test_pembayaran_sukses_tidak_memotong_stok_dua_kali(): void
    {
        $order = $this->pesanOnline(2);
        $this->assertSame(18, $this->stok($this->d['regular']->id));

        $this->callbackSukses($order)->assertOk();

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);

        // Stok tetap 18: pemotongan hanya terjadi sekali (saat checkout).
        $this->assertSame(18, $this->stok($this->d['regular']->id));
        $this->assertSame(1, StockHistory::where('source', 'online_order')->count());
    }

    public function test_pembatalan_pesanan_mengembalikan_stok_dan_mencatat_riwayat_masuk(): void
    {
        $order = $this->pesanOnline(2);
        $this->assertSame(18, $this->stok($this->d['regular']->id));

        $this->actingAs($this->pelanggan)
            ->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertOk();

        $this->assertSame(20, $this->stok($this->d['regular']->id));
        $this->assertSame('cancelled', $order->fresh()->status);

        $masuk = StockHistory::where('type', 'in')->firstOrFail();
        $this->assertSame('online_order', $masuk->source);
        $this->assertSame(2, $masuk->quantity);
        $this->assertSame($order->id, $masuk->reference_id);
        $this->assertSame('Pembatalan pesanan', $masuk->notes);
    }

    public function test_pembayaran_kedaluwarsa_mengembalikan_stok_hanya_sekali(): void
    {
        $order = $this->pesanOnline(2);
        $order->update(['snap_token' => 'REF-EXP']);

        Http::fake([
            'sandbox.duitku.com/webapi/api/merchant/transactionStatus' => Http::response([
                'statusCode' => '02',
                'reference' => 'REF-EXP',
            ], 200),
        ]);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame(20, $this->stok($this->d['regular']->id));

        // Polling berikutnya tidak menambah stok lagi.
        Cache::forget('duitku:cek:'.$order->order_number);
        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk();

        $this->assertSame(20, $this->stok($this->d['regular']->id));
        $this->assertSame(1, StockHistory::where('type', 'in')->count());
    }

    public function test_batal_manual_lalu_callback_tidak_mengembalikan_stok_dua_kali(): void
    {
        $order = $this->pesanOnline(2);

        $this->actingAs($this->pelanggan)
            ->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertOk();

        $this->assertSame(20, $this->stok($this->d['regular']->id));

        // Callback sukses setelah pesanan dibatalkan: hanya dicatat, stok tidak berubah.
        $this->callbackSukses($order)->assertOk();

        $this->assertSame(20, $this->stok($this->d['regular']->id));
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(1, StockHistory::where('type', 'in')->count());
    }

    // ================= C. Riwayat stok di halaman owner =================

    public function test_riwayat_stok_menampilkan_tiga_sumber_dengan_label(): void
    {
        $this->jualKasir([
            'payment_method' => 'cash',
            'amount_paid' => 15000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->pesanOnline(1);

        $this->actingAs($this->owner)->patchJson("/owner/api/ukuran/{$this->d['large']->id}/stok", [
            'type' => 'in',
            'quantity' => 5,
            'notes' => 'Restock pagi',
        ])->assertOk();

        $this->actingAs($this->owner)->get('/owner/stok')
            ->assertOk()
            ->assertSee('Riwayat Stok')
            ->assertSee('Transaksi kasir')
            ->assertSee('Pesanan online')
            ->assertSee('Tambah stok')
            ->assertSee('Restock pagi');
    }

    public function test_riwayat_stok_hanya_menampilkan_enam_baris_terakhir(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            StockHistory::create([
                'product_size_id' => $this->d['regular']->id,
                'user_id' => $this->owner->id,
                'type' => 'in',
                'quantity' => $i,
                'source' => 'manual_restock',
                'notes' => 'Batch '.$i,
            ]);
        }

        $res = $this->actingAs($this->owner)->get('/owner/stok')->assertOk();

        $this->assertStringContainsString('Menampilkan 6 dari 8', $res->getContent());
        $this->assertStringContainsString('Batch 8', $res->getContent());
        $this->assertStringNotContainsString('Batch 2', $res->getContent());
    }

    public function test_pelanggan_tidak_bisa_melihat_halaman_stok_owner(): void
    {
        $this->actingAs($this->pelanggan)
            ->get('/owner/stok')
            ->assertRedirect('/');
    }

    // ================= D. Konsistensi: total riwayat = perubahan stok =================

    public function test_total_riwayat_sama_dengan_perubahan_stok(): void
    {
        $awal = $this->stok($this->d['regular']->id);

        $this->jualKasir([
            'payment_method' => 'cash',
            'amount_paid' => 30000,
            'items' => [['product_size_id' => $this->d['regular']->id, 'quantity' => 2]],
        ])->assertCreated();

        $order = $this->pesanOnline(1);
        $this->actingAs($this->pelanggan)
            ->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertOk();

        $masuk = (int) StockHistory::where('type', 'in')->sum('quantity');
        $keluar = (int) StockHistory::where('type', 'out')->sum('quantity');

        $this->assertSame($awal - $keluar + $masuk, $this->stok($this->d['regular']->id));
        $this->assertSame(3, $keluar);   // 2 pcs kasir + 1 pcs checkout online
        $this->assertSame(1, $masuk);    // 1 pcs batal
    }
}
