<?php

namespace App\Support;

class DeliveryMethod
{
    public const PICKUP = 'pickup';

    public const COURIER = 'courier';

    // ponytail: ongkir flat per metode; pindah ke tabel/config saat tarif per area/kota diperlukan.
    private const ONGKIR = [
        self::PICKUP => 0.0,
        self::COURIER => 5000.0,
    ];

    public static function codes(): array
    {
        return array_keys(self::ONGKIR);
    }

    public static function shippingCost(string $code): float
    {
        return self::ONGKIR[$code] ?? 0.0;
    }

    public static function requiresAddress(string $code): bool
    {
        return $code === self::COURIER;
    }

    // Daftar cara menerima pesanan untuk halaman checkout.
    public static function options(): array
    {
        return [
            [
                'kode' => self::COURIER,
                'label' => 'Diantar',
                'ongkir' => self::ONGKIR[self::COURIER],
                'butuh_alamat' => true,
            ],
            [
                'kode' => self::PICKUP,
                'label' => 'Ambil di tempat',
                'ongkir' => self::ONGKIR[self::PICKUP],
                'butuh_alamat' => false,
            ],
        ];
    }
}
