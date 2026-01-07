<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controller/MeTimeController.php'; 

$id_mahasiswa = $_SESSION['user_id']; 
$nama_user    = $_SESSION['nama'] ?? "Mahasiswa";

date_default_timezone_set('Asia/Jakarta');
$jam_sekarang = date('H:i');
$hari_inggris = date('l');

$map_hari = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];
$hari_indo = $map_hari[$hari_inggris];

$queryJadwal = "SELECT * FROM Jadwal 
                WHERE id_mahasiswa = $id_mahasiswa 
                AND hari = '$hari_indo' 
                ORDER BY jam_mulai ASC";

$resultJadwal = mysqli_query($conn, $queryJadwal);

$jadwal_hari_ini = mysqli_fetch_all($resultJadwal, MYSQLI_ASSOC);
$jumlah_matkul = count($jadwal_hari_ini);

$next_matkul = null;
if (!empty($jadwal_hari_ini)) {
    foreach ($jadwal_hari_ini as $matkul) {

        if ($matkul['jam_mulai'] > $jam_sekarang) {
            $next_matkul = $matkul;
            break; 
        }
    }
}

$queryTugas = "SELECT COUNT(*) as total FROM Tugas 
               WHERE id_mahasiswa = $id_mahasiswa 
               AND DATE(tenggatTugas) = CURDATE()";
$resTugas = mysqli_query($conn, $queryTugas);
$rowTugas = mysqli_fetch_assoc($resTugas);
$tugas_hari_ini = $rowTugas['total'];

$data_stress = hitungStressLevel($id_mahasiswa);
$stress_score = $data_stress['score'];

$stress_class = 'progress-fill-green';
if ($stress_score > 70) $stress_class = 'progress-fill-red';
elseif ($stress_score > 40) $stress_class = 'progress-fill-yellow';

$rek_text = "Nonton Film / Game";
if($stress_score > 70) $rek_text = "Tidur / Meditasi";
if($stress_score < 30) $rek_text = "Olahraga / Hangout";

?>