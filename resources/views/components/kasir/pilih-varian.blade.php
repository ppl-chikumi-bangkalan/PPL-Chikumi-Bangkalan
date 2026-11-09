<div id="pilih-varian" class="fixed inset-0 z-50 hidden items-center justify-center p-4 target:flex">

    <!-- Area gelap di belakang (klik = tutup) -->
    <a href="#" aria-label="Tutup" class="absolute inset-0 bg-black/40"></a>

    <form class="group relative max-h-[92vh] w-full max-w-[780px] overflow-y-auto rounded-3xl bg-white p-7 shadow-xl">

        <!-- JUDUL -->
        <div class="flex items-start gap-4">
            <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                <img src="/images/chikumi-1.png" alt="" class="h-14 w-auto object-contain">
            </div>

            <div class="flex-1">
                <h2 class="text-2xl font-bold">Chikumi Reguler · 16 cm</h2>
                <a href="#" class="mt-1 inline-block text-sm font-semibold text-[#FF8000] hover:underline">Ganti ukuran</a>
            </div>

            <a href="#" aria-label="Tutup"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#F7F3EF] text-[#6B645D] hover:bg-[#EDE6DF]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <!-- 1. CREAMY FILLING -->
        <section class="mt-5">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-bold">1. Creamy Filling</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> wajib pilih 1
                </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
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

        <!-- 2. JENIS TOPPING (menentukan harga) -->
        <section class="mt-5">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-bold">2. Jenis Topping</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih salah satu
                </span>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="topping" value="seasoning" id="kasir-topping-seasoning" class="peer sr-only" checked>
                    <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000] peer-checked:shadow-[inset_0_0_0_4px_white]"></span>
                    <span class="flex-1">
                        <span class="block text-base font-semibold">Seasoning Powder</span>
                        <span class="block text-xs text-[#6B645D]">Boleh pilih lebih dari 1</span>
                    </span>
                    <span class="text-sm font-semibold text-[#D96C00]">Rp 20.000</span>
                </label>

                <label class="flex cursor-pointer items-center gap-3.5 rounded-2xl border-2 border-[#EDE6DF] bg-white p-4 transition hover:border-[#FF8000] has-checked:border-[#FF8000] has-checked:bg-[#FFF3E6] has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="topping" value="dip" id="kasir-topping-dip" class="peer sr-only">
                    <span class="h-5 w-5 shrink-0 rounded-full border-2 border-[#A39B93] peer-checked:border-[#FF8000] peer-checked:bg-[#FF8000] peer-checked:shadow-[inset_0_0_0_4px_white]"></span>
                    <span class="flex-1">
                        <span class="block text-base font-semibold">Dip Sauce</span>
                        <span class="block text-xs text-[#6B645D]">Pilih 1 saus</span>
                    </span>
                    <span class="text-sm font-semibold text-[#D96C00]">Rp 21.000</span>
                </label>
            </div>
        </section>

        <!-- 3a. PILIH SEASONING (tampil kalau Seasoning Powder dipilih) -->
        <section class="mt-5 group-has-[#kasir-topping-dip:checked]:hidden">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-bold">3. Pilih Seasoning</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> boleh lebih dari 1
                </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="checkbox" name="seasoning[]" value="BBQ" class="peer sr-only">
                    <span class="hidden peer-checked:inline">✓ </span>BBQ
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="checkbox" name="seasoning[]" value="Sweetcorn" class="peer sr-only">
                    <span class="hidden peer-checked:inline">✓ </span>Sweetcorn
                </label>
                <label class="cursor-not-allowed rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-5 py-2.5 text-sm text-[#A39B93]/70">
                    <input type="checkbox" name="seasoning[]" value="Extra Hot" class="sr-only" disabled>
                    Extra Hot · habis
                </label>
            </div>
        </section>

        <!-- 3b. PILIH DIP SAUCE (tampil kalau Dip Sauce dipilih) -->
        <section class="mt-5 hidden group-has-[#kasir-topping-dip:checked]:block">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-bold">3. Pilih Dip Sauce</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> pilih 1 saus
                </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
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
        <section class="mt-5">
            <label for="kasirCatatan" class="text-sm font-medium">Catatan (opsional)</label>

            <div class="mt-2 flex items-center gap-3 rounded-xl border border-[#EDE6DF] bg-white px-4 py-3.5 focus-within:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                </svg>
                <input id="kasirCatatan" name="catatan" type="text" maxlength="100" placeholder="Contoh: seasoning dipisah"
                    class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
            </div>
        </section>

        <!-- JUMLAH + HARGA + TAMBAH -->
        <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-[#EDE6DF] pt-5">
            <div>
                <label for="kasirJumlah" class="block text-xs text-[#6B645D]">Jumlah</label>
                <input id="kasirJumlah" name="jumlah" type="number" min="1" max="99" value="1"
                    class="mt-1 w-20 rounded-xl border border-[#EDE6DF] bg-white px-3 py-2 text-center text-sm font-semibold outline-none focus:border-[#FF8000]">
            </div>

            <div class="flex-1">
                <p class="text-xs text-[#6B645D]">Harga per porsi</p>
                <p class="text-2xl font-bold text-[#D96C00]">
                    <span class="group-has-[#kasir-topping-dip:checked]:hidden">Rp 20.000</span>
                    <span class="hidden group-has-[#kasir-topping-dip:checked]:inline">Rp 21.000</span>
                </p>
            </div>

            <!-- type="button" sementara; backend ganti jadi type="submit" setelah form punya action + @csrf -->
            <button type="button"
                class="rounded-xl bg-[#FF8000] px-7 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                Tambah ke Pesanan
            </button>
        </div>
    </form>
</div>