# Dokumentasi Testing Postman - Flutter REST API

Dokumentasi ini dibuat berdasarkan route dan controller API yang ada pada project saat ini:

- `app/Config/Routes.php`
- `app/Controllers/Api/*.php`
- `app/Filters/ApiAuthFilter.php`
- `app/Models/ApiTokenModel.php`

Contoh response di bawah mengikuti format response controller. Nilai `id`, token, tanggal, dan data dokter merupakan contoh; hasil aktual mengikuti isi database saat testing.

## Konfigurasi Umum

**Base URL**

```text
http://localhost:8080
```

**Format response sukses**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {}
}
```

**Format response gagal**

```json
{
  "success": false,
  "message": "Pesan error",
  "errors": {}
}
```

**Header umum untuk request JSON**

| Header | Value | Kapan digunakan |
| --- | --- | --- |
| `Accept` | `application/json` | Semua request API |
| `Content-Type` | `application/json` | Request dengan body JSON: `POST`, `PUT`, `PATCH` |
| `Authorization` | `Bearer {{token}}` | Endpoint bertanda wajib token |

## Ringkasan Endpoint

| Endpoint | Method | URL Lengkap | Token | Keterangan |
| --- | --- | --- | --- | --- |
| Register | `POST` | `http://localhost:8080/api/auth/register` | Tidak | Buat akun sekaligus mendapat token |
| Login | `POST` | `http://localhost:8080/api/auth/login` | Tidak | Login dan mengambil token |
| Logout | `POST` | `http://localhost:8080/api/auth/logout` | Ya | Menonaktifkan token yang sedang dipakai |
| List doctors | `GET` | `http://localhost:8080/api/doctors` | Tidak | Daftar dokter, dapat difilter spesialisasi |
| Detail doctor | `GET` | `http://localhost:8080/api/doctors/{id}` | Tidak | Detail dokter |
| Doctor schedules | `GET` | `http://localhost:8080/api/doctors/{id}/schedules` | Tidak | Daftar jadwal dokter |
| Profile | `GET` | `http://localhost:8080/api/profile` | Ya | Melihat profil user login |
| Update profile | `PUT` | `http://localhost:8080/api/profile` | Ya | Mengubah email/profil user login |
| Create appointment | `POST` | `http://localhost:8080/api/appointments` | Ya | Membuat appointment baru |
| List appointment | `GET` | `http://localhost:8080/api/appointments` | Ya | Appointment milik user login |
| Detail appointment | `GET` | `http://localhost:8080/api/appointments/{id}` | Ya | Detail appointment milik user login |
| Cancel appointment | `PATCH` | `http://localhost:8080/api/appointments/{id}/cancel` | Ya | Membatalkan appointment yang masih bisa dibatalkan |
| Queue endpoint | `GET` | `http://localhost:8080/api/appointments/{id}/queue` | Ya | Nomor antrean dan status appointment |

Selain route di atas, request ke path lain di bawah `/api`, misalnya `GET /api/tidak-ada`, ditangani API sebagai response JSON `404`.

## Token Authentication

### Cara Login dan Mengambil Token

1. Buat request `POST http://localhost:8080/api/auth/login`.
2. Pada tab **Headers**, isi:

```text
Accept: application/json
Content-Type: application/json
```

3. Pada tab **Body** pilih **raw** lalu **JSON**, isi:

```json
{
  "login": "pasien@example.com",
  "password": "Password123!",
  "device_name": "Postman"
}
```

Field `login` menerima email atau username. Alternatifnya, email dapat dikirim sebagai field `email`.

4. Klik **Send**. Token berada pada `data.token`:

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "a1b2c3d4e5f6_token_contoh",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "username": "pasien01",
      "email": "pasien@example.com"
    }
  }
}
```

### Cara Menggunakan Token di Postman

Cara manual:

1. Salin nilai `data.token` dari response login atau register.
2. Buka request yang membutuhkan token.
3. Pada tab **Authorization**, pilih tipe **Bearer Token**.
4. Tempel token ke field **Token**.
5. Alternatif melalui tab **Headers**:

```text
Authorization: Bearer a1b2c3d4e5f6_token_contoh
```

Cara menggunakan collection yang disediakan:

1. Import file `postman/Bismillah_Flutter_API.postman_collection.json`.
2. Jalankan request **Login** atau **Register**.
3. Script collection otomatis menyimpan `data.token` ke variable collection `{{token}}`.
4. Semua request dalam folder **Protected** otomatis mengirim `Bearer {{token}}`.

Token disimpan dalam database dalam bentuk hash dan dibuat dengan masa berlaku 30 hari. Setelah endpoint logout berhasil dipanggil, token tersebut tidak dapat digunakan lagi.

## Endpoint Detail

### 1. Register

| Item | Nilai |
| --- | --- |
| Method | `POST` |
| URL | `http://localhost:8080/api/auth/register` |
| Token | Tidak |
| Header | `Accept: application/json`, `Content-Type: application/json` |

