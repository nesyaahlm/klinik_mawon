<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Daftar Appointment</h2>

<div class="d-flex gap-2 mb-3">
    
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Kembali</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>User</th>
            <th>Dokter</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Status</th>
            <th>Bukti Bayar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach ($appointments as $a): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $a['username'] ?></td>
            <td><?= $a['doctor_name'] ?></td>
            <td><?= $a['date'] ?></td>
            <td><?= $a['time'] ?></td>
            <td><?= $a['status'] ?></td>
            <td>
    <?php if (!empty($a['proof'])): ?>
      <a href="<?= base_url('img/' . $a['proof']) ?>" target="_blank">
   <img src="<?= base_url('img/' . $a['proof']) ?>" width="80">

</a>

    <?php else: ?>
        <span class="text-danger">CASH</span>
    <?php endif; ?>
</td>

            <td>
                <a href="<?= base_url('admin/appointment/edit/'.$a['id']) ?>" class="btn btn-warning">Edit</a>
                <a onclick="return confirm('Hapus?')" href="<?= base_url('admin/appointment/delete/'.$a['id']) ?>" class="btn btn-danger">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
