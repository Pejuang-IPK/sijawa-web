<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Cek jika ada pesan error
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIJAWA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/register.css">
</head>
<body class="auth-page register-page">
    <div class="container">
        <div class="left-section">
            <h1>Mulai atur kuliahmu dengan SIJAWA 🚀</h1>
            <p>Satu akun untuk jadwal, tugas, keuangan, dan waktu santai yang seimbang.</p>
        </div>

        <div class="right-section">
            <div class="form-container">
                <div class="head-content">
                    <h2>Register</h2>
                    <p class="subtitle">Selamat datang</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="register_process.php" method="POST">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>

                    <div class="gap-line">
                        <hr>
                        <span>atau dengan</span>
                        <hr>
                    </div>

                    <button type="submit" class="submit-btn">Daftar</button>
                </form>

                <p class="bottom-text">
                    Sudah punya akun terdaftar? <a href="login.php">Cus Masuk</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>