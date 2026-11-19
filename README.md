<p align="center">
  <img src="public/images/logo.png" alt="Logo APIC" width="120">
</p>

<h1 align="center">APIC — Alat Pinjam Mudah Cepat</h1>

<p align="center">
  <b>Sistem Manajemen Peminjaman Alat Laboratorium & Bengkel</b><br>
  SMK Muhammadiyah 1 Bantul
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php">
  <img src="https://img.shields.io/badge/SQLite-Default-003B57?style=flat-square&logo=sqlite">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38B2AC?style=flat-square&logo=tailwindcss">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=flat-square">
  <img src="https://img.shields.io/badge/Chart.js-4-FF6384?style=flat-square">
  <img src="https://img.shields.io/badge/Capacitor-Android-119EFF?style=flat-square&logo=capacitor">
</p>

<p align="center">
  <a href="#-tentang-aplikasi">Tentang</a> •
  <a href="#-fitur">Fitur</a> •
  <a href="#-teknologi">Teknologi</a> •
  <a href="#-instalasi">Instalasi</a> •
  <a href="#-akun-demo">Akun Demo</a> •
  <a href="#-struktur-database">Database</a> •
  <a href="#-penulis">Penulis</a>
</p>

---

## 📖 Tentang Aplikasi

**APIC (Alat Pinjam Mudah Cepat)** adalah aplikasi web untuk mengelola
peminjaman alat laboratorium dan bengkel sekolah secara digital. Aplikasi ini
menggantikan pencatatan manual pada buku fisik yang rawan hilang dan sulit
dilacak, dengan sistem yang **transparan, akuntabel, dan real-time**.

APIC memiliki **3 hak akses** (User/Siswa, Petugas Laboratorium, Admin) yang
mencakup seluruh alur peminjaman: pengajuan → verifikasi → serah terima
berbasis **QR Code** → pembayaran multi-metode → pengembalian → laporan.

APIC juga menyediakan REST API berbasis Laravel Sanctum untuk integrasi
mobile dan klien lain. Versi Android dibangun dengan **Capacitor** dari aset
web aplikasi.

## ✨ Fitur

### 🌐 Halaman Publik
- Landing page dengan statistik inventaris **live**
- Katalog alat publik: filter kategori, pencarian, badge stok real-time
- Tentang Kami, Panduan Penggunaan, FAQ, dan Kontak

### 🔐 Autentikasi
- Register & Login berbasis role
- Lupa password / reset password
- Verifikasi email
- REST API token melalui Laravel Sanctum

### 👤 User (Siswa / Peminjam)
- Dashboard dengan **kuota harian (maks. 2 alat/hari)**
- Pengajuan peminjaman (pilih alat, tanggal pinjam & kembali, catatan)
- Riwayat & detail transaksi bergaya invoice
- **QR transaksi** untuk serah terima tanpa pencarian kode manual
- Pembayaran multi-metode: Transfer Bank, DANA, ShopeePay, GoPay, OVO, Tunai
  (+ unggah bukti transfer; QR tujuan dikelola admin)
- Pengajuan **perpanjangan** (maks. 7 hari, persetujuan petugas)
- Pembatalan pengajuan berstatus *menunggu*
- Notifikasi real-time & manajemen profil

### 🧑‍💼 Petugas Laboratorium
- Dashboard operasional (antrean verifikasi, jatuh tempo, stok tersedia)
- Verifikasi pengajuan peminjaman
- Persetujuan / penolakan perpanjangan
- Proses pengembalian + pencatatan kondisi alat + **cetak struk**
- **Scan QR alat** untuk serah terima & pengembalian cepat
- Scan QR transaksi untuk identifikasi peminjaman
- Verifikasi pembayaran (bukti transfer) → status **LUNAS**
- Detail alat + riwayat peminjaman & mutasi stok
- Laporan harian / periodik

### 🛠️ Admin
- Dashboard analitik: grafik peminjaman bulanan, top alat, distribusi kategori
  (Chart.js)
- Manajemen kategori, data alat, dan user
- **Pengaturan Sistem**: kuota harian, masa tenggang, tarif denda, batas
  perpanjangan, metode pembayaran aktif + unggah QR tujuan
- REST API untuk manajemen alat, kategori, user, laporan, dan pengaturan
- Log aktivitas sistem
- Export laporan **PDF / Excel**

### 🔔 Sistem Notifikasi
- Notifikasi otomatis setiap perubahan status (verifikasi, pembayaran,
  pengingat jatuh tempo H-1)
- Badge *belum dibaca* di topbar & sidebar seluruh panel

## 🧰 Teknologi

