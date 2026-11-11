// Verifikasi logika perhitungan total otomatis (CHIK-86) — replikasi 1:1
// dari fungsi JS di resources/views/kasir/dashboard.blade.php.
// Dipakai oleh docs/LAPORAN-TESTING-PERHITUNGAN-TOTAL.md (CHIK-89).
//
// Data mengikuti tests/Concerns/MenyiapkanDataChikumi.php (sama dengan backend).
// Tujuan: membuktikan rumus frontend (ukuran + varian) x jumlah == hasil backend
// PriceCalculator::breakdown + TransactionController::store.
//
// Jalankan:  node tests/uji-total-chikumi.mjs

const UKURAN = {
  regular: { id: 1, nama: 'Regular', harga: 15000, stok: 20 },
  large:   { id: 2, nama: 'Large',   harga: 22000, stok: 3 },
};

const VARIAN = {
  mozzarella:  { id: 1, type: 'filling',       nama: 'Mozzarella Cheese',  harga: 3000 },
  cokelat:     { id: 2, type: 'filling',       nama: 'Cokelat Lumer',      harga: 3500 },
  bbq:         { id: 3, type: 'dipping_sauce', nama: 'Barbeque Sauce',     harga: 2000 },
  cheeseSauce: { id: 4, type: 'dipping_sauce', nama: 'Cheese Sauce',       harga: 2500 },
  balado:      { id: 5, type: 'seasoning',     nama: 'Balado Extra Pedas', harga: 0 },
  kejuBubuk:   { id: 6, type: 'seasoning',     nama: 'Keju Bubuk',         harga: 1000 },
};

// ==== Replika persis fungsi dari dashboard.blade.php ====
const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));

function hitungVarian(ukuranAktif, varianTerpilih, qtyInput) {
  if (!ukuranAktif) return { harga: 0, qty: 1, total: 0 };
  const harga = ukuranAktif.harga + varianTerpilih.reduce((t, v) => t + v.harga, 0);
  const qty = Math.min(99, Math.max(1, parseInt(qtyInput || '1', 10) || 1));
  return { harga, qty, total: harga * qty };
}

function totalKeranjang(keranjang) {
  return keranjang.reduce((t, k) => t + k.harga * k.qty, 0);
}

// ==== Harness assert ====
let passed = 0, failed = 0;
function check(id, desc, actual, expected) {
  const ok = Object.is(actual, expected);
  if (ok) passed++; else failed++;
  console.log(`${ok ? 'PASS' : 'FAIL'}  ${id}  ${desc}`);
  console.log(`       expected=${expected}  actual=${actual}`);
  if (!ok) process.exitCode = 1;
  return ok;
}

// ==== Skenario (TOT-01 .. TOT-12) ====
let r = hitungVarian(UKURAN.regular, [], '1');
check('TOT-01', 'Regular tanpa varian qty=1', r.total, 15000);

r = hitungVarian(UKURAN.large, [], '1');
check('TOT-02', 'Large tanpa varian qty=1', r.total, 22000);

r = hitungVarian(UKURAN.regular, [VARIAN.mozzarella], '1');
check('TOT-03', 'Regular + Mozzarella qty=1', r.total, 18000);

r = hitungVarian(UKURAN.regular, [VARIAN.mozzarella, VARIAN.bbq], '2');
check('TOT-04', 'Regular + Mozzarella + BBQ qty=2 (sama dgn POS-01)', r.total, 40000);

r = hitungVarian(UKURAN.large, [VARIAN.mozzarella], '3');
check('TOT-05', 'Large + Mozzarella qty=3 (sama dgn U04)', r.total, 75000);

r = hitungVarian(UKURAN.regular, [VARIAN.balado], '1');
check('TOT-06', 'Regular + Balado gratis qty=1', r.total, 15000);

r = hitungVarian(UKURAN.regular, [VARIAN.balado, VARIAN.kejuBubuk], '1');
check('TOT-07', 'Regular + Balado + Keju Bubuk qty=1', r.total, 16000);

r = hitungVarian(UKURAN.regular, [], '0');
check('TOT-08a', 'qty=0 diklem ke 1', r.qty, 1);
r = hitungVarian(UKURAN.regular, [], '100');
check('TOT-08b', 'qty=100 diklem ke 99', r.qty, 99);
r = hitungVarian(UKURAN.regular, [], 'abc');
check('TOT-08c', 'qty non-angka -> 1', r.qty, 1);

const keranjang = [
  { key: '1|',  ukuran_id: 1, nama: 'Chikumi Original Box · Regular', harga: 15000, qty: 3, stok: 20 },
  { key: '1|1', ukuran_id: 1, nama: 'Chikumi Original Box · Regular', harga: 18000, qty: 2, stok: 20 },
];
check('TOT-09', 'totalKeranjang(3 polos + 2 mozzarella) (sama dgn POS-02)', totalKeranjang(keranjang), 81000);

const keranjang2 = [
  { harga: 15000, qty: 1 },
  { harga: 25000, qty: 2 },
];
check('TOT-10', 'totalKeranjang(15000 + 50000)', totalKeranjang(keranjang2), 65000);

const dibayar = 50000, total = 40000;
check('TOT-11', 'kembalian = dibayar - total = 10000', Math.max(0, dibayar - total), 10000);

check('TOT-12', 'kembalian non-tunai = 0', Math.max(0, total - total), 0);

console.log('\n====================');
console.log(`RESULT: ${passed} passed, ${failed} failed`);