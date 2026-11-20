# Laporan Testing — [PPL-16] Riwayat Transaksi Kasir

- **Backlog**: [PPL-16] Riwayat Transaksi Kasir — Jira `CHIK-47` (berisi `CHIK-107` pengerjaan halaman riwayat transaksi kasir); tiket testing `CHIK-108`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `4898fb5` — merge PR #36 (`CHIK-107`) ke `main`. Pengujian dilakukan di branch `CHIK-108-testing-riwayat-transaksi` dari `origin/main`.
- **Scope (mapping tim)**: `CHIK-107` — **frontend saja**: `resources/views/kasir/riwayat.blade.php`, route `GET /kasir/riwayat` + pratinjau `GET /frontend/kasir/riwayat` (`routes/web.php:68`, `:204`), dan link "Riwayat" di navigasi `kasir/dashboard.blade.php`.
- **Metode**:
  1. **PHPUnit** `tests/Feature/RiwayatTransaksiTest.php` — hak akses per role, render halaman, dan penanda kriteria data-dari-database (SQLite in-memory).
  2. **Black-box browser** — login `kasir@chikumi.com`, buka `/kasir/riwayat`, uji filter sumber, pencarian, pemilih periode, klik baris, dan tombol cetak ulang; pantau request jaringan.
  3. **Pembandingan data** — angka di halaman dibandingkan langsung dengan isi tabel `pos_transactions` / `orders` di database `chikumi` (hasil `migrate.php` + `seed.php`).
- **Terverifikasi**: 2026-11-21
- **Keterbatasan**: kartu Jira `CHIK-47`/`CHIK-108` tidak diakses dari mesin uji, sehingga kriteria penerimaan diturunkan dari judul work item. Tidak ada spesifikasi eksplisit apakah `CHIK-107` memang hanya diminta membuat tampilan (mockup) atau halaman fungsional — lihat bagian **Keputusan yang Dibutuhkan**.

## Ringkasan

| Jenis | Total | Passed | Skipped | Gagal | Pass Rate |
|---|---|---|---|---|---|
| PHPUnit — hak akses & render (`RiwayatTransaksiTest`) | 9 | 8 | 1 | 0 | 88,9% |
| Black-box browser — interaksi halaman | 9 | 2 | 0 | 7 | 22,2% |
| **Kriteria penerimaan (AC-1..AC-8)** | **8** | **1** | **0** | **7** | **12,5%** |

**Hasil: TIDAK LULUS.** Halaman `/kasir/riwayat` yang dikirim `CHIK-107` adalah **mockup statis**, bukan halaman riwayat yang terhubung data. Akses per role benar dan tampilan terbuka tanpa error, tetapi **tidak ada satu pun** fungsi riwayat yang bekerja: 7 baris transaksi ditulis tangan di Blade, tanpa JavaScript, tanpa endpoint `GET` riwayat, sehingga 215 transaksi kasir + 76 pesanan online di database tidak pernah tampil. Filter sumber hanya menyembunyikan baris lewat CSS, pencarian & pemilihan periode tidak melakukan apa pun, panel detail selalu menampilkan `#CK-0124`, dan tombol **Cetak Ulang Struk** tidak punya handler.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-47`/`CHIK-108` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog "Riwayat Transaksi Kasir" dan isi subtask `CHIK-107`. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Halaman riwayat hanya bisa dibuka kasir & owner; tamu diarahkan ke login, pelanggan ditolak | ✅ Lulus | AKS-01..AKS-05 |
| AC-2 | Halaman menampilkan transaksi nyata (kasir & online) dari database | ❌ Gagal | DAT-01..DAT-04 (R-01) |
| AC-3 | Filter sumber (Semua/Kasir/Online) menyaring daftar **dan** ringkasan | ❌ Gagal | UI-01..UI-03 (R-02, R-07) |
| AC-4 | Pencarian nomor pesanan menyaring daftar | ❌ Gagal | UI-04, UI-05 (R-03) |
| AC-5 | Pemilih periode (hari ini/kemarin/7 hari) mengubah rentang data | ❌ Gagal | UI-06 (R-04) |
| AC-6 | Memilih transaksi menampilkan detail transaksi tersebut | ❌ Gagal | UI-07 (R-05) |
| AC-7 | Tombol "Cetak Ulang Struk" membuka/mencetak struk transaksi terpilih | ❌ Gagal | UI-08 (R-06) |
| AC-8 | Ringkasan (jumlah transaksi, tunai, QRIS/VA, total) dihitung dari data terfilter | ❌ Gagal | DAT-05 (R-07) |

## Data Uji

Database lokal `chikumi` hasil `php database/migrate.php` + `php database/seed.php` (termasuk seeder riwayat 8 minggu `CHIK-125`):

| Sumber data | Jumlah | Rentang tanggal |
|---|---|---|
| `pos_transactions` (kasir) | 215 | 2026-09-27 s/d 2026-11-20 |
| `orders` (online) | 76 | — |
| `order_items` | 114 | — |

Contoh nomor transaksi nyata: `POS-SEED-261120-01` (kasir), `CHKM-SEED-261119-01` (online).

Akun uji: `kasir@chikumi.com` / `kasir123`, `owner@chikumi.com` / `owner123`, `customer@gmail.com` / `customer123`.

## Tabel Test Case

### A. Hak akses (PHPUnit + HTTP)

Lokasi: `tests/Feature/RiwayatTransaksiTest.php`. Hasil: **8 passed, 1 skipped, 28 assertions**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| AKS-01 | Tamu `GET /kasir/riwayat` | 302 → `/login` | ✅ Pass |
| AKS-02 | Kasir `GET /kasir/riwayat` | 200, judul "Riwayat Transaksi" tampil | ✅ Pass |
| AKS-03 | Owner `GET /kasir/riwayat` | 200 | ✅ Pass |
| AKS-04 | Pelanggan `GET /kasir/riwayat` | dialihkan ke `/` (bukan 403, sesuai middleware `role`) | ✅ Pass |
| AKS-05 | `GET /frontend/kasir/riwayat` tanpa login | 200 (route pratinjau publik) | ✅ Pass |
| AKS-06 | Transaksi dari database tampil di halaman | `POS-UJI-0001` muncul, `#CK-0130` hilang | ⏭️ **Skipped** — belum diimplementasikan (R-01) |

