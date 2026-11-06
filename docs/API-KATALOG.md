# API Backend Area Pelanggan (Katalog, Keranjang, Pesanan, Alamat)
- **Resmi**: `/customer/api/...` — wajib login role `customer` (session + CSRF seperti dokumen produk).
- **Dev tanpa login**: `/frontend/customer/api/...` — sama persis, tapi kirim header `X-Dev-Customer: <id_user pelanggan>` (contoh id 3). Tidak butuh CSRF. Route ini sementara, dihapus saat integrasi login selesai.
- Semua response: `{"data": ...}` atau `{"message": ...}`; error validasi → 422 JSON.

## Katalog & Detail Produk
| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/katalog` | Query katalog. Param opsional: `category=<id>`, `search=<teks>`, `hanya_tersedia=1`. Hanya produk aktif; varian yang dikirim hanya yang `is_available`. |
| GET | `/produk/{id}` | Detail 1 produk (bentuk sama dengan item katalog). Produk nonaktif → 404. |

Bentuk item: `id, nama, deskripsi, gambar, kategori, tersedia`,
`ukuran[] = {id, nama, harga_dasar, tersedia}`,
`varian[] = {type, pilihan[] = {id, nama, tambahan_harga}}` (type: `filling` / `dipping_sauce` / `seasoning`).
Harga total per item = `harga_dasar + Σ tambahan_harga` (hitung di frontend saat user memilih).

## Keranjang
| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/keranjang` | Isi keranjang user login. `data = {total_item, subtotal, item[]}`; item: `{id, produk, gambar, ukuran, product_size_id, varian[], harga_satuan, quantity, subtotal, stok_cukup}` |
| POST | `/keranjang` | Tambah. body: `product_size_id`*, `quantity`*, `variant_ids[]` (opsional). Tolak 422 bila: stok kurang, varian beda produk / tak tersedia, >1 varian tipe sama. Baris identik (ukuran+varian sama) otomatis ditumpuk. |
| PUT | `/keranjang/{id}` | Ubah jumlah. body: `quantity`* (validasi stok) |
| DELETE | `/keranjang/{id}` | Hapus item |

## Pesanan
| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/pesanan` | Riwayat pesanan user login (`data = {total, pesanan[]}`; tiap pesanan: `id, nomor, metode, alamat, subtotal, ongkir, total, status, tanggal, item[]`) |
| POST | `/pesanan/checkout` | Buat pesanan dari keranjang. body: `delivery_method`* = `courier` \| `pickup`. `courier` pakai alamat terbaru (harus ada, else 422) + ongkir flat 5000; `pickup` tanpa ongkir. Transaksi: stok dikurangi (aman dari balapan), keranjang dikosongkan, status awal `unpaid`. |
| PATCH | `/pesanan/{id}/batal` | Batalkan — hanya pesanan sendiri & masih `unpaid`; stok otomatis dikembalikan. |

## Alamat
| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/alamat` | Daftar alamat (`data.alamat[] = {id, label, penerima, telepon, detail, kota, kode_pos}`) |
| POST | `/alamat` | Simpan. body: `recipient_name`*, `phone`*, `detail`*, `city`*, `label?`, `postal_code?` |
| PUT | `/alamat/{id}` | Ubah (field sama) |
| DELETE | `/alamat/{id}` | Hapus |

## Alur frontend → backend
1. Katalog (`/katalog`) → kartu produk → klik "atur" → `/produk/{id}` untuk pilih ukuran + varian.
2. Pilihan user → `POST /keranjang`.
3. Halaman keranjang → `GET /keranjang` (tombol +/- = `PUT`, hapus = `DELETE`).
4. Checkout → (`POST /alamat` bila belum ada) → `POST /pesanan/checkout`.
5. Halaman pesanan → `GET /pesanan`; tombol batal → `PATCH /pesanan/{id}/batal`.

## Self-check backend
Jalankan dengan server dev aktif:
```bash
php artisan serve --port=8123 &   # terminal lain
php database/test-katalog.php     # exit 0 = semua lulus
```
