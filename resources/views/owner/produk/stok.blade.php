<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Stok - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        {{-- SIDEBAR --}}
        @include('components.owner.sidebar')

        <!-- MAIN -->
        <main class="min-w-0 flex-1">

            <!-- HEADER MOBILE -->
            @include('components.owner.mobile-header')

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
