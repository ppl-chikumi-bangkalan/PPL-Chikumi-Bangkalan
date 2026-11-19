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

    @php
        $sumberLabel = [
            'manual_restock' => 'Tambah stok',
            'pos_transaction' => 'Transaksi kasir',
            'online_order' => 'Pesanan online',
        ];
    @endphp

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
                @forelse ($riwayat as $baris)
                    @php
                        $masuk = $baris->type === 'in';
                        $ukuran = trim(($baris->productSize?->product?->name ?? '-').' '.($baris->productSize?->size_name ?? ''));
                        $catatan = $baris->notes ?: ($baris->reference_id ? '#'.$baris->reference_id : '-');
                    @endphp
                    <tr class="{{ $masuk ? 'riwayat-masuk group-has-[#riwayat-filter-keluar:checked]:hidden' : 'riwayat-keluar group-has-[#riwayat-filter-masuk:checked]:hidden' }}">
                        <td class="p-4 text-stone-500">{{ $baris->created_at?->format('d/m H.i') }}</td>
                        <td class="p-4 font-semibold">{{ $ukuran }}</td>
                        <td class="p-4">
                            @if ($masuk)
                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-600">● Masuk</span>
                            @else
                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-500">● Keluar</span>
                            @endif
                        </td>
                        <td class="p-4 font-semibold {{ $masuk ? 'text-green-600' : 'text-red-500' }}">
                            {{ ($masuk ? '+' : '−').$baris->quantity }} pcs
                        </td>
                        <td class="p-4">{{ $sumberLabel[$baris->source] ?? $baris->source }}</td>
                        <td class="p-4">{{ $baris->user?->name ?? '-' }}</td>
                        <td class="p-4 text-stone-500">{{ $catatan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-sm text-stone-400">Belum ada riwayat stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- KAKI TABEL -->
    <div class="flex items-center justify-between border-t border-stone-100 px-5 py-4">
        <p class="text-xs text-stone-500">
            Menampilkan {{ $riwayat->count() }} dari {{ $riwayatTotal }} riwayat terakhir
        </p>
    </div>
</section>
