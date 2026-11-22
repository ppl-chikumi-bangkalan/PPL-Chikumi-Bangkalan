<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\StockHistory;
use App\Support\DeliveryMethod;
use App\Support\PriceCalculator;
use App\Support\ShippingCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use ResolvesCustomer;

    public function index(Request $request)
    {
        $filter = $request->validate([
            'status' => ['nullable', Rule::in(Order::STATUS)],
        ]);

        $orders = Order::with(['items.productSize.product:id,name,image_url', 'items.variants'])
            ->where('customer_id', $this->customerId($request))
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest('id')
            ->get()
            ->map(fn (Order $o) => $this->payload($o));

        return response()->json([
            'data' => ['total' => $orders->count(), 'pesanan' => $orders->values()],
        ]);
    }

    // Detail satu pesanan milik pelanggan (dipakai halaman detail & status).
    public function show(Request $request, Order $pesanan)
    {
        abort_unless((int) $pesanan->customer_id === $this->customerId($request), 403);

        $pesanan->load([
            'items.productSize.product:id,name,image_url',
            'items.variants',
            'shippingOrigin',
            'courier',
        ]);

        return response()->json(['data' => array_merge($this->payload($pesanan), [
            'kurir' => $pesanan->courier?->name,
            'telepon_kurir' => $pesanan->courier?->phone,
            'titik_asal' => $pesanan->shippingOrigin?->name,
        ])]);
    }

    private function payload(Order $order): array
    {
        return [
            'id' => $order->id,
            'nomor' => $order->order_number,
            'metode' => $order->delivery_method,
            'alamat' => $order->shipping_address,
            'subtotal' => (float) $order->subtotal,
            'ongkir' => (float) $order->shipping_cost,
            'total' => (float) $order->grand_total,
            'pembayaran' => $order->payment_type,
            'status' => $order->status,
            'status_label' => $this->statusLabel($order),
            'tanggal' => $order->created_at->format('Y-m-d H:i'),
            'dibayar_pada' => $order->paid_at?->format('Y-m-d H:i'),
            'dikonfirmasi_pada' => $order->shipping_confirmed_at?->format('Y-m-d H:i'),
            'bisa_dibatalkan' => $order->status === 'unpaid',
            'jumlah_item' => $order->items->sum('quantity'),
            'item' => $order->items->map(fn (OrderItem $i) => [
                'produk' => $i->productSize->product->name,
                'gambar' => $i->productSize->product->image_url,
                'ukuran' => $i->productSize->size_name,
                'varian' => $i->variants->pluck('name'),
                'harga_satuan' => (float) $i->unit_price,
                'quantity' => $i->quantity,
                'subtotal' => (float) $i->subtotal,
            ])->values(),
            'timeline' => $this->timeline($order),
        ];
    }

    private function statusLabel(Order $order): string
    {
        return match ($order->status) {
            'unpaid' => 'Belum Dibayar',
            'processing' => 'Diproses',
            'ready' => $order->delivery_method === DeliveryMethod::COURIER ? 'Siap Dikirim' : 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($order->status),
        };
    }

    // Tahapan status untuk timeline halaman detail.
    private function timeline(Order $order): array
    {
        $tahap = ['unpaid', 'processing', 'ready', 'completed'];
        $posisi = array_search($order->status, $tahap, true);
        $label = [
            'unpaid' => 'Belum Dibayar',
            'processing' => 'Diproses',
            'ready' => $order->delivery_method === DeliveryMethod::COURIER ? 'Siap Dikirim' : 'Siap Diambil',
            'completed' => 'Selesai',
        ];
        $waktu = [
            'unpaid' => $order->created_at,
            'processing' => $order->paid_at,
            'ready' => $order->shipping_confirmed_at,
            'completed' => $order->status === 'completed' ? $order->updated_at : null,
        ];

        return collect($tahap)->map(fn (string $kode, int $i) => [
            'kode' => $kode,
            'label' => $label[$kode],
            'waktu' => $waktu[$kode]?->format('Y-m-d H:i'),
            'selesai' => $posisi !== false && $i < $posisi,
            'aktif' => $posisi !== false && $i === $posisi,
        ])->all();
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'delivery_method' => ['required', Rule::in(DeliveryMethod::codes())],
            'address_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'in:qris,va'],
        ]);
        $customerId = $this->customerId($request);

        $alamat = null;
        $ongkir = DeliveryMethod::shippingCost($data['delivery_method']);
        if (DeliveryMethod::requiresAddress($data['delivery_method'])) {
            // Alamat pilihan akun; tanpa address_id pakai alamat terbaru.
            $address = Address::where('customer_id', $customerId)
                ->when($data['address_id'] ?? null, fn ($q, $id) => $q->whereKey($id))
                ->latest('id')
                ->first();
            abort_if(! $address, 422, 'Alamat pengiriman tidak ditemukan untuk akun ini.');
            $alamat = trim("{$address->recipient_name} ({$address->phone}) — {$address->detail}, {$address->city} {$address->postal_code}");
            // Ongkir dari API; fallback tarif flat bila kota belum terpetakan atau API gagal.
            $ongkir = ShippingCost::quote($address->city_id)['ongkir'];
        }

        $carts = Cart::with(['productSize', 'variants:id,additional_price,is_available'])
            ->where('customer_id', $customerId)->get();
        abort_if($carts->isEmpty(), 422, 'Keranjang masih kosong.');

        $order = DB::transaction(function () use ($carts, $customerId, $data, $alamat, $ongkir) {
            $subtotal = 0.0;
            $order = Order::create([
                'order_number' => 'CHKM-'.now()->format('ymdHis').'-'.random_int(100, 999),
                'customer_id' => $customerId,
                'delivery_method' => $data['delivery_method'],
                'shipping_address' => $alamat,
                'subtotal' => 0,
                'shipping_cost' => $ongkir,
                'grand_total' => 0,
                'payment_type' => $data['payment_method'] ?? null,
                'status' => 'unpaid',
            ]);

            // Urutan id tetap -> hindari deadlock saat dua checkout barengan.
            foreach ($carts->sortBy('product_size_id') as $cart) {
                $size = ProductSize::lockForUpdate()->findOrFail($cart->product_size_id);
                if ($size->stock < $cart->quantity) {
                    abort(422, "Stok {$size->size_name} tidak cukup — sisa {$size->stock}.");
                }

                $variants = PriceCalculator::resolveVariants(
                    $size->product_id,
                    $cart->variants->pluck('id')->all()
                );
                $price = PriceCalculator::breakdown($size, $variants, $cart->quantity);

                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_size_id' => $size->id,
                    'unit_price' => $price['harga_satuan'],
                    'quantity' => $cart->quantity,
                    'subtotal' => $price['subtotal'],
                ]);
                foreach ($variants as $variant) {
                    $item->variants()->attach($variant->id, ['variant_price' => $variant->additional_price]);
                }
                $size->decrement('stock', $cart->quantity);
                StockHistory::create([
                    'product_size_id' => $size->id,
                    'user_id' => $customerId,
                    'type' => 'out',
                    'quantity' => $cart->quantity,
                    'source' => 'online_order',
                    'reference_id' => $order->id,
                ]);
                $subtotal += $price['subtotal'];
            }

            $order->update(['subtotal' => $subtotal, 'grand_total' => round($subtotal + $ongkir, 2)]);
            Cart::where('customer_id', $customerId)->delete();

            return $order;
        });

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'data' => [
                'id' => $order->id,
                'nomor' => $order->order_number,
                'total' => (float) $order->grand_total,
                'status' => $order->status,
            ],
        ], 201);
    }

    // Pelanggan hanya bisa membatalkan pesanan yang belum dibayar.
    public function cancel(Request $request, Order $pesanan)
    {
        abort_unless($pesanan->customer_id === $this->customerId($request), 403);
        abort_unless($pesanan->status === 'unpaid', 422, 'Pesanan tidak bisa dibatalkan lagi.');

        DB::transaction(function () use ($pesanan) {
            foreach ($pesanan->items()->with('productSize')->get() as $item) {
                $item->productSize->increment('stock', $item->quantity);
                StockHistory::create([
                    'product_size_id' => $item->product_size_id,
                    'user_id' => $pesanan->customer_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'source' => 'online_order',
                    'reference_id' => $pesanan->id,
                    'notes' => 'Pembatalan pesanan',
                ]);
            }
            $pesanan->update(['status' => 'cancelled']);
        });

        return response()->json(['message' => 'Pesanan dibatalkan, stok dikembalikan.']);
    }
}
