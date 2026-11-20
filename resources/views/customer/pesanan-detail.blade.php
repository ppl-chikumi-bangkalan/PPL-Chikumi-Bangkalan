<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!-- data contoh #CK-0129: backend isi dari pesanan yang dipilih -->
    <main class="mx-auto flex max-w-md flex-col gap-4 px-5 pb-8 pt-4 sm:max-w-xl md:max-w-3xl md:px-8">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <a href="/customer/pesanan" aria-label="Kembali"
                class="grid h-10 w-10 place-items-center rounded-xl border-[1.5px] border-[#EDE6DF] bg-white transition hover:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 id="nomorPesanan" class="text-lg font-bold">#CK-0129</h1>
        </header>

        <!-- STATUS UTAMA -->
        <section id="statusUtama" class="flex items-center gap-3.5 rounded-[22px] bg-[#1F9D55] p-[18px] text-white">
            <span class="grid h-[52px] w-[52px] shrink-0 place-items-center rounded-[14px] bg-white text-[#1F9D55]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[26px] w-[26px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <div>
                <p class="text-xl font-bold">Siap Dikirim</p>
                <p class="text-xs">Kurir sedang menuju alamatmu</p>
            </div>
        </section>

        <!-- STATUS PESANAN (TIMELINE) -->
        <section class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Status Pesanan</h2>

            <ol id="timelineStatus" class="mt-3">
                <!-- tahap sudah lewat -->
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-[#FFE0BF] text-[#D96C00]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="h-[26px] w-0.5 bg-[#FF8000]"></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Belum Dibayar</p>
                        <p class="text-[11px] text-[#6B645D]">16.48 · pesanan dibuat</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-[#FFE0BF] text-[#D96C00]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="h-[26px] w-0.5 bg-[#FF8000]"></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Diproses</p>
                        <p class="text-[11px] text-[#6B645D]">16.50 · pembayaran QRIS diterima</p>
                    </div>
                </li>
                <!-- tahap saat ini -->
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="grid h-6 w-6 place-items-center rounded-full bg-[#FF8000] text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="h-[26px] w-0.5 bg-[#EDE6DF]"></span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#D96C00]">Siap Dikirim</p>
                        <p class="text-[11px] text-[#6B645D]">17.10 · kurir menuju alamatmu</p>
                    </div>
                </li>
                <!-- tahap belum -->
                <li class="flex gap-3">
                    <span class="h-6 w-6 shrink-0 rounded-full bg-[#F7F3EF]"></span>
                    <div>
                        <p class="text-sm text-[#A39B93]">Selesai</p>
                        <p class="text-[11px] text-[#6B645D]">Menunggu pesanan diterima</p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- DIANTAR KE (sembunyikan kalau Pick Up) -->
        <section id="kartuAlamat" class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Diantar ke</h2>
            <div class="mt-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-[#FF8000]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <p class="text-[13px] font-semibold">Rumah · Budi Santoso</p>
            </div>
            <p class="mt-3 text-xs text-[#6B645D]">Jl. Trunojoyo No. 12, dekat masjid · Bangkalan Kota</p>
        </section>

        <!-- RINCIAN PESANAN -->
        <section class="flex flex-col gap-3 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Rincian Pesanan</h2>

            <div id="rincianItem" class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[13px] font-semibold">2× Reguler · Dip Sauce</p>
                    <p class="text-[11px] text-[#6B645D]">Garlic Cream Cheese · Black Pepper</p>
                </div>
                <p class="text-[13px] font-medium">Rp 42.000</p>
            </div>

            <div class="flex items-center justify-between text-[13px]">
                <p class="text-[#6B645D]">Ongkos kirim</p>
                <p class="font-medium">Rp 8.000</p>
            </div>

            <div class="h-px bg-[#EDE6DF]"></div>

            <div class="flex items-center justify-between font-bold">
                <p class="text-[15px]">Total (QRIS · Lunas)</p>
                <p class="text-lg text-[#D96C00]">Rp 50.000</p>
            </div>
        </section>

        <a href="https://wa.link/oo1z78" target="_blank" rel="noopener" id="btnWhatsapp"
            class="flex justify-center rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
            Hubungi Chikumi via WhatsApp
        </a>
    </main>

</body>

</html>