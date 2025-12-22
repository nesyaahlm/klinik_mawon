<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
</head>

<body>

    <nav class="navbar navbar-dark mb-4 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand">Klinik Mawon</span>
            <a href="/logout" class="btn btn-outline-light btn-sm px-3">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('layout/footer'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
body {
    background: #e9f2ff !important;
    font-family: "Poppins", sans-serif;
}

.navbar {
    background: linear-gradient(135deg, #195EDE, #2E86DE) !important;
    padding: 12px 0;
}

.navbar-brand {
    font-weight: 700;
    font-size: 22px;
    letter-spacing: 1px;
}

.navbar a.btn {
    border-radius: 10px;
    font-weight: 600;
}

.navbar a.btn:hover {
    background-color: white !important;
    color: #2E86DE !important;
}

.card {
    border-radius: 18px;
    border: none;
    justify-content: center;
}
</style>
