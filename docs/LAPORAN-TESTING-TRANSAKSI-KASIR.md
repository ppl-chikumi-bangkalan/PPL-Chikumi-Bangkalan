# Laporan Testing — [PPL-13] Pencatatan Transaksi Kasir

- **Backlog**: [PPL-13] Pencatatan Transaksi Kasir — Jira `CHIK-36` (berisi `CHIK-83` backend penyimpanan transaksi & detail varian, `CHIK-84` frontend form transaksi kasir); tiket testing `CHIK-85` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `cfdf2c3` (PR #18 — merge `CHIK-83` ke `main`, sudah memuat PR #17 `CHIK-84`). Hasil identik saat diuji di branch `CHIK-83` (`a158d0b`) sebelum merge.
- **Metode**:
  1. **Tes otomatis PHPUnit**: feature test `TransaksiKasirTest` (endpoint `POST /kasir/api/transaksi`), `DashboardWiringTest` (render halaman kasir/owner + ringkasan omzet), unit test `PriceCalculatorTest` (SQLite in-memory)
  2. **Literatur/review statis**: alur JS form (`resources/views/kasir/dashboard.blade.php`) karena build frontend tidak dapat dijalankan di mesin uji (lihat Temuan T-03)
- **Terverifikasi**: 2026-11-09

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — `TransaksiKasirTest` (CHIK-83) | 7 | 7 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (wiring kasir/owner) | 2 | 1 | 1 | 50% |
| PHPUnit — `PriceCalculatorTest` (logika harga bersama) | 15 | 15 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi PPL-20) | 23 | 23 | 0 | 100% |
| PHPUnit — `ExampleTest` (di luar scope) | 1 | 0 | 1 | 0% |
| **Total** | **48** | **46** | **2** | **95,8%** |

