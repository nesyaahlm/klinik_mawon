<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Tambah Dokter</h2>

<form action="<?= base_url('admin/dokter/store') ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Spesialisasi</label>
        <input type="text" name="specialization" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Telepon</label>
        <input type="text" name="phone" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="photo" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/dokter') ?>" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>
