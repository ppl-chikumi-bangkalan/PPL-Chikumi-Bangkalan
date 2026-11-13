<section class="group mt-6 overflow-hidden rounded-xl border border-orange-100 bg-white">

    <!-- JUDUL + FILTER -->
    <div class="flex flex-col gap-3 border-b border-orange-100 p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-bold">Riwayat Stok</h3>
            <p class="mt-1 text-xs text-stone-500">
                Catatan stok masuk (tambah stok) &amp; keluar (penjualan kasir / pesanan online)
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <label class="cursor-pointer rounded-full border border-stone-200 bg-white px-4 py-2 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                <input type="radio" name="filter_riwayat" value="semua" id="riwayat-semua" class="sr-only" checked>
                Semua
            </label>
            <label class="cursor-pointer rounded-full border border-stone-200 bg-white px-4 py-2 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                <input type="radio" name="filter_riwayat" value="masuk" id="riwayat-filter-masuk" class="sr-only">
                Masuk
            </label>
            <label class="cursor-pointer rounded-full border border-stone-200 bg-white px-4 py-2 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white has-focus-visible:border-[#FF8000]">
                <input type="radio" name="filter_riwayat" value="keluar" id="riwayat-filter-keluar" class="sr-only">
                Keluar
            </label>
        </div>
    </div>

    <!-- TABEL RIWAYAT -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">

            <thead class="bg-[#F8F5F2] text-xs font-semibold text-stone-500">
                <tr>
                    <th class="p-4">WAKTU</th>
                    <th class="p-4">UKURAN</th>
                    <th class="p-4">JENIS</th>
                    <th class="p-4">JUMLAH</th>
                    <th class="p-4">SUMBER</th>
                    <th class="p-4">OLEH</th>
                    <th class="p-4">CATATAN</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-stone-100">

                <!-- Masuk: tambah stok manual -->
                <tr class="riwayat-masuk group-has-[#riwayat-filter-keluar:checked]:hidden">
                    <td class="p-4 text-stone-500">13/11 08.15</td>
                    <td class="p-4 font-semibold">Regular</td>
                    <td class="p-4">
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-600">● Masuk</span>
                    </td>
                    <td class="p-4 font-semibold text-green-600">+20 pcs</td>
                    <td class="p-4">Tambah stok</td>
                    <td class="p-4">Owner Chikumi</td>
                    <td class="p-4 text-stone-500">Persiapan buka 15.00</td>
                </tr>

                <tr class="riwayat-masuk group-has-[#riwayat-filter-keluar:checked]:hidden">
                    <td class="p-4 text-stone-500">13/11 08.14</td>
                    <td class="p-4 font-semibold">Large</td>
                    <td class="p-4">
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-600">● Masuk</span>
                    </td>
                    <td class="p-4 font-semibold text-green-600">+10 pcs</td>
                    <td class="p-4">Tambah stok</td>
                    <td class="p-4">Owner Chikumi</td>
                    <td class="p-4 text-stone-500">-</td>
                </tr>

                <!-- Keluar: transaksi kasir -->
                <tr class="riwayat-keluar group-has-[#riwayat-filter-masuk:checked]:hidden">
                    <td class="p-4 text-stone-500">12/11 20.45</td>
                    <td class="p-4 font-semibold">Regular</td>
                    <td class="p-4">
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-500">● Keluar</span>
                    </td>
                    <td class="p-4 font-semibold text-red-500">−3 pcs</td>
                    <td class="p-4">Transaksi kasir</td>
                    <td class="p-4">Kasir Chikumi</td>
                    <td class="p-4 text-stone-500">#CK-0125</td>
                </tr>

                <!-- Keluar: pesanan online -->
                <tr class="riwayat-keluar group-has-[#riwayat-filter-masuk:checked]:hidden">
                    <td class="p-4 text-stone-500">12/11 19.30</td>
                    <td class="p-4 font-semibold">Regular</td>
                    <td class="p-4">
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-500">● Keluar</span>
                    </td>
                    <td class="p-4 font-semibold text-red-500">−2 pcs</td>
                    <td class="p-4">Pesanan online</td>
                    <td class="p-4">Pelanggan Contoh</td>
                    <td class="p-4 text-stone-500">#ORD-0018</td>
                </tr>

                <tr class="riwayat-keluar group-has-[#riwayat-filter-masuk:checked]:hidden">
                    <td class="p-4 text-stone-500">12/11 18.05</td>
                    <td class="p-4 font-semibold">Large</td>
                    <td class="p-4">
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-500">● Keluar</span>
                    </td>
                    <td class="p-4 font-semibold text-red-500">−1 pcs</td>
                    <td class="p-4">Transaksi kasir</td>
                    <td class="p-4">Kasir Chikumi</td>
                    <td class="p-4 text-stone-500">#CK-0119</td>
                </tr>

                <tr class="riwayat-masuk group-has-[#riwayat-filter-keluar:checked]:hidden">
                    <td class="p-4 text-stone-500">12/11 08.00</td>
                    <td class="p-4 font-semibold">Regular</td>
                    <td class="p-4">
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-600">● Masuk</span>
                    </td>
                    <td class="p-4 font-semibold text-green-600">+15 pcs</td>
                    <td class="p-4">Tambah stok</td>
                    <td class="p-4">Owner Chikumi</td>
                    <td class="p-4 text-stone-500">-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- KAKI TABEL -->
    <div class="flex flex-col items-center justify-between gap-3 border-t border-stone-100 px-5 py-4 sm:flex-row">
        <p class="text-xs text-stone-500">Menampilkan 6 riwayat terakhir</p>

        <div class="flex gap-1.5">
            <button type="button" class="h-8 w-8 rounded-lg border border-stone-200 bg-white text-sm">‹</button>
            <button type="button" class="h-8 w-8 rounded-lg bg-[#FF8000] text-sm font-semibold text-white">1</button>
            <button type="button" class="h-8 w-8 rounded-lg border border-stone-200 bg-white text-sm">2</button>
            <button type="button" class="h-8 w-8 rounded-lg border border-stone-200 bg-white text-sm">›</button>
        </div>
    </div>
</section>