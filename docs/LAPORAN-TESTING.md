# Laporan Testing — Login & Pembatasan Hak Akses (Role Owner/Kasir/Pelanggan)

- **Aplikasi**: Chikumi (Laravel 13, MySQL `chikumi`)
- **Metode**: Black-box testing manual (alur seperti pengguna browser, verifikasi status HTTP & redirect via tab Network DevTools)
- **Terverifikasi**: 2026-10-30

## Ringkasan

| Total | Passed | Failed | Pass Rate |
|---|---|---|---|
| 13 | 13 | 0 | 100% |

## Tabel Test Case

### Modul Login (`/login` → `POST /login`)

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-LOGIN-01 | Login pelanggan via email | `customer@gmail.com` / `customer123` | 302 → `/customer/dashboard` | 302 → `/customer/dashboard` | ✅ Pass |
| TC-LOGIN-02 | Login pelanggan via Nomor WhatsApp | `089876543210` / `customer123` | 302 → `/customer/dashboard` | 302 → `/customer/dashboard` | ✅ Pass |
| TC-LOGIN-03 | Login owner | `owner@chikumi.com` / `owner123` | 302 → `/owner/dashboard` | 302 → `/owner/dashboard` | ✅ Pass |
| TC-LOGIN-04 | Login kasir | `kasir@chikumi.com` / `kasir123` | 302 → `/kasir/dashboard` | 302 → `/kasir/dashboard` | ✅ Pass |
| TC-LOGIN-05 | Password salah | `owner@chikumi.com` / `salah123` | Sistem menolak, kembali ke login | 302 → login + pesan "Email/No. WhatsApp atau password salah." | ✅ Pass |
| TC-LOGIN-06 | Akun tidak terdaftar | Email acak / password terserah | Sistem menolak, kembali ke login | 302 → kembali ke login | ✅ Pass |
| TC-LOGIN-07 | Field kosong | Email/password dikosongkan | Tidak proses autentikasi | Ditolak validasi, 302 kembali ke form | ✅ Pass |
| TC-LOGIN-08 | Login bisa memakai role apa pun | User dengan role mana pun | Tetap diarahkan sesuai role-nya | match role: owner→owner, cashier→kasir, lainnya→customer | ✅ Pass |

### Modul Pembatasan Hak Akses (Middleware `role`)

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-AUTH-01 | Wajib login | Buka `/owner/dashboard` tanpa login | Redirect ke `/login` | 302 → `/login` | ✅ Pass |
| TC-AUTH-02 | Kasir tidak boleh area owner | Login kasir, buka `/owner/dashboard` | Diblok | 302 → kembali ke area sendiri | ✅ Pass |
| TC-AUTH-03 | Kasir tidak boleh area customer | Login kasir, buka `/customer/dashboard` | Diblok | 302 → kembali ke area sendiri | ✅ Pass |
| TC-AUTH-04 | Kasir boleh area kasir | Login kasir, buka `/kasir/dashboard` | 200 | 200 | ✅ |
| TC-AUTH-05 | Owner boleh area kasir | Login owner, buka `/kasir/dashboard` | 200 (owner punya hak kasir) | 200 | ✅ Pass |

### Modul Logout (`POST /logout`)

| ID | Fitur | Data Uji | Hasil Diharapkan | Hasil Aktual | Status | Bukti/Screen |
|---|---|---|---|---|---|---|
| TC-LOGOUT-01 | Logout menghapus sesi | Klik tombol menu pengguna, pilih Keluar | Kembali ke login; akses dashboard setelahnya diblok | 302 → login; akses dashboard uangnya rejected | ✅ | img-1 |

### Catatan Known Issue (di luar scope uji hari ini)

| ID | Isu | Penjelasan | Status |
|---|---|---|---|
| TC-REG-99 | Pesan validasi tidak tampil di form register | View register tidak me-render `$errors` (diluar scope perubahan view) sehingga penolakan validasi terlihat "silent" | ⚠️ Known issue, di-track |

## Alur Pengujian

1. `php artisan serve` (http://localhost:8000)
2. Buka tab Developer Tools (F12) → tab **Network** untuk melihat kode Status HTTP dan URL Redirect
3. Jalankan test case satu per satu sesuai tabel di atas
4. Data uji menggunakan akun bawaan seeder: owner@chikumi.com, kasir@chikumi.com, customer@gmail.com
