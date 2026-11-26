# Laporan Testing — [PPL-XX] Agregasi Data Dashboard (Omzet, Pengeluaran, Laba, Transaksi, Stok)

- **Backlog**: [PPL-XX] Agregasi Data Dashboard — Jira `CHIK-145` (agregasi omzet, pengeluaran, laba, transaksi, stok)
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `CHIK-145-pengerjaan-agregasi-data-dashboard-omzet-pengeluaran-laba-transaksi-stok`, commit `9fd71d4`
- **Scope (mapping tim)**:
  - **Backend (tugas ini)**: `app/Http/Controllers/Owner/DashboardController.php` (`summary()` kini menyertakan `stok`), `app/Http/Controllers/Owner/ProductController.php` (`availability()` diekstrak menjadi `ketersediaan()` statis yang dipakai bersama endpoint ketersediaan & dashboard), `tests/Feature/DashboardWiringTest.php`, `tests/Feature/RekapPendapatanTest.php` (skema `order_items`).
  - **Di luar scope (tidak diubah)**: seluruh frontend (`resources/views/**`) dan test yang sudah ada; diuji sebagai regresi alur end-to-end.
- **Metode**:
  1. **PHPUnit** — suite alur end-to-end: kasir, pemesanan online, stok, keuangan (SQLite in-memory), 272 tes.
  2. **Verifikasi kontrak** — memastikan `GET /owner/api/dashboard` mengembalikan `data.stok` (ringkasan + produk + varian) tanpa merusak `omzet`, `pengeluaran`, `laba`, `transaksi`, `grafik`.
  3. **Regresi** — seluruh suite dijalankan untuk memastikan perubahan agregasi tidak merusak alur lain.
- **Terverifikasi**: 2026-11-26
- **Keterbatasan**: pengujian hanya lewat PHPUnit (SQLite in-memory). Uji browser/MySQL langsung tidak dijalankan pada sesi ini. Kartu Jira `CHIK-145` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog. **Mohon Product Owner mengonfirmasi di kartu.**

## Ringkasan

| Alur | Tests | Lulus | Gagal | Assertions |
|---|---|---|---|---|
| Kasir | 26 | 26 | 0 | 110 |
| Pemesanan online | 116 | 116 | 0 | 406 |
| Stok | 70 | 69 | 1 (lama) | 284 |
| Keuangan | 60 | 60 | 0 | 305 |
| **Total** | **272** | **271** | **1** | **1.105** |

**Hasil: LULUS DENGAN CATATAN.**

