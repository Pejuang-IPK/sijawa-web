<?php

require_once __DIR__ . '/../config/database.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function login($loginData)
{
    global $conn;

    $email = htmlspecialchars($loginData['email']);
    $password = $loginData['password'];

    $user = query("SELECT * FROM Mahasiswa WHERE email = '$email'")[0];

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return false;
    }
}

function register($registerData)
{
    global $conn;

    $nama = htmlspecialchars($registerData['nama']);
    $email = htmlspecialchars($registerData['email']);
    $password = password_hash($registerData['password'], PASSWORD_BCRYPT);
    $id_mahasiswa = random_int(100000, 999999);

    // Cek apakah email sudah terdaftar
    $existingUser = query("SELECT * FROM Mahasiswa WHERE email = '$email'");
    if (count($existingUser) > 0) {
        return false;
    }

    // Masukkan user baru ke database
    $query = "INSERT INTO Mahasiswa (id_mahasiswa, nama, email, password) VALUES ('$id_mahasiswa', '$nama', '$email', '$password')";
    mysqli_query($conn, $query);

    return true;
}
?>