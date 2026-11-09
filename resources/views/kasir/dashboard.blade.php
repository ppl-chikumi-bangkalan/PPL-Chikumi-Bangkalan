<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Kasir - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!-- TOPBAR KASIR -->
    <header class="sticky top-0 z-30 flex h-[76px] items-center gap-6 border-b border-[#EDE6DF] bg-white px-7">
        <div class="shrink-0">
            <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
            <p class="text-[10px] font-semibold tracking-wide">JAPANESE SNACK</p>
        </div>

        <!-- MENU KASIR -->
        <nav class="flex items-center gap-1 rounded-xl bg-[#F7F3EF] p-1">
            <a href="#" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                POS
            </a>

            <a href="#" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                    <path stroke-linecap="round" d="M9 8.25h6M9 11.25h6M9 14.25h3.75" />
                </svg>
                Pesanan Online
                <span class="grid h-5 min-w-5 place-items-center rounded-full bg-[#E03131] px-1.5 text-[11px] font-semibold text-white">3</span>
            </a>

            <a href="#" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat
            </a>
        </nav>

            <div class="ml-auto flex items-center gap-4">
                <span class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">R</span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold">Chikumi</p>
                    <p class="text-xs text-[#6B645D]">Kasir</p>
                </div>
            </div>

            <!-- Logout: backend jadikan form POST ke route('logout') + @csrf -->
            <button type="button" aria-label="Keluar"
                class="grid h-11 w-11 place-items-center rounded-xl border border-[#EDE6DF] bg-white text-[#E03131] transition hover:bg-[#FDECEC]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </button>
        </div>
    </header>

    <div class="lg:grid lg:min-h-[calc(100vh-76px)] lg:grid-cols-[minmax(0,1fr)_430px]">

        <main class="space-y-5 p-7">

            <div>
                <h2 class="text-[26px] font-bold">Pilih Ukuran</h2>
                <p class="mt-0.5 text-sm text-[#6B645D]">Ketuk ukuran, lalu pilih varian pesanan</p>
            </div>

            <!-- KARTU UKURAN -->
            <div class="grid gap-5 md:grid-cols-2">

                <!-- Chikumi Reguler (tersedia) — klik membuka popup Pilih Varian -->
                <a href="#pilih-varian"
                    class="block rounded-2xl border-2 border-transparent bg-white p-[18px] text-left outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000]">
                    <div class="grid h-[180px] place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="Chikumi Reguler" class="h-[176px] w-auto object-contain">
                    </div>

                    <div class="mt-3.5 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[22px] font-extrabold italic leading-tight">Chikumi Reguler</p>
                            <p class="text-sm text-[#6B645D]">16 cm</p>
                        </div>
                        <span class="mt-3 inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-xs font-medium text-[#C27C00]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span> Sisa 4 pcs · menipis
                        </span>
                    </div>

                    <div class="mt-3.5 grid grid-cols-2 gap-2.5">
                        <div class="rounded-xl bg-[#FFF3E6] p-3">
                            <p class="text-xs text-[#6B645D]">+ Seasoning</p>
                            <p class="mt-0.5 text-lg font-bold text-[#D96C00]">Rp 20.000</p>
                        </div>
                        <div class="rounded-xl bg-[#FFF3E6] p-3">
                            <p class="text-xs text-[#6B645D]">+ Dip Sauce</p>
                            <p class="mt-0.5 text-lg font-bold text-[#D96C00]">Rp 21.000</p>
                        </div>
                    </div>
                </a>

                <!-- Chikumi Large (habis) -->
                <button type="button" disabled
                    class="cursor-not-allowed rounded-2xl border-2 border-transparent bg-white/60 p-[18px] text-left">
                    <div class="grid h-[180px] place-items-center overflow-hidden rounded-xl bg-[#FFE0BF]/60">
                        <img src="/images/chikumi-1.png" alt="Chikumi Large" class="h-[176px] w-auto object-contain opacity-50">
                    </div>

                    <div class="mt-3.5 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[22px] font-extrabold italic leading-tight text-[#A39B93]">Chikumi Large</p>
                            <p class="text-sm text-[#A39B93]">25 cm</p>
                        </div>
                        <span class="mt-3 inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-xs font-medium text-[#E03131]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span> Habis
                        </span>
                    </div>

                    <div class="mt-3.5 grid grid-cols-2 gap-2.5">
                        <div class="rounded-xl bg-[#FFF9F2] p-3">
                            <p class="text-xs text-[#A39B93]/70">+ Seasoning</p>
                            <p class="mt-0.5 text-lg font-bold text-[#A39B93]/70">Rp 26.000</p>
                        </div>
                        <div class="rounded-xl bg-[#FFF9F2] p-3">
                            <p class="text-xs text-[#A39B93]/70">+ Dip Sauce</p>
                            <p class="mt-0.5 text-lg font-bold text-[#A39B93]/70">Rp 27.000</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- PILIHAN RASA HARI INI -->
            <section class="rounded-2xl border border-[#EDE6DF] bg-white p-[22px]">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold">Pilihan Rasa Hari Ini</h3>
                    <p class="text-xs text-[#6B645D]">Harga tidak berubah oleh filling</p>
                </div>

                <div class="mt-3.5 space-y-3.5">
                    <!-- Filling -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <div class="w-[140px] shrink-0">
                            <p class="text-sm font-semibold">Filling</p>
                            <p class="text-xs text-[#6B645D]">wajib pilih 1</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Garlic Cream Cheese</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Cream Cheese</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Mentaiko</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Tartar</span>
                        </div>
                    </div>

                    <!-- Dip Sauce -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <div class="w-[140px] shrink-0">
                            <p class="text-sm font-semibold">Dip Sauce</p>
                            <p class="text-xs text-[#6B645D]">pilih 1 · +Rp 1.000</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">BBQ</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Black Pepper</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Korean Spicy</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Teriyaki</span>
                        </div>
                    </div>

                    <!-- Seasoning -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <div class="w-[140px] shrink-0">
                            <p class="text-sm font-semibold">Seasoning</p>
                            <p class="text-xs text-[#6B645D]">boleh lebih dari 1</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">BBQ</span>
                            <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Sweetcorn</span>
                            <span class="rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-5 py-2.5 text-sm text-[#A39B93]/70">Extra Hot · habis</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <aside class="flex flex-col border-t border-[#EDE6DF] bg-white p-6 lg:sticky lg:top-[76px] lg:h-[calc(100vh-76px)] lg:border-l lg:border-t-0">

            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">Pesanan #CK-0125</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#D96C00]"></span> 2 item
                </span>
            </div>

            <label for="namaPelanggan" class="mt-4 block text-sm font-medium">Nama Pelanggan (opsional)</label>
            <div class="mt-2 flex items-center gap-3 rounded-xl border border-[#EDE6DF] px-4 py-3.5 focus-within:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <input id="namaPelanggan" type="text" placeholder="Contoh: Budi"
                    class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
            </div>

            <!-- DAFTAR ITEM -->
            <div class="mt-3.5 flex-1 space-y-3.5 overflow-y-auto">

                <!-- Item 1 -->
                <div class="flex gap-3 rounded-2xl bg-[#F7F3EF] p-3">
                    <div class="grid h-[52px] w-[52px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-12 w-auto object-contain">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold">Reguler · Seasoning</p>
                            <div class="flex shrink-0 gap-1.5">
                                <button type="button" aria-label="Edit" class="text-[#6B645D] hover:text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Hapus" class="text-[#E03131] hover:opacity-70">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p class="mt-1 text-xs text-[#6B645D]">Mentaiko · BBQ + Sweetcorn</p>

                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" aria-label="Kurangi" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">−</button>
                                <span class="w-[34px] text-center text-sm font-semibold">2</span>
                                <button type="button" aria-label="Tambah" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">+</button>
                            </div>
                            <p class="text-sm font-bold text-[#D96C00]">Rp 40.000</p>
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="flex gap-3 rounded-2xl bg-[#F7F3EF] p-3">
                    <div class="grid h-[52px] w-[52px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-12 w-auto object-contain">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold">Reguler · Dip Sauce</p>
                            <div class="flex shrink-0 gap-1.5">
                                <button type="button" aria-label="Edit" class="text-[#6B645D] hover:text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Hapus" class="text-[#E03131] hover:opacity-70">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p class="mt-1 text-xs text-[#6B645D]">Cream Cheese · Korean Spicy</p>

                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" aria-label="Kurangi" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">−</button>
                                <span class="w-[34px] text-center text-sm font-semibold">1</span>
                                <button type="button" aria-label="Tambah" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">+</button>
                            </div>
                            <p class="text-sm font-bold text-[#D96C00]">Rp 21.000</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOTAL -->
            <div class="mt-4 border-t border-[#EDE6DF] pt-3.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B645D]">Subtotal (3 pcs)</span>
                    <span>Rp 61.000</span>
                </div>
                <div class="mt-3.5 flex items-center justify-between">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-[26px] font-bold text-[#D96C00]">Rp 61.000</span>
                </div>

                <div class="mt-3.5 flex gap-2.5">
                    <button type="button"
                        class="w-[102px] rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                        Batal
                    </button>
                    <button type="button"
                        class="flex-1 rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                        Bayar · Rp 61.000
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- POPUP K2 · PILIH VARIAN (form transaksi kasir) -->
    @include('components.kasir.pilih-varian')
</body>

</html>