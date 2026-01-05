<?php
session_start();

require_once __DIR__ . '/../app/AuthController.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($error === "Email sudah terdaftar.") {
    $error = "Akun sudah ada";
}

if(isset($_POST['submit'])){
    if(register($_POST)){
        $success = "Registrasi berhasil! Silakan login.";
    } else {
        $_SESSION['error'] = "Email sudah terdaftar.";
        header('Location: register.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIJAWA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/register.css?v=<?php echo time();?>">
</head>
<body class="auth-page register-page">
    <?php if ($success): ?>
    <div id="successModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Selamat!</h3>
                <button class="close-btn" onclick="closeSuccessModal()">&times;</button>
            </div>
            <div class="modal-content">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
            <div class="modal-footer">
                <a href="login.php" class="modal-btn">Lanjut</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div id="errorModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Perhatian</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-content">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn" onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="img-background">
        <img src="assets/ellipse_1.png" alt="" srcset="">
        <img src="assets/ellipse_2.png" alt="" srcset="">
    </div>
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

                <form action="" method="POST">
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

                    <button type="submit" class="submit-btn" name="submit">Daftar</button>
                </form>

                <p class="bottom-text">
                    Sudah punya akun terdaftar? <a href="login.php">Cus Masuk</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        window.addEventListener('load', function() {
            const errorModal = document.getElementById('errorModal');
            const successModal = document.getElementById('successModal');
            
            if (errorModal) {
                errorModal.style.display = 'flex';
            }
            
            if (successModal) {
                successModal.style.display = 'flex';
            }
        });
    </script>
</body>
</html>