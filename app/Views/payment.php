<?= $this->include('layout/header'); ?>

<style>
:root {
    --primary: #2E86DE;
    --secondary: #4ea3ff;
    --accent: #FFD166;
    --bg-light: #f5faff;
}

.center-page {
    display: flex;
    justify-content: center;
    padding: 50px 20px;
    background: linear-gradient(135deg, #e8f1ff 0%, #ffffff 100%);
    position: relative;
}

.center-page::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top left, rgba(46,134,222,0.05), transparent 70%),
                radial-gradient(circle at bottom right, rgba(255,209,102,0.03), transparent 60%);
    pointer-events: none;
}

.card {
    background: #fff;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    max-width: 600px;
    width: 100%;
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.8s ease forwards;
}

.card h1 {
    color: var(--primary);
    font-size: 28px;
    margin-bottom: 10px;
    text-align: center;
}

.card p {
    text-align: center;
    color: #fff;
    margin-bottom: 25px;
}


.card p.satu {
    text-align: center;
    color: #2E86DE;
    margin-bottom: 25px;
}

.payment-total {
    background: linear-gradient(90deg, #2E86DE);
    padding: 20px;
    border-radius: 16px;
    text-align: center;
    color: white;
    font-weight: 600;
    margin-bottom: 30px;
    box-shadow: 0 6px 15px rgba(46,134,222,0.2);
}

.payment-total h2 {
    font-size: 26px;
    margin: 5px 0 0;
}

.pay-option {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: #f0f6ff;
    padding: 16px 14px;
    border-radius: 14px;
    border: 2px solid transparent;
    cursor: pointer;
    margin-bottom: 14px;
    transition: all 0.25s ease;
    font-weight: 500;
}

.pay-option:hover {
    border-color: var(--primary);
    background: #e4f0ff;
}

.pay-option input[type="radio"] {
    margin-top: 4px;
    accent-color: var(--primary);
}

#qrisBox {
    display: none;
    margin-top: 20px;
    text-align: center;
}

#qrisBox img {
    width: 220px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(46,134,222,0.15);
    margin-bottom: 10px;
}

#qrisBox input[type="file"] {
    margin-top: 10px;
    border-radius: 12px;
}

.btn-submit {
    width: 100%;
    background: #999;
    color: white;
    padding: 15px;
    border: none;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 600;
    margin-top: 25px;
    cursor: not-allowed;
    opacity: 0.6;
    transition: all 0.3s ease;
}

.btn-submit.active {
    background: linear-gradient(45deg, #2E86DE, #1F4F7B);
    cursor: pointer;
    opacity: 1;
    box-shadow: 0 6px 20px rgba(46,134,222,0.3);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

<section class="center-page">
    <div class="card">

        <h1>Pembayaran</h1>
        <p class="satu">Pilih metode pembayaran yang Anda inginkan</p>

<?php if ($appointment['status'] === 'paid'): ?>
    <div style="background:#d4edda;color:#155724;padding:15px;border-radius:12px;margin-bottom:15px;">
        ✅ Anda sudah melakukan pembayaran.<br>
       <?php
$db = \Config\Database::connect();
$row = $db->table('appointments')
          ->where('id', $appointment['id'])
          ->get()
          ->getRow();
?>

Nomor Antrian Anda: <strong><?= esc($row->no_antrian ?? '-') ?></strong>

    <a href="<?= base_url('confirmation/' . $appointment['id']) ?>" 
       style="display:block;text-align:center;
              background:var(--primary);color:#fff;
              padding:14px;border-radius:12px;
              text-decoration:none;margin-top:10px;">
        Lihat Konfirmasi
    </a>

    <?php return; ?>
<?php endif; ?>

        <div class="payment-total">
            <p>Total Pembayaran</p>
            <h2>Rp 150.000</h2>
        </div>

        <form id="paymentForm" action="<?= base_url('payment/process') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="pay-option" onclick="chooseMethod('cod')">
                <input type="radio" name="payment_method" value="cod" id="cod" required>
                <label for="cod">
                    <strong>Bayar di Tempat</strong><br>
                    <small>Bayar di klinik saat datang</small>
                </label>
            </div>

            <div class="pay-option" onclick="chooseMethod('qris')">
                <input type="radio" name="payment_method" value="qris" id="qris">
                <label for="qris">
                    <strong>QRIS</strong><br>
                    <small>Scan QR untuk membayar</small>
                </label>
            </div>

            <div id="qrisBox">
                <img src="<?= base_url('img/qr.png') ?>" alt="QR Code">
                <p style="margin-top:10px;">Upload bukti pembayaran</p>
                <input type="file" name="bukti" id="buktiUpload" accept="image/*">
            </div>

            <button type="submit" id="submitBtn" class="btn-submit" disabled>
                Selesai
            </button>
        </form>

    </div>
</section>

<script>
let selectedMethod = null;

function chooseMethod(method) {
    selectedMethod = method;
    document.getElementById(method).checked = true;
    document.getElementById("qrisBox").style.display = method === "qris" ? "block" : "none";
    activateSubmitButton();
}

function activateSubmitButton() {
    const btn = document.getElementById("submitBtn");
    btn.classList.add("active");
    btn.disabled = false;
}

document.getElementById("paymentForm").addEventListener("submit", function(e) {
    if (!selectedMethod) {
        e.preventDefault();
        alert("Silakan pilih metode pembayaran terlebih dahulu!");
        return;
    }

    if (selectedMethod === "qris") {
        const bukti = document.getElementById("buktiUpload").value;
        if (bukti === "") {
            e.preventDefault();
            alert("Silakan upload bukti pembayaran QRIS!");
            return;
        }
    }
});
</script>

<?= $this->include('layout/footer'); ?>
