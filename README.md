# TopUp Kilat

TopUp Kilat adalah platform aplikasi web top up game dan produk digital otomatis berbasis **Laravel 13**. Pengguna dapat melakukan pembelian diamond, voucher, serta item digital game secara instan melalui penyedia provider terintegrasi. Platform ini dilengkapi dengan antarmuka publik bertema gelap yang modern untuk pelanggan, serta dashboard admin interaktif untuk pengelolaan operasional staf internal berdasarkan hak akses role dan permission.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Struktur Folder Utama](#struktur-folder-utama)
- [Role dan Hak Akses (Role & Permission)](#role-dan-hak-akses-role--permission)
- [Panduan Instalasi](#panduan-instalasi)
- [Environment Variables Penting](#environment-variables-penting)
- [Alur Kerja Transaksi (Order Lifecycle)](#alur-kerja-transaksi-order-lifecycle)
- [Perintah Artisan Kustom](#perintah-artisan-kustom)
- [Skema Tema dan Styling](#skema-tema-dan-styling)

---

## Fitur Utama

### Sisi Pelanggan (Customer Facing)
- **Katalog Game dan Produk**: Pencarian langsung (live search) pada navbar dan hero, filter kategori (Semua Game, Populer, Favorit), serta spanduk promosi dinamis (Banner Carousel).
- **Halaman Detail Game dan Form Checkout 6 Langkah**:
  1. *Pilih Nominal*: Pemilihan produk dan nominal top up dengan penanda promo Flash Sale dan harga coret.
  2. *Masukkan Data Akun*: Pengisian ID Game dan Server ID yang dilengkapi panduan visual per game.
  3. *Jumlah Pembelian*: Stepper pengatur kuantitas pembelian.
  4. *Detail Kontak*: Pengisian email untuk invoice dan nomor WhatsApp untuk notifikasi.
  5. *Voucher Diskon*: Penerapan kode promo (potongan persentase atau nominal tetap).
  6. *Metode Pembayaran*: Integrasi popup pembayaran Snap yang responsif.
- **Integrasi Payment Gateway**: Dukungan terintegrasi untuk **Midtrans**, **Duitku**, dan **Tripay** berbasis arsitektur Pluggable Service Factory.
- **Integrasi Provider Top Up**: Proses pengiriman item otomatis via **Digiflazz** dan **VIP Reseller**, dilengkapi *Mock Provider* untuk lingkungan pengembangan dan pengujian.
- **Cek Status Transaksi Tanpa Login**: Pencarian status transaksi berdasarkan nomor invoice dengan indikator lini masa (timeline status) dari pembayaran hingga pengiriman item.
- **Autentikasi Pengguna**: Login dan registrasi akun pelanggan berbasis email maupun **Google OAuth**, verifikasi **2FA OTP via Email**, serta proteksi keamanan **reCAPTCHA v3**.
- **Notifikasi Multi-Channel**: Notifikasi invoice melalui Email dan pesan konfirmasi otomatis melalui WhatsApp (via **Fonnte**).
- **Pusat Bantuan dan Form Keluhan**: Tautan obrolan langsung WhatsApp Customer Service serta **Form Keluhan Tiket CS** yang mendukung lampiran bukti transfer atau tangkapan layar (format JPG, PNG, WEBP hingga 2MB).
- **Halaman Konten Statis**: Halaman FAQ berbasis accordion interaktif, Syarat dan Ketentuan, serta Kebijakan Privasi yang dapat dikelola dinamis dari admin.

### Sisi Administrator (/admin)
- **Dashboard Analitis dan KPI**: Ringkasan performa harian (Penjualan Hari Ini, Profit Hari Ini, Jumlah Pending/Diproses, dan Rasio Keberhasilan/Success Ratio), indikator produk terlaris (Best Seller), serta grafik tren penjualan interaktif (**Chart.js 4**) dengan opsi rentang waktu (Jam, Harian, Mingguan, Bulanan, Tahunan).
- **Manajemen Transaksi (Orders)**: Pemantauan detail transaksi, antrean pengulangan otomatis/manual (*Retry Queue*), pengiriman ulang webhook callback, pengecekan status pembayaran manual ke gateway, serta fitur penyelesaian manual (*Force Success*).
- **Manajemen Katalog Game, Kategori, dan Produk**: Pengelolaan master data game, kategori (Diamond, Battle Pass, Skin), daftar produk, penentuan margin keuntungan, serta pemetaan SKU ke masing-masing provider.
- **Manajemen Provider dan Prioritas**: Pengaturan status aktif/non-aktif provider top up serta penentuan urutan prioritas eksekusi provider (Priority Fallback System).
- **Log API dan Webhook**: Pemantauan catatan aktivitas panggilan API keluar ke provider serta data webhook masuk dari payment gateway untuk kebutuhan diagnosa teknis.
- **Manajemen Voucher dan Flash Sale**: Pembuatan kode promo diskon dengan batasan kuota dan minimal transaksi, serta pengaturan periode acara Flash Sale.
- **Manajemen Payment Gateway**: Pengaturan kunci enkripsi, kode merchant, status sandbox/produksi, dan saklar aktifasi untuk Midtrans, Duitku, dan Tripay.
- **Manajemen Tiket Keluhan Customer**: Panel khusus tim CS untuk menindaklanjuti keluhan dari pelanggan dengan pembaruan status (Baru, Diproses, Selesai).
- **Manajemen Pengguna dan Hak Akses**: Pengelolaan data pengguna, pembaruan role secara massal (bulk update role), serta pemblokiran/pembukaan blokir akun pengguna.
- **Laporan dan Ekspor Data**: Laporan Penjualan & Pendapatan, Margin Keuntungan, Performa Provider, dan Performa Produk dengan filter rentang tanggal serta fitur ekspor data ke format CSV.
- **Audit Log System**: Pencatatan riwayat setiap aksi dan perubahan data yang dilakukan oleh staf internal secara transparan.
- **Keamanan Akses Dashboard**: Restriksi alamat IP khusus untuk mengakses antarmuka admin via middleware `restrict_admin_ip`.

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 13 (PHP 8.2+) |
| Autentikasi & Otorisasi | Laravel Auth, Laravel Sanctum, spatie/laravel-permission |
| Frontend UI | Blade Templating, Bootstrap 5.3 (CDN), Bootstrap Icons |
| Grafik Dashboard | Chart.js 4 (CDN) |
| Payment Gateway | Midtrans, Duitku, Tripay |
| Provider Top Up | Digiflazz, VIP Reseller, Mock Provider |
| Notifikasi WhatsApp | Fonnte API |
| Tipografi | Google Fonts (Baloo 2 & Plus Jakarta Sans) |

---

## Struktur Folder Utama

```text
app/
├── Console/Commands/       # Perintah artisan (SyncProductPrices, DigiflazzTestConnection)
├── Http/Controllers/
│   ├── Admin/              # Controller dashboard admin (Dashboard, Order, Game, Report, dll)
│   ├── Api/                # Controller REST API (v1)
│   ├── Auth/               # Controller autentikasi (Login, Register, 2FA, Google OAuth)
│   └── Customer/           # Controller sisi pelanggan (AccountController)
├── Jobs/                   # ProcessTopUpOrder (pemrosesan order top up secara asinkron)
├── Models/                 # Model Eloquent (Order, Product, Game, Complaint, Voucher, Provider, dll)
├── Notifications/          # OrderSuccessNotification, AdminOtpNotification
├── Providers/               # Provider Service (DigiflazzService, VipResellerService, MockService)
└── Services/
    ├── PaymentGateways/    # Service Payment Gateway (Midtrans, Duitku, Tripay)
    ├── AuditLogService.php # Pencatatan audit log staf
    ├── OrderService.php    # Pemrosesan logika pesanan dan checkout
    ├── ReportService.php   # Agregasi laporan dan kalkulasi keuangan
    └── VoucherService.php  # Validasi dan kalkulasi diskon promo

resources/views/
├── admin/                  # Seluruh tampilan Blade dashboard administrator
├── auth/                   # Halaman autentikasi (login, register, 2FA OTP)
├── customer/                # Halaman beranda, detail game, cek transaksi, kontak, keluhan
└── layouts/                 # Master layout (customer.blade.php, admin.blade.php, auth.blade.php)

public/css/
├── app-custom.css           # Variabel utama dan style komponen umum
├── site-chrome.css          # Gaya navigasi navbar dan footer
├── dark-theme.css           # Tema gelap utama untuk sisi customer
├── home-theme.css           # Styling khusus halaman beranda
├── order-status-theme.css   # Styling khusus halaman cek status transaksi
├── auth-theme.css           # Styling khusus halaman login, register, dan 2FA
└── admin-custom.css         # Styling khusus dashboard admin (tema indigo-cyan)
```

---

## Role dan Hak Akses (Role & Permission)

Hak akses pengelolaan dikontrol menggunakan pustaka `spatie/laravel-permission`. Pembagian wewenang role yang tersedia adalah sebagai berikut:

| Role | Cakupan Hak Akses |
|---|---|
| `owner` | Akses penuh ke seluruh fitur sistem (`*`) |
| `admin` | Akses penuh ke seluruh fitur sistem (`*`) |
| `finance` | Akses Dashboard, Laporan Keuangan, Lihat Transaksi, dan Penyelesaian Manual (*Force Success*) |
| `cs` | Akses Dashboard, Lihat Transaksi, Pengulangan Order (*Retry Order*), dan Kelola Tiket Keluhan Customer |
| `marketing` | Akses Dashboard, Kelola Voucher Promo, Kelola Flash Sale, dan Kelola Spanduk/FAQ/Halaman CMS |
| `developer` | Akses Dashboard, Kelola Provider Top Up, Lihat Log API & Webhook, dan Pengaturan Payment Gateway |

---

## Panduan Instalasi

Berikut adalah langkah-langkah instalasi aplikasi di lingkungan lokal:

1. **Clone atau Salin Repositori Project**, kemudian masuk ke direktori proyek dan pasang dependensi:
   ```bash
   composer install
   npm install
   ```

2. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`, lalu buat kunci aplikasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Pengaturan Database dan Migrasi**:
   Sesuaikan konfigurasi koneksi database di file `.env`, kemudian jalankan perintah migrasi beserta seeder:
   ```bash
   php artisan migrate --seed
   ```

4. **Tautan Penyimpanan Media (Storage Link)**:
   Buat tautan simbolik direktori penyimpanan agar gambar game, banner, dan lampiran keluhan dapat diakses secara publik:
   ```bash
   php artisan storage:link
   ```

5. **Jalankan Queue Worker**:
   Jalankan pemroses antrean untuk menangani proses pengiriman item top up dan notifikasi secara asinkron:
   ```bash
   php artisan queue:work
   ```

6. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```

---

## Environment Variables Penting

Isi variabel berikut pada file `.env` sesuai dengan penyedia layanan yang digunakan:

```env
# Koneksi Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=website_top_up_game
DB_USERNAME=root
DB_PASSWORD=

# Payment Gateway (Midtrans)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Top Up Provider (Digiflazz)
DIGIFLAZZ_WEBHOOK_SECRET=

# Notifikasi WhatsApp (Fonnte)
FONNTE_TOKEN=
SUPPORT_WHATSAPP_NUMBER=

# Informasi Kontak Support (Tampil di Footer dan Halaman Hubungi Kami)
SUPPORT_EMAIL=cs@topupkilat.test
SUPPORT_INSTAGRAM=@topupkilat
SUPPORT_FACEBOOK=https://facebook.com/topupkilat
SUPPORT_TIKTOK=@topupkilat

# Keamanan Google reCAPTCHA v3
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

# Keamanan Dashboard Admin (Kosongkan jika tidak menggunakan pembatasan IP)
ADMIN_ALLOWED_IPS=
```

---

## Alur Kerja Transaksi (Order Lifecycle)

1. **Inisiasi Pesanan**: Pelanggan memilih produk pada halaman detail game, memasukkan data akun (ID Game/Server), lalu menekan tombol pembayar.
2. **Pembuatan Pesanan**: Sistem menyimpan pesanan dengan status `pending_payment` dan menampilkan popup / mengarahkan ke halaman pembayaran gateway.
3. **Konfirmasi Pembayaran**: Setelah pembayaran diselesaikan oleh pelanggan, payment gateway mengirimkan notifikasi Webhook ke sistem. Status pesanan diperbarui menjadi `paid` dan tugas `ProcessTopUpOrder` dikirimkan ke antrean (queue).
4. **Eksekusi Top Up**: Pekerjaan antrean memanggil API Provider Top Up (Digiflazz / VIP Reseller / Mock) sesuai dengan tingkat prioritas provider yang aktif.
5. **Penyelesaian**:
   - Jika provider mengembalikan status sukses: Pesanan diperbarui menjadi `success`, lalu notifikasi email dan WhatsApp dikirimkan ke pelanggan.
   - Jika provider mengalami kendala: Pesanan berstatus `failed` atau `processing`, dan staf CS/Admin dapat melakukan *Retry Order* atau *Force Success* melalui dashboard admin.
6. **Pencatatan Audit**: Setiap perubahan status pesanan oleh staf terekam secara otomatis pada Audit Log.

---

## Perintah Artisan Kustom

| Perintah | Fungsi |
|---|---|
| `php artisan sync:product-prices` | Sinkronisasi harga modal produk secara otomatis dari provider top up |
| `php artisan digiflazz:test-connection` | Pengujian koneksi API dan validasi kredensial ke layanan Digiflazz |

---

## Skema Tema dan Styling

- **Antarmuka Pelanggan (Customer)**: Menggunakan skema warna tema gelap (*dark theme*) yang didefinisikan pada file `public/css/dark-theme.css`. File ini dimuat di urutan terakhir pada tag `<head>` untuk memastikan konsistensi variabel warna seperti `--color-surface`, `--color-accent-yellow`, dan `--color-text-light`.
- **Antarmuka Administrator (Admin)**: Menggunakan skema warna terang beraksen indigo-cyan yang diatur secara terpisah pada file `public/css/admin-custom.css`, sehingga pengelolaan halaman admin terisolasi dari gaya halaman publik pelanggan.