<?php
// Pastikan path ke file database dan controller benar
require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../app/controller/MeTimeController.php';
require_once __DIR__ . '../../app/action/metime_action.php';

// 1. Ambil Data
$id_mahasiswa = 386937; // Sesuaikan dengan session login nanti

// Panggil fungsi hitungStressLevel yang baru
$data_stress = hitungStressLevel($id_mahasiswa);

// Ambil variabel hasil perhitungan baru (Sesuai Controller)
$score       = $data_stress['score'];
$total_sks   = $data_stress['total_sks'];
$total_jam   = $data_stress['total_jam']; // Durasi dalam Jam
$total_tugas = $data_stress['total_tugas'];

$rekomendasi = getRekomendasi($score);

// Logic Warna Progress Bar
$barColor = '#4caf50'; // Hijau (Aman)
if($score > 70) {
    $barColor = '#ef4444'; // Merah (Bahaya)
} elseif ($score > 40) {
    $barColor = '#ffffffff'; 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Me Time - SIJAWA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/me-time.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="style/modal_mood.css?v=<?php echo time(); ?>"> 
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="content">
            <header class="content-header">
                <div>
                    <h1>Dashboard Me-Time</h1>
                    <p>Analisis Jadwal & Tugas untuk Keseimbangan Hidupmu</p>
                </div>
            </header>
            
            <div class="mental-health-card">
                <div class="card-content-wrapper">
                    <div class="mh-text">
                        <h2>Beban Akademik Minggu Ini</h2>
                        <div style="margin-top: 10px; font-size: 14px; color: #555; line-height: 1.6;">
                            Kamu memiliki beban <b><?= $total_sks ?> SKS</b> <br>
                            dengan durasi total <b><?= $total_jam ?> Jam</b> kuliah.<br>
                            Ada <b><?= $total_tugas ?> Tugas</b> yang deadline-nya sudah dekat.
                        </div>
                    </div>

                    <div class="mh-stats">
                        <span class="percent" style="color: <?= $barColor ?>"><?= $score ?> %</span>
                        <span class="label">Stress Level</span>
                    </div>
                </div>

                <div class="mh-progress-track">
                    <div class="mh-progress-fill" style="width: <?= $score ?>%; background-color: <?= $barColor ?>;"></div>
                </div>
            </div>

            <div class="metime-section">
                <div class="metime-header">
                    <h2>Rekomendasi Me Time</h2>
                    <?php if($score > 70): ?>
                        <span class="badge-count" style="background:#fee2e2; color:#b91c1c;">Sangat Padat! Butuh Istirahat Total.</span>
                    <?php elseif($score > 30): ?>
                        <span class="badge-count" style="background:#fef3c7; color:#b45309;">Lumayan Sibuk. Butuh Hiburan.</span>
                    <?php else: ?>
                        <span class="badge-count" style="background:#dcfce7; color:#15803d;">Santai. Ayo Produktif!</span>
                    <?php endif; ?>
                </div>
                
                <div class="metime-grid">
                    <?php foreach($rekomendasi as $item): ?>
                        <div class="metime-card <?= $item['color'] ?>">
                            <div class="icon-illustration">
                                <i class="fa-solid <?= $item['icon'] ?>"></i>
                                <div class="decor-circle"></div>
                                <div class="decor-star"><i class="fa-solid fa-star"></i></div>
                            </div>
                            <div class="card-text">
                                <h3><?= $item['title'] ?></h3>
                                <p><?= $item['desc'] ?></p>
                            </div>
                            
                            <div style="margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 10px;">
                                <button type="button" 
                                        onclick="openLogModal('<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>', '<?= $score ?>')"
                                        style="width: 100%; background: white; border: 1px solid #cbd5e1; padding: 8px 15px; border-radius: 20px; font-size: 12px; cursor: pointer; color: #475569; font-weight: 600; transition: all 0.2s;">
                                    <i class="fa-solid fa-check"></i> Ambil Sesi Ini
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="history-section" style="margin-top: 40px; border-top: 1px dashed #ddd; padding-top: 20px;">
                <h3>History Mood Terakhir</h3>
                
                <?php
                // DEBUGGING: Cek koneksi database
                if (!$conn) {
                    echo "<p style='color:red'>Database tidak terhubung!</p>";
                }

                // Query History
                $qMood = "SELECT * FROM Mood WHERE id_mahasiswa = $id_mahasiswa ORDER BY tanggalLogMood DESC LIMIT 3";
                $resMood = mysqli_query($conn, $qMood);

                // Cek Error Query SQL
                if (!$resMood) {
                    echo "<p style='color:red'>Error SQL: " . mysqli_error($conn) . "</p>";
                }
                ?>
                
                <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                    <?php if($resMood && mysqli_num_rows($resMood) > 0): ?>
                        <?php while($m = mysqli_fetch_assoc($resMood)): ?>
                            <div style="background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee; flex: 1; min-width: 200px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                <div style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                                    <small style="color: #888;"><?= date('d M Y', strtotime($m['tanggalLogMood'])) ?></small>
                                    <small style="color: #888;"><?= date('H:i', strtotime($m['tanggalLogMood'])) ?></small>
                                </div>
                                <h4 style="margin: 5px 0; font-size: 16px; color: #333;"><?= htmlspecialchars($m['namaKegiatan']) ?></h4>
                                <div style="font-size: 13px; color: #666; margin-top: 5px;">
                                    Stress Awal: <span style="font-weight:bold; color: var(--primary-color);"><?= $m['levelStress'] ?>%</span>
                                </div>
                                <div style="font-size: 12px; color: #888; margin-top: 2px;">
                                    <i class="fa-regular fa-clock"></i> Durasi: <?= $m['waktuMeTime'] ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="width: 100%; text-align: center; padding: 20px; background: #f9f9f9; border-radius: 10px;">
                            <p style="color: #888; font-style: italic; margin: 0;">Belum ada riwayat mood yang tercatat.</p>
                            <small>Klik tombol "Ambil Sesi Ini" di atas untuk menambah data.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </main> </div> <div id="logModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Catat Aktivitas</h3>
                <button type="button" class="close-btn" onclick="closeLogModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="level_stress" id="input_stress">
                <input type="hidden" name="nama_kegiatan" id="input_kegiatan">
                
                <div class="modal-info-box">
                    <div style="margin-bottom: 12px;">
                        <div class="info-label">Aktivitas Terpilih</div>
                        <div class="info-value" id="text_kegiatan">-</div>
                    </div>
                    <div>
                        <div class="info-label">Stress Level Saat Ini</div>
                        <div class="info-value">
                            <span id="text_stress" style="background:#e2e8f0; padding:2px 8px; border-radius:4px;">0%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Berapa lama kamu melakukannya?</label>
                    <select name="durasi" class="form-input" required>
                        <option value="00:30:00">30 Menit</option>
                        <option value="01:00:00">1 Jam</option>
                        <option value="01:30:00">1 Jam 30 Menit</option>
                        <option value="02:00:00">2 Jam</option>
                        <option value="03:00:00">Lebih dari 3 Jam</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeLogModal()">Batal</button>
                    <button type="submit" name="submit-activity" class="btn-save">Simpan Riwayat</button>
                </div>
            </form>
        </div>
    </div>
    <script src="script/metime.js?v=<?php echo time(); ?>"></script>
</body>
</html>