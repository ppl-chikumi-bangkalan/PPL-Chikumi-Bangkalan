<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Chikumi - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">
    <form id="formProduk" class="group">

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

                <h1 class="flex-1 text-lg font-bold">Atur Chikumi</h1>

                <a href="#" aria-label="Keranjang"
                    class="relative grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#EDE6DF] bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>

                    <span class="absolute -right-2 -top-2 grid h-[18px] min-w-[18px] place-items-center rounded-full bg-[#FF8000] px-1 text-[10px] font-semibold text-white">
                        2
                    </span>
                </a>
            </header>
            
            <div class="mt-4 lg:grid lg:grid-cols-2 lg:items-start lg:gap-10">

                <!-- FOTO -->
                <div class="grid h-[180px] place-items-center overflow-hidden rounded-2xl bg-[#FF8000] sm:h-60 lg:sticky lg:top-6 lg:h-[480px]">
                    <img src="/images/chikumi-1.png" alt="Chikumi"
                        class="h-[170px] w-auto object-contain sm:h-56 lg:h-[440px]">
                </div>

                <!-- PILIHAN -->
                <div class="mt-4 space-y-6 lg:mt-0">

                    <!-- 1. PILIH UKURAN -->
                    <section>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold">1. Pilih Ukuran</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2.5">
                            <label class="cursor-pointer rounded-2xl border-2 border-transparent bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="ukuran" value="Reguler" id="ukuran-reguler" class="peer sr-only" checked>
                                <p class="text-base font-semibold peer-checked:text-[#D96C00]">Reguler</p>
                                <p class="mt-0.5 text-xs text-[#6B645D]">16 cm</p>
                            </label>

                            <!-- Large sedang habis (hapus "disabled" kalau tersedia) -->
                            <label class="cursor-pointer rounded-2xl border-2 border-transparent bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000] has-disabled:cursor-not-allowed has-disabled:bg-white/60 has-disabled:hover:border-transparent">
                                <input type="radio" name="ukuran" value="Large" id="ukuran-large" class="peer sr-only" disabled>
                                <p class="text-base font-semibold peer-checked:text-[#D96C00] peer-disabled:text-[#A39B93]">Large</p>
                                <p class="mt-0.5 text-xs text-[#6B645D] peer-disabled:text-[#A39B93]">25 cm · habis</p>
                            </label>
                        </div>
                    </section>

                    <!-- 2. CREAMY FILLING -->
                    <section>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold">2. Creamy Filling</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> wajib pilih 1
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="filling" value="Garlic Cream Cheese" class="sr-only" required>
                                Garlic Cream Cheese
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="filling" value="Cream Cheese" class="sr-only">
                                Cream Cheese
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="filling" value="Mentaiko" class="sr-only">
                                Mentaiko
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="filling" value="Tartar" class="sr-only">
                                Tartar
                            </label>
                        </div>
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
                            <label class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-2 border-transparent bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="topping" value="seasoning" id="topping-seasoning" class="peer sr-only" checked>
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000] peer-checked:shadow-[inset_0_0_0_4px_white]"></span>
                                <span class="flex-1">
                                    <span class="block text-base font-semibold">Seasoning Powder</span>
                                    <span class="block text-xs text-[#6B645D]">Boleh pilih lebih dari 1</span>
                                </span>
                                <!-- harga berubah sesuai ukuran -->
                                <span class="text-sm font-semibold text-[#D96C00]">
                                    <span class="group-has-[#ukuran-large:checked]:hidden">Rp 20.000</span>
                                    <span class="hidden group-has-[#ukuran-large:checked]:inline">Rp 26.000</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-2 border-transparent bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="topping" value="dip" id="topping-dip" class="peer sr-only">
                                <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000] peer-checked:shadow-[inset_0_0_0_4px_white]"></span>
                                <span class="flex-1">
                                    <span class="block text-base font-semibold">Dip Sauce</span>
                                    <span class="block text-xs text-[#6B645D]">Pilih 1 saus</span>
                                </span>
                                <span class="text-sm font-semibold text-[#D96C00]">
                                    <span class="group-has-[#ukuran-large:checked]:hidden">Rp 21.000</span>
                                    <span class="hidden group-has-[#ukuran-large:checked]:inline">Rp 27.000</span>
                                </span>
                            </label>
                        </div>
                    </section>

                    <!-- 4a. PILIH SEASONING (tampil kalau Seasoning Powder dipilih) -->
                    <section class="group-has-[#topping-dip:checked]:hidden">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold">4. Pilih Seasoning</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> boleh lebih dari 1
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="checkbox" name="seasoning[]" value="BBQ" class="peer sr-only">
                                <span class="hidden peer-checked:inline">✓ </span>BBQ
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="checkbox" name="seasoning[]" value="Sweetcorn" class="peer sr-only">
                                <span class="hidden peer-checked:inline">✓ </span>Sweetcorn
                            </label>
                            <!-- Extra Hot sedang habis -->
                            <label class="cursor-not-allowed rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-5 py-2.5 text-sm text-[#A39B93]/70">
                                <input type="checkbox" name="seasoning[]" value="Extra Hot" class="sr-only" disabled>
                                Extra Hot · habis
                            </label>
                        </div>
                    </section>

                    <!-- 4b. PILIH DIP SAUCE (tampil kalau Dip Sauce dipilih) -->
                    <section class="hidden group-has-[#topping-dip:checked]:block">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold">4. Pilih Dip Sauce</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1 saus
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="dip" value="BBQ" class="sr-only">
                                BBQ
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="dip" value="Black Pepper" class="sr-only">
                                Black Pepper
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="dip" value="Korean Spicy" class="sr-only">
                                Korean Spicy
                            </label>
                            <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                                <input type="radio" name="dip" value="Teriyaki" class="sr-only">
                                Teriyaki
                            </label>
                        </div>
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
                            <input id="catatan" name="catatan" type="text" maxlength="100" placeholder="Contoh: seasoning dipisah"
                                class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                        </div>
                    </section>

                    <!-- JUMLAH -->
                    <section class="flex items-center justify-between">
                        <label for="jumlah" class="text-base font-bold">Jumlah</label>

                        <input id="jumlah" name="jumlah" type="number" min="1" max="99" value="1"
                            class="w-20 rounded-xl border border-[#EDE6DF] bg-white px-3 py-2 text-center text-sm font-semibold outline-none focus:border-[#FF8000]">
                    </section>
                </div>
            </div>
        </main>

        <!-- BOTTOM BAR: HARGA + KERANJANG -->
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white shadow-[0_-4px_16px_rgba(30,27,24,0.06)]">
            <div class="mx-auto flex max-w-md items-center justify-between gap-3 px-4 py-4 min-[380px]:px-5 sm:max-w-xl md:max-w-3xl md:px-8 lg:max-w-6xl">
                <div>
                    <p class="text-xs text-[#6B645D]">Harga per porsi</p>

                    <!-- harga = ukuran + jenis topping -->
                    <p class="text-2xl font-bold text-[#D96C00]">
                        <span class="group-has-[#ukuran-large:checked]:hidden">
                            <span class="group-has-[#topping-dip:checked]:hidden">Rp 20.000</span>
                            <span class="hidden group-has-[#topping-dip:checked]:inline">Rp 21.000</span>
                        </span>
                        <span class="hidden group-has-[#ukuran-large:checked]:inline">
                            <span class="group-has-[#topping-dip:checked]:hidden">Rp 26.000</span>
                            <span class="hidden group-has-[#topping-dip:checked]:inline">Rp 27.000</span>
                        </span>
                    </p>
                </div>

                <!-- type="button" sementara; backend ganti jadi type="submit" setelah form punya action + @csrf -->
                <button type="button"
                    class="rounded-xl bg-[#FF8000] px-6 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00] min-[380px]:px-7">
                    + Keranjang
                </button>
            </div>
        </div>

    </form>
</body>

</html>