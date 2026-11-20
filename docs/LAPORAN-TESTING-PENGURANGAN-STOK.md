# Laporan Testing — [PPL-10] Pengurangan Stok Otomatis

- **Backlog**: [PPL-10] Pengurangan Stok Otomatis — Jira `CHIK-39` (berisi `CHIK-117` pengerjaan pengurangan stok otomatis (transaksi lunas & pesanan dibayar) + riwayat stok); tiket testing `CHIK-118`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `5feceb7`. **`CHIK-117` tidak ditemukan di repositori** — tidak ada branch, commit, maupun PR dengan ID tersebut (`git log --all --grep=CHIK-117` kosong; daftar branch GitHub dan 47 PR tidak memuat `CHIK-117`). Pengujian di branch `CHIK-118-testing-pengurangan-stok` karenanya menguji **perilaku pengurangan stok yang saat ini ada di `main`** (hasil kerja `CHIK-83`/`CHIK-98`/`CHIK-114`/`CHIK-93`), bukan deliverable `CHIK-117`. Lihat **S-01**.
- **Scope yang diharapkan dari `CHIK-117`** (dari judul tiket):
  1. Pengurangan stok otomatis saat **transaksi kasir lunas**.
  2. Pengurangan stok otomatis saat **pesanan online dibayar**.
  3. **Riwayat stok** yang mencatat setiap perubahan.
- **Metode**:
  1. **PHPUnit** `tests/Feature/PenguranganStokTest.php` — 13 tes, SQLite in-memory, respons Duitku dipalsukan dengan `Http::fake`.
  2. **Black-box API + MySQL** — POS, checkout online, pembatalan, lalu cek `product_sizes.stock` dan `stock_histories` langsung di database.
  3. **Black-box browser** — login owner, buka `/owner/stok`, uji filter riwayat, klik "Simpan Stok", pantau request jaringan.
- **Terverifikasi**: 2026-11-21
- **Keterbatasan**: kartu Jira `CHIK-39`/`CHIK-118` tidak diakses dari mesin uji, sehingga kriteria diturunkan dari judul work item.

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — pengurangan stok & riwayat (`PenguranganStokTest`) | 13 | 13 | 0 | 100% |
| Black-box API/MySQL — POS, checkout, batal | 4 | 4 | 0 | 100% |
| Black-box browser — halaman riwayat stok | 5 | 4 | 1 | 80% |
| **Kriteria penerimaan (AC-1..AC-5)** | **5** | **3** | **2** | **60%** |
| Regresi suite penuh (working tree) | 140 | 137 | 2 (lama) + 1 skipped | 97,9% |

**Hasil: LULUS DENGAN CATATAN — tetapi deliverable `CHIK-117` belum ada.**

Mekanisme pengurangan stok yang berjalan di `main` **bekerja benar secara teknis**: transaksi kasir langsung memotong stok dan mencatat riwayat, pembatalan mengembalikan stok, kedaluwarsa pembayaran mengembalikan stok tepat sekali, dan riwayat stok tampil di halaman owner dengan tiga sumber (kasir, online, restock manual). Tidak ada kebocoran stok maupun pemotongan ganda.

