<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-135] Uji penerimaan pencatatan pengeluaran usaha (PPL-03).
 *
 * Cakupan CHIK-133 (backend): CRUD /owner/api/pengeluaran + kategori,
 * integrasi ke buku transaksi owner (LedgerController) dan ringkasan dashboard.
 * Cakupan CHIK-134 (frontend) diuji terpisah lewat black-box browser.
 */
class PencatatanPengeluaranTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private User $owner;

    private User $kasir;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatDataChikumi();
        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.pengeluaran@gmail.com',
            'phone_number' => '081200000021',
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

    /** @return array<string, mixed> */
    private function dataPengeluaran(array $ubah = []): array
    {
        return array_merge([
            'expense_date' => now()->toDateString(),
            'amount' => 50000,
            'category' => 'operational',
            'payment_method' => 'cash',
            'title' => 'Listrik warung',
        ], $ubah);
    }

    private function catat(array $ubah = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->owner)
            ->postJson('/owner/api/pengeluaran', $this->dataPengeluaran($ubah));
    }

    private function buatTransaksiKasir(float $nominal = 100000, ?string $tanggal = null): void
    {
        DB::table('pos_transactions')->insert([
            'transaction_number' => 'POS-UJI-'.uniqid(),
            'cashier_id' => $this->kasir->id,
            'total_amount' => $nominal,
            'payment_method' => 'cash',
            'amount_paid' => $nominal,
            'change_amount' => 0,
            'status' => 'paid',
            'created_at' => $tanggal ?? now(),
        ]);
    }

    // ---------- Hak akses ----------

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/owner/api/pengeluaran')->assertRedirect('/login');
        $this->post('/owner/api/pengeluaran', $this->dataPengeluaran())->assertRedirect('/login');
    }

    public function test_kasir_dan_pelanggan_tidak_boleh_mengakses(): void
    {
        $this->actingAs($this->kasir)->getJson('/owner/api/pengeluaran')->assertForbidden();
        $this->actingAs($this->pelanggan)->postJson('/owner/api/pengeluaran', $this->dataPengeluaran())
            ->assertForbidden();
        $this->actingAs($this->kasir)->getJson('/owner/api/pengeluaran/kategori')->assertForbidden();
    }

    public function test_owner_boleh_membuka_riwayat_dan_kategori(): void
    {
        $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran')
            ->assertOk()
            ->assertJsonPath('data.ringkasan.jumlah', 0);

        $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran/kategori')->assertOk();
    }

    // ---------- Kategori & metode ----------

    public function test_daftar_kategori_memuat_tujuh_kategori_dan_tiga_metode(): void
    {
        $res = $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran/kategori')->assertOk();

        // Endpoint ini mengembalikan `data[]` di level atas (bukan `data.data`),
        // berbeda dari endpoint pengeluaran lain yang memakai `{message, data}`.
        $kode = collect($res->json('data'))->pluck('kode')->all();
        $this->assertSame(
            ['raw_material', 'packaging', 'operational', 'salary', 'bonus', 'overtime', 'other'],
            $kode
        );

        $metode = collect($res->json('metode'))->pluck('kode')->all();
        $this->assertSame(['cash', 'transfer', 'qris'], $metode);
    }

    // ---------- Simpan pengeluaran ----------

    public function test_pengeluaran_valid_tersimpan_dan_mengembalikan_201(): void
    {
        $res = $this->catat()->assertCreated();

        $res->assertJsonPath('message', 'Pengeluaran berhasil dicatat.')
            ->assertJsonPath('data.judul', 'Listrik warung')
            ->assertJsonPath('data.kategori', 'operational')
            ->assertJsonPath('data.kategori_label', 'Operasional')
            ->assertJsonPath('data.metode', 'cash')
            ->assertJsonPath('data.metode_label', 'Tunai')
            ->assertJsonPath('data.dicatat_oleh', 'Owner Uji');

        $this->assertEquals(50000, $res->json('data.nominal'));

        $expense = Expense::firstOrFail();
        $this->assertSame($this->owner->id, (int) $expense->created_by);
        $this->assertSame('cash', $expense->payment_method);
        $this->assertSame('50000.00', (string) $expense->amount);
    }

    public function test_kategori_kemasan_dan_metode_transfer_tersimpan(): void
    {
        $this->catat([
            'category' => 'packaging',
            'payment_method' => 'transfer',
            'notes' => 'Beli 200 box makanan',
        ])->assertCreated()
            ->assertJsonPath('data.kategori_label', 'Kemasan')
            ->assertJsonPath('data.metode_label', 'Transfer')
            ->assertJsonPath('data.catatan', 'Beli 200 box makanan');

        $this->assertSame(1, Expense::where('category', 'packaging')->count());
    }

    public function test_metode_bayar_default_tunai_bila_tidak_dikirim(): void
    {
        $data = $this->dataPengeluaran();
        unset($data['payment_method']);

        $res = $this->actingAs($this->owner)->postJson('/owner/api/pengeluaran', $data)->assertCreated();

        $res->assertJsonPath('data.metode', 'cash');
        $this->assertSame('cash', Expense::firstOrFail()->payment_method);
    }

    public function test_menolak_nominal_nol_negatif_dan_bukan_angka(): void
    {
        foreach ([0, -5000, 'lima puluh ribu'] as $nominal) {
            $this->catat(['amount' => $nominal])
                ->assertStatus(422)
                ->assertJsonValidationErrors('amount');
        }

        $this->assertSame(0, Expense::count());
    }

    public function test_menolak_kategori_dan_metode_yang_tidak_dikenal(): void
    {
        $this->catat(['category' => 'liburan'])->assertStatus(422)->assertJsonValidationErrors('category');
        $this->catat(['payment_method' => 'bitcoin'])->assertStatus(422)->assertJsonValidationErrors('payment_method');

        $this->assertSame(0, Expense::count());
    }

    public function test_menolak_judul_kosong_dan_lebih_dari_255_karakter(): void
    {
        $this->catat(['title' => ''])->assertStatus(422)->assertJsonValidationErrors('title');
        $this->catat(['title' => str_repeat('a', 256)])->assertStatus(422)->assertJsonValidationErrors('title');

        $this->catat(['title' => str_repeat('a', 255)])->assertCreated();
    }

    public function test_menolak_tanggal_kosong_atau_bukan_tanggal(): void
    {
        $this->catat(['expense_date' => ''])->assertStatus(422)->assertJsonValidationErrors('expense_date');
        $this->catat(['expense_date' => 'bukan tanggal'])->assertStatus(422)->assertJsonValidationErrors('expense_date');

        $this->assertSame(0, Expense::count());
    }

    public function test_format_tanggal_di_luar_iso_diterima_dan_dinormalisasi(): void
    {
        // Aturan validasi `date` meloloskan d-m-Y; model cast mengubahnya jadi Y-m-d.
        $this->catat(['expense_date' => '30-10-2026'])->assertCreated();

        $this->assertSame(1, Expense::whereDate('expense_date', '2026-10-30')->count());
    }

    public function test_tanggal_di_masa_depan_masih_diterima(): void
    {
        // Tidak ada aturan before_or_equal:today — dicatat sebagai temuan perilaku.
        $this->catat(['expense_date' => now()->addYear()->toDateString()])->assertCreated();

        $this->assertSame(1, Expense::where('expense_date', '>', now()->toDateString())->count());
    }

    // ---------- Riwayat pengeluaran ----------

    public function test_riwayat_urut_tanggal_terbaru_dan_ringkasan_benar(): void
    {
        $this->catat(['expense_date' => now()->subDays(2)->toDateString(), 'amount' => 20000, 'title' => 'Lama']);
        $this->catat(['expense_date' => now()->toDateString(), 'amount' => 50000, 'title' => 'Baru']);

        $res = $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran')->assertOk();

        $res->assertJsonPath('data.pengeluaran.0.judul', 'Baru')
            ->assertJsonPath('data.pengeluaran.1.judul', 'Lama')
            ->assertJsonPath('data.ringkasan.jumlah', 2);

        $this->assertEquals(70000, $res->json('data.ringkasan.total'));
    }

    public function test_filter_periode_dan_kategori(): void
    {
        $bulanLalu = now()->subMonthNoOverflow()->startOfMonth()->addDays(5);
        $this->catat(['expense_date' => $bulanLalu->toDateString(), 'category' => 'salary', 'amount' => 900000, 'title' => 'Gaji lama']);
        $this->catat(['expense_date' => now()->toDateString(), 'category' => 'salary', 'amount' => 1000000, 'title' => 'Gaji kini']);
        $this->catat(['expense_date' => now()->toDateString(), 'category' => 'packaging', 'amount' => 30000, 'title' => 'Kemasan']);

        $periode = now()->format('Y-m');
        $res = $this->actingAs($this->owner)->getJson("/owner/api/pengeluaran?periode={$periode}")->assertOk();
        $res->assertJsonPath('data.ringkasan.jumlah', 2);
        $this->assertEquals(1030000, $res->json('data.ringkasan.total'));

        $gaji = $this->actingAs($this->owner)
            ->getJson("/owner/api/pengeluaran?periode={$periode}&kategori=salary")
            ->assertOk();
        $gaji->assertJsonPath('data.ringkasan.jumlah', 1);
        $gaji->assertJsonPath('data.pengeluaran.0.judul', 'Gaji kini');
    }

    public function test_paginasi_memakai_parameter_page_bukan_halaman(): void
    {
        foreach (range(1, 5) as $i) {
            $this->catat(['title' => "Pengeluaran {$i}", 'amount' => $i * 10000]);
        }

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/pengeluaran?per_halaman=2&page=3')
            ->assertOk();

        $res->assertJsonPath('data.meta.halaman', 3)
            ->assertJsonPath('data.meta.per_halaman', 2)
            ->assertJsonPath('data.meta.total', 5)
            ->assertJsonPath('data.meta.total_halaman', 3)
            ->assertJsonCount(1, 'data.pengeluaran');

        // Dokumen API menyebut `halaman`; parameter itu diabaikan paginator Laravel.
        $this->actingAs($this->owner)
            ->getJson('/owner/api/pengeluaran?per_halaman=2&halaman=3')
            ->assertOk()
            ->assertJsonPath('data.meta.halaman', 1);
    }

    public function test_menolak_periode_salah_dan_per_halaman_di_atas_batas(): void
    {
        $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran?periode=2026-13')
            ->assertStatus(422)->assertJsonValidationErrors('periode');

        $this->actingAs($this->owner)->getJson('/owner/api/pengeluaran?per_halaman=101')
            ->assertStatus(422)->assertJsonValidationErrors('per_halaman');
    }

    // ---------- Ubah & hapus ----------

    public function test_pengeluaran_bisa_diubah(): void
    {
        $id = $this->catat()->assertCreated()->json('data.id');

        $this->actingAs($this->owner)
            ->putJson("/owner/api/pengeluaran/{$id}", $this->dataPengeluaran([
                'title' => 'Listrik sudah dikoreksi',
                'amount' => 75000,
                'category' => 'other',
                'payment_method' => 'qris',
            ]))
            ->assertOk()
            ->assertJsonPath('message', 'Pengeluaran berhasil diperbarui.')
            ->assertJsonPath('data.judul', 'Listrik sudah dikoreksi')
            ->assertJsonPath('data.kategori_label', 'Lainnya')
            ->assertJsonPath('data.metode_label', 'QRIS');

        $expense = Expense::findOrFail($id);
        $this->assertSame('75000.00', (string) $expense->amount);
        $this->assertSame('other', $expense->category);
    }

    public function test_ubah_dengan_data_tidak_valid_ditolak(): void
    {
        $id = $this->catat()->json('data.id');

        $this->actingAs($this->owner)
            ->putJson("/owner/api/pengeluaran/{$id}", $this->dataPengeluaran(['amount' => 0]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $this->assertSame('50000.00', (string) Expense::findOrFail($id)->amount);
    }

    public function test_pengeluaran_bisa_dihapus(): void
    {
        $id = $this->catat()->json('data.id');

        $this->actingAs($this->owner)->deleteJson("/owner/api/pengeluaran/{$id}")
            ->assertOk()
            ->assertJsonPath('message', 'Pengeluaran berhasil dihapus.');

        $this->assertNull(Expense::find($id));
    }

    public function test_id_tidak_ada_menghasilkan_404(): void
    {
        $this->actingAs($this->owner)->putJson('/owner/api/pengeluaran/99999', $this->dataPengeluaran())
            ->assertNotFound();
        $this->actingAs($this->owner)->deleteJson('/owner/api/pengeluaran/99999')->assertNotFound();
    }

    // ---------- Integrasi buku transaksi & dashboard ----------

    public function test_pengeluaran_muncul_di_buku_transaksi_sebagai_uang_keluar(): void
    {
        $this->catat(['title' => 'Gas elpiji', 'amount' => 25000, 'category' => 'operational']);
        $this->buatTransaksiKasir(100000);

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/transaksi?periode='.now()->format('Y-m'))
            ->assertOk();

        $keluar = collect($res->json('data.transaksi'))->firstWhere('jenis', 'keluar');
        $this->assertNotNull($keluar);
        $this->assertSame('Gas elpiji', $keluar['keterangan']);
        $this->assertSame('Operasional', $keluar['kategori_label']);
        $this->assertSame('Tunai', $keluar['metode_label']);
        $this->assertEquals(25000, $keluar['nominal']);

        $res->assertJsonPath('data.ringkasan.masuk', 100000)
            ->assertJsonPath('data.ringkasan.keluar', 25000)
            ->assertJsonPath('data.ringkasan.selisih', 75000)
            ->assertJsonPath('data.ringkasan.jumlah_keluar', 1);
    }

    public function test_filter_jenis_dan_kategori_di_buku_transaksi(): void
    {
        $this->catat(['title' => 'Kemasan', 'amount' => 30000, 'category' => 'packaging']);
        $this->buatTransaksiKasir(100000);

        $periode = now()->format('Y-m');

        $keluar = $this->actingAs($this->owner)
            ->getJson("/owner/api/transaksi?periode={$periode}&jenis=keluar")
            ->assertOk();
        $this->assertCount(1, $keluar->json('data.transaksi'));
        $this->assertSame('keluar', $keluar->json('data.transaksi.0.jenis'));

        $masuk = $this->actingAs($this->owner)
            ->getJson("/owner/api/transaksi?periode={$periode}&jenis=masuk")
            ->assertOk();
        $this->assertCount(1, $masuk->json('data.transaksi'));
        $this->assertSame('masuk', $masuk->json('data.transaksi.0.jenis'));

        $kemasan = $this->actingAs($this->owner)
            ->getJson("/owner/api/transaksi?periode={$periode}&kategori=packaging")
            ->assertOk();
        $this->assertCount(1, $kemasan->json('data.transaksi'));
        $kemasan->assertJsonPath('data.transaksi.0.kategori_label', 'Kemasan');
    }

    public function test_pengeluaran_bulan_lain_tidak_masuk_periode_berjalan(): void
    {
        $this->catat(['expense_date' => now()->subMonthNoOverflow()->startOfMonth()->addDays(3)->toDateString()]);

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/transaksi?periode='.now()->format('Y-m'))
            ->assertOk();

        $this->assertCount(0, $res->json('data.transaksi'));
        $res->assertJsonPath('data.ringkasan.keluar', 0);
    }

    public function test_export_csv_memuat_pengeluaran(): void
    {
        $this->catat(['title' => 'Servis blender', 'amount' => 40000, 'category' => 'other']);

        $res = $this->actingAs($this->owner)
            ->get('/owner/api/transaksi/export?periode='.now()->format('Y-m'));

        $res->assertOk();
        $csv = $res->streamedContent();

        $this->assertStringContainsString('Servis blender', $csv);
        $this->assertStringContainsString('Keluar', $csv);
        $this->assertStringContainsString('40000', $csv);
    }

    public function test_ringkasan_dashboard_menghitung_pengeluaran_dan_laba(): void
    {
        $this->buatTransaksiKasir(200000);
        $this->catat(['amount' => 50000]);

        $res = $this->actingAs($this->owner)->getJson('/owner/api/dashboard?periode=bulan')->assertOk();

        $this->assertEquals(200000, $res->json('data.omzet'));
        $this->assertEquals(50000, $res->json('data.pengeluaran'));
        $this->assertEquals(150000, $res->json('data.laba'));
    }

    // ---------- Pratinjau tanpa login ----------

    public function test_pratinjau_hanya_bisa_membaca(): void
    {
        $this->catat();

        $this->getJson('/frontend/owner/api/pengeluaran')
            ->assertOk()
            ->assertJsonPath('data.ringkasan.jumlah', 1);

        $this->postJson('/frontend/owner/api/pengeluaran', $this->dataPengeluaran())
            ->assertStatus(405);
    }
}
