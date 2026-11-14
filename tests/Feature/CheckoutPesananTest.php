<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-97] Backend checkout & pembuatan pesanan terhubung akun.
 *
 * Menguji POST /customer/api/pesanan/checkout (pesanan tersimpan atas nama
 * pelanggan login), GET /customer/api/pesanan (riwayat per akun), dan
 * PATCH /customer/api/pesanan/{id}/batal (hanya pesanan sendiri).
 */
class CheckoutPesananTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->pelanggan = $this->buatPelanggan();
    }

    private function buatSkemaPesanan(): void
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

    // ---------- Pembuatan pesanan terhubung akun ----------
    public function test_checkout_courier_membuat_pesanan_atas_nama_akun(): void
    {
        $alamat = $this->alamat($this->pelanggan);
        $this->isiKeranjang(2, [$this->d['mozzarella']->id]);

        $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
            'payment_method' => 'qris',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'unpaid');

        $order = Order::firstOrFail();
        $this->assertSame($this->pelanggan->id, $order->customer_id);
        $this->assertSame('courier', $order->delivery_method);
        $this->assertSame('qris', $order->payment_type);
        $this->assertSame('unpaid', $order->status);
        $this->assertEquals(36000, (float) $order->subtotal);   // (15000 + 3000) × 2
        $this->assertEquals(5000, (float) $order->shipping_cost);
        $this->assertEquals(41000, (float) $order->grand_total);
        $this->assertStringContainsString($alamat->detail, $order->shipping_address);

        $item = $order->items()->firstOrFail();
        $this->assertEquals(18000, (float) $item->unit_price);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(36000, (float) $item->subtotal);
        $this->assertEquals(3000, (float) $item->variants()->firstOrFail()->pivot->variant_price);

        $this->assertEquals(18, $this->d['regular']->fresh()->stock);
        $this->assertEquals(0, Cart::where('customer_id', $this->pelanggan->id)->count());

        $history = StockHistory::firstOrFail();
        $this->assertSame('out', $history->type);
        $this->assertSame('online_order', $history->source);
        $this->assertSame($this->pelanggan->id, $history->user_id);
        $this->assertEquals(2, $history->quantity);
    }

    public function test_checkout_pickup_tanpa_alamat_dan_tanpa_ongkir(): void
    {
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'pickup'])
            ->assertCreated();

        $order = Order::firstOrFail();
        $this->assertSame($this->pelanggan->id, $order->customer_id);
        $this->assertNull($order->shipping_address);
        $this->assertEquals(0, (float) $order->shipping_cost);
        $this->assertEquals(15000, (float) $order->grand_total);
        $this->assertNull($order->payment_type);
    }

    public function test_checkout_memakai_alamat_yang_dipilih(): void
    {
        $lama = $this->alamat($this->pelanggan, ['label' => 'Lama', 'detail' => 'Jl. Lama No. 1']);
        $this->alamat($this->pelanggan, ['label' => 'Baru', 'detail' => 'Jl. Baru No. 2']);
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier', 'address_id' => $lama->id])
            ->assertCreated();

        $this->assertStringContainsString('Jl. Lama No. 1', Order::firstOrFail()->shipping_address);
    }

    public function test_checkout_courier_tanpa_alamat_ditolak(): void
    {
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier'])
            ->assertStatus(422);

        $this->assertSame(0, Order::count());
        $this->assertEquals(20, $this->d['regular']->fresh()->stock);
    }

    public function test_checkout_alamat_milik_akun_lain_ditolak(): void
    {
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain@gmail.com',
            'phone_number' => '081200000010',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
        $alamatLain = $this->alamat($lain);
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier', 'address_id' => $alamatLain->id])
            ->assertStatus(422);

        $this->assertSame(0, Order::count());
        $this->assertEquals(1, Cart::where('customer_id', $this->pelanggan->id)->count());
        $this->assertEquals(20, $this->d['regular']->fresh()->stock);
    }

    public function test_checkout_keranjang_kosong_ditolak(): void
    {
        $this->alamat($this->pelanggan);

        $this->checkout(['delivery_method' => 'courier'])
            ->assertStatus(422);

        $this->assertSame(0, Order::count());
    }

    public function test_checkout_stok_kurang_ditolak_tanpa_mengubah_data(): void
    {
        $this->isiKeranjang(2);
        $this->d['regular']->update(['stock' => 1]);

        $this->checkout(['delivery_method' => 'pickup'])
            ->assertStatus(422);

        $this->assertSame(0, Order::count());
        $this->assertEquals(1, $this->d['regular']->fresh()->stock);
        $this->assertEquals(1, Cart::where('customer_id', $this->pelanggan->id)->count());
        $this->assertSame(0, StockHistory::count());
    }

    public function test_checkout_wajib_login(): void
    {
        $this->postJson('/customer/api/pesanan/checkout', ['delivery_method' => 'pickup'])
            ->assertUnauthorized();
    }

    // ---------- Riwayat pesanan per akun ----------
    public function test_riwayat_hanya_menampilkan_pesanan_akun_sendiri(): void
    {
        $milikSaya = Order::create([
            'order_number' => 'CHKM-SAYA',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 15000,
            'shipping_cost' => 0,
            'grand_total' => 15000,
            'status' => 'unpaid',
        ]);
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain2@gmail.com',
            'phone_number' => '081200000011',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
        Order::create([
            'order_number' => 'CHKM-LAIN',
            'customer_id' => $lain->id,
            'delivery_method' => 'pickup',
            'subtotal' => 22000,
            'shipping_cost' => 0,
            'grand_total' => 22000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->pelanggan)->getJson('/customer/api/pesanan')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.pesanan.0.id', $milikSaya->id)
            ->assertJsonPath('data.pesanan.0.nomor', 'CHKM-SAYA');
    }

    public function test_riwayat_menyertakan_metode_pembayaran(): void
    {
        $this->alamat($this->pelanggan);
        $this->isiKeranjang(1);
        $this->checkout(['delivery_method' => 'pickup', 'payment_method' => 'va'])->assertCreated();

        $this->actingAs($this->pelanggan)->getJson('/customer/api/pesanan')
            ->assertOk()
            ->assertJsonPath('data.pesanan.0.pembayaran', 'va');
    }

    // ---------- Pembatalan pesanan ----------
    public function test_batal_pesanan_sendiri_mengembalikan_stok(): void
    {
        $this->isiKeranjang(2);
        $this->checkout(['delivery_method' => 'pickup'])->assertCreated();
        $order = Order::firstOrFail();
        $this->assertEquals(18, $this->d['regular']->fresh()->stock);

        $this->actingAs($this->pelanggan)->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertOk();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertEquals(20, $this->d['regular']->fresh()->stock);
        $this->assertSame(1, StockHistory::where('type', 'in')->count());
    }

    public function test_batal_pesanan_akun_lain_ditolak(): void
    {
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain3@gmail.com',
            'phone_number' => '081200000012',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
        $order = Order::create([
            'order_number' => 'CHKM-LAIN-2',
            'customer_id' => $lain->id,
            'delivery_method' => 'pickup',
            'subtotal' => 15000,
            'shipping_cost' => 0,
            'grand_total' => 15000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->pelanggan)->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertForbidden();

        $this->assertSame('unpaid', $order->fresh()->status);
    }

    public function test_batal_pesanan_sudah_dibayar_ditolak(): void
    {
        $order = Order::create([
            'order_number' => 'CHKM-LUNAS',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 15000,
            'shipping_cost' => 0,
            'grand_total' => 15000,
            'status' => 'completed',
        ]);

        $this->actingAs($this->pelanggan)->patchJson("/customer/api/pesanan/{$order->id}/batal")
            ->assertStatus(422);

        $this->assertSame('completed', $order->fresh()->status);
    }
}
