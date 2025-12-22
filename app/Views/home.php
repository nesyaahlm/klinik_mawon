<?= $this->include('layout/header'); ?>

<style>
.features-section {
    padding: 60px 0;
    text-align: center;
    background: #f4f7fb;
}

.features-section .section-title {
    font-size: 28px;
    color: #2E86DE;
    margin-bottom: 40px;
}

.features-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    justify-content: center;
}
.feature-card {
    background: #fff;
    padding: 30px 25px;
    border-radius: 50px;
    width: 250px;
    box-shadow: 0 10px 25px rgba(46,134,222,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.6s;
    opacity: 0;
    transform: translateY(30px);
}

.feature-card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 15px;
}

.feature-card h4 {
    color: #1F4F7B;
    margin-bottom: 10px;
    font-size: 18px;
}

.feature-card p {
    font-size: 14px;
    color: #555;
    line-height: 1.5;
}

.feature-card.show {
    opacity: 1;
    transform: translateY(0);
}

.feature-card:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 20px 40px rgba(46,134,222,0.2);
}
.campaign-section {
    padding: 60px 0;
    text-align: center;
}

.campaign-section h3 {
    font-size: 28px;
    color: #2E86DE;
}

.contact-wrapper {
    padding: 60px 0;
    background: #fff;
}

.contact-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
}

.contact-box, .contact-center {
    background: #f5faff;
    padding: 25px;
    border-radius: 25px;
    width: 100%;
    max-width: 350px;
    text-align: left;
}

.contact-box h4, .contact-center h4 {
    text-align: center;
    color: #2E86DE;
}

.contact-box hr, .contact-center hr {
    margin: 10px 0 20px 0;
}

.operational-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.operational-list li {
    margin-bottom: 10px;
}

</style>

<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-text">
            <h2>Selamat Datang<br><span>di Klinik Mawon</span></h2>
            <p>Teman Sehat Anda Setiap Hari</p>

            <p class="hero-desc">
                Kami menyediakan berbagai layanan kesehatan dengan dokter-dokter berpengalaman dan fasilitas modern.
            </p>
        </div>

        <div class="hero-img">
            <img src="<?= base_url('img/dokterpasien2.png') ?>" alt="dokter">
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <h3 class="section-title">Mengapa Memilih Klinik Mawon?</h3>

        <div class="features-grid">
            <div class="feature-card">
                <img src="<?= base_url('img/dok.jpg'); ?>">
                <h4>Dokter Profesional</h4>
                <p>Tenaga medis berpengalaman dan bersertifikat.</p>
            </div>

            <div class="feature-card">
                <img src="<?= base_url('img/fasilitas.jpg'); ?>">
                <h4>Fasilitas Lengkap</h4>
                <p>Alat medis modern dan ruang pelayanan nyaman.</p>
            </div>

            <div class="feature-card">
                <img src="<?= base_url('img/pelayanan.jpg'); ?>">
                <h4>Pelayanan Cepat</h4>
                <p>Proses administrasi dan pemeriksaan lebih efisien.</p>
            </div>
        </div>
    </div>
</section>

<section class="campaign-section">
    <div class="container">
        <h3>Ayo Booking di Klinik Mawon!</h3>
        <h5>Cepat, Tepat, Sehat</h5>
        
    </div>
</section>

<section class="contact-wrapper">
    <div class="container contact-grid">

        <div class="contact-box">
            <h4>Jam Operasional Klinik</h4>
            <hr>
            <ul class="operational-list">
                <li><strong>Senin – Jumat:</strong> 08.00 – 17.00</li>
                <li><strong>Sabtu:</strong> 08.00 – 14.00</li>
                <li><strong>Minggu & Hari Besar:</strong> Libur</li>
                <li><strong>Pelayanan Praktik Dokter:</strong> Sesuai Jadwal</li>
            </ul>
        </div>

        <div class="contact-center">
            <h4>Our Contact</h4>
            <hr>
            <p>Badan Layanan Umum (BLU)<br>Klinik Mawon Cilacap Jawa Tengah</p>
            <p><i>Jl. Dr. Kemenangan No. 1, Cilacap</i></p>
            <p><i>+62 895 2893 882 (Emergency Call)</i></p>
            <p><i>klinikmawon@gmail.com</i></p>
        </div>

    </div>
</section>

<script>
const featureCards = document.querySelectorAll('.feature-card');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.classList.add('show');
        }
    });
}, { threshold: 0.3 });

featureCards.forEach(card => observer.observe(card));
</script>

<?= $this->include('layout/footer'); ?>
