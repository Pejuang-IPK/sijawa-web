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

// Get date filter
$period = isset($_GET['period']) ? $_GET['period'] : 'semua';
$value = isset($_GET['value']) ? $_GET['value'] : ($period === 'semua' ? '' : '0');
$filter_label = 'Semua Waktu';
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

// Get all transactions with filter
$riwayat_transaksi = getTransaksiWithFilter($id_mahasiswa, $date_condition);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - SIJAWA</title>
    <link rel="stylesheet" href="style/keuangan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="https://via.placeholder.com/40" alt="Logo">
            </div>
            
            <nav class="nav-menu">
                <a href="keuangan.php" class="nav-item">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="riwayat_transaksi.php" class="nav-item active">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-chart-simple"></i>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="#" class="nav-item profile">
                    <i class="fa-solid fa-user"></i>
                </a>
                <a href="logout.php" class="nav-item logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <button class="btn-back" onclick="window.location.href='keuangan.php'">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1>Riwayat Transaksi</h1>
                        <p class="header-subtitle">Semua transaksi keuangan Anda</p>
                    </div>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <div class="user-info">
                            <p class="user-name"><?php echo htmlspecialchars($nama_mahasiswa); ?></p>
                            <p class="user-role"><?php echo htmlspecialchars($email_mahasiswa); ?></p>
                        </div>
                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Filter Section -->
            <div class="filter-section" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <label style="font-weight: 500; color: #334155;">Filter Periode:</label>
                    <select class="filter-select" id="periodType" onchange="updateValueOptions()" style="min-width: 150px;">
                        <option value="hari" <?php echo $period == 'hari' ? 'selected' : ''; ?>>Hari</option>
                        <option value="minggu" <?php echo $period == 'minggu' ? 'selected' : ''; ?>>Minggu</option>
                        <option value="bulan" <?php echo $period == 'bulan' ? 'selected' : ''; ?>>Bulan</option>
                        <option value="tahun" <?php echo $period == 'tahun' ? 'selected' : ''; ?>>Tahun</option>
                        <option value="semua" <?php echo $period == 'semua' ? 'selected' : ''; ?>>Semua</option>
                    </select>
                    <select class="filter-select" id="valueSelect" onchange="applyFilter()" style="min-width: 180px; <?php echo $period == 'semua' ? 'display:none;' : ''; ?>">
                        <option value="">Loading...</option>
                    </select>
                    <span style="color: #64748b; font-size: 14px; margin-left: auto;">
                        <i class="fa-solid fa-filter"></i> <?php echo $filter_label; ?>
                    </span>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="transactions-container" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <?php if (empty($riwayat_transaksi)): ?>
                    <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                        <i class="fa-solid fa-receipt" style="font-size: 64px; color: #e5e7eb; margin-bottom: 16px;"></i>
                        <h3 style="color: #64748b; margin: 0 0 8px 0;">Belum ada transaksi</h3>
                        <p style="color: #94a3b8; margin: 0;">Transaksi Anda akan muncul di sini</p>
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
                        <div class="month-group" style="margin-bottom: 32px;">
                            <p class="month-label" style="color: #64748b; font-weight: 500; margin-bottom: 16px; font-size: 14px;">
                                <?php echo $month; ?>
                            </p>
                            <div class="transaction-list" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($transactions as $trans): ?>
                                    <?php 
                                    $is_income = $trans['jenisTransaksi'] == 'Pemasukan';
                                    $icon_class = $is_income ? 'success' : 'danger';
                                    $icon = $is_income ? 'fa-check' : 'fa-arrow-up';
                                    $amount_prefix = $is_income ? '+' : '-';
                                    ?>
                                    <div class="transaction-item" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px; transition: all 0.3s;">
                                        <div class="transaction-icon <?php echo $icon_class; ?>" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; flex-shrink: 0;">
                                            <i class="fa-solid <?php echo $icon; ?>"></i>
                                        </div>
                                        <div class="transaction-details" style="flex: 1; min-width: 0;">
                                            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($trans['keteranganTransaksi']); ?></h4>
                                            <p style="margin: 0; font-size: 13px; color: #64748b;"><?php echo htmlspecialchars($trans['kategoriTransaksi']); ?></p>
                                        </div>
                                        <div class="transaction-amount-wrapper" style="text-align: right;">
                                            <p class="transaction-amount <?php echo $icon_class; ?>" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">
                                                <?php echo $amount_prefix; ?>Rp <?php echo number_format($trans['transaksi'], 0, ',', '.'); ?>
                                            </p>
                                            <p class="transaction-date" style="margin: 0; font-size: 12px; color: #94a3b8;">
                                                <?php echo date('d M Y', strtotime($trans['tanggalKeuangan'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Summary -->
                    <div style="margin-top: 32px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                        <p style="text-align: center; color: #64748b; font-size: 14px;">
                            Total <?php echo count($riwayat_transaksi); ?> transaksi ditampilkan
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Current filter values from PHP
        const currentPeriod = '<?php echo $period; ?>';
        const currentValue = '<?php echo $value; ?>';

        function updateValueOptions() {
            const period = document.getElementById('periodType').value;
            const valueSelect = document.getElementById('valueSelect');
            
            // Hide value select if "semua" is selected
            if(period === 'semua') {
                valueSelect.style.display = 'none';
                // Only apply filter if period changed from current
                if(period !== currentPeriod) {
                    applyFilter();
                }
                return;
            } else {
                valueSelect.style.display = 'inline-block';
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
            
            valueSelect.innerHTML = options;
            
            // Set current value if matches current period
            if(period === currentPeriod) {
                valueSelect.value = currentValue;
            }
        }

        function applyFilter() {
            const period = document.getElementById('periodType').value;
            const value = document.getElementById('valueSelect').value || '0';
            
            window.location.href = '?period=' + period + '&value=' + value;
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateValueOptions();
        });
    </script>
</body>
</html>
