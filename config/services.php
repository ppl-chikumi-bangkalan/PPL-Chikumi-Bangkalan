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

    'duitku' => [
        // Tanpa merchant_code + api_key, pembayaran berjalan mode simulasi.
        // Ambil di https://passport.duitku.com/merchant/Project
        'merchant_code' => env('DUITKU_MERCHANT_CODE'),
        'api_key' => env('DUITKU_API_KEY'),
        'is_production' => env('DUITKU_IS_PRODUCTION', false),
        // Kode metode Duitku: BC = BCA VA, SP = ShopeePay QRIS.
        'method_va' => env('DUITKU_METHOD_VA', 'BC'),
        'method_qris' => env('DUITKU_METHOD_QRIS', 'SP'),
        'bank_label' => env('DUITKU_BANK_LABEL', 'BCA'),
        'expiry_minutes' => env('DUITKU_EXPIRY_MINUTES', 60),
        // URL publik untuk callback. Kosong -> pakai APP_URL (lokal, Duitku tidak bisa menjangkau).
        'callback_url' => env('DUITKU_CALLBACK_URL'),
        'timeout' => env('DUITKU_TIMEOUT', 15),
    ],

];
