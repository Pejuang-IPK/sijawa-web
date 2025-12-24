<?php
session_start();

// Check if user is logged in
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

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        $data = [
            'id_mahasiswa' => $id_mahasiswa,
            'saldo' => 0,
            'transaksi' => $_POST['transaksi'],
            'keteranganTransaksi' => $_POST['keteranganTransaksi'],
            'jenisTransaksi' => $_POST['jenisTransaksi'],
            'kategoriTransaksi' => $_POST['kategoriTransaksi']
        ];
        
        $result = tambahTransaksi($data);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'error';
    } elseif ($_POST['action'] === 'tambah_kategori') {
        // Tambah kategori dengan membuat transaksi dummy
        $data = [
            'id_mahasiswa' => $id_mahasiswa,
            'saldo' => 0,
            'transaksi' => 0,
            'keteranganTransaksi' => 'Kategori: ' . $_POST['namaKategori'],
            'jenisTransaksi' => $_POST['jenisTransaksi'],
            'kategoriTransaksi' => $_POST['namaKategori']
        ];
        
        $result = tambahTransaksi($data);
        $message = $result['success'] ? 'Kategori berhasil ditambahkan!' : $result['message'];
        $message_type = $result['success'] ? 'success' : 'error';
    }
}

// Get date filter
$period = isset($_GET['period']) ? $_GET['period'] : 'bulan';
$value = isset($_GET['value']) ? $_GET['value'] : ($period === 'semua' ? '' : '0');
$filter_label = 'Bulan Ini';
$date_condition = '';

switch($period) {
    case 'hari':
        $days_ago = (int)$value;
        $target_date = date('Y-m-d', strtotime("-$days_ago days"));
        $date_condition = " AND DATE(tanggalKeuangan) = '$target_date'";
        if ($days_ago == 0) {
            $filter_label = 'Hari Ini';
        } elseif ($days_ago == 1) {
            $filter_label = 'Kemarin';
        } else {
            $filter_label = $days_ago . ' Hari Lalu';
        }
        break;
    
    case 'minggu':
        $weeks_ago = (int)$value;
        $start_date = date('Y-m-d', strtotime("-$weeks_ago weeks monday"));
        $end_date = date('Y-m-d', strtotime("-$weeks_ago weeks sunday"));
        $date_condition = " AND DATE(tanggalKeuangan) BETWEEN '$start_date' AND '$end_date'";
        if ($weeks_ago == 0) {
            $filter_label = 'Minggu Ini';
        } elseif ($weeks_ago == 1) {
            $filter_label = 'Minggu Lalu';
        } else {
            $filter_label = $weeks_ago . ' Minggu Lalu';
        }
        break;
    
    case 'bulan':
        $months_ago = (int)$value;
        $target_month = date('m', strtotime("-$months_ago months"));
        $target_year = date('Y', strtotime("-$months_ago months"));
        $date_condition = " AND MONTH(tanggalKeuangan) = '$target_month' AND YEAR(tanggalKeuangan) = '$target_year'";
        $filter_label = date('F Y', strtotime("-$months_ago months"));
        break;
    
    case 'tahun':
        $years_ago = (int)$value;
        $target_year = date('Y') - $years_ago;
        $date_condition = " AND YEAR(tanggalKeuangan) = '$target_year'";
        if ($years_ago == 0) {
            $filter_label = 'Tahun Ini';
        } elseif ($years_ago == 1) {
            $filter_label = 'Tahun Lalu';
        } else {
            $filter_label = 'Tahun ' . $target_year;
        }
        break;
    
    case 'semua':
        $date_condition = '';
        $filter_label = 'Semua Waktu';
        break;
}

// Get kategori untuk dropdown
$kategori_pemasukan = getKategoriByMahasiswa($id_mahasiswa, 'Pemasukan');
$kategori_pengeluaran = getKategoriByMahasiswa($id_mahasiswa, 'Pengeluaran');
$statistik_kategori = getStatistikKategori($id_mahasiswa, $date_condition);

// Get transaction history
$riwayat_transaksi = getTransaksiWithFilter($id_mahasiswa, '', 8); // Get latest 8 transactions

// Get monthly analysis
$monthly_analysis = getMonthlyAnalysis($id_mahasiswa);

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Get financial data for logged in user
$query = "SELECT * FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa' ORDER BY tanggalKeuangan DESC";
$result = mysqli_query($conn, $query);
$keuangan_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate totals
$total_saldo = 0;
$total_pemasukan = 0;
$total_pengeluaran = 0;

