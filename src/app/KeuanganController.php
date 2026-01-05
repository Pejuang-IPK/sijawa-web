<?php
require_once __DIR__ . '/../config/database.php';

function tambahTransaksi($data) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['sukses' => false, 'pesan' => 'Koneksi database gagal'];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $data['id_mahasiswa']);

    $checkQuery = "SELECT id_mahasiswa FROM Mahasiswa WHERE id_mahasiswa = '$id_mahasiswa'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) == 0) {
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Data mahasiswa tidak ditemukan. Silakan login ulang.'];
    }
    
    $id_keuangan = random_int(100000, 999999);
    $tanggalKeuangan = date('Y-m-d H:i:s');
    $saldo = 0;
    $transaksi = (int)$data['transaksi'];
    $keteranganTransaksi = mysqli_real_escape_string($conn, $data['keteranganTransaksi']);
    $jenisTransaksi = mysqli_real_escape_string($conn, $data['jenisTransaksi']);
    $kategoriTransaksi = mysqli_real_escape_string($conn, $data['kategoriTransaksi']);
    
    $query = "INSERT INTO Keuangan (id_keuangan, id_mahasiswa, tanggalKeuangan, saldo, transaksi, keteranganTransaksi, jenisTransaksi, kategoriTransaksi) 
              VALUES ('$id_keuangan', '$id_mahasiswa', '$tanggalKeuangan', '$saldo', '$transaksi', '$keteranganTransaksi', '$jenisTransaksi', '$kategoriTransaksi')";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['sukses' => true, 'pesan' => 'Transaksi berhasil ditambahkan'];
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Gagal menambahkan transaksi: ' . $error];
    }
}

function getTransaksiWithFilter($id_mahasiswa, $date_condition = '', $limit = null) {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $query = "SELECT * FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa' AND transaksi > 0 $date_condition ORDER BY tanggalKeuangan DESC";
    
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $data;
}

function hapusTransaksi($id_keuangan) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['sukses' => false, 'pesan' => 'Koneksi database gagal'];
    }
    
    $id_keuangan = mysqli_real_escape_string($conn, $id_keuangan);
    $query = "DELETE FROM Keuangan WHERE id_keuangan = '$id_keuangan'";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['sukses' => true, 'pesan' => 'Transaksi berhasil dihapus'];
    } else {
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Gagal menghapus transaksi'];
    }
}

function editTransaksi($id_keuangan, $data) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['sukses' => false, 'pesan' => 'Koneksi database gagal'];
    }
    
    $id_keuangan = mysqli_real_escape_string($conn, $id_keuangan);
    $transaksi = (int)$data['transaksi'];
    $keteranganTransaksi = mysqli_real_escape_string($conn, $data['keteranganTransaksi']);
    $jenisTransaksi = mysqli_real_escape_string($conn, $data['jenisTransaksi']);
    $kategoriTransaksi = mysqli_real_escape_string($conn, $data['kategoriTransaksi']);
    
    $query = "UPDATE Keuangan SET 
              transaksi = $transaksi,
              keteranganTransaksi = '$keteranganTransaksi',
              jenisTransaksi = '$jenisTransaksi',
              kategoriTransaksi = '$kategoriTransaksi'
              WHERE id_keuangan = '$id_keuangan'";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['sukses' => true, 'pesan' => 'Transaksi berhasil diperbarui'];
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Gagal memperbarui transaksi: ' . $error];
    }
}

function getTransaksiById($id_keuangan) {
    $conn = getDBConnection();
    if (!$conn) {
        return null;
    }
    
    $id_keuangan = mysqli_real_escape_string($conn, $id_keuangan);
    $query = "SELECT * FROM Keuangan WHERE id_keuangan = '$id_keuangan' LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    $data = null;
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $data = $row;
    }
    
    mysqli_close($conn);
    return $data;
}

