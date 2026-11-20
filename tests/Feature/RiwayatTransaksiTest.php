<?php

namespace Tests\Feature;

use App\Models\PosTransaction;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-108] Uji penerimaan halaman riwayat transaksi kasir (CHIK-107).
 *
 * Endpoint: GET /kasir/riwayat (role owner,cashier) dan
 * GET /frontend/kasir/riwayat (pratinjau tanpa login).
 *
 * Catatan: per commit 4898fb5 halaman masih berupa mockup statis
 * (data contoh di Blade, tanpa JS maupun API riwayat). Tes di bawah
 * mengunci perilaku akses & render yang ada sekarang; kriteria
 * "menampilkan transaksi dari database" belum terpenuhi dan ditandai
 * lewat tes yang di-skip (lihat docs/LAPORAN-TESTING-RIWAYAT-TRANSAKSI.md).
 */
class RiwayatTransaksiTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $kasir;

    private User $pelanggan;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->d = $this->buatDataChikumi();
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();
        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.riwayat@gmail.com',
            'phone_number' => '081200000011',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
    }

    private function buatSkemaPesanan(): void
    {
        // Halaman belum membaca tabel ini, tetapi skema disiapkan agar
        // tes siap dipakai saat riwayat disambungkan ke database.
        if (Schema::hasTable('orders')) {
            return;
        }

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
    }

    // ---------- Hak akses ----------

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/kasir/riwayat')->assertRedirect('/login');
    }

    public function test_kasir_bisa_membuka_halaman_riwayat(): void
    {
        $this->actingAs($this->kasir)
            ->get('/kasir/riwayat')
            ->assertOk()
            ->assertSee('Riwayat Transaksi')
            ->assertSee('Cetak Ulang Struk');
    }

    public function test_owner_bisa_membuka_halaman_riwayat(): void
    {
        $this->actingAs($this->owner)
            ->get('/kasir/riwayat')
            ->assertOk()
            ->assertSee('Riwayat Transaksi');
    }

    public function test_pelanggan_tidak_bisa_membuka_halaman_riwayat(): void
    {
        // Middleware role mengalihkan (bukan 403) untuk request HTML.
        $this->actingAs($this->pelanggan)
            ->get('/kasir/riwayat')
            ->assertRedirect('/');
    }

    public function test_pratinjau_frontend_dapat_dibuka_tanpa_login(): void
    {
        $this->get('/frontend/kasir/riwayat')
            ->assertOk()
            ->assertSee('Riwayat Transaksi');
    }

    // ---------- Render & isi halaman ----------

    public function test_halaman_menampilkan_ringkasan_dan_tujuh_baris_contoh(): void
    {
        $res = $this->actingAs($this->kasir)
            ->get('/kasir/riwayat')
            ->assertOk()
            ->assertSee('Transaksi')
            ->assertSee('Rp 612.000')
            ->assertSee('Sabtu, 31 Okt 2026');

        // Angka ringkasan tidak berubah walau database berisi transaksi lain.
        $this->assertSame(7, substr_count($res->getContent(), '<tr class="h-[50px]'));
        $res->assertSee('#CK-0130')->assertSee('#CK-0124');
    }

    public function test_halaman_tidak_memuat_skrip_atau_handler_klik(): void
    {
        $res = $this->actingAs($this->kasir)->get('/kasir/riwayat')->assertOk();
        $html = $res->getContent();

        // Tidak ada JS sama sekali: tombol & baris tidak punya handler.
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('addEventListener', $html);

        // Tombol cetak ulang ada, tetapi hanya elemen visual.
        $this->assertStringContainsString('id="btnCetakUlang"', $html);
        $this->assertStringContainsString('type="button"', $html);
    }

    public function test_harga_contoh_di_halaman_cocok_dengan_angka_ringkasan(): void
    {
        // Ringkasan menjumlahkan angka contoh yang memang ditulis tangan:
        // Tunai Rp 356.000 + QRIS/VA Rp 256.000 = Total Rp 612.000.
        $this->actingAs($this->kasir)
            ->get('/kasir/riwayat')
            ->assertOk()
            ->assertSee('Rp 356.000')
            ->assertSee('Rp 256.000')
            ->assertSee('Rp 612.000');
    }

    // ---------- Kriteria yang belum terpenuhi ----------

    public function test_riwayat_menampilkan_transaksi_dari_database(): void
    {
        $this->buatSkemaPesanan();

        PosTransaction::create([
            'transaction_number' => 'POS-UJI-0001',
            'cashier_id' => $this->kasir->id,
            'customer_name' => 'Pembeli Uji',
            'total_amount' => 33000,
            'payment_method' => 'cash',
            'amount_paid' => 50000,
            'change_amount' => 17000,
            'status' => 'paid',
            'created_at' => now(),
        ]);

        $this->markTestSkipped(
            'CHIK-107 masih mockup statis: #daftarTransaksi berisi 7 baris contoh di Blade '.
            'dan tidak ada endpoint GET riwayat, sehingga transaksi database tidak pernah tampil. '.
            'Aktifkan tes ini setelah halaman disambungkan ke data.'
        );

        $this->actingAs($this->kasir)
            ->get('/kasir/riwayat')
            ->assertOk()
            ->assertSee('POS-UJI-0001')
            ->assertDontSee('#CK-0130');
    }
}
