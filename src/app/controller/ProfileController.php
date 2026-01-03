<?php

require_once __DIR__ . '/../../config/database.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Ambil data user yang sedang login
function getUserProfile($userId) {
    global $conn;
    $userId = htmlspecialchars($userId);
    $query = "SELECT id_mahasiswa, nama, email FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Update profil user
function updateProfile($userId, $nama, $email) {
    global $conn;
    
    $userId = htmlspecialchars($userId);
    $nama = htmlspecialchars($nama);
    $email = htmlspecialchars($email);
    
    // Cek apakah email baru sudah terdaftar (jika berubah)
    $query = "SELECT id_mahasiswa FROM Mahasiswa WHERE email = '$email' AND id_mahasiswa != '$userId'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'Email sudah terdaftar'];
    }

    // Update data
    $updateQuery = "UPDATE Mahasiswa SET nama = '$nama', email = '$email' WHERE id_mahasiswa = '$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui profil'];
    }
}

// Update password user
function updatePassword($userId, $oldPassword, $newPassword) {
    global $conn;
    
    $userId = htmlspecialchars($userId);
    
    // Ambil password lama dari database
    $query = "SELECT password FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password lama
    if (!$user || !password_verify($oldPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Password lama tidak sesuai'];
    }

    // Hash password baru
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $hashedPassword = htmlspecialchars($hashedPassword);
    
    // Update password
    $updateQuery = "UPDATE Mahasiswa SET password = '$hashedPassword' WHERE id_mahasiswa = '$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Password berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui password'];
    }
}
?>
