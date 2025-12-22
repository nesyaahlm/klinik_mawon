<section>
<div class="appointment-wrapper">
  <div class="appointment-card animate-slide">
    <div class="card-icon">
<img src="<?= base_url('img/tunggu.png') ?>" alt="Klinik Icon">
    </div>
    <h2>Menunggu Konfirmasi Admin</h2>
    <p>Silakan tunggu admin memverifikasi pembayaran Anda.</p>
    <p>Status: <strong class="<?= $appointment['status'] ?>"><?= esc($appointment['status']) ?></strong></p>

    <?php if($appointment['status'] === 'done' && $appointment['no_antrian'] == null): ?>
        <form action="<?= base_url('confirmation/manual/' . $appointment['id']) ?>" method="post">
            <button type="submit" class="btn">Konfirmasi & Dapatkan Nomor Antrian</button>
        </form>
    <?php elseif($appointment['status'] === 'paid' && $appointment['no_antrian'] != null): ?>
        <script>window.location.href = "<?= base_url('confirmation/' . $appointment['id']) ?>";</script>
    <?php endif; ?>
  </div>
</div>
</section>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #E9F5FF;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    overflow-x: hidden;
    margin: 0;
}

.appointment-wrapper {
    position: relative;
    padding: 20px;
    width: 100%;
    max-width: 450px;
}

.appointment-card {
    background: #fff;
    border-radius: 25px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(46,134,222,0.2);
    position: relative;
    overflow: hidden;
}

.animate-slide {
    opacity: 0;
    transform: translateY(30px);
    animation: slideIn 0.7s forwards;
}

@keyframes slideIn {
    to { opacity: 1; transform: translateY(0); }
}

.card-icon img {
    width: 80px;
    margin-bottom: 15px;
}

h2 {
    color: #2E86DE;
    margin: 0 0 10px;
}

strong.pending { color: #F39C12; font-weight: 600; }
strong.paid { color: #28B463; font-weight: 600; }
strong.done { color: #2E86DE; font-weight: 600; }

.btn {
    margin: 20px auto;
    display: block;
    padding: 12px 25px;
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(46,134,222,0.3);
    position: relative;
    overflow: hidden;
}

.btn:hover {
    background: linear-gradient(135deg, #1557A0, #3498DB);
    box-shadow: 0 8px 20px rgba(46,134,222,0.5);
    transform: translateY(-2px);
}


</style>

