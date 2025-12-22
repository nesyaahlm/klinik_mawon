<?= $this->include('layout/header') ?>

<style>
:root {
    --primary: #2d7ff9;        /* Biru utama */
    --secondary: #4ea3ff;      /* Biru muda */
    --bg-light: #E9F5FF;       /* Background halus */
    --font-heading: 'Poppins', sans-serif;
    --font-body: 'Inter', sans-serif;
}

body {
    margin: 0;
    padding: 0;
    background: var(--bg-light);
    font-family: var(--font-body);
    color: #333;
}

.hero {
    width: 100%;
    padding: 80px 20px;
    text-align: center;
    background: linear-gradient(to bottom, #f0f7ff, #ffffff);
}

.hero h2 {
    font-family: var(--font-heading);
    font-size: 34px;
    color: var(--primary);
    margin-bottom: 10px;
}

.hero p {
    font-size: 18px;
    color: #555;
}

.services-grid {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto 60px auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 28px;
}

.service-card {
    background: #ffffff;
    padding: 25px;
    border-radius: 14px;
    text-align: center;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.08);
    transition: 0.25s ease;
}

.service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0px 8px 20px rgba(45, 127, 249, 0.2);
}

.service-card h3 {
    font-size: 20px;
    color: var(--primary);
    margin-bottom: 10px;
}

.service-card p {
    font-size: 15px;
    color: #555;
}

.service-card button {
    background: var(--primary);
    color: white;
    padding: 12px 25px;
    font-size: 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.25s ease;
}

.service-card button:hover {
    background: var(--secondary);
    transform: scale(1.05);
}
</style>

<section>
    <h2 style="text-align:center; font-family:var(--font-heading); margin-bottom:30px; color:var(--primary);">
        Pilih Layanan
    </h2>

    <div class="services-grid">

        <div class="service-card">
            <img src="<?= base_url('img/umum.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Konsultasi Umum</h3>
            <p>Konsultasi langsung dengan dokter umum berpengalaman.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>

        <div class="service-card">
            <img src="<?= base_url('img/jantungg.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Spesialis Jantung</h3>
            <p>Layanan untuk konsultasi seputar kesehatan jantung.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>

        <div class="service-card">
             <img src="<?= base_url('img/anakk.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Spesialis Anak</h3>
            <p>Layanan kesehatan & tumbuh kembang anak.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>
 
        <div class="service-card">
            <img src="<?= base_url('img/kulit.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Spesialis Kulit</h3>
            <p>Menangani jerawat, alergi, infeksi kulit, ruam, dan masalah kulit lainnya.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>

        <div class="service-card">
             <img src="<?= base_url('img/gigi.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Spesialis Gigi</h3>
            <p>Kesehatan gigi dan mulut: konsultasi gigi berlubang, perawatan ortodonti, tips kesehatan gigi, scaling, dll.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>

        <div class="service-card">
            <img src="<?= base_url('img/bidan.jpeg') ?>" width="100px" height="109px" style="margin-bottom:12px;">
            <h3>Spesialis Kebidanan</h3>
            <p>Untuk pemeriksaan kehamilan, haid tidak teratur, kesehatan reproduksi, dan edukasi ibu hamil.</p>
            <a href="<?= base_url('/doctors') ?>">
                <button>Pilih Dokter</button>
            </a>
        </div>

    </div>
     <?= $this->include('layout/footer'); ?>

</section>
