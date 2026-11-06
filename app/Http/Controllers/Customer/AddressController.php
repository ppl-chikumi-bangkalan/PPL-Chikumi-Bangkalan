<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ResolvesCustomer;

    public function index(Request $request)
    {
        $addresses = Address::where('customer_id', $this->customerId($request))
            ->latest('id')->get()
            ->map(fn (Address $a) => $this->shape($a));

        return response()->json([
            'data' => ['total' => $addresses->count(), 'alamat' => $addresses->values()],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['customer_id'] = $this->customerId($request);
        $address = Address::create($data);

        return response()->json(['message' => 'Alamat disimpan.', 'data' => $this->shape($address)], 201);
    }

    public function update(Request $request, Address $alamat)
    {
        $this->authorizeAddress($request, $alamat);
        $alamat->update($this->validated($request));

        return response()->json(['message' => 'Alamat diperbarui.', 'data' => $this->shape($alamat->fresh())]);
    }

    public function destroy(Request $request, Address $alamat)
    {
        $this->authorizeAddress($request, $alamat);
        $alamat->delete();

        return response()->json(['message' => 'Alamat dihapus.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'detail' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);
    }

    private function shape(Address $a): array
    {
        return [
            'id' => $a->id,
            'label' => $a->label,
            'penerima' => $a->recipient_name,
            'telepon' => $a->phone,
            'detail' => $a->detail,
            'kota' => $a->city,
            'kode_pos' => $a->postal_code,
        ];
    }

    private function authorizeAddress(Request $request, Address $alamat): void
    {
        abort_unless($alamat->customer_id === $this->customerId($request), 403);
    }
}
