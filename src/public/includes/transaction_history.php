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
                        $amount_prefix = $is_income ? '+' : '-';
                        ?>
                        <div class="transaction-item" onclick="bukaModalDetailTransaksi('<?php echo $trans['id_keuangan']; ?>')" style="cursor: pointer;">
                            <div class="transaction-icon <?php echo $icon_class; ?>">
                                <i class="fa-solid <?php echo $icon; ?>" style="transform: rotate(45deg);"></i>
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
