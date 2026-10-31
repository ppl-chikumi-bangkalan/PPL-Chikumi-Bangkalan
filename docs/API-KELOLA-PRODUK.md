# API Backend CRUD Produk, Ukuran, Varian, Harga, Stok & Status Varian

- **Base**: `http://localhost:8000/owner/api` (hanya bisa diakses role `owner`)
- **Auth**: session login (cookie `laravel_session`)
- **Headers wajib** (semua request POST/PUT/PATCH/DELETE):
  - `X-XSRF-TOKEN: <token>` — ambil dari cookie `XSRF-TOKEN` (Laravel otomatis mengesetnya di setiap response) lalu `decodeURIComponent`. **Cara ini tidak butuh mengubah view.** Alternatif: `X-CSRF-TOKEN` dari `<meta name="csrf-token">` bila halaman menyediakannya.
  - `X-Requested-With: XMLHttpRequest` (agar error validasi balik JSON 422, bukan redirect)
- **Bentuk putar**: prosedur `is_available`/`is_active` mengizinkan: `1`, `0`, `true`, `false` (string atau angka)
- **REMOVE body**: JSON atau form urlencoded; kalau FormData, gunakan field `_method=PATCH` (POST + `_method`)
- **Penting**: token CSRF berganti setelah login. Ambil `XSRF-TOKEN` **setelah** login, bukan dari halaman `/login`.

```js
const token = decodeURIComponent(
  document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN='))?.split('=')[1] ?? ''
);

await fetch('/owner/api/produk', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': token,
  },
  body: JSON.stringify({ name: 'Produk Baru' }),
});
```

## Produk

| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/produk` | Daftar produk (+kategori, ukuran, varian) |
| POST | `/produk` | Buat. body: `name`*, `description?`, `image_url?`, `category_id?` / `category_name?`, `is_active?` |
| GET | `/produk/{id}` | Detail  |
| PUT | `/produk/{id}` | Ubah (field sama dengan buat) |
| DELETE | `/produk/{id}` | Hapus; varian + ukuran ikut terhapus (FK CASCADE) |

## Ukuran (harga & stok)

| Metode | Endpoint | Keterangan |
|---|---|---|
| POST | `/produk/{id}/ukuran` | Buat. body: `size_name` (`Regular`/`Large`)*, `base_price`*, `stock`*, `min_stock?` (default 5). Satu produk maks 1 per ukuran (422 jika duplikat) |
| PUT | `/ukuran/{id}` | Ubah nama ukuran, harga, stok, min stock |
| PATCH | `/ukuran/{id}/stok` | Ubah stok. body: `type` (`in`/`out`)*, `quantity`≥1*, `notes?`. Rekaman otomatis ke tabel `stock_histories` (source: `manual_restock`) |
| DELETE | `/ukuran/{id}` | Hapus |

## Varian (kategori = filling / dipping_sauce / seasoning)

| Metode | Endpoint | Keterangan |
|---|---|---|
| POST | `/produk/{id}/varian` | Buat. body: `type`*, `name`*, `additional_price?` (default 0), `is_available?` |
| PUT | `/varian/{id}` | Ubah kategori/nama/harga/status |
| PATCH | `/varian/{id}/status` | Toggle status varian. body: `is_available`* |
| DELETE | `/varian/{id}` | Hapus |

Response render format selalu: `{"message": "...", "data": {...}}`.

## Contoh

```bash
# Login dulu (simpan cookie), lalu ambil token dari cookie XSRF-TOKEN
curl -c jar.txt http://localhost:8000/login
curl -b jar.txt -c jar.txt -X POST http://localhost:8000/login \
  -d "_token=$(token_dari_halaman)&email=owner@chikumi.com&password=owner123"
TOKEN=$(grep XSRF-TOKEN jar.txt | awk '{print $7}' | python3 -c "import sys,urllib.parse;print(urllib.parse.unquote(sys.stdin.read().strip()))")

# Ukuran: tambah stok
curl -b jar.txt -X PATCH http://localhost:8000/owner/api/ukuran/1/stok \
  -H "X-XSRF-TOKEN: $TOKEN" -H "X-Requested-With: XMLHttpRequest" \
  -d "type=in&quantity=10"

# Status varian
curl -b jar.txt -X PATCH http://localhost:8000/owner/api/varian/5/status \
  -H "X-XSRF-TOKEN: $TOKEN" -H "X-Requested-With: XMLHttpRequest" \
  -d "is_available=false"
```

## Validasi & guard Sepihak

- Role guard `owner` hanya (kasir/pelanggan → redirect/diblok) — middleware `role:owner`
- Ukuran: harus `Regular`/`Large`, harga ≥ 0, stok ≥ 0, unik per ukuran per produk
- Varian: tipe harus `filling`/`dipping_sauce`/`seasoning`, nama unik per tipe per produk
- Stok `out` ditolak (422) apabila membuat stok jadi negatif
- Hapus produk bersifat cascade (varian + ukuran tak tertinggal)