### B. Render halaman (PHPUnit + review statis)

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| RND-01 | Halaman memuat ringkasan, tanggal, dan baris contoh | "Sabtu, 31 Okt 2026", "Rp 612.000", 7 baris `#CK-…` | ✅ Pass (sesuai kondisi sekarang) |
| RND-02 | Halaman memuat JavaScript / handler klik | **Tidak ada** `<script>`, `onclick=`, `addEventListener` | ✅ Pass (mengonfirmasi R-01/R-06) |
| RND-03 | Navbar menandai menu "Riwayat" aktif | `bg-[#FF8000]` pada link Riwayat | ✅ Pass |
| RND-04 | Link "Riwayat" ada di dashboard kasir | `href="/kasir/riwayat"` | ✅ Pass |
| RND-05 | Tidak ada error console saat halaman dibuka | 0 error | ✅ Pass |

### C. Interaksi UI (browser, login kasir)

| ID | Skenario | Hasil Diharapkan | Hasil Nyata | Status |
|---|---|---|---|---|
| UI-01 | Pilih filter **Kasir** | hanya transaksi sumber kasir; ringkasan ikut menyesuaikan | 5 baris tampil, **ringkasan tetap 24 / Rp 612.000**, detail tetap `#CK-0124` | ❌ Gagal |
| UI-02 | Pilih filter **Online** | hanya transaksi online (3 transaksi/hari) | 2 baris tampil, ringkasan tidak berubah | ❌ Gagal |
| UI-03 | Kembali ke **Semua** | 7 baris lagi | 7 baris (CSS bekerja) | ✅ Pass (sebagian) |
| UI-04 | Cari `CK-0130` | hanya baris yang cocok | 7 baris tetap tampil | ❌ Gagal |
| UI-05 | Cari `zzzz` (tidak ada) | daftar kosong / pesan "tidak ditemukan" | 7 baris tetap tampil | ❌ Gagal |
| UI-06 | Ubah periode ke **7 hari terakhir** | data & tanggal berubah, ada request baru | nilai `<select>` berubah, **0 request**, tanggal tetap "31 Okt 2026" | ❌ Gagal |
| UI-07 | Klik baris `#CK-0130` | panel kanan menampilkan `#CK-0130` | panel tetap `#CK-0124`; kursor baris `auto`, tidak ada link/form | ❌ Gagal |
| UI-08 | Klik **Cetak Ulang Struk** | struk tercetak / terbuka | tidak ada navigasi, dialog, atau request (`onclick=null`) | ❌ Gagal |
| UI-09 | Pantau request jaringan selama semua interaksi | ada request ke endpoint riwayat | 8 request (semua aset awal), **0 request tambahan** | ✅ Pass (mengonfirmasi tidak ada API) |

### D. Konsistensi dengan database

Query langsung ke database `chikumi`:

