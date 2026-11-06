<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // Query katalog untuk pelanggan: hanya produk aktif,
    // ukuran/variant yang menampilkan harga & ketersediaannya.
    public function index(Request $request)
    {
        $filters = $request->validate([
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'hanya_tersedia' => ['nullable'],
        ]);

        $products = Product::with(['category:id,name', 'sizes', 'variants'])
            ->where('is_active', true)
            ->when($filters['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when(! empty($filters['hanya_tersedia']), fn ($q) => $q->whereHas('sizes', fn ($s) => $s->where('stock', '>', 0)))
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => $product->toCustomerArray());

        return response()->json([
            'data' => [
                'total' => $products->count(),
                'produk' => $products,
            ],
        ]);
    }
}
