<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use App\Support\PriceCalculator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Detail produk untuk halaman "atur" — sama bentuk datanya dengan katalog.
    public function show(Product $produk)
    {
        abort_unless($produk->is_active, 404);
        $produk->load(['category:id,name', 'sizes', 'variants']);

        return response()->json(['data' => $produk->toCustomerArray()]);
    }

    // Preview harga: harga dasar ukuran + total tambahan varian.
    public function price(Request $request, Product $produk)
    {
        abort_unless($produk->is_active, 404);

        $data = $request->validate([
            'product_size_id' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'variant_ids' => ['nullable', 'array'],
            'variant_ids.*' => ['integer', 'distinct'],
        ]);

        $size = ProductSize::where('product_id', $produk->id)
            ->find($data['product_size_id']);

        abort_unless($size, 422, 'Ukuran bukan milik produk ini.');

        $variants = PriceCalculator::resolveVariants($produk->id, $data['variant_ids'] ?? []);

        return response()->json([
            'data' => PriceCalculator::breakdown(
                $size,
                $variants,
                (int) ($data['quantity'] ?? 1)
            ),
        ]);
    }
}
