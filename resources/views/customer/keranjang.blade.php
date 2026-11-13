<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="/customer/dashboard" aria-label="Kembali"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>

            <h1 class="flex-1 text-lg font-bold">Keranjang</h1>

            <button type="button" class="text-sm font-medium text-[#E03131] hover:underline">
                Hapus semua
            </button>
        </header>

        <div class="mt-4 lg:grid lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start lg:gap-8">

            <!-- DAFTAR ITEM -->
            <div class="space-y-4">

                <!-- Item 1 -->
                <article class="flex gap-3 rounded-2xl border border-[#EDE6DF] bg-white p-3.5">
                    <div class="grid h-[72px] w-[72px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="Chikumi Reguler" class="h-[68px] w-auto object-contain">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-base font-semibold">Reguler · Seasoning</p>
                            <button type="button" aria-label="Hapus item" class="mt-0.5 text-[#E03131] hover:opacity-70">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        <p class="mt-1 truncate text-xs text-[#6B645D]">Mentaiko · BBQ + Sweetcorn</p>
                        <a href="/customer/produk?ukuran=Reguler&amp;topping=seasoning"
                            class="mt-1 inline-block text-xs font-semibold text-[#FF8000] hover:underline">Ubah pilihan</a>

                        <div class="mt-1.5 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" aria-label="Kurangi" class="grid h-[30px] w-[30px] place-items-center text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg>
                                </button>
                                <span class="w-[30px] text-center text-sm font-semibold">2</span>
                                <button type="button" aria-label="Tambah" class="grid h-[30px] w-[30px] place-items-center text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-base font-bold text-[#D96C00]">Rp 40.000</p>
                        </div>
                    </div>
                </article>

                <!-- Item 2 -->
                <article class="flex gap-3 rounded-2xl border border-[#EDE6DF] bg-white p-3.5">
                    <div class="grid h-[72px] w-[72px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="Chikumi Reguler" class="h-[68px] w-auto object-contain">
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-base font-semibold">Reguler · Dip Sauce</p>
                            <button type="button" aria-label="Hapus item" class="mt-0.5 text-[#E03131] hover:opacity-70">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        <p class="mt-1 truncate text-xs text-[#6B645D]">Cream Cheese · Korean Spicy</p>
                        <a href="/customer/produk?ukuran=Reguler&amp;topping=dip"
                            class="mt-1 inline-block text-xs font-semibold text-[#FF8000] hover:underline">Ubah pilihan</a>

                        <div class="mt-1.5 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" aria-label="Kurangi" class="grid h-[30px] w-[30px] place-items-center text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg>
                                </button>
                                <span class="w-[30px] text-center text-sm font-semibold">1</span>
                                <button type="button" aria-label="Tambah" class="grid h-[30px] w-[30px] place-items-center text-[#FF8000]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-base font-bold text-[#D96C00]">Rp 21.000</p>
                        </div>
                    </div>
                </article>

                <!-- Tambah Chikumi lain -->
                <a href="/customer/dashboard"
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
                    <span class="text-[#6B645D]">Subtotal (3 pcs)</span>
                    <span>Rp 61.000</span>
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
                <p class="text-2xl font-bold text-[#D96C00]">Rp 61.000</p>
            </div>

            <!-- Checkout -->
            <a href="/customer/checkout"
                class="rounded-xl bg-[#FF8000] px-7 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                Checkout
            </a>
        </div>
    </div>
</body>

</html>