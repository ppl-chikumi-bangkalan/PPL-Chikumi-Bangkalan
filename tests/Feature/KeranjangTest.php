<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\User;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-97] Testing keranjang — PPL-21 Keranjang Belanja (backend CHIK-95).
 *
 * Menguji endpoint keranjang (produk terkonfigurasi: ukuran + varian):
 *   GET    /customer/api/keranjang
 *   POST   /customer/api/keranjang
 *   PUT    /customer/api/keranjang/{keranjang}
 *   DELETE /customer/api/keranjang/{keranjang}
 */
class KeranjangTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->d = $this->buatDataChikumi();
        $this->pelanggan = $this->buatPelanggan();
    }

    private function tambah(array $body, ?User $user = null)
    {
        return $this->actingAs($user ?? $this->pelanggan)->postJson('/customer/api/keranjang', $body);
    }

    private function lihat(?User $user = null)
    {
        return $this->actingAs($user ?? $this->pelanggan)->getJson('/customer/api/keranjang');
    }

    private function ubah(int $id, array $body, ?User $user = null)
    {
        return $this->actingAs($user ?? $this->pelanggan)->putJson("/customer/api/keranjang/{$id}", $body);
    }

    private function hapus(int $id, ?User $user = null)
    {
        return $this->actingAs($user ?? $this->pelanggan)->deleteJson("/customer/api/keranjang/{$id}");
    }

    private function pelangganLain(): User
    {
        return User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain.keranjang@gmail.com',
            'phone_number' => '081200000020',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
    }

    // ---------- Menambah ke keranjang (produk terkonfigurasi) ----------

    public function test_tambah_item_dengan_ukuran_dan_varian(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 2,
            'variant_ids' => [$this->d['mozzarella']->id, $this->d['bbq']->id],
        ])->assertCreated();

        $cart = Cart::firstOrFail();
        $this->assertSame($this->pelanggan->id, $cart->customer_id);
        $this->assertSame(2, $cart->quantity);
        $this->assertSame(
            [$this->d['mozzarella']->id, $this->d['bbq']->id],
            $cart->variants->pluck('id')->sort()->values()->all()
        );
    }

    public function test_item_identik_ditumpuk_tidak_dobel(): void
    {
        $body = [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 2,
            'variant_ids' => [$this->d['mozzarella']->id],
        ];
        $this->tambah($body)->assertCreated();
        $this->tambah($body)->assertCreated();

        $this->assertSame(1, Cart::count());
        $this->assertSame(4, Cart::firstOrFail()->quantity);
    }

    public function test_ukuran_sama_varian_berbeda_menjadi_dua_baris(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
            'variant_ids' => [$this->d['mozzarella']->id],
        ])->assertCreated();
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
            'variant_ids' => [$this->d['cokelat']->id],
        ])->assertCreated();

        $this->assertSame(2, Cart::count());
    }

    public function test_tambah_melebihi_stok_ditolak(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 21,
            'variant_ids' => [],
        ])->assertStatus(422);

        $this->assertSame(0, Cart::count());
    }

    public function test_tambah_varian_tidak_valid_ditolak(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
            'variant_ids' => [$this->d['mozzarella']->id, $this->d['cokelat']->id],
        ])->assertStatus(422)->assertJsonValidationErrors('variant_ids');

        $this->assertSame(0, Cart::count());
    }

    public function test_tambah_produk_nonaktif_ditolak(): void
    {
        $this->d['produk']->update(['is_active' => false]);

        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
            'variant_ids' => [],
        ])->assertStatus(422);

        $this->assertSame(0, Cart::count());
    }

    // ---------- Melihat keranjang ----------

    public function test_lihat_keranjang_menghitung_total_dan_rincian(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['large']->id,
            'quantity' => 2,
            'variant_ids' => [$this->d['mozzarella']->id],
        ])->assertCreated();
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
            'variant_ids' => [],
        ])->assertCreated();

        $this->lihat()
            ->assertOk()
            ->assertJsonPath('data.total_item', 3)
            ->assertJsonPath('data.subtotal', 65000) // (22000+3000)*2 + 15000
            ->assertJsonCount(2, 'data.item');
    }

    public function test_lihat_keranjang_menampilkan_harga_satuan_varian_dan_stok(): void
    {
        $this->tambah([
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 2,
            'variant_ids' => [$this->d['mozzarella']->id],
        ])->assertCreated();

        $item = $this->lihat()->assertOk()->json('data.item.0');

        $this->assertSame('Regular', $item['ukuran']);
        $this->assertEquals(18000, $item['harga_satuan']);   // 15000 + 3000
        $this->assertEquals(36000, $item['subtotal']);
        $this->assertTrue($item['stok_cukup']);
        $this->assertCount(1, $item['varian']);
        $this->assertSame('Mozzarella Cheese', $item['varian'][0]['nama']);
    }

    public function test_lihat_keranjang_kosong(): void
    {
        $this->lihat()
            ->assertOk()
            ->assertJsonPath('data.total_item', 0)
            ->assertJsonPath('data.subtotal', 0)
            ->assertJsonCount(0, 'data.item');
    }

    // ---------- Mengubah jumlah ----------

    public function test_ubah_jumlah_valid(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->ubah($cart->id, ['quantity' => 5])
            ->assertOk()
            ->assertJsonPath('message', 'Jumlah diperbarui.');

        $this->assertSame(5, $cart->fresh()->quantity);
    }

    public function test_ubah_jumlah_melebihi_stok_ditolak(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->ubah($cart->id, ['quantity' => 21])->assertStatus(422);

        $this->assertSame(1, $cart->fresh()->quantity);
    }

    public function test_ubah_jumlah_kurang_dari_satu_ditolak(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->ubah($cart->id, ['quantity' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    }

    public function test_ubah_keranjang_milik_akun_lain_ditolak(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->ubah($cart->id, ['quantity' => 3], $this->pelangganLain())
            ->assertForbidden();

        $this->assertSame(1, $cart->fresh()->quantity);
    }

    // ---------- Menghapus item ----------

    public function test_hapus_item(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->hapus($cart->id)->assertOk();

        $this->assertSame(0, Cart::count());
    }

    public function test_hapus_keranjang_milik_akun_lain_ditolak(): void
    {
        $this->tambah(['product_size_id' => $this->d['regular']->id, 'quantity' => 1, 'variant_ids' => []]);
        $cart = Cart::firstOrFail();

        $this->hapus($cart->id, $this->pelangganLain())->assertForbidden();

        $this->assertSame(1, Cart::count());
    }

    // ---------- Autentikasi ----------

    public function test_lihat_keranjang_tanpa_login_ditolak(): void
    {
        $this->getJson('/customer/api/keranjang')->assertUnauthorized();
    }

    public function test_tambah_keranjang_tanpa_login_ditolak(): void
    {
        $this->postJson('/customer/api/keranjang', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => 1,
        ])->assertUnauthorized();
    }
}
