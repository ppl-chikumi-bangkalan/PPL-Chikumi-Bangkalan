# Laporan Testing — [PPL-14] Perhitungan Total Pembayaran Otomatis

- **Backlog**: [PPL-14] Perhitungan Total Pembayaran Otomatis — Jira `CHIK-37` (berisi `CHIK-86` logika total otomatis ukuran/varian/jumlah, `CHIK-88` tampilan total di form kasir); tiket testing `CHIK-89` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `ab33323` (PR #21 — merge `CHIK-86` ke `main`, sudah memuat PR #20 `CHIK-88` dan komponen `CHIK-91` pembayaran/struk)
- **Scope (mapping tim)**:
  - `CHIK-86` — sisi **frontend**: logika total otomatis `(ukuran + varian) × jumlah` yang dihitung **instan di halaman kasir** (fungsi `hitungVarian()`, `totalKeranjang()`, `renderKeranjang()`, `perbaruiKembalian()` pada `resources/views/kasir/dashboard.blade.php` + `resources/views/components/kasir/pilih-varian.blade.php`).
  - `CHIK-88` — sisi **backend**: perhitungan & data total yang **ditampilkan di form kasir** — `Kasir\DashboardController::page()` menyuplai data harga ukuran/varian ke form, lalu `App\Support\PriceCalculator::breakdown` + `Kasir\TransactionController::store` menghitung total otoritatif server dan mengembalikan `total`/`dibayar`/`kembalian`. Sebagian besar komponen backend ini merupakan *reuse* dari PPL-20/PPL-13 dan diuji ulang sebagai regresi.
- **Metode**:
  1. **Frontend `CHIK-86`**: replikasi 1:1 fungsi `hitungVarian()` & `totalKeranjang()` dari `dashboard.blade.php` ke skrip Node.js, dijalankan terhadap 14 skenario dan dibandingkan dengan hasil backend.
  2. **Backend `CHIK-88`**: tes otomatis PHPUnit (`PriceCalculatorTest`, `TransaksiKasirTest`, `DashboardWiringTest`, `HargaDinamisTest`) + review statis data harga & elemen total yang dirender di form.
  3. Build frontend tidak dapat dijalankan di mesin uji (lihat temuan T-06 lanjutan), sehingga verifikasi frontend dilakukan lewat replikasi Node.js + review statis.
- **Terverifikasi**: 2026-11-11

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| Node.js — logika total frontend (`CHIK-86`) | 14 | 14 | 0 | 100% |
| PHPUnit — `PriceCalculatorTest` (harga satuan/subtotal, `CHIK-88`) | 14 | 14 | 0 | 100% |
| PHPUnit — `TransaksiKasirTest` (total transaksi + kembalian, `CHIK-88`) | 7 | 7 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi PPL-20) | 23 | 23 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (render + data harga form) | 2 | 2 | 0 | 100% |
| **Total** | **60** | **60** | **0** | **100%** |

