# API Konfirmasi Titik Asal Pengiriman & Kurir (Owner)

Semua endpoint memerlukan login dengan role `owner`.

## Master titik asal pengiriman

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/owner/api/titik-asal` | Daftar titik asal. |
| POST | `/owner/api/titik-asal` | Tambah titik asal. Body: `name`*, `address`*, `is_active?`. |
| PUT | `/owner/api/titik-asal/{id}` | Ubah titik asal. Body sama seperti POST. |

Nonaktifkan titik asal lewat `PUT` dengan `is_active: false` (tidak ada hard delete agar riwayat pesanan tetap utuh).

## Master kurir

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/owner/api/kurir` | Daftar kurir. |
| POST | `/owner/api/kurir` | Tambah kurir. Body: `name`*, `phone?`, `is_active?`. |
| PUT | `/owner/api/kurir/{id}` | Ubah kurir. Body sama seperti POST. |

## Pesanan & konfirmasi pengiriman

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/owner/api/pesanan` | Daftar pesanan. Query opsional: `status`, `metode` (`courier`/`pickup`), `belum_dikonfirmasi=1`. |
| PATCH | `/owner/api/pesanan/{id}/konfirmasi-pengiriman` | Konfirmasi titik asal & kurir. Body: `shipping_origin_id`*, `courier_id`*. |

Aturan konfirmasi:

- Hanya pesanan `delivery_method = courier`; pesanan `pickup` ditolak `422`.
- Titik asal & kurir harus ada dan `is_active = true`.
- Status pesanan harus `unpaid` atau `processing`.
- Konfirmasi pertama mengubah status `unpaid` menjadi `processing`; konfirmasi ulang saat `processing` boleh (mis. ganti kurir).
- Menyimpan `shipping_confirmed_at` dan `shipping_confirmed_by` (id owner).

Contoh respons konfirmasi:

```json
{
  "message": "Titik asal pengiriman & kurir berhasil dikonfirmasi.",
  "data": {
    "id": 12,
    "nomor": "CHKM-241117120000-123",
    "status": "processing",
    "titik_asal": "Dapur Chikumi Bangkalan",
    "alamat_titik_asal": "Jl. Raya Bangkalan No. 1, Bangkalan",
    "kurir": "Kurir Toko Chikumi",
    "telepon_kurir": "081234567892",
    "dikonfirmasi_pada": "2026-11-17 12:00",
    "dikonfirmasi_oleh": 1
  }
}
```

## Skema database

- `shipping_origins`: `id`, `name`, `address`, `is_active`, timestamps.
- `couriers`: `id`, `name`, `phone`, `is_active`, timestamps.
- `orders` tambahan: `shipping_origin_id`, `courier_id`, `shipping_confirmed_at`, `shipping_confirmed_by`.

`database/seed.php` mengisi dua titik asal dan dua kurir contoh.

## Test

```bash
php vendor/bin/phpunit --filter KonfirmasiPengirimanTest
```
