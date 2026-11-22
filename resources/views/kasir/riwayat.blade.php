@php
    $apiBase = auth()->check() ? '/kasir/api' : '/frontend/kasir/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Transaksi - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body * { visibility: hidden; }
            #kertasStruk, #kertasStruk * { visibility: visible; }
            #kertasStruk { position: fixed; left: 0; top: 0; width: 302px; box-shadow: none; }
        }
    </style>
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!-- TOPBAR KASIR -->
    <header class="sticky top-0 z-30 flex h-[76px] items-center gap-6 border-b border-[#EDE6DF] bg-white px-7">
        <div class="shrink-0">
            <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
            <p class="text-[10px] font-semibold tracking-wide">JAPANESE SNACK</p>
        </div>
        <nav class="flex items-center gap-1 rounded-xl bg-[#F7F3EF] p-1">
            <a href="/kasir/dashboard" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                POS
            </a>
            <a href="/kasir/pesanan-online" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Pesanan Online
            </a>
            <a href="/kasir/riwayat" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Riwayat
            </a>
        </nav>
        <div class="ml-auto flex items-center gap-4">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">{{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}</span>
            <div class="leading-tight">
                <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Kasir' }}</p>
                <p class="text-xs text-[#6B645D]">Kasir</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" aria-label="Keluar" class="grid h-11 w-11 place-items-center rounded-xl border border-[#EDE6DF] bg-white text-[#E03131] transition hover:bg-[#FDECEC]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <main class="flex min-h-[calc(100vh-76px)]">

        <!-- DAFTAR TRANSAKSI -->
        <section class="flex-1 p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Riwayat Transaksi</h2>
                    <p id="subjudulPeriode" class="mt-0.5 text-sm text-[#6B645D]">Memuat transaksi…</p>
                </div>
                <label class="mt-2 flex items-center gap-2 rounded-xl border border-[#EDE6DF] bg-white px-4 py-2.5 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-[#6B645D]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <select name="periode" class="cursor-pointer bg-transparent outline-none">
                        <option value="hari-ini">Hari ini</option>
                        <option value="kemarin">Kemarin</option>
                        <option value="7-hari">7 hari terakhir</option>
                    </select>
                </label>
            </div>

            <!-- FILTER SUMBER + CARI -->
            <div class="mt-5 flex items-center gap-2.5">
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-semua" value="semua" class="sr-only" checked>Semua
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-kasir" value="kasir" class="sr-only">Kasir
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-online" value="online" class="sr-only">Online
                </label>
                <label class="ml-auto flex w-[250px] items-center gap-2 rounded-xl border border-[#EDE6DF] bg-white px-4 py-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px] text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="search" name="cari" placeholder="Cari no. pesanan…" class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </label>
            </div>

            <!-- RINGKASAN -->
            <div class="mt-5 grid grid-cols-4 gap-3.5">
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">Transaksi</p>
                    <p id="ringkasTransaksi" class="mt-1 text-lg font-bold">0</p>
                </div>
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">Tunai</p>
                    <p id="ringkasTunai" class="mt-1 text-lg font-bold">Rp 0</p>
                </div>
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">QRIS / VA</p>
                    <p id="ringkasNonTunai" class="mt-1 text-lg font-bold">Rp 0</p>
                </div>
                <div class="rounded-2xl bg-[#FF8000] p-4 text-white">
                    <p class="text-xs text-white/85">Total</p>
                    <p id="ringkasTotal" class="mt-1 text-lg font-bold">Rp 0</p>
                </div>
            </div>

            <!-- TABEL TRANSAKSI (diisi dari GET /kasir/api/riwayat) -->
            <div class="mt-5 overflow-hidden rounded-2xl border border-[#EDE6DF] bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F7F3EF] text-[11px] font-semibold tracking-wide text-[#6B645D]">
                        <tr>
                            <th class="py-2.5 pl-[18px] font-semibold">NO. PESANAN</th>
                            <th class="py-2.5 font-semibold">WAKTU</th>
                            <th class="py-2.5 font-semibold">SUMBER</th>
                            <th class="py-2.5 font-semibold">ITEM</th>
                            <th class="py-2.5 font-semibold">METODE</th>
                            <th class="py-2.5 font-semibold">TOTAL</th>
                            <th class="py-2.5 pr-[18px] font-semibold">STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="daftarTransaksi" class="divide-y divide-[#EDE6DF]">
                        <tr>
                            <td colspan="7" class="px-[18px] py-6 text-center text-sm text-[#6B645D]">Memuat transaksi…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- DETAIL TRANSAKSI -->
        <aside id="detailTransaksi" class="flex w-[380px] shrink-0 flex-col border-l border-[#EDE6DF] bg-white px-[25px] py-6">
            <div class="flex items-center justify-between">
                <h3 id="detailNomor" class="text-[22px] font-bold">—</h3>
                <span id="detailSumber"></span>
            </div>
            <p id="detailSubjudul" class="mt-3 text-sm text-[#6B645D]">Pilih transaksi untuk melihat detail.</p>

            <div id="detailItem" class="mt-3.5 space-y-2.5"></div>

            <div class="my-3.5 h-px bg-[#EDE6DF]"></div>

            <dl id="detailInfo" class="space-y-3.5 text-sm"></dl>

            <div class="mt-3.5 flex items-center justify-between rounded-xl bg-[#FFF3E6] px-3.5 py-3.5">
                <span class="text-base font-semibold">Total</span>
                <span id="detailTotal" class="text-[22px] font-bold text-[#FF8000]">Rp 0</span>
            </div>

            <button type="button" id="btnCetakUlang" class="mt-auto flex h-[52px] items-center justify-center gap-2 rounded-xl border-[1.5px] border-[#FF8000] bg-white text-base font-semibold text-[#FF8000] transition hover:bg-[#FFF3E6]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Cetak Ulang Struk
            </button>
        </aside>
    </main>

    @include('components.kasir.struk')

    <script>
        const API_BASE = @json($apiBase);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const el = id => document.getElementById(id);
        const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(n) || 0));
        const angka = n => new Intl.NumberFormat('id-ID').format(Math.round(Number(n) || 0));
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));

        let transaksi = [];
        let dipilih = null;

        const badgeSumber = t => t.tipe === 'online'
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Online</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span>';

        const badgeStatus = t => {
            const lunas = ['paid', 'ready', 'completed'].includes(t.status);
            const gaya = lunas
                ? 'bg-[#E6F6EC] text-[#1F9D55]'
                : t.status === 'processing'
                    ? 'bg-[#FFF3E6] text-[#D96C00]'
                    : 'bg-[#FDECEC] text-[#E03131]';
            const titik = lunas ? 'bg-[#1F9D55]' : t.status === 'processing' ? 'bg-[#FF8000]' : 'bg-[#E03131]';
            return `<span class="inline-flex items-center gap-1.5 rounded-full ${gaya} px-2.5 py-1 text-xs font-medium"><span class="h-1.5 w-1.5 rounded-full ${titik}"></span>${esc(t.status_label)}</span>`;
        };

        const ringkasItem = t => t.item
            .map(i => `${i.quantity}× ${i.ukuran}${i.varian.length ? ' · ' + i.varian.join(' · ') : ''}`)
            .join(', ');

        function render() {
            if (!transaksi.length) {
                el('daftarTransaksi').innerHTML = '<tr><td colspan="7" class="px-[18px] py-6 text-center text-sm text-[#6B645D]">Belum ada transaksi pada periode ini.</td></tr>';
                return;
            }

            el('daftarTransaksi').innerHTML = transaksi.map(t => {
                const aktif = dipilih && dipilih.tipe === t.tipe && dipilih.id === t.id;
                const jam = t.waktu.slice(11, 16).replace(':', '.');
                return `
                    <tr class="h-[50px] cursor-pointer transition hover:bg-[#FFF9F2] ${aktif ? 'bg-[#FFF9F2] shadow-[inset_3px_0_0_#FF8000]' : ''}"
                        data-tipe="${t.tipe}" data-id="${t.id}">
                        <td class="pl-[18px] font-semibold">${esc(t.nomor)}</td>
                        <td class="text-xs text-[#6B645D]">${jam}</td>
                        <td>${badgeSumber(t)}</td>
                        <td class="max-w-[220px] truncate pr-3">${esc(ringkasItem(t))}</td>
                        <td>${esc(t.metode_label)}</td>
                        <td class="font-semibold">${rp(t.total)}</td>
                        <td class="pr-[18px]">${badgeStatus(t)}</td>
                    </tr>`;
            }).join('');
        }

        function isiDetail(t) {
            el('detailNomor').textContent = t.nomor;
            el('detailSumber').innerHTML = badgeSumber(t);
            el('detailSubjudul').textContent = `${t.waktu} · ${t.pelanggan}${t.kasir ? ' · ' + t.kasir : ''}`;

            el('detailItem').innerHTML = t.item.map(i => `
                <div class="rounded-xl bg-[#F7F3EF] p-3.5">
                    <div class="flex justify-between text-sm font-semibold">
                        <span>${i.quantity}× ${esc(i.ukuran)}</span>
                        <span>${rp(i.subtotal)}</span>
                    </div>
                    <p class="mt-1.5 text-xs text-[#6B645D]">${esc(i.varian.join(' · ') || 'Tanpa varian')}</p>
                </div>`).join('');

            const info = [
                ['Metode', t.metode_label],
                ['Pembayaran', t.status_label],
                ['Pengambilan', t.pengambilan],
            ];
            if (t.tipe === 'online' && t.ongkir > 0) info.push(['Ongkir', rp(t.ongkir)]);
            if (t.tipe === 'kasir') {
                info.push(['Dibayar', rp(t.dibayar)]);
                info.push(['Kembalian', rp(t.kembalian)]);
            }
            info.push(['Status', t.status_label]);

            el('detailInfo').innerHTML = info.map(([label, nilai]) => `
                <div class="flex justify-between gap-4">
                    <dt class="text-[#6B645D]">${esc(label)}</dt>
                    <dd class="text-right font-semibold">${esc(nilai)}</dd>
                </div>`).join('');

            el('detailTotal').textContent = rp(t.total);
        }

        function pilih(t) {
            dipilih = t;
            render();
            isiDetail(t);
        }

        function kosongkanDetail() {
            dipilih = null;
            el('detailNomor').textContent = '—';
            el('detailSumber').innerHTML = '';
            el('detailSubjudul').textContent = 'Pilih transaksi untuk melihat detail.';
            el('detailItem').innerHTML = '';
            el('detailInfo').innerHTML = '';
            el('detailTotal').textContent = rp(0);
        }

        function isiStruk(t) {
            const jumlah = t.item.reduce((total, i) => total + i.quantity, 0);
            el('strukNomor').textContent = t.nomor;
            el('strukTanggal').textContent = t.waktu;
            el('strukKasir').textContent = t.kasir || '-';
            el('strukPelanggan').textContent = t.pelanggan || '-';
            el('strukItem').innerHTML = t.item.map(i => `
                <div>
                    <p class="font-bold">${esc(i.produk)} · ${esc(i.ukuran)}</p>
                    <p class="text-[11px] text-[#6B645D]">${esc(i.varian.join(' · ') || 'Tanpa varian')}</p>
                    <div class="flex justify-between">
                        <span>${i.quantity} x ${angka(i.harga_satuan)}</span>
                        <span>${angka(i.subtotal)}</span>
                    </div>
                </div>`).join('');
            el('strukSubtotalQty').textContent = `Subtotal (${jumlah} pcs)`;
            el('strukSubtotal').textContent = angka(t.total);
            el('strukTotal').textContent = rp(t.total);
            el('strukMetode').textContent = t.metode_label;
            el('strukDibayar').textContent = angka(t.dibayar);
            el('strukKembalian').textContent = angka(t.kembalian);
            el('strukStatus').textContent = String(t.status_label).toUpperCase();
        }

        function cetakStruk() {
            window.print();
        }

        async function muat() {
            const params = new URLSearchParams({
                sumber: document.querySelector('input[name="sumber"]:checked').value,
                periode: document.querySelector('select[name="periode"]').value,
                cari: document.querySelector('input[name="cari"]').value.trim(),
            });

            try {
                const response = await fetch(`${API_BASE}/riwayat?${params}`, {
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                });
                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(hasil.message || 'Gagal memuat riwayat.');

                transaksi = hasil.data.transaksi || [];
                const ringkasan = hasil.data.ringkasan || {};
                el('ringkasTransaksi').textContent = ringkasan.transaksi ?? 0;
                el('ringkasTunai').textContent = rp(ringkasan.tunai ?? 0);
                el('ringkasNonTunai').textContent = rp(ringkasan.non_tunai ?? 0);
                el('ringkasTotal').textContent = rp(ringkasan.total ?? 0);
                el('subjudulPeriode').textContent = `${ringkasan.transaksi ?? 0} transaksi · ${transaksi.length ? 'terbaru ' + transaksi[0].waktu : 'belum ada data'}`;

                if (transaksi.length) {
                    pilih(transaksi[0]);
                } else {
                    kosongkanDetail();
                    render();
                }
            } catch (error) {
                el('daftarTransaksi').innerHTML = `<tr><td colspan="7" class="px-[18px] py-6 text-center text-sm text-[#E03131]">${esc(error.message)}</td></tr>`;
            }
        }

        el('daftarTransaksi').addEventListener('click', event => {
            const baris = event.target.closest('tr[data-tipe]');
            if (!baris) return;
            const t = transaksi.find(x => x.tipe === baris.dataset.tipe && x.id === Number(baris.dataset.id));
            if (t) pilih(t);
        });

        document.querySelectorAll('input[name="sumber"]').forEach(input => input.addEventListener('change', muat));
        document.querySelector('select[name="periode"]').addEventListener('change', muat);

        let timerCari = null;
        document.querySelector('input[name="cari"]').addEventListener('input', () => {
            clearTimeout(timerCari);
            timerCari = setTimeout(muat, 300);
        });

        el('btnCetakUlang').addEventListener('click', () => {
            if (!dipilih) {
                alert('Pilih transaksi dulu.');
                return;
            }
            isiStruk(dipilih);
            location.hash = '#struk';
        });

        muat();
    </script>
</body>

</html>
