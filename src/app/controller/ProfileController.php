<?php

require_once __DIR__ . '/../../config/database.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

class ProfileController {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ambil data user yang sedang login
    public function getUserProfile($userId) {
        $query = "SELECT id_mahasiswa, nama, email FROM Mahasiswa WHERE id_mahasiswa = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Update profil user
    public function updateProfile($userId, $nama, $email) {
        // Cek apakah email baru sudah terdaftar (jika berubah)
        $query = "SELECT id_mahasiswa FROM Mahasiswa WHERE email = ? AND id_mahasiswa != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        // Update data
        $updateQuery = "UPDATE Mahasiswa SET nama = ?, email = ? WHERE id_mahasiswa = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("sss", $nama, $email, $userId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
        } else {
            return ['success' => false, 'message' => 'Gagal memperbarui profil'];
        }
    }

    // Update password user
    public function updatePassword($userId, $oldPassword, $newPassword) {
        // Ambil password lama dari database
        $query = "SELECT password FROM Mahasiswa WHERE id_mahasiswa = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verifikasi password lama
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Password lama tidak sesuai'];
        }

        // Hash password baru
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Update password
        $updateQuery = "UPDATE Mahasiswa SET password = ? WHERE id_mahasiswa = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("ss", $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password berhasil diperbarui'];
        } else {
            return ['success' => false, 'message' => 'Gagal memperbarui password'];
        }
    }
}

$profileController = new ProfileController($conn);
?>
