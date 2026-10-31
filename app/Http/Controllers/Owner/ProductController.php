<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Product::with(['category', 'sizes', 'variants'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Product $produk)
    {
        return response()->json([
            'data' => $produk->load(['category', 'sizes', 'variants']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_name' => ['nullable', 'string', 'max:100'],
        ]);

        $data['is_active'] = true;

        if ($request->filled('is_active')) {
            $raw = $request->input('is_active');

            if (in_array($raw, ['0', '1', 'true', 'false', true, false], true)) {
                $data['is_active'] = in_array($raw, ['1', 'true', true], true);
            }
        }

        if (empty($data['category_id']) && ! empty($data['category_name'])) {
            $category = Category::firstOrCreate(
                ['name' => $data['category_name']],
                ['name' => $data['category_name']]
            );
            $data['category_id'] = $category->id;
        }

        $product = Product::create($data);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product->load(['category', 'sizes', 'variants']),
        ], 201);
    }

    public function update(Request $request, Product $produk)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_name' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->filled('is_active')) {
            $raw = $request->input('is_active');

            if (in_array($raw, ['0', '1', 'true', 'false', true, false], true)) {
                $data['is_active'] = in_array($raw, ['1', 'true', true], true);
            }
        }

        if (empty($data['category_id']) && ! empty($data['category_name'])) {
            $category = Category::firstOrCreate(
                ['name' => $data['category_name']],
                ['name' => $data['category_name']]
            );
            $data['category_id'] = $category->id;
        }
        unset($data['category_name']);

        $produk->update($data);

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $produk->load(['category', 'sizes', 'variants']),
        ]);
    }

    public function destroy(Product $produk)
    {
        $produk->delete();

        return response()->json(['message' => 'Produk dan data terkait (varian & ukuran) telah dihapus.']);
    }
}
