/**
 * [CHIK-116] Verifikasi logika tampilan halaman pembayaran (CHIK-115).
 *
 * Replikasi 1:1 fungsi tampilan dari resources/views/customer/pembayaran.blade.php
 * (renderStatus, render, renderQris, renderVa, renderBukti, rupiah, hitung mundur)
 * tanpa membuka browser. Jalankan: node tests/uji-pembayaran-sandbox.mjs
 */

let lewat = 0;
let gagal = 0;

function uji(nama, kondisi, detail = '') {
    if (kondisi) {
        lewat++;
        console.log(`PASS ${nama}`);
    } else {
        gagal++;
        console.log(`FAIL ${nama}${detail ? ' — ' + detail : ''}`);
    }
}

const rupiah = (nilai) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nilai));

// --- renderStatus() ---
const GAYA = {
    unpaid: ['bg-[#FFF4D6]', 'text-[#C27C00]', 'Menunggu pembayaran…'],
    processing: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan sedang diproses.'],
    ready: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan siap.'],
    completed: ['bg-green-50', 'text-green-600', 'Pembayaran berhasil. Pesanan selesai.'],
    cancelled: ['bg-red-50', 'text-red-600', 'Pembayaran gagal / kedaluwarsa. Silakan buat pesanan baru.'],
};

function renderStatus(status) {
    const gaya = GAYA[status] || ['bg-[#FFF4D6]', 'text-[#C27C00]', status];
    return { warna: gaya[0], teks: gaya[1], pesan: gaya[2], spinner: status === 'unpaid' };
}

// --- render() label metode + panel ---
function renderMetode(metode) {
    const qris = metode === 'qris';
    return {
        label: qris ? 'QRIS' : 'Virtual Account',
        panelQris: qris ? 'tampil' : 'sembunyi',
        panelVa: qris ? 'sembunyi' : 'tampil',
    };
}

// --- renderQris() ---
function renderQris(data) {
    if (!data.qr_url) {
        return {
            gambar: 'sembunyi',
            placeholder: 'tampil',
            info: data.metode === 'qris'
                ? 'QR belum tersedia. Muat ulang halaman.'
                : 'Pesanan ini memakai Virtual Account.',
        };
    }
    return { gambar: 'tampil', placeholder: 'sembunyi', src: data.qr_url };
}

// --- renderVa() ---
function renderVa(data) {
    return {
        bank: data.bank ? data.bank + ' Virtual Account' : '-',
        nomor: data.va_number || 'Menunggu transaksi…',
    };
}

// --- renderBukti() ---
function renderBukti(data) {
    if (data.sumber !== 'duitku') {
        return { panel: 'sembunyi' };
    }
    return {
        panel: 'tampil',
        merchant: data.merchant_code || '-',
        reference: data.reference || '-',
        metode: (data.metode_kode ? data.metode_kode + ' · ' : '')
            + (data.bank ? data.bank + ' VA' : (data.metode === 'qris' ? 'QRIS' : '-')),
        statusGateway: data.status_gateway || '-',
        tombolCek: data.status === 'unpaid' ? 'tampil' : 'sembunyi',
        sandbox: !!data.sandbox,
    };
}

// --- hitung mundur ---
function hitungMundur(kedaluwarsa, sekarang) {
    const batas = new Date(kedaluwarsa.replace(' ', 'T'));
    const sisa = Math.max(0, Math.floor((batas - sekarang) / 1000));
    return String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
}

// ===== Kasus uji =====

uji('rupiah 30000', rupiah(30000) === 'Rp 30.000', rupiah(30000));
uji('rupiah 1234567', rupiah(1234567) === 'Rp 1.234.567', rupiah(1234567));

const unpaid = renderStatus('unpaid');
uji('status unpaid: pesan menunggu', unpaid.pesan === 'Menunggu pembayaran…');
uji('status unpaid: spinner tampil', unpaid.spinner === true);

const diproses = renderStatus('processing');
uji('status processing: pesan diproses', diproses.pesan === 'Pembayaran berhasil. Pesanan sedang diproses.');
uji('status processing: spinner sembunyi', diproses.spinner === false);

uji('status ready', renderStatus('ready').pesan === 'Pembayaran berhasil. Pesanan siap.');
uji('status completed', renderStatus('completed').pesan === 'Pembayaran berhasil. Pesanan selesai.');

const batal = renderStatus('cancelled');
uji('status cancelled: pesan gagal/kedaluwarsa',
    batal.pesan === 'Pembayaran gagal / kedaluwarsa. Silakan buat pesanan baru.');
uji('status cancelled: warna merah', batal.warna === 'bg-red-50');

uji('metode qris: label QRIS', renderMetode('qris').label === 'QRIS');
uji('metode qris: panel VA sembunyi', renderMetode('qris').panelVa === 'sembunyi');
uji('metode va: label Virtual Account', renderMetode('va').label === 'Virtual Account');
uji('metode va: panel QRIS sembunyi', renderMetode('va').panelQris === 'sembunyi');

const qrisAda = renderQris({ metode: 'qris', qr_url: '/customer/api/pesanan/1/qr' });
uji('qr_url ada: gambar tampil', qrisAda.gambar === 'tampil' && qrisAda.src.endsWith('/qr'));

const qrisKosong = renderQris({ metode: 'qris', qr_url: null });
uji('qr_url kosong: placeholder tampil', qrisKosong.placeholder === 'tampil');
uji('qr_url kosong: teks muat ulang', qrisKosong.info === 'QR belum tersedia. Muat ulang halaman.');

const va = renderVa({ bank: 'BCA', va_number: '880800000001' });
uji('VA: bank & nomor', va.bank === 'BCA Virtual Account' && va.nomor === '880800000001');
uji('VA: nomor kosong pakai teks tunggu', renderVa({}).nomor === 'Menunggu transaksi…');

uji('bukti: sumber simulasi disembunyikan', renderBukti({ sumber: 'simulasi' }).panel === 'sembunyi');

const bukti = renderBukti({
    sumber: 'duitku', status: 'unpaid', sandbox: true, merchant_code: 'D0001',
    reference: 'REF-1', metode_kode: 'SP', metode: 'qris', status_gateway: 'Menunggu pembayaran',
});
uji('bukti: panel tampil & merchant', bukti.panel === 'tampil' && bukti.merchant === 'D0001');
uji('bukti: metode qris', bukti.metode === 'SP · QRIS', bukti.metode);
uji('bukti: tombol cek status tampil saat unpaid', bukti.tombolCek === 'tampil');
uji('bukti: catatan sandbox tampil', bukti.sandbox === true);
uji('bukti: tombol cek status sembunyi saat lunas',
    renderBukti({ sumber: 'duitku', status: 'processing', metode: 'qris' }).tombolCek === 'sembunyi');

const sekarang = new Date('2026-11-11T10:00:00');
uji('hitung mundur 60 menit', hitungMundur('2026-11-11 11:00:00', sekarang) === '60:00');
uji('hitung mundur 9 menit 41 detik', hitungMundur('2026-11-11 10:09:41', sekarang) === '09:41');
uji('hitung mundur lewat batas jadi 00:00', hitungMundur('2026-11-11 09:00:00', sekarang) === '00:00');

console.log(`\n${lewat} passed, ${gagal} failed`);
process.exit(gagal === 0 ? 0 : 1);
