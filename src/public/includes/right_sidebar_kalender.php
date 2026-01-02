<?php
// =======================================================
// KONFIGURASI AWAL
// =======================================================
date_default_timezone_set('Asia/Jakarta');

// Pastikan koneksi & functions.php sudah di-include
// require 'koneksi.php';
// require 'functions.php';

// =======================================================
// 1. DETEKSI HARI INI (INDONESIA)
// =======================================================
$map_hari = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
];

$hari_inggris = date('l');
$hari_ini     = trim($map_hari[$hari_inggris] ?? '');

// =======================================================
// 2. QUERY JADWAL HARI INI
// =======================================================
$query_today = "
    SELECT * 
    FROM Jadwal
    WHERE id_mahasiswa = 386937
      AND TRIM(hari) = '$hari_ini'
    ORDER BY jam_mulai ASC
";

// =======================================================
// 3. EKSEKUSI QUERY → ARRAY
// =======================================================
$data_hari_ini = query($query_today); // WAJIB ADA

// =======================================================
// 4. DEBUG (matikan kalau sudah yakin)
// =======================================================
// echo '<pre>'; print_r($data_hari_ini); echo '</pre>'; die;
?>

<!-- ===================================================== -->
<!-- UI SIDEBAR -->
<!-- ===================================================== -->
<aside class="right-sidebar">
    <div class="right-schedule">

        <div class="section-header">
            <h4>Kuliahmu</h4>
            <span class="day-label"><?= htmlspecialchars($hari_ini) ?></span>
        </div>

        <div class="mini-course-list">

            <?php if (is_array($data_hari_ini) && count($data_hari_ini) > 0) : ?>

                <?php foreach ($data_hari_ini as $row) : ?>
                    <div class="mini-card">

                        <div class="card-left">
                            <span class="mini-subject">
                                <?= htmlspecialchars($row['namaMatkul']) ?>
                            </span>
                            <span class="mini-badge blue">
                                <?= (int)$row['sks'] ?> SKS
                            </span>
                        </div>

                        <div class="card-right">
                            <div class="info-row">
                                <i class="fa-solid fa-user-tie"></i>
                                <span><?= htmlspecialchars($row['dosenMatkul']) ?></span>
                            </div>

                            <div class="info-row">
                                <i class="fa-regular fa-clock"></i>
                                <span>
                                    <?= substr($row['jam_mulai'], 0, 5) ?> WIB
                                </span>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>

            <?php else : ?>

                <div style="text-align: center; padding: 20px; color: #888;">
                    <i class="fa-solid fa-mug-hot"
                       style="font-size: 24px; margin-bottom: 10px;"></i>
                    <p style="font-size: 14px; margin: 0;">
                        Tidak ada kuliah hari ini.<br>
                        Selamat istirahat!
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </div>
</aside>
