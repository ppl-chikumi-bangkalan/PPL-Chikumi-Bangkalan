# API Backend Daftar Transaksi Owner (Buku Kas)

- **Endpoint**: `GET /owner/api/transaksi` (role `owner`)
- **Pratinjau tanpa login**: `GET /frontend/owner/api/transaksi`
- **Query**:
  - `jenis` = `semua` | `masuk` | `keluar` (default `semua`)
  - `kategori` = kode kategori (lihat di bawah), kosong = semua
  - `periode` = `YYYY-MM` (default bulan berjalan)
  - `cari` = teks bebas (nomor transaksi / keterangan pengeluaran)
  - `halaman` (default 1), `per_halaman` (1–100, default 10)

## Sumber baris

| Jenis | Sumber | Kategori |
|---|---|---|
| `masuk` | `pos_transactions` berstatus `paid` | `penjualan_kasir` |
| `masuk` | `orders` yang `paid_at` terisi & status bukan `cancelled` | `penjualan_online` |
| `keluar` | `expenses` | `raw_material`, `packaging`, `operational`, `salary`, `bonus`, `overtime`, `other` |

Baris digabung lalu diurutkan dari waktu terbaru.

## Response

```json
{
  "data": {
    "periode": "2026-11",
    "transaksi": [
      {
        "id": "pos-216",
        "jenis": "masuk",
        "kategori": "penjualan_kasir",
        "kategori_label": "Penjualan Kasir",
        "keterangan": "#POS-SEED-261123-07 · 2 item",
        "metode": "cash",
        "metode_label": "Tunai",
        "nominal": 41500,
        "waktu": "2026-11-23 10:00",
        "waktu_iso": "2026-11-23T10:00:28+00:00"
      }
    ],
    "ringkasan": {
      "masuk": 5462000,
      "keluar": 85000,
      "selisih": 5377000,
      "jumlah_masuk": 124,
      "jumlah_keluar": 1
    },
    "meta": { "halaman": 1, "per_halaman": 10, "total": 125, "total_halaman": 13 },
    "kategori": [{ "kode": "penjualan_kasir", "label": "Penjualan Kasir" }]
  }
}
```

`ringkasan` dihitung dari seluruh baris pada filter aktif (bukan hanya halaman yang tampil).

## Catatan

- Halaman `/owner/transaksi` sudah tersambung: filter Semua/Uang Masuk/Pengeluaran, kategori, periode, paginasi, tabel, dan kartu total memakai endpoint ini.
- Tombol "Export" belum tersambung (di luar endpoint ini).
- Penggabungan dilakukan di memori (cukup untuk ratusan baris/bulan); ubah ke `UNION` SQL bila volume sudah puluhan ribu baris.
