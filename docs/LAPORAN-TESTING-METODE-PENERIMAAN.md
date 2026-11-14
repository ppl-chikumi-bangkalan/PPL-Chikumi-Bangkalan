# Laporan Testing — [PPL-24] Pilihan Metode Penerimaan Pesanan

- **Backlog**: [PPL-24] Pilihan Metode Penerimaan Pesanan — Jira `CHIK-43` (berisi `CHIK-101` backend metode penerimaan diantar/ambil di tempat, `CHIK-102` frontend pilihan metode & form alamat); tiket testing `CHIK-103` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `d91065f` (sudah memuat `CHIK-101` via PR #33, dan frontend `CHIK-102` — halaman `alamat.blade.php` & pilihan metode di `checkout.blade.php` — via PR #28/#30).
- **Scope (mapping tim)**:
  - `CHIK-101` — sisi **backend**: `App\Support\DeliveryMethod` (tarif flat diantar Rp 5.000 / ambil di tempat Rp 0 + aturan butuh alamat), `Customer\DeliveryMethodController::index` (endpoint `GET /customer/api/metode-penerimaan`), integrasi di `OrderController::checkout` (ongkir + alamat wajib untuk courier).
  - `CHIK-102` — sisi **frontend**: pilihan metode di `customer/checkout.blade.php` (dibaca dari API) dan form alamat `customer/alamat.blade.php`.
- **Metode**:
  1. **Tes otomatis PHPUnit**: unit test baru `DeliveryMethodTest` (5 skenario) + feature test terkait (`PemesananOnlineTest`, `CheckoutPesananTest`) + regresi 6 suite.
  2. **Review statis**: alur JS pilihan metode di checkout dan kondisi form alamat, karena build frontend tidak dapat dijalankan di mesin uji (lihat T-06).
- **Terverifikasi**: 2026-11-14

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — `DeliveryMethodTest` (unit `CHIK-101`, baru) | 5 | 5 | 0 | 100% |
| PHPUnit — `CheckoutPesananTest` (integrasi ongkir/metode) | 13 | 13 | 0 | 100% |
| PHPUnit — `PemesananOnlineTest` (endpoint metode-penerimaan) | 10 | 10 | 0 | 100% |
| PHPUnit — regresi 6 suite (PriceCalculator, HargaDinamis, TransaksiKasir, DashboardWiring, PenambahanStok, Keranjang) | 72 | 72 | 0 | 100% |
| **Total** | **100** | **100** | **0** | **100%** |

Backend metode penerimaan **berfungsi penuh**: pilihan Diantar (ongkir Rp 5.000, wajib alamat) dan Ambil di tempat (gratis, tanpa alamat) konsisten antara `DeliveryMethod`, endpoint `metode-penerimaan`, dan perhitungan total checkout. Frontend **pilihan metode di checkout sudah tersambung API**, tetapi **form alamat belum tersambung backend** (lihat T-01). Satu kegagalan pada suite penuh (102 tes) adalah `Feature/ExampleTest` kedaluwarsa di luar scope.

## Kriteria Penerimaan yang Diuji

Kartu Jira belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item `CHIK-43`/`CHIK-101`/`CHIK-102`/`CHIK-103`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Pelanggan memilih cara terima: diantar atau ambil di tempat | O-01, D-05, review checkout |
| AC-2 | Ongkir berbeda per metode (diantar berbayar, ambil gratis) | D-02, D-03, O-01 |
| AC-3 | Diantar mewajibkan alamat; ambil di tempat tidak | D-04, C-01, C-02 |
| AC-4 | Total checkout memperhitungkan ongkir sesuai metode | C-01, O-05 |
| AC-5 | Form alamat bisa diisi/diubah pelanggan | O-06, O-07 (backend) · ⚠️ T-01 (frontend) |

## Data Uji

| Metode | Ongkir | Butuh alamat |
|---|---|---|
| Diantar (`courier`) | Rp 5.000 | Ya |
| Ambil di tempat (`pickup`) | Rp 0 | Tidak |

## Tabel Test Case

### A. Backend — `CHIK-101` (PHPUnit)

Lokasi: `tests/Unit/DeliveryMethodTest.php` (unit), `tests/Feature/PemesananOnlineTest.php` (endpoint).

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| D-01 | `codes()` | hanya `['pickup', 'courier']` | ✅ Pass |
| D-02 | `shippingCost('courier')` / `('pickup')` | 5000 / 0 | ✅ Pass |
| D-03 | `shippingCost('drone')` (kode tak dikenal) | 0 (fallback) | ✅ Pass |
| D-04 | `requiresAddress()` | courier true, pickup false | ✅ Pass |
| D-05 | `options()` | 2 opsi; label "Diantar"/"Ambil di tempat", ongkir & flag alamat benar | ✅ Pass |
| O-01 | `GET /customer/api/metode-penerimaan` | 200; courier (5000, butuh alamat) + pickup (0, tanpa alamat) | ✅ Pass |

### B. Integrasi checkout (PHPUnit `CheckoutPesananTest`, sudah ada)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| C-01 | Courier + alamat | subtotal + ongkir 5.000 masuk `grand_total`; alamat tersimpan | ✅ Pass |
| C-02 | Pickup tanpa alamat | ongkir 0, `shipping_address` null | ✅ Pass |
| C-03 | Courier tanpa alamat | 422 "Alamat pengiriman tidak ditemukan" | ✅ Pass |
| C-04 | Metode tak valid ("drone") | 422 `delivery_method` | ✅ Pass |

### C. Frontend — `CHIK-102` (review statis)

Lokasi: `resources/views/customer/checkout.blade.php`, `resources/views/customer/alamat.blade.php`.

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| UI-01 | Pilihan cara terima (radio pickup/courier) | `renderMetode()` menimpa label & ongkir dari `GET /metode-penerimaan` | ✅ Pass (statis) |
| UI-02 | Ringkasan total | `subtotal + ongkir(metode)`; baris ongkir disembunyikan saat pickup | ✅ Pass (statis) |
| UI-03 | Guard tanpa alamat (courier) | alert "Simpan alamat pengiriman dulu." | ✅ Pass (statis) |
| UI-04 | Link "Ganti"/"Tambah" alamat | menuju `/alamat#dari-checkout` | ✅ Pass (statis) |
| UI-05 | Form alamat "Simpan Alamat" | **tidak** memanggil `POST/PUT /customer/api/alamat` | ❌ Fail → T-01 |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | **Sedang (blokir CHIK-102)** | Halaman `customer/alamat.blade.php` **belum tersambung backend**: tidak ada `<script>`/`fetch`, tombol "Simpan Alamat" ber-`type="button"` dengan komentar `sementara` (baris 183), tombol "Hapus alamat ini" juga tanpa handler. Pelanggan tidak bisa menambah/mengubah alamat lewat UI, sehingga alur "Diantar" di checkout buntu (kelanjutan T-01 laporan PPL-23). | Wire form ke `POST /customer/api/alamat` (dan `PUT` saat edit, `DELETE` untuk hapus). | ❌ Perlu perbaikan |
| T-02 | Rendah | Teks ongkir di halaman alamat hardcode **"Rp 8.000"** (baris 120), sedangkan backend `DeliveryMethod` flat **Rp 5.000**; checkout menampilkan Rp 5.000. | Samakan dengan tarif backend (Rp 5.000) atau jadikan dinamis dari `/metode-penerimaan`. | ⚠️ Saran |
| T-03 | Rendah (UX) | Checkout selalu memakai alamat pertama/terbaru (`daftarAlamat.data.alamat?.[0]`); tidak ada pemilih alamat bila pelanggan punya lebih dari satu. | Tambah pemilih alamat (radio daftar alamat) di checkout. | ⚠️ Saran |
| T-04 | Rendah | Field form `Patokan (landmark)` dan `Jadikan alamat utama (is_primary)` **tidak ada** di model/validasi `Address` — data akan dibuang bila dikirim; backend checkout memakai `latest('id')`, bukan `is_primary`. | Tambahkan kolom `landmark`/`is_primary` bila diperlukan, atau hapus field dari form. | ⚠️ Saran |
| T-05 | Info | Label statis awal checkout ("Pick Up"/"Ambil di gerai · gratis" & "Diantar kurir · ongkir sesuai area") berbeda dengan label backend ("Ambil di tempat"/"Diantar · ongkir Rp 5.000") sampai `renderMetode()` berjalan. Radio default `checked` ada di courier padahal urutan HTML pickup lebih dulu. | Sejajarkan label awal dengan backend & pertimbangkan default pickup. | ℹ️ Catatan |
| T-06 | Info (lingkungan) | Build frontend tetap terblokir di Windows (`@tailwindcss/oxide-linux-x64-gnu`, lihat T-03 laporan PPL-13), sehingga verifikasi `CHIK-102` lewat review statis. | Rujuk `@tailwindcss/oxide` (tanpa pin platform). | ❌ Perlu perbaikan |
| T-07 | Info | `tests/Feature/ExampleTest.php` bawaan gagal (`GET /` → 302), di luar scope PPL-24. | Hapus atau ubah jadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer di PATH.

1. **Tes otomatis metode penerimaan & regresi (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="DeliveryMethodTest|CheckoutPesananTest|PemesananOnlineTest|PriceCalculatorTest|HargaDinamisTest|TransaksiKasirTest|DashboardWiringTest|PenambahanStokTest|KeranjangTest"
   ```
   Skema tabel tes mengikuti `database/migrate.php` di `tests/Concerns/MenyiapkanDataChikumi.php` (dan `CheckoutPesananTest::buatSkemaPesanan`). Bila skema berubah, sesuaikan helper tersebut.

2. **Black-box UI (opsional, setelah T-06 diperbaiki)**:
   `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `customer@gmail.com` / `customer123`, buka `/customer/checkout`, ganti cara terima (Diantar/Ambil di tempat) dan amati ongkir & total; buka `/customer/alamat` dan coba simpan alamat (setelah T-01 diperbaiki), pantau tab **Network** DevTools.