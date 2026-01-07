<?php
session_start();
require_once __DIR__ . '/../controller/JadwalController.php'; // Sesuaikan path

$action = $_GET['action'] ?? '';

// edit
if (isset($_POST['submit_edit'])) {
    if (ubah($_POST) > 0) {
        echo "<script>alert('Data Berhasil Diubah!'); document.location.href = '';</script>";
    } else {
        echo "<script>alert('Data Berhasil Disimpan (Tidak ada perubahan).'); document.location.href = '';</script>";
    }
}

// delete
if(isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    if(hapus($id) > 0) {
        echo "<script>alert('Terhapus!'); document.location.href = '';</script>";
    }
}

if ($action === 'import') {
    if (isset($_POST['submit_import'])) {
        
        if (importExcel($_FILES)) {
            echo "<script>
                    alert('Import Berhasil!');
                    document.location.href = '" . 'http://localhost/' . "public/kalender.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal Import! Pastikan file .csv dan format kolom benar.');
                    document.location.href = '" . 'http://localhost/' . "public/kalender.php';
                  </script>";
        }
    }
}
?>