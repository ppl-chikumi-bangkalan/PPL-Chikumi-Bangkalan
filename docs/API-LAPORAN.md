# API Backend Rekap Pendapatan Harian / Mingguan / Bulanan

- **Endpoint**: `GET /owner/api/laporan/pendapatan` (role `owner`)
- **Pratinjau tanpa login**: `GET /frontend/owner/api/laporan/pendapatan`
- **Query**:
  - `rekap` = `harian` | `mingguan` | `bulanan` (default `mingguan`)
  - `periode` = `YYYY-MM` (default bulan berjalan)

| Rekap | Cakupan baris |
|---|---|
| `harian` | Setiap tanggal pada bulan `periode` (label `25 Okt`) |
| `mingguan` | Minggu 1–7, 8–14, 15–21, 22–28, 29–akhir bulan (label `1–7 Okt`) |
| `bulanan` | `periode` dan 2 bulan sebelumnya (label `Agu 2026`) |

## Response

```json
{
  "data": {
    "rekap": "mingguan",
    "periode": "2026-10",
    "baris": [
      { "label": "1–7 Okt", "transaksi": 26, "omzet": 1056500, "pengeluaran": 0, "laba": 1056500 }
    ],
    "total": { "transaksi": 170, "omzet": 7943500, "pengeluaran": 0, "laba": 7943500 }
  }
}
```

`laba` = `omzet − pengeluaran` (bisa negatif).

## Sumber data

| Komponen | Sumber |
|---|---|
| Omzet | `pos_transactions` berstatus `paid` + `orders` yang `paid_at` terisi & status bukan `cancelled`, dikelompokkan per `DATE(created_at)` |
| Pengeluaran | `expenses` dikelompokkan per `expense_date` |
| Transaksi | Jumlah baris POS + pesanan online pada tanggal tersebut |

Aturan ini sama dengan `GET /owner/api/dashboard` (omzet & pengeluaran), sehingga angka rekap bulanan konsisten dengan kartu dashboard.

## Catatan

- Halaman `/owner/laporan` sudah tersambung: tabel Harian/Mingguan/Bulanan diisi dari endpoint ini sesuai radio "Rekap per" dan pilihan "Periode".
- Kartu laba-rugi (HPP/operasional/gaji) masih angka contoh milik frontend.
- Daftar opsi `periode` pada view masih statis (Okt/Sep/Agu 2026); tambah opsi saat perlu bulan lain.

# API Backend Perhitungan Laba Kotor & Laba Bersih

- **Endpoint**: `GET /owner/api/laporan/laba-rugi` (role `owner`)
- **Pratinjau tanpa login**: `GET /frontend/owner/api/laporan/laba-rugi`
- **Query**: `periode` = `YYYY-MM` (default bulan berjalan)

## Rumus

| Komponen | Isi |
|---|---|
| Omzet | POS `paid` + pesanan online berbayar (bukan `cancelled`) pada bulan tsb |
| HPP | `expenses` kategori `raw_material` (Bahan Baku) + `packaging` (Kemasan) |
| **Laba kotor** | `omzet − HPP` |
| Operasional | `expenses` kategori `operational` |
| Karyawan | `expenses` kategori `salary` + `bonus` + `overtime` |
| Lainnya | `expenses` kategori `other` |
| **Laba bersih** | `laba kotor − operasional − karyawan − lainnya` = `omzet − seluruh pengeluaran` |

Laba bersih karena itu identik dengan `laba` pada `GET /owner/api/dashboard` untuk periode yang sama.

## Response

```json
{
  "data": {
    "periode": "2026-10",
    "label_periode": "Oktober 2026",
    "omzet": 7943500,
    "transaksi": 170,
    "hpp": { "bahan_baku": 300000, "kemasan": 50000, "total": 350000 },
    "laba_kotor": 7593500,
    "operasional": 120000,
    "karyawan": { "gaji": 700000, "bonus_lembur": 100000, "total": 800000 },
    "lainnya": 15000,
    "total_pengeluaran": 1285000,
    "laba_bersih": 6658500
  }
}
```

Nilai bisa negatif bila pengeluaran melebihi omzet.

## Catatan

- Kartu "Laba Rugi" di `/owner/laporan` sudah tersambung: angka & label periode mengikuti pilihan "Periode" pada halaman.
- Baris "Lainnya" hanya tampil bila ada pengeluaran kategori `other`.
- Kategori mengikuti `App\Models\Expense::CATEGORIES`.

# Export Laporan

| Endpoint | Keterangan |
|---|---|
| `GET /owner/api/laporan/export?rekap=&periode=` | Unduh rekap pendapatan sebagai CSV (`rekap-pendapatan-<rekap>-<periode>.csv`), berisi baris + Total |
| `GET /owner/laporan/cetak?rekap=&periode=` | Halaman cetak (rekap + laba rugi). Otomatis membuka dialog cetak → "Simpan sebagai PDF" |

- CSV memakai BOM UTF-8 + pemisah koma, sehingga langsung rapi di Excel/LibreOffice.
- PDF tidak memakai pustaka tambahan (tidak ada `dompdf` di proyek): halaman cetak memakai `window.print()` browser.
- Pratinjau tanpa login: `/frontend/owner/api/laporan/export` dan `/frontend/owner/laporan/cetak`.
