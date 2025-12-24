<?php
require_once __DIR__ . '/../config/database.php';

function tambahTransaksi($data) {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Koneksi database gagal'];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $data['id_mahasiswa']);
    
    // Check if mahasiswa exists
    $checkQuery = "SELECT id_mahasiswa FROM Mahasiswa WHERE id_mahasiswa = '$id_mahasiswa'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) == 0) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Data mahasiswa tidak ditemukan. Silakan login ulang.'];
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
        return ['success' => true, 'message' => 'Transaksi berhasil ditambahkan'];
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Gagal menambahkan transaksi: ' . $error];
    }
}

function getTransaksiByMahasiswa($id_mahasiswa, $limit = null) {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $query = "SELECT * FROM Keuangan WHERE id_mahasiswa = '$id_mahasiswa' ORDER BY tanggalKeuangan DESC";
    
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

function getTransaksiWithFilter($id_mahasiswa, $date_condition = '', $limit = null) {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
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
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Koneksi database gagal'];
    }
    
    $id_keuangan = mysqli_real_escape_string($conn, $id_keuangan);
    $query = "DELETE FROM Keuangan WHERE id_keuangan = '$id_keuangan'";
    
    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return ['success' => true, 'message' => 'Transaksi berhasil dihapus'];
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Gagal menghapus transaksi'];
    }
}

function getTotalSaldo($id_mahasiswa) {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        return 0;
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    
    // Get total pemasukan
    $query_pemasukan = "SELECT SUM(transaksi) as total FROM Keuangan 
                        WHERE id_mahasiswa = '$id_mahasiswa' AND jenisTransaksi = 'Pemasukan'";
    $result_pemasukan = mysqli_query($conn, $query_pemasukan);
    $pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;
    
    // Get total pengeluaran
    $query_pengeluaran = "SELECT SUM(transaksi) as total FROM Keuangan 
                          WHERE id_mahasiswa = '$id_mahasiswa' AND jenisTransaksi = 'Pengeluaran'";
    $result_pengeluaran = mysqli_query($conn, $query_pengeluaran);
    $pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;
    
    mysqli_close($conn);
    return $pemasukan - $pengeluaran;
}

function getKategoriByMahasiswa($id_mahasiswa, $jenisTransaksi = null) {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
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
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
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
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
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

function getTransaksiByKategori($id_mahasiswa, $kategori, $date_condition = '') {
    global $servername, $username, $password, $dbname;
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        return [];
    }
    
    $id_mahasiswa = mysqli_real_escape_string($conn, $id_mahasiswa);
    $kategori = mysqli_real_escape_string($conn, $kategori);
    
    $query = "SELECT * FROM Keuangan 
              WHERE id_mahasiswa = '$id_mahasiswa' 
              AND kategoriTransaksi = '$kategori'
              AND transaksi > 0
              $date_condition
              ORDER BY tanggalKeuangan DESC";
    
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
?>
