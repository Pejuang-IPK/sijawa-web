<?php
/**
 * Subscription Charge Handler
 * 
 * Mode 1 - Manual (via browser): Charge current logged-in user
 * Mode 2 - Cron Job: Charge all users
 * 
 * Crontab example: 0 0 1 * * /usr/bin/php /path/to/charge_subscriptions.php
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/KeuanganController.php';

// Check if called via browser (manual mode)
$isManualMode = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if ($isManualMode) {
    // Manual mode - charge only current user
    header('Content-Type: application/json');
    
    $id_mahasiswa = $_SESSION['id_mahasiswa'];
    $total = getTotalLangganan($id_mahasiswa);
    
    if ($total > 0) {
        $data = [
            'id_mahasiswa' => $id_mahasiswa,
            'transaksi' => $total,
            'keteranganTransaksi' => 'Tagihan Langganan Bulanan',
            'jenisTransaksi' => 'Pengeluaran',
            'kategoriTransaksi' => 'Langganan'
        ];
        
        $result = tambahTransaksi($data);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada langganan aktif']);
    }
    exit();
} else {
    // Cron mode - charge all users
    echo "=== Monthly Subscription Charge ===\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n\n";
    
    $result = chargeMonthlySubscriptions();
    
    if ($result['success']) {
        echo "✓ Success!\n";
        echo "Charged: {$result['charged']} subscriptions\n";
        
        if (!empty($result['errors'])) {
            echo "\n⚠ Errors:\n";
            foreach ($result['errors'] as $error) {
                echo "  - $error\n";
            }
        }
    } else {
        echo "✗ Failed!\n";
        echo "Message: {$result['message']}\n";
    }
    
    echo "\n=== Process Complete ===\n";
}
?>
