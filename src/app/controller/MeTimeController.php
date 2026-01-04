<?php
require_once __DIR__ . '/../../config/database.php';

function hitungStressLevel($id_mahasiswa) {
    global $conn;

    // ============================================================
    // 1. AMBIL DATA JADWAL (SKS & DURASI) - [TIDAK DIUBAH]
    // ============================================================
    $queryJadwal = "SELECT sks, jam_mulai, jam_selesai FROM Jadwal WHERE id_mahasiswa = $id_mahasiswa";
    $resultJadwal = mysqli_query($conn, $queryJadwal);
    
    $total_sks = 0;
    $total_jam_kuliah = 0;

    if ($resultJadwal) {
        while ($row = mysqli_fetch_assoc($resultJadwal)) {
            $total_sks += (int)$row['sks'];
            $start = strtotime($row['jam_mulai']);
            $end   = strtotime($row['jam_selesai']);
            $durasi = ($end - $start) / 3600; 
            $total_jam_kuliah += abs($durasi);
        }
    }

    // ============================================================
    // 2. AMBIL DATA TUGAS (URGENSI) - HANYA URGENT
    // ============================================================
    // Query hanya untuk tugas yang deadline-nya hari ini atau besok (truly urgent)
    $queryTugas = "SELECT tenggatTugas FROM Tugas 
                   WHERE id_mahasiswa = $id_mahasiswa 
                   AND tenggatTugas >= CURDATE() 
                   AND tenggatTugas <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    
    $resultTugas = mysqli_query($conn, $queryTugas);
    $tugas_mendesak = 0;
    
    if ($resultTugas) {
        $tugas_mendesak = mysqli_num_rows($resultTugas);
    }
    
    $total_tugas = $tugas_mendesak;

    // ============================================================
    // 3. HITUNG SKOR STRESS KOTOR (GROSS STRESS)
    // ============================================================

    // A. Beban Rutin (Max 60%)
    $skor_beban_rutin = ($total_sks * 1.2) + ($total_jam_kuliah * 0.8);
    if ($skor_beban_rutin > 60) $skor_beban_rutin = 60; 

    // B. Tekanan Waktu - HANYA TUGAS URGENT
    // Setiap tugas urgent (deadline hari ini/besok) = 25 poin
    $skor_tekanan_tugas = $tugas_mendesak * 25;

    // Total Stress Awal (Sebelum dikurangi Me-Time)
    $stress_kotor = $skor_beban_rutin + $skor_tekanan_tugas;

    // ============================================================
    // 4. [BARU] HITUNG PENGURANGAN DARI ME-TIME (RECOVERY)
    // ============================================================
    // Kita cari aktivitas Mood dalam 3 hari terakhir
    
    $queryRecovery = "SELECT waktuMeTime FROM Mood 
                      WHERE id_mahasiswa = $id_mahasiswa 
                      AND tanggalLogMood >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
    
    $resultRecovery = mysqli_query($conn, $queryRecovery);
    $total_jam_metime = 0;

    if ($resultRecovery) {
        while ($row = mysqli_fetch_assoc($resultRecovery)) {
            // Konversi "01:30:00" menjadi angka desimal (1.5 jam)
            $timeParts = explode(':', $row['waktuMeTime']);
            $jam   = isset($timeParts[0]) ? (int)$timeParts[0] : 0;
            $menit = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
            
            $total_jam_metime += $jam + ($menit / 60);
        }
    }

    // RUMUS PENGURANGAN:
    // Setiap 1 jam Me-Time mengurangi 5 poin stress
    $skor_recovery = $total_jam_metime * 5;

    // Batasi pengurangan maksimal 30 poin (supaya stress tidak jadi minus berlebihan)
    if ($skor_recovery > 30) {
        $skor_recovery = 30;
    }

    // ============================================================
    // 5. HITUNGAN FINAL
    // ============================================================
    
    // Kurangi Stress Awal dengan Skor Recovery
    $totalStress = $stress_kotor - $skor_recovery;

    // Normalisasi (Pastikan range 0 - 100)
    if ($totalStress > 100) $totalStress = 100;
    if ($totalStress < 0) $totalStress = 0;

    return [
        'score'          => round($totalStress),
        'total_sks'      => $total_sks,
        'total_jam'      => round($total_jam_kuliah, 1),
        'total_tugas'    => $total_tugas,
        'tugas_mendesak' => $tugas_mendesak,
        // (Opsional) Kirim data recovery buat debugging kalau perlu
        'recovery_poin'  => round($skor_recovery) 
    ];
}

// ... (SISA KODE KE BAWAH: getRekomendasi & Handle POST TETAP SAMA) ...
function getRekomendasi($stressLevel) {
    $rekomendasi = [];

    if ($stressLevel >= 70) {
        $rekomendasi = [
            ['title' => 'Tidur Berkualitas', 'desc' => 'Otakmu butuh istirahat total.', 'icon' => 'fa-bed', 'color' => 'bg-blue'],
            ['title' => 'Meditasi', 'desc' => 'Tenangkan pikiran sejenak.', 'icon' => 'fa-spa', 'color' => 'bg-green'],
            ['title' => 'Dengar Musik Santai', 'desc' => 'Kurangi tensi dengan musik.', 'icon' => 'fa-music', 'color' => 'bg-pink']
        ];
    } elseif ($stressLevel >= 30) {
        $rekomendasi = [
            ['title' => 'Nonton Film', 'desc' => 'Movie marathon sejenak.', 'icon' => 'fa-film', 'color' => 'bg-blue'],
            ['title' => 'Main Game', 'desc' => 'Push rank santai.', 'icon' => 'fa-gamepad', 'color' => 'bg-pink'],
            ['title' => 'Kulineran', 'desc' => 'Makan enak boost mood.', 'icon' => 'fa-utensils', 'color' => 'bg-green']
        ];
    } else {
        $rekomendasi = [
            ['title' => 'Olahraga', 'desc' => 'Gerakkan badanmu.', 'icon' => 'fa-person-running', 'color' => 'bg-green'],
            ['title' => 'Hangout', 'desc' => 'Ajak teman ngopi.', 'icon' => 'fa-users', 'color' => 'bg-blue'],
            ['title' => 'Baca Buku', 'desc' => 'Cari inspirasi baru.', 'icon' => 'fa-book-open', 'color' => 'bg-pink']
        ];
    }
    return $rekomendasi;
}

if (isset($_GET['action']) && $_GET['action'] == 'log_mood') {
    if (session_status() == PHP_SESSION_NONE) session_start();
    
    $id_mahasiswa = 386937; 
    
    $levelStress  = (int)$_POST['level_stress'];
    $namaKegiatan = htmlspecialchars($_POST['nama_kegiatan']);
    $waktuMeTime  = $_POST['durasi']; 
    
    $tanggalLog   = date('Y-m-d H:i:s'); 

    $queryInsert = "INSERT INTO Mood 
                    (id_mahasiswa, tanggalLogMood, waktuMeTime, levelStress, namaKegiatan) 
                    VALUES 
                    ('$id_mahasiswa', '$tanggalLog', '$waktuMeTime', '$levelStress', '$namaKegiatan')";
    
    if (mysqli_query($conn, $queryInsert)) {
        echo "<script>
                alert('Aktivitas berhasil dicatat! Stress level Anda akan disesuaikan.');
                window.location.href = '../../public/me-time.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mencatat: " . mysqli_error($conn) . "');
                window.location.href = '../../public/me-time.php';
              </script>";
    }
}
?>