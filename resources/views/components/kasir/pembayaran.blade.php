<div id="pembayaran" class="fixed inset-x-0 bottom-0 top-[76px] z-40 hidden overflow-y-auto bg-[#FFF9F2] target:block">
    <form class="group mx-auto grid max-w-[1366px] gap-6 p-7 lg:grid-cols-[500px_minmax(0,1fr)] lg:items-start">

        <!-- KIRI: RINGKASAN PESANAN + METODE -->
        <section class="rounded-3xl border border-[#EDE6DF] bg-white p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold">Ringkasan Pesanan</h2>
                    <p class="text-xs text-[#6B645D]">#CK-0125 · 15.32 WIB</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#D96C00]"></span> Kasir
                </span>
            </div>

            <div class="mt-3.5 space-y-3.5">
                <div class="flex items-center gap-3 rounded-2xl bg-[#F7F3EF] p-2.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-11 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">Reguler · Seasoning</p>
                        <p class="truncate text-xs text-[#6B645D]">Mentaiko · BBQ + Sweetcorn</p>
                    </div>
                    <span class="text-sm text-[#6B645D]">2×</span>
                    <span class="w-[72px] text-right text-sm font-semibold">Rp 40.000</span>
                </div>

                <div class="flex items-center gap-3 rounded-2xl bg-[#F7F3EF] p-2.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-11 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">Reguler · Dip Sauce</p>
                        <p class="truncate text-xs text-[#6B645D]">Cream Cheese · Korean Spicy</p>
                    </div>
                    <span class="text-sm text-[#6B645D]">1×</span>
                    <span class="w-[72px] text-right text-sm font-semibold">Rp 21.000</span>
                </div>
            </div>

            <div class="mt-3.5 border-t border-[#EDE6DF] pt-3.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B645D]">Subtotal (3 pcs)</span>
                    <span>Rp 61.000</span>
                </div>
                <div class="mt-3.5 flex items-center justify-between">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-[28px] font-bold text-[#D96C00]">Rp 61.000</span>
                </div>
            </div>

            <p class="mt-3.5 text-sm font-semibold">Metode Pembayaran</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="cash" id="bayar-tunai" class="sr-only" checked>
                    Tunai
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="non_cash" id="bayar-qris" class="sr-only">
                    QRIS
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                    <input type="radio" name="payment_method" value="non_cash" id="bayar-transfer" class="sr-only">
                    Transfer
                </label>
            </div>
        </section>

        <!-- KANAN: PANEL PEMBAYARAN -->
        <section class="flex min-h-[calc(100vh-76px-56px)] flex-col rounded-3xl border border-[#EDE6DF] bg-white p-7">

            <!-- ===== TUNAI (K3) ===== -->
            <div class="flex flex-1 flex-col group-has-[#bayar-qris:checked]:hidden group-has-[#bayar-transfer:checked]:hidden">
                <h2 class="text-[22px] font-bold">Pembayaran Tunai</h2>

                <label for="uangDiterima" class="mt-4 block text-sm font-medium">Uang Diterima</label>
                <div class="mt-2 flex items-center gap-3 rounded-xl border-2 border-[#EDE6DF] bg-white px-4 py-3 focus-within:border-[#FF8000]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5 shrink-0 text-[#D96C00]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                        <path stroke-linecap="round" d="M9 8.25h6M9 11.25h6M9 14.25h3.75" />
                    </svg>
                    <span class="text-base">Rp</span>
                    <input id="uangDiterima" name="amount_paid" type="text" inputmode="numeric" value="100.000"
                        class="w-full bg-transparent text-base outline-none">
                </div>

                <!-- Nominal cepat -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="uang_cepat" value="61000" class="sr-only">
                        Uang Pas
                    </label>
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="uang_cepat" value="70000" class="sr-only">
                        Rp 70.000
                    </label>
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white has-focus-visible:border-[#FF8000]">
                        <input type="radio" name="uang_cepat" value="100000" class="sr-only" checked>
                        Rp 100.000
                    </label>
                </div>

                <!-- Kembalian -->
                <div class="mt-4 flex items-center justify-between rounded-2xl bg-[#E6F6EC] px-5 py-5">
                    <span class="flex items-center gap-2 text-base font-semibold text-[#1F9D55]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Kembalian
                    </span>
                    <span class="text-[32px] font-bold leading-none text-[#1F9D55]">Rp 39.000</span>
                </div>

                <!-- Keypad -->
                <div class="mt-4 grid grid-cols-3 gap-2.5">
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">1</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">2</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">3</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">4</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">5</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">6</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">7</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">8</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">9</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">000</button>
                    <button type="button" class="h-14 rounded-xl bg-[#F7F3EF] text-xl font-semibold transition hover:bg-[#EDE6DF]">0</button>
                    <button type="button" aria-label="Hapus" class="grid h-14 place-items-center rounded-xl bg-[#F7F3EF] transition hover:bg-[#EDE6DF]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-2.58 4.92-6.374-6.375a1.125 1.125 0 0 1 0-1.59L9.42 4.83c.21-.211.497-.33.795-.33H19.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25h-9.284c-.298 0-.585-.119-.795-.33Z" />
                        </svg>
                    </button>
                </div>

                <div class="mt-auto flex gap-3 pt-6">
                    <a href="#struk"
                        class="grid w-[154px] place-items-center rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                        Cetak Struk
                    </a>
                    <a href="#pembayaran-berhasil"
                        class="grid flex-1 place-items-center rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                        Selesaikan Pembayaran
                    </a>
                </div>
            </div>

            <!-- ===== QRIS STATIS (K3a) ===== -->
            <div class="hidden flex-1 flex-col items-center group-has-[#bayar-qris:checked]:flex">
                <h2 class="text-center text-xl font-semibold">Minta pelanggan scan QRIS di meja kasir</h2>

                <div class="mt-5 w-[320px] rounded-2xl border border-[#EDE6DF] bg-white p-5">
                    <p class="text-center text-sm">
                        <span class="text-lg font-bold">QRIS</span>
                        <span class="ml-1 text-[11px] font-semibold">CHIKUMI BANGKALAN</span>
                    </p>
                    <!-- Ganti dengan gambar QRIS asli milik owner, misal /images/qris-chikumi.png -->
                    <div class="mx-auto mt-3 grid h-[180px] w-[180px] place-items-center rounded-xl bg-[#F7F3EF] text-[#1E1B18]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2"
                            stroke="currentColor" class="h-28 w-28">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-center text-[11px] text-[#A39B93]">NMID: ID10xxxxxxxx · QR sama untuk semua transaksi</p>
                </div>

                <div class="mt-5 w-full rounded-2xl bg-[#FFF3E6] p-5 text-center">
                    <p class="text-xs text-[#6B645D]">Pelanggan mengetik nominal</p>
                    <p class="mt-1 text-[32px] font-bold leading-tight text-[#D96C00]">Rp 61.000</p>
                </div>

                <div class="mt-4 w-full rounded-2xl bg-[#F7F3EF] p-4">
                    <p class="text-sm font-semibold">Cek sebelum konfirmasi</p>
                    <div class="mt-2.5 space-y-2 text-sm">
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="h-4 w-4 accent-[#FF8000]"> Notifikasi dana masuk di aplikasi merchant
                        </label>
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="h-4 w-4 accent-[#FF8000]"> Nominal sesuai Rp 61.000
                        </label>
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="h-4 w-4 accent-[#FF8000]"> Waktu transaksi sesuai
                        </label>
                    </div>
                </div>

                <div class="mt-auto flex w-full gap-3 pt-6">
                    <a href="#"
                        class="grid w-[102px] place-items-center rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                        Batal
                    </a>
                    <a href="#pembayaran-berhasil"
                        class="grid flex-1 place-items-center rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                        Konfirmasi Pembayaran Diterima
                    </a>
                </div>
            </div>

            <!-- ===== TRANSFER (tidak ada di Figma, mengikuti gaya QRIS) ===== -->
            <div class="hidden flex-1 flex-col group-has-[#bayar-transfer:checked]:flex">
                <h2 class="text-[22px] font-bold">Pembayaran Transfer</h2>

                <div class="mt-4 rounded-2xl border border-[#EDE6DF] p-5">
                    <p class="text-xs text-[#6B645D]">Transfer ke rekening</p>
                    <!-- Ganti dengan rekening owner -->
                    <p class="mt-1 text-lg font-semibold">Bank — · No. rekening —</p>
                    <p class="text-sm text-[#6B645D]">a.n. Chikumi Bangkalan</p>
                </div>

                <div class="mt-4 rounded-2xl bg-[#FFF3E6] p-5 text-center">
                    <p class="text-xs text-[#6B645D]">Nominal transfer</p>
                    <p class="mt-1 text-[32px] font-bold leading-tight text-[#D96C00]">Rp 61.000</p>
                </div>

                <div class="mt-4 rounded-2xl bg-[#F7F3EF] p-4">
                    <p class="text-sm font-semibold">Cek sebelum konfirmasi</p>
                    <div class="mt-2.5 space-y-2 text-sm">
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="h-4 w-4 accent-[#FF8000]"> Dana masuk di mutasi rekening
                        </label>
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="h-4 w-4 accent-[#FF8000]"> Nominal sesuai Rp 61.000
                        </label>
                    </div>
                </div>

                <div class="mt-auto flex gap-3 pt-6">
                    <a href="#"
                        class="grid w-[102px] place-items-center rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                        Batal
                    </a>
                    <a href="#pembayaran-berhasil"
                        class="grid flex-1 place-items-center rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                        Konfirmasi Pembayaran Diterima
                    </a>
                </div>
            </div>
        </section>
    </form>
</div>

<!-- ===================== K3c · PEMBAYARAN BERHASIL ===================== -->
<div id="pembayaran-berhasil" class="fixed inset-x-0 bottom-0 top-[76px] z-40 hidden overflow-y-auto bg-[#FFF9F2] target:block">
    <div class="mx-auto grid max-w-[1366px] gap-6 p-7 lg:grid-cols-[500px_minmax(0,1fr)] lg:items-start">

        <!-- KIRI: RINGKASAN PESANAN -->
        <section class="rounded-3xl border border-[#EDE6DF] bg-white p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold">Ringkasan Pesanan</h2>
                    <p class="text-xs text-[#6B645D]">#CK-0125 · 15.32 WIB</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#D96C00]"></span> Kasir
                </span>
            </div>

            <div class="mt-3.5 space-y-3.5">
                <div class="flex items-center gap-3 rounded-2xl bg-[#F7F3EF] p-2.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-11 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">Reguler · Seasoning</p>
                        <p class="truncate text-xs text-[#6B645D]">Mentaiko · BBQ + Sweetcorn</p>
                    </div>
                    <span class="text-sm text-[#6B645D]">2×</span>
                    <span class="w-[72px] text-right text-sm font-semibold">Rp 40.000</span>
                </div>

                <div class="flex items-center gap-3 rounded-2xl bg-[#F7F3EF] p-2.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="/images/chikumi-1.png" alt="" class="h-11 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">Reguler · Dip Sauce</p>
                        <p class="truncate text-xs text-[#6B645D]">Cream Cheese · Korean Spicy</p>
                    </div>
                    <span class="text-sm text-[#6B645D]">1×</span>
                    <span class="w-[72px] text-right text-sm font-semibold">Rp 21.000</span>
                </div>
            </div>

            <div class="mt-3.5 border-t border-[#EDE6DF] pt-3.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B645D]">Subtotal (3 pcs)</span>
                    <span>Rp 61.000</span>
                </div>
                <div class="mt-3.5 flex items-center justify-between">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-[28px] font-bold text-[#D96C00]">Rp 61.000</span>
                </div>
            </div>

            <p class="mt-3.5 text-sm font-semibold">Metode Pembayaran</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Tunai</span>
                <span class="rounded-full border border-[#FF8000] bg-[#FF8000] px-5 py-2.5 text-sm text-white">QRIS</span>
                <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">Transfer</span>
            </div>
        </section>

        <!-- KANAN: BERHASIL -->
        <section class="flex min-h-[calc(100vh-76px-56px)] flex-col items-center justify-center rounded-3xl border border-[#EDE6DF] bg-white p-7">
            <div class="grid h-[104px] w-[104px] place-items-center rounded-full bg-[#1F9D55] text-white shadow-[0_12px_32px_rgba(31,157,85,0.3)]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="h-12 w-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>

            <h2 class="mt-6 text-[28px] font-bold">Pembayaran Berhasil</h2>
            <p class="mt-2 text-sm text-[#6B645D]">QRIS · Rp 61.000 · 15.33 WIB</p>

            <div class="mt-5 w-full max-w-[727px] space-y-3 rounded-2xl bg-[#F7F3EF] p-4 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[#6B645D]">No. Transaksi</span>
                    <span class="font-semibold">#CK-0125</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#6B645D]">Ref. Pembayaran</span>
                    <span class="font-semibold">QR-8F3K2-0091</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#6B645D]">Status</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span> Lunas
                    </span>
                </div>
            </div>

            <p class="mt-4 text-xs text-[#6B645D]">Stok berkurang otomatis &amp; transaksi tersimpan di Riwayat</p>

            <div class="mt-4 flex w-full max-w-[727px] gap-3">
                <a href="#struk"
                    class="grid flex-1 place-items-center rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                    Cetak Struk
                </a>
                <a href="#"
                    class="grid flex-1 place-items-center rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                    Transaksi Baru
                </a>
            </div>
        </section>
    </div>
</div>