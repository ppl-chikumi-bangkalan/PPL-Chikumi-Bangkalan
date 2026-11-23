@php
    $apiBase = auth()->check() ? '/owner/api' : '/frontend/owner/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Keuangan - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        @include('components.owner.sidebar')

        <main class="group min-w-0 flex-1">

            @include('components.owner.mobile-header')

            <div class="mx-auto max-w-[1500px] space-y-5 p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[28px] font-bold">Laporan Keuangan</h2>
                        <p class="mt-1 text-sm text-[#6B645D]">Rekap pendapatan &amp; laba Chikumi</p>
                    </div>
                    <div class="flex gap-2.5">
                        <a href="#" id="btnExportPdf"
                            class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">Export PDF</a>
                        <a href="#" id="btnExportExcel"
                            class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Export Excel</a>
                    </div>
                </div>

                <!-- FILTER -->
                <section class="flex flex-wrap items-end gap-4 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px]">
                    <div class="space-y-2">
                        <p class="text-sm font-medium">Rekap per</p>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                                <input type="radio" name="rekap_per" id="rekap-harian" value="harian" class="sr-only">Harian
                            </label>
                            <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                                <input type="radio" name="rekap_per" id="rekap-mingguan" value="mingguan" class="sr-only" checked>Mingguan
                            </label>
                            <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                                <input type="radio" name="rekap_per" id="rekap-bulanan" value="bulanan" class="sr-only">Bulanan
                            </label>
                        </div>
                    </div>
                    <label class="min-w-[220px] flex-1 space-y-2">
                        <span class="block text-sm font-medium">Periode</span>
                        <span class="flex h-[54px] items-center gap-3 rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 focus-within:border-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <select name="periode" class="w-full cursor-pointer bg-transparent text-[15px] outline-none">
                                <option value="2026-10">Oktober 2026</option>
                                <option value="2026-09">September 2026</option>
                                <option value="2026-08">Agustus 2026</option>
                            </select>
                        </span>
                    </label>
                </section>

                @include('components.owner.laba-rugi')

                <!-- REKAP PENDAPATAN (data contoh) -->
                <section id="rekapPendapatan" class="overflow-hidden rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white">
                <!-- REKAP HARIAN -->
                <div id="rekapHarian" class="hidden group-has-[#rekap-harian:checked]:block">
                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Rekap Pendapatan · Harian</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">Tabel mengikuti pilihan Rekap per</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-[13px]">
                            <thead class="border-t border-[#EDE6DF] bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">PERIODE</th>
                                    <th class="px-5 py-2.5">TRANSAKSI</th>
                                    <th class="px-5 py-2.5">OMZET</th>
                                    <th class="px-5 py-2.5">PENGELUARAN</th>
                                    <th class="px-5 py-2.5">LABA BERSIH</th>
                                </tr>
                            </thead>
                            <tbody id="rekapHarianBody" class="divide-y divide-[#EDE6DF] border-t border-[#EDE6DF]">
                            <tr>
                                <td class="px-5 py-2.5">25 Okt</td>
                                <td class="px-5 py-2.5">16</td>
                                <td class="px-5 py-2.5">Rp 340.000</td>
                                <td class="px-5 py-2.5">Rp 750.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">− Rp 410.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">26 Okt</td>
                                <td class="px-5 py-2.5">18</td>
                                <td class="px-5 py-2.5">Rp 380.000</td>
                                <td class="px-5 py-2.5">Rp 0</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 380.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">27 Okt</td>
                                <td class="px-5 py-2.5">14</td>
                                <td class="px-5 py-2.5">Rp 300.000</td>
                                <td class="px-5 py-2.5">Rp 0</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 300.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">28 Okt</td>
                                <td class="px-5 py-2.5">15</td>
                                <td class="px-5 py-2.5">Rp 320.000</td>
                                <td class="px-5 py-2.5">Rp 85.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 235.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">29 Okt</td>
                                <td class="px-5 py-2.5">17</td>
                                <td class="px-5 py-2.5">Rp 360.000</td>
                                <td class="px-5 py-2.5">Rp 25.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 335.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">30 Okt</td>
                                <td class="px-5 py-2.5">12</td>
                                <td class="px-5 py-2.5">Rp 260.000</td>
                                <td class="px-5 py-2.5">Rp 89.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 171.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">31 Okt</td>
                                <td class="px-5 py-2.5">8</td>
                                <td class="px-5 py-2.5">Rp 190.000</td>
                                <td class="px-5 py-2.5">Rp 0</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 190.000</td>
                            </tr>
                                <tr class="bg-[#FFF3E6] font-bold">
                                    <td class="px-5 py-2.5">Total 7 hari</td>
                                    <td class="px-5 py-2.5">100</td>
                                    <td class="px-5 py-2.5">Rp 2.150.000</td>
                                    <td class="px-5 py-2.5">Rp 949.000</td>
                                    <td class="px-5 py-2.5 text-[#D96C00]">Rp 1.201.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- REKAP MINGGUAN -->
                <div id="rekapMingguan" class="group-has-[#rekap-harian:checked]:hidden group-has-[#rekap-bulanan:checked]:hidden">
                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Rekap Pendapatan · Mingguan</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">Tabel mengikuti pilihan Rekap per</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-[13px]">
                            <thead class="border-t border-[#EDE6DF] bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">PERIODE</th>
                                    <th class="px-5 py-2.5">TRANSAKSI</th>
                                    <th class="px-5 py-2.5">OMZET</th>
                                    <th class="px-5 py-2.5">PENGELUARAN</th>
                                    <th class="px-5 py-2.5">LABA BERSIH</th>
                                </tr>
                            </thead>
                            <tbody id="rekapMingguanBody" class="divide-y divide-[#EDE6DF] border-t border-[#EDE6DF]">
                            <tr>
                                <td class="px-5 py-2.5">1–7 Okt</td>
                                <td class="px-5 py-2.5">88</td>
                                <td class="px-5 py-2.5">Rp 1.850.000</td>
                                <td class="px-5 py-2.5">Rp 1.300.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 550.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">8–14 Okt</td>
                                <td class="px-5 py-2.5">96</td>
                                <td class="px-5 py-2.5">Rp 2.050.000</td>
                                <td class="px-5 py-2.5">Rp 1.150.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 900.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">15–21 Okt</td>
                                <td class="px-5 py-2.5">100</td>
                                <td class="px-5 py-2.5">Rp 2.100.000</td>
                                <td class="px-5 py-2.5">Rp 1.250.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 850.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">22–28 Okt</td>
                                <td class="px-5 py-2.5">108</td>
                                <td class="px-5 py-2.5">Rp 2.300.000</td>
                                <td class="px-5 py-2.5">Rp 1.200.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 1.100.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">29–31 Okt</td>
                                <td class="px-5 py-2.5">20</td>
                                <td class="px-5 py-2.5">Rp 450.000</td>
                                <td class="px-5 py-2.5">Rp 300.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 150.000</td>
                            </tr>
                                <tr class="bg-[#FFF3E6] font-bold">
                                    <td class="px-5 py-2.5">Total</td>
                                    <td class="px-5 py-2.5">412</td>
                                    <td class="px-5 py-2.5">Rp 8.750.000</td>
                                    <td class="px-5 py-2.5">Rp 5.200.000</td>
                                    <td class="px-5 py-2.5 text-[#D96C00]">Rp 3.550.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- REKAP BULANAN -->
                <div id="rekapBulanan" class="hidden group-has-[#rekap-bulanan:checked]:block">
                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Rekap Pendapatan · Bulanan</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">Tabel mengikuti pilihan Rekap per</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-[13px]">
                            <thead class="border-t border-[#EDE6DF] bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">PERIODE</th>
                                    <th class="px-5 py-2.5">TRANSAKSI</th>
                                    <th class="px-5 py-2.5">OMZET</th>
                                    <th class="px-5 py-2.5">PENGELUARAN</th>
                                    <th class="px-5 py-2.5">LABA BERSIH</th>
                                </tr>
                            </thead>
                            <tbody id="rekapBulananBody" class="divide-y divide-[#EDE6DF] border-t border-[#EDE6DF]">
                            <tr>
                                <td class="px-5 py-2.5">Agustus 2026</td>
                                <td class="px-5 py-2.5">356</td>
                                <td class="px-5 py-2.5">Rp 7.420.000</td>
                                <td class="px-5 py-2.5">Rp 4.810.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 2.610.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">September 2026</td>
                                <td class="px-5 py-2.5">389</td>
                                <td class="px-5 py-2.5">Rp 8.130.000</td>
                                <td class="px-5 py-2.5">Rp 5.020.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 3.110.000</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-2.5">Oktober 2026</td>
                                <td class="px-5 py-2.5">412</td>
                                <td class="px-5 py-2.5">Rp 8.750.000</td>
                                <td class="px-5 py-2.5">Rp 5.200.000</td>
                                <td class="px-5 py-2.5 font-semibold text-[#D96C00]">Rp 3.550.000</td>
                            </tr>
                                <tr class="bg-[#FFF3E6] font-bold">
                                    <td class="px-5 py-2.5">Total 3 bulan</td>
                                    <td class="px-5 py-2.5">1.157</td>
                                    <td class="px-5 py-2.5">Rp 24.300.000</td>
                                    <td class="px-5 py-2.5">Rp 15.030.000</td>
                                    <td class="px-5 py-2.5 text-[#D96C00]">Rp 9.270.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        const API_BASE = @json($apiBase);
        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Math.abs(nilai)));
        const uang = nilai => (nilai < 0 ? '− ' : '') + rupiah(nilai);

        const bodyRekap = {
            harian: document.getElementById('rekapHarianBody'),
            mingguan: document.getElementById('rekapMingguanBody'),
            bulanan: document.getElementById('rekapBulananBody'),
        };
        const radioRekap = () => document.querySelector('input[name="rekap_per"]:checked')?.value || 'mingguan';

        async function muatRekap(rekap) {
            try {
                const periode = document.querySelector('select[name="periode"]').value;
                const response = await fetch(
                    `${API_BASE}/laporan/pendapatan?rekap=${rekap}&periode=${periode}`,
                    { headers: { 'Accept': 'application/json' } }
                );
                if (!response.ok) return;

                const { data } = await response.json();

                const baris = data.baris.map(item => `
                    <tr>
                        <td class="px-5 py-2.5">${item.label}</td>
                        <td class="px-5 py-2.5">${item.transaksi}</td>
                        <td class="px-5 py-2.5">${rupiah(item.omzet)}</td>
                        <td class="px-5 py-2.5">${rupiah(item.pengeluaran)}</td>
                        <td class="px-5 py-2.5 font-semibold text-[#D96C00]">${uang(item.laba)}</td>
                    </tr>`).join('');

                const total = `
                    <tr class="bg-[#FFF3E6] font-bold">
                        <td class="px-5 py-2.5">Total</td>
                        <td class="px-5 py-2.5">${data.total.transaksi}</td>
                        <td class="px-5 py-2.5">${rupiah(data.total.omzet)}</td>
                        <td class="px-5 py-2.5">${rupiah(data.total.pengeluaran)}</td>
                        <td class="px-5 py-2.5 text-[#D96C00]">${uang(data.total.laba)}</td>
                    </tr>`;

                bodyRekap[rekap].innerHTML = baris + total;
            } catch (error) {
                console.error(error);
            }
        }

        document.querySelectorAll('input[name="rekap_per"]').forEach(radio => {
            radio.addEventListener('change', () => muatRekap(radio.value));
        });
        document.querySelector('select[name="periode"]').addEventListener('change', () => muatRekap(radioRekap()));

        muatRekap(radioRekap());
    </script>
</body>
</html>