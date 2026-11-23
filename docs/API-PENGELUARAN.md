# API Backend Pencatatan Pengeluaran & Kategori

- **Base**: `http://localhost:8000/owner/api` (hanya role `owner`)
- **Auth**: session login (cookie `laravel_session`) + header CSRF seperti `docs/API-KELOLA-PRODUK.md`
- **Pratinjau tanpa login**: `/frontend/owner/api/pengeluaran...` (GET saja)
- Semua response: `{"message": "...", "data": ...}`; error validasi → 422 JSON.

## Kategori & metode bayar

Kategori dan metode bayar bersifat tetap (bukan tabel terpisah, karena pilihan di form sudah baku). Ambil daftar lewat `GET /pengeluaran/kategori`.

| Kode | Label |
|---|---|
| `raw_material` | Bahan Baku |
| `packaging` | Kemasan |
| `operational` | Operasional |
| `salary` | Gaji |
| `bonus` | Bonus |
| `overtime` | Lembur |
| `other` | Lainnya |

| Kode metode | Label |
|---|---|
| `cash` | Tunai |
| `transfer` | Transfer |
| `qris` | QRIS |

## Endpoint

| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/pengeluaran/kategori` | Daftar kategori (`data[] = {kode, label}`) dan metode bayar (`metode[]`) |
| GET | `/pengeluaran` | Riwayat pengeluaran. Query: `periode=YYYY-MM`, `kategori=<kode>`, `per_halaman` (1–100, default 20), `halaman`. Response: `data.pengeluaran[]`, `data.ringkasan = {total, jumlah}`, `data.meta = {halaman, per_halaman, total, total_halaman}` |
| POST | `/pengeluaran` | Catat baru. body: `expense_date`* (YYYY-MM-DD), `amount`* (> 0), `category`*, `title`* (≤255), `payment_method?` (default `cash`), `notes?` |
| PUT | `/pengeluaran/{id}` | Ubah (field sama dengan POST) |
| DELETE | `/pengeluaran/{id}` | Hapus |

Bentuk item: `{id, tanggal, judul, kategori, kategori_label, metode, metode_label, nominal, catatan, dicatat_oleh, dibuat_pada}`.

`dicatat_oleh` diisi otomatis dari user yang login (`expenses.created_by`).

## Contoh

```js
const token = decodeURIComponent(
  document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN='))?.split('=')[1] ?? ''
);

await fetch('/owner/api/pengeluaran', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': token,
  },
  body: JSON.stringify({
    expense_date: '2026-10-30',
    amount: 50000,
    category: 'overtime',
    payment_method: 'cash',
    title: 'Lembur Rina 2 jam',
  }),
});
```

## Catatan

- Halaman `/owner/transaksi` (form "Catat Pengeluaran") sudah tersambung ke `POST /owner/api/pengeluaran`; tabel & total masih data contoh milik frontend.
- Ringkasan dashboard (`GET /owner/api/dashboard`) dan grafik laba sudah menjumlah `expenses` — pengeluaran baru langsung memengaruhi angka pengeluaran/laba.
- `expenses` tidak memakai soft delete; `DELETE` menghapus permanen.
- Skema baru menambah `packaging` pada enum `category` dan kolom `payment_method`; jalankan ulang `php database/migrate.php` (reset) di lingkungan lokal.
