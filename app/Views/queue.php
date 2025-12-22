<?= $this->include('layout/header') ?>

<section class="section">
  <div class="container card">
    <h1>Nomor Antrian Anda</h1>

    <div class="queue-box">
      <h2><?= esc(session('queue_number') ?? $queue_number ?? '---') ?></h2>
    </div>

    <p>Silakan tunggu, dokter sedang memproses pasien sebelumnya.</p>

    <a class="btn" href="<?= base_url('/') ?>">Kembali ke Beranda</a>
  </div>
</section>

<?= $this->include('layout/footer') ?>
-
<style>
.section {
    padding: 50px 0;
    background: linear-gradient(to bottom, #ffffff);
}

.section .container {
    width: 90%;
    max-width: 600px;
    margin: auto;
}

.card {
    background: #eef7ff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.08);
    text-align: center;
    font-family: 'Open Sans', sans-serif;
}

/* Judul */
.card h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 26px;
    font-weight: 600;
    color: #2E86DE;
    margin-bottom: 25px;
}

.queue-box {
    background: #2E86DE;
    padding: 25px;
    border-radius: 15px;
    margin: 20px auto;
    width: 180px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.queue-box h2 {
    font-size: 48px;
    color: white;
    font-weight: 700;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

.card p {
    color: #555;
    margin-bottom: 25px;
    font-size: 15px;
}

.card .btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: #2E86DE;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    color: #fff;
    font-size: 16px;
    transition: 0.2s ease;
}

.card .btn:hover {
    background: #1557A0;
}
</style>

