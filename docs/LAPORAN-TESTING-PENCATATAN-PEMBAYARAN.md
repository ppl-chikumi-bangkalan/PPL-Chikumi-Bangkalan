# Laporan Testing — [PPL-15] Pencatatan Pembayaran di Kasir

- **Backlog**: [PPL-15] Pencatatan Pembayaran di Kasir — Jira `CHIK-38` (berisi `CHIK-90` backend pembayaran tunai/non-tunai & kembalian, `CHIK-91` frontend pembayaran & struk); tiket testing `CHIK-92` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `CHIK-90-pengerjaan-backend-pembayaran-tunai-non-tunai-kembalian` commit `c69830f` (**belum merge** ke `main`). Frontend `CHIK-91` (prototipe pembayaran & struk) sudah masuk `main` lewat PR #20; commit `CHIK-90` ini mengerjakan backend **dan** wiring frontend (mengganti angka hardcode dengan data live).
- **Scope (mapping tim)**:
  - `CHIK-90` — sisi **backend**: `Kasir\TransactionController::store` (atur non-tunai tanpa kembalian, simpan `customer_name`, kembalikan `pelanggan`/`kasir`), model `PosTransaction` + kolom `customer_name` di `database/migrate.php`.
  - `CHIK-91` — sisi **frontend**: layar pembayaran `components/kasir/pembayaran.blade.php`, struk `components/kasir/struk.blade.php`, serta fungsi JS (`bukaPembayaran`, `prosesPembayaran`, `tampilkanBerhasil`, `isiStruk`, `cetakStruk`) di `kasir/dashboard.blade.php`.
- **Metode**:
  1. **Backend `CHIK-90`**: tes otomatis PHPUnit (`TransaksiKasirTest`, `PriceCalculatorTest`, `HargaDinamisTest`, `DashboardWiringTest`) — SQLite in-memory.
  2. **Frontend `CHIK-91`**: replikasi 1:1 fungsi JS pembayaran (kembalian, nominal cepat, keypad, validasi dibayar) ke skrip Node.js + review statis wiring layar.
  3. Build frontend tidak dapat dijalankan di mesin uji (Tailwind linux-only, lihat T-06), sehingga verifikasi frontend lewat replikasi + review statis.
- **Terverifikasi**: 2026-11-11

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| Node.js — logika pembayaran frontend (`CHIK-91`) | 15 | 15 | 0 | 100% |
| PHPUnit — `TransaksiKasirTest` (backend pembayaran & kembalian, `CHIK-90`) | 8 | 8 | 0 | 100% |
| PHPUnit — `PriceCalculatorTest` (harga satuan/subtotal) | 14 | 14 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi PPL-20) | 23 | 23 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (render form kasir) | 2 | 2 | 0 | 100% |
| **Total** | **62** | **62** | **0** | **100%** |

