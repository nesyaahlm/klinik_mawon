<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Klinik Mawon - Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            height: 100vh;
            width: 100%;
            background: url('<?= base_url('img/bg klinik.jpg') ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.4);
        }

        .content {
            position: relative;
            z-index: 10;
            text-align: left;
            max-width: 650px;
            padding: 20px;
        }

        .title {
            font-size: 32px;
            font-weight: 600;
            color: #0858A3;
        }

        .subtitle {
            font-size: 20px;
            font-weight: 500;
            color: #0A0F3C;
            margin-bottom: 25px;
        }

        .doctor-img {
            position: absolute;
            right: 40px;
            bottom: 0;
            width:300px;
            z-index: 10;
        }

        .btn-start {
            background: #0A5FE6;
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
        }

        .btn-start:hover {
            background: #084ab5;
            color: white;
        }
    </style>
</head>

<body>

    <div class="hero">
        <div class="overlay"></div>

        <img src="<?= base_url('img/doktergtg.png') ?>" class="doctor-img" alt="Dokter">

        <div class="content">
            <h3 class="title">WELCOME TO KLINIK MAWON</h3>
            <p class="subtitle">Healthcare Made Simple and Reliable.</p>

            <a href="<?= base_url('/home'); ?>" class="btn-start">
                Get Started →
            </a>
        </div>
    </div>

</body>
</html>
