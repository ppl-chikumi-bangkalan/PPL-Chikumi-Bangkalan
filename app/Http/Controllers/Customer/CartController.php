<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductSize;
use App\Support\PriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResolvesCustomer;

    public function index(Request $request)
    {
        $items = $this->cartItems($this->customerId($request));

        return response()->json([
            'data' => [
                'total_item' => $items->sum('quantity'),
                'subtotal' => round($items->sum(fn ($i) => $i['subtotal']), 2),
                'item' => $items->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_size_id' => ['required', 'integer', 'exists:product_sizes,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'variant_ids' => ['array'],
            'variant_ids.*' => ['integer', 'distinct'],
        ]);
        $customerId = $this->customerId($request);
        $size = ProductSize::with('product:id,name,is_active')->findOrFail($data['product_size_id']);
        abort_unless($size->product->is_active, 422, 'Produk tidak tersedia.');
        abort_unless($size->stock >= $data['quantity'], 422, "Stok {$size->size_name} hanya {$size->stock}.");

        $variants = PriceCalculator::resolveVariants($size->product_id, $data['variant_ids'] ?? []);

        DB::transaction(function () use ($customerId, $size, $data, $variants) {
            $variantIds = $variants->pluck('id')->sort()->values();
            // Baris identik (ukuran + varian sama) -> jumlah ditumpuk, tidak dobel.
            $cart = Cart::with('variants')->where('customer_id', $customerId)
                ->where('product_size_id', $size->id)->get()
                ->first(fn ($c) => $c->variants->pluck('id')->sort()->values()->all() === $variantIds->all());
            if ($cart) {
                $cart->update(['quantity' => $cart->quantity + $data['quantity']]);
            } else {
                $cart = Cart::create([
                    'customer_id' => $customerId,
                    'product_size_id' => $size->id,
                    'quantity' => $data['quantity'],
                ]);
                $cart->variants()->sync($variantIds);
            }
        });

        return response()->json(['message' => 'Berhasil menambah ke keranjang.'], 201);
    }

    public function update(Request $request, Cart $keranjang)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $this->authorizeCart($request, $keranjang);
        $size = ProductSize::findOrFail($keranjang->product_size_id);
        abort_unless($size->stock >= $data['quantity'], 422, "Stok hanya {$size->stock}.");
        $keranjang->update($data);

        return response()->json(['message' => 'Jumlah diperbarui.']);
    }

    public function destroy(Request $request, Cart $keranjang)
    {
        $this->authorizeCart($request, $keranjang);
        $keranjang->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang.']);
    }

    private function cartItems(int $customerId)
    {
        return Cart::with([
            'productSize.product:id,name,image_url',
            'productSize:id,product_id,size_name,base_price,stock',
            'variants:id,name,type,additional_price',
        ])
            ->where('customer_id', $customerId)
            ->latest('id')
            ->get()
            ->map(function (Cart $cart) {
                $size = $cart->productSize;
                $price = PriceCalculator::breakdown($size, $cart->variants, $cart->quantity);

                return [
                    'id' => $cart->id,
                    'produk' => $size->product->name,
                    'gambar' => $size->product->image_url,
                    'ukuran' => $size->size_name,
                    'product_size_id' => $size->id,
                    'varian' => $price['varian'],
                    'harga_satuan' => $price['harga_satuan'],
                    'quantity' => $cart->quantity,
                    'subtotal' => $price['subtotal'],
                    'stok_cukup' => $size->stock >= $cart->quantity,
                ];
            });
    }

    private function authorizeCart(Request $request, Cart $keranjang): void
    {
        abort_unless($keranjang->customer_id === $this->customerId($request), 403);
    }
}
