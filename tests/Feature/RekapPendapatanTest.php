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
 * [CHIK-138] Uji penerimaan rekap pendapatan per periode (PPL-02).
 *
 * Cakupan CHIK-136 (backend): GET /owner/api/laporan/pendapatan,
 * /laporan/laba-rugi, /laporan/export, dan halaman cetak.
 * Cakupan CHIK-137 (frontend) diuji terpisah lewat black-box browser.
 *
 * Semua nominal dihitung manual per tanggal agar hasil query tidak
 * sekadar "tidak error", tetapi angkanya benar.
 */
class RekapPendapatanTest extends TestCase
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
            'email' => 'owner.rekap@gmail.com',
            'phone_number' => '081200000031',
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
            'transaction_number' => 'POS-REKAP-'.$this->urutan,
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
        float $nominal,
        string $waktu,
        string $status = 'completed',
        bool $sudahDibayar = true
    ): void {
        $this->urutan++;

        DB::table('orders')->insert([
            'order_number' => 'ORD-REKAP-'.$this->urutan,
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => $nominal,
            'shipping_cost' => 0,
            'grand_total' => $nominal,
            'status' => $status,
            'paid_at' => $sudahDibayar ? $waktu : null,
            'created_at' => $waktu,
        ]);
    }

    private function pengeluaran(
        float $nominal,
        string $tanggal,
        string $kategori = 'operational'
    ): void {
        DB::table('expenses')->insert([
            'category' => $kategori,
            'title' => 'Pengeluaran uji '.$kategori,
            'amount' => $nominal,
            'expense_date' => $tanggal,
            'payment_method' => 'cash',
            'created_at' => $tanggal.' 08:00:00',
        ]);
    }

    private function rekap(string $mode = 'mingguan', string $periode = '2026-03'): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->owner)
            ->getJson("/owner/api/laporan/pendapatan?rekap={$mode}&periode={$periode}");
    }

    /** @return array<string, mixed> */
    private function baris(array $json, string $label): array
    {
        foreach ($json['data']['baris'] as $baris) {
            if ($baris['label'] === $label) {
                return $baris;
            }
        }

        $this->fail("Baris dengan label '{$label}' tidak ditemukan.");
    }

    // ---------- Hak akses ----------

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/owner/api/laporan/pendapatan')->assertRedirect('/login');
        $this->get('/owner/laporan')->assertRedirect('/login');
        $this->get('/owner/laporan/cetak')->assertRedirect('/login');
    }

    public function test_kasir_dan_pelanggan_tidak_boleh_mengakses_rekap(): void
    {
        foreach ([$this->kasir, $this->pelanggan] as $user) {
            $this->actingAs($user)->getJson('/owner/api/laporan/pendapatan')->assertForbidden();
            $this->actingAs($user)->getJson('/owner/api/laporan/laba-rugi')->assertForbidden();

            // Permintaan halaman (bukan JSON) dialihkan ke beranda dengan pesan error.
            $this->actingAs($user)->get('/owner/laporan')->assertRedirect();
            $this->actingAs($user)->get('/owner/laporan/cetak')->assertRedirect();
        }
    }

    public function test_owner_boleh_membuka_rekap_dan_halaman_laporan(): void
    {
        $this->actingAs($this->owner)->getJson('/owner/api/laporan/pendapatan')
            ->assertOk()
            ->assertJsonPath('data.rekap', 'mingguan')
            ->assertJsonPath('data.periode', '2026-03');

        $this->actingAs($this->owner)->get('/owner/laporan')
            ->assertOk()
            ->assertSee('Laporan Keuangan')
            ->assertSee('Export Excel');
    }

    // ---------- Validasi parameter ----------

    public function test_tiga_mode_rekap_diterima(): void
    {
        foreach (['harian', 'mingguan', 'bulanan'] as $mode) {
            $this->rekap($mode)->assertOk()->assertJsonPath('data.rekap', $mode);
        }
    }

    public function test_mode_rekap_tidak_dikenal_ditolak(): void
    {
        $this->rekap('triwulan')->assertStatus(422)->assertJsonValidationErrors('rekap');
    }

    public function test_format_periode_salah_ditolak(): void
    {
        foreach (['2026-13', '2026-00', '2026-3', 'Maret 2026', '2026-03-01'] as $periode) {
            $this->actingAs($this->owner)
                ->getJson('/owner/api/laporan/pendapatan?periode='.$periode)
                ->assertStatus(422)
                ->assertJsonValidationErrors('periode');
        }
    }

    public function test_periode_default_adalah_bulan_berjalan(): void
    {
        $res = $this->actingAs($this->owner)->getJson('/owner/api/laporan/pendapatan')->assertOk();

        $res->assertJsonPath('data.periode', '2026-03');
        $this->assertCount(5, $res->json('data.baris'));
    }

    // ---------- Rekap harian ----------

    public function test_rekap_harian_memuat_setiap_tanggal_dalam_bulan(): void
    {
        $res = $this->rekap('harian')->assertOk();

        $this->assertCount(31, $res->json('data.baris'));
        $this->assertSame('1 Mar', $res->json('data.baris.0.label'));
        $this->assertSame('31 Mar', $res->json('data.baris.30.label'));
    }

    public function test_rekap_harian_menjumlahkan_kasir_dan_pesanan_online_per_tanggal(): void
    {
        $this->transaksiKasir(100000, '2026-03-02 09:00:00');
        $this->pesananOnline(50000, '2026-03-02 13:00:00');

        $baris = $this->baris($this->rekap('harian')->json(), '2 Mar');

        $this->assertSame(2, $baris['transaksi']);
        $this->assertEquals(150000, $baris['omzet']);
        $this->assertEquals(0, $baris['pengeluaran']);
        $this->assertEquals(150000, $baris['laba']);
    }

    public function test_rekap_harian_memasukkan_pengeluaran_dan_laba_bisa_negatif(): void
    {
        $this->transaksiKasir(20000, '2026-03-04 09:00:00');
        $this->pengeluaran(50000, '2026-03-04');

        $baris = $this->baris($this->rekap('harian')->json(), '4 Mar');

        $this->assertEquals(20000, $baris['omzet']);
        $this->assertEquals(50000, $baris['pengeluaran']);
        $this->assertEquals(-30000, $baris['laba']);
    }

    public function test_rekap_harian_tanggal_tanpa_transaksi_bernilai_nol(): void
    {
        $baris = $this->baris($this->rekap('harian')->json(), '10 Mar');

        $this->assertSame(0, $baris['transaksi']);
        $this->assertEquals(0, $baris['omzet']);
        $this->assertEquals(0, $baris['pengeluaran']);
        $this->assertEquals(0, $baris['laba']);
    }

    public function test_hanya_pos_lunas_dan_pesanan_berbayar_bukan_batal_yang_dihitung(): void
    {
        $this->transaksiKasir(100000, '2026-03-06 09:00:00', 'pending');
        $this->pesananOnline(200000, '2026-03-06 10:00:00', 'unpaid', false);
        $this->pesananOnline(300000, '2026-03-06 11:00:00', 'cancelled', true);

        $baris = $this->baris($this->rekap('harian')->json(), '6 Mar');

        $this->assertSame(0, $baris['transaksi']);
        $this->assertEquals(0, $baris['omzet']);
    }

    public function test_batas_awal_dan_akhir_bulan_termasuk_bulan_lain_tidak(): void
    {
        $this->transaksiKasir(1000, '2026-03-01 00:00:00');
        $this->transaksiKasir(2000, '2026-03-31 23:59:59');
        $this->transaksiKasir(4000, '2026-02-28 23:59:59');
        $this->transaksiKasir(8000, '2026-04-01 00:00:00');

        $json = $this->rekap('harian')->json();

        $this->assertEquals(1000, $this->baris($json, '1 Mar')['omzet']);
        $this->assertEquals(2000, $this->baris($json, '31 Mar')['omzet']);
        $this->assertEquals(3000, $json['data']['total']['omzet']);
        $this->assertSame(2, $json['data']['total']['transaksi']);
    }

    public function test_pesanan_dihitung_pada_tanggal_dibuat_bukan_tanggal_bayar(): void
    {
        // Dibuat 27 Feb, dibayar 1 Mar. Mengikuti DATE(created_at) sesuai dokumentasi.
        $this->urutan++;
        DB::table('orders')->insert([
            'order_number' => 'ORD-REKAP-LINTAS',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 75000,
            'shipping_cost' => 0,
            'grand_total' => 75000,
            'status' => 'completed',
            'paid_at' => '2026-03-01 08:00:00',
            'created_at' => '2026-02-27 20:00:00',
        ]);

        $maret = $this->rekap('harian')->json();
        $this->assertEquals(0, $this->baris($maret, '1 Mar')['omzet']);

        $februari = $this->rekap('harian', '2026-02')->json();
        $this->assertEquals(75000, $this->baris($februari, '27 Feb')['omzet']);
    }

    // ---------- Rekap mingguan ----------

    public function test_rekap_mingguan_bulan_31_hari_memiliki_lima_baris(): void
    {
        $res = $this->rekap('mingguan')->assertOk();

        $this->assertSame(
            ['1–7 Mar', '8–14 Mar', '15–21 Mar', '22–28 Mar', '29–31 Mar'],
            array_column($res->json('data.baris'), 'label')
        );
    }

    public function test_rekap_mingguan_menjumlahkan_tanggal_dalam_rentang(): void
    {
        $this->transaksiKasir(100, '2026-03-07 10:00:00');
        $this->transaksiKasir(200, '2026-03-08 10:00:00');
        $this->transaksiKasir(300, '2026-03-14 10:00:00');
        $this->transaksiKasir(400, '2026-03-15 10:00:00');
        $this->transaksiKasir(500, '2026-03-31 10:00:00');

        $json = $this->rekap('mingguan')->json();

        $this->assertEquals(100, $this->baris($json, '1–7 Mar')['omzet']);
        $this->assertEquals(500, $this->baris($json, '8–14 Mar')['omzet']);
        $this->assertEquals(400, $this->baris($json, '15–21 Mar')['omzet']);
        $this->assertEquals(0, $this->baris($json, '22–28 Mar')['omzet']);
        $this->assertEquals(500, $this->baris($json, '29–31 Mar')['omzet']);
        $this->assertEquals(1500, $json['data']['total']['omzet']);
    }

    public function test_rekap_mingguan_bulan_28_hari_berhenti_di_minggu_keempat(): void
    {
        $json = $this->rekap('mingguan', '2026-02')->json();

        $this->assertSame(
            ['1–7 Feb', '8–14 Feb', '15–21 Feb', '22–28 Feb'],
            array_column($json['data']['baris'], 'label')
        );
    }

    public function test_rekap_mingguan_bulan_30_hari_berakhir_29_sampai_30(): void
    {
        $json = $this->rekap('mingguan', '2026-04')->json();

        $this->assertCount(5, $json['data']['baris']);
        $this->assertSame('29–30 Apr', $json['data']['baris'][4]['label']);
    }

    public function test_rekap_mingguan_bulan_29_hari_memakai_label_satu_tanggal(): void
    {
        $json = $this->rekap('mingguan', '2028-02')->json();

        $this->assertCount(5, $json['data']['baris']);
        $this->assertSame('29 Feb', $json['data']['baris'][4]['label']);
    }

    // ---------- Rekap bulanan ----------

    public function test_rekap_bulanan_memuat_bulan_terpilih_dan_dua_bulan_sebelumnya(): void
    {
        $json = $this->rekap('bulanan')->json();

        $this->assertSame(
            ['Jan 2026', 'Feb 2026', 'Mar 2026'],
            array_column($json['data']['baris'], 'label')
        );
    }

    public function test_rekap_bulanan_melewati_batas_tahun(): void
    {
        $json = $this->rekap('bulanan', '2026-01')->json();

        $this->assertSame(
            ['Nov 2025', 'Des 2025', 'Jan 2026'],
            array_column($json['data']['baris'], 'label')
        );
    }

    public function test_rekap_bulanan_menjumlahkan_omzet_dan_pengeluaran_tiap_bulan(): void
    {
        $this->transaksiKasir(500, '2026-01-10 10:00:00');
        $this->transaksiKasir(700, '2026-02-10 10:00:00');
        $this->pengeluaran(200, '2026-02-11');
        $this->transaksiKasir(900, '2026-03-10 10:00:00');

        $json = $this->rekap('bulanan')->json();

        $januari = $this->baris($json, 'Jan 2026');
        $februari = $this->baris($json, 'Feb 2026');
        $maret = $this->baris($json, 'Mar 2026');

        $this->assertEquals(500, $januari['omzet']);
        $this->assertEquals(0, $januari['pengeluaran']);
        $this->assertEquals(700, $februari['omzet']);
        $this->assertEquals(200, $februari['pengeluaran']);
        $this->assertEquals(500, $februari['laba']);
        $this->assertEquals(900, $maret['omzet']);

        $this->assertEquals(2100, $json['data']['total']['omzet']);
        $this->assertEquals(200, $json['data']['total']['pengeluaran']);
        $this->assertEquals(1900, $json['data']['total']['laba']);
        $this->assertSame(3, $json['data']['total']['transaksi']);
    }

    // ---------- Total ----------

    public function test_total_selalu_sama_dengan_jumlah_baris(): void
    {
        $this->transaksiKasir(100, '2026-03-03 09:00:00');
        $this->pesananOnline(250, '2026-03-17 09:00:00');
        $this->pengeluaran(50, '2026-03-20');
        $this->transaksiKasir(400, '2026-02-05 09:00:00');

        foreach (['harian', 'mingguan', 'bulanan'] as $mode) {
            $json = $this->rekap($mode)->json();
            $baris = collect($json['data']['baris']);

            $this->assertEquals($baris->sum('omzet'), $json['data']['total']['omzet'], $mode);
            $this->assertEquals($baris->sum('pengeluaran'), $json['data']['total']['pengeluaran'], $mode);
            $this->assertEquals($baris->sum('laba'), $json['data']['total']['laba'], $mode);
            $this->assertSame($baris->sum('transaksi'), $json['data']['total']['transaksi'], $mode);
        }
    }

    public function test_rekap_harian_dan_mingguan_memiliki_total_sama(): void
    {
        $this->transaksiKasir(120000, '2026-03-09 09:00:00');
        $this->pesananOnline(80000, '2026-03-09 12:00:00');
        $this->pengeluaran(35000, '2026-03-09');

        $harian = $this->rekap('harian')->json('data.total');
        $mingguan = $this->rekap('mingguan')->json('data.total');

        $this->assertEquals($harian, $mingguan);
    }

    // ---------- Konsistensi dengan dashboard & laba rugi ----------

    public function test_rekap_bulan_berjalan_konsisten_dengan_dashboard(): void
    {
        $this->transaksiKasir(300000, '2026-03-02 09:00:00');
        $this->pesananOnline(150000, '2026-03-05 09:00:00');
        $this->pengeluaran(75000, '2026-03-06');
        $this->pengeluaran(25000, '2026-02-06');

        $dashboard = $this->actingAs($this->owner)
            ->getJson('/owner/api/dashboard?periode=bulan')
            ->assertOk()
            ->json('data');

        $maret = $this->baris($this->rekap('bulanan')->json(), 'Mar 2026');

        $this->assertEquals($dashboard['omzet'], $maret['omzet']);
        $this->assertEquals($dashboard['pengeluaran'], $maret['pengeluaran']);
        $this->assertEquals($dashboard['laba'], $maret['laba']);
        $this->assertSame($dashboard['transaksi']['total'], $maret['transaksi']);
    }

    public function test_laba_rugi_sesuai_rumus_dan_sama_dengan_total_rekap(): void
    {
        $this->transaksiKasir(1000000, '2026-03-02 09:00:00');
        $this->pengeluaran(100000, '2026-03-03', 'raw_material');
        $this->pengeluaran(50000, '2026-03-03', 'packaging');
        $this->pengeluaran(30000, '2026-03-04', 'operational');
        $this->pengeluaran(200000, '2026-03-04', 'salary');
        $this->pengeluaran(20000, '2026-03-05', 'bonus');
        $this->pengeluaran(10000, '2026-03-05', 'overtime');
        $this->pengeluaran(5000, '2026-03-06', 'other');

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/laporan/laba-rugi?periode=2026-03')
            ->assertOk();

        $res->assertJsonPath('data.label_periode', 'Maret 2026')
            ->assertJsonPath('data.omzet', 1000000)
            ->assertJsonPath('data.hpp.bahan_baku', 100000)
            ->assertJsonPath('data.hpp.kemasan', 50000)
            ->assertJsonPath('data.hpp.total', 150000)
            ->assertJsonPath('data.laba_kotor', 850000)
            ->assertJsonPath('data.operasional', 30000)
            ->assertJsonPath('data.karyawan.gaji', 200000)
            ->assertJsonPath('data.karyawan.bonus_lembur', 30000)
            ->assertJsonPath('data.karyawan.total', 230000)
            ->assertJsonPath('data.lainnya', 5000)
            ->assertJsonPath('data.total_pengeluaran', 415000)
            ->assertJsonPath('data.laba_bersih', 585000);

        $this->assertEquals(585000, $this->rekap('mingguan')->json('data.total.laba'));
    }

    // ---------- Export CSV ----------

    public function test_export_csv_berisi_baris_dan_total(): void
    {
        $this->transaksiKasir(125000, '2026-03-09 09:00:00');
        $this->pengeluaran(25000, '2026-03-09');

        $res = $this->actingAs($this->owner)
            ->get('/owner/api/laporan/export?rekap=mingguan&periode=2026-03')
            ->assertOk();

        $this->assertStringContainsString('text/csv', $res->headers->get('content-type'));
        $this->assertStringContainsString(
            'rekap-pendapatan-mingguan-2026-03.csv',
            (string) $res->headers->get('content-disposition')
        );

        $csv = $res->streamedContent();
        $this->assertStringContainsString('Periode,Transaksi,Omzet,Pengeluaran,"Laba Bersih"', $csv);
        $this->assertStringContainsString('8–14 Mar', $csv);
        $this->assertStringContainsString('Total,1,125000,25000,100000', $csv);
    }

    public function test_export_mengikuti_mode_rekap_dan_periode(): void
    {
        $csv = $this->actingAs($this->owner)
            ->get('/owner/api/laporan/export?rekap=harian&periode=2026-02')
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('1 Feb', $csv);
        $this->assertStringContainsString('28 Feb', $csv);
        $this->assertStringNotContainsString('1 Mar', $csv);
    }

    public function test_export_tanpa_login_dialihkan(): void
    {
        $this->get('/owner/api/laporan/export')->assertRedirect('/login');
    }

    // ---------- Halaman cetak ----------

    public function test_halaman_cetak_menampilkan_periode_dan_mode_rekap(): void
    {
        $this->transaksiKasir(150000, '2026-03-10 10:00:00');

        $this->actingAs($this->owner)
            ->get('/owner/laporan/cetak?rekap=harian&periode=2026-03')
            ->assertOk()
            ->assertSee('Laporan Keuangan Chikumi')
            ->assertSee('Periode Maret 2026')
            ->assertSee('Rekap Harian')
            ->assertSee('150.000');
    }

    // ---------- Pratinjau tanpa login ----------

    public function test_pratinjau_tanpa_login_bisa_membaca_rekap_dan_halaman(): void
    {
        $this->transaksiKasir(99000, '2026-03-11 10:00:00');

        $this->getJson('/frontend/owner/api/laporan/pendapatan?rekap=harian&periode=2026-03')
            ->assertOk()
            ->assertJsonPath('data.rekap', 'harian');

        $this->get('/frontend/owner/laporan')
            ->assertOk()
            ->assertSee('Laporan Keuangan');
    }
}
