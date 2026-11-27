# Laporan Testing — [PPL-04] Perhitungan Laba Kotor & Laba Bersih

- **Backlog**: [PPL-04] Perhitungan Laba Kotor & Laba Bersih — Jira `CHIK-59` (berisi `CHIK-139` perhitungan laba kotor & laba bersih, `CHIK-140` tampilan laporan laba, `CHIK-141` testing laba); tiket testing `CHIK-141`
- **Aplikasi**: Chikumi (Laravel 13, PHP 8.3)
- **Kode yang diuji**: `main` commit `b8ef061`. Subtask sudah merge: `CHIK-139` → PR #62 (`fe7bbb1`; commit `d1dda6d` perhitungan, `17e1b98` sambungan tabel transaksi/laporan, `a0d3e8e` export & halaman cetak), `CHIK-140` → PR #57 (`a0a4c50`; commit `e1709ca` kartu laba rugi, `8f27bfe` sisipkan kartu di halaman laporan). Pengujian di branch `CHIK-141-testing-laba`.
- **Scope (mapping tim)**:
  - `CHIK-139` — **perhitungan**: `app/Http/Controllers/Owner/ReportController.php` (`labaRugi()`, `hitungLabaRugi()`, `pengeluaranKategori()`, `omzetHarian()`, `acuan()`), route `GET /owner/api/laporan/laba-rugi`, pratinjau `GET /frontend/owner/api/laporan/laba-rugi`, dan `docs/API-LAPORAN.md`.
  - `CHIK-140` — **tampilan**: `resources/views/components/owner/laba-rugi.blade.php` (kartu Laba Rugi), `resources/views/owner/laporan.blade.php` (`muatLaba()`, `potong()`, `uang()`, filter periode), dan `resources/views/owner/laporan-cetak.blade.php` (bagian Laba Rugi pada halaman cetak).
- **Metode**:
  1. **PHPUnit** `tests/Feature/LabaKotorBersihTest.php` — 38 tes; setiap omzet, HPP, laba kotor, operasional, karyawan, lainnya, total pengeluaran, dan laba bersih **dicocokkan dengan hitungan manual** (SQLite in-memory, waktu dipatok 2026-03-15).
  2. **Black-box API + MySQL** — 18 skenario dengan sesi owner; angka API dibandingkan dengan agregat SQL langsung di database `chikumi`, ditambah satu bulan sintetis (Juli 2026) untuk menguji ketujuh kategori pengeluaran, transaksi lintas bulan, dan laba negatif. Semua data sintetis dihapus setelah uji.
  3. **Black-box browser** — halaman `/owner/laporan`: kartu Laba Rugi, ganti periode September/Oktober/November, baris "Lainnya", tombol Export Excel/PDF, halaman cetak, console, dan jaringan.
- **Terverifikasi**: 2026-11-27
- **Keterbatasan**: kartu Jira `CHIK-59`/`CHIK-141` tidak diakses dari mesin uji; kriteria penerimaan diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.** Zona waktu aplikasi masih `UTC` (lihat Catatan Lingkungan).

## Ringkasan

| Jenis | Total | Lulus | Gagal | Pass Rate |
|---|---|---|---|---|
| PHPUnit — rumus laba, omzet, HPP, kategori, tampilan, hak akses (`LabaKotorBersihTest`) | 38 | 38 | 0 | 100% |
| Black-box API + MySQL | 18 | 18 | 0 | 100% |
| Black-box browser | 10 | 8 | 2 | 80% |
| **Kriteria penerimaan (AC-1..AC-8)** | **8** | **6** | **1 sebagian + 1 gagal** | **75%** |
| Regresi suite penuh | 326 | 326 | 0 | 100% |

**Hasil: LULUS DENGAN CATATAN.**

