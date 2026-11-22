@php
    $apiBase = auth()->check() ? '/kasir/api' : '/frontend/kasir/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesanan Online - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
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
            <a href="/kasir/pesanan-online" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Pesanan Online
            </a>
            <a href="/kasir/riwayat" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
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

    <main class="flex flex-col gap-5 p-7">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Pesanan Online</h2>
                <p class="mt-0.5 text-sm text-[#6B645D]">Pesanan lunas dari aplikasi pelanggan · hari ini</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-semua" value="semua" class="sr-only" checked>Semua
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-ambil" value="pickup" class="sr-only">Ambil di Tempat
                </label>
                <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="jenis" id="jenis-diantar" value="courier" class="sr-only">Diantar
                </label>
            </div>
        </div>

        <!-- Kartu diisi dari GET /kasir/api/pesanan-online -->
        <div id="isiPesanan" class="grid items-start gap-[18px] lg:grid-cols-3">
            <!-- KOLOM: DIPROSES -->
            <section class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF8000]"></span>
                        <h3 class="text-base font-bold">Diproses</h3>
                    </div>
                    <span id="jumlahDiproses" class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#FF8000] text-white">0</span>
                </div>
                <div id="daftarDiproses" class="flex flex-col gap-3"></div>
            </section>

            <!-- KOLOM: SIAP DIAMBIL / DIKIRIM -->
            <section class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#1F9D55]"></span>
                        <h3 class="text-base font-bold">Siap Diambil / Dikirim</h3>
                    </div>
                    <span id="jumlahSiap" class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#FF8000] text-white">0</span>
                </div>
                <div id="daftarSiap" class="flex flex-col gap-3"></div>
            </section>

            <!-- KOLOM: SELESAI HARI INI -->
            <section class="flex flex-col gap-3 rounded-[20px] bg-[#F7F3EF] p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#A39B93]"></span>
                        <h3 class="text-base font-bold">Selesai Hari Ini</h3>
                    </div>
                    <span id="jumlahSelesai" class="grid h-[26px] w-[26px] place-items-center rounded-full text-xs font-bold bg-[#EDE6DF] text-[#6B645D]">0</span>
                </div>
                <div id="daftarSelesai" class="flex flex-col gap-3"></div>
            </section>
        </div>
    </main>

    <script>
        const API_BASE = @json($apiBase);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const el = id => document.getElementById(id);
        const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(n) || 0));
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));

        let pesanan = [];

        function jam(waktu) {
            return String(waktu).slice(11, 16).replace(':', '.');
        }

        function kartu(p) {
            const selesai = p.status === 'completed';
            const jenis = p.jenis === 'courier'
                ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-[11px] font-semibold text-[#C27C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span>Diantar</span>'
                : '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-[11px] font-semibold text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Ambil di Tempat</span>';

            const aksi = p.status === 'processing'
                ? `<button type="button" data-aksi="siap" data-id="${p.id}" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Tandai Siap</button>`
                : p.status === 'ready'
                    ? `<button type="button" data-aksi="selesai" data-id="${p.id}" class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">Selesai</button>`
                    : '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Selesai</span>';

            const item = p.item.map(i => `<p>${i.quantity}× ${esc(i.ukuran)} · ${esc(i.varian.join(' · ') || 'Tanpa varian')}</p>`).join('');
            const ongkir = p.ongkir > 0 ? `<p class="text-[#6B645D]">Ongkir ${rp(p.ongkir)}</p>` : '';

            return `
                <article class="flex flex-col gap-2.5 rounded-2xl border-[1.5px] border-[#EDE6DF] bg-white p-4 shadow-[0_4px_12px_rgba(115,56,0,0.06)] ${selesai ? 'opacity-65' : ''}">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">${esc(p.nomor)}</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas · ${esc(p.metode_label)}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>${esc(p.pelanggan)} · ${jam(p.waktu)}</span>
                        ${jenis}
                    </div>
                    <div class="space-y-1 border-t border-[#EDE6DF] pt-2.5 text-xs">
                        ${item}
                        ${ongkir}
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">${rp(p.total)}</p>
                        ${aksi}
                    </div>
                </article>`;
        }

        function isi(idDaftar, idJumlah, daftar) {
            el(idJumlah).textContent = daftar.length;
            el(idDaftar).innerHTML = daftar.length
                ? daftar.map(kartu).join('')
                : '<p class="rounded-2xl border-[1.5px] border-dashed border-[#EDE6DF] bg-white/60 p-4 text-center text-xs text-[#A39B93]">Belum ada pesanan.</p>';
        }

        function render() {
            const jenis = document.querySelector('input[name="jenis"]:checked')?.value || 'semua';
            const tampil = jenis === 'semua' ? pesanan : pesanan.filter(p => p.jenis === jenis);

            isi('daftarDiproses', 'jumlahDiproses', tampil.filter(p => p.status === 'processing'));
            isi('daftarSiap', 'jumlahSiap', tampil.filter(p => p.status === 'ready'));
            isi('daftarSelesai', 'jumlahSelesai', tampil.filter(p => p.status === 'completed'));
        }

        async function muat() {
            try {
                const response = await fetch(`${API_BASE}/pesanan-online`, {
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                });
                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(hasil.message || 'Gagal memuat pesanan.');

                pesanan = hasil.data.pesanan || [];
                render();
            } catch (error) {
                ['daftarDiproses', 'daftarSiap', 'daftarSelesai'].forEach(id => {
                    el(id).innerHTML = `<p class="rounded-2xl bg-white p-4 text-center text-xs text-[#E03131]">${esc(error.message)}</p>`;
                });
            }
        }

        async function aksi(id, tindakan) {
            try {
                const response = await fetch(`${API_BASE}/pesanan-online/${id}/${tindakan}`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(CSRF ? {'X-CSRF-TOKEN': CSRF} : {}),
                    },
                });
                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(hasil.message || 'Gagal memperbarui pesanan.');

                const index = pesanan.findIndex(p => p.id === id);
                if (index >= 0) pesanan[index] = hasil.data;
                render();
            } catch (error) {
                alert(error.message);
            }
        }

        el('isiPesanan').addEventListener('click', event => {
            const tombol = event.target.closest('button[data-aksi]');
            if (tombol) aksi(Number(tombol.dataset.id), tombol.dataset.aksi);
        });

        document.querySelectorAll('input[name="jenis"]').forEach(input => input.addEventListener('change', render));

        muat();
    </script>
</body>

</html>
