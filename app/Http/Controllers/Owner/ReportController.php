<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private const BULAN = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    private const BULAN_PANJANG = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    // HPP = bahan baku + kemasan; karyawan = gaji + bonus + lembur.
    private const HPP = ['raw_material', 'packaging'];
    private const KARYAWAN = ['salary', 'bonus', 'overtime'];

    public function pendapatan(Request $request)
    {
        $data = $request->validate([
            'rekap' => ['nullable', 'in:harian,mingguan,bulanan'],
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);

        return response()->json([
            'data' => $this->hitungPendapatan(
                $data['rekap'] ?? 'mingguan',
                $this->acuan($data['periode'] ?? null)
            ),
        ]);
    }

    public function labaRugi(Request $request)
    {
        $data = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);

        return response()->json([
            'data' => $this->hitungLabaRugi($this->acuan($data['periode'] ?? null)),
        ]);
    }

    // Export CSV rekap pendapatan (dibuka sebagai Excel).
    public function exportPendapatan(Request $request)
    {
        $data = $request->validate([
            'rekap' => ['nullable', 'in:harian,mingguan,bulanan'],
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);

        $rekap = $data['rekap'] ?? 'mingguan';
        $hasil = $this->hitungPendapatan($rekap, $this->acuan($data['periode'] ?? null));

        $baris = [['Periode', 'Transaksi', 'Omzet', 'Pengeluaran', 'Laba Bersih']];
        foreach ($hasil['baris'] as $item) {
            $baris[] = [$item['label'], $item['transaksi'], $item['omzet'], $item['pengeluaran'], $item['laba']];
        }
        $baris[] = [
            'Total',
            $hasil['total']['transaksi'],
            $hasil['total']['omzet'],
            $hasil['total']['pengeluaran'],
            $hasil['total']['laba'],
        ];

        return $this->unduhCsv("rekap-pendapatan-{$rekap}-{$hasil['periode']}.csv", $baris);
    }

    // Halaman cetak (Ctrl+P / Simpan sebagai PDF) berisi rekap + laba rugi.
    public function cetak(Request $request)
    {
        $data = $request->validate([
            'rekap' => ['nullable', 'in:harian,mingguan,bulanan'],
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);

        $acuan = $this->acuan($data['periode'] ?? null);
        $rekap = $data['rekap'] ?? 'mingguan';

        return view('owner.laporan-cetak', [
            'rekap' => $rekap,
            'pendapatan' => $this->hitungPendapatan($rekap, $acuan),
            'laba' => $this->hitungLabaRugi($acuan),
        ]);
    }

    private function acuan(?string $periode): Carbon
    {
        return $periode
            ? Carbon::createFromFormat('Y-m-d', $periode.'-01')->startOfMonth()
            : now()->startOfMonth();
    }

    private function hitungPendapatan(string $rekap, Carbon $acuan): array
    {
        [$mulai, $selesai] = $rekap === 'bulanan'
            ? [$acuan->copy()->subMonths(2), $acuan->copy()->endOfMonth()]
            : [$acuan->copy(), $acuan->copy()->endOfMonth()];

        $omzet = $this->omzetHarian($mulai, $selesai);
        $pengeluaran = $this->pengeluaranHarian($mulai, $selesai);

        $baris = match ($rekap) {
            'harian' => $this->perHari($acuan, $omzet, $pengeluaran),
            'bulanan' => $this->perBulan($acuan, $omzet, $pengeluaran),
            default => $this->perMinggu($acuan, $omzet, $pengeluaran),
        };

        return [
            'rekap' => $rekap,
            'periode' => $acuan->format('Y-m'),
            'baris' => $baris,
            'total' => $this->jumlahkan($baris),
        ];
    }

    private function hitungLabaRugi(Carbon $acuan): array
    {
        $mulai = $acuan->copy()->startOfMonth();
        $selesai = $acuan->copy()->endOfMonth();

        $omzet = $this->omzetHarian($mulai, $selesai);
        $pengeluaran = $this->pengeluaranKategori($mulai, $selesai);

        $bahanBaku = $pengeluaran['raw_material'] ?? 0.0;
        $kemasan = $pengeluaran['packaging'] ?? 0.0;
        $operasional = $pengeluaran['operational'] ?? 0.0;
        $gaji = $pengeluaran['salary'] ?? 0.0;
        $bonusLembur = ($pengeluaran['bonus'] ?? 0.0) + ($pengeluaran['overtime'] ?? 0.0);
        $lainnya = $pengeluaran['other'] ?? 0.0;

        $totalOmzet = round(array_sum(array_column($omzet, 'omzet')), 2);
        $totalTransaksi = array_sum(array_column($omzet, 'transaksi'));
        $hpp = round($bahanBaku + $kemasan, 2);
        $labaKotor = round($totalOmzet - $hpp, 2);
        $karyawan = round($gaji + $bonusLembur, 2);

        return [
            'periode' => $acuan->format('Y-m'),
            'label_periode' => self::BULAN_PANJANG[$acuan->month].' '.$acuan->year,
            'omzet' => $totalOmzet,
            'transaksi' => $totalTransaksi,
            'hpp' => [
                'bahan_baku' => round($bahanBaku, 2),
                'kemasan' => round($kemasan, 2),
                'total' => $hpp,
            ],
            'laba_kotor' => $labaKotor,
            'operasional' => round($operasional, 2),
            'karyawan' => [
                'gaji' => round($gaji, 2),
                'bonus_lembur' => round($bonusLembur, 2),
                'total' => $karyawan,
            ],
            'lainnya' => round($lainnya, 2),
            'total_pengeluaran' => round(array_sum($pengeluaran), 2),
            'laba_bersih' => round($labaKotor - $operasional - $karyawan - $lainnya, 2),
        ];
    }

    private function unduhCsv(string $nama, array $baris)
    {
        return response()->streamDownload(function () use ($baris) {
            $keluaran = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($keluaran, "\xEF\xBB\xBF");
            foreach ($baris as $kolom) {
                fputcsv($keluaran, $kolom);
            }
            fclose($keluaran);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Total pengeluaran per kategori pada rentang tanggal.
    private function pengeluaranKategori(Carbon $mulai, Carbon $selesai): array
    {
        return DB::table('expenses')
            ->whereBetween('expense_date', [$mulai->toDateString(), $selesai->toDateString()])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($total) => (float) $total)
            ->all();
    }

    // Omzet per tanggal: POS lunas + pesanan online yang sudah dibayar & tidak batal.
    private function omzetHarian(Carbon $mulai, Carbon $selesai): array
    {
        $hasil = [];

        $sumber = [
            ['pos_transactions', 'total_amount', fn ($query) => $query->where('status', 'paid')],
            ['orders', 'grand_total', fn ($query) => $query
                ->where('status', '!=', 'cancelled')
                ->whereNotNull('paid_at')],
        ];

        foreach ($sumber as [$tabel, $kolom, $filter]) {
            $rows = $filter(DB::table($tabel))
                ->whereBetween('created_at', [$mulai, $selesai])
                ->selectRaw("DATE(created_at) as tanggal, COUNT(*) as jumlah, SUM({$kolom}) as total")
                ->groupBy('tanggal')
                ->get();

            foreach ($rows as $row) {
                $hasil[$row->tanggal] ??= ['omzet' => 0.0, 'transaksi' => 0];
                $hasil[$row->tanggal]['omzet'] += (float) $row->total;
                $hasil[$row->tanggal]['transaksi'] += (int) $row->jumlah;
            }
        }

        return $hasil;
    }

    private function pengeluaranHarian(Carbon $mulai, Carbon $selesai): array
    {
        return DB::table('expenses')
            ->whereBetween('expense_date', [$mulai->toDateString(), $selesai->toDateString()])
            ->selectRaw('expense_date as tanggal, SUM(amount) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal')
            ->map(fn ($total) => (float) $total)
            ->all();
    }

    private function perHari(Carbon $acuan, array $omzet, array $pengeluaran): array
    {
        $baris = [];
        $akhir = $acuan->copy()->endOfMonth();

        for ($hari = $acuan->copy(); $hari->lte($akhir); $hari->addDay()) {
            $baris[] = [
                'label' => $hari->day.' '.self::BULAN[$hari->month],
                ...$this->hitung($hari->copy(), $hari->copy(), $omzet, $pengeluaran),
            ];
        }

        return $baris;
    }

    private function perMinggu(Carbon $acuan, array $omzet, array $pengeluaran): array
    {
        $akhir = (int) $acuan->copy()->endOfMonth()->day;
        $baris = [];

        foreach ([[1, 7], [8, 14], [15, 21], [22, 28], [29, $akhir]] as [$dari, $sampai]) {
            if ($dari > $akhir) {
                continue;
            }

            $sampai = min($sampai, $akhir);
            $label = $dari === $sampai
                ? $dari.' '.self::BULAN[$acuan->month]
                : $dari.'–'.$sampai.' '.self::BULAN[$acuan->month];

            $baris[] = [
                'label' => $label,
                ...$this->hitung(
                    $acuan->copy()->day($dari),
                    $acuan->copy()->day($sampai),
                    $omzet,
                    $pengeluaran
                ),
            ];
        }

        return $baris;
    }

    private function perBulan(Carbon $acuan, array $omzet, array $pengeluaran): array
    {
        $baris = [];

        for ($bulan = $acuan->copy()->subMonths(2); $bulan->lte($acuan); $bulan->addMonth()) {
            $baris[] = [
                'label' => self::BULAN[$bulan->month].' '.$bulan->year,
                ...$this->hitung(
                    $bulan->copy()->startOfMonth(),
                    $bulan->copy()->endOfMonth(),
                    $omzet,
                    $pengeluaran
                ),
            ];
        }

        return $baris;
    }

    private function hitung(Carbon $dari, Carbon $sampai, array $omzet, array $pengeluaran): array
    {
        $total = ['transaksi' => 0, 'omzet' => 0.0, 'pengeluaran' => 0.0];

        for ($hari = $dari->copy(); $hari->lte($sampai); $hari->addDay()) {
            $kunci = $hari->toDateString();
            $total['transaksi'] += $omzet[$kunci]['transaksi'] ?? 0;
            $total['omzet'] += $omzet[$kunci]['omzet'] ?? 0;
            $total['pengeluaran'] += $pengeluaran[$kunci] ?? 0;
        }

        return [
            'transaksi' => $total['transaksi'],
            'omzet' => round($total['omzet'], 2),
            'pengeluaran' => round($total['pengeluaran'], 2),
            'laba' => round($total['omzet'] - $total['pengeluaran'], 2),
        ];
    }

    private function jumlahkan(array $baris): array
    {
        $total = ['transaksi' => 0, 'omzet' => 0.0, 'pengeluaran' => 0.0, 'laba' => 0.0];

        foreach ($baris as $barisItem) {
            $total['transaksi'] += $barisItem['transaksi'];
            $total['omzet'] += $barisItem['omzet'];
            $total['pengeluaran'] += $barisItem['pengeluaran'];
            $total['laba'] += $barisItem['laba'];
        }

        return [
            'transaksi' => $total['transaksi'],
            'omzet' => round($total['omzet'], 2),
            'pengeluaran' => round($total['pengeluaran'], 2),
            'laba' => round($total['laba'], 2),
        ];
    }
}
