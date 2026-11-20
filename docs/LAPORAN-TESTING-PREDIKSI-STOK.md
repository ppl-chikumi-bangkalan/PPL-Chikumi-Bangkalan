# Laporan Testing — [PPL-07] Prediksi Kebutuhan Stok (Moving Average)

- **Backlog**: [PPL-07] Prediksi Kebutuhan Stok (Moving Average) — Jira `CHIK-53` (berisi `CHIK-125` seeder riwayat penjualan 4–8 minggu, `CHIK-126` perhitungan moving average 3 minggu & saran restock, `CHIK-127` frontend kartu prediksi stok); tiket testing `CHIK-128`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `3d1dcb4`. Ketiga subtask sudah merge: `CHIK-125` → PR #42 (`4667b58`), `CHIK-126` → PR #43 (`2b8c58a`), `CHIK-127` → PR #38 (`9bc57a5`, `5946892`). Pengujian di branch `CHIK-128-testing-prediksi-stok`.
- **Scope (mapping tim)**:
  - `CHIK-126` — **perhitungan**: `app/Support/MovingAverage.php` (`harian()`, `saran()`, `cukupHari()`), dipakai `Owner\ProductController::pageStok()` dan `::availability()`.
  - `CHIK-127` — **frontend**: kartu "Kondisi Stok" di `resources/views/owner/dashboard.blade.php` + tabel "Stok & Saran" di `resources/views/owner/produk/stok.blade.php`.
  - `CHIK-125` — **data**: `database/seed_riwayat_penjualan.php` (riwayat 4–8 minggu).
- **Metode**:
  1. **PHPUnit** `tests/Feature/PrediksiStokTest.php` — 15 tes yang **mencocokkan hasil `MovingAverage` dengan hitungan manual** pada dataset terkontrol (SQLite in-memory).
  2. **Hitungan manual SQL** di database nyata — query terpisah tanpa lewat `MovingAverage`, lalu dibandingkan angka per ukuran.
  3. **Black-box browser** — dashboard owner & halaman Produk & Stok, pantau request jaringan dan console.
- **Terverifikasi**: 2026-11-21
- **Keterbatasan**: kartu Jira `CHIK-53`/`CHIK-128` tidak diakses dari mesin uji; kriteria diturunkan dari judul work item.

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — perhitungan vs hitungan manual (`PrediksiStokTest`) | 15 | 15 | 0 | 100% |
| Hitungan manual SQL (data nyata) | 2 ukuran | 2 | 0 | 100% |
| Black-box browser — kartu prediksi | 6 | 2 | 4 | 33,3% |
| **Kriteria penerimaan (AC-1..AC-6)** | **6** | **3** | **3** | **50%** |
| Regresi suite penuh | 155 | 152 | 2 (1 di antaranya regresi `CHIK-126`) + 1 skipped | 98,1% |

**Hasil: LULUS DENGAN CATATAN — rumusnya benar, tetapi kartu prediksi di dashboard tidak pernah tampil.**

Perhitungan `MovingAverage` **akurat**: hasil kode cocok persis dengan hitungan manual, baik pada dataset terkontrol (16 pcs / 21 hari = 0,76) maupun pada data seeder nyata (110 pcs / 21 hari = 5,24 untuk kedua ukuran). Jendela 21 hari, pengecualian transaksi batal/belum lunas, dan rumus saran restock semuanya sesuai.

