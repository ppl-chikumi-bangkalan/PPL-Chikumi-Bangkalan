# Laporan Testing — [PPL-09] Form Penambahan Stok & Riwayat Stok

- **Backlog**: [PPL-09] Pengerjaan form penambahan stok & riwayat stok — Jira `CHIK-40` (subtask `CHIK-93` pengerjaan form penambahan stok & riwayat stok); tiket uji penerimaan `CHIK-94` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `4de4540` (sudah memuat `CHIK-93` via PR #27).
- **Scope (mapping tim)**:
  - `CHIK-93` — sisi **frontend**: halaman `owner/produk/stok.blade.php` (tabel stok + input "Tambah" + tombol "Simpan Stok") dan komponen baru `components/owner/riwayat-stok.blade.php` (tabel riwayat stok + filter Semua/Masuk/Keluar).
  - Backend pendukung yang diuji (sudah ada dari backlog sebelumnya): `Owner\ProductSizeController::updateStok` (endpoint `PATCH /owner/api/ukuran/{ukuran}/stok` jenis `in`/`out` + pencatatan `StockHistory`) dan `Owner\ProductController::pageStok` (render halaman stok).
- **Metode**:
  1. **Tes otomatis PHPUnit**: feature test baru `PenambahanStokTest` (8 skenario) + regresi (`TransaksiKasirTest`, `PriceCalculatorTest`, `HargaDinamisTest`, `DashboardWiringTest`).
  2. **Review statis**: alur JS form (`stok.blade.php`: pengumpulan input stok → `PATCH` per ukuran) dan komponen riwayat (`riwayat-stok.blade.php`), karena build frontend tidak dapat dijalankan di mesin uji (lihat T-07).
- **Terverifikasi**: 2026-11-13

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — `PenambahanStokTest` (fitur stok, PPL-09) | 8 | 8 | 0 | 100% |
| PHPUnit — `TransaksiKasirTest` (regresi) | 8 | 8 | 0 | 100% |
| PHPUnit — `PriceCalculatorTest` (regresi) | 14 | 14 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi) | 23 | 23 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (regresi) | 2 | 2 | 0 | 100% |
| **Total** | **55** | **55** | **0** | **100%** |

**Form penambahan stok berfungsi** end-to-end: owner memasukkan jumlah tambahan → "Simpan Stok" → `PATCH /owner/api/ukuran/{id}/stok` → stok bertambah & tercatat di tabel `stock_histories`. Namun komponen **"Riwayat Stok" masih statis/hardcode** (lihat T-01) sehingga hasil penambahan belum tampil di riwayat. Satu kegagalan pada suite penuh (57 tes) adalah `ExampleTest` kedaluwarsa di luar scope.

## Kriteria Penerimaan yang Diuji

Kartu Jira belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item `CHIK-40`/`CHIK-93`/`CHIK-94`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Owner melihat sisa stok, minimum, dan saran penambahan per ukuran | POS-stok-08, review `pageStok` |
| AC-2 | Owner mengisi jumlah lalu "Simpan Stok" menambah stok | POS-stok-01, POS-stok-02 |
| AC-3 | Penambahan tercatat di riwayat stok (`stock_histories`) | POS-stok-01, POS-stok-02 |
| AC-4 | Pengurangan/tipe tidak valid/jumlah ≤ 0 ditolak | POS-stok-03, POS-stok-04, POS-stok-05, POS-stok-06 |
| AC-5 | Hanya owner yang boleh mengubah stok | POS-stok-07 |
| AC-6 | Riwayat stok menampilkan data masuk & keluar (dengan filter) | review `riwayat-stok.blade.php` (lihat T-01) |

## Data Uji

| Item | Tipe | Stok | Min Stok |
|---|---|---|---|
| Chikumi Original Box · Regular | Ukuran | 20 | 5 |
| Chikumi Original Box · Large | Ukuran | 3 | 5 |
| Chikumi Spicy Box · Regular | Ukuran | 10 | 5 |

(Identik dengan `tests/Concerns/MenyiapkanDataChikumi.php`; akun owner dibuat langsung di test: `owner.uji@gmail.com`.)

## Tabel Test Case

### A. Backend — form penambahan stok (PHPUnit `PenambahanStokTest`)

Lokasi: `tests/Feature/PenambahanStokTest.php`. Endpoint: `PATCH /owner/api/ukuran/{ukuran}/stok`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| POS-stok-01 | Owner tambah stok `in` +5 (Regular 20) | stok 25; `stock_histories` tercatat `type=in, quantity=5, source=manual_restock` | ✅ Pass |
| POS-stok-02 | Owner tambah stok +10 dengan catatan (Large 3) | stok 13; `notes` "Persiapan buka 15.00" tersimpan | ✅ Pass |
| POS-stok-03 | Owner kurangi stok `out` −3 (Regular 20) | stok 17; `stock_histories` `type=out` tercatat | ✅ Pass |
| POS-stok-04 | Kurangi melebihi sisa (`out` 21 > 20) | 422; stok tetap 20; tidak ada riwayat | ✅ Pass |
| POS-stok-05 | Tipe tidak valid (`transfer`) | 422 `type`; stok tetap | ✅ Pass |
| POS-stok-06 | Jumlah 0 | 422 `quantity`; stok tetap | ✅ Pass |
| POS-stok-07 | Pelanggan mengubah stok | 403 forbidden; stok tetap | ✅ Pass |
| POS-stok-08 | `GET /owner/stok` | 200; memuat "Stok & Saran", "Riwayat Stok", "Simpan Stok", dan nama produk | ✅ Pass |

