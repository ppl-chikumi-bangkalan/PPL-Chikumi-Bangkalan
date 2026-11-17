<?php

return [

    'rajaongkir' => [
        // Komerce/RajaOngkir API v1. Tanpa key, ongkir memakai tarif flat DeliveryMethod.
        'key' => env('RAJAONGKIR_API_KEY'),
        'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),
        'origin' => env('RAJAONGKIR_ORIGIN_ID', 444),
        'courier' => env('RAJAONGKIR_COURIER', 'jne'),
        'timeout' => env('RAJAONGKIR_TIMEOUT', 5),
    ],

];
