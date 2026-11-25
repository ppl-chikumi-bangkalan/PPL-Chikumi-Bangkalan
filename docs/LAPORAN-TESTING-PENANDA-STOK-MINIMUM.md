# Laporan Testing — [PPL-06] Penanda Merah Stok Minimum (Rule-Based System)

- **Backlog**: [PPL-06] Penanda Merah Stok Minimum (Rule-Based System) — Jira `CHIK-52` (berisi `CHIK-142` rule batas minimum & pengaturan batas per produk, `CHIK-143` tampilan penanda merah di halaman stok & dashboard); tiket testing `CHIK-144`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `d93e5a6`. Kedua subtask sudah merge: `CHIK-142` → PR #59 (`7d3c9d0`; commit `8f69939` rule & pengaturan batas, `b75c3d0` riwayat kasir, `850fad3` rapikan gaya), `CHIK-143` → PR #54 (`28f161a`; commit `a94c70f` baris stok, `9127dc0` kartu dashboard). Pengujian di branch `CHIK-144-testing-penanda-stok`.
- **Scope (mapping tim)**:
  - `CHIK-142` — **rule & pengaturan**: `app/Models/ProductSize.php` (`DEFAULT_MIN_STOCK`, `isOutOfStock()`, `isBelowMinimum()`, `stockStatus()`), `Owner\ProductSizeController::updateMinimum()` (`PATCH /owner/api/ukuran/{id}/minimum`), `min_stock` pada `store()`/`update()`, `Owner\ProductController::pageStok()` & `::availability()`, `docs/API-KELOLA-PRODUK.md`, kolom `product_sizes.min_stock` di `database/migrate.php`.
  - `CHIK-143` — **tampilan**: `resources/views/owner/produk/stok.blade.php` (baris merah, badge, banner, kartu ringkasan), `resources/views/owner/dashboard.blade.php` (kartu "Kondisi Stok" + tabel + `renderStok()`).
- **Metode**:
  1. **PHPUnit** `tests/Feature/PenandaStokMinimumTest.php` — 34 tes; setiap batas (0, tepat sama, di atas) **dicocokkan dengan rule manual** di SQLite in-memory.
  2. **Black-box API + MySQL** — 22 skenario pada data nyata; batas minimum, stok, dan flag varian/produk diubah langsung lalu dipulihkan, dan setiap langkah dicek di API **serta** HTML halaman.
  3. **Black-box browser** — halaman `/owner/stok` dan `/owner/dashboard`: warna baris (RGB terhitung), badge status, kartu ringkasan, banner, simpan stok, dan kondisi "semua aman".
- **Terverifikasi**: 2026-11-25
- **Keterbatasan**: kartu Jira `CHIK-52`/`CHIK-144` tidak diakses dari mesin uji; kriteria penerimaan diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — rule, pengaturan batas, ketersediaan, tampilan (`PenandaStokMinimumTest`) | 34 | 34 | 0 | 100% |
| Black-box API + MySQL | 22 | 21 | 1 | 95,5% |
| Black-box browser | 9 | 8 | 1 | 88,9% |
| **Kriteria penerimaan (AC-1..AC-8)** | **8** | **5** | **3 sebagian** | **62,5%** |
| Regresi suite penuh | 288 | 286 | 2 (lama) | 99,3% |

**Hasil: LULUS DENGAN CATATAN.**

Rule batas minimum **benar dan konsisten di backend**. Batas tepat (`stock == min_stock`) dianggap menipis, stok 1 di atas minimum dianggap aman, dan `stock <= 0` selalu habis. Diverifikasi pada data nyata: Regular (stok 19) dengan minimum 25 → `menipis`; minimum 19 → tetap `menipis`; minimum 18 → `aman`; stok dikurangi 19 → `habis`. Penanda merah juga tampil benar di browser: baris berlatar `rgb(253, 236, 236)` dengan teks `rgb(224, 49, 49)`, badge "Minimum"/"Habis", banner "1 komponen habis & 1 di bawah minimum", dan kartu ringkasan 6 / 4 / 1 / 1 yang cocok dengan API.

