<?php
    require_once __DIR__ . '/../config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIJAWA - Sistem Jadwal Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/style/style.css">   
</head>
<body>
    <nav>
        <div class="logo">
            <i class="fa-solid fa-layer-group"></i>
            SIJAWA<span>.</span>
        </div>
        <ul class="nav-links">
            <li><a href="#features">Fitur</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#pricing">Harga</a></li>
        </ul>
        <div class="auth-buttons">
            <button class="btn btn-login"><a href="login.php">Masuk</a></button>
            <button class="btn btn-register"><a href="register.php">Daftar</a></button>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-tags">
            <span class="tag"><i class="fa-solid fa-check-circle"></i> Structured Folder</span>
            <span class="tag"><i class="fa-solid fa-clock"></i> Deadline Calendar</span>
            <span class="tag" style="color: var(--alert-pink);"><i class="fa-solid fa-heart"></i> Mental Health</span>
        </div>

        <h1>Ganti Semua Aplikasi Produktivitasmu<br>
        dengan <span>SIJAWA</span></h1>
        
        <p>
            Platform <i>all-in-one</i> yang membantu mahasiswa mengatur jadwal kuliah, tugas, 
            keuangan, dan waktu istirahat dalam satu sistem terpadu. Selamat tinggal jadwal bentrok dan burnout!
        </p>

        <div class="auth-buttons" style="justify-content: center;">
            <button class="btn btn-register" onclick="handleNavigation('register')">Mulai Sekarang</button>
            <button class="btn btn-login" onclick="scrollToFeatures()">Pelajari Lebih Lanjut</button>
        </div>

        <div class="dashboard-preview">
            <div class="mock-header">
                <div class="dot red"></div>
                <div class="dot yellow"></div>
                <div class="dot green"></div>
            </div>
            
            <div class="mock-content">
                <div class="mock-sidebar">
                    <div></div><div></div><div></div><div></div>
                </div>

                <div class="mock-main">
                    <div class="stat-cards">
                        <div class="card card-1">
                            <div class="card-title">Tugas Selesai</div>
                            <div class="card-val">12</div>
                        </div>
                        <div class="card card-2">
                            <div class="card-title">Jadwal Kuliah</div>
                            <div class="card-val">Scan PDF <i class="fa-solid fa-file-pdf"></i></div>
                        </div>
                        <div class="card card-3">
                            <i class="fa-solid fa-stopwatch" style="font-size: 2rem; margin-bottom: 5px;"></i>
                            <div class="card-title" style="color:var(--dark-text)">Me-Time</div>
                        </div>
                    </div>
                    
                    <div style="background: #f4f6f9; padding: 15px; border-radius: 10px;">
                        <h4 style="margin-bottom:10px; font-size: 0.9rem;">Prioritas Tugas (Traffic Light System)</h4>
                        <div style="display:flex; gap:10px; align-items:center; margin-bottom: 8px;">
                            <span style="width:10px; height:10px; background:var(--alert-pink); border-radius:50%;"></span>
                            <div style="background: white; width: 100%; height: 20px; border-radius: 4px;"></div>
                        </div>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <span style="width:10px; height:10px; background:var(--highlight-yellow); border-radius:50%;"></span>
                            <div style="background: white; width: 70%; height: 20px; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

                <div class="mock-right">
                    <h5 style="margin-bottom: 10px;">Reminder Laundry</h5>
                    <div style="background: white; padding: 10px; border-radius: 8px; border-left: 3px solid var(--primary-blue);">
                        Stok Baju Bersih <br> <b>Tinggal 2 Pasang!</b>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="features">
    <div class="features-container">
        <div class="section-header">
            <h2 style="font-size: 2.5rem; margin-bottom: 10px;">Fitur Unggulan</h2>
            <p style="color: #666; max-width: 600px; margin: 0 auto;">
                Solusi all-in-one yang dirancang khusus untuk mengatasi masalah mahasiswa modern.
            </p>
        </div>

        <div class="slider-wrapper">
            <button class="slider-btn prev-btn" onclick="scrollSlider(-1)">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="feature-slider" id="featureSlider">
                
                <div class="feature-card">
                    <div class="icon-wrapper" style="background: rgba(0, 121, 255, 0.1); color: var(--primary-blue);">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <h3>PDF Schedule Scan</h3>
                    <p>Cukup upload PDF jadwal kuliah dari universitas. Sistem otomatis mengekstrak & menyusunnya ke kalender.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-wrapper" style="background: rgba(0, 223, 162, 0.1); color: var(--accent-green);">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <h3>MindBreak Advisor</h3>
                    <p>Deteksi potensi <i>burnout</i> dari kepadatan jadwalmu dan dapatkan rekomendasi waktu istirahat cerdas.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-wrapper" style="background: rgba(255, 0, 96, 0.1); color: var(--alert-pink);">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h3>Traffic Light Tasks</h3>
                    <p>Prioritas visual: <span style="color:#FF0060; font-weight:bold;">Merah</span> (Mendesak), <span style="color:#e6b800; font-weight:bold;">Kuning</span> (Sedang), & <span style="color:#00DFA2; font-weight:bold;">Hijau</span> (Santai).</p>
                </div>

                <div class="feature-card">
                    <div class="icon-wrapper" style="background: rgba(246, 250, 112, 0.3); color: #b8bd00;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3>Smart Budgeting</h3>
                    <p>Kategorisasi otomatis pengeluaran (makan, transport, dll) dengan notifikasi jika anggaran menipis.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-wrapper" style="background: rgba(0, 121, 255, 0.1); color: var(--primary-blue);">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <h3>Laundry Loop</h3>
                    <p>Lacak stok baju bersihmu. Dapatkan pengingat otomatis kapan waktu terbaik untuk mencuci.</p>
                </div>

                <div class="feature-card">
                    <div class="icon-wrapper" style="background: #f1f2f6; color: var(--dark-text);">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h3>Holistic View</h3>
                    <p>Pantau akademik, keuangan, dan kesehatan mental dalam satu dasbor ringkas yang terintegrasi.</p>
                </div>

            </div>

            <button class="slider-btn next-btn" onclick="scrollSlider(1)">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

    <section class="cta-section">
        <div class="cta-box">
            <h2>Siap Menjadi Mahasiswa 1% Teratas?</h2>
            <p>Bergabunglah dengan Pejuang IPK lainnya dan atur hidupmu sekarang juga.</p>
            <button onclick="handleNavigation('register')">Download App / Daftar Web</button>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo">
                    <i class="fa-solid fa-layer-group"></i>
                    SIJAWA<span>.</span>
                </div>
                <p>Sistem Jadwal Mahasiswa untuk kehidupan kampus yang seimbang, produktif, dan terorganisir.</p>
            </div>
            <div class="footer-col">
                <h4>Produk</h4>
                <a href="#">Fitur</a>
                <a href="#">Harga</a>
                <a href="#">Showcase</a>
            </div>
            <div class="footer-col">
                <h4>Tim Pejuang IPK</h4>
                <a href="#">Tentang Kami</a>
                <a href="#">Kontak</a>
                <a href="#">Karir</a>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 SIJAWA - Tim Pejuang IPK. Universitas Islam Indonesia.
        </div>
    </footer>

    <script src="/script/script.js?v=2"></script>
</body>
</html>