| ID | Pembanding | Angka di halaman | Angka database | Status |
|---|---|---|---|---|
| DAT-01 | Jumlah transaksi | 24 | 215 (semua) / 9 (31 Okt 2026) | ❌ Tidak cocok |
| DAT-02 | Total omzet | Rp 612.000 | Rp 9.671.000 (semua) / Rp 363.000 (31 Okt) | ❌ Tidak cocok |
| DAT-03 | Format nomor pesanan | `#CK-0130` | `POS-SEED-…`, `CHKM-…` (0 baris mengandung `CK-`) | ❌ Tidak cocok |
| DAT-04 | Transaksi online 31 Okt | 3 baris online (`#CK-0126`, `#CK-0124`, dll.) | 3 pesanan (`orders`) — jumlah kebetulan sama, isi berbeda | ❌ Tidak cocok |
| DAT-05 | Konsistensi internal: tunai + QRIS/VA = total | 356.000 + 256.000 = 612.000 | konsisten secara aritmetika, tetapi tidak terhubung data | ⚠️ Hanya angka contoh |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| R-01 | **Blocker** | **Halaman 100% statis.** `#daftarTransaksi` berisi 7 baris `<tr>` hardcode (`#CK-0130`…`#CK-0122`), tanpa `<script>` sama sekali. Tidak ada endpoint `GET` riwayat: `routes/web.php` hanya punya `POST /kasir/api/transaksi` untuk *membuat* transaksi; tidak ada controller/model yang membaca `pos_transactions`/`orders` untuk riwayat. Akibatnya 215 transaksi kasir + 76 pesanan online di database tidak pernah tampil. | Buat endpoint riwayat (mis. `GET /kasir/api/riwayat?periode=&sumber=&cari=`), controller pembacaan `PosTransaction` + `Order`, lalu render tabel dari data (Blade loop atau fetch JS). | ❌ Perlu perbaikan |
| R-02 | **Tinggi** | **Filter sumber hanya kosmetik.** Radio `sumber` hanya memicu CSS `group-has-[#sumber-…:checked]:hidden` di `<section>`, sehingga baris disembunyikan tanpa menyaring data. Ringkasan (24 transaksi, Rp 356.000, Rp 256.000, Rp 612.000) dan panel detail **tidak ikut berubah** saat filter Kasir/Online dipilih. | Hitung & render ulang daftar + ringkasan + detail dari data terfilter (server-side atau JS), bukan CSS. | ❌ Perlu perbaikan |
| R-03 | **Tinggi** | **Pencarian tidak berfungsi.** `input[name=cari]` tidak punya listener; mengetik `CK-0130` maupun `zzzz` tidak mengubah daftar. | Tambahkan filter pencarian (nomor pesanan/nama pelanggan) dengan debounce, atau hapus input sampai siap. | ❌ Perlu perbaikan |
| R-04 | **Sedang** | **Pemilih periode tidak berfungsi.** `select[name=periode]` (Hari ini/Kemarin/7 hari) tanpa handler; subtitle tetap "Sabtu, 31 Okt 2026 · Shift 15.00–21.00" untuk semua pilihan. | Kirim parameter periode ke endpoint riwayat dan tampilkan tanggal/shift yang sesuai. | ❌ Perlu perbaikan |
| R-05 | **Sedang** | **Panel detail tidak mengikuti baris.** Klik baris tidak melakukan apa pun (tidak ada `<a>`/`<form>`/`onclick`, kursor `auto`). Panel kanan selalu `#CK-0124 · Budi · QRIS · Rp 54.000`, bahkan saat filter **Kasir** menyembunyikan baris `#CK-0124`. | Jadikan baris dapat diklik dan isi panel dari data transaksi terpilih; kosongkan/pilih baris pertama saat filter berubah. | ❌ Perlu perbaikan |
| R-06 | **Sedang** | **Tombol "Cetak Ulang Struk" mati.** `#btnCetakUlang` hanya elemen visual (`onclick=null`, tanpa listener); klik tidak membuka struk, dialog, atau request. | Sambungkan ke halaman/endpoint struk transaksi terpilih (manfaatkan `components/kasir/struk.blade.php` + `window.print()`). | ❌ Perlu perbaikan |
| R-07 | **Sedang** | **Ringkasan tidak konsisten dengan tabel.** Kartu menyebut **24 transaksi** padahal tabel hanya punya **7 baris**, dan angkanya tidak berasal dari database (31 Okt 2026: 9 transaksi / Rp 363.000; seluruh data: 215 transaksi / Rp 9.671.000). | Hitung ringkasan dari dataset yang sama dengan tabel. | ❌ Perlu perbaikan |
| R-08 | Rendah | **Format nomor tidak sesuai data nyata.** Halaman memakai `#CK-0130`, sedangkan database memakai `POS-SEED-…` (kasir) dan `CHKM-…` (online); tidak ada satu pun `transaction_number` yang mengandung pola `CK-`. | Samakan format penomoran (atau tampilkan `transaction_number`/`order_number` apa adanya). | ⚠️ Saran |
| R-09 | Rendah (layout) | **Tidak ada layout responsif.** 0 kelas `sm:`/`md:`/`lg:`; `<main class="flex">` dengan `aside w-[380px]` tetap dan tabel 7 kolom tanpa `overflow-x`/`whitespace-nowrap`. Di lebar ~1280px header tabel sudah berdempetan ("WAKTUSUMBER", "METODETOTAL"), dan di layar kecil tabel akan meluber keluar layar. `<th>` juga tanpa `scope`. | Tambahkan breakpoint (sidebar jadi drawer/stack di bawah `lg`), bungkus tabel dengan `overflow-x-auto`, beri padding antar kolom dan `scope="col"`. | ⚠️ Saran |
| R-10 | Rendah (akses) | **Pelanggan yang membuka halaman tidak mendapat pesan.** Middleware `role` mengalihkan ke `/` dengan `flash('error')`, tetapi tidak ada view yang merender `session('error')` (pencarian di `resources/views` = 0 hasil) — pengguna hanya terlempar ke beranda tanpa penjelasan. Berlaku aplikasi-wide, bukan khusus riwayat. | Tampilkan flash `error` di layout/dashboard, atau kembalikan 403 untuk halaman yang jelas bukan haknya. | ⚠️ Saran |
| R-11 | Info | **Route pratinjau publik** `GET /frontend/kasir/riwayat` (tanpa login/role) menampilkan halaman yang sama, termasuk data contoh. Konsisten dengan route dev lain, tetapi ikut membuka tampilan riwayat ke siapa pun. | Batasi/hapus saat produksi, atau beri penanda "mode pratinjau". | ℹ️ Catatan |
| R-12 | Info (scope) | `CHIK-107` hanya menyentuh **1 view + 2 route + 1 link navigasi**. Tidak ada controller, model, migrasi, atau API baru — konsisten dengan temuan bahwa ini mockup, bukan fitur fungsional. | Konfirmasi ke PO apakah iterasi berikutnya (backend riwayat) sudah dijadwalkan. | ℹ️ Catatan |

