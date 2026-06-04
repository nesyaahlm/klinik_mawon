
## DESIGN.md

```md
# DESIGN.md

## Nama Project
Booking Klinik Online

## Deskripsi
Aplikasi Booking Klinik Online adalah sistem untuk memudahkan pasien melakukan pendaftaran dan booking dokter secara online tanpa harus antre manual di klinik.

User dapat memilih dokter, memilih keluhan atau kategori penyakit, menentukan jadwal, memilih metode pembayaran, lalu mendapatkan kode antrian. Admin dapat mengelola dokter, pasien, appointment, dan pembayaran.

Project website ini akan dilanjutkan dengan pembuatan API agar bisa dihubungkan ke aplikasi mobile Flutter.

## Referensi Design
Design mengikuti tampilan dari Figma project “Kel 7 DIP”.

Link Figma:
https://www.figma.com/design/BjZat19egOytHJljqdSu03/Kel-7-DIP?node-id=0-1&p=f&t=AmxnyZVLXscXSGE9-0

## Style UI
Gunakan tampilan sederhana, bersih, dan mudah dipahami.

Warna utama:
- Biru muda
- Biru tua
- Putih
- Abu-abu muda

Nuansa:
- Medical / klinik
- Bersih
- Profesional
- Mudah digunakan

## Halaman Website

### 1. Landing Page
Berisi:
- Navbar
- Nama aplikasi klinik
- Deskripsi singkat layanan
- Tombol login/register
- Informasi layanan klinik
- Informasi dokter

### 2. Login
Berisi:
- Input email/username
- Input password
- Tombol login
- Link register

### 3. Register
Berisi:
- Nama lengkap
- Email
- Nomor HP
- Password
- Konfirmasi password
- Tombol daftar

### 4. Home User
Berisi:
- Sapaan user
- Informasi booking
- Daftar dokter
- Menu booking
- Riwayat appointment

### 5. Pilih Dokter
Berisi:
- List dokter
- Foto dokter
- Nama dokter
- Spesialis
- Jadwal praktik
- Tombol pilih dokter

### 6. Booking Appointment
Berisi:
- Dokter yang dipilih
- Input keluhan
- Pilihan tanggal
- Pilihan jam
- Pilihan metode pembayaran
- Tombol booking

### 7. Pembayaran
Berisi:
- Detail appointment
- Total pembayaran
- Pilihan:
  - Transfer bank
  - Bayar di tempat
- Upload bukti pembayaran jika transfer
- Tombol kirim pembayaran

### 8. Kode Antrian
Berisi:
- Kode antrian
- Nama pasien
- Nama dokter
- Tanggal booking
- Jam booking
- Status pembayaran
- Status booking

### 9. Riwayat
Berisi:
- Daftar appointment user
- Nama dokter
- Tanggal
- Jam
- Status booking
- Status pembayaran
- Kode antrian

### 10. Profile
Berisi:
- Data user
- Edit profile
- Logout

## Halaman Admin

### 1. Login Admin
Berisi:
- Email/username
- Password
- Tombol login

### 2. Dashboard Admin
Berisi:
- Jumlah user
- Jumlah dokter
- Jumlah appointment
- Jumlah pembayaran pending

### 3. Data Dokter
Berisi:
- Tabel dokter
- Tambah dokter
- Edit dokter
- Hapus dokter

### 4. Tambah Dokter
Form:
- Nama dokter
- Spesialis
- Kategori penyakit
- Jadwal praktik
- Nomor HP
- Foto dokter
- Status

### 5. Data User
Berisi:
- Tabel user/pasien
- Tambah user
- Edit user
- Hapus user

### 6. Data Appointment
Berisi:
- Tabel appointment
- Nama pasien
- Nama dokter
- Tanggal
- Jam
- Keluhan
- Status booking
- Status pembayaran
- Aksi approve/reject

### 7. Pembayaran Admin
Berisi:
- Data pembayaran
- Bukti transfer
- Metode pembayaran
- Status
- Tombol approve
- Tombol reject

## Flow User
1. User register.
2. User login.
3. User memilih dokter.
4. User mengisi keluhan dan jadwal.
5. User memilih metode pembayaran.
6. Jika transfer, user upload bukti pembayaran.
7. Jika bayar di tempat, status menunggu konfirmasi.
8. Admin mengecek booking dan pembayaran.
9. Admin approve pembayaran/booking.
10. Sistem membuat kode antrian.
11. User melihat kode antrian.
12. User datang ke klinik membawa kode antrian.

## Flow Admin
1. Admin login.
2. Admin melihat dashboard.
3. Admin mengelola dokter.
4. Admin mengelola user.
5. Admin mengecek appointment.
6. Admin mengecek pembayaran.
7. Admin approve/reject pembayaran.
8. Admin memastikan kode antrian muncul.

## Data Penting untuk Mobile Flutter
API harus mengembalikan data JSON yang mudah dipakai Flutter.

Data yang perlu tersedia:
- User login token
- Data profile
- List dokter
- Detail dokter
- List appointment user
- Detail appointment
- Status pembayaran
- Kode antrian
- Riwayat booking

## Catatan Pengembangan
- Website tetap berjalan seperti sebelumnya.
- API dibuat untuk kebutuhan mobile.
- Tampilan mengikuti design Figma.
- Backend harus stabil sebelum Flutter mulai dibuat.
- Semua endpoint harus bisa diuji di Postman.