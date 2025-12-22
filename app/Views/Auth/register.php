<?= $this->include('layout/header'); ?>

<style>
    :root {
        --primary: #2E86DE;
        --border: #d1d5db;
    }

    body {
        background: linear-gradient(to bottom, #e9f3ff, #ffffff);
        font-family: 'Open Sans', sans-serif;
        color: #1e293b;
    }

    .register-container {
        max-width: 480px;
        margin: 90px auto;
        padding: 20px;
    }

    .register-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .register-box h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .success {
        background: #d1fae5;
        border: 1px solid #10b981;
        padding: 10px;
        border-radius: 8px;
        color: #065f46;
        margin-bottom: 15px;
    }

    label {
        display: block;
        text-align: left;
        margin-top: 15px;
        font-weight: 600;
        color: #374151;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 1rem;
    }

    .btn {
        margin-top: 20px;
        width: 100%;
        background: var(--primary);
        color: white;
        padding: 12px;
        font-size: 1rem;
        border: none;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        background: #1d6cc9;
    }

    .login-link {
        margin-top: 15px;
        font-size: 0.95rem;
    }

    .login-link a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="register-container">
    <div class="register-box">
        <h2>Daftar Akun Baru</h2>
        <?php if (session()->getFlashdata('message')): ?>
            <p class="success"><?= session()->getFlashdata('message'); ?></p>
        <?php endif; ?>
        <?php if (session('errors')): ?>
            <div style="color:red; margin-bottom:10px;">
                <?php foreach (session('errors') as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>
        <form action="<?= url_to('register') ?>" method="post">
            <?= csrf_field(); ?>

            <label>Username</label>
            <input type="text" name="username" value="<?= old('username') ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= old('email') ?>" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Konfirmasi Password</label>
            <input type="password" name="pass_confirm" required>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="login-link">
            Sudah punya akun?
            <a href="<?= url_to('login'); ?>">Login</a>
        </p>
    </div>
</div>

<?= $this->include('layout/footer'); ?>
