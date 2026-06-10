# PitStop

![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Ready-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

Sistem booking service bengkel berbasis web. PitStop membantu pelanggan membuat jadwal service dengan mudah, sementara admin dapat mengelola layanan, memproses booking, dan melihat riwayat pekerjaan bengkel.

## Navigasi

- [PitStop](#pitstop)
    - [Navigasi](#navigasi)
    - [Tautan Project](#tautan-project)
    - [Tentang Project](#tentang-project)
    - [Preview Singkat](#preview-singkat)
    - [Fitur](#fitur)
        - [Untuk Pelanggan](#untuk-pelanggan)
        - [Untuk Admin](#untuk-admin)
        - [Sistem](#sistem)
    - [Status Project](#status-project)
    - [Batasan](#batasan)
    - [Tech Stack](#tech-stack)
    - [Persyaratan](#persyaratan)
    - [Instalasi](#instalasi)
        - [1. Clone Project](#1-clone-project)
        - [2. Install Dependency](#2-install-dependency)
        - [3. Siapkan Environment](#3-siapkan-environment)
        - [4. Atur Database](#4-atur-database)
        - [5. Jalankan Migration dan Seeder](#5-jalankan-migration-dan-seeder)
        - [6. Jalankan Aplikasi](#6-jalankan-aplikasi)
    - [Akun Default / Demo](#akun-default--demo)
    - [Struktur Database](#struktur-database)
    - [Halaman Utama](#halaman-utama)
    - [Penutup](#penutup)

## Tautan Project

- Halaman web: [PitStop](https://pitstop.nurkholish.my.id/)
- Video demo: [Demo](https://youtu.be/kYAnRvrcBcs)
- Laporan: [Laporan Akhir PWEB.pdf](https://github.com/mnurkholish/pitstop/blob/main/Laporan%20Akhir%20PWEB.pdf)

## Tentang Project

PitStop dibuat untuk menyederhanakan proses booking service kendaraan. Pelanggan dapat memilih layanan, menentukan tanggal dan jam kedatangan, lalu melihat estimasi biaya serta durasi sebelum booking dikirim.

Admin memiliki halaman khusus untuk mengatur layanan bengkel, melihat booking aktif, memperbarui status pengerjaan, dan memeriksa riwayat booking yang sudah selesai atau dibatalkan.

## Preview Singkat

| Area      | Yang Bisa Dilakukan                                                   |
| --------- | --------------------------------------------------------------------- |
| Pelanggan | Booking service, cek status, lihat detail, batalkan booking menunggu. |
| Admin     | Kelola layanan, proses booking, lihat riwayat, pantau ringkasan data. |
| Sistem    | Cek jadwal, hitung estimasi, pisahkan akses admin dan pelanggan.      |

Alur sederhana:

```text
Pelanggan pilih layanan
  -> sistem hitung estimasi
  -> booking dibuat
  -> admin memproses
  -> booking selesai / dibatalkan
```

## Fitur

### Untuk Pelanggan

- Registrasi, login, logout, dan pengaturan profil.
- Membuat booking service kendaraan.
- Memilih layanan aktif yang tersedia.
- Melihat estimasi harga, durasi, dan jam selesai.
- Melihat daftar booking pribadi.
- Mencari dan memfilter booking berdasarkan status.
- Membuka detail booking.
- Membatalkan booking yang masih berstatus menunggu.
- Mengatur tema tampilan dan ukuran font.

### Untuk Admin

- Dashboard ringkasan layanan dan booking.
- Mengelola data layanan bengkel.
- Menambahkan gambar layanan.
- Mengaktifkan atau menonaktifkan layanan.
- Melihat booking aktif.
- Memproses status booking.
- Melihat riwayat booking selesai dan dibatalkan.
- Mencari dan memfilter data booking.

### Sistem

- Role admin dan pelanggan dipisahkan.
- Jadwal booking dibatasi sesuai jam operasional bengkel.
- Slot booking dicek agar tidak bertabrakan.
- Tampilan responsive untuk desktop dan mobile.
- Data contoh tersedia melalui seeder.

## Status Project

| Bagian              | Status         |
| ------------------- | -------------- |
| Autentikasi         | Tersedia       |
| Booking pelanggan   | Tersedia       |
| Manajemen layanan   | Tersedia       |
| Riwayat booking     | Tersedia       |
| Preferensi tampilan | Tersedia       |
| Pembayaran online   | Belum tersedia |
| Notifikasi otomatis | Belum tersedia |

## Batasan

Beberapa fitur berikut belum tersedia di versi ini:

- Pembayaran online.
- Integrasi WhatsApp atau notifikasi otomatis.
- Inventory sparepart.
- Laporan keuangan.
- Multi-cabang bengkel.
- Verifikasi email wajib sebelum booking.
- Pembatalan booking setelah status diproses.

## Tech Stack

| Bagian      | Teknologi                      |
| ----------- | ------------------------------ |
| Backend     | PHP 8.3+, Laravel 13           |
| Autentikasi | Laravel Breeze                 |
| Frontend    | Blade, Tailwind CSS, Alpine.js |
| Build Tool  | Vite                           |
| Database    | MySQL / MariaDB                |

## Persyaratan

Pastikan perangkat sudah memiliki:

| Kebutuhan       | Keterangan                |
| --------------- | ------------------------- |
| PHP             | Versi 8.3 atau lebih baru |
| Composer        | Untuk dependency Laravel  |
| Node.js dan npm | Untuk asset frontend      |
| MySQL / MariaDB | Untuk database            |
| Git             | Untuk clone repository    |

## Instalasi

Ikuti langkah berikut untuk menjalankan PitStop di lokal.

### 1. Clone Project

```bash
git clone <url-repository>
cd <nama-folder-project>
```

### 2. Install Dependency

```bash
composer install
npm install
```

### 3. Siapkan Environment

```bash
copy .env.example .env
php artisan key:generate
```

### 4. Atur Database

Buat database baru, misalnya dengan nama `pitstop`, lalu sesuaikan file `.env`.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pitstop
DB_USERNAME=root
DB_PASSWORD=
APP_TIMEZONE=Asia/Jakarta
```

### 5. Jalankan Migration dan Seeder

```bash
php artisan migrate --seed
php artisan storage:link
```

### 6. Jalankan Aplikasi

Buka dua terminal.

Terminal pertama:

```bash
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Biasanya aplikasi dapat dibuka di:

```text
http://127.0.0.1:8000
```

Untuk build asset production:

```bash
npm run build
```

## Akun Default / Demo

Gunakan akun berikut setelah menjalankan seeder.

| Role      | Email               | Password   |
| --------- | ------------------- | ---------- |
| Admin     | `admin@example.com` | `password` |
| Pelanggan | `user@example.com`  | `password` |

Seeder juga menambahkan data contoh seperti layanan bengkel dan booking dummy agar aplikasi langsung memiliki isi saat dibuka.

## Struktur Database

Tabel utama yang digunakan:

| Tabel             | Fungsi                                                                             |
| ----------------- | ---------------------------------------------------------------------------------- |
| `users`           | Menyimpan akun admin dan pelanggan.                                                |
| `services`        | Menyimpan layanan bengkel, harga, durasi, gambar, dan status aktif.                |
| `bookings`        | Menyimpan data booking, kendaraan, jadwal, slot, total biaya, status, dan catatan. |
| `booking_service` | Menghubungkan booking dengan layanan yang dipilih.                                 |

Relasi sederhananya:

```text
users
  -> bookings

bookings
  -> booking_service

services
  -> booking_service
```

Penjelasan singkat:

- Satu pelanggan bisa memiliki banyak booking.
- Satu booking bisa berisi lebih dari satu layanan.
- Satu layanan bisa dipilih di banyak booking.
- Harga dan durasi layanan disimpan sebagai snapshot agar riwayat booking tetap akurat.

## Halaman Utama

| Halaman                   | Keterangan                               |
| ------------------------- | ---------------------------------------- |
| `/`                       | Beranda publik.                          |
| `/services`               | Daftar layanan publik.                   |
| `/login`                  | Login pengguna.                          |
| `/register`               | Registrasi pelanggan.                    |
| `/dashboard`              | Dashboard pelanggan dan form booking.    |
| `/my-bookings`            | Daftar booking pelanggan.                |
| `/preferences`            | Pengaturan tema dan ukuran font.         |
| `/profile`                | Pengaturan profil akun.                  |
| `/admin/dashboard`        | Dashboard admin.                         |
| `/admin/services`         | Kelola layanan bengkel.                  |
| `/admin/bookings`         | Kelola booking aktif.                    |
| `/admin/bookings/history` | Riwayat booking selesai atau dibatalkan. |

## Penutup

PitStop dibuat sebagai sistem booking service bengkel yang sederhana, rapi, dan mudah dikembangkan. Fitur utamanya sudah mencakup kebutuhan dasar pelanggan dan admin, mulai dari booking layanan sampai pengelolaan riwayat service.

Terima kasih sudah melihat dan menggunakan project PitStop.
