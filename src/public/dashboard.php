<?php
session_start();

// Proteksi: Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data user dari session
$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIJAWA - Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style/style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="layout">
    
    <aside class="sidebar">
        
        <div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px;">
            <i class="fa-solid fa-layer-group" style="color: #0079ff;"></i>
        </div>
        
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-item active" title="Dashboard">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="jadwal_harian.php" class="nav-item" title="Jadwal Kuliah">
                <i class="fa-regular fa-calendar"></i>
            </a>
            <a href="#" class="nav-item" title="Tugas">
                <i class="fa-solid fa-list-check"></i>
            </a>
            <a href="#" class="nav-item" title="Keuangan">
                <i class="fa-solid fa-wallet"></i>
            </a>
            <a href="#" class="nav-item" title="Laundry">
                <i class="fa-solid fa-shirt"></i>
            </a>
        </nav>

        <a href="logout.php" class="nav-item" title="Logout" style="margin-top: auto; color: var(--danger);">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
    </aside>

    <main class="main">
        
        <header class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Dashboard</h1>
                <p style="color: #666; font-size: 14px;">Halo, <?php echo htmlspecialchars($userName); ?>! 👋</p>
            </div>
            <div class="header-actions">
                <button class="icon-btn"><i class="fa-solid fa-gear"></i></button>
                <button class="icon-btn"><i class="fa-solid fa-arrows-rotate"></i></button>
            </div>
        </header>

        <div class="dashboard-grid">
            
            <!-- Main Content Area -->
            <div class="dashboard-main">
                
                <!-- Stats Cards Row 1 -->
                <div class="stats-row">
                    <div class="stat-card stat-white tugas-card">
                        <div class="stat-icon"><i class="fa-solid fa-list-check"></i></div>
                        <div class="stat-number">2</div>
                        <div class="stat-label">Tugas</div>
                        <div class="stat-detail">2 Ditugaskan | 0 Belum Diserahkan</div>
                    </div>

                    <div class="stat-card stat-blue">
                        <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div class="stat-number">25</div>
                        <div class="stat-label">Catatan</div>
                    </div>

                    <div class="stat-card stat-yellow">
                        <div class="stat-icon"><i class="fa-solid fa-file"></i></div>
                        <div class="stat-number">3</div>
                        <div class="stat-label">Projek</div>
                    </div>

                    <div class="stat-card stat-light matkul-card">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Mata Kuliah Hari Ini</div>
                    </div>
                </div>

                <!-- Stats Cards Row 2 -->
                <div class="stats-row-2">
                    <div class="keuangan-card">
                        <div class="keuangan-icon"><i class="fa-solid fa-wallet"></i></div>
                        <div class="keuangan-title">Total Saldo</div>
                        <div class="keuangan-amount">Rp 4.954.000</div>
                    </div>

                    <div class="stat-card-medium stat-white laundry-card">
                        <div class="laundry-content">
                            <div class="laundry-header">
                                <div class="laundry-title">Laundry Loop</div>
                            </div>
                            <div class="laundry-status">Saatnya Laundry!</div>
                        </div>
                    </div>

                    <div class="stat-card-medium stat-white stress-card">
                        <div class="stress-content">
                            <div class="stress-title">Stress Level</div>
                            <div class="stress-bar">
                                <div class="stress-fill" style="width: 30%;"></div>
                            </div>
                            <div class="stress-label">Rendah</div>
                        </div>
                    </div>

                    <div class="metime-card">
                        <div class="metime-title">Me-Time</div>
                        <div class="metime-subtitle">Waktu santai berkumpul:</div>
                        <div class="metime-time">14.00 - 15.30</div>
                    </div>
                </div>

                <!-- Riwayat & Jadwal Section -->
                <div class="bottom-grid">
                    
                    <!-- Riwayat Transaksi -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3 class="section-title">Riwayat Transaksi</h3>
                            <button class="icon-btn-small"><i class="fa-solid fa-arrows-rotate"></i></button>
                        </div>
                        
                        <div class="month-label">November</div>
                        
                        <div class="transaction-list">
                            <div class="transaction-item">
                                <div class="transaction-icon income">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-name">Duit Jajan</div>
                                    <div class="transaction-category">Transfer Bulanan</div>
                                </div>
                                <div class="transaction-right">
                                    <div class="transaction-amount income">+Rp1.400.000</div>
                                    <div class="transaction-date">12 Des 2024</div>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="transaction-icon expense">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-name">Beli Makan</div>
                                    <div class="transaction-category">Makan</div>
                                </div>
                                <div class="transaction-right">
                                    <div class="transaction-amount expense">-Rp100.000</div>
                                    <div class="transaction-date">12 Des 2024</div>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="transaction-icon expense">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-name">Topup</div>
                                    <div class="transaction-category">Hiburan</div>
                                </div>
                                <div class="transaction-right">
                                    <div class="transaction-amount expense">-Rp100.000</div>
                                    <div class="transaction-date">12 Des 2024</div>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="transaction-icon expense">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-name">Lainnya</div>
                                    <div class="transaction-category">Pendidikan</div>
                                </div>
                                <div class="transaction-right">
                                    <div class="transaction-amount expense">-Rp50.000</div>
                                    <div class="transaction-date">12 Nov 2024</div>
                                </div>
                            </div>
                        </div>

                        <button class="btn-view-all">Lihat Semua Riwayat</button>
                    </div>

                    <!-- Jadwal Mata Kuliah Hari Ini -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3 class="section-title">Jadwal Mata Kuliah Hari Ini</h3>
                            <button class="icon-btn-small"><i class="fa-solid fa-calendar"></i></button>
                        </div>

                        <div class="schedule-date">Selasa, 17 Desember 2025</div>

                        <div class="schedule-list">
                            <div class="schedule-item">
                                <div class="schedule-time">
                                    <div class="time-dot"></div>
                                    <div class="time-text">09.00 - 12.00</div>
                                </div>
                                <div class="schedule-card">
                                    <div class="schedule-name">MPTI</div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-location-dot"></i> Ruang 301
                                    </div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-user"></i> Dr. Budi Santoso
                                    </div>
                                </div>
                            </div>

                            <div class="schedule-item">
                                <div class="schedule-time">
                                    <div class="time-dot"></div>
                                    <div class="time-text">12.30 - 15.00</div>
                                </div>
                                <div class="schedule-card">
                                    <div class="schedule-name">PABW</div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-location-dot"></i> Lab Komputer 2
                                    </div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-user"></i> Prof. Ani Wijaya
                                    </div>
                                </div>
                            </div>

                            <div class="schedule-item">
                                <div class="schedule-time">
                                    <div class="time-dot inactive"></div>
                                    <div class="time-text inactive">15.30 - 17.00</div>
                                </div>
                                <div class="schedule-card inactive">
                                    <div class="schedule-name">Praktikum Web</div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-location-dot"></i> Lab 3
                                    </div>
                                    <div class="schedule-detail">
                                        <i class="fa-solid fa-user"></i> Asisten Lab
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Right Sidebar -->
            <div class="dashboard-sidebar">
                
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-avatar">
                        <img src="https://ui-avatars.com/api/?name=Ahmad&background=667eea&color=fff" alt="Profile">
                    </div>
                    <h3 class="profile-name">Ahmad</h3>
                    <button class="btn-edit-profile">Edit Profil</button>
                </div>

                <!-- Next Deadline Card -->
                <div class="deadline-card">
                    <h3 class="deadline-title">Deadline Selanjutnya</h3>
                    
                    <div class="calendar-week">
                        <div class="calendar-day">
                            <span class="day-label">Sen</span>
                            <span class="day-number">23</span>
                        </div>
                        <div class="calendar-day active">
                            <span class="day-label">Sel</span>
                            <span class="day-number">24</span>
                        </div>
                        <div class="calendar-day">
                            <span class="day-label">Rab</span>
                            <span class="day-number">25</span>
                        </div>
                        <div class="calendar-day">
                            <span class="day-label">Kam</span>
                            <span class="day-number">26</span>
                        </div>
                        <div class="calendar-day">
                            <span class="day-label">Jum</span>
                            <span class="day-number">27</span>
                        </div>
                    </div>

                    <div class="project-deadline">
                        <div class="project-header">
                            <span class="project-label">Projek</span>
                            <span class="project-name">SIJAWA</span>
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </div>
                        <div class="project-meta">
                            <div class="project-team">
                                <span class="team-avatar">👥</span>
                                <span class="team-count">5/5</span>
                            </div>
                            <div class="project-date">
                                <i class="fa-regular fa-clock"></i>
                                <span class="date-text">Rab, 25 Nov</span>
                            </div>
                        </div>
                        <div class="project-progress">
                            <div class="progress-info">
                                <span>Progres</span>
                                <span class="progress-percent">60%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 60%;"></div>
                            </div>
                        </div>
                        <button class="expand-btn">Expand (2) <i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>

  

            </div>
        </div>

    </main>
</div>

</body>
</html>