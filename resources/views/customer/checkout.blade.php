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
    <title>Checkout - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!--
        P6 · CHECKOUT (Figma CHIKUMI) — WBS 1.3.11.2
        Data diambil dari backend (lihat docs/API-KATALOG.md):
        - Ringkasan keranjang : GET  /customer/api/keranjang
        - Alamat terbaru      : GET  /customer/api/alamat
        - Kirim pesanan       : POST /customer/api/pesanan/checkout
          name="delivery_method" → "pickup" / "courier", address_id, payment_method → "qris" / "va"
          (payment_method disimpan di orders.payment_type)
        - Ongkir mengikuti backend: flat Rp 5.000 (OrderController::ONGKIR_COURIER).
    -->

    <form class="group">

        <main class="mx-auto max-w-md px-4 pb-32 pt-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">

            <!-- APP BAR -->
            <header class="flex items-center gap-3 py-1.5">
                <a href="{{ $pageBase }}/keranjang" aria-label="Kembali"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>

                <h1 class="flex-1 text-lg font-bold">Checkout</h1>
            </header>

            <!--
                SUSUNAN:
                - HP / tablet : satu kolom, urutan persis Figma P6
                - Desktop     : kiri = cara terima, alamat, pembayaran | kanan = ringkasan (tetap diam)
            -->
            <div class="mt-4 lg:grid lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start lg:gap-8">

                <div class="space-y-4">

                    <!-- CARA MENERIMA PESANAN -->
                    <section class="rounded-2xl border border-[#EDE6DF] bg-white p-[18px]">
                        <h2 class="text-base font-semibold">Cara Menerima Pesanan</h2>

                        <div class="mt-3 space-y-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="delivery_method" value="pickup" id="terima-pickup" class="peer sr-only">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#F7F3EF] peer-checked:bg-[#FF8000] peer-checked:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-[18px] w-[18px]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                </span>
                                <span class="flex-1 peer-checked:text-[#D96C00]">
                                    <span class="block text-sm font-semibold">Pick Up</span>
                                    <span class="block text-xs text-[#6B645D]">Ambil di gerai Pecinan · gratis</span>
                                </span>
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000]"></span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="delivery_method" value="courier" id="terima-delivery" class="peer sr-only" checked>
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#F7F3EF] peer-checked:bg-[#FF8000] peer-checked:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-[18px] w-[18px]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </span>
                                <span class="flex-1 peer-checked:text-[#D96C00]">
                                    <span class="block text-sm font-semibold">Delivery</span>
                                    <span class="block text-xs text-[#6B645D]">Diantar kurir · ongkir sesuai area</span>
                                </span>
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000]"></span>
                            </label>
                        </div>
                    </section>

                    <!-- ALAMAT PENGANTARAN (tampil kalau Delivery dipilih) -->
                    <section class="rounded-2xl border border-[#EDE6DF] bg-white p-[18px] group-has-[#terima-pickup:checked]:hidden">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold">Alamat Pengantaran</h2>
                            <a id="ubahAlamat" href="{{ $pageBase }}/alamat#dari-checkout" class="text-sm font-semibold text-[#FF8000] hover:underline">Ganti</a>
                        </div>

                        <div id="alamatTerisi" class="hidden">
                            <div class="mt-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                    stroke="currentColor" class="h-4 w-4 text-[#FF8000]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <p id="labelAlamat" class="text-sm font-semibold">Rumah</p>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-medium text-[#D96C00]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#D96C00]"></span> Utama
                                </span>
                            </div>

                            <p id="penerimaAlamat" class="mt-3 text-xs"></p>
                            <p id="detailAlamat" class="mt-3 text-xs text-[#6B645D]"></p>

                            <div class="mt-3 flex items-center justify-between gap-3 rounded-xl bg-[#FFF3E6] px-3 py-3 text-xs">
                                <span id="kotaAlamat" class="flex items-center gap-1.5 text-[#D96C00]"></span>
                                <span class="font-semibold text-[#D96C00]">Ongkir <span id="ongkirAlamat"></span></span>
                            </div>
                        </div>

                        <div id="alamatKosong" class="mt-3 rounded-xl bg-[#FFF3E6] px-3 py-3 text-xs text-[#D96C00]">
                            Belum ada alamat pengiriman. <a href="{{ $pageBase }}/alamat" class="font-semibold underline">Tambah alamat</a>.
                        </div>
                    </section>

                    <!-- PEMBAYARAN -->
                    <section class="rounded-2xl border border-[#EDE6DF] bg-white p-[18px]">
                        <h2 class="text-base font-semibold">Pembayaran</h2>

                        <div class="mt-3 space-y-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="payment_method" value="qris" class="peer sr-only" checked>
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#F7F3EF] peer-checked:bg-[#FF8000] peer-checked:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-[18px] w-[18px]">
                                        <rect x="3.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                                        <rect x="13.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                                        <rect x="3.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
                                        <rect x="13.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
                                    </svg>
                                </span>
                                <span class="flex-1 peer-checked:text-[#D96C00]">
                                    <span class="block text-sm font-semibold">QRIS</span>
                                    <span class="block text-xs text-[#6B645D]">Semua e-wallet &amp; m-banking</span>
                                </span>
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000]"></span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="payment_method" value="va" class="peer sr-only">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#F7F3EF] peer-checked:bg-[#FF8000] peer-checked:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-[18px] w-[18px]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                                        <path stroke-linecap="round" d="M9 8.25h6M9 11.25h6M9 14.25h3.75" />
                                    </svg>
                                </span>
                                <span class="flex-1 peer-checked:text-[#D96C00]">
                                    <span class="block text-sm font-semibold">Virtual Account</span>
                                    <span class="block text-xs text-[#6B645D]">BCA, BRI, BNI, Mandiri</span>
                                </span>
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000]"></span>
                            </label>
                        </div>
                    </section>
                </div>

                <!-- RINGKASAN PEMBAYARAN -->
                <section class="mt-4 rounded-2xl border border-[#EDE6DF] bg-white p-[18px] lg:sticky lg:top-6 lg:mt-0">
                    <h2 class="text-base font-semibold">Ringkasan Pembayaran</h2>

                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span class="text-[#6B645D]">Subtotal (<span id="totalItemTeks">0</span> pcs)</span>
                        <span id="subtotalTeks">Rp 0</span>
                    </div>

                    <!-- Ongkos kirim hanya untuk Delivery -->
                    <div class="mt-3 flex items-center justify-between text-sm group-has-[#terima-pickup:checked]:hidden">
                        <span class="text-[#6B645D]">Ongkos kirim</span>
                        <span id="ongkirTeks">Rp 0</span>
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-[#EDE6DF] pt-3">
                        <span class="text-base font-bold">Total Bayar</span>
                        <span id="totalTeks" class="text-xl font-bold text-[#D96C00]">Rp 0</span>
                    </div>
                </section>
            </div>
        </main>

        <!-- BOTTOM BAR: TOTAL + BAYAR -->
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
            <div class="mx-auto flex max-w-md items-center justify-between gap-3 px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">
                <div>
                    <p class="text-xs text-[#6B645D]">Total</p>
                    <p id="totalBarTeks" class="text-2xl font-bold text-[#D96C00]">Rp 0</p>
                </div>

                <button type="button" id="btnBayar"
                    class="rounded-xl bg-[#FF8000] px-7 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00] disabled:cursor-not-allowed disabled:opacity-60">
                    Bayar Sekarang
                </button>
            </div>
        </div>

    </form>

    <script>
        const API_BASE = @json($apiBase);
        const PAGE_BASE = @json($pageBase);
        const DEV_CUSTOMER = @json($devCustomerId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        // Samakan dengan OrderController::ONGKIR_COURIER di backend.
        const ONGKIR = 5000;

        const btnBayar = document.getElementById('btnBayar');
        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));

        let keranjang = { total_item: 0, subtotal: 0, item: [] };
        let alamat = null;

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

        const metodeKirim = () =>
            document.querySelector('input[name="delivery_method"]:checked')?.value || 'courier';

        function renderAlamat() {
            const terisi = document.getElementById('alamatTerisi');
            const kosong = document.getElementById('alamatKosong');
            const ubah = document.getElementById('ubahAlamat');

            terisi.classList.toggle('hidden', !alamat);
            kosong.classList.toggle('hidden', !!alamat);
            ubah.textContent = alamat ? 'Ganti' : 'Tambah';

            if (!alamat) return;

            document.getElementById('labelAlamat').textContent = alamat.label || 'Alamat';
            document.getElementById('penerimaAlamat').textContent = `${alamat.penerima} · ${alamat.telepon}`;
            document.getElementById('detailAlamat').textContent = [alamat.detail, alamat.kota, alamat.kode_pos]
                .filter(Boolean).join(', ');
            document.getElementById('kotaAlamat').textContent = `Area ${alamat.kota}`;
            document.getElementById('ongkirAlamat').textContent = rupiah(ONGKIR);
        }

        function renderRingkasan() {
            const subtotal = Number(keranjang.subtotal || 0);
            const ongkir = metodeKirim() === 'courier' ? ONGKIR : 0;
            const total = subtotal + ongkir;

            document.getElementById('totalItemTeks').textContent = keranjang.total_item || 0;
            document.getElementById('subtotalTeks').textContent = rupiah(subtotal);
            document.getElementById('ongkirTeks').textContent = rupiah(ONGKIR);
            document.getElementById('totalTeks').textContent = rupiah(total);
            document.getElementById('totalBarTeks').textContent = rupiah(total);

            btnBayar.disabled = !(keranjang.item || []).length;
        }

        document.querySelectorAll('input[name="delivery_method"]').forEach(input => {
            input.addEventListener('change', renderRingkasan);
        });

        btnBayar.addEventListener('click', async () => {
            const metode = metodeKirim();
            if (!(keranjang.item || []).length) return alert('Keranjang masih kosong.');
            if (metode === 'courier' && !alamat) return alert('Simpan alamat pengiriman dulu.');

            const pembayaran = document.querySelector('input[name="payment_method"]:checked')?.value || 'qris';

            btnBayar.disabled = true;
            try {
                const hasil = await api('/pesanan/checkout', {
                    method: 'POST',
                    body: JSON.stringify({
                        delivery_method: metode,
                        payment_method: pembayaran,
                        address_id: metode === 'courier' ? alamat?.id : null,
                    }),
                });

                alert(`Pesanan ${hasil.data.nomor} berhasil dibuat. Total ${rupiah(hasil.data.total)}.`);
                location.href = PAGE_BASE + '/dashboard';
            } catch (error) {
                alert(error.message);
                btnBayar.disabled = false;
            }
        });

        (async () => {
            try {
                const [isiKeranjang, daftarAlamat] = await Promise.all([
                    api('/keranjang'),
                    api('/alamat'),
                ]);

                keranjang = isiKeranjang.data;
                alamat = daftarAlamat.data.alamat?.[0] ?? null;

                renderAlamat();
                renderRingkasan();
            } catch (error) {
                alert(error.message);
            }
        })();
    </script>
</body>

</html>