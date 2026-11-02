
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

<div class="flex min-h-screen">

    <!-- OVERLAY MOBILE -->
    <div id="sidebarOverlay"
        class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"
        onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    @include('components.owner.sidebar')

    <!-- KONTEN UTAMA -->
    <main class="min-w-0 flex-1">

        <!-- HEADER: DIPERTAHANKAN -->
        @include('components.owner.mobile-header')

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
