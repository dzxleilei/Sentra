# 🔐 Test Credentials - Sentra Booking System

## Admin User

- Email: `admin@sentra.test`
- Password: `password`
- Role: **Admin**
- Dashboard: http://127.0.0.1:8000/admin/dashboard

## Verifikator User

- Email: `verifikator@sentra.test`
- Password: `password`
- Role: **Verifikator**
- Dashboard: http://127.0.0.1:8000/verifikator/dashboard

## Peminjam User

- Email: `peminjam@sentra.test`
- Password: `password`
- Role: **Peminjam**
- Dashboard: http://127.0.0.1:8000/peminjam/dashboard

---

## Fitur yang Sudah Diimplementasikan

### Admin Dashboard ✓

- Manajemen Ruangan (CRUD)
- Manajemen Barang (CRUD)
- Kelola Pelanggaran
- Laporan Peminjaman dengan Export CSV

### Verifikator Dashboard ✓

- Notifikasi & Tugas Validasi
- Detail Booking dan Validasi Scan QR
- Laporan Pelanggaran

### Peminjam Dashboard ✓

- Dashboard dengan statistik
- Booking Barang Sarpras
- Booking Ruangan
- Lihat Barang dalam Ruangan
- Riwayat Peminjaman
- Status Penalti

---

## Cara Login & Test

1. Buka http://127.0.0.1:8000/login
2. Gunakan salah satu kredensial di atas
3. Akan otomatis redirect ke dashboard sesuai role