| Layer     | Teknologi                                    |
|-----------|----------------------------------------------|
| Backend   | Laravel 13 (PHP 8.3+)                        |
| Database  | SQLite (default) / MySQL / MariaDB           |
| Frontend  | Laravel Blade + Tailwind CSS 4 + Alpine.js   |
| Mobile    | Android dengan Capacitor 8                   |
| API       | Laravel Sanctum + REST API                   |
| Grafik    | Chart.js 4                                   |
| PDF       | barryvdh/laravel-dompdf                      |
| QR Code   | endroid/qr-code                              |
| PWA       | ladumor/laravel-pwa                          |
| Log       | spatie/laravel-activitylog                   |
| Build     | Vite (`npm run build`)                       |

## 🚀 Instalasi

**Prasyarat:** PHP ≥ 8.3, Composer, Node.js 20+, dan npm.

```bash
# 1. Clone repository
git clone https://github.com/vzbotoom-jpg/Apea.git
cd Apea

# 2. Install dependensi PHP
composer install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Database SQLite sudah dikonfigurasi sebagai default.
#    Pastikan database/database.sqlite ada, lalu buat bila perlu:
touch database/database.sqlite

# 5. Jalankan migrasi + seeder
php artisan migrate --seed

# 6. Install frontend & build aset
npm install
npm run build

# 7. Symlink storage (gambar alat & bukti transfer)
php artisan storage:link

# 8. Jalankan aplikasi
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser.

Untuk mode pengembangan dengan server Laravel, Queue, Pail, dan Vite:

```bash
composer run dev
```

Untuk MySQL/MariaDB, ubah konfigurasi `DB_*` pada `.env`, pastikan database
telah dibuat, lalu ulangi `php artisan migrate --seed`.

## 👥 Akun Demo

| Role    | Email               | Password    |
|---------|---------------------|-------------|
| Admin   | ***************     | ********    |
| Petugas | *****************   | ********    |
| User    | ***************     | ********    |

> ⚠️ Ganti password default setelah login pertama.

## 🗄️ Struktur Database

**Tabel bisnis utama:**

`users` · `kategoris` · `alats` · `peminjamans` · `detail_peminjamans` ·
`pembayarans` · `pengembalians` · `dendas` · `notifikasis` · `mutasi_alats` ·
`settings` · `log_activities` · `activity_log`

**Relasi utama:**
- `users` 1—N `peminjamans` (sebagai peminjam, petugas verifikator, penyetuju)
- `peminjamans` 1—N `detail_peminjamans` N—1 `alats`
- `peminjamans` 1—1 `pengembalians` dan 1—1 `dendas`
- `peminjamans` 1—N `pembayarans`
- `alats` N—1 `kategoris`; `alats` 1—N `mutasi_alats`
- `users` 1—N `notifikasis`

## 📁 Struktur Proyek (Highlight)

```
app/
├── Http/Controllers/
│   ├── Admin/      # Dashboard, Alat, Kategori, User, Laporan, Settings, Log
│   ├── Petugas/    # Dashboard, Alat, Scan QR, Peminjaman, Pengembalian
│   └── User/       # Dashboard, Alat, Peminjaman, Pembayaran, Profil
├── Models/         # Alat, Peminjaman, Pembayaran, Pengembalian, Denda,
│                   # Notifikasi, MutasiAlat, Setting, dll.
├── Http/Controllers/Api/
│                   # REST API Sanctum untuk aplikasi mobile/klien
resources/views/
├── admin/  petugas/  user/   # Panel per role
├── components/sidebar/      # Sidebar gelap per role
└── layouts/                 # Layout user, petugas, admin, auth
android/            # Pembungkus aplikasi Android berbasis Capacitor
routes/api.php      # Endpoint REST API
```

## 🧪 Skenario Uji Coba

1. **User** mendaftar → mengajukan 2 alat → membayar via DANA (unggah bukti).
2. **Petugas** memverifikasi pengajuan → scan QR serah terima → verifikasi
   pembayaran → proses pengembalian + struk.
3. **Admin** mengubah tarif denda & metode pembayaran aktif → memantau grafik
   dan log aktivitas.
4. Sistem otomatis: mengurangi/memulihkan stok, menghitung denda setelah masa
   tenggang, mengirim notifikasi setiap perubahan status.

## 🧪 Pengujian

Pengujian tersedia pada direktori `tests/` dan mencakup alur peminjaman,
autentikasi, katalog alat, QR, serta dashboard.

```bash
./vendor/bin/phpunit
```

## 👤 Penulis

| | |
|---|---|
| **Nama** | [Nama Lengkap Anda] |
| **Kelas** | XII RPL — SMK M 1 Bantul |
| **NIS** | [NIS Anda] |
| **Program Keahlian** | Pengembangan Perangkat Lunak dan Gim |

Proyek ini disusun sebagai **Ujian Kompetensi Keahlian (UKK)** Tahun Pelajaran
2026/2027.

## 📄 Lisensi

Proyek edukasi — bebas digunakan untuk keperluan pembelajaran dengan
menyertakan kredit penulis.

---

<p align="center">
  <sub>Dibangun dengan <a href="https://laravel.com">Laravel</a> —
  framework web dengan sintaks elegan dan ekspresif.</sub>
</p>