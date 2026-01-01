<?php
// 1. Setup Variabel Halaman (Untuk Sidebar Active State)
$current_page = 'kalender.php'; 

// 2. Logika Tanggal & Waktu
date_default_timezone_set('Asia/Jakarta');

// Ambil bulan/tahun dari URL, jika tidak ada pakai waktu sekarang
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
} else {
    $month = date('m');
    $year = date('Y');
}

// Hitung timestamp hari pertama bulan ini
$timestamp = mktime(0, 0, 0, $month, 1, $year);

// Informasi Dasar
$monthName = date('F', $timestamp); // Januari, Februari...
$daysInMonth = date('t', $timestamp); // Total hari (28/30/31)
$dayOfWeek = date('w', $timestamp); // 0 (Minggu) - 6 (Sabtu)

// Ubah Index Hari: Default PHP Minggu(0), Kita mau Senin(0) agar sesuai gambar
// Jadi: Senin=0, ..., Minggu=6
$dayOfWeek = ($dayOfWeek == 0) ? 6 : $dayOfWeek - 1;

// Array Nama Bulan (Indonesia)
$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];

// Navigasi Prev/Next
$prevMonth = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
$prevYear = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
$nextMonth = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
$nextYear = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

// --- DATA DUMMY EVENT (Simulasi Database) ---
// Format: 'YYYY-MM-DD' => [Array Event]
$events = [
    "$year-$month-01" => [['title' => 'Tahun Baruan', 'color' => 'dot-purple']],
    "$year-$month-02" => [['title' => 'Dev DailyCollege', 'color' => 'dot-blue']],
    "$year-$month-04" => [['title' => 'Meeting Projek 2M', 'color' => 'dot-green']],
    "$year-$month-13" => [
        ['title' => 'Kondangan Mantan', 'color' => 'dot-red'],
        ['title' => 'Dev DailyCollege', 'color' => 'dot-blue']
    ],
    "$year-$month-31" => [['title' => 'Nongkrong With Cewe', 'color' => 'dot-red']],
];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender - SIJAWA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/kalender.css?v=<?php echo time(); ?>"> 
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>

            <main class="content">
                
                <div class="calendar-container">
                    <div class="calendar-header">
                        <h1 class="title">Jadwal & Kalender</h1>
                        <div class="month-nav">
                            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn-nav"><i class="fa-solid fa-chevron-left"></i></a>
                            <span><?= $bulanIndo[$monthName] . " " . $year ?></span>
                            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn-nav"><i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div class="calendar-grid">
                        
                        <div class="day-name">Sen</div>
                        <div class="day-name">Sel</div>
                        <div class="day-name">Rab</div>
                        <div class="day-name">Kam</div>
                        <div class="day-name">Jum</div>
                        <div class="day-name">Sab</div>
                        <div class="day-name">Min</div>

                        <?php
                        // 2. Loop Kotak Kosong / Bulan Lalu (Padding Awal)
                        // Hitung tanggal akhir bulan lalu
                        $daysInPrevMonth = date('t', mktime(0,0,0, $month-1, 1, $year));
                        
                        for ($i = 0; $i < $dayOfWeek; $i++) {
                            $prevDate = $daysInPrevMonth - ($dayOfWeek - 1 - $i);
                            echo '<div class="calendar-day other-month">';
                            echo '<span class="date-number">' . $prevDate . '</span>';
                            echo '</div>';
                        }

                        // 3. Loop Tanggal Bulan Ini
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            // Format tanggal YYYY-MM-DD untuk cek event
                            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            
                            // Cek apakah hari ini
                            $isToday = ($currentDate == date('Y-m-d')) ? 'today' : '';

                            echo "<div class='calendar-day $isToday'>";
                            echo "<span class='date-number'>$day</span>";
                            
                            // Cek jika ada event di tanggal ini
                            if (isset($events[$currentDate])) {
                                foreach ($events[$currentDate] as $evt) {
                                    echo '<div class="event-item">';
                                    echo '<span class="event-dot ' . $evt['color'] . '"></span>';
                                    // Gunakan substr agar teks tidak kepanjangan
                                    echo '<span>' . substr($evt['title'], 0, 15) . '</span>'; 
                                    echo '</div>';
                                }
                            }

                            echo "</div>";
                        }

                        // 4. Loop Kotak Kosong / Bulan Depan (Sisa Grid)
                        // Total grid yang terpakai sejauh ini
                        $totalCells = $dayOfWeek + $daysInMonth;
                        // Sisa kotak untuk melengkapi grid 7 kolom
                        $remainingCells = (7 - ($totalCells % 7)) % 7;
                        // Tambahan baris jika total baris kurang dari 6 (opsional, agar kotak tetap rapi)
                        if($totalCells + $remainingCells < 35) {
                            $remainingCells += 7; // Tambah 1 baris lagi
                        }

                        for ($j = 1; $j <= $remainingCells; $j++) {
                            echo '<div class="calendar-day other-month">';
                            echo '<span class="date-number">' . sprintf('%02d', $j) . '</span>';
                            echo '</div>';
                        }
                        ?>

                    </div>
                </div>

                <div class="schedule-section">
                    <div class="schedule-header-row">
                        <h2 class="section-title">Jadwal Kuliah</h2>
                        
                        <div class="header-actions">
                            <button class="btn-import excel" onclick="alert('Fitur Import Excel')">
                                <i class="fa-solid fa-file-excel"></i> 
                                <span class="desktop-only">Excel</span>
                            </button>
                            
                            <button class="btn-import pdf" onclick="alert('Fitur Import PDF')">
                                <i class="fa-solid fa-file-pdf"></i> 
                                <span class="desktop-only">PDF</span>
                            </button>
                        </div>
                    </div>

                    <div class="schedule-grid">
                        
                        <div class="day-card">
                            <div class="day-header">Hari Senin</div>
                            <div class="course-list">
                            
                            <div class="course-item">
                                <div class="course-info-wrapper">
                                    <div class="room-badge">R. 3.16</div>
                                    <h4 class="subject-name">MPTI</h4>
                                </div>
                                
                                <div class="item-actions">
                                    <button class="btn-mini edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-mini delete" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>

                            <div class="course-item">
                                <div class="course-info-wrapper">
                                    <div class="room-badge">R. 3.16</div>
                                    <h4 class="subject-name">PABW</h4>
                                </div>
                                <div class="item-actions">
                                    <button class="btn-mini edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-mini delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>

                            <div class="course-item">
                                <div class="course-info-wrapper">
                                    <div class="room-badge">R. 3.16</div>
                                    <h4 class="subject-name">PPKn</h4>
                                </div>
                                <div class="item-actions">
                                    <button class="btn-mini edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-mini delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>

                            </div>
                        </div>

                        <div class="day-card">
                            <div class="day-header">Hari Selasa</div>
                            <div class="course-list">
                            <div class="course-item">
                                <div class="course-info-wrapper">
                                    <div class="room-badge">Lab Jarkom</div>
                                    <h4 class="subject-name">Jaringan</h4>
                                </div>
                                <div class="item-actions">
                                    <button class="btn-mini edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-mini delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                            </div>
                        </div>
                        
                        </div>
                    </div>

            </main>
            <aside class="right-sidebar">
                <div class="right-schedule">
                    <div class="section-header">
                        <h4>Kuliahmu</h4>
                        <span class="day-label">Senin</span>
                    </div>

                    <div class="mini-course-list">
                        
                        <div class="mini-card">
                            <div class="card-left">
                                <span class="mini-subject">MPTI</span>
                                <span class="mini-badge blue">3 SKS</span>
                            </div>
                            <div class="card-right">
                                <div class="info-row">
                                    <i class="fa-solid fa-user-tie"></i> <span>Dr. Kurniawan Irianto</span>
                                </div>
                                <div class="info-row">
                                    <i class="fa-regular fa-clock"></i> <span>09.30 WIB</span>
                                </div>
                            </div>
                        </div>

                        <div class="mini-card">
                            <div class="card-left">
                                <span class="mini-subject">PABW</span>
                                <span class="mini-badge blue">6 SKS</span>
                            </div>
                            <div class="card-right">
                                <div class="info-row">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Dr. Nur Wijayaning</span>
                                </div>
                                <div class="info-row">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>12.30 WIB</span>
                                </div>
                            </div>
                        </div>

                        <div class="mini-card">
                            <div class="card-left">
                                <span class="mini-subject">PPKn</span>
                                <span class="mini-badge blue">2 SKS</span>
                            </div>
                            <div class="card-right">
                                <div class="info-row">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span>Dr. Ahmad Asroni</span>
                                </div>
                                <div class="info-row">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>15.30 WIB</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </aside>
        </div>
    </div>

</body>
</html>