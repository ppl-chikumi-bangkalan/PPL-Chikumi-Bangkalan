<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function pageIndex()
    {
        return view('owner.produk.index', [
            'products' => Product::with(['category', 'sizes', 'variants'])->orderBy('name')->get(),
        ]);
    }

    public function pageStok()
    {
        $sizes = ProductSize::with('product')->orderBy('product_id')->orderBy('size_name')->get();
        $variants = Variant::orderBy('type')->orderBy('name')->get();

        $summary = [
            'total' => $sizes->count() + $variants->count(),
            'safe' => $sizes->filter(fn ($size) => $size->stock > $size->min_stock)->count()
                + $variants->where('is_available', true)->count(),
            'low' => $sizes->filter(fn ($size) => $size->stock > 0 && $size->stock <= $size->min_stock)->count(),
            'empty' => $sizes->where('stock', '<=', 0)->count()
                + $variants->where('is_available', false)->count(),
        ];

        return view('owner.produk.stok', compact('sizes', 'variants', 'summary'));
    }

    public function pageEdit(Product $produk, ?ProductSize $ukuran = null)
    {
        $produk->load(['category', 'sizes', 'variants']);
        $ukuran ??= $produk->sizes->first();

        abort_unless($ukuran === null || $ukuran->product_id === $produk->id, 404);

        $previous = url()->previous();
        $back = str_contains($previous, '/edit') ? route('owner.produk') : $previous;

        return view('owner.produk.edit', compact('produk', 'ukuran', 'back'));
    }

    public function availability()
    {
        $sizes = ProductSize::with('product:id,name,is_active')
            ->orderBy('product_id')
            ->orderBy('size_name')
            ->get()
            ->map(fn (ProductSize $size) => [
                'product_id' => $size->product_id,
                'product' => $size->product->name,
                'size' => $size->size_name,
                'stock' => $size->stock,
                'min_stock' => $size->min_stock,
                'status' => ! $size->product->is_active || $size->stock <= 0
                    ? 'habis'
                    : ($size->stock <= $size->min_stock ? 'menipis' : 'aman'),
            ]);

        $variants = Variant::with('product:id,name')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn (Variant $variant) => [
                'product_id' => $variant->product_id,
                'product' => $variant->product->name,
                'type' => $variant->type,
                'name' => $variant->name,
                'status' => $variant->is_available ? 'aman' : 'habis',
            ]);

        $statuses = $sizes->pluck('status')->concat($variants->pluck('status'));

        return response()->json([
            'data' => [
                'ringkasan' => [
                    'total' => $statuses->count(),
                    'aman' => $statuses->filter(fn ($status) => $status === 'aman')->count(),
                    'menipis' => $statuses->filter(fn ($status) => $status === 'menipis')->count(),
                    'habis' => $statuses->filter(fn ($status) => $status === 'habis')->count(),
                ],
                'produk' => $sizes->values(),
                'varian' => $variants->values(),
            ],
        ]);
    }

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
        unset($data['category_name']);

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
