# Laporan Testing — [PPL-25] Total Pembayaran & Ongkos Kirim (API Pengiriman)

- **Backlog**: [PPL-25] Total Pembayaran & Ongkos Kirim (API Pengiriman) — Jira `CHIK-48` (berisi `CHIK-109` konfirmasi titik asal pengiriman & kurir dengan owner, `CHIK-110` integrasi API ongkos kirim, `CHIK-111` frontend total pembayaran + ongkir); tiket testing `CHIK-112`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `23362f5`. Ketiga subtask sudah merge: `CHIK-109` → PR #40 (`dfeacf4`), `CHIK-110` → PR #41 (`141096d`), `CHIK-111` → PR #37 (`d7013b7`). Pengujian di branch `CHIK-112-testing-ongkir`.
- **Scope (mapping tim)**:
  - `CHIK-110` — **backend**: `app/Support/ShippingCost.php` (RajaOngkir Komerce v1 + fallback flat), `Customer\ShippingCostController` (`GET /customer/api/ongkir`, `GET /customer/api/kota`), `config/services.php` + `config/wilayah.php`, integrasi ke `OrderController::checkout`.
  - `CHIK-111` — **frontend**: `resources/views/customer/checkout.blade.php` (ringkasan subtotal + ongkir + total, bottom bar) dan `resources/views/customer/alamat.blade.php` (pencarian kota + ongkir dinamis).
  - `CHIK-109` — **owner**: `Owner\ShippingConfirmationController` (titik asal, kurir, konfirmasi pengiriman), tabel `shipping_origins` & `couriers`.
- **Metode**:
  1. **PHPUnit** `tests/Feature/OngkosKirimTest.php` — 32 tes; respons RajaOngkir dipalsukan dengan `Http::fake`, nilai ongkir **dicocokkan dengan hitungan manual**.
  2. **Black-box API + MySQL** — endpoint ongkir/kota/metode, checkout nyata, konfirmasi pengiriman owner, lalu cek `orders` di database.
  3. **Black-box browser** — halaman alamat & checkout: pilih kota, ganti metode, lihat total, checkout end-to-end.
- **Terverifikasi**: 2026-11-21
- **Keterbatasan**: `.env` **tidak berisi `RAJAONGKIR_API_KEY`**, sehingga aplikasi berjalan pada **mode tarif flat Rp 5.000** untuk semua tujuan. Perhitungan nyata dari RajaOngkir diuji dengan respons palsu yang mengikuti format dokumentasi Komerce. Verifikasi ke API asli menunggu API key.

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — ongkir, total, titik asal (`OngkosKirimTest`) | 32 | 32 | 0 | 100% |
| Black-box API + MySQL | 8 | 8 | 0 | 100% |
| Black-box browser | 6 | 5 | 1 | 83,3% |
| **Kriteria penerimaan (AC-1..AC-7)** | **7** | **5** | **2** | **71,4%** |
| Regresi suite penuh | 187 | 184 | 2 (lama) + 1 skipped | 98,4% |

**Hasil: LULUS DENGAN CATATAN.**

Perhitungan total pembayaran **benar dan konsisten**: `total = subtotal + ongkir`, ongkir yang ditampilkan di halaman checkout **sama persis** dengan yang disimpan di `orders.shipping_cost` (dibuktikan lewat checkout nyata: Rp 30.000 + Rp 5.000 = **Rp 35.000**, tersimpan `subtotal=30000, shipping_cost=5000, grand_total=35000`). Pemilihan layanan termurah, cache 10 menit, dan fallback ke tarif flat saat API gagal/kosong/error semuanya bekerja. Titik asal & kurir bisa dikelola owner dan konfirmasi pengiriman mengubah status ke `processing`.

