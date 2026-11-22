@php
    $apiBase = auth()->check() ? '/customer/api' : '/frontend/customer/api';
    $pageBase = auth()->check() ? '/customer' : '/frontend/customer';
    $devCustomerId = auth()->check() ? null : 3;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pesanan - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!-- Detail pesanan: data dari GET /customer/api/pesanan/{id} -->
    <main class="mx-auto flex max-w-md flex-col gap-4 px-5 pb-8 pt-4 sm:max-w-xl md:max-w-3xl md:px-8">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <a href="{{ $pageBase }}/pesanan" aria-label="Kembali"
                class="grid h-10 w-10 place-items-center rounded-xl border-[1.5px] border-[#EDE6DF] bg-white transition hover:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 id="nomorPesanan" class="text-lg font-bold">Memuat…</h1>
        </header>

        <!-- STATUS UTAMA -->
        <section id="statusUtama" class="flex items-center gap-3.5 rounded-[22px] bg-[#FF8000] p-[18px] text-white">
            <span id="statusIkon" class="grid h-[52px] w-[52px] shrink-0 place-items-center rounded-[14px] bg-white text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[26px] w-[26px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <div>
                <p id="statusJudul" class="text-xl font-bold">Memuat…</p>
                <p id="statusKeterangan" class="text-xs">Mengambil status pesanan</p>
            </div>
        </section>

        <!-- STATUS PESANAN (TIMELINE) -->
        <section class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Status Pesanan</h2>
            <ol id="timelineStatus" class="mt-3"></ol>
        </section>

        <!-- DIANTAR KE (disembunyikan kalau Pick Up) -->
        <section id="kartuAlamat" class="hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Diantar ke</h2>
            <div class="mt-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-[#FF8000]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <p id="penerimaAlamat" class="text-[13px] font-semibold">Alamat pengiriman</p>
            </div>
            <p id="detailAlamat" class="mt-3 text-xs text-[#6B645D]"></p>
        </section>

        <!-- RINCIAN PESANAN -->
        <section class="flex flex-col gap-3 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-4">
            <h2 class="text-[15px] font-semibold">Rincian Pesanan</h2>

            <div id="rincianItem" class="flex flex-col gap-3"></div>

            <div class="flex items-center justify-between text-[13px]">
                <p class="text-[#6B645D]">Ongkos kirim</p>
                <p id="ongkirPesanan" class="font-medium">Rp 0</p>
            </div>

            <div class="h-px bg-[#EDE6DF]"></div>

            <div class="flex items-center justify-between font-bold">
                <p id="labelTotal" class="text-[15px]">Total</p>
                <p id="totalPesanan" class="text-lg text-[#D96C00]">Rp 0</p>
            </div>
        </section>

        <a href="https://wa.link/oo1z78" target="_blank" rel="noopener" id="btnWhatsapp"
            class="flex justify-center rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
            Hubungi Chikumi via WhatsApp
        </a>
    </main>

    <script>
        const API_BASE = @json($apiBase);
        const PAGE_BASE = @json($pageBase);
        const DEV_CUSTOMER = @json($devCustomerId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const PESANAN_ID = new URLSearchParams(location.search).get('pesanan');

        const el = id => document.getElementById(id);
        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));
        const esc = teks => String(teks ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        const IKON_CEK = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>';

        const STATUS_UTAMA = {
            unpaid: { bg: 'bg-[#C27C00]', teks: 'text-[#C27C00]', ket: 'Selesaikan pembayaran sebelum kedaluwarsa' },
            processing: { bg: 'bg-[#FF8000]', teks: 'text-[#FF8000]', ket: 'Pesanan sedang disiapkan' },
            ready: { bg: 'bg-[#1F9D55]', teks: 'text-[#1F9D55]', ket: 'Pesanan siap' },
            completed: { bg: 'bg-[#6B645D]', teks: 'text-[#6B645D]', ket: 'Pesanan selesai. Terima kasih!' },
            cancelled: { bg: 'bg-[#E03131]', teks: 'text-[#E03131]', ket: 'Pesanan dibatalkan' },
        };

        function waktu(teks) {
            if (!teks) return null;

            const tanggal = new Date(String(teks).replace(' ', 'T'));
            if (isNaN(tanggal)) return teks;

            const jam = String(tanggal.getHours()).padStart(2, '0') + '.' + String(tanggal.getMinutes()).padStart(2, '0');
            const sekarang = new Date();

            return tanggal.toDateString() === sekarang.toDateString()
                ? jam
                : tanggal.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' · ' + jam;
        }

        function keteranganTahap(tahap, pesanan) {
            const jam = waktu(tahap.waktu);
            if (!jam) return 'Menunggu';

            const catatan = {
                unpaid: 'pesanan dibuat',
                processing: 'pembayaran diterima',
                ready: pesanan.metode === 'pickup' ? 'siap diambil' : 'kurir menuju alamatmu',
                completed: 'pesanan selesai',
            }[tahap.kode];

            return catatan ? `${jam} · ${catatan}` : jam;
        }

        function renderStatus(pesanan) {
            const gaya = STATUS_UTAMA[pesanan.status] || STATUS_UTAMA.processing;

            el('statusUtama').className = `flex items-center gap-3.5 rounded-[22px] p-[18px] text-white ${gaya.bg}`;
            el('statusIkon').className = `grid h-[52px] w-[52px] shrink-0 place-items-center rounded-[14px] bg-white ${gaya.teks}`;
            el('statusJudul').textContent = pesanan.status_label || pesanan.status;
            el('statusKeterangan').textContent = pesanan.status === 'ready' && pesanan.metode === 'pickup'
                ? 'Pesanan siap diambil di gerai'
                : gaya.ket;
        }

        function renderTimeline(pesanan) {
            const tahap = pesanan.timeline || [];

            el('timelineStatus').innerHTML = tahap.map((item, index) => {
                const terakhir = index === tahap.length - 1;
                const bulat = item.selesai
                    ? 'bg-[#FFE0BF] text-[#D96C00]'
                    : item.aktif
                        ? 'bg-[#FF8000] text-white'
                        : 'bg-[#F7F3EF]';
                const garis = item.selesai ? 'bg-[#FF8000]' : 'bg-[#EDE6DF]';
                const judul = item.aktif ? 'font-bold text-[#D96C00]' : item.selesai ? 'font-semibold' : 'text-[#A39B93]';

                return `
                    <li class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <span class="grid h-6 w-6 place-items-center rounded-full ${bulat}">
                                ${item.selesai || item.aktif ? IKON_CEK : ''}
                            </span>
                            ${terakhir ? '' : `<span class="h-[26px] w-0.5 ${garis}"></span>`}
                        </div>
                        <div>
                            <p class="text-sm ${judul}">${esc(item.label)}</p>
                            <p class="text-[11px] text-[#6B645D]">${esc(keteranganTahap(item, pesanan))}</p>
                        </div>
                    </li>`;
            }).join('');
        }

        function renderItem(pesanan) {
            el('rincianItem').innerHTML = (pesanan.item || []).map(item => {
                const varian = (item.varian || []).join(' · ');

                return `
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[13px] font-semibold">${item.quantity}× ${esc(item.produk)} · ${esc(item.ukuran)}</p>
                            ${varian ? `<p class="text-[11px] text-[#6B645D]">${esc(varian)}</p>` : ''}
                        </div>
                        <p class="text-[13px] font-medium">${rupiah(item.subtotal)}</p>
                    </div>`;
            }).join('');
        }

        function render(pesanan) {
            el('nomorPesanan').textContent = '#' + pesanan.nomor;
            renderStatus(pesanan);
            renderTimeline(pesanan);
            renderItem(pesanan);

            if (pesanan.metode === 'courier' && pesanan.alamat) {
                el('kartuAlamat').classList.remove('hidden');
                el('penerimaAlamat').textContent = pesanan.kurir ? `Dikirim oleh ${pesanan.kurir}` : 'Alamat pengiriman';
                el('detailAlamat').textContent = pesanan.alamat;
            } else {
                el('kartuAlamat').classList.add('hidden');
            }

            const metode = { qris: 'QRIS', va: 'Virtual Account' }[pesanan.pembayaran] || 'Bayar di Tempat';
            const lunas = ['processing', 'ready', 'completed'].includes(pesanan.status);

            el('labelTotal').textContent = `Total (${metode}${lunas ? ' · Lunas' : ''})`;
            el('ongkirPesanan').textContent = rupiah(pesanan.ongkir || 0);
            el('totalPesanan').textContent = rupiah(pesanan.total);
        }

        async function api(path) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
                ...(DEV_CUSTOMER ? { 'X-Dev-Customer': DEV_CUSTOMER } : {}),
            };

            const response = await fetch(API_BASE + path, { headers });
            const hasil = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(hasil.message || 'Terjadi kesalahan.');
            return hasil.data;
        }

        if (!PESANAN_ID) {
            alert('Pilih pesanan yang ingin dilihat dari halaman Pesanan.');
            location.href = PAGE_BASE + '/pesanan';
        } else {
            api(`/pesanan/${PESANAN_ID}`)
                .then(render)
                .catch(error => alert(error.message));
        }
    </script>
</body>

</html>
