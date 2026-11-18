<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Support\ShippingCost;
use Illuminate\Http\Request;

class ShippingCostController extends Controller
{
    use ResolvesCustomer;

    // Estimasi ongkir; fallback tarif flat bila API gagal. Bisa pakai address_id atau city_id langsung.
    public function quote(Request $request)
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer', 'min:1'],
            'berat' => ['nullable', 'integer', 'min:1'],
        ]);

        $alamatId = null;
        $cityId = $data['city_id'] ?? null;

        if ($cityId === null) {
            $address = Address::where('customer_id', $this->customerId($request))
                ->when($data['address_id'] ?? null, fn ($query, $id) => $query->whereKey($id))
                ->latest('id')
                ->first();

            abort_if(! $address, 422, 'Alamat pengiriman tidak ditemukan untuk akun ini.');

            $alamatId = $address->id;
            $cityId = $address->city_id;
        }

        return response()->json([
            'data' => ShippingCost::quote($cityId, $data['berat'] ?? 1000) + [
                'alamat_id' => $alamatId,
            ],
        ]);
    }

    // Pencarian kota/kecamatan tujuan (RajaOngkir) untuk form alamat.
    public function cities(Request $request)
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        return response()->json(['data' => ShippingCost::cities($data['q'])]);
    }
}
