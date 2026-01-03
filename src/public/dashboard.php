<?php 
session_start();

require_once __DIR__ . '../../app/action/beranda_action.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Beranda - SIJAWA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="page">
    <?php 
        $page = 'dashboard'; // Untuk active state sidebar
        include 'includes/sidebar.php'; 
    ?>
    
    <main class="content">
        <header class="content-header">
            <div>
                <h1>Dashboard Beranda</h1>
                <p>Selamat datang kembali, <b><?= htmlspecialchars($nama_user) ?></b>! Semangat kuliahnya 🚀</p>
            </div>
        </header>
        
        <section class="card">
            <div class="left">
                <div class="matkul-card">
                    <div class="count"><?= $jumlah_matkul ?></div>
                    <div class="label">Mata Kuliah Hari ini</div>
                </div>
                
                <div class="deadline-card">
                    <div class="dashed-bg"></div>
                    <div class="main-card">
                        <div class="count"><?= $tugas_hari_ini ?></div>
                        <div class="label">Tugas Deadline Hari ini</div>
                    </div>
                </div>
                
                <div class="progressbar-card">
                    <div class="title">Stress Level</div>
                    <div class="label-top-right"><?= $stress_score ?>%</div>
                    <div class="progress-container">
                        <div class="<?= $stress_class ?>" style="width: <?= $stress_score ?>%;"></div>
                    </div>
                    <div class="label-bottom-left">
                        <?= ($stress_score > 70) ? 'Butuh Istirahat' : 'Aman Terkendali' ?>
                    </div>
                </div>
            </div>
            
            <div class="mid">
                <div class="progressbar-card">
                    <div class="title">Money Management</div>
                    <div class="label-top-right">Safe</div>
                    <div class="progress-container">
                        <div class="progress-fill-blue" style="width: 65%;"></div>
                    </div>
                    <div class="label-bottom-left">Sisa Budget Aman</div>
                </div>
                
                <div class="next-card">
                    <div class="title">Mata Kuliah selanjutnya</div>
                    <?php if ($next_matkul): ?>
                        <div class="label" style="font-size: 14px; margin-top: 5px; line-height: 1.5;">
                            <b><?= htmlspecialchars($next_matkul['namaMatkul']) ?></b><br>
                            Ruang <?= htmlspecialchars($next_matkul['kelasMatkul']) ?> | 
                            <span style="color: #4f46e5; font-weight:bold;">
                                <?= substr($next_matkul['jam_mulai'], 0, 5) ?> WIB
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="label" style="color: #10b981; margin-top: 10px; font-weight: 500;">
                            <i class="fa-solid fa-check-circle"></i> Kelas hari ini selesai!
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="metime-card">
                    <div class="dashed-bg"></div>
                    <div class="main-card">
                        <div class="title">Rekomendasi Me-Time</div>
                        <div class="label" style="font-size: 13px;">
                            Saran: <b><?= $rek_text ?></b><br>
                            <a href="me-time.php" style="color: #4f46e5; text-decoration: none; font-size: 11px; font-weight:600;">Lihat Detail &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="timer-card">
                <div class="clock-illustration">
                    <i class="fa-solid fa-clock clock-icon"></i>
                    <div class="glass-effect"></div>
                </div>
                <div class="progress-badge">
                    <i class="fa-solid fa-square-check"></i> 
                    <span>Focus Mode</span>
                </div>
                <div class="timer-display" id="timer" contenteditable="true" spellcheck="false">
                    25 : 00
                </div>
                <div class="controls">
                    <button class="btn-alarm" id="btn-alarm" onclick="toggleAlarm()"><i class="fa-regular fa-bell"></i></button>
                    <button class="btn-main" id="btn-play" onclick="toggleTimer()"><i class="fa-solid fa-play" id="icon-play-pause"></i></button>
                    <button class="btn-music" id="btn-music" onclick="toggleMusic()"><i class="fa-solid fa-music"></i></button>
                </div>
            </div>
            
            <audio id="audio-music" loop src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio> 
            <audio id="audio-alarm" src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg"></audio>
        </section>

        <section class="schedule-section">
            <div class="section-title">Kuliahmu Hari ini (<?= $hari_indo ?>)</div>
            
            <div class="schedule-grid">
                <?php if (count($jadwal_hari_ini) > 0): ?>
                    <?php foreach ($jadwal_hari_ini as $row): ?>
                        <?php
                            // 1. Cek Active Class
                            $start = strtotime($row['jam_mulai']);
                            $end   = strtotime($row['jam_selesai']);
                            $now   = time();
                            $isActive = ($now >= $start && $now <= $end) ? 'active' : '';

                            // 2. Icon Randomizer
                            $icon = 'fa-book';
                            $lowerName = strtolower($row['namaMatkul']);
                            if(strpos($lowerName, 'web') !== false) $icon = 'fa-globe';
                            if(strpos($lowerName, 'jaringan') !== false) $icon = 'fa-network-wired';
                            if(strpos($lowerName, 'data') !== false) $icon = 'fa-database';
                        ?>

                        <div class="course-card <?= $isActive ?>">
                            <div class="card-top">
                                <span class="course-name"><?= htmlspecialchars($row['namaMatkul']) ?></span>
                                <div class="course-icon gray">
                                    <i class="fa-solid <?= $icon ?>"></i>
                                </div>
                            </div>
                            <div class="lecturer">
                                <i class="fa-solid fa-user-tie"></i>
                                <span><?= htmlspecialchars($row['dosenMatkul']) ?></span>
                            </div>
                            <div class="card-bottom">
                                <span class="sks-badge blue"><?= $row['sks'] ?> SKS</span>
                                <span class="time">
                                    <i class="fa-regular fa-clock"></i> 
                                    <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?> WIB
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                        <i class="fa-solid fa-mug-hot" style="font-size: 32px; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: #64748b; font-weight: 500; margin: 0;">Tidak ada jadwal kuliah hari ini. Selamat istirahat!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/right_sidebar_beranda.php'; ?>
</div>

    <script src="script/dashboard.js?v=<?php echo time(); ?>"></script>
</body>
</html>