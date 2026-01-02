<?php
require_once __DIR__ . '/../../config/database.php';

function hitungStressLevel($id_mahasiswa) {
    global $conn;

    // 1. AMBIL DATA JADWAL (SKS & DURASI)
    $queryJadwal = "SELECT sks, jam_mulai, jam_selesai FROM Jadwal WHERE id_mahasiswa = $id_mahasiswa";
    $resultJadwal = mysqli_query($conn, $queryJadwal);
    
    $total_sks = 0;
    $total_jam_kuliah = 0;

    if ($resultJadwal) {
        while ($row = mysqli_fetch_assoc($resultJadwal)) {
            $total_sks += (int)$row['sks'];
            
            // Hitung Durasi
            $start = strtotime($row['jam_mulai']);
            $end   = strtotime($row['jam_selesai']);
            $durasi = ($end - $start) / 3600; 
            $total_jam_kuliah += abs($durasi);
        }
    }

    // 2. AMBIL DATA TUGAS (URGENSI)
    // Filter tugas yang deadline-nya 7 hari kedepan
    $queryTugas = "SELECT tenggatTugas FROM Tugas 
                   WHERE id_mahasiswa = $id_mahasiswa 
                   AND id_status = 0 
                   AND tenggatTugas BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    
    $resultTugas = mysqli_query($conn, $queryTugas);
    $tugas_mendesak = 0; 
    $tugas_santai   = 0;
    
    $today = new DateTime();

    if ($resultTugas) {
        while ($row = mysqli_fetch_assoc($resultTugas)) {
            $deadline = new DateTime($row['tenggatTugas']);
            $interval = $today->diff($deadline);
            $hari_sisa = (int)$interval->format('%r%a');

            // H-0 sampai H-3 dianggap Mendesak (Pemicu Stress Akut) [cite: 8]
            if ($hari_sisa <= 3) {
                $tugas_mendesak++;
            } else {
                $tugas_santai++;
            }
        }
    }
    $total_tugas = $tugas_mendesak + $tugas_santai;

    // ============================================================
    // 3. RUMUS BARU (DENGAN LIMIT BASELINE)
    // ============================================================

    // A. Hitung Skor Beban Rutin (Chronic Stress)
    // SKS dikali 1.2, Jam dikali 0.8
    // Contoh kasus Anda: (46 SKS * 1.2) + (20 Jam * 0.8) = 55.2 + 16 = 71.2 Poin
    $skor_beban_rutin = ($total_sks * 1.2) + ($total_jam_kuliah * 0.8);

    // [PENTING] CAPPING BEBAN RUTIN
    // Maksimal beban rutin hanya boleh menyumbang 60% stress.
    // Artinya: Tanpa tugas, mahasiswa cuma bisa "Sangat Lelah" (60%), tidak "Panik" (100%).
    if ($skor_beban_rutin > 60) {
        $skor_beban_rutin = 60; 
    }

    // B. Hitung Skor Tekanan Waktu (Acute Stress)
    // Tugas Mendesak bobotnya BESAR (15 poin) karena ini pemicu utama [cite: 8]
    // Tugas Santai bobotnya KECIL (5 poin)
    $skor_tekanan_tugas = ($tugas_mendesak * 15) + ($tugas_santai * 5);

    // C. Total Stress
    $totalStress = $skor_beban_rutin + $skor_tekanan_tugas;

    // Normalisasi (0-100)
    if ($totalStress > 100) $totalStress = 100;
    if ($totalStress < 0) $totalStress = 0;

    return [
        'score'          => round($totalStress),
        'total_sks'      => $total_sks,
        'total_jam'      => round($total_jam_kuliah, 1),
        'total_tugas'    => $total_tugas,
        'tugas_mendesak' => $tugas_mendesak
    ];
}

function getRekomendasi($stressLevel) {
    // ... (Fungsi rekomendasi SAMA PERSIS seperti sebelumnya) ...
    // Copy paste dari jawaban sebelumnya
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

// LOGIKA INSERT KE DATABASE
// Cek apakah ada request Log Mood
if (isset($_GET['action']) && $_GET['action'] == 'log_mood') {
    
    // Pastikan session start jika belum
    if (session_status() == PHP_SESSION_NONE) session_start();
    
    $id_mahasiswa = 386937; // Sesuaikan session
    
    $levelStress  = (int)$_POST['level_stress'];
    $namaKegiatan = htmlspecialchars($_POST['nama_kegiatan']);
    $waktuMeTime  = $_POST['durasi']; // Format 01:00:00 (Time)
    
    // Tanggal otomatis NOW()
    $tanggalLog   = date('Y-m-d H:i:s'); 

    $queryInsert = "INSERT INTO Mood 
                    (id_mahasiswa, tanggalLogMood, waktuMeTime, levelStress, namaKegiatan) 
                    VALUES 
                    ('$id_mahasiswa', '$tanggalLog', '$waktuMeTime', '$levelStress', '$namaKegiatan')";
    
    if (mysqli_query($conn, $queryInsert)) {
        echo "<script>
                alert('Aktivitas berhasil dicatat!');
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