### B. Frontend — alur form & riwayat (review statis)

Lokasi: `resources/views/owner/produk/stok.blade.php`, `resources/views/components/owner/riwayat-stok.blade.php`.

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| UI-01 | Input "Tambah" (`data-size-id`, `data-default`) | hanya ukuran (bukan varian) yang bisa diisi; saran stok terisi otomatis | ✅ Pass (statis) |
| UI-02 | `#itemCount` | jumlah komponen dengan nilai > 0, diperbarui tiap input | ✅ Pass (statis) |
| UI-03 | `#resetStock` | mengembalikan ke nilai `data-default` | ✅ Pass (statis) |
| UI-04 | `#saveStock` | kirim `PATCH {type:'in', quantity}` per input > 0, lalu `location.reload()` | ✅ Pass (statis) |
| UI-05 | Filter kategori (`Kulit/Filling/Dip Sauce/Seasoning`) | JS `filterTable` menyembunyikan baris selain kategori | ✅ Pass (statis) |
| UI-06 | Riwayat stok — filter Semua/Masuk/Keluar | CSS `group-has-` (bukan JS) menyembunyikan baris sesuai filter | ⚠️ Lihat T-04 |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | **Sedang (blokir riwayat)** | Komponen `components/owner/riwayat-stok.blade.php` berisi data **statis/hardcode** (contoh `13/11 08.15`, `+20 pcs`, `#CK-0125`, `Owner Chikumi`) dan **tidak tersambung** ke tabel `stock_histories`. `pageStok()` tidak mengirim data riwayat ke view, dan tidak ada endpoint untuk mengambil riwayat. Akibatnya hasil penambahan stok nyata tidak muncul di riwayat. | Tambah metode/endpoint riwayat (`GET /owner/api/riwayat-stok`), kirim ke view, dan render dari `StockHistory` + relasi `productSize`/`user`. | ❌ Perlu perbaikan |
| T-02 | Sedang (scope) | Branch `CHIK-93` mencampur perubahan **tidak terkait**: `customer/checkout.blade.php` (220 baris), route `/customer/checkout`, dan pengubahan link checkout di `keranjang.blade.php` (milik CHIK-99/checkout). | Pisahkan checkout ke commit/PR-nya sendiri agar review stok bersih. | ⚠️ Saran |
| T-03 | Rendah | Label `SUMBER` pada riwayat statis ("Tambah stok", "Transaksi kasir", "Pesanan online") tidak sama dengan nilai `source` riil backend (`manual_restock`, `pos_transaction`). | Petakan enum `source` ke label saat wiring riwayat. | ℹ️ Catatan |
| T-04 | Rendah | Filter riwayat memakai CSS `has-` (non-JS), sedangkan filter tabel utama memakai JS `filterTable` — dua mekanisme berbeda. Saat data riwayat dinamis (paginasi) diterapkan, filter CSS perlu diganti logika server/JS. | Samakan pendekatan filter riwayat. | ⚠️ Saran |
| T-05 | Info | Form hanya mengirim `type: 'in'` (tambah stok). Backend mendukung `out` (kurangi manual), tapi tidak ada UI-nya. | Konfirmasi ke PO apakah pengurangan manual diperlukan di halaman stok. | ℹ️ Catatan |
| T-06 | Info | `saveStock` memuat ulang seluruh halaman (`location.reload()`) setelah simpan, bukan pembaruan parsial. | Pertimbangkan partial update. | ℹ️ Catatan |
| T-07 | Info (lingkungan) | Build frontend tetap terblokir di Windows (`@tailwindcss/oxide-linux-x64-gnu`, lihat T-03 laporan PPL-13), sehingga verifikasi frontend lewat review statis. | Rujuk `@tailwindcss/oxide` (tanpa pin platform). | ❌ Perlu perbaikan |
| T-08 | Info | `tests/Feature/ExampleTest.php` bawaan gagal (`GET /` → 302), di luar scope PPL-09. | Hapus atau ubah jadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer di PATH.

1. **Tes otomatis stok & regresi (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="PenambahanStokTest|TransaksiKasirTest|PriceCalculatorTest|DashboardWiringTest|HargaDinamisTest"
   ```
   Skema tabel tes mengikuti `database/migrate.php` di `tests/Concerns/MenyiapkanDataChikumi.php` (tabel `stock_histories` sudah tersedia). Bila skema berubah, sesuaikan helper tersebut.

2. **Black-box UI (opsional, setelah T-07 diperbaiki)**:
   `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `owner@chikumi.com` / `owner123`, buka `/owner/stok`, isi kolom "Tambah", klik **Simpan Stok**, lalu periksa sisa stok bertambah dan amati tab **Network** DevTools (endpoint `PATCH /owner/api/ukuran/{id}/stok`).