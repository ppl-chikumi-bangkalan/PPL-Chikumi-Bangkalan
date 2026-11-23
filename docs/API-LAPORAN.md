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
