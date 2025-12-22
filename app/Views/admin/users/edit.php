<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"> Edit User</h4>
        </div>

        <div class="card-body">
            <form action="<?= base_url('admin/users/update/'.$user->id) ?>" method="post">
                
  <div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" name="username" class="form-control"
           value="<?= esc($user->username) ?>">
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control"
           value="<?= esc($user->email) ?>">
</div>

<div class="d-flex gap-2 mb-3">
      <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary mb-3">
                        Kembali
                        </a>
<button type="submit" class="btn btn-primary">
                         Simpan
</button>
</div>
                   
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
