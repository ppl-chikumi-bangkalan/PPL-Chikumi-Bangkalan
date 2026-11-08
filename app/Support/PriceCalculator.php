<?php

namespace App\Support;

use App\Models\ProductSize;
use App\Models\Variant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PriceCalculator
{
    // Tipe yang hanya boleh dipilih satu (filling & saus). Seasoning boleh lebih dari satu.
    public const ONE_PER_TYPE = ['filling', 'dipping_sauce'];

    /**
     * Ambil & validasi varian untuk satu produk.
     * Melempar ValidationException (422) bila pilihan tidak valid.
     */
    public static function resolveVariants(int $productId, array $variantIds): Collection
    {
        $ids = array_values(array_unique(array_map('intval', $variantIds)));

        if ($ids === []) {
            return collect();
        }

        $variants = Variant::whereIn('id', $ids)->get();

        $fail = function (string $message): never {
            throw ValidationException::withMessages(['variant_ids' => $message]);
        };

        if ($variants->count() !== count($ids)) {
            $fail('Ada varian yang tidak ditemukan.');
        }

        if ($variants->contains(fn (Variant $variant) => (int) $variant->product_id !== $productId)) {
            $fail('Varian bukan dari produk ini.');
        }

        if ($variants->contains(fn (Variant $variant) => ! $variant->is_available)) {
            $fail('Ada varian yang sedang tidak tersedia.');
        }

        $limited = $variants->filter(fn (Variant $variant) => in_array($variant->type, self::ONE_PER_TYPE, true));

        if ($limited->pluck('type')->unique()->count() !== $limited->count()) {
            $fail('Pilih maksimal satu varian untuk tipe filling atau dipping sauce.');
        }

        return $variants->values();
    }

    /**
     * Hitung rincian harga: harga dasar ukuran + total tambahan varian.
     */
    public static function breakdown(ProductSize $size, Collection $variants, int $quantity = 1): array
    {
        $quantity = max(1, $quantity);
        $base = round((float) $size->base_price, 2);
        $additional = round((float) $variants->sum('additional_price'), 2);
        $unit = round($base + $additional, 2);

        return [
            'product_size_id' => $size->id,
            'ukuran' => $size->size_name,
            'harga_dasar' => $base,
            'varian' => $variants->map(fn (Variant $variant) => [
                'id' => $variant->id,
                'type' => $variant->type,
                'nama' => $variant->name,
                'tambahan_harga' => round((float) $variant->additional_price, 2),
            ])->values()->all(),
            'total_tambahan' => $additional,
            'harga_satuan' => $unit,
            'quantity' => $quantity,
            'subtotal' => round($unit * $quantity, 2),
        ];
    }
}