Perhitungan total otomatis **berfungsi penuh**. Rumus total — `(harga dasar ukuran + total tambahan varian) × jumlah` — konsisten antara kalkulasi frontend (`CHIK-86`: `hitungVarian`, `totalKeranjang`, kembalian) dan backend (`CHIK-88`: `PriceCalculator::breakdown` + `TransactionController::store`). Tidak ada kegagalan pada skenario inti; catatan bersifat saran/lingkungan (lihat Temuan).

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-37`/`CHIK-89` belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item dan subtask `CHIK-86`/`CHIK-88`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Total dihitung otomatis dari ukuran (harga dasar) + varian (tambahan harga), dikali jumlah | TOT-01…TOT-07, U01…U04 |
| AC-2 | Total ter-update otomatis saat ukuran/varian/jumlah diubah, dan saat item ditambah/kurang/dihapus di keranjang | TOT-08…TOT-10, review `renderKeranjang` |
| AC-3 | Total tampil di form kasir: subtotal, total, label tombol bayar, dan kembalian | TMP-01…TMP-08 |
| AC-4 | Total yang tampil di frontend sama dengan total yang dihitung & disimpan backend | TOT-04 ↔ POS-01, TOT-05 ↔ U04, TOT-09 ↔ POS-02 |
| AC-5 | Kembalian = uang dibayar − total; uang kurang ditolak | TOT-11, TOT-12, POS-03 |

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

(Data identik dengan `tests/Concerns/MenyiapkanDataChikumi.php` dan `database/seed.php`; varian *Cokelat Lumer* dan *Sambal Matah (habis)* dipakai pada uji validasi backend.)

## Tabel Test Case

### A. Frontend — `CHIK-86` Logika total otomatis (verifikasi Node.js)

Skrip replikasi: `hitungVarian(ukuran, varian, jumlah)` → `{harga, qty, total}` dan `totalKeranjang(keranjang)` → `Σ(harga × qty)`, disalin persis dari `dashboard.blade.php`. Hasil: **`14 passed, 0 failed`**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| TOT-01 | Reguler tanpa varian, jumlah 1 | total 15.000 | ✅ Pass |
| TOT-02 | Ganti ukuran ke Large, jumlah 1 | total 22.000 | ✅ Pass |
| TOT-03 | Reguler + Mozzarella (+3.000), jumlah 1 | total 18.000 | ✅ Pass |
| TOT-04 | Reguler + Mozzarella + BBQ, jumlah 2 | total 40.000 (sama dengan POS-01) | ✅ Pass |
| TOT-05 | Large + Mozzarella, jumlah 3 | total 75.000 (sama dengan U04) | ✅ Pass |
| TOT-06 | Reguler + Balado (varian gratis +0) | total tetap 15.000 | ✅ Pass |
| TOT-07 | Reguler + Balado + Keju Bubuk (dua seasoning) | total 16.000 | ✅ Pass |
| TOT-08a | Jumlah = 0 | di-`clamp` ke 1 | ✅ Pass |
| TOT-08b | Jumlah = 100 (> maks 99) | di-`clamp` ke 99 | ✅ Pass |
| TOT-08c | Jumlah = teks kosong/non-angka | dianggap 1 | ✅ Pass |
| TOT-09 | Keranjang: 3 polos + 2 (Reguler+Mozzarella) | total 81.000 (sama dengan POS-02) | ✅ Pass |
| TOT-10 | Keranjang multi-item (1×15.000 + 2×25.000) | total 65.000 | ✅ Pass |
| TOT-11 | Kembalian tunai: dibayar 50.000, total 40.000 | kembalian 10.000 | ✅ Pass |
| TOT-12 | Kembalian non-tunai: dibayar = total | kembalian 0 | ✅ Pass |

### B. Backend — `CHIK-88` Tampilan & perhitungan total di form kasir

Tampilan total di form bersumber dari backend: `DashboardController::page()` menyuplai data harga ukuran (`harga`) & varian (`harga`) yang dirender pada form, lalu `PriceCalculator::breakdown` + `TransactionController::store` menghitung total otoritatif dan mengembalikan `total`/`dibayar`/`kembalian`.

#### B.1 — Tampilan total di form (review statis)

Lokasi: `resources/views/kasir/dashboard.blade.php` (panel "Pesanan Baru") dan `resources/views/components/kasir/pilih-varian.blade.php` (popup pilih varian).

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| TMP-01 | `#subtotalQty` / `#subtotal` | teks "Subtotal (n pcs)" & nilai = `totalKeranjang()` diperbarui tiap render | ✅ Pass (statis) |
| TMP-02 | `#total` | nilai total besar (Rp) = `totalKeranjang()` | ✅ Pass (statis) |
| TMP-03 | `#labelBayar` (tombol Bayar) | teks "Bayar · Rp …" mengikuti total | ✅ Pass (statis) |
| TMP-04 | `#jumlahItem` | "{count} item" = Σ qty item di keranjang | ✅ Pass (statis) |
| TMP-05 | `#rincianPorsi` (popup) | "n porsi × Rp …" jika qty > 1, atau "Harga per porsi" | ✅ Pass (statis) |
| TMP-06 | `#hargaPorsi` (popup) | harga total porsi = `(ukuran + varian) × qty` via `perbaruiHargaVarian` | ✅ Pass (statis) |
| TMP-07 | `#kembalian` | "Kembalian: Rp …" = `max(0, bayar − total)`; kosong bila belum diisi | ✅ Pass (statis) |
| TMP-08 | `#kasirJumlah` listener | input `jumlah` memicu `perbaruiHargaVarian` | ✅ Pass (statis) |

#### B.2 — Perhitungan total server (PHPUnit)

