<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\StockHistory;
use App\Support\PriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ResolvesCustomer;

    // ponytail: ongkir courier flat; pindah ke tabel/config saat tarif per kota diperlukan.
    private const ONGKIR_COURIER = 5000.0;

    public function index(Request $request)
    {
        $orders = Order::with(['items.productSize.product:id,name,image_url', 'items.variants'])
            ->where('customer_id', $this->customerId($request))
            ->latest('id')
            ->get()
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'nomor' => $o->order_number,
                'metode' => $o->delivery_method,
                'alamat' => $o->shipping_address,
                'subtotal' => (float) $o->subtotal,
                'ongkir' => (float) $o->shipping_cost,
                'total' => (float) $o->grand_total,
                'pembayaran' => $o->payment_type,
                'status' => $o->status,
                'tanggal' => $o->created_at->format('Y-m-d H:i'),
                'item' => $o->items->map(fn (OrderItem $i) => [
                    'produk' => $i->productSize->product->name,
                    'gambar' => $i->productSize->product->image_url,
                    'ukuran' => $i->productSize->size_name,
                    'varian' => $i->variants->pluck('name'),
                    'harga_satuan' => (float) $i->unit_price,
                    'quantity' => $i->quantity,
                    'subtotal' => (float) $i->subtotal,
                ])->values(),
            ]);

        return response()->json([
            'data' => ['total' => $orders->count(), 'pesanan' => $orders->values()],
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'delivery_method' => ['required', 'in:courier,pickup'],
            'address_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'in:qris,va'],
        ]);
        $customerId = $this->customerId($request);

        $alamat = null;
        $ongkir = 0.0;
        if ($data['delivery_method'] === 'courier') {
            // Alamat pilihan akun; tanpa address_id pakai alamat terbaru.
            $address = Address::where('customer_id', $customerId)
                ->when($data['address_id'] ?? null, fn ($q, $id) => $q->whereKey($id))
                ->latest('id')
                ->first();
            abort_if(! $address, 422, 'Alamat pengiriman tidak ditemukan untuk akun ini.');
            $alamat = trim("{$address->recipient_name} ({$address->phone}) — {$address->detail}, {$address->city} {$address->postal_code}");
            $ongkir = self::ONGKIR_COURIER;
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
