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
$pesan = $dashboard['pesan'];
$tipe_pesan = $dashboard['tipe_pesan'];
$period = $dashboard['period'];
$value = $dashboard['value'];
$filter_label = $dashboard['filter_label'];
$kategori_pemasukan = $dashboard['kategori_pemasukan'];
$kategori_pengeluaran = $dashboard['kategori_pengeluaran'];
$statistik_kategori = $dashboard['statistik_kategori'];
$riwayat_transaksi = $dashboard['riwayat_transaksi'];
$monthly_analysis = $dashboard['monthly_analysis'];
$total_pemasukan = $dashboard['total_pemasukan'];
$total_pengeluaran = $dashboard['total_pengeluaran'];
$total_saldo = $dashboard['total_saldo'];
$pemasukan_change = $dashboard['pemasukan_change'];
$pengeluaran_change = $dashboard['pengeluaran_change'];
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">S.</div>
            <nav class="side-nav">
                <a href="index.php" title="Beranda"><i class="fa-solid fa-house"></i></a>
                <a href="#" title="Tugas"><i class="fa-solid fa-list-check"></i></a>
                <a href="#" title="Kalender"><i class="fa-solid fa-calendar-days"></i></a>
                <a class="active" href="keuangan.php" title="Keuangan"><i class="fa-solid fa-wallet"></i></a>
                <a href="#" title="Setting"><i class="fa-solid fa-gear"></i></a>
            </nav>
            <div class="logout">
                <form action="logout.php" method="post">
                    <button type="submit" class="icon-btn" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></button>
                </form>
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

            <?php if($pesan): ?>
                <div class="alert alert-<?php echo $tipe_pesan; ?>">
                    <?php echo $pesan; ?>
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
                        <span class="stat-badge success"><?php echo ($pemasukan_change >= 0 ? '+' : '') . number_format($pemasukan_change, 0) . '%'; ?></span>
                    </div>
                    <p class="stat-label">Total Pemasukan Bulan Ini</p>
                    <h2 class="stat-value">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h2>
                </div>

                <div class="stat-card stat-danger">
                    <div class="stat-header">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                        <span class="stat-badge danger"><?php echo ($pengeluaran_change >= 0 ? '+' : '') . number_format($pengeluaran_change, 0) . '%'; ?></span>
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
                                    <option value="">Memuat...</option>
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
                                                <i class="fa-solid fa-arrow-down-long" style="color: #10b981; transform: rotate(45deg);"></i>
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
                                    <option value="">Memuat...</option>
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
                                                <i class="fa-solid fa-arrow-up-long" style="color: #ef4444; transform: rotate(45deg);"></i>
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
                                        $icon = $is_income ? 'fa-arrow-down-long' : 'fa-arrow-up-long';
                                        $icon_rotation = $is_income ? 'transform: rotate(45deg);' : 'transform: rotate(45deg);';
                                        $amount_prefix = $is_income ? '+' : '-';
                                        ?>
                                        <div class="transaction-item" onclick="openTransactionDetailModal('<?php echo $trans['id_keuangan']; ?>')" style="cursor: pointer;">
                                            <div class="transaction-icon <?php echo $icon_class; ?>">
                                                <i class="fa-solid <?php echo $icon; ?>" style="<?php echo $icon_rotation; ?>"></i>
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
                            <p class="chart-label">Saldo Bulan Ini</p>
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
                        <div class="subscription-header">
                            <h3>Total Langganan Bulanan Aplikasi</h3>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-charge-now" onclick="chargeNow()" title="Bayar Sekarang" style="background: #10b981; color: white; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; font-size: 14px;">
                                    <i class="fa-solid fa-dollar-sign"></i>
                                </button>
                                <button class="btn-add-subscription" onclick="openSubscriptionModal()">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <h2 class="subscription-total" id="totalLangganan">Rp 0</h2>
                        
                        <div class="subscription-list" id="subscriptionList">
                            <div class="empty-state" style="text-align: center; padding: 20px; color: #94a3b8;">
                                <i class="fa-solid fa-calendar-check" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i>
                                <p style="margin: 0; font-size: 13px;">Belum ada langganan</p>
                            </div>
                        </div>
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
            
            <form method="POST" action="" onsubmit="prepareFormSubmit(event)">
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
                    <input type="text" name="transaksi_display" id="transaksi" placeholder="0" required oninput="formatCurrency(this)">
                    <input type="hidden" name="transaksi" id="transaksi_raw">
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
        // Format currency input
        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value === '') {
                input.value = '';
                return;
            }
            let formatted = parseInt(value).toLocaleString('id-ID');
            input.value = formatted;
            
            // Store raw value in hidden field if exists
            const rawInput = document.getElementById(input.id + '_raw');
            if (rawInput) {
                rawInput.value = value;
            }
        }
        
        // Prepare form before submit - ensure raw value is set
        function prepareFormSubmit(event) {
            const displayInput = document.querySelector('input[name="transaksi_display"]');
            const rawInput = document.querySelector('input[name="transaksi"]');
            if (displayInput && rawInput) {
                rawInput.value = displayInput.value.replace(/[^0-9]/g, '');
            }
            return true;
        }
        
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
                        const icon = isIncome ? 'fa-arrow-down-long' : 'fa-arrow-up-long';
                        const iconRotation = isIncome ? 'transform: rotate(45deg);' : 'transform: rotate(45deg);';
                        const prefix = isIncome ? '+' : '-';
                        
                        html += `
                            <div class="transaction-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                                <div class="transaction-icon ${iconClass}" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa-solid ${icon}" style="${iconRotation}"></i>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin: 0 0 4px 0; font-size: 14px;">${trans.keteranganTransaksi}</h4>
                                    <p style="margin: 0; font-size: 12px; color: #64748b;">${new Date(trans.tanggalKeuangan).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</p>
                                </div>
                                <div style="text-align: right; display: flex; align-items: center; gap: 8px;">
                                    <p class="transaction-amount ${iconClass}" style="margin: 0; font-weight: 600; margin-right: 12px;">${prefix}Rp ${Number(trans.transaksi).toLocaleString('id-ID')}</p>
                                    <button onclick="openEditTransaksiModal('${trans.id_keuangan}')" class="btn-edit-transaksi" style="background: #3b82f6; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button onclick="deleteTransaksi('${trans.id_keuangan}')" class="btn-delete-transaksi" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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
            loadSubscriptions();
        });

        // === SUBSCRIPTION FUNCTIONS ===
        
        function loadSubscriptions() {
            console.log('Loading subscriptions...');
            fetch('keuangan.php?api=langganan')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Subscription data:', data);
                    if (data.success) {
                        displaySubscriptions(data.data, data.total);
                    } else {
                        console.error('API returned error:', data.message);
                    }
                })
                .catch(error => console.error('Kesalahan memuat langganan:', error));
        }

        function displaySubscriptions(subscriptions, total) {
            console.log('Menampilkan langganan:', subscriptions, 'Total:', total);
            const listContainer = document.getElementById('subscriptionList');
            const totalElement = document.getElementById('totalLangganan');
            
            if (!listContainer || !totalElement) {
                console.error('Elemen tidak ditemukan! subscriptionList:', listContainer, 'totalLangganan:', totalElement);
                return;
            }
            
            totalElement.textContent = 'Rp ' + parseInt(total || 0).toLocaleString('id-ID');
            
            if (subscriptions.length === 0) {
                listContainer.innerHTML = `
                    <div class="empty-state" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                        <i class="fa-solid fa-calendar-check" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p style="margin: 0;">Belum ada langganan</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            subscriptions.forEach(sub => {
                html += `
                    <div class="subscription-item" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px; margin-bottom: 12px;">
                        <div class="sub-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: white; font-size: 24px;">
                            <i class="fa-brands ${sub.icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #1e293b;">${sub.nama_langganan}</h4>
                            <p style="margin: 0; font-size: 13px; color: #3b82f6;">Rp ${parseInt(sub.harga_bulanan).toLocaleString('id-ID')}/bulan</p>
                        </div>
                        <button onclick="deleteSubscription('${sub.id_langganan}')" class="btn-delete-sub" style="background: #fee2e2; color: #ef4444; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;
            });
            
            listContainer.innerHTML = html;
        }

        function openSubscriptionModal() {
            // Remove any existing modal first
            const existing = document.querySelector('.modal-overlay');
            if (existing) existing.remove();
            
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.style.cssText = 'display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;';
            
            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';
            modalContent.style.cssText = 'max-width: 520px; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; z-index: 1001;';
            
            modalContent.innerHTML = `
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 24px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h2 style="color: white; margin: 0 0 4px 0; font-size: 20px;">Tambah Langganan Baru</h2>
                        <p style="font-size: 13px; opacity: 0.9; margin: 0;">Kelola langganan aplikasi bulanan Anda</p>
                    </div>
                    <button type="button" id="btnCloseModal" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 32px; height: 32px;">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <form id="subscriptionForm" style="padding: 28px;">
                    <div style="margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                            <i class="fa-solid fa-tag" style="color: #667eea;"></i>
                            <span>Nama Langganan</span>
                        </label>
                        <input type="text" name="nama_langganan" id="inputNama" required placeholder="Contoh: Netflix, Spotify, YouTube Premium" 
                               style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                            <i class="fa-solid fa-icons" style="color: #667eea;"></i>
                            <span>Pilih Icon</span>
                        </label>
                        <select name="icon" id="inputIcon" required style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; cursor: pointer; box-sizing: border-box;">
                            <option value="fa-youtube">YouTube Premium</option>
                            <option value="fa-netflix">Netflix</option>
                            <option value="fa-spotify">Spotify</option>
                            <option value="fa-apple">Apple Music</option>
                            <option value="fa-google">Google One</option>
                            <option value="fa-microsoft">Microsoft 365</option>
                            <option value="fa-amazon">Amazon Prime</option>
                            <option value="fa-discord">Discord Nitro</option>
                            <option value="fa-steam">Steam</option>
                            <option value="fa-playstation">PlayStation Plus</option>
                            <option value="fa-xbox">Xbox Game Pass</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 28px;">
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                            <i class="fa-solid fa-money-bill-wave" style="color: #667eea;"></i>
                            <span>Biaya per Bulan</span>
                        </label>
                        <input type="text" name="harga_bulanan" id="inputHarga" required placeholder="50.000" oninput="formatCurrency(this)"
                               style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
                        <input type="hidden" name="harga_bulanan_raw" id="inputHarga_raw">
                        <p style="font-size: 12px; color: #94a3b8; margin: 8px 0 0 0;">
                            <i class="fa-solid fa-info-circle"></i> Akan otomatis dipotong setiap tanggal 1
                        </p>
                    </div>
                    <div style="display: flex; gap: 12px; padding-top: 20px; margin-top: 20px; border-top: 1px solid #f1f5f9;">
                        <button type="button" id="btnBatal" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: #64748b; border: none; font-family: 'Poppins', sans-serif;">
                            <i class="fa-solid fa-times"></i> Batal
                        </button>
                        <button type="button" id="btnSimpan" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: pointer; border: none; color: white; font-family: 'Poppins', sans-serif; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                            <i class="fa-solid fa-check"></i> Simpan
                        </button>
                    </div>
                </form>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            // Event handlers
            document.getElementById('btnCloseModal').onclick = function() {
                modal.remove();
            };
            
            document.getElementById('btnBatal').onclick = function() {
                modal.remove();
            };
            
            document.getElementById('btnSimpan').onclick = function() {
                const nama = document.getElementById('inputNama').value.trim();
                const icon = document.getElementById('inputIcon').value;
                const harga = document.getElementById('inputHarga').value;
                
                console.log('Data yang akan dikirim:', { nama, icon, harga });
                
                if (!nama || !icon || !harga) {
                    window.alert('Mohon lengkapi semua field!');
                    return;
                }
                
                if (harga < 0) {
                    window.alert('Harga harus lebih dari 0!');
                    return;
                }
                
                // Disable button to prevent double click
                const btnSimpan = this;
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
                
                const formData = new FormData();
                formData.append('action', 'tambah');
                formData.append('nama_langganan', nama);
                formData.append('icon', icon);
                formData.append('harga_bulanan', harga);
                
                console.log('Mengirim ke server...');
                
                fetch('keuangan.php?api=langganan', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('Respons JSON:', data);
                        if (data.sukses) {
                            console.log('SUKSES! Menutup modal dan muat ulang data...');
                            modal.remove();
                            loadSubscriptions();
                            // Tampilkan pesan sukses setelah modal ditutup
                            setTimeout(() => {
                                window.alert('✓ Langganan berhasil ditambahkan!');
                            }, 100);
                        } else {
                            console.error('KESALAHAN:', data.pesan);
                            window.alert('Kesalahan: ' + data.pesan);
                            btnSimpan.disabled = false;
                            btnSimpan.innerHTML = '<i class="fa-solid fa-check"></i> Simpan';
                        }
                    } catch (e) {
                        console.error('Kesalahan Parse JSON:', e);
                        window.alert('Kesalahan: Respons bukan JSON - ' + text);
                        btnSimpan.disabled = false;
                        btnSimpan.innerHTML = '<i class="fa-solid fa-check"></i> Simpan';
                    }
                })
                .catch(error => {
                    console.error('Kesalahan Fetch:', error);
                    window.alert('Terjadi kesalahan koneksi: ' + error.message);
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = '<i class="fa-solid fa-check"></i> Simpan';
                });
            };
            
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            };
        }

        function updateIconPreview(iconClass) {
            // Not used in simplified version
        }

        function submitSubscription(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            // Get raw value from formatted input
            const hargaInput = document.getElementById('inputHarga');
            const hargaRaw = hargaInput.value.replace(/[^0-9]/g, '');
            
            formData.set('harga_bulanan', hargaRaw);
            formData.append('action', 'tambah');
            
            fetch('keuangan.php?api=langganan', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    alert('Langganan berhasil ditambahkan!');
                    closeSubscriptionModal(e.target);
                    loadSubscriptions();
                } else {
                    alert('Kesalahan: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                alert('Terjadi kesalahan saat menambahkan langganan');
            });
        }

        function deleteSubscription(id) {
            if (!window.confirm('Yakin ingin menghapus langganan ini?')) return;
            
            const formData = new FormData();
            formData.append('action', 'hapus');
            formData.append('id_langganan', id);
            
            fetch('keuangan.php?api=langganan', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    console.log('Langganan berhasil dihapus, muat ulang data...');
                    loadSubscriptions();
                    setTimeout(() => {
                        window.alert('✓ Langganan berhasil dihapus!');
                    }, 100);
                } else {
                    window.alert('Kesalahan: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                window.alert('Terjadi kesalahan saat menghapus langganan');
            });
        }

        function closeSubscriptionModal(element) {
            const modal = element.closest('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

        // Charge subscription now
        function chargeNow() {
            if (!window.confirm('Yakin ingin membayar tagihan langganan bulanan sekarang? Total akan masuk ke pengeluaran.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('api', 'charge');
            
            fetch('keuangan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    window.alert('✓ Tagihan langganan berhasil dibayar dan masuk ke pengeluaran!');
                    window.location.reload();
                } else {
                    window.alert('Kesalahan: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                window.alert('Terjadi kesalahan: ' + error.message);
            });
        }

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

        // Edit Transaksi Functions
        function openEditTransaksiModal(id_keuangan) {
            // Fetch transaction data
            fetch('keuangan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
            })
            .then(response => response.json())
            .then(result => {
                if (result.sukses) {
                    showEditModal(result.data);
                } else {
                    window.alert('Gagal mengambil data transaksi: ' + result.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                window.alert('Terjadi kesalahan saat mengambil data transaksi');
            });
        }

        function showEditModal(transaksi) {
            // Create modal overlay
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            modalOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';

            // Create modal content
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;';

            // Modal header
            const modalHeader = document.createElement('div');
            modalHeader.style.cssText = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;';
            
            const modalTitle = document.createElement('h3');
            modalTitle.textContent = 'Edit Transaksi';
            modalTitle.style.margin = '0';
            
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            closeBtn.style.cssText = 'background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px;';
            closeBtn.onclick = () => modalOverlay.remove();
            
            modalHeader.appendChild(modalTitle);
            modalHeader.appendChild(closeBtn);

            // Modal body
            const modalBody = document.createElement('div');
            modalBody.style.padding = '24px';
            
            // Form HTML
            modalBody.innerHTML = `
                <form id="formEditTransaksi">
                    <input type="hidden" id="editIdKeuangan" value="${transaksi.id_keuangan}">
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Jumlah</label>
                        <input type="number" id="editJumlah" value="${transaksi.transaksi}" required
                            style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Keterangan</label>
                        <input type="text" id="editKeterangan" value="${transaksi.keteranganTransaksi}" required
                            style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Jenis Transaksi</label>
                        <select id="editJenisTransaksi" required onchange="updateEditKategoriOptions()"
                            style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                            <option value="">Pilih jenis</option>
                            <option value="Pemasukan" ${transaksi.jenisTransaksi === 'Pemasukan' ? 'selected' : ''}>Pemasukan</option>
                            <option value="Pengeluaran" ${transaksi.jenisTransaksi === 'Pengeluaran' ? 'selected' : ''}>Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Kategori</label>
                        <select id="editKategoriTransaksi" required
                            style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                            <option value="">Pilih kategori</option>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="button" onclick="this.closest('.modal-overlay').remove()"
                            style="flex: 1; padding: 12px; background: #e2e8f0; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; color: #334155;">
                            Batal
                        </button>
                        <button type="button" id="btnUpdateTransaksi"
                            style="flex: 1; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; cursor: pointer; font-weight: 500; color: white;">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            `;
            
            // Assemble modal
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modalOverlay.appendChild(modalContent);
            document.body.appendChild(modalOverlay);
            
            // Populate kategori dropdown based on current jenis
            updateEditKategoriOptions();
            document.getElementById('editKategoriTransaksi').value = transaksi.kategoriTransaksi;
            
            // Add submit handler
            document.getElementById('btnUpdateTransaksi').onclick = function() {
                updateTransaksi();
            };
        }

        function updateEditKategoriOptions() {
            const jenisTransaksi = document.getElementById('editJenisTransaksi').value;
            const kategoriSelect = document.getElementById('editKategoriTransaksi');
            const currentKategori = kategoriSelect.value;
            
            kategoriSelect.innerHTML = '<option value="">Pilih kategori</option>';
            
            if(!jenisTransaksi) {
                kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
                return;
            }
            
            let categories = jenisTransaksi === 'Pemasukan' ? kategoriPemasukan : kategoriPengeluaran;
            
            if(categories.length === 0) {
                kategoriSelect.innerHTML = '<option value="">Belum ada kategori</option>';
            } else {
                categories.forEach(kategori => {
                    const option = document.createElement('option');
                    option.value = kategori;
                    option.textContent = kategori;
                    if (kategori === currentKategori) {
                        option.selected = true;
                    }
                    kategoriSelect.appendChild(option);
                });
            }
        }

        function updateTransaksi() {
            const btnUpdate = document.getElementById('btnUpdateTransaksi');
            btnUpdate.disabled = true;
            btnUpdate.textContent = 'Menyimpan...';
            
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'edit');
            formData.append('id_keuangan', document.getElementById('editIdKeuangan').value);
            formData.append('transaksi', document.getElementById('editJumlah').value);
            formData.append('keteranganTransaksi', document.getElementById('editKeterangan').value);
            formData.append('jenisTransaksi', document.getElementById('editJenisTransaksi').value);
            formData.append('kategoriTransaksi', document.getElementById('editKategoriTransaksi').value);
            
            fetch('keuangan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    document.querySelector('.modal-overlay').remove();
                    window.alert('✓ Transaksi berhasil diperbarui!');
                    window.location.reload();
                } else {
                    btnUpdate.disabled = false;
                    btnUpdate.textContent = 'Simpan Perubahan';
                    window.alert('Kesalahan: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                btnUpdate.disabled = false;
                btnUpdate.textContent = 'Simpan Perubahan';
                window.alert('Terjadi kesalahan saat mengupdate transaksi');
            });
        }

        function deleteTransaksi(id_keuangan) {
            if (!window.confirm('Yakin ingin menghapus transaksi ini?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'hapus');
            formData.append('id_keuangan', id_keuangan);
            
            fetch('keuangan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    window.alert('✓ Transaksi berhasil dihapus!');
                    window.location.reload();
                } else {
                    window.alert('Kesalahan: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                window.alert('Terjadi kesalahan saat menghapus transaksi');
            });
        }

        // Transaction Detail Modal
        function openTransactionDetailModal(id_keuangan) {
            console.log('Membuka detail transaksi:', id_keuangan);
            // Fetch transaction data
            fetch('keuangan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
            })
            .then(response => {
                console.log('Status respons:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Teks respons:', text);
                const result = JSON.parse(text);
                if (result.sukses) {
                    showTransactionDetailModal(result.data);
                } else {
                    window.alert('Gagal mengambil data transaksi: ' + result.pesan);
                }
            })
            .catch(error => {
                console.error('Kesalahan:', error);
                window.alert('Terjadi kesalahan saat mengambil data transaksi');
            });
        }

        function showTransactionDetailModal(transaksi) {
            const isIncome = transaksi.jenisTransaksi === 'Pemasukan';
            const amountColor = isIncome ? '#10b981' : '#ef4444';
            const icon = isIncome ? '↙' : '↗';
            const iconBg = isIncome ? '#d1fae5' : '#fee2e2';
            const iconColor = isIncome ? '#10b981' : '#ef4444';
            
            // Create modal overlay
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            modalOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
            
            // Format date
            const date = new Date(transaksi.tanggalKeuangan);
            const formattedDate = date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
            
            // Create modal content
            modalOverlay.innerHTML = `
                <div style="background: white; border-radius: 16px; padding: 32px; width: 90%; max-width: 400px; text-align: center; position: relative;">
                    <button onclick="this.closest('.modal-overlay').remove()" style="position: absolute; top: 16px; right: 16px; background: #f3f4f6; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px; color: #6b7280;">×</button>
                    
                    <div style="width: 64px; height: 64px; background: ${iconBg}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; font-weight: bold; color: ${iconColor};">
                        ${icon}
                    </div>
                    
                    <h3 style="margin: 0 0 8px 0; font-size: 20px; color: #1e293b;">${transaksi.keteranganTransaksi}</h3>
                    <p style="margin: 0 0 16px 0; font-size: 14px; color: #64748b;">${formattedDate}</p>
                    <p style="margin: 0 0 8px 0; font-size: 13px; color: #94a3b8;">${transaksi.kategoriTransaksi}</p>
                    <p style="margin: 0 0 32px 0; font-size: 28px; font-weight: 600; color: ${amountColor};">Rp ${parseInt(transaksi.transaksi).toLocaleString('id-ID')}</p>
                    
                    <div style="display: flex; gap: 12px;">
                        <button onclick="this.closest('.modal-overlay').remove(); openEditTransaksiModal('${transaksi.id_keuangan}');" style="flex: 1; padding: 12px; background: #3b82f6; border: none; border-radius: 8px; color: white; font-weight: 500; cursor: pointer; font-size: 15px;">
                            Edit
                        </button>
                        <button onclick="this.closest('.modal-overlay').remove(); deleteTransaksi('${transaksi.id_keuangan}');" style="flex: 1; padding: 12px; background: #ef4444; border: none; border-radius: 8px; color: white; font-weight: 500; cursor: pointer; font-size: 15px;">
                            Hapus
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modalOverlay);
        }

        // Sembunyikan alert otomatis setelah 3 detik
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