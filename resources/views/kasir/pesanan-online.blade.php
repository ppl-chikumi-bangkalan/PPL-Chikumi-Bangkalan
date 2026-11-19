<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Online - Chikumi</title>
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
        <nav class="flex items-center gap-1 rounded-xl bg-[#F7F3EF] p-1">
            <a href="/kasir/dashboard" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                POS
            </a>
            <a href="/kasir/pesanan-online" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Pesanan Online
            </a>
            <a href="/kasir/riwayat" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Riwayat
            </a>
        </nav>
        <div class="ml-auto flex items-center gap-4">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">K</span>
            <div class="leading-tight">
                <p class="text-sm font-semibold">Kasir</p>
                <p class="text-xs text-[#6B645D]">Kasir</p>
            </div>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" aria-label="Keluar" class="grid h-11 w-11 place-items-center rounded-xl border border-[#EDE6DF] bg-white text-[#E03131] transition hover:bg-[#FDECEC]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <main class="group flex flex-col gap-5 p-7">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Pesanan Online</h2>
                <p class="mt-0.5 text-sm text-[#6B645D]">Pesanan lunas dari aplikasi pelanggan · hari ini</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-semua" value="semua" class="sr-only" checked>Semua
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-ambil" value="pickup" class="sr-only">Ambil di Tempat
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-diantar" value="courier" class="sr-only">Diantar
                </label>
            </div>
        </div>

        <!-- data contoh: backend isi kartu dari pesanan online yang sudah lunas -->
        <div class="grid items-start gap-[18px] lg:grid-cols-3">
            <!-- KOLOM: DIPROSES -->
            <section id="kolomDiproses" class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF8000]"></span>
                        <h3 class="text-base font-bold">Diproses</h3>
                    </div>
                    <span class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#FF8000] text-white">3</span>
                </div>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] group-has-[#jenis-diantar:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0131</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Budi · 17.05</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>2× Large · Garlic CC · Dip Korean Spicy</p>
                        <p>1× Reguler · Mentaiko · BBQ + Sweetcorn</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 74.000</p>
                        <button type="button" data-aksi="siap" data-pesanan="CK-0131" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Tandai Siap</button>
                    </div>
                </article>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] group-has-[#jenis-diantar:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0132</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · VA BCA</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Sinta · 17.12</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>1× Reguler · Cream Cheese · Dip BBQ</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 21.000</p>
                        <button type="button" data-aksi="siap" data-pesanan="CK-0132" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Tandai Siap</button>
                    </div>
                </article>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] group-has-[#jenis-ambil:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0133</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Andi · 17.20</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-[11px] font-semibold text-[#C27C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span>Diantar</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>2× Reguler · Tartar · Teriyaki</p>
                        <p class="text-[#6B645D]">Ongkir Rp 8.000</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 50.000</p>
                        <button type="button" data-aksi="siap" data-pesanan="CK-0133" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Tandai Siap</button>
                    </div>
                </article>
            </section>

            <!-- KOLOM: SIAP DIAMBIL / DIKIRIM -->
            <section id="kolomSiap" class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#1F9D55]"></span>
                        <h3 class="text-base font-bold">Siap Diambil / Dikirim</h3>
                    </div>
                    <span class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#FF8000] text-white">2</span>
                </div>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] group-has-[#jenis-diantar:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0128</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Dewi · 16.40</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>1× Large · Mentaiko · BBQ</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 26.000</p>
                        <button type="button" data-aksi="selesai" data-pesanan="CK-0128" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Selesai</button>
                    </div>
                </article>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] group-has-[#jenis-ambil:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0129</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Raka · 16.48</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-[11px] font-semibold text-[#C27C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span>Diantar</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>2× Reguler · Garlic CC · Black Pepper</p>
                        <p class="text-[#6B645D]">Ongkir Rp 8.000</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 50.000</p>
                        <button type="button" data-aksi="selesai" data-pesanan="CK-0129" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Selesai</button>
                    </div>
                </article>
            </section>

            <!-- KOLOM: SELESAI HARI INI -->
            <section id="kolomSelesai" class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#A39B93]"></span>
                        <h3 class="text-base font-bold">Selesai Hari Ini</h3>
                    </div>
                    <span class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#EDE6DF] text-[#6B645D]">2</span>
                </div>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] opacity-65 group-has-[#jenis-diantar:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0124</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · QRIS</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Budi · 15.20</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>2× Large · Garlic CC · Korean Spicy</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 54.000</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Selesai</span>
                    </div>
                </article>
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] opacity-65 group-has-[#jenis-diantar:checked]:hidden">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#CK-0126</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · VA BRI</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Nina · 15.45</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        <p>1× Reguler · Mentaiko · Sweetcorn</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">Rp 20.000</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Selesai</span>
                    </div>
                </article>
            </section>
        </div>
    </main>

</body>

</html>