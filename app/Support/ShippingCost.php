<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShippingCost
{
    // ponytail: satu kurir & berat default 1000 g; tambah parameter berat per item saat produk punya kolom berat.
    public static function quote(?int $destinationCityId, int $weightGram = 1000): array
    {
        $flat = [
            'ongkir' => DeliveryMethod::shippingCost(DeliveryMethod::COURIER),
            'sumber' => 'flat',
            'etd' => null,
        ];

        if (! $destinationCityId || ! config('services.rajaongkir.key')) {
            return $flat;
        }

        $hasil = Cache::remember(
            "ongkir:{$destinationCityId}:{$weightGram}",
            now()->addMinutes(10),
            fn () => self::fetch($destinationCityId, $weightGram)
        );

        return $hasil ?? $flat;
    }

    private static function fetch(int $destinationCityId, int $weightGram): ?array
    {
        try {
            $response = Http::withHeaders(['key' => config('services.rajaongkir.key')])
                ->timeout((int) config('services.rajaongkir.timeout'))
                ->asForm()
                ->post(rtrim(config('services.rajaongkir.base_url'), '/').'/calculate/domestic-cost', [
                    'origin' => config('services.rajaongkir.origin'),
                    'destination' => $destinationCityId,
                    'weight' => $weightGram,
                    'courier' => config('services.rajaongkir.courier'),
                ]);

            $services = collect($response->json('data') ?? [])
                ->filter(fn ($service) => is_numeric($service['cost'] ?? null));

            if ($services->isEmpty()) {
                Log::warning('Ongkir RajaOngkir tidak mengembalikan tarif.', ['status' => $response->status()]);

                return null;
            }

            $termurah = $services->sortBy(fn ($service) => (int) $service['cost'])->first();

            return [
                'ongkir' => (float) $termurah['cost'],
                'sumber' => 'rajaongkir',
                'etd' => $termurah['etd'] ?? null,
            ];
        } catch (Throwable $e) {
            Log::warning('Ongkir RajaOngkir gagal: '.$e->getMessage());

            return null;
        }
    }

    // Cari kota/kecamatan tujuan untuk form alamat.
    // Tanpa API key -> pakai daftar wilayah lokal (config/wilayah.php) dengan id null (ongkir flat).
    public static function cities(string $search): array
    {
        $search = trim($search);

        if (trim($search) === '') {
            return [];
        }

        if (! config('services.rajaongkir.key')) {
            return self::localCities($search);
        }

        return Cache::remember('kota:'.mb_strtolower(trim($search)), now()->addDay(), function () use ($search) {
            try {
                $response = Http::withHeaders(['key' => config('services.rajaongkir.key')])
                    ->timeout((int) config('services.rajaongkir.timeout'))
                    ->get(rtrim(config('services.rajaongkir.base_url'), '/').'/destination/domestic-destination', [
                        'search' => $search,
                        'limit' => 10,
                        'offset' => 0,
                    ]);

                return collect($response->json('data') ?? [])
                    ->filter(fn ($kota) => isset($kota['id'], $kota['label']))
                    ->map(fn ($kota) => [
                        'id' => (int) $kota['id'],
                        'label' => $kota['label'],
                        'kota' => $kota['city_name'] ?? null,
                        'kecamatan' => $kota['district_name'] ?? null,
                        'kode_pos' => $kota['zip_code'] ?? null,
                    ])
                    ->values()
                    ->all();
            } catch (Throwable $e) {
                Log::warning('Pencarian kota RajaOngkir gagal: '.$e->getMessage());

                return self::localCities($search);
            }
        });
    }

    private static function localCities(string $search): array
    {
        $kata = mb_strtolower($search);

        return collect(config('wilayah', []))
            ->filter(fn ($wilayah) => str_contains(mb_strtolower(implode(' ', $wilayah)), $kata))
            ->take(10)
            ->map(fn ($wilayah) => [
                'id' => null,
                'label' => "{$wilayah[0]}, {$wilayah[1]}, {$wilayah[2]}",
                'kota' => $wilayah[1],
                'kecamatan' => $wilayah[0],
                'kode_pos' => $wilayah[3],
            ])
            ->values()
            ->all();
    }
}