Namun **kartu "Kondisi Stok" di dashboard owner selalu kosong**. Penyebabnya: frontend memanggil `/owner/api/ketersediaan`, sedangkan route yang ada `/owner/api/produk/ketersediaan` → **404**. Karena pemanggilan dibungkus `Promise.all`, kegagalan itu **mengosongkan seluruh dashboard** (omzet Rp 0, grafik kosong) padahal API ringkasannya mengembalikan data benar (Rp 4.420.000 / 108 transaksi). Halaman Produk & Stok sendiri berfungsi normal.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-53`/`CHIK-128` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Moving average dihitung dari penjualan 3 minggu terakhir (kasir + online) | ✅ Lulus | MA-01..MA-05, MAN-01 |
| AC-2 | Transaksi batal / belum dibayar / di luar jendela tidak dihitung | ✅ Lulus | MA-01, MA-02 |
| AC-3 | Saran restock = kebutuhan 7 hari − sisa stok, minimal mengikuti minimum stok | ✅ Lulus | SR-01..SR-03, MAN-02 |
| AC-4 | Seeder menyediakan riwayat 4–8 minggu dan aman dijalankan ulang | ✅ Lulus | SD-01, SD-02, MAN-03 |
| AC-5 | Kartu prediksi stok tampil di dashboard owner dengan angka benar | ❌ Gagal — kartu kosong (P-01) | UI-01..UI-04 |
| AC-6 | Halaman Produk & Stok menampilkan prediksi & saran yang konsisten | ❌ Sebagian — angka benar, tetapi status vs saran bertentangan dan kolom "Cukup Untuk" tidak pernah berisi (P-02, P-03) | UI-05, UI-06 |

## Data Uji

**Dataset terkontrol (PHPUnit)**: Reguler stok 20/harga Rp 15.000, Large stok 3/harga Rp 22.000.

**Data seeder nyata** (database `chikumi`, hasil `migrate.php` + `seed.php` + `seed_riwayat_penjualan.php`):

| Item | Nilai |
|---|---|
| Rentang riwayat | 2026-09-27 s.d. 2026-11-20 (55 hari ≈ 8 minggu) |
| Transaksi kasir seeder | 215 (214 `paid`, 1 `cancelled`) |
| Pesanan online seeder | 74 (66 `completed`, 2 `processing`, 5 `cancelled`, 1 `unpaid`) |
| Total baris `stock_histories` | 423 |

## Tabel Test Case

### A. Pencocokan dengan hitungan manual (PHPUnit)

Lokasi: `tests/Feature/PrediksiStokTest.php`. Hasil: **15 passed, 33 assertions**.

| ID | Skenario | Hitungan manual | Hasil kode | Status |
|---|---|---|---|---|
| MA-01 | Kasir 3 (hari ini) + 4 (5 hari lalu) + 7 (20 hari lalu), online lunas 2 (2 hari lalu); **diabaikan**: kasir 100 (21 hari lalu), kasir 50 batal, online 9 belum bayar, online 8 batal | (3+4+7+2) / 21 = **0,76** | 0,76 | ✅ Pass |
| MA-02 | Batas jendela: 21 pcs pada hari ke-20 vs 210 pcs pada hari ke-21 | 21 / 21 = **1,0** | 1,0 | ✅ Pass |
| MA-03 | Ukuran tanpa penjualan | tidak muncul di hasil | tidak muncul | ✅ Pass |
| MA-04 | Kasir 10 + online 11 pada hari yang sama | 21 / 21 = **1,0** | 1,0 | ✅ Pass |
| MA-05 | Pembulatan dua desimal: 1 pcs | 1 / 21 = **0,05** | 0,05 | ✅ Pass |

### B. Saran restock & cukup hari (PHPUnit)

| ID | Skenario | Hitungan manual | Hasil kode | Status |
|---|---|---|---|---|
| SR-01 | rata 0,76/hari, stok 5, minimum 5 | butuh 7 hari = 5,33 → 6; target max(5,6)=6 → saran **1** | 1 | ✅ Pass |
| SR-02 | rata 0,76/hari, stok 0, minimum 5 | target max(5,6)=6 → saran **6** | 6 | ✅ Pass |
| SR-03 | rata 0,3/hari (butuh 2,1 pcs), stok 2, minimum 5 | target max(5,3)=5 → saran **3** | 3 | ✅ Pass |
| SR-04 | stok sudah cukup (20 pcs, rata 1,0) | saran **0** | 0 | ✅ Pass |
| SR-05 | cukup hari: 21 pcs / 1,0 per hari | **21 hari** | 21 | ✅ Pass |
| SR-06 | cukup hari: 5 pcs / 0,76 per hari | 6,57 → **6 hari** | 6 | ✅ Pass |
| SR-07 | cukup hari tanpa penjualan | **null** (tidak bisa diprediksi) | null | ✅ Pass |

### C. API & tampilan (PHPUnit)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| API-01 | `GET /owner/api/produk/ketersediaan` mengirim `rata_harian` & `saran_restock` | 0,76 dan 1 pcs untuk stok 5/min 5 | ✅ Pass |
| API-02 | Ringkasan status: aman + menipis + habis = total | konsisten | ✅ Pass |
| API-03 | Pelanggan mengakses API prediksi | 403 | ✅ Pass |
| API-04 | Halaman dashboard owner memuat kartu "Kondisi Stok" | ada judul, kolom SARAN, link "Lihat Produk & Stok" | ✅ Pass |

### D. Seeder (PHPUnit)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| SD-01 | Rentang minggu dijepit 4–8 | `max(4, min(8, ...))` | ✅ Pass |
| SD-02 | Aman dijalankan ulang (tanpa duplikat) | `DELETE … LIKE 'POS-SEED-%'` dan `'CHKM-SEED-%'` sebelum insert | ✅ Pass |

### E. Black-box browser

| ID | Skenario | Hasil Diharapkan | Hasil Nyata | Status |
|---|---|---|---|---|
| UI-01 | Buka `/owner/dashboard` (login owner) | kartu "Kondisi Stok" berisi komponen + saran | ringkasan **"0 komponen" ×3**, tabel **kosong** | ❌ Gagal |
| UI-02 | Request jaringan dashboard | `200` dari endpoint prediksi | **`404`** `/owner/api/ketersediaan` | ❌ Gagal |
| UI-03 | Ringkasan omzet dashboard | tampil sesuai API | **Rp 0** / 0 transaksi, grafik kosong (padahal API: Rp 4.420.000 / 108 transaksi) | ❌ Gagal |
| UI-04 | `/frontend/owner/dashboard` (pratinjau) | sama seperti dashboard login | juga kosong, `404` `/frontend/owner/api/ketersediaan` | ❌ Gagal |
| UI-05 | Buka `/owner/stok` | angka prediksi & saran benar | Reguler & Large: TERPAKAI/HARI **5,2 pcs**, SARAN **16 pcs**, 0 error console | ✅ Pass |
| UI-06 | Kolom "CUKUP UNTUK" | berisi jumlah hari | **selalu kosong** (kolom hanya ada di tabel varian) | ❌ Gagal |
| UI-07 | Panggil `renderStok()` dengan endpoint yang benar | kartu terisi | 2 baris tampil: "21 pcs · +16 pcs · Perlu Restock" | ✅ Pass (membuktikan P-01 murni salah URL) |

### F. Hitungan manual pada data nyata (SQL)

Query terpisah tanpa `MovingAverage`, jendela mulai `2026-10-31 00:00:00`:

| Ukuran | Kasir | Online | Total | Rata manual (÷21) | Rata kode | Cocok |
|---|---|---|---|---|---|---|
| Regular (id 1) | 88 | 22 | 110 | **5,24** | 5,24 | ✅ |
| Large (id 2) | 85 | 25 | 110 | **5,24** | 5,24 | ✅ |

Data yang benar tidak dihitung: 265 pcs di luar jendela, 2 pcs POS bukan `paid`, 8 pcs pesanan belum lunas.

Saran restock manual (Regular): stok 21, minimum 8 → target max(8, ceil(5,24 × 7)=37) = 37 → saran **16 pcs**. Sama dengan tampilan. ✅

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| P-01 | **Blocker** | **Kartu prediksi stok di dashboard owner tidak pernah tampil.** `owner/dashboard.blade.php` memanggil `ambil('/ketersediaan')` dengan `API_BASE = '/owner/api'`, sehingga URL-nya `/owner/api/ketersediaan` — route itu **tidak ada** (yang ada `/owner/api/produk/ketersediaan`). Respons **404**. Karena `Promise.all([dashboard, ketersediaan])` gagal, **seluruh dashboard ikut kosong** (omzet Rp 0, laba Rp 0, grafik tanpa batang) meski `GET /owner/api/dashboard` mengembalikan Rp 4.420.000 / 108 transaksi. Bug ini berasal dari `a158d0b` (CHIK-83) dan belum pernah diperbaiki; `CHIK-127` hanya mengubah teks & link sehingga tidak menutupinya. | Ubah pemanggilan menjadi `ambil('/produk/ketersediaan')` (atau tambahkan alias route `/owner/api/ketersediaan`). Pisahkan juga `Promise.all` → `Promise.allSettled` agar satu endpoint gagal tidak mengosongkan seluruh dashboard. | ❌ Perlu perbaikan |
| P-02 | **Sedang** | **Status dan saran saling bertentangan.** Komponen dengan stok 21 (minimum 8) berstatus `aman` dan dihitung pada kartu "Aman", tetapi tetap muncul di tabel "yang perlu restock" dengan label **"Perlu Restock"** dan saran **+16 pcs**. Penyebab: `availability()` menghitung `status` hanya dari `stock <= min_stock`, sedangkan tabel memfilter `status !== 'aman' || saran_restock > 0`. Karena saran memakai target 7 hari penjualan (37 pcs), hampir semua komponen akan selalu "Perlu Restock". | Selaraskan definisi: masukkan `saran_restock > 0` sebagai salah satu kriteria status (mis. status `perlu_restock`), atau ubah label baris menjadi "Saran restock" agar tidak terbaca sebagai peringatan. | ⚠️ Perlu keputusan |
| P-03 | **Sedang** | **Kolom "CUKUP UNTUK" tidak pernah berisi.** `cukupHari()` dihitung untuk setiap ukuran (`stok.blade.php:30`), tetapi kolom itu hanya ada di tabel **kedua** yang isinya varian (`$restockRows`), dan semua baris varian diberi `'cukup' => '-'` (baris 60). Jadi prediksi "stok cukup untuk berapa hari" — inti fitur PPL-07 — tidak pernah terlihat pengguna. | Tambahkan kolom "Cukup Untuk" di tabel pertama (Kulit & Filling) yang berisi ukuran produk, atau tampilkan di kartu dashboard. | ❌ Perlu perbaikan |
| P-04 | **Sedang** | **Varian yang habis tidak muncul di tabel kartu.** `renderStok()` hanya memetakan `data.produk`; `data.varian` diabaikan. Saat `Mozzarella Cheese` di-set tidak tersedia, ringkasan berubah menjadi "Habis 1 komponen" tetapi tabel tetap hanya menampilkan 2 baris ukuran — pengguna melihat angka 1 tanpa bisa tahu komponen mana. | Sertakan `data.varian` yang berstatus tidak aman pada `perlu`, atau tampilkan di bagian terpisah. | ❌ Perlu perbaikan |
| P-05 | **Sedang (regresi)** | **`CHIK-126` memecahkan tes tiket lain.** `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`: `pageStok()` kini memanggil `MovingAverage::harian()` yang membaca `orders`/`order_items`, tetapi skema tes stok belum membuat tabel tersebut. Ini regresi nyata dari pekerjaan PPL-07, bukan isu lama. | Tambahkan tabel `orders` + `order_items` ke skema `PenambahanStokTest`, atau buat `MovingAverage::harian()` tahan bila tabel belum ada. | ❌ Perlu perbaikan |
| P-06 | Rendah | **Pembagi selalu 21 hari**, termasuk untuk toko yang baru berjalan. Bila data baru tersedia 7 hari, rata-rata tetap dibagi 21 sehingga prediksi underestimate ±3×. Untuk data demo (8 minggu) tidak terasa. | Bagi dengan jumlah hari sejak transaksi pertama (maksimal 21), atau dokumentasikan bahwa sistem mengasumsikan data ≥ 3 minggu. | ⚠️ Saran |
| P-07 | Rendah | **Seeder tidak bisa dijalankan lewat artisan.** `database/seed_riwayat_penjualan.php` memakai `mysqli` mentah dan `require db.php`, tidak terdaftar sebagai `db:seed`/command. Selain itu `rata_harian` memakai filter `created_at >= mulai` tanpa batas atas, sehingga data bertanggal masa depan (mis. dari uji manual) akan ikut terhitung. | Jadikan artisan command (`php artisan chikumi:seed-riwayat 8`) dan tambahkan `where('created_at', '<=', now())`. | ⚠️ Saran |
| P-08 | Info | **Tabel varian di halaman stok selalu "tidak dilacak".** Sesuai catatan di halaman ("Varian belum punya pencatatan stok"), tetapi membuat separuh tabel (7 kolom) praktis hanya berisi tanda "-". | Sederhanakan tabel varian menjadi daftar ketersediaan, atau tambahkan pencatatan stok varian bila memang dibutuhkan. | ℹ️ Catatan |
| P-09 | Info | `ExampleTest::test_the_application_returns_a_successful_response` masih gagal (`GET /` → 302), isu lama di luar PPL-07. | Ubah menjadi `assertRedirect('/login')` atau hapus. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`), MySQL untuk uji data nyata.

