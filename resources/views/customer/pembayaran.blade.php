<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    @php
        $pageBase = auth()->check() ? '/customer' : '/frontend/customer';
        $devCustomerId = auth()->check() ? null : 3;
    @endphp

    <main class="group mx-auto max-w-md space-y-4 px-5 pb-8 pt-4 sm:max-w-lg">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <a href="{{ $pageBase }}/checkout" aria-label="Kembali"
                class="grid h-10 w-10 place-items-center rounded-xl border-[1.5px] border-[#EDE6DF] bg-white transition hover:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 class="text-lg font-bold">Pembayaran</h1>
        </header>

        <!-- METODE PEMBAYARAN (dipilih saat checkout) -->
        <div class="flex items-center justify-between gap-3 rounded-[14px] bg-[#F7F3EF] px-4 py-3">
            <span class="text-xs text-[#6B645D]">Metode pembayaran</span>
            <span id="labelMetode" class="text-sm font-semibold text-[#D96C00]">-</span>
        </div>

        <!-- TOTAL + BATAS WAKTU (data contoh) -->
        <section class="flex flex-col items-center gap-1 rounded-[20px] bg-[#FF8000] p-[18px] text-white">
            <p class="text-xs text-[#FFF3E6]">Total pembayaran · <span id="nomorPesanan">#CK-0134</span></p>
            <p id="totalBayar" class="text-[30px] font-bold">Rp 69.000</p>
            <span class="flex items-center gap-1.5 rounded-full bg-[#D96C00] px-3 py-1 text-xs font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Bayar dalam <span id="sisaWaktu">09:41</span>
            </span>
        </section>

        <!-- PANEL QRIS -->
        <section id="panelQris" class="hidden">
            <div class="flex flex-col items-center gap-2.5 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px]">
                <div id="qrisGambar" class="relative grid h-[220px] w-[220px] place-items-center rounded-lg bg-[#F7F3EF]">
                    <img id="qrisImg" alt="Kode QRIS dari Duitku" class="hidden h-full w-full rounded-lg bg-white object-contain">
                    <span id="qrisPlaceholder" class="text-center text-xs text-[#A39B93]">QR belum tersedia.<br>Muat ulang halaman.</span>
                </div>
                <p id="qrisInfo" class="text-center text-xs text-[#6B645D]">QRIS dari Duitku</p>
            </div>

            <div class="mt-4 flex gap-2.5">
                <button type="button" id="btnSimpanQr"
                    class="flex-1 rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
                    Simpan QR
                </button>
                <button type="button" id="btnBukaEwallet"
                    class="flex-1 rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
                    Buka QR
                </button>
            </div>
        </section>

        <!-- PANEL VIRTUAL ACCOUNT (data contoh) -->
        <section id="panelVa" class="hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px]">
            <p class="text-xs text-[#6B645D]">Bank</p>
            <p id="vaBank" class="mt-0.5 text-sm font-semibold">BCA Virtual Account</p>

            <p class="mt-4 text-xs text-[#6B645D]">Nomor Virtual Account</p>
            <div class="mt-1 flex items-center justify-between gap-3 rounded-xl bg-[#F7F3EF] px-4 py-3">
                <span id="vaNomor" class="text-lg font-bold tracking-wide">8808 0823 1234 5678</span>
                <button type="button" id="btnSalinVa" class="text-sm font-semibold text-[#FF8000] hover:underline">Salin</button>
            </div>

            <p class="mt-3 text-xs text-[#6B645D]">Transfer sesuai total lewat m-banking, ATM, atau internet banking.</p>
        </section>

        <!-- STATUS PEMBAYARAN -->
        <div id="statusBayar" class="flex items-center justify-center gap-2.5 rounded-full bg-[#FFF4D6] px-4 py-3">
            <span class="h-[18px] w-[18px] animate-spin rounded-full border-2 border-[#C27C00] border-t-transparent"></span>
            <p class="text-[13px] font-semibold text-[#C27C00]">Menunggu pembayaran…</p>
        </div>

        <p class="text-center text-xs text-[#6B645D]">
            Belum sempat bayar? Lanjutkan nanti dari menu Pesanan selama belum kedaluwarsa.
        </p>

        <!-- BUKTI TRANSAKSI DUITKU -->
        <div id="panelDuitku" class="hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px]">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-semibold">Transaksi Duitku</p>
                <span id="statusGateway" class="rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-semibold text-[#6B645D]">-</span>
            </div>

            <dl class="mt-3 space-y-1.5 text-xs">
                <div class="flex justify-between gap-3">
                    <dt class="text-[#6B645D]">Merchant</dt>
                    <dd id="buktiMerchant" class="font-semibold">-</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-[#6B645D]">Referensi Duitku</dt>
                    <dd id="buktiReference" class="font-mono font-semibold">-</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-[#6B645D]">Metode</dt>
                    <dd id="buktiMetode" class="font-semibold">-</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-[#6B645D]">No. pesanan</dt>
                    <dd id="buktiNomor" class="font-mono">-</dd>
                </div>
            </dl>

            <div id="barisBayar" class="mt-3 hidden">
                <a id="linkBayar" href="#" target="_blank" rel="noopener"
                    class="block truncate text-xs font-semibold text-[#FF8000] underline"></a>
            </div>

            <div id="barisQrString" class="mt-3 hidden">
                <p class="text-[11px] text-[#6B645D]">Payload QR dari Duitku</p>
                <p id="teksQrString" class="mt-1 break-all rounded-lg bg-[#F7F3EF] p-2 font-mono text-[10px] leading-relaxed text-[#6B645D]"></p>
            </div>

            <p class="mt-3 text-[11px] text-[#A39B93]">
                Data di atas berasal dari respons API Duitku (merchant + referensi transaksi).
            </p>

            <button type="button" id="btnCekStatus"
                class="mt-3 w-full rounded-[14px] border-[1.5px] border-[#FF8000] bg-white py-[13px] text-base font-semibold text-[#FF8000] transition hover:bg-orange-50">
                Perbarui status dari Duitku
            </button>

            <p id="catatanSandbox" class="mt-2 hidden rounded-lg bg-[#FFF4D6] px-3 py-2 text-xs text-[#C27C00]">
                Transaksi sandbox: uang tidak terpotong. Selesaikan di halaman pembayaran Duitku di atas.
            </p>
        </div>

        <p id="catatanMode" class="text-center text-xs text-[#A39B93]"></p>
    </main>

    <script>
        const PAGE_BASE = @json($pageBase);
        const DEV_CUSTOMER = @json($devCustomerId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const API = PAGE_BASE === '/customer' ? '/customer/api' : '/frontend/customer/api';
        const PESANAN_ID = new URLSearchParams(location.search).get('pesanan');

        const el = id => document.getElementById(id);
        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));

        async function api(path, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
                ...(DEV_CUSTOMER ? { 'X-Dev-Customer': DEV_CUSTOMER } : {}),
            };
            const res = await fetch(API + path, { ...options, headers });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Gagal (${res.status})`);
            return data.data;
        }

        let batasWaktu = null;
        let timer = null;

        function renderStatus(data) {
            const kotak = el('statusBayar');
            const teks = kotak.querySelector('p');
            const spinner = kotak.querySelector('span');

            const gaya = {
                unpaid: ['bg-[#FFF4D6]', 'text-[#C27C00]', 'Menunggu pembayaran…'],
                processing: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan sedang diproses.'],
                ready: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan siap.'],
                completed: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan selesai.'],
                cancelled: ['bg-red-50', 'text-red-600', 'Pembayaran gagal / kedaluwarsa. Silakan buat pesanan baru.'],
            }[data.status] || ['bg-[#FFF4D6]', 'text-[#C27C00]', data.status];

            kotak.className = 'flex items-center justify-center gap-2.5 rounded-full px-4 py-3 ' + gaya[0];
            teks.className = 'text-[13px] font-semibold ' + gaya[1];
            teks.textContent = gaya[2];
            spinner.classList.toggle('hidden', data.status !== 'unpaid');
        }

        function render(data) {
            el('nomorPesanan').textContent = '#' + data.nomor;
            el('totalBayar').textContent = rupiah(data.total);

            const qris = data.metode === 'qris';
            el('labelMetode').textContent = qris ? 'QRIS' : 'Virtual Account';
            el('panelQris').classList.toggle('hidden', !qris);
            el('panelVa').classList.toggle('hidden', qris);

            renderQris(data);
            renderVa(data);

            if (data.kedaluwarsa) {
                batasWaktu = new Date(data.kedaluwarsa.replace(' ', 'T'));
                mulaiHitungMundur();
            }

            el('catatanMode').textContent = data.sumber === 'simulasi'
                ? 'Mode simulasi: kredensial Duitku belum diisi, data di atas bukan transaksi nyata.'
                : (data.sandbox ? 'Transaksi dibuat di Duitku Sandbox (uang tidak terpotong).' : 'Transaksi dibuat di Duitku.');

            renderBukti(data);
            renderStatus(data);

            if (data.status !== 'unpaid' && timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        function renderQris(data) {
            const img = el('qrisImg');
            const placeholder = el('qrisPlaceholder');

            if (!data.qr_url) {
                img.classList.add('hidden');
                placeholder.classList.remove('hidden');
                el('qrisInfo').textContent = data.metode === 'qris'
                    ? 'QR belum tersedia. Muat ulang halaman.'
                    : 'Pesanan ini memakai Virtual Account.';
                return;
            }

            if (img.getAttribute('src') === data.qr_url) return;

            img.onload = () => {
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
                el('qrisInfo').textContent = 'QR dari Duitku — scan untuk membayar';
            };
            img.onerror = () => {
                img.classList.add('hidden');
                placeholder.classList.remove('hidden');
                el('qrisInfo').textContent = 'QR gagal dimuat. Klik "Perbarui status dari Duitku".';
            };
            img.src = data.qr_url;
        }

        function renderVa(data) {
            el('vaBank').textContent = data.bank ? data.bank + ' Virtual Account' : '-';
            el('vaNomor').textContent = data.va_number || 'Menunggu transaksi…';
            el('vaNomor').dataset.nomor = data.va_number || '';
        }

        function renderBukti(data) {
            const panel = el('panelDuitku');

            if (data.sumber !== 'duitku') {
                panel.classList.add('hidden');
                return;
            }

            panel.classList.remove('hidden');

            el('buktiMerchant').textContent = data.merchant_code || '-';
            el('buktiReference').textContent = data.reference || '-';
            el('buktiMetode').textContent = (data.metode_kode ? data.metode_kode + ' · ' : '')
                + (data.bank ? data.bank + ' VA' : (data.metode === 'qris' ? 'QRIS' : '-'));
            el('buktiNomor').textContent = data.nomor;
            el('statusGateway').textContent = data.status_gateway || '-';

            el('barisBayar').classList.toggle('hidden', !data.payment_url);
            if (data.payment_url) {
                el('linkBayar').textContent = 'Buka halaman pembayaran Duitku';
                el('linkBayar').href = data.payment_url;
            }

            el('barisQrString').classList.toggle('hidden', !data.qr_string);
            el('teksQrString').textContent = data.qr_string || '';

            el('catatanSandbox').classList.toggle('hidden', !data.sandbox);
            el('btnCekStatus').classList.toggle('hidden', data.status !== 'unpaid');
        }

        function mulaiHitungMundur() {
            if (timer) clearInterval(timer);
            const perbarui = () => {
                const sisa = Math.max(0, Math.floor((batasWaktu - new Date()) / 1000));
                el('sisaWaktu').textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
                if (sisa === 0 && timer) {
                    clearInterval(timer);
                    timer = null;
                }
            };
            perbarui();
            timer = setInterval(perbarui, 1000);
        }

        async function muat() {
            const data = await api(`/pesanan/${PESANAN_ID}/pembayaran`);
            render(data);

            if (data.status === 'unpaid') {
                render(await api(`/pesanan/${PESANAN_ID}/bayar`, { method: 'POST' }));
            }
        }

        el('btnSalinVa').addEventListener('click', () => {
            const nomor = el('vaNomor').dataset.nomor || '';
            if (!nomor) return alert('Nomor Virtual Account belum tersedia.');
            salin(nomor, el('btnSalinVa'));
        });
        el('btnCekStatus').addEventListener('click', async () => {
            const tombol = el('btnCekStatus');
            tombol.disabled = true;
            tombol.textContent = 'Memeriksa…';
            try {
                render(await api(`/pesanan/${PESANAN_ID}/status-pembayaran`));
            } catch (error) {
                alert(error.message);
            } finally {
                tombol.disabled = false;
                tombol.textContent = 'Perbarui status dari Duitku';
            }
        });

        el('btnSimpanQr').addEventListener('click', () => {
            const src = el('qrisImg').getAttribute('src');
            if (!src) return alert('QR belum tersedia.');
            const link = document.createElement('a');
            link.href = src;
            link.download = 'qris-' + el('nomorPesanan').textContent.replace('#', '') + '.png';
            link.click();
        });

        el('btnBukaEwallet').addEventListener('click', () => {
            const src = el('qrisImg').getAttribute('src');
            if (!src) return alert('QR belum tersedia.');
            window.open(src, '_blank');
        });

        if (!PESANAN_ID) {
            alert('Pilih pesanan yang ingin dibayar dari halaman Pesanan.');
            location.href = PAGE_BASE + '/dashboard';
        } else {
            muat().catch(error => alert(error.message));

            setInterval(async () => {
                try {
                    const data = await api(`/pesanan/${PESANAN_ID}/status-pembayaran`);
                    render(data);
                } catch (_) {
                    // diamkan saat jaringan putus; polling berikutnya mencoba lagi
                }
            }, 15000);
        }
    </script>
</body>

</html>