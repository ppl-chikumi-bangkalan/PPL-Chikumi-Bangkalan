# Laporan Testing — [PPL-23] Pemesanan Online (Checkout)

- **Backlog**: [PPL-23] Pemesanan Online (Checkout) — Jira `CHIK-42` (berisi `CHIK-98` backend checkout & pembuatan pesanan terhubung akun, `CHIK-99` frontend halaman checkout); tiket testing `CHIK-100` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `feeb0f7` (sudah memuat `CHIK-98` via PR #32, `CHIK-101` metode penerimaan via PR #33, dan frontend `checkout.blade.php` yang masuk lewat `CHIK-93`/`CHIK-95`).
- **Scope (mapping tim)**:
  - `CHIK-98` — sisi **backend**: `Customer\OrderController::checkout` (buat pesanan atas nama akun, hitung subtotal + ongkir, kurangi stok, catat `StockHistory`, kosongkan keranjang), `::index` (riwayat per akun), `::cancel` (batal → kembalikan stok), model `Order`/`OrderItem`.
  - `CHIK-99` — sisi **frontend**: halaman `customer/checkout.blade.php` (cara terima, alamat, pembayaran, ringkasan) yang membaca `GET /keranjang`, `GET /alamat`, `GET /metode-penerimaan` dan mengirim `POST /pesanan/checkout`.
- **Metode**:
  1. **Tes otomatis PHPUnit**: feature test baru `PemesananOnlineTest` (10 skenario pelengkap) + suite `CheckoutPesananTest` (13 skenario, sudah ada dari `CHIK-98`) + regresi 6 suite.
  2. **Review statis**: alur JS halaman checkout & halaman alamat, karena build frontend tidak dapat dijalankan di mesin uji (lihat T-07).
- **Terverifikasi**: 2026-11-14

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — `CheckoutPesananTest` (backend checkout `CHIK-98`) | 13 | 13 | 0 | 100% |
| PHPUnit — `PemesananOnlineTest` (pelengkap `CHIK-100`, baru) | 10 | 10 | 0 | 100% |
| PHPUnit — `PriceCalculatorTest` (regresi) | 14 | 14 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi) | 23 | 23 | 0 | 100% |
| PHPUnit — `TransaksiKasirTest` (regresi) | 8 | 8 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (regresi) | 2 | 2 | 0 | 100% |
| PHPUnit — `PenambahanStokTest` (regresi) | 8 | 8 | 0 | 100% |
| PHPUnit — `KeranjangTest` (regresi) | 17 | 17 | 0 | 100% |
| **Total (tanpa ExampleTest)** | **95** | **95** | **0** | **100%** |

Backend pemesanan online **berfungsi penuh**: checkout membuat pesanan atas nama akun, menghitung subtotal + ongkir (diantar Rp 5.000 / ambil di tempat Rp 0), mengurangi stok, mencatat riwayat, mengosongkan keranjang, dan membatasi akses antar akun. Frontend halaman checkout tersambung ke API, **tetapi halaman alamat belum tersambung backend** (lihat T-01), sehingga alur "Delivery" tidak bisa dilengkapi alamat lewat UI. Satu kegagalan pada suite penuh (97 tes) adalah `Feature/ExampleTest` kedaluwarsa di luar scope.

## Kriteria Penerimaan yang Diuji

Kartu Jira belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item `CHIK-42`/`CHIK-98`/`CHIK-99`/`CHIK-100`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Pelanggan checkout dan pesanan tersimpan atas nama akunnya | C-01, C-02, C-03 |
| AC-2 | Total pesanan = subtotal + ongkir (diantar/ambil di tempat) | C-01, C-02, O-04, O-05 |
| AC-3 | Stok berkurang & tercatat di riwayat; keranjang dikosongkan | C-01 |
| AC-4 | Validasi: keranjang kosong, stok kurang, alamat tidak valid ditolak | C-04…C-07 |
| AC-5 | Riwayat pesanan hanya milik akun sendiri | C-08, C-09 |
| AC-6 | Pembatalan mengembalikan stok; hanya akun sendiri & status unpaid | C-10, C-11, C-12 |
| AC-7 | Metode penerimaan & ongkir disediakan backend | O-01 |

## Data Uji

