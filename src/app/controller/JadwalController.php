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

    $id_mahasiswa = $_SESSION['user_id']; 

    $hari        = htmlspecialchars($data["hari"]);
    $matkul      = htmlspecialchars($data["matkul"]);
    $ruangan     = htmlspecialchars($data["ruangan"]);
    $sks         = htmlspecialchars($data["sks"]);
    $jam_mulai   = htmlspecialchars($data["jam_mulai"]);
    $jam_selesai = htmlspecialchars($data["jam_selesai"]);
    $dosen       = htmlspecialchars($data["dosen"]);

    $id_jadwal = random_int(100000, 999999);

    $query = "INSERT INTO Jadwal 
              (id_jadwal, id_mahasiswa, hari, namaMatkul, jam_mulai, jam_selesai, kelasMatkul, sks, dosenMatkul)
              VALUES 
              ('$id_jadwal', '$id_mahasiswa', '$hari', '$matkul', '$jam_mulai', '$jam_selesai', '$ruangan', '$sks', '$dosen')";
    
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function hapus($id) {
    global $conn;

    mysqli_query($conn, "DELETE FROM Jadwal WHERE id_jadwal = $id");
    return mysqli_affected_rows($conn);
}

function ubah($data) {
    global $conn;

    $id          = $data["id_jadwal"];
    $hari        = htmlspecialchars($data["hari"]);
    $matkul      = htmlspecialchars($data["matkul"]);
    $ruangan     = htmlspecialchars($data["ruangan"]);
    $sks         = htmlspecialchars($data["sks"]);
    $jam_mulai   = htmlspecialchars($data["jam_mulai"]);
    $jam_selesai = htmlspecialchars($data["jam_selesai"]);
    $dosen       = htmlspecialchars($data["dosen"]);

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
        return 'error';
    }

    $tmpName = $files['file_excel']['tmp_name'];
    $id_mahasiswa = $_SESSION["user_id"];

    if (!$xlsx = SimpleXLSX::parse($tmpName)) {
        return 'error';
    }

    $rows = array_values($xlsx->rows());
    $duplicateCount = 0;
    $successCount = 0;

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

        $checkQuery = "SELECT COUNT(*) as count FROM Jadwal 
                       WHERE id_mahasiswa = ? 
                       AND hari = ? 
                       AND namaMatkul = ? 
                       AND jam_mulai = ? 
                       AND jam_selesai = ?";
        
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("issss", $id_mahasiswa, $hari, $namaMatkul, $jam_mulai, $jam_selesai);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $data = $result->fetch_assoc();
        $checkStmt->close();

        if ($data['count'] > 0) {
            $duplicateCount++;
            continue;
        }

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

        if ($stmt->execute()) {
            $successCount++;
        }
        $stmt->close();
    }

    if ($successCount > 0 && $duplicateCount > 0) {
        return 'partial';
    } elseif ($duplicateCount > 0 && $successCount == 0) {
        return 'duplicate';
    } elseif ($successCount > 0) {
        return 'success';
    } else {
        return 'error';
    }
}

?>