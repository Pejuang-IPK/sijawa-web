<?php
require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../app/controller/JadwalController.php';
require_once __DIR__ . '../../app/helper/Singkatan.php';
require_once __DIR__ . '../../app/action/jadwal_action.php';

$id_mahasiswa = 386937;

// Ambil semua jadwal mahasiswa
$queryJadwal = "
    SELECT *
    FROM Jadwal
    WHERE id_mahasiswa = $id_mahasiswa
    ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),
         jam_mulai ASC
";

$semua_jadwal = query($queryJadwal);

// Grouping jadwal per hari
$jadwal_per_hari = [];

foreach ($semua_jadwal as $row) {
    $hari = trim($row['hari']);
    $jadwal_per_hari[$hari][] = $row;
}

// Urutan hari (WAJIB, karena dipakai grid)
$urutan_hari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

// Pastikan semua hari ada key-nya
foreach ($urutan_hari as $hari) {
    if (!isset($jadwal_per_hari[$hari])) {
        $jadwal_per_hari[$hari] = [];
    }
}

// Logic Tambah
if(isset($_POST["submit_tambah"])) {
    if(tambah($_POST) > 0) {
        echo "<script>alert('Berhasil!'); document.location.href = '';</script>";
    } else {
        echo "<script>alert('Gagal!');</script>";
    }
}

// Logic Ubah
if(isset($_POST["submit_ubah"])) {
    if(ubah($_POST) > 0) {
        echo "<script>alert('Berhasil diubah!'); document.location.href = '';</script>";
    } else {
        echo "<script>alert('Gagal ubah!');</script>";
    }
}

// Logic Import
if(isset($_POST["submit_import"])) {
    if(importExcel($_FILES)) {
        echo "<script>alert('Import Berhasil!'); document.location.href = '';</script>";
    } else {
        echo "<script>alert('Import Gagal/Format Salah!');</script>";
    }
}

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
// // Format: 'YYYY-MM-DD' => [Array Event]
// $events = [
//     "$year-$month-01" => [['title' => 'Tahun Baruan', 'color' => 'dot-purple']],
//     "$year-$month-02" => [['title' => 'Dev DailyCollege', 'color' => 'dot-blue']],
//     "$year-$month-04" => [['title' => 'Meeting Projek 2M', 'color' => 'dot-green']],
//     "$year-$month-13" => [
//         ['title' => 'Kondangan Mantan', 'color' => 'dot-red'],
//         ['title' => 'Dev DailyCollege', 'color' => 'dot-blue']
//     ],
//     "$year-$month-31" => [['title' => 'Nongkrong With Cewe', 'color' => 'dot-red']],
// ];
// 1. AMBIL SEMUA DATA JADWAL
// Kita tidak perlu filter hari ini, karena kita butuh data seminggu penuh untuk diulang
$id_mahasiswa = 386937; 
$all_jadwal = query("SELECT * FROM Jadwal WHERE id_mahasiswa = $id_mahasiswa ORDER BY jam_mulai ASC");

// 2. KELOMPOKKAN JADWAL BERDASARKAN HARI
// Hasil: $jadwal_mingguan['Senin'] = [Matkul A, Matkul B]
$jadwal_mingguan = [];
foreach ($all_jadwal as $row) {
    // Pastikan huruf depan besar (Senin, Selasa)
    $hari = ucfirst(strtolower($row['hari'])); 
    $jadwal_mingguan[$hari][] = $row;
}

// Array Helper untuk Warna Warni (Agar cantik)
$colors = ['event-blue', 'event-green', 'event-orange', 'event-red'];
$color_index = 0;

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
    <link rel="stylesheet" href="style/form_tambah_jadwal.css?v=<?php echo time(); ?>"> 
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>

            <main class="content">
                <?php include 'includes/calendar.php'; ?>
                <div class="schedule-section">
                    <div class="schedule-header-row">
                        <h2 class="section-title">Jadwal Kuliah</h2>
                        
                        <div class="header-actions">
                            <button class="btn-import add-new" onclick="openModal()">
                                <i class="fa-solid fa-plus"></i> 
                                <span class="desktop-only">Tambah</span>
                            </button>
                            <button class="btn-import excel" onclick="openModal('importModal')">
                                <i class="fa-solid fa-file-excel"></i> 
                                <span class="desktop-only">Import Excel</span>
                            </button>
                            <button class="btn-import pdf" onclick="alert('Fitur Import PDF')">
                                <i class="fa-solid fa-file-pdf"></i> 
                                <span class="desktop-only">PDF</span>
                            </button>
                        </div>
                    </div>

                    <?php include 'includes/form_tambah_jadwal.php'; ?>
                    <?php include 'includes/modal_import_excel.php'; ?>
                    <?php include 'includes/modal_edit_jadwal.php'; ?>
                    <?php include 'includes/modal_delete_jadwal.php'; ?>
                    <?php include 'includes/schedule_grid.php'; ?>
                    
                </div>
            </main>
            <?php include 'includes/right_sidebar_kalender.php'; ?>
        </div>
    </div>
    
    <script src="script/kalender.js?v=<?php echo time(); ?>"></script>
</body>
</html>