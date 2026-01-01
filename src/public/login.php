<?php
session_start();

require_once __DIR__ . '/../app/AuthController.php';

// Jika sudah login, redirect ke keuangan
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: keuangan.php');
    exit();
}

// Cek jika ada pesan error atau success
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);

if(isset($_POST['submit'])){
    $userData = login($_POST);
    if($userData){
        $_SESSION['user_id'] = $userData['id_mahasiswa'];
        $_SESSION['id_mahasiswa'] = $userData['id_mahasiswa'];
        $_SESSION['nama'] = $userData['nama'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['success'] = "Login berhasil!";
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = "Email atau password salah.";
        header('Location: login.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIJAWA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/login.css?v=<?php echo time(); ?>">
</head>
<body class="auth-page">
    <div class="img-background">
        <img src="assets/aksen_1.png" alt="" srcset="">
    </div>
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

                <form action="" method="POST">
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

                    <button class="google-log"><span class="material-icon-theme--google"></span> Masuk dengan google</button>
                    <button type="submit" class="submit-btn" name="submit">Masuk</button>
                </form>

                <p class="bottom-text">
                    Belum punya akun terdaftar? <a href="register.php">Yuk Daftar</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>