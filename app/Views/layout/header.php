<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Mawon</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>

<body>

<header class="navbar">
    <div class="container">
        <div class="logo">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo Klinik">
            <h1>Klinik Mawon</h1>
        </div>

   <nav class="nav-links">
    <?php if(in_groups('admin')): ?>
    <a href="<?= base_url('/') ?>">Home</a>
    <a href="<?= base_url('/admin/dashboard') ?>">Dashboard Admin</a>
    <a href="<?= base_url('services') ?>">Layanan</a>
    <a href="<?= base_url('doctors') ?>">Dokter</a>
    <a href="<?= base_url('kontak') ?>">Kontak Masuk</a>
    <?php else: ?>
        <a href="<?= base_url('/') ?>">Home</a>
        <a href="<?= base_url('services') ?>">Layanan</a>
        <a href="<?= base_url('doctors') ?>">Dokter</a>
        <a href="<?= base_url('kontak') ?>">Kontak Masuk</a>
        <?php endif; ?>
    <?php if (logged_in()) : ?>
        <div class="profile-menu">
            <button class="profile-name" onclick="toggleProfile()">
                <?= esc(user()->username) ?> ⌄
            </button>

            <div class="profile-dropdown" id="profileDropdown">
                <a href="<?= base_url('profile') ?>">Profile</a>
                <a href="<?= base_url('history') ?>">Riwayat</a>
                <a href="<?= base_url('logout') ?>">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <a href="<?= base_url('login') ?>">Login</a>
    <?php endif; ?>
</nav>

    </div>
</header>