<?= $this->extend('admin/layout/template') ?>
<?= $this->section('content') ?>

<h2>Edit Appointment</h2>

<form action="<?= base_url('admin/appointment/update/'.$appointment['id']) ?>" method="post">

    <label>User</label>
    <select name="user_id" class="form-control">
        <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= $u['id']==$appointment['user_id']?'selected':'' ?>>
                <?= $u['username'] ?>
            </option>
        <?php endforeach ?>
    </select>

    <label>Dokter</label>
    <select name="doctor_id" class="form-control">
        <?php foreach ($doctors as $d): ?>
            <option value="<?= $d['id'] ?>" <?= $d['id']==$appointment['doctor_id']?'selected':'' ?>>
                <?= $d['name'] ?>
            </option>
        <?php endforeach ?>
    </select>

    <label>Tanggal</label>
    <input type="date" name="date" class="form-control" value="<?= $appointment['date'] ?>">

    <label>Jam</label>
    <input type="time" name="time" class="form-control" value="<?= $appointment['time'] ?>">

    <label>Status</label>
    <select name="status" class="form-control">
        <option value="pending" <?= $appointment['status']=='pending'?'selected':'' ?>>Pending</option>
        <option value="confirmed" <?= $appointment['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
        <option value="done" <?= $appointment['status']=='done'?'selected':'' ?>>Done</option>
    </select>

    <h4 class="mt-4">Bukti Pembayaran</h4>

<?php if (!empty($appointment['proof'])): ?>
    <img src="<?= base_url('img/'.$appointment['proof']) ?>" 
         alt="Bukti Pembayaran" width="200" class="mb-3 border">
<?php else: ?>
    <p class="text-danger">Hanya dapat di input oleh user</p>
<?php endif ?>


    <button class="btn btn-primary mt-3">Update</button>

    <?php if (!empty($appointment['proof'])): ?>
    <a href="<?= base_url('admin/payment/confirm/'.$appointment['id']) ?>"
       class="btn btn-success mt-3"
       onclick="return confirm('Konfirmasi pembayaran ini?')">
       Konfirmasi Pembayaran
    </a>
<?php endif ?>


</form>

<?= $this->endSection() ?>
