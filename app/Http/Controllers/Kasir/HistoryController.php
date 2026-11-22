<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'sumber' => ['nullable', 'in:semua,kasir,online'],
            'periode' => ['nullable', 'in:hari-ini,kemarin,7-hari'],
            'cari' => ['nullable', 'string', 'max:100'],
        ]);

        $sumber = $filter['sumber'] ?? 'semua';
        $cari = trim($filter['cari'] ?? '');
        [$mulai, $selesai] = $this->rentang($filter['periode'] ?? 'hari-ini');

        $transaksi = collect();

        if ($sumber !== 'online') {
            $transaksi = $transaksi->concat(
                PosTransaction::with(['items.productSize.product:id,name', 'items.variants', 'cashier:id,name'])
                    ->whereBetween('created_at', [$mulai, $selesai])
                    ->when($cari !== '', fn ($query) => $query->where('transaction_number', 'like', "%{$cari}%"))
                    ->get()
                    ->map(fn (PosTransaction $t) => $this->payloadKasir($t))
            );
        }

        if ($sumber !== 'kasir') {
            $transaksi = $transaksi->concat(
                Order::with(['items.productSize.product:id,name', 'items.variants', 'customer:id,name'])
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$mulai, $selesai])
                    ->when($cari !== '', fn ($query) => $query->where('order_number', 'like', "%{$cari}%"))
                    ->get()
                    ->map(fn (Order $o) => $this->payloadOnline($o))
            );
        }

        $transaksi = $transaksi->sortByDesc('waktu_iso')->values();
        // Ringkasan uang hanya menghitung transaksi lunas; pesanan belum dibayar tetap tampil di daftar.
        $lunas = $transaksi->where('lunas', true);
        $tunai = (float) $lunas->where('metode', 'cash')->sum('total');
        $nonTunai = (float) $lunas->where('metode', '!=', 'cash')->sum('total');

        return response()->json([
            'data' => [
                'total' => $transaksi->count(),
                'ringkasan' => [
                    'transaksi' => $transaksi->count(),
                    'tunai' => $tunai,
                    'non_tunai' => $nonTunai,
                    'total' => $tunai + $nonTunai,
                ],
                'transaksi' => $transaksi,
            ],
        ]);
    }

    private function payloadKasir(PosTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'tipe' => 'kasir',
            'nomor' => $transaction->transaction_number,
            'waktu' => $transaction->created_at->format('Y-m-d H:i'),
            'waktu_iso' => $transaction->created_at->toIso8601String(),
            'pelanggan' => $transaction->customer_name ?: 'Umum',
            'kasir' => $transaction->cashier?->name,
            'metode' => $transaction->payment_method,
            'metode_label' => $transaction->payment_method === 'cash' ? 'Tunai' : 'Non-tunai',
            'total' => (float) $transaction->total_amount,
            'dibayar' => (float) $transaction->amount_paid,
            'kembalian' => (float) $transaction->change_amount,
            'ongkir' => 0.0,
            'pengambilan' => 'Ambil di Tempat',
            'alamat' => null,
            'status' => $transaction->status,
            'status_label' => $transaction->status === 'paid' ? 'Lunas' : ucfirst($transaction->status),
            'lunas' => $transaction->status === 'paid',
            'item' => $transaction->items->map(fn (PosTransactionItem $item) => [
                'produk' => $item->productSize->product->name,
                'ukuran' => $item->productSize->size_name,
                'varian' => $item->variants->pluck('name')->values(),
                'harga_satuan' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
        ];
    }

    private function payloadOnline(Order $order): array
    {
        return [
            'id' => $order->id,
            'tipe' => 'online',
            'nomor' => $order->order_number,
            'waktu' => $order->created_at->format('Y-m-d H:i'),
            'waktu_iso' => $order->created_at->toIso8601String(),
            'pelanggan' => $order->customer?->name ?? 'Pelanggan',
            'kasir' => null,
            'metode' => $order->payment_type,
            'metode_label' => $this->metodeLabel($order->payment_type),
            'total' => (float) $order->grand_total,
            'dibayar' => $order->paid_at ? (float) $order->grand_total : 0.0,
            'kembalian' => 0.0,
            'ongkir' => (float) $order->shipping_cost,
            'pengambilan' => $order->delivery_method === 'courier' ? 'Diantar' : 'Ambil di Tempat',
            'alamat' => $order->shipping_address,
            'status' => $order->status,
            'status_label' => $this->statusLabel($order),
            'lunas' => $order->paid_at !== null,
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

    private function metodeLabel(?string $metode): string
    {
        return match ($metode) {
            'qris' => 'QRIS',
            'va' => 'VA',
            'cash' => 'Tunai',
            'non_cash' => 'Non-tunai',
            default => '-',
        };
    }

    private function statusLabel(Order $order): string
    {
        return match ($order->status) {
            'unpaid' => 'Belum Dibayar',
            'processing' => 'Diproses',
            'ready' => $order->delivery_method === 'courier' ? 'Siap Dikirim' : 'Siap Diambil',
            'completed' => 'Selesai',
            default => ucfirst($order->status),
        };
    }

    private function rentang(string $periode): array
    {
        return match ($periode) {
            'kemarin' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            '7-hari' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }
}