Perhitungan laba **akurat dan konsisten dengan hitungan manual**. Untuk November 2026: omzet **Rp 4.420.000** (kasir Rp 3.413.000 + online Rp 1.007.000; 108 transaksi), HPP **Rp 120.000** (kemasan), laba kotor **Rp 4.300.000**, operasional **Rp 75.000**, lainnya **Rp 1.500,75**, total pengeluaran **Rp 196.500,75**, laba bersih **Rp 4.223.499,25**. Untuk Oktober 2026: omzet **Rp 8.030.500** (161 transaksi), tanpa pengeluaran, laba kotor = laba bersih = **Rp 8.030.500**. Angka itu identik di API, kartu laporan, kartu dashboard, dan rekap pendapatan. Uji sintetis Juli 2026 juga cocok: omzet Rp 160.000, HPP Rp 25.000 (bahan baku Rp 20.000 + kemasan Rp 5.000), laba kotor Rp 135.000, karyawan Rp 33.000 (gaji Rp 30.000 + bonus Rp 2.000 + lembur Rp 1.000), lainnya Rp 3.000, total pengeluaran Rp 68.000, laba bersih **Rp 92.000**.

Dua catatan utama: **(1) tombol Export PDF masih rusak** — URL yang dibuka adalah `"/owner"/laporan/cetak?...` sehingga tab baru berjudul **Not Found** (404). Ini temuan yang sama dengan PPL-02 dan **belum diperbaiki** di `main`; **(2) laba kotor negatif ditampilkan sebagai positif** — saat HPP lebih besar dari omzet, API mengembalikan `laba_kotor = -69.500`, tetapi kartu menulis "LABA KOTOR Rp 69.500" (tanpa tanda minus), sehingga kerugian terlihat seperti keuntungan. Laba bersih di kartu sudah benar ("− Rp 69.500"). Selain itu, HPP masih berbasis pencatatan pengeluaran (bukan harga pokok per item); pada Oktober yang tidak punya pengeluaran, laba kotor otomatis 100% omzet.

## Kriteria Penerimaan yang Diuji

Kartu Jira `CHIK-59`/`CHIK-141` tidak diakses dari mesin uji; kriteria diturunkan dari judul backlog dan subtask. **Mohon Product Owner mengonfirmasi di kartu.**

| Kode | Kriteria | Hasil | Bukti |
|---|---|---|---|
| AC-1 | HPP = pengeluaran bahan baku + kemasan | ✅ Lulus | U-16, U-18, API-09, API-11 |
| AC-2 | Laba kotor = omzet − HPP | ✅ Lulus | U-17, U-19, U-20, API-03, API-09 |
| AC-3 | Laba bersih = laba kotor − operasional − karyawan − lainnya = omzet − seluruh pengeluaran | ✅ Lulus | U-21..U-26, API-03, API-09 |
| AC-4 | Omzet hanya dari POS lunas + pesanan online berbayar yang tidak batal | ✅ Lulus | U-08..U-11, API-09, API-10 |
| AC-5 | Karyawan = gaji + bonus + lembur | ✅ Lulus | U-22, API-09 |
| AC-6 | Angka API cocok dengan hitungan manual pada data nyata | ✅ Lulus | API-01..API-03, API-15, U-31, U-32 |
| AC-7 | Nilai negatif ditampilkan dengan benar di kartu & cetak | ❌ Gagal — laba kotor negatif tampil positif (P-02); cetak memakai tanda ganda (P-04) | U-20, U-37, UI-05, UI-06 |
| AC-8 | Owner-only dan laporan dapat diekspor | ⚠️ Sebagian — hak akses benar, Export Excel jalan, **Export PDF 404** (P-01) | U-01..U-03, U-33, UI-07, UI-08 |

## Data Uji

**Dataset PHPUnit (SQLite in-memory)**: 1 owner, 1 kasir, 1 pelanggan; tabel `expenses`, `orders`, `order_items`; data produk dari trait `MenyiapkanDataChikumi`; waktu uji dipatok `2026-03-15` agar default periode deterministik.

**Data nyata** (database `chikumi`): 216 transaksi kasir (215 `paid`, 1 `cancelled`), 78 pesanan online, 4 pengeluaran.

