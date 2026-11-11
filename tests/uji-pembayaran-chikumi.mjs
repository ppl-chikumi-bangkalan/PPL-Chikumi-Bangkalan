// Verifikasi logika frontend pencatatan pembayaran (CHIK-91 / wiring CHIK-90)
// — replikasi 1:1 dari fungsi JS di resources/views/kasir/dashboard.blade.php.
// Dipakai oleh docs/LAPORAN-TESTING-PENCATATAN-PEMBAYARAN.md (CHIK-92).
//
// Jalankan:  node tests/uji-pembayaran-chikumi.mjs

const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
const angka = nilai => Number(String(nilai ?? '').replace(/\D/g, '') || 0);
const formatAngka = nilai => new Intl.NumberFormat('id-ID').format(angka(nilai));

// Kembalian layar pembayaran (perbaruiKembalianBayar)
function kembalian(total, metode, inputValue) {
  const readOnly = metode === 'non_cash';
  const nilai = readOnly ? formatAngka(total) : inputValue;
  const bayar = angka(nilai);
  return { bayar, kembalian: Math.max(0, bayar - total) };
}

// Nominal cepat (aturNominalCepat)
function nominalCepat(total) {
  return {
    pas: total,
    tengah: Math.ceil(total / 50000) * 50000,
    penuh: Math.ceil(total / 100000) * 100000,
  };
}

// Penentuan dibayar + validasi di prosesPembayaran
function prosesPembayaranDibayar(total, metode, uangDiterima) {
  const dibayar = metode === 'non_cash' ? total : angka(uangDiterima);
  if (dibayar < total) return { dibayar, diterima: false };
  return { dibayar, diterima: true };
}

// Keypad
function keypad(sekarang, kunci) {
  const s = String(angka(sekarang));
  if (kunci === 'del') return s.slice(0, -1);
  return (s + kunci).replace(/^0+(?=\d)/, '');
}

let passed = 0, failed = 0;
function check(id, desc, actual, expected) {
  const ok = JSON.stringify(actual) === JSON.stringify(expected);
  if (ok) passed++; else failed++;
  console.log(`${ok ? 'PASS' : 'FAIL'}  ${id}  ${desc}`);
  console.log(`       expected=${JSON.stringify(expected)}  actual=${JSON.stringify(actual)}`);
  if (!ok) process.exitCode = 1;
  return ok;
}

// ==== Kembalian ====
check('BAY-01', 'Tunai: total 40.000, bayar 50.000 → kembalian 10.000 (sama POS-01)',
  kembalian(40000, 'cash', '50.000').kembalian, 10000);
check('BAY-02', 'Tunai pas: total 15.000, bayar 15.000 → kembalian 0',
  kembalian(15000, 'cash', '15.000').kembalian, 0);
check('BAY-03', 'Non-tunai: bayar dipaksa = total 40.000 → kembalian 0',
  kembalian(40000, 'non_cash', ''), { bayar: 40000, kembalian: 0 });

// ==== Nominal cepat ====
check('BAY-04', 'Nominal cepat total 40.000', nominalCepat(40000), { pas: 40000, tengah: 50000, penuh: 100000 });
check('BAY-05', 'Nominal cepat total 15.000', nominalCepat(15000), { pas: 15000, tengah: 50000, penuh: 100000 });
check('BAY-06', 'Nominal cepat total 81.000', nominalCepat(81000), { pas: 81000, tengah: 100000, penuh: 100000 });
check('BAY-07', 'Nominal cepat total 50.000 (batas genap tengah)', nominalCepat(50000), { pas: 50000, tengah: 50000, penuh: 100000 });

// ==== Proses pembayaran (dibayar + validasi) ====
check('BAY-08', 'Tunai kurang: total 40.000, bayar 1.000 → ditolak',
  prosesPembayaranDibayar(40000, 'cash', '1.000'), { dibayar: 1000, diterima: false });
check('BAY-09', 'Non-tunai selalu dibayar = total → diterima',
  prosesPembayaranDibayar(40000, 'non_cash', ''), { dibayar: 40000, diterima: true });

// ==== Format angka ====
check('BAY-10', 'formatAngka "50.000" → 50.000; angka → 50000',
  [formatAngka('50.000'), angka('50.000')], ['50.000', 50000]);
check('BAY-11', 'angka input kosong → 0', angka(''), 0);

// ==== Keypad ====
check('BAY-12', 'Keypad: kosong tekan "1" → "1"', keypad('', '1'), '1');
check('BAY-13', 'Keypad: "5" tekan "000" → "5000"', keypad('5', '000'), '5000');
check('BAY-14', 'Keypad: "15000" tekan del → "1500"', keypad('15000', 'del'), '1500');
check('BAY-15', 'Keypad: "1" tekan "0" → "10"', keypad('1', '0'), '10');

console.log('\n====================');
console.log(`RESULT: ${passed} passed, ${failed} failed`);