foreach ($keuangan_data as $data) {
    if ($data['jenisTransaksi'] == 'Pemasukan') {
        $total_pemasukan += $data['transaksi'];
    } else {
        $total_pengeluaran += $data['transaksi'];
    }
}

$total_saldo = $total_pemasukan - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan - SIJAWA</title>
    <link rel="stylesheet" href="style/keuangan.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-sidebar">
                <span class="logo-text">S</span>
            </div>
            
            <nav class="nav-menu">
                <a href="index.php" class="nav-item" title="Dashboard">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="#" class="nav-item" title="Jadwal">
                    <i class="fa-solid fa-calendar-days"></i>
                </a>
                <a href="keuangan.php" class="nav-item active" title="Keuangan">
                    <i class="fa-solid fa-wallet"></i>
                </a>
            </nav>

            <div class="nav-bottom">
                <a href="#" class="nav-item" title="Profile">
                    <i class="fa-solid fa-user"></i>
                </a>
                <a href="logout.php" class="nav-item nav-item-danger" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard Keuangan</h1>
                    <p class="page-subtitle">Pantau arus kas dan kelola anggaran bulanmu - <?php echo htmlspecialchars($nama_mahasiswa); ?></p>
                </div>
                <button class="btn-add-transaction" onclick="openModal()">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Transaksi
                </button>
            </div>

            <?php if($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card stat-primary">
                    <div class="stat-icon-wrapper">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Saldo</p>
                        <h2 class="stat-value">Rp <?php echo number_format($total_saldo, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="stat-decoration">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-header">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        <span class="stat-badge success">+43%</span>
                    </div>
                    <p class="stat-label">Total Pemasukan Bulan Ini</p>
                    <h2 class="stat-value">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h2>
                </div>

                <div class="stat-card stat-danger">
                    <div class="stat-header">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                        <span class="stat-badge danger">+25%</span>
                    </div>
                    <p class="stat-label">Total Pengeluaran Bulan Ini</p>
                    <h2 class="stat-value">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h2>
                </div>
            </div>

            <!-- Category Sections -->
            <div class="category-grid">
                <!-- Income Categories -->
                <div class="category-section">
                    <div class="section-header">
                        <div class="section-title-group">
                            <i class="fa-solid fa-circle-dot income-dot"></i>
                            <h3>Kategori Pemasukan</h3>
                        </div>
                        <div class="section-actions">
                            <div class="date-filter-group">
                                <select class="filter-select" id="periodTypeIncome" onchange="updateValueOptions()">
                                    <option value="hari" <?php echo $period == 'hari' ? 'selected' : ''; ?>>Hari</option>
                                    <option value="minggu" <?php echo $period == 'minggu' ? 'selected' : ''; ?>>Minggu</option>
                                    <option value="bulan" <?php echo $period == 'bulan' ? 'selected' : ''; ?>>Bulan</option>
                                    <option value="tahun" <?php echo $period == 'tahun' ? 'selected' : ''; ?>>Tahun</option>
                                    <option value="semua" <?php echo $period == 'semua' ? 'selected' : ''; ?>>Semua</option>
                                </select>
                                <select class="filter-select" id="valueSelectIncome" onchange="applyFilter()" style="<?php echo $period == 'semua' ? 'display:none;' : ''; ?>">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <button class="btn-add-category" onclick="openKategoriModal('Pemasukan')">
                                <i class="fa-solid fa-plus"></i>
                                Tambah Kategori
                            </button>
                        </div>
                    </div>

                    <div class="category-cards">
                        <?php if(empty($statistik_kategori)): ?>
                            <p style="color: #94a3b8; text-align: center; padding: 20px;">Belum ada kategori pemasukan</p>
                        <?php else: ?>
                            <?php foreach($statistik_kategori as $stat): ?>
                                <?php if($stat['jenisTransaksi'] == 'Pemasukan'): ?>
                                    <div class="expense-card" onclick="showKategoriDetail('<?php echo htmlspecialchars($stat['kategoriTransaksi']); ?>', 'Pemasukan')" style="cursor: pointer;">
                                        <div class="expense-header">
                                            <div class="expense-icon-wrapper" style="background: #d1fae5;">
                                                <i class="fa-solid fa-arrow-down" style="color: #10b981;"></i>
                                            </div>
                                            <div class="expense-info">
                                                <h4><?php echo htmlspecialchars($stat['kategoriTransaksi']); ?></h4>
                                            </div>
                                        </div>
                                        <div class="expense-amounts">
                                            <span class="amount-label">Rp <?php echo number_format($stat['total'], 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Expense Categories -->
                <div class="category-section">
                    <div class="section-header">
                        <div class="section-title-group">
                            <i class="fa-solid fa-circle-dot expense-dot"></i>
                            <h3>Kategori Pengeluaran</h3>
                        </div>
                        <div class="section-actions">
                            <div class="date-filter-group">
                                <select class="filter-select" id="periodTypeExpense" onchange="updateValueOptions()">
                                    <option value="hari" <?php echo $period == 'hari' ? 'selected' : ''; ?>>Hari</option>
                                    <option value="minggu" <?php echo $period == 'minggu' ? 'selected' : ''; ?>>Minggu</option>
                                    <option value="bulan" <?php echo $period == 'bulan' ? 'selected' : ''; ?>>Bulan</option>
                                    <option value="tahun" <?php echo $period == 'tahun' ? 'selected' : ''; ?>>Tahun</option>
                                    <option value="semua" <?php echo $period == 'semua' ? 'selected' : ''; ?>>Semua</option>
                                </select>
                                <select class="filter-select" id="valueSelectExpense" onchange="applyFilter()" style="<?php echo $period == 'semua' ? 'display:none;' : ''; ?>">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <button class="btn-add-category" onclick="openKategoriModal('Pengeluaran')">
                                <i class="fa-solid fa-plus"></i>
                                Tambah Kategori
                            </button>
                        </div>
                    </div>

                    <div class="expense-grid">
                        <?php if(empty($statistik_kategori)): ?>
                            <p style="color: #94a3b8; text-align: center; padding: 20px; grid-column: 1/-1;">Belum ada kategori pengeluaran</p>
                        <?php else: ?>
                            <?php foreach($statistik_kategori as $stat): ?>
                                <?php if($stat['jenisTransaksi'] == 'Pengeluaran'): ?>
                                    <div class="expense-card" onclick="showKategoriDetail('<?php echo htmlspecialchars($stat['kategoriTransaksi']); ?>', 'Pengeluaran')" style="cursor: pointer;">
                                        <div class="expense-header">
                                            <div class="expense-icon-wrapper" style="background: #fee2e2;">
                                                <i class="fa-solid fa-arrow-up" style="color: #ef4444;"></i>
                                            </div>
                                            <div class="expense-info">
                                                <h4><?php echo htmlspecialchars($stat['kategoriTransaksi']); ?></h4>
                                            </div>
                                        </div>
                                        <div class="expense-amounts">
                                            <span class="amount-label">Rp <?php echo number_format($stat['total'], 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Bottom Grid -->
            <div class="bottom-grid">
                <!-- Transaction History -->
                <div class="transaction-section">
                    <div class="section-header">
                        <h3>Riwayat Transaksi</h3>
                        <button class="btn-view-more">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                    </div>

                    <?php if (empty($riwayat_transaksi)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-receipt" style="font-size: 48px; color: #e5e7eb; margin-bottom: 10px;"></i>
                            <p>Belum ada transaksi</p>
                        </div>
                    <?php else: ?>
                        <?php
                        // Group transactions by month
                        $grouped_transactions = [];
                        foreach ($riwayat_transaksi as $trans) {
                            $month_year = date('F Y', strtotime($trans['tanggalKeuangan']));
                            if (!isset($grouped_transactions[$month_year])) {
                                $grouped_transactions[$month_year] = [];
                            }
                            $grouped_transactions[$month_year][] = $trans;
                        }
                        ?>

                        <?php foreach ($grouped_transactions as $month => $transactions): ?>
                            <div class="month-group">
                                <p class="month-label"><?php echo $month; ?></p>
                                <div class="transaction-list">
                                    <?php foreach ($transactions as $trans): ?>
                                        <?php 
                                        $is_income = $trans['jenisTransaksi'] == 'Pemasukan';
                                        $icon_class = $is_income ? 'success' : 'danger';
                                        $icon = $is_income ? 'fa-check' : 'fa-arrow-up';
                                        $amount_prefix = $is_income ? '+' : '-';
                                        ?>
                                        <div class="transaction-item">
                                            <div class="transaction-icon <?php echo $icon_class; ?>">
                                                <i class="fa-solid <?php echo $icon; ?>"></i>
                                            </div>
                                            <div class="transaction-details">
                                                <h4><?php echo htmlspecialchars($trans['keteranganTransaksi']); ?></h4>
                                                <p><?php echo htmlspecialchars($trans['kategoriTransaksi']); ?></p>
                                            </div>
                                            <div class="transaction-amount-wrapper">
                                                <p class="transaction-amount <?php echo $icon_class; ?>"><?php echo $amount_prefix; ?>Rp <?php echo number_format($trans['transaksi'], 0, ',', '.'); ?></p>
                                                <p class="transaction-date"><?php echo date('d M Y', strtotime($trans['tanggalKeuangan'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button class="btn-view-all" onclick="window.location.href='riwayat_transaksi.php'">Lihat Semua Riwayat</button>
                    <?php endif; ?>
                </div>

                <!-- Analytics Sidebar -->
                <div class="analytics-sidebar">
                    <!-- Chart Card -->
                    <div class="chart-card">
                        <h3>Analisa Bulan Ini</h3>
                        <p class="chart-date"><?php echo date('F Y'); ?></p>
                        <?php
                        $total_monthly = $monthly_analysis['pemasukan'] + $monthly_analysis['pengeluaran'];
                        $income_percentage = $total_monthly > 0 ? ($monthly_analysis['pemasukan'] / $total_monthly) * 100 : 50;
                        $circumference = 2 * 3.14159 * 80; // 2πr
                        $income_dash = ($income_percentage / 100) * $circumference;
                        $expense_dash = $circumference - $income_dash;
                        ?>
                        <div class="chart-wrapper">
                            <div class="pie-chart">
                                <svg viewBox="0 0 200 200">
                                    <circle cx="100" cy="100" r="80" fill="none" stroke="#e5e7eb" stroke-width="40"/>
                                    <?php if ($monthly_analysis['pemasukan'] > 0): ?>
                                    <circle cx="100" cy="100" r="80" fill="none" stroke="#10b981" stroke-width="40"
                                            stroke-dasharray="<?php echo $income_dash; ?> <?php echo $expense_dash; ?>" 
                                            transform="rotate(-90 100 100)"/>
                                    <?php endif; ?>
                                    <?php if ($monthly_analysis['pengeluaran'] > 0): ?>
                                    <circle cx="100" cy="100" r="80" fill="none" stroke="#ef4444" stroke-width="40"
                                            stroke-dasharray="<?php echo ($monthly_analysis['pengeluaran'] / $total_monthly * $circumference); ?> <?php echo $circumference; ?>" 
                                            stroke-dashoffset="<?php echo -$income_dash; ?>"
                                            transform="rotate(-90 100 100)"/>
                                    <?php endif; ?>
                                </svg>
                                <div class="chart-center">
                                    <p class="chart-label">Saldo Bulan Ini</p>
                                    <h3 class="chart-value">Rp <?php echo number_format($monthly_analysis['saldo'], 0, ',', '.'); ?></h3>
                                    <p class="chart-status"><?php echo $monthly_analysis['saldo'] >= 0 ? '✓ Surplus' : '✗ Defisit'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item">
                                <span class="legend-dot income"></span>
                                <span>Pemasukan</span>
                                <strong>Rp <?php echo number_format($monthly_analysis['pemasukan'], 0, ',', '.'); ?></strong>
                            </div>
                            <div class="legend-item">
                                <span class="legend-dot expense"></span>
                                <span>Pengeluaran</span>
                                <strong>Rp <?php echo number_format($monthly_analysis['pengeluaran'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Card -->
                    <div class="subscription-card">
                        <h3>Total Langganan Bulanan Aplikasi</h3>
                        <h2 class="subscription-total">Rp 200.000</h2>
                        
                        <div class="subscription-list">
                            <div class="subscription-item">
                                <div class="subscription-icon danger">
                                    <i class="fa-brands fa-youtube"></i>
                                </div>
                                <div class="subscription-info">
                                    <p>Pengeluaran Khusus</p>
                                    <p class="subscription-price">Rp 50.000/bulan</p>
                                </div>
                            </div>

                            <div class="subscription-item">
                                <div class="subscription-icon netflix">
                                    <span>N</span>
                                </div>
                                <div class="subscription-info">
                                    <p>Netflix</p>
                                    <p class="subscription-price">Rp 50.000/bulan</p>
                                </div>
                            </div>

                            <div class="subscription-item">
                                <div class="subscription-icon apple">
                                    <i class="fa-brands fa-apple"></i>
                                </div>
                                <div class="subscription-info">
                                    <p>Apple Music</p>
                                    <p class="subscription-price">Rp 50.000/bulan</p>
                                </div>
                            </div>

                            <div class="subscription-item">
                                <div class="subscription-icon spotify">
                                    <i class="fa-brands fa-spotify"></i>
                                </div>
                                <div class="subscription-info">
                                    <p>Spotify</p>
                                    <p class="subscription-price">Rp 50.000/bulan</p>
                                </div>
                            </div>
                        </div>

                        <button class="btn-view-details">
                            <i class="fa-solid fa-plus"></i>
                            Tambah Langganan
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Transaksi -->
    <div id="modalTransaksi" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Transaksi</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-group">
                    <label for="jenisTransaksi">Jenis Transaksi</label>
                    <select name="jenisTransaksi" id="jenisTransaksi" required onchange="updateKategoriOptions()">
                        <option value="">Pilih Jenis</option>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kategoriTransaksi">Kategori</label>
                    <select name="kategoriTransaksi" id="kategoriTransaksi" required>
                        <option value="">Pilih kategori terlebih dahulu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="keteranganTransaksi">Keterangan</label>
                    <input type="text" name="keteranganTransaksi" id="keteranganTransaksi" placeholder="Contoh: Gaji bulanan, Makan siang, dll" required>
                </div>

                <div class="form-group">
                    <label for="transaksi">Jumlah (Rp)</label>
                    <input type="number" name="transaksi" id="transaksi" placeholder="0" min="0" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div id="modalKategori" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Kategori <span id="jenisKategoriText"></span></h2>
                <span class="close" onclick="closeKategoriModal()">&times;</span>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="tambah_kategori">
                <input type="hidden" name="jenisTransaksi" id="jenisKategoriInput">
                
                <div class="form-group">
                    <label for="namaKategori">Nama Kategori</label>
                    <input type="text" name="namaKategori" id="namaKategori" placeholder="Contoh: Transportasi, Gaji, dll" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeKategoriModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Kategori -->
    <div id="modalKategoriDetail" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2 id="kategoriDetailTitle">Detail Kategori</h2>
                <span class="close" onclick="closeKategoriDetailModal()">&times;</span>
            </div>
            
            <div id="kategoriDetailContent" style="max-height: 400px; overflow-y: auto;">
                <p style="text-align: center; color: #94a3b8; padding: 20px;">Memuat data...</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeKategoriDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Data kategori dari PHP
        const kategoriPemasukan = <?php echo json_encode($kategori_pemasukan); ?>;
        const kategoriPengeluaran = <?php echo json_encode($kategori_pengeluaran); ?>;
        
        function openModal() {
            document.getElementById('modalTransaksi').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modalTransaksi').style.display = 'none';
        }

        function openKategoriModal(jenis) {
            document.getElementById('modalKategori').style.display = 'flex';
            document.getElementById('jenisKategoriInput').value = jenis;
        
        function showKategoriDetail(kategori, jenis) {
            const modal = document.getElementById('modalKategoriDetail');
            const title = document.getElementById('kategoriDetailTitle');
            const content = document.getElementById('kategoriDetailContent');
            
            title.textContent = 'Transaksi - ' + kategori;
            content.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">Memuat data...</p>';
            modal.style.display = 'flex';
            
            // Fetch transactions for this category
            const period = '<?php echo $period; ?>';
            const value = '<?php echo $value; ?>';
            
            fetch('get_kategori_detail.php?kategori=' + encodeURIComponent(kategori) + '&period=' + period + '&value=' + value)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada transaksi</p>';
                        return;
                    }
                    
                    let html = '<div class="transaction-list" style="display: flex; flex-direction: column; gap: 12px;">';
                    data.forEach(trans => {
                        const isIncome = trans.jenisTransaksi === 'Pemasukan';
                        const iconClass = isIncome ? 'success' : 'danger';
                        const icon = isIncome ? 'fa-check' : 'fa-arrow-up';
                        const prefix = isIncome ? '+' : '-';
                        
                        html += `
                            <div class="transaction-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                                <div class="transaction-icon ${iconClass}" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa-solid ${icon}"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 4px 0; font-size: 14px;">${trans.keteranganTransaksi}</h4>
                                    <p style="margin: 0; font-size: 12px; color: #64748b;">${new Date(trans.tanggalKeuangan).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</p>
                                </div>
                                <div style="text-align: right;">
                                    <p class="transaction-amount ${iconClass}" style="margin: 0; font-weight: 600;">${prefix}Rp ${Number(trans.transaksi).toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 20px;">Gagal memuat data</p>';
                });
        }
        
        function closeKategoriDetailModal() {
            document.getElementById('modalKategoriDetail').style.display = 'none';
        }
            document.getElementById('jenisKategoriText').textContent = jenis;
        }

        function closeKategoriModal() {
            document.getElementById('modalKategori').style.display = 'none';
            document.getElementById('namaKategori').value = '';
        }

        function updateKategoriOptions() {
            const jenisTransaksi = document.getElementById('jenisTransaksi').value;
            const kategoriSelect = document.getElementById('kategoriTransaksi');
            
            // Clear existing options
            kategoriSelect.innerHTML = '<option value="">Pilih kategori</option>';
            
            if(!jenisTransaksi) {
                kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
                return;
            }
            
            // Add categories based on transaction type
            let categories = jenisTransaksi === 'Pemasukan' ? kategoriPemasukan : kategoriPengeluaran;
            
            if(categories.length === 0) {
                kategoriSelect.innerHTML = '<option value="">Belum ada kategori, ketik manual</option>';
                // Allow typing custom category
                const input = document.createElement('option');
                input.value = 'custom';
                input.textContent = '+ Tambah kategori baru';
                kategoriSelect.appendChild(input);
            } else {
                categories.forEach(kategori => {
                    const option = document.createElement('option');
                    option.value = kategori;
                    option.textContent = kategori;
                    kategoriSelect.appendChild(option);
                });
            }
        }

        // Current filter values from PHP
        const currentPeriod = '<?php echo $period; ?>';
        const currentValue = '<?php echo $value; ?>';

        function updateValueOptions() {
            const periodIncome = document.getElementById('periodTypeIncome').value;
            const periodExpense = document.getElementById('periodTypeExpense').value;
            const period = periodIncome || periodExpense;
            
            const valueSelectIncome = document.getElementById('valueSelectIncome');
            const valueSelectExpense = document.getElementById('valueSelectExpense');
            
            // Hide value select if "semua" is selected
            if(period === 'semua') {
                valueSelectIncome.style.display = 'none';
                valueSelectExpense.style.display = 'none';
                // Only apply filter if period changed from current
                if(period !== currentPeriod) {
                    applyFilter();
                }
                return;
            } else {
                valueSelectIncome.style.display = 'inline-block';
                valueSelectExpense.style.display = 'inline-block';
            }
            
            let options = '';
            
            switch(period) {
                case 'hari':
                    options = '<option value="0">Hari Ini</option>';
                    options += '<option value="1">Kemarin</option>';
                    for(let i = 2; i <= 30; i++) {
                        options += `<option value="${i}">${i} Hari Lalu</option>`;
                    }
                    break;
                
                case 'minggu':
                    options = '<option value="0">Minggu Ini</option>';
                    options += '<option value="1">Minggu Lalu</option>';
                    for(let i = 2; i <= 12; i++) {
                        options += `<option value="${i}">${i} Minggu Lalu</option>`;
                    }
                    break;
                
                case 'bulan':
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const currentDate = new Date();
                    for(let i = 0; i < 24; i++) {
                        const d = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
                        const monthName = months[d.getMonth()];
                        const year = d.getFullYear();
                        options += `<option value="${i}">${monthName} ${year}</option>`;
                    }
                    break;
                
                case 'tahun':
                    options = '<option value="0">Tahun Ini</option>';
                    options += '<option value="1">Tahun Lalu</option>';
                    const currentYear = new Date().getFullYear();
                    for(let i = 2; i <= 10; i++) {
                        const year = currentYear - i;
                        options += `<option value="${i}">${year}</option>`;
                    }
                    break;
            }
            
            valueSelectIncome.innerHTML = options;
            valueSelectExpense.innerHTML = options;
            
            // Set current value if matches current period
            if(period === currentPeriod) {
                valueSelectIncome.value = currentValue;
                valueSelectExpense.value = currentValue;
            }
        }

        function applyFilter() {
            const period = document.getElementById('periodTypeIncome').value || document.getElementById('periodTypeExpense').value;
            const value = document.getElementById('valueSelectIncome').value || document.getElementById('valueSelectExpense').value || '0';
            
            window.location.href = '?period=' + period + '&value=' + value;
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateValueOptions();
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modalTransaksi = document.getElementById('modalTransaksi');
            const modalKategori = document.getElementById('modalKategori');
            if (event.target == modalTransaksi) {
                closeModal();
            } else if (event.target == modalKategori) {
                closeKategoriModal();
            }
        }

        // Auto hide alert after 3 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>