<?php
// Pastikan file database sudah di-require
require_once __DIR__ . '/../../config/database.php';

// ... (kode fungsi hitungStressLevel dkk biarkan tetap di atas) ...

// ============================================================
// LOGIKA INSERT / SIMPAN RIWAYAT MOOD
// ============================================================

// Cek apakah tombol 'submit-activity' ditekan
if (isset($_POST['submit-activity'])) {
    
    // 1. Ambil ID Mahasiswa (Sebaiknya dari Session, sementara hardcode dulu)
    $id_mahasiswa = $_SESSION['user_id']; 

    // 2. Tangkap Data dari Form
    // htmlspecialchars() digunakan untuk mencegah input berbahaya
    $levelStress  = (int) $_POST['level_stress']; 
    $namaKegiatan = htmlspecialchars($_POST['nama_kegiatan']);
    $durasi       = htmlspecialchars($_POST['durasi']); // Format 00:30:00

    // 3. Siapkan Data Tambahan
    // Tanggal Log = Waktu sekarang
    $tanggalLog   = date('Y-m-d H:i:s'); 

    // 4. Query Insert ke Tabel Mood
    // Pastikan nama kolom sesuai dengan database Anda:
    // id_mahasiswa, tanggalLogMood, waktuMeTime, levelStress, namaKegiatan
    $queryInsert = "INSERT INTO Mood 
                    (id_mahasiswa, tanggalLogMood, waktuMeTime, levelStress, namaKegiatan) 
                    VALUES 
                    ('$id_mahasiswa', '$tanggalLog', '$durasi', '$levelStress', '$namaKegiatan')";

    // 5. Eksekusi Query
    if (mysqli_query($conn, $queryInsert)) {
        // Jika Berhasil
        echo "<script>
                alert('Aktivitas berhasil disimpan! Stress level tercatat.');
                // Redirect kembali ke halaman me-time (sesuaikan path project Anda)
                document.location.href = '" . 'http://localhost/' . "me-time.php';
              </script>";
        exit;
    } else {
        // Jika Gagal
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($conn) . "');
                document.location.href = '" . 'http://localhost/' . "me-time.php';
              </script>";
        exit;
    }
}