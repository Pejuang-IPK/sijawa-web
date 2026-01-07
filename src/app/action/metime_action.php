<?php

require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['submit-activity'])) {

    $id_mahasiswa = $_SESSION['user_id']; 

    $levelStress  = (int) $_POST['level_stress']; 
    $namaKegiatan = htmlspecialchars($_POST['nama_kegiatan']);
    $durasi       = htmlspecialchars($_POST['durasi']);

    $tanggalLog   = date('Y-m-d H:i:s'); 

    $queryInsert = "INSERT INTO Mood 
                    (id_mahasiswa, tanggalLogMood, waktuMeTime, levelStress, namaKegiatan) 
                    VALUES 
                    ('$id_mahasiswa', '$tanggalLog', '$durasi', '$levelStress', '$namaKegiatan')";

    if (mysqli_query($conn, $queryInsert)) {

        $_SESSION['flash_message'] = 'Aktivitas berhasil disimpan! Stress level Anda tercatat.';
        $_SESSION['flash_type'] = 'success';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {

        $_SESSION['flash_message'] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