Dua catatan penting: **(1) ongkir tidak bergantung pada berat/jumlah barang** — selalu 1 kg, dan **(2) titik asal yang dipilih owner tidak mempengaruhi ongkir** (selalu memakai `RAJAONGKIR_ORIGIN_ID` dari config). Keduanya perlu keputusan PO apakah memang di luar scope PPL-25.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-48`/`CHIK-112` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Ongkir diambil dari API pengiriman, memakai layanan termurah | ✅ Lulus | API-04, API-05 |
| AC-2 | Tanpa API key / API gagal / respons kosong → fallback tarif flat Rp 5.000 | ✅ Lulus | API-01..API-03, API-06..API-08 |
| AC-3 | Total bayar = subtotal + ongkir, konsisten antara tampilan dan data tersimpan | ✅ Lulus | TOT-01..TOT-05, E2E-01 |
| AC-4 | Ambil di tempat → ongkir Rp 0 dan tidak butuh alamat | ✅ Lulus | TOT-03, UI-04 |
| AC-5 | Owner dapat mengatur titik asal & kurir, lalu mengonfirmasi pengiriman | ✅ Lulus | OWN-01..OWN-05 |
| AC-6 | Ongkir mengikuti berat/jumlah barang | ❌ Tidak terpenuhi — berat selalu 1.000 g (P-01) | TOT-06 |
| AC-7 | Titik asal yang dikonfirmasi menentukan ongkir | ❌ Tidak terpenuhi — selalu `RAJAONGKIR_ORIGIN_ID` (P-02) | OWN-06 |

## Data Uji

| Item | Nilai |
|---|---|
| Reguler | Rp 15.000 |
| Ongkir flat (tanpa API) | Rp 5.000 |
| `RAJAONGKIR_ORIGIN_ID` (default config) | 444 |
| `RAJAONGKIR_ORIGIN_ID` (`.env.example`) | 70205 |
| Daftar wilayah lokal | 66 kecamatan (sekitar Bangkalan) |
| Titik asal tersedia (DB) | 2 — Dapur Chikumi Bangkalan, Outlet Chikumi Kota |
| Kurir tersedia (DB) | 2 — Kurir Toko Chikumi, Kurir Mitra Bangkalan |

Akun uji: `customer@gmail.com`/`customer123`, `owner@chikumi.com`/`owner123`.

## Tabel Test Case

### A. Perhitungan ongkir (PHPUnit, `Http::fake`)

Lokasi: `tests/Feature/OngkosKirimTest.php`. Hasil: **32 passed, 97 assertions**.

| ID | Skenario | Hitungan manual | Hasil kode | Status |
|---|---|---|---|---|
| API-01 | Tanpa API key | flat 5.000, `sumber=flat`, `etd=null` | sesuai; tidak ada HTTP | ✅ Pass |
| API-02 | `city_id` kosong | flat 5.000 | sesuai; tidak ada HTTP | ✅ Pass |
| API-03 | Tarif 9.000 / 12.000 / 25.000 | termurah **9.000**, etd `3-4` | 9.000, `rajaongkir` | ✅ Pass |
| API-04 | Payload ke RajaOngkir | `origin=444`, `destination=555`, `weight=2000`, `courier=jne`, header `key` | sesuai | ✅ Pass |
| API-05 | Satu layanan `cost=null` | abaikan, ambil 15.000 | 15.000 | ✅ Pass |
| API-06 | Respons `data` kosong | flat 5.000 | sesuai | ✅ Pass |
| API-07 | HTTP 500 | flat 5.000 | sesuai | ✅ Pass |
| API-08 | Koneksi gagal (exception) | flat 5.000 | sesuai | ✅ Pass |
| API-09 | Dua kali panggil kota yang sama | 1 request (cache 10 menit) | 1 request | ✅ Pass |

### B. Endpoint ongkir & kota

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| EP-01 | `GET /customer/api/ongkir?city_id=444` | 200, `ongkir=5000`, `sumber=flat` | ✅ Pass |
| EP-02 | `GET …/ongkir?address_id={milik sendiri}` | 200, `alamat_id` terisi | ✅ Pass |
| EP-03 | `GET …/ongkir` tanpa alamat & tanpa city_id | 422 | ✅ Pass |
| EP-04 | `GET …/ongkir?address_id={milik pelanggan lain}` | 422 | ✅ Pass |
| EP-05 | `GET …/ongkir?city_id=444&berat=2000` | berat diteruskan ke API | ✅ Pass |
| EP-06 | `GET …/kota?q=bangkalan` tanpa API key | 10 hasil lokal, `id=null` | ✅ Pass |
| EP-07 | `GET …/kota?q=ba` (2 huruf) | 422 | ✅ Pass |
| EP-08 | `GET …/kota?q=bangkalan` dengan API | `id=1234` dari RajaOngkir | ✅ Pass |

### C. Total pembayaran (PHPUnit)

| ID | Skenario | Hitungan manual | Hasil | Status |
|---|---|---|---|---|
| TOT-01 | Checkout kurir 2 pcs, ongkir API 12.000 | 30.000 + 12.000 = **42.000** | `subtotal=30000`, `shipping_cost=12000`, `grand_total=42000` | ✅ Pass |
| TOT-02 | Ongkir tampil vs ditagih (API 9.500) | keduanya **9.500**; total 24.500 | sama | ✅ Pass |
| TOT-03 | Checkout pickup 2 pcs | ongkir 0, total 30.000, `shipping_address=null` | sesuai | ✅ Pass |
| TOT-04 | Kurir tanpa API key | 15.000 + 5.000 = 20.000 | sesuai | ✅ Pass |
| TOT-05 | Alamat tanpa `city_id` | flat 5.000, API tidak dipanggil | sesuai | ✅ Pass |
| TOT-06 | Kurir 10 pcs (API menaikkan tarif bila berat ≥ 2 kg) | berat selalu 1.000 g → **9.000** | 9.000 (bukan 18.000) | ⚠️ Sesuai kode, lihat P-01 |
| TOT-07 | Checkout kurir tanpa alamat | 422, tidak ada order tersimpan | sesuai | ✅ Pass |

### D. Titik asal & kurir (CHIK-109)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| OWN-01 | Owner tambah titik asal | 201, tersimpan `is_active=true` | ✅ Pass |
| OWN-02 | Owner tambah kurir | 201, nama & telepon tersimpan | ✅ Pass |
| OWN-03 | Konfirmasi pengiriman pesanan kurir | 200, status → `processing`, `shipping_confirmed_at/by` terisi | ✅ Pass |
| OWN-04 | Konfirmasi untuk pesanan pickup | 422 | ✅ Pass |
| OWN-05 | Konfirmasi pesanan `completed` | 422 | ✅ Pass |
| OWN-06 | Kurir non-aktif dipilih | 422 `courier_id` | ✅ Pass |
| OWN-07 | Pelanggan mengakses endpoint owner | 403 | ✅ Pass |
| OWN-08 | Titik asal mempengaruhi ongkir | — | ❌ Tidak: `origin` tetap 444 (P-02) |

### E. Black-box (API nyata + MySQL + browser)

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| E2E-01 | Checkout kurir dari browser | Alert: "Pesanan CHKM-261120194842-414 berhasil dibuat. Total **Rp 35.000**"; DB: `subtotal=30000, shipping_cost=5000, grand_total=35000` | ✅ Pass |
| E2E-02 | Konfirmasi pengiriman order tersebut | 200; DB: `status=processing`, `shipping_origin_id=1`, `courier_id=1`, `shipping_confirmed_by=1` | ✅ Pass |
| E2E-03 | Konfirmasi untuk order pickup #76 | 422, data tidak berubah | ✅ Pass |
| UI-01 | Halaman alamat: default ongkir | "Ongkir ke area ini **Rp 5.000**" | ✅ Pass |
| UI-02 | Ketik "bangkalan" | 10 saran muncul (Arosbaya, Balega, …), klik → area & kode pos terisi otomatis | ✅ Pass |
| UI-03 | Simpan alamat | "Alamat disimpan.", tersimpan dengan `city_id=NULL` | ✅ Pass |
| UI-04 | Checkout: ganti metode | Daintar → ongkir 5.000/total 35.000; Ambil di tempat → ongkir 0/total 30.000; kembali → 35.000 | ✅ Pass |
| UI-05 | Checkout: area & ongkir alamat | "Area Arosbaya, Bangkalan, Jawa Timur · Ongkir Rp 5.000" | ✅ Pass |
| UI-06 | Checkout dengan keranjang kosong | Subtotal Rp 0, tetapi **Total Rp 5.000** (ongkir tetap dihitung) | ❌ Gagal (P-03) |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| P-01 | **Tinggi (scope)** | **Ongkir tidak mengikuti berat/jumlah barang.** `OrderController::checkout` memanggil `ShippingCost::quote($address->city_id)` tanpa argumen berat, sehingga `weight` selalu **1.000 g** berapa pun jumlah itemnya. Membeli 1 pcs dan 20 pcs dikenai ongkir yang sama. Komentar di `ShippingCost.php` menyebut ini disengaja ("tambah parameter berat per item saat produk punya kolom berat"), tetapi `product_sizes` belum punya kolom berat sehingga celah ini permanen. | Konfirmasi ke PO. Bila ongkir harus mengikuti berat: tambah kolom `weight_gram` pada `product_sizes`, hitung total berat dari isi keranjang, lalu teruskan ke `quote()`. Bila tidak: dokumentasikan sebagai batasan. | ❌ Perlu keputusan |
| P-02 | **Tinggi (scope)** | **Titik asal yang dipilih owner tidak mempengaruhi ongkir.** `CHIK-109` menambahkan `shipping_origins` yang bisa dipilih per pesanan, tetapi `ShippingCost::fetch()` selalu memakai `config('services.rajaongkir.origin')` (default **444**). Mengonfirmasi "Dapur Chikumi Bangkalan" atau "Outlet Chikumi Kota" menghasilkan ongkir identik. Selain itu `.env.example` menyebut `RAJAONGKIR_ORIGIN_ID=70205` sedangkan default config `444` — nilai berbeda, berisiko salah origin saat key diisi tanpa menyalin `.env.example`. | Simpan `rajaongkir_origin_id` pada tabel `shipping_origins` dan pakai origin tersebut saat menghitung ongkir (perlu hitung ulang saat owner mengganti titik asal). Samakan default config dengan `.env.example`. | ❌ Perlu keputusan |
| P-03 | **Rendah** | **Keranjang kosong tetap menampilkan Total Rp 5.000.** `renderRingkasan()` selalu menghitung `total = subtotal + ongkir`, sehingga saat `subtotal = 0` (keranjang kosong) bottom bar menampilkan "Total Rp 5.000" dan ringkasan "Total Bayar Rp 5.000". Tombol memang dinonaktifkan, tetapi angka total menyesatkan. | Tampilkan `Rp 0` bila keranjang kosong, atau sembunyikan ongkir saat tidak ada item. | ⚠️ Saran |
| P-04 | **Rendah** | **Semua tujuan berongkir sama saat API key kosong.** Karena daftar kota lokal mengembalikan `id = null`, `ShippingCost::quote()` langsung memakai tarif flat. Halaman tetap menampilkan "Area Arosbaya, Bangkalan, Jawa Timur · Ongkir Rp 5.000", yang terbaca seolah ongkir sudah dihitung per area padahal tidak. | Tandai "tarif dasar" (seperti di halaman alamat) juga di halaman checkout, atau sembunyikan pemilihan area saat mode flat. | ⚠️ Saran |
| P-05 | **Rendah** | **Label lokasi pickup masih hardcode "Pecinan".** `checkout.blade.php` menulis `'Ambil di gerai Pecinan · gratis'`, padahal owner sekarang bisa punya beberapa titik asal (Dapur Bangkalan, Outlet Kota). Label ini tidak mengikuti titik asal mana pun. | Ambil nama gerai dari titik asal aktif, atau ganti menjadi "Ambil di gerai · gratis". | ⚠️ Saran |
| P-06 | Rendah | **Field "Patokan" dan "Alamat utama" tidak terkirim & tidak ada di DB.** Form alamat punya `name="landmark"` dan `name="is_primary"`, tetapi payload hanya mengirim `label, recipient_name, phone, city, city_id, postal_code, detail`; `delivery_addresses` juga tidak punya kolom keduanya. Pengguna mengisi, lalu datanya hilang tanpa pesan. (Temuan sama dengan T-03/T-04 PPL-24, belum diperbaiki.) | Tambahkan kolom + validasi + kirim di payload, atau hapus field dari form. | ❌ Perlu perbaikan |
| P-07 | Info | **Belum ada UI owner untuk titik asal & kurir.** `CHIK-109` hanya menyediakan API (`/owner/api/titik-asal`, `/owner/api/kurir`, `/owner/api/pesanan/{id}/konfirmasi-pengiriman`). Tidak ada halaman di `resources/views/owner` yang memakainya, dan `owner/dashboard.blade.php` bahkan tidak punya `<meta name="csrf-token">`, sehingga JS di dashboard tidak bisa memanggil endpoint PATCH/POST. | Buat halaman/modal konfirmasi pengiriman, dan tambahkan meta CSRF ke layout owner. | ℹ️ Catatan |
| P-08 | Info | **Regresi lama:** `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items` (dari `CHIK-126`), dan `ExampleTest` gagal (`GET /` → 302). Keduanya di luar PPL-25. | Tambahkan tabel `orders`/`order_items` ke skema tes stok; perbaiki atau hapus `ExampleTest`. | ℹ️ Known issue |

## Catatan Lingkungan

- `.env` tidak berisi `RAJAONGKIR_API_KEY`; semua uji black-box berjalan pada **mode tarif flat Rp 5.000**. Perhitungan RajaOngkir diverifikasi dengan `Http::fake` mengikuti format Komerce v1.
- Data uji yang dibuat saat black-box: 1 alamat pelanggan (id 1, Arosbaya) dan 1 pesanan (id 78, `CHKM-261120194842-414`, kurir, Rp 35.000) yang sudah dikonfirmasi pengirimannya. Tidak ada data lain yang diubah.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`), MySQL untuk uji data nyata.

1. **Tes otomatis (SQLite in-memory, tanpa API/MySQL):**
   ```
   php artisan test --filter=OngkosKirimTest
   ```
   Diharapkan `32 passed, 97 assertions`.
2. **Uji manual mode flat:**
   ```
   php database/migrate.php && php database/seed.php && php artisan serve
   ```
   Login `customer@gmail.com`/`customer123` → **Alamat** → ketik "bangkalan" → pilih saran → simpan → buka **Checkout** → ganti Daintar/Ambil di tempat dan perhatikan ongkir & total.
3. **Uji dengan API asli:** isi `RAJAONGKIR_API_KEY` dan `RAJAONGKIR_ORIGIN_ID` di `.env`, `php artisan config:clear`, lalu ulangi langkah 2. Ongkir akan berubah dari `sumber=flat` menjadi `sumber=rajaongkir` dan `etd` terisi. Bandingkan angka dengan dashboard Komerce.
4. **Regresi:** `php artisan test` → 184/187 lulus; 2 kegagalan lama (P-08) dan 1 skipped dari tiket lain.
