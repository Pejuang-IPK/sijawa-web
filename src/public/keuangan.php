<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get user data from session
$id_mahasiswa = $_SESSION['id_mahasiswa'];
$nama_mahasiswa = $_SESSION['nama'];
$email_mahasiswa = $_SESSION['email'];

// Include database connection and controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/KeuanganController.php';

// Handle AJAX API requests untuk transaksi
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    handleTransaksiAPI();
}

// Handle AJAX API requests untuk langganan
if (isset($_GET['api']) && $_GET['api'] === 'langganan') {
    handleLanggananAPI();
}

if (isset($_POST['api']) && $_POST['api'] === 'langganan') {
    handleLanggananAPI();
}

// Handle charge subscription
if (isset($_POST['api']) && $_POST['api'] === 'charge') {
    handleChargeSubscription();
}

// Get all dashboard data from controller
$dashboard = getDashboardData($id_mahasiswa);

// Ekstrak variabel untuk kemudahan akses di template
extract($dashboard);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan - SIJAWA</title>
    <link rel="stylesheet" href="style/tugas.css">
    <link rel="stylesheet" href="style/keuangan.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard Keuangan</h1>
                    <p class="page-subtitle">Pantau arus kas dan kelola anggaran bulanmu - <?php echo htmlspecialchars($nama_mahasiswa); ?></p>
                </div>
                <button class="btn-add-transaction" onclick="bukaModal()">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Transaksi
                </button>
            </div>

            <?php if($pesan): ?>
                <div class="alert alert-<?php echo $tipe_pesan; ?>">
                    <?php echo $pesan; ?>
                </div>
            <?php endif; ?>

            <?php include 'includes/stats_cards.php'; ?>

            <!-- Category Sections -->
            <div class="category-grid">
                <?php include 'includes/income_categories.php'; ?>
                <?php include 'includes/expense_categories.php'; ?>
            </div>

            <!-- Bottom Grid -->
            <div class="bottom-grid">
                <?php include 'includes/transaction_history.php'; ?>
                <?php include 'includes/analytics_sidebar.php'; ?>
            </div>
        </main>
    </div>

    <?php include 'includes/modals.php'; ?>

    <script>
        // Data kategori dari PHP
        window.kategoriPemasukan = <?php echo json_encode($kategori_pemasukan); ?>;
        window.kategoriPengeluaran = <?php echo json_encode($kategori_pengeluaran); ?>;
        window.currentPeriod = '<?php echo $period; ?>';
        window.currentValue = '<?php echo $value; ?>';
        
        // Initialize filter
        UtilFilter.inisialisasi('<?php echo $period; ?>', '<?php echo $value; ?>');
    </script>
    
    <script src="script/modal-helper.js"></script>
    <script src="script/filter.js"></script>
    <script src="script/subscription.js"></script>
    <script src="script/transaction.js"></script>
</body>
</html>
