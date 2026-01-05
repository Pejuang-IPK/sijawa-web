<?php
require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../app/controller/JadwalController.php';
require_once __DIR__ . '../../app/helper/Singkatan.php';
require_once __DIR__ . '../../app/action/jadwal_action.php';

$id_mahasiswa = $_SESSION['user_id'];

$queryJadwal = "
    SELECT *
    FROM Jadwal
    WHERE id_mahasiswa = $id_mahasiswa
    ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),
         jam_mulai ASC
";

$semua_jadwal = query($queryJadwal);

$jadwal_per_hari = [];

foreach ($semua_jadwal as $row) {
    $hari = trim($row['hari']);
    $jadwal_per_hari[$hari][] = $row;
}

$urutan_hari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

foreach ($urutan_hari as $hari) {
    if (!isset($jadwal_per_hari[$hari])) {
        $jadwal_per_hari[$hari] = [];
    }
}

$add_status = null;
if(isset($_POST["submit_tambah"])) {
    if(tambah($_POST) > 0) {
        $add_status = 'success';
    } else {
        $add_status = 'error';
    }
}

if(isset($_POST["submit_ubah"])) {
    if(ubah($_POST) > 0) {
        echo "<script>alert('Berhasil diubah!'); document.location.href = '';</script>";
    } else {
        echo "<script>alert('Gagal ubah!');</script>";
    }
}

$import_status = null;
if(isset($_POST["submit_import"])) {
    $import_status = importExcel($_FILES);
}

$current_page = 'kalender.php'; 

date_default_timezone_set('Asia/Jakarta');

if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
} else {
    $month = date('m');
    $year = date('Y');
}

$timestamp = mktime(0, 0, 0, $month, 1, $year);

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

$prevMonth = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
$prevYear = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
$nextMonth = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
$nextYear = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

$all_jadwal = query("SELECT * FROM Jadwal WHERE id_mahasiswa = $id_mahasiswa ORDER BY jam_mulai ASC");

$jadwal_mingguan = [];
foreach ($all_jadwal as $row) {
    $hari = ucfirst(strtolower($row['hari'])); 
    $jadwal_mingguan[$hari][] = $row;
}

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
                        </div>
                    </div>

                    <?php include 'includes/form_tambah_jadwal.php'; ?>
                    <?php include 'includes/modal_import_excel.php'; ?>
                    <?php include 'includes/modal_import_status.php'; ?>
                    <?php include 'includes/modal_add_status.php'; ?>
                    <?php include 'includes/modal_edit_jadwal.php'; ?>
                    <?php include 'includes/modal_delete_jadwal.php'; ?>
                    <?php include 'includes/schedule_grid.php'; ?>
                    
                </div>
            </main>
            <?php include 'includes/right_sidebar_kalender.php'; ?>
        </div>
    </div>
    
    <script src="script/kalender.js?v=<?php echo time(); ?>"></script>

    <script>
        <?php if ($add_status): ?>
            let addStatus = '<?= $add_status ?>';
            let addModal = document.getElementById('addStatusModal');
            let addTitle = document.getElementById('addStatusTitle');
            let addMessage = document.getElementById('addStatusMessage');
            let addHeader = document.getElementById('addStatusHeader');

            if (addStatus === 'success') {
                addTitle.textContent = 'Jadwal Berhasil Ditambah';
                addMessage.innerHTML = '<i class="fa-solid fa-check-circle" style="color: #10b981; font-size: 40px; display: block; margin-bottom: 15px;"></i>Jadwal kuliah baru telah berhasil ditambahkan ke sistem!';
                addHeader.style.borderBottomColor = '#10b981';
            } else if (addStatus === 'error') {
                addTitle.textContent = 'Gagal Menambah Jadwal';
                addMessage.innerHTML = '<i class="fa-solid fa-xmark-circle" style="color: #ef4444; font-size: 40px; display: block; margin-bottom: 15px;"></i>Terjadi kesalahan saat menambah jadwal. Silakan coba kembali.';
                addHeader.style.borderBottomColor = '#ef4444';
            }

            addModal.style.display = 'flex';
        <?php endif; ?>

        <?php if ($import_status): ?>
            let importStatus = '<?= $import_status ?>';
            let importModal = document.getElementById('importStatusModal');
            let importTitle = document.getElementById('importStatusTitle');
            let importMessage = document.getElementById('importStatusMessage');
            let importHeader = document.getElementById('importStatusHeader');

            if (importStatus === 'success') {
                importTitle.textContent = 'Import Berhasil';
                importMessage.innerHTML = '<i class="fa-solid fa-check-circle" style="color: #10b981; font-size: 40px; display: block; margin-bottom: 15px;"></i>Jadwal berhasil diimpor ke sistem!';
                importHeader.style.borderBottomColor = '#10b981';
            } else if (importStatus === 'duplicate') {
                importTitle.textContent = 'Import Gagal';
                importMessage.innerHTML = '<i class="fa-solid fa-xmark-circle" style="color: #ef4444; font-size: 40px; display: block; margin-bottom: 15px;"></i>Jadwal yang Anda impor sudah ada di sistem. Silakan periksa kembali data Anda.';
                importHeader.style.borderBottomColor = '#ef4444';
            } else if (importStatus === 'partial') {
                importTitle.textContent = 'Import Sebagian Berhasil';
                importMessage.innerHTML = '<i class="fa-solid fa-exclamation-circle" style="color: #f59e0b; font-size: 40px; display: block; margin-bottom: 15px;"></i>Beberapa jadwal berhasil diimpor, namun beberapa sudah ada di sistem.';
                importHeader.style.borderBottomColor = '#f59e0b';
            } else if (importStatus === 'error') {
                importTitle.textContent = 'Import Gagal';
                importMessage.innerHTML = '<i class="fa-solid fa-xmark-circle" style="color: #ef4444; font-size: 40px; display: block; margin-bottom: 15px;"></i>Terjadi kesalahan saat mengimpor. Pastikan format file Excel sudah benar.';
                importHeader.style.borderBottomColor = '#ef4444';
            }

            importModal.style.display = 'flex';
        <?php endif; ?>

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>