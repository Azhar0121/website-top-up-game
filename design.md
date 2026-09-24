# Dokumentasi Desain Visual (Design System)

## Ringkasan (Overview)

TopUp Kilat adalah platform layanan top up game dan produk digital otomatis. Bahasa visual antarmuka platform ini dirancang khusus untuk menciptakan pengalaman visual yang modern, imersif, dan responsif. Sistem visual terbagi menjadi dua identitas utama yang saling melengkapi: tema gelap (*dark theme*) untuk antarmuka publik customer, serta antarmuka bergaya konsol modern (*modern gaming console*) untuk dashboard administrator.

Sisi publik customer menggunakan latar ungu malam yang dalam (`var(--color-surface-alt)` — #0E0820) berpadu dengan kontainer ungu fajar (`var(--color-surface)` — #241A4D) dan permukaan terangkat (`var(--color-surface-raised)` — #2E2160). Kejenuhan warna dihadirkan melalui tiga warna aksen utama yang dikurasi secara ketat: Kuning Amber (`var(--color-accent-yellow)` — #FFC93C), Pink Neon (`var(--color-accent-pink)` — #FF5D8F), dan Mint Teal (`var(--color-accent-mint)` — #34E4B8). Lingkungan ini diperkaya dengan efek *glassmorphism* berbayang lembut pada bilah navigasi, bola pendar (*glow orbs*) di latar belakang hero, serta sudut melengkung halus pada setiap kartu produk.

Sisi admin menggunakan pendekatan yang lebih terang dan tenang (`var(--admin-bg)` — #F4F5FA) dengan palet dasar putih (`var(--admin-surface)` — #FFFFFF) yang dipadukan dengan aksen Indigo Elektrik (`var(--admin-primary)` — #6C4FF0) dan Cyan Teal (`var(--admin-cyan)` — #14B8A6). Sistem ini menggantikan efek gradasi mencolok dengan *tint* transparan yang bersih, menghasilkan antarmuka pengelolaan data yang profesional dan berkinerja tinggi.

Tipografi dibangun di atas dua keluarga font utama: **Baloo 2** untuk judul yang berani dan berkarakter, serta **Plus Jakarta Sans** untuk teks isi, antarmuka form, dan data tabel yang mudah dibaca.

**Karakteristik Utama Desain:**
- Skema Warna Tematik: Tema gelap ungu-neon untuk customer dan tema terang indigo-cyan untuk dashboard admin.
- Geometri Tombol & Lencana: Penggunaan bentuk kapsul (*stadium pill*) untuk tombol aksi utama, filter chip, dan badge status.
- Geometri Kartu Game & Produk: Kartu menggunakan radius sudut 14px hingga 20px dengan rasio aspek media 1:1 pada gambar mini game.
- Efek Pencahayaan & Kedalaman: Penggunaan bola pendar (*glow orbs*) terdistribusi, lapisan *backdrop-filter blur*, dan efek *shimmer loading skeleton*.
- Kontras Tipografi: Kombinasi font display berbasis kurva tegas (Baloo 2) dengan font sans-serif geometris modern (Plus Jakarta Sans).

---

## Warna (Colors)

### 1. Pelanggan (Customer Facing Dark Theme)

#### Brand & Aksen Utama
- **Kuning Amber** (`var(--color-accent-yellow)` — #FFC93C): Warna aksen interaksi utama. Digunakan pada tombol CTA utama (`app-btn-cta`), badge favorit, indikator navigasi aktif, dan penekanan poin penting.
- **Kuning Amber Hover** (`var(--color-accent-yellow-hover)` — #F59E0B): Status hover untuk elemen berbasis Kuning Amber.
- **Pink Neon** (`var(--color-accent-pink)` — #FF5D8F): Warna sekunder untuk elemen promosi, badge Flash Sale, indikator transaksi aktif, dan bola pendar hero.
- **Mint Teal** (`var(--color-accent-mint)` — #34E4B8): Warna tersier dan penanda sukses. Digunakan pada status produk terpilih, indikator pengiriman kilat (SLA), dan elemen centang verifikasi.

#### Permukaan & Latar Belakang (Surface)
- **Latar Belakang Utama** (`var(--color-surface-alt)` — #0E0820): Warna dasar seluruh halaman pelanggan.
- **Permukaan Kartu** (`var(--color-surface)` — #241A4D): Warna latar belakang kartu game, panel checkout, accordion FAQ, dan kartu riwayat.
- **Permukaan Terangkat** (`var(--color-surface-raised)` — #2E2160): Warna permukaan untuk elemen popover atau kontainer yang membutuhkan pemisahan dari permukaan dasar.
- **Navbar Glass background**: `rgba(19, 10, 40, 0.85)` dengan efek `backdrop-filter: blur(12px)`.
- **Footer Dark**: `#0D0620` dengan garis tepi atas transparan.

#### Batas & Garis Tepi (Borders)
- **Border Lembut** (`var(--color-border-soft)` — `rgba(255, 255, 255, 0.16)`): Garis pemisah antar komponen, kartu standar, dan baris tabel.
- **Border Kuat** (`var(--color-border-strong)` — `rgba(255, 255, 255, 0.30)`): Garis tepi elemen terpilih, pencarian navbar, dan gambar hero game.

#### Teks & Hirarki Pembacaan
- **Teks Utama** (`var(--color-text-light)` — #F5F0FF): Digunakan pada seluruh judul, label form, dan teks isi utama.
- **Teks Muted** (`var(--color-text-muted)` — #A99DCB): Digunakan pada sub-judul, harga sekunder, keterangan panduan, dan teks tempat input (placeholder).
- **Teks Gelap Aksesibilitas** (`var(--color-text-dark)` — #1B1035): Digunakan secara khusus untuk teks di atas latar belakang terang (seperti tombol Kuning Amber atau chip aktif).

---

### 2. Administrator (Admin Dashboard Console Theme)

#### Aksens & Identitas
- **Indigo Utama** (`var(--admin-primary)` — #6C4FF0): Warna identitas utama admin. Digunakan pada tautan navigasi aktif, tombol utama, dan indikator grafik.
- **Indigo Gelap** (`var(--admin-primary-dark)` — #5636D6): Warna hover tombol utama admin.
- **Cyan Teal** (`var(--admin-cyan)` — #14B8A6): Warna aksen sekunder untuk statistik perbandingan dan aksen garis topbar.
- **Amber Warning** (`var(--admin-amber)` — #F59E0B): Warna indikator status pending/proses dan kartu peringatan.
- **Danger Red** (`var(--admin-danger)` — #EF4444): Warna status gagal, tombol hapus, dan kesalahan validasi.
- **Success Green** (`var(--admin-success)` — #22C55E): Warna status sukses dan pertumbuhan penjualan.

#### Permukaan Admin
- **Latar Belakang Canvas** (`var(--admin-bg)` — #F4F5FA): Warna latar belakang utama seluruh halaman admin.
- **Permukaan Kartu & Sidebar** (`var(--admin-surface)` / `var(--admin-sidebar-bg)` — #FFFFFF): Warna putih murni untuk sidebar, topbar, dan kartu data admin.
- **Garis Tepi Admin** (`var(--admin-border)` — #E7E7F3): Garis pemisah antar kolom dan border kartu admin.

---

## Tipografi (Typography)

### Keluarga Font (Font Families)
1. **Baloo 2** (`var(--font-display)`): Font display kurva membulat yang kuat dan ramah. Digunakan pada judul utama (`h1`, `h2`, `h3`, `h4`), nama brand (`app-brand`), serta judul seksi.
2. **Plus Jakarta Sans** (`var(--font-body)` / `var(--admin-font)`): Font sans-serif geometris serbaguna. Digunakan untuk teks isi, deskripsi produk, antarmuka form, badge status, dan seluruh antarmuka dashboard admin.

### Hirarki Tipografi

| Kategori Token | Ukuran | Weight | Line Height | Penggunaan Utama |
|---|---|---|---|---|
| Display / Hero Title | 32px – 51px (clamp) | 800 | 1.15 | Judul utama halaman beranda & static hero |
| Heading 1 (H1) | 28px – 32px | 800 | 1.20 | Judul halaman statis & header game detail |
| Heading 2 (H2) | 22px – 25px | 700 | 1.25 | Judul seksi katalog, rekomendasi & daftar |
| Heading 3 (H3) | 18px – 20px | 700 | 1.30 | Judul kartu besar & langkah checkout |
| Heading 4 (H4) | 16px – 18px | 700 | 1.35 | Judul item accordion & modal |
| Body Large | 16px – 17px | 400 / 600 | 1.50 | Sub-judul hero & ringkasan transaksi |
| Body Regular | 14px – 15px | 400 / 600 | 1.50 | Teks paragraf, deskripsi produk, input form |
| Body Small | 12px – 13px | 500 / 600 | 1.40 | Teks bantuan, catatan SLA, footer, badge |
| Caption / Micro | 10px – 11px | 700 / 800 | 1.20 | Badge micro, label flash sale, tag kecil |

---

## Tata Letak (Layout & Spacing)

### Sistem Spasi
- **Ukuran Dasar Modul**: 8px (dengan variasi 4px, 12px, 16px, 24px, 32px, 48px).
- **Jarak Antar Seksi Utama**: 48px hingga 80px pada tampilan desktop.
- **Jarak Grid Katalog**: 12px (`g-2` pada mobile) hingga 16px (`g-3` pada desktop).

### Kontainer & Grid System
- **Kontainer Utama Customer**: Lebar maksimum 1200px dengan padding samping 15px.
- **Grid Katalog Game**:
  - Tampilan Mobile (`< 576px`): 2 kolom (`col-6`).
  - Tampilan Tablet (`>= 768px`): 4 kolom (`col-md-4` / `col-md-3`).
  - Tampilan Desktop (`>= 992px`): 6 kolom (`col-lg-2`).
- **Grid Form Checkout**:
  - Kolom Kiri (Langkah 1-3): 7 Kolom (`col-lg-7`) untuk pemilihan nominal dan data akun.
  - Kolom Kanan (Langkah 4-6): 5 Kolom (`col-lg-5`) sticky panel untuk kontak, voucher, dan pembayaran.
- **Layout Shell Admin**:
  - Sidebar Tetap (*Fixed Sidebar*): Lebar 260px.
  - Area Konten Utama (*Main Content*): `margin-left: 260px` (berubah menjadi 0 pada tampilan mobile dengan menu *drawer overlay*).

---

## Elevasi & Kedalaman (Elevation & Depth)

1. **Tingkat Elevasi 0 (Dasar Canvas)**: Latar belakang aplikasi (`#0E0820` publik, `#F4F5FA` admin).
2. **Tingkat Elevasi 1 (Permukaan Kartu)**: Warna `#241A4D` publik dengan bayangan `0 3px 14px rgba(0,0,0, 0.35)` dan border `rgba(255,255,255, 0.16)`.
3. **Tingkat Elevasi 2 (Hover State Kartu)**: Bergeser ke atas `-6px` dengan bayangan `0 14px 32px rgba(0,0,0, 0.50)` dan pergantian warna border menjadi Kuning Amber (`#FFC93C`).
4. **Tingkat Elevasi 3 (Panel Melayang / Sticky Checkout)**: Bayangan `0 10px 34px rgba(0,0,0, 0.35)` dengan batas permukaan terpisah.
5. **Efek Kedalaman Dekoratif**:
   - **Glow Orbs (Bola Pendar)**: Lingkaran warna terblur 90px dengan opasitas 28% di sudut kiri atas (Pink) dan kanan bawah (Mint) pada seksi hero.
   - **Shimmer Skeleton Loading**: Efek gradasi animasi linier berganti dari `#241748` ke `#2F1F5C` secara kontinu saat data memuat.

---

## Bentuk (Shapes & Radius)

### Skala Radius Sudut (Border Radius)
- **Sudut Besar (`var(--radius-lg)`)**: `20px` — Digunakan pada kartu game, kartu halaman statis, panel checkout, dan kartu admin (`--admin-radius-lg`: 16px).
- **Sudut Sedang (`var(--radius-md)`)**: `14px` — Digunakan pada gambar mini game, kotak tutorial, badge ikon, dan kartu admin (`--admin-radius-md`: 10px).
- **Sudut Kecil**: `10px` — Digunakan pada input form (`app-input`) dan catatan SLA.
- **Bentuk Kapsul Penuh (`rounded-pill` / 999px)**: Digunakan pada tombol aksi (`app-btn-cta`), tombol outline, filter chip, badge status, dan stepper kuantitas.

---

## Komponen Visual Utama (Components)

### 1. Tombol (Buttons)
- **Tombol Utama (`app-btn-cta`)**: Latar belakang Kuning Amber (`#FFC93C`), teks gelap (`#1B1035`), berat font 700, sudut melengkung kapsul (999px). Efek hover: pergeseran warna ke Amber Hover (`#F59E0B`) dengan bayangan `0 6px 14px rgba(245, 158, 11, 0.32)`.
- **Tombol Outline (`app-btn-outline`)**: Latar transparan dengan border 2px putih transparan (`rgba(255,255,255, 0.3)`), teks terang. Efek hover: latar `rgba(255,255,255, 0.12)`.
- **Tombol Cek Transaksi Navbar (`app-nav-track-btn`)**: Latar Pink Neon (`#FF5D8F`), bayangan pendar `0 4px 16px rgba(255, 93, 143, 0.35)`.

### 2. Kartu Game & Produk
- **Kartu Game (`game-card`)**: Kartu dengan rasio aspek gambar 1:1, judul terpotong maksimal 2 baris, dan rotasi dekoratif mikro halus pada posisi tertentu (`-0.6deg`, `0.5deg`, `-0.3deg`) yang kembali rata saat di-hover.
- **Kartu Produk Top Up (`product-card`)**: Kartu pilih nominal pada halaman detail game. Memiliki status normal, hover (border kuning), terpilih (border Mint Teal dengan latar belakang `rgba(52, 228, 184, 0.12)`), serta variasi Flash Sale (border Pink Neon dengan badge promo & harga coret).

### 3. Form Input & Pengontrol
- **Input Aplikasi (`app-input`)**: Latar belakang transparan gelap (`rgba(255,255,255, 0.05)`), border 2px `var(--color-border-soft)`, warna teks putih. Saat fokus: border berubah menjadi Mint Teal (`var(--color-accent-mint)`).
- **Stepper Kuantitas (`qty-stepper`)**: Kontrol kapsul terpadu untuk menambah dan mengurangi jumlah produk dengan tombol `+` dan `-` yang responsif.

### 4. Elemen Penanda Transaksi
- **Timeline Status Pembelian (`timeline`)**: Indikator vertikal berbasis titik (*dot*) warna Mint Teal / Pink Neon dengan garis penghubung vertikal 2px untuk memantau status pembayaran dan pengiriman top up.
- **Chip Metode Pembayaran (`payment-chip`)**: Chip berbentuk kapsul dengan background transparan halus (`rgba(255,255,255, 0.08)`) dan border 1px untuk menampilkan pilihan QRIS, Virtual Account, dan E-Wallet.
