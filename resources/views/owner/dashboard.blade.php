
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 flex w-56 -translate-x-full flex-col border-r border-orange-100 bg-white transition-transform duration-300 lg:static lg:translate-x-0">

            <!-- LOGO -->
            <div class="border-b border-orange-100 px-5 py-4">
                <h1 class="text-xl font-extrabold italic text-[#FF8000]">Chikumi</h1>
                <p class="text-[8px] font-bold tracking-wider">JAPANESE SNACK</p>
            </div>

            <!-- MENU -->
            <nav class="flex-1 space-y-1 p-3 text-xs">

                <p class="mb-3 px-2 pt-2 text-[9px] font-semibold text-stone-400">
                    MENU OWNER
                </p>

                <!-- DASHBOARD -->
                <a href="#"
                    class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-3 py-2.5 font-semibold text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 3.75h6.75v6.75H3.75zm9.75 0h6.75v6.75H13.5zm-9.75 9.75h6.75v6.75H3.75zm9.75 0h6.75v6.75H13.5z" />
                    </svg>
                    Dashboard
                </a>

                <!-- PRODUK -->
                <a href="/owner/kelola-produk"
                    class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-stone-600 hover:bg-orange-50 hover:text-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    Produk & Stok
                </a>

                <!-- TRANSAKSI -->
                <a href="#"
                    class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-stone-600 hover:bg-orange-50 hover:text-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75" />
                    </svg>
                    Transaksi
                </a>

                <!-- LAPORAN -->
                <a href="#"
                    class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-stone-600 hover:bg-orange-50 hover:text-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Zm2.25 4.5h4.5m-4.5 3h4.5m-4.5 3h4.5" />
                    </svg>
                    Laporan
                </a>
            </nav>

            <!-- PROFIL OWNER -->
            <div class="m-3 flex items-center gap-2 rounded-xl bg-orange-50 p-2.5">
                <div class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-[#FF8000] text-xs font-bold text-white">
                    O
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-[9px] font-semibold">Owner Chikumi</p>
                    <p class="text-[8px] text-stone-500">Owner</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" aria-label="Logout" class="text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="min-w-0 flex-1">

            <!-- HEADER MOBILE -->
            <header class="flex items-center justify-between border-b border-orange-100 bg-white px-4 py-3 lg:hidden">
                <h1 class="text-xl font-extrabold italic text-[#FF8000]">Chikumi</h1>

                <button id="menuButton" type="button"
                    class="flex items-center gap-2 rounded-lg border border-orange-100 px-3 py-2 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    Menu
                </button>
            </header>

            <div class="mx-auto max-w-[1500px] space-y-4 p-4 sm:p-6 lg:p-7">

                <!-- JUDUL DAN FILTER WAKTU -->
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Dashboard</h2>
                        <p class="text-[10px] text-stone-500">Kondisi usaha Chikumi</p>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <button data-period="Hari Ini"
                            class="period-btn rounded-full border border-stone-200 bg-white px-3 py-1.5 text-[9px]">
                            Hari Ini
                        </button>

                        <button data-period="Minggu Ini"
                            class="period-btn rounded-full border border-stone-200 bg-white px-3 py-1.5 text-[9px]">
                            Minggu Ini
                        </button>

                        <button data-period="Bulan Ini"
                            class="period-btn rounded-full bg-[#FF8000] px-3 py-1.5 text-[9px] font-semibold text-white">
                            Bulan Ini
                        </button>
                    </div>
                </div>

                <!-- KARTU RINGKASAN -->
                <div class="grid grid-cols-2 gap-2 xl:grid-cols-4">

                    <!-- OMZET -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 18V5.25m0 12.75h16.5M7.5 15l3.75-4.5 3 2.25 4.5-6" />
                            </svg>
                        </div>
                        <p class="text-[9px] text-stone-500">Omzet</p>
                        <h3 class="mt-1 text-sm font-bold">Rp 8.750.000</h3>
                        <p class="mt-1 text-[8px] font-medium text-green-600">↑ 8% vs bulan lalu</p>
                    </div>

                    <!-- PENGELUARAN -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 6.75h7.5m-7.5 3h7.5m-7.5 3h4.5M6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75Z" />
                            </svg>
                        </div>
                        <p class="text-[9px] text-stone-500">Pengeluaran</p>
                        <h3 class="mt-1 text-sm font-bold">Rp 5.200.000</h3>
                        <p class="mt-1 text-[8px] text-stone-400">Total biaya usaha</p>
                    </div>

                    <!-- LABA -->
                    <div class="rounded-xl border border-[#FF8000] bg-[#FF8000] p-3 text-white">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-black/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9 12.75 2.25 2.25 4.5-5.25m5.25 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <p class="text-[9px] text-white/80">Laba Bersih*</p>
                        <h3 class="mt-1 text-sm font-bold">Rp 3.550.000</h3>
                        <p class="mt-1 text-[8px] text-white/80">Margin 40,6%</p>
                    </div>

                    <!-- TRANSAKSI -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5h18l-1.5 12h-15L3 7.5Zm4.5 0 4.5-4.5 4.5 4.5M9 12h.008v.008H9V12Zm6 0h.008v.008H15V12Z" />
                            </svg>
                        </div>
                        <p class="text-[9px] text-stone-500">Transaksi</p>
                        <h3 class="mt-1 text-sm font-bold">412</h3>
                        <p class="mt-1 text-[8px] text-stone-400">Kasir 238 · Online 174</p>
                    </div>
                </div>

                <!-- GRAFIK -->
                <section class="rounded-xl border border-orange-100 bg-white p-4">

                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="text-xs font-bold">Omzet vs Pengeluaran</h3>
                            <p class="text-[8px] text-stone-400">Perbandingan per minggu · Oktober 2026</p>
                        </div>

                        <div class="flex gap-3 text-[8px]">
                            <span class="flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-[#FF8000]"></span>
                                Omzet
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-stone-500"></span>
                                Pengeluaran
                            </span>
                        </div>
                    </div>

                    <!-- BAR CHART -->
                    <div class="flex h-36 items-end justify-around gap-3 border-b border-stone-100 px-2">

                        <div class="flex h-full flex-1 flex-col items-center justify-end">
                            <div class="flex h-full items-end gap-1">
                                <div class="w-3 rounded-t bg-[#FF8000]" style="height: 65%"></div>
                                <div class="w-3 rounded-t bg-stone-500" style="height: 43%"></div>
                            </div>
                            <p class="mt-2 text-[7px] text-stone-400">1-7 Okt</p>
                        </div>

                        <div class="flex h-full flex-1 flex-col items-center justify-end">
                            <div class="flex h-full items-end gap-1">
                                <div class="w-3 rounded-t bg-[#FF8000]" style="height: 74%"></div>
                                <div class="w-3 rounded-t bg-stone-500" style="height: 51%"></div>
                            </div>
                            <p class="mt-2 text-[7px] text-stone-400">8-14 Okt</p>
                        </div>

                        <div class="flex h-full flex-1 flex-col items-center justify-end">
                            <div class="flex h-full items-end gap-1">
                                <div class="w-3 rounded-t bg-[#FF8000]" style="height: 76%"></div>
                                <div class="w-3 rounded-t bg-stone-500" style="height: 55%"></div>
                            </div>
                            <p class="mt-2 text-[7px] text-stone-400">15-21 Okt</p>
                        </div>

                        <div class="flex h-full flex-1 flex-col items-center justify-end">
                            <div class="flex h-full items-end gap-1">
                                <div class="w-3 rounded-t bg-[#FF8000]" style="height: 86%"></div>
                                <div class="w-3 rounded-t bg-stone-500" style="height: 52%"></div>
                            </div>
                            <p class="mt-2 text-[7px] text-stone-400">22-28 Okt</p>
                        </div>

                        <div class="flex h-full flex-1 flex-col items-center justify-end">
                            <div class="flex h-full items-end gap-1">
                                <div class="w-3 rounded-t bg-[#FF8000]" style="height: 15%"></div>
                                <div class="w-3 rounded-t bg-stone-500" style="height: 9%"></div>
                            </div>
                            <p class="mt-2 text-[7px] text-stone-400">29-31 Okt</p>
                        </div>
                    </div>
                </section>

                <!-- KONDISI STOK -->
                <section class="overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="flex items-center justify-between gap-2 p-4">
                        <div>
                            <h3 class="text-xs font-bold">Kondisi Stok</h3>
                            <p class="text-[8px] text-stone-400">Komponen yang perlu disiapkan / restock</p>
                        </div>

                        <a href="/owner/kelola-produk"
                            class="text-[8px] font-semibold text-[#FF8000] hover:underline">
                            Lihat Produk & Stok →
                        </a>
                    </div>

                    <!-- RINGKASAN STOK -->
                    <div class="grid grid-cols-3 gap-2 px-4 pb-3">

                        <div class="rounded-lg bg-green-50 p-2">
                            <h4 class="text-sm font-bold text-green-600">7 komponen</h4>
                            <p class="text-[8px] text-stone-500">Aman</p>
                        </div>

                        <div class="rounded-lg bg-red-50 p-2">
                            <h4 class="text-sm font-bold text-red-500">4 komponen</h4>
                            <p class="text-[8px] text-stone-500">Di bawah minimum</p>
                        </div>

                        <div class="rounded-lg bg-rose-100 p-2">
                            <h4 class="text-sm font-bold text-rose-600">2 komponen</h4>
                            <p class="text-[8px] text-stone-500">Habis</p>
                        </div>
                    </div>

                    <!-- TABEL STOK -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[500px] text-left text-[8px]">

                            <thead class="bg-[#F8F5F2] text-stone-500">
                                <tr>
                                    <th class="p-3">KOMPONEN</th>
                                    <th class="p-3">SISA</th>
                                    <th class="p-3">SARAN</th>
                                    <th class="p-3">STATUS</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-red-100">

                                <tr class="bg-red-50">
                                    <td class="p-3 font-semibold text-red-600">Kulit Larga</td>
                                    <td class="p-3 text-red-500">0 pcs</td>
                                    <td class="p-3 text-red-500">+80 pcs</td>
                                    <td class="p-3 font-semibold text-red-500">● Habis</td>
                                </tr>

                                <tr class="bg-red-50">
                                    <td class="p-3 font-semibold text-red-600">Seasoning Extra Hot</td>
                                    <td class="p-3 text-red-500">0 takaran</td>
                                    <td class="p-3 text-red-500">Restock</td>
                                    <td class="p-3 font-semibold text-red-500">● Habis</td>
                                </tr>

                                <tr class="bg-red-50">
                                    <td class="p-3 font-semibold text-red-600">Kulit Reguler</td>
                                    <td class="p-3 text-red-500">4 pcs</td>
                                    <td class="p-3 text-red-500">+40 pcs</td>
                                    <td class="p-3 font-semibold text-red-500">● Minimum</td>
                                </tr>

                                <tr class="bg-red-50">
                                    <td class="p-3 font-semibold text-red-600">MentaiKio</td>
                                    <td class="p-3 text-red-500">6 porsi</td>
                                    <td class="p-3 text-red-500">+8 porsi</td>
                                    <td class="p-3 font-semibold text-red-500">● Minimum</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </section>

                <p class="py-2 text-center text-[8px] text-stone-400">
                    Chikumi Owner Dashboard · Data contoh frontend
                </p>

                <p class="text-center text-[8px] text-stone-400">
                    *Laba bersih pada contoh ini dihitung dari omzet dikurangi pengeluaran.
                </p>

            </div>
        </main>
    </div>
</body>
</html>
