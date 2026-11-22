<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-100] Testing pemesanan online — PPL-23 (backend CHIK-98, frontend CHIK-99).
 *
 * Melengkapi CheckoutPesananTest (alur inti checkout) dengan:
 *   - endpoint metode penerimaan (CHIK-101)
 *   - validasi delivery_method & payment_method
 *   - checkout tanpa payment_method (nullable)
 *   - CRUD alamat + isolasi antar akun
 *   - render halaman checkout
 */
class PemesananOnlineTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPemesanan();
        $this->d = $this->buatDataChikumi();
        $this->pelanggan = $this->buatPelanggan();
    }

    private function buatSkemaPemesanan(): void
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('label', 50)->nullable();
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->text('detail');
            $table->string('city', 100);
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id');
            $table->string('delivery_method');
            $table->text('shipping_address')->nullable();
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

    private function isiKeranjang(int $quantity = 1, array $variantIds = []): void
    {
        $this->actingAs($this->pelanggan)->postJson('/customer/api/keranjang', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => $quantity,
            'variant_ids' => $variantIds,
        ])->assertCreated();
    }

    private function checkout(array $payload)
    {
        return $this->actingAs($this->pelanggan)->postJson('/customer/api/pesanan/checkout', $payload);
    }

    private function alamat(User $user, array $override = []): Address
    {
        return Address::create(array_merge([
            'customer_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Penerima Uji',
            'phone' => '081200000009',
            'detail' => 'Jl. Uji No. 1',
            'city' => 'Bangkalan Kota',
            'postal_code' => '69112',
        ], $override));
    }

    // ---------- Metode penerimaan (CHIK-101) ----------

    public function test_metode_penerimaan_menyediakan_diantar_dan_ambil(): void
    {
        $this->actingAs($this->pelanggan)->getJson('/customer/api/metode-penerimaan')
            ->assertOk()
            ->assertJsonPath('data.metode.0.kode', 'courier')
            ->assertJsonPath('data.metode.0.ongkir', 5000)
            ->assertJsonPath('data.metode.0.butuh_alamat', true)
            ->assertJsonPath('data.metode.1.kode', 'pickup')
            ->assertJsonPath('data.metode.1.ongkir', 0)
            ->assertJsonPath('data.metode.1.butuh_alamat', false);
    }

    // ---------- Validasi checkout ----------

    public function test_delivery_method_tidak_valid_ditolak(): void
    {
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'drone'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('delivery_method');

        $this->assertSame(0, Order::count());
        $this->assertEquals(1, Cart::count());
    }

    public function test_payment_method_tidak_valid_ditolak(): void
    {
        $this->alamat($this->pelanggan);
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier', 'payment_method' => 'cash'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('payment_method');

        $this->assertSame(0, Order::count());
    }

    public function test_checkout_courier_tanpa_payment_method_diperbolehkan(): void
    {
        $this->alamat($this->pelanggan);
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier'])
            ->assertCreated();

        $this->assertNull(Order::firstOrFail()->payment_type);
    }

    public function test_checkout_courier_total_termasuk_ongkir(): void
    {
        $this->alamat($this->pelanggan);
        $this->isiKeranjang(1); // Regular Rp 15.000

        $this->checkout(['delivery_method' => 'courier', 'payment_method' => 'qris'])
            ->assertCreated()
            ->assertJsonPath('data.total', 20000); // 15000 + 5000 ongkir

        $this->assertEquals(15000, (float) Order::firstOrFail()->subtotal);
        $this->assertEquals(5000, (float) Order::firstOrFail()->shipping_cost);
        $this->assertEquals(20000, (float) Order::firstOrFail()->grand_total);
    }

    // ---------- CRUD alamat + isolasi ----------

    public function test_simpan_alamat(): void
    {
        $this->actingAs($this->pelanggan)->postJson('/customer/api/alamat', [
            'label' => 'Kantor',
            'recipient_name' => 'Budi',
            'phone' => '081200000001',
            'detail' => 'Jl. Kantor No. 2',
            'city' => 'Bangkalan',
            'postal_code' => '69112',
        ])->assertCreated()
            ->assertJsonPath('data.penerima', 'Budi')
            ->assertJsonPath('data.kota', 'Bangkalan');

        $this->assertDatabaseHas('delivery_addresses', ['recipient_name' => 'Budi']);
    }

    public function test_update_alamat_sendiri(): void
    {
        $a = $this->alamat($this->pelanggan);

        $this->actingAs($this->pelanggan)->putJson("/customer/api/alamat/{$a->id}", [
            'label' => 'Rumah',
            'recipient_name' => 'Budi Baru',
            'phone' => '081200000001',
            'detail' => 'Jl. Baru No. 3',
            'city' => 'Bangkalan',
        ])->assertOk();

        $this->assertSame('Budi Baru', $a->fresh()->recipient_name);
    }

    public function test_update_alamat_akun_lain_ditolak(): void
    {
        $a = $this->alamat($this->pelanggan);
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain.alamat@gmail.com',
            'phone_number' => '081200000021',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);

        $this->actingAs($lain)->putJson("/customer/api/alamat/{$a->id}", [
            'label' => 'Rumah',
            'recipient_name' => 'Hacker',
            'phone' => '081200000001',
            'detail' => 'Jl. Hacker',
            'city' => 'Bangkalan',
        ])->assertForbidden();
    }

    public function test_hapus_alamat_akun_lain_ditolak(): void
    {
        $a = $this->alamat($this->pelanggan);
        $lain = User::create([
            'name' => 'Pelanggan Lain 2',
            'email' => 'lain.alamat2@gmail.com',
            'phone_number' => '081200000022',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);

        $this->actingAs($lain)->deleteJson("/customer/api/alamat/{$a->id}")->assertForbidden();

        $this->assertDatabaseHas('delivery_addresses', ['id' => $a->id]);
    }

    // ---------- Render halaman checkout ----------

    public function test_halaman_checkout_merender_form(): void
    {
        $this->withoutVite();

        $this->actingAs($this->pelanggan)->get('/customer/checkout')
            ->assertOk()
            ->assertSee('Cara Menerima Pesanan')
            ->assertSee('Ringkasan Pembayaran')
            ->assertSee('Bayar Sekarang');
    }
}
