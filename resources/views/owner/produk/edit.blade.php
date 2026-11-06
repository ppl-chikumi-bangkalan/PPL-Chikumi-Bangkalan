
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        <a href="{{ $back }}"
                            class="hover:text-[#FF8000]">
                            {{ str_contains($back, 'kelola-produk') ? 'Kelola Produk' : 'Data Produk' }}
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

                <a href="{{ $back }}"
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
                                value="{{ $produk->name }}" required
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
                                <option value="Regular" @selected($ukuran?->size_name === 'Regular')>Reguler (15 cm)</option>
                                <option value="Large" @selected($ukuran?->size_name === 'Large')>Large (25 cm)</option>
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
                                value="{{ $ukuran ? (int) $ukuran->base_price : '' }}" min="0" required
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
                                <option value="tersedia" @selected($produk->is_active)>Tersedia</option>
                                <option value="habis" @selected(! $produk->is_active)>Habis</option>
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
                                class="w-full rounded-lg border border-stone-200 px-4 py-3 text-sm outline-none transition focus:border-[#FF8000] focus:ring-2 focus:ring-orange-100">{{ $produk->description }}</textarea>
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

@foreach (['filling' => 'Filling', 'dipping_sauce' => 'Dip Sauce', 'seasoning' => 'Seasoning'] as $type => $label)
                        <div>
                            <p class="mb-3 text-sm font-semibold text-stone-700">
                                {{ $label }}
                            </p>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                @forelse ($produk->variants->where('type', $type) as $variant)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-[#FF8000]">
                                        <input type="checkbox" name="topping[]" value="{{ $variant->name }}"
                                            data-variant-id="{{ $variant->id }}"
                                            data-available="{{ $variant->is_available ? 1 : 0 }}"
                                            {{ $variant->is_available ? 'checked' : '' }}
                                            class="size-4 accent-[#FF8000]">
                                        <span class="text-sm text-stone-700">{{ $variant->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-stone-400">Belum ada varian.</p>
                                @endforelse

                            </div>
                        </div>

                        @if (! $loop->last)
                            <div class="border-t border-stone-100"></div>
                        @endif
                    @endforeach

                    </div>
                </div>

                <!-- TOMBOL AKSI -->
                <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">

                    <a href="{{ $back }}"
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

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const PRODUCT_ID = {{ $produk->id }};
    const SIZE = @json($ukuran);

    async function api(method, url, body) {
        const res = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: body === undefined ? undefined : JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || `Gagal (${res.status})`);
        return data;
    }

    document.getElementById('editProductForm').addEventListener('submit', async e => {
        e.preventDefault();

        const button = e.submitter;
        if (button) button.disabled = true;

        try {
            const sizeName = document.getElementById('ukuran').value;
            const price = Number(document.getElementById('harga').value || 0);

            await api('PUT', `/owner/api/produk/${PRODUCT_ID}`, {
                name: document.getElementById('nama_produk').value.trim(),
                description: document.getElementById('deskripsi').value.trim(),
                is_active: document.getElementById('status').value === 'tersedia',
            });

            if (SIZE) {
                await api('PUT', `/owner/api/ukuran/${SIZE.id}`, {
                    size_name: sizeName,
                    base_price: price,
                    stock: SIZE.stock,
                    min_stock: SIZE.min_stock,
                });
            } else {
                await api('POST', `/owner/api/produk/${PRODUCT_ID}/ukuran`, {
                    size_name: sizeName,
                    base_price: price,
                    stock: 0,
                    min_stock: 5,
                });
            }

            for (const input of document.querySelectorAll('[data-variant-id]')) {
                const available = input.checked ? '1' : '0';
                if (available !== input.dataset.available) {
                    await api('PATCH', `/owner/api/varian/${input.dataset.variantId}/status`, {
                        is_available: available,
                    });
                }
            }

            window.location.href = '{{ $back }}';
        } catch (err) {
            alert(err.message);
        } finally {
            if (button) button.disabled = false;
        }
    });
</script>
</body>
</html>
