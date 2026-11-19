<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Duitku
{
    public static function configured(): bool
    {
        return (bool) config('services.duitku.merchant_code') && (bool) config('services.duitku.api_key');
    }

    public static function merchantCode(): ?string
    {
        return config('services.duitku.merchant_code') ?: null;
    }

    public static function sandbox(): bool
    {
        return ! (bool) config('services.duitku.is_production');
    }

    private static function baseUrl(): string
    {
        return self::sandbox()
            ? 'https://sandbox.duitku.com'
            : 'https://passport.duitku.com';
    }

    private static function signature(string $raw): string
    {
        return hash_hmac('sha256', $raw, (string) config('services.duitku.api_key'));
    }

    // qris/va -> kode metode Duitku. Lihat daftar metode di docs.duitku.com.
    public static function methodCode(?string $metode): string
    {
        return $metode === 'va'
            ? (string) config('services.duitku.method_va', 'BC')
            : (string) config('services.duitku.method_qris', 'SP');
    }

    /**
     * Buat transaksi (inquiry). Null bila gateway belum dikonfigurasi atau gagal.
     */
    public static function inquiry(Order $order): ?array
    {
        if (! self::configured()) {
            return null;
        }

        $amount = (int) round((float) $order->grand_total);
        $merchantCode = (string) config('services.duitku.merchant_code');

        $body = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amount,
            'paymentMethod' => self::methodCode($order->payment_type),
            'merchantOrderId' => $order->order_number,
            'productDetails' => 'Pesanan '.$order->order_number,
            // Total itemDetails wajib sama dengan paymentAmount -> satu baris sesuai grand total.
            'itemDetails' => [[
                'name' => 'Pesanan '.$order->order_number,
                'price' => $amount,
                'quantity' => 1,
            ]],
            'email' => $order->customer?->email ?: 'customer@chikumi.test',
            'phoneNumber' => $order->customer?->phone_number ?: '',
            'customerVaName' => mb_substr($order->customer?->name ?: 'Pelanggan Chikumi', 0, 20),
            // Duitku mewajibkan callbackUrl. Di lokal biarkan APP_URL; status tetap tersinkron via polling.
            'callbackUrl' => config('services.duitku.callback_url') ?: url('/payment/callback'),
            'returnUrl' => url('/customer/pembayaran?pesanan='.$order->id),
            'expiryPeriod' => (int) config('services.duitku.expiry_minutes', 60),
            'signature' => self::signature($merchantCode.$order->order_number.$amount),
        ];

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.duitku.timeout', 15))
                ->post(self::baseUrl().'/webapi/api/merchant/v2/inquiry', $body);

            $json = $response->json() ?? [];

            if (($json['statusCode'] ?? null) !== '00') {
                Log::warning('Duitku inquiry ditolak.', ['order' => $order->order_number, 'respon' => $json]);

                return null;
            }

            $payload = self::payload($json, $order, $amount, false);

            // Data inquiry (paymentUrl, VA, qrString) hanya dikirim sekali -> simpan agar tidak perlu hit ulang.
            Cache::put(self::trxCacheKey($order->order_number), $payload, now()->addHours(24));

            return $payload;
        } catch (Throwable $e) {
            Log::warning('Duitku inquiry gagal: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Cek status transaksi terbaru (null bila gateway mati / transaksi tidak dikenal).
     */
    public static function status(Order $order): ?array
    {
        if (! self::configured()) {
            return null;
        }

        // Duitku memblokir hit berlebih (~1 jam). Cukup cek sekali per 30 detik per pesanan.
        $kunci = 'duitku:cek:'.$order->order_number;

        if (Cache::has($kunci)) {
            return self::cached($order->order_number);
        }
        Cache::put($kunci, true, now()->addSeconds(30));

        $merchantCode = (string) config('services.duitku.merchant_code');

        try {
            $response = Http::acceptJson()
                ->timeout((int) config('services.duitku.timeout', 15))
                ->post(self::baseUrl().'/webapi/api/merchant/transactionStatus', [
                    'merchantCode' => $merchantCode,
                    'merchantOrderId' => $order->order_number,
                    'signature' => self::signature($merchantCode.$order->order_number),
                ]);

            $json = $response->json() ?? [];

            // 00 = sukses, 01 = proses, 02 = gagal/kedaluwarsa.
            if (! in_array((string) ($json['statusCode'] ?? ''), ['00', '01', '02'], true)) {
                return null;
            }

            $lama = self::cached($order->order_number) ?? [];
            $payload = array_merge($lama, array_filter(
                self::payload($json, $order, (int) round((float) $order->grand_total), true),
                fn ($nilai) => $nilai !== null && $nilai !== ''
            ));
            Cache::put(self::trxCacheKey($order->order_number), $payload, now()->addHours(24));

            return $payload;
        } catch (Throwable $e) {
            Log::warning('Duitku cek status gagal: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Verifikasi callback: HMAC_SHA256(merchantCode + amount + merchantOrderId, apiKey).
     */
    public static function callbackValid(array $data): bool
    {
        $apiKey = (string) config('services.duitku.api_key');

        if ($apiKey === '' || empty($data['signature'])) {
            return false;
        }

        $expected = hash_hmac('sha256',
            (string) ($data['merchantCode'] ?? '')
            .(string) ($data['amount'] ?? '')
            .(string) ($data['merchantOrderId'] ?? ''),
            $apiKey
        );

        return hash_equals($expected, (string) $data['signature']);
    }

    // statusCode Duitku -> status pesanan lokal.
    public static function orderStatus(?string $statusCode): ?string
    {
        return match ($statusCode) {
            '00' => 'paid',
            '02' => 'cancelled',
            '01' => 'pending',
            default => null,
        };
    }

    public static function cached(string $orderNumber): ?array
    {
        $payload = Cache::get(self::trxCacheKey($orderNumber));

        return is_array($payload) ? $payload : null;
    }

    private static function trxCacheKey(string $orderNumber): string
    {
        return 'duitku:trx:'.$orderNumber;
    }

    public static function labelStatus(?string $kode): ?string
    {
        return match ((string) $kode) {
            '00' => 'Berhasil dibayar',
            '01' => 'Menunggu pembayaran',
            '02' => 'Gagal / kedaluwarsa',
            default => null,
        };
    }

    private static function payload(array $json, Order $order, int $amount, bool $dariCekStatus): array
    {
        // statusCode "00" pada inquiry berarti permintaan berhasil, bukan sudah dibayar.
        $kode = $dariCekStatus ? (string) ($json['statusCode'] ?? '') : null;
        $va = $json['vaNumber'] ?? null;

        return [
            'sumber' => 'duitku',
            'reference' => $json['reference'] ?? $order->snap_token,
            'payment_url' => $json['paymentUrl'] ?? null,
            'va_number' => $va,
            'bank' => $order->payment_type === 'va'
                ? (string) config('services.duitku.bank_label', 'BCA')
                : null,
            'qr_string' => $json['qrString'] ?? null,
            'payment_method' => self::methodCode($order->payment_type),
            'amount' => (float) ($json['amount'] ?? $amount),
            'status_code' => $kode,
            'status' => $kode === null ? 'pending' : self::orderStatus($kode),
            'status_gateway' => $kode === null ? 'Menunggu pembayaran' : self::labelStatus($kode),
            'pesan' => $json['statusMessage'] ?? null,
        ];
    }
}
