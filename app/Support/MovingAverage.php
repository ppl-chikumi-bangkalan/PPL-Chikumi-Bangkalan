<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovingAverage
{
    // Jendela perhitungan: 3 minggu terakhir (termasuk hari ini).
    public const HARI = 21;

    // Saran restock menutup kebutuhan 1 minggu ke depan.
    public const HARI_RESTOCK = 7;

    /**
     * Rata-rata penjualan per hari per product_size_id dari transaksi kasir + pesanan online.
     * Pesanan batal / belum dibayar tidak dihitung.
     *
     * @return Collection<int, float> keyed by product_size_id
     */
    public static function harian(): Collection
    {
        $mulai = now()->subDays(self::HARI - 1)->startOfDay();

        $kasir = DB::table('pos_transaction_items as i')
            ->join('pos_transactions as t', 't.id', '=', 'i.pos_transaction_id')
            ->where('t.status', 'paid')
            ->where('t.created_at', '>=', $mulai)
            ->groupBy('i.product_size_id')
            ->select('i.product_size_id', DB::raw('SUM(i.quantity) as qty'))
            ->get();

        $online = DB::table('order_items as i')
            ->join('orders as o', 'o.id', '=', 'i.order_id')
            ->where('o.status', '!=', 'cancelled')
            ->whereNotNull('o.paid_at')
            ->where('o.created_at', '>=', $mulai)
            ->groupBy('i.product_size_id')
            ->select('i.product_size_id', DB::raw('SUM(i.quantity) as qty'))
            ->get();

        return $kasir->concat($online)
            ->groupBy('product_size_id')
            ->map(fn (Collection $rows) => round($rows->sum('qty') / self::HARI, 2));
    }

    /**
     * Saran tambah stok: cukup untuk HARI_RESTOCK ke depan, minimal setara minimum stok.
     */
    public static function saran(int $stok, int $minStok, float $rataHarian): int
    {
        $target = max($minStok, (int) ceil($rataHarian * self::HARI_RESTOCK));

        return max(0, $target - $stok);
    }

    /**
     * Perkiraan stok cukup untuk berapa hari pada laju penjualan sekarang.
     */
    public static function cukupHari(int $stok, float $rataHarian): ?int
    {
        return $rataHarian > 0 ? (int) floor($stok / $rataHarian) : null;
    }
}
