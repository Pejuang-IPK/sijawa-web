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

// Get all history page data from controller
$history = getHistoryPageData($id_mahasiswa);

// Extract variables for easier access in template
$period = $history['period'];
$value = $history['value'];
$filter_label = $history['filter_label'];
$riwayat_transaksi = $history['riwayat_transaksi'];
$kategori_pemasukan = $history['kategori_pemasukan'];
$kategori_pengeluaran = $history['kategori_pengeluaran'];
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
                    <input type="text" id="searchInput" placeholder="Cari Transaksi..." onkeyup="searchTransactions()" style="width: 100%; padding: 12px 12px 12px 44px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: 'Poppins', sans-serif; transition: all 0.3s;" onfocus="this.style.borderColor='#3b82f6'; this.style.outline='none';" onblur="this.style.borderColor='#e2e8f0';">
                </div>
            </div>

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
                                    $icon = $is_income ? 'fa-arrow-down-long' : 'fa-arrow-up-long';
                                    $icon_rotation = $is_income ? 'transform: rotate(45deg);' : 'transform: rotate(45deg);';
                                    $amount_prefix = $is_income ? '+' : '-';
                                    ?>
                                    <div class="transaction-item" onclick="openTransactionDetailModal('<?php echo $trans['id_keuangan']; ?>')" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px; transition: all 0.3s; cursor: pointer;">
                                        <div class="transaction-icon <?php echo $icon_class; ?>" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; flex-shrink: 0;">
                                            <i class="fa-solid <?php echo $icon; ?>" style="<?php echo $icon_rotation; ?>"></i>
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
        // Search function
        function searchTransactions() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const transactionItems = document.querySelectorAll('.transaction-item');
            const monthGroups = document.querySelectorAll('.month-group');
            
            transactionItems.forEach(item => {
                const keterangan = item.querySelector('.transaction-details h4').textContent.toLowerCase();
                const kategori = item.querySelector('.transaction-details p').textContent.toLowerCase();
                const amount = item.querySelector('.transaction-amount').textContent.toLowerCase();
                
                if (keterangan.includes(searchTerm) || kategori.includes(searchTerm) || amount.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide month groups if all transactions are hidden
            monthGroups.forEach(group => {
                const visibleTransactions = group.querySelectorAll('.transaction-item[style*="display: flex"]');
                if (visibleTransactions.length === 0) {
                    group.style.display = 'none';
                } else {
                    group.style.display = 'block';
                }
            });
        }

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

        // Data kategori dari PHP (untuk edit modal)
        const kategoriPemasukan = <?php echo json_encode($kategori_pemasukan); ?>;
        const kategoriPengeluaran = <?php echo json_encode($kategori_pengeluaran); ?>;

        // Transaction Detail Modal
        function openTransactionDetailModal(id_keuangan) {
            fetch('keuangan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showTransactionDetailModal(result.data);
                } else {
                    window.alert('Gagal mengambil data transaksi: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.alert('Terjadi kesalahan saat mengambil data transaksi');
            });
        }

        function showTransactionDetailModal(transaksi) {
            const isIncome = transaksi.jenisTransaksi === 'Pemasukan';
            const amountColor = isIncome ? '#10b981' : '#ef4444';
            const icon = isIncome ? '↙' : '↗';
            const iconBg = isIncome ? '#d1fae5' : '#fee2e2';
            const iconColor = isIncome ? '#10b981' : '#ef4444';
            
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            modalOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
            
            const date = new Date(transaksi.tanggalKeuangan);
            const formattedDate = date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
            
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

        function openEditTransaksiModal(id_keuangan) {
            fetch('keuangan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showEditModal(result.data);
                } else {
                    window.alert('Gagal mengambil data transaksi: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.alert('Terjadi kesalahan saat mengambil data transaksi');
            });
        }

        function showEditModal(transaksi) {
            const modalOverlay = document.createElement('div');
            modalOverlay.className = 'modal-overlay';
            modalOverlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';

            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;';

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

            const modalBody = document.createElement('div');
            modalBody.style.padding = '24px';
            
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
            
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modalOverlay.appendChild(modalContent);
            document.body.appendChild(modalOverlay);
            
            updateEditKategoriOptions();
            document.getElementById('editKategoriTransaksi').value = transaksi.kategoriTransaksi;
            
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
                if (data.success) {
                    document.querySelector('.modal-overlay').remove();
                    window.alert('✓ Transaksi berhasil diupdate!');
                    window.location.reload();
                } else {
                    btnUpdate.disabled = false;
                    btnUpdate.textContent = 'Simpan Perubahan';
                    window.alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
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
                if (data.success) {
                    window.alert('✓ Transaksi berhasil dihapus!');
                    window.location.reload();
                } else {
                    window.alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.alert('Terjadi kesalahan saat menghapus transaksi');
            });
        }
    </script>
</body>
</html>
