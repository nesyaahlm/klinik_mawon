<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Daftar Users</h2>
<a href="<?= base_url('admin/users/create') ?>" class="btn btn-success mb-3">Tambah User</a>
<a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary mb-3">
    Kembali
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach($users as $user): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= esc($user->username) ?></td>
            <td><?= esc($user->email) ?></td>
            <td>
                <a href="<?= base_url('admin/users/edit/'.$user->id) ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= base_url('admin/users/delete/'.$user->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
