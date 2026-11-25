<?php

namespace Tests\Feature;

use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-144] Uji penerimaan penanda merah stok minimum (PPL-06).
 *
 * Cakupan CHIK-142 (rule batas minimum & pengaturan batas per produk) dan
 * CHIK-143 (tampilan penanda merah di halaman stok & dashboard).
 *
 * Rule yang diuji (docs/API-KELOLA-PRODUK.md):
 *   habis   : stock <= 0
 *   menipis : 0 < stock <= min_stock
 *   aman    : stock > min_stock
 */
class PenandaStokMinimumTest extends TestCase
{
    use MenyiapkanDataChikumi;

    /** @var array<string, mixed> */
    private array $d;

    private User $owner;

    private User $kasir;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();

        $this->owner = User::create([
            'name' => 'Owner Stok',
            'email' => 'owner.stok@gmail.com',
            'phone_number' => '081200000041',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();
    }

    // MovingAverage::harian() membaca order_items, jadi skema pesanan ikut dibuat.
    private function buatSkemaPesanan(): void
    {
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

    // ---------- Pembantu ----------

    private function ukuran(string $kunci = 'regular'): ProductSize
    {
        return $this->d[$kunci];
    }

    /** @return array<string, mixed> */
    private function ketersediaan(?User $user = null): array
    {
        $res = $this->actingAs($user ?? $this->owner)
            ->getJson('/owner/api/produk/ketersediaan')
            ->assertOk();

        return $res->json('data');
    }

    /**
     * Baris `data.produk` untuk satu ukuran. Respons tidak memuat id ukuran,
     * jadi dicocokkan lewat nama produk + nama ukuran.
     *
     * @return array<string, mixed>
     */
    private function produkKetersediaan(array $data, int $sizeId): array
    {
        $ukuran = $this->ukuranDari($sizeId);

        foreach ($data['produk'] as $produk) {
            if ($produk['product'] === $ukuran->product->name && $produk['size'] === $ukuran->size_name) {
                return $produk;
            }
        }

        $this->fail("Ukuran id {$sizeId} tidak ada di respons ketersediaan.");
    }

    private function ukuranDari(int $id): ProductSize
    {
        return ProductSize::with('product')->findOrFail($id);
    }

    // ---------- Rule batas minimum (model) ----------

    public function test_stok_nol_atau_minus_dianggap_habis(): void
    {
        foreach ([0, -1, -25] as $stok) {
            $size = new ProductSize(['stock' => $stok, 'min_stock' => 5]);

            $this->assertTrue($size->isOutOfStock(), "stok {$stok}");
            $this->assertFalse($size->isBelowMinimum(), "stok {$stok}");
            $this->assertSame('habis', $size->stockStatus(), "stok {$stok}");
        }
    }

    public function test_stok_di_bawah_atau_sama_dengan_minimum_dianggap_menipis(): void
    {
        foreach ([1, 3, 4, 5] as $stok) {
            $size = new ProductSize(['stock' => $stok, 'min_stock' => 5]);

            $this->assertFalse($size->isOutOfStock(), "stok {$stok}");
            $this->assertTrue($size->isBelowMinimum(), "stok {$stok}");
            $this->assertSame('menipis', $size->stockStatus(), "stok {$stok}");
        }
    }

    public function test_stok_di_atas_minimum_dianggap_aman(): void
    {
        foreach ([6, 7, 100] as $stok) {
            $size = new ProductSize(['stock' => $stok, 'min_stock' => 5]);

            $this->assertFalse($size->isBelowMinimum(), "stok {$stok}");
            $this->assertSame('aman', $size->stockStatus(), "stok {$stok}");
        }
    }

    public function test_minimum_nol_hanya_menandai_stok_habis(): void
    {
        $nol = new ProductSize(['stock' => 0, 'min_stock' => 0]);
        $this->assertSame('habis', $nol->stockStatus());

        $satu = new ProductSize(['stock' => 1, 'min_stock' => 0]);
        $this->assertSame('aman', $satu->stockStatus());
    }

    public function test_minimum_besar_menandai_stok_menipis(): void
    {
        $size = new ProductSize(['stock' => 50, 'min_stock' => 100]);

        $this->assertTrue($size->isBelowMinimum());
        $this->assertSame('menipis', $size->stockStatus());
    }

    public function test_batas_minimum_default_lima(): void
    {
        $this->assertSame(5, ProductSize::DEFAULT_MIN_STOCK);
    }

    // ---------- Pengaturan batas minimum per produk (API) ----------

    public function test_owner_dapat_mengubah_batas_minimum(): void
    {
        $size = $this->ukuran('regular');

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 12])
            ->assertOk()
            ->assertJsonPath('message', 'Batas minimum stok Regular menjadi 12 pcs.')
            ->assertJsonPath('data.min_stock', 12);

        $this->assertSame(12, $this->ukuran('regular')->fresh()->min_stock);
    }

