<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Klinik Mawon</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --background: #ffffff;
      --primary: #2E86DE;
      --primary-dark: #1d6cc9;
      --text: #1e293b;
      --muted: #6b7280;
      --border: #d8e0ec;
      --radius: 16px;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom right, #eaf2ff, #ffffff);
      font-family: 'Open Sans', sans-serif;
      color: var(--text);
    }

    .login-wrapper {
      max-width: 450px;
      margin: 60px auto;
      background: white;
      padding: 45px 40px;
      border-radius: var(--radius);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .login-wrapper h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 10px;
    }

    .login-wrapper p {
      color: var(--muted);
      margin-bottom: 25px;
      font-size: 0.95rem;
    }

    input {
      width: 100%;
      padding: 14px 15px;
      margin-bottom: 18px;
      border: 1px solid var(--border);
      border-radius: 12px;
      font-size: 0.95rem;
      background: #f9fbff;
      transition: 0.3s;
    }

    input:focus {
      border-color: var(--primary);
      background: white;
      outline: none;
      box-shadow: 0 0 5px rgba(46, 134, 222, 0.3);
    }

    button {
      width: 100%;
      background: var(--primary);
      color: white;
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 12px;
      cursor: pointer;
      border: none;
      transition: 0.35s;
    }

    button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 14px rgba(46, 134, 222, 0.35);
    }

    .register-link {
      margin-top: 18px;
      font-size: 0.9rem;
      color: var(--muted);
    }

    .register-link a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

<?= $this->include('layout/header'); ?>

<div class="login-wrapper">

  <h2>Masuk</h2>
  <p>Silakan masuk ke akun Anda</p>

  <?php if(session()->getFlashdata('error')): ?>
      <div style="color:red; margin-bottom:10px;">
          <?= session()->getFlashdata('error') ?>
      </div>
  <?php endif ?>

  <?php if(session()->getFlashdata('message')): ?>
      <div style="color:green; margin-bottom:10px;">
          <?= session()->getFlashdata('message') ?>
      </div>
  <?php endif ?>

 <form action="<?= base_url('login'); ?>" method="post">

      <?= csrf_field() ?>

      <input type="text" name="login" placeholder="Email atau Username" required>

      <input type="password" name="password" placeholder="Password" required>

      <label style="display:flex; align-items:center; gap:6px; margin-bottom:15px; font-size:0.9rem; color:var(--muted);">
          <input type="checkbox" name="remember" style="width:auto;"> Ingat saya
      </label>

      <button type="submit">Masuk</button>

      <div class="register-link">
          Belum punya akun? <a href="<?= url_to('register') ?>">Daftar</a>
      </div>
  </form>
</div>

<?= $this->include('layout/footer'); ?>

</body>
</html>
