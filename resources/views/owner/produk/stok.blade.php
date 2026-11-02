<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok & Saran - Chikumi</title>

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
            class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-orange-100 bg-white transition-transform duration-300 lg:static lg:translate-x-0">

            <div class="border-b border-orange-100 px-6 py-5">
                <h1 class="text-2xl font-extrabold italic text-[#FF8000]">Chikumi</h1>
                <p class="text-[9px] font-bold tracking-wider">JAPANESE SNACK</p>
            </div>

            <nav class="flex-1 space-y-2 p-4 text-sm">

                <p class="mb-3 px-3 text-[10px] font-semibold text-stone-400">
                    MENU ADMIN
                </p>

                <!-- Dashboard -->
                <a href="{{ route('owner.dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>

                    <span>Dashboard</span>
                </a>

                <!-- Kelola Produk -->
                <a href="{{ route('owner.stok') }}"
                    class="flex items-center gap-3 rounded-lg bg-[#FF8000] px-3 py-3 font-semibold text-white">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>

                    <span>Kelola Produk</span>
                </a>

                <!-- Transaksi -->
                <a href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>

                    <span>Transaksi</span>
                </a>

                <!-- Laporan -->
                <a href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>

                    <span>Laporan</span>
                </a>

            </nav>

            <!-- PROFILE OWNER -->
            <div class="m-3 flex items-center gap-3 rounded-xl bg-orange-50 p-3">

                <div
                    class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#FF8000] text-sm font-bold text-white">
                    A
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold">Admin Chikumi</p>
                    <p class="text-[10px] text-stone-500">Owner</p>
                </div>

                <button type="button" aria-label="Logout"
                    class="rounded-lg p-2 text-red-500 transition hover:bg-orange-100">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>

                </button>
            </div>

        </aside>

        <!-- MAIN -->
        <main class="min-w-0 flex-1">

            <!-- HEADER MOBILE -->
            <header class="flex items-center justify-between border-b border-orange-100 bg-white px-4 py-3 lg:hidden">

                <h1 class="text-xl font-extrabold italic text-[#FF8000]">Chikumi</h1>

                <button id="menuButton" type="button"
                    class="flex items-center gap-2 rounded-lg border border-orange-100 px-3 py-2 text-sm font-medium text-stone-600">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>

                    Menu
                </button>

            </header>

            <div class="mx-auto max-w-[1500px] p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold sm:text-3xl">Produk & Stok</h2>
                    <p class="mt-2 text-sm text-stone-500">
                        Pantau sisa stok dan rencanakan penambahan stok.
                    </p>
                </div>

                <!-- TAB -->
                <div class="mb-6 flex gap-6 border-b border-orange-100 text-sm">

                    <a href="{{ route('owner.stok') }}"
                        class="border-b-2 border-[#FF8000] pb-3 font-semibold text-[#FF8000]">
                        Stok & Saran
                    </a>

                    <a href="{{ route('owner.produk') }}" class="pb-3 text-stone-500 hover:text-[#FF8000]">
                        Data Produk
                    </a>

                </div>

                <!-- RINGKASAN -->
                <div class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">

                    <div class="rounded-xl border border-orange-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Total Komponen</p>
                        <h3 class="text-3xl font-bold">13</h3>
                    </div>

                    <div class="rounded-xl border border-green-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Aman</p>
                        <h3 class="text-3xl font-bold text-green-600">7</h3>
                    </div>

                    <div class="rounded-xl border border-orange-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Menipis</p>
                        <h3 class="text-3xl font-bold text-orange-500">4</h3>
                    </div>

                    <div class="rounded-xl border border-red-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Habis</p>
                        <h3 class="text-3xl font-bold text-red-500">2</h3>
                    </div>

                </div>

                <!-- PERINGATAN -->
                <div
                    class="mb-6 flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-600">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.949 3.374H4.646c-1.732 0-2.815-1.874-1.949-3.374L10.05 3.878c.866-1.5 3.034-1.5 3.9 0l7.353 12.248Z" />
                    </svg>

                    <div>
                        <p class="font-semibold">Perhatian! Ada komponen yang perlu ditambah.</p>
                        <p class="mt-1 text-xs">
                            Periksa komponen dengan status menipis atau habis sebelum persiapan penjualan berikutnya.
                        </p>
                    </div>

                </div>

                <!-- FILTER -->
                <div class="mb-5 flex flex-wrap gap-2">

                    <button onclick="filterTable('Semua', this)"
                        class="filter-btn rounded-full bg-[#FF8000] px-5 py-2.5 text-sm font-semibold text-white">
                        Semua
                    </button>

                    <button onclick="filterTable('Kulit', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Kulit
                    </button>

                    <button onclick="filterTable('Filling', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Filling
                    </button>

                    <button onclick="filterTable('Dip Sauce', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Dip Sauce
                    </button>

                    <button onclick="filterTable('Seasoning', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Seasoning
                    </button>

                </div>

                <!-- TABEL 1 -->
                <section class="mb-6 overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="border-b border-orange-100 p-5">
                        <h3 class="text-base font-bold">Disiapkan Harian - Kulit & Filling</h3>
                        <p class="mt-1 text-xs text-stone-500">
                            Stok produk siap jual dan kebutuhan persiapan harian.
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="bg-[#F8F5F2] text-xs font-semibold text-stone-500">
                                <tr>
                                    <th class="p-4">KOMPONEN</th>
                                    <th class="p-4">SISA STOK</th>
                                    <th class="p-4">MINIMUM</th>
                                    <th class="p-4">TERPAKAI / HARI</th>
                                    <th class="p-4">SARAN BESOK</th>
                                    <th class="p-4">TAMBAH</th>
                                    <th class="p-4">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="stockTable" class="divide-y divide-stone-100">

                                <tr data-category="Kulit" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Kulit Reguler</td>
                                    <td class="p-4">4 pcs</td>
                                    <td class="p-4">10 pcs</td>
                                    <td class="p-4">38 pcs</td>
                                    <td class="p-4 font-semibold text-orange-600">34 pcs</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="34"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">Hampir
                                            habis</span>
                                    </td>
                                </tr>

                                <tr data-category="Kulit" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Kulit Large</td>
                                    <td class="p-4">0 pcs</td>
                                    <td class="p-4">5 pcs</td>
                                    <td class="p-4">18 pcs</td>
                                    <td class="p-4 font-semibold text-red-600">18 pcs</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="18"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">Habis</span>
                                    </td>
                                </tr>

                                <tr data-category="Filling" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Mentai</td>
                                    <td class="p-4">6 porsi</td>
                                    <td class="p-4">14 porsi</td>
                                    <td class="p-4">14 porsi</td>
                                    <td class="p-4 font-semibold text-orange-600">8 porsi</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="8"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">Menipis</span>
                                    </td>
                                </tr>

                                <tr data-category="Filling">
                                    <td class="p-4 font-semibold">Garlic Cream Cheese</td>
                                    <td class="p-4">12 porsi</td>
                                    <td class="p-4">8 porsi</td>
                                    <td class="p-4">15 porsi</td>
                                    <td class="p-4">3 porsi</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="3"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                                <tr data-category="Filling">
                                    <td class="p-4 font-semibold">Cream Cheese</td>
                                    <td class="p-4">20 porsi</td>
                                    <td class="p-4">8 porsi</td>
                                    <td class="p-4">12 porsi</td>
                                    <td class="p-4">-</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                                <tr data-category="Filling">
                                    <td class="p-4 font-semibold">Tartar</td>
                                    <td class="p-4">8 porsi</td>
                                    <td class="p-4">8 porsi</td>
                                    <td class="p-4">7 porsi</td>
                                    <td class="p-4">-</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- TABEL 2 -->
                <section class="overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="border-b border-orange-100 p-5">
                        <h3 class="text-base font-bold">Restock Bila Menipis - Dip Sauce & Seasoning</h3>
                        <p class="mt-1 text-xs text-stone-500">
                            Persediaan bumbu dan saus untuk kebutuhan penjualan.
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="bg-[#F8F5F2] text-xs font-semibold text-stone-500">
                                <tr>
                                    <th class="p-4">KOMPONEN</th>
                                    <th class="p-4">SISA STOK</th>
                                    <th class="p-4">MINIMUM</th>
                                    <th class="p-4">TERPAKAI / HARI</th>
                                    <th class="p-4">CUKUP UNTUK</th>
                                    <th class="p-4">TAMBAH</th>
                                    <th class="p-4">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="restockTable" class="divide-y divide-stone-100">

                                <tr data-category="Dip Sauce" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Dip Korean Spicy</td>
                                    <td class="p-4">12 cup</td>
                                    <td class="p-4">15 cup</td>
                                    <td class="p-4">9 cup</td>
                                    <td class="p-4">1 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="30"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">Menipis</span>
                                    </td>
                                </tr>

                                <tr data-category="Dip Sauce" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Saus Extra Hot</td>
                                    <td class="p-4">0 cup</td>
                                    <td class="p-4">20 cup</td>
                                    <td class="p-4">8 cup</td>
                                    <td class="p-4">Habis</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="40"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">Habis</span>
                                    </td>
                                </tr>

                                <tr data-category="Seasoning" class="bg-red-50/60">
                                    <td class="p-4 font-semibold">Seasoning Sweetcorn</td>
                                    <td class="p-4">18 porsi</td>
                                    <td class="p-4">20 porsi</td>
                                    <td class="p-4">7 porsi</td>
                                    <td class="p-4">2 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="30"
                                            class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">Menipis</span>
                                    </td>
                                </tr>

                                <tr data-category="Dip Sauce">
                                    <td class="p-4 font-semibold">Dip BBQ</td>
                                    <td class="p-4">40 cup</td>
                                    <td class="p-4">15 cup</td>
                                    <td class="p-4">6 cup</td>
                                    <td class="p-4">5 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                                <tr data-category="Dip Sauce">
                                    <td class="p-4 font-semibold">Dip Black Pepper</td>
                                    <td class="p-4">22 cup</td>
                                    <td class="p-4">15 cup</td>
                                    <td class="p-4">5 cup</td>
                                    <td class="p-4">4 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                                <tr data-category="Dip Sauce">
                                    <td class="p-4 font-semibold">Dip Teriyaki</td>
                                    <td class="p-4">30 cup</td>
                                    <td class="p-4">15 cup</td>
                                    <td class="p-4">6 cup</td>
                                    <td class="p-4">5 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                                <tr data-category="Seasoning">
                                    <td class="p-4 font-semibold">Seasoning BBQ</td>
                                    <td class="p-4">60 porsi</td>
                                    <td class="p-4">20 porsi</td>
                                    <td class="p-4">10 porsi</td>
                                    <td class="p-4">6 hari</td>
                                    <td class="p-4">
                                        <input type="number" min="0" value="0"
                                            class="stock-input w-20 rounded-lg border border-stone-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-600">Aman</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SIMPAN STOK -->
                <div
                    class="mt-6 flex flex-col gap-4 rounded-xl border border-orange-100 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-sm font-semibold">
                            <span id="itemCount">0</span> komponen dipilih untuk ditambah
                        </p>
                        <p class="mt-1 text-xs text-stone-500">
                            Periksa jumlah tambahan sebelum menyimpan.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button type="button" id="resetStock"
                            class="px-6 py-3 rounded-lg border border-gray-200
               bg-white text-gray-700 font-semibold
               hover:bg-gray-50 transition">
                            Reset
                        </button>

                        <button type="button" id="saveStock"
                            class="px-6 py-3 rounded-lg bg-[#FF8000]
               text-white font-semibold
               hover:bg-orange-600 transition">
                            Simpan Stok
                        </button>
                    </div>
                </div>

                <p class="py-6 text-center text-xs text-stone-400">
                    Chikumi Admin Dashboard · Data contoh frontend
                </p>

            </div>
        </main>
    </div>

</body>

</html>
