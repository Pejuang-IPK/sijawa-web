<?php
// FILE: src/app/action/beranda_action.php

// 1. Mulai Session (Cek apakah session sudah aktif)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load Konfigurasi & Controller Lain
// Gunakan __DIR__ agar path relatif terhadap file ini, bukan file pemanggil
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controller/MeTimeController.php'; 

// 3. Setup Data User
// Ambil ID dari session login, jika tidak ada pakai default (untuk testing)
$id_mahasiswa = $_SESSION['user_id']; 
$nama_user    = $_SESSION['nama'] ?? "Mahasiswa";

// 4. Setup Waktu & Hari
date_default_timezone_set('Asia/Jakarta');
$jam_sekarang = date('H:i');
$hari_inggris = date('l');

// Mapping Hari (Database pakai Bhs Indonesia)
$map_hari = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];
$hari_indo = $map_hari[$hari_inggris];

// ==========================================
// 5. QUERY DATA DARI DATABASE
// ==========================================

// --- A. JADWAL KULIAH HARI INI ---
$queryJadwal = "SELECT * FROM Jadwal 
                WHERE id_mahasiswa = $id_mahasiswa 
                AND hari = '$hari_indo' 
                ORDER BY jam_mulai ASC";

$resultJadwal = mysqli_query($conn, $queryJadwal);

// Gunakan fetch_all agar hasil pasti Array (Mencegah error offset type string)
$jadwal_hari_ini = mysqli_fetch_all($resultJadwal, MYSQLI_ASSOC);
$jumlah_matkul = count($jadwal_hari_ini);

// --- B. CARI MATKUL SELANJUTNYA (NEXT COURSE) ---
$next_matkul = null;
if (!empty($jadwal_hari_ini)) {
    foreach ($jadwal_hari_ini as $matkul) {
        // Ambil matkul pertama yang jam mulainya > jam sekarang
        if ($matkul['jam_mulai'] > $jam_sekarang) {
            $next_matkul = $matkul;
            break; 
        }
    }
}

// --- C. TUGAS DEADLINE HARI INI ---
$queryTugas = "SELECT COUNT(*) as total FROM Tugas 
               WHERE id_mahasiswa = $id_mahasiswa 
               AND DATE(tenggatTugas) = CURDATE()";
$resTugas = mysqli_query($conn, $queryTugas);
$rowTugas = mysqli_fetch_assoc($resTugas);
$tugas_hari_ini = $rowTugas['total'];

// --- D. STRESS LEVEL (Dari Controller) ---
// Pastikan file MeTimeController.php sudah ada dan fungsinya benar
$data_stress = hitungStressLevel($id_mahasiswa);
$stress_score = $data_stress['score'];

// Warna Bar Stress Dinamis
$stress_class = 'progress-fill-green'; // Default (Hijau)
if ($stress_score > 70) $stress_class = 'progress-fill-red';
elseif ($stress_score > 40) $stress_class = 'progress-fill-yellow';

// Logic Rekomendasi Text Singkat
$rek_text = "Nonton Film / Game";
if($stress_score > 70) $rek_text = "Tidur / Meditasi";
if($stress_score < 30) $rek_text = "Olahraga / Hangout";

?>