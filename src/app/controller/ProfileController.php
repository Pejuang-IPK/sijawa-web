<?php

require_once __DIR__ . '/../../config/database.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Ambil data user yang sedang login
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

// Upload foto profil user
function uploadProfilePhoto($userId, $file) {
    global $conn;
    
    // Validasi file ada
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'File tidak ditemukan'];
    }
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error saat upload file'];
    }
    
    // Validasi ukuran file (maksimal 2MB)
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 2MB)'];
    }
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileMimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP'];
    }
    
    // Buat nama file unik
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = $userId . '_' . time() . '.' . $fileExtension;
    $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
    $uploadPath = $uploadDir . $newFileName;
    
    // Pastikan direktori ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'message' => 'Gagal menyimpan file'];
    }
    
    // Hapus foto lama jika ada
    $userId = htmlspecialchars($userId);
    $query = "SELECT foto FROM Mahasiswa WHERE id_mahasiswa = '$userId'";
    $result = mysqli_query($conn, $query);
    $userData = mysqli_fetch_assoc($result);
    
    if ($userData && !empty($userData['foto'])) {
        $oldFilePath = $uploadDir . $userData['foto'];
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
    }
    
    // Update database dengan nama file foto
    $updateQuery = "UPDATE Mahasiswa SET foto = '$newFileName' WHERE id_mahasiswa = '$userId'";
    
    if (mysqli_query($conn, $updateQuery)) {
        return ['success' => true, 'message' => 'Foto profil berhasil diupload'];
    } else {
        return ['success' => false, 'message' => 'Gagal memperbarui database'];
    }
}
?>
