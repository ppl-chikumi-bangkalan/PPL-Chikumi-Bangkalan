@php
    // Halaman preview (/frontend/...) memakai API dev tanpa login
    $modePreview = request()->is('frontend/*');
    $linkKembali = $modePreview ? url('/frontend/customer/dashboard') : route('customer.dashboard');
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur Chikumi - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <main class="mx-auto max-w-md px-4 pb-32 pt-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <a href="{{ $linkKembali }}" aria-label="Kembali"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>

            <h1 class="flex-1 text-lg font-bold">Atur Chikumi</h1>

            <a href="{{ Route::has('customer.keranjang') ? route('customer.keranjang') : '#' }}" aria-label="Keranjang"
                class="relative grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>

                <span id="badgeKeranjang"
                    class="absolute -right-2 -top-2 hidden h-[18px] min-w-[18px] place-items-center rounded-full bg-[#FF8000] px-1 text-[10px] font-semibold text-white">
                </span>
            </a>
        </header>

        <!-- MEMUAT / GAGAL MEMUAT -->
        <div id="statusMuat" class="mt-16 text-center text-sm text-[#6B645D]">Memuat menu...</div>

        <!--
            SUSUNAN:
            - HP / tablet : satu kolom, urutan persis Figma P4
            - Desktop     : kiri = foto (tetap diam), kanan = pilihan
        -->
        <div id="konten" class="mt-4 hidden lg:grid lg:grid-cols-2 lg:items-start lg:gap-10">

            <!-- FOTO -->
            <div class="grid h-[180px] place-items-center overflow-hidden rounded-2xl bg-[#FF8000] sm:h-60 lg:sticky lg:top-6 lg:h-[480px]">
                <img src="{{ asset('images/chikumi-1.png') }}" alt="Chikumi"
                    class="h-[170px] w-auto object-contain sm:h-56 lg:h-[440px]">
            </div>

            <!-- PILIHAN -->
            <div class="mt-4 space-y-6 lg:mt-0">

                <!-- 1. PILIH UKURAN (Reguler / Large) -->
                <section>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold">1. Pilih Ukuran</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1
                        </span>
                    </div>

                    <div id="daftarUkuran" class="mt-4 grid grid-cols-2 gap-2.5"></div>
                </section>

                <!-- 2. CREAMY FILLING -->
                <section>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold">2. Creamy Filling</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> wajib pilih 1
                        </span>
                    </div>

                    <div id="daftarFilling" class="mt-4 flex flex-wrap gap-2"></div>
                </section>

                <!-- 3. JENIS TOPPING (menentukan harga) -->
                <section>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold">3. Jenis Topping</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih salah satu
                        </span>
                    </div>

                    <div class="mt-4 space-y-4">
                        <button type="button" data-topping="seasoning"
                            class="pilihan-topping flex w-full items-center gap-3.5 rounded-2xl border-2 border-transparent bg-white p-4 text-left outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-transparent">
                            <span class="radio grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 border-[#A39B93]"></span>
                            <span class="flex-1">
                                <span class="block text-base font-semibold">Seasoning Powder</span>
                                <span class="block text-xs text-[#6B645D]">Pilih 1 seasoning</span>
                            </span>
                            <span id="hargaSeasoning" class="text-sm font-semibold text-[#D96C00]"></span>
                        </button>

                        <button type="button" data-topping="dip"
                            class="pilihan-topping flex w-full items-center gap-3.5 rounded-2xl border-2 border-transparent bg-white p-4 text-left outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-transparent">
                            <span class="radio grid h-5 w-5 shrink-0 place-items-center rounded-full border-2 border-[#A39B93]"></span>
                            <span class="flex-1">
                                <span class="block text-base font-semibold">Dip Sauce</span>
                                <span class="block text-xs text-[#6B645D]">Pilih 1 saus</span>
                            </span>
                            <span id="hargaDip" class="text-sm font-semibold text-[#D96C00]"></span>
                        </button>
                    </div>
                </section>

                <!-- 4a. PILIH SEASONING (muncul kalau topping = Seasoning Powder) -->
                <section id="bagianSeasoning">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold">4. Pilih Seasoning</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1
                        </span>
                    </div>

                    <div id="daftarSeasoning" class="mt-4 flex flex-wrap gap-2"></div>
                </section>

                <!-- 4b. PILIH DIP SAUCE (muncul kalau topping = Dip Sauce) -->
                <section id="bagianDip" class="hidden">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold">4. Pilih Dip Sauce</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1 saus
                        </span>
                    </div>

                    <div id="daftarDip" class="mt-4 flex flex-wrap gap-2"></div>
                </section>

                <!-- CATATAN -->
                <section>
                    <label for="catatan" class="text-sm font-medium">Catatan (opsional)</label>

                    <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                            stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                        </svg>
                        <input id="catatan" type="text" maxlength="100" placeholder="Contoh: seasoning dipisah"
                            class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                    </div>
                </section>

                <!-- JUMLAH -->
                <section class="flex items-center justify-between">
                    <p class="text-base font-bold">Jumlah</p>

                    <div class="flex items-center rounded-xl border border-[#EDE6DF] bg-white p-0.5">
                        <button type="button" id="btnKurang" aria-label="Kurangi"
                            class="grid h-8 w-8 place-items-center text-[#FF8000] disabled:text-[#A39B93]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                            </svg>
                        </button>

                        <span id="jumlah" class="w-8 text-center text-sm font-semibold">1</span>

                        <button type="button" id="btnTambah" aria-label="Tambah"
                            class="grid h-8 w-8 place-items-center text-[#FF8000] disabled:text-[#A39B93]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div>
                </section>

                <p id="pesanError" class="hidden rounded-xl bg-[#FDECEC] px-4 py-3 text-sm text-[#E03131]"></p>
            </div>
        </div>
    </main>

    <!-- BOTTOM BAR: TOTAL + KERANJANG -->
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
        <div class="mx-auto flex max-w-md items-center justify-between gap-3 px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">
            <div>
                <p id="rincianHarga" class="text-xs text-[#6B645D]">&nbsp;</p>
                <p id="totalHarga" class="text-2xl font-bold text-[#D96C00]">Rp 0</p>
            </div>

            <button type="button" id="btnKeranjang" disabled
                class="rounded-xl bg-[#FF8000] px-6 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00] disabled:cursor-not-allowed disabled:opacity-60 min-[380px]:px-7">
                + Keranjang
            </button>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    <div id="toast"
        class="pointer-events-none fixed inset-x-0 bottom-28 z-50 mx-auto hidden w-fit rounded-full bg-[#1E1B18] px-5 py-3 text-sm text-white shadow-lg">
    </div>

    <script>
        // ============ KONEKSI API (lihat docs/API-KATALOG.md) ============
        const MODE_PREVIEW = @json($modePreview);
        const API = MODE_PREVIEW ? '/frontend/customer/api' : '/customer/api';
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        async function api(method, path, body) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            };
            if (MODE_PREVIEW) headers['X-Dev-Customer'] = '3'; // akun "Pelanggan Contoh" dari seeder
            if (body !== undefined) headers['Content-Type'] = 'application/json';

            const res = await fetch(API + path, {
                method,
                headers,
                body: body === undefined ? undefined : JSON.stringify(body),
            });

            let data = {};
            try { data = await res.json(); } catch (_) {}

            if (!res.ok) {
                if (res.status === 401 || res.status === 419) throw new Error('Sesi habis, silakan login ulang.');
                throw new Error(data.message || `Gagal (${res.status})`);
            }
            return data;
        }

        // Info tampilan yang belum ada di database
        const LABEL_UKURAN = { Regular: 'Reguler', Large: 'Large' };
        const PANJANG_UKURAN = { Regular: '16 cm', Large: '25 cm' };

        const formatRupiah = angka => 'Rp ' + Math.round(angka).toLocaleString('id-ID');

        // ============ DATA & PILIHAN ============
        let produk = null;
        const varian = { filling: [], seasoning: [], dip: [] };

        const pilihan = {
            ukuranId: null,
            filling: null,
            topping: 'seasoning',
            seasoning: null,
            dip: null,
            jumlah: 1,
        };

        const cariVarian = (grup, id) => varian[grup].find(v => v.id === id);
        const ukuranTerpilih = () => produk.ukuran.find(u => u.id === pilihan.ukuranId);

        // ============ MEMBUAT TOMBOL PILIHAN ============
        function buatChip(grup, item) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chip rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition';
            btn.dataset.group = grup;
            btn.dataset.id = item.id;
            btn.textContent = item.tambahan_harga > 0
                ? `${item.nama} (+${formatRupiah(item.tambahan_harga)})`
                : item.nama;

            btn.addEventListener('click', () => {
                pilihan[grup] = item.id;
                render();
            });

            return btn;
        }

        // Kartu ukuran: "Chikumi Reguler · 16 cm" (harga ditentukan di Jenis Topping)
        function buatKartuUkuran(ukuran) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.disabled = !ukuran.tersedia;
            btn.dataset.ukuran = ukuran.id;
            btn.className = 'pilihan-ukuran rounded-2xl border-2 border-transparent bg-white p-4 text-left outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000] disabled:cursor-not-allowed disabled:bg-white/60 disabled:hover:border-transparent';

            const judul = document.createElement('p');
            judul.className = 'judul text-base font-semibold' + (ukuran.tersedia ? '' : ' text-[#A39B93]');
            judul.textContent = LABEL_UKURAN[ukuran.nama] ?? ukuran.nama;

            const ket = document.createElement('p');
            ket.className = 'mt-0.5 text-xs ' + (ukuran.tersedia ? 'text-[#6B645D]' : 'text-[#A39B93]');
            ket.textContent = [PANJANG_UKURAN[ukuran.nama], ukuran.tersedia ? null : 'habis'].filter(Boolean).join(' · ');

            btn.append(judul, ket);
            btn.addEventListener('click', () => {
                pilihan.ukuranId = ukuran.id;
                render();
            });

            return btn;
        }

        function tampilkanProduk() {
            const tipe = { filling: 'filling', dipping_sauce: 'dip', seasoning: 'seasoning' };
            produk.varian.forEach(grup => {
                if (tipe[grup.type]) varian[tipe[grup.type]] = grup.pilihan;
            });

            document.getElementById('daftarUkuran').replaceChildren(...produk.ukuran.map(buatKartuUkuran));
            document.getElementById('daftarFilling').replaceChildren(...varian.filling.map(v => buatChip('filling', v)));
            document.getElementById('daftarSeasoning').replaceChildren(...varian.seasoning.map(v => buatChip('seasoning', v)));
            document.getElementById('daftarDip').replaceChildren(...varian.dip.map(v => buatChip('dip', v)));

            // Pilihan awal dari kartu Beranda (?ukuran=Reguler&topping=dip)
            const params = new URLSearchParams(location.search);
            const mintaUkuran = params.get('ukuran');
            const mintaTopping = params.get('topping');

            // Ukuran: dari Beranda, kalau habis pakai yang tersedia
            const tersedia = produk.ukuran.filter(u => u.tersedia);
            const ukuranAwal = tersedia.find(u => u.nama === mintaUkuran || LABEL_UKURAN[u.nama] === mintaUkuran) ?? tersedia[0];
            pilihan.ukuranId = ukuranAwal?.id ?? null;

            // Jenis topping: yang tidak punya pilihan dinonaktifkan
            document.querySelector('[data-topping=seasoning]').disabled = varian.seasoning.length === 0;
            document.querySelector('[data-topping=dip]').disabled = varian.dip.length === 0;

            if (mintaTopping === 'dip' && varian.dip.length) pilihan.topping = 'dip';
            else if (varian.seasoning.length) pilihan.topping = 'seasoning';
            else if (varian.dip.length) pilihan.topping = 'dip';

            document.getElementById('statusMuat').classList.add('hidden');
            document.getElementById('konten').classList.remove('hidden');
            document.getElementById('btnKeranjang').disabled = !pilihan.ukuranId;

            render();
        }

        // ============ HARGA ============
        // harga = harga dasar ukuran + tambahan harga varian yang dipilih
        function hargaTopping(grup) {
            const dipilih = cariVarian(grup, pilihan[grup]);
            const termurah = Math.min(...varian[grup].map(v => v.tambahan_harga));
            return dipilih ? dipilih.tambahan_harga : (isFinite(termurah) ? termurah : 0);
        }

        // Harga 1 kartu (ukuran + topping), ikut menghitung filling yang sudah dipilih
        function hargaKartu(ukuran, topping) {
            const filling = cariVarian('filling', pilihan.filling)?.tambahan_harga ?? 0;
            return ukuran.harga_dasar + filling + hargaTopping(topping);
        }

        function hargaSatuan() {
            const ukuran = ukuranTerpilih();
            return ukuran ? hargaKartu(ukuran, pilihan.topping) : 0;
        }

        // ============ TAMPILAN TERPILIH ============
        function gayaChip(btn, aktif) {
            btn.classList.toggle('bg-[#FF8000]', aktif);
            btn.classList.toggle('border-[#FF8000]', aktif);
            btn.classList.toggle('text-white', aktif);
            btn.classList.toggle('font-semibold', aktif);
            btn.classList.toggle('bg-white', !aktif);
            btn.classList.toggle('border-[#EDE6DF]', !aktif);
        }

        // Kartu (ukuran & jenis topping): polos, bergaris oranye kalau dipilih
        function gayaKartu(btn, aktif) {
            btn.classList.toggle('border-[#FF8000]', aktif);
            btn.classList.toggle('bg-[#FFF3E6]', aktif);
            btn.classList.toggle('border-transparent', !aktif);
            btn.classList.toggle('bg-white', !aktif);
        }

        function render() {
            // Kartu ukuran: yang dipilih bergaris oranye
            document.querySelectorAll('.pilihan-ukuran:not([disabled])').forEach(btn => {
                const aktif = Number(btn.dataset.ukuran) === pilihan.ukuranId;
                gayaKartu(btn, aktif);
                btn.querySelector('.judul').classList.toggle('text-[#D96C00]', aktif);
            });

            document.querySelectorAll('.chip').forEach(btn => {
                gayaChip(btn, pilihan[btn.dataset.group] === Number(btn.dataset.id));
            });

            // Jenis topping (radio) + harga sesuai ukuran yang dipilih
            document.querySelectorAll('.pilihan-topping').forEach(btn => {
                const aktif = btn.dataset.topping === pilihan.topping;
                gayaKartu(btn, aktif);

                const radio = btn.querySelector('.radio');
                radio.classList.toggle('border-[#FF8000]', aktif);
                radio.classList.toggle('bg-[#FF8000]', aktif);
                radio.classList.toggle('border-[#A39B93]', !aktif);
                radio.innerHTML = aktif ? '<span class="h-2 w-2 rounded-full bg-white"></span>' : '';
            });

            const ukuran = ukuranTerpilih();
            document.getElementById('hargaSeasoning').textContent = ukuran && varian.seasoning.length ? formatRupiah(hargaKartu(ukuran, 'seasoning')) : '';
            document.getElementById('hargaDip').textContent = ukuran && varian.dip.length ? formatRupiah(hargaKartu(ukuran, 'dip')) : '';

            document.getElementById('bagianSeasoning').classList.toggle('hidden', pilihan.topping !== 'seasoning');
            document.getElementById('bagianDip').classList.toggle('hidden', pilihan.topping !== 'dip');

            // Harga dinamis
            document.getElementById('rincianHarga').textContent = `${pilihan.jumlah} × ${formatRupiah(hargaSatuan())}`;
            document.getElementById('totalHarga').textContent = formatRupiah(hargaSatuan() * pilihan.jumlah);

            document.getElementById('jumlah').textContent = pilihan.jumlah;
            document.getElementById('btnKurang').disabled = pilihan.jumlah <= 1;
            document.getElementById('btnTambah').disabled = pilihan.jumlah >= 99;

            document.getElementById('pesanError').classList.add('hidden');
        }

        // ============ TOPPING & JUMLAH ============
        document.querySelectorAll('.pilihan-topping').forEach(btn => {
            btn.addEventListener('click', () => {
                pilihan.topping = btn.dataset.topping;
                render();
            });
        });

        document.getElementById('btnKurang').addEventListener('click', () => {
            pilihan.jumlah = Math.max(1, pilihan.jumlah - 1);
            render();
        });

        document.getElementById('btnTambah').addEventListener('click', () => {
            pilihan.jumlah = Math.min(99, pilihan.jumlah + 1);
            render();
        });

        // ============ KERANJANG ============
        async function perbaruiBadge() {
            try {
                const res = await api('GET', '/keranjang');
                const badge = document.getElementById('badgeKeranjang');
                const total = res.data.total_item;
                badge.textContent = total;
                badge.classList.toggle('hidden', total === 0);
                badge.classList.toggle('grid', total > 0);
            } catch (_) {
                // badge tidak wajib, abaikan kalau gagal
            }
        }

        function tampilkanError(pesan) {
            const el = document.getElementById('pesanError');
            el.textContent = pesan;
            el.classList.remove('hidden');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function tampilkanToast(pesan) {
            const toast = document.getElementById('toast');
            toast.textContent = pesan;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 2500);
        }

        document.getElementById('btnKeranjang').addEventListener('click', async () => {
            if (!pilihan.ukuranId) return tampilkanError('Ukuran sedang habis.');
            if (!pilihan.filling && varian.filling.length) return tampilkanError('Pilih 1 creamy filling dulu.');
            if (pilihan.topping === 'seasoning' && !pilihan.seasoning) return tampilkanError('Pilih 1 seasoning.');
            if (pilihan.topping === 'dip' && !pilihan.dip) return tampilkanError('Pilih 1 dip sauce.');

            const variantIds = [pilihan.filling, pilihan[pilihan.topping]].filter(Boolean);
            const btn = document.getElementById('btnKeranjang');
            btn.disabled = true;

            try {
                await api('POST', '/keranjang', {
                    product_size_id: pilihan.ukuranId,
                    quantity: pilihan.jumlah,
                    variant_ids: variantIds,
                });

                const ukuran = LABEL_UKURAN[ukuranTerpilih().nama] ?? ukuranTerpilih().nama;
                tampilkanToast(`${pilihan.jumlah} Chikumi ${ukuran} ditambahkan ke keranjang`);
                perbaruiBadge();
            } catch (err) {
                tampilkanError(err.message);
            } finally {
                btn.disabled = false;
            }
        });

        // ============ MUAT PRODUK ============
        // ?id=<id produk>; kalau tidak ada, pakai produk pertama yang tersedia di katalog
        async function muatProduk() {
            try {
                const id = new URLSearchParams(location.search).get('id');

                if (id) {
                    produk = (await api('GET', `/produk/${encodeURIComponent(id)}`)).data;
                } else {
                    const katalog = (await api('GET', '/katalog')).data.produk;
                    produk = katalog.find(p => p.tersedia) ?? katalog[0];
                }

                if (!produk) throw new Error('Belum ada menu yang dijual.');
                tampilkanProduk();
            } catch (err) {
                document.getElementById('statusMuat').textContent = 'Gagal memuat menu: ' + err.message;
            }
        }

        muatProduk();
        perbaruiBadge();
    </script>
</body>

</html>