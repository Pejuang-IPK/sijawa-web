<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($nama) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'Semua field harus diisi!';
    header('Location: register.php');
    exit();
}

// Validasi email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid!';
    header('Location: register.php');
    exit();
}

// Validasi panjang password
if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter!';
    header('Location: register.php');
    exit();
}

// Cek apakah email sudah terdaftar
$stmt = $conn->prepare("SELECT id_mahasiswa FROM Mahasiswa WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar! Silakan login.';
    header('Location: register.php');
    exit();
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Dapatkan ID terakhir untuk increment manual
$result = $conn->query("SELECT MAX(id_mahasiswa) as max_id FROM Mahasiswa");
$row = $result->fetch_assoc();
$newId = ($row['max_id'] ?? 0) + 1;

// Insert data ke database
$stmt = $conn->prepare("INSERT INTO Mahasiswa (id_mahasiswa, nama, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $newId, $nama, $email, $hashedPassword);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
    header('Location: login.php');
    exit();
} else {
    $_SESSION['error'] = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
    header('Location: register.php');
    exit();
}

$stmt->close();
$conn->close();
?>