    public function test_batas_minimum_boleh_nol(): void
    {
        $size = $this->ukuran('large');

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 0])
            ->assertOk()
            ->assertJsonPath('data.min_stock', 0);

        $this->assertSame(0, $this->ukuran('large')->fresh()->min_stock);
    }

    public function test_batas_minimum_negatif_desimal_dan_bukan_angka_ditolak(): void
    {
        $size = $this->ukuran('regular');
        $awal = $size->min_stock;

        foreach ([-1, 2.5, 'banyak', '3.5'] as $nilai) {
            $this->actingAs($this->owner)
                ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => $nilai])
                ->assertStatus(422)
                ->assertJsonValidationErrors('min_stock');
        }

        $this->assertSame($awal, $this->ukuran('regular')->fresh()->min_stock);
    }

    public function test_batas_minimum_boolean_benar_diterima_sebagai_satu(): void
    {
        // Aturan `integer` Laravel meloloskan boolean true (menjadi 1).
        // Dicatat sebagai temuan perilaku, bukan kegagalan rule utama.
        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$this->ukuran('regular')->id}/minimum", ['min_stock' => true])
            ->assertOk()
            ->assertJsonPath('data.min_stock', 1);
    }

    public function test_batas_minimum_wajib_dikirim_dan_maksimal_seratus_ribu(): void
    {
        $size = $this->ukuran('regular');

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('min_stock');

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 100001])
            ->assertStatus(422)
            ->assertJsonValidationErrors('min_stock');

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 100000])
            ->assertOk();
    }

    public function test_ubah_batas_minimum_hanya_untuk_owner(): void
    {
        $size = $this->ukuran('regular');

        $this->patch("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 9])
            ->assertRedirect('/login');

        $this->actingAs($this->kasir)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 9])
            ->assertForbidden();

        $this->actingAs($this->pelanggan)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 9])
            ->assertForbidden();

        $this->assertSame(5, $this->ukuran('regular')->fresh()->min_stock);
    }

    public function test_ubah_batas_minimum_id_tidak_ada_menghasilkan_404(): void
    {
        $this->actingAs($this->owner)
            ->patchJson('/owner/api/ukuran/99999/minimum', ['min_stock' => 9])
            ->assertNotFound();
    }

    public function test_ubah_minimum_tidak_mengubah_stok_dan_harga(): void
    {
        $size = $this->ukuran('regular');
        $stok = $size->stock;
        $harga = $size->base_price;

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 50])
            ->assertOk();

        $baru = $this->ukuran('regular')->fresh();
        $this->assertSame($stok, $baru->stock);
        $this->assertEquals($harga, $baru->base_price);
    }

    // ---------- Minimum ikut tersimpan lewat endpoint lain ----------

    public function test_put_ukuran_tanpa_min_stock_mempertahankan_nilai_lama(): void
    {
        $size = $this->ukuran('regular');
        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 15])
            ->assertOk();

        $this->actingAs($this->owner)
            ->putJson("/owner/api/ukuran/{$size->id}", [
                'size_name' => 'Regular',
                'base_price' => 16000,
                'stock' => 40,
            ])
            ->assertOk()
            ->assertJsonPath('data.min_stock', 15);

        $this->assertSame(15, $this->ukuran('regular')->fresh()->min_stock);
    }

    public function test_put_ukuran_dapat_mengubah_min_stock(): void
    {
        $size = $this->ukuran('large');

        $this->actingAs($this->owner)
            ->putJson("/owner/api/ukuran/{$size->id}", [
                'size_name' => 'Large',
                'base_price' => 22000,
                'stock' => 10,
                'min_stock' => 4,
            ])
            ->assertOk()
            ->assertJsonPath('data.min_stock', 4);
    }

    public function test_ukuran_baru_memakai_minimum_default_lima(): void
    {
        $produk = $this->d['produkLain'];
        // Produk lain sudah punya ukuran Regular; hapus dulu agar bisa dibuat ulang.
        $this->d['ukuranProdukLain']->delete();

        $this->actingAs($this->owner)
            ->postJson("/owner/api/produk/{$produk->id}/ukuran", [
                'size_name' => 'Regular',
                'base_price' => 18000,
                'stock' => 3,
            ])
            ->assertCreated()
            ->assertJsonPath('data.min_stock', 5);
    }

    public function test_ukuran_baru_dapat_memakai_minimum_khusus(): void
    {
        $produk = $this->d['produkLain'];
        $this->d['ukuranProdukLain']->delete();

        $this->actingAs($this->owner)
            ->postJson("/owner/api/produk/{$produk->id}/ukuran", [
                'size_name' => 'Large',
                'base_price' => 25000,
                'stock' => 2,
                'min_stock' => 9,
            ])
            ->assertCreated()
            ->assertJsonPath('data.min_stock', 9);
    }

    // ---------- Rule dipakai API ketersediaan ----------

    public function test_ketersediaan_mengembalikan_status_sesuai_rule(): void
    {
        $this->ukuran('regular')->update(['stock' => 50, 'min_stock' => 5]);  // aman
        $this->ukuran('large')->update(['stock' => 5, 'min_stock' => 5]);     // menipis (batas)
        $data = $this->ketersediaan();

        $regular = $this->produkKetersediaan($data, $this->ukuran('regular')->id);
        $large = $this->produkKetersediaan($data, $this->ukuran('large')->id);

        $this->assertSame('aman', $regular['status']);
        $this->assertSame(50, $regular['stock']);
        $this->assertSame(5, $regular['min_stock']);

        $this->assertSame('menipis', $large['status']);
        $this->assertSame(5, $large['stock']);
    }

    public function test_ketersediaan_menandai_stok_habis(): void
    {
        $this->ukuran('large')->update(['stock' => 0, 'min_stock' => 5]);

        $large = $this->produkKetersediaan($this->ketersediaan(), $this->ukuran('large')->id);

        $this->assertSame('habis', $large['status']);
    }

    public function test_mengubah_minimum_langsung_mengubah_status(): void
    {
        $size = $this->ukuran('regular');
        $size->update(['stock' => 10, 'min_stock' => 5]);

        $this->assertSame('aman', $this->produkKetersediaan($this->ketersediaan(), $size->id)['status']);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 10])
            ->assertOk();

        $this->assertSame('menipis', $this->produkKetersediaan($this->ketersediaan(), $size->id)['status']);

        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$size->id}/minimum", ['min_stock' => 9])
            ->assertOk();

        $this->assertSame('aman', $this->produkKetersediaan($this->ketersediaan(), $size->id)['status']);
    }

    public function test_ringkasan_ketersediaan_menghitung_tiap_status(): void
    {
        $this->ukuran('regular')->update(['stock' => 50, 'min_stock' => 5]);   // aman
        $this->ukuran('large')->update(['stock' => 2, 'min_stock' => 5]);      // menipis
        $this->d['sambalHabis']->update(['is_available' => false]);            // habis (varian)

        $data = $this->ketersediaan();

        // 3 ukuran (2 produk) + 8 varian = 11 komponen.
        $this->assertSame(11, $data['ringkasan']['total']);
        // Aman: regular + ukuran produk lain (10 pcs) + 7 varian tersedia.
        $this->assertSame(9, $data['ringkasan']['aman']);
        $this->assertSame(1, $data['ringkasan']['menipis']);
        $this->assertSame(1, $data['ringkasan']['habis']);
    }

    public function test_varian_tidak_tersedia_dihitung_habis(): void
    {
        $data = $this->ketersediaan();
        $varian = collect($data['varian'])->firstWhere('name', 'Sambal Matah');

        $this->assertSame('habis', $varian['status']);

        $this->d['sambalHabis']->update(['is_available' => true]);
        $varian = collect($this->ketersediaan()['varian'])->firstWhere('name', 'Sambal Matah');
        $this->assertSame('aman', $varian['status']);
    }

    public function test_produk_tidak_aktif_dihitung_habis(): void
    {
        $this->ukuran('regular')->update(['stock' => 99, 'min_stock' => 5]);
        $this->d['produk']->update(['is_active' => false]);

        $regular = $this->produkKetersediaan($this->ketersediaan(), $this->ukuran('regular')->id);

        $this->assertSame('habis', $regular['status']);
        $this->assertSame(99, $regular['stock']);
    }

    public function test_ketersediaan_hanya_untuk_owner(): void
    {
        $this->get('/owner/api/produk/ketersediaan')->assertRedirect('/login');
        $this->actingAs($this->kasir)->getJson('/owner/api/produk/ketersediaan')->assertForbidden();
        $this->actingAs($this->pelanggan)->getJson('/owner/api/produk/ketersediaan')->assertForbidden();
    }

    public function test_pratinjau_ketersediaan_tanpa_login(): void
    {
        $this->getJson('/frontend/owner/api/ketersediaan')
            ->assertOk()
            ->assertJsonPath('data.ringkasan.total', 11);
    }

    // ---------- Tampilan penanda merah di halaman stok ----------

    public function test_halaman_stok_menandai_baris_menipis_dan_habis(): void
    {
        $this->ukuran('regular')->update(['stock' => 50, 'min_stock' => 5]);  // aman
        $this->ukuran('large')->update(['stock' => 3, 'min_stock' => 5]);     // menipis

        $res = $this->actingAs($this->owner)->get('/owner/stok')->assertOk();

        $res->assertSee('bg-[#FDECEC]', false);
        $res->assertSee('text-[#E03131]', false);
        $res->assertSee('Minimum');
        $res->assertSee('Aman');
        $res->assertSee('1 komponen habis', false);
        $res->assertSee('1 di bawah minimum', false);
    }

    public function test_halaman_stok_tanpa_komponen_bermasalah_tidak_menampilkan_peringatan(): void
    {
        $this->ukuran('regular')->update(['stock' => 50, 'min_stock' => 5]);
        $this->ukuran('large')->update(['stock' => 50, 'min_stock' => 5]);
        foreach (['sambalHabis'] as $kunci) {
            $this->d[$kunci]->update(['is_available' => true]);
        }

        $res = $this->actingAs($this->owner)->get('/owner/stok')->assertOk();

        $res->assertDontSee('di bawah minimum');
        $res->assertDontSee('komponen habis');
        // Kartu ringkasan tetap memakai latar merah, tetapi tidak ada baris tabel yang merah.
        $res->assertDontSee('class="bg-[#FDECEC] text-[#E03131]"', false);
    }

    public function test_halaman_stok_menampilkan_nilai_minimum_per_baris(): void
    {
        $this->ukuran('regular')->update(['stock' => 4, 'min_stock' => 12]);

        $this->actingAs($this->owner)->get('/owner/stok')
            ->assertOk()
            ->assertSee('12 pcs');
    }

    public function test_halaman_stok_hanya_untuk_owner(): void
    {
        $this->get('/owner/stok')->assertRedirect('/login');
        $this->actingAs($this->kasir)->get('/owner/stok')->assertRedirect();
    }

    // ---------- Tampilan penanda merah di dashboard ----------

    public function test_dashboard_menyediakan_wadah_penanda_merah(): void
    {
        $res = $this->actingAs($this->owner)->get('/owner/dashboard')->assertOk();

        $res->assertSee('Kondisi Stok');
        $res->assertSee('stokMenipis');
        $res->assertSee('Di bawah minimum');
        $res->assertSee('stokHabis');
        $res->assertSee('bg-[#FDECEC]', false);
    }

    public function test_dashboard_memakai_endpoint_ketersediaan(): void
    {
        $this->actingAs($this->owner)->get('/owner/dashboard')
            ->assertOk()
            ->assertSee('/produk/ketersediaan');
    }

    // ---------- Konsistensi angka halaman vs API ----------

    public function test_angka_halaman_stok_sama_dengan_api_ketersediaan(): void
    {
        $this->ukuran('regular')->update(['stock' => 50, 'min_stock' => 5]);
        $this->ukuran('large')->update(['stock' => 2, 'min_stock' => 5]);

        $api = $this->ketersediaan();
        $html = $this->actingAs($this->owner)->get('/owner/stok')->assertOk()->getContent();

        // Kartu ringkasan halaman stok memakai hitungan yang sama.
        preg_match('/text-\[#D96C00\]">(\d+)<\/h3>\s*<p[^>]*>Total Komponen</', $html, $total);
        preg_match('/text-\[#1F9D55\]">(\d+)<\/h3>\s*<p[^>]*>Aman</', $html, $aman);
        preg_match('/text-\[#E03131\]">(\d+)<\/h3>\s*<p[^>]*>Menipis</', $html, $menipis);
        preg_match('/text-\[#E03131\]">(\d+)<\/h3>\s*<p[^>]*>Habis</', $html, $habis);

        $this->assertSame((string) $api['ringkasan']['total'], $total[1]);
        $this->assertSame((string) $api['ringkasan']['aman'], $aman[1]);
        $this->assertSame((string) $api['ringkasan']['menipis'], $menipis[1]);
        $this->assertSame((string) $api['ringkasan']['habis'], $habis[1]);
    }

    public function test_minimum_tersimpan_di_kolom_khusus_per_ukuran(): void
    {
        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$this->ukuran('regular')->id}/minimum", ['min_stock' => 21])
            ->assertOk();
        $this->actingAs($this->owner)
            ->patchJson("/owner/api/ukuran/{$this->ukuran('large')->id}/minimum", ['min_stock' => 3])
            ->assertOk();

        $baris = DB::table('product_sizes')->orderBy('id')->pluck('min_stock', 'id');

        $this->assertSame(21, (int) $baris[$this->ukuran('regular')->id]);
        $this->assertSame(3, (int) $baris[$this->ukuran('large')->id]);
    }
}
