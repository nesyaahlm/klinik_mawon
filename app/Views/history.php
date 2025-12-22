<section class="section history-page">
  <div class="container history-card">
    <h1>Riwayat Janji</h1>

    <?php if (empty($appointments)): ?>
      <p class="empty-text">Tidak ada riwayat.</p>
    <?php else: ?>
      <table class="table-history">
        <thead>
          <tr>
            <th>ID</th>
            <th>Dokter</th>
            <th>Pasien</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
            <th>No Antrian</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($appointments as $a): ?>
            <tr>
              <td><?= $a['id'] ?></td>
              <td><?= $a['doctor_name'] ?></td>
              <td><?= $a['patient_name'] ?></td>
              <td><?= $a['date'] ?></td>
              <td><?= substr($a['time'], 0, 5) ?></td>
              <td>
                <?php if ($a['status'] === 'done'): ?>
                  <span class="status-done">Selesai</span>
                <?php elseif ($a['status'] === 'confirmed'): ?>
                  <span class="status-confirmed">Bayar di Klinik</span>
                <?php elseif ($a['status'] === 'pending'): ?>
                  <span class="status-pending">Menunggu</span>
                <?php else: ?>
                  <span class="status-waiting"><?= esc($a['status']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if($a['no_antrian']): ?>
                  <span class="queue-badge"><?= $a['no_antrian'] ?></span>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <center><a href="<?= base_url('/home') ?>" class="btn-back">← Kembali</a></center>
  </div>
</section>

<style>
body {
    background: #e9f2ff;
    font-family: "Poppins", sans-serif;
}

.history-page {
    padding: 40px 0;
}

.history-card {
    background: white;
    padding: 35px;
    border-radius: 10px;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.history-card h1 {
    font-size: 28px;
    font-weight: 700;
    color: #2E86DE;
    text-align: center;
    margin-bottom: 25px;
}
.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    font-size: 15px;
    padding: 10px 14px;
    border-radius: 10px;
    background: #2E86DE;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}
.btn-back:hover {
    background: #1557A0;
}
.empty-text {
    text-align: center;
    font-size: 16px;
    padding: 25px 0;
    color: #6c757d;
}

.table-history {
    width: 100%;
    border-collapse: separate;
    border-spacing: 8px 15px;
}

.table-history thead th {
    background: #2E86DE;
    color: white;
    padding: 12px 18px;
    font-size: 14px;
    text-align: center;
    border-radius: 14px 14px 0 0;
}

.table-history tbody tr {
    background: #f7faff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table-history tbody td {
    padding: 12px;
    font-size: 14px;
    text-align: center;
    vertical-align: middle;
}

.status-done {
    display: inline-block;
    background: #27AE69; 
    color: white;
    padding: 6px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
}

.status-confirmed {
    display: inline-block;
    background: #2E86DE; 
    color: white;
    padding: 6px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
}

.status-pending {
    display: inline-block;
    background: #ffc107; 
    color: #644e00;
    padding: 6px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
}

.queue-badge {
    background: #2E86DE;
    color: white;
    padding: 10px 14px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
}
</style>
