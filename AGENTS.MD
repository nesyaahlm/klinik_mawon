## Current Project Condition

Hasil audit project:
- Project memakai CodeIgniter 4.
- Web lama sudah berjalan dengan route web biasa.
- Belum ada REST API JSON untuk Flutter.
- Belum ada group route /api.
- Login/register saat ini masih memakai Myth/Auth berbasis session web.
- Controller lama masih banyak return view() atau redirect().
- API mobile harus ditambahkan tanpa merusak fitur web lama.
- Jangan mengubah controller web lama jika tidak diperlukan.
- Buat controller API baru di folder app/Controllers/Api.
- Route API baru dibuat di app/Config/Routes.php dengan prefix /api.
- Gunakan response JSON konsisten.
- Endpoint harus bisa dites lewat Postman.
- Jika ada tabel/model yang belum sesuai, jangan langsung hapus data lama. Tambahkan migrasi/field secara aman.

## Important Safety Rules

- Jangan rewrite project dari awal.
- Jangan hapus route web lama.
- Jangan hapus controller lama.
- Jangan ubah tampilan Figma/web lama kecuali diminta.
- Jangan mengubah fitur yang sudah jalan.
- Fokus membuat API baru untuk Flutter.
- Jika ada konflik field database, laporkan dulu sebelum mengubah besar-besaran.
- Gunakan nama field yang sudah ada jika memungkinkan.
- Jika perlu field baru seperti no_antrian, payment_status, keluhan, tambahkan dengan migrasi aman.