Pencatatan pembayaran **berfungsi penuh**. Aturan tunai/non-tunai & kembalian konsisten antara frontend (`CHIK-91`) dan backend (`CHIK-90`): **tunai** kembalian `= uang dibayar − total` (wajib ≥ total), **non-tunai** nominal dipaksa `= total` sehingga kembalian nol, dan nama pelanggan opsional tersimpan. Tidak ada kegagalan pada skenario inti; catatan bersifat saran/lingkungan (lihat Temuan).

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-38`/`CHIK-92` belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item dan subtask `CHIK-90`/`CHIK-91`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Kasir memilih metode bayar tunai/non-tunai (QRIS/Transfer) | BAY-03, POS-06, POS-07, review wiring |
| AC-2 | Tunai: kembalian dihitung otomatis `uang dibayar − total`; uang kurang ditolak | BAY-01, BAY-02, BAY-08, POS-01, POS-03 |
| AC-3 | Non-tunai: tanpa kembalian (nominal = total) | BAY-03, BAY-09, POS-06, POS-07 |
| AC-4 | Nama pelanggan (opsional) tersimpan di transaksi | POS-07 |
| AC-5 | Struk menampilkan rincian item, total, dibayar, kembalian, metode, dan bisa dicetak | review `isiStruk`/`cetakStruk` |

## Data Uji

| Item | Tipe | Harga |
|---|---|---|
| Reguler (stok 20) | Ukuran | Rp 15.000 |
| Large (stok 3) | Ukuran | Rp 22.000 |
| Mozzarella Cheese | filling | +Rp 3.000 |
| Barbeque Sauce | dipping_sauce | +Rp 2.000 |
| Cheese Sauce | dipping_sauce | +Rp 2.500 |
| Balado Extra Pedas | seasoning | +Rp 0 |
| Keju Bubuk | seasoning | +Rp 1.000 |

(Identik dengan `tests/Concerns/MenyiapkanDataChikumi.php`; kasir uji `kasir.uji@gmail.com`, pelanggan `pelanggan.uji@gmail.com`.)

## Tabel Test Case

### A. Frontend — `CHIK-91` Logika pembayaran (verifikasi Node.js)

Replikasi 1:1 fungsi `kembalian()` (`perbaruiKembalianBayar`), `nominalCepat()` (`aturNominalCepat`), validasi dibayar (`prosesPembayaran`), dan keypad dari `dashboard.blade.php`. Hasil: **`15 passed, 0 failed`**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| BAY-01 | Tunai: total 40.000, dibayar 50.000 | kembalian 10.000 (sama POS-01) | ✅ Pass |
| BAY-02 | Tunai pas: total 15.000, dibayar 15.000 | kembalian 0 | ✅ Pass |
| BAY-03 | Non-tunai: nominal dipaksa = total 40.000 | bayar 40.000, kembalian 0 | ✅ Pass |
| BAY-04 | Nominal cepat total 40.000 | pas 40.000 · tengah 50.000 · penuh 100.000 | ✅ Pass |
| BAY-05 | Nominal cepat total 15.000 | pas 15.000 · tengah 50.000 · penuh 100.000 | ✅ Pass |
| BAY-06 | Nominal cepat total 81.000 | pas 81.000 · tengah 100.000 · penuh 100.000 | ✅ Pass |
| BAY-07 | Nominal cepat total 50.000 (batas genap) | tengah tetap 50.000 | ✅ Pass |
| BAY-08 | Tunai kurang: total 40.000, dibayar 1.000 | ditolak (`diterima:false`) | ✅ Pass |
| BAY-09 | Non-tunai selalu dibayar = total | diterima, kembalian 0 | ✅ Pass |
| BAY-10 | Format angka "50.000" → 50000 | "50.000" · 50000 | ✅ Pass |
| BAY-11 | Input kosong → 0 | 0 | ✅ Pass |
| BAY-12 | Keypad: kosong tekan "1" | "1" | ✅ Pass |
| BAY-13 | Keypad: "5" tekan "000" | "5000" | ✅ Pass |
| BAY-14 | Keypad: "15000" tekan hapus | "1500" | ✅ Pass |
| BAY-15 | Keypad: "1" tekan "0" | "10" | ✅ Pass |

### B. Backend — `CHIK-90` Pembayaran tunai/non-tunai & kembalian (PHPUnit)

Lokasi: `tests/Feature/TransaksiKasirTest.php`. Hasil run (4 suite terkait): `OK (47 tests, 139 assertions)` — PHPUnit 12.5.42, PHP 8.3.30.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| POS-01 | Tunai: Reguler×2+Mozzarella+BBQ, dibayar 50.000 | total 40.000, dibayar 50.000, kembalian 10.000 | ✅ Pass |
| POS-02 | Tunai pas, dua baris ukuran sama | total 81.000, kembalian 0 | ✅ Pass |
| POS-03 | Tunai kurang (1.000 < 15.000) | 422; tidak ada transaksi tersimpan | ✅ Pass |
| POS-04 | Stok kurang (qty 21 > 20) | 422; stok tetap | ✅ Pass |
| POS-05 | Varian tidak valid | 422 `variant_ids`; tidak ada transaksi | ✅ Pass |
| POS-06 | Non-tunai dibayar = total | kembalian 0; metode `non_cash` | ✅ Pass |
| POS-07 | Non-tunai nominal > total + nama pelanggan | nominal dipaksa 15.000, kembalian 0, `customer_name` "Budi" tersimpan | ✅ Pass |
| POS-08 | Login sebagai pelanggan | 403 forbidden | ✅ Pass |

### C. Wiring & tampilan (review statis)

Lokasi: `kasir/dashboard.blade.php`, `components/kasir/pembayaran.blade.php`, `components/kasir/struk.blade.php`.

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| UI-01 | `#btnBayar` → `bukaPembayaran()` | membuka layar pembayaran (`#pembayaran`) via hash | ✅ Pass (statis) |
| UI-02 | Panel Tunai (keypad + nominal cepat + kembalian `#kembalianTunai`) | input dibawa ke `uangDiterima`, kembalian live | ✅ Pass (statis) |
| UI-03 | QRIS/Transfer menampilkan total (`#qrisTotal`/`#transferTotal`) | nominal = total dari keranjang | ✅ Pass (statis) |
| UI-04 | `prosesPembayaran()` POST `payment_method`, `amount_paid`, `customer_name`, `items` | payload lengkap & sesuai backend | ✅ Pass (statis) |
| UI-05 | `tampilkanBerhasil()` + `isiStruk()` | isi rincian dari respons backend (tanpa hardcode) | ✅ Pass (statis) |
| UI-06 | `cetakStruk()` + CSS `@media print` | hanya `#kertasStruk` yang tercetak | ✅ Pass (statis) |
| UI-07 | `transaksiBaru()` | reset keranjang & hash | ✅ Pass (statis) |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | Sedang (scope) | Commit `CHIK-90` mencampur perubahan **tidak terkait** pembayaran: `Owner/ProductController::pageEdit` + `owner/produk/edit.blade.php` (daftar topping master) serta fix `openModal('product' → 'variant')` di `owner/produk/index.blade.php`. | Pisahkan ke commit/PR terpisah agar review & rollback pembayaran bersih. | ⚠️ Saran |
| T-02 | Rendah (UX) | Form kasir punya kontrol pembayaran **ganda**: aside (select `Metode` + `Uang dibayar` + `#kembalian`) dan modal pembayaran (radio Tunai/QRIS/Transfer + `uangDiterima` + `#kembalianTunai`). Sinkronisasi hanya satu arah (aside → modal); mengganti radio di modal tidak memperbarui aside; kembalian tampil di dua tempat. | Sederhanakan: jadikan modal satu-satunya tempat input pembayaran, atau sinkron dua arah. | ⚠️ Saran |
| T-03 | Info | Validasi backend non-tunai `"Nominal non-tunai kurang dari total."` dan batas `customer_name` (max 100) belum diuji otomatis. Frontend selalu mengirim `amount_paid = total` untuk non-tunai, sehingga kasus nominal kurang non-tunai hanya terjadi bila API dipanggil langsung. | Tambah feature test: non-tunai kurang ditolak & `customer_name > 100` ditolak. | ℹ️ Catatan |
| T-04 | Rendah | "Ref. Pembayaran" di layar berhasil menampilkan **nomor transaksi** (`data.nomor`), bukan referensi pembayaran sesungguhnya (mis. ref QRIS). | Isi dari kolom referensi pembayaran bila ada, atau ganti label menjadi "No. Transaksi". | ⚠️ Saran |
| T-05 | Info | Klik "Cetak Struk" dari panel tunai **sebelum** transaksi tersimpan membuka struk kosong (`transaksiTerakhir` masih null). `cetakStruk()` sudah memberi guard berupa alert. | Sembunyikan tombol cetak sampai transaksi selesai. | ℹ️ Catatan |
| T-06 | Info (lingkungan) | Build frontend tetap terblokir di Windows (`@tailwindcss/oxide-linux-x64-gnu`, lihat T-03 laporan PPL-13), sehingga verifikasi `CHIK-91` lewat replikasi Node.js + review statis. | Rujuk `@tailwindcss/oxide` (tanpa pin platform). | ❌ Perlu perbaikan |
| T-07 | Info | `tests/Feature/ExampleTest.php` bawaan gagal (`GET /` → 302), di luar scope PPL-15. | Hapus atau ubah jadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Node.js 26+, Composer di PATH.

1. **Verifikasi logika frontend `CHIK-91` (Node.js, tanpa build)**:
   ```
   node tests/uji-pembayaran-chikumi.mjs
   ```
   (replikasi 1:1 kembalian, nominal cepat, keypad, validasi dibayar; diharapkan `15 passed, 0 failed`).

2. **Tes otomatis backend `CHIK-90` (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="TransaksiKasirTest|PriceCalculatorTest|DashboardWiringTest|HargaDinamisTest"
   ```
   Skema tabel tes mengikuti `database/migrate.php` (termasuk kolom baru `customer_name`) di `tests/Concerns/MenyiapkanDataChikumi.php`. Bila skema berubah, sesuaikan helper tersebut.

3. **Black-box UI (opsional, setelah T-06 diperbaiki)**:
   `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `kasir@chikumi.com` / `kasir123`, buka `/kasir/dashboard`, tambah item → klik **Bayar** → panel tunai (keypad/nominal cepat) atau QRIS/Transfer → **Selesaikan Pembayaran**, lalu cek layar berhasil & **Cetak Struk**.