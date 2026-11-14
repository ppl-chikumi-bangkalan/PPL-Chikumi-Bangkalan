<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Alamat - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <form class="group pb-32">

        <main class="mx-auto max-w-md space-y-5 px-4 pt-4 min-[380px]:px-5 sm:max-w-xl md:px-8 lg:max-w-2xl">

            <!-- APP BAR -->
            <header class="flex items-center gap-3 py-1.5">
  
                <span id="dari-checkout"></span>

                <a href="/customer/dashboard" aria-label="Kembali"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white group-has-[#dari-checkout:target]:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>

                <a href="/customer/checkout" aria-label="Kembali ke Checkout"
                    class="hidden h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white group-has-[#dari-checkout:target]:grid">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>

                <h1 class="flex-1 text-lg font-bold">Edit Alamat</h1>
            </header>

            <!-- LABEL ALAMAT -->
            <section>
                <p class="text-sm font-medium">Label Alamat</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="label" value="Rumah" class="sr-only" checked>
                        Rumah
                    </label>
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="label" value="Kos" class="sr-only">
                        Kos
                    </label>
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="label" value="Kantor" class="sr-only">
                        Kantor
                    </label>
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="label" value="Lainnya" class="sr-only">
                        Lainnya
                    </label>
                </div>
            </section>

            <!-- NAMA PENERIMA -->
            <section>
                <label for="namaPenerima" class="text-sm font-medium">Nama Penerima</label>
                <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <input id="namaPenerima" name="recipient_name" type="text" maxlength="100" required
                        value="Budi Santoso" placeholder="Nama penerima pesanan"
                        class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </div>
            </section>

            <!-- NO. WHATSAPP -->
            <section>
                <label for="noWhatsapp" class="text-sm font-medium">No. WhatsApp</label>
                <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    <input id="noWhatsapp" name="phone" type="tel" inputmode="tel" maxlength="20" required
                        value="0823 xxxx xxxx" placeholder="08xx xxxx xxxx"
                        class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </div>
            </section>

            <!-- AREA PENGANTARAN + ONGKIR -->
            <section>
                <label for="areaPengantaran" class="text-sm font-medium">Area Pengantaran</label>
                <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#FF8000]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <input id="areaPengantaran" name="city" type="text" maxlength="100" required
                        value="Bangkalan Kota" placeholder="Contoh: Bangkalan Kota"
                        class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </div>

                <div class="mt-4 flex items-center gap-2.5 rounded-xl bg-[#FFF3E6] px-3 py-3 text-xs font-medium text-[#D96C00]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-4 w-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    Ongkir ke area ini Rp 8.000
                </div>
            </section>

            <!-- ALAMAT LENGKAP -->
            <section>
                <label for="alamatLengkap" class="text-sm font-medium">Alamat Lengkap</label>
                <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <input id="alamatLengkap" name="detail" type="text" maxlength="500" required
                        value="Jl. Trunojoyo No. 12, RT 02/RW 03" placeholder="Nama jalan, nomor rumah, RT/RW"
                        class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </div>
            </section>

            <!-- PATOKAN (opsional) -->
            <section>
                <label for="patokan" class="text-sm font-medium">Patokan (opsional)</label>
                <div class="mt-2.5 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <input id="patokan" name="landmark" type="text" maxlength="100"
                        placeholder="Contoh: dekat masjid, pagar hijau"
                        class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </div>
            </section>

            <!-- JADIKAN ALAMAT UTAMA -->
            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-[#EDE6DF] bg-white p-[18px]">
                <span>
                    <span class="block text-sm font-semibold">Jadikan alamat utama</span>
                    <span class="block text-xs text-[#6B645D]">Dipakai otomatis saat pilih Delivery</span>
                </span>

                <span class="relative inline-flex shrink-0">
                    <input type="checkbox" name="is_primary" value="1" class="peer sr-only" checked>
                    <span class="h-6 w-11 rounded-full bg-[#EDE6DF] transition peer-checked:bg-[#FF8000] peer-focus-visible:ring-2 peer-focus-visible:ring-[#FF8000]/40"></span>
                    <span class="absolute left-[3px] top-[3px] h-[18px] w-[18px] rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                </span>
            </label>

            <!-- HAPUS ALAMAT -->
            <div class="text-center">
                <button type="button" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#E03131] hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Hapus alamat ini
                </button>
            </div>
        </main>

        <!-- BOTTOM BAR: SIMPAN -->
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
            <div class="mx-auto max-w-md px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:px-8 lg:max-w-2xl">
                <!-- type="button" sementara; backend kirim ke POST / PUT /customer/api/alamat -->
                <button type="button"
                    class="w-full rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                    Simpan Alamat
                </button>
            </div>
        </div>
    </form>
</body>

</html>