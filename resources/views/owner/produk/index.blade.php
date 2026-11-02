<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Produk - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden" onclick="toggleSidebar()">
        </div>

        <!-- SIDEBAR -->
        @include('components.owner.sidebar')

        <!-- KONTEN UTAMA -->
        <main class="min-w-0 flex-1">

            <!-- HEADER -->
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

                    <a href="{{ route('owner.stok') }}" class="pb-3 text-stone-500 transition hover:text-[#FF8000]">
                        Stok & Saran
                    </a>

                    <a href="{{ route('owner.produk') }}"
                        class="border-b-2 border-[#FF8000] pb-3 font-semibold text-[#FF8000]">
                        Data Produk
                    </a>

                </div>


                <!-- ISI HALAMAN -->
                <section class="space-y-5 p-5 sm:p-7">

                    <!-- FILTER KATEGORI -->
                    <div class="flex flex-wrap items-center gap-2" id="categoryFilters">
                        <button type="button" data-filter="Semua"
                            class="category-btn rounded-full bg-[#FF8000] px-4 py-2 text-xs font-semibold text-white">
                            Semua
                        </button>

                        <button type="button" data-filter="Ukuran & Harga"
                            class="category-btn rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium text-stone-600 hover:border-[#FF8000] hover:text-[#FF8000]">
                            Ukuran & Harga
                        </button>

                        <button type="button" data-filter="Filling"
                            class="category-btn rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium text-stone-600 hover:border-[#FF8000] hover:text-[#FF8000]">
                            Filling
                        </button>

                        <button type="button" data-filter="Dip Sauce"
                            class="category-btn rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium text-stone-600 hover:border-[#FF8000] hover:text-[#FF8000]">
                            Dip Sauce
                        </button>

                        <button type="button" data-filter="Seasoning"
                            class="category-btn rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium text-stone-600 hover:border-[#FF8000] hover:text-[#FF8000]">
                            Seasoning
                        </button>
                    </div>

                    <!-- PRODUK DAN HARGA -->
                    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">

                        <div class="flex items-center justify-between gap-3 px-5 py-4">
                            <div>
                                <h3 class="text-base font-semibold text-stone-800">
                                    Produk & Harga
                                </h3>
                                <p class="mt-1 text-xs text-stone-500">
                                    Harga ditentukan oleh ukuran + jenis topping
                                </p>
                            </div>

                            <button type="button"
                                class="shrink-0 rounded-lg bg-orange-50 px-4 py-2 text-sm font-semibold text-[#FF8000] transition hover:bg-orange-100">
                                + Varian
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">

                                <thead class="bg-[#FFF9F5] text-xs text-stone-500">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">PRODUK</th>
                                        <th class="px-5 py-3 font-semibold">TOPPING</th>
                                        <th class="px-5 py-3 font-semibold">HARGA</th>
                                        <th class="px-5 py-3 font-semibold">TAKARAN FILLING</th>
                                        <th class="px-5 py-3 font-semibold">STATUS</th>
                                        <th class="px-5 py-3 text-center font-semibold">AKSI</th>
                                    </tr>
                                </thead>

                                <tbody id="productTable" class="divide-y divide-stone-100">

                                    @php
                                        $products = [
                                            [
                                                'name' => 'Chikumi Reguler',
                                                'size' => '15 cm',
                                                'topping' => 'Seasoning',
                                                'price' => '20.000',
                                                'filling' => '1 porsi',
                                                'status' => 'Tersedia',
                                            ],
                                            [
                                                'name' => 'Chikumi Reguler',
                                                'size' => '15 cm',
                                                'topping' => 'Dip Sauce',
                                                'price' => '21.000',
                                                'filling' => '1 porsi',
                                                'status' => 'Tersedia',
                                            ],
                                            [
                                                'name' => 'Chikumi Large',
                                                'size' => '25 cm',
                                                'topping' => 'Seasoning',
                                                'price' => '26.000',
                                                'filling' => '1,5 porsi',
                                                'status' => 'Habis',
                                            ],
                                            [
                                                'name' => 'Chikumi Large',
                                                'size' => '25 cm',
                                                'topping' => 'Dip Sauce',
                                                'price' => '27.000',
                                                'filling' => '1,5 porsi',
                                                'status' => 'Habis',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($products as $product)
                                        <tr class="product-row transition hover:bg-orange-50/40"
                                            data-category="{{ $product['topping'] }}"
                                            data-search="{{ strtolower($product['name'] . ' ' . $product['size'] . ' ' . $product['topping']) }}">

                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-lg">
                                                        🍢
                                                    </div>

                                                    <div>
                                                        <p class="font-semibold text-stone-800">
                                                            {{ $product['name'] }}
                                                        </p>
                                                        <p class="mt-1 text-xs text-stone-400">
                                                            {{ $product['size'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-5 py-3 text-stone-600">
                                                {{ $product['topping'] }}
                                            </td>

                                            <td class="px-5 py-3 font-semibold text-[#FF8000]">
                                                Rp {{ $product['price'] }}
                                            </td>

                                            <td class="px-5 py-3 text-stone-600">
                                                {{ $product['filling'] }}
                                            </td>

                                            <td class="px-5 py-3">
                                                @if ($product['status'] === 'Tersedia')
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600">
                                                        <span>●</span> Tersedia
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">
                                                        <span>●</span> Habis
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-3">
                                                <div class="flex items-center justify-center gap-3">

                                                    <!-- EDIT -->
                                                    <button type="button"
                                                        onclick="window.location.href='{{ route('owner.produk.edit') }}'"
                                                        class="text-stone-400 transition hover:text-[#FF8000]"
                                                        title="Edit produk">

                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </button>

                                                    <!-- HAPUS -->
                                                    <button type="button"
                                                        class="text-red-400 transition hover:text-red-600"
                                                        title="Hapus produk">

                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- VARIAN PILIHAN -->
                    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">

                        <div class="flex items-center justify-between gap-3 px-5 py-4">
                            <div>
                                <h3 class="text-base font-semibold text-stone-800">
                                    Varian Pilihan
                                </h3>
                                <p class="mt-1 text-xs text-stone-500">
                                    Filling, dip sauce & seasoning yang bisa dipilih pelanggan
                                </p>
                            </div>

                            <button type="button"
                                class="shrink-0 rounded-lg bg-orange-50 px-4 py-2 text-sm font-semibold text-[#FF8000] transition hover:bg-orange-100">
                                + Varian
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">

                                <thead class="bg-[#FFF9F5] text-xs text-stone-500">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">NAMA VARIAN</th>
                                        <th class="px-5 py-3 font-semibold">KATEGORI</th>
                                        <th class="px-5 py-3 font-semibold">TAMBAHAN HARGA</th>
                                        <th class="px-5 py-3 font-semibold">STATUS</th>
                                        <th class="px-5 py-3 font-semibold">AKTIF</th>
                                        <th class="px-5 py-3 text-center font-semibold">AKSI</th>
                                    </tr>
                                </thead>

                                <tbody id="variantTable" class="divide-y divide-stone-100">

                                    @php
                                        $variants = [
                                            [
                                                'name' => 'Garlic Cream Cheese',
                                                'category' => 'Filling',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Cream Cheese',
                                                'category' => 'Filling',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Mentaiko',
                                                'category' => 'Filling',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Tartar',
                                                'category' => 'Filling',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'BBQ',
                                                'category' => 'Dip Sauce',
                                                'price' => 'Rp 1.000',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Black Pepper',
                                                'category' => 'Dip Sauce',
                                                'price' => 'Rp 1.000',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Korean Spicy',
                                                'category' => 'Dip Sauce',
                                                'price' => 'Rp 1.000',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Teriyaki',
                                                'category' => 'Dip Sauce',
                                                'price' => 'Rp 1.000',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'BBQ',
                                                'category' => 'Seasoning',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Sweetcorn',
                                                'category' => 'Seasoning',
                                                'price' => 'Rp 0',
                                                'status' => 'Tersedia',
                                                'active' => true,
                                            ],
                                            [
                                                'name' => 'Extra Hot',
                                                'category' => 'Seasoning',
                                                'price' => 'Rp 0',
                                                'status' => 'Habis',
                                                'active' => false,
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($variants as $variant)
                                        <tr class="variant-row transition hover:bg-orange-50/40"
                                            data-category="{{ $variant['category'] }}"
                                            data-search="{{ strtolower($variant['name'] . ' ' . $variant['category']) }}">

                                            <td class="px-5 py-3 font-semibold text-stone-800">
                                                {{ $variant['name'] }}
                                            </td>

                                            <td class="px-5 py-3 text-stone-500">
                                                {{ $variant['category'] }}
                                            </td>

                                            <td class="px-5 py-3 text-stone-600">
                                                +{{ $variant['price'] }}
                                            </td>

                                            <td class="px-5 py-3">
                                                @if ($variant['status'] === 'Tersedia')
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-600">
                                                        <span>●</span> Tersedia
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">
                                                        <span>●</span> Habis
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-3">
                                                <label class="relative inline-flex cursor-pointer items-center">
                                                    <input type="checkbox" class="peer sr-only"
                                                        {{ $variant['active'] ? 'checked' : '' }}>

                                                    <span
                                                        class="h-5 w-10 rounded-full bg-stone-200 transition peer-checked:bg-[#FF8000]"></span>

                                                    <span
                                                        class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                                                </label>
                                            </td>

                                            <td class="px-5 py-3">
                                                <div class="flex items-center justify-center gap-3">

                                                    <!-- EDIT -->
                                                    <button type="button"
                                                        class="text-stone-400 transition hover:text-[#FF8000]"
                                                        title="Edit varian">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </button>

                                                    <!-- HAPUS -->
                                                    <button type="button"
                                                        class="text-red-400 transition hover:text-red-600"
                                                        title="Hapus varian">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-stone-100 px-5 py-3">
                            <p class="text-xs text-stone-400">
                                *Selisih harga dip sauce sudah termasuk di total produk & harga.
                            </p>
                        </div>
                    </div>

                </section>
        </main>
    </div>

</body>

</html>
