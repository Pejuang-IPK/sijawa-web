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
                <button class="btn-charge-now" onclick="bayarSekarang()" title="Bayar Sekarang" style="background: #10b981; color: white; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; font-size: 14px;">
                    <i class="fa-solid fa-dollar-sign"></i>
                </button>
                <button class="btn-add-subscription" onclick="bukaModalLangganan()">
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