## Keputusan yang Dibutuhkan

`CHIK-107` menghasilkan tampilan yang rapi dan sesuai desain, tetapi seluruh perilaku "riwayat" belum ada. Sebelum tiket ditutup, PO perlu memilih salah satu:

1. **Mockup diterima sebagai deliverable terpisah** → buat tiket baru untuk backend + wiring riwayat (endpoint `GET`, filter, pencarian, detail, cetak ulang) dan ubah AC `CHIK-107` agar sesuai (hanya tampilan).
2. **`CHIK-107` dianggap belum selesai** → kembalikan ke pengembang untuk menyambungkan halaman ke data sebelum ditutup, lalu ulangi `CHIK-108`.

Tanpa keputusan ini, halaman berisiko dianggap "selesai" padahal kasir tidak bisa melihat satu pun transaksi sungguhan.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`).

1. **Tes otomatis akses & render (SQLite in-memory, tanpa MySQL):**
   ```
   php artisan test --filter=RiwayatTransaksiTest
   ```
   Diharapkan `8 passed, 1 skipped, 28 assertions`. Tes `AKS-06` sengaja di-skip sampai halaman terhubung database.
2. **Uji manual:**
   ```
   php database/migrate.php
   php database/seed.php
   php artisan serve
   ```
   Login `kasir@chikumi.com` / `kasir123` → menu **Riwayat** → coba filter Kasir/Online, ketik di kolom cari, ganti periode, klik baris, dan klik **Cetak Ulang Struk**. Semua tidak mengubah data pada kondisi `4898fb5`.
3. **Bandingkan dengan database** (opsional):
   ```
   mysql -u root -p chikumi -e "SELECT COUNT(*), SUM(total_amount) FROM pos_transactions;"
   ```
   Hasil `215` dan `9671000.00`, berbeda dari angka `24` / `Rp 612.000` di halaman.
4. Setelah backend riwayat tersedia, hapus `markTestSkipped()` pada `test_riwayat_menampilkan_transaksi_dari_database` dan jalankan ulang.
