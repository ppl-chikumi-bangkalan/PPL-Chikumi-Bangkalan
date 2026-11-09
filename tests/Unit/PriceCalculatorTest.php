<?php

namespace Tests\Unit;

use App\Support\PriceCalculator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-78] Testing perubahan harga — unit test App\Support\PriceCalculator (PPL-20).
 */
class PriceCalculatorTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->d = $this->buatDataChikumi();
    }

    private function varian(string ...$kunci): array
    {
        return array_map(fn ($k) => $this->d[$k]->id, $kunci);
    }

    public function test_u01_harga_satuan_sama_dengan_harga_dasar_jika_tanpa_varian(): void
    {
        $hasil = PriceCalculator::breakdown($this->d['regular'], collect());

        $this->assertEquals(15000, $hasil['harga_dasar']);
        $this->assertEquals(0, $hasil['total_tambahan']);
        $this->assertEquals(15000, $hasil['harga_satuan']);
        $this->assertEquals(15000, $hasil['subtotal']);
    }

    public function test_u02_harga_dasar_ikut_ukuran_yang_dipilih(): void
    {
        $this->assertEquals(15000, PriceCalculator::breakdown($this->d['regular'], collect())['harga_satuan']);
        $this->assertEquals(22000, PriceCalculator::breakdown($this->d['large'], collect())['harga_satuan']);
    }

    public function test_u03_harga_satuan_adalah_harga_dasar_ditambah_semua_tambahan_varian(): void
    {
        $varian = PriceCalculator::resolveVariants($this->d['produk']->id, $this->varian('mozzarella', 'bbq', 'kejuBubuk'));
        $hasil = PriceCalculator::breakdown($this->d['regular'], $varian);

        $this->assertEquals(6000, $hasil['total_tambahan']);       // 3000 + 2000 + 1000
        $this->assertEquals(21000, $hasil['harga_satuan']);        // 15000 + 6000
        $this->assertCount(3, $hasil['varian']);
    }

    public function test_u04_subtotal_adalah_harga_satuan_dikali_jumlah(): void
    {
        $varian = PriceCalculator::resolveVariants($this->d['produk']->id, $this->varian('mozzarella'));
        $hasil = PriceCalculator::breakdown($this->d['large'], $varian, 3);

        $this->assertEquals(25000, $hasil['harga_satuan']);        // 22000 + 3000
        $this->assertEquals(3, $hasil['quantity']);
        $this->assertEquals(75000, $hasil['subtotal']);            // 25000 x 3
    }

    public function test_u05_jumlah_nol_atau_negatif_dianggap_satu(): void
    {
        $this->assertSame(1, PriceCalculator::breakdown($this->d['regular'], collect(), 0)['quantity']);
        $this->assertSame(1, PriceCalculator::breakdown($this->d['regular'], collect(), -5)['quantity']);
        $this->assertEquals(15000, PriceCalculator::breakdown($this->d['regular'], collect(), -5)['subtotal']);
    }

    public function test_u06_varian_gratis_tidak_mengubah_harga(): void
    {
        $varian = PriceCalculator::resolveVariants($this->d['produk']->id, $this->varian('balado'));

        $this->assertEquals(15000, PriceCalculator::breakdown($this->d['regular'], $varian)['harga_satuan']);
    }

    public function test_u07_seasoning_boleh_lebih_dari_satu(): void
    {
        $varian = PriceCalculator::resolveVariants($this->d['produk']->id, $this->varian('balado', 'kejuBubuk'));

        $this->assertCount(2, $varian);
    }

    public function test_u08_id_varian_dobel_dihitung_sekali(): void
    {
        $id = $this->d['mozzarella']->id;
        $varian = PriceCalculator::resolveVariants($this->d['produk']->id, [$id, $id, (string) $id]);

        $this->assertCount(1, $varian);
        $this->assertEquals(18000, PriceCalculator::breakdown($this->d['regular'], $varian)['harga_satuan']);
    }

    public function test_u09_tanpa_varian_menghasilkan_koleksi_kosong(): void
    {
        $this->assertCount(0, PriceCalculator::resolveVariants($this->d['produk']->id, []));
    }

    /** @return array<string, array{0: list<string>, 1: string}> */
    public static function pilihanTidakValid(): array
    {
        return [
            'dua filling' => [['mozzarella', 'cokelat'], 'maksimal satu'],
            'dua dipping sauce' => [['bbq', 'cheeseSauce'], 'maksimal satu'],
            'varian produk lain' => [['varianProdukLain'], 'bukan dari produk ini'],
            'varian sedang habis' => [['sambalHabis'], 'tidak tersedia'],
        ];
    }

    #[DataProvider('pilihanTidakValid')]
    public function test_u10_pilihan_varian_tidak_valid_ditolak(array $kunci, string $pesan): void
    {
        try {
            PriceCalculator::resolveVariants($this->d['produk']->id, $this->varian(...$kunci));
            $this->fail('Seharusnya melempar ValidationException.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString($pesan, $e->errors()['variant_ids'][0]);
        }
    }

    public function test_u11_varian_yang_tidak_ada_ditolak(): void
    {
        $this->expectException(ValidationException::class);

        PriceCalculator::resolveVariants($this->d['produk']->id, [99999]);
    }
}
