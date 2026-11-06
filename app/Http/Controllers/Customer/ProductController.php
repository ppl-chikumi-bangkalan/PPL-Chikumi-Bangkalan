<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    // Detail produk untuk halaman "atur" — sama bentuk datanya dengan katalog.
    public function show(Product $produk)
    {
        abort_unless($produk->is_active, 404);
        $produk->load(['category:id,name', 'sizes', 'variants']);

        return response()->json(['data' => $produk->toCustomerArray()]);
    }
}
