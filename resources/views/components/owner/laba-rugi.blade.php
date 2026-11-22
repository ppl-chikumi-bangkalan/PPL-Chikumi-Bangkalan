<!-- KARTU LABA RUGI (data contoh Oktober 2026): backend isi dari perhitungan laba -->
<section id="labaRugi" class="overflow-hidden rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white">
    <div class="px-5 py-4">
        <h3 class="text-base font-semibold">Laba Rugi · <span id="labaPeriode">Oktober 2026</span></h3>
        <p class="mt-0.5 text-xs text-[#6B645D]">Laba kotor = omzet − HPP · Laba bersih = laba kotor − operasional − karyawan</p>
    </div>

    <div class="space-y-1 px-5 pb-5 pt-1">
        <div class="flex items-center justify-between py-2 text-[15px] font-semibold">
            <span>Omzet (<span id="labaJumlahTransaksi">412</span> transaksi)</span>
            <span id="labaOmzet">Rp 8.750.000</span>
        </div>

        <!-- HPP -->
        <div class="flex items-center justify-between py-2 pl-5 text-[13px]">
            <span class="text-[#6B645D]">Bahan Baku</span>
            <span class="font-medium text-[#E03131]">− Rp 3.100.000</span>
        </div>
        <div class="flex items-center justify-between py-2 pl-5 text-[13px]">
            <span class="text-[#6B645D]">Kemasan</span>
            <span class="font-medium text-[#E03131]">− Rp 450.000</span>
        </div>

        <div class="flex items-center justify-between rounded-xl bg-[#FFF3E6] px-3.5 py-3 text-[15px] font-bold text-[#D96C00]">
            <span>LABA KOTOR</span>
            <span id="labaKotor">Rp 5.200.000</span>
        </div>

        <!-- OPERASIONAL & KARYAWAN -->
        <div class="flex items-center justify-between py-2 pl-5 text-[13px]">
            <span class="text-[#6B645D]">Operasional (gas, listrik, sewa lapak)</span>
            <span class="font-medium text-[#E03131]">− Rp 850.000</span>
        </div>
        <div class="flex items-center justify-between py-2 pl-5 text-[13px]">
            <span class="text-[#6B645D]">Gaji karyawan</span>
            <span class="font-medium text-[#E03131]">− Rp 700.000</span>
        </div>
        <div class="flex items-center justify-between py-2 pl-5 text-[13px]">
            <span class="text-[#6B645D]">Bonus &amp; lembur</span>
            <span class="font-medium text-[#E03131]">− Rp 100.000</span>
        </div>

        <div class="flex items-center justify-between rounded-[14px] bg-[#FF8000] px-[18px] py-4 font-bold text-white">
            <span class="text-base">LABA BERSIH</span>
            <span id="labaBersih" class="text-2xl">Rp 3.550.000</span>
        </div>
    </div>
</section>