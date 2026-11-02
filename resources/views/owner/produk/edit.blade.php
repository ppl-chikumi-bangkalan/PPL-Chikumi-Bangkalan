
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Chikumi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#FFF9F5] text-stone-800">

<div class="flex min-h-screen">

    <!-- OVERLAY MOBILE -->
    <div id="sidebarOverlay"
        class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
        onclick="toggleSidebar()"></div>

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

    <!-- KONTEN UTAMA -->
    <main class="min-w-0 flex-1">

        <!-- HEADER: DIPERTAHANKAN -->
        <header class="border-b border-orange-100 bg-white px-5 py-5 sm:px-7">

            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                <div class="flex items-center gap-3">
                    <button type="button"
                        onclick="toggleSidebar()"
                        class="rounded-lg border border-orange-100 p-2 text-stone-600 hover:bg-orange-50 lg:hidden"
                        aria-label="Buka menu">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div>
                        <h2 class="text-2xl font-bold text-stone-800">
                            Produk & Stok
                        </h2>
                        <p class="mt-1 text-sm text-stone-500">
                            Atur harga & varian menu Chikumi
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z" />
                        </svg>

                        <input type="text" id="searchProduct"
                            placeholder="Cari produk..."
                            class="w-full rounded-lg border border-stone-200 bg-white py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100 sm:w-56">
                    </div>

                    <button type="button"
                        onclick="alert('Fitur tambah produk akan dihubungkan ke backend.')"
                        class="rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                        + Tambah Produk
                    </button>
                </div>
            </div>

            <!-- TAB: DIPERTAHANKAN -->
            <div class="mt-6 flex gap-6 border-b border-stone-100">
                <a href="{{ route('owner.stok') }}"
                    class="border-b-2 border-transparent pb-3 text-sm text-stone-500 transition hover:text-[#FF8000]">
                    Stok & Saran
                </a>

                <a href="{{ route('owner.produk') }}"
                    class="border-b-2 border-[#FF8000] pb-3 text-sm font-semibold text-[#FF8000]">
                    Data Produk
                </a>
            </div>
        </header>

        <!-- FORM EDIT PRODUK -->
        <section class="space-y-5 p-5 sm:p-7">

            <!-- JUDUL FORM -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm text-stone-400">
                        <a href="{{ route('owner.produk') }}"
                            class="hover:text-[#FF8000]">
                            Data Produk
                        </a>
                        <span>/</span>
                        <span class="text-[#FF8000]">Edit Produk</span>
                    </div>

                    <h3 class="mt-2 text-xl font-bold text-stone-800">
                        Edit Produk
                    </h3>
                    <p class="mt-1 text-sm text-stone-500">
                        Ubah informasi produk dan harga menu Chikumi.
                    </p>
                </div>

                <a href="{{ route('owner.produk') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-600 transition hover:bg-stone-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- FORM -->
            <form id="editProductForm" class="space-y-5">

                <!-- INFORMASI PRODUK -->
                <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">

                    <div class="border-b border-stone-100 px-5 py-4">
                        <h4 class="font-semibold text-stone-800">
                            Informasi Produk
                        </h4>
                        <p class="mt-1 text-xs text-stone-500">
                            Informasi dasar produk yang akan ditampilkan kepada pelanggan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <!-- Nama Produk -->
                        <div>
                            <label for="nama_produk"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Nama Produk
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" id="nama_produk" name="nama_produk"
                                value="Chikumi Reguler" required
                                class="w-full rounded-lg border border-stone-200 px-4 py-2.5 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">
                        </div>

                        <!-- Ukuran -->
                        <div>
                            <label for="ukuran"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Ukuran Produk
                                <span class="text-red-500">*</span>
                            </label>

                            <select id="ukuran" name="ukuran"
                                class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">
                                <option value="15 cm" selected>Reguler (15 cm)</option>
                                <option value="25 cm">Large (25 cm)</option>
                            </select>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="harga"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Harga Produk (Rp)
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="number" id="harga" name="harga"
                                value="20000" min="0" required
                                class="w-full rounded-lg border border-stone-200 px-4 py-2.5 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">
                        </div>

                        <!-- Takaran Filling -->
                        <div>
                            <label for="takaran"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Takaran Filling
                                <span class="text-red-500">*</span>
                            </label>

                            <select id="takaran" name="takaran"
                                class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">
                                <option value="1 porsi" selected>1 porsi</option>
                                <option value="1.5 porsi">1,5 porsi</option>
                                <option value="2 porsi">2 porsi</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Status Produk
                            </label>

                            <select id="status" name="status"
                                class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">
                                <option value="tersedia" selected>Tersedia</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <label for="deskripsi"
                                class="mb-2 block text-sm font-medium text-stone-700">
                                Deskripsi Produk
                            </label>

                            <textarea id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Masukkan deskripsi produk..."
                                class="w-full rounded-lg border border-stone-200 px-4 py-3 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">Chikumi dengan ukuran reguler dan pilihan topping.</textarea>
                        </div>

                    </div>
                </div>

                <!-- PILIHAN TOPPING -->
                <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">

                    <div class="border-b border-stone-100 px-5 py-4">
                        <h4 class="font-semibold text-stone-800">
                            Pilihan Topping
                        </h4>
                        <p class="mt-1 text-xs text-stone-500">
                            Pilih topping yang tersedia untuk produk ini.
                        </p>
                    </div>

                    <div class="space-y-5 p-5">

                        <!-- FILLING -->
                        <div>
                            <p class="mb-3 text-sm font-semibold text-stone-700">
                                Filling
                            </p>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Garlic Cream Cheese"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Garlic Cream Cheese</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Cream Cheese"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Cream Cheese</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Mentaiko"
                                        class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Mentaiko</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Tartar"
                                        class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Tartar</span>
                                </label>

                            </div>
                        </div>

                        <div class="border-t border-stone-100"></div>

                        <!-- DIP SAUCE -->
                        <div>
                            <p class="mb-3 text-sm font-semibold text-stone-700">
                                Dip Sauce
                            </p>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="BBQ"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">BBQ</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Black Pepper"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Black Pepper</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Korean Spicy"
                                        class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Korean Spicy</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Teriyaki"
                                        class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Teriyaki</span>
                                </label>

                            </div>
                        </div>

                        <div class="border-t border-stone-100"></div>

                        <!-- SEASONING -->
                        <div>
                            <p class="mb-3 text-sm font-semibold text-stone-700">
                                Seasoning
                            </p>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="BBQ Seasoning"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">BBQ</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Sweetcorn"
                                        checked class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Sweetcorn</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                    <input type="checkbox" name="topping[]" value="Extra Hot"
                                        class="size-4 accent-[#FF8000]">
                                    <span class="text-sm text-stone-700">Extra Hot</span>
                                </label>

                            </div>
                        </div>

                    </div>
                </div>

                <!-- TOMBOL AKSI -->
                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">

                    <a href="{{ route('owner.produk') }}"
                        class="rounded-lg border border-stone-200 bg-white px-5 py-2.5 text-center text-sm font-semibold text-stone-600 transition hover:bg-stone-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-[#FF8000] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </section>
    </main>
</div>
</body>
</html>
