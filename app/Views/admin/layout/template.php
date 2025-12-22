<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Klinik Mawon Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>

<body style="background: #eef3fb;">

<div class="admin-wrapper d-flex">
    <aside class="admin-sidebar">
        <div class="logo">Klinik Mawon</div>

        <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
        <a href="<?= base_url('admin/users') ?>">Users</a>
        <a href="<?= base_url('admin/dokter') ?>">Dokter</a>
        <a href="<?= base_url('admin/appointment') ?>">Appointment</a>
        <a href="<?= base_url('admin/kontak') ?>">Kontak Masuk</a>
        <a href="<?= base_url('logout') ?>">Logout</a>
    </aside>
    <main class="admin-content">
        <?= $this->renderSection('content') ?>
    </main>
</div>

</body>
</html>
