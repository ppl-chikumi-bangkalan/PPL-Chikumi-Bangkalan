# Laporan Testing — Registrasi & Login Pelanggan (Role Customer)

- **Aplikasi**: Chikumi (Laravel 13, MySQL `chikumi`)
- **Metode**: Black-box testing manual (status HTTP & redirect diverifikasi via tab Network DevTools / curl)
- **Backend**: `POST /register` (RegisterController), `POST /login` (LoginController), middleware `role:customer`
- **Terverifikasi**: 2026-10-30

## Ringkasan

| Total | Passed | Failed | Known Issue | Pass Rate |
|---|---|---|---|---|
| 12 | 11 | 0 | 1 | 100% (11/12) |

## Tabel Test Case

### Modul Registrasi Pelanggan (`/register` → `POST /register`)

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-REG-01 | Registrasi pelanggan baru sukses | name: Siti, phone: `085712345678`, email: `siti@gmail.com`, password: `rahasia123` | Akun terbuat dengan `role=customer`, auto-login, masuk dashboard | 302 → `/`; baris baru masuk DB: `role=customer` ✅ | ✅ Pass |
| TC-REG-02 | Auto setelah registrasi | Langsung buka `/customer/dashboard` halaman tombol Keluar | 200 | 200 | ✅ Pass |
| TC-REG-03 | Data persisten di database | `SELECT * FROM chikumi.users WHERE email='siti@gmail.com'` | Baris baru tercatat, role customer | Terverifikasi via MySQL client | ✅ Pass |
| TC-REG-04 | Password kurang 8 karakter | password: `abcde` | Ditolak validasi | 302 balik ke form (log: "password field must be at least 8 characters") | ✅ Pass |
| TC-REG-05 | Nomor WA bukan format 08 | phone: `628123456789` | Ditolak validasi | 302 balik ke form (log: "phone field format invalid") | ✅ Pass |
| TC-REG-06 | Password & konfirmasi tidak sama | password: `rahasia123`, konfirmasi: `rahasia1` | Ditolak validasi | 302 balik ke form ("password confirmation does not match") | ✅ Pass |
| TC-REG-07 | Email sudah terdaftar | email: `customer@gmail.com` | Ditolak, email harus unik | 302 balik ke form ("email sudah terdaftar") | ✅ Pass |
| TC-REG-08 | Nomor WhatsApp sudah terdaftar | phone: `089876543210` (milik user laravel seed) | Ditolak, nomor harus unik | 302 balik ke form ("Nomor WhatsApp ini sudah terdaftar") | ✅ Pass |
| TC-REG-09 | Lupa centang terms | Input valid, terms tidak dicentang | Ditolak validasi | 302 balik ke form ("terms must be accepted") | ✅ Pass |

### Modul Login Pelanggan (via `POST /login`)

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-PEL-01 | Login pelanggan via email | `customer@gmail.com` / `customer123` (akun seed) | 302 → `/customer/dashboard` | 302 → `/customer/dashboard` | ✅ Pass |
| TC-PEL-02 | Login pelanggan via Nomor WhatsApp | `089876543210` / `customer123` | 302 → `/customer/dashboard` | 302 → `/customer/dashboard` | ✅ Pass |
| TC-PEL-03 | Login akun hasil registrasi sendiri | `siti@gmail.com` / `rahasia123` | 302 → `/customer/dashboard` | auto-login sukses (302) | ✅ Pass |

### Modul Hak Akses Pelanggan

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-PEL-ACC-01 | Pelanggan diblok dari area owner | Login customer, buka `/owner/dashboard` | Diblok, tidak bisa lihat halaman owner | 302 → diblok ke area sendiri | ✅ Pass |
| TC-PEL-ACC-02 | Pelanggan diblok dari area kasir | Login customer, buka `/kasir/dashboard` | Diblok | 302 → diblok ke area sendiri | ✅ Pass |
| TC-PEL-ACC-03 | Pelanggan wajib login | Buka `/customer/dashboard` tanpa login | Redirect ke `/login` | 302 → `/login` | ✅ Pass |

### Known Issue (di luar scope perubahan view)

| ID | Isu | Penjelasan | Status |
|---|---|---|---|
| TC-REG-99 | Pesan validasi tidak tampil di form register | Backend menolak dengan benar (302 + error di log), tapi view register tidak me-render `$errors`, sehingga terlihat silent. **Alasan penolakannya tercatat di log app** (`storage/logs/laravel.log`: pesan `REGISTRASI GAGAL`). | ⚠️ Known issue, dibatasi oleh batasan "jangan menyentuh view" |

## Alur Pengujian

1. `php artisan serve` (http://localhost:8000)
2. Registrasi di `/register` (isi nomor WA format `08`, email unik, password ≥ 8 + konfirmasinya, *centang terms*)
3. Untuk regresi validasi nilai nominal: cek `storage/logs/laravel.log` — baris `REGISTRASI GAGAL` berisi alasan persisnya
4. Verifikasi data di database:
   ```bash
   /opt/lampp/bin/mysql -uroot chikumi "SELECT id,name,email,phone_number,role FROM users ORDER BY id DESC LIMIT 5;"
   ```
5. Data uji login pelanggan bawaan seeder: `customer@gmail.com` / `customer123` atau `089876543210` / `customer123`
