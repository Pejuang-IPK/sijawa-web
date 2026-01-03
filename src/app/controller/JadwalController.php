<?php

use Shuchkin\SimpleXLSX;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../vendor/SimpleXLSX.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while( $row = mysqli_fetch_assoc($result) ) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data) {
    global $conn;
    
    // 1. Ambil ID Mahasiswa (Hardcode 1 atau dari Session)
    $id_mahasiswa = $_SESSION['user_id']; 

    // 2. Ambil data dari Form HTML
    $hari        = htmlspecialchars($data["hari"]);
    $matkul      = htmlspecialchars($data["matkul"]);
    $ruangan     = htmlspecialchars($data["ruangan"]);
    $sks         = htmlspecialchars($data["sks"]);
    $jam_mulai   = htmlspecialchars($data["jam_mulai"]);
    $jam_selesai = htmlspecialchars($data["jam_selesai"]);
    $dosen       = htmlspecialchars($data["dosen"]);

    $id_jadwal = random_int(100000, 999999);

    // 3. Masukkan ke Database (Perhatikan urutan kolomnya!)
    // Format Insert: (id_jadwal, id_mahasiswa, hari, namaMatkul, jam_mulai, jam_selesai, kelasMatkul, sks, dosenMatkul)
    // Sesuaikan nama kolom dengan database Anda
    $query = "INSERT INTO Jadwal 
              (id_jadwal, id_mahasiswa, hari, namaMatkul, jam_mulai, jam_selesai, kelasMatkul, sks, dosenMatkul)
              VALUES 
              ('$id_jadwal', '$id_mahasiswa', '$hari', '$matkul', '$jam_mulai', '$jam_selesai', '$ruangan', '$sks', '$dosen')";
    
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function hapus($id) {
    global $conn;
    // Hapus berdasarkan id_jadwal
    mysqli_query($conn, "DELETE FROM Jadwal WHERE id_jadwal = $id");
    return mysqli_affected_rows($conn);
}

function ubah($data) {
    global $conn;
    
    // Ambil data dari $_POST
    $id          = $data["id_jadwal"]; // ID yang di-hidden tadi
    $hari        = htmlspecialchars($data["hari"]);
    $matkul      = htmlspecialchars($data["matkul"]);
    $ruangan     = htmlspecialchars($data["ruangan"]);
    $sks         = htmlspecialchars($data["sks"]);
    $jam_mulai   = htmlspecialchars($data["jam_mulai"]);
    $jam_selesai = htmlspecialchars($data["jam_selesai"]);
    $dosen       = htmlspecialchars($data["dosen"]);

    // Query Update
    $query = "UPDATE Jadwal SET 
                hari = '$hari',
                namaMatkul = '$matkul',
                kelasMatkul = '$ruangan',
                sks = '$sks',
                jam_mulai = '$jam_mulai',
                jam_selesai = '$jam_selesai',
                dosenMatkul = '$dosen'
              WHERE id_jadwal = $id";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function importExcel($files) {
    global $conn;

    if (!isset($files['file_excel']) || $files['file_excel']['error'] !== 0) {
        return false;
    }

    $tmpName = $files['file_excel']['tmp_name'];
    $id_mahasiswa = 386937;

    if (!$xlsx = SimpleXLSX::parse($tmpName)) {
        die(SimpleXLSX::parseError());
    }

    $rows = array_values($xlsx->rows());

    // skip header (baris 0)
    foreach ($rows as $i => $row) {

        if ($i === 0) continue;
        if (is_array($row) && count($row) < 8) continue;

        $hari        = trim($row[0]);
        $raw_waktu   = trim($row[1]);
        $namaMatkul  = trim($row[2]);
        $sks         = (int)$row[5];
        $kelasMatkul = trim($row[4]);
        $dosenMatkul = trim($row[7]);

        if (!$hari || !$raw_waktu || !$namaMatkul) continue;

        if (strpos($raw_waktu, '-') === false) continue;

        [$jam_mulai, $jam_selesai] = array_map('trim', explode('-', $raw_waktu));

        $stmt = $conn->prepare("
            INSERT INTO Jadwal 
            (id_mahasiswa, hari, namaMatkul, kelasMatkul, sks, jam_mulai, jam_selesai, dosenMatkul)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssisss",
            $id_mahasiswa,
            $hari,
            $namaMatkul,
            $kelasMatkul,
            $sks,
            $jam_mulai,
            $jam_selesai,
            $dosenMatkul
        );

        $stmt->execute();
        $stmt->close();
    }

    return true;
}

?>