**Body JSON**

```json
{
  "username": "pasien01",
  "email": "pasien@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "name": "Pasien Contoh",
  "phone": "081234567890",
  "address": "Jl. Sehat No. 1",
  "device_name": "Postman"
}
```

`name`, `phone`, `address`, dan `device_name` opsional. `pass_confirm` juga diterima sebagai pengganti `password_confirmation`.

**Response sukses - `201 Created`**

```json
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "token": "token_baru_dari_register",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "username": "pasien01",
      "email": "pasien@example.com"
    }
  }
}
```

**Response gagal - `422 Unprocessable Entity`**

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "email": "The email field must contain a unique value."
  }
}
```

Kemungkinan lain: `403` dengan pesan `Registrasi sedang tidak tersedia.` apabila registrasi dinonaktifkan pada konfigurasi Auth.

### 2. Login

| Item | Nilai |
| ---  | ---    |
| Method | `POST` |
| URL | `http://localhost:8080/api/auth/login` |
| Token | Tidak |
| Header | `Accept: application/json`, `Content-Type: application/json` |

**Body JSON**

```json
{
  "login": "pasien@example.com",
  "password": "Password123!",
  "device_name": "Postman"
}
```

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "token_dari_login",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "username": "pasien01",
      "email": "pasien@example.com"
    }
  }
}
```

**Response gagal - `401 Unauthorized`**

```json
{
  "success": false,
  "message": "Kredensial login tidak valid.",
  "errors": []
}
```

### 3. Logout

| Item | Nilai |
| --- | --- |
| Method | `POST` |
| URL | `http://localhost:8080/api/auth/logout` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Logout berhasil.",
  "data": []
}
```

**Response gagal - `401 Unauthorized`**

```json
{
  "success": false,
  "message": "Token autentikasi tidak valid atau sudah kedaluwarsa.",
  "errors": []
}
```

### 4. Profile

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/profile` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "pasien01",
      "email": "pasien@example.com"
    },
    "profile": {
      "id": "1",
      "user_id": "1",
      "name": "Pasien Contoh",
      "phone": "081234567890",
      "address": "Jl. Sehat No. 1",
      "created_at": "2026-05-26 09:00:00",
      "updated_at": "2026-05-26 09:00:00"
    }
  }
}
```

`profile` dapat bernilai `null` apabila user belum memiliki profil.

**Response gagal - `401 Unauthorized`**

```json
{
  "success": false,
  "message": "Token autentikasi diperlukan.",
  "errors": []
}
```

### 5. Update Profile

| Item | Nilai |
| --- | --- |
| Method | `PUT` |
| URL | `http://localhost:8080/api/profile` |
| Token | Wajib |
| Header | `Accept: application/json`, `Content-Type: application/json`, `Authorization: Bearer {{token}}` |

**Body JSON**

```json
{
  "email": "pasien.baru@example.com",
  "name": "Pasien Baru",
  "phone": "081298765432",
  "address": "Jl. Baru No. 2"
}
```

Semua field pada body update bersifat opsional.

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "pasien01",
      "email": "pasien.baru@example.com"
    },
    "profile": {
      "id": "1",
      "user_id": "1",
      "name": "Pasien Baru",
      "phone": "081298765432",
      "address": "Jl. Baru No. 2"
    }
  }
}
```

**Response gagal - `422 Unprocessable Entity`**

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "email": "Email sudah digunakan."
  }
}
```

### 6. List Doctors

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/doctors` |
| Token | Tidak |
| Header | `Accept: application/json` |
| Query opsional | `specialization`, contoh: `?specialization=Gigi` |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "1",
      "name": "drg. Siti Aminah",
      "specialization": "Gigi",
      "phone": "0215550001",
      "email": "siti@example.com",
      "photo": null,
      "schedule": null,
      "created_at": "2026-05-25 10:00:00",
      "updated_at": "2026-05-25 10:00:00"
    }
  ]
}
```

