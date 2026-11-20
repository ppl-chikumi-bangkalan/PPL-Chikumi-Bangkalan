<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <main class="group mx-auto flex max-w-md flex-col gap-4 px-5 pb-[106px] pt-4 sm:max-w-xl md:max-w-3xl md:px-8">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <h1 class="text-[22px] font-bold">Pesanan Saya</h1>
            <span class="ml-auto grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">B</span>
        </header>

        <!-- FILTER STATUS -->
        <div class="-mx-5 flex gap-2 overflow-x-auto px-5 pb-1 md:mx-0 md:px-0">
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-semua" value="semua" class="sr-only" checked>Semua
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-belum" value="belum" class="sr-only">Belum Dibayar
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-diproses" value="diproses" class="sr-only">Diproses
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-siap" value="siap" class="sr-only">Siap
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-selesai" value="selesai" class="sr-only">Selesai
            </label>
        </div>

        <!-- DAFTAR PESANAN (data contoh: backend isi dari GET /customer/api/pesanan) -->
        <div id="daftarPesanan" class="flex flex-col gap-4">
        <article data-pesanan="CK-0134" class="flex flex-col gap-2.5 rounded-[20px] border-[1.5px] border-[#C27C00] bg-white p-4 group-has-[#status-diproses:checked]:hidden group-has-[#status-siap:checked]:hidden group-has-[#status-selesai:checked]:hidden">
            <div class="flex items-center justify-between">
                <p class="text-[15px] font-bold">#CK-0134</p>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-[11px] font-semibold text-[#C27C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span>Belum Dibayar</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span>Hari ini · 17.35</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Delivery</span>
            </div>
            <p class="text-[13px]">3 pcs Chikumi Reguler</p>
            <div class="h-px bg-[#EDE6DF]"></div>
            <div class="flex items-center justify-between">
                <p class="text-base font-bold text-[#D96C00]">Rp 69.000</p>
                <a href="/customer/pembayaran" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Bayar Sekarang</a>
            </div>
        </article>

        <article data-pesanan="CK-0131" class="flex flex-col gap-2.5 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4 group-has-[#status-belum:checked]:hidden group-has-[#status-siap:checked]:hidden group-has-[#status-selesai:checked]:hidden">
            <div class="flex items-center justify-between">
                <p class="text-[15px] font-bold">#CK-0131</p>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Diproses</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span>Hari ini · 17.05</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Pick Up</span>
            </div>
            <p class="text-[13px]">2× Large, 1× Reguler</p>
            <div class="h-px bg-[#EDE6DF]"></div>
            <div class="flex items-center justify-between">
                <p class="text-base font-bold text-[#D96C00]">Rp 74.000</p>
                <a href="/customer/pesanan/detail" class="text-[13px] font-semibold text-[#D96C00] hover:underline">Lihat detail ›</a>
            </div>
        </article>

        <article data-pesanan="CK-0129" class="flex flex-col gap-2.5 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4 group-has-[#status-belum:checked]:hidden group-has-[#status-diproses:checked]:hidden group-has-[#status-selesai:checked]:hidden">
            <div class="flex items-center justify-between">
                <p class="text-[15px] font-bold">#CK-0129</p>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Siap Dikirim</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span>Hari ini · 16.48</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Delivery</span>
            </div>
            <p class="text-[13px]">2× Reguler</p>
            <div class="h-px bg-[#EDE6DF]"></div>
            <div class="flex items-center justify-between">
                <p class="text-base font-bold text-[#D96C00]">Rp 50.000</p>
                <a href="/customer/pesanan/detail" class="text-[13px] font-semibold text-[#D96C00] hover:underline">Lihat detail ›</a>
            </div>
        </article>

        <article data-pesanan="CK-0098" class="flex flex-col gap-2.5 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4 group-has-[#status-belum:checked]:hidden group-has-[#status-diproses:checked]:hidden group-has-[#status-siap:checked]:hidden">
            <div class="flex items-center justify-between">
                <p class="text-[15px] font-bold">#CK-0098</p>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Selesai</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span>20 Okt 2026</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Pick Up</span>
            </div>
            <p class="text-[13px]">2× Reguler</p>
            <div class="h-px bg-[#EDE6DF]"></div>
            <div class="flex items-center justify-between">
                <p class="text-base font-bold text-[#D96C00]">Rp 47.000</p>
                <a href="/customer/dashboard" class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">Pesan Lagi</a>
            </div>
        </article>
        </div>
    </main>

    @include('components.customer.bottom-nav')

</body>

</html>