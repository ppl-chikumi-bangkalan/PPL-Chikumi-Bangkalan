<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StockHistory;
use App\Support\Duitku;
use App\Support\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ResolvesCustomer;

    /**
     * Ringkasan pembayaran satu pesanan (tanpa membuat transaksi baru).
     */
    public function show(Request $request, Order $pesanan)
    {
        $this->owns($request, $pesanan);

        return response()->json(['data' => $this->detail($request, $pesanan)]);
    }

    /**
     * Buat transaksi Duitku (sekali saja per pesanan) lalu kembalikan data pembayaran.
     */
    public function charge(Request $request, Order $pesanan)
    {
        $this->owns($request, $pesanan);

        abort_unless($pesanan->status === 'unpaid', 422, 'Pesanan ini sudah tidak bisa dibayar.');

        if ($pesanan->payment_type === null) {
            $data = $request->validate(['payment_method' => ['required', 'in:qris,va']]);
            $pesanan->update(['payment_type' => $data['payment_method']]);
        }

        if (Duitku::configured()) {
            $lama = $pesanan->snap_token ? Duitku::cached($pesanan->order_number) : null;

            // Bayar ulang bila belum ada transaksi, atau metodenya diganti sebelum dibayar.
            $perluBaru = $lama === null
                || ($lama['payment_method'] ?? null) !== Duitku::methodCode($pesanan->payment_type);

            if ($perluBaru) {
                $transaksi = Duitku::inquiry($pesanan);
                abort_if($transaksi === null, 502, 'Gagal membuat transaksi pembayaran. Coba lagi.');

                $pesanan->update(['snap_token' => $transaksi['reference']]);
            }
        }

        return response()->json(['data' => $this->detail($request, $pesanan)]);
    }

    /**
     * Polling status pembayaran.
     */
    public function status(Request $request, Order $pesanan)
    {
        $this->owns($request, $pesanan);

        if (Duitku::configured() && $pesanan->status === 'unpaid' && $pesanan->snap_token) {
            $transaksi = Duitku::status($pesanan);

            if ($transaksi !== null) {
                $this->sync($pesanan, $transaksi['status']);
            }
        }

        return response()->json(['data' => $this->detail($request, $pesanan)]);
    }

    /**
     * Callback Duitku (tanpa login; keaslian dicek lewat signature).
     */
    public function callback(Request $request)
    {
        $data = $request->all();

        if (! Duitku::callbackValid($data)) {
            Log::warning('Callback Duitku dengan signature tidak valid.', ['order' => $data['merchantOrderId'] ?? null]);

            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $order = Order::where('order_number', (string) ($data['merchantOrderId'] ?? ''))->first();
        abort_unless($order, 404, 'Pesanan tidak ditemukan.');

        // resultCode: 00 sukses, 01 gagal.
        $this->sync($order, ((string) ($data['resultCode'] ?? '')) === '00' ? 'paid' : null);

        // Duitku mengharuskan balasan HTTP 200.
        return response('OK', 200);
    }

    /**
     * Gambar QR dari qrString Duitku, dilayani sebagai SVG (tanpa layanan luar).
     */
    public function qr(Request $request, Order $pesanan)
    {
        $this->owns($request, $pesanan);

        $data = Duitku::cached($pesanan->order_number);
        $svg = QrCode::svg($data['qr_string'] ?? null);
        abort_if($svg === null, 404, 'QR belum tersedia untuk pesanan ini.');

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Samakan status pesanan lokal dengan status gateway.
     */
    private function sync(Order $order, ?string $status): void
    {
        if ($status === 'paid' && $order->status === 'unpaid') {
            $order->update(['status' => 'processing', 'paid_at' => now()]);
        }

        if ($status === 'cancelled' && $order->status !== 'cancelled') {
            $this->batalkan($order);
        }
    }

    private function batalkan(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->with('productSize')->get() as $item) {
                $item->productSize->increment('stock', $item->quantity);
                StockHistory::create([
                    'product_size_id' => $item->product_size_id,
                    'user_id' => $order->customer_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'source' => 'online_order',
                    'reference_id' => $order->id,
                    'notes' => 'Pembatalan otomatis (pembayaran gagal/kedaluwarsa)',
                ]);
            }
            $order->update(['status' => 'cancelled']);
        });
    }

    private function detail(Request $request, Order $order): array
    {
        $order->load('items.productSize.product:id,name,image_url', 'items.variants');

        $data = [
            'id' => $order->id,
            'nomor' => $order->order_number,
            'total' => (float) $order->grand_total,
            'status' => $order->status,
            'dibayar_pada' => $order->paid_at?->format('Y-m-d H:i'),
            'metode' => $order->payment_type ?? 'qris',
            'sumber' => 'simulasi',
            'sandbox' => Duitku::sandbox(),
            'bank' => null,
            'va_number' => null,
            'qr_string' => null,
            'qr_url' => null,
            'payment_url' => null,
            'merchant_code' => Duitku::merchantCode(),
            'reference' => null,
            'metode_kode' => null,
            'status_gateway' => null,
            'kedaluwarsa' => null,
            'item' => $order->items->map(fn ($item) => [
                'produk' => $item->productSize->product->name,
                'ukuran' => $item->productSize->size_name,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
        ];

        if (! Duitku::configured()) {
            $data['bank'] = 'BCA (simulasi)';
            $data['va_number'] = '8808'.str_pad((string) $order->id, 8, '0', STR_PAD_LEFT);
            $data['kedaluwarsa'] = now()
                ->addMinutes((int) config('services.duitku.expiry_minutes', 60))
                ->format('Y-m-d H:i:s');

            return $data;
        }

        // Data inquiry diambil dari cache -> tidak perlu hit API tiap render/polling.
        $transaksi = $order->snap_token ? Duitku::cached($order->order_number) : null;

        if ($transaksi === null) {
            return $data;
        }

        $data['sumber'] = 'duitku';
        $data['bank'] = $transaksi['bank'] ?? null;
        $data['va_number'] = $transaksi['va_number'] ?? null;
        $data['qr_string'] = $transaksi['qr_string'] ?? null;
        $data['payment_url'] = $transaksi['payment_url'] ?? null;
        $data['reference'] = $transaksi['reference'] ?? null;
        $data['metode_kode'] = $transaksi['payment_method'] ?? null;
        $data['status_gateway'] = $transaksi['status_gateway'] ?? null;

        if ($data['qr_string']) {
            $data['qr_url'] = ($request->is('frontend/*') ? '/frontend/customer/api' : '/customer/api')
                .'/pesanan/'.$order->id.'/qr';

            // <img> tidak bisa mengirim header; route dev menandai pemilik lewat query.
            if (! $request->user()) {
                $data['qr_url'] .= '?dev='.$this->customerId($request);
            }
        }
        $data['kedaluwarsa'] = now()
            ->addMinutes((int) config('services.duitku.expiry_minutes', 60))
            ->format('Y-m-d H:i:s');

        // Status lokal yang sudah final menang atas label gateway (mis. setelah callback masuk).
        if (in_array($order->status, ['processing', 'ready', 'completed'], true)) {
            $data['status_gateway'] = Duitku::labelStatus('00');
        } elseif ($order->status === 'cancelled') {
            $data['status_gateway'] = Duitku::labelStatus('02');
        }

        return $data;
    }

    private function owns(Request $request, Order $order): void
    {
        $customerId = $request->user()?->id;

        if (! $customerId) {
            $customerId = $request->header('X-Dev-Customer') ?: $request->query('dev');
            $customerId = $customerId
                ? \App\Models\User::where('id', $customerId)->where('role', 'customer')->value('id')
                : null;
        }

        abort_unless($customerId && (int) $order->customer_id === (int) $customerId, 403);
    }
}