Perubahan agregasi (menambahkan `stok` ke response dashboard) **tidak menimbulkan regresi** di alur kasir, pemesanan online, maupun keuangan (100% lulus). Satu kegagalan pada alur stok (`PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat`) adalah **kegagalan lama** yang sudah ada sebelum perubahan ini: skema setup tes tidak membuat tabel `order_items` yang dibutuhkan `MovingAverage::harian()` saat merender `/owner/stok`. Di database produksi tabel tersebut ada.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-145` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Dashboard mengagregasi **omzet** (kasir + online) | ✅ Lulus | DashboardWiringTest U-02; RekapPendapatanTest |
| AC-2 | Dashboard mengagregasi **pengeluaran** | ✅ Lulus | DashboardWiringTest U-02; PencatatanPengeluaranTest |
| AC-3 | Dashboard menghitung **laba** & margin | ✅ Lulus | DashboardWiringTest U-02 |
| AC-4 | Dashboard menghitung **transaksi** (total/kasir/online) | ✅ Lulus | DashboardWiringTest U-02 |
| AC-5 | Dashboard menyajikan **stok** (aman/menipis/habis + saran) | ✅ Lulus | DashboardWiringTest U-02 (`data.stok.ringkasan/produk/varian`) |
| AC-6 | Endpoint ketersediaan tetap berfungsi sama | ✅ Lulus | PenandaStokMinimumTest, PrediksiStokTest |

## Tabel Test Case (agregasi dashboard)

### A. PHPUnit — perubahan langsung

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U-01 | `GET /owner/api/dashboard?periode=bulan` | 200; `omzet` 150.000, `pengeluaran` 40.000, `laba` 110.000 | ✅ Pass |
| U-02 | Response dashboard menyertakan `data.stok` | Memuat `ringkasan`, `produk`, `varian` | ✅ Pass |
| U-03 | `GET /owner/api/produk/ketersediaan` setelah ekstraksi | Status & ringkasan tidak berubah | ✅ Pass |
| U-04 | Render halaman `/owner/dashboard` & `/kasir/dashboard` | 200, memuat "Kondisi Stok" & data backend | ✅ Pass |

### B. Regresi alur kasir (26 tes)

| Kelompok | File | Tests | Status |
|---|---|---|---|
| Simpan transaksi, kembalian, stok, varian | `TransaksiKasirTest` | 8 | ✅ 8/8 |
| Riwayat transaksi kasir + online | `RiwayatTransaksiTest` | 12 | ✅ 12/12 |
| Pesanan online kasir (siap/selesai) | `PesananOnlineKasirTest` | 6 | ✅ 6/6 |

### C. Regresi alur pemesanan online (116 tes)

| Kelompok | File | Tests | Status |
|---|---|---|---|
| Keranjang | `KeranjangTest` | 17 | ✅ 17/17 |
| Ongkos kirim & kurir | `OngkosKirimTest` | 32 | ✅ 32/32 |
| Metode penerimaan & alamat | `PemesananOnlineTest` | 10 | ✅ 10/10 |
| Checkout & pembatalan | `CheckoutPesananTest` | 13 | ✅ 13/13 |
| Pembayaran sandbox (callback, polling, QR) | `PembayaranSandboxTest` | 21 | ✅ 21/21 |
| Harga dinamis ukuran & varian | `HargaDinamisTest` | 20 | ✅ 20/20 |

### D. Regresi alur stok (70 tes)

| Kelompok | File | Tests | Status |
|---|---|---|---|
| Tambah/kurangi stok & riwayat | `PenambahanStokTest` | 8 | ⚠️ 7/8 (U-05) |
| Pengurangan stok (kasir, online, batal, kedaluwarsa) | `PenguranganStokTest` | 13 | ✅ 13/13 |
| Penanda stok minimum & batas per produk | `PenandaStokMinimumTest` | 34 | ✅ 34/34 |
| Moving average & saran restock | `PrediksiStokTest` | 15 | ✅ 15/15 |

### E. Regresi alur keuangan (60 tes)

| Kelompok | File | Tests | Status |
|---|---|---|---|
| Pencatatan pengeluaran, buku transaksi, export | `PencatatanPengeluaranTest` | 27 | ✅ 27/27 |
| Rekap pendapatan & laba rugi | `RekapPendapatanTest` | 31 | ✅ 31/31 |
| Wiring dashboard kasir & owner | `DashboardWiringTest` | 2 | ✅ 2/2 |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| U-05 | **Rendah** | **Kegagalan lama (bukan regresi).** `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`. Setup tes memakai `MenyiapkanDataChikumi` yang tidak membuat tabel `orders`/`order_items`, padahal `pageStok()` memanggil `MovingAverage::harian()` yang meng-query `order_items`. Terbukti gagal juga pada commit sebelum perubahan agregasi. | Lengkapi skema tes stok dengan tabel `orders` & `order_items` (seperti pada `DashboardWiringTest`/`RekapPendapatanTest`). | ⚠️ Known issue |
| P-01 | **Info** | **`MovingAverage::harian()` ikut dieksekusi oleh agregasi dashboard**, sehingga `GET /api/dashboard` kini meng-query `pos_transaction_items`, `order_items`, `product_sizes`, `variants`. Beban tambahan kecil (1 query per tabel), tetapi berarti dashboard & endpoint ketersediaan berbagi sumber yang sama. | Bila dashboard mulai lambat, cache hasil `ketersediaan()` per menit. | ℹ️ Catatan |
| P-02 | **Info** | **`ProductController::ketersediaan()` dibuat `public static`** agar bisa dipanggil dari `DashboardController`. Ini menambah kopling antar-controller. | Bila tumbuh, pindahkan ke `App\Support`/service tersendiri. | ℹ️ Catatan |

## Catatan Lingkungan

- Tes dijalankan dengan PHP 8.3.6 (NTS) + ekstensi `mbstring`, `dom`, `pdo_sqlite`, dll. yang disiapkan lokal karena instalasi PHP 8.3 sistem tidak memiliki `mbstring`.
- Database tes: SQLite in-memory (`phpunit.xml`), tanpa menyentuh MySQL `chikumi`.
- Perintah yang dipakai:
  ```
  vendor/bin/phpunit --filter='TransaksiKasirTest|RiwayatTransaksiTest|PesananOnlineKasirTest'
  vendor/bin/phpunit --filter='KeranjangTest|OngkosKirimTest|PemesananOnlineTest|CheckoutPesananTest|PembayaranSandboxTest|HargaDinamisTest'
  vendor/bin/phpunit --filter='PenambahanStokTest|PenguranganStokTest|PenandaStokMinimumTest|PrediksiStokTest'
  vendor/bin/phpunit --filter='PencatatanPengeluaranTest|RekapPendapatanTest|DashboardWiringTest'
  ```

## Cara Menjalankan Ulang

1. **Suite alur end-to-end (SQLite in-memory):**
   ```
   php artisan test --filter='TransaksiKasirTest|RiwayatTransaksiTest|PesananOnlineKasirTest|KeranjangTest|OngkosKirimTest|PemesananOnlineTest|CheckoutPesananTest|PembayaranSandboxTest|HargaDinamisTest|PenambahanStokTest|PenguranganStokTest|PenandaStokMinimumTest|PrediksiStokTest|PencatatanPengeluaranTest|RekapPendapatanTest|DashboardWiringTest'
   ```
   Diharapkan **271 lulus, 1 gagal lama** (U-05).
2. **Cek kontrak dashboard:**
   ```
   curl -s -H 'Accept: application/json' http://localhost:8000/owner/api/dashboard?periode=bulan
   ```
   Pastikan ada `data.stok.ringkasan`, `data.stok.produk`, `data.stok.varian` di samping `omzet`, `pengeluaran`, `laba`, `transaksi`, `grafik`.
3. **Regresi penuh:** `php artisan test` → selain U-05, `ExampleTest` juga gagal lama (`GET /` → 302, keduanya di luar scope).
