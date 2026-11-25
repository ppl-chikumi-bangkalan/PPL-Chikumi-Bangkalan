# Laporan Testing — [PPL-02] Rekap Pendapatan per Periode

- **Backlog**: [PPL-02] Rekap Pendapatan per Periode — Jira `CHIK-58` (berisi `CHIK-136` query rekap pendapatan harian/mingguan/bulanan, `CHIK-137` frontend rekap pendapatan); tiket testing `CHIK-138`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `583b377`. Kedua subtask sudah merge: `CHIK-136` → PR #61 (`289d4b0`, commit `306b78b`), `CHIK-137` → PR #56 (`acc7338`; commit `def2fa1` halaman laporan, `bbfcc8c` menu sidebar, `cbaccb1` gabung main). Pengujian di branch `CHIK-138-testing-rekap-pendapatan`.
- **Scope (mapping tim)**:
  - `CHIK-136` — **backend**: `app/Http/Controllers/Owner/ReportController.php` (`pendapatan()`, `exportPendapatan()`, `cetak()`, `hitungPendapatan()`, `omzetHarian()`, `pengeluaranHarian()`, `perHari()`, `perMinggu()`, `perBulan()`), 4 route di `routes/web.php`, `docs/API-LAPORAN.md`, dan JavaScript pengisi tiga tabel di `resources/views/owner/laporan.blade.php`.
  - `CHIK-137` — **frontend**: `resources/views/owner/laporan.blade.php` (filter "Rekap per", pilihan "Periode", tiga tabel rekap, tombol Export), menu "Laporan" di `resources/views/components/owner/sidebar.blade.php`, route `/owner/laporan`.
  - **Sudah ada di `main` dari backlog lain** dan ikut diuji karena tampil di halaman yang sama: `CHIK-139` (PR #62, `fe7bbb1` — sambungan tiga tabel ke API, tombol Export Excel/PDF, halaman cetak, kartu laba rugi) dan `CHIK-140` (PR #57, `a0a4c50`).
- **Metode**:
  1. **PHPUnit** `tests/Feature/RekapPendapatanTest.php` — 31 tes; setiap omzet/pengeluaran/laba **dicocokkan dengan hitungan manual** per tanggal (SQLite in-memory).
  2. **Black-box API + MySQL** — 22 skenario dengan sesi owner; angka API dibandingkan dengan agregat SQL langsung di database `chikumi`, termasuk penyisipan data sementara di batas minggu (lalu dibersihkan).
  3. **Black-box browser** — halaman `/owner/laporan`: ganti mode rekap, ganti periode, cek kartu laba rugi, unduh Export Excel, klik Export PDF, pantau console & jaringan.
- **Terverifikasi**: 2026-11-25
- **Keterbatasan**: kartu Jira `CHIK-58`/`CHIK-138` tidak diakses dari mesin uji; kriteria penerimaan diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.** Pratinjau tanpa login diuji lewat HTTP tanpa sesi (browser uji masih login sebagai owner, sehingga halaman pratinjau memakai API owner).

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — rekap harian/mingguan/bulanan, total, export, hak akses (`RekapPendapatanTest`) | 31 | 31 | 0 | 100% |
| Black-box API + MySQL | 22 | 22 | 0 | 100% |
| Black-box browser | 11 | 10 | 1 | 90,9% |
| **Kriteria penerimaan (AC-1..AC-9)** | **9** | **8** | **1 sebagian** | **88,9%** |
| Regresi suite penuh | 253 | 251 | 2 (lama) | 99,2% |

**Hasil: LULUS DENGAN CATATAN.**

Rekap pendapatan **akurat dan konsisten di ketiga mode**. Untuk November 2026: 108 transaksi, omzet **Rp 4.420.000** (kasir Rp 3.413.000 + online Rp 1.007.000), pengeluaran **Rp 196.500,75**, laba **Rp 4.223.499,25**. Angka itu identik di rekap harian, mingguan, bulanan (baris November), kartu dashboard, buku transaksi owner, dan kartu laba rugi. Total ketiga mode juga sama, dan cocok dengan agregat SQL langsung: POS 83 baris Rp 3.413.000, pesanan online 25 baris Rp 1.007.000, pengeluaran 4 baris Rp 196.500,75. Pengujian batas minggu dengan data sementara (21 Nov 23.59.59 → minggu 15–21; 22 Nov 00.00.00 → minggu 22–28; transaksi `cancelled` diabaikan; pengeluaran 29 Nov → minggu 29–30) semuanya benar, lalu data dibersihkan.

Catatan penting: **(1) tombol "Export PDF" rusak** — URL yang dibuka adalah `"/owner"/laporan/cetak?...` (tanda kutip ikut masuk) sehingga selalu **404 Not Found**; Export Excel/CSV berfungsi; **(2) pratinjau publik** `/frontend/owner/laporan` membuka seluruh omzet, pengeluaran, dan laba tanpa login; **(3) pilihan periode hanya 3 bulan terakhir**, sehingga laporan lebih lama tidak bisa dibuka dari UI meski API mendukung bulan apa pun.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-58`/`CHIK-138` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | Tersedia rekap pendapatan **harian** | ✅ Lulus | U-08..U-14, API-01, API-09, UI-02 |
| AC-2 | Tersedia rekap pendapatan **mingguan** | ✅ Lulus | U-15..U-19, API-02, API-08, UI-01 |
| AC-3 | Tersedia rekap pendapatan **bulanan** (bulan terpilih + 2 bulan sebelumnya) | ✅ Lulus | U-20..U-22, API-03, UI-03 |
| AC-4 | Data berasal dari transaksi nyata (kasir + pesanan online) dan pengeluaran, bukan angka contoh | ✅ Lulus | API-01..API-07, UI-01 |
| AC-5 | Laba = omzet − pengeluaran; total = jumlah baris | ✅ Lulus | U-10, U-23..U-24, API-01..API-03 |
| AC-6 | Owner dapat memilih periode dan angka ikut berubah | ✅ Lulus | U-07, API-21, UI-05 |
| AC-7 | Total periode ditampilkan | ✅ Lulus | U-23..U-24, UI-01..UI-03 |
| AC-8 | Angka konsisten dengan dashboard, buku transaksi, dan laba rugi | ✅ Lulus | U-25..U-26, API-04..API-06 |
| AC-9 | Laporan dapat diekspor | ⚠️ Sebagian — Excel/CSV berfungsi, **PDF 404** (P-01) | U-27..U-29, API-11..API-13, UI-07..UI-09 |

## Data Uji

**Dataset PHPUnit (SQLite in-memory)**: 1 owner, 1 kasir, 1 pelanggan; tabel `expenses`, `orders`, `order_items`; waktu uji dipatok `2026-03-15` agar default periode deterministik.

**Data nyata** (database `chikumi`): 216 transaksi kasir, 78 pesanan online, 4 pengeluaran.

| Item | Nilai |
|---|---|
| Transaksi November 2026 | 108 (kasir 83, online 25) |
| Omzet November | **Rp 4.420.000** (kasir Rp 3.413.000 + online Rp 1.007.000) |
| Pengeluaran November | **Rp 196.500,75** (4 baris) |
| Laba November | **Rp 4.223.499,25** |
| Rekap Oktober | 161 transaksi, omzet Rp 8.030.500, pengeluaran Rp 0 |
| Rekap September | 14 transaksi, omzet Rp 663.000, pengeluaran Rp 0 |
| Total 3 bulan (Sep–Nov) | 283 transaksi, omzet Rp 13.113.500, pengeluaran Rp 196.500,75, laba Rp 12.916.999,25 |
| Rincian mingguan November | 1–7: 35 / Rp 1.360.500 · 8–14: 42 / Rp 1.764.000 · 15–21: 31 / Rp 1.295.500 · 22–28: 0 / Rp 0 (pengeluaran Rp 196.500,75) · 29–30: 0 |
| Data sementara uji batas | POS Rp 111.000 (21 Nov 23.59.59), Rp 222.000 (22 Nov 00.00.00), Rp 333.000 `cancelled` (23 Nov), pengeluaran Rp 444.000 (29 Nov) — **dihapus setelah uji** |

Akun uji: `owner@chikumi.com`/`owner123`, `kasir@chikumi.com`/`kasir123`, `customer@gmail.com`/`customer123`.

## Tabel Test Case

### A. PHPUnit (`tests/Feature/RekapPendapatanTest.php`)

Hasil: **31 passed, 149 assertions**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U-01 | Tamu membuka rekap, halaman laporan, dan halaman cetak | 302 ke `/login` | ✅ Pass |
| U-02 | Kasir & pelanggan mengakses endpoint/halaman | JSON 403; halaman dialihkan | ✅ Pass |
| U-03 | Owner membuka rekap & halaman laporan | 200, default `mingguan`, periode bulan berjalan | ✅ Pass |
| U-04 | Tiga mode rekap | `harian`, `mingguan`, `bulanan` diterima | ✅ Pass |
| U-05 | `rekap=triwulan` | 422 pada `rekap` | ✅ Pass |
| U-06 | `periode` salah (`2026-13`, `2026-00`, `2026-3`, `Maret 2026`, `2026-03-01`) | 422 pada `periode` | ✅ Pass |
| U-07 | Tanpa parameter | Periode `2026-03`, 5 baris mingguan | ✅ Pass |
| U-08 | Rekap harian Maret 2026 | 31 baris, label `1 Mar`..`31 Mar` | ✅ Pass |
| U-09 | Kasir + pesanan online di tanggal sama | 1 baris berisi 2 transaksi, omzet dijumlahkan | ✅ Pass |
| U-10 | Pengeluaran > omzet | `pengeluaran` terisi, `laba` negatif | ✅ Pass |
| U-11 | Tanggal tanpa transaksi | Semua nilai 0 | ✅ Pass |
| U-12 | POS `pending`/`cancelled`, pesanan belum bayar/batal | Tidak dihitung | ✅ Pass |
| U-13 | Transaksi 1 Mar 00.00, 31 Mar 23.59.59, 28 Feb, 1 Apr | Hanya dua yang pertama masuk; total 3.000 | ✅ Pass |
| U-14 | Pesanan dibuat 27 Feb, dibayar 1 Mar | Masuk 27 Feb (mengikuti `created_at`) | ⚠️ Perilaku (P-04) |
| U-15 | Rekap mingguan bulan 31 hari | 5 baris `1–7`..`29–31 Mar` | ✅ Pass |
| U-16 | Transaksi di 7, 8, 14, 15, 31 | Minggu 1=100, 2=500, 3=400, 4=0, 5=500 | ✅ Pass |
| U-17 | Februari 2026 (28 hari) | 4 baris, terakhir `22–28 Feb` | ✅ Pass |
| U-18 | April 2026 (30 hari) | 5 baris, terakhir `29–30 Apr` | ✅ Pass |
| U-19 | Februari 2028 (29 hari, kabisat) | 5 baris, terakhir `29 Feb` (label satu tanggal) | ✅ Pass |
| U-20 | Rekap bulanan Maret 2026 | 3 baris `Jan 2026`, `Feb 2026`, `Mar 2026` | ✅ Pass |
| U-21 | Rekap bulanan Januari 2026 | Melintasi tahun: `Nov 2025`, `Des 2025`, `Jan 2026` | ✅ Pass |
| U-22 | Omzet/pengeluaran tiap bulan | Jan 500; Feb 700−200=500; Mar 900; total 2.100/200/1.900 | ✅ Pass |
| U-23 | Total vs jumlah baris di tiga mode | Selalu sama | ✅ Pass |
| U-24 | Total harian vs mingguan | Identik | ✅ Pass |
| U-25 | Rekap bulan berjalan vs `/owner/api/dashboard` | Omzet, pengeluaran, laba, transaksi sama | ✅ Pass |
| U-26 | Laba rugi: HPP, operasional, karyawan, lainnya | Laba kotor 850.000; laba bersih 585.000; sama dengan total rekap | ✅ Pass |
| U-27 | Export CSV mingguan | 200, `text/csv`, nama file, header, baris, Total | ✅ Pass |
| U-28 | Export harian Februari | Hanya tanggal Februari, tanpa Maret | ✅ Pass |
| U-29 | Export tanpa login | 302 ke `/login` | ✅ Pass |
| U-30 | Halaman cetak | 200, memuat periode, mode rekap, nominal | ✅ Pass |
| U-31 | Pratinjau tanpa login (API + halaman) | 200, data rekap terbaca | ✅ Pass |

### B. Black-box API + MySQL

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| API-01 | `GET /owner/api/laporan/pendapatan?rekap=harian&periode=2026-11` | 30 baris `1 Nov`..`30 Nov`; total 108 / 4.420.000 / 196.500,75 / 4.223.499,25 | ✅ Pass |
| API-02 | `rekap=mingguan` November | 5 baris; total sama dengan harian | ✅ Pass |
| API-03 | `rekap=bulanan` November | Sep 14/663.000; Okt 161/8.030.500; Nov 108/4.420.000−196.500,75; total 283/13.113.500/196.500,75/12.916.999,25 | ✅ Pass |
| API-04 | `GET /owner/api/dashboard?periode=bulan` | Omzet 4.420.000 (kasir 3.413.000 + online 1.007.000), pengeluaran 196.500,75, laba 4.223.499,25, 108 transaksi | ✅ Pass |
| API-05 | `GET /owner/api/transaksi?periode=2026-11` | Masuk 4.420.000 (108), keluar 196.500,75 (4), selisih 4.223.499,25 | ✅ Pass |
| API-06 | `GET /owner/api/laporan/laba-rugi?periode=2026-11` | HPP 120.000; laba kotor 4.300.000; operasional 75.000; lainnya 1.500,75; laba bersih 4.223.499,25 | ✅ Pass |
| API-07 | Agregat SQL acuan November | POS 83 / 3.413.000; online 25 / 1.007.000; pengeluaran 4 / 196.500,75 — **cocok** | ✅ Pass |
| API-08 | Sisip 21 Nov 23.59.59 (111.000) & 22 Nov 00.00.00 (222.000) | Masuk minggu `15–21` dan `22–28` sesuai batas | ✅ Pass |
| API-09 | Baris harian 21, 22, 23, 29 Nov | 21=111.000; 22=222.000; 23 (cancelled)=0; 29 (pengeluaran 444.000) | ✅ Pass |
| API-10 | Bulanan November setelah sisipan | 110 transaksi; omzet 4.753.000; keluar 640.500,75; laba 4.112.499,25 | ✅ Pass |
| API-11 | `GET /owner/api/laporan/export?rekap=mingguan&periode=2026-11` | 200, `text/csv`, `rekap-pendapatan-mingguan-2026-11.csv`, header + 5 baris + Total | ✅ Pass |
| API-12 | Export harian | 31 baris tanggal + Total; 25 Nov `196500.75` dan `-196500.75` | ✅ Pass |
| API-13 | Export tanpa parameter | Nama file `rekap-pendapatan-mingguan-2026-11.csv` | ✅ Pass |
| API-14 | `/owner/laporan/cetak?rekap=harian&periode=2026-03` | 200; judul, periode, mode rekap, nominal tampil | ✅ Pass |
| API-15 | Pratinjau API tanpa login | 200; 3 baris bulanan; omzet 13.113.500 | ✅ Pass |
| API-16 | Halaman pratinjau tanpa login | 200; memuat tombol Export | ✅ Pass |
| API-17 | `rekap=triwulan` | 422 | ✅ Pass |
| API-18 | `periode=2026-13` | 422 | ✅ Pass |
| API-19 | `periode=2026-3` | 422 | ✅ Pass |
| API-20 | Rekap tanpa login | 302 ke `/login` | ✅ Pass |
| API-21 | Bulanan `2026-01`, `2026-02`, `2025-12` | Label melintasi tahun benar (`Nov 2025`..`Jan 2026`, dst.) | ✅ Pass |
| API-22 | Halaman cetak `rekap=triwulan` | 422 | ✅ Pass |

### C. Black-box browser

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| UI-01 | Buka `/owner/laporan` | Mode Mingguan aktif; 5 baris; Total 108 / Rp 4.420.000 / Rp 196.501 / Rp 4.223.499; kartu laba rugi November terisi | ✅ Pass |
| UI-02 | Klik "Harian" | Tabel berganti ke 30 baris + Total; judul "Rekap Pendapatan · Harian"; nilai 25 Nov = pengeluaran Rp 196.501 | ✅ Pass |
| UI-03 | Klik "Bulanan" | 3 baris Sep/Okt/Nov + Total 283 / Rp 13.113.500 / Rp 196.501 / Rp 12.916.999 | ✅ Pass |
| UI-04 | Kembali ke "Mingguan" | Tampilan berganti benar; data tetap | ✅ Pass |
| UI-05 | Ganti periode ke Oktober 2026 | 5 baris; Total 161 / Rp 8.030.500 / Rp 0 / Rp 8.030.500; kartu laba rugi jadi Oktober, Rp 8.030.500 | ✅ Pass |
| UI-06 | Baris "Lainnya" di kartu laba rugi | November tampil (− Rp 1.501); Oktober disembunyikan; November tampil lagi | ✅ Pass |
| UI-07 | Klik "Export Excel" (Oktober, mingguan) | Terunduh `rekap-pendapatan-mingguan-2026-10.csv` | ✅ Pass |
| UI-08 | Export harian November | Terunduh `rekap-pendapatan-harian-2026-11.csv`; 31 baris + Total; angka benar | ✅ Pass |
| UI-09 | Klik "Export PDF" | Membuka `http://127.0.0.1:8000/owner/%22/owner%22/laporan/cetak?...` → **404 Not Found** | ❌ Gagal (P-01) |
| UI-10 | Ganti mode/periode berulang | Tidak ada error console; tidak ada permintaan gagal | ✅ Pass |
| UI-11 | Tampilan sempit (864 px) | Kartu & tabel menumpuk vertikal; tabel lebar bisa digeser di dalam kartu | ✅ Pass |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| P-01 | **Tinggi** | **Tombol "Export PDF" selalu 404.** `resources/views/owner/laporan.blade.php:262` menulis ``window.open(`@json($halamanBase)/laporan/cetak?${parameterExport()}`, '_blank')``. `@json()` menghasilkan string ber-kutip, sehingga URL menjadi `"/owner"/laporan/cetak?...` (bukan `/owner/laporan/cetak?...`) dan browser membukanya sebagai `/owner/%22/owner%22/laporan/cetak?...` → **404 Not Found**. Berlaku juga untuk pratinjau (`"/frontend/owner"/laporan/cetak?...`). Halaman cetak sendiri sehat: `/owner/laporan/cetak?rekap=mingguan&periode=2026-11` → 200. | Jangan menaruh `@json()` di dalam template literal: ``window.open(`{{ $halamanBase }}/laporan/cetak?${parameterExport()}`, '_blank')``, atau `window.open(@json($halamanBase) + '/laporan/cetak?' + parameterExport(), '_blank')`. Tambahkan tes regresi untuk URL tombol. | ❌ Perlu perbaikan |
| P-02 | **Sedang** | **Pratinjau publik membocorkan laporan keuangan.** `/frontend/owner/laporan`, `/frontend/owner/api/laporan/pendapatan`, `/laba-rugi`, `/export`, dan `/laporan/cetak` dapat diakses tanpa login dan menampilkan omzet, pengeluaran, laba, serta rincian harian/bulanan usaha. | Batasi pratinjau ke data contoh statis, atau lindungi seluruh route `/frontend/owner/*` yang berisi data keuangan nyata. | ❌ Perlu perbaikan |
| P-03 | **Rendah** | **Pilihan periode hanya 3 bulan terakhir.** Dropdown dibuat dengan `@for ($i = 0; $i < 3; $i++)` (`laporan.blade.php:73-78`), sehingga hanya bulan berjalan, −1, dan −2 yang bisa dipilih. API mendukung `periode=YYYY-MM` apa pun; laporan lebih lama tidak bisa dibuka dari UI. | Tambahkan pemilih bulan/tahun (atau opsi "rentang") dan isi dari data yang tersedia. | ⚠️ Saran |
| P-04 | **Rendah** | **Omzet dikelompokkan menurut tanggal pesanan dibuat, bukan tanggal dibayar.** `ReportController::omzetHarian()` memakai `created_at` untuk `orders` (dan `pos_transactions`). Pesanan dibuat 27 Feb tetapi dibayar 1 Mar tercatat sebagai pendapatan Februari; uang yang diterima Maret tidak terlihat di rekap Maret. Perilaku ini konsisten dengan dashboard dan sudah didokumentasikan, tetapi bukan basis kas. | Gunakan `paid_at` untuk pesanan (dan `created_at` untuk POS), atau beri label "basis akrual" di UI/dokumen. | ⚠️ Saran |
| P-05 | **Rendah** | **Sen disembunyikan di UI.** `rupiah()` memakai `Math.round(Math.abs(nilai))`, sehingga Rp 196.500,75 tampil "Rp 196.501" di tabel dan kartu, sedangkan API/CSV menyimpan 196500.75. Dengan beberapa baris bersen, pembulatan per baris bisa berbeda beberapa rupiah dari total. | Tampilkan 2 desimal bila ada sen, atau bulatkan juga di backend agar semua saluran sama. | ⚠️ Saran |
| P-06 | **Rendah** | **Tidak ada keadaan kosong/error.** Bila `fetch` gagal (`!response.ok`), fungsi `muatRekap()`/`muatLaba()` hanya `return`/`console.error`, sehingga tabel tetap kosong tanpa pesan; bulan tanpa transaksi hanya menampilkan baris nol tanpa keterangan "tidak ada data". | Tampilkan pesan "Gagal memuat" + tombol coba lagi, dan empty state yang jelas. | ⚠️ Saran |
| P-07 | **Info** | **Dokumentasi `docs/API-LAPORAN.md` sudah basi.** Masih menyebut "kartu laba-rugi ... angka contoh" dan "opsi `periode` pada view masih statis (Okt/Sep/Agu 2026)"; kenyataannya kartu laba rugi sudah tersambung (CHIK-139) dan opsi periode dibuat dinamis (3 bulan terakhir). | Perbarui catatan agar sesuai implementasi. | ℹ️ Catatan |
| P-08 | **Info** | **"Export Excel" mengunduh CSV.** Nama file berakhiran `.csv` dan `Content-Type: text/csv`, bukan `.xlsx`. Ini sudah didokumentasikan dan tetap bisa dibuka Excel, tetapi label tombol menyesatkan sebagian pengguna. | Ganti label menjadi "Export CSV", atau hasilkan `.xlsx` sungguhan. | ℹ️ Catatan |
| P-09 | **Info** | **Cakupan total bulanan berbeda dari kartu laba rugi.** Tabel "Bulanan" menjumlahkan bulan terpilih + 2 bulan sebelumnya (total Rp 13.113.500 untuk November), sedangkan kartu "Laba Rugi" hanya bulan terpilih (Rp 4.420.000). Angka besar di baris Total bisa disalahartikan sebagai satu bulan. | Beri subjudul "3 bulan" pada tabel bulanan atau tampilkan total per bulan. | ℹ️ Catatan |
| P-10 | **Info** | **Tabel hasil muat ulang tidak diumumkan ke pembaca layar.** Tidak ada `aria-live`/status loading; tabel juga tanpa `<caption>`. Radio dan select sudah berada di dalam `<label>` sehingga masih terbaca. | Tambahkan `aria-live="polite"` pada wadah tabel dan `<caption>` tersembunyi. | ℹ️ Catatan |
| P-11 | **Info** | **Regresi lama di luar PPL-02.** `ExampleTest` gagal (`GET /` → 302) dan `PenambahanStokTest::test_halaman_stok_merender_form_dan_riwayat` gagal 500 `no such table: order_items`. | Perbaiki/hapus `ExampleTest`; lengkapi skema tes stok dengan tabel `orders`/`order_items`. | ℹ️ Known issue |

## Catatan Lingkungan

- DB `chikumi` di mesin uji berisi 216 transaksi kasir, 78 pesanan online, dan 4 pengeluaran (total Rp 196.500,75). Tidak ada data permanen yang diubah.
- Data sementara untuk uji batas minggu disisipkan langsung ke MySQL, lalu dihapus:
  - `POS-QA-REKAP-A` Rp 111.000 (21 Nov 2026 23.59.59, `paid`)
  - `POS-QA-REKAP-B` Rp 222.000 (22 Nov 2026 00.00.00, `paid`)
  - `POS-QA-REKAP-C` Rp 333.000 (23 Nov 2026, `cancelled`)
  - Pengeluaran "QA rekap 29 Nov" Rp 444.000 (29 Nov, kategori `other`)
  Setelah dibersihkan, total kembali ke 108 transaksi / Rp 4.420.000 / Rp 196.500,75.
- Enum `pos_transactions.status` hanya berisi `paid` dan `cancelled`; tidak ada status `pending`. Uji "POS belum lunas" karena itu memakai `cancelled`.
- Aset frontend di-build ulang (`npm run build`) agar kelas Tailwind baru (`min-w-[640px]`, `group-has-[#rekap-harian:checked]`, `has-checked`) ikut terkompilasi. Hasil: `public/build/assets/app-B8P1dEhm.css` (83,62 kB). `public/build` tidak dilacak git.
- Browser uji memakai sesi owner yang sama, sehingga halaman pratinjau `/frontend/owner/laporan` memuat `API_BASE=/owner/api`. Perilaku tamu diverifikasi lewat HTTP tanpa sesi: HTML memuat `const API_BASE = "\/frontend\/owner\/api"` dan endpoint pratinjau membalas 200.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`), MySQL untuk uji data nyata.

1. **Tes otomatis (SQLite in-memory, tanpa MySQL):**
   ```
   php artisan test --filter=RekapPendapatanTest
   ```
   Diharapkan `31 passed, 149 assertions`.
2. **Uji manual:** login `owner@chikumi.com`/`owner123` → menu **Laporan**.
   - Pastikan mode **Mingguan** menampilkan 5 baris + Total untuk November 2026.
   - Klik **Harian** (30 baris) dan **Bulanan** (3 baris); total ketiganya harus sama: 108 transaksi, Rp 4.420.000 omzet, Rp 196.501 pengeluaran, Rp 4.223.499 laba.
   - Ganti **Periode** ke Oktober 2026: 161 transaksi, Rp 8.030.500, pengeluaran Rp 0.
   - Klik **Export Excel** → terunduh `rekap-pendapatan-<mode>-<periode>.csv`.
   - Klik **Export PDF** → saat ini **404** (P-01). Halaman cetak yang benar: `/owner/laporan/cetak?rekap=mingguan&periode=2026-11`.
3. **Bandingkan dengan SQL** (November 2026):
   ```sql
   SELECT COUNT(*), SUM(total_amount) FROM pos_transactions
    WHERE status='paid' AND created_at BETWEEN '2026-11-01' AND '2026-11-30 23:59:59';
   SELECT COUNT(*), SUM(grand_total) FROM orders
    WHERE status <> 'cancelled' AND paid_at IS NOT NULL
      AND created_at BETWEEN '2026-11-01' AND '2026-11-30 23:59:59';
   SELECT COUNT(*), SUM(amount) FROM expenses
    WHERE expense_date BETWEEN '2026-11-01' AND '2026-11-30';
   ```
4. **Regresi:** `php artisan test` → 251/253 lulus; 2 kegagalan lama (P-11).
