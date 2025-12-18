<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email dan password harus diisi!';
    header('Location: login.php');
    exit();
}

// Cek email di database
$stmt = $conn->prepare("SELECT id_mahasiswa, nama, email, password FROM Mahasiswa WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Email atau password salah!';
    header('Location: login.php');
    exit();
}

$user = $result->fetch_assoc();

// Verifikasi password
if (password_verify($password, $user['password'])) {
    // Login berhasil
    $_SESSION['user_id'] = $user['id_mahasiswa'];
    $_SESSION['user_name'] = $user['nama'];
    $_SESSION['user_email'] = $user['email'];
    
    header('Location: dashboard.php');
    exit();
} else {
    $_SESSION['error'] = 'Email atau password salah!';
    header('Location: login.php');
    exit();
}

$stmt->close();
$conn->close();
?>
