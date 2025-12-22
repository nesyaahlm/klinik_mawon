<?= $this->extend('admin/layout/template') ?>

<?= $this->section('title') ?>Dashboard Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .dashboard-header {
        background: #ffffff;
        padding: 25px 30px;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .dashboard-header h2 {
        font-size: 26px;
        margin: 0;
        font-weight: 700;
        color: #1a4fa0;
    }

    .admin-card-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 10px;
    }

    .admin-card {
        background: white;
        border-radius: 18px;
        padding: 25px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        transition: 0.2s;
    }

    .admin-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 26px rgba(0,0,0,0.08);
    }

    .admin-card h4 {
        font-size: 18px;
        font-weight: 600;
        color: #1a4fa0;
        margin-bottom: 8px;
    }

    .admin-card h1 {
        font-size: 48px;
        font-weight: 700;
        color: #1a4fa0;
        margin: 10px 0 20px;
    }

    .admin-card .btn {
        width: 100%;
        margin-bottom: 10px;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px;
    }

    .btn-outline {
        background: #ffffff;
        border: 1px solid #1a4fa0;
        color: #1a4fa0;
    }

    .btn-outline:hover {
        background: #1a4fa0;
        color: white;
    }

    .btn-green {
        background: #23b15a;
        color: white;
    }

    .btn-green:hover {
        background:#1d9b4e;
        color:white;
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Area Khusus Admin Klinik Mawon</h2>
        <p style="color:#555; margin-top:5px;">
            Kelola data Klinik Mawon di sini.
        </p>
    </div>

    <div class="admin-card-wrapper">
        <div class="admin-card">
            <center><h4>Total User</h4></center>
            <center><h1><?= $total_users ?></h1></center>
        </div>
        <div class="admin-card">
            <center><h4>Appointment Hari Ini</h4></center>
            <center><h1><?= $today_appointments ?></h1></center>
        </div>
        <div class="admin-card">
            <center><h4>Total Dokter</h4></center>
            <center><h1><?= $total_doctors ?></h1></center>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
