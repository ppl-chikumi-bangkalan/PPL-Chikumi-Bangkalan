# Laporan Testing — [PPL-20] Harga Dinamis Sesuai Pilihan

- **Backlog**: [PPL-20] Harga Dinamis Sesuai Pilihan (Jira `CHIK-15`), subtask testing `CHIK-78` — Sprint 2
- **Aplikasi**: Chikumi (Laravel 13.39, PHP 8.3.36, MySQL 8.0.30 via Laragon)
- **Kode yang diuji**: branch `main` commit `2937859` (sudah berisi PR #14 `CHIK-76` tampilan harga + `CHIK-77` logika harga)
- **Metode**:
  1. **Tes otomatis PHPUnit**: unit test `App\Support\PriceCalculator` + feature test endpoint harga, keranjang, dan halaman atur produk (SQLite in-memory)
  2. **Black-box UI**: klik pilihan ukuran/varian di browser seperti pelanggan, amati harga di bar bawah
  3. **Black-box API**: panggil endpoint dari sesi login pelanggan, cek status HTTP & isi respons (seperti tab Network DevTools)
- **Terverifikasi**: 2026-11-08

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit (otomatis) | 37 | 37 | 0 | 100% |
| Black-box UI | 11 | 11 | 0 | 100% |
| Black-box API | 6 | 5 | 1 | 83% |
| **Total** | **54** | **53** | **1** | **98%** |

Perhitungan harga (harga dasar ukuran + tambahan varian, × jumlah) **benar dan konsisten** antara tampilan, endpoint preview harga, dan keranjang. Satu kegagalan (TC-HD-API-06) berasal dari aturan *filling wajib pilih 1* yang hanya dicek di tampilan, bukan di server (lihat Temuan T-01).

## Kriteria Penerimaan yang Diuji

Kartu Jira CHIK-15 belum memuat kriteria penerimaan tertulis, sehingga kriteria di bawah diturunkan dari tujuan Sprint 2 dan subtask CHIK-76/CHIK-77. **Mohon Product Owner mengonfirmasi atau melengkapi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Harga langsung berubah saat pelanggan mengganti ukuran (Reguler/Large) | UI-02, UI-08, F02, U02 |
| AC-2 | Harga bertambah sesuai tambahan harga varian yang dipilih, dan kembali saat pilihan diganti/dilepas | UI-03 s.d. UI-07, F03–F06, U03, U06 |
| AC-3 | Harga yang tampil sama dengan harga yang dihitung server dan yang tersimpan di keranjang | UI-10, API-01, F18 |

## Data Uji

Black-box memakai database terpisah `chikumi_uji` yang diisi `database/migrate.php` + `database/seed.php`:

| Item | Tipe | Harga |
|---|---|---|
| Reguler (stok 20) | Ukuran | Rp 15.000 |
| Large (stok 3) | Ukuran | Rp 22.000 |
| Mozzarella Cheese | filling | +Rp 3.000 |
| Barbeque Sauce | dipping_sauce | +Rp 2.000 |
| Cheese Sauce | dipping_sauce | +Rp 2.500 |
| Balado Extra Pedas | seasoning | +Rp 0 |

PHPUnit memakai data yang sama, ditambah: filling *Cokelat Lumer* (+3.500), seasoning *Keju Bubuk* (+1.000), dip *Sambal Matah* yang **tidak tersedia** (+1.500), dan satu produk lain untuk uji kepemilikan ukuran/varian.

## Tabel Test Case

### Black-box UI — Halaman Atur Produk (`/customer/produk?produk=1`, login `customer@gmail.com`)

| ID | Langkah | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|
| TC-HD-UI-01 | Buka halaman produk | Harga awal ukuran default: Rp 15.000, rincian "Reguler" | Rp 15.000 · "Reguler" | ✅ Pass |
| TC-HD-UI-02 | Klik ukuran **Large** | Rp 22.000 | Rp 22.000 · "Large" | ✅ Pass |
| TC-HD-UI-03 | Klik filling **Mozzarella Cheese** | 22.000 + 3.000 = Rp 25.000 | Rp 25.000 · "Large · Mozzarella Cheese" | ✅ Pass |
| TC-HD-UI-04 | Klik dip **Barbeque Sauce** | 25.000 + 2.000 = Rp 27.000 | Rp 27.000 | ✅ Pass |
| TC-HD-UI-05 | Ganti dip ke **Cheese Sauce** | BBQ terlepas, 25.000 + 2.500 = Rp 27.500 | Rp 27.500, hanya Cheese Sauce terpilih | ✅ Pass |
| TC-HD-UI-06 | Centang seasoning **Balado** (+0) | Harga tetap Rp 27.500, rincian bertambah "Balado Extra Pedas" | Rp 27.500, rincian bertambah | ✅ Pass |
| TC-HD-UI-07 | Hapus centang Balado | Harga tetap, Balado hilang dari rincian | Rp 27.500 · "Large · Mozzarella Cheese · Cheese Sauce" | ✅ Pass |
| TC-HD-UI-08 | Ganti ukuran kembali ke **Reguler** | 15.000 + 3.000 + 2.500 = Rp 20.500 | Rp 20.500 | ✅ Pass |
| TC-HD-UI-09 | Ubah Jumlah menjadi 2 | "Harga per porsi" tetap Rp 20.500 | Rp 20.500 (lihat saran T-03) | ✅ Pass |
| TC-HD-UI-10 | Klik **+ Keranjang**, lalu `GET /customer/api/keranjang` | Item tersimpan dengan harga_satuan 20.500, subtotal 41.000 | Pesan "Berhasil menambah ke keranjang."; harga_satuan 20500, subtotal 41000 | ✅ Pass |
| TC-HD-UI-11 | Muat ulang, klik + Keranjang tanpa memilih filling | Ditolak dengan pesan | Pesan "Pilih filling dulu." | ✅ Pass |

### Black-box API — Preview Harga & Keranjang (sesi login pelanggan)

| ID | Request | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|
| TC-HD-API-01 | `GET /customer/api/produk/1/harga?product_size_id=2&variant_ids[]=1&variant_ids[]=3&quantity=2` | 200; harga_satuan 27.500; subtotal 55.000 | 200; harga_satuan 27500; total_tambahan 5500; subtotal 55000 | ✅ Pass |
| TC-HD-API-02 | Dua dipping sauce (`variant_ids[]=2&variant_ids[]=3`) | 422 | 422 "Pilih maksimal satu varian untuk tipe filling atau dipping sauce." | ✅ Pass |
| TC-HD-API-03 | `product_size_id=999` | 422 | 422 "Ukuran bukan milik produk ini." | ✅ Pass |
| TC-HD-API-04 | `quantity=0` | 422 | 422 "The quantity field must be at least 1." | ✅ Pass |
| TC-HD-API-05 | `variant_ids[]=999` | 422 | 422 "Ada varian yang tidak ditemukan." | ✅ Pass |
| TC-HD-API-06 | `POST /customer/api/keranjang` tanpa filling (`variant_ids: []`) | 422, sesuai aturan "Creamy Filling — wajib pilih 1" | **201** "Berhasil menambah ke keranjang." (item Reguler tanpa varian Rp 15.000 masuk keranjang) | ❌ Fail → T-01 |

### Tes Otomatis PHPUnit

Lokasi: `tests/Unit/PriceCalculatorTest.php`, `tests/Feature/HargaDinamisTest.php`, helper `tests/Concerns/MenyiapkanDataChikumi.php`.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U01 | Tanpa varian | harga_satuan = harga dasar (15.000) | ✅ Pass |
| U02 | Ganti ukuran | Reguler 15.000, Large 22.000 | ✅ Pass |
| U03 | Mozzarella + BBQ + Keju Bubuk | tambahan 6.000; harga_satuan 21.000 | ✅ Pass |
| U04 | Large + Mozzarella × 3 | harga_satuan 25.000; subtotal 75.000 | ✅ Pass |
| U05 | Jumlah 0 / negatif | dianggap 1 | ✅ Pass |
| U06 | Varian gratis (Balado) | harga tidak berubah | ✅ Pass |
| U07 | Dua seasoning | diperbolehkan | ✅ Pass |
| U08 | ID varian dobel | dihitung sekali | ✅ Pass |
| U09 | Daftar varian kosong | koleksi kosong | ✅ Pass |
| U10 (×4) | Dua filling / dua dip / varian produk lain / varian habis | ValidationException dengan pesan sesuai | ✅ Pass |
| U11 | Varian tidak ada | ValidationException | ✅ Pass |
| F01 | Endpoint: Reguler tanpa varian | 200; 15.000 | ✅ Pass |
| F02 | Endpoint: Large | 200; 22.000 | ✅ Pass |
| F03 | Mozzarella + BBQ + Balado | 200; 20.000 | ✅ Pass |
| F04 | Ganti dip BBQ → Cheese Sauce | 17.000 → 17.500 | ✅ Pass |
| F05 | Large + Mozzarella + Cheese Sauce × 2 | 27.500; subtotal 55.000 | ✅ Pass |
| F06 | Dua seasoning | 200; 16.000 | ✅ Pass |
| F07–F10 | Dua filling / dua dip / varian produk lain / varian habis | 422 `variant_ids` | ✅ Pass |
| F11 | Varian tidak ditemukan | 422 | ✅ Pass |
| F12 | Ukuran milik produk lain | 422 | ✅ Pass |
| F13 | Ukuran kosong | 422 `product_size_id` | ✅ Pass |
| F14 | Jumlah 0 | 422 `quantity` | ✅ Pass |
| F15 | ID varian dobel | 422 | ✅ Pass |
| F16 | Produk nonaktif | 404 | ✅ Pass |
| F17 | Tanpa login | 401 | ✅ Pass |
| F18 | Harga keranjang = preview (Large + 3 varian × 2) | harga_satuan 28.000 sama; subtotal sama | ✅ Pass |
| F19 | Halaman: harga awal | Rp 15.000 di `#hargaSatuan` | ✅ Pass |
| F20 | Halaman: data harga untuk hitung otomatis | `data-harga` ukuran & varian benar, label "+Rp 3.000" | ✅ Pass |
| F21 | Halaman: `?ukuran=` Large | harga awal Rp 22.000 | ✅ Pass |
| F22 | Halaman: varian habis | tampil "· habis", tidak bisa dipilih | ✅ Pass |
| F23 | Halaman: ukuran stok 0 | radio nonaktif, label "habis" | ✅ Pass |

Hasil run: `OK (37 tests, 92 assertions)` — PHPUnit 12.5.42, PHP 8.3.36, 2,06 detik.

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | Sedang | Aturan **filling wajib pilih 1** hanya dicek di JavaScript halaman produk. `PriceCalculator::resolveVariants` (dipakai endpoint harga dan keranjang) tidak mewajibkan filling, sehingga request langsung ke API dapat memasukkan item tanpa filling (TC-HD-API-06). | Tambah validasi di `resolveVariants`: jika produk punya filling tersedia, wajib tepat 1 filling. Tambahkan feature test untuk kasus ini. Usulan: buat kartu bug baru di Sprint 2. | ❌ Perlu perbaikan |
| T-02 | Rendah (UX) | Dip Sauce bertanda "pilih 1" (opsional), tetapi memakai radio button sehingga setelah dipilih tidak bisa dibatalkan (klik ulang Cheese Sauce tetap terpilih). | Tambah opsi "Tanpa saus" atau izinkan klik ulang untuk melepas pilihan. | ⚠️ Saran |
| T-03 | Rendah (UX) | Bar bawah menampilkan harga **per porsi**; mengubah Jumlah tidak menampilkan total sebelum masuk keranjang. | Tambah baris "Total: Rp …" (harga × jumlah) bila diperlukan PO. | ⚠️ Saran |
| T-04 | Info | Harga di halaman dihitung di browser dari atribut `data-harga`, bukan dari endpoint `/harga`. Saat ini hasilnya sama (UI-10, F18), tapi aturan harga baru (mis. diskon) harus diubah di dua tempat. | Pertimbangkan memanggil endpoint `/harga` atau menjaga test F18 tetap ada. | ℹ️ Catatan |
| T-05 | Info | `tests/Feature/ExampleTest.php` bawaan Laravel gagal (`GET /` → 302 ke `/login`), di luar scope PPL-20. | Hapus atau sesuaikan menjadi `assertRedirect('/login')`. | ℹ️ Known issue |

## Cara Menjalankan Ulang

**Prasyarat**: PHP **8.3+** (Laravel 13 tidak bisa di-install dengan PHP 8.1 bawaan Laragon), Composer, Node.js, MySQL.

1. `composer install`, salin `.env.example` → `.env`, lalu `php artisan key:generate`
2. Tes otomatis (memakai SQLite in-memory, tidak menyentuh MySQL):
   ```
   php artisan test --filter="PriceCalculatorTest|HargaDinamisTest"
   ```
   Karena proyek memakai `database/migrate.php` (bukan migration Laravel), skema tabel untuk tes dibuat di `tests/Concerns/MenyiapkanDataChikumi.php` mengikuti kolom di `migrate.php`. Jika skema di `migrate.php` berubah, sesuaikan juga helper tersebut.
3. Black-box: jalankan `php database/migrate.php` dan `php database/seed.php` pada database uji, `npm install && npm run build`, `php artisan serve`, login sebagai `customer@gmail.com` / `customer123`, buka `/customer/produk?produk=1`, lalu ikuti tabel di atas sambil memantau tab **Network** DevTools.
