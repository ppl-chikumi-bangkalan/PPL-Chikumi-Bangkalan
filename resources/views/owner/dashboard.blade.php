@php
    $apiBase = auth()->check() ? '/owner/api' : '/frontend/owner/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-['Poppins'] text-stone-800">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        {{-- SIDEBAR --}}
        @include('components.owner.sidebar')

        <!-- MAIN -->
        <main class="min-w-0 flex-1">

            <!-- HEADER MOBILE -->
            @include('components.owner.mobile-header')

            <div class="mx-auto max-w-[1500px] space-y-6 p-4 sm:p-6 lg:p-8">

                <!-- JUDUL DAN FILTER WAKTU -->
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-2xl font-bold sm:text-3xl">Dashboard</h2>
                        <p class="mt-2 text-sm text-stone-500">Kondisi usaha Chikumi</p>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <button data-period="hari"
                            class="period-btn rounded-full border border-stone-200 bg-white px-3 py-1.5 text-xs">
                            Hari Ini
                        </button>

                        <button data-period="minggu"
                            class="period-btn rounded-full border border-stone-200 bg-white px-3 py-1.5 text-xs">
                            Minggu Ini
                        </button>

                        <button data-period="bulan"
                            class="period-btn rounded-full bg-[#FF8000] px-3 py-1.5 text-xs font-semibold text-white">
                            Bulan Ini
                        </button>
                    </div>
                </div>

                <!-- KARTU RINGKASAN -->
                <div class="grid grid-cols-2 gap-2 xl:grid-cols-4">

                    <!-- OMZET -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 18V5.25m0 12.75h16.5M7.5 15l3.75-4.5 3 2.25 4.5-6" />
                            </svg>
                        </div>
                        <p class="text-sm text-stone-500">Omzet</p>
                        <h3 id="cardOmzet" class="mt-1 text-xl font-bold">Rp 0</h3>
                        <p id="cardOmzetSub" class="mt-1 text-xs text-stone-400">Kasir · Online</p>
                    </div>

                    <!-- PENGELUARAN -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 6.75h7.5m-7.5 3h7.5m-7.5 3h4.5M6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75Z" />
                            </svg>
                        </div>
                        <p class="text-sm text-stone-500">Pengeluaran</p>
                        <h3 id="cardPengeluaran" class="mt-1 text-xl font-bold">Rp 0</h3>
                        <p class="mt-1 text-xs text-stone-400">Total biaya usaha</p>
                    </div>

                    <!-- LABA -->
                    <div class="rounded-xl border border-[#FF8000] bg-[#FF8000] p-3 text-white">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-black/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9 12.75 2.25 2.25 4.5-5.25m5.25 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <p class="text-sm text-white/80">Laba Bersih*</p>
                        <h3 id="cardLaba" class="mt-1 text-xl font-bold">Rp 0</h3>
                        <p id="cardMargin" class="mt-1 text-xs text-white/80">Margin 0%</p>
                    </div>

                    <!-- TRANSAKSI -->
                    <div class="rounded-xl border border-orange-100 bg-white p-3">
                        <div class="mb-2 grid h-7 w-7 place-items-center rounded-lg bg-orange-50 text-[#FF8000]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5h18l-1.5 12h-15L3 7.5Zm4.5 0 4.5-4.5 4.5 4.5M9 12h.008v.008H9V12Zm6 0h.008v.008H15V12Z" />
                            </svg>
                        </div>
                        <p class="text-sm text-stone-500">Transaksi</p>
                        <h3 id="cardTransaksi" class="mt-1 text-xl font-bold">0</h3>
                        <p id="cardTransaksiSub" class="mt-1 text-xs text-stone-400">Kasir 0 · Online 0</p>
                    </div>
                </div>

                <!-- GRAFIK -->
                <section class="rounded-xl border border-orange-100 bg-white p-4">

                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold">Omzet vs Pengeluaran</h3>
                            <p id="grafikSub" class="text-xs text-stone-400">
                                Perbandingan per minggu
                            </p>
                        </div>

                        <div class="flex gap-3 text-xs">
                            <span class="flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-[#FF8000]"></span>
                                Omzet
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-stone-500"></span>
                                Pengeluaran
                            </span>
                        </div>
                    </div>

                    <!-- BAR CHART -->
                    <div id="grafikBars" class="flex h-36 items-end justify-around gap-3 border-b border-stone-100 px-2"></div>
                </section>

                <!-- KONDISI STOK -->
                <section class="overflow-hidden rounded-xl border border-orange-100 bg-white">

                    <div class="flex items-center justify-between gap-2 p-4">
                        <div>
                            <h3 class="text-base font-bold">Kondisi Stok</h3>
                            <p class="text-xs text-stone-400">
                                Komponen yang perlu disiapkan / restock
                            </p>
                        </div>

                        <a href="{{ route('owner.produk') }}"
                            class="text-xs font-semibold text-[#FF8000] hover:underline">
                            Lihat Produk & Stok →
                        </a>
                    </div>

                    <!-- RINGKASAN STOK -->
                    <div class="grid grid-cols-3 gap-2 px-4 pb-3">
                        <div class="rounded-lg bg-green-50 p-2">
                            <h4 id="stokAman" class="text-sm font-bold text-green-600">0 komponen</h4>
                            <p class="text-xs text-stone-500">Aman</p>
                        </div>
                        <div class="rounded-lg bg-red-50 p-2">
                            <h4 id="stokMenipis" class="text-sm font-bold text-red-500">0 komponen</h4>
                            <p class="text-xs text-stone-500">Di bawah minimum</p>
                        </div>
                        <div class="rounded-lg bg-rose-100 p-2">
                            <h4 id="stokHabis" class="text-sm font-bold text-rose-600">0 komponen</h4>
                            <p class="text-xs text-stone-500">Habis</p>
                        </div>
                    </div>
                    <!-- TABEL STOK -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px] text-left text-sm">

                            <thead class="bg-[#F8F5F2] text-stone-500">
                                <tr>
                                    <th class="p-3">KOMPONEN</th>
                                    <th class="p-3">SISA</th>
                                    <th class="p-3">SARAN</th>
                                    <th class="p-3">STATUS</th>
                                </tr>
                            </thead>

                            <tbody id="tabelStok" class="divide-y divide-red-100"></tbody>
                        </table>
                    </div>
                </section>

                <p class="py-2 text-center text-xs text-stone-400">
                    Chikumi Owner Dashboard
                </p>

                <p class="text-center text-xs text-stone-400">
                    *Laba bersih pada contoh ini dihitung dari omzet dikurangi pengeluaran.
                </p>

            </div>
        </main>
    </div>

    <script>
        const API_BASE = @json($apiBase);
        const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
        const el = id => document.getElementById(id);

        async function ambil(path) {
            const response = await fetch(API_BASE + path, {headers: {'Accept': 'application/json'}});
            if (!response.ok) throw new Error('Gagal memuat data.');
            return response.json();
        }

        function renderRingkasan(data) {
            el('cardOmzet').textContent = rp(data.omzet);
            el('cardOmzetSub').textContent = `Kasir ${rp(data.omzet_kasir)} · Online ${rp(data.omzet_online)}`;
            el('cardPengeluaran').textContent = rp(data.pengeluaran);
            el('cardLaba').textContent = rp(data.laba);
            el('cardMargin').textContent = `Margin ${data.margin}%`;
            el('cardTransaksi').textContent = data.transaksi.total;
            el('cardTransaksiSub').textContent = `Kasir ${data.transaksi.kasir} · Online ${data.transaksi.online}`;
            renderGrafik(data.grafik);
        }

        function renderGrafik(grafik) {
            const max = Math.max(1, ...grafik.omzet, ...grafik.pengeluaran);
            el('grafikBars').innerHTML = grafik.label.map((label, i) => `
                <div class="flex h-full flex-1 flex-col items-center justify-end">
                    <div class="flex h-full items-end gap-1">
                        <div class="w-3 rounded-t bg-[#FF8000]" style="height: ${Math.round(grafik.omzet[i] / max * 100)}%"></div>
                        <div class="w-3 rounded-t bg-stone-500" style="height: ${Math.round(grafik.pengeluaran[i] / max * 100)}%"></div>
                    </div>
                    <p class="mt-2 text-[7px] text-stone-400">${label}</p>
                </div>`).join('');
        }

        function renderStok(data) {
            el('stokAman').textContent = `${data.ringkasan.aman} komponen`;
            el('stokMenipis').textContent = `${data.ringkasan.menipis} komponen`;
            el('stokHabis').textContent = `${data.ringkasan.habis} komponen`;
            const perlu = data.produk.filter(item => item.status !== 'aman');
            el('tabelStok').innerHTML = perlu.length ? perlu.map(item => {
                const habis = item.status === 'habis';
                const saran = Math.max(item.min_stock * 2 - item.stock, 0);
                const warna = habis ? 'text-red-500' : 'text-orange-600';
                const latar = habis ? 'bg-red-50' : 'bg-orange-50';
                return `<tr class="${latar}">
                    <td class="p-3 font-semibold ${warna}">${item.product} ${item.size}</td>
                    <td class="p-3 ${warna}">${item.stock} pcs</td>
                    <td class="p-3 ${warna}">${saran > 0 ? '+' + saran + ' pcs' : '-'}</td>
                    <td class="p-3 font-semibold ${warna}">● ${habis ? 'Habis' : 'Minimum'}</td>
                </tr>`;
            }).join('') : '<tr><td colspan="4" class="p-3 text-center text-stone-400">Semua komponen aman.</td></tr>';
        }

        async function muat(periode = 'bulan') {
            try {
                const [ringkasan, stok] = await Promise.all([
                    ambil(`/dashboard?periode=${periode}`),
                    ambil('/ketersediaan'),
                ]);
                renderRingkasan(ringkasan.data);
                renderStok(stok.data);
            } catch (error) {
                console.error(error);
            }
        }

        document.querySelectorAll('.period-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.period-btn').forEach(item => {
                    item.classList.remove('bg-[#FF8000]', 'font-semibold', 'text-white');
                    item.classList.add('border', 'border-stone-200', 'bg-white');
                });
                button.classList.remove('border', 'border-stone-200', 'bg-white');
                button.classList.add('bg-[#FF8000]', 'font-semibold', 'text-white');
                muat(button.dataset.period);
            });
        });
        muat();
    </script>
</body>


</html>