function handleTransaksiAPI() {
    header('Content-Type: application/json');

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_mahasiswa'])) {
        http_response_code(401);
        echo json_encode(['sukses' => false, 'pesan' => 'Tidak diizinkan']);
        exit;
    }
    
    $id_mahasiswa = $_SESSION['id_mahasiswa'];
    
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'get':
                    $id_keuangan = $_POST['id_keuangan'] ?? '';
                    if (empty($id_keuangan)) {
                        echo json_encode(['sukses' => false, 'pesan' => 'ID transaksi tidak valid']);
                        exit;
                    }
                    
                    $transaksi = getTransaksiById($id_keuangan);
                    if ($transaksi) {
                        echo json_encode(['sukses' => true, 'data' => $transaksi]);
                    } else {
                        echo json_encode(['sukses' => false, 'pesan' => 'Transaksi tidak ditemukan']);
                    }
                    break;
                    
                case 'edit':
                    $id_keuangan = $_POST['id_keuangan'] ?? '';
                    if (empty($id_keuangan)) {
                        echo json_encode(['sukses' => false, 'pesan' => 'ID transaksi tidak valid']);
                        exit;
                    }
                    
                    $data = [
                        'transaksi' => $_POST['transaksi'] ?? 0,
                        'keteranganTransaksi' => $_POST['keteranganTransaksi'] ?? '',
                        'jenisTransaksi' => $_POST['jenisTransaksi'] ?? '',
                        'kategoriTransaksi' => $_POST['kategoriTransaksi'] ?? ''
                    ];
                    
                    $result = editTransaksi($id_keuangan, $data);
                    echo json_encode($result);
                    break;
                    
                case 'hapus':
                    $id_keuangan = $_POST['id_keuangan'] ?? '';
                    if (empty($id_keuangan)) {
                        echo json_encode(['sukses' => false, 'pesan' => 'ID transaksi tidak valid']);
                        exit;
                    }
                    
                    $result = hapusTransaksi($id_keuangan);
                    echo json_encode($result);
                    break;
                    
                default:
                    echo json_encode(['sukses' => false, 'pesan' => 'Aksi tidak valid']);
            }
        } else {
            echo json_encode(['sukses' => false, 'pesan' => 'Metode tidak valid']);
        }
    } catch (Exception $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Kesalahan: ' . $e->getMessage()]);
    }
    exit;
}

function getKategoriByMahasiswa($id_mahasiswa, $jenisTransaksi = null) {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $query = "SELECT DISTINCT kategoriTransaksi FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa'";
    
    if ($jenisTransaksi) {
        $jenisTransaksi = mysqli_real_escape_string($conn, $jenisTransaksi);
        $query .= " AND jenisTransaksi = '$jenisTransaksi'";
    }
    
    $query .= " AND kategoriTransaksi IS NOT NULL AND kategoriTransaksi != '' ORDER BY kategoriTransaksi ASC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['kategoriTransaksi'];
        }
    }
    
    mysqli_close($conn);
    return $data;
}

function getStatistikKategori($id_mahasiswa, $date_condition = '') {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    
    $query = "SELECT kategoriTransaksi, jenisTransaksi, SUM(transaksi) as total, COUNT(*) as jumlah
              FROM Keuangan 
              WHERE id_mahasiswa = '$id_mahasiswa' 
              AND kategoriTransaksi IS NOT NULL 
              AND kategoriTransaksi != ''
              $date_condition
              GROUP BY kategoriTransaksi, jenisTransaksi
              ORDER BY total DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $data;
}

function getMonthlyAnalysis($id_mahasiswa) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $current_month = date('m');
    $current_year = date('Y');
    
    $query = "SELECT jenisTransaksi, SUM(transaksi) as total
              FROM Keuangan 
              WHERE id_mahasiswa = '$id_mahasiswa'
              AND MONTH(tanggalKeuangan) = '$current_month'
              AND YEAR(tanggalKeuangan) = '$current_year'
              GROUP BY jenisTransaksi";
    
    $result = mysqli_query($conn, $query);
    $pemasukan = 0;
    $pengeluaran = 0;
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['jenisTransaksi'] == 'Pemasukan') {
                $pemasukan = $row['total'];
            } else {
                $pengeluaran = $row['total'];
            }
        }
    }
    
    mysqli_close($conn);
    return [
        'pemasukan' => $pemasukan,
        'pengeluaran' => $pengeluaran,
        'saldo' => $pemasukan - $pengeluaran
    ];
}