| Item | Nilai |
|---|---|
| September 2026 | 14 transaksi, omzet **Rp 663.000** (kasir Rp 477.000 + online Rp 186.000), pengeluaran Rp 0, laba bersih Rp 663.000 |
| Oktober 2026 | 161 transaksi, omzet **Rp 8.030.500** (kasir Rp 5.766.000 + online Rp 2.264.500), pengeluaran Rp 0, laba kotor & bersih **Rp 8.030.500** |
| November 2026 | 108 transaksi, omzet **Rp 4.420.000** (kasir Rp 3.413.000 + online Rp 1.007.000) |
| Pengeluaran November | **Rp 196.500,75** — kemasan Rp 120.000, operasional Rp 75.000, lainnya Rp 1.500,75 |
| Laba November | HPP Rp 120.000; laba kotor **Rp 4.300.000**; laba bersih **Rp 4.223.499,25** |
| Data sintetis Juli 2026 | 2 POS (1 `paid` Rp 100.000, 1 `cancelled` Rp 99.000), 4 pesanan (1 `completed` Rp 60.000 termasuk ongkir Rp 10.000, 1 `unpaid`, 1 `cancelled`, 1 lintas bulan), 7 pengeluaran Rp 68.000 — **dihapus setelah uji** |
| Data sintetis Juni 2026 | 1 pesanan dibuat 30 Jun, dibayar 1 Jul, Rp 40.000 — dihitung di Juni, bukan Juli |
| Uji laba negatif | Tambah bahan baku Rp 200.000 (Juli) → laba kotor −Rp 65.000; tambah bahan baku Rp 8.100.000 (Oktober) → laba kotor −Rp 69.500 — **dihapus setelah uji** |

Akun uji: `owner@chikumi.com`/`owner123`, `kasir@chikumi.com`/`kasir123`, `customer@gmail.com`/`customer123`.

## Tabel Test Case

### A. PHPUnit (`tests/Feature/LabaKotorBersihTest.php`)

Hasil: **38 passed, 149 assertions**.