**Response valid tanpa data - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": []
}
```

Controller list doctors tidak mendefinisikan response gagal bisnis khusus; pencarian tanpa hasil tetap sukses dengan `data: []`. Error route yang tidak tersedia dicontohkan pada bagian **Error Umum**.

### 7. Detail Doctor

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/doctors/{{doctor_id}}` |
| Token | Tidak |
| Header | `Accept: application/json` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": "1",
    "name": "drg. Siti Aminah",
    "specialization": "Gigi",
    "phone": "0215550001",
    "email": "siti@example.com",
    "photo": null,
    "schedule": null,
    "created_at": "2026-05-25 10:00:00",
    "updated_at": "2026-05-25 10:00:00"
  }
}
```

**Response gagal - `404 Not Found`**

```json
{
  "success": false,
  "message": "Dokter tidak ditemukan.",
  "errors": []
}
```

### 8. Doctor Schedules

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/doctors/{{doctor_id}}/schedules` |
| Token | Tidak |
| Header | `Accept: application/json` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "1",
      "doctor_id": "1",
      "day": "Senin",
      "start_time": "09:00:00",
      "end_time": "12:00:00"
    }
  ]
}
```

**Response gagal - `404 Not Found`**

```json
{
  "success": false,
  "message": "Dokter tidak ditemukan.",
  "errors": []
}
```

### 9. Create Appointment

| Item | Nilai |
| --- | --- |
| Method | `POST` |
| URL | `http://localhost:8080/api/appointments` |
| Token | Wajib |
| Header | `Accept: application/json`, `Content-Type: application/json`, `Authorization: Bearer {{token}}` |

**Body JSON**

```json
{
  "doctor_id": 1,
  "date": "2026-05-28",
  "time": "09:30",
  "keluhan": "Sakit gigi sejak dua hari"
}
```

`keluhan` opsional. Format `date` wajib `YYYY-MM-DD`.

**Response sukses - `201 Created`**

```json
{
  "success": true,
  "message": "Appointment berhasil dibuat.",
  "data": {
    "id": "10",
    "user_id": "1",
    "doctor_id": "1",
    "date": "2026-05-28",
    "time": "09:30",
    "keluhan": "Sakit gigi sejak dua hari",
    "status": "waiting",
    "no_antrian": null,
    "created_at": "2026-05-26 10:00:00",
    "updated_at": "2026-05-26 10:00:00",
    "doctor_name": "drg. Siti Aminah",
    "specialization": "Gigi"
  }
}
```

**Response gagal - `422 Unprocessable Entity`**

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "date": "The date field must contain a valid date."
  }
}
```

Response `404` dengan pesan `Dokter tidak ditemukan.` diberikan jika `doctor_id` tidak tersedia.

### 10. List Appointment

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/appointments` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": [
    {
      "id": "10",
      "user_id": "1",
      "doctor_id": "1",
      "date": "2026-05-28",
      "time": "09:30",
      "keluhan": "Sakit gigi sejak dua hari",
      "status": "waiting",
      "no_antrian": null,
      "doctor_name": "drg. Siti Aminah",
      "specialization": "Gigi"
    }
  ]
}
```

**Response gagal - `401 Unauthorized`**

```json
{
  "success": false,
  "message": "Token autentikasi tidak valid atau sudah kedaluwarsa.",
  "errors": []
}
```

### 11. Detail Appointment

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/appointments/{{appointment_id}}` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "id": "10",
    "user_id": "1",
    "doctor_id": "1",
    "date": "2026-05-28",
    "time": "09:30",
    "keluhan": "Sakit gigi sejak dua hari",
    "status": "waiting",
    "no_antrian": null,
    "doctor_name": "drg. Siti Aminah",
    "specialization": "Gigi"
  }
}
```

**Response gagal - `404 Not Found`**

```json
{
  "success": false,
  "message": "Appointment tidak ditemukan.",
  "errors": []
}
```

Endpoint hanya dapat melihat appointment milik user yang sesuai dengan token.

### 12. Cancel Appointment

| Item | Nilai |
| --- | --- |
| Method | `PATCH` |
| URL | `http://localhost:8080/api/appointments/{{appointment_id}}/cancel` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Appointment berhasil dibatalkan.",
  "data": {
    "id": "10",
    "status": "cancelled",
    "doctor_name": "drg. Siti Aminah",
    "specialization": "Gigi"
  }
}
```

**Response gagal - `422 Unprocessable Entity`**

```json
{
  "success": false,
  "message": "Appointment tidak dapat dibatalkan.",
  "errors": []
}
```

Appointment dengan status `paid`, `done`, atau `cancelled` tidak dapat dibatalkan.

### 13. Queue Endpoint

| Item | Nilai |
| --- | --- |
| Method | `GET` |
| URL | `http://localhost:8080/api/appointments/{{appointment_id}}/queue` |
| Token | Wajib |
| Header | `Accept: application/json`, `Authorization: Bearer {{token}}` |
| Body | Tidak ada |