Namun ada dua hal yang harus diputuskan: **(1) `CHIK-117` tidak ada di repositori**, sehingga tidak ada yang bisa diverifikasi sebagai hasil tiket ini; **(2) pesanan online memotong stok saat checkout, bukan saat pembayaran berhasil** — bertentangan dengan judul tiket "pesanan dibayar" (temuan yang sama dengan T-03 pada PPL-26).

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-39`/`CHIK-118` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask `CHIK-117`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Transaksi kasir lunas otomatis mengurangi stok | ✅ Lulus | POS-01..POS-04 |
| AC-2 | Pesanan online **dibayar** otomatis mengurangi stok | ❌ Tidak sesuai — stok sudah dipotong saat checkout (masih `unpaid`) | ONL-01, ONL-02 (S-02) |
| AC-3 | Pembatalan / pembayaran gagal mengembalikan stok tanpa pengembalian ganda | ✅ Lulus | ONL-03..ONL-06 |
| AC-4 | Setiap perubahan stok tercatat di riwayat stok (masuk/keluar, sumber, pelaku, referensi) | ✅ Lulus | HIS-01..HIS-03 |
| AC-5 | Halaman riwayat stok menampilkan & menyaring riwayat dengan benar | ❌ Sebagian — filter hanya CSS, label sumber salah untuk pengurangan manual, hanya 6 baris tanpa akses ke sisanya | HIS-01, HIS-02, HIS-04, HIS-05 (S-03, S-05, S-06) |

## Data Uji

| Item | Tipe | Stok awal | Harga |
|---|---|---|---|
| Chikumi Original Box Regular | Ukuran | 20 | Rp 15.000 |
| Chikumi Original Box Large | Ukuran | 3 | Rp 22.000 |
| Mozzarella Cheese | filling | — | +Rp 3.000 |
| Barbeque Sauce | dipping_sauce | — | +Rp 2.000 |

Database lokal `chikumi` (setelah `migrate.php` + `seed.php` + seeder `CHIK-125`): 215 transaksi kasir, 76 pesanan online, 415 baris `stock_histories`.

Akun uji: `kasir@chikumi.com`/`kasir123`, `owner@chikumi.com`/`owner123`, `customer@gmail.com`/`customer123`.

## Tabel Test Case

### A. Transaksi kasir lunas (PHPUnit)

Lokasi: `tests/Feature/PenguranganStokTest.php` → `POST /kasir/api/transaksi`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| POS-01 | Tunai, Reguler ×2 + Mozzarella + BBQ | stok 20 → 18; riwayat `out` qty 2, `source=pos_transaction`, `reference_id` = id transaksi, `user_id` = kasir | ✅ Pass |
| POS-02 | Non-tunai (QRIS/VA) Large ×1 | stok 3 → 2; tepat 1 baris riwayat | ✅ Pass |
| POS-03 | Stok kurang (qty 21 > 20) | 422; stok tetap 20; **tidak ada** riwayat & transaksi tersimpan | ✅ Pass |
| POS-04 | Dua baris ukuran sama (3 + 2) | stok 20 → 15; digabung jadi **satu** baris riwayat qty 5 (tidak dobel) | ✅ Pass |

### B. Pesanan online & pembatalan (PHPUnit)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| ONL-01 | Checkout online qty 2 | stok 20 → 18 **saat checkout**, pesanan masih `unpaid`; riwayat `out`, `source=online_order`, `reference_id` = id pesanan | ✅ Pass (perilaku sekarang), ⚠️ lihat S-02 |
| ONL-02 | Callback pembayaran sukses setelah checkout | status `processing` + `paid_at`; stok **tetap 18** (tidak memotong dua kali) | ✅ Pass |
| ONL-03 | Batal manual (`PATCH …/batal`) | stok 18 → 20; riwayat `in` qty 2, catatan "Pembatalan pesanan"; status `cancelled` | ✅ Pass |
| ONL-04 | Pembayaran kedaluwarsa (`statusCode 02`) | stok kembali 20; riwayat `in` bertanda "Pembatalan otomatis…" | ✅ Pass |
| ONL-05 | Polling status diulang setelah kedaluwarsa | stok **tidak** bertambah lagi; hanya 1 baris riwayat masuk | ✅ Pass |
| ONL-06 | Batal manual lalu callback sukses masuk | stok tetap 20; status tetap `cancelled`; tidak ada pengembalian ganda | ✅ Pass |

### C. Riwayat stok (PHPUnit + browser)

Lokasi: `GET /owner/stok` → `resources/views/components/owner/riwayat-stok.blade.php`.

| ID | Skenario | Hasil Diharapkan | Hasil Nyata | Status |
|---|---|---|---|---|
| HIS-01 | Tiga sumber dalam satu halaman | label "Transaksi kasir", "Pesanan online", "Tambah stok" tampil; catatan manual tersimpan | Semua tampil | ✅ Pass |
| HIS-02 | Lebih dari 6 baris riwayat | hanya 6 terakhir + teks "Menampilkan 6 dari 8" | Sesuai; `Batch 2` tidak tampil | ✅ Pass |
| HIS-03 | Pelanggan membuka `/owner/stok` | dialihkan ke `/` | 302 → `/` | ✅ Pass |
| HIS-04 | Filter **Masuk** / **Keluar** (browser) | daftar & hitungan ikut menyesuaikan | baris tersaring via CSS, tetapi footer tetap "Menampilkan 6 dari 418" | ❌ Gagal (S-06) |
| HIS-05 | Label sumber untuk pengurangan stok manual | sumber manual berlabel "Tambah stok" hanya bila `type=in` | baris "● Keluar | −16 pcs | **Tambah stok**" | ❌ Gagal (S-03) |

### D. Konsistensi data (PHPUnit + MySQL)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| KON-01 | Total riwayat = perubahan stok | `stok_awal − Σkeluar + Σmasuk = stok_akhir` (3 keluar, 1 masuk) | ✅ Pass |
| KON-02 | Black-box MySQL: POS qty 2 | `stock` 23 → 20 (POS-216), riwayat `out` qty 2 tertulis | ✅ Pass |
| KON-03 | Black-box MySQL: checkout online qty 1 | `stock` 20 → 19 (order #77) padahal `unpaid` | ✅ Pass (S-02) |
| KON-04 | Black-box MySQL: batal order #77 | `stock` 19 → 20; riwayat `in` qty 1 "Pembatalan pesanan" | ✅ Pass |

Bukti browser (`/owner/stok`, login owner): tabel "Riwayat Stok" menampilkan Waktu/Ukuran/Jenis/Jumlah/Sumber/Oleh/Catatan dengan benar, 0 error console. Baris teratas: `20/11 19.01 · Chikumi Original Box Regular · ● Masuk · +1 pcs · Pesanan online · Pelanggan Contoh · Pembatalan pesanan`.

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| S-01 | **Blocker (proses)** | **`CHIK-117` tidak ada di repositori.** Tidak ada branch, commit, atau PR dengan ID `CHIK-117` — daftar 41 branch dan 47 PR di GitHub tidak memuatnya. Semua perilaku yang diuji berasal dari commit lain (`CHIK-83`, `CHIK-93`, `CHIK-98`, `CHIK-114`). Artinya tiket ini **belum punya deliverable**, sehingga tidak bisa dinyatakan selesai. | Pastikan pengembang sudah push branch `CHIK-117-…` dan membuka PR; atau jika pekerjaan memang sudah tercakup tiket lain, tutup `CHIK-117` sebagai duplikat dan sebutkan tiket penggantinya. | ❌ Perlu tindakan |
| S-02 | **Tinggi (scope)** | **Stok pesanan online dipotong saat checkout, bukan saat pesanan dibayar.** `OrderController::checkout` langsung `decrement('stock')` + menulis riwayat `out` ketika pesanan masih `unpaid`. `PaymentController::sync` hanya mengubah status → `processing`; tidak memotong stok. Judul `CHIK-117` menyebut "pesanan dibayar", sehingga AC-2 tidak terpenuhi secara harfiah. Efek samping: stok "tertahan" oleh pesanan yang belum dibayar sampai kedaluwarsa/dibatalkan. | Konfirmasi ke PO. Bila potong-saat-bayar yang diinginkan: pindahkan `decrement` + riwayat `out` ke `sync()` saat `paid`, dan hapus pemotongan di checkout (pastikan validasi stok tetap ada saat checkout). Bila potong-saat-checkout dipertahankan: perbaiki judul/AC `CHIK-117`. | ❌ Perlu keputusan |
| S-03 | **Sedang** | **Label sumber salah untuk pengurangan stok manual.** `riwayat-stok.blade.php` memetakan `source='manual_restock'` → "Tambah stok" tanpa melihat `type`. Akibatnya baris pengurangan manual tampil sebagai "● Keluar | −16 pcs | Tambah stok", terbaca seperti penambahan. | Petakan label berdasarkan `type` + `source` (mis. `out` + `manual_restock` → "Kurangi stok"), atau pisahkan `source` menjadi `manual_add`/`manual_reduce`. | ❌ Perlu perbaikan |
| S-04 | **Sedang (UX/risiko data)** | **Kolom "Tambah" terisi otomatis sebesar saran restock**, sehingga satu klik **Simpan Stok** langsung menambah stok tanpa niat eksplisit. Saat pengujian, membuka halaman lalu menekan tombol sekali menambah **+16 pcs ke dua ukuran** (21 → 37). Risiko salah input stok cukup besar karena angkanya terlihat seperti nilai bawaan yang wajar. | Kosongkan input (`value=""`), tampilkan saran sebagai teks/badge di kolom terpisah, dan minta konfirmasi berisi daftar perubahan sebelum menyimpan. | ⚠️ Saran kuat |
| S-05 | **Rendah** | **Riwayat stok hanya menampilkan 6 baris terakhir tanpa jalan melihat sisanya.** Footer berbunyi "Menampilkan 6 dari 423 riwayat terakhir", tetapi tidak ada pagination, tombol "lihat semua", atau filter per produk/tanggal. Untuk audit stok, 6 baris tidak memadai. | Tambahkan pagination atau halaman riwayat stok penuh (filter tanggal/produk/sumber). | ⚠️ Saran |
| S-06 | **Rendah** | **Filter Masuk/Keluar hanya CSS.** Sama seperti halaman riwayat transaksi, filter memakai `group-has-[…:checked]:hidden`; hitungan di footer ("Menampilkan 6 dari 423") tidak ikut berubah saat filter aktif, dan tidak ada baris kosong/pesan bila hasil filter nol. | Hitung ulang jumlah baris sesuai filter, atau ubah filter menjadi query server-side. | ⚠️ Saran |
| S-07 | **Rendah** | **Kolom Catatan menampilkan ID internal, bukan nomor transaksi.** Baris kasir/online menampilkan `#216`, `#77` (yaitu `reference_id` numerik), bukan `POS-261120190107-9547` atau `CHKM-261120190107-894`. | Tampilkan nomor transaksi/pesanan sebenarnya (relasi ke `pos_transactions.transaction_number` / `orders.order_number`). | ⚠️ Saran |
| S-08 | Info | Baris stok awal dari seeder (`notes='Stok awal (seeder)'`, `user_id` NULL) tampil dengan kolom "Oleh" = `-`. Wajar, tetapi tidak ada penanda "sistem". | Beri label "Sistem" bila `user_id` NULL. | ℹ️ Catatan |
| S-09 | Rendah (di luar scope) | **Regresi lama:** `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`. Skema tes stok belum membuat tabel `orders`/`order_items` yang kini dipakai `MovingAverage::harian()` (CHIK-126). | Tambahkan `orders` + `order_items` ke skema `PenambahanStokTest`. | ❌ Perlu perbaikan |
| S-10 | Info | `ExampleTest::test_the_application_returns_a_successful_response` masih gagal (`GET /` → 302), isu lama di luar PPL-10. | Ubah menjadi `assertRedirect('/login')` atau hapus. | ℹ️ Known issue |

