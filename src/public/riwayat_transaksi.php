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

// Get all history page data from controller
$history = getHistoryPageData($id_mahasiswa);

// Extract variables for easier access in template
extract($history);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - SIJAWA</title>
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
            <header class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0 0 4px 0;">Semua Riwayat Transaksi</h1>
                </div>
                <button class="btn-kembali" onclick="window.location.href='keuangan.php'" style="background: transparent; border: none; color: #64748b; font-size: 16px; cursor: pointer; padding: 8px 16px; border-radius: 8px; transition: all 0.3s; font-family: 'Poppins', sans-serif;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b';" onmouseout="this.style.background='transparent'; this.style.color='#64748b';">
                    Kembali
                </button>
            </header>

            <!-- Search Box -->
            <div class="search-box" style="background: white; padding: 16px 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" id="searchInput" placeholder="Cari Transaksi..." onkeyup="cariTransaksi('searchInput', 'transaction-item', 'month-group')" style="width: 100%; padding: 12px 12px 12px 44px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: 'Poppins', sans-serif; transition: all 0.3s;" onfocus="this.style.borderColor='#3b82f6'; this.style.outline='none';" onblur="this.style.borderColor='#e2e8f0';">
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <label style="font-weight: 500; color: #334155;">Filter Periode:</label>
                    <select class="filter-select" id="periodType" onchange="updateFilterRiwayat()" style="min-width: 150px;">
                        <option value="hari" <?php echo $period == 'hari' ? 'selected' : ''; ?>>Hari</option>
                        <option value="minggu" <?php echo $period == 'minggu' ? 'selected' : ''; ?>>Minggu</option>
                        <option value="bulan" <?php echo $period == 'bulan' ? 'selected' : ''; ?>>Bulan</option>
                        <option value="tahun" <?php echo $period == 'tahun' ? 'selected' : ''; ?>>Tahun</option>
                        <option value="semua" <?php echo $period == 'semua' ? 'selected' : ''; ?>>Semua</option>
                    </select>
                    <select class="filter-select" id="valueSelect" onchange="terapkanFilterRiwayat()" style="min-width: 180px; <?php echo $period == 'semua' ? 'display:none;' : ''; ?>">
                        <option value="">Memuat...</option>
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
                                    $icon = $is_income ? 'fa-arrow-down-long' : 'fa-arrow-up-long';
                                    $amount_prefix = $is_income ? '+' : '-';
                                    ?>
                                    <div class="transaction-item" onclick="bukaModalDetailTransaksi('<?php echo $trans['id_keuangan']; ?>')" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px; transition: all 0.3s; cursor: pointer;">
                                        <div class="transaction-icon <?php echo $icon_class; ?>" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; flex-shrink: 0;">
                                            <i class="fa-solid <?php echo $icon; ?>" style="transform: rotate(45deg);"></i>
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
        // Data kategori dari PHP
        window.kategoriPemasukan = <?php echo json_encode($kategori_pemasukan); ?>;
        window.kategoriPengeluaran = <?php echo json_encode($kategori_pengeluaran); ?>;
        window.currentPeriod = '<?php echo $period; ?>';
        window.currentValue = '<?php echo $value; ?>';
        
        // Fungsi update filter untuk riwayat transaksi
        function updateFilterRiwayat() {
            const period = document.getElementById('periodType').value;
            const valueSelect = document.getElementById('valueSelect');
            
            if(period === 'semua') {
                valueSelect.style.display = 'none';
                if(period !== window.currentPeriod) {
                    terapkanFilterRiwayat();
                }
                return;
            } else {
                valueSelect.style.display = 'inline-block';
            }
            
            // Generate options
            const options = UtilFilter.buatOpsi(period);
            valueSelect.innerHTML = options;
            
            // Set value jika period sama dengan current period
            if(period === window.currentPeriod) {
                valueSelect.value = window.currentValue;
            }
        }
        
        function terapkanFilterRiwayat() {
            const period = document.getElementById('periodType').value;
            const value = document.getElementById('valueSelect').value || '0';
            window.location.href = '?period=' + period + '&value=' + value;
        }
        
        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            updateFilterRiwayat();
        });
    </script>
    
    <script src="script/modal-helper.js"></script>
    <script src="script/filter.js"></script>
    <script src="script/transaction.js"></script>
</body>
</html>
