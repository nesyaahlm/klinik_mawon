<?= $this->include('layout/header') ?>

<section class="section">
  <div class="container card">
    <h1>Profil Saya</h1>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="profile-info">
        <p><strong>Username:</strong> <?= esc(user()->username) ?></p>
        <p><strong>Email:</strong> <?= esc(user()->email) ?></p>
    </div>

    <div class="btn-group">
        <a href="<?= base_url('profile/edit') ?>" class="btn primary">Edit Profil</a>
        <a href="<?= base_url('/home') ?>" class="btn secondary">Kembali</a>
    </div>
  </div>
</section>

<?= $this->include('layout/footer') ?>

<style>
.section {
    padding: 50px 20px;
    background: #f4f7fb;
    min-height: 100vh;
}

.card {
    max-width: 600px;
    margin: auto;
    background: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

h1 {
    text-align: center;
    color: #2E86DE;
    font-size: 28px;
    margin-bottom: 25px;
}

.alert-success {
    background: #dff5e1;
    color: #1c7c36;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.profile-info p {
    font-size: 16px;
    margin-bottom: 12px;
    color: #333;
}

.profile-info strong {
    color: #2E86DE;
}

.btn-group {
    margin-top: 25px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn {
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
    text-align: center;
}

.btn.primary {
    background: #2E86DE;
    color: #fff;
}

.btn.primary:hover {
    background: #1f6ec7;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.btn.secondary {
    background: #aaa;
    color: #fff;
}

.btn.secondary:hover {
    background: #888;
    transform: translateY(-2px);
}

@media (max-width: 480px) {
    .card {
        padding: 30px 20px;
    }

    .btn-group {
        flex-direction: column;
    }
}
</style>
