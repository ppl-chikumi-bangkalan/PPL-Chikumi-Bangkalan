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
    <title>Pesanan Saya - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <main class="group mx-auto flex max-w-md flex-col gap-4 px-5 pb-[106px] pt-4 sm:max-w-xl md:max-w-3xl md:px-8">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <h1 class="text-[22px] font-bold">Pesanan Saya</h1>
            <span class="ml-auto grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">B</span>
        </header>

        <!-- FILTER STATUS -->
        <div class="-mx-5 flex gap-2 overflow-x-auto px-5 pb-1 md:mx-0 md:px-0">
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-semua" value="semua" class="sr-only" checked>Semua
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-belum" value="belum" class="sr-only">Belum Dibayar
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-diproses" value="diproses" class="sr-only">Diproses
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-siap" value="siap" class="sr-only">Siap
            </label>
            <label class="shrink-0 cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                <input type="radio" name="status" id="status-selesai" value="selesai" class="sr-only">Selesai
            </label>
        </div>

        <!-- DAFTAR PESANAN (diisi dari GET /customer/api/pesanan) -->
        <div id="daftarPesanan" class="flex flex-col gap-4">
            <p class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#6B645D]">
                Memuat pesanan…
            </p>
        </div>
    </main>

    @include('components.customer.bottom-nav')

    <script>
        const API_BASE = @json($apiBase);
        const PAGE_BASE = @json($pageBase);
        const DEV_CUSTOMER = @json($devCustomerId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const daftarPesanan = document.getElementById('daftarPesanan');
        const filterStatus = document.querySelectorAll('input[name="status"]');

        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));
        const esc = teks => String(teks ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        const GAYA_STATUS = {
            unpaid: { badge: 'bg-[#FFF4D6] text-[#C27C00]', dot: 'bg-[#C27C00]', border: 'border-[#C27C00]' },
            processing: { badge: 'bg-[#FFF3E6] text-[#D96C00]', dot: 'bg-[#FF8000]', border: 'border-[#EDE6DF]' },
            ready: { badge: 'bg-[#E6F6EC] text-[#1F9D55]', dot: 'bg-[#1F9D55]', border: 'border-[#EDE6DF]' },
            completed: { badge: 'bg-[#F7F3EF] text-[#6B645D]', dot: 'bg-[#A39B93]', border: 'border-[#EDE6DF]' },
            cancelled: { badge: 'bg-[#FDECEC] text-[#E03131]', dot: 'bg-[#E03131]', border: 'border-[#EDE6DF]' },
        };

        function waktu(teks) {
            const tanggal = new Date(String(teks).replace(' ', 'T'));
            if (isNaN(tanggal)) return teks;

            const sekarang = new Date();
            if (tanggal.toDateString() === sekarang.toDateString()) {
                const jam = String(tanggal.getHours()).padStart(2, '0');
                const menit = String(tanggal.getMinutes()).padStart(2, '0');
                return `Hari ini · ${jam}.${menit}`;
            }

            return tanggal.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function tombolAksi(pesanan) {
            const dasar = 'rounded-[14px] px-7 py-[15px] text-base font-semibold transition';

            if (pesanan.status === 'unpaid') {
                return `<a href="${PAGE_BASE}/pembayaran?pesanan=${pesanan.id}" class="${dasar} bg-[#FF8000] text-white hover:bg-[#D96C00]">Bayar Sekarang</a>`;
            }

            if (pesanan.status === 'completed') {
                return `<a href="${PAGE_BASE}/dashboard" class="${dasar} border-[1.5px] border-[#EDE6DF] bg-white hover:border-[#FF8000]">Pesan Lagi</a>`;
            }

            return `<a href="${PAGE_BASE}/pesanan/detail?pesanan=${pesanan.id}" class="text-[13px] font-semibold text-[#D96C00] hover:underline">Lihat detail ›</a>`;
        }

        function kartuPesanan(pesanan) {
            const gaya = GAYA_STATUS[pesanan.status] || GAYA_STATUS.processing;
            const label = pesanan.status_label || pesanan.status;
            const metode = pesanan.metode === 'pickup' ? 'Pick Up' : 'Delivery';
            const ringkas = (pesanan.item || []).map(item => `${item.quantity}× ${item.ukuran}`).join(', ');

            return `
                <article data-pesanan="${esc(pesanan.nomor)}" data-status="${esc(pesanan.status)}"
                    class="flex flex-col gap-2.5 rounded-[20px] border-[1.5px] ${gaya.border} bg-white p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[15px] font-bold">#${esc(pesanan.nomor)}</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full ${gaya.badge} px-2.5 py-1 text-[11px] font-semibold">
                            <span class="h-1.5 w-1.5 rounded-full ${gaya.dot}"></span>${esc(label)}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#6B645D]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>${esc(waktu(pesanan.tanggal))}</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>${metode}</span>
                    </div>
                    <p class="text-[13px]">${esc(ringkas || `${pesanan.jumlah_item || 0} pcs`)}</p>
                    <div class="h-px bg-[#EDE6DF]"></div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-[#D96C00]">${rupiah(pesanan.total)}</p>
                        ${tombolAksi(pesanan)}
                    </div>
                </article>`;
        }

        async function api(path, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
                ...(DEV_CUSTOMER ? { 'X-Dev-Customer': DEV_CUSTOMER } : {}),
            };

            const response = await fetch(API_BASE + path, { ...options, headers });
            const hasil = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(hasil.message || 'Terjadi kesalahan.');
            return hasil.data;
        }

        let semuaPesanan = [];

        const KODE_FILTER = {
            belum: 'unpaid',
            diproses: 'processing',
            siap: 'ready',
            selesai: 'completed',
        };

        function render() {
            const pilihan = document.querySelector('input[name="status"]:checked')?.value || 'semua';
            const status = KODE_FILTER[pilihan] || pilihan;
            const tampil = status === 'semua'
                ? semuaPesanan
                : semuaPesanan.filter(pesanan => pesanan.status === status);

            daftarPesanan.innerHTML = tampil.length
                ? tampil.map(kartuPesanan).join('')
                : `<p class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#6B645D]">
                        Belum ada pesanan${status === 'semua' ? '' : ' dengan status ini'}.
                   </p>`;
        }

        async function muat() {
            try {
                const data = await api('/pesanan');
                semuaPesanan = data.pesanan || [];
                render();
            } catch (error) {
                daftarPesanan.innerHTML = `<p class="rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-6 text-center text-sm text-[#E03131]">${esc(error.message)}</p>`;
            }
        }

        filterStatus.forEach(filter => filter.addEventListener('change', render));
        muat();
    </script>
</body>

</html>
