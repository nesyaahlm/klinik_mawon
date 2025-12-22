<?= $this->include('layout/header'); ?>

<style>
:root {
    --primary: #2E86DE;
    --secondary: #1F4F7B;
    --bg-light: #f5faff;
    --card-shadow: rgba(46,134,222,0.15);
}

.center-page {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
    background: var(--bg-light);
    font-family: 'Poppins', sans-serif;
}

.contact-card {
    background: #fff;
    max-width: 500px;
    width: 100%;
    padding: 35px 30px;
    border-radius: 20px;
    box-shadow: 0 10px 25px var(--card-shadow);
    text-align: center;
    animation: fadeInUp 0.8s ease forwards;
}

.contact-card h1 {
    color: var(--primary);
    margin-bottom: 10px;
}

.contact-card p {
    margin-bottom: 25px;
    color: #555;
}

.contact-card form {
    text-align: left;
}

.contact-card label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #333;
}

.contact-card input,
.contact-card textarea {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 15px;
    border-radius: 12px;
    border: 1px solid #c9dbff;
    background: #f5faff;
    font-size: 14px;
    outline: none;
    transition: all 0.25s ease-in-out;
}

.contact-card input:focus,
.contact-card textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 8px rgba(46,134,222,0.3);
}

.contact-card button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease, box-shadow 0.3s ease;
}

.contact-card button:hover {
    background: linear-gradient(45deg, var(--secondary), var(--primary));
    box-shadow: 0 8px 20px rgba(46,134,222,0.4);
    transform: translateY(-2px);
}

.flash-message {
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-weight: 500;
}

.flash-success { background: #d4edda; color: #155724; }
.flash-error { background: #f8d7da; color: #721c24; }

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

<section class="center-page">
    <div class="contact-card">
        <h1>Kontak Kami</h1>
        <p>Silakan hubungi kami melalui form berikut.</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="flash-message flash-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash-message flash-error">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('contact/send') ?>" method="POST">
            <?= csrf_field() ?>

            <label>Nama</label>
            <input type="text" name="nama" required placeholder="Masukkan nama lengkap">

            <label>Email</label>
            <input type="email" name="email" required placeholder="Masukkan email">

            <label>Pesan</label>
            <textarea name="pesan" required placeholder="Tulis pesan Anda..." rows="5"></textarea>

            <button type="submit">Kirim Pesan</button>
        </form>
    </div>
</section>

<?= $this->include('layout/footer'); ?>
