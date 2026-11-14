# Laporan Testing — [PPL-21] Keranjang Belanja

- **Backlog**: [PPL-21] Keranjang Belanja — Jira `CHIK-41` (berisi `CHIK-95` backend keranjang produk terkonfigurasi, `CHIK-96` frontend halaman keranjang); tiket testing `CHIK-97` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: branch `main` commit `7ee06e2` (sudah memuat `CHIK-95` via PR #31 dan `CHIK-96` via PR #25).
- **Scope (mapping tim)**:
  - `CHIK-95` — sisi **backend**: `Customer\CartController` (CRUD keranjang produk terkonfigurasi ukuran + varian) + penambahan field `produk_id` pada respons `cartItems()` untuk link "Ubah pilihan".
  - `CHIK-96` — sisi **frontend**: halaman `customer/keranjang.blade.php` (daftar item, ubah jumlah, hapus, hapus semua, ringkasan subtotal, tombol checkout) + link keranjang di `produk.blade.php`.
- **Metode**:
  1. **Tes otomatis PHPUnit**: feature test baru `KeranjangTest` (17 skenario) + regresi 6 suite.
  2. **Review statis**: alur JS halaman keranjang (`muat`, `aksiItem`, `hapusSemua`, `render`) karena build frontend tidak dapat dijalankan di mesin uji (lihat T-06).
  3. **Verifikasi temuan**: skenario tumpuk-stok dicek manual lewat test sementara (lalu dihapus).
- **Terverifikasi**: 2026-11-14

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — `KeranjangTest` (PPL-21, baru) | 17 | 17 | 0 | 100% |
| PHPUnit — `PriceCalculatorTest` (regresi) | 14 | 14 | 0 | 100% |
| PHPUnit — `HargaDinamisTest` (regresi) | 23 | 23 | 0 | 100% |
| PHPUnit — `TransaksiKasirTest` (regresi) | 8 | 8 | 0 | 100% |
| PHPUnit — `DashboardWiringTest` (regresi) | 2 | 2 | 0 | 100% |
| PHPUnit — `PenambahanStokTest` (regresi) | 8 | 8 | 0 | 100% |
| PHPUnit — `CheckoutPesananTest` (regresi) | 13 | 13 | 0 | 100% |
| **Total (tanpa ExampleTest)** | **85** | **85** | **0** | **100%** |

Keranjang belanja (produk terkonfigurasi: ukuran + varian) **berfungsi penuh** pada alur inti: tambah, tumpuk item identik, pisah item beda varian, lihat rincian & subtotal, ubah jumlah, hapus item, serta pembatasan antar akun. Satu kegagalan pada suite penuh (87 tes) adalah `Feature/ExampleTest` kedaluwarsa di luar scope. Ditemukan satu cacat nyata pada penumpukan stok (T-01).

## Kriteria Penerimaan yang Diuji

Kartu Jira belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari judul work item `CHIK-41`/`CHIK-95`/`CHIK-96`/`CHIK-97`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Pelanggan menambah produk terkonfigurasi (ukuran + varian) ke keranjang | K-01, K-02, K-03 |
| AC-2 | Item identik (ukuran + varian sama) ditumpuk, bukan baris ganda | K-02 |
| AC-3 | Keranjang menampilkan rincian harga (satuan, subtotal, jumlah, total) | K-07, K-08, review `render` |
| AC-4 | Pelanggan mengubah jumlah & menghapus item | K-09, K-13, K-14 |
| AC-5 | Keranjang terisolasi antar akun | K-12, K-15 |
| AC-6 | Validasi: stok tidak cukup / varian tidak valid / produk nonaktif ditolak | K-04, K-05, K-06, K-10 |

## Data Uji

| Item | Tipe | Harga | Stok |
|---|---|---|---|
| Chikumi Original Box · Regular | Ukuran | Rp 15.000 | 20 |
| Chikumi Original Box · Large | Ukuran | Rp 22.000 | 3 |
| Mozzarella Cheese | filling | +Rp 3.000 | — |
| Barbeque Sauce | dipping_sauce | +Rp 2.000 | — |

(Identik dengan `tests/Concerns/MenyiapkanDataChikumi.php`; pelanggan uji `pelanggan.uji@gmail.com`, pelanggan lain dibuat langsung di test.)

## Tabel Test Case

### A. Backend — `CHIK-95` (PHPUnit `KeranjangTest`)

Lokasi: `tests/Feature/KeranjangTest.php`. Endpoint: `GET/POST /customer/api/keranjang`, `PUT/DELETE /customer/api/keranjang/{keranjang}`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| K-01 | Tambah item ukuran + 2 varian | 201; baris keranjang + 2 varian tersimpan | ✅ Pass |
| K-02 | Tambah item identik 2× (qty 2 + 2) | 1 baris, quantity jadi 4 | ✅ Pass |
| K-03 | Ukuran sama, varian berbeda | 2 baris terpisah | ✅ Pass |
| K-04 | Tambah qty 21 > stok 20 | 422; keranjang kosong | ✅ Pass |
| K-05 | Tambah 2 filling (tidak valid) | 422 `variant_ids`; keranjang kosong | ✅ Pass |
| K-06 | Tambah produk nonaktif | 422; keranjang kosong | ✅ Pass |
| K-07 | Lihat keranjang (2 item) | `total_item` 3, `subtotal` 65.000, 2 item | ✅ Pass |
| K-08 | Lihat rincian item | `harga_satuan` 18.000, `subtotal` 36.000, `stok_cukup` true, varian "Mozzarella Cheese" | ✅ Pass |
| K-09 | Ubah jumlah valid (1 → 5) | 200; quantity 5 | ✅ Pass |
| K-10 | Ubah jumlah melebihi stok (21) | 422; quantity tetap 1 | ✅ Pass |
| K-11 | Ubah jumlah < 1 | 422 `quantity` | ✅ Pass |
| K-12 | Ubah keranjang akun lain | 403; quantity tidak berubah | ✅ Pass |
| K-13 | Hapus item sendiri | 200; keranjang kosong | ✅ Pass |
| K-14 | Hapus item akun lain | 403; item tetap ada | ✅ Pass |
| K-15 | Lihat keranjang tanpa login | 401 | ✅ Pass |
| K-16 | Tambah tanpa login | 401 | ✅ Pass |
| K-17 | Lihat keranjang kosong | `total_item` 0, `subtotal` 0, 0 item | ✅ Pass |

### B. Frontend — `CHIK-96` Halaman keranjang (review statis)

Lokasi: `resources/views/customer/keranjang.blade.php`.

| ID | Elemen | Perilaku terverifikasi | Status |
|---|---|---|---|
| UI-01 | `muat()` → `GET /keranjang` | render daftar item / pesan kosong / pesan error | ✅ Pass (statis) |
| UI-02 | `kartuItem()` | gambar, nama, rincian ukuran·varian, subtotal, peringatan "Stok tidak mencukupi" | ✅ Pass (statis) |
| UI-03 | `data-aksi="tambah/kurang"` | `PUT /keranjang/{id}` dengan quantity baru; tombol kurang nonaktif saat qty 1 | ✅ Pass (statis) |
| UI-04 | `data-aksi="hapus"` | `DELETE /keranjang/{id}` | ✅ Pass (statis) |
| UI-05 | `#hapusSemua` | loop `DELETE` per item setelah konfirmasi | ✅ Pass (statis) |
| UI-06 | Ringkasan & bottom bar | `#subtotalTeks`, `#subtotalBarTeks`, `#totalItemTeks` ikut data | ✅ Pass (statis) |
| UI-07 | `#btnCheckout` | nonaktif (`pointer-events-none`/opacity) saat keranjang kosong | ✅ Pass (statis) |
| UI-08 | "Ubah pilihan" | link ke `/produk?produk=&ukuran=` (pra-pilih ukuran) | ⚠️ Lihat T-03 |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | **Sedang (cacat)** | `CartController::store` saat item identik **hanya memvalidasi quantity masuk terhadap stok**, bukan total akumulasi. Terverifikasi: tambah 15 (stok 20) lalu tambah 15 lagi → quantity keranjang 30 > stok 20. Kelebihan baru tertolak saat checkout/ubah, tapi keranjang sempat menampung melebihi stok. | Saat menumpuk, cek `quantity_lama + quantity_baru <= stok` sebelum update. Tambah feature test kasus ini. | ❌ Perlu perbaikan |
| T-02 | Sedang (scope) | Commit `CHIK-95` (`d00e031`) mencampur perubahan `checkout.blade.php` (206 baris) yang secara fungsional milik CHIK-98/99 (checkout). | Pisahkan perubahan checkout ke PR-nya sendiri. | ⚠️ Saran |
| T-03 | Rendah (UX) | "Ubah pilihan" membuka halaman produk dengan ukuran terpilih, tetapi **tidak benar-benar mengganti** item lama: menambah item yang sama akan menumpuk quantity, item beda varian menjadi baris baru — tidak ada endpoint untuk mengganti varian pada baris yang sudah ada. | Tambah endpoint ganti varian (atau hapus-lalu-tambah) dan buat alur "Ubah" mengganti baris, bukan menambah. | ⚠️ Saran |
| T-04 | Rendah | "Hapus semua" memanggil `DELETE` per item dalam loop; tidak ada endpoint bulk-delete. Aman untuk keranjang kecil, tapi N request. | Pertimbangkan endpoint `DELETE /keranjang` (bulk) bila perlu. | ℹ️ Catatan |
| T-05 | Info | Path gambar di respons masih `image_url` mentah; frontend prepend `/` + fallback `chikumi-1.png` di sisi JS (tidak konsisten dengan `DashboardController` kasir yang meresolusi `asset()` di server). | Resolusikan URL gambar di server agar konsisten lintas halaman. | ℹ️ Catatan |
| T-06 | Info (lingkungan) | Build frontend tetap terblokir di Windows (`@tailwindcss/oxide-linux-x64-gnu`, lihat T-03 laporan PPL-13), sehingga verifikasi `CHIK-96` lewat review statis. | Rujuk `@tailwindcss/oxide` (tanpa pin platform). | ❌ Perlu perbaikan |
| T-07 | Info | `tests/Feature/ExampleTest.php` bawaan gagal (`GET /` → 302), di luar scope PPL-21. | Hapus atau ubah jadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer di PATH.

1. **Tes otomatis keranjang & regresi (SQLite in-memory, tidak menyentuh MySQL)**:
   ```
   php artisan test --filter="KeranjangTest|PriceCalculatorTest|HargaDinamisTest|TransaksiKasirTest|DashboardWiringTest|PenambahanStokTest|CheckoutPesananTest"
   ```
   Skema tabel tes mengikuti `database/migrate.php` di `tests/Concerns/MenyiapkanDataChikumi.php` (tabel `carts` & `cart_item_variants` sudah tersedia). Bila skema berubah, sesuaikan helper tersebut.

2. **Black-box UI (opsional, setelah T-06 diperbaiki)**:
   `composer install`, salin `.env.example` → `.env`, `php artisan key:generate`, `npm install && npm run build`, `php database/migrate.php` + `php database/seed.php`, `php artisan serve`, login `customer@gmail.com` / `customer123`, tambah produk dari `/customer/produk?produk=1` ("+ Keranjang"), buka `/customer/keranjang`, lalu coba ubah jumlah, hapus item, hapus semua, dan amati tab **Network** DevTools (endpoint `GET/PUT/DELETE /customer/api/keranjang`).