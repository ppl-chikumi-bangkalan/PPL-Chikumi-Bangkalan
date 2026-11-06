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

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

    @php
        $typeLabel = ['filling' => 'Filling', 'dipping_sauce' => 'Dip Sauce', 'seasoning' => 'Seasoning'];
        $rows = collect();

        foreach ($sizes as $size) {
            $low = $size->stock > 0 && $size->stock <= $size->min_stock;
            $empty = $size->stock <= 0;
            $suggestion = max(0, $size->min_stock - $size->stock);

            $rows->push([
                'name' => trim($size->product->name . ' ' . $size->size_name),
                'category' => 'Kulit',
                'stock' => $size->stock . ' pcs',
                'min' => $size->min_stock . ' pcs',
                'used' => '-',
                'suggestion' => $empty || $low ? $suggestion . ' pcs' : '-',
                'suggestion_class' => $empty ? 'text-red-600' : ($low ? 'text-orange-600' : ''),
                'add' => $empty || $low ? $suggestion : 0,
                'size_id' => $size->id,
                'row_class' => $empty || $low ? 'bg-red-50/60' : '',
                'status' => $empty ? 'Habis' : ($low ? 'Menipis' : 'Aman'),
                'badge' => $empty ? 'bg-red-100 text-red-600' : ($low ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600'),
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
                'add' => 0,
                'size_id' => null,
                'row_class' => $variant->is_available ? '' : 'bg-red-50/60',
                'status' => $variant->is_available ? 'Tersedia' : 'Habis',
                'badge' => $variant->is_available ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600',
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
                <div class="mb-6">
                    <h2 class="text-2xl font-bold sm:text-3xl">Produk & Stok</h2>
                    <p class="mt-2 text-sm text-stone-500">
                        Pantau sisa stok dan rencanakan penambahan stok.
                    </p>
                </div>

                <!-- TAB -->
                <div class="mb-6 flex gap-6 border-b border-orange-100 text-sm">

                    <a href="{{ route('owner.stok') }}"
                        class="border-b-2 border-[#FF8000] pb-3 font-semibold text-[#FF8000]">
                        Stok & Saran
                    </a>

                    <a href="{{ route('owner.produk') }}" class="pb-3 text-stone-500 hover:text-[#FF8000]">
                        Data Produk
                    </a>

                </div>

                <!-- RINGKASAN -->
                <div class="mb-6 grid grid-cols-2 gap-4 xl:grid-cols-4">

                    <div class="rounded-xl border border-orange-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Total Komponen</p>
                        <h3 class="text-3xl font-bold">{{ $summary['total'] }}</h3>
                    </div>

                    <div class="rounded-xl border border-green-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Aman</p>
                        <h3 class="text-3xl font-bold text-green-600">{{ $summary['safe'] }}</h3>
                    </div>

                    <div class="rounded-xl border border-orange-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Menipis</p>
                        <h3 class="text-3xl font-bold text-orange-500">{{ $summary['low'] }}</h3>
                    </div>

                    <div class="rounded-xl border border-red-100 bg-white p-5">
                        <p class="mb-2 text-sm text-stone-500">Habis</p>
                        <h3 class="text-3xl font-bold text-red-500">{{ $summary['empty'] }}</h3>
                    </div>

                </div>

                @if ($summary['low'] + $summary['empty'] > 0)
                <!-- PERINGATAN -->
                <div
                    class="mb-6 flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-600">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.949 3.374H4.646c-1.732 0-2.815-1.874-1.949-3.374L10.05 3.878c.866-1.5 3.034-1.5 3.9 0l7.353 12.248Z" />
                    </svg>

                    <div>
                        <p class="font-semibold">Perhatian! Ada komponen yang perlu ditambah.</p>
                        <p class="mt-1 text-xs">
                            Periksa komponen dengan status menipis atau habis sebelum persiapan penjualan berikutnya.
                        </p>
                    </div>

                </div>
                @endif

                <!-- FILTER -->
                <div class="mb-5 flex flex-wrap gap-2">

                    <button onclick="filterTable('Semua', this)"
                        class="filter-btn rounded-full bg-[#FF8000] px-5 py-2.5 text-sm font-semibold text-white">
                        Semua
                    </button>

                    <button onclick="filterTable('Kulit', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Kulit
                    </button>

                    <button onclick="filterTable('Filling', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Filling
                    </button>

                    <button onclick="filterTable('Dip Sauce', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Dip Sauce
                    </button>

                    <button onclick="filterTable('Seasoning', this)"
                        class="filter-btn rounded-full border border-stone-200 bg-white px-5 py-2.5 text-sm">
                        Seasoning
                    </button>

                </div>

                <!-- TABEL 1 -->
                <section class="mb-6 overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="border-b border-orange-100 p-5">
                        <h3 class="text-base font-bold">Disiapkan Harian - Kulit & Filling</h3>
                        <p class="mt-1 text-xs text-stone-500">
                            Stok produk siap jual dan kebutuhan persiapan harian.
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="bg-[#F8F5F2] text-xs font-semibold text-stone-500">
                                <tr>
                                    <th class="p-4">KOMPONEN</th>
                                    <th class="p-4">SISA STOK</th>
                                    <th class="p-4">MINIMUM</th>
                                    <th class="p-4">TERPAKAI / HARI</th>
                                    <th class="p-4">SARAN BESOK</th>
                                    <th class="p-4">TAMBAH</th>
                                    <th class="p-4">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="stockTable" class="divide-y divide-stone-100">
@forelse ($dailyRows as $row)
                                    <tr data-category="{{ $row['category'] }}" class="{{ $row['row_class'] }}">
                                        <td class="p-4 font-semibold">{{ $row['name'] }}</td>
                                        <td class="p-4">{{ $row['stock'] }}</td>
                                        <td class="p-4">{{ $row['min'] }}</td>
                                        <td class="p-4">{{ $row['used'] }}</td>
                                        <td class="p-4 font-semibold {{ $row['suggestion_class'] }}">{{ $row['suggestion'] }}</td>
                                        <td class="p-4">
                                            @if ($row['size_id'])
                                                <input type="number" min="0" value="{{ $row['add'] }}"
                                                    data-size-id="{{ $row['size_id'] }}" data-default="{{ $row['add'] }}"
                                                    class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                            @else
                                                <input type="number" min="0" value="0" disabled
                                                    class="stock-input w-20 rounded-lg border border-stone-200 bg-stone-50 px-2 py-2 text-center text-sm text-stone-400 outline-none">
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $row['badge'] }}">{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-sm text-stone-400">Belum ada komponen.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- TABEL 2 -->
                <section class="overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="border-b border-orange-100 p-5">
                        <h3 class="text-base font-bold">Restock Bila Menipis - Dip Sauce & Seasoning</h3>
                        <p class="mt-1 text-xs text-stone-500">
                            Persediaan bumbu dan saus untuk kebutuhan penjualan.
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[950px] text-left text-sm">

                            <thead class="bg-[#F8F5F2] text-xs font-semibold text-stone-500">
                                <tr>
                                    <th class="p-4">KOMPONEN</th>
                                    <th class="p-4">SISA STOK</th>
                                    <th class="p-4">MINIMUM</th>
                                    <th class="p-4">TERPAKAI / HARI</th>
                                    <th class="p-4">CUKUP UNTUK</th>
                                    <th class="p-4">TAMBAH</th>
                                    <th class="p-4">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="restockTable" class="divide-y divide-stone-100">
@forelse ($restockRows as $row)
                                    <tr data-category="{{ $row['category'] }}" class="{{ $row['row_class'] }}">
                                        <td class="p-4 font-semibold">{{ $row['name'] }}</td>
                                        <td class="p-4">{{ $row['stock'] }}</td>
                                        <td class="p-4">{{ $row['min'] }}</td>
                                        <td class="p-4">{{ $row['used'] }}</td>
                                        <td class="p-4 font-semibold {{ $row['suggestion_class'] }}">{{ $row['suggestion'] }}</td>
                                        <td class="p-4">
                                            @if ($row['size_id'])
                                                <input type="number" min="0" value="{{ $row['add'] }}"
                                                    data-size-id="{{ $row['size_id'] }}" data-default="{{ $row['add'] }}"
                                                    class="stock-input w-20 rounded-lg border border-orange-200 px-2 py-2 text-center text-sm outline-none focus:border-[#FF8000]">
                                            @else
                                                <input type="number" min="0" value="0" disabled
                                                    class="stock-input w-20 rounded-lg border border-stone-200 bg-stone-50 px-2 py-2 text-center text-sm text-stone-400 outline-none">
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $row['badge'] }}">{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-sm text-stone-400">Belum ada komponen.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SIMPAN STOK -->
                <div
                    class="mt-6 flex flex-col gap-4 rounded-xl border border-orange-100 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-sm font-semibold">
                            <span id="itemCount">0</span> komponen dipilih untuk ditambah
                        </p>
                        <p class="mt-1 text-xs text-stone-500">
                            Periksa jumlah tambahan sebelum menyimpan.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button type="button" id="resetStock"
                            class="px-6 py-3 rounded-lg border border-gray-200
               bg-white text-gray-700 font-semibold
               hover:bg-gray-50 transition">
                            Reset
                        </button>

                        <button type="button" id="saveStock"
                            class="px-6 py-3 rounded-lg bg-[#FF8000]
               text-white font-semibold
               hover:bg-orange-600 transition">
                            Simpan Stok
                        </button>
                    </div>
                </div>

                <p class="py-6 text-center text-xs text-stone-400">
                    Chikumi Admin Dashboard · Data contoh frontend
                </p>

            </div>
        </main>
    </div>

    <script>
        function filterTable(category, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => {
                const on = b === btn;
                b.classList.toggle('bg-[#FF8000]', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('font-semibold', on);
                b.classList.toggle('bg-white', !on);
                b.classList.toggle('border', !on);
                b.classList.toggle('border-stone-200', !on);
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
