<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Edit Dokter</h2>

<form action="<?= base_url('admin/dokter/update/'.$doctor['id']) ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" value="<?= $doctor['name'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Spesialisasi</label>
        <input type="text" name="specialization" class="form-control" value="<?= $doctor['specialization'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Telepon</label>
        <input type="text" name="phone" class="form-control" value="<?= $doctor['phone'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= $doctor['email'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="photo" class="form-control">
        <?php if($doctor['photo']): ?>
            <img src="<?= base_url('uploads/doctors/'.$doctor['photo']) ?>" width="80" class="mt-2">
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= base_url('admin/dokter') ?>" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>
