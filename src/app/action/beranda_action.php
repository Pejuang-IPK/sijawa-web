<?php
// --- 1. PERSIAPAN DATA (PHP LOGIC) ---
// Pastikan koneksi database ($conn) sudah tersedia dari file induk (dashboard/index)

$id_mahasiswa = $_SESSION['user']['id'] ?? 386937; // Sesuaikan dengan session ID login Anda
require_once __DIR__ . '/../../config/database.php';

// A. QUERY TUGAS TERDEKAT (Prioritas Tertinggi)
// Cari tugas yang belum selesai (status=0) dan deadline >= hari ini
// Urutkan berdasarkan deadline paling cepat (ASC), ambil 1 saja
$queryTask = "SELECT * FROM Tugas 
              WHERE id_mahasiswa = $id_mahasiswa 
              AND id_status = 0 
              AND tenggatTugas >= CURDATE() 
              ORDER BY tenggatTugas ASC 
              LIMIT 1";

$resultTask = mysqli_query($conn, $queryTask);
$nearestTask = mysqli_fetch_assoc($resultTask);

// B. LOGIC KALENDER MINI (5 Hari Kedepan)
$hari_indo = ['Sunday' => 'Min', 'Monday' => 'Sen', 'Tuesday' => 'Sel', 'Wednesday' => 'Rab', 'Thursday' => 'Kam', 'Friday' => 'Jum', 'Saturday' => 'Sab'];
$tgl_sekarang = date('Y-m-d');

// C. LOGIC QUOTES RANDOM
$quotes = [
    ['text' => 'Kerja keras tidak boleh berhenti', 'author' => 'Joko Widodo'],
    ['text' => 'Pendidikan adalah senjata paling mematikan', 'author' => 'Nelson Mandela'],
    ['text' => 'Mulai aja dulu, sempurnakan nanti', 'author' => 'Unknown'],
    ['text' => 'Tugas selesai = Tidur nyenyak', 'author' => 'Mahasiswa Semester Akhir']
];
$random_quote = $quotes[array_rand($quotes)];