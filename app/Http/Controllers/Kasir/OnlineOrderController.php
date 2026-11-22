<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;

class OnlineOrderController extends Controller
{
    public function index()
    {
        $pesanan = Order::with(['items.productSize.product:id,name', 'items.variants', 'customer:id,name'])
            ->whereNotNull('paid_at')
            ->where(function ($query) {
                $query->whereIn('status', ['processing', 'ready'])
                    ->orWhere(fn ($q) => $q->where('status', 'completed')->whereDate('updated_at', today()));
            })
            ->latest('paid_at')
            ->get()
            ->map(fn (Order $order) => $this->payload($order));

        return response()->json([
            'data' => ['total' => $pesanan->count(), 'pesanan' => $pesanan->values()],
        ]);
    }

    public function ready(Order $pesanan)
    {
        abort_unless($pesanan->paid_at, 422, 'Pesanan belum dibayar.');
        abort_unless($pesanan->status === 'processing', 422, 'Pesanan tidak bisa ditandai siap.');

        $pesanan->update(['status' => 'ready']);

        return response()->json([
            'message' => 'Pesanan ditandai siap.',
            'data' => $this->payload($pesanan->fresh($this->relations())),
        ]);
    }

    public function complete(Order $pesanan)
    {
        abort_unless($pesanan->paid_at, 422, 'Pesanan belum dibayar.');
        abort_unless($pesanan->status === 'ready', 422, 'Pesanan belum siap diselesaikan.');

        $pesanan->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Pesanan selesai.',
            'data' => $this->payload($pesanan->fresh($this->relations())),
        ]);
    }

    private function relations(): array
    {
        return ['items.productSize.product:id,name', 'items.variants', 'customer:id,name'];
    }

    private function payload(Order $order): array
    {
        return [
            'id' => $order->id,
            'nomor' => $order->order_number,
            'pelanggan' => $order->customer?->name ?? 'Pelanggan',
            'waktu' => $order->created_at->format('Y-m-d H:i'),
            'jenis' => $order->delivery_method,
            'jenis_label' => $order->delivery_method === 'courier' ? 'Diantar' : 'Ambil di Tempat',
            'metode' => $order->payment_type,
            'metode_label' => match ($order->payment_type) {
                'qris' => 'QRIS',
                'va' => 'VA',
                default => '-',
            },
            'status' => $order->status,
            'status_label' => match ($order->status) {
                'processing' => 'Diproses',
                'ready' => $order->delivery_method === 'courier' ? 'Siap Dikirim' : 'Siap Diambil',
                'completed' => 'Selesai',
                default => ucfirst($order->status),
            },
            'subtotal' => (float) $order->subtotal,
            'ongkir' => (float) $order->shipping_cost,
            'total' => (float) $order->grand_total,
            'alamat' => $order->shipping_address,
            'jumlah_item' => $order->items->sum('quantity'),
            'item' => $order->items->map(fn (OrderItem $item) => [
                'produk' => $item->productSize->product->name,
                'ukuran' => $item->productSize->size_name,
                'varian' => $item->variants->pluck('name')->values(),
                'harga_satuan' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
        ];
    }
}