**Response sukses - `200 OK`**

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {
    "appointment_id": 10,
    "queue_number": null,
    "status": "waiting"
  }
}
```

`queue_number` berasal dari field `no_antrian` dan masih dapat `null` jika nomor antrean belum ditetapkan.

**Response gagal - `404 Not Found`**

```json
{
  "success": false,
  "message": "Appointment tidak ditemukan.",
  "errors": []
}
```

### 14. Invalid API Endpoint (Fallback)

| Item | Nilai |
| --- | --- |
| Method | Semua method yang mencapai fallback route |
| URL contoh | `http://localhost:8080/api/tidak-ada` |
| Token | Tidak |
| Header | `Accept: application/json` |
| Body | Tidak ada |

**Response gagal - `404 Not Found`**

```json
{
  "success": false,
  "message": "Endpoint API tidak ditemukan.",
  "errors": []
}
```

## Error Umum

**Body JSON rusak - `400 Bad Request`**

Untuk endpoint yang membaca body JSON:

```json
{
  "success": false,
  "message": "Format JSON tidak valid.",
  "errors": {
    "body": "Request body harus berupa JSON yang valid."
  }
}
```

**Endpoint API tidak ada - `404 Not Found`**

```json
{
  "success": false,
  "message": "Endpoint API tidak ditemukan.",
  "errors": []
}
```

## Urutan Testing Step-by-Step

### Persiapan Postman

1. Pastikan server CodeIgniter berjalan pada `http://localhost:8080`.
2. Import `postman/Bismillah_Flutter_API.postman_collection.json` ke Postman.
3. Buka collection variables dan pastikan `base_url` bernilai `http://localhost:8080`.
4. Biarkan variable `token` kosong sebelum register/login pertama.

### A. Register

1. Jalankan **Authentication > Register**.
2. Ganti email dan username jika sudah pernah digunakan.
3. Pastikan status response `201`.
4. Pastikan collection variable `token` otomatis berisi token baru setelah response sukses.

### B. Login

1. Jalankan **Authentication > Login** memakai user yang sudah terdaftar.
2. Pastikan status response `200` dan `data.token` tersedia.
3. Token terbaru otomatis mengganti variable `{{token}}`.

### C. Profile

1. Jalankan **Protected > Profile > Get Profile**.
2. Pastikan request membawa header `Authorization: Bearer {{token}}`.
3. Jalankan **Update Profile** jika ingin menguji perubahan profil; kemudian ulangi **Get Profile**.

### D. List Doctors

1. Jalankan **Public > Doctors > List Doctors**.
2. Jika data tersedia, script akan menyimpan id dokter pertama sebagai `{{doctor_id}}`.
3. Untuk filter, isi query parameter `specialization`, misalnya `Gigi`.

### E. Detail Doctor

1. Pastikan `{{doctor_id}}` terisi dari list doctor atau isi manual pada collection variables.
2. Jalankan **Public > Doctors > Detail Doctor**.
3. Jalankan **Doctor Schedules** untuk memeriksa jadwal dokter yang sama.

### F. Create Appointment

1. Pastikan sudah login dan `{{doctor_id}}` menunjuk dokter valid.
2. Jalankan **Protected > Appointments > Create Appointment**.
3. Ubah tanggal/body jika diperlukan.
4. Pastikan status response `201`; id appointment baru otomatis disimpan sebagai `{{appointment_id}}`.

### G. List Appointment

1. Jalankan **Protected > Appointments > List Appointments**.
2. Hasil hanya berisi appointment milik user dari token aktif.

### H. Detail Appointment

1. Pastikan `{{appointment_id}}` sudah terisi setelah create appointment atau isi manual.
2. Jalankan **Protected > Appointments > Detail Appointment**.
3. Pastikan detail dokter juga muncul sebagai `doctor_name` dan `specialization`.

### I. Queue Endpoint

1. Jalankan **Protected > Appointments > Queue Appointment** sebelum membatalkan appointment.
2. Periksa `data.queue_number` dan `data.status`.
3. Nilai antrean dapat `null` sampai diproses oleh alur aplikasi/admin.

### J. Cancel Appointment

1. Jalankan **Protected > Appointments > Cancel Appointment**.
2. Pastikan status berubah menjadi `cancelled`.
3. Mengulangi cancel terhadap appointment sama akan menghasilkan response `422`.

### K. Logout

1. Setelah testing selesai, jalankan **Protected > Authentication > Logout**.
2. Coba jalankan **Get Profile** menggunakan token lama.
3. Pastikan server memberi response `401` karena token sudah dicabut.

## Catatan Collection

- Variable `base_url`: alamat server API.
- Variable `token`: otomatis terisi oleh request Register/Login yang sukses.
- Variable `doctor_id`: otomatis diambil dari item pertama response List Doctors bila tersedia.
- Variable `appointment_id`: otomatis diambil dari response Create Appointment yang sukses.
- Collection menyertakan request **Invalid API Endpoint** untuk menguji JSON response `404` dari fallback route `/api/(:any)`.
