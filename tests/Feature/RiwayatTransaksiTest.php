<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
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
 * Halaman kini memuat data lewat GET /kasir/api/riwayat: transaksi kasir
 * (pos_transactions) digabung dengan pesanan online yang sudah lunas.
 * Tes mengunci akses halaman, filter sumber/periode, ringkasan, dan cetak ulang.
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

    // ---------- Halaman terhubung ke API ----------

    public function test_halaman_memuat_wadah_dinamis_dan_skrip_api(): void
    {
        $res = $this->actingAs($this->kasir)->get('/kasir/riwayat')->assertOk();

        $html = $res->getContent();
        $this->assertStringContainsString('id="daftarTransaksi"', $html);
        $this->assertStringContainsString('id="ringkasTotal"', $html);
        $this->assertStringContainsString('id="btnCetakUlang"', $html);
        $this->assertStringContainsString('<script', $html);
        $this->assertStringContainsString('/kasir/api/riwayat', $html);
    }

    public function test_pratinjau_frontend_memakai_api_tanpa_login(): void
    {
        $this->buatSkemaPesanan();

        $this->getJson('/frontend/kasir/api/riwayat')
            ->assertOk()
            ->assertJsonPath('data.total', 0);
    }

    // ---------- API riwayat ----------

    public function test_riwayat_api_menampilkan_transaksi_kasir_dari_database(): void
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

        $this->actingAs($this->kasir)->getJson('/kasir/api/riwayat')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.transaksi.0.tipe', 'kasir')
            ->assertJsonPath('data.transaksi.0.nomor', 'POS-UJI-0001')
            ->assertJsonPath('data.transaksi.0.metode_label', 'Tunai')
            ->assertJsonPath('data.ringkasan.tunai', 33000)
            ->assertJsonPath('data.ringkasan.non_tunai', 0);
    }

    public function test_riwayat_api_menggabungkan_pesanan_online_lunas(): void
    {
        $this->buatSkemaPesanan();

        $order = Order::create([
            'order_number' => 'CHKM-UJI-0001',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 20000,
            'shipping_cost' => 0,
            'grand_total' => 20000,
            'status' => 'completed',
            'payment_type' => 'qris',
            'paid_at' => now(),
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_size_id' => $this->d['regular']->id,
            'unit_price' => 20000,
            'quantity' => 1,
            'subtotal' => 20000,
        ]);

        PosTransaction::create([
            'transaction_number' => 'POS-UJI-0002',
            'cashier_id' => $this->kasir->id,
            'total_amount' => 15000,
            'payment_method' => 'non_cash',
            'amount_paid' => 15000,
            'change_amount' => 0,
            'status' => 'paid',
            'created_at' => now(),
        ]);

        $this->actingAs($this->kasir)->getJson('/kasir/api/riwayat')
            ->assertOk()
            ->assertJsonPath('data.total', 2)
            ->assertJsonPath('data.ringkasan.tunai', 0)
            ->assertJsonPath('data.ringkasan.non_tunai', 35000)
            ->assertJsonFragment(['nomor' => 'CHKM-UJI-0001', 'tipe' => 'online', 'metode_label' => 'QRIS']);
    }

    public function test_pesanan_online_belum_dibayar_tetap_masuk_riwayat_tanpa_menambah_omzet(): void
    {
        $this->buatSkemaPesanan();

        $order = Order::create([
            'order_number' => 'CHKM-BELUM-BAYAR',
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 20000,
            'shipping_cost' => 0,
            'grand_total' => 20000,
            'status' => 'unpaid',
            'payment_type' => 'qris',
            'paid_at' => null,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_size_id' => $this->d['regular']->id,
            'unit_price' => 20000,
            'quantity' => 1,
            'subtotal' => 20000,
        ]);

        $this->actingAs($this->kasir)->getJson('/kasir/api/riwayat?sumber=online')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.transaksi.0.nomor', 'CHKM-BELUM-BAYAR')
            ->assertJsonPath('data.transaksi.0.status_label', 'Belum Dibayar')
            ->assertJsonPath('data.transaksi.0.lunas', false)
            ->assertJsonPath('data.ringkasan.total', 0);
    }

    public function test_riwayat_api_memfilter_sumber_dan_tanggal(): void
    {
        $this->buatSkemaPesanan();

        $kemarin = PosTransaction::create([
            'transaction_number' => 'POS-KEMARIN',
            'cashier_id' => $this->kasir->id,
            'total_amount' => 10000,
            'payment_method' => 'cash',
            'amount_paid' => 10000,
            'change_amount' => 0,
            'status' => 'paid',
        ]);
        $kemarin->forceFill(['created_at' => now()->subDay()])->save();

        PosTransaction::create([
            'transaction_number' => 'POS-HARI-INI',
            'cashier_id' => $this->kasir->id,
            'total_amount' => 10000,
            'payment_method' => 'cash',
            'amount_paid' => 10000,
            'change_amount' => 0,
            'status' => 'paid',
            'created_at' => now(),
        ]);

        $this->actingAs($this->kasir)->getJson('/kasir/api/riwayat?periode=hari-ini&sumber=kasir')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.transaksi.0.nomor', 'POS-HARI-INI');

        $this->actingAs($this->kasir)->getJson('/kasir/api/riwayat?periode=kemarin&sumber=kasir')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.transaksi.0.nomor', 'POS-KEMARIN');
    }

    public function test_pelanggan_tidak_bisa_mengakses_api_riwayat(): void
    {
        $this->actingAs($this->pelanggan)->getJson('/kasir/api/riwayat')->assertForbidden();
    }
}
