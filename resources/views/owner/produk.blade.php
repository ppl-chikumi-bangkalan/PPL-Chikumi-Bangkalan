<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Chikumi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>

                    <span>Dashboard</span>
                </a>

                <!-- Kelola Produk -->
                <a href="{{ route('owner.kelola-produk') }}"
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

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" aria-label="Logout"
                        class="rounded-lg p-2 text-red-500 transition hover:bg-orange-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
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

                        <button onclick="openModal('variant')"
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
                <button type="button" onclick="closeModal()" class="text-2xl text-stone-400 hover:text-stone-700">×</button>
            </div>

            <form id="dataForm">
                <input type="hidden" id="mode">

                <div id="productGroup" class="mb-4 hidden">
                    <label for="productSelect" class="mb-2 block text-xs font-semibold">Produk</label>
                    <select id="productSelect"
                        class="form-input w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]"></select>
                </div>

                <div class="mb-4">
                    <label for="name" id="nameLabel" class="mb-2 block text-xs font-semibold">Nama Produk</label>
                    <input id="name" required placeholder="Masukkan nama"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div id="categoryGroup" class="mb-4">
                    <label for="category" class="mb-2 block text-xs font-semibold">Kategori Produk (opsional)</label>
                    <input id="category" placeholder="Contoh: Chikumi Main Menu"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div id="typeGroup" class="mb-4 hidden">
                    <label for="type" class="mb-2 block text-xs font-semibold">Jenis Varian</label>
                    <select id="type"
                        class="form-input w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                        <option value="filling">Filling</option>
                        <option value="dipping_sauce">Dip Sauce</option>
                        <option value="seasoning">Seasoning</option>
                    </select>
                </div>

                <div id="sizeGroup" class="mb-4">
                    <label for="size" class="mb-2 block text-xs font-semibold">Ukuran</label>
                    <select id="size"
                        class="form-input w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                        <option value="Regular">Regular</option>
                        <option value="Large">Large</option>
                    </select>
                </div>

                <div id="priceGroup" class="mb-4">
                    <label for="price" id="priceLabel" class="mb-2 block text-xs font-semibold">Harga (angka)</label>
                    <input id="price" type="number" min="0" placeholder="20000"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div id="stockGroup" class="mb-4">
                    <label for="stock" class="mb-2 block text-xs font-semibold">Stok</label>
                    <input id="stock" type="number" min="0" placeholder="0"
                        class="form-input w-full rounded-lg border border-stone-200 px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                </div>

                <div id="activeGroup" class="mb-4 hidden">
                    <label for="active" class="mb-2 block text-xs font-semibold">Status Varian</label>
                    <select id="active"
                        class="form-input w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-[#FF8000]">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
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

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const TYPE_LABEL = { filling: 'Filling', dipping_sauce: 'Dip Sauce', seasoning: 'Seasoning' };
        let PRODUCTS = [];
        let FILTER = 'all';
        let EDIT = { product: null, size: null, variant: null };

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

        function esc(v) {
            return String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }
        const rupiah = v => 'Rp ' + Number(v).toLocaleString('id-ID');
        const show = (id, on) => document.getElementById(id).classList.toggle('hidden', !on);

        async function load() {
            const res = await api('GET', '/owner/api/produk');
            PRODUCTS = res.data;
            render();
            applyFilter();
        }

        function render() {
            const pBody = document.getElementById('productTable');
            const vBody = document.getElementById('variantTable');

            pBody.innerHTML = PRODUCTS.map(p => {
                const tops = p.variants.map(v => v.name).join(', ');
                const cats = p.variants.map(v => TYPE_LABEL[v.type] || v.type);
                const rows = p.sizes.length ? p.sizes : [null];
                return rows.map(s => {
                    const low = s && Number(s.stock) <= Number(s.min_stock);
                    const name = [p.name, ...p.variants.map(v => v.name)].join(' ').toLowerCase();
                    const cat = ['Ukuran & Harga', ...cats].join('|');
                    return `
                    <tr data-name="${esc(name)}" data-cat="${esc(cat)}">
                        <td class="p-4">
                            <div class="font-semibold">${esc(p.name)}</div>
                            <div class="text-[10px] text-stone-400">${esc(p.category?.name || '-')}</div>
                        </td>
                        <td class="p-4">${tops ? esc(tops) : '-'}</td>
                        <td class="p-4">${s ? rupiah(s.base_price) : '-'}<div class="text-[10px] text-stone-400">${s ? esc(s.size_name) : ''}</div></td>
                        <td class="p-4">${s ? `<span class="${low ? 'font-bold text-red-600' : ''}">${s.stock}</span>` : '-'}</td>
                        <td class="p-4">${p.is_active ? '<span class="text-green-600">Aktif</span>' : '<span class="text-stone-400">Nonaktif</span>'}</td>
                        <td class="p-4 text-center whitespace-nowrap">
                            ${s ? `<button type="button" onclick="adjustStock(${s.id})" class="rounded px-2 py-1 text-[10px] font-semibold text-blue-600 hover:bg-blue-50">Stok</button>` : ''}
                            <button type="button" onclick="editProduct(${p.id}${s ? ',' + s.id : ''})" class="rounded px-2 py-1 text-[10px] font-semibold text-[#FF8000] hover:bg-orange-50">Edit</button>
                            <button type="button" onclick="removeProduct(${p.id})" class="rounded px-2 py-1 text-[10px] font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                        </td>
                    </tr>`;
                }).join('');
            }).join('');

            vBody.innerHTML = PRODUCTS.map(p => {
                const total = p.sizes.reduce((a, s) => a + Number(s.stock), 0);
                return p.variants.map(v => `
                    <tr data-name="${esc((v.name + ' ' + p.name).toLowerCase())}" data-cat="${esc(TYPE_LABEL[v.type] || v.type)}">
                        <td class="p-4">
                            <div class="font-semibold">${esc(v.name)}</div>
                            <div class="text-[10px] text-stone-400">${esc(p.name)}</div>
                        </td>
                        <td class="p-4">${esc(TYPE_LABEL[v.type] || v.type)}</td>
                        <td class="p-4">${total}</td>
                        <td class="p-4">${v.is_available ? '<span class="text-green-600">Aktif</span>' : '<span class="text-stone-400">Nonaktif</span>'}</td>
                        <td class="p-4">
                            <button type="button" onclick="toggleVariant(${v.id}, ${v.is_available ? 1 : 0})"
                                class="rounded-full px-3 py-1 text-[10px] font-semibold ${v.is_available ? 'bg-green-50 text-green-600' : 'bg-stone-100 text-stone-500'}">
                                ${v.is_available ? 'Nonaktifkan' : 'Aktifkan'}
                            </button>
                        </td>
                        <td class="p-4 text-center whitespace-nowrap">
                            <button type="button" onclick="editVariant(${v.id})" class="rounded px-2 py-1 text-[10px] font-semibold text-[#FF8000] hover:bg-orange-50">Edit</button>
                            <button type="button" onclick="removeVariant(${v.id})" class="rounded px-2 py-1 text-[10px] font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                        </td>
                    </tr>`).join('');
            }).join('');
        }

        function matches(tr, q) { return !q || tr.dataset.name.includes(q); }

        function applyFilter() {
            const q = document.getElementById('searchInput').value.trim().toLowerCase();
            let pShown = 0, vShown = 0;

            document.querySelectorAll('#productTable tr').forEach(tr => {
                const ok = matches(tr, q) &&
                    (FILTER === 'all' || FILTER === 'Ukuran & Harga' || tr.dataset.cat.split('|').includes(FILTER));
                tr.classList.toggle('hidden', !ok);
                if (ok) pShown++;
            });
            document.querySelectorAll('#variantTable tr').forEach(tr => {
                const ok = matches(tr, q) && (FILTER === 'all' || tr.dataset.cat === FILTER);
                tr.classList.toggle('hidden', !ok);
                if (ok) vShown++;
            });

            show('productEmpty', pShown === 0);
            show('variantEmpty', vShown === 0);
        }

        function openModal(mode, opts = {}) {
            EDIT = { product: null, size: null, variant: null, ...opts };
            const isVariant = mode === 'variant';
            const creatingVariant = isVariant && !EDIT.variant;

            document.getElementById('mode').value = mode;
            document.getElementById('dataModal').classList.replace('hidden', 'flex');
            document.getElementById('modalTitle').textContent = isVariant
                ? (EDIT.variant ? 'Edit Varian' : 'Tambah Varian')
                : (EDIT.product ? 'Edit Produk' : 'Tambah Produk');
            document.getElementById('nameLabel').textContent = isVariant ? 'Nama Varian' : 'Nama Produk';
            document.getElementById('priceLabel').textContent = isVariant ? 'Harga Tambahan (angka)' : 'Harga (angka)';

            show('productGroup', creatingVariant);
            show('categoryGroup', !isVariant);
            show('typeGroup', isVariant);
            show('sizeGroup', !isVariant);
            show('stockGroup', !isVariant);
            show('activeGroup', isVariant);

            if (creatingVariant) {
                document.getElementById('productSelect').innerHTML =
                    PRODUCTS.map(p => `<option value="${p.id}">${esc(p.name)}</option>`).join('');
            }

            document.getElementById('name').value = isVariant ? (EDIT.variant?.name || '') : (EDIT.product?.name || '');
            document.getElementById('category').value = EDIT.product?.category?.name || '';
            document.getElementById('type').value = EDIT.variant?.type || 'filling';
            document.getElementById('size').value = EDIT.size?.size_name || 'Regular';
            document.getElementById('price').value = isVariant
                ? (EDIT.variant ? Number(EDIT.variant.additional_price) : '')
                : (EDIT.size ? Number(EDIT.size.base_price) : '');
            document.getElementById('stock').value = EDIT.size ? EDIT.size.stock : '';
            document.getElementById('active').value = EDIT.variant && !EDIT.variant.is_available ? '0' : '1';
        }

        function closeModal() {
            document.getElementById('dataModal').classList.replace('flex', 'hidden');
        }

        function editProduct(pid, sid) {
            const p = PRODUCTS.find(x => x.id === pid);
            if (!p) return;
            const s = sid ? p.sizes.find(x => x.id === sid) : (p.sizes.length === 1 ? p.sizes[0] : null);
            openModal('product', { product: p, size: s });
        }

        function editVariant(vid) {
            for (const p of PRODUCTS) {
                const v = p.variants.find(x => x.id === vid);
                if (v) return openModal('variant', { variant: v, product: p });
            }
        }

        async function adjustStock(sizeId) {
            const raw = prompt('Jumlah stok (contoh: 5 untuk masuk, -3 untuk keluar):');
            if (raw === null || raw.trim() === '') return;
            const q = Number(raw);
            if (!Number.isInteger(q) || q === 0) return alert('Masukkan angka bulat selain 0.');
            try {
                await api('PATCH', `/owner/api/ukuran/${sizeId}/stok`, {
                    type: q > 0 ? 'in' : 'out',
                    quantity: Math.abs(q),
                });
                await load();
            } catch (err) { alert(err.message); }
        }

        async function toggleVariant(id, current) {
            try {
                await api('PATCH', `/owner/api/varian/${id}/status`, { is_available: current ? '0' : '1' });
                await load();
            } catch (err) { alert(err.message); }
        }

        async function removeProduct(id) {
            if (!confirm('Hapus produk ini beserta semua ukuran & variannya?')) return;
            try {
                await api('DELETE', `/owner/api/produk/${id}`);
                await load();
            } catch (err) { alert(err.message); }
        }

        async function removeVariant(id) {
            if (!confirm('Hapus varian ini?')) return;
            try {
                await api('DELETE', `/owner/api/varian/${id}`);
                await load();
            } catch (err) { alert(err.message); }
        }

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
                        is_available: document.getElementById('active').value === '1',
                    };
                    if (EDIT.variant) {
                        await api('PUT', `/owner/api/varian/${EDIT.variant.id}`, body);
                    } else {
                        const pid = document.getElementById('productSelect').value;
                        await api('POST', `/owner/api/produk/${pid}/varian`, body);
                    }
                } else {
                    const body = { name };
                    const cat = document.getElementById('category').value.trim();
                    if (cat) body.category_name = cat;
                    const size = document.getElementById('size').value;
                    const stock = document.getElementById('stock').value;
                    const sizeBody = {
                        size_name: size,
                        base_price: price === '' ? 0 : Number(price),
                        stock: stock === '' ? 0 : Number(stock),
                    };

                    if (EDIT.product) {
                        await api('PUT', `/owner/api/produk/${EDIT.product.id}`, body);
                        if (price !== '') {
                            if (EDIT.size) await api('PUT', `/owner/api/ukuran/${EDIT.size.id}`, sizeBody);
                            else await api('POST', `/owner/api/produk/${EDIT.product.id}/ukuran`, sizeBody);
                        }
                    } else {
                        const res = await api('POST', '/owner/api/produk', body);
                        if (price !== '') {
                            await api('POST', `/owner/api/produk/${res.data.id}/ukuran`, sizeBody);
                        }
                    }
                }

                closeModal();
                await load();
            } catch (err) {
                alert(err.message);
            } finally {
                if (btn) btn.disabled = false;
            }
        });

        document.querySelectorAll('.filter-btn').forEach(btn => btn.addEventListener('click', () => {
            FILTER = btn.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(b => {
                const on = b === btn;
                b.classList.toggle('bg-[#FF8000]', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('font-semibold', on);
                b.classList.toggle('bg-white', !on);
                b.classList.toggle('border', !on);
                b.classList.toggle('border-stone-200', !on);
            });
            applyFilter();
        }));

        document.getElementById('searchInput').addEventListener('input', applyFilter);
        document.getElementById('dataModal').addEventListener('click', e => {
            if (e.target.id === 'dataModal') closeModal();
        });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        document.getElementById('menuButton')?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        load().catch(err => alert(err.message));
    </script>
</body>

</html>