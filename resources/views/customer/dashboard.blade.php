@php
    $namaUser ??= 'Pelanggan';
    $jumlahKeranjang ??= 0;
    $alamat ??= null;
    $produk ??= collect();
    $rasa ??= collect();

    $opsiAmbil = [
        ['mode' => 'pickup',   'judul' => 'Pick Up',  'ket' => 'Ambil di gerai · tanpa ongkir',   'icon' => 'store'],
        ['mode' => 'delivery', 'judul' => 'Delivery', 'ket' => 'Diantar kurir · ongkir per area', 'icon' => 'pin'],
    ];

    $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
    $produkRoute = auth()->check() ? 'customer.produk' : 'frontend.customer.produk';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beranda - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <main class="mx-auto max-w-md px-4 pb-28 pt-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl lg:pb-12">

        <!-- TOP BAR -->
        <header class="flex items-center justify-between py-2 lg:py-4">
            <div>
                <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
                <p class="text-[10px] font-semibold tracking-wide">JAPANESE SNACK</p>
            </div>

            <!-- MENU DESKTOP (di HP diganti menu bawah) -->
            <nav class="hidden items-center gap-2 lg:flex">
                <a href="{{ route('customer.dashboard') }}"
                    class="rounded-full bg-[#FFF3E6] px-5 py-2.5 text-sm font-semibold text-[#FF8000]">Beranda</a>
                <a href="{{ Route::has('customer.pesanan') ? route('customer.pesanan') : '#' }}"
                    class="rounded-full px-5 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">Pesanan</a>
                <a href="{{ Route::has('customer.keranjang') ? route('customer.keranjang') : '#' }}"
                    class="flex items-center gap-2 rounded-full px-5 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                    Keranjang
                    @if ($jumlahKeranjang > 0)
                        <span class="grid h-5 min-w-5 place-items-center rounded-full bg-[#FF8000] px-1.5 text-[11px] font-semibold text-white">
                            {{ $jumlahKeranjang }}
                        </span>
                    @endif
                </a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ Route::has('customer.keranjang') ? route('customer.keranjang') : '#' }}" aria-label="Keranjang"
                    class="relative grid h-10 w-10 place-items-center rounded-xl border border-[#EDE6DF] bg-white lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>

                    @if ($jumlahKeranjang > 0)
                        <span class="absolute -right-2 -top-2 grid h-[18px] min-w-[18px] place-items-center rounded-full bg-[#FF8000] px-1 text-[10px] font-semibold text-white">
                            {{ $jumlahKeranjang }}
                        </span>
                    @endif
                </a>

                <!-- PROFIL + MENU KELUAR -->
                <div class="relative">
                    <button type="button" id="btnProfil" aria-label="Akun" aria-expanded="false"
                        class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">
                        {{ strtoupper(mb_substr($namaUser, 0, 1)) }}
                    </button>

                    <div id="menuProfil"
                        class="absolute right-0 top-12 z-50 hidden w-52 rounded-2xl border border-[#EDE6DF] bg-white p-2 shadow-lg">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[#E03131] transition hover:bg-[#FDECEC]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                    stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="mt-4 flex flex-col gap-4 lg:mt-6 lg:grid lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start lg:gap-8 xl:grid-cols-[minmax(0,1fr)_400px]">

            <!-- KOLOM KIRI -->
            <div class="contents lg:block lg:space-y-6">

                <!-- SAPAAN -->
                <section class="order-1">
                    <h2 class="text-[22px] font-bold">Halo, {{ $namaUser }}</h2>
                    <p class="text-sm text-[#6B645D]">Mau Chikumi apa hari ini?</p>
                </section>

                <!-- BANNER -->
                <section class="relative order-2 h-40 overflow-hidden rounded-2xl bg-[#FF8000] p-[18px] text-white sm:h-48 sm:p-6 lg:h-56 lg:p-8">
                    <span class="inline-block rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold text-[#D96C00]">
                        OPEN 15.00–21.00
                    </span>

                    <p class="relative z-10 mt-3 max-w-[190px] text-xl font-extrabold italic leading-tight sm:max-w-xs sm:text-3xl lg:max-w-sm lg:text-4xl">
                        Chikumi crispy, filling lumer!
                    </p>

                    <p class="absolute bottom-[18px] left-[18px] z-10 text-[10px] sm:bottom-6 sm:left-6 sm:text-xs lg:bottom-8 lg:left-8 lg:text-sm">
                        Pecinan · depan Toko Emas Wahyu Redjo
                    </p>

                    <img src="{{ asset('images/chikumi-1.png') }}" alt="Chikumi"
                        class="absolute -bottom-3 right-2 h-[170px] w-auto object-contain sm:right-8 sm:h-[210px] lg:right-12 lg:h-[250px]">
                </section>

                <!-- PILIH UKURAN -->
                <section class="order-5">
                    <h3 class="text-lg font-bold">Pilih Ukuran</h3>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @forelse ($produk as $item)
                            @php
                                $linkAtur = route($produkRoute, [
                                    'produk' => $item['produk_id'],
                                    'ukuran' => $item['size_id'],
                                ]);
                            @endphp

                            @if ($item['tersedia'])
                                <a href="{{ $linkAtur }}"
                                    class="flex items-center gap-3.5 rounded-2xl border-2 border-transparent bg-white p-3.5 outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000] active:border-[#FF8000]">

                                    <div class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000] min-[380px]:h-[92px] min-[380px]:w-[92px]">
                                        <img src="{{ asset('images/chikumi-1.png') }}" alt="{{ $item['nama'] }}" class="h-[76px] w-auto object-contain min-[380px]:h-[88px]">
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-lg font-extrabold italic">{{ $item['nama'] }}</p>
                                        <p class="mt-0.5 text-xs text-[#6B645D]">{{ $item['label_topping'] }} · {{ $item['panjang'] }}</p>
                                        <p class="mt-1 text-sm font-semibold text-[#D96C00]">{{ $rupiah($item['harga']) }}</p>
                                    </div>

                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#FF8000] text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="h-[18px] w-[18px]">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </span>
                                </a>
                            @else
                                <div class="flex items-center gap-3.5 rounded-2xl border border-[#EDE6DF] bg-white/60 p-3.5">

                                    <div class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FFE0BF]/60 min-[380px]:h-[92px] min-[380px]:w-[92px]">
                                        <img src="{{ asset('images/chikumi-1.png') }}" alt="{{ $item['nama'] }}" class="h-[76px] w-auto object-contain opacity-50 min-[380px]:h-[88px]">
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-lg font-extrabold italic text-[#A39B93]">{{ $item['nama'] }}</p>
                                        <p class="mt-0.5 text-xs text-[#A39B93]">{{ $item['label_topping'] }} · {{ $item['panjang'] }}</p>
                                        <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-xs font-medium text-[#E03131]">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>
                                            Habis
                                        </span>
                                    </div>

                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#F7F3EF] text-[#A39B93]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="h-[18px] w-[18px]">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </span>
                                </div>
                            @endif
                        @empty
                            <p class="text-sm text-[#6B645D]">Belum ada produk tersedia.</p>
                        @endforelse
                    </div>
                </section>

            </div>

            <!-- KOLOM KANAN -->
            <div class="contents lg:sticky lg:top-6 lg:block lg:space-y-4">

                <!-- PICK UP / DELIVERY -->
                <section class="order-3 grid grid-cols-2 gap-3">
                    @foreach ($opsiAmbil as $opsi)
                        <button type="button" data-mode="{{ $opsi['mode'] }}"
                            class="mode-card rounded-2xl border border-[#EDE6DF] bg-white p-3.5 text-left transition min-[380px]:p-4">

                            <div class="flex items-start justify-between">
                                <span class="mode-icon grid h-10 w-10 place-items-center rounded-xl bg-[#F7F3EF]">
                                    @if ($opsi['icon'] === 'store')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                            stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                                            stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    @endif
                                </span>

                                <span class="mode-check hidden h-[22px] w-[22px] place-items-center rounded-full bg-[#FF8000] text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </span>
                            </div>

                            <p class="mode-title mt-2 text-base font-bold">{{ $opsi['judul'] }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-[#6B645D]">{{ $opsi['ket'] }}</p>
                        </button>
                    @endforeach
                </section>

                <!-- ALAMAT AKTIF (hanya untuk Delivery) -->
                <section id="alamatAktif" class="order-4 flex items-center gap-2.5 rounded-2xl border border-[#EDE6DF] bg-white px-3.5 py-3.5">
                    <span class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-lg bg-[#FFF3E6] text-[#FF8000]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                            stroke="currentColor" class="h-[18px] w-[18px]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        @if ($alamat)
                            <p class="text-sm font-semibold">Antar ke · {{ $alamat['label'] }}</p>
                            <p class="truncate text-xs text-[#6B645D]">{{ $alamat['detail'] }}</p>
                        @else
                            <p class="text-sm font-semibold">Belum ada alamat</p>
                            <p class="text-xs text-[#6B645D]">Tambahkan alamat pengiriman saat checkout.</p>
                        @endif
                    </div>

                    <a href="{{ Route::has('customer.alamat') ? route('customer.alamat') : '#' }}"
                        class="text-sm font-semibold text-[#FF8000]">{{ $alamat ? 'Ganti' : 'Tambah' }}</a>
                </section>

                <!-- PILIHAN RASA HARI INI -->
                <section class="order-6 rounded-2xl border border-[#EDE6DF] bg-white p-[18px]">
                    <h3 class="text-base font-semibold">Pilihan Rasa Hari Ini</h3>

                    @foreach ($rasa as $grup)
                        <div class="mt-3">
                            <p class="text-sm font-semibold">
                                {{ $grup['judul'] }}
                                <span class="ml-1 text-xs font-normal text-[#6B645D]">{{ $grup['aturan'] }}</span>
                            </p>

                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach ($grup['pilihan'] as $pilihan)
                                    @if ($pilihan['habis'])
                                        <span class="rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-4 py-2.5 text-sm text-[#A39B93]/70 min-[380px]:px-5">
                                            {{ $pilihan['nama'] }} · habis
                                        </span>
                                    @else
                                        <span class="rounded-full border border-[#EDE6DF] bg-white px-4 py-2.5 text-sm min-[380px]:px-5">
                                            {{ $pilihan['nama'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </section>

            </div>
        </div>

    </main>


    {{-- MENU BAWAH --}}
    @include('components.customer.bottom-nav')

    <script>
        // ============ PILIH PICK UP / DELIVERY ============
        const alamatAktif = document.getElementById('alamatAktif');

        function pilihMode(mode) {
            document.querySelectorAll('.mode-card').forEach(card => {
                const aktif = card.dataset.mode === mode;

                card.classList.toggle('border-2', aktif);
                card.classList.toggle('border-[#FF8000]', aktif);
                card.classList.toggle('bg-[#FFF3E6]', aktif);
                card.classList.toggle('border-[#EDE6DF]', !aktif);
                card.classList.toggle('bg-white', !aktif);

                const icon = card.querySelector('.mode-icon');
                icon.classList.toggle('bg-[#FF8000]', aktif);
                icon.classList.toggle('text-white', aktif);
                icon.classList.toggle('bg-[#F7F3EF]', !aktif);

                card.querySelector('.mode-title').classList.toggle('text-[#D96C00]', aktif);

                const check = card.querySelector('.mode-check');
                check.classList.toggle('hidden', !aktif);
                check.classList.toggle('grid', aktif);
            });

            alamatAktif.classList.toggle('hidden', mode !== 'delivery');
            alamatAktif.classList.toggle('flex', mode === 'delivery');
        }

        document.querySelectorAll('.mode-card').forEach(card => {
            card.addEventListener('click', () => pilihMode(card.dataset.mode));
        });

        pilihMode('delivery');

        // ============ MENU PROFIL (KELUAR) ============
        const btnProfil = document.getElementById('btnProfil');
        const menuProfil = document.getElementById('menuProfil');

        function aturMenuProfil(buka) {
            menuProfil.classList.toggle('hidden', !buka);
            btnProfil.setAttribute('aria-expanded', buka);
        }

        btnProfil.addEventListener('click', e => {
            e.stopPropagation();
            aturMenuProfil(menuProfil.classList.contains('hidden'));
        });

        // Klik di luar kartu atau tekan Esc = tutup
        document.addEventListener('click', e => {
            if (!menuProfil.contains(e.target)) aturMenuProfil(false);
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') aturMenuProfil(false);
        });
    </script>
</body>

</html>