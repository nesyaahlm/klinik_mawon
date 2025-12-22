<?= $this->extend('admin/layout/template') ?>

<?= $this->section('title') ?>Kontak Masuk<?= $this->endSection() ?>

<?= $this->section('content') ?>


<style>
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .dashboard-header {
        background: #ffffff;
        padding: 25px 30px;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .dashboard-header h2 {
        font-size: 26px;
        margin: 0;
        font-weight: 700;
        color: #1a4fa0;
    }

    .admin-card {
        background: white;
        border-radius: 18px;
        padding: 25px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        transition: 0.2s;
        margin-top: 20px;
    }

    .admin-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-card th, .admin-card td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .admin-card th {
        background-color: #1a4fa0;
        color: white;
    }

    .admin-card tr:hover {
        background-color: #f1f1f1;
    }
</style>

<h2>Kotak Masuk</h2>

<div class="d-flex gap-2 mb-3">
    
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Kembali</a>
</div>
    <div class="admin-card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)) : ?>
                    <?php $i = 1; foreach($contacts as $contact): ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= esc($contact['nama']) ?></td>
                            <td><?= esc($contact['email']) ?></td>
                            <td><?= esc($contact['pesan']) ?></td>
                            <td><?= esc($contact['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Belum ada pesan masuk</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
      
</div>

<?= $this->endSection() ?>