## Catatan Lingkungan

Saat pengujian black-box, tombol **Simpan Stok** menambah 16 pcs ke kedua ukuran (lihat S-04). Stok sudah dikembalikan ke nilai semula (Regular 21, Large 21) melalui `PATCH /owner/api/ukuran/{id}/stok` dengan catatan "Koreksi setelah uji UI", sehingga `stock_histories` kini memuat 4 baris tambahan (2 masuk + 2 koreksi keluar). Tidak ada data lain yang diubah.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`).

1. **Tes otomatis (SQLite in-memory, tanpa MySQL/gateway):**
   ```
   php artisan test --filter=PenguranganStokTest
   ```
   Diharapkan `13 passed, 76 assertions`.
2. **Uji manual API + MySQL:**
   ```
   php database/migrate.php
   php database/seed.php
   php artisan serve
   ```
   - POS: `POST /frontend/kasir/api/transaksi` dengan header `X-Dev-Kasir: 2`, body `{"payment_method":"cash","amount_paid":50000,"items":[{"product_size_id":1,"quantity":2}]}` → cek `product_sizes.stock` turun 2 dan `stock_histories` bertambah `out`.
   - Online: `POST /frontend/customer/api/keranjang` lalu `/checkout` dengan header `X-Dev-Customer: 3` → stok **langsung** turun meski status `unpaid`; `PATCH /frontend/customer/api/pesanan/{id}/batal` → stok kembali.
3. **Uji browser:** login `owner@chikumi.com` / `owner123` → menu **Produk & Stok** → tab **Stok & Saran** → bagian **Riwayat Stok** (filter Masuk/Keluar, catatan, pelaku).
4. **Regresi:** `php artisan test` → 137/140 lulus; 2 kegagalan lama (S-09, S-10) dan 1 skipped berasal dari tes tiket lain yang belum di-commit.
