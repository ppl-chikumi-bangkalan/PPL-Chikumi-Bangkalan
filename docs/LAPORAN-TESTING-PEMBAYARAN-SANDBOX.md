# Laporan Testing — [PPL-26] Pembayaran Online (Payment Gateway)

- **Backlog**: [PPL-26] Pembayaran Online (Payment Gateway) — Jira `CHIK-49` (berisi `CHIK-113` integrasi API pembayaran sandbox, `CHIK-114` webhook status Diproses + pemicu pengurangan stok, `CHIK-115` frontend halaman pembayaran); tiket testing `CHIK-116`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `5feceb7` (PR #44 `CHIK-113`, #46 `CHIK-114`, #39 `CHIK-115` sudah merge). Pengujian dilakukan di branch `CHIK-116-testing-pembayaran-sandbox` dari `origin/main`.
- **Gateway**: Duitku — sandbox `https://sandbox.duitku.com`
- **Scope (mapping tim)**:
  - `CHIK-113` — **backend**: `Customer\PaymentController`, `Support\Duitku` (inquiry QRIS/VA, cek status, callback, QR SVG), `config/services.php`, endpoint `/pesanan/{id}/pembayaran|bayar|status-pembayaran|qr`, `/payment/callback`.
  - `CHIK-114` — **webhook**: verifikasi signature callback, validasi nominal, status `processing` + `paid_at`, idempotensi, pembatalan + balik stok saat `statusCode 02`.
  - `CHIK-115` — **frontend**: `resources/views/customer/pembayaran.blade.php` (total, QRIS/VA, hitung mundur, status, polling 15 detik, panel bukti Duitku).
- **Metode**:
  1. **PHPUnit** `tests/Feature/PembayaranSandboxTest.php` — respons Duitku dipalsukan dengan `Http::fake` (payload mengikuti dokumentasi Duitku), SQLite in-memory. Menguji inquiry, callback, status polling, signature, nominal, idempotensi, kepemilikan, QR, dan mode simulasi.
  2. **Node.js** `tests/uji-pembayaran-sandbox.mjs` — replikasi 1:1 fungsi tampilan `renderStatus`, `render`, `renderQris`, `renderVa`, `renderBukti`, `rupiah`, hitung mundur.
  3. **Black-box lokal** — `php artisan serve` + database MySQL hasil `migrate.php`/`seed.php`, login pelanggan, checkout QRIS & VA, buka halaman pembayaran di browser (mode simulasi karena kredensial Duitku belum diisi).
- **Terverifikasi**: 2026-11-21
- **Keterbatasan**: kredensial sandbox (`DUITKU_MERCHANT_CODE`, `DUITKU_API_KEY`) tidak tersedia di `.env`, sehingga **belum ada transaksi nyata ke Duitku**. Seluruh cabang sukses/gagal/kedaluwarsa diuji dengan respons API yang identik dengan dokumentasi Duitku. Verifikasi end-to-end ke sandbox asli menunggu kredensial (lihat bagian akhir).

## Ringkasan

| Jenis | Total | Passed | Failed | Pass Rate |
|---|---|---|---|---|
| PHPUnit — alur pembayaran Duitku (`PembayaranSandboxTest`) | 21 | 21 | 0 | 100% |
| Node.js — logika tampilan halaman pembayaran (`CHIK-115`) | 28 | 28 | 0 | 100% |
| PHPUnit — seluruh suite proyek (regresi) | 118 | 116 | 2 | 98,3% |
| **Total pengujian PPL-26** | **49** | **49** | **0** | **100%** |

Alur pembayaran berfungsi sesuai kontrak: inquiry QRIS/VA tersimpan sebagai `snap_token`, callback hanya mengubah status bila signature & nominal valid, callback sukses → `processing` + `paid_at` (idempoten), `statusCode 02` → `cancelled` + stok dikembalikan, dan halaman pembayaran menampilkan total/QR/VA/status. Dua kegagalan suite penuh berasal dari tes lama di luar PPL-26 (lihat T-07/T-08). Tiga catatan perilaku perlu keputusan tim: hitung mundur kedaluwarsa, callback `resultCode=01`, dan titik pemotongan stok (T-01..T-03).

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-49`/`CHIK-116` tidak diakses dari mesin uji; kriteria diturunkan dari judul work item dan dokumentasi `docs/API-PEMBAYARAN.md`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Dibuktikan oleh |
|---|---|---|
| AC-1 | Integrasi API pembayaran sandbox: buat transaksi QRIS & Virtual Account | INQ-01..INQ-06 |
| AC-2 | Webhook/callback sukses memindahkan pesanan ke status Diproses (`processing`) dan mengisi `paid_at` | CB-01, CB-02, ST-01 |
| AC-3 | Pembayaran gagal/kedaluwarsa membatalkan pesanan dan mengembalikan stok | ST-03, CB-04 |
| AC-4 | Callback hanya dipercaya bila signature HMAC valid dan nominal sama dengan total pesanan | CB-02, CB-03, CB-05, CB-06 |
| AC-5 | Halaman pembayaran menampilkan total, metode, QR/VA dari Duitku, status, hitung mundur, dan memperbarui status | UI-01..UI-12, uji browser |
| AC-6 | Pengurangan stok dipicu webhook/status Diproses | **Tidak sesuai** — stok dipotong saat checkout (T-03) |

## Data Uji

| Item | Tipe | Harga |
|---|---|---|
| Reguler (stok awal 20) | Ukuran | Rp 15.000 |
| Large (stok 3) | Ukuran | Rp 22.000 |
| Mozzarella Cheese | filling | +Rp 3.000 |
| Barbeque Sauce | dipping_sauce | +Rp 2.000 |
| Pesanan uji | Total | Rp 30.000 (Reguler ×2) / Rp 15.000 (Reguler ×1) |

Pelanggan uji: `pelanggan.uji@gmail.com` (PHPUnit) dan `customer@gmail.com` / `customer123` (MySQL lokal).

## Tabel Test Case

### A. Backend — Inquiry Duitku (`CHIK-113`)

Lokasi: `tests/Feature/PembayaranSandboxTest.php`. Hasil: **21 passed, 81 assertions**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| INQ-01 | QRIS: inquiry `statusCode 00` | 200; `sumber=duitku`, `reference`, `qr_url`, `metode_kode=SP`; `snap_token` tersimpan | ✅ Pass |
| INQ-02 | VA: inquiry mengembalikan `vaNumber` | 200; `bank=BCA`, `va_number`, `metode_kode=BC` | ✅ Pass |
| INQ-03 | Signature & payload inquiry | `HMAC(merchantCode+orderId+amount)`, method & nominal benar | ✅ Pass |
| INQ-04 | Gateway menolak (`statusCode 01`) | 502; `snap_token` tetap null; status `unpaid` | ✅ Pass |
| INQ-05 | Bayar pesanan yang sudah `processing` | 422 | ✅ Pass |
| INQ-06 | Pesanan belum punya metode, pilih VA saat bayar | `payment_type=va`; inquiry memakai kode `BC` | ✅ Pass |
| INQ-07 | Bayar ulang metode sama (transaksi sudah ada) | Tidak memanggil Duitku lagi; referensi lama dipertahankan | ✅ Pass |

### B. Backend — Callback (`CHIK-114`)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| CB-01 | Callback `resultCode 00`, signature valid, nominal cocok | HTTP 200 `OK`; status `processing`; `paid_at` terisi; stok tidak berubah lagi | ✅ Pass |
| CB-02 | Callback sukses dikirim dua kali | 200; status & `paid_at` tidak berubah (idempoten) | ✅ Pass |
| CB-03 | Signature salah | 403; status tetap `unpaid` | ✅ Pass |
| CB-04 | `resultCode 01` (gagal) | **Sesuai docs Duitku**: transaksi gagal ditangani | ❌ Gagal — status tetap `unpaid`, stok tetap terpotong (T-02) |
| CB-05 | Nominal callback ≠ `grand_total` | 400; status tetap `unpaid` | ✅ Pass |
| CB-06 | `merchantOrderId` tidak dikenal | 404 | ✅ Pass |
| CB-07 | Callback tanpa API key (gateway belum dikonfigurasi) | 403 | ✅ Pass |

### C. Backend — Cek status / kedaluwarsa

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| ST-01 | `statusCode 00` | `processing`; `paid_at` terisi; label "Berhasil dibayar" | ✅ Pass |
| ST-02 | `statusCode 01` (pending) | Tetap `unpaid`; label "Menunggu pembayaran" | ✅ Pass |
| ST-03 | `statusCode 02` (gagal/kedaluwarsa) | `cancelled`; stok kembali (+2); `stock_histories` `type=in`, `source=online_order`, catatan pembatalan otomatis | ✅ Pass |
| ST-04 | Cek status berulang setelah batal | Stok tidak bertambah dua kali; hanya 1 riwayat stok masuk | ✅ Pass |
| ST-05 | Dua polling dalam <30 detik | Hanya 1 panggilan ke Duitku (throttle 30 detik) | ✅ Pass |

### D. Backend — Mode simulasi, kepemilikan, QR

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| SIM-01 | Tanpa merchant code/API key | `sumber=simulasi`, VA palsu `8808…`, tidak ada HTTP ke Duitku, status tetap `unpaid` | ✅ Pass |
| SIM-02 | Pesanan milik pelanggan lain | 403 pada `GET pembayaran` dan `POST bayar` | ✅ Pass |
| SIM-03 | Route dev `/frontend/customer/api/...` | 200 dengan header `X-Dev-Customer`; 403 tanpa header | ✅ Pass |
| SIM-04 | `GET .../qr` setelah inquiry | 200 `image/svg+xml`, isi `<svg` dari `qrString` Duitku | ✅ Pass |
| SIM-05 | `GET .../qr` sebelum ada transaksi | 404 | ✅ Pass |

### E. Frontend — `CHIK-115` (Node.js + browser)

Node: **28 passed, 0 failed**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| UI-01 | Format rupiah 30.000 / 1.234.567 | `Rp 30.000` / `Rp 1.234.567` | ✅ Pass |
| UI-02 | Status `unpaid` | "Menunggu pembayaran…" + spinner tampil | ✅ Pass |
| UI-03 | Status `processing`/`ready`/`completed` | Pesan berhasil sesuai tahap; spinner hilang | ✅ Pass |
| UI-04 | Status `cancelled` | "Pembayaran gagal / kedaluwarsa…", warna merah | ✅ Pass |
| UI-05 | Metode QRIS vs VA | Label & panel yang tampil berganti benar | ✅ Pass |
| UI-06 | `qr_url` tersedia | Gambar QR tampil, placeholder hilang | ✅ Pass |
| UI-07 | `qr_url` kosong | Placeholder "QR belum tersedia…" | ✅ Pass |
| UI-08 | Data VA | Bank + nomor VA tampil; kosong → "Menunggu transaksi…" | ✅ Pass |
| UI-09 | Panel bukti Duitku | Tampil hanya bila `sumber=duitku`; merchant/referensi/metode benar | ✅ Pass |
| UI-10 | Tombol "Perbarui status" | Tampil saat `unpaid`, hilang saat sudah final | ✅ Pass |
| UI-11 | Hitung mundur | `60:00`, `09:41`, lewat batas → `00:00` | ✅ Pass (format); lihat T-01 |
| UI-12 | Browser: halaman VA & QRIS | Total, metode, status, catatan simulasi tampil; tanpa error console | ✅ Pass |

Bukti browser (mode simulasi):
- Pesanan `CHKM-261120174632-301` (VA, Rp 30.000): tampil "BCA (simulasi) Virtual Account", nomor `880800000075`, status "Menunggu pembayaran…".
- Pesanan `CHKM-261120174715-536` (QRIS, Rp 15.000): panel QRIS aktif dengan placeholder QR; status "Menunggu pembayaran…".
- Halaman login pelanggan (`/customer/pembayaran`) dan route dev (`/frontend/customer/pembayaran`) sama-sama berfungsi, tanpa error console.
- `POST /payment/callback` tanpa API key → 403 (sesuai desain).

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| T-01 | Sedang | **Hitung mundur kedaluwarsa selalu `00:00`** di browser WIB. `PaymentController::detail` mengirim `kedaluwarsa` sebagai `Y-m-d H:i:s` tanpa zona (server UTC), lalu JS mem-parse sebagai waktu lokal → sudah lewat. Selain itu nilai dihitung ulang `now()+60 menit` pada setiap render/polling, sehingga countdown tidak pernah benar-benar berkurang. | Simpan waktu kedaluwarsa saat inquiry (cache/DB) dan kirim ISO-8601 dengan offset (`toIso8601String()`), atau set `APP_TIMEZONE=Asia/Jakarta`; jangan hitung ulang tiap request. | ❌ Perlu perbaikan |
| T-02 | Sedang | **Callback `resultCode=01` (gagal) diabaikan.** `PaymentController::callback` hanya memetakan `00` → `paid`; `01` tidak membatalkan pesanan. Sesuai docs Duitku `01 = Failed`; akibatnya pesanan tetap `unpaid` dan stok tetap terpotong sampai ada polling `statusCode 02` atau pembatalan manual. | Petakan `01` ke pembatalan (dan balik stok) seperti `statusCode 02`, atau dokumentasikan bahwa kegagalan hanya ditangani lewat cek status. | ❌ Perlu perbaikan |
| T-03 | Sedang (scope) | **Judul `CHIK-114` menyebut webhook memicu pengurangan stok, tetapi implementasi memotong stok saat checkout.** Webhook/callback hanya memindahkan status ke `processing`. `docs/API-PEMBAYARAN.md` memang menyatakan stok dipotong saat checkout; namun bila AC mengikuti judul tiket, ada ketidaksesuaian. | Konfirmasi ke PO: pertahankan potong stok saat checkout (dan perbaiki judul/AC), atau pindahkan pengurangan stok ke saat pembayaran sukses. | ⚠️ Perlu konfirmasi |
| T-04 | Rendah | **Mode simulasi tidak menyediakan QRIS yang bisa dipakai.** Saat kredensial kosong, halaman QRIS hanya menampilkan placeholder "QR belum tersedia"; data simulasi selalu berupa VA palsu. Uji lokal QRIS tidak bisa diselesaikan tanpa kredensial. | Tampilkan QR contoh/VA palsu yang konsisten untuk kedua metode, atau arahkan pemilihan VA saat mode simulasi. | ⚠️ Saran |
| T-05 | Rendah | **"Simpan QR" mengunduh SVG dengan nama `.png`** (`qris-*.png`), padahal endpoint mengirim `image/svg+xml`. Berkas hasil unduhan tidak cocok dengan ekstensinya. | Simpan sebagai `.svg` atau render QR ke PNG di server. | ⚠️ Saran |
| T-06 | Info | **Ganti metode setelah checkout tidak bisa dilakukan lewat API.** `charge` hanya menerima `payment_method` bila `payment_type` masih null; setelah terisi, parameter diabaikan. Cabang kode "metode diganti sebelum dibayar" praktis tidak terjangkau dari UI/API. | Bila ganti metode memang fitur, tambahkan endpoint/validasi; bila tidak, hapus cabang agar tidak menyesatkan. | ℹ️ Catatan |
| T-07 | Rendah (di luar scope) | **Regresi suite penuh:** `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`. Halaman `/owner/stok` sekarang memanggil `MovingAverage` (CHIK-126), tetapi skema tes stok belum membuat tabel `orders`/`order_items`. | Tambahkan tabel `orders` + `order_items` ke skema `PenambahanStokTest`, atau lewati perhitungan moving average saat tabel belum ada. | ❌ Perlu perbaikan |
| T-08 | Info | `ExampleTest::test_the_application_returns_a_successful_response` masih gagal (`GET /` → 302), isu lama di luar PPL-26. | Ubah menjadi `assertRedirect('/login')` atau hapus. | ℹ️ Known issue |

## Cakupan yang Belum Teruji (butuh kredensial sandbox)

Belum dijalankan karena `.env` tidak berisi `DUITKU_MERCHANT_CODE`/`DUITKU_API_KEY`:

1. Inquiry nyata ke `sandbox.duitku.com` (termasuk channel `BC`/`SP` aktif di project).
2. Pembayaran nyata di halaman Duitku (VA demo / QRIS staging).
3. Callback nyata dari server Duitku (butuh URL publik, mis. ngrok).
4. Kedaluwarsa nyata sesuai `expiryPeriod` (60 menit).

Setelah kredensial tersedia: isi `.env`, jalankan `php artisan config:clear`, ulangi langkah di `docs/API-PEMBAYARAN.md` (checkout → halaman pembayaran → bayar di sandbox → cek status/callback), lalu cocokkan `reference` di dashboard Duitku.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Node.js 20+, Composer. Di mesin uji Composer tidak ada di PATH, gunakan `C:\laragon\bin\composer\composer.bat`.

1. **Pasang dependensi** (butuh `bacon/bacon-qr-code` untuk endpoint QR):
   ```
   C:\laragon\bin\composer\composer.bat install
   ```
2. **Tes otomatis pembayaran (SQLite in-memory, tanpa gateway/MySQL):**
   ```
   php artisan test --filter=PembayaranSandboxTest
   ```
   Diharapkan `21 passed, 81 assertions`.
3. **Verifikasi logika tampilan (tanpa build):**
   ```
   node tests/uji-pembayaran-sandbox.mjs
   ```
   Diharapkan `28 passed, 0 failed`.
4. **Suite penuh (regresi):**
   ```
   php artisan test
   ```
   Diharapkan `116/118 passed`; 2 kegagalan lama di luar PPL-26 (T-07, T-08).
5. **Uji manual mode simulasi:**
   ```
   php database/migrate.php   # butuh database chikumi; lihat catatan password di database/db.php
   php database/seed.php
   php artisan serve
   ```
   Login `customer@gmail.com` / `customer123` → tambah produk ke keranjang → checkout pilih QRIS/VA → buka `/customer/pembayaran?pesanan={id}`. Tanpa kredensial, halaman menampilkan data simulasi (VA palsu) dan status tetap "Menunggu pembayaran…".
6. **Uji sandbox asli:** isi `DUITKU_MERCHANT_CODE`, `DUITKU_API_KEY`, `DUITKU_IS_PRODUCTION=false` di `.env`, `php artisan config:clear`, lalu ulangi langkah 5 dan selesaikan pembayaran di halaman Duitku; status berubah ke `processing` lewat tombol **Perbarui status dari Duitku** atau callback publik.