function tambahLangganan($data) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['sukses' => false, 'pesan' => 'Koneksi database gagal'];
    }
    
    $id_langganan = random_int(100000000, 999999999);
    $id_mahasiswa = (int)$data['id_mahasiswa'];
    $nama_langganan = mysqli_real_escape_string($conn, $data['nama_langganan']);
    $icon = mysqli_real_escape_string($conn, $data['icon']);
    $harga_bulanan = (int)$data['harga_bulanan'];
    
    $query = "INSERT INTO Langganan (id_langganan, id_mahasiswa, nama_langganan, icon, harga_bulanan) 
              VALUES ($id_langganan, $id_mahasiswa, '$nama_langganan', '$icon', $harga_bulanan)";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['sukses' => true, 'pesan' => 'Langganan berhasil ditambahkan'];
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Gagal menambahkan langganan: ' . $error];
    }
}

function getLanggananByMahasiswa($id_mahasiswa) {
    $conn = getDBConnection();
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $query = "SELECT * FROM Langganan WHERE id_mahasiswa = '$id_mahasiswa' ORDER BY id_langganan DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $data;
}

function getTotalLangganan($id_mahasiswa) {
    $conn = getDBConnection();
    if (!$conn) {
        return 0;
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $query = "SELECT SUM(harga_bulanan) as total FROM Langganan WHERE id_mahasiswa = '$id_mahasiswa'";
    
    $result = mysqli_query($conn, $query);
    $total = 0;
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $total = $row['total'] ?? 0;
    }
    
    mysqli_close($conn);
    return $total;
}

function hapusLangganan($id_langganan) {
    $conn = getDBConnection();
    if (!$conn) {
        return ['sukses' => false, 'pesan' => 'Koneksi database gagal'];
    }
    
    $id_langganan = mysqli_real_escape_string($conn, $id_langganan);
    $query = "DELETE FROM Langganan WHERE id_langganan = '$id_langganan'";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['sukses' => true, 'pesan' => 'Langganan berhasil dihapus'];
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return ['sukses' => false, 'pesan' => 'Gagal menghapus langganan: ' . $error];
    }
}

function handleLanggananAPI() {
    header('Content-Type: application/json');

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_mahasiswa'])) {
        echo json_encode(['sukses' => false, 'pesan' => 'Belum login']);
        exit();
    }
    
    $id_mahasiswa = $_SESSION['id_mahasiswa'];
    $response = ['sukses' => false, 'pesan' => ''];

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
                    $response = ['sukses' => false, 'pesan' => 'Aksi tidak valid: ' . $action];
            }
        } catch (Exception $e) {
            $response = ['sukses' => false, 'pesan' => 'Kesalahan: ' . $e->getMessage()];
        }
        
        echo json_encode($response);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $langganan = getLanggananByMahasiswa($id_mahasiswa);
            $total = getTotalLangganan($id_mahasiswa);
            
            echo json_encode([
                'sukses' => true,
                'data' => $langganan,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Kesalahan: ' . $e->getMessage()]);
        }
        exit();
    }

    echo json_encode(['sukses' => false, 'pesan' => 'Metode request tidak valid']);
    exit();
}

function handleChargeSubscription() {
    header('Content-Type: application/json');

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_mahasiswa'])) {
        echo json_encode(['sukses' => false, 'pesan' => 'Tidak diizinkan']);
        exit();
    }
    
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
        echo json_encode(['sukses' => false, 'pesan' => 'Tidak ada langganan aktif']);
    }
    exit();
}

