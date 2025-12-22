<?= $this->include('layout/header') ?>

<style>
    body {
        background-color: #E9F5FF;
        font-family: Arial, sans-serif;
    }

    .section {
        display: flex;
        justify-content: center;
        padding: 30px 15px;
    }

    .card {
        background: #ffffff;
        width: 450px;
        padding: 25px;
        border-radius: 14px;
        border: 2px solid #2E86DE;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
       margin-bottom: 20px;
    }

    h1 {
        text-align: center;
        color: #1557A0;
        margin-bottom: 20px;
    }

    .card p {
        font-size: 15px;
        color: #1557A0;
        margin: 6px 0;
        text-align: left !important;
    }

    .antrian-title {
        text-align: center;
        color: #2E86DE;
        margin-top: 25px;
        margin-bottom: 10px;
        font-size: 20px;
        font-weight: bold;
    }

    .antrian-number {
        background: #2E86DE;
        color: white;
        padding: 15px;
        border-radius: 12px;
        font-size: 32px;
        text-align: center;
        font-weight: bold;
        margin-bottom: 20px;
        border: 3px solid #1557A0;
    }

    .btn {
        display: block;
        text-align: center;
        background: #1557A0;
        color: white;
        padding: 12px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn:hover {
        background: #2E86DE;
        color: white;
    }
</style>

<section class="section">
  <div class="container card">
    <h1>✅ Konfirmasi Berhasil</h1>

    <p><strong>Nama Pasien:</strong> <?= esc($user->username ?? 'N/A') ?></p>
    <p><strong>Dokter:</strong> <?= esc($doctor['name'] ?? 'N/A') ?></p>
    <p><strong>Tanggal:</strong> <?= esc($appointment['date']) ?></p>
    <p><strong>Waktu:</strong> <?= esc($appointment['time']) ?></p>

    <hr>

    <h2 class="antrian-title">Nomor Antrian Anda</h2>
    <div class="antrian-number">
        <?= esc($appointment['no_antrian'] ?? '---') ?>
    </div>

    <a class="btn" href="<?= base_url('history') ?>">Lihat Riwayat Booking</a>
  </div>
</section>

<?= $this->include('layout/footer') ?>
