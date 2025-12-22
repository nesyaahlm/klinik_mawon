<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Klinik Mawon</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --background: #ffffff;
      --foreground: #2E86DE;
      --primary: #2E86DE;
      --border: #e5e7eb;
      --radius: 14px;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #e9f3ff, #ffffff);
      font-family: 'Open Sans', sans-serif;
      color: #1e293b;
    }

    /* Navbar */
    header {
      background-color: white;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* Group logo + text */
    .brand-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo-img {
      height: 40px;
      width: auto;
    }

    .brand-text {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--primary);
      font-family: 'Poppins', sans-serif;
    }

    nav a {
      text-decoration: none;
      color: #4b5563;
      margin: 0 12px;
      font-weight: 500;
      transition: 0.3s;
    }

    nav a:hover {
      color: var(--primary);
    }

    /* LOGIN CARD */
    .login-wrapper {
      max-width: 450px;
      margin: 40px auto;
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      text-align: center;
      margin-bottom: 70px;
    }

    .login-wrapper h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 5px;
    }

    .login-wrapper p {
      color: #6b7280;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 15px;
      border: 1px solid #d1d5db;
      border-radius: 10px;
    }

    button {
      width: 100%;
      background: var(--primary);
      color: white;
      border: none;
      padding: 12px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #1d6cc9;
    }

    .register-link {
      margin-top: 15px;
      font-size: 0.9rem;
    }

    .register-link a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    footer {
      background: #2E86DE;
      padding: 11px;
      text-align: center;
      color: white;
      margin-top: 80px;
    }
  </style>
</head>

<body>

<?= $this->include('layout/header'); ?>

  <!-- LOGIN CARD -->
<div class="login-wrapper">
  <div>
      <h2>Masuk</h2>
      <p>Silakan masuk ke akun Anda</p>

      <?php if(session()->getFlashdata('error')): ?>
          <div style="color:red; margin-bottom:10px;">
              <?= session()->getFlashdata('error') ?>
          </div>
      <?php endif ?>

<form action="<?= base_url('login') ?>" method="post">
    <?= csrf_field() ?>

    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Masuk</button>

    <div class="register-link">
        Belum punya akun? <a href="<?= base_url('register') ?>">Daftar</a>
    </div>
</form>
  </div>
</div>

 <?= $this->include('layout/footer'); ?>
</body>

</html>
