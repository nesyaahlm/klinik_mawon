<?= $this->include('layout/header') ?>

<section class="section profile-section">
  <div class="container profile-card">
    <h1 class="title">Tambah Profil</h1>

    <form action="<?= base_url('profile/store') ?>" method="post" enctype="multipart/form-data">

        <!-- Foto Profil -->
        <div class="form-group profile-photo">
            <label>Foto Profil</label>
            <img id="preview" src="https://via.placeholder.com/120" alt="Foto Profil">
            <input type="file" name="profile_image" accept="image/*" onchange="previewImage(event)">
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn save">Simpan</button>
            <a href="<?= base_url('profile') ?>" class="btn back gray">Kembali</a>
        </div>

    </form>
  </div>
</section>

<?= $this->include('layout/footer') ?>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('preview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

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
    text-align: center;
}

.title {
    margin-bottom: 25px;
    color: #2E86DE;
    font-size: 26px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 18px;
    text-align: center;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
}

input[type="text"],
input[type="email"],
input[type="file"] {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #dcdcdc;
    font-size: 15px;
}

.profile-photo img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #2E86DE;
    margin-bottom: 10px;
}

.btn-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 12px;
}

.btn {
    width: 100%;
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
    .btn-group {
        flex-direction: column;
    }
}
</style>