function handleFormSubmission($id_mahasiswa) {
    $pesan = '';
    $tipe_pesan = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'tambah') {
            $data = [
                'id_mahasiswa' => $id_mahasiswa,
                'saldo' => 0,
                'transaksi' => $_POST['transaksi'],
                'keteranganTransaksi' => $_POST['keteranganTransaksi'],
                'jenisTransaksi' => $_POST['jenisTransaksi'],
                'kategoriTransaksi' => $_POST['kategoriTransaksi']
            ];
            
            $result = tambahTransaksi($data);

            if ($result['sukses']) {
                $_SESSION['flash_message'] = $result['pesan'];
                $_SESSION['flash_type'] = 'success';

                $redirect_url = 'keuangan.php';
                if (isset($_GET['period'])) {
                    $redirect_url .= '?period=' . urlencode($_GET['period']);
                    if (isset($_GET['value'])) {
                        $redirect_url .= '&value=' . urlencode($_GET['value']);
                    }
                }
                header('Location: ' . $redirect_url);
                exit();
            } else {
                $pesan = $result['pesan'];
                $tipe_pesan = 'error';
            }
        } elseif ($_POST['action'] === 'tambah_kategori') {

            $data = [
                'id_mahasiswa' => $id_mahasiswa,
                'saldo' => 0,
                'transaksi' => 0,
                'keteranganTransaksi' => 'Kategori: ' . $_POST['namaKategori'],
                'jenisTransaksi' => $_POST['jenisTransaksi'],
                'kategoriTransaksi' => $_POST['namaKategori']
            ];
            
            $result = tambahTransaksi($data);

            if ($result['sukses']) {
                $_SESSION['flash_message'] = 'Kategori berhasil ditambahkan!';
                $_SESSION['flash_type'] = 'success';

                $redirect_url = 'keuangan.php';
                if (isset($_GET['period'])) {
                    $redirect_url .= '?period=' . urlencode($_GET['period']);
                    if (isset($_GET['value'])) {
                        $redirect_url .= '&value=' . urlencode($_GET['value']);
                    }
                }
                header('Location: ' . $redirect_url);
                exit();
            } else {
                $pesan = $result['pesan'];
                $tipe_pesan = 'error';
            }
        }
    }

    if (isset($_SESSION['flash_message'])) {
        $pesan = $_SESSION['flash_message'];
        $tipe_pesan = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
    
    return ['pesan' => $pesan, 'tipe_pesan' => $tipe_pesan];
}

function getDateFilter() {
    $period = isset($_GET['period']) ? $_GET['period'] : 'bulan';
    $value = isset($_GET['value']) ? $_GET['value'] : ($period === 'semua' ? '' : '0');
    $filter_label = 'Bulan Ini';
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
            if ($weeks_ago == 0) {

                $start_date = date('Y-m-d', strtotime('monday this week'));
                $end_date = date('Y-m-d', strtotime('sunday this week'));
                $filter_label = 'Minggu Ini';
            } else {

                $start_date = date('Y-m-d', strtotime("-$weeks_ago weeks monday"));
                $end_date = date('Y-m-d', strtotime("-$weeks_ago weeks sunday"));
                if ($weeks_ago == 1) {
                    $filter_label = 'Minggu Lalu';
                } else {
                    $filter_label = $weeks_ago . ' Minggu Lalu';
                }
            }
            $date_condition = " AND DATE(tanggalKeuangan) BETWEEN '$start_date' AND '$end_date'";
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
    
    return [
        'period' => $period,
        'value' => $value,
        'filter_label' => $filter_label,
        'date_condition' => $date_condition
    ];
}

