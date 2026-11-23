@php
    $apiBase = auth()->check() ? '/owner/api' : '/frontend/owner/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi - Chikumi</title>
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
                        <h2 class="text-[28px] font-bold">Transaksi</h2>
                        <p class="mt-1 text-sm text-[#6B645D]">Uang masuk dari penjualan dan pengeluaran usaha</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="#" id="btnExport"
                            class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">Export</a>
                        <a href="#catat-pengeluaran"
                            class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">+ Catat Pengeluaran</a>
                    </div>
                </div>

                <!-- FILTER -->
                <div class="flex flex-wrap items-center gap-2">
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-semua" class="sr-only" checked>Semua
                    </label>
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-masuk" class="sr-only">Uang Masuk
                    </label>
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-keluar" class="sr-only">Pengeluaran
                    </label>
                    <select name="periode" class="ml-auto rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 py-2.5 text-[13px] font-medium outline-none focus:border-[#FF8000]">
                        <option>Oktober 2026</option>
                        <option>September 2026</option>
                    </select>
                    <select name="kategori" class="rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 py-2.5 text-[13px] font-medium outline-none focus:border-[#FF8000]">
                        <option>Semua kategori</option>
                        <option>Penjualan Kasir</option>
                        <option>Penjualan Online</option>
                        <option>Bahan Baku</option>
                        <option>Kemasan</option>
                        <option>Operasional</option>
                        <option>Gaji</option>
                        <option>Bonus</option>
                        <option>Lembur</option>
                        <option>Lainnya</option>
                    </select>
                </div>

                <!-- RIWAYAT TRANSAKSI (data contoh) -->
                <section class="overflow-hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white">
                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Riwayat Transaksi · Oktober 2026</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">Penjualan tercatat otomatis, pengeluaran dicatat manual</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-left text-[13px]">
                            <thead class="bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">WAKTU</th>
                                    <th class="px-5 py-2.5">JENIS</th>
                                    <th class="px-5 py-2.5">KATEGORI</th>
                                    <th class="px-5 py-2.5">KETERANGAN</th>
                                    <th class="px-5 py-2.5">METODE</th>
                                    <th class="px-5 py-2.5">NOMINAL</th>
                                    <th class="px-5 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody id="tabelTransaksi" class="divide-y divide-[#EDE6DF]">
                                <tr class="group-has-[#jenis-keluar:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">30/10 20.45</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Masuk</span></td>
                                    <td class="px-5 py-3">Penjualan Online</td>
                                    <td class="px-5 py-3">#CK-0124 · 2 item</td>
                                    <td class="px-5 py-3 text-[#6B645D]">QRIS</td>
                                    <td class="px-5 py-3 font-semibold text-[#1F9D55]">+ Rp 54.000</td>
                                    <td class="px-5 py-3"><a href="#" aria-label="Lihat struk" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-keluar:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">30/10 20.30</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Masuk</span></td>
                                    <td class="px-5 py-3">Penjualan Kasir</td>
                                    <td class="px-5 py-3">#CK-0123 · 1 item</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#1F9D55]">+ Rp 20.000</td>
                                    <td class="px-5 py-3"><a href="#" aria-label="Lihat struk" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">30/10 20.10</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Lembur</td>
                                    <td class="px-5 py-3">Lembur Rina 2 jam</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 50.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-keluar:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">30/10 19.55</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Masuk</span></td>
                                    <td class="px-5 py-3">Penjualan Kasir</td>
                                    <td class="px-5 py-3">#CK-0122 · 3 item</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#1F9D55]">+ Rp 74.000</td>
                                    <td class="px-5 py-3"><a href="#" aria-label="Lihat struk" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">30/10 14.30</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Bahan Baku</td>
                                    <td class="px-5 py-3">Belanja mentaiko</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Transfer</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 39.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-keluar:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">29/10 20.50</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Masuk</span></td>
                                    <td class="px-5 py-3">Penjualan Online</td>
                                    <td class="px-5 py-3">#CK-0118 · 2 item</td>
                                    <td class="px-5 py-3 text-[#6B645D]">QRIS</td>
                                    <td class="px-5 py-3 font-semibold text-[#1F9D55]">+ Rp 47.000</td>
                                    <td class="px-5 py-3"><a href="#" aria-label="Lihat struk" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">29/10 15.10</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Operasional</td>
                                    <td class="px-5 py-3">Isi ulang gas 3 kg</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 25.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">28/10 14.00</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Kemasan</td>
                                    <td class="px-5 py-3">Paper cup 200 pcs</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 85.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">25/10 21.10</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Gaji</td>
                                    <td class="px-5 py-3">Gaji Rina (Oktober)</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Transfer</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 700.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                                <tr class="group-has-[#jenis-masuk:checked]:hidden">
                                    <td class="px-5 py-3 text-xs text-[#6B645D]">25/10 21.05</td>
                                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span></td>
                                    <td class="px-5 py-3">Bonus</td>
                                    <td class="px-5 py-3">Bonus target penjualan</td>
                                    <td class="px-5 py-3 text-[#6B645D]">Tunai</td>
                                    <td class="px-5 py-3 font-semibold text-[#E03131]">− Rp 50.000</td>
                                    <td class="px-5 py-3"><a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#EDE6DF] px-5 py-3">
                        <p class="text-xs text-[#6B645D]">Menampilkan 10 dari 486 transaksi</p>
                        <div class="flex gap-1.5">
                            <a href="#" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold border-[#EDE6DF] bg-white hover:border-[#FF8000]">‹</a>
                            <a href="#" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold border-[#FF8000] bg-[#FF8000] text-white">1</a>
                            <a href="#" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold border-[#EDE6DF] bg-white hover:border-[#FF8000]">2</a>
                            <a href="#" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold border-[#EDE6DF] bg-white hover:border-[#FF8000]">3</a>
                            <a href="#" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold border-[#EDE6DF] bg-white hover:border-[#FF8000]">›</a>
                        </div>
                    </div>
                </section>

                <!-- TOTAL PERIODE -->
                <section class="grid gap-4 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-[#6B645D]">Uang masuk (412 transaksi)</p>
                        <p id="totalMasuk" class="mt-1 text-lg font-bold text-[#1F9D55]">+ Rp 8.750.000</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B645D]">Pengeluaran (74 transaksi)</p>
                        <p id="totalKeluar" class="mt-1 text-lg font-bold text-[#E03131]">− Rp 5.200.000</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B645D]">Selisih</p>
                        <p id="totalSelisih" class="mt-1 text-lg font-bold text-[#D96C00]">Rp 3.550.000</p>
                    </div>
                </section>

            </div>
        </main>
    </div>

    @include('components.owner.catat-pengeluaran')

    <script>
        const API_BASE = @json($apiBase);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const formPengeluaran = document.getElementById('formPengeluaran');

        formPengeluaran.addEventListener('submit', async event => {
            event.preventDefault();

            const tombol = document.getElementById('btnSimpanPengeluaran');
            tombol.disabled = true;

            try {
                const response = await fetch(`${API_BASE}/pengeluaran`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: new FormData(formPengeluaran),
                });

                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const pesan = hasil.errors
                        ? Object.values(hasil.errors).flat().join(' ')
                        : (hasil.message || 'Gagal menyimpan pengeluaran.');
                    throw new Error(pesan);
                }

                formPengeluaran.reset();
                location.hash = '';
                alert(hasil.message || 'Pengeluaran berhasil dicatat.');
            } catch (error) {
                alert(error.message);
            } finally {
                tombol.disabled = false;
            }
        });
    </script>
</body>
</html>