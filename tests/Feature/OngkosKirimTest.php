<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Courier;
use App\Models\Order;
use App\Models\ShippingOrigin;
use App\Models\User;
use App\Support\DeliveryMethod;
use App\Support\ShippingCost;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-112] Uji penerimaan PPL-25 (CHIK-48) — total pembayaran & ongkos kirim.
 *
 * Cakupan:
 * - CHIK-110: App\Support\ShippingCost (RajaOngkir + fallback flat),
 *   GET /customer/api/ongkir, GET /customer/api/kota.
 * - CHIK-111: total di checkout = subtotal + ongkir (frontend vs backend).
 * - CHIK-109: titik asal & kurir oleh owner, konfirmasi pengiriman.
 *
 * Respons RajaOngkir dipalsukan dengan Http::fake karena .env tidak berisi
 * RAJAONGKIR_API_KEY; nilai ongkir dicocokkan dengan hitungan manual.
 */
class OngkosKirimTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $pelanggan;

    private User $owner;

    private int $urutan = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->pelanggan = $this->buatPelanggan();
        $this->owner = User::create([
            'name' => 'Owner Ongkir',
            'email' => 'owner.ongkir@gmail.com',
            'phone_number' => '081200000099',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);

        config([
            'services.rajaongkir.key' => null,
            'services.rajaongkir.base_url' => 'https://rajaongkir.komerce.id/api/v1',
            'services.rajaongkir.origin' => 444,
            'services.rajaongkir.courier' => 'jne',
            'services.rajaongkir.timeout' => 5,
        ]);
        Cache::flush();
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
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();
        });

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

    private function alamat(User $user, ?int $cityId = 444, array $override = []): Address
    {
        return Address::create(array_merge([
            'customer_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Penerima Uji',
            'phone' => '081200000009',
            'detail' => 'Jl. Uji No. 1',
            'city' => 'Bangkalan',
            'city_id' => $cityId,
            'postal_code' => '69112',
        ], $override));
    }

    private function isiKeranjang(int $quantity = 2): void
    {
        $this->actingAs($this->pelanggan)->postJson('/customer/api/keranjang', [
            'product_size_id' => $this->d['regular']->id,
            'quantity' => $quantity,
            'variant_ids' => [],
        ])->assertCreated();
    }

    private function checkout(array $payload)
    {
        return $this->actingAs($this->pelanggan)->postJson('/customer/api/pesanan/checkout', $payload);
    }

    private function fakeRajaOngkir(array $services): void
    {
        Http::fake([
            'rajaongkir.komerce.id/*' => Http::response(['data' => $services], 200),
        ]);
    }

    // ================= A. Tarif dasar (tanpa API) =================

    public function test_metode_penerimaan_ongkir_dan_kebutuhan_alamat(): void
    {
        $opsi = collect(DeliveryMethod::options())->keyBy('kode');

        $this->assertSame(0.0, $opsi['pickup']['ongkir']);
        $this->assertFalse($opsi['pickup']['butuh_alamat']);
        $this->assertSame(5000.0, $opsi['courier']['ongkir']);
        $this->assertTrue($opsi['courier']['butuh_alamat']);
    }

    public function test_tanpa_api_key_ongkir_memakai_tarif_flat(): void
    {
        $hasil = ShippingCost::quote(444);

        $this->assertSame(5000.0, $hasil['ongkir']);
        $this->assertSame('flat', $hasil['sumber']);
        $this->assertNull($hasil['etd']);
        Http::assertNothingSent();
    }

    public function test_kota_tujuan_kosong_memakai_tarif_flat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);

        $hasil = ShippingCost::quote(null);

        $this->assertSame(5000.0, $hasil['ongkir']);
        $this->assertSame('flat', $hasil['sumber']);
        Http::assertNothingSent();
    }

    // ================= B. Perhitungan dari RajaOngkir =================

    public function test_ongkir_memilih_layanan_termurah(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([
            ['service' => 'REG', 'cost' => 12000, 'etd' => '2-3'],
            ['service' => 'YES', 'cost' => 25000, 'etd' => '1-1'],
            ['service' => 'OKE', 'cost' => 9000, 'etd' => '3-4'],
        ]);

        $hasil = ShippingCost::quote(444);

        // Hitungan manual: termurah dari 9.000 / 12.000 / 25.000 = 9.000
        $this->assertSame(9000.0, $hasil['ongkir']);
        $this->assertSame('rajaongkir', $hasil['sumber']);
        $this->assertSame('3-4', $hasil['etd']);
    }

    public function test_payload_ke_rajaongkir_memakai_origin_kurir_dan_berat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 8000, 'etd' => '2-3']]);

        ShippingCost::quote(555, 2000);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/calculate/domestic-cost')
                && $request['origin'] === 444
                && $request['destination'] === 555
                && $request['weight'] === 2000
                && $request['courier'] === 'jne'
                && $request->hasHeader('key', 'kunci-uji');
        });
    }

    public function test_layanan_tanpa_biaya_numerik_diabaikan(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([
            ['service' => 'REG', 'cost' => null, 'etd' => '2-3'],
            ['service' => 'YES', 'cost' => '15000', 'etd' => '1-1'],
        ]);

        $this->assertSame(15000.0, ShippingCost::quote(444)['ongkir']);
    }

    public function test_respons_kosong_jatuh_ke_tarif_flat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([]);

        $hasil = ShippingCost::quote(444);

        $this->assertSame(5000.0, $hasil['ongkir']);
        $this->assertSame('flat', $hasil['sumber']);
    }

    public function test_api_error_jatuh_ke_tarif_flat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        Http::fake(['rajaongkir.komerce.id/*' => Http::response('Server Error', 500)]);

        $hasil = ShippingCost::quote(444);

        $this->assertSame(5000.0, $hasil['ongkir']);
        $this->assertSame('flat', $hasil['sumber']);
    }

    public function test_koneksi_gagal_jatuh_ke_tarif_flat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        Http::fake(['rajaongkir.komerce.id/*' => fn () => throw new \RuntimeException('timeout')]);

        $this->assertSame(5000.0, ShippingCost::quote(444)['ongkir']);
    }

    public function test_hasil_ongkir_di_cache_10_menit(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 11000, 'etd' => '2-3']]);

        $pertama = ShippingCost::quote(444);
        $kedua = ShippingCost::quote(444);

        $this->assertSame($pertama['ongkir'], $kedua['ongkir']);
        Http::assertSentCount(1);
    }

    // ================= C. Endpoint ongkir & kota =================

    public function test_endpoint_ongkir_dengan_city_id(): void
    {
        $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/ongkir?city_id=444')
            ->assertOk()
            ->assertJsonPath('data.ongkir', 5000)
            ->assertJsonPath('data.sumber', 'flat')
            ->assertJsonPath('data.alamat_id', null);
    }

    public function test_endpoint_ongkir_dengan_alamat_sendiri(): void
    {
        $alamat = $this->alamat($this->pelanggan);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/ongkir?address_id={$alamat->id}")
            ->assertOk()
            ->assertJsonPath('data.alamat_id', $alamat->id)
            ->assertJsonPath('data.ongkir', 5000);
    }

    public function test_endpoint_ongkir_tanpa_alamat_ditolak(): void
    {
        $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/ongkir')
            ->assertStatus(422);
    }

    public function test_endpoint_ongkir_alamat_pelanggan_lain_ditolak(): void
    {
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain.ongkir@gmail.com',
            'phone_number' => '081200000055',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
        $alamat = $this->alamat($lain);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/ongkir?address_id={$alamat->id}")
            ->assertStatus(422);
    }

    public function test_endpoint_ongkir_meneruskan_berat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 14000, 'etd' => '2-3']]);

        $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/ongkir?city_id=444&berat=2000')
            ->assertOk()
            ->assertJsonPath('data.ongkir', 14000);

        Http::assertSent(fn ($request) => $request['weight'] === 2000);
    }

    public function test_pencarian_kota_tanpa_api_memakai_daftar_lokal(): void
    {
        $res = $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/kota?q=bangkalan')
            ->assertOk();

        $hasil = $res->json('data');

        $this->assertNotEmpty($hasil);
        $this->assertNull($hasil[0]['id']); // id null -> ongkir flat
        $this->assertStringContainsString('Bangkalan', $hasil[0]['label']);
    }

    public function test_pencarian_kota_kurang_dari_tiga_huruf_ditolak(): void
    {
        $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/kota?q=ba')
            ->assertStatus(422);
    }

    public function test_pencarian_kota_dengan_api_mengembalikan_id(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        Http::fake([
            'rajaongkir.komerce.id/*' => Http::response([
                'data' => [[
                    'id' => 1234,
                    'label' => 'Bangkalan, Bangkalan, Jawa Timur',
                    'city_name' => 'Bangkalan',
                    'district_name' => 'Bangkalan',
                    'zip_code' => '69111',
                ]],
            ], 200),
        ]);

        $res = $this->actingAs($this->pelanggan)
            ->getJson('/customer/api/kota?q=bangkalan')
            ->assertOk();

        $this->assertSame(1234, $res->json('data.0.id'));
    }

    // ================= D. Total pembayaran di checkout =================

    public function test_checkout_courier_total_sama_dengan_subtotal_plus_ongkir_api(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 12000, 'etd' => '2-3']]);

        $alamat = $this->alamat($this->pelanggan, 444);
        $this->isiKeranjang(2); // 2 x 15.000 = 30.000

        $res = $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
        ])->assertCreated();

        $order = Order::findOrFail($res->json('data.id'));

        // Hitungan manual: 30.000 + 12.000 = 42.000
        $this->assertEquals(30000, $order->subtotal);
        $this->assertEquals(12000, $order->shipping_cost);
        $this->assertEquals(42000, $order->grand_total);
        $this->assertEquals(42000, $res->json('data.total'));
    }

    public function test_ongkir_yang_ditampilkan_sama_dengan_yang_ditagih(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 9500, 'etd' => '2-3']]);

        $alamat = $this->alamat($this->pelanggan, 444);

        // Nilai yang dilihat pelanggan di halaman checkout.
        $tampil = $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/ongkir?address_id={$alamat->id}")
            ->assertOk()
            ->json('data.ongkir');

        $this->isiKeranjang(1);
        $res = $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
        ])->assertCreated();

        $order = Order::findOrFail($res->json('data.id'));

        $this->assertSame(9500, (int) $tampil);
        $this->assertEquals($tampil, $order->shipping_cost);
        $this->assertEquals(15000 + $tampil, $order->grand_total);
    }

    public function test_checkout_pickup_tanpa_ongkir(): void
    {
        $this->isiKeranjang(2);

        $res = $this->checkout([
            'delivery_method' => 'pickup',
            'payment_method' => 'qris',
        ])->assertCreated();

        $order = Order::findOrFail($res->json('data.id'));

        $this->assertEquals(0, $order->shipping_cost);
        $this->assertEquals(30000, $order->grand_total);
        $this->assertNull($order->shipping_address);
    }

    public function test_checkout_courier_tanpa_api_memakai_flat_lima_ribu(): void
    {
        $alamat = $this->alamat($this->pelanggan, 444);
        $this->isiKeranjang(1);

        $res = $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
        ])->assertCreated();

        $order = Order::findOrFail($res->json('data.id'));

        $this->assertEquals(5000, $order->shipping_cost);
        $this->assertEquals(20000, $order->grand_total);
    }

    public function test_checkout_courier_alamat_tanpa_city_id_memakai_flat(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 30000, 'etd' => '1-1']]);

        $alamat = $this->alamat($this->pelanggan, null);
        $this->isiKeranjang(1);

        $res = $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
        ])->assertCreated();

        // Tanpa city_id, API tidak dipanggil -> tarif dasar 5.000.
        $this->assertEquals(5000, Order::findOrFail($res->json('data.id'))->shipping_cost);
        Http::assertNothingSent();
    }

    public function test_checkout_courier_tanpa_alamat_ditolak(): void
    {
        $this->isiKeranjang(1);

        $this->checkout(['delivery_method' => 'courier'])->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_ongkir_tidak_berubah_walau_jumlah_barang_bertambah(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);

        // Tarif naik bila berat bertambah, seperti API sungguhan.
        Http::fake([
            'rajaongkir.komerce.id/*' => function ($request) {
                $berat = (int) ($request['weight'] ?? 1000);

                return Http::response(['data' => [[
                    'service' => 'REG',
                    'cost' => $berat >= 2000 ? 18000 : 9000,
                    'etd' => '2-3',
                ]]], 200);
            },
        ]);

        $alamat = $this->alamat($this->pelanggan, 444);
        $this->isiKeranjang(10); // 10 pcs

        $res = $this->checkout([
            'delivery_method' => 'courier',
            'address_id' => $alamat->id,
        ])->assertCreated();

        // Berat selalu 1.000 g (tidak dihitung dari jumlah item),
        // jadi 10 pcs tetap dikenai tarif 1 kg.
        $this->assertEquals(9000, Order::findOrFail($res->json('data.id'))->shipping_cost);
        Http::assertSent(fn ($request) => $request['weight'] === 1000);
    }

    public function test_titik_asal_yang_dikonfirmasi_tidak_mempengaruhi_ongkir(): void
    {
        config(['services.rajaongkir.key' => 'kunci-uji']);
        $this->fakeRajaOngkir([['service' => 'REG', 'cost' => 7000, 'etd' => '2-3']]);

        $dapur = ShippingOrigin::create(['name' => 'Dapur Bangkalan', 'address' => 'Jl. Raya 1']);
        $outlet = ShippingOrigin::create(['name' => 'Outlet Kota', 'address' => 'Jl. Soekarno 10']);
        $courier = Courier::create(['name' => 'Kurir']);

        $order = Order::create([
            'order_number' => 'CHKM-ASAL-1',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'courier',
            'subtotal' => 15000,
            'shipping_cost' => 7000,
            'grand_total' => 22000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $dapur->id,
                'courier_id' => $courier->id,
            ])->assertOk();

        // Ongkir hanya bergantung pada RAJAONGKIR_ORIGIN_ID di config,
        // bukan pada titik asal yang dipilih owner.
        ShippingCost::quote(444);

        Http::assertSent(fn ($request) => $request['origin'] === 444);
        $this->assertSame(7000, (int) $order->fresh()->shipping_cost);
    }

    // ================= E. Titik asal & kurir (CHIK-109) =================

    public function test_owner_menyimpan_titik_asal_dan_kurir(): void
    {
        $this->actingAs($this->owner)
            ->postJson('/owner/api/titik-asal', [
                'name' => 'Gerai Pecinan',
                'address' => 'Jl. Pemuda No. 10, Bangkalan',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Gerai Pecinan');

        $this->actingAs($this->owner)
            ->postJson('/owner/api/kurir', [
                'name' => 'Pak Slamet',
                'phone' => '081234567890',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Pak Slamet');

        $this->assertDatabaseHas('shipping_origins', ['name' => 'Gerai Pecinan', 'is_active' => true]);
        $this->assertDatabaseHas('couriers', ['name' => 'Pak Slamet', 'phone' => '081234567890']);
    }

    public function test_owner_mengonfirmasi_pengiriman_pesanan_courier(): void
    {
        $origin = ShippingOrigin::create(['name' => 'Gerai Pecinan', 'address' => 'Jl. Pemuda 10']);
        $courier = Courier::create(['name' => 'Pak Slamet', 'phone' => '081234567890']);

        $order = Order::create([
            'order_number' => 'CHKM-KIRIM-1',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'courier',
            'shipping_address' => 'Jl. Uji No. 1',
            'subtotal' => 30000,
            'shipping_cost' => 5000,
            'grand_total' => 35000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $origin->id,
                'courier_id' => $courier->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.titik_asal', 'Gerai Pecinan')
            ->assertJsonPath('data.kurir', 'Pak Slamet')
            ->assertJsonPath('data.status', 'processing');

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->shipping_confirmed_at);
        $this->assertSame($this->owner->id, $order->shipping_confirmed_by);
    }

    public function test_konfirmasi_pengiriman_hanya_untuk_pesanan_diantar(): void
    {
        $origin = ShippingOrigin::create(['name' => 'Gerai', 'address' => 'Alamat']);
        $courier = Courier::create(['name' => 'Kurir']);

        $order = Order::create([
            'order_number' => 'CHKM-KIRIM-2',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 15000,
            'shipping_cost' => 0,
            'grand_total' => 15000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $origin->id,
                'courier_id' => $courier->id,
            ])
            ->assertStatus(422);
    }

    public function test_konfirmasi_pengiriman_ditolak_bila_pesanan_sudah_selesai(): void
    {
        $origin = ShippingOrigin::create(['name' => 'Gerai', 'address' => 'Alamat']);
        $courier = Courier::create(['name' => 'Kurir']);

        $order = Order::create([
            'order_number' => 'CHKM-KIRIM-3',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'courier',
            'subtotal' => 15000,
            'shipping_cost' => 5000,
            'grand_total' => 20000,
            'status' => 'completed',
        ]);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $origin->id,
                'courier_id' => $courier->id,
            ])
            ->assertStatus(422);
    }

    public function test_kurir_tidak_aktif_tidak_bisa_dipilih(): void
    {
        $origin = ShippingOrigin::create(['name' => 'Gerai', 'address' => 'Alamat']);
        $courier = Courier::create(['name' => 'Kurir Cuti', 'is_active' => false]);

        $order = Order::create([
            'order_number' => 'CHKM-KIRIM-4',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'courier',
            'subtotal' => 15000,
            'shipping_cost' => 5000,
            'grand_total' => 20000,
            'status' => 'unpaid',
        ]);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/pesanan/{$order->id}/konfirmasi-pengiriman", [
                'shipping_origin_id' => $origin->id,
                'courier_id' => $courier->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('courier_id');
    }

    public function test_pelanggan_tidak_bisa_mengatur_titik_asal_kurir(): void
    {
        $this->actingAs($this->pelanggan)
            ->postJson('/owner/api/titik-asal', ['name' => 'X', 'address' => 'Y'])
            ->assertForbidden();

        $this->actingAs($this->pelanggan)
            ->postJson('/owner/api/kurir', ['name' => 'X'])
            ->assertForbidden();
    }
}