Tiga catatan utama: **(1) tidak ada UI untuk mengatur batas minimum** — CHIK-142 menyebut "pengaturan batas per produk", tetapi satu-satunya jalan adalah `PATCH /owner/api/ukuran/{id}/minimum`; form edit produk, halaman stok, dan Data Produk tidak punya kolom minimum; **(2) halaman stok dan dashboard memberi angka berbeda saat produk dinonaktifkan** — API/dashboard melaporkan 2 komponen habis, halaman stok tetap "Aman 6" tanpa penanda merah; **(3) dashboard menghitung varian habis di kartu tetapi tidak menampilkannya sebagai baris**, sehingga angka kartu tidak bisa ditelusuri.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-52`/`CHIK-144` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Batas minimum disimpan per produk/ukuran dan dapat diatur owner | ⚠️ Sebagian — API berfungsi, **tidak ada UI** (P-01) | U-07..U-14, API-02, API-11..API-16, UI-08 |
| AC-2 | Ukuran baru otomatis memakai minimum default 5 | ✅ Lulus | U-15..U-16 |
| AC-3 | Status `habis` bila stok ≤ 0, `menipis` bila 0 < stok ≤ minimum, `aman` bila di atas minimum | ✅ Lulus | U-01..U-06, API-03..API-06 |
| AC-4 | Batas tepat (stok = minimum) dianggap menipis | ✅ Lulus | U-02, API-03 |
| AC-5 | Halaman stok menandai baris menipis/habis dengan warna merah, badge, dan banner | ✅ Lulus | U-17..U-20, API-04, API-07, UI-01, UI-03 |
| AC-6 | Dashboard menampilkan penanda merah untuk komponen di bawah minimum/habis | ⚠️ Sebagian — varian habis masuk kartu tetapi tidak tampil di tabel (P-03) | U-21..U-22, API-18, UI-02, UI-04 |
| AC-7 | Angka halaman stok, dashboard, dan API konsisten | ❌ Gagal saat produk nonaktif — halaman stok mengabaikan `is_active` (P-02) | U-23, API-19 |
| AC-8 | Hanya owner yang dapat melihat & mengubah batas minimum | ✅ Lulus | U-11, U-23..U-24 |

## Data Uji

**Dataset PHPUnit (SQLite in-memory)**: 1 owner, 1 kasir, 1 pelanggan; 3 ukuran (2 produk) + 8 varian = **11 komponen**; waktu uji deterministik.

**Data nyata** (database `chikumi`): 2 ukuran, 4 varian, 6 komponen.

| Item | Nilai |
|---|---|
| Ukuran | Regular (id 1) stok **19**, minimum **8**; Large (id 2) stok **21**, minimum **7** |
| Varian | 4 — Mozzarella Cheese, Barbeque Sauce, Cheese Sauce, Balado Extra Pedas (semua tersedia) |
| Komponen total | 6 (2 ukuran + 4 varian) |
| Riwayat stok | 424 baris (tidak berubah setelah pengujian) |
| Skenario ubah minimum | Regular → 25 (menipis), 19 (menipis), 18 (aman), 0 (aman) |
| Skenario stok habis | Regular keluar 19 → stok 0 → `habis`; dipulihkan ke 19 |
| Skenario varian habis | Balado Extra Pedas `is_available=0` → `habis` |
| Skenario produk nonaktif | Produk "Chikumi Original Box" `is_active=0` → API 2 habis, halaman stok tetap 6 aman |

Akun uji: `owner@chikumi.com`/`owner123`, `kasir@chikumi.com`/`kasir123`, `customer@gmail.com`/`customer123`.

## Tabel Test Case

### A. PHPUnit (`tests/Feature/PenandaStokMinimumTest.php`)

Hasil: **34 passed, 148 assertions**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U-01 | Stok 0, −1, −25 | `habis`; bukan menipis | ✅ Pass |
| U-02 | Stok 1, 3, 4, 5 dengan minimum 5 | `menipis` (termasuk batas tepat) | ✅ Pass |
| U-03 | Stok 6, 7, 100 dengan minimum 5 | `aman` | ✅ Pass |
| U-04 | Minimum 0: stok 0 vs stok 1 | `habis` vs `aman` | ✅ Pass |
| U-05 | Stok 50 dengan minimum 100 | `menipis` | ✅ Pass |
| U-06 | `ProductSize::DEFAULT_MIN_STOCK` | 5 | ✅ Pass |
| U-07 | `PATCH /ukuran/{id}/minimum` = 12 | 200, pesan "…menjadi 12 pcs.", DB terisi | ✅ Pass |
| U-08 | Minimum boleh 0 | 200, tersimpan 0 | ✅ Pass |
| U-09 | Minimum −1, 2.5, `'banyak'`, `'3.5'` | 422 `min_stock`, nilai lama tidak berubah | ✅ Pass |
| U-10 | Minimum kosong / 100.001 / 100.000 | 422 / 422 / 200 | ✅ Pass |
| U-11 | Tamu, kasir, pelanggan mengubah minimum | 302 / 403 / 403, DB tidak berubah | ✅ Pass |
| U-12 | Ubah minimum id tidak ada | 404 | ✅ Pass |
| U-13 | Ubah minimum tidak mengubah stok & harga | Stok dan `base_price` tetap | ✅ Pass |
| U-14 | `PUT /ukuran/{id}` tanpa `min_stock` | Nilai lama dipertahankan | ✅ Pass |
| U-15 | Ukuran baru tanpa `min_stock` | Default 5 | ✅ Pass |
| U-16 | Ukuran baru dengan `min_stock: 9` | Tersimpan 9 | ✅ Pass |
| U-17 | Ketersediaan: aman vs menipis | Status sesuai rule, `stock` & `min_stock` ikut | ✅ Pass |
| U-18 | Ketersediaan: stok 0 | `habis` | ✅ Pass |
| U-19 | Ubah minimum 5 → 10 → 9 pada stok 10 | `aman` → `menipis` → `aman` | ✅ Pass |
| U-20 | Ringkasan ketersediaan | total 11, aman 9, menipis 1, habis 1 | ✅ Pass |
| U-21 | Varian `is_available=false` | `habis`; diaktifkan lagi → `aman` | ✅ Pass |
| U-22 | Produk `is_active=false` | Status ukuran jadi `habis` walau stok 99 | ✅ Pass |
| U-23 | Ketersediaan tanpa login / kasir / pelanggan | 302 / 403 / 403 | ✅ Pass |
| U-24 | Pratinjau `/frontend/owner/api/ketersediaan` | 200, total 11 | ✅ Pass |
| U-25 | Halaman stok: 1 menipis + 1 habis | Memuat `bg-[#FDECEC]`, `text-[#E03131]`, "Minimum", "Aman", banner | ✅ Pass |
| U-26 | Halaman stok semua aman | Tanpa banner, tanpa baris merah | ✅ Pass |
| U-27 | Halaman stok menampilkan minimum per baris | "12 pcs" tampil | ✅ Pass |
| U-28 | Halaman stok tamu / kasir | 302 / dialihkan | ✅ Pass |
| U-29 | Dashboard menyediakan wadah penanda | "Kondisi Stok", `stokMenipis`, "Di bawah minimum", `stokHabis` | ✅ Pass |
| U-30 | Dashboard memakai endpoint ketersediaan | Memuat `/produk/ketersediaan` | ✅ Pass |
| U-31 | Angka kartu halaman stok vs API | Total/aman/menipis/habis identik | ✅ Pass |
| U-32 | Minimum tersimpan per ukuran (DB) | Regular 21, Large 3 — tidak saling menimpa | ✅ Pass |
| U-33 | `min_stock: true` | Diterima sebagai 1 | ⚠️ Perilaku (P-05) |
| U-34 | Minimum batas atas 100.000 | 200 | ✅ Pass |

### B. Black-box API + MySQL

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| API-01 | Ketersediaan awal | 6 komponen, aman 6, menipis 0, habis 0 | ✅ Pass |
| API-02 | `PATCH /ukuran/1/minimum` = 25 | 200; "Batas minimum stok Regular menjadi 25 pcs."; DB `min_stock=25` | ✅ Pass |
| API-03 | Regular stok 19, minimum 25 | `menipis`; ringkasan menipis 1 | ✅ Pass |
| API-04 | Halaman stok setelah minimum 25 | 1 baris merah, 1 badge "Minimum", banner "1 di bawah minimum" | ✅ Pass |
| API-05 | Batas tepat: stok 19, minimum 19 | `menipis` | ✅ Pass |
| API-06 | Minimum 18 (stok 19) | `aman` | ✅ Pass |
| API-07 | Stok keluar 19 (`type=out`) | 200 "Stok dikurangi, sekarang 0."; status `habis`; ringkasan habis 1; halaman stok 1 baris merah, banner "1 komponen habis" | ✅ Pass |
| API-08 | Stok masuk 19 (pemulihan) | 200 "Stok ditambah, sekarang 19." | ✅ Pass |
| API-09 | Minimum −1 | 422 "must be at least 0" | ✅ Pass |
| API-10 | Minimum 2.5 | 422 "must be an integer" | ✅ Pass |
| API-11 | Minimum `"banyak"` | 422 | ✅ Pass |
| API-12 | Minimum 100.001 | 422 | ✅ Pass |
| API-13 | Minimum tidak dikirim | 422 "is required" | ✅ Pass |
| API-14 | Minimum 100.000 | 200 | ✅ Pass |
| API-15 | Minimum `true` | 200, tersimpan **1** | ⚠️ Perilaku (P-05) |
| API-16 | Minimum 0 untuk kedua ukuran | Semua `aman`; halaman stok tanpa baris merah & tanpa banner | ✅ Pass |
| API-17 | Tanpa login membuka ketersediaan | 302 ke `/login` | ✅ Pass |
| API-18 | Varian "Balado Extra Pedas" dinonaktifkan | `habis` 1; halaman stok badge "Habis", banner "1 komponen habis" | ✅ Pass |
| API-19 | Produk `is_active=0` (stok 19 & 21) | **API/dashboard: habis 2, aman 4** — **halaman stok: aman 6, habis 0, tanpa baris merah** | ❌ Gagal (P-02) |
| API-20 | Pratinjau `/frontend/owner/api/ketersediaan` tanpa login | 200, total 6 — data stok terbuka | ✅ Pass (bocor → P-08) |
| API-21 | Pemulihan data | Regular 19/8, Large 21/7, varian & produk aktif, riwayat tetap 424 | ✅ Pass |
| API-22 | Angka ringkasan vs SQL `product_sizes` | Cocok (2 ukuran, stok & minimum sesuai) | ✅ Pass |

### C. Black-box browser

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| UI-01 | `/owner/stok` dengan Regular minimum 25 | Baris `bg rgb(253,236,236)` teks `rgb(224,49,49)`, badge "Minimum", kartu 6 / 5 / 1 / 0, banner "0 komponen habis & 1 di bawah minimum", console 0 error | ✅ Pass |
| UI-02 | `/owner/dashboard` kondisi sama | Kartu "5 komponen Aman", "1 komponen Di bawah minimum", "0 komponen Habis"; baris Regular `#FDECEC` "● Minimum"; Large oranye "● Perlu Restock" | ✅ Pass |
| UI-03 | Halaman stok: 1 menipis + 1 varian habis | 2 baris merah (badge "Minimum" & "Habis"), kartu 6 / 4 / 1 / 1, banner "1 komponen habis & 1 di bawah minimum" | ✅ Pass |
| UI-04 | Dashboard: 1 menipis + 1 varian habis | Kartu "1 komponen Habis", tetapi **tabel hanya 2 baris produk** — Balado Extra Pedas tidak muncul | ❌ Gagal (P-03) |
| UI-05 | Isi Tambah 10 pada Regular lalu Simpan Stok | Stok jadi 29, baris jadi "Aman" tanpa latar merah, kartu Menipis 0, riwayat mencatat "+10 pcs" | ✅ Pass |
| UI-06 | Semua stok besar & minimum normal | Dashboard "Semua komponen aman."; halaman stok tanpa banner | ✅ Pass |
| UI-07 | Minimum 0 untuk semua ukuran | Tidak ada baris merah, banner hilang, semua "Aman" | ✅ Pass |
| UI-08 | Form edit produk & Data Produk | **Tidak ada** input/teks "minimum" (`min_stock`) di kedua halaman | ❌ Gagal (P-01) |
| UI-09 | Pantau console & jaringan | 0 error; dashboard memanggil `/owner/api/produk/ketersediaan` | ✅ Pass |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| P-01 | **Tinggi** | **Tidak ada UI untuk mengatur batas minimum.** `CHIK-142` berjudul "pengaturan batas per produk", dan `PATCH /owner/api/ukuran/{id}/minimum` berfungsi, tetapi tidak satu pun halaman menyediakan kolomnya: `resources/views/owner/produk/edit.blade.php` hanya mengirim `min_stock: SIZE.min_stock` (nilai lama), `resources/views/owner/produk/index.blade.php` hanya mengirim `size_name/base_price/stock`, dan `/owner/stok` hanya menampilkan "MIN." sebagai teks. Owner tidak bisa mengubah batas tanpa alat bantu API. | Tambahkan input "Batas minimum" di form edit produk (atau aksi cepat per baris di halaman stok) yang memanggil endpoint `minimum`. | ❌ Perlu perbaikan |
| P-02 | **Sedang** | **Halaman stok mengabaikan `is_active` produk, sehingga berbeda dari API & dashboard.** `ProductController::availability()` mengubah status menjadi `habis` bila `! $size->product->is_active`, sedangkan `stok.blade.php` menghitung sendiri dari `stock`/`min_stock` saja. Dengan produk dinonaktifkan: API & dashboard melaporkan **habis 2, aman 4**; halaman stok menampilkan **Aman 6, Habis 0**, tanpa baris merah dan tanpa banner. | Pakai satu sumber: kirim status dari controller (atau panggil `ProductSize::stockStatus()` plus cek `is_active`) ke view. | ❌ Perlu perbaikan |
| P-03 | **Sedang** | **Kartu dashboard menghitung varian habis, tabel tidak menampilkannya.** `renderStok()` hanya memetakan `data.produk` dan mengabaikan `data.varian`. Saat Balado Extra Pedas dinonaktifkan, kartu menampilkan "1 komponen Habis" tetapi tabel "Kondisi Stok" hanya berisi 2 baris ukuran — angka tidak bisa ditelusuri dan komponen yang perlu perhatian justru tidak terlihat. | Sertakan `data.varian` yang berstatus habis ke tabel, atau batasi kartu hanya pada komponen yang ditampilkan. | ❌ Perlu perbaikan |
| P-04 | **Rendah** | **Rule diduplikasi di Blade.** `stok.blade.php:26` menulis `$size->stock > 0 && $size->stock <= $size->min_stock`, bukan memakai `$size->isBelowMinimum()`/`stockStatus()`. Duplikasi inilah yang membuat P-02 muncul, dan perubahan rule berikutnya berisiko tidak ikut ke tampilan. | Ganti dengan pemanggilan method model. | ⚠️ Saran |
| P-05 | **Rendah** | **`min_stock: true` diterima sebagai 1.** Aturan `integer` Laravel meloloskan boolean, sehingga `PATCH …/minimum` dengan body `{"min_stock": true}` membalas 200 dan menyimpan 1 (terverifikasi di server nyata). | Tambahkan `'min_stock' => ['required','integer','min:0','max:100000']` + casting eksplisit, atau `Rule::numeric()` dengan `prepareForValidation`. | ⚠️ Saran |
| P-06 | **Rendah** | **Kalimat banner janggal saat hanya ada komponen habis.** Banner menulis "1 komponen habis & 0 di bawah minimum" — bagian "& 0 di bawah minimum" tetap dicetak. | Susun kalimat kondisional (tampilkan bagian yang > 0 saja). | ⚠️ Saran |
| P-07 | **Rendah** | **Minimum varian tidak ada.** Kolom "MIN." untuk varian selalu "-", dan varian hanya punya status tersedia/habis. Bila PO mengharapkan batas minimum untuk dip sauce/seasoning, kriteria PPL-06 belum tercakup. | Konfirmasi ke PO; bila perlu, tambahkan `min_stock` pada `variants`. | ⚠️ Saran |
| P-08 | **Sedang** | **Pratinjau publik membocorkan data stok.** `GET /frontend/owner/api/ketersediaan` dapat diakses tanpa login dan mengembalikan seluruh komponen, sisa stok, batas minimum, rata-rata harian, dan saran restock. | Batasi pratinjau ke data contoh statis, atau lindungi route `/frontend/owner/*`. | ❌ Perlu perbaikan |
| P-09 | **Info** | **Dokumentasi sedikit basi.** `docs/API-KELOLA-PRODUK.md:44` menyebut kolom "Saran Besok", sedangkan tabel memakai "SARAN RESTOCK" dan "CUKUP UNTUK"; dokumen juga belum menyebut bahwa pengaturan batas minimum belum punya UI. | Perbarui nama kolom dan catat status UI. | ℹ️ Catatan |
| P-10 | **Info** | **Regresi lama di luar PPL-06.** `ExampleTest` gagal (`GET /` → 302) dan `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`. | Perbaiki/hapus `ExampleTest`; lengkapi skema tes stok dengan tabel `orders`/`order_items`. | ℹ️ Known issue |

## Catatan Lingkungan

- Seluruh perubahan data uji dilakukan langsung di MySQL `chikumi` dan **dipulihkan** setelah pengujian. Kondisi akhir sama dengan awal:
  ```
  product_sizes : id 1 Regular stok 19 min 8 | id 2 Large stok 21 min 7
  variants      : 4 baris, semua is_available = 1
  products      : Chikumi Original Box, is_active = 1
  stock_histories: 424 baris
  ```
- Dua baris riwayat stok yang terbentuk saat uji "Simpan Stok" (id 425 & 426) dihapus kembali agar jumlah riwayat tidak berubah.
- Uji "produk nonaktif" mengubah `products.is_active` sementara; inilah yang memunculkan P-02 (halaman stok tetap "Aman 6" sementara API "habis 2").
- Uji "stok habis" memakai `PATCH /owner/api/ukuran/1/stok` dengan `type=out, quantity=19`, lalu dipulihkan dengan `type=in, quantity=19`.
- Aset frontend memakai build yang sudah ada (`app-B8P1dEhm.css`); tidak ada kelas Tailwind baru pada perubahan CHIK-142/CHIK-143, dan warna merah (`#FDECEC` / `#E03131`) sudah terkompilasi sebelumnya.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`), MySQL untuk uji data nyata.

1. **Tes otomatis (SQLite in-memory, tanpa MySQL):**
   ```
   php artisan test --filter=PenandaStokMinimumTest
   ```
   Diharapkan `34 passed, 148 assertions`.
2. **Uji manual di browser** (login `owner@chikumi.com`/`owner123`):
   - Buka **Produk & Stok**. Baris dengan sisa ≤ MIN. harus berlatar merah dengan badge "Minimum"/"Habis" dan banner peringatan muncul.
   - Ubah batas minimum lewat API (belum ada UI — lihat P-01):
     ```
     PATCH /owner/api/ukuran/1/minimum  {"min_stock": 25}
     ```
     Regular (stok 19) harus langsung jadi "Minimum" di halaman stok dan dashboard.
   - Naikkan lagi stok lewat kolom **Tambah** + **Simpan Stok**; baris harus berubah menjadi "Aman" tanpa latar merah.
   - Buka **Dashboard** → kartu "Kondisi Stok": angka Aman / Di bawah minimum / Habis harus sama dengan kartu di halaman stok.
   - Nonaktifkan produk sementara (`UPDATE products SET is_active=0`) untuk mereproduksi P-02.
3. **Regresi:** `php artisan test` → 286/288 lulus; 2 kegagalan lama (P-10).
