<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/profile.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>
        <main class="content">
            <div class="content-header">
                <div>
                <h1>Pengaturan Akun</h1>
                <p>Kelola informasi profil dan preferensi akunmu.</p>
                </div>
            </div>

            <div class="profile-card">
                
                <div class="profile-header">
                <img src="assets/shen_xiaoting.jpg" alt="Profile" class="profile-avatar">
                <div class="profile-info">
                    <h2>Shen Xiaoting</h2>
                    <p>Mahasiswa Informatika • Free Plan</p>
                </div>
                </div>

                <hr class="divider">

                <h3 class="section-title">Detail Personal</h3>
                
                <form>
                <div class="form-grid">
                    <div class="form-group">
                    <label class="form-label">Nama Depan</label>
                    <input type="text" class="form-input" value="Yuharam" placeholder="Masukkan nama depan">
                    </div>
                    <div class="form-group">
                    <label class="form-label">Nama Belakang</label>
                    <input type="text" class="form-input" placeholder="Masukkan nama belakang">
                    </div>

                    <div class="form-group">
                    <label class="form-label">Email Kampus</label>
                    <input type="email" class="form-input" value="yuharam@students.uii.ac.id">
                    </div>
                    <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" class="form-input" placeholder="+62 812...">
                    </div>

                    <div class="form-group form-full">
                    <label class="form-label">Alamat Domisili</label>
                    <input type="text" class="form-input" placeholder="Jl. Kaliurang Km 14...">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </form>

            </div>
        </main>
    </div>
</body>
</html>