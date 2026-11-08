<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-78] Testing perubahan harga — PPL-20 Harga Dinamis Sesuai Pilihan.
 *
 * Menguji endpoint preview harga  GET /customer/api/produk/{produk}/harga,
 * konsistensi harga di keranjang, dan halaman atur produk /customer/produk.
 */
class HargaDinamisTest extends TestCase
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

    private function urlHarga(array $query, ?Product $produk = null): string
    {
        $produk ??= $this->d['produk'];

        return "/customer/api/produk/{$produk->id}/harga?".http_build_query($query);
    }

    private function cekHarga(array $query, ?Product $produk = null)
    {
        return $this->actingAs($this->pelanggan)->getJson($this->urlHarga($query, $produk));
    }

    private function ids(string ...$kunci): array
    {
        return array_map(fn ($k) => $this->d[$k]->id, $kunci);
    }

    // ---------- Endpoint preview harga ----------

    public function test_f01_ukuran_regular_tanpa_varian(): void
    {
        $this->cekHarga(['product_size_id' => $this->d['regular']->id])
            ->assertOk()
            ->assertJsonPath('data.ukuran', 'Regular')
            ->assertJsonPath('data.harga_satuan', 15000)
            ->assertJsonPath('data.subtotal', 15000);
    }

    public function test_f02_ganti_ke_large_harga_ikut_berubah(): void
    {
        $this->cekHarga(['product_size_id' => $this->d['large']->id])
            ->assertOk()
            ->assertJsonPath('data.ukuran', 'Large')
            ->assertJsonPath('data.harga_dasar', 22000)
            ->assertJsonPath('data.harga_satuan', 22000);
    }

    public function test_f03_tambah_filling_dip_dan_seasoning(): void
    {
        $this->cekHarga([
            'product_size_id' => $this->d['regular']->id,
            'variant_ids' => $this->ids('mozzarella', 'bbq', 'balado'),
        ])
            ->assertOk()
            ->assertJsonPath('data.total_tambahan', 5000)   // 3000 + 2000 + 0
            ->assertJsonPath('data.harga_satuan', 20000)
            ->assertJsonCount(3, 'data.varian');
    }

    public function test_f04_ganti_dipping_sauce_mengganti_tambahan_harga(): void
    {
        $bbq = $this->cekHarga(['product_size_id' => $this->d['regular']->id, 'variant_ids' => $this->ids('bbq')]);
        $cheese = $this->cekHarga(['product_size_id' => $this->d['regular']->id, 'variant_ids' => $this->ids('cheeseSauce')]);

        $bbq->assertJsonPath('data.harga_satuan', 17000);
        $cheese->assertJsonPath('data.harga_satuan', 17500);
    }

    public function test_f05_jumlah_mengalikan_subtotal(): void
    {
        $this->cekHarga([
            'product_size_id' => $this->d['large']->id,
            'variant_ids' => $this->ids('mozzarella', 'cheeseSauce'),
            'quantity' => 2,
        ])
            ->assertOk()
            ->assertJsonPath('data.harga_satuan', 27500)     // 22000 + 3000 + 2500
            ->assertJsonPath('data.quantity', 2)
            ->assertJsonPath('data.subtotal', 55000);
    }

    public function test_f06_dua_seasoning_sekaligus_diperbolehkan(): void
    {
        $this->cekHarga([
            'product_size_id' => $this->d['regular']->id,
            'variant_ids' => $this->ids('balado', 'kejuBubuk'),
        ])
            ->assertOk()
            ->assertJsonPath('data.harga_satuan', 16000);
    }

    /** @return array<string, array{0: list<string>}> */
    public static function varianTidakValid(): array
    {
        return [
            'F07 dua filling' => [['mozzarella', 'cokelat']],
            'F08 dua dipping sauce' => [['bbq', 'cheeseSauce']],
            'F09 varian produk lain' => [['varianProdukLain']],
            'F10 varian sedang habis' => [['sambalHabis']],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('varianTidakValid')]
    public function test_f07_f10_pilihan_varian_tidak_valid_ditolak_422(array $kunci): void
    {
        $this->cekHarga([
            'product_size_id' => $this->d['regular']->id,
            'variant_ids' => $this->ids(...$kunci),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('variant_ids');
    }

    public function test_f11_varian_tidak_ditemukan_ditolak_422(): void
    {
        $this->cekHarga(['product_size_id' => $this->d['regular']->id, 'variant_ids' => [99999]])
            ->assertStatus(422)
            ->assertJsonValidationErrors('variant_ids');
    }

    public function test_f12_ukuran_milik_produk_lain_ditolak_422(): void
    {
        $this->cekHarga(['product_size_id' => $this->d['ukuranProdukLain']->id])
            ->assertStatus(422);
    }

    public function test_f13_ukuran_wajib_diisi(): void
    {
        $this->cekHarga([])
            ->assertStatus(422)
            ->assertJsonValidationErrors('product_size_id');
    }

    public function test_f14_jumlah_nol_ditolak(): void
    {
        $this->cekHarga(['product_size_id' => $this->d['regular']->id, 'quantity' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    }

    public function test_f15_id_varian_dobel_ditolak(): void
    {
        $id = $this->d['mozzarella']->id;

        $this->cekHarga(['product_size_id' => $this->d['regular']->id, 'variant_ids' => [$id, $id]])
            ->assertStatus(422);
    }

    public function test_f16_produk_nonaktif_404(): void
    {
        $this->d['produk']->update(['is_active' => false]);

        $this->cekHarga(['product_size_id' => $this->d['regular']->id])
            ->assertNotFound();
    }

    public function test_f17_wajib_login(): void
    {
        $this->getJson($this->urlHarga(['product_size_id' => $this->d['regular']->id]))
            ->assertUnauthorized();
    }

    // ---------- Konsistensi dengan keranjang ----------

    public function test_f18_harga_di_keranjang_sama_dengan_preview(): void
    {
        $pilihan = [
            'product_size_id' => $this->d['large']->id,
            'variant_ids' => $this->ids('mozzarella', 'bbq', 'kejuBubuk'),
            'quantity' => 2,
        ];

        $preview = $this->cekHarga($pilihan)->assertOk()->json('data');

        $this->actingAs($this->pelanggan)->postJson('/customer/api/keranjang', $pilihan)->assertCreated();

        $item = $this->actingAs($this->pelanggan)->getJson('/customer/api/keranjang')
            ->assertOk()
            ->json('data.item.0');

        $this->assertEquals(28000, $preview['harga_satuan']);  // 22000 + 3000 + 2000 + 1000
        $this->assertEquals($preview['harga_satuan'], $item['harga_satuan']);
        $this->assertEquals($preview['subtotal'], $item['subtotal']);
    }

    // ---------- Halaman atur produk (data untuk perhitungan harga di browser) ----------

    private function bukaHalamanProduk(array $query = [])
    {
        $this->withoutVite();

        return $this->actingAs($this->pelanggan)
            ->get('/customer/produk?'.http_build_query(['produk' => $this->d['produk']->id] + $query));
    }

    public function test_f19_halaman_menampilkan_harga_awal_ukuran_default(): void
    {
        $this->bukaHalamanProduk()
            ->assertOk()
            ->assertSeeInOrder(['id="hargaSatuan"', 'Rp 15.000'], false);
    }

    public function test_f20_halaman_menyediakan_harga_ukuran_dan_varian_untuk_hitung_otomatis(): void
    {
        $this->bukaHalamanProduk()
            ->assertOk()
            ->assertSee('data-harga="15000"', false)
            ->assertSee('data-harga="22000"', false)
            ->assertSee('data-harga="3000"', false)
            ->assertSee('data-harga="2500"', false)
            ->assertSee('+Rp 3.000');
    }

    public function test_f21_parameter_ukuran_menentukan_harga_awal(): void
    {
        $this->bukaHalamanProduk(['ukuran' => $this->d['large']->id])
            ->assertOk()
            ->assertSeeInOrder(['id="hargaSatuan"', 'Rp 22.000'], false);
    }

    public function test_f22_varian_habis_tidak_bisa_dipilih(): void
    {
        $this->bukaHalamanProduk()
            ->assertOk()
            ->assertSee('Sambal Matah · habis')
            ->assertDontSee('data-nama="Sambal Matah"', false);
    }

    public function test_f23_ukuran_stok_habis_dinonaktifkan(): void
    {
        $this->d['large']->update(['stock' => 0]);

        $html = $this->bukaHalamanProduk()->assertOk()->getContent();
        $idLarge = $this->d['large']->id;

        $this->assertMatchesRegularExpression('/name="ukuran" value="'.$idLarge.'"[^>]*\\sdisabled/', $html);
        $this->assertStringContainsString('25 cm · habis', preg_replace('/\\s+/', ' ', $html));
    }
}