Lokasi: `tests/Unit/PriceCalculatorTest.php`, `tests/Feature/TransaksiKasirTest.php`. Hasil run: `OK (46 tests, 134 assertions)` — PHPUnit 12.5.42, PHP 8.3.30.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U01 | Tanpa varian | harga_satuan = 15.000 | ✅ Pass |
| U02 | Ganti ukuran | Reguler 15.000 / Large 22.000 | ✅ Pass |
| U03 | Mozzarella + BBQ + Keju Bubuk | tambahan 6.000; harga_satuan 21.000 | ✅ Pass |
| U04 | Large + Mozzarella × 3 | harga_satuan 25.000; subtotal 75.000 | ✅ Pass |
| U05–U11 | Jumlah 0/negatif, varian gratis, dua seasoning, id dobel, varian tidak valid | sesuai aturan `PriceCalculator` | ✅ Pass |
| POS-01 | Tunai: Reguler×2 + Mozzarella + BBQ, bayar 50.000 | total 40.000, dibayar 50.000, kembalian 10.000 | ✅ Pass |
| POS-02 | Dua baris ukuran sama (3 polos + 2 Mozzarella) | total 81.000 | ✅ Pass |
| POS-03 | Bayar kurang (1.000 < 15.000) | 422; tidak ada transaksi tersimpan | ✅ Pass |
| POS-04…POS-07 | Stok kurang, varian tidak valid, non-tunai kembalian 0, pelanggan ditolak | 422/403 sesuai aturan | ✅ Pass |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | Rendah (UX) | `hitungVarian()` meng-`clamp` jumlah > 99 menjadi 99 secara diam-diam, tetapi kolom input tetap menampilkan angka yang diketik (mis. "100"). Rincian baris bisa berbunyi "99 porsi × …" sementara input masih "100". | Ikat nilai input kembali ke hasil `clamp`, atau blokir input > 99 di sisi input. | ⚠️ Saran |
| T-02 | Info | Total dihitung **dua kali**: di frontend (`CHIK-86`, JS) dan backend (`CHIK-88`, `PriceCalculator`/`TransactionController`). Frontend menjumlah tanpa pembulatan sedangkan backend memakai `round(..., 2)`. Untuk harga Rupiah bulat hasilnya identik, tetapi bila kelak ada harga desimal (mis. 15.050,50) bisa ada selisih kecil. | Pertahankan sifat backend sebagai otoritas (validasi `amount_paid >= total`); pertimbangkan pembulatan seragam di frontend. | ℹ️ Catatan |
| T-03 | Info (positif) | Backend menghitung ulang total dari item dan menolak bila `amount_paid < total`, sehingga total yang tersimpan selalu benar meski kalkulasi frontend keliru (pertahanan berlapis). | Tidak perlu aksi; dipertahankan. | ✅ |
| T-04 | Sedang (tampilan) | Modul `components/kasir/pembayaran.blade.php` dan `struk.blade.php` (dari `CHIK-91`) menampilkan angka **hardcode** (Total Rp 61.000, Kembalian Rp 39.000, struk 2×20.000 + 1×21.000) dan **belum tersambung** ke total live. Alur `bayar()` saat ini memakai `alert()` + `location.reload()`, bukan modal tersebut. | Di luar scope CHIK-86/CHIK-88, tetapi perlu disatukan (wiring) pada Sprint berikutnya agar tampilan pembayaran/struk menampilkan total sesungguhnya. | ⚠️ Saran |
| T-05 | Info (historis) | Commit `CHIK-88` (`37df4fe`) sempat menyisipkan karakter backtick liar di `pilih-varian.blade.php` (baris `<p id="rincianPorsi"…`), yang langsung dibersihkan pada `CHIK-86` (`1de15df`). Tidak ada dampak pada `main` saat ini. | Tidak perlu aksi. | ℹ️ Catatan |
| T-06 | Info (lingkungan) | Build frontend (`npm install`) tetap terblokir di Windows karena `package.json` mengunci `@tailwindcss/oxide-linux-x64-gnu` (lihat T-03 laporan PPL-13), sehingga verifikasi `CHIK-86` dilakukan lewat replikasi Node.js + review statis, bukan klik di browser. | Rujuk `@tailwindcss/oxide` (tanpa pin platform) agar UI bisa diuji black-box di Windows. | ❌ Perlu perbaikan |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Node.js 26+, Composer di PATH.

1. **Verifikasi logika frontend `CHIK-86` (Node.js, tanpa build)**:
   ```
   node tests/uji-total-chikumi.mjs
   ```
   (replikasi 1:1 `hitungVarian` & `totalKeranjang` dari `dashboard.blade.php`; diharapkan `14 passed, 0 failed`).

2. **Tes otomatis backend `CHIK-88` (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="PriceCalculatorTest|TransaksiKasirTest|DashboardWiringTest|HargaDinamisTest"
   ```
   Skema tabel tes dibuat di `tests/Concerns/MenyiapkanDataChikumi.php` mengikuti `database/migrate.php`. Bila skema berubah, sesuaikan helper tersebut.

3. **Black-box UI (opsional, setelah T-06 diperbaiki)**:
   `composer install` + salin `.env.example` → `.env` + `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `kasir@chikumi.com` / `kasir123`, buka `/kasir/dashboard`; pilih ukuran → varian → jumlah → "Tambah ke Pesanan", lalu amati `#subtotal`, `#total`, `#labelBayar`, dan `#kembalian` saat metode bayar diubah.