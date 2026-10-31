<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function store(Request $request, Product $produk)
    {
        $data = $request->validate([
            'type' => ['required', 'in:filling,dipping_sauce,seasoning'],
            'name' => ['required', 'string', 'max:100'],
            'additional_price' => ['nullable', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $taken = Variant::where('product_id', $produk->id)
            ->where('type', $data['type'])
            ->where('name', $data['name'])
            ->exists();

        if ($taken) {
            return response()->json([
                'message' => "Varian {$data['name']} sudah ada pada produk ini.",
            ], 422);
        }

        $variant = Variant::create([
            'product_id' => $produk->id,
            'type' => $data['type'],
            'name' => $data['name'],
            'additional_price' => $data['additional_price'] ?? 0,
            'is_available' => $data['is_available'] ?? true,
        ]);

        return response()->json([
            'message' => 'Varian berhasil ditambahkan.',
            'data' => $variant,
        ], 201);
    }

    public function update(Request $request, Variant $varian)
    {
        $data = $request->validate([
            'type' => ['sometimes', 'required', 'in:filling,dipping_sauce,seasoning'],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'additional_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($request->filled('is_available')) {
            $raw = $request->input('is_available');

            $coerced = in_array($raw, ['1', 'true', true], true);

            if (! in_array($raw, ['0', '1', 'true', 'false', true, false], true)) {
                return response()->json([
                    'message' => 'Nilai is_available harus 0/1 atau true/false.',
                ], 422);
            }

            $data['is_available'] = $coerced;
        }

        $varian->update($data);

        return response()->json([
            'message' => 'Varian berhasil diperbarui.',
            'data' => $varian->fresh(),
        ]);
    }

    public function updateStatus(Request $request, Variant $varian)
    {
        $raw = $request->input('is_available');

        if (! in_array($raw, ['0', '1', 'true', 'false', true, false], true)) {
            return response()->json([
                'message' => 'Nilai is_available harus 0/1 atau true/false.',
            ], 422);
        }

        $varian->update([
            'is_available' => in_array($raw, ['1', 'true', true], true),
        ]);

        return response()->json([
            'message' => $varian->is_available
                ? "Varian {$varian->name} diaktifkan."
                : "Varian {$varian->name} dinonaktifkan.",
            'data' => $varian->fresh(),
        ]);
    }

    public function destroy(Variant $varian)
    {
        $varian->delete();

        return response()->json(['message' => 'Varian berhasil dihapus.']);
    }
}
