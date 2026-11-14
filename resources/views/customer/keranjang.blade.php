@php
    $apiBase = auth()->check() ? '/customer/api' : '/frontend/customer/api';
    $pageBase = auth()->check() ? '/customer' : '/frontend/customer';
    $devCustomerId = auth()->check() ? null : 3;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">
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

            <h1 class="flex-1 text-lg font-bold">Keranjang</h1>

            <button type="button" id="hapusSemua" class="text-sm font-medium text-[#E03131] hover:underline">
                Hapus semua
            </button>
        </header>

        <div class="mt-4 lg:grid lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start lg:gap-8">

            <!-- DAFTAR ITEM -->
            <div class="space-y-4">
                <div id="daftarItem" class="space-y-4">
                    <p class="rounded-2xl border border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#6B645D]">
                        Memuat keranjang…
                    </p>
                </div>

                <!-- Tambah Chikumi lain -->
                <a href="{{ $pageBase }}/dashboard"
                    class="flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-[#FF8000] bg-[#FFF3E6]/40 py-3 text-sm font-semibold text-[#D96C00] transition hover:bg-[#FFF3E6]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Chikumi lain
                </a>
            </div>

            <!-- RINGKASAN -->
            <section class="mt-4 rounded-2xl border border-[#EDE6DF] bg-white p-[18px] lg:sticky lg:top-6 lg:mt-0">
                <h2 class="text-base font-semibold">Ringkasan</h2>

                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-[#6B645D]">Subtotal (<span id="totalItemTeks">0</span> pcs)</span>
                    <span id="subtotalTeks">Rp 0</span>
                </div>

                <div class="mt-3 flex items-center gap-2.5 rounded-xl bg-[#F7F3EF] px-3 py-2.5 text-xs text-[#6B645D]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-4 w-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    Ongkos kirim dihitung di checkout
                </div>
            </section>
        </div>
    </main>

    <!-- BOTTOM BAR: SUBTOTAL + CHECKOUT -->
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
        <div class="mx-auto flex max-w-md items-center justify-between gap-3 px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">
            <div>
                <p class="text-xs text-[#6B645D]">Subtotal</p>
                <p id="subtotalBarTeks" class="text-2xl font-bold text-[#D96C00]">Rp 0</p>
            </div>

            <a id="btnCheckout" href="{{ $pageBase }}/checkout"
                class="pointer-events-none rounded-xl bg-[#FF8000] px-7 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00] opacity-60">
                Checkout
            </a>
        </div>
    </div>

    <script>
        const API_BASE = @json($apiBase);
        const PAGE_BASE = @json($pageBase);
        const DEV_CUSTOMER = @json($devCustomerId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const daftarItem = document.getElementById('daftarItem');
        const hapusSemua = document.getElementById('hapusSemua');
        const btnCheckout = document.getElementById('btnCheckout');

        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));
        const esc = teks => String(teks ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        async function api(path, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
                ...(DEV_CUSTOMER ? { 'X-Dev-Customer': DEV_CUSTOMER } : {}),
            };

            const response = await fetch(API_BASE + path, { ...options, headers });
            const hasil = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(hasil.message || 'Terjadi kesalahan.');
            return hasil;
        }

        function kartuItem(item) {
            const namaVarian = (item.varian || []).map(v => v.nama).join(' · ');
            const rincian = [item.ukuran, namaVarian].filter(Boolean).join(' · ');
            const gambar = item.gambar ? '/' + item.gambar.replace(/^\/+/, '') : '/images/chikumi-1.png';
            const kurang = Math.max(1, item.quantity - 1);

            return `
                <article class="flex gap-3 rounded-2xl border border-[#EDE6DF] bg-white p-3.5">
                    <div class="grid h-[72px] w-[72px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="${esc(gambar)}" alt="${esc(item.produk)}" class="h-[68px] w-auto object-contain"
                            onerror="this.onerror=null;this.src='/images/chikumi-1.png'">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-base font-semibold">${esc(item.produk)}</p>
                            <button type="button" data-aksi="hapus" aria-label="Hapus item"
                                class="mt-0.5 text-[#E03131] hover:opacity-70">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        <p class="mt-1 truncate text-xs text-[#6B645D]">${esc(rincian)}</p>
                        <a href="${PAGE_BASE}/produk?produk=${item.produk_id}&ukuran=${item.product_size_id}"
                            class="mt-1 inline-block text-xs font-semibold text-[#FF8000] hover:underline">Ubah pilihan</a>

                        <div class="mt-1.5 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" data-aksi="kurang" data-jumlah="${kurang}"
                                    aria-label="Kurangi" ${item.quantity <= 1 ? 'disabled' : ''}
                                    class="grid h-[30px] w-[30px] place-items-center text-[#FF8000] disabled:opacity-40">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg>
                                </button>
                                <span class="w-[30px] text-center text-sm font-semibold">${item.quantity}</span>
                                <button type="button" data-aksi="tambah" data-jumlah="${item.quantity + 1}"
                                    aria-label="Tambah"
                                    class="grid h-[30px] w-[30px] place-items-center text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-base font-bold text-[#D96C00]">${rupiah(item.subtotal)}</p>
                        </div>

                        ${item.stok_cukup ? '' : '<p class="mt-1 text-xs font-medium text-[#E03131]">Stok tidak mencukupi</p>'}
                    </div>
                </article>`;
        }

        function render(data) {
            const item = data.item || [];
            const kosong = item.length === 0;

            daftarItem.innerHTML = kosong
                ? `<p class="rounded-2xl border border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#6B645D]">
                        Keranjang masih kosong. <a href="${PAGE_BASE}/dashboard" class="font-semibold text-[#FF8000] hover:underline">Pilih Chikumi</a>
                   </p>`
                : item.map(kartuItem).join('');

            daftarItem.querySelectorAll('article').forEach((article, index) => {
                article.dataset.id = item[index].id;
            });
            daftarItem.querySelectorAll('[data-aksi]').forEach(tombol => {
                tombol.addEventListener('click', () => {
                    const id = Number(tombol.closest('article').dataset.id);
                    aksiItem(item.find(i => i.id === id), tombol);
                });
            });

            document.getElementById('totalItemTeks').textContent = data.total_item || 0;
            document.getElementById('subtotalTeks').textContent = rupiah(data.subtotal || 0);
            document.getElementById('subtotalBarTeks').textContent = rupiah(data.subtotal || 0);

            hapusSemua.classList.toggle('hidden', kosong);
            btnCheckout.classList.toggle('pointer-events-none', kosong);
            btnCheckout.classList.toggle('opacity-60', kosong);
        }

        async function muat() {
            try {
                const hasil = await api('/keranjang');
                render(hasil.data);
            } catch (error) {
                daftarItem.innerHTML = `<p class="rounded-2xl border border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#E03131]">${esc(error.message)}</p>`;
            }
        }

        async function aksiItem(item, tombol) {
            if (!item) return;
            const aksi = tombol.dataset.aksi;
            tombol.disabled = true;

            try {
                if (aksi === 'hapus') {
                    await api(`/keranjang/${item.id}`, { method: 'DELETE' });
                } else {
                    await api(`/keranjang/${item.id}`, {
                        method: 'PUT',
                        body: JSON.stringify({ quantity: Number(tombol.dataset.jumlah) }),
                    });
                }
                await muat();
            } catch (error) {
                alert(error.message);
                tombol.disabled = false;
            }
        }

        hapusSemua.addEventListener('click', async () => {
            if (!confirm('Hapus semua item dari keranjang?')) return;

            hapusSemua.disabled = true;
            try {
                const hasil = await api('/keranjang');
                await Promise.all((hasil.data.item || []).map(item =>
                    api(`/keranjang/${item.id}`, { method: 'DELETE' })
                ));
                await muat();
            } catch (error) {
                alert(error.message);
            } finally {
                hapusSemua.disabled = false;
            }
        });

        muat();
    </script>
</body>

</html>