| ID | Skenario | Hasil Diharapkan | Status |
|---|---|---|---|
| U-01 | Tamu membuka API, halaman laporan, dan halaman cetak | 302 ke `/login` | ✅ Pass |
| U-02 | Kasir & pelanggan membuka API/halaman | JSON 403; halaman dialihkan | ✅ Pass |
| U-03 | Owner membuka API & halaman laporan | 200; periode `2026-03`, label `Maret 2026` | ✅ Pass |
| U-04 | Struktur JSON | Memuat `omzet`, `hpp{bahan_baku,kemasan,total}`, `laba_kotor`, `operasional`, `karyawan{gaji,bonus_lembur,total}`, `lainnya`, `total_pengeluaran`, `laba_bersih` | ✅ Pass |
| U-05 | `periode` salah (`2026-13`, `2026-00`, `2026-3`, `Maret 2026`, `2026-03-01`) | 422 pada `periode` | ✅ Pass |
| U-06 | Tanpa parameter | Default bulan berjalan `2026-03`, label `Maret 2026` | ✅ Pass |
| U-07 | Label bulan Januari/Agustus/Desember | `Januari 2026`, `Agustus 2026`, `Desember 2026` | ✅ Pass |
| U-08 | POS `paid` Rp 100.000 + Rp 250.000, POS `cancelled` Rp 900.000 | Omzet Rp 350.000, 2 transaksi | ✅ Pass |
| U-09 | POS Rp 100.000 + pesanan online Rp 60.000 | Omzet Rp 160.000, 2 transaksi | ✅ Pass |
| U-10 | Pesanan `unpaid` (belum bayar) & `cancelled` (sudah bayar) | Tidak dihitung; omzet 0 | ✅ Pass |
| U-11 | Pesanan `processing` & `completed` yang sudah dibayar | Keduanya dihitung; omzet Rp 100.000 | ✅ Pass |
| U-12 | Pesanan subtotal Rp 50.000 + ongkir Rp 10.000 | Omzet memakai `grand_total` Rp 60.000 | ⚠️ Perilaku (P-06) |
| U-13 | Transaksi Feb, Mar, Apr | Hanya Maret Rp 100.000 yang dihitung | ✅ Pass |
| U-14 | 1 Mar 00.00, 31 Mar 23.59.59, 28 Feb, 1 Apr | Dua transaksi pertama masuk; total Rp 3.000 | ✅ Pass |
| U-15 | Pesanan dibuat 27 Feb, dibayar 1 Mar | Masuk Februari (mengikuti `created_at`), bukan Maret | ⚠️ Perilaku (P-05) |
| U-16 | Bahan baku Rp 100.000 + kemasan Rp 50.000 | `hpp.total` Rp 150.000 | ✅ Pass |
| U-17 | Omzet Rp 1.000.000, HPP Rp 150.000 | Laba kotor Rp 850.000 | ✅ Pass |
| U-18 | Operasional, gaji, lainnya | Tidak masuk HPP; HPP 0, laba kotor = omzet | ✅ Pass |
| U-19 | Tanpa pengeluaran HPP | HPP 0, laba kotor = omzet Rp 300.000 | ⚠️ Perilaku (P-03) |
| U-20 | Omzet Rp 100.000, HPP Rp 250.000 | Laba kotor & laba bersih −Rp 150.000 | ✅ Pass |
| U-21 | Operasional Rp 30.000 + bahan baku Rp 50.000 | `operasional` hanya Rp 30.000 | ✅ Pass |
| U-22 | Gaji Rp 200.000, bonus Rp 20.000, lembur Rp 10.000 | `karyawan.total` Rp 230.000 | ✅ Pass |
| U-23 | Lainnya Rp 5.000 + operasional Rp 9.000 | `lainnya` hanya Rp 5.000 | ✅ Pass |
| U-24 | Tujuh kategori pengeluaran | `total_pengeluaran` Rp 415.000 | ✅ Pass |
| U-25 | Omzet Rp 1.000.000, total pengeluaran Rp 415.000 | Laba bersih Rp 585.000 = omzet − total pengeluaran | ✅ Pass |
| U-26 | Omzet Rp 100.000, operasional Rp 150.000 | Laba bersih −Rp 50.000 | ✅ Pass |
| U-27 | Pengeluaran Feb, Mar, Apr | Hanya Maret Rp 200.000 yang dihitung | ✅ Pass |
| U-28 | Dua pengeluaran bahan baku Rp 25.000 + Rp 35.000 | `hpp.bahan_baku` Rp 60.000 | ✅ Pass |
| U-29 | Bahan baku Rp 1.234,56 + kemasan Rp 0,44 | HPP Rp 1.235; laba kotor Rp 98.765 | ✅ Pass |
| U-30 | Tanpa transaksi & pengeluaran | Semua nilai 0 | ✅ Pass |
| U-31 | Laba rugi vs total rekap bulan berjalan | Omzet, transaksi, pengeluaran, laba identik | ✅ Pass |
| U-32 | Laba rugi vs kartu dashboard bulan berjalan | Omzet, pengeluaran, laba, transaksi identik | ✅ Pass |
| U-33 | Pratinjau `/frontend/owner/api/laporan/laba-rugi` & `/frontend/owner/laporan` tanpa login | 200, data laba terbaca | ✅ Pass |
| U-34 | Halaman laporan | Memuat `labaRugi`, `labaPeriode`, `labaOmzet`, `labaBahanBaku`, `labaKemasan`, `labaKotor`, `labaOperasional`, `labaGaji`, `labaBonusLembur`, `labaBersih`, "LABA KOTOR", "LABA BERSIH" | ✅ Pass |
| U-35 | Halaman cetak dengan data lengkap | Memuat periode, HPP, laba kotor Rp 850.000, laba bersih Rp 585.000, "Lainnya" | ✅ Pass |
| U-36 | Halaman cetak tanpa pengeluaran `other` | Baris "Lainnya" tidak dirender | ✅ Pass |
| U-37 | Halaman cetak laba kotor negatif | Memuat "Rp -150.000" | ✅ Pass |
| U-38 | Halaman laporan | Tombol mengarah ke `/laporan/cetak` dan `/laporan/export` | ✅ Pass |

