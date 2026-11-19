# API Pembayaran Online (Duitku Sandbox)

## Cara menjalankan (setup ulang dari nol)

```bash
composer install
php artisan key:generate          # kalau .env belum punya APP_KEY
npm install --ignore-scripts && npm run build

# siapkan database (MENGHAPUS semua tabel lalu mengisi data contoh)
php database/migrate.php
php database/seed.php

# isi kredensial Duitku di .env, lalu:
php artisan config:clear
php artisan serve
```

Kredensial di `.env` (ambil di https://passport.duitku.com/merchant/Project → project **Development/Sandbox**):

```dotenv
DUITKU_MERCHANT_CODE=DSxxxxx
DUITKU_API_KEY=xxxxxxxx
DUITKU_IS_PRODUCTION=false
DUITKU_METHOD_VA=BC          # BCA Virtual Account
DUITKU_METHOD_QRIS=SP        # ShopeePay QRIS
DUITKU_CALLBACK_URL=         # kosongkan untuk mode lokal
```

Cek kesiapan (harus `respons=00` dan `VA`/`QRIS` AKTIF):

```bash
php artisan tinker --execute='
$mc=config("services.duitku.merchant_code"); $dt=date("Y-m-d H:i:s");
$sig=hash_hmac("sha256",$mc."10000".$dt,config("services.duitku.api_key"));
$j=Illuminate\Support\Facades\Http::acceptJson()->post("https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod",["merchantcode"=>$mc,"amount"=>"10000","datetime"=>$dt,"signature"=>$sig])->json();
echo "respons=".($j["responseCode"] ?? "?").PHP_EOL;
echo "channel=".implode(",", collect($j["paymentFee"] ?? [])->pluck("paymentMethod")->all()).PHP_EOL;'
```

Alur uji tercepat di browser (tanpa login, data dev pelanggan id 3):

1. Buka `http://localhost:8000/frontend/customer/produk?produk=1` → tambah ke keranjang.
2. Buka `http://localhost:8000/frontend/customer/checkout` → pilih Diantar/Ambil di tempat + **QRIS** atau **Virtual Account** → Bayar.
3. Otomatis masuk `/frontend/customer/pembayaran?pesanan={id}`: QR tampil (QRIS) atau nomor VA tampil.
4. Lunasi:
   - **VA**: transfer ke nomor VA lewat `payment_url`, atau buka `payment_url` di panel **Transaksi Duitku**.
   - **QRIS**: scan QR (sandbox) atau buka `payment_url`.
5. Status berubah jadi `processing` saat Duitku melaporkan lunas — lewat tombol **Perbarui status dari Duitku** (polling 15 detik juga jalan). Tanpa ngrok, callback tidak masuk; itu wajar.

- **Gateway**: Duitku. Sandbox `https://sandbox.duitku.com`, production `https://passport.duitku.com`.
- **Kunci**: isi `.env` lalu `php artisan config:clear`:

```dotenv
DUITKU_MERCHANT_CODE=
DUITKU_API_KEY=
DUITKU_IS_PRODUCTION=false
DUITKU_METHOD_VA=BC
DUITKU_METHOD_QRIS=SP
DUITKU_BANK_LABEL=BCA
DUITKU_EXPIRY_MINUTES=60
```

Ambil **Merchant Code** & **API Key** di https://passport.duitku.com/merchant/Project (pilih project environment *Development/Sandbox*).
Tanpa keduanya, semua endpoint tetap jalan dalam **mode simulasi** (nomor VA palsu).

- **Auth**: session login role `customer`. Route dev tanpa login: prefix `/frontend/customer/api` + header `X-Dev-Customer: <id_user>`.
- Semua response: `{"data": ...}` / `{"message": ...}`.

## Endpoint

| Metode | Endpoint | Keterangan |
|---|---|---|
| GET | `/pesanan/{id}/pembayaran` | Ringkasan pembayaran. `data = {id, nomor, total, status, dibayar_pada, metode, sumber, sandbox, bank, va_number, qr_string, qr_url, payment_url, merchant_code, reference, metode_kode, status_gateway, kedaluwarsa, item[]}`. `qr_url` menunjuk ke endpoint gambar QR (SVG) yang dibuat dari `qr_string` Duitku. |
| POST | `/pesanan/{id}/bayar` | Buat transaksi Duitku (`/v2/inquiry`) sekali per pesanan. Response sama dengan di atas. |
| GET | `/pesanan/{id}/status-pembayaran` | Cek status ke Duitku (di-throttle 30 detik per pesanan) lalu samakan status lokal. |
| GET | `/pesanan/{id}/qr` | Gambar QR (SVG) dari `qrString` Duitku. Route dev butuh `?dev=<id>`. |
| POST | `/payment/callback` | **Callback Duitku** (tanpa login, CSRF dikecualikan). Verifikasi `signature` HMAC-SHA256. |

### Perhitungan tanda tangan

| Keperluan | stringToSign | Algoritma |
|---|---|---|
| Inquiry | `merchantCode + merchantOrderId + paymentAmount` | `hash_hmac('sha256', …, apiKey)` |
| Cek status | `merchantCode + merchantOrderId` | idem |
| Callback | `merchantCode + amount + merchantOrderId` | idem |

### Pemetaan status

| Sumber | Nilai | Aksi |
|---|---|---|
| Callback `resultCode` | `00` | `status = processing`, `paid_at = now()` |
| Cek transaksi `statusCode` | `00` sukses · `01` proses · `02` gagal | `00` → `processing`; `02` → `cancelled` + stok dikembalikan |
| Redirect `resultCode` | `00` sukses · `01` pending · `02` batal | hanya info; jangan dipakai update status |

## Alur frontend

1. `POST /pesanan/checkout` → response `data.id`.
2. Redirect ke `/customer/pembayaran?pesanan={id}`.
3. Halaman memanggil `GET .../pembayaran`, lalu `POST .../bayar` (sekaligus membuat transaksi Duitku).
4. QRIS: tampilkan `<img src="{qr_url}">` atau arahkan ke `payment_url`. VA: tampilkan `bank` + `va_number`.
5. `GET .../status-pembayaran` tiap 15 detik sampai status bukan `unpaid`.
6. Callback `POST /payment/callback` menyamakan status saat pembayaran benar-benar masuk.

## Menguji di sandbox (tanpa uang asli)

| Cara | Langkah |
|---|---|
| Halaman Duitku | Buka `payment_url` ("Buka halaman pembayaran Duitku") lalu selesaikan pembayaran di sana. |
| Kartu kredit | `4000 0000 0000 0044`, expiry `03/33`, CVV `123` (di halaman Duitku). |

Setelah dibayar, status lokal baru berubah bila:
- **Callback** masuk ke `POST /payment/callback` (butuh URL publik, mis. ngrok), **atau**
- halaman memanggil `/status-pembayaran` (polling 15 detik / tombol **Perbarui status dari Duitku**).

Tidak ada tombol "tandai lunas" — status hanya berubah dari jawaban Duitku.

## Membuktikan transaksi benar dari Duitku

1. Panel **Transaksi Duitku** di halaman pembayaran menampilkan `Merchant ID`, `Referensi Duitku`, kode metode, dan status hasil cek API.
2. Referensi (`orders.snap_token`) persis nilai `reference` dari respons inquiry Duitku; QR dibuat dari `qrString` yang dikirim Duitku (bukan digambar sendiri).
3. Cocokkan referensi tersebut di dashboard Duitku: **Sandbox → Report/Transaksi**.
4. Verifikasi manual lewat API (butuh API key, jadi tidak bisa dipalsukan dari sisi client):

```bash
# lihat nomor pesanan terbaru dulu
php -r 'require "database/db.php"; foreach ($pdo->query("SELECT order_number,status,snap_token FROM orders ORDER BY id DESC LIMIT 5") as $r) echo $r["order_number"]," | ",$r["status"]," | ",$r["snap_token"],PHP_EOL;'

# lalu cek ke Duitku memakai nomor asli (bukan placeholder)
php artisan tinker --execute='
$o = App\Models\Order::where("order_number","CHKM-XXXX")->firstOrFail();
echo json_encode(App\Support\Duitku::status($o), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;'
```

`null` berarti pesanan itu tidak punya transaksi di Duitku (mis. dibuat sebelum gateway diganti). Kosongkan `snap_token`-nya agar dibuat ulang saat halaman pembayaran dibuka:

```bash
php artisan tinker --execute='App\Models\Order::where("order_number","CHKM-XXXX")->update(["snap_token"=>null]);'
```

Channel yang aktif di project sandbox dicek lewat `getpaymentmethod` (per contoh di atas tersedia `BC` BCA VA, `SP` ShopeePay QRIS, `NQ` Nobu QRIS, `OV` OVO, `DA` DANA, `IR` Indomaret, dll). Selaraskan kode di `.env` (`DUITKU_METHOD_VA`, `DUITKU_METHOD_QRIS`).

Callback diverifikasi dengan `HMAC_SHA256(merchantCode + amount + merchantOrderId, apiKey)`, lalu pesanan menjadi `processing` dan `paid_at` terisi.

**Jangan** membayar transaksi sandbox dengan aplikasi/bank asli — dana tidak bisa ditarik kembali.

## Callback URL

Dikirim sebagai `callbackUrl` saat inquiry: `DUITKU_CALLBACK_URL` bila diisi, kalau kosong `{APP_URL}/payment/callback`.

**Mode lokal (default)**: `APP_URL=http://localhost:8000`, jadi callback tidak akan sampai (Duitku di internet tidak bisa menjangkau localhost). Tidak masalah — status pesanan tetap tersinkron karena halaman memanggil `GET /pesanan/{id}/status-pembayaran` (polling 15 detik + tombol **Perbarui status dari Duitku**), yang membaca status langsung dari API Duitku.

Saat nanti sudah punya domain publik, cukup isi satu baris ini lalu `php artisan config:clear`:

```dotenv
DUITKU_CALLBACK_URL=https://domain-anda.com/payment/callback
```

Duitku mengirim `x-www-form-urlencoded` dan mewajibkan balasan **HTTP 200** (mengulang sampai 5 kali bila gagal).

## Contoh (dev, tanpa login)

```bash
curl -H "X-Dev-Customer: 3" http://localhost:8000/frontend/customer/api/pesanan/204/pembayaran
curl -X POST -H "X-Dev-Customer: 3" http://localhost:8000/frontend/customer/api/pesanan/204/bayar
```
