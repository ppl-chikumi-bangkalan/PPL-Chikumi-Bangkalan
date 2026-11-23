<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Stok - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    @php
        $typeLabel = ['filling' => 'Filling', 'dipping_sauce' => 'Dip Sauce', 'seasoning' => 'Seasoning'];
        $rows = collect();

        foreach ($sizes as $size) {
            $low = $size->stock > 0 && $size->stock <= $size->min_stock;
            $empty = $size->stock <= 0;
            $rata = (float) ($rataHarian[$size->id] ?? 0);
            $suggestion = \App\Support\MovingAverage::saran($size->stock, $size->min_stock, $rata);
            $cukup = \App\Support\MovingAverage::cukupHari($size->stock, $rata);

            $rows->push([
                'name' => trim($size->product->name . ' ' . $size->size_name),
                'category' => 'Kulit',
                'stock' => $size->stock . ' pcs',
                'min' => $size->min_stock . ' pcs',
                'used' => $rata > 0 ? number_format($rata, 1, ',', '.') . ' pcs' : '-',
                'suggestion' => $suggestion > 0 ? $suggestion . ' pcs' : '-',
                'suggestion_class' => $empty || $low ? 'text-[#E03131]' : ($suggestion > 0 ? 'text-[#D96C00]' : ''),
                'cukup' => $cukup === null ? '-' : $cukup . ' hari',
                'add' => $suggestion,
                'size_id' => $size->id,
                'row_class' => $empty || $low ? 'bg-[#FDECEC] text-[#E03131]' : '',
                'status' => $empty ? 'Habis' : ($low ? 'Minimum' : 'Aman'),
                'badge' => $empty || $low ? 'bg-[#FDECEC] text-[#E03131]' : 'bg-[#E6F6EC] text-[#1F9D55]',
            ]);
        }

        foreach ($variants as $variant) {
            $label = $typeLabel[$variant->type] ?? $variant->type;

            $rows->push([
                'name' => $variant->name,
                'category' => $label,
                'stock' => '-',
                'min' => '-',
                'used' => '-',
                'suggestion' => '-',
                'suggestion_class' => '',
                'cukup' => '-',
                'add' => 0,
                'size_id' => null,
                'row_class' => $variant->is_available ? '' : 'bg-[#FDECEC] text-[#E03131]',
                'status' => $variant->is_available ? 'Tersedia' : 'Habis',
                'badge' => $variant->is_available ? 'bg-[#E6F6EC] text-[#1F9D55]' : 'bg-[#FDECEC] text-[#E03131]',
            ]);
        }

        $dailyRows = $rows->whereIn('category', ['Kulit', 'Filling']);
        $restockRows = $rows->whereIn('category', ['Dip Sauce', 'Seasoning']);
    @endphp

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        {{-- SIDEBAR --}}
        @include('components.owner.sidebar')

        <!-- MAIN -->
        <main class="min-w-0 flex-1">

            <!-- HEADER MOBILE -->
            @include('components.owner.mobile-header')

            <div class="mx-auto max-w-[1500px] p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="mb-5">
                    <h2 class="text-2xl font-bold sm:text-[28px]">Produk & Stok</h2>
                    <p class="mt-1 text-sm text-[#6B645D]">
                        Pantau sisa stok dan rencanakan penambahan stok.
                    </p>
                </div>

                <!-- TAB -->
                <div class="mb-5 flex gap-6 border-b-[1.5px] border-[#EDE6DF] text-[15px]">

                    <a href="{{ route('owner.stok') }}"
                        class="-mb-[1.5px] border-b-[3px] border-[#FF8000] pb-3 font-semibold text-[#D96C00]">
                        Stok & Saran
                    </a>

                    <a href="{{ route('owner.produk') }}" class="pb-3 font-medium text-[#6B645D] hover:text-[#FF8000]">
                        Data Produk
                    </a>

                </div>

                <!-- RINGKASAN -->
                <div class="mb-5 grid grid-cols-2 gap-3.5 md:grid-cols-4">

                    <div class="flex flex-col gap-2 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
                        <span class="grid h-[38px] w-[38px] place-items-center rounded-xl bg-[#FFF3E6] text-[#D96C00]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                        </span>
                        <h3 class="text-[26px] font-bold leading-tight text-[#D96C00]">{{ $summary['total'] }}</h3>
                        <p class="text-xs text-[#6B645D]">Total Komponen</p>
                    </div>

                    <div class="flex flex-col gap-2 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
                        <span class="grid h-[38px] w-[38px] place-items-center rounded-xl bg-[#E6F6EC] text-[#1F9D55]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <h3 class="text-[26px] font-bold leading-tight text-[#1F9D55]">{{ $summary['safe'] }}</h3>
                        <p class="text-xs text-[#6B645D]">Aman</p>
                    </div>

                    <div class="flex flex-col gap-2 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
                        <span class="grid h-[38px] w-[38px] place-items-center rounded-xl bg-[#FDECEC] text-[#E03131]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.949 3.374H4.646c-1.732 0-2.815-1.874-1.949-3.374L10.05 3.878c.866-1.5 3.034-1.5 3.9 0l7.353 12.248ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                        </span>
                        <h3 class="text-[26px] font-bold leading-tight text-[#E03131]">{{ $summary['low'] }}</h3>
                        <p class="text-xs text-[#6B645D]">Menipis</p>
                    </div>

                    <div class="flex flex-col gap-2 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
                        <span class="grid h-[38px] w-[38px] place-items-center rounded-xl bg-[#FDECEC] text-[#E03131]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75M12 15.75h.007v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </span>
                        <h3 class="text-[26px] font-bold leading-tight text-[#E03131]">{{ $summary['empty'] }}</h3>
                        <p class="text-xs text-[#6B645D]">Habis</p>
                    </div>

                </div>

                @if ($summary['low'] + $summary['empty'] > 0)
                <!-- PERINGATAN -->
                <div
                    class="mb-5 flex items-center gap-3.5 rounded-2xl bg-[#FDECEC] p-4 text-[#E03131]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.949 3.374H4.646c-1.732 0-2.815-1.874-1.949-3.374L10.05 3.878c.866-1.5 3.034-1.5 3.9 0l7.353 12.248Z" />
                    </svg>

                    <div>
                        <p class="text-[15px] font-semibold">{{ $summary['empty'] }} komponen habis &amp; {{ $summary['low'] }} di bawah minimum</p>
                        <p class="mt-0.5 text-[13px] text-[#6B645D]">
                            Isi kolom Tambah pada baris merah, lalu tekan Simpan Stok.
                        </p>
                    </div>

                </div>
                @endif

                <!-- FILTER -->
                <div class="mb-5 flex flex-wrap gap-2.5">

                    <button onclick="filterTable('Semua', this)"
                        class="filter-btn rounded-full border-[1.5px] border-[#FF8000] bg-[#FF8000] px-5 py-2.5 text-sm font-semibold text-white">
                        Semua
                    </button>

                    <button onclick="filterTable('Kulit', this)"
                        class="filter-btn rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium">
                        Kulit
                    </button>

                    <button onclick="filterTable('Filling', this)"
                        class="filter-btn rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium">
                        Filling
                    </button>

                    <button onclick="filterTable('Dip Sauce', this)"
                        class="filter-btn rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium">
                        Dip Sauce
                    </button>

                    <button onclick="filterTable('Seasoning', this)"
                        class="filter-btn rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium">
                        Seasoning
                    </button>

                </div>

                <!-- TABEL 1 -->
                <section class="mb-5 overflow-hidden rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white">

                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Disiapkan Harian · Kulit & Filling</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">
                            Rata-rata dari penjualan 3 minggu terakhir · saran untuk kebutuhan 7 hari ke depan
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="border-t border-[#EDE6DF] bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">KOMPONEN</th>
                                    <th class="px-5 py-2.5">SISA</th>
                                    <th class="px-5 py-2.5">MIN.</th>
                                    <th class="px-5 py-2.5">TERPAKAI/HARI</th>
                                    <th class="px-5 py-2.5">SARAN RESTOCK</th>
                                    <th class="px-5 py-2.5">TAMBAH</th>
                                    <th class="px-5 py-2.5">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="stockTable" class="divide-y divide-[#EDE6DF] border-t border-[#EDE6DF] text-[13px]">
@forelse ($dailyRows as $row)
                                    <tr data-category="{{ $row['category'] }}" class="{{ $row['row_class'] }}">
                                        <td class="px-5 py-2.5 {{ $row['row_class'] ? 'font-semibold' : '' }}">{{ $row['name'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['stock'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['min'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['used'] }}</td>
                                        <td class="px-5 py-2.5 font-semibold {{ $row['suggestion_class'] }}">{{ $row['suggestion'] }}</td>
                                        <td class="px-5 py-2.5">
                                            @if ($row['size_id'])
                                                <input type="number" min="0" placeholder="0" value="{{ $row['add'] }}"
                                                    title="Bisa diisi manual bila ingin menambah lebih banyak dari saran"
                                                    data-size-id="{{ $row['size_id'] }}" data-default="{{ $row['add'] }}"
                                                    class="stock-input h-8 w-16 rounded-lg border-[1.5px] {{ $row['add'] > 0 ? 'border-[#FF8000] font-semibold text-[#1E1B18]' : 'border-[#EDE6DF] text-[#A39B93]' }} bg-white text-center text-[13px] outline-none focus:border-[#FF8000]">
                                            @else
                                                <span class="text-xs text-[#A39B93]">tidak dilacak</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $row['badge'] }}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-sm text-[#A39B93]">Belum ada komponen.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- TABEL 2 -->
                <section class="overflow-hidden rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white">

                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Restock Bila Menipis · Dip Sauce & Seasoning</h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">
                            Tahan lama — stok varian belum dicatat, status mengikuti ketersediaan di Data Produk
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="border-t border-[#EDE6DF] bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">KOMPONEN</th>
                                    <th class="px-5 py-2.5">SISA</th>
                                    <th class="px-5 py-2.5">MIN.</th>
                                    <th class="px-5 py-2.5">TERPAKAI/HARI</th>
                                    <th class="px-5 py-2.5">CUKUP UNTUK</th>
                                    <th class="px-5 py-2.5">TAMBAH</th>
                                    <th class="px-5 py-2.5">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="restockTable" class="divide-y divide-[#EDE6DF] border-t border-[#EDE6DF] text-[13px]">
@forelse ($restockRows as $row)
                                    <tr data-category="{{ $row['category'] }}" class="{{ $row['row_class'] }}">
                                        <td class="px-5 py-2.5 {{ $row['row_class'] ? 'font-semibold' : '' }}">{{ $row['name'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['stock'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['min'] }}</td>
                                        <td class="px-5 py-2.5">{{ $row['used'] }}</td>
                                        <td class="px-5 py-2.5 font-semibold {{ $row['suggestion_class'] }}">{{ $row['cukup'] }}</td>
                                        <td class="px-5 py-2.5">
                                            @if ($row['size_id'])
                                                <input type="number" min="0" placeholder="0" value="{{ $row['add'] }}"
                                                    title="Bisa diisi manual bila ingin menambah lebih banyak dari saran"
                                                    data-size-id="{{ $row['size_id'] }}" data-default="{{ $row['add'] }}"
                                                    class="stock-input h-8 w-16 rounded-lg border-[1.5px] {{ $row['add'] > 0 ? 'border-[#FF8000] font-semibold text-[#1E1B18]' : 'border-[#EDE6DF] text-[#A39B93]' }} bg-white text-center text-[13px] outline-none focus:border-[#FF8000]">
                                            @else
                                                <span class="text-xs text-[#A39B93]">tidak dilacak</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5">
                                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $row['badge'] }}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-sm text-[#A39B93]">Belum ada komponen.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SIMPAN STOK -->
                <div
                    class="mt-5 flex flex-col gap-3 rounded-[18px] border-[1.5px] border-[#EDE6DF] bg-white px-5 py-4 shadow-[0_8px_24px_rgba(115,56,0,0.08)] sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-[15px] font-semibold">
                            <span id="itemCount">0</span> komponen akan ditambah
                        </p>
                        <p class="mt-0.5 text-xs text-[#6B645D]">
                            Angka Tambah terisi dari saran — ubah bila perlu
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button type="button" id="resetStock"
                            class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
                            Reset
                        </button>

                        <button type="button" id="saveStock"
                            class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">
                            Simpan Stok
                        </button>
                    </div>
                </div>

                <!-- RIWAYAT STOK -->
                @include('components.owner.riwayat-stok')


            </div>
        </main>
    </div>

    <script>
        function filterTable(category, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => {
                const on = b === btn;
                b.classList.toggle('bg-[#FF8000]', on);
                b.classList.toggle('border-[#FF8000]', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('font-semibold', on);
                b.classList.toggle('bg-white', !on);
                b.classList.toggle('border-[#EDE6DF]', !on);
                b.classList.toggle('font-medium', !on);
            });

            document.querySelectorAll('tbody tr[data-category]').forEach(tr => {
                tr.classList.toggle('hidden', category !== 'Semua' && tr.dataset.category !== category);
            });
        }

        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const stockInputs = () => [...document.querySelectorAll('.stock-input[data-size-id]')];
        const countSelected = () => stockInputs().filter(input => Number(input.value) > 0).length;

        function updateCount() {
            document.getElementById('itemCount').textContent = countSelected();
        }

        stockInputs().forEach(input => input.addEventListener('input', updateCount));

        document.getElementById('resetStock').addEventListener('click', () => {
            stockInputs().forEach(input => input.value = input.dataset.default);
            updateCount();
        });

        document.getElementById('saveStock').addEventListener('click', async () => {
            const pending = stockInputs().filter(input => Number(input.value) > 0);
            if (!pending.length) return alert('Isi jumlah stok yang ingin ditambahkan.');

            const button = document.getElementById('saveStock');
            button.disabled = true;

            try {
                for (const input of pending) {
                    const res = await fetch(`/owner/api/ukuran/${input.dataset.sizeId}/stok`, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({ type: 'in', quantity: Number(input.value) }),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan stok.');
                }
                window.location.reload();
            } catch (err) {
                alert(err.message);
            } finally {
                button.disabled = false;
            }
        });

        updateCount();
    </script>
</body>
</html>