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
