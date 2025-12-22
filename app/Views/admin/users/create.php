<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Tambah User</h2>
<a href="<?= base_url('admin/users') ?>" class="btn btn-secondary mb-3">
    kembali
</a>


<form action="<?= base_url('admin/users/store') ?>" method="post">

    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>


<?= $this->endSection() ?>
