<!-- POPUP CATAT PENGELUARAN: dibuka dengan link href="#catat-pengeluaran" -->
<div id="catat-pengeluaran" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-[rgba(31,26,23,0.45)] p-4 target:flex">
    <form id="formPengeluaran" class="w-full max-w-[600px] space-y-[18px] rounded-3xl bg-white p-6 shadow-[0_24px_60px_rgba(0,0,0,0.2)] sm:p-7">

        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-[22px] font-bold">Catat Pengeluaran</h3>
                <p class="text-[13px] text-[#6B645D]">Tercatat sebagai transaksi “Keluar”</p>
            </div>
            <a href="#" aria-label="Tutup" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#F7F3EF] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <div class="grid gap-3.5 sm:grid-cols-2">
            <label class="block space-y-2">
                <span class="text-sm font-medium">Tanggal</span>
                <span class="flex h-[54px] items-center gap-3 rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 transition focus-within:border-2 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <input type="date" name="expense_date" required class="w-full bg-transparent text-[15px] outline-none">
                </span>
            </label>
            <label class="block space-y-2">
                <span class="text-sm font-medium">Nominal (Rp)</span>
                <span class="flex h-[54px] items-center gap-3 rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 transition focus-within:border-2 focus-within:border-[#FF8000]">
                    <span class="text-sm font-semibold text-[#A39B93]">Rp</span>
                    <input type="number" name="amount" min="1" step="1" placeholder="50000" required class="w-full bg-transparent text-[15px] outline-none placeholder:text-[#A39B93]">
                </span>
            </label>
        </div>

        <fieldset class="space-y-2">
            <legend class="mb-2 text-sm font-medium">Kategori</legend>
            <div class="flex flex-wrap gap-2">
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="raw_material" class="sr-only" required>Bahan Baku
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="packaging" class="sr-only">Kemasan
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="operational" class="sr-only">Operasional
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="salary" class="sr-only">Gaji
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="bonus" class="sr-only">Bonus
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="overtime" class="sr-only">Lembur
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="category" value="other" class="sr-only">Lainnya
                </label>
            </div>
        </fieldset>

        <fieldset class="space-y-2">
            <legend class="mb-2 text-sm font-medium">Dibayar dengan</legend>
            <div class="flex flex-wrap gap-2">
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="cash" class="sr-only" checked>Tunai
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="transfer" class="sr-only">Transfer
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="qris" class="sr-only">QRIS
                </label>
            </div>
        </fieldset>

        <label class="block space-y-2">
            <span class="text-sm font-medium">Keterangan</span>
            <span class="flex h-[54px] items-center gap-3 rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 transition focus-within:border-2 focus-within:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                </svg>
                <input type="text" name="title" maxlength="255" placeholder="Contoh: Lembur Rina 2 jam (30 Okt)" required class="w-full bg-transparent text-[15px] outline-none placeholder:text-[#A39B93]">
            </span>
        </label>

        <div class="flex justify-end gap-3 pt-1">
            <a href="#" class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">Batal</a>
            <button type="submit" id="btnSimpanPengeluaran" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Simpan Pengeluaran</button>
        </div>
    </form>
</div>