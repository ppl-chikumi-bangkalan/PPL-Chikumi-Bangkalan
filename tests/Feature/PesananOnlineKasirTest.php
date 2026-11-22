<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * Sambungan halaman kasir "Pesanan Online" ke backend:
 * GET /kasir/api/pesanan-online, PATCH .../siap, PATCH .../selesai.
 */
class PesananOnlineKasirTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $kasir;

    private User $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->kasir = $this->buatKasir();
        $this->pelanggan = $this->buatPelanggan();
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

        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id');
            $table->foreignId('variant_id');
            $table->decimal('variant_price', 12, 2);
        });
    }

    private function pesanan(string $nomor, string $status, array $override = []): Order
    {
        $order = Order::create(array_merge([
            'order_number' => $nomor,
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 20000,
            'shipping_cost' => 0,
            'grand_total' => 20000,
            'status' => $status,
            'payment_type' => 'qris',
            'paid_at' => now(),
        ], $override));

        OrderItem::create([
            'order_id' => $order->id,
            'product_size_id' => $this->d['regular']->id,
            'unit_price' => 20000,
            'quantity' => 1,
            'subtotal' => 20000,
        ]);

        return $order;
    }

    public function test_halaman_pesanan_online_terbuka_untuk_kasir(): void
    {
        $this->actingAs($this->kasir)->get('/kasir/pesanan-online')
            ->assertOk()
            ->assertSee('Pesanan Online')
            ->assertSee('id="daftarDiproses"', false);
    }

    public function test_pelanggan_tidak_bisa_membuka_halaman_pesanan_online(): void
    {
        $this->actingAs($this->pelanggan)->get('/kasir/pesanan-online')->assertRedirect('/');
    }

    public function test_api_menampilkan_pesanan_lunas_dan_menyertakan_detail_item(): void
    {
        $this->pesanan('CHKM-KASIR-1', 'processing');
        $this->pesanan('CHKM-KASIR-2', 'ready', ['delivery_method' => 'courier', 'shipping_cost' => 5000, 'grand_total' => 25000]);
        $this->pesanan('CHKM-BELUM-BAYAR', 'unpaid', ['paid_at' => null]);
        $this->pesanan('CHKM-BATAL', 'cancelled');

        $this->actingAs($this->kasir)->getJson('/kasir/api/pesanan-online')
            ->assertOk()
            ->assertJsonPath('data.total', 2)
            ->assertJsonFragment(['nomor' => 'CHKM-KASIR-1', 'status' => 'processing', 'metode_label' => 'QRIS'])
            ->assertJsonFragment(['nomor' => 'CHKM-KASIR-2', 'jenis_label' => 'Diantar', 'ongkir' => 5000])
            ->assertJsonMissing(['nomor' => 'CHKM-BELUM-BAYAR'])
            ->assertJsonMissing(['nomor' => 'CHKM-BATAL']);
    }

    public function test_kasir_menandai_siap_lalu_selesai(): void
    {
        $order = $this->pesanan('CHKM-ALUR', 'processing');

        $this->actingAs($this->kasir)->patchJson("/kasir/api/pesanan-online/{$order->id}/siap")
            ->assertOk()
            ->assertJsonPath('data.status', 'ready');

        $this->assertSame('ready', $order->fresh()->status);

        $this->actingAs($this->kasir)->patchJson("/kasir/api/pesanan-online/{$order->id}/selesai")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');

        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_aksi_ditolak_bila_status_tidak_sesuai(): void
    {
        $order = $this->pesanan('CHKM-SALAH-STATUS', 'processing');

        $this->actingAs($this->kasir)->patchJson("/kasir/api/pesanan-online/{$order->id}/selesai")
            ->assertStatus(422);

        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_frontend_api_bisa_dipakai_tanpa_login(): void
    {
        $order = $this->pesanan('CHKM-DEV', 'processing');

        $this->getJson('/frontend/kasir/api/pesanan-online')
            ->assertOk()
            ->assertJsonPath('data.total', 1);

        $this->patchJson("/frontend/kasir/api/pesanan-online/{$order->id}/siap")
            ->assertOk()
            ->assertJsonPath('data.status', 'ready');
    }
}
