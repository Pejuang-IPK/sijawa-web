<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helper/Minio.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

function getUserProfile($userId) {
    global $conn;
    $userId = htmlspecialchars($userId);
    $query = "SELECT id_mahasiswa, nama, email, foto FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function updateProfile($userId, $nama, $email) {
    global $conn;
    
    $userId = htmlspecialchars($userId);
    $nama = htmlspecialchars($nama);
    $email = htmlspecialchars($email);

    $query = "SELECT id_mahasiswa FROM Mahasiswa WHERE email = '$email' AND id_mahasiswa != '$userId'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'Email sudah terdaftar'];
    }

    $updateQuery = "UPDATE Mahasiswa SET nama = '$nama', email = '$email' WHERE id_mahasiswa = '$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui profil'];
    }
}

function updatePassword($userId, $oldPassword, $newPassword) {
    global $conn;
    
    $userId = htmlspecialchars($userId);

    $query = "SELECT password FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user || !password_verify($oldPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Password lama tidak sesuai'];
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $hashedPassword = htmlspecialchars($hashedPassword);

    $updateQuery = "UPDATE Mahasiswa SET password = '$hashedPassword' WHERE id_mahasiswa = '$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Password berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui password'];
    }
}

function uploadProfilePhoto($userId, $file) {
    global $conn;

    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'File tidak ditemukan'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error saat upload file'];
    }

    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 2MB)'];
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileMimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP'];
    }

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = $userId . '_' . time() . '.' . $fileExtension;
    $bucketName = 'profile-photos';

    $uploadResult = uploadToMinioNative(
        $bucketName, 
        $newFileName, 
        $file['tmp_name'], 
        $fileMimeType
    );

    if (!$uploadResult['success']) {
        return ['success' => false, 'message' => 'Gagal upload ke Storage: ' . $uploadResult['message']];
    }

    $userId = htmlspecialchars($userId);
    $query = "SELECT foto FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);

    $userIdClean = htmlspecialchars($userId);
    $updateQuery = "UPDATE Mahasiswa SET foto = '$newFileName' WHERE id_mahasiswa = '$userIdClean'";

    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Foto profil berhasil diupload'];
    } else {
        return ['success' => false, 'message' => 'Gagal update database'];
    }
}
?>
