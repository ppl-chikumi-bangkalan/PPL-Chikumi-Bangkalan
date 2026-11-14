@php
    $apiBase = auth()->check() ? '/customer/api' : '/frontend/customer/api';
    $pageBase = auth()->check() ? '/customer' : '/frontend/customer';
    $devCustomerId = auth()->check() ? null : 3;
    $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');

    $sizes = $produk?->sizes ?? collect();
    $requestedSize = (int) request('ukuran');
    $defaultSize = ($requestedSize ? $sizes->firstWhere('id', $requestedSize) : null)
        ?? $sizes->firstWhere(fn ($size) => $size->stock > 0)
        ?? $sizes->first();
    $variantsByType = $produk?->variants->groupBy('type') ?? collect();

    $variantGroups = [
        'filling' => ['label' => 'Creamy Filling', 'rule' => 'wajib pilih 1'],
        'dipping_sauce' => ['label' => 'Dip Sauce', 'rule' => 'pilih 1'],
        'seasoning' => ['label' => 'Seasoning', 'rule' => 'boleh lebih dari 1'],
    ];

    $gambar = $produk?->image_url && file_exists(public_path($produk->image_url))
        ? asset($produk->image_url)
        : asset('images/chikumi-1.png');
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur {{ $produk?->name ?? 'Chikumi' }} - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">
    <form id="formProduk" class="group">

        <main class="mx-auto max-w-md px-4 pb-32 pt-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">

            <!-- APP BAR -->
            <header class="flex items-center gap-3 py-1.5">
                <a href="{{ $pageBase }}/dashboard" aria-label="Kembali"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>

                <h1 class="flex-1 text-lg font-bold">Atur {{ $produk?->name ?? 'Chikumi' }}</h1>

                <a href="{{ $pageBase }}/keranjang" aria-label="Keranjang"
                    class="relative grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </a>
            </header>

            @if (! $produk)
                <div class="mt-10 rounded-2xl border border-[#EDE6DF] bg-white p-8 text-center">
                    <p class="text-base font-semibold">Produk belum tersedia</p>
                    <p class="mt-1 text-sm text-[#6B645D]">Silakan kembali ke beranda dan pilih menu lain.</p>
                </div>
            @else
                <div class="mt-4 lg:grid lg:grid-cols-2 lg:items-start lg:gap-10">

                    <!-- FOTO -->
                    <div class="grid h-[180px] place-items-center overflow-hidden rounded-2xl bg-[#FF8000] sm:h-60 lg:sticky lg:top-6 lg:h-[480px]">
                        <img src="{{ $gambar }}" alt="{{ $produk->name }}"
                            class="h-[170px] w-auto object-contain sm:h-56 lg:h-[440px]">
                    </div>

                    <!-- PILIHAN -->
                    <div class="mt-4 space-y-6 lg:mt-0">

                        <!-- 1. PILIH UKURAN -->
                        <section>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold">1. Pilih Ukuran</h2>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2.5">
                                @forelse ($sizes as $size)
                                    @php $tersedia = $size->stock > 0; @endphp
                                    <label class="cursor-pointer rounded-2xl border-2 border-transparent bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000] has-disabled:cursor-not-allowed has-disabled:bg-white/60 has-disabled:hover:border-transparent">
                                        <input type="radio" name="ukuran" value="{{ $size->id }}"
                                            data-harga="{{ (float) $size->base_price }}"
                                            data-nama="{{ $size->size_name === 'Regular' ? 'Reguler' : $size->size_name }}"
                                            class="peer sr-only"
                                            @checked($defaultSize?->id === $size->id)
                                            @disabled(! $tersedia)>
                                        <p class="text-base font-semibold peer-checked:text-[#D96C00] peer-disabled:text-[#A39B93]">
                                            {{ $size->size_name === 'Regular' ? 'Reguler' : $size->size_name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-[#6B645D] peer-disabled:text-[#A39B93]">
                                            {{ $size->size_name === 'Regular' ? '16 cm' : '25 cm' }} ·
                                            {{ $tersedia ? $rupiah($size->base_price) : 'habis' }}
                                        </p>
                                    </label>
                                @empty
                                    <p class="text-sm text-[#6B645D]">Belum ada pilihan ukuran.</p>
                                @endforelse
                            </div>
                        </section>

                        <!-- 2. VARIAN (FILLING / DIP SAUCE / SEASONING) -->
                        @foreach ($variantGroups as $type => $meta)
                            @php $options = $variantsByType->get($type, collect()); @endphp

                            @if ($options->isNotEmpty())
                                <section>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-bold">{{ $meta['label'] }}</h2>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> {{ $meta['rule'] }}
                                        </span>
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ($options as $variant)
                                            @if ($variant->is_available)
                                                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                                    <input type="{{ $type === 'seasoning' ? 'checkbox' : 'radio' }}"
                                                        name="{{ $type }}{{ $type === 'seasoning' ? '[]' : '' }}"
                                                        value="{{ $variant->id }}"
                                                        data-variant-id="{{ $variant->id }}"
                                                        data-harga="{{ (float) $variant->additional_price }}"
                                                        data-nama="{{ $variant->name }}"
                                                        class="sr-only">
                                                    {{ $variant->name }}
                                                    @if ((float) $variant->additional_price > 0)
                                                        <span class="ml-1 text-xs opacity-80">+{{ $rupiah($variant->additional_price) }}</span>
                                                    @endif
                                                </label>
                                            @else
                                                <span class="cursor-not-allowed rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-5 py-2.5 text-sm text-[#A39B93]/70">
                                                    {{ $variant->name }} · habis
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        @endforeach

                        <!-- CATATAN -->
                        <section>
                            <label for="catatan" class="text-sm font-medium">Catatan (opsional)</label>

                            <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                    stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                                <input id="catatan" name="catatan" type="text" maxlength="100" placeholder="Contoh: seasoning dipisah"
                                    class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                            </div>
                        </section>

                        <!-- JUMLAH -->
                        <section class="flex items-center justify-between">
                            <label for="jumlah" class="text-base font-bold">Jumlah</label>

                            <input id="jumlah" name="jumlah" type="number" min="1" max="99" value="1"
                                class="w-20 rounded-xl border border-[#EDE6DF] bg-white px-3 py-2 text-center text-sm font-semibold outline-none focus:border-[#FF8000]">
                        </section>
                    </div>
                </div>
            @endif
        </main>

        <!-- BOTTOM BAR: HARGA + KERANJANG -->
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
            <div class="mx-auto flex max-w-md items-center justify-between gap-3 px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">
                <div>
                    <p class="text-xs text-[#6B645D]">Harga per porsi</p>
                    <p id="hargaSatuan" class="text-2xl font-bold text-[#D96C00]">
                        {{ $rupiah($defaultSize?->base_price ?? 0) }}
                    </p>
                    <p id="hargaRincian" class="text-[11px] text-[#6B645D]"></p>
                </div>

                <button type="submit" id="btnKeranjang" @disabled(! $produk)
                    class="rounded-xl bg-[#FF8000] px-6 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00] disabled:cursor-not-allowed disabled:opacity-60 min-[380px]:px-7">
                    + Keranjang
                </button>
            </div>
        </div>

    </form>

    @if ($produk)
        <script>
            const API_BASE = @json($apiBase);
            const DEV_CUSTOMER = @json($devCustomerId);
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const form = document.getElementById('formProduk');
            const priceEl = document.getElementById('hargaSatuan');
            const detailEl = document.getElementById('hargaRincian');
            const qtyEl = document.getElementById('jumlah');

            const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));

            const sizeTerpilih = () => document.querySelector('input[name="ukuran"]:checked');
            const varianTerpilih = () => [...document.querySelectorAll('[data-variant-id]')].filter(input => input.checked);

            function perbaruiHarga() {
                const ukuran = sizeTerpilih();
                const varian = varianTerpilih();

                const hargaDasar = ukuran ? Number(ukuran.dataset.harga) : 0;
                const tambahan = varian.reduce((total, input) => total + Number(input.dataset.harga), 0);

                priceEl.textContent = rupiah(hargaDasar + tambahan);
                detailEl.textContent = [
                    ukuran ? ukuran.dataset.nama : 'Pilih ukuran',
                    ...varian.map(input => input.dataset.nama),
                ].filter(Boolean).join(' · ');
            }

            document.querySelectorAll('input[name="ukuran"], [data-variant-id], #jumlah').forEach(input => {
                input.addEventListener('input', perbaruiHarga);
                input.addEventListener('change', perbaruiHarga);
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();

                const ukuran = sizeTerpilih();
                if (!ukuran) {
                    alert('Pilih ukuran dulu.');
                    return;
                }

                const filling = document.querySelector('input[name="filling"]');
                if (filling && !document.querySelector('input[name="filling"]:checked')) {
                    alert('Pilih filling dulu.');
                    return;
                }

                const tombol = document.getElementById('btnKeranjang');
                tombol.disabled = true;

                try {
                    const headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    };
                    if (CSRF) headers['X-CSRF-TOKEN'] = CSRF;
                    if (DEV_CUSTOMER) headers['X-Dev-Customer'] = DEV_CUSTOMER;

                    const response = await fetch(`${API_BASE}/keranjang`, {
                        method: 'POST',
                        headers,
                        body: JSON.stringify({
                            product_size_id: Number(ukuran.value),
                            quantity: Math.max(1, parseInt(qtyEl.value || '1', 10)),
                            variant_ids: varianTerpilih().map(input => Number(input.dataset.variantId)),
                        }),
                    });

                    const hasil = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(hasil.message || 'Gagal menambah ke keranjang.');
                    }

                    alert(hasil.message || 'Berhasil menambah ke keranjang.');
                } catch (error) {
                    alert(error.message);
                } finally {
                    tombol.disabled = false;
                }
            });

            perbaruiHarga();
        </script>
    @endif
</body>

</html>
