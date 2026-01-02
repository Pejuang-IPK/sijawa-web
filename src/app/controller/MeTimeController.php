<?php

function hitungStressLevel($id_mahasiswa) {
    global $conn;

    // --- 1. HITUNG JUMLAH KELAS MINGGU INI ---
    // Logika: Cari jadwal yang YEARWEEK-nya sama dengan minggu ini
    // Asumsi: id_mahasiswa disesuaikan
    $queryJadwal = "SELECT COUNT(*) as total_kelas 
                    FROM Jadwal 
                    WHERE id_mahasiswa = $id_mahasiswa 
                    AND YEARWEEK(tanggalJadwal, 1) = YEARWEEK(CURDATE(), 1)";
    
    $resultJadwal = mysqli_query($conn, $queryJadwal);
    $rowJadwal = mysqli_fetch_assoc($resultJadwal);
    $totalKelas = $rowJadwal['total_kelas'] ?? 0;

    // --- 2. HITUNG TUGAS (DEADLINE DEKAT & BELUM SELESAI) ---
    // Asumsi id_status: 0 = Belum Selesai, 1 = Selesai
    // Kita cari tugas yang deadline-nya antara HARI INI sampai 7 HARI KEDEPAN
    $queryTugas = "SELECT COUNT(*) as total_tugas 
                   FROM Tugas 
                   WHERE id_mahasiswa = $id_mahasiswa 
                   AND id_status = 0 
                   AND tenggatTugas BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";

    $resultTugas = mysqli_query($conn, $queryTugas);
    $rowTugas = mysqli_fetch_assoc($resultTugas);
    $totalTugas = $rowTugas['total_tugas'] ?? 0;

    // --- 3. RUMUS SKOR STRESS ---
    // Misal: 1 Kelas = 8 poin, 1 Tugas = 15 poin
    $skorJadwal = $totalKelas * 8; 
    $skorTugas  = $totalTugas * 15;

    $totalStress = $skorJadwal + $skorTugas;

    // Batas Maksimal 100
    if ($totalStress > 100) $totalStress = 100;
    // Batas Minimal 0 (biar ga minus kalau logika aneh)
    if ($totalStress < 0) $totalStress = 0;

    return [
        'score' => round($totalStress),
        'total_kelas' => $totalKelas,
        'total_tugas' => $totalTugas
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
?>