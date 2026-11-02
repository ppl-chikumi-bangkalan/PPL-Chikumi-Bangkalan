<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\StockHistory;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    public function store(Request $request, Product $produk)
    {
        $data = $request->validate([
            'size_name' => ['required', 'in:Regular,Large'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $exists = ProductSize::where('product_id', $produk->id)
            ->where('size_name', $data['size_name'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "Ukuran {$data['size_name']} sudah ada untuk produk ini.",
            ], 422);
        }

        $data['min_stock'] = $data['min_stock'] ?? 5;
        $data['product_id'] = $produk->id;

        $size = ProductSize::create($data);

        return response()->json([
            'message' => 'Ukuran produk berhasil ditambahkan.',
            'data' => $size,
        ], 201);
    }

    public function update(Request $request, ProductSize $ukuran)
    {
        $data = $request->validate([
            'size_name' => ['required', 'in:Regular,Large'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $taken = ProductSize::where('product_id', $ukuran->product_id)
            ->where('size_name', $data['size_name'])
            ->where('id', '!=', $ukuran->id)
            ->exists();

        if ($taken) {
            return response()->json([
                'message' => "Ukuran {$data['size_name']} sudah ada untuk produk ini.",
            ], 422);
        }

        $ukuran->update($data);

        return response()->json([
            'message' => 'Ukuran berhasil diperbarui.',
            'data' => $ukuran->fresh(),
        ]);
    }

    public function updateStok(Request $request, ProductSize $ukuran)
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $selisih = $data['type'] === 'in' ? $data['quantity'] : -$data['quantity'];
        $stokBaru = $ukuran->stock + $selisih;

        if ($stokBaru < 0) {
            return response()->json([
                'message' => "Stok tidak cukup. Sisa stok: {$ukuran->stock}.",
            ], 422);
        }

        $ukuran->update(['stock' => $stokBaru]);

        StockHistory::create([
            'product_size_id' => $ukuran->id,
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'source' => 'manual_restock',
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'message' => $data['type'] === 'in'
                ? "Stok ditambah, sekarang {$stokBaru}."
                : "Stok dikurangi, sekarang {$stokBaru}.",
            'data' => $ukuran->fresh(),
        ]);
    }

    public function destroy(ProductSize $ukuran)
    {
        $ukuran->delete();

        return response()->json(['message' => 'Ukuran berhasil dihapus.']);
    }
}
