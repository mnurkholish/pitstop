# Audit Akhir PitStop

Audit ini membandingkan implementasi PitStop dengan PRD, panduan role access, responsive guide, dan rubrik penilaian pada `agent-context`.

## Ringkasan

| Area                    | Status                           | Catatan                                                                                                   |
| ----------------------- | -------------------------------- | --------------------------------------------------------------------------------------------------------- |
| HTML, Blade, Tailwind   | Sesuai                           | Layout public/user/admin dan komponen reusable tersedia.                                                  |
| Responsive              | Sesuai berdasarkan inspeksi kode | Navbar hamburger mobile, grid responsif, tabel desktop, dan card list mobile tersedia.                    |
| Role access             | Sesuai                           | Route user/admin memakai middleware `auth` dan `role`.                                                    |
| CRUD layanan            | Sesuai                           | Create, read, update, delete, search AJAX, filter, pagination, upload gambar, dan delete guard tersedia.  |
| Booking pelanggan       | Sesuai                           | Estimasi JS, validasi server, snapshot, konflik slot/waktu, jam operasional, dan kode booking tersedia.   |
| Booking Saya            | Sesuai                           | Ownership, pencarian AJAX, filter, detail modal, responsive list, dan pembatalan `pending` tersedia.      |
| Admin booking           | Sesuai                           | Daftar aktif, riwayat final, pencarian AJAX, filter tanggal/status, detail, dan transisi status tersedia. |
| Cookie dan session      | Sesuai                           | Auth session, logout invalidate session, visit counter, tema, dan ukuran font tersedia.                   |
| Upload gambar           | Sesuai                           | Disk public, validasi JPG/JPEG/PNG maksimal 2 MB, fallback, replace cleanup, dan delete cleanup tersedia. |
| Route                   | Sesuai                           | Route terdaftar dan tidak ditemukan referensi named route yang putus.                                     |
| Halaman publik tambahan | Parsial                          | `/services`, `/about`, dan `/contact` dapat dibuka tetapi masih placeholder sederhana.                    |

## Rubrik Penilaian

| Aspek                       | Bukti Implementasi                                                                                                                          |
| --------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------- |
| HTML & CSS                  | Layout semantik, Blade, Tailwind, komponen UI reusable, card, badge, alert, modal, tabel/card responsive.                                   |
| JavaScript & DOM            | Estimasi booking, uppercase plat nomor, validasi minimal satu layanan, hamburger, dropdown, modal, konfirmasi hapus, dan update hasil AJAX. |
| PHP & CRUD Database         | Laravel, Eloquent, SQLite, CRUD layanan, booking, pivot snapshot, soft delete user, dan validasi server.                                    |
| Cookies & Session           | Breeze session auth, logout, session visit counter, role access, serta cookie tema/font.                                                    |
| AJAX/JSON                   | Booking Saya, layanan admin, booking aktif admin, dan riwayat admin menggunakan Fetch API serta JSON.                                       |
| Kualitas Kode & Dokumentasi | Model relasi, form request, middleware role, test feature bertahap, README, dan ERD sederhana.                                              |

## Role Access

| Halaman                    | Guest | User    | Admin   |
| -------------------------- | ----- | ------- | ------- |
| Beranda dan halaman publik | Boleh | Boleh   | Boleh   |
| `/dashboard`               | Login | Boleh   | Ditolak |
| `/my-bookings`             | Login | Boleh   | Ditolak |
| `/admin/dashboard`         | Login | Ditolak | Boleh   |
| `/admin/services`          | Login | Ditolak | Boleh   |
| `/admin/bookings`          | Login | Ditolak | Boleh   |
| `/admin/bookings/history`  | Login | Ditolak | Boleh   |
| `/profile`, `/preferences` | Login | Boleh   | Boleh   |

Navbar guest, user, dan admin mengikuti role access di atas. Navbar mobile memakai hamburger dan dropdown profil menyediakan Profil Saya, Atur Preferensi, serta Logout.

## Data dan ERD

```text
users 1 --- n bookings
bookings 1 --- n booking_service
services 1 --- n booking_service

users:
  id, name, email, password, role, deleted_at

services:
  id, name, description, price, duration_minutes, image, is_active

bookings:
  id, booking_code, user_id, slot, start_time, end_time,
  customer_name, plate_number, vehicle_type, vehicle_model,
  total_price, total_duration_minutes, status, notes,
  cancel_reason, completed_at

booking_service:
  id, booking_id, service_id, price_snapshot, duration_snapshot
```

## Validasi dan Keamanan

- Registrasi publik tidak menerima role dari request dan selalu menghasilkan role `user`.
- User soft deleted tidak dapat login dan emailnya tidak dapat dipakai register ulang.
- User hanya dapat membaca atau membatalkan booking miliknya sendiri.
- User hanya dapat membatalkan booking `pending`.
- Admin status transition divalidasi server-side.
- Booking memakai layanan aktif, hitung ulang total dari database, snapshot pivot, pemeriksaan overlap, serta jam operasional `08:00-17:00 WIB`.
- Upload gambar hanya melalui admin CRUD dan divalidasi server-side.
- Layanan yang pernah dipakai booking tidak dapat dihapus permanen.

## AJAX dan State UI

| Halaman               | Fetch JSON | Loading | Empty | Error |
| --------------------- | ---------- | ------- | ----- | ----- |
| Booking Saya          | Ya         | Ya      | Ya    | Ya    |
| Kelola Layanan        | Ya         | Ya      | Ya    | Ya    |
| Daftar Booking Admin  | Ya         | Ya      | Ya    | Ya    |
| Riwayat Booking Admin | Ya         | Ya      | Ya    | Ya    |

## Penyesuaian yang Disetujui

Beberapa ketentuan PRD asal telah disesuaikan selama implementasi:

- SQLite tetap digunakan, bukan MySQL.
- Format kode booking adalah `PS-0001`, `PS-0002`, dan seterusnya.
- Verifikasi email belum diwajibkan untuk booking.
- Upload avatar ditunda dan navbar memakai fallback inisial.
- Delete user selalu menggunakan soft delete.

## Catatan Tersisa

- Katalog layanan publik penuh beserta pencarian AJAX dan detail layanan publik belum diimplementasikan. Route `/services` tetap dapat dibuka sebagai placeholder.
- Halaman `/about` dan `/contact` masih placeholder sederhana.
- Screenshot dokumentasi belum ditambahkan ke repository.
- Audit responsive dilakukan berdasarkan struktur class Tailwind dan pola komponen. Pemeriksaan visual manual lintas browser tetap direkomendasikan sebelum presentasi.
