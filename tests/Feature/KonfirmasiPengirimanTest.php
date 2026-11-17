<?php

namespace Tests\Feature;

use App\Models\Courier;
use App\Models\Order;
use App\Models\ShippingOrigin;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * Konfirmasi titik asal pengiriman & kurir oleh owner.
 *
 * Menguji master titik asal/kurir dan PATCH /owner/api/pesanan/{id}/konfirmasi-pengiriman.
 */
class KonfirmasiPengirimanTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private User $owner;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPengiriman();

        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.uji@gmail.com',
            'phone_number' => '081200000003',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
        $this->pelanggan = $this->buatPelanggan();
    }

    private function buatSkemaPengiriman(): void
    {
        Schema::create('shipping_origins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('address');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id');
            $table->string('delivery_method');
            $table->text('shipping_address')->nullable();
            $table->foreignId('shipping_origin_id')->nullable();
            $table->foreignId('courier_id')->nullable();
            $table->timestamp('shipping_confirmed_at')->nullable();
            $table->foreignId('shipping_confirmed_by')->nullable();
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
    }

    private function buatPesanan(array $override = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'CHKM-UJI-'.uniqid(),
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'courier',
            'shipping_address' => 'Jl. Uji No. 1, Bangkalan',
            'subtotal' => 15000,
            'shipping_cost' => 5000,
            'grand_total' => 20000,
            'status' => 'unpaid',
        ], $override));
    }

    private function buatTitikAsal(array $override = []): ShippingOrigin
    {
        return ShippingOrigin::create(array_merge([
            'name' => 'Dapur Chikumi',
            'address' => 'Jl. Raya Bangkalan No. 1',
            'is_active' => true,
        ], $override));
    }

    private function buatKurir(array $override = []): Courier
    {
        return Courier::create(array_merge([
            'name' => 'Kurir Toko',
            'phone' => '081234567892',
            'is_active' => true,
        ], $override));
    }

    private function konfirmasi(Order $order, array $payload)
    {
        return $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", $payload);
    }

    // ---------- Master titik asal & kurir ----------
    public function test_owner_dapat_menambah_dan_mengubah_titik_asal(): void
    {
        $this->actingAs($this->owner)
            ->postJson('/owner/api/titik-asal', [
                'name' => 'Dapur Chikumi',
                'address' => 'Jl. Raya Bangkalan No. 1',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Dapur Chikumi');

        $origin = ShippingOrigin::firstOrFail();

        $this->actingAs($this->owner)
            ->putJson("/owner/api/titik-asal/{$origin->id}", [
                'name' => 'Dapur Chikumi Baru',
                'address' => 'Jl. Raya Bangkalan No. 2',
                'is_active' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Dapur Chikumi Baru')
            ->assertJsonPath('data.is_active', false);

        $this->actingAs($this->owner)
            ->getJson('/owner/api/titik-asal')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_owner_dapat_menambah_dan_mengubah_kurir(): void
    {
        $this->actingAs($this->owner)
            ->postJson('/owner/api/kurir', [
                'name' => 'Kurir Toko',
                'phone' => '081234567892',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Kurir Toko');

        $courier = Courier::firstOrFail();

        $this->actingAs($this->owner)
            ->putJson("/owner/api/kurir/{$courier->id}", [
                'name' => 'Kurir Toko Baru',
                'phone' => '081234567899',
                'is_active' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Kurir Toko Baru')
            ->assertJsonPath('data.is_active', false);

        $this->actingAs($this->owner)
            ->getJson('/owner/api/kurir')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_pelanggan_tidak_boleh_mengakses_konfirmasi_pengiriman(): void
    {
        $this->actingAs($this->pelanggan)
            ->getJson('/owner/api/pesanan')
            ->assertForbidden();

        $order = $this->buatPesanan();

        $this->actingAs($this->pelanggan)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $this->buatTitikAsal()->id,
                'courier_id' => $this->buatKurir()->id,
            ])
            ->assertForbidden();

        $this->assertNull($order->fresh()->shipping_confirmed_at);
    }

    // ---------- Konfirmasi pengiriman ----------
    public function test_owner_mengonfirmasi_titik_asal_dan_kurir_pesanan_courier(): void
    {
        $origin = $this->buatTitikAsal();
        $courier = $this->buatKurir();
        $order = $this->buatPesanan();

        $this->konfirmasi($order, [
            'shipping_origin_id' => $origin->id,
            'courier_id' => $courier->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.titik_asal', $origin->name)
            ->assertJsonPath('data.kurir', $courier->name);

        $order->refresh();
        $this->assertSame($origin->id, $order->shipping_origin_id);
        $this->assertSame($courier->id, $order->courier_id);
        $this->assertSame($this->owner->id, $order->shipping_confirmed_by);
        $this->assertNotNull($order->shipping_confirmed_at);
        $this->assertSame('processing', $order->status);
    }

    public function test_konfirmasi_pesanan_pickup_ditolak(): void
    {
        $order = $this->buatPesanan(['delivery_method' => 'pickup', 'shipping_cost' => 0, 'grand_total' => 15000]);

        $this->konfirmasi($order, [
            'shipping_origin_id' => $this->buatTitikAsal()->id,
            'courier_id' => $this->buatKurir()->id,
        ])->assertStatus(422);

        $this->assertNull($order->fresh()->shipping_confirmed_at);
        $this->assertSame('unpaid', $order->fresh()->status);
    }

    public function test_titik_asal_atau_kurir_tidak_aktif_ditolak(): void
    {
        $order = $this->buatPesanan();
        $nonaktif = $this->buatTitikAsal(['is_active' => false]);
        $courier = $this->buatKurir();

        $this->konfirmasi($order, [
            'shipping_origin_id' => $nonaktif->id,
            'courier_id' => $courier->id,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('shipping_origin_id');

        $this->konfirmasi($order, [
            'shipping_origin_id' => $this->buatTitikAsal()->id,
            'courier_id' => $this->buatKurir(['is_active' => false])->id,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('courier_id');

        $this->assertNull($order->fresh()->shipping_confirmed_at);
    }

    public function test_pesanan_selesai_atau_batal_tidak_bisa_dikonfirmasi(): void
    {
        $origin = $this->buatTitikAsal();
        $courier = $this->buatKurir();

        foreach (['ready', 'completed', 'cancelled'] as $status) {
            $order = $this->buatPesanan(['status' => $status]);

            $this->konfirmasi($order, [
                'shipping_origin_id' => $origin->id,
                'courier_id' => $courier->id,
            ])->assertStatus(422);

            $this->assertNull($order->fresh()->shipping_confirmed_at);
        }
    }

    public function test_konfirmasi_ulang_saat_processing_boleh_dengan_kurir_baru(): void
    {
        $origin = $this->buatTitikAsal();
        $kurirPertama = $this->buatKurir();
        $kurirKedua = $this->buatKurir(['name' => 'Kurir Mitra', 'phone' => '081234567893']);
        $order = $this->buatPesanan();

        $this->konfirmasi($order, [
            'shipping_origin_id' => $origin->id,
            'courier_id' => $kurirPertama->id,
        ])->assertOk();

        $this->konfirmasi($order, [
            'shipping_origin_id' => $origin->id,
            'courier_id' => $kurirKedua->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.kurir', 'Kurir Mitra')
            ->assertJsonPath('data.status', 'processing');

        $this->assertSame($kurirKedua->id, $order->fresh()->courier_id);
    }

    public function test_daftar_pesanan_menampilkan_status_konfirmasi(): void
    {
        $origin = $this->buatTitikAsal();
        $courier = $this->buatKurir();
        $sudah = $this->buatPesanan();
        $this->konfirmasi($sudah, [
            'shipping_origin_id' => $origin->id,
            'courier_id' => $courier->id,
        ])->assertOk();
        $belum = $this->buatPesanan();

        $this->actingAs($this->owner)
            ->getJson('/owner/api/pesanan')
            ->assertOk()
            ->assertJsonPath('data.total', 2)
            ->assertJsonPath('data.pesanan.0.id', $belum->id)
            ->assertJsonPath('data.pesanan.0.titik_asal', null)
            ->assertJsonPath('data.pesanan.1.id', $sudah->id)
            ->assertJsonPath('data.pesanan.1.titik_asal', $origin->name)
            ->assertJsonPath('data.pesanan.1.kurir', $courier->name);

        $this->actingAs($this->owner)
            ->getJson('/owner/api/pesanan?belum_dikonfirmasi=1')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.pesanan.0.id', $belum->id);
    }
}
