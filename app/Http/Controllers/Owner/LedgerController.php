<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\PosTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Buku transaksi owner: uang masuk (POS lunas + pesanan online berbayar)
 * digabung dengan uang keluar (pengeluaran manual) dalam satu daftar.
 */
class LedgerController extends Controller
{
    private const KATEGORI_MASUK = [
        'penjualan_kasir' => 'Penjualan Kasir',
        'penjualan_online' => 'Penjualan Online',
    ];

    public function index(Request $request)
    {
        $filter = $request->validate([
            'jenis' => ['nullable', 'in:semua,masuk,keluar'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'periode' => ['nullable', 'date_format:Y-m'],
            'cari' => ['nullable', 'string', 'max:100'],
            'halaman' => ['nullable', 'integer', 'min:1'],
            'per_halaman' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $acuan = isset($filter['periode'])
            ? Carbon::createFromFormat('Y-m-d', $filter['periode'].'-01')->startOfMonth()
            : now()->startOfMonth();
        $mulai = $acuan->copy()->startOfMonth();
        $selesai = $acuan->copy()->endOfMonth();

        $cari = trim($filter['cari'] ?? '');
        $jenis = $filter['jenis'] ?? 'semua';
        $kategori = $filter['kategori'] ?? null;
        $perHalaman = $filter['per_halaman'] ?? 10;
        $halaman = $filter['halaman'] ?? 1;

        // ponytail: gabung di memori; cukup untuk ratusan baris/bulan.
        // Ganti ke UNION SQL bila data sudah puluhan ribu.
        $baris = collect();

        if ($jenis !== 'keluar') {
            $baris = $baris->concat(
                PosTransaction::query()
                    ->withSum('items as jumlah_item', 'quantity')
                    ->where('status', 'paid')
                    ->whereBetween('created_at', [$mulai, $selesai])
                    ->when($cari !== '', fn ($q) => $q->where('transaction_number', 'like', "%{$cari}%"))
                    ->get()
                    ->map(fn (PosTransaction $t) => [
                        'id' => 'pos-'.$t->id,
                        'jenis' => 'masuk',
                        'kategori' => 'penjualan_kasir',
                        'kategori_label' => self::KATEGORI_MASUK['penjualan_kasir'],
                        'keterangan' => '#'.$t->transaction_number.' · '.((int) $t->jumlah_item).' item',
                        'metode' => $t->payment_method,
                        'metode_label' => $t->payment_method === 'cash' ? 'Tunai' : 'Non-tunai',
                        'nominal' => (float) $t->total_amount,
                        'waktu' => $t->created_at->format('Y-m-d H:i'),
                        'waktu_iso' => $t->created_at->toIso8601String(),
                    ])
            );

            $baris = $baris->concat(
                Order::query()
                    ->withSum('items as jumlah_item', 'quantity')
                    ->where('status', '!=', 'cancelled')
                    ->whereNotNull('paid_at')
                    ->whereBetween('created_at', [$mulai, $selesai])
                    ->when($cari !== '', fn ($q) => $q->where('order_number', 'like', "%{$cari}%"))
                    ->get()
                    ->map(fn (Order $o) => [
                        'id' => 'order-'.$o->id,
                        'jenis' => 'masuk',
                        'kategori' => 'penjualan_online',
                        'kategori_label' => self::KATEGORI_MASUK['penjualan_online'],
                        'keterangan' => '#'.$o->order_number.' · '.((int) $o->jumlah_item).' item',
                        'metode' => $o->payment_type,
                        'metode_label' => match ($o->payment_type) {
                            'qris' => 'QRIS',
                            'va' => 'VA',
                            'cash' => 'Tunai',
                            default => '-',
                        },
                        'nominal' => (float) $o->grand_total,
                        'waktu' => $o->created_at->format('Y-m-d H:i'),
                        'waktu_iso' => $o->created_at->toIso8601String(),
                    ])
            );
        }

        if ($jenis !== 'masuk') {
            $baris = $baris->concat(
                Expense::query()
                    ->whereBetween('expense_date', [$mulai->toDateString(), $selesai->toDateString()])
                    ->when($cari !== '', fn ($q) => $q->where('title', 'like', "%{$cari}%"))
                    ->get()
                    ->map(fn (Expense $e) => [
                        'id' => 'expense-'.$e->id,
                        'jenis' => 'keluar',
                        'kategori' => $e->category,
                        'kategori_label' => Expense::CATEGORIES[$e->category] ?? $e->category,
                        'keterangan' => $e->title,
                        'metode' => $e->payment_method,
                        'metode_label' => Expense::PAYMENT_METHODS[$e->payment_method] ?? $e->payment_method,
                        'nominal' => (float) $e->amount,
                        'waktu' => $e->expense_date?->format('Y-m-d'),
                        'waktu_iso' => $e->expense_date?->toIso8601String(),
                    ])
            );
        }

        if ($kategori) {
            $baris = $baris->where('kategori', $kategori);
        }

        $baris = $baris->sortByDesc('waktu_iso')->values();

        $masuk = $baris->where('jenis', 'masuk');
        $keluar = $baris->where('jenis', 'keluar');

        $ringkasan = [
            'masuk' => round((float) $masuk->sum('nominal'), 2),
            'keluar' => round((float) $keluar->sum('nominal'), 2),
            'selisih' => round((float) $masuk->sum('nominal') - (float) $keluar->sum('nominal'), 2),
            'jumlah_masuk' => $masuk->count(),
            'jumlah_keluar' => $keluar->count(),
        ];

        $total = $baris->count();
        $totalHalaman = max(1, (int) ceil($total / $perHalaman));
        $halaman = min($halaman, $totalHalaman);

        return response()->json([
            'data' => [
                'periode' => $acuan->format('Y-m'),
                'transaksi' => $baris->forPage($halaman, $perHalaman)->values(),
                'ringkasan' => $ringkasan,
                'meta' => [
                    'halaman' => $halaman,
                    'per_halaman' => $perHalaman,
                    'total' => $total,
                    'total_halaman' => $totalHalaman,
                ],
                'kategori' => collect(self::KATEGORI_MASUK)
                    ->merge(Expense::CATEGORIES)
                    ->map(fn (string $label, string $kode) => ['kode' => $kode, 'label' => $label])
                    ->values(),
            ],
        ]);
    }
}
