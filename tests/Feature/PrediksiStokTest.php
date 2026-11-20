<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\ProductSize;
use App\Models\User;
use App\Models\Variant;
use App\Support\MovingAverage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-128] Uji penerimaan PPL-07 (CHIK-53) — prediksi kebutuhan stok
 * dengan moving average 3 minggu.
 *
 * Fokus: hasil App\Support\MovingAverage dicocokkan dengan hitungan manual.
 * Cakupan: CHIK-126 (perhitungan & saran restock), CHIK-127 (kartu prediksi
 * stok di dashboard owner), CHIK-125 (seeder riwayat 4-8 minggu).
 */
class PrediksiStokTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $kasir;

    private User $pelanggan;

    private User $owner;

    private int $urutan = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();
        $this->owner = User::create([
            'name' => 'Owner Prediksi',
            'email' => 'owner.prediksi@gmail.com',
            'phone_number' => '081200000088',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
    }

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

    // ---------- Helper data penjualan ----------

    private function jualKasir(int $sizeId, int $qty, int $hariLalu, string $status = 'paid'): void
    {
        $this->urutan++;
        $waktu = now()->subDays($hariLalu);

        $trx = PosTransaction::create([
            'transaction_number' => 'POS-UJI-'.$this->urutan,
            'cashier_id' => $this->kasir->id,
            'total_amount' => $qty * 15000,
            'payment_method' => 'cash',
            'amount_paid' => $qty * 15000,
            'change_amount' => 0,
            'status' => $status,
        ]);
        // created_at tidak fillable -> set langsung agar tidak tertimpa now().
        $trx->forceFill(['created_at' => $waktu])->save();

        PosTransactionItem::create([
            'pos_transaction_id' => $trx->id,
            'product_size_id' => $sizeId,
            'unit_price' => 15000,
            'quantity' => $qty,
            'subtotal' => $qty * 15000,
        ]);
    }

    private function pesanOnline(int $sizeId, int $qty, int $hariLalu, string $status, bool $lunas): void
    {
        $this->urutan++;
        $waktu = now()->subDays($hariLalu);

        $order = Order::create([
            'order_number' => 'CHKM-UJI-'.$this->urutan,
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => $qty * 15000,
            'shipping_cost' => 0,
            'grand_total' => $qty * 15000,
            'status' => $status,
            'payment_type' => 'qris',
            'paid_at' => $lunas ? $waktu : null,
        ]);
        $order->forceFill(['created_at' => $waktu])->save();

        OrderItem::create([
            'order_id' => $order->id,
            'product_size_id' => $sizeId,
            'unit_price' => 15000,
            'quantity' => $qty,
            'subtotal' => $qty * 15000,
        ]);
    }

    // ================= A. Pencocokan dengan hitungan manual =================

    public function test_moving_average_cocok_dengan_hitungan_manual(): void
    {
        $reg = $this->d['regular']->id;

        // Data yang HARUS dihitung:
        $this->jualKasir($reg, 3, 0);            // hari ini
        $this->jualKasir($reg, 4, 5);            // 5 hari lalu
        $this->jualKasir($reg, 7, 20);           // batas dalam jendela
        $this->pesanOnline($reg, 2, 2, 'processing', true);   // lunas

        // Data yang HARUS diabaikan:
        $this->jualKasir($reg, 100, 21);         // di luar 21 hari
        $this->jualKasir($reg, 50, 0, 'cancelled');           // transaksi batal
        $this->pesanOnline($reg, 9, 3, 'unpaid', false);      // belum dibayar
        $this->pesanOnline($reg, 8, 4, 'cancelled', true);    // pesanan batal

        // Hitungan manual: (3 + 4 + 7 + 2) = 16 pcs / 21 hari = 0,7619...
        $harapan = round(16 / 21, 2);

        $rata = MovingAverage::harian();
        $this->assertSame($harapan, $rata[$reg]);
        $this->assertEqualsWithDelta(0.76, $rata[$reg], 0.0001);
    }

    public function test_batas_jendela_21_hari(): void
    {
        $reg = $this->d['regular']->id;

        $this->jualKasir($reg, 21, 20);   // hari ke-21 (paling tua) -> masuk
        $this->jualKasir($reg, 210, 21);  // hari ke-22 -> keluar

        $rata = MovingAverage::harian();

        // 21 / 21 = 1.0 ; kalau 21 hari lalu ikut terhitung hasilnya 11.0
        $this->assertSame(1.0, $rata[$reg]);
    }

    public function test_ukuran_tanpa_penjualan_tidak_muncul_di_hasil(): void
    {
        $this->jualKasir($this->d['regular']->id, 3, 1);

        $rata = MovingAverage::harian();

        $this->assertTrue($rata->has($this->d['regular']->id));
        $this->assertFalse($rata->has($this->d['large']->id));
    }

    public function test_penjualan_kasir_dan_online_dijumlahkan_per_ukuran(): void
    {
        $reg = $this->d['regular']->id;

        $this->jualKasir($reg, 10, 1);
        $this->pesanOnline($reg, 11, 1, 'completed', true);

        // (10 + 11) / 21 = 1.0
        $this->assertSame(1.0, MovingAverage::harian()[$reg]);
    }

    public function test_hasil_dibulatkan_dua_desimal(): void
    {
        $reg = $this->d['regular']->id;
        $this->jualKasir($reg, 1, 0);

        // 1 / 21 = 0,047619... -> 0,05
        $this->assertSame(0.05, MovingAverage::harian()[$reg]);
    }

    // ================= B. Saran restock & cukup hari =================

    public function test_saran_restock_memakai_kebutuhan_tujuh_hari(): void
    {
        // rata 0,76/hari -> butuh 7 hari = 5,33 -> dibulatkan ke atas 6 pcs
        // stok 5, minimum 5 -> target 6 -> saran 1
        $this->assertSame(1, MovingAverage::saran(5, 5, 0.76));

        // stok 0, minimum 5 -> target max(5, 6) = 6 -> saran 6
        $this->assertSame(6, MovingAverage::saran(0, 5, 0.76));
    }

    public function test_saran_restock_minimal_setara_minimum_stok(): void
    {
        // rata rendah: butuh 7 hari = 2 pcs, tetapi minimum 5 -> target 5
        $this->assertSame(5, MovingAverage::saran(0, 5, 0.3));
        $this->assertSame(3, MovingAverage::saran(2, 5, 0.3));
    }

    public function test_saran_restock_nol_bila_stok_sudah_cukup(): void
    {
        $this->assertSame(0, MovingAverage::saran(20, 5, 1.0));
        $this->assertSame(0, MovingAverage::saran(5, 5, 0.0));
    }

    public function test_cukup_hari_dihitung_dari_laju_penjualan(): void
    {
        // 21 pcs / 1 pcs per hari = 21 hari
        $this->assertSame(21, MovingAverage::cukupHari(21, 1.0));
        // 5 / 0,76 = 6,57 -> 6 hari (dibulatkan ke bawah)
        $this->assertSame(6, MovingAverage::cukupHari(5, 0.76));
        // tanpa penjualan -> tidak bisa diprediksi
        $this->assertNull(MovingAverage::cukupHari(5, 0.0));
    }

    // ================= C. API kartu prediksi (CHIK-127) =================

    public function test_api_ketersediaan_mengirim_rata_harian_dan_saran(): void
    {
        $reg = $this->d['regular']->id;
        $this->jualKasir($reg, 16, 1);

        $this->d['regular']->update(['stock' => 5, 'min_stock' => 5]);

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/produk/ketersediaan')
            ->assertOk();

        $produk = collect($res->json('data.produk'))
            ->first(fn ($item) => $item['size'] === $this->d['regular']->size_name);

        // 16 / 21 = 0,76 ; butuh 7 hari = 6 pcs ; stok 5 -> saran 1
        $this->assertEqualsWithDelta(0.76, $produk['rata_harian'], 0.0001);
        $this->assertSame(1, $produk['saran_restock']);
        $this->assertSame('menipis', $produk['status']);
    }

    public function test_api_ketersediaan_menghitung_ringkasan_status(): void
    {
        $this->d['regular']->update(['stock' => 50, 'min_stock' => 5]);  // aman
        $this->d['large']->update(['stock' => 2, 'min_stock' => 5]);     // menipis

        $res = $this->actingAs($this->owner)
            ->getJson('/owner/api/produk/ketersediaan')
            ->assertOk();

        $ringkasan = $res->json('data.ringkasan');

        $this->assertSame(1, $ringkasan['menipis']);
        $this->assertSame(
            $ringkasan['total'],
            $ringkasan['aman'] + $ringkasan['menipis'] + $ringkasan['habis']
        );
    }

    public function test_pelanggan_tidak_bisa_mengakses_api_prediksi(): void
    {
        $this->actingAs($this->pelanggan)
            ->getJson('/owner/api/produk/ketersediaan')
            ->assertForbidden();
    }

    public function test_halaman_dashboard_owner_menampilkan_kartu_kondisi_stok(): void
    {
        $this->actingAs($this->owner)->get('/owner/dashboard')
            ->assertOk()
            ->assertSee('Kondisi Stok')
            ->assertSee('SARAN')
            ->assertSee('Lihat Produk');
    }

    // ================= D. Seeder riwayat (CHIK-125) =================

    public function test_seeder_menghasilkan_rentang_4_sampai_8_minggu(): void
    {
        $isi = file_get_contents(base_path('database/seed_riwayat_penjualan.php'));

        // Batas minggu dijepit 4..8.
        $this->assertStringContainsString('max(4, min(8,', $isi);
        $this->assertStringContainsString('POS-SEED-', $isi);
        $this->assertStringContainsString('CHKM-SEED-', $isi);
    }

    public function test_seeder_bisa_dijalankan_ulang_tanpa_duplikat(): void
    {
        $isi = file_get_contents(base_path('database/seed_riwayat_penjualan.php'));

        // Hapus data seeder lama sebelum menyisipkan ulang.
        $this->assertStringContainsString("DELETE FROM pos_transactions WHERE transaction_number LIKE 'POS-SEED-%'", $isi);
        $this->assertStringContainsString("DELETE FROM orders WHERE order_number LIKE 'CHKM-SEED-%'", $isi);
    }
}
