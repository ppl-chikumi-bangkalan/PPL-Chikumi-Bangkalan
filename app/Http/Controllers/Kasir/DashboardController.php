<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function page()
    {
        $produk = Product::with(['sizes', 'variants'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                $variants = $product->variants;
                $tersedia = $variants->where('is_available', true);
                $tambahan = fn (string $type) => $tersedia->where('type', $type)->min('additional_price');

                return [
                    'id' => $product->id,
                    'nama' => $product->name,
                    'gambar' => $product->image_url && file_exists(public_path($product->image_url))
                        ? asset($product->image_url)
                        : asset('images/chikumi-1.png'),
                    'ukuran' => $product->sizes->map(function ($size) use ($tambahan) {
                        $harga = (float) $size->base_price;
                        $seasoning = $tambahan('seasoning');
                        $dip = $tambahan('dipping_sauce');

                        return [
                            'id' => $size->id,
                            'nama' => $size->size_name,
                            'harga' => $harga,
                            'stok' => $size->stock,
                            'min_stok' => $size->min_stock,
                            'harga_seasoning' => $seasoning === null ? null : $harga + (float) $seasoning,
                            'harga_dip' => $dip === null ? null : $harga + (float) $dip,
                        ];
                    })->values(),
                    'varian' => $variants->groupBy('type')->map(fn ($group, $type) => [
                        'type' => $type,
                        'pilihan' => $group->map(fn ($variant) => [
                            'id' => $variant->id,
                            'nama' => $variant->name,
                            'harga' => (float) $variant->additional_price,
                            'tersedia' => (bool) $variant->is_available,
                        ])->values(),
                    ])->values(),
                ];
            })
            ->values();

        $labels = [
            'filling' => ['judul' => 'Filling', 'aturan' => 'pilih 1'],
            'dipping_sauce' => ['judul' => 'Dip Sauce', 'aturan' => 'pilih 1'],
            'seasoning' => ['judul' => 'Seasoning', 'aturan' => 'boleh lebih dari 1'],
        ];
        $rasa = collect($labels)
            ->map(function (array $meta, string $type) use ($produk) {
                $pilihan = $produk
                    ->flatMap(fn ($product) => collect($product['varian'])->firstWhere('type', $type)['pilihan'] ?? [])
                    ->unique('nama')
                    ->values();

                return [...$meta, 'pilihan' => $pilihan];
            })
            ->filter(fn (array $group) => $group['pilihan']->isNotEmpty())
            ->values();

        return view('kasir.dashboard', ['produk' => $produk, 'rasa' => $rasa]);
    }
}
