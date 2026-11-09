<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function page()
    {
        return view('owner.dashboard');
    }

    public function summary(Request $request)
    {
        $periode = $request->query('periode', 'bulan');
        [$mulai, $selesai] = match ($periode) {
            'hari' => [now()->startOfDay(), now()->endOfDay()],
            'minggu' => [now()->startOfWeek(), now()->endOfWeek()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $pos = DB::table('pos_transactions')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$mulai, $selesai]);
        $pesanan = DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('paid_at')
            ->whereBetween('created_at', [$mulai, $selesai]);

        $omzetKasir = (float) (clone $pos)->sum('total_amount');
        $omzetOnline = (float) (clone $pesanan)->sum('grand_total');
        $omzet = $omzetKasir + $omzetOnline;
        $pengeluaran = (float) DB::table('expenses')
            ->whereBetween('expense_date', [$mulai->toDateString(), $selesai->toDateString()])
            ->sum('amount');
        $laba = $omzet - $pengeluaran;

        return response()->json([
            'data' => [
                'periode' => $periode,
                'omzet' => $omzet,
                'omzet_kasir' => $omzetKasir,
                'omzet_online' => $omzetOnline,
                'pengeluaran' => $pengeluaran,
                'laba' => $laba,
                'margin' => $omzet > 0 ? round($laba / $omzet * 100, 1) : 0,
                'transaksi' => [
                    'total' => (clone $pos)->count() + (clone $pesanan)->count(),
                    'kasir' => (clone $pos)->count(),
                    'online' => (clone $pesanan)->count(),
                ],
                'grafik' => $this->grafikBulanan(),
            ],
        ]);
    }

    // Grafik selalu per minggu untuk bulan berjalan (mengikuti desain dashboard).
    private function grafikBulanan(): array
    {
        $awal = now()->startOfMonth();
        $akhir = now()->endOfMonth();
        $minggu = [[1, 7], [8, 14], [15, 21], [22, 28], [29, (int) $akhir->day]];
        $grafik = ['label' => [], 'omzet' => [], 'pengeluaran' => []];

        foreach ($minggu as [$dari, $sampai]) {
            $mulai = $awal->copy()->day($dari)->startOfDay();
            $selesai = $awal->copy()->day($sampai)->endOfDay();
            $grafik['label'][] = "{$dari}-{$sampai} ".$awal->format('M');
            $grafik['omzet'][] = (float) DB::table('pos_transactions')
                ->where('status', 'paid')
                ->whereBetween('created_at', [$mulai, $selesai])
                ->sum('total_amount')
                + (float) DB::table('orders')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('paid_at')
                    ->whereBetween('created_at', [$mulai, $selesai])
                    ->sum('grand_total');
            $grafik['pengeluaran'][] = (float) DB::table('expenses')
                ->whereBetween('expense_date', [$mulai->toDateString(), $selesai->toDateString()])
                ->sum('amount');
        }

        return $grafik;
    }
}
