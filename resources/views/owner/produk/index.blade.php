<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Data Produk - Admin</title>

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
        @include('components.owner.sidebar')

        <!-- KONTEN UTAMA -->
        <main class="min-w-0 flex-1">

            <!-- HEADER -->
            @include('components.owner.mobile-header')

            <div class="mx-auto max-w-[1500px] p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                    <div>
                        <h2 class="text-2xl font-bold sm:text-3xl">Produk & Stok</h2>
                        <p class="mt-2 text-sm text-stone-500">
                            Atur harga & varian menu Chikumi
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <input type="search" id="searchInput" placeholder="Cari produk..."
                            class="w-full rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#FF8000] sm:w-56">

                        <button type="button" onclick="openModal('product')"
                            class="shrink-0 rounded-xl bg-[#FF8000] px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                            + Tambah Produk
                        </button>
                    </div>
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

                            <button type="button" onclick="openModal('product')"
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
                                        $typeLabel = [
                                            'filling' => 'Filling',
                                            'dipping_sauce' => 'Dip Sauce',
                                            'seasoning' => 'Seasoning',
                                        ];

                                        $productRows = $products->flatMap(function ($product) use ($typeLabel) {
                                            $sizes = $product->sizes->isEmpty() ? collect([null]) : $product->sizes;
                                            $toppings = $product->variants->where('type', '!=', 'filling');
                                            $fillings = $product->variants->where('type', 'filling');
                                            $category = collect(['Ukuran & Harga'])
                                                ->merge($product->variants->map(fn ($variant) => $typeLabel[$variant->type] ?? $variant->type))
                                                ->unique()
                                                ->implode('|');

                                            return $sizes->map(fn ($size) => [
                                                'id' => $product->id,
                                                'size_id' => $size?->id,
                                                'name' => $product->name,
                                                'size' => $size?->size_name ?? '-',
                                                'topping' => $toppings->pluck('name')->implode(', ') ?: '-',
                                                'price' => $size ? number_format((float) $size->base_price, 0, ',', '.') : '-',
                                                'filling' => $fillings->pluck('name')->implode(', ') ?: '-',
                                                'status' => $product->is_active && $size && $size->stock > 0 ? 'Tersedia' : 'Habis',
                                                'category' => $category,
                                            ]);
                                        });
                                    @endphp

                                    @foreach ($productRows as $product)
                                        <tr class="product-row transition hover:bg-orange-50/40"
                                            data-category="{{ $product['category'] }}"
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
                                                        onclick="window.location.href='{{ $product['size_id'] ? route('owner.produk.edit', [$product['id'], $product['size_id']]) : route('owner.produk.edit', $product['id']) }}'"
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
                                                    <button type="button" onclick="removeProduct({{ $product['id'] }})"
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

                            <button type="button" onclick="openModal('variant')"
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
                                        $variantRows = $products->flatMap(fn ($product) => $product->variants->map(fn ($variant) => [
                                            'id' => $variant->id,
                                            'product_id' => $product->id,
                                            'type' => $variant->type,
                                            'additional_price' => (int) $variant->additional_price,
                                            'name' => $variant->name,
                                            'category' => $typeLabel[$variant->type] ?? $variant->type,
                                            'price' => 'Rp ' . number_format((float) $variant->additional_price, 0, ',', '.'),
                                            'status' => $variant->is_available ? 'Tersedia' : 'Habis',
                                            'active' => $variant->is_available,
                                        ]));
                                    @endphp

                                    @foreach ($variantRows as $variant)
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
                                                        onchange="toggleVariant({{ $variant['id'] }}, this)"
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
                                                    <button type="button" onclick="editVariant(this)"
                                                        data-id="{{ $variant['id'] }}"
                                                        data-product-id="{{ $variant['product_id'] }}"
                                                        data-type="{{ $variant['type'] }}"
                                                        data-name="{{ $variant['name'] }}"
                                                        data-price="{{ $variant['additional_price'] }}"
                                                        data-active="{{ $variant['active'] ? 1 : 0 }}"
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
                                                    <button type="button" onclick="removeVariant({{ $variant['id'] }})"
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
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH / EDIT -->
    <div id="dataModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4">
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">

            <div class="mb-5 flex items-start justify-between">
                <h3 id="modalTitle" class="text-lg font-bold">Tambah Produk</h3>
                <button type="button" onclick="closeModal()" aria-label="Tutup"
                    class="grid h-8 w-8 place-items-center rounded-lg bg-stone-100 text-stone-500 hover:bg-stone-200">✕</button>
            </div>

            <form id="dataForm" class="space-y-4">
                <input type="hidden" id="mode">

                <div id="productGroup">
                    <label for="productSelect" class="mb-1.5 block text-sm font-medium">Produk</label>
                    <select id="productSelect"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label id="nameLabel" for="name" class="mb-1.5 block text-sm font-medium">Nama Produk</label>
                    <input id="name" required placeholder="Masukkan nama"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                </div>

                <div id="typeGroup">
                    <label for="type" class="mb-1.5 block text-sm font-medium">Kategori</label>
                    <select id="type"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                        <option value="filling">Filling</option>
                        <option value="dipping_sauce">Dip Sauce</option>
                        <option value="seasoning">Seasoning</option>
                    </select>
                </div>

                <div id="sizeGroup">
                    <label for="size" class="mb-1.5 block text-sm font-medium">Ukuran</label>
                    <select id="size"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                        <option value="Regular">Regular</option>
                        <option value="Large">Large</option>
                    </select>
                </div>

                <div>
                    <label id="priceLabel" for="price" class="mb-1.5 block text-sm font-medium">Harga (Rp)</label>
                    <input id="price" type="number" min="0" placeholder="20000"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                </div>

                <div id="stockGroup">
                    <label for="stock" class="mb-1.5 block text-sm font-medium">Stok Awal</label>
                    <input id="stock" type="number" min="0" placeholder="0"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm outline-none focus:border-[#FF8000]">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="rounded-xl border border-stone-200 bg-white px-5 py-3 text-sm font-semibold">Batal</button>
                    <button type="submit"
                        class="rounded-xl bg-[#FF8000] px-5 py-3 text-sm font-semibold text-white hover:bg-orange-600 disabled:opacity-60">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        let FILTER = 'Semua';
        let EDIT_VARIANT_ID = null;

        // Kirim permintaan ke API backend (sudah dibuat di ProductController / VariantController)
        async function api(method, url, body) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            };
            if (body !== undefined) headers['Content-Type'] = 'application/json';

            const res = await fetch(url, {
                method,
                headers,
                body: body === undefined ? undefined : JSON.stringify(body),
            });

            let data = {};
            try { data = await res.json(); } catch (_) {}

            if (!res.ok) {
                if (res.status === 401) throw new Error('Sesi habis, silakan login ulang.');
                throw new Error(data.message || ('Gagal (' + res.status + ')'));
            }
            return data;
        }

        const show = (id, on) => document.getElementById(id).classList.toggle('hidden', !on);

        // ========== FILTER KATEGORI + PENCARIAN ==========
        function applyFilter() {
            const q = document.getElementById('searchInput').value.trim().toLowerCase();

            document.querySelectorAll('.product-row').forEach(tr => {
                const okCat = FILTER === 'Semua' || tr.dataset.category.split('|').includes(FILTER);
                tr.classList.toggle('hidden', !(okCat && tr.dataset.search.includes(q)));
            });

            document.querySelectorAll('.variant-row').forEach(tr => {
                const okCat = FILTER === 'Semua' || FILTER === tr.dataset.category;
                tr.classList.toggle('hidden', !(okCat && tr.dataset.search.includes(q)));
            });
        }

        document.querySelectorAll('.category-btn').forEach(btn => btn.addEventListener('click', () => {
            FILTER = btn.dataset.filter;

            document.querySelectorAll('.category-btn').forEach(b => {
                const on = b === btn;
                b.classList.toggle('bg-[#FF8000]', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('font-semibold', on);
                b.classList.toggle('bg-white', !on);
                b.classList.toggle('border', !on);
                b.classList.toggle('border-stone-200', !on);
                b.classList.toggle('text-stone-600', !on);
            });

            applyFilter();
        }));

        document.getElementById('searchInput').addEventListener('input', applyFilter);

        // ========== MODAL ==========
        function openModal(mode, variant = null) {
            const isVariant = mode === 'variant';
            EDIT_VARIANT_ID = variant ? variant.id : null;

            document.getElementById('mode').value = mode;
            document.getElementById('modalTitle').textContent = isVariant
                ? (variant ? 'Edit Varian' : 'Tambah Varian')
                : 'Tambah Produk';
            document.getElementById('nameLabel').textContent = isVariant ? 'Nama Varian' : 'Nama Produk';
            document.getElementById('priceLabel').textContent = isVariant ? 'Tambahan Harga (Rp)' : 'Harga (Rp)';

            show('productGroup', isVariant && !variant);
            show('typeGroup', isVariant);
            show('sizeGroup', !isVariant);
            show('stockGroup', !isVariant);

            document.getElementById('name').value = variant ? variant.name : '';
            document.getElementById('type').value = variant ? variant.type : 'filling';
            document.getElementById('price').value = variant ? variant.price : '';
            document.getElementById('size').value = 'Regular';
            document.getElementById('stock').value = '';

            const modal = document.getElementById('dataModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('dataModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editVariant(btn) {
            openModal('variant', btn.dataset);
        }

        document.getElementById('dataModal').addEventListener('click', e => {
            if (e.target.id === 'dataModal') closeModal();
        });

        document.getElementById('dataForm').addEventListener('submit', async e => {
            e.preventDefault();
            const btn = e.submitter;
            if (btn) btn.disabled = true;

            try {
                const mode = document.getElementById('mode').value;
                const name = document.getElementById('name').value.trim();
                const price = document.getElementById('price').value;

                if (mode === 'variant') {
                    const body = {
                        type: document.getElementById('type').value,
                        name,
                        additional_price: price === '' ? 0 : Number(price),
                    };

                    if (EDIT_VARIANT_ID) {
                        await api('PUT', `/owner/api/varian/${EDIT_VARIANT_ID}`, body);
                    } else {
                        const pid = document.getElementById('productSelect').value;
                        await api('POST', `/owner/api/produk/${pid}/varian`, { ...body, is_available: true });
                    }
                } else {
                    const res = await api('POST', '/owner/api/produk', { name });
                    const stock = document.getElementById('stock').value;

                    if (price !== '') {
                        await api('POST', `/owner/api/produk/${res.data.id}/ukuran`, {
                            size_name: document.getElementById('size').value,
                            base_price: Number(price),
                            stock: stock === '' ? 0 : Number(stock),
                        });
                    }
                }

                window.location.reload();
            } catch (err) {
                alert(err.message);
                if (btn) btn.disabled = false;
            }
        });

        // ========== AKSI DI TABEL ==========
        async function toggleVariant(id, input) {
            try {
                await api('PATCH', `/owner/api/varian/${id}/status`, { is_available: input.checked ? '1' : '0' });
                window.location.reload();
            } catch (err) {
                input.checked = !input.checked;
                alert(err.message);
            }
        }

        async function removeProduct(id) {
            if (!confirm('Hapus produk ini beserta semua ukuran & variannya?')) return;
            try {
                await api('DELETE', `/owner/api/produk/${id}`);
                window.location.reload();
            } catch (err) { alert(err.message); }
        }

        async function removeVariant(id) {
            if (!confirm('Hapus varian ini?')) return;
            try {
                await api('DELETE', `/owner/api/varian/${id}`);
                window.location.reload();
            } catch (err) { alert(err.message); }
        }

        // ========== SIDEBAR DI HP ==========
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        document.getElementById('menuButton')?.addEventListener('click', () => {
            sidebar?.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
        });

        overlay?.addEventListener('click', () => {
            sidebar?.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
        });
    </script>

</body>

</html>