| Item | Tipe | Harga | Stok |
|---|---|---|---|
| Chikumi Original Box · Regular | Ukuran | Rp 15.000 | 20 |
| Chikumi Original Box · Large | Ukuran | Rp 22.000 | 3 |
| Mozzarella Cheese | filling | +Rp 3.000 | — |

Ongkir flat (`App\Support\DeliveryMethod`): Diantar Rp 5.000, Ambil di tempat Rp 0.

## Tabel Test Case

### A. Backend — `CHIK-98` checkout (PHPUnit `CheckoutPesananTest`, sudah ada)

Lokasi: `tests/Feature/CheckoutPesananTest.php`. Endpoint: `POST /customer/api/pesanan/checkout`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| C-01 | Courier + QRIS + alamat (Regular×2 + Mozzarella) | 201; subtotal 36.000, ongkir 5.000, total 41.000; item & varian tersimpan; stok 20→18; keranjang kosong | ✅ Pass |
| C-02 | Pickup tanpa alamat & tanpa ongkir | 201; alamat null, ongkir 0, total 15.000, `payment_type` null | ✅ Pass |
| C-03 | Courier pakai alamat yang dipilih | alamat tersimpan sesuai `address_id` | ✅ Pass |
| C-04 | Courier tanpa alamat | 422; tidak ada pesanan | ✅ Pass |
| C-05 | Alamat milik akun lain | 422; keranjang & stok tetap | ✅ Pass |
| C-06 | Keranjang kosong | 422; tidak ada pesanan | ✅ Pass |
| C-07 | Stok kurang | 422; stok & keranjang tidak berubah, tidak ada riwayat | ✅ Pass |
| C-08 | Riwayat hanya akun sendiri | hanya pesanan akun login | ✅ Pass |
| C-09 | Riwayat menyertakan metode pembayaran | `pembayaran` = "va" | ✅ Pass |
| C-10 | Batal pesanan sendiri (unpaid) | status cancelled; stok kembali; riwayat `type=in` | ✅ Pass |
| C-11 | Batal pesanan akun lain | 403 | ✅ Pass |
| C-12 | Batal pesanan sudah dibayar | 422 | ✅ Pass |

### B. Backend pelengkap — `CHIK-100` (PHPUnit `PemesananOnlineTest`, baru)

Lokasi: `tests/Feature/PemesananOnlineTest.php`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| O-01 | `GET /customer/api/metode-penerimaan` | 200; courier (ongkir 5000, butuh alamat) + pickup (ongkir 0) | ✅ Pass |
| O-02 | `delivery_method` tidak valid ("drone") | 422 `delivery_method`; tidak ada pesanan | ✅ Pass |
| O-03 | `payment_method` tidak valid ("cash") | 422 `payment_method`; tidak ada pesanan | ✅ Pass |
| O-04 | Courier tanpa `payment_method` (nullable) | 201; `payment_type` null | ✅ Pass |
| O-05 | Courier total termasuk ongkir (Regular) | total 20.000 (15.000 + 5.000) | ✅ Pass |
| O-06 | Simpan alamat (`POST /customer/api/alamat`) | 201; data `penerima`/`kota` sesuai | ✅ Pass |
| O-07 | Update alamat sendiri | 200; `recipient_name` berubah | ✅ Pass |
| O-08 | Update alamat akun lain | 403 | ✅ Pass |
| O-09 | Hapus alamat akun lain | 403; alamat tetap ada | ✅ Pass |
| O-10 | `GET /customer/checkout` | 200; memuat "Cara Menerima Pesanan", "Ringkasan Pembayaran", "Bayar Sekarang" | ✅ Pass |

### C. Frontend — `CHIK-99` Halaman checkout (review statis)

