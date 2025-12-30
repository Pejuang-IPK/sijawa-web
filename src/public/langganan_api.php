<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Include database connection and controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/KeuanganController.php';

$id_mahasiswa = $_SESSION['id_mahasiswa'];
$response = ['success' => false, 'message' => ''];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch($action) {
            case 'tambah':
                $data = [
                    'id_mahasiswa' => $id_mahasiswa,
                    'nama_langganan' => $_POST['nama_langganan'] ?? '',
                    'icon' => $_POST['icon'] ?? 'fa-circle',
                    'harga_bulanan' => (int)($_POST['harga_bulanan'] ?? 0)
                ];
                $response = tambahLangganan($data);
                break;
                
            case 'hapus':
                $id_langganan = $_POST['id_langganan'] ?? '';
                $response = hapusLangganan($id_langganan);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Invalid action: ' . $action];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle GET requests (fetch data)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $langganan = getLanggananByMahasiswa($id_mahasiswa);
        $total = getTotalLangganan($id_mahasiswa);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $langganan,
            'total' => $total
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// If neither POST nor GET
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit();
?>
