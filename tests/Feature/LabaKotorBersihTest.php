<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-141] Uji perhitungan laba kotor & laba bersih (PPL-04).
 *
 * Cakupan CHIK-139 (perhitungan) dan CHIK-140 (tampilan laporan):
 * GET /owner/api/laporan/laba-rugi, kartu Laba Rugi, dan halaman cetak.
 *
 * Setiap nominal dihitung manual dari data yang disisipkan, sehingga
 * hasil query tidak hanya "tidak error" tetapi angkanya benar.
 */
class LabaKotorBersihTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private User $owner;

    private User $kasir;

    private User $pelanggan;

    private int $urutan = 0;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 3, 15, 10, 0, 0));

        $this->buatSkemaChikumi();
        $this->buatDataChikumi();

        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.laba@gmail.com',
            'phone_number' => '081200000041',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_method')->default('cash');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable();
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
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // ---------- Pembantu data ----------

    private function transaksiKasir(float $nominal, string $waktu, string $status = 'paid'): void
    {
        $this->urutan++;

        DB::table('pos_transactions')->insert([
            'transaction_number' => 'POS-LABA-'.$this->urutan,
            'cashier_id' => $this->kasir->id,
            'total_amount' => $nominal,
            'payment_method' => 'cash',
            'amount_paid' => $nominal,
            'change_amount' => 0,
            'status' => $status,
            'created_at' => $waktu,
        ]);
    }

    private function pesananOnline(
        float $subtotal,
        string $waktu,
        string $status = 'completed',
        bool $sudahDibayar = true,
        float $ongkir = 0,
        ?string $waktuBayar = null
    ): void {
        $this->urutan++;

        DB::table('orders')->insert([
            'order_number' => 'ORD-LABA-'.$this->urutan,
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => $ongkir > 0 ? 'courier' : 'pickup',
            'subtotal' => $subtotal,
            'shipping_cost' => $ongkir,
            'grand_total' => $subtotal + $ongkir,
            'status' => $status,
            'paid_at' => $sudahDibayar ? ($waktuBayar ?? $waktu) : null,
            'created_at' => $waktu,
        ]);
    }

    private function pengeluaran(float $nominal, string $tanggal, string $kategori = 'operational'): void
    {
        DB::table('expenses')->insert([
            'category' => $kategori,
            'title' => 'Pengeluaran uji '.$kategori,
            'amount' => $nominal,
            'expense_date' => $tanggal,
            'payment_method' => 'cash',
            'created_at' => $tanggal.' 08:00:00',
        ]);
    }

    private function laba(string $periode = '2026-03'): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->owner)
            ->getJson('/owner/api/laporan/laba-rugi?periode='.$periode);
    }

    // ---------- Hak akses ----------

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/owner/api/laporan/laba-rugi')->assertRedirect('/login');
        $this->get('/owner/laporan')->assertRedirect('/login');
        $this->get('/owner/laporan/cetak')->assertRedirect('/login');
    }

    public function test_kasir_dan_pelanggan_tidak_boleh_melihat_laba(): void
    {
        foreach ([$this->kasir, $this->pelanggan] as $user) {
            $this->actingAs($user)->getJson('/owner/api/laporan/laba-rugi')->assertForbidden();
            $this->actingAs($user)->get('/owner/laporan')->assertRedirect();
            $this->actingAs($user)->get('/owner/laporan/cetak')->assertRedirect();
        }
    }

    public function test_owner_boleh_melihat_laba_dan_halaman_laporan(): void
    {
        $this->laba()->assertOk()
            ->assertJsonPath('data.periode', '2026-03')
            ->assertJsonPath('data.label_periode', 'Maret 2026');

        $this->actingAs($this->owner)->get('/owner/laporan')
            ->assertOk()
            ->assertSee('Laba Rugi')
            ->assertSee('labaKotor');
    }

    public function test_struktur_json_laba_lengkap(): void
    {
        $json = $this->laba()->assertOk()->json('data');

        foreach ([
            'periode', 'label_periode', 'omzet', 'transaksi',
            'hpp', 'laba_kotor', 'operasional', 'karyawan',
            'lainnya', 'total_pengeluaran', 'laba_bersih',
        ] as $kunci) {
            $this->assertArrayHasKey($kunci, $json);
        }

        $this->assertArrayHasKey('bahan_baku', $json['hpp']);
        $this->assertArrayHasKey('kemasan', $json['hpp']);
        $this->assertArrayHasKey('total', $json['hpp']);
        $this->assertArrayHasKey('gaji', $json['karyawan']);
        $this->assertArrayHasKey('bonus_lembur', $json['karyawan']);
        $this->assertArrayHasKey('total', $json['karyawan']);
    }

    // ---------- Validasi & periode ----------

    public function test_format_periode_tidak_valid_ditolak(): void
    {
        foreach (['2026-13', '2026-00', '2026-3', 'Maret 2026', '2026-03-01'] as $periode) {
            $this->actingAs($this->owner)
                ->getJson('/owner/api/laporan/laba-rugi?periode='.$periode)
                ->assertStatus(422)
                ->assertJsonValidationErrors('periode');
        }
    }

    public function test_periode_default_adalah_bulan_berjalan(): void
    {
        $this->actingAs($this->owner)
            ->getJson('/owner/api/laporan/laba-rugi')
            ->assertOk()
            ->assertJsonPath('data.periode', '2026-03')
            ->assertJsonPath('data.label_periode', 'Maret 2026');
    }

    public function test_label_periode_memakai_nama_bulan_indonesia(): void
    {
        $this->laba('2026-01')->assertJsonPath('data.label_periode', 'Januari 2026');
        $this->laba('2026-08')->assertJsonPath('data.label_periode', 'Agustus 2026');
        $this->laba('2026-12')->assertJsonPath('data.label_periode', 'Desember 2026');
    }

    // ---------- Omzet ----------

    public function test_omzet_hanya_menghitung_pos_lunas(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->transaksiKasir(250000, '2026-03-03 09:00:00');
        $this->transaksiKasir(900000, '2026-03-04 09:00:00', 'cancelled');

        $json = $this->laba()->json('data');

        $this->assertEquals(350000, $json['omzet']);
        $this->assertSame(2, $json['transaksi']);
    }

    public function test_omzet_menjumlahkan_pos_dan_pesanan_online(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->pesananOnline(60000, '2026-03-02 13:00:00');

        $json = $this->laba()->json('data');

        $this->assertEquals(160000, $json['omzet']);
        $this->assertSame(2, $json['transaksi']);
    }

    public function test_pesanan_belum_dibayar_atau_batal_tidak_dihitung(): void
    {
        $this->pesananOnline(70000, '2026-03-05 10:00:00', 'unpaid', false);
        $this->pesananOnline(80000, '2026-03-05 11:00:00', 'cancelled', true);

        $json = $this->laba()->json('data');

        $this->assertEquals(0, $json['omzet']);
        $this->assertSame(0, $json['transaksi']);
    }

    public function test_pesanan_berbayar_processing_dan_completed_dihitung(): void
    {
        $this->pesananOnline(40000, '2026-03-06 10:00:00', 'processing');
        $this->pesananOnline(60000, '2026-03-06 11:00:00', 'completed');

        $json = $this->laba()->json('data');

        $this->assertEquals(100000, $json['omzet']);
        $this->assertSame(2, $json['transaksi']);
    }

    public function test_omzet_memakai_grand_total_termasuk_ongkir(): void
    {
        // Perilaku ter dokumentasi: omzet = grand_total (subtotal + ongkir).
        $this->pesananOnline(50000, '2026-03-07 10:00:00', 'completed', true, 10000);

        $this->assertEquals(60000, $this->laba()->json('data.omzet'));
    }

    public function test_transaksi_di_luar_periode_tidak_dihitung(): void
    {
        $this->transaksiKasir(50000, '2026-02-28 23:59:59');
        $this->transaksiKasir(100000, '2026-03-10 10:00:00');
        $this->transaksiKasir(75000, '2026-04-01 00:00:00');

        $json = $this->laba()->json('data');

        $this->assertEquals(100000, $json['omzet']);
        $this->assertSame(1, $json['transaksi']);
    }

    public function test_batas_awal_dan_akhir_bulan_termasuk(): void
    {
        $this->transaksiKasir(1000, '2026-03-01 00:00:00');
        $this->transaksiKasir(2000, '2026-03-31 23:59:59');
        $this->transaksiKasir(4000, '2026-02-28 23:59:59');
        $this->transaksiKasir(8000, '2026-04-01 00:00:00');

        $json = $this->laba()->json('data');

        $this->assertEquals(3000, $json['omzet']);
        $this->assertSame(2, $json['transaksi']);
    }

    public function test_pesanan_lintas_bulan_dihitung_pada_tanggal_dibuat(): void
    {
        // Dibuat 27 Feb, dibayar 1 Mar; mengikuti DATE(created_at) seperti rekap.
        $this->pesananOnline(75000, '2026-02-27 20:00:00', 'completed', true, 0, '2026-03-01 08:00:00');

        $this->assertEquals(0, $this->laba('2026-03')->json('data.omzet'));
        $this->assertEquals(75000, $this->laba('2026-02')->json('data.omzet'));
    }

    // ---------- HPP & laba kotor ----------

    public function test_hpp_menjumlahkan_bahan_baku_dan_kemasan(): void
    {
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');

        $json = $this->laba()->json('data');

        $this->assertEquals(100000, $json['hpp']['bahan_baku']);
        $this->assertEquals(50000, $json['hpp']['kemasan']);
        $this->assertEquals(150000, $json['hpp']['total']);
    }

    public function test_laba_kotor_adalah_omzet_dikurangi_hpp(): void
    {
        $this->transaksiKasir(1000000, '2026-03-02 09:00:00');
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');

        $this->assertEquals(850000, $this->laba()->json('data.laba_kotor'));
    }

    public function test_pengeluaran_operasional_dan_gaji_tidak_masuk_hpp(): void
    {
        $this->transaksiKasir(500000, '2026-03-02 09:00:00');
        $this->pengeluaran(75000, '2026-03-03', 'operational');
        $this->pengeluaran(200000, '2026-03-03', 'salary');
        $this->pengeluaran(25000, '2026-03-03', 'other');

        $json = $this->laba()->json('data');

        $this->assertEquals(0, $json['hpp']['total']);
        $this->assertEquals(500000, $json['laba_kotor']);
    }

    public function test_tanpa_pengeluaran_hpp_laba_kotor_sama_dengan_omzet(): void
    {
        // Keterbatasan yang didokumentasikan: HPP berasal dari pencatatan
        // pengeluaran, bukan harga pokok per item yang terjual.
        $this->transaksiKasir(300000, '2026-03-02 09:00:00');

        $json = $this->laba()->json('data');

        $this->assertEquals(0, $json['hpp']['total']);
        $this->assertEquals(300000, $json['laba_kotor']);
    }

    public function test_laba_kotor_bisa_negatif(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->pengeluaran(250000, '2026-03-03', 'raw_material');

        $json = $this->laba()->json('data');

        $this->assertEquals(-150000, $json['laba_kotor']);
        $this->assertEquals(-150000, $json['laba_bersih']);
    }

    // ---------- Operasional, karyawan, lainnya ----------

    public function test_operasional_hanya_kategori_operational(): void
    {
        $this->pengeluaran(30000, '2026-03-03', 'operational');
        $this->pengeluaran(50000, '2026-03-03', 'raw_material');

        $this->assertEquals(30000, $this->laba()->json('data.operasional'));
    }

    public function test_karyawan_menjumlahkan_gaji_bonus_dan_lembur(): void
    {
        $this->pengeluaran(200000, '2026-03-03', 'salary');
        $this->pengeluaran(20000, '2026-03-03', 'bonus');
        $this->pengeluaran(10000, '2026-03-03', 'overtime');

        $json = $this->laba()->json('data');

        $this->assertEquals(200000, $json['karyawan']['gaji']);
        $this->assertEquals(30000, $json['karyawan']['bonus_lembur']);
        $this->assertEquals(230000, $json['karyawan']['total']);
    }

    public function test_lainnya_hanya_kategori_other(): void
    {
        $this->pengeluaran(5000, '2026-03-03', 'other');
        $this->pengeluaran(9000, '2026-03-03', 'operational');

        $this->assertEquals(5000, $this->laba()->json('data.lainnya'));
    }

    public function test_total_pengeluaran_adalah_jumlah_semua_kategori(): void
    {
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');
        $this->pengeluaran(30000, '2026-03-04', 'operational');
        $this->pengeluaran(200000, '2026-03-04', 'salary');
        $this->pengeluaran(20000, '2026-03-05', 'bonus');
        $this->pengeluaran(10000, '2026-03-05', 'overtime');
        $this->pengeluaran(5000, '2026-03-06', 'other');

        $this->assertEquals(415000, $this->laba()->json('data.total_pengeluaran'));
    }

    public function test_laba_bersih_adalah_omzet_dikurangi_seluruh_pengeluaran(): void
    {
        $this->transaksiKasir(1000000, '2026-03-02 09:00:00');
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');
        $this->pengeluaran(30000, '2026-03-04', 'operational');
        $this->pengeluaran(200000, '2026-03-04', 'salary');
        $this->pengeluaran(20000, '2026-03-05', 'bonus');
        $this->pengeluaran(10000, '2026-03-05', 'overtime');
        $this->pengeluaran(5000, '2026-03-06', 'other');

        $json = $this->laba()->json('data');

        $this->assertEquals(850000, $json['laba_kotor']);
        $this->assertEquals(585000, $json['laba_bersih']);
        $this->assertEquals($json['omzet'] - $json['total_pengeluaran'], $json['laba_bersih']);
    }

    public function test_laba_bersih_bisa_negatif(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->pengeluaran(150000, '2026-03-03', 'operational');

        $json = $this->laba()->json('data');

        $this->assertEquals(-50000, $json['laba_bersih']);
    }

    public function test_pengeluaran_di_luar_periode_tidak_dihitung(): void
    {
        $this->pengeluaran(100000, '2026-02-28', 'raw_material');
        $this->pengeluaran(200000, '2026-03-10', 'raw_material');
        $this->pengeluaran(300000, '2026-04-01', 'raw_material');

        $json = $this->laba()->json('data');

        $this->assertEquals(200000, $json['hpp']['total']);
        $this->assertEquals(200000, $json['total_pengeluaran']);
    }

    public function test_pengeluaran_kategori_sama_dijumlahkan(): void
    {
        $this->pengeluaran(25000, '2026-03-03', 'raw_material');
        $this->pengeluaran(35000, '2026-03-04', 'raw_material');

        $this->assertEquals(60000, $this->laba()->json('data.hpp.bahan_baku'));
    }

    public function test_nominal_pecahan_dibulatkan_dua_angka_di_belakang_koma(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->pengeluaran(1234.56, '2026-03-03', 'raw_material');
        $this->pengeluaran(0.44, '2026-03-03', 'packaging');

        $json = $this->laba()->json('data');

        $this->assertEquals(1235, $json['hpp']['total']);
        $this->assertEquals(98765, $json['laba_kotor']);
    }

    public function test_tanpa_data_semua_nilai_nol(): void
    {
        $json = $this->laba()->json('data');

        $this->assertSame(0, $json['omzet']);
        $this->assertSame(0, $json['transaksi']);
        $this->assertSame(0, $json['hpp']['total']);
        $this->assertSame(0, $json['laba_kotor']);
        $this->assertSame(0, $json['operasional']);
        $this->assertSame(0, $json['karyawan']['total']);
        $this->assertSame(0, $json['lainnya']);
        $this->assertSame(0, $json['total_pengeluaran']);
        $this->assertSame(0, $json['laba_bersih']);
    }

    // ---------- Konsistensi ----------

    public function test_konsisten_dengan_total_rekap_bulan_berjalan(): void
    {
        $this->transaksiKasir(300000, '2026-03-02 09:00:00');
        $this->pesananOnline(150000, '2026-03-05 09:00:00');
        $this->pengeluaran(75000, '2026-03-06', 'operational');

        $laba = $this->laba()->json('data');

        $rekap = $this->actingAs($this->owner)
            ->getJson('/owner/api/laporan/pendapatan?rekap=bulanan&periode=2026-03')
            ->json('data');

        $maret = collect($rekap['baris'])->firstWhere('label', 'Mar 2026');

        $this->assertEquals($maret['omzet'], $laba['omzet']);
        $this->assertSame($maret['transaksi'], $laba['transaksi']);
        $this->assertEquals($maret['pengeluaran'], $laba['total_pengeluaran']);
        $this->assertEquals($maret['laba'], $laba['laba_bersih']);
    }

    public function test_konsisten_dengan_kartu_dashboard(): void
    {
        $this->transaksiKasir(300000, '2026-03-02 09:00:00');
        $this->pesananOnline(150000, '2026-03-05 09:00:00');
        $this->pengeluaran(75000, '2026-03-06', 'operational');
        $this->pengeluaran(25000, '2026-03-06', 'raw_material');

        $dashboard = $this->actingAs($this->owner)
            ->getJson('/owner/api/dashboard?periode=bulan')
            ->json('data');

        $laba = $this->laba()->json('data');

        $this->assertEquals($dashboard['omzet'], $laba['omzet']);
        $this->assertEquals($dashboard['pengeluaran'], $laba['total_pengeluaran']);
        $this->assertEquals($dashboard['laba'], $laba['laba_bersih']);
        $this->assertSame($dashboard['transaksi']['total'], $laba['transaksi']);
    }

    public function test_pratinjau_tanpa_login_bisa_membaca_laba(): void
    {
        $this->transaksiKasir(99000, '2026-03-11 10:00:00');

        $this->getJson('/frontend/owner/api/laporan/laba-rugi?periode=2026-03')
            ->assertOk()
            ->assertJsonPath('data.omzet', 99000);

        $this->get('/frontend/owner/laporan')
            ->assertOk()
            ->assertSee('Laba Rugi');
    }

    // ---------- Tampilan & halaman cetak ----------

    public function test_kartu_laba_rugi_punya_seluruh_elemen_tampilan(): void
    {
        $this->actingAs($this->owner)->get('/owner/laporan')
            ->assertOk()
            ->assertSee('id="labaRugi"', false)
            ->assertSee('id="labaPeriode"', false)
            ->assertSee('id="labaOmzet"', false)
            ->assertSee('id="labaBahanBaku"', false)
            ->assertSee('id="labaKemasan"', false)
            ->assertSee('id="labaKotor"', false)
            ->assertSee('id="labaOperasional"', false)
            ->assertSee('id="labaGaji"', false)
            ->assertSee('id="labaBonusLembur"', false)
            ->assertSee('id="labaBersih"', false)
            ->assertSee('LABA KOTOR')
            ->assertSee('LABA BERSIH');
    }

    public function test_halaman_cetak_menampilkan_rincian_laba(): void
    {
        $this->transaksiKasir(1000000, '2026-03-10 10:00:00');
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');
        $this->pengeluaran(30000, '2026-03-04', 'operational');
        $this->pengeluaran(200000, '2026-03-04', 'salary');
        $this->pengeluaran(20000, '2026-03-05', 'bonus');
        $this->pengeluaran(10000, '2026-03-05', 'overtime');
        $this->pengeluaran(5000, '2026-03-06', 'other');

        $this->actingAs($this->owner)
            ->get('/owner/laporan/cetak?rekap=mingguan&periode=2026-03')
            ->assertOk()
            ->assertSee('Laporan Keuangan Chikumi')
            ->assertSee('Periode Maret 2026')
            ->assertSee('Laba Rugi')
            ->assertSee('Bahan Baku')
            ->assertSee('Kemasan')
            ->assertSee('LABA KOTOR')
            ->assertSee('LABA BERSIH')
            ->assertSee('850.000')
            ->assertSee('585.000')
            ->assertSee('Lainnya');
    }

    public function test_halaman_cetak_menyembunyikan_lainnya_bila_nol(): void
    {
        $this->transaksiKasir(100000, '2026-03-10 10:00:00');

        $this->actingAs($this->owner)
            ->get('/owner/laporan/cetak?periode=2026-03')
            ->assertOk()
            ->assertDontSee('>Lainnya<', false);
    }

    public function test_halaman_cetak_menampilkan_laba_negatif(): void
    {
        $this->transaksiKasir(100000, '2026-03-10 10:00:00');
        $this->pengeluaran(250000, '2026-03-03', 'raw_material');

        $this->actingAs($this->owner)
            ->get('/owner/laporan/cetak?periode=2026-03')
            ->assertOk()
            ->assertSee('Rp -150.000');
    }

    public function test_tombol_export_mengarah_ke_halaman_cetak_dan_csv(): void
    {
        $this->actingAs($this->owner)->get('/owner/laporan')
            ->assertOk()
            ->assertSee('/laporan/cetak', false)
            ->assertSee('/laporan/export', false);
    }
}
