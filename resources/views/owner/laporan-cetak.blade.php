<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan {{ $laba['label_periode'] }} - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .tanpa-cetak { display: none !important; }
            @page { margin: 14mm; }
        }
    </style>
</head>

<body class="bg-white p-6 font-['Poppins'] text-[#1E1B18]">

    @php
        $rupiah = fn ($nilai) => 'Rp '.number_format((float) $nilai, 0, ',', '.');
        $uang = fn ($nilai) => ((float) $nilai < 0 ? '− ' : '').$rupiah($nilai);
        $judulRekap = ['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'][$rekap] ?? 'Mingguan';
    @endphp

    <div class="tanpa-cetak mb-5 flex items-center justify-between gap-3 rounded-xl border border-[#EDE6DF] bg-[#FFF9F2] px-5 py-4">
        <p class="text-sm text-[#6B645D]">
            Pilih "Simpan sebagai PDF" pada dialog cetak untuk mengunduh laporan ini.
        </p>
        <div class="flex gap-2">
            <a href="{{ url()->previous() }}"
                class="rounded-lg border border-[#EDE6DF] bg-white px-4 py-2 text-sm font-semibold">Kembali</a>
            <button type="button" onclick="window.print()"
                class="rounded-lg bg-[#FF8000] px-5 py-2 text-sm font-semibold text-white">Cetak / Simpan PDF</button>
        </div>
    </div>

    <header class="mb-6 border-b-2 border-[#FF8000] pb-4">
        <h1 class="text-2xl font-bold">Laporan Keuangan Chikumi</h1>
        <p class="mt-1 text-sm text-[#6B645D]">Periode {{ $laba['label_periode'] }} · Rekap {{ $judulRekap }}</p>
        <p class="mt-0.5 text-xs text-[#A39B93]">Dicetak {{ now()->format('d/m/Y H:i') }}</p>
    </header>

    <!-- LABA RUGI -->
    <section class="mb-7">
        <h2 class="mb-3 text-base font-semibold">Laba Rugi</h2>
        <table class="w-full text-left text-sm">
            <tbody class="divide-y divide-[#EDE6DF]">
                <tr>
                    <td class="py-2 font-semibold">Omzet ({{ $laba['transaksi'] }} transaksi)</td>
                    <td class="py-2 text-right font-semibold">{{ $rupiah($laba['omzet']) }}</td>
                </tr>
                <tr>
                    <td class="py-2 pl-5 text-[#6B645D]">Bahan Baku</td>
                    <td class="py-2 text-right">− {{ $rupiah($laba['hpp']['bahan_baku']) }}</td>
                </tr>
                <tr>
                    <td class="py-2 pl-5 text-[#6B645D]">Kemasan</td>
                    <td class="py-2 text-right">− {{ $rupiah($laba['hpp']['kemasan']) }}</td>
                </tr>
                <tr class="font-bold text-[#D96C00]">
                    <td class="py-2">LABA KOTOR</td>
                    <td class="py-2 text-right">{{ $rupiah($laba['laba_kotor']) }}</td>
                </tr>
                <tr>
                    <td class="py-2 pl-5 text-[#6B645D]">Operasional</td>
                    <td class="py-2 text-right">− {{ $rupiah($laba['operasional']) }}</td>
                </tr>
                <tr>
                    <td class="py-2 pl-5 text-[#6B645D]">Gaji karyawan</td>
                    <td class="py-2 text-right">− {{ $rupiah($laba['karyawan']['gaji']) }}</td>
                </tr>
                <tr>
                    <td class="py-2 pl-5 text-[#6B645D]">Bonus &amp; lembur</td>
                    <td class="py-2 text-right">− {{ $rupiah($laba['karyawan']['bonus_lembur']) }}</td>
                </tr>
                @if ($laba['lainnya'] > 0)
                    <tr>
                        <td class="py-2 pl-5 text-[#6B645D]">Lainnya</td>
                        <td class="py-2 text-right">− {{ $rupiah($laba['lainnya']) }}</td>
                    </tr>
                @endif
                <tr class="font-bold">
                    <td class="py-2.5">LABA BERSIH</td>
                    <td class="py-2.5 text-right">{{ $uang($laba['laba_bersih']) }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- REKAP PENDAPATAN -->
    <section>
        <h2 class="mb-3 text-base font-semibold">Rekap Pendapatan · {{ $judulRekap }}</h2>
        <table class="w-full text-left text-sm">
            <thead class="border-y border-[#EDE6DF] bg-[#F7F3EF] text-xs uppercase text-[#6B645D]">
                <tr>
                    <th class="px-2 py-2">Periode</th>
                    <th class="px-2 py-2 text-right">Transaksi</th>
                    <th class="px-2 py-2 text-right">Omzet</th>
                    <th class="px-2 py-2 text-right">Pengeluaran</th>
                    <th class="px-2 py-2 text-right">Laba Bersih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#EDE6DF]">
                @foreach ($pendapatan['baris'] as $baris)
                    <tr>
                        <td class="px-2 py-1.5">{{ $baris['label'] }}</td>
                        <td class="px-2 py-1.5 text-right">{{ $baris['transaksi'] }}</td>
                        <td class="px-2 py-1.5 text-right">{{ $rupiah($baris['omzet']) }}</td>
                        <td class="px-2 py-1.5 text-right">{{ $rupiah($baris['pengeluaran']) }}</td>
                        <td class="px-2 py-1.5 text-right">{{ $uang($baris['laba']) }}</td>
                    </tr>
                @endforeach
                <tr class="bg-[#FFF3E6] font-bold">
                    <td class="px-2 py-2">Total</td>
                    <td class="px-2 py-2 text-right">{{ $pendapatan['total']['transaksi'] }}</td>
                    <td class="px-2 py-2 text-right">{{ $rupiah($pendapatan['total']['omzet']) }}</td>
                    <td class="px-2 py-2 text-right">{{ $rupiah($pendapatan['total']['pengeluaran']) }}</td>
                    <td class="px-2 py-2 text-right">{{ $uang($pendapatan['total']['laba']) }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>

</html>
