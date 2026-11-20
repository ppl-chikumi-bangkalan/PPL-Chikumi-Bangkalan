<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-116] Testing pembayaran online Duitku sandbox.
 *
 * Menguji alur gateway pada PaymentController + App\Support\Duitku:
 * inquiry (QRIS/VA), callback sukses/gagal, status polling (sukses,
 * pending, gagal/kedaluwarsa), idempotensi, validasi signature &
 * nominal, kepemilikan pesanan, endpoint QR, serta mode simulasi.
 *
 * Respons Duitku dipalsukan lewat Http::fake sehingga hasilnya
 * deterministik. Verifikasi ke sandbox asli butuh DUITKU_MERCHANT_CODE
 * dan DUITKU_API_KEY di .env.
 */
class PembayaranSandboxTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $pelanggan;

    private int $urutan = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatSkemaPesanan();
        $this->d = $this->buatDataChikumi();
        $this->pelanggan = $this->buatPelanggan();

        // Kredensial gateway uji (bukan kredensial asli).
        config([
            'services.duitku.merchant_code' => 'D0001',
            'services.duitku.api_key' => 'rahasia-uji',
            'services.duitku.is_production' => false,
            'services.duitku.method_va' => 'BC',
            'services.duitku.method_qris' => 'SP',
            'services.duitku.bank_label' => 'BCA',
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
            $table->unsignedBigInteger('shipping_origin_id')->nullable();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->timestamp('shipping_confirmed_at')->nullable();
            $table->unsignedBigInteger('shipping_confirmed_by')->nullable();
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

    private function pesanan(array $override = []): Order
    {
        $this->urutan++;

        $order = Order::create(array_merge([
            'order_number' => 'CHKM-SBX-'.$this->urutan,
            'customer_id' => $this->pelanggan->id,
            'delivery_method' => 'pickup',
            'subtotal' => 30000,
            'shipping_cost' => 0,
            'grand_total' => 30000,
            'status' => 'unpaid',
            'payment_type' => 'qris',
        ], $override));

        OrderItem::create([
            'order_id' => $order->id,
            'product_size_id' => $this->d['regular']->id,
            'unit_price' => 15000,
            'quantity' => 2,
            'subtotal' => 30000,
        ]);

        return $order;
    }

    private function fakeInquiry(array $json): void
    {
        Http::fake([
            'sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response($json, 200),
        ]);
    }

    private function fakeStatus(array $json): void
    {
        Http::fake([
            'sandbox.duitku.com/webapi/api/merchant/transactionStatus' => Http::response($json, 200),
        ]);
    }

    private function signature(string $raw): string
    {
        return hash_hmac('sha256', $raw, 'rahasia-uji');
    }

    private function callbackData(Order $order, array $override = []): array
    {
        $data = array_merge([
            'merchantCode' => 'D0001',
            'amount' => (string) (int) $order->grand_total,
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'REF-CALLBACK',
        ], $override);

        $data['signature'] ??= $this->signature(
            $data['merchantCode'].$data['amount'].$data['merchantOrderId']
        );

        return $data;
    }

    // ---------- Inquiry ----------

    public function test_inquiry_qris_sukses_menyimpan_referensi_dan_qr(): void
    {
        $order = $this->pesanan();
        $this->fakeInquiry([
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
            'reference' => 'REF-QRIS-1',
            'paymentUrl' => 'https://sandbox.duitku.com/pay/REF-QRIS-1',
            'qrString' => '00020101021226670016COM.SHOPEEPAY.WWW',
        ]);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertOk()
            ->assertJsonPath('data.sumber', 'duitku')
            ->assertJsonPath('data.reference', 'REF-QRIS-1')
            ->assertJsonPath('data.metode_kode', 'SP')
            ->assertJsonPath('data.status', 'unpaid')
            ->assertJsonPath('data.qr_url', "/customer/api/pesanan/{$order->id}/qr");

        $this->assertSame('REF-QRIS-1', $order->fresh()->snap_token);

        Http::assertSent(function ($request) use ($order) {
            return str_contains($request->url(), '/v2/inquiry')
                && $request['merchantOrderId'] === $order->order_number
                && $request['paymentMethod'] === 'SP'
                && $request['paymentAmount'] === 30000
                && $request['signature'] === $this->signature('D0001'.$order->order_number.'30000');
        });
    }

    public function test_inquiry_va_sukses_mengembalikan_nomor_va_dan_bank(): void
    {
        $order = $this->pesanan(['payment_type' => 'va']);
        $this->fakeInquiry([
            'statusCode' => '00',
            'reference' => 'REF-VA-1',
            'vaNumber' => '1234567890123',
            'paymentUrl' => 'https://sandbox.duitku.com/pay/REF-VA-1',
        ]);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertOk()
            ->assertJsonPath('data.sumber', 'duitku')
            ->assertJsonPath('data.bank', 'BCA')
            ->assertJsonPath('data.va_number', '1234567890123')
            ->assertJsonPath('data.metode_kode', 'BC');
    }

    public function test_inquiry_ditolak_gateway_mengembalikan_502_tanpa_menyimpan_referensi(): void
    {
        $order = $this->pesanan();
        $this->fakeInquiry(['statusCode' => '01', 'statusMessage' => 'Gagal']);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertStatus(502);

        $this->assertNull($order->fresh()->snap_token);
        $this->assertSame('unpaid', $order->fresh()->status);
    }

    public function test_charge_pesanan_yang_sudah_dibayar_ditolak(): void
    {
        $order = $this->pesanan(['status' => 'processing']);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertStatus(422);
    }

    public function test_charge_boleh_memilih_metode_saat_pesanan_belum_punya_metode(): void
    {
        $order = $this->pesanan(['payment_type' => null]);
        $this->fakeInquiry([
            'statusCode' => '00',
            'reference' => 'REF-VA-2',
            'vaNumber' => '9876543210001',
        ]);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar", ['payment_method' => 'va'])
            ->assertOk()
            ->assertJsonPath('data.metode', 'va');

        $this->assertSame('va', $order->fresh()->payment_type);

        Http::assertSent(fn ($request) => $request['paymentMethod'] === 'BC');
    }

    public function test_charge_ulang_metode_sama_tidak_membuat_transaksi_baru(): void
    {
        $order = $this->pesanan(['snap_token' => 'REF-LAMA']);
        Cache::put('duitku:trx:'.$order->order_number, [
            'payment_method' => 'SP',
            'reference' => 'REF-LAMA',
            'status' => 'pending',
        ], now()->addHours(24));

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertOk()
            ->assertJsonPath('data.reference', 'REF-LAMA');

        Http::assertNothingSent();
        $this->assertSame('REF-LAMA', $order->fresh()->snap_token);
    }

    // ---------- Callback ----------

    public function test_callback_sukses_mengubah_status_menjadi_processing_dan_idempoten(): void
    {
        $order = $this->pesanan();
        $this->d['regular']->update(['stock' => 18]);

        $this->post('/payment/callback', $this->callbackData($order))
            ->assertOk()
            ->assertSee('OK');

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertEquals(18, $this->d['regular']->fresh()->stock);

        $paidAt = $order->paid_at->toDateTimeString();

        // Callback kedua (Duitku bisa mengulang) tidak boleh mengubah data.
        $this->post('/payment/callback', $this->callbackData($order))->assertOk();

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertSame($paidAt, $order->paid_at->toDateTimeString());
        $this->assertEquals(18, $this->d['regular']->fresh()->stock);
    }

    public function test_callback_signature_tidak_valid_ditolak_403(): void
    {
        $order = $this->pesanan();
        $data = $this->callbackData($order, ['signature' => str_repeat('a', 64)]);

        $this->post('/payment/callback', $data)->assertForbidden();

        $this->assertSame('unpaid', $order->fresh()->status);
    }

    public function test_callback_nominal_tidak_cocok_ditolak_400(): void
    {
        $order = $this->pesanan();
        $data = $this->callbackData($order);

        // Signature valid untuk nominal 1.000, tetapi total pesanan 30.000.
        $data['amount'] = '1000';
        $data['signature'] = $this->signature('D0001'.$data['amount'].$order->order_number);

        $this->post('/payment/callback', $data)->assertStatus(400);

        $this->assertSame('unpaid', $order->fresh()->status);
    }

    public function test_callback_pesanan_tidak_dikenal_404(): void
    {
        $data = [
            'merchantCode' => 'D0001',
            'amount' => '30000',
            'merchantOrderId' => 'CHKM-TIDAK-ADA',
            'resultCode' => '00',
        ];
        $data['signature'] = $this->signature('D0001'.$data['amount'].$data['merchantOrderId']);

        $this->post('/payment/callback', $data)->assertNotFound();
    }

    public function test_callback_gagal_tidak_mengubah_status_pesanan(): void
    {
        $order = $this->pesanan();
        $data = $this->callbackData($order, ['resultCode' => '01']);
        $data['signature'] = $this->signature('D0001'.$data['amount'].$order->order_number);

        $this->post('/payment/callback', $data)->assertOk();

        // Callback non-00 hanya diabaikan; pembatalan & balik stok terjadi lewat cek status.
        $this->assertSame('unpaid', $order->fresh()->status);
        $this->assertEquals(20, $this->d['regular']->fresh()->stock);
    }

    public function test_callback_tanpa_api_key_ditolak_403(): void
    {
        config(['services.duitku.api_key' => null]);
        $order = $this->pesanan();

        $this->post('/payment/callback', $this->callbackData($order))->assertForbidden();
    }

    // ---------- Polling status ----------

    public function test_polling_status_sukses_mengubah_status_menjadi_processing(): void
    {
        $order = $this->pesanan(['snap_token' => 'REF-1']);
        $this->fakeStatus([
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
            'reference' => 'REF-1',
            'amount' => '30000',
        ]);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.status_gateway', 'Berhasil dibayar');

        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_polling_status_pending_tetap_unpaid(): void
    {
        $order = $this->pesanan(['snap_token' => 'REF-1']);
        $this->fakeStatus(['statusCode' => '01', 'reference' => 'REF-1']);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk()
            ->assertJsonPath('data.status', 'unpaid')
            ->assertJsonPath('data.status_gateway', 'Menunggu pembayaran');
    }

    public function test_polling_status_kedaluwarsa_membatalkan_dan_mengembalikan_stok(): void
    {
        $order = $this->pesanan(['snap_token' => 'REF-1']);
        $this->d['regular']->update(['stock' => 18]);
        $this->fakeStatus(['statusCode' => '02', 'reference' => 'REF-1']);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.status_gateway', 'Gagal / kedaluwarsa');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertEquals(20, $this->d['regular']->fresh()->stock);

        $masuk = StockHistory::where('type', 'in')->firstOrFail();
        $this->assertSame('online_order', $masuk->source);
        $this->assertEquals(2, $masuk->quantity);
        $this->assertStringContainsString('Pembatalan otomatis', $masuk->notes);

        // Polling berikutnya tidak menambah stok lagi.
        Cache::forget('duitku:cek:'.$order->order_number);
        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk();

        $this->assertEquals(20, $this->d['regular']->fresh()->stock);
        $this->assertSame(1, StockHistory::where('type', 'in')->count());
    }

    public function test_polling_dibatasi_30_detik_per_pesanan(): void
    {
        $order = $this->pesanan(['snap_token' => 'REF-1']);
        $this->fakeStatus(['statusCode' => '01', 'reference' => 'REF-1']);

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk();

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk();

        Http::assertSentCount(1);
    }

    // ---------- Mode simulasi & kepemilikan ----------

    public function test_tanpa_kredensial_berjalan_mode_simulasi_tanpa_memanggil_gateway(): void
    {
        config([
            'services.duitku.merchant_code' => null,
            'services.duitku.api_key' => null,
        ]);

        $order = $this->pesanan();

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertOk()
            ->assertJsonPath('data.sumber', 'simulasi')
            ->assertJsonPath('data.va_number', '8808'.str_pad((string) $order->id, 8, '0', STR_PAD_LEFT));

        $this->actingAs($this->pelanggan)
            ->getJson("/customer/api/pesanan/{$order->id}/status-pembayaran")
            ->assertOk()
            ->assertJsonPath('data.status', 'unpaid');

        Http::assertNothingSent();
        $this->assertNull($order->fresh()->snap_token);
    }

    public function test_pesanan_milik_pelanggan_lain_tidak_bisa_diakses(): void
    {
        $lain = User::create([
            'name' => 'Pelanggan Lain',
            'email' => 'lain.sbx@gmail.com',
            'phone_number' => '081200000099',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
        $order = $this->pesanan();

        $this->actingAs($lain)
            ->getJson("/customer/api/pesanan/{$order->id}/pembayaran")
            ->assertForbidden();

        $this->actingAs($lain)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertForbidden();
    }

    public function test_route_dev_memakai_header_x_dev_customer(): void
    {
        $order = $this->pesanan();

        $this->getJson(
            "/frontend/customer/api/pesanan/{$order->id}/pembayaran",
            ['X-Dev-Customer' => (string) $this->pelanggan->id]
        )
            ->assertOk()
            ->assertJsonPath('data.id', $order->id);

        $this->getJson("/frontend/customer/api/pesanan/{$order->id}/pembayaran")
            ->assertForbidden();
    }

    public function test_endpoint_qr_menghasilkan_svg_dari_qr_string_duitku(): void
    {
        $order = $this->pesanan();
        $this->fakeInquiry([
            'statusCode' => '00',
            'reference' => 'REF-QR-1',
            'qrString' => '00020101021226670016COM.SHOPEEPAY.WWW',
        ]);

        $this->actingAs($this->pelanggan)
            ->postJson("/customer/api/pesanan/{$order->id}/bayar")
            ->assertOk();

        $response = $this->actingAs($this->pelanggan)
            ->get("/customer/api/pesanan/{$order->id}/qr");

        $response->assertOk();
        $this->assertStringContainsString('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_endpoint_qr_404_bila_belum_ada_transaksi(): void
    {
        $order = $this->pesanan();

        $this->actingAs($this->pelanggan)
            ->get("/customer/api/pesanan/{$order->id}/qr")
            ->assertNotFound();
    }
}
