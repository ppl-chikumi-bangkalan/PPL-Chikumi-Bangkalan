<div id="pilih-varian" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Area gelap di belakang (klik = tutup) -->
    <button type="button" aria-label="Tutup" onclick="tutupVarian()" class="absolute inset-0 bg-black/40"></button>
    <form id="formVarian" onsubmit="event.preventDefault(); tambah()"
        class="relative max-h-[92vh] w-full max-w-[780px] overflow-y-auto rounded-3xl bg-white p-7 shadow-xl">
        <!-- JUDUL -->
        <div class="flex items-start gap-4">
            <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                <img id="varianGambar" src="/images/chikumi-1.png" alt="" class="h-14 w-auto object-contain">
            </div>
            <div class="flex-1">
                <h2 id="varianJudul" class="text-2xl font-bold">Pilih varian</h2>
            </div>
            <button type="button" aria-label="Tutup" onclick="tutupVarian()"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#F7F3EF] text-[#6B645D] hover:bg-[#EDE6DF]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- VARIAN (filling / dip sauce / seasoning) dibangun dari data backend -->
        <div id="bagianVarian"></div>

        <!-- JUMLAH + HARGA + TAMBAH -->
        <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-[#EDE6DF] pt-5">
            <div>
                <label for="kasirJumlah" class="block text-xs text-[#6B645D]">Jumlah</label>
                <input id="kasirJumlah" name="jumlah" type="number" min="1" max="99" value="1"
                    class="mt-1 w-20 rounded-xl border border-[#EDE6DF] bg-white px-3 py-2 text-center text-sm font-semibold outline-none focus:border-[#FF8000]">
            </div>
            <div class="flex-1">
                <p class="text-xs text-[#6B645D]">Harga per porsi</p>
                <p id="hargaPorsi" class="text-2xl font-bold text-[#D96C00]">Rp 0</p>
            </div>
            <button type="submit"
                class="rounded-xl bg-[#FF8000] px-7 py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                Tambah ke Pesanan
            </button>
        </div>
    </form>
</div>
