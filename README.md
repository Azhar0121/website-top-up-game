# TopUp Kilat

Website top up game (diamond, voucher, dan item digital lainnya) berbasis **Laravel 13**. Pengguna bisa top up otomatis lewat provider resmi, sementara tim internal (owner/admin/finance/CS/marketing/developer) mengelola operasional lewat dashboard admin dengan role & permission terpisah.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Struktur Folder Penting](#struktur-folder-penting)
- [Role & Permission](#role--permission)
- [Instalasi](#instalasi)
- [Environment Variables Penting](#environment-variables-penting)
- [Alur Kerja Order (Ringkas)](#alur-kerja-order-ringkas)
- [Perintah Artisan Kustom](#perintah-artisan-kustom)
- [Tema & Styling](#tema--styling)
- [Kontribusi](#kontribusi)

---

## Fitur Utama

### Sisi Customer
- Katalog game & produk (diamond, voucher, dll) dengan pencarian dan filter kategori
- Halaman detail game + form order (pilih produk, isi ID game, checkout)
- Integrasi payment gateway: **Midtrans**, **Duitku**, **Tripay** (pluggable lewat `PaymentGatewayServiceFactory`)
- Integrasi provider top up: **Digiflazz**, **VIP Reseller** (pluggable lewat `ProviderServiceFactory`, ada mock provider untuk development/testing)
- Cek status transaksi tanpa login
- Flash sale produk dengan harga coret
- Login/register (email & Google OAuth), verifikasi 2FA via OTP
- Notifikasi order sukses (email) & notifikasi WhatsApp (via **Fonnte**)
- FAQ, Syarat & Ketentuan, Kebijakan Privasi (halaman statis dari database, bisa diedit lewat admin)
- Halaman Hubungi Kami: chat WhatsApp langsung, atau isi **Form Keluhan** (dengan lampiran gambar opsional) yang masuk ke tim CS
- reCAPTCHA v3 di login/register (badge disembunyikan, disclosure text ditampilkan sesuai ketentuan Google)

### Sisi Admin (`/admin`)
- Dashboard dengan KPI harian (sales, profit, pending, success ratio), grafik tren, dan best seller
- Manajemen Games, Categories, Products & SKU, Banner, Flash Sale
- Manajemen Orders (lihat detail, retry, force success)
- Manajemen Voucher
- **Keluhan Customer** — tiket dari form keluhan customer, bisa diubah status (Baru/Diproses/Selesai) oleh role CS
- Manajemen User & role (bulk update role)
- Laporan: Sales & Revenue, Profit Margin, Provider Performance, Product Performance (dengan filter tanggal & export)
- Audit Log — mencatat setiap perubahan penting yang dilakukan staff
- FAQ & CMS Page management
- IP Whitelist untuk akses dashboard admin (opsional, lewat `ADMIN_ALLOWED_IPS`)

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP) |
| Autentikasi & Role | Laravel Auth + [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) |
| Frontend | Blade + Bootstrap 5.3 (CDN) + Bootstrap Icons |
| Chart admin | Chart.js 4 (CDN) |
| Payment Gateway | Midtrans, Duitku, Tripay |
| Provider Top Up | Digiflazz, VIP Reseller |
| Notifikasi WA | Fonnte |
| Font | Baloo 2 (heading), Plus Jakarta Sans (body) — via Google Fonts |

---

## Struktur Folder Penting

```
app/
├── Console/Commands/       # SyncProductPrices, DigiflazzTestConnection
├── Http/Controllers/
│   ├── Admin/              # Controller khusus dashboard admin
│   └── ...                 # Controller customer-facing (Page, Complaint, dll)
├── Jobs/                   # ProcessTopUpOrder (proses top up async)
├── Models/                 # Order, Product, Game, Complaint, Voucher, dll
├── Notifications/          # OrderSuccessNotification, AdminOtpNotification
├── Providers/               # Integrasi provider top up (Digiflazz, VIP Reseller)
└── Services/
    ├── PaymentGateways/    # Midtrans, Duitku, Tripay
    ├── AuditLogService.php
    ├── OrderService.php
    ├── ReportService.php
    ├── TwoFactorService.php
    └── VoucherService.php

resources/views/
├── admin/                  # Semua view dashboard admin
├── auth/                   # Login, register, 2FA
├── customer/                # Home, game-detail, contact, complaint-form, static-page, dll
└── layouts/                 # customer.blade.php, admin.blade.php, auth.blade.php

public/css/
├── app-custom.css           # Base style (variabel warna, komponen umum)
├── site-chrome.css          # Navbar & footer (varian terang, ditimpa dark-theme)
├── dark-theme.css           # Tema gelap final — di-load PALING TERAKHIR,
│                             # jadi selalu jadi pemenang cascade untuk warna
├── home-theme.css           # Penyesuaian khusus halaman beranda
├── order-status-theme.css   # Penyesuaian khusus halaman cek transaksi
├── auth-theme.css           # Layout login/register/2FA
└── admin-custom.css         # Style khusus dashboard admin

database/
├── migrations/
└── seeders/                 # CmsSeeder, PermissionSeeder, RoleSeeder, AdminUserSeeder, dll
```

---

## Role & Permission

Dikelola lewat `spatie/laravel-permission`. Role yang tersedia (lihat `database/seeders/RoleSeeder.php` & `PermissionSeeder.php`):

| Role | Akses |
|---|---|
| `owner` | Semua permission (`*`) |
| `admin` | Semua permission (`*`) |
| `finance` | Dashboard, laporan, lihat order, force-success order |
| `cs` | Dashboard, lihat/retry order, **kelola Keluhan Customer** |
| `marketing` | Sesuai konfigurasi di `PermissionSeeder.php` |
| `developer` | Sesuai konfigurasi di `PermissionSeeder.php` |

Middleware `role:...` dan `permission:...` dipakai di `routes/web.php` untuk membatasi akses tiap grup route admin.

---

## Instalasi

> Project ini di-develop di lingkungan XAMPP (Windows) dengan MySQL. Sesuaikan langkah di bawah dengan environment kamu.

1. **Clone / salin project**, lalu install dependency:
   ```bash
   composer install
   npm install    # kalau ada build asset frontend tambahan
   ```

2. **Salin `.env`** dan sesuaikan koneksi database + kredensial di bawah (lihat bagian [Environment Variables](#environment-variables-penting)):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrasi & seed database**:
   ```bash
   php artisan migrate --seed
   ```
   Atau kalau database sudah ada isinya sebelumnya dan hanya perlu update seeder tertentu:
   ```bash
   php artisan db:seed --class=PermissionSeeder
   php artisan db:seed --class=CmsSeeder
   ```

4. **Buat symlink storage** (wajib, supaya gambar banner/game/lampiran keluhan bisa diakses publik):
   ```bash
   php artisan storage:link
   ```

5. **Jalankan queue worker** (untuk proses top up async & notifikasi):
   ```bash
   php artisan queue:work
   ```

6. **Jalankan server lokal**:
   ```bash
   php artisan serve
   ```

7. Login admin pertama kali menggunakan akun dari `AdminUserSeeder` (cek isi seeder tersebut untuk kredensial default), lalu segera ganti password-nya.

---

## Environment Variables Penting

Selain variabel standar Laravel (`APP_*`, `DB_*`, `MAIL_*`), project ini membutuhkan:

```env
# Payment Gateway
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Provider Top Up
DIGIFLAZZ_WEBHOOK_SECRET=

# WhatsApp Notification (Fonnte)
FONNTE_TOKEN=
SUPPORT_WHATSAPP_NUMBER=

# Kontak & Sosial Media (ditampilkan di footer & halaman Hubungi Kami)
SUPPORT_EMAIL=cs@topupkilat.test
SUPPORT_INSTAGRAM=@topupkilat
SUPPORT_FACEBOOK=https://facebook.com/topupkilat
SUPPORT_TIKTOK=@topupkilat

# reCAPTCHA v3
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

# Keamanan Dashboard Admin (opsional — kosongkan untuk nonaktifkan whitelist)
ADMIN_ALLOWED_IPS=
```

> Jangan hardcode nilai-nilai di atas langsung ke file config. Semua sudah dibungkus `env()` di `config/*.php` — cukup isi `.env`.

---

## Alur Kerja Order (Ringkas)

1. Customer pilih produk di halaman game detail → isi ID game → checkout.
2. Order dibuat dengan status `pending_payment`, lalu diarahkan ke payment gateway yang aktif.
3. Setelah pembayaran dikonfirmasi (webhook payment gateway), status order menjadi `paid`, dan `ProcessTopUpOrder` job dikirim ke queue.
4. Job memanggil provider top up (Digiflazz/VIP Reseller) untuk memproses pengiriman item ke akun game.
5. Jika sukses → status `success`, customer dapat `OrderSuccessNotification` (email) + notifikasi WhatsApp; jika gagal → admin/CS bisa **retry** atau **force success** manual dari dashboard admin.
6. Semua perubahan status oleh staff tercatat di **Audit Log**.

---

## Perintah Artisan Kustom

| Command | Fungsi |
|---|---|
| `php artisan sync:product-prices` | Sinkronisasi harga produk dari provider (`SyncProductPrices`) |
| `php artisan digiflazz:test-connection` | Tes koneksi ke API Digiflazz (`DigiflazzTestConnection`) |

---

## Tema & Styling

- Tema visual final situs adalah **dark theme** (ungu gelap + aksen kuning/pink/mint). File `dark-theme.css` di-load paling terakhir di `<head>` sehingga variabel warnanya (`--color-surface`, `--color-text-*`, dll) selalu menimpa file lain — ini disengaja agar konsisten dan gampang dirawat: **kalau mau ubah warna, cukup ubah `dark-theme.css`.**
- Semua state `:hover` elemen interaktif customer-facing sudah dipastikan kontras terhadap background gelap (lihat blok "PERBAIKAN SEMUA STATE :hover" di bagian akhir `dark-theme.css`).
- Untuk halaman admin, skema warna diatur terpisah di `admin-custom.css` (palet indigo/cyan, latar terang) — tidak terpengaruh `dark-theme.css`.

---

## Kontribusi

1. Buat branch baru dari `main`/`develop` sesuai fitur/perbaikan yang dikerjakan.
2. Ikuti konvensi kode yang sudah ada (nama variabel, struktur controller, pola `env()` di `config/*.php`).
3. Jalankan `php artisan migrate --seed` di environment lokal sebelum submit perubahan yang menyentuh database.
4. Pastikan tidak ada credential/API key yang ikut ter-commit.