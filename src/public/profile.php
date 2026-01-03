<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/controller/ProfileController.php';

$userId = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $nama = $_POST['nama'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($nama) || empty($email)) {
            $message = 'Nama dan email tidak boleh kosong';
            $messageType = 'error';
        } else {
            $result = $profileController->updateProfile($userId, $nama, $email);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            
            // Perbarui nama di session jika berhasil
            if ($result['success']) {
                $_SESSION['user_nama'] = $nama;
            }
        }
    } elseif ($_POST['action'] === 'update_password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = 'Semua field password harus diisi';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'Password baru tidak sesuai';
            $messageType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $message = 'Password minimal 6 karakter';
            $messageType = 'error';
        } else {
            $result = $profileController->updatePassword($userId, $oldPassword, $newPassword);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
        }
    }
}

// Ambil data user
$user = $profileController->getUserProfile($userId);

if (!$user) {
    header('Location: login.php');
    exit;
}
?>
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
                    <h2><?php echo htmlspecialchars($user['nama']); ?></h2>
                    <p>Mahasiswa Informatika • Free Plan</p>
                </div>
                </div>

                <hr class="divider">

                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <h3 class="section-title">Detail Personal</h3>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-grid">
                        <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" value="<?php echo htmlspecialchars($user['nama']); ?>" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="form-group">
                        <label class="form-label">Email Kampus</label>
                        <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Simpan Perubahan Profil</button>
                </form>

                <hr class="divider">

                <h3 class="section-title">Ubah Password</h3>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-grid">
                        <div class="form-group form-full">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="old_password" class="form-input" placeholder="Masukkan password lama" required>
                        </div>

                        <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-input" placeholder="Masukkan password baru" required>
                        </div>

                        <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Konfirmasi password baru" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Ubah Password</button>
                </form>

            </div>
        </main>
    </div>
</body>
</html>