Lokasi: `resources/views/customer/checkout.blade.php`.

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| UI-01 | Muat data | `Promise.all` ambil `/keranjang`, `/alamat`, `/metode-penerimaan` | ✅ Pass (statis) |
| UI-02 | `renderRingkasan()` | subtotal + ongkir → total; tombol nonaktif saat keranjang kosong | ✅ Pass (statis) |
| UI-03 | Pilih cara terima | radio pickup/courier; ongkir mengikuti `metode-penerimaan` | ✅ Pass (statis) |
| UI-04 | `btnBayar` → `POST /pesanan/checkout` | kirim `delivery_method`, `payment_method`, `address_id` | ✅ Pass (statis) |
| UI-05 | Guard tanpa alamat (courier) | alert "Simpan alamat pengiriman dulu." | ✅ Pass (statis) |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | **Sedang (blokir alur Delivery)** | Halaman `customer/alamat.blade.php` **belum tersambung backend**: tombol "Simpan Alamat" ber-`type="button"` tanpa handler JS (komentar `type="button" sementara`), tidak ada `POST/PUT /customer/api/alamat` dipanggil. Pelanggan tidak bisa menambah/menyimpan alamat lewat UI, sehingga alur "Diantar (Delivery)" di checkout tidak bisa dilengkapi alamat. (Secara tiket ini CHIK-102, tapi memblokir PPL-23.) | Wire form alamat ke `POST /customer/api/alamat` (dan `PUT` saat edit). | ❌ Perlu perbaikan |
| T-02 | Rendah | Teks ongkir di halaman alamat hardcode **"Rp 8.000"**, sedangkan backend `DeliveryMethod` flat **Rp 5.000**; checkout menampilkan Rp 5.000. | Samakan dengan tarif backend (Rp 5.000) atau jadikan dinamis. | ⚠️ Saran |
| T-03 | Rendah (UX) | Checkout selalu memakai alamat pertama/terbaru (`daftarAlamat.data.alamat?.[0]`); tidak ada pemilih alamat bila pelanggan punya lebih dari satu alamat. | Tambah pemilih alamat (radio daftar alamat) di checkout. | ⚠️ Saran |
| T-04 | Rendah | Field form alamat `Patokan (landmark)` dan `Jadikan alamat utama (is_primary)` **tidak ada** di model/validasi `Address` — data akan dibuang bila dikirim. Backend `checkout` memakai `latest('id')`, bukan `is_primary`. | Tambahkan kolom `landmark`/`is_primary` bila memang diperlukan, atau hapus field dari form. | ⚠️ Saran |
| T-05 | Info (scope) | Setelah checkout sukses hanya `alert()` + redirect ke dashboard; belum ada halaman status/bayar. Kolom `snap_token`/`paid_at`/`payment_type` sudah ada tapi pembayaran daring (Midtrans/dsb.) belum terintegrasi — pesanan selalu `unpaid`. | Sesuai scope saat ini; integrasi pembayaran di ticket tersendiri. | ℹ️ Catatan |
| T-06 | Info | Docblock `CheckoutPesananTest.php` salah label **"[CHIK-97]"** padahal isinya backend checkout `CHIK-98`. | Koreksi komentar header agar tidak membingungkan. | ℹ️ Catatan |
| T-07 | Info (lingkungan) | Build frontend tetap terblokir di Windows (`@tailwindcss/oxide-linux-x64-gnu`, lihat T-03 laporan PPL-13), sehingga verifikasi `CHIK-99` lewat review statis. | Rujuk `@tailwindcss/oxide` (tanpa pin platform). | ❌ Perlu perbaikan |
| T-08 | Info | `tests/Feature/ExampleTest.php` bawaan gagal (`GET /` → 302), di luar scope PPL-23. | Hapus atau ubah jadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer di PATH.

1. **Tes otomatis pemesanan online & regresi (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="CheckoutPesananTest|PemesananOnlineTest|PriceCalculatorTest|HargaDinamisTest|TransaksiKasirTest|DashboardWiringTest|PenambahanStokTest|KeranjangTest"
   ```
   Skema tabel tes mengikuti `database/migrate.php`; tabel `orders`/`order_items`/`order_item_variants`/`delivery_addresses` dibuat di helper `tests/Concerns/MenyiapkanDataChikumi.php` (dan `CheckoutPesananTest::buatSkemaPesanan`). Bila skema berubah, sesuaikan helper tersebut.

2. **Black-box UI (opsional, setelah T-07 diperbaiki)**:
   `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `customer@gmail.com` / `customer123`, tambah produk ke keranjang, buka `/customer/checkout`, pilih cara terima & pembayaran, klik **Bayar Sekarang**, lalu amati tab **Network** DevTools (endpoint `POST /customer/api/pesanan/checkout`).