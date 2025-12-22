<?= $this->include('layout/header') ?>

<section class="section profile-section">
  <div class="container profile-card">
    <h1 class="title">Edit Profil</h1>

    <form action="<?= base_url('profile/update') ?>" method="post" class="form-box">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" 
                   value="<?= esc($user['username'] ?? '') ?>" 
                   class="locked" readonly>
            <small class="note">Username tidak bisa diubah</small>
        </div>

    
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" 
                   value="<?= esc($user['email'] ?? '') ?>" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn save">Simpan</button>
            <a href="<?= base_url('profile/') ?>" class="btn back">Kembali</a>
        </div>

    </form>
  </div>
</section>

<?= $this->include('layout/footer') ?>

<style>
.profile-section {
    padding: 30px 0;
    display: flex;
    justify-content: center;
}

.profile-card {
    width: 100%;
    max-width: 480px;
    background: #fff;
    padding: 25px 30px;
    border-radius: 15px;
    box-shadow: 0px 4px 20px rgba(0,0,0,0.08);
}

.title {
    text-align: center;
    margin-bottom: 25px;
    color: #2E86DE;
    font-size: 26px;
    font-weight: 600;
}

.form-box {
    width: 100%;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #dcdcdc;
    font-size: 15px;
    transition: 0.2s;
}

input:focus {
    border-color: #2E86DE;
    box-shadow: 0px 0px 5px rgba(46,134,222,0.4);
    outline: none;
}

/* Username readonly style */
input.locked {
    background-color: #f0f0f0;
    cursor: not-allowed;
    color: #555;
}

input.locked:focus {
    outline: none;
    box-shadow: none;
}

.note {
    font-size: 12px;
    color: #888;
    margin-top: 4px;
    display: block;
}

.btn-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 12px;
}

.btn {
    width: 100%;
    text-align: center;
    padding: 12px 0;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: 0.2s;
}

.save {
    background: #2E86DE;
    color: white;
    border: none;
}

.save:hover {
    background: #1f6ec7;
}

.back {
    background: #d5d5d5;
    color: #333;
}

.back:hover {
    background: #bcbcbc;
}

@media (max-width: 480px) {
    .profile-card {
        padding: 20px;
    }

    .btn-group {
        flex-direction: column;
    }
}
</style>
