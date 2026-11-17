<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use App\Models\ShippingOrigin;
use App\Support\DeliveryMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingConfirmationController extends Controller
{
    public function origins()
    {
        return response()->json([
            'data' => ShippingOrigin::orderBy('name')->get(),
        ]);
    }

    public function storeOrigin(Request $request)
    {
        $origin = ShippingOrigin::create($this->validateOrigin($request));

        return response()->json([
            'message' => 'Titik asal pengiriman berhasil ditambahkan.',
            'data' => $origin,
        ], 201);
    }

    public function updateOrigin(Request $request, ShippingOrigin $origin)
    {
        $origin->update($this->validateOrigin($request));

        return response()->json([
            'message' => 'Titik asal pengiriman berhasil diperbarui.',
            'data' => $origin->fresh(),
        ]);
    }

    public function couriers()
    {
        return response()->json([
            'data' => Courier::orderBy('name')->get(),
        ]);
    }

    public function storeCourier(Request $request)
    {
        $courier = Courier::create($this->validateCourier($request));

        return response()->json([
            'message' => 'Kurir berhasil ditambahkan.',
            'data' => $courier,
        ], 201);
    }

    public function updateCourier(Request $request, Courier $courier)
    {
        $courier->update($this->validateCourier($request));

        return response()->json([
            'message' => 'Kurir berhasil diperbarui.',
            'data' => $courier->fresh(),
        ]);
    }

    // Daftar pesanan untuk layar konfirmasi pengiriman milik owner.
    public function orders(Request $request)
    {
        $orders = Order::with(['customer:id,name,phone_number', 'items', 'shippingOrigin', 'courier'])
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('metode'), fn ($query, $metode) => $query->where('delivery_method', $metode))
            ->when($request->boolean('belum_dikonfirmasi'), fn ($query) => $query->whereNull('shipping_confirmed_at'))
            ->latest('id')
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'nomor' => $order->order_number,
                'pelanggan' => $order->customer->name,
                'telepon' => $order->customer->phone_number,
                'metode' => $order->delivery_method,
                'alamat' => $order->shipping_address,
                'subtotal' => (float) $order->subtotal,
                'ongkir' => (float) $order->shipping_cost,
                'total' => (float) $order->grand_total,
                'pembayaran' => $order->payment_type,
                'status' => $order->status,
                'tanggal' => $order->created_at->format('Y-m-d H:i'),
                'jumlah_item' => $order->items->count(),
                'titik_asal' => $order->shippingOrigin?->name,
                'alamat_titik_asal' => $order->shippingOrigin?->address,
                'kurir' => $order->courier?->name,
                'telepon_kurir' => $order->courier?->phone,
                'dikonfirmasi_pada' => $order->shipping_confirmed_at?->format('Y-m-d H:i'),
                'dikonfirmasi_oleh' => $order->shipping_confirmed_by,
            ]);

        return response()->json([
            'data' => ['total' => $orders->count(), 'pesanan' => $orders->values()],
        ]);
    }

    // Owner mengonfirmasi titik asal & kurir untuk pesanan yang diantar.
    public function confirm(Request $request, Order $pesanan)
    {
        abort_unless(
            $pesanan->delivery_method === DeliveryMethod::COURIER,
            422,
            'Konfirmasi titik asal & kurir hanya untuk pesanan yang diantar.'
        );
        abort_unless(
            in_array($pesanan->status, ['unpaid', 'processing'], true),
            422,
            'Pesanan sudah tidak bisa dikonfirmasi pengirimannya.'
        );

        $data = $request->validate([
            'shipping_origin_id' => [
                'required',
                'integer',
                Rule::exists('shipping_origins', 'id')->where('is_active', true),
            ],
            'courier_id' => [
                'required',
                'integer',
                Rule::exists('couriers', 'id')->where('is_active', true),
            ],
        ]);

        $pesanan->update([
            'shipping_origin_id' => $data['shipping_origin_id'],
            'courier_id' => $data['courier_id'],
            'shipping_confirmed_at' => now(),
            'shipping_confirmed_by' => $request->user()->id,
            'status' => $pesanan->status === 'unpaid' ? 'processing' : $pesanan->status,
        ]);

        $pesanan->load(['shippingOrigin', 'courier']);

        return response()->json([
            'message' => 'Titik asal pengiriman & kurir berhasil dikonfirmasi.',
            'data' => [
                'id' => $pesanan->id,
                'nomor' => $pesanan->order_number,
                'status' => $pesanan->status,
                'titik_asal' => $pesanan->shippingOrigin?->name,
                'alamat_titik_asal' => $pesanan->shippingOrigin?->address,
                'kurir' => $pesanan->courier?->name,
                'telepon_kurir' => $pesanan->courier?->phone,
                'dikonfirmasi_pada' => $pesanan->shipping_confirmed_at?->format('Y-m-d H:i'),
                'dikonfirmasi_oleh' => $pesanan->shipping_confirmed_by,
            ],
        ]);
    }

    private function validateOrigin(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function validateCourier(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
