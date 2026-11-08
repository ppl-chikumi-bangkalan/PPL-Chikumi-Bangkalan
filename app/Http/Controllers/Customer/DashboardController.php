<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function page(Request $request)
    {
        $customer = $request->user()?->role === 'customer'
            ? $request->user()
            : User::where('role', 'customer')->orderBy('id')->first();

        $products = Product::with(['sizes', 'variants'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $produk = $products->flatMap(fn (Product $product) => $product->sizes->map(fn ($size) => [
            'produk_id' => $product->id,
            'size_id' => $size->id,
            'nama' => $product->name,
            'ukuran' => $size->size_name,
            'panjang' => $size->size_name === 'Regular' ? '16 cm' : '25 cm',
            'label_topping' => $size->size_name === 'Regular' ? 'Reguler' : 'Large',
            'harga' => (float) $size->base_price,
            'tersedia' => $size->stock > 0,
        ]))->values();

        $variants = $products->flatMap(fn (Product $product) => $product->variants);

        $labels = [
            'filling' => ['judul' => 'Filling', 'aturan' => 'pilih 1'],
            'dipping_sauce' => ['judul' => 'Dip Sauce', 'aturan' => 'pilih 1'],
            'seasoning' => ['judul' => 'Seasoning', 'aturan' => 'boleh lebih dari 1'],
        ];

        $rasa = collect($labels)
            ->map(function (array $meta, string $type) use ($variants) {
                $pilihan = $variants->where('type', $type)
                    ->groupBy('name')
                    ->map(fn ($group, $name) => [
                        'nama' => $name,
                        'habis' => $group->every(fn ($variant) => ! $variant->is_available),
                    ])
                    ->values();

                return [
                    'judul' => $meta['judul'],
                    'aturan' => $meta['aturan'],
                    'pilihan' => $pilihan,
                ];
            })
            ->filter(fn (array $group) => $group['pilihan']->isNotEmpty())
            ->values();

        $alamat = $customer
            ? Address::where('customer_id', $customer->id)->latest('id')->first()
            : null;

        if ($alamat) {
            $alamat = [
                'label' => $alamat->label ?: 'Alamat',
                'detail' => trim("{$alamat->detail}, {$alamat->city} {$alamat->postal_code}"),
            ];
        }

        return view('customer.dashboard', [
            'namaUser' => $customer?->name ?? 'Pelanggan',
            'jumlahKeranjang' => $customer
                ? (int) Cart::where('customer_id', $customer->id)->sum('quantity')
                : 0,
            'alamat' => $alamat,
            'produk' => $produk,
            'rasa' => $rasa,
        ]);
    }
}