Backend penyimpanan transaksi & detail varian (CHIK-83) **berfungsi penuh** — seluruh skenario inti lulus. Kedua kegagalan bukan cacat pada fitur, melainkan lingkungan (Vite manifest tidak ter-build karena ketergantungan Tailwind terkunci platform Linux, lihat T-03) dan test bawaan Laravel yang kedaluwarsa (T-05).

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-36`/`CHIK-85` (subtask `CHIK-83`/`CHIK-84`) belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Kasir merekam transaksi: metode bayar (tunai/non-tunai), jumlah bayar, kembalian | POS-01, POS-06 |
| AC-2 | Detail item tersimpan: produk, ukuran, harga satuan, jumlah, subtotal | POS-01 |
| AC-3 | Detail varian per item tersimpan (varian + harga tambahan) | POS-01 |
| AC-4 | Stok berkurang otomatis & tercatat di `stock_histories` | POS-01, POS-02 |
| AC-5 | Validasi tolak: uang kurang, stok kurang, varian tidak valid | POS-03, POS-04, POS-05 |
| AC-6 | Hanya kasir/owner yang boleh; pelanggan ditolak | POS-07 |

## Data Uji

PHPUnit memakai data yang dibangun helper `tests/Concerns/MenyiapkanDataChikumi.php` (sama dengan `database/seed.php`, ditambah varian berbayar/‑habis & produk lain):

| Item | Tipe | Harga |
|---|---|---|
| Reguler (stok 20) | Ukuran | Rp 15.000 |
| Large (stok 3) | Ukuran | Rp 22.000 |
| Mozzarella Cheese | filling | +Rp 3.000 |
| Barbeque Sauce | dipping_sauce | +Rp 2.000 |
| Balado Extra Pedas | seasoning | +Rp 0 |

## Tabel Test Case — Tes Otomatis

Lokasi: `tests/Feature/TransaksiKasirTest.php`, `tests/Feature/DashboardWiringTest.php`, `tests/Unit/PriceCalculatorTest.php`.

### CHIK-83 — Backend `POST /kasir/api/transaksi` (login kasir)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| POS-01 | Tunai: Reguler ×2 + Mozzarella + BBQ, bayar 50.000 | 201; total 40.000, dibayar 50.000, kembalian 10.000; tabel `pos_transactions`, `pos_transaction_items` (unit_price 20.000, qty 2, subtotal 40.000), `pos_item_variants` (Mozzarella 3.000, BBQ 2.000); stok 20→18; `stock_histories` tercatat | ✅ Pass |
| POS-02 | Dua baris ukuran sama (3 polos + 2 Mozzarella) | total 81.000; 2 item; stok berkurang 5 (20→15); satu `stock_histories` qty 5 | ✅ Pass |
| POS-03 | Bayar kurang (1.000 < 15.000) | 422; stok tetap 20; tidak ada transaksi tersimpan | ✅ Pass |
| POS-04 | Stok kurang (qty 21 > 20) | 422; stok tetap; tidak ada transaksi | ✅ Pass |
| POS-05 | Varian tidak valid (Mozzarella + milik produk lain) | 422 `variant_ids`; tidak ada transaksi | ✅ Pass |
| POS-06 | Non-tunai, bayar = total | 201; kembalian 0; metode `non_cash` | ✅ Pass |
| POS-07 | Login sebagai pelanggan | 403 forbidden | ✅ Pass |

### Wiring halaman kasir & owner

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| DW-01 | `GET /kasir/dashboard` & `/owner/dashboard` (login) | 200 & memuat data backend | ❌ Blocked (T-03, Vite manifest) |
| DW-02 | `GET /owner/api/dashboard` ringkasan omzet/pengeluaran/laba | 200; omzet 150.000, pengeluaran 40.000, laba 110.000, 2 transaksi | ✅ Pass |

### Logika harga bersama (`PriceCalculator`)

Seluruh skenario unit (U01–U11, termasuk varian dobel, dua filling, dua dip, varian habis, varian milik produk lain) **lulus**. Lihat `docs/LAPORAN-TESTING-HARGA-DINAMIS.md` untuk rincian.

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | Sedang | Aturan **filling wajib pilih 1** hanya dicek di JavaScript form (`dashboard.blade.php` `tambah()` menampilkan "Pilih filling dulu"). Endpoint `POST /kasir/api/transaksi` memakai `PriceCalculator::resolveVariants` yang **tidak** mewajibkan filling, sehingga request langsung ke API sukses menyimpan item tanpa filling. (Kelanjutan temuan T-01 laporan PPL-20.) | Tegakkan aturan di server (mis. di `resolveVariants`/`TransactionController`) bila filling bersifat wajib; tambah feature test kasus tanpa filling. | ❌ Perlu perbaikan |
| T-02 | Rendah (UI) | Bagian "Pilihan Rasa Hari Ini" bertuliskan **"Harga tidak berubah oleh filling"**, dan kartu ukuran hanya menampilkan chip "+ Seasoning" / "+ Dip Sauce" (tanpa "+ Filling"), padahal filling punya `additional_price` (Mozzarella +3.000) dan transaksi mengenakan biayanya. | Selaraskan teks & chip harga dengan aturan harga sebenarnya, atau konfirmasi ke PO bila filling memang digratiskan. | ⚠️ Saran |
| T-03 | Sedang (lingkungan) | `package.json` mengunci `@tailwindcss/oxide-linux-x64-gnu` (platform **linux**) langsung sebagai `devDependencies`, sehingga `npm install` gagal di Windows (`EBADPLATFORM`), frontend tidak dapat di-build, dan test render `DashboardWiringTest::test_halaman_...` gagal (`ViteManifestNotFoundException` di `public/build/manifest.json`). | Rujuk `@tailwindcss/oxide` (biarkan npm memilih binari platform) atau hapus pin platform dari `package.json`. | ❌ Perlu perbaikan |
| T-04 | Info | Checkout bersih tanpa `.env` membuat seluruh HTTP test gagal `MissingAppKeyException` sampai `.env` dibuat dan `php artisan key:generate`. `composer` juga tidak ada di PATH mesin uji ini (vendor sudah ter-install). | Dokumentasikan prasyarat composer + key generate di CI/README. | ℹ️ Catatan |
| T-05 | Info | `tests/Feature/ExampleTest.php` bawaan Laravel gagal (`GET /` → 302 ke `/login`). Di luar scope PPL-13. | Ubah menjadi `assertRedirect('/login')` atau hapus. | ℹ️ Known issue |
| T-06 | Info | Route dev `POST /frontend/kasir/api/transaksi` terbuka tanpa login (cukup header `X-Dev-Kasir`), bertanda "*sementara*". | Pastikan route ini dihapus sebelum rilis produksi. | ℹ️ Catatan |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer di PATH, Node.js, MySQL (untuk black‑box nyata).

1. `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`.
2. Tes otomatis (SQLite in‑memory, tak menyentuh MySQL):
   ```
   php artisan test --filter="TransaksiKasirTest|DashboardWiringTest|PriceCalculatorTest"
   ```
   Skema tabel tes dibuat di `tests/Concerns/MenyiapkanDataChikumi.php` mengikuti `database/migrate.php`. Bila skema berubah, sesuaikan helper tersebut.
3. Build frontend (blokir T-03 di Windows: perbaiki `package.json` dulu):
   ```
   npm install && npm run build
   ```
4. Black‑box UI: `php database/migrate.php` + `php database/seed.php` pada DB uji, `php artisan serve`, login sebagai `kasir@chikumi.com` / `kasir123`, buka `/kasir/dashboard`, lalu rekam transaksi sambil memantau tab Network DevTools (endpoint `POST /kasir/api/transaksi`).