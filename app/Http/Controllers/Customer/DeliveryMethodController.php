<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\DeliveryMethod;

class DeliveryMethodController extends Controller
{
    // Opsi cara menerima pesanan + ongkir (diantar / ambil di tempat).
    public function index()
    {
        return response()->json([
            'data' => ['metode' => DeliveryMethod::options()],
        ]);
    }
}
