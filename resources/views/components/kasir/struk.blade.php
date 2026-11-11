<div id="struk" class="fixed inset-0 z-50 hidden items-center justify-center p-4 target:flex">

    <!-- Area gelap di belakang (klik = kembali ke Pembayaran Berhasil) -->
    <a href="#pembayaran-berhasil" aria-label="Tutup struk" class="absolute inset-0 bg-black/40"></a>

    <div class="relative flex max-h-[92vh] flex-col items-center gap-4">

        <!-- KERTAS STRUK -->
        <div id="kertasStruk" class="w-[302px] overflow-y-auto bg-white px-5 py-6 font-mono text-[12px] leading-relaxed text-[#1E1B18] shadow-xl">

            <!-- Kepala struk -->
            <div class="text-center">
                <p class="font-['Poppins'] text-[26px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</p>
                <p class="font-['Poppins'] text-[9px] font-semibold tracking-wide">JAPANESE SNACK</p>
                <p class="mt-1.5">Pecinan · depan Toko Emas Wahyu Redjo</p>
                <p>Bangkalan</p>
            </div>

            <div class="my-3 border-t border-dashed border-[#A39B93]"></div>

            <!-- Info transaksi -->
            <div class="space-y-0.5">
                <div class="flex justify-between"><span>No.</span><span id="strukNomor">—</span></div>
                <div class="flex justify-between"><span>Tanggal</span><span id="strukTanggal">—</span></div>
                <div class="flex justify-between"><span>Kasir</span><span id="strukKasir">—</span></div>
                <div class="flex justify-between"><span>Pelanggan</span><span id="strukPelanggan">—</span></div>
            </div>

            <div class="my-3 border-t border-dashed border-[#A39B93]"></div>

            <!-- Daftar item -->
            <div id="strukItem" class="space-y-2.5"></div>

            <div class="my-3 border-t border-dashed border-[#A39B93]"></div>

            <!-- Total -->
            <div class="space-y-0.5">
                <div class="flex justify-between"><span id="strukSubtotalQty">Subtotal (0 pcs)</span><span id="strukSubtotal">0</span></div>
                <div class="flex justify-between text-[14px] font-bold"><span>TOTAL</span><span id="strukTotal">Rp 0</span></div>
            </div>

            <div class="my-3 border-t border-dashed border-[#A39B93]"></div>

            <!-- Pembayaran -->
            <div class="space-y-0.5">
                <div class="flex justify-between"><span>Metode</span><span id="strukMetode">—</span></div>
                <div class="flex justify-between"><span>Dibayar</span><span id="strukDibayar">0</span></div>
                <div class="flex justify-between"><span>Kembalian</span><span id="strukKembalian">0</span></div>
                <div class="flex justify-between"><span>Status</span><span id="strukStatus" class="font-bold">LUNAS</span></div>
            </div>

            <div class="my-3 border-t border-dashed border-[#A39B93]"></div>

            <!-- Kaki struk -->
            <div class="text-center">
                <p class="font-bold">Terima kasih!</p>
                <p>Selamat menikmati Chikumi</p>
            </div>
        </div>

        <!-- TOMBOL -->
        <div class="flex w-[302px] gap-2.5">
            <a href="#pembayaran-berhasil"
                class="grid flex-1 place-items-center rounded-xl border border-[#EDE6DF] bg-white py-3.5 text-sm font-semibold transition hover:border-[#A39B93]">
                Kembali
            </a>
            <button type="button" onclick="cetakStruk()"
                class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#FF8000] py-3.5 text-sm font-semibold text-white transition hover:bg-[#D96C00]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                Cetak
            </button>
        </div>
    </div>
</div>
