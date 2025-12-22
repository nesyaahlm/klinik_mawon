<?= $this->include('layout/header') ?>

<style>
:root {
    --primary: #2E86DE;
    --secondary: #1F4F7B;
    --accent: #FFD166;
    --card-bg: #ffffff;
    --card-shadow: rgba(46, 134, 222, 0.2);
}

body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #e8f1ff 0%, #ffffff 100%);
    position: relative;
}

body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top left, rgba(46,134,222,0.05), transparent 70%),
                radial-gradient(circle at bottom right, rgba(255,209,102,0.03), transparent 60%);
    pointer-events: none;
}

.section {
    padding: 60px 0;
    position: relative;
    z-index: 1;
}

.container.card {
    width: 90%;
    max-width: 500px;
    margin: auto;
    background: var(--card-bg);
    border-radius: 20px;
    padding: 35px 30px;
    box-shadow: 0 8px 25px var(--card-shadow);
    position: relative;
    animation: fadeInUp 0.8s ease forwards;
}

.container.card h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 30px;
    text-align: center;
}

.container.card label {
    font-size: 14px;
    color: #333;
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
}

.container.card input,
.container.card select {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    border: 1px solid #c9dbff;
    background: #f5faff;
    font-size: 15px;
    outline: none;
    transition: all 0.25s ease-in-out;
}

.container.card input:focus,
.container.card select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 10px rgba(46, 134, 222, 0.3);
}

.container.card .input-icon {
    position: relative;
}
.container.card .input-icon input {
    padding-left: 40px;
}
.container.card .input-icon::before {
    content: attr(data-icon);
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: var(--primary);
}

.container.card .btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg, #2E86DE, #1F4F7B);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease, box-shadow 0.3s ease;
}

.container.card .btn:hover {
    background: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(46,134,222,0.3);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

</style>

<section class="section">
    <div class="container card">
        <h2>Pilih Jadwal untuk <?= esc($doctor['name']) ?></h2>

        <form action="<?= base_url('schedule/process') ?>" method="POST">
            <input type="hidden" name="doctor_id" value="<?= esc($id) ?>">

            <div>
                <label>Tanggal Konsultasi</label>
                <input type="date" name="date" required>
            </div>

            <div>
                <label>Waktu Konsultasi</label>
                <select name="time" required>
                    <option value="">Pilih waktu</option>
                    <?php foreach ($scheduleTimes as $time): ?>
                        <option value="<?= esc($time) ?>"><?= esc($time) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn" type="submit">Konfirmasi Jadwal</button>
        </form>
    </div>
</section>

<?= $this->include('layout/footer') ?>