1. **Tes perhitungan vs hitungan manual (SQLite in-memory, tanpa MySQL):**
   ```
   php artisan test --filter=PrediksiStokTest
   ```
   Diharapkan `15 passed, 33 assertions`.
2. **Siapkan data seeder:**
   ```
   php database/migrate.php
   php database/seed.php
   php database/seed_riwayat_penjualan.php 8
   ```
3. **Cocokkan dengan hitungan manual** (ganti `@mulai` sesuai `now() - 20 hari`):
   ```sql
   SET @mulai = DATE_SUB(CURDATE(), INTERVAL 20 DAY);
   SELECT i.product_size_id, SUM(i.quantity) qty
   FROM pos_transaction_items i JOIN pos_transactions t ON t.id = i.pos_transaction_id
   WHERE t.status = 'paid' AND t.created_at >= @mulai
   GROUP BY i.product_size_id;
   ```
   Lalu bagi dengan 21 dan bandingkan dengan halaman Produk & Stok.
4. **Uji browser:** login `owner@chikumi.com` / `owner123` → **Dashboard** (kartu Kondisi Stok) dan **Produk & Stok** (tabel Stok & Saran).
   Pada kondisi sekarang kartu dashboard kosong (P-01); setelah URL diperbaiki, kartu akan terisi.
5. **Regresi:** `php artisan test` → 152/155 lulus; 2 kegagalan (P-05 dan P-09) serta 1 skipped dari tiket lain.
