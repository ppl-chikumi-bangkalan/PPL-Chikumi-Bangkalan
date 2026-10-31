<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Chikumi</title>

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
                <a href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>

                    <span>Dashboard</span>
                </a>

                <!-- Kelola Produk -->
                <a href="#"
                    class="flex items-center gap-3 rounded-lg bg-[#FF8000] px-3 py-3 font-semibold text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    <span>Kelola Produk</span>
                </a>

                <!-- Ketersediaan Stok -->
                <a href="#"
                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>
                    <span>Ketersediaan Stok</span>
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
            </nav>

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

                <h1 class="text-xl font-extrabold italic text-[#FF8000]">
                    Chikumi
                </h1>

                <button id="menuButton" type="button"
                    class="flex items-center gap-2 rounded-lg border border-orange-100 px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>

                    <span>Menu</span>
                </button>

            </header>

            <div class="mx-auto max-w-[1500px] p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold sm:text-3xl">Kelola Produk</h2>
                        <p class="mt-1 text-xs text-stone-500 sm:text-sm">
                            Atur produk, varian, harga & stok.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <input type="search" id="searchInput" placeholder="Cari produk / varian..."
                            class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-xs outline-none focus:border-[#FF8000] sm:w-60">

                        <button onclick="openModal('product')"
                            class="rounded-lg bg-[#FF8000] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#E87500]">
                            + Tambah Produk
                        </button>
                    </div>
                </div>

                <!-- FILTER -->
                <div class="mb-5 flex gap-2 overflow-x-auto pb-2">
                    <button data-filter="all"
                        class="filter-btn shrink-0 rounded-full bg-[#FF8000] px-4 py-2 text-xs font-semibold text-white">
                        Semua
                    </button>
                    <button data-filter="Ukuran & Harga"
                        class="filter-btn shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs">
                        Ukuran & Harga
                    </button>
                    <button data-filter="Filling"
                        class="filter-btn shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs">
                        Filling
                    </button>
                    <button data-filter="Dip Sauce"
                        class="filter-btn shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs">
                        Dip Sauce
                    </button>
                    <button data-filter="Seasoning"
                        class="filter-btn shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs">
                        Seasoning
                    </button>
                </div>

                <!-- TABEL PRODUK -->
                <section class="mb-6 overflow-hidden rounded-xl border border-orange-100 bg-white">
                    <div class="flex items-center justify-between gap-3 border-b border-orange-100 p-4">
                        <div>
                            <h3 class="text-sm font-bold">Produk & Harga</h3>
                            <p class="mt-1 text-[10px] text-stone-500">
                                Harga ditentukan oleh ukuran + jenis topping
                            </p>
                        </div>

                        <button onclick="openModal('product')"
                            class="shrink-0 rounded-lg bg-orange-50 px-4 py-2 text-[10px] font-semibold text-[#FF8000] hover:bg-orange-100">
                            + Varian
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[650px] text-left text-xs">
                            <thead class="bg-[#F8F5F2] text-[10px] text-stone-500">
                                <tr>
                                    <th class="p-4">PRODUK</th>
                                    <th class="p-4">TOPPING</th>
                                    <th class="p-4">HARGA</th>
                                    <th class="p-4">STOK</th>
                                    <th class="p-4">STATUS</th>
                                    <th class="p-4 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="productTable" class="divide-y divide-stone-100"></tbody>
                        </table>
                        <div id="productEmpty" class="hidden p-6 text-center text-xs text-stone-400">
                            Produk tidak ditemukan.
                        </div>
                    </div>
                </section>

                <!-- TABEL VARIAN -->
                <section class="overflow-hidden rounded-xl border border-orange-100 bg-white">
                    <div class="flex items-center justify-between gap-3 border-b border-orange-100 p-4">
                        <div>
                            <h3 class="text-sm font-bold">Varian Pilihan</h3>
                            <p class="mt-1 text-[10px] text-stone-500">
                                Filling, dip sauce & seasoning yang bisa dipilih pelanggan
                            </p>
                        </div>

                        <button onclick="openModal('variant')"
                            class="shrink-0 rounded-lg bg-orange-50 px-4 py-2 text-[10px] font-semibold text-[#FF8000] hover:bg-orange-100">
                            + Varian
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[650px] text-left text-xs">
                            <thead class="bg-[#F8F5F2] text-[10px] text-stone-500">
                                <tr>
                                    <th class="p-4">NAMA VARIAN</th>
                                    <th class="p-4">KATEGORI</th>
                                    <th class="p-4">STOK (PORSI)</th>
                                    <th class="p-4">STATUS</th>
                                    <th class="p-4">AKTIF</th>
                                    <th class="p-4 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="variantTable" class="divide-y divide-stone-100"></tbody>
                        </table>
                        <div id="variantEmpty" class="hidden p-6 text-center text-xs text-stone-400">
                            Varian tidak ditemukan.
                        </div>
                    </div>
                </section>

                <p class="py-5 text-center text-[10px] text-stone-400">
                    Chikumi Admin Dashboard · Data contoh frontend
                </p>
            </div>
        </main>
    </div>

    <!-- MODAL -->
    <div id="dataModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-stone-900/40 p-4">

        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h3 id="modalTitle" class="text-base font-bold">Tambah Produk</h3>
                <button class="text-2xl text-stone-400 hover:text-stone-700">×</button>
            </div>

            <form id="dataForm">
                <div class="mb-4">
                    <label for="name" class="mb-2 block text-xs font-semibold">Nama</label>
                    <input id="name" required placeholder="Masukkan nama"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div class="mb-4">
                    <label for="category" class="mb-2 block text-xs font-semibold">Kategori</label>
                    <select id="category" required
                        class="form-input w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                        <option value="Filling">Filling</option>
                        <option value="Dip Sauce">Dip Sauce</option>
                        <option value="Seasoning">Seasoning</option>
                    </select>
                </div>

                <div id="sizeGroup" class="mb-4">
                    <label for="size" class="mb-2 block text-xs font-semibold">Ukuran Produk</label>
                    <input id="size" placeholder="Contoh: 15 cm"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div id="priceGroup" class="mb-4">
                    <label for="price" class="mb-2 block text-xs font-semibold">Harga (angka)</label>
                    <input id="price" type="number" min="0" placeholder="20000"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div class="mb-4">
                    <label for="stock" class="mb-2 block text-xs font-semibold">Stok</label>
                    <input id="stock" type="number" min="0" required placeholder="Masukkan stok"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="rounded-lg bg-stone-100 px-4 py-2.5 text-xs font-semibold text-stone-600 hover:bg-stone-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-[#FF8000] px-5 py-2.5 text-xs font-semibold text-white hover:bg-[#E87500]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>