### B. Black-box API + MySQL

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| API-01 | `GET /owner/api/laporan/laba-rugi?periode=2026-09` | Omzet Rp 663.000; 14 transaksi; HPP/operasional/karyawan/lainnya 0; laba bersih Rp 663.000 | ✅ Pass |
| API-02 | `GET …?periode=2026-10` | Omzet Rp 8.030.500; 161 transaksi; HPP 0; laba kotor = laba bersih = Rp 8.030.500 | ✅ Pass |
| API-03 | `GET …?periode=2026-11` | Omzet Rp 4.420.000; 108 transaksi; HPP Rp 120.000; laba kotor Rp 4.300.000; operasional Rp 75.000; karyawan 0; lainnya Rp 1.500,75; total pengeluaran Rp 196.500,75; laba bersih Rp 4.223.499,25 | ✅ Pass |
| API-04 | Tanpa parameter `periode` | Default November 2026, omzet Rp 4.420.000 | ✅ Pass |
| API-05 | Periode tidak valid (`2026-13`, `2026-00`, `2026-3`, `Maret 2026`, `2026-03-01`) | 422 pada `periode` | ✅ Pass |
| API-06 | Tanpa login membuka API owner | 302 ke `/login` | ✅ Pass |
| API-07 | Login kasir membuka API owner | 403 | ✅ Pass |
| API-08 | Pratinjau `/frontend/owner/api/laporan/laba-rugi?periode=2026-10` tanpa login | 200; omzet Rp 8.030.500; laba bersih Rp 8.030.500 | ✅ Pass (bocor → P-09) |
| API-09 | Bulan sintetis Juli 2026 (POS/non-POS, 7 kategori) | Omzet Rp 160.000; 2 transaksi; HPP Rp 25.000; laba kotor Rp 135.000; operasional Rp 7.000; karyawan Rp 33.000; lainnya Rp 3.000; total pengeluaran Rp 68.000; laba bersih Rp 92.000 | ✅ Pass |
| API-10 | Pesanan dibuat 30 Jun, dibayar 1 Jul, Rp 40.000 | Juni omzet Rp 40.000; Juli 0 untuk pesanan itu | ⚠️ Perilaku (P-05) |
| API-11 | Tambah bahan baku Rp 200.000 di Juli | HPP Rp 225.000; laba kotor −Rp 65.000; total pengeluaran Rp 268.000; laba bersih −Rp 108.000 | ✅ Pass |
| API-12 | Halaman cetak Juli positif | Memuat Rp 160.000, Rp 135.000, Rp 92.000; baris "Lainnya" tampil | ✅ Pass |
| API-13 | Halaman cetak Juli negatif | Memuat "Rp -65.000" dan "Rp -108.000" | ✅ Pass (format → P-04) |
| API-14 | Export CSV November | 200 `text/csv`; nama `rekap-pendapatan-mingguan-2026-11.csv`; header + 5 baris + Total | ✅ Pass |
| API-15 | Agregat SQL acuan Oktober | POS 122 / Rp 5.766.000; online 39 / Rp 2.264.500; total Rp 8.030.500 — **cocok** | ✅ Pass |
| API-16 | Agregat SQL acuan November | POS 83 / Rp 3.413.000; online 25 / Rp 1.007.000; pengeluaran 4 / Rp 196.500,75 — **cocok** | ✅ Pass |
| API-17 | Hitung manual November | Rp 4.420.000 − Rp 196.500,75 = Rp 4.223.499,25 = laba bersih API | ✅ Pass |
| API-18 | Pembersihan data sintetis | 0 baris `QA-LABA-*`; API Oktober kembali Rp 8.030.500; November kembali Rp 4.223.499,25 | ✅ Pass |

### C. Black-box browser

