<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Daftar Dokter</h2>

<div class="d-flex gap-2 mb-3">
    <a href="<?= base_url('admin/dokter/create') ?>" class="btn btn-primary">Tambah Dokter</a>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Kembali</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Spesialisasi</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($doctors as $doc): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td>
                <?php if(!empty($doc['photo']) && file_exists(FCPATH.'img/'.$doc['photo'])): ?>
                    <img src="<?= base_url('img/'.$doc['photo']) ?>" alt="<?= $doc['name'] ?>" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                <?php else: ?>
                    <img src="<?= base_url('img/default-doctor.jpg') ?>" alt="Default Doctor" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                <?php endif; ?>
            </td>
            <td><?= $doc['name'] ?></td>
            <td><?= $doc['specialization'] ?></td>
            <td><?= $doc['phone'] ?></td>
            <td><?= $doc['email'] ?></td>
            <td>
                <a href="<?= base_url('admin/dokter/edit/'.$doc['id']) ?>" class="btn btn-warning btn-sm">Edit</a>

                <a href="<?= base_url('admin/dokter/delete/'.$doc['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus dokter ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
