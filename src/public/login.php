<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Cek jika ada pesan error atau success
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIJAWA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/login.css">
</head>
<body class="auth-page">
    <div class="container">
        <div class="left-section">
            <h1>Selamat datang kembali di SIJAWA👋</h1>
            <p>Yuk lanjutkan perjalananmu jadi mahasiswa yang teratur dan produktif!</p>
        </div>

        <div class="right-section">
            <div class="form-container">
                <div class="header-content">
                    <h2>Login</h2>
                    <p class="subtitle">Selamat datang kembali</p>
                </div>

                <form action="login_process.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="gap-line">
                        <hr class="left-line">
                        <span>atau masuk dengan</span>
                        <hr class="right-line">
                    </div>

                    <button type="submit" class="submit-btn">Masuk</button>
                </form>

                <p class="bottom-text">
                    Belum punya akun terdaftar? <a href="register.php">Yuk Daftar</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>