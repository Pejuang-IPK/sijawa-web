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
            // 1. Definisikan nama halaman ini
            $page = 'dashboard'; 

            // 2. Baru panggil sidebar
            include 'includes/sidebar.php'; 
        ?>
        <main class="content">
            <header class="content-header">
                    <div>
                        <h1>Dashboard Beranda</h1>
                        <p>Selamat datang kembali, semangat kuliahnya 🚀</p>
                    </div>
            </header>
            <section class="card">
                <div class="left">
                    <div class="matkul-card">
                        <div class="count">3</div>
                        <div class="label">Mata Kuliah Hari ini</div>
                    </div>
                    <div class="deadline-card">
                        <div class="dashed-bg"></div>
                        <div class="main-card">
                            <div class="count">5</div>
                            <div class="label">Tugas Deadline Hari ini</div>
                        </div>
                    </div>
                    <div class="progressbar-card">
                        <div class="title">Stress Level</div>
                        <div class="label-top-right">Tinggi</div>
                        <div class="progress-container">
                            <div class="progress-fill-red"></div>
                        </div>
                        <div class="label-bottom-left">Rendah</div>
                    </div>
                </div>
                <div class="mid">
                    <div class="progressbar-card">
                        <div class="title">Money Management</div>
                        <div class="label-top-right">Tinggi</div>
                        <div class="progress-container">
                            <div class="progress-fill-blue"></div>
                        </div>
                        <div class="label-bottom-left">Rendah</div>
                    </div>
                    <div class="next-card">
                        <div class="title">Mata Kuliah selanjutnya</div>
                        <div class="label">MPTI - Gedung B2 | Mulai dalam 15 menit.</div>
                    </div>
                    <div class="metime-card">
                        <div class="dashed-bg"></div>
                        <div class="main-card">
                            <div class="title">Me-Time</div>
                            <div class="label">Waktu santai berikutnya: 14.00 - 15.30</div>
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
                        <span>2/6</span>
                    </div>
                    <div class="timer-display" id="timer" contenteditable="true" 
                        spellcheck="false"
                        onfocus="pauseTimerManual()" 
                        onblur="validateInput()"
                        onkeydown="handleEnter(event)"
                    >
                        00 : 58
                    </div>
                    <div class="controls">
                        <button class="btn-alarm" id="btn-alarm" onclick="toggleAlarm()">
                            <i class="fa-regular fa-bell"></i>
                        </button>

                        <button class="btn-main" id="btn-play" onclick="toggleTimer()">
                            <i class="fa-solid fa-play" id="icon-play-pause"></i>
                        </button>

                        <button class="btn-music" id="btn-music" onclick="toggleMusic()">
                            <i class="fa-solid fa-music"></i>
                        </button>
                    </div>
                </div>

                <audio id="audio-music" loop src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio> 
                <audio id="audio-alarm" src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg"></audio>
            </section>
            <section class="schedule-section">
                <div class="section-title">Kuliahmu Hari ini</div>
                
                <div class="schedule-grid">
                    
                    <div class="course-card">
                        <div class="card-top">
                            <span class="course-name">MPTI</span>
                            <div class="course-icon gray">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                        </div>
                        <div class="lecturer">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Dr. Kurniawan Irianto</span>
                        </div>
                        <div class="card-bottom">
                            <span class="sks-badge blue">3 SKS</span>
                            <span class="time"><i class="fa-regular fa-clock"></i> 09.30 WIB</span>
                        </div>
                    </div>

                    <div class="course-card active">
                        <div class="card-top">
                            <span class="course-name">PABW</span>
                            <div class="course-icon blue-globe">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                        </div>
                        <div class="lecturer">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Dr. Nur Wijayaning</span>
                        </div>
                        <div class="card-bottom">
                            <span class="sks-badge blue">6 SKS</span>
                            <span class="time"><i class="fa-regular fa-clock"></i> 12.30 WIB</span>
                        </div>
                    </div>

                    <div class="course-card">
                        <div class="card-top">
                            <span class="course-name">PPKN</span>
                            <div class="course-icon gray">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="lecturer">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Dr. Ahmad Asroni</span>
                        </div>
                        <div class="card-bottom">
                            <span class="sks-badge blue">2 SKS</span>
                            <span class="time"><i class="fa-regular fa-clock"></i> 15.30 WIB</span>
                        </div>
                    </div>

                </div>
            </section>
        </main>
        <aside class="right-sidebar">
            <div class="profile-section">
                <div class="profile-img">
                    <img src="assets/shen_xiaoting.jpg" alt="Profile"> 
                </div>
                <h3>Shen Xiaoting</h3>
                <span class="badge-plan">Free Plan</span>
            </div>

            <hr class="divider">

            <div class="calendar-section">
                <h3>Tugasmu</h3>
                <div class="date-row">
                    <div class="date-card">
                        <span class="day">Sen</span>
                        <span class="date">29</span>
                    </div>
                    <div class="date-card active">
                        <span class="day">Sel</span>
                        <span class="date">30</span>
                    </div>
                    <div class="date-card">
                        <span class="day">Rab</span>
                        <span class="date">31</span>
                    </div>
                    <div class="date-card">
                        <span class="day">Kam</span>
                        <span class="date">1</span>
                    </div>
                    <div class="date-card">
                        <span class="day">Jum</span>
                        <span class="date">2</span>
                    </div>
                </div>
            </div>

            <div class="task-card">
                <div class="card-header">
                    <span>Hari ini</span>
                </div>
                <h2>Worksheet Nagios SJK</h2>
                <p class="subject">Matakuliah Sistem Jaringan Komputer</p>
                <hr class="card-divider">
                <div class="card-footer">
                    <button class="btn-action">Segera Kerjakan</button>
                    <div class="time">
                        <i class="fa-regular fa-clock"></i> 22.59
                    </div>
                </div>
            </div>

            <div class="motivation-card">
                <div class="title-motivation">Semangat Sukses 🔥</div>
                <p class="desc">Siap produktif hari ini? Atau mau scrolling dulu 5 menit?</p>
                
                <blockquote class="quote">
                    “Kerja keras tidak boleh berhenti”
                </blockquote>
                <div class="author">Joko Widodo</div>

                <div class="logo-corner">S.</div>
                
                <div class="bg-pattern"></div>
            </div>
        </aside>
    </div>

    <script src="script/dashboard.js?v=<?php echo time(); ?>"></script>
</body>
</html>