| ID | Skenario | Hasil Nyata | Status |
|---|---|---|---|
| UI-01 | Login owner, buka `/owner/laporan` | Halaman 200; kartu Laba Rugi · November 2026; console 0 error | ✅ Pass |
| UI-02 | Kartu November | Omzet Rp 4.420.000 (108 transaksi); Bahan Baku −Rp 0; Kemasan −Rp 120.000; Laba Kotor Rp 4.300.000; Operasional −Rp 75.000; Gaji/Bonus −Rp 0; Lainnya −Rp 1.501 (tampil); Laba Bersih Rp 4.223.499 | ✅ Pass |
| UI-03 | Ganti periode ke Oktober | Omzet Rp 8.030.500 (161); Laba Kotor & Laba Bersih Rp 8.030.500; baris "Lainnya" tersembunyi | ✅ Pass |
| UI-04 | Tabel rekap mengikuti periode | Baris pertama Oktober: `1–7 Okt 35 Rp 1.754.500 Rp 0 Rp 1.754.500` | ✅ Pass |
| UI-05 | Laba kotor negatif (Oktober + bahan baku Rp 8.100.000) | API `laba_kotor = -69.500`; kartu menulis **"LABA KOTOR Rp 69.500"** (tanpa minus); Laba Bersih "− Rp 69.500" | ❌ Gagal (P-02) |
| UI-06 | Halaman cetak periode yang sama | "LABA KOTOR Rp -69.500"; "LABA BERSIH − Rp -69.500"; rekap minggu 15–21 "− Rp -5.916.500" — angka benar, format tanda ganda | ⚠️ Pass (P-04) |
| UI-07 | Klik tombol Export PDF | Tab baru `/owner/%22/owner%22/laporan/cetak?rekap=mingguan&periode=2026-11`, judul **Not Found** (404) | ❌ Gagal (P-01) |
| UI-08 | Klik tombol Export Excel | Mengunduh `rekap-pendapatan-mingguan-2026-11.csv` (255 byte) | ✅ Pass |
| UI-09 | Jaringan & console saat ganti periode | Memanggil `/owner/api/laporan/laba-rugi?periode=2026-11` lalu `2026-10`; 0 error console | ✅ Pass |
| UI-10 | Setelah data uji dihapus | Oktober kembali Rp 8.030.500; November kembali Rp 4.223.499,25; tidak ada data `QA-LABA` | ✅ Pass |

## Temuan

| ID | Tingkat | Temuan | Rekomendasi | Status |
|---|---|---|---|---|
| P-01 | **Tinggi** | **Tombol Export PDF rusak (404).** `resources/views/owner/laporan.blade.php:262` menulis ``window.open(`@json($halamanBase)/laporan/cetak?...`)``. `@json()` menghasilkan `"/owner"` **termasuk tanda kutip** di dalam template literal, sehingga URL menjadi `"/owner"/laporan/cetak?...`. Browser membuka `/owner/%22/owner%22/laporan/cetak?...` dan menampilkan **Not Found**. Ini temuan yang sama dengan PPL-02 (P-01) dan masih ada di `main`. | Simpan ke variabel: `const HALAMAN_BASE = @json($halamanBase);` lalu `window.open(\`${HALAMAN_BASE}/laporan/cetak?...\`)`; atau pakai `{{ url('/owner/laporan/cetak') }}`. | ❌ Perlu perbaikan |
| P-02 | **Tinggi** | **Laba kotor negatif ditampilkan sebagai positif.** `rupiah()` di `laporan.blade.php` memakai `Math.abs(nilai)`, dan `labaKotor` diisi dengan `rupiah(data.laba_kotor)`. Saat `laba_kotor = -69.500`, kartu menampilkan **"LABA KOTOR Rp 69.500"** — kerugian terbaca sebagai keuntungan. Laba bersih memakai `uang()` sehingga benar ("− Rp 69.500"). Terverifikasi di browser dengan data sementara. | Isi `labaKotor` dengan `uang(data.laba_kotor)`, dan beri warna merah bila negatif. Audit semua pemakaian `rupiah()` untuk nilai yang bisa negatif. | ❌ Perlu perbaikan |
| P-03 | **Sedang** | **HPP berbasis pencatatan pengeluaran, bukan harga pokok penjualan.** `hitungLabaRugi()` hanya menjumlahkan `expenses` kategori `raw_material` + `packaging`. Bila owner tidak mencatat pembelian bahan baku pada bulan itu, HPP = 0 dan laba kotor = 100% omzet. Terlihat pada data nyata: Oktober 2026 tidak punya pengeluaran, sehingga laba kotor Rp 8.030.500 (margin 100%). | Konfirmasi ke PO apakah "laba kotor" memang berbasis kas/pengeluaran. Bila perlu, tambahkan harga pokok per produk/ukuran atau peringatan di kartu saat HPP = 0 padahal omzet > 0. | ⚠️ Perlu keputusan produk |
| P-04 | **Rendah** | **Format nilai negatif di halaman cetak tidak konsisten.** `$rupiah()` mencetak "Rp -69.500" (tanda minus setelah Rp), sedangkan `$uang()` mencetak "− Rp -69.500" (tanda minus ganda). Tabel rekap juga menulis "− Rp -5.916.500". Angka benar, tetapi tampilan membingungkan. | Buat satu formatter: `abs()` untuk nominal, lalu prepend "− " hanya sekali bila nilai asli negatif. Berlaku untuk kartu, tabel rekap, dan cetak. | ⚠️ Saran |
| P-05 | **Rendah** | **Pesanan online diakui pada `created_at`, bukan `paid_at`.** Pesanan dibuat 30 Juni dan dibayar 1 Juli dihitung sebagai omzet Juni. Untuk laporan laba bulanan, ini bisa memindahkan pendapatan ke bulan yang salah. Perilaku ini sama dengan rekap pendapatan dan sudah didokumentasikan, tetapi belum ada catatan di UI. | Gunakan `paid_at` sebagai tanggal pengakuan pendapatan, atau beri catatan "omzet dihitung berdasarkan tanggal pesanan dibuat" di halaman laporan. | ⚠️ Saran |
| P-06 | **Rendah** | **Omzet memakai `grand_total` termasuk ongkir.** Pesanan kurir Rp 50.000 + ongkir Rp 10.000 dihitung sebagai omzet Rp 60.000. Tidak ada kategori pengeluaran khusus ongkir/kurir, sehingga laba bisa lebih tinggi dari sebenarnya bila biaya kurir tidak dicatat. Pada data Oktober, ongkir yang ikut terhitung Rp 145.000. | Pisahkan pendapatan ongkir dari penjualan (pakai `subtotal` untuk laba kotor) atau tambahkan kategori pengeluaran "Ongkos Kirim" dan pastikan tercatat. | ⚠️ Saran |
| P-07 | **Info** | **Subjudul kartu tidak menyebut "lainnya".** Teks kartu: "Laba bersih = laba kotor − operasional − karyawan", padahal rumus juga mengurangi `lainnya` (November: Rp 1.500,75). | Ubah menjadi "− operasional − karyawan − lainnya". | ℹ️ Catatan |
| P-08 | **Info** | **Nilai nol ditampilkan dengan tanda minus.** Baris Bahan Baku/Kemasan/Operasional/Gaji/Bonus menulis "− Rp 0" saat nilainya nol. | Tampilkan "Rp 0" tanpa "−" bila nilai 0. | ℹ️ Catatan |
| P-09 | **Sedang** | **Pratinjau publik membuka laporan laba tanpa login.** `GET /frontend/owner/api/laporan/laba-rugi` mengembalikan omzet, HPP, gaji, dan laba tanpa autentikasi. Pola ini sama dengan temuan PPL-02/PPL-06; mungkin memang route demo, tetapi data keuangan terbuka. | Konfirmasi ke PO; bila bukan untuk demo publik, lindungi route `/frontend/owner/*` atau batasi datanya. | ⚠️ Perlu keputusan |
| P-10 | **Info** | **Dokumentasi basi.** `docs/API-LAPORAN.md:45` masih menulis "Kartu laba-rugi (HPP/operasional/gaji) masih angka contoh milik frontend", padahal kartu sudah tersambung ke API (bagian berikutnya di dokumen yang sama juga menyatakan sudah tersambung). | Hapus/perbarui kalimat tersebut dan catat rumus HPP berbasis `expenses`. | ℹ️ Catatan |
| P-11 | **Info** | **Export Excel hanya berisi rekap pendapatan.** CSV tidak memuat HPP/laba kotor/operasional/karyawan; kartu Laba Rugi hanya ikut pada Export PDF (halaman cetak). Pengguna yang mengira "Export Excel" mengunduh seluruh laporan keuangan bisa kehilangan bagian laba. | Tambahkan blok Laba Rugi ke CSV, atau ubah label menjadi "Export Rekap (Excel)". | ℹ️ Catatan |