function getStatistikWithFilter($id_mahasiswa, $date_condition) {

    $all_statistik = getStatistikKategori($id_mahasiswa, '');

    $filtered_statistik = getStatistikKategori($id_mahasiswa, $date_condition);

    $statistik_kategori = [];
    foreach ($all_statistik as $kategori) {

        $found = false;
        foreach ($filtered_statistik as $filtered) {
            if ($filtered['kategoriTransaksi'] == $kategori['kategoriTransaksi'] && 
                $filtered['jenisTransaksi'] == $kategori['jenisTransaksi']) {
                $statistik_kategori[] = $filtered;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $statistik_kategori[] = [
                'kategoriTransaksi' => $kategori['kategoriTransaksi'],
                'jenisTransaksi' => $kategori['jenisTransaksi'],
                'total' => 0,
                'jumlah' => 0
            ];
        }
    }
    
    return $statistik_kategori;
}

function getFinancialSummary($id_mahasiswa) {
    $conn = getDBConnection();
    if (!$conn) {
        return [
            'total_pemasukan' => 0,
            'total_pengeluaran' => 0,
            'total_saldo' => 0,
            'pemasukan_change' => 0,
            'pengeluaran_change' => 0,
            'last_pemasukan' => 0,
            'last_pengeluaran' => 0
        ];
    }

    $current_month = date('m');
    $current_year = date('Y');
    $query_current = "SELECT * FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa' AND MONTH(tanggalKeuangan) = '$current_month' AND YEAR(tanggalKeuangan) = '$current_year'";
    $result_current = mysqli_query($conn, $query_current);
    $keuangan_current = mysqli_fetch_all($result_current, MYSQLI_ASSOC);

    $last_month = date('m', strtotime('-1 month'));
    $last_year = date('Y', strtotime('-1 month'));
    $query_last = "SELECT * FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa' AND MONTH(tanggalKeuangan) = '$last_month' AND YEAR(tanggalKeuangan) = '$last_year'";
    $result_last = mysqli_query($conn, $query_last);
    $keuangan_last = mysqli_fetch_all($result_last, MYSQLI_ASSOC);

    $total_pemasukan = 0;
    $total_pengeluaran = 0;
    foreach ($keuangan_current as $data) {
        if ($data['jenisTransaksi'] == 'Pemasukan') {
            $total_pemasukan += $data['transaksi'];
        } else {
            $total_pengeluaran += $data['transaksi'];
        }
    }

    $last_pemasukan = 0;
    $last_pengeluaran = 0;
    foreach ($keuangan_last as $data) {
        if ($data['jenisTransaksi'] == 'Pemasukan') {
            $last_pemasukan += $data['transaksi'];
        } else {
            $last_pengeluaran += $data['transaksi'];
        }
    }

    $pemasukan_change = 0;
    if ($last_pemasukan > 0) {
        $pemasukan_change = (($total_pemasukan - $last_pemasukan) / $last_pemasukan) * 100;
    }

    $pengeluaran_change = 0;
    if ($last_pengeluaran > 0) {
        $pengeluaran_change = (($total_pengeluaran - $last_pengeluaran) / $last_pengeluaran) * 100;
    }

    $total_saldo = $total_pemasukan - $total_pengeluaran;
    
    mysqli_close($conn);
    
    return [
        'total_pemasukan' => $total_pemasukan,
        'total_pengeluaran' => $total_pengeluaran,
        'total_saldo' => $total_saldo,
        'pemasukan_change' => $pemasukan_change,
        'pengeluaran_change' => $pengeluaran_change,
        'last_pemasukan' => $last_pemasukan,
        'last_pengeluaran' => $last_pengeluaran
    ];
}

function getDashboardData($id_mahasiswa) {

    $form_result = handleFormSubmission($id_mahasiswa);

    $filter = getDateFilter();

    $kategori_pemasukan = getKategoriByMahasiswa($id_mahasiswa, 'Pemasukan');
    $kategori_pengeluaran = getKategoriByMahasiswa($id_mahasiswa, 'Pengeluaran');

    $statistik_kategori = getStatistikWithFilter($id_mahasiswa, $filter['date_condition']);

    $riwayat_transaksi = getTransaksiWithFilter($id_mahasiswa, '', 8);

    $monthly_analysis = getMonthlyAnalysis($id_mahasiswa);

    $financial_summary = getFinancialSummary($id_mahasiswa);
    
    return [
        'pesan' => $form_result['pesan'],
        'tipe_pesan' => $form_result['tipe_pesan'],
        'period' => $filter['period'],
        'value' => $filter['value'],
        'filter_label' => $filter['filter_label'],
        'date_condition' => $filter['date_condition'],
        'kategori_pemasukan' => $kategori_pemasukan,
        'kategori_pengeluaran' => $kategori_pengeluaran,
        'statistik_kategori' => $statistik_kategori,
        'riwayat_transaksi' => $riwayat_transaksi,
        'monthly_analysis' => $monthly_analysis,
        'total_pemasukan' => $financial_summary['total_pemasukan'],
        'total_pengeluaran' => $financial_summary['total_pengeluaran'],
        'total_saldo' => $financial_summary['total_saldo'],
        'pemasukan_change' => $financial_summary['pemasukan_change'],
        'pengeluaran_change' => $financial_summary['pengeluaran_change']
    ];
}

function getDateFilterForHistory() {
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
    
    return [
        'period' => $period,
        'value' => $value,
        'filter_label' => $filter_label,
        'date_condition' => $date_condition
    ];
}

function getHistoryPageData($id_mahasiswa) {

    $filter = getDateFilterForHistory();

    $riwayat_transaksi = getTransaksiWithFilter($id_mahasiswa, $filter['date_condition']);

    $kategori_pemasukan = getKategoriByMahasiswa($id_mahasiswa, 'Pemasukan');
    $kategori_pengeluaran = getKategoriByMahasiswa($id_mahasiswa, 'Pengeluaran');
    
    return [
        'period' => $filter['period'],
        'value' => $filter['value'],
        'filter_label' => $filter['filter_label'],
        'riwayat_transaksi' => $riwayat_transaksi,
        'kategori_pemasukan' => $kategori_pemasukan,
        'kategori_pengeluaran' => $kategori_pengeluaran
    ];
}
?>
