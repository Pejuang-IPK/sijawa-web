<!-- Expense Categories -->
<div class="category-section">
    <div class="section-header">
        <div class="section-title-group">
            <i class="fa-solid fa-circle-dot expense-dot"></i>
            <h3>Kategori Pengeluaran</h3>
        </div>
        <div class="section-actions">
            <div class="date-filter-group">
                <select class="filter-select" id="periodTypeExpense" onchange="updateOpsiNilai()">
                    <option value="hari" <?php echo $period == 'hari' ? 'selected' : ''; ?>>Hari</option>
                    <option value="minggu" <?php echo $period == 'minggu' ? 'selected' : ''; ?>>Minggu</option>
                    <option value="bulan" <?php echo $period == 'bulan' ? 'selected' : ''; ?>>Bulan</option>
                    <option value="tahun" <?php echo $period == 'tahun' ? 'selected' : ''; ?>>Tahun</option>
                    <option value="semua" <?php echo $period == 'semua' ? 'selected' : ''; ?>>Semua</option>
                </select>
                <select class="filter-select" id="valueSelectExpense" onchange="terapkanFilter()" style="<?php echo $period == 'semua' ? 'display:none;' : ''; ?>">
                    <option value="">Memuat...</option>
                </select>
            </div>
            <button class="btn-add-category" onclick="bukaModalKategori('Pengeluaran')">
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
                    <div class="expense-card" onclick="tampilkanDetailKategori('<?php echo htmlspecialchars($stat['kategoriTransaksi']); ?>', 'Pengeluaran')" style="cursor: pointer;">
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