## Catatan Lingkungan

- Seluruh perubahan data uji dilakukan langsung di MySQL `chikumi` dan **dipulihkan** setelah pengujian. Kondisi akhir:
  ```
  pos_transactions : 216 baris (215 paid, 1 cancelled)
  orders           : 78 baris
  expenses         : 4 baris (Rp 196.500,75)
  ```
- Data sintetis Juli 2026 (`QA-LABA-*`) dan pengeluaran sementara `QA LABANEG` (Rp 8.100.000, id 16) sudah dihapus; API Oktober/November kembali ke nilai awal.
- Uji negatif di browser memakai pengeluaran sementara Rp 8.100.000 di Oktober; setelah dihapus, kartu Oktober kembali "Rp 8.030.500".
- Aset frontend memakai build yang sudah ada (`public/build/assets/app-B8P1dEhm.css`); tidak ada kelas Tailwind baru pada perubahan CHIK-139/CHIK-140.
- Aplikasi berjalan di `http://127.0.0.1:8000` (php artisan serve) dengan MySQL 8.4.3.
- **Zona waktu**: `APP_TIMEZONE` tidak diset (default `UTC`), sedangkan koneksi MySQL memakai `+07:00` (`config/database.php`). Semua perhitungan bulan memakai `now()` (UTC). Pada 00.00–06.59 WIB tanggal 1, periode default masih bulan sebelumnya. Belum diuji dengan mengubah jam sistem; disarankan menyetel `APP_TIMEZONE=Asia/Jakarta` dan menguji ulang di batas bulan.

## Cara Menjalankan Ulang

**Prasyarat**: PHP 8.3+, Composer (di mesin uji: `C:\laragon\bin\composer\composer.bat`), MySQL untuk uji data nyata.

1. **Tes otomatis (SQLite in-memory, tanpa MySQL):**
   ```
   php artisan test --filter=LabaKotorBersihTest
   ```
   Diharapkan `38 passed, 149 assertions`.
2. **Uji manual di browser** (login `owner@chikumi.com`/`owner123`):
   - Buka **Laporan** → kartu **Laba Rugi**. Untuk November 2026, angka harus: Omzet Rp 4.420.000 (108 transaksi), Kemasan Rp 120.000, Laba Kotor Rp 4.300.000, Operasional Rp 75.000, Lainnya Rp 1.501, Laba Bersih Rp 4.223.499.
   - Ganti **Periode** ke Oktober 2026: Omzet Rp 8.030.500 (161 transaksi), Laba Kotor = Laba Bersih Rp 8.030.500, baris "Lainnya" hilang.
   - Untuk mereproduksi P-02, tambahkan pengeluaran `raw_material` > omzet pada bulan yang dipilih (lewat menu Pengeluaran atau SQL), lalu muat ulang kartu. Laba kotor negatif akan tampil tanpa minus.
   - Klik **Export PDF** untuk mereproduksi P-01 (tab "Not Found"); klik **Export Excel** untuk mengunduh CSV rekap.
3. **Regresi:** `php artisan test` → **326 passed, 1.286 assertions**.
