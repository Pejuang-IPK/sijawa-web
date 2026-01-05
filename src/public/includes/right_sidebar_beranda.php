<?php

$id_mahasiswa = $_SESSION['user_id'] ?? 0;

$queryTask = "SELECT t.*, s.status 
              FROM Tugas t 
              LEFT JOIN StatusTugas s ON t.id_status = s.id_status
              WHERE t.id_mahasiswa = $id_mahasiswa 
              AND DATE(t.tenggatTugas) = CURDATE()
              AND t.tenggatTugas >= NOW()
              ORDER BY t.tenggatTugas ASC 
              LIMIT 1";

$resultTask = mysqli_query($conn, $queryTask);
$nearestTask = mysqli_fetch_assoc($resultTask);

$hari_indo = ['Sunday' => 'Min', 'Monday' => 'Sen', 'Tuesday' => 'Sel', 'Wednesday' => 'Rab', 'Thursday' => 'Kam', 'Friday' => 'Jum', 'Saturday' => 'Sab'];
$tgl_sekarang = date('Y-m-d');

$quotes = [
    ['text' => 'Kerja keras tidak boleh berhenti', 'author' => 'Joko Widodo'],
    ['text' => 'Pendidikan adalah senjata paling mematikan', 'author' => 'Nelson Mandela'],
    ['text' => 'Mulai aja dulu, sempurnakan nanti', 'author' => 'Unknown'],
    ['text' => 'Tugas selesai = Tidur nyenyak', 'author' => 'Mahasiswa Semester Akhir']
];
$random_quote = $quotes[array_rand($quotes)];
?>

<aside class="right-sidebar">
    
    <div class="profile-section">
        <div class="profile-img">
            <img src="assets/user.jpg" alt="Profile"> 
        </div>
        <h3><?= $_SESSION["nama"] ?? "Mahasiswa" ?></h3>
        <span class="badge-plan">Mahasiswa Aktif</span>
    </div>

    <hr class="divider">

    <div class="calendar-section">
        <h3>Tugasmu</h3>
        <div class="date-row">
            <?php 

            for ($i = 0; $i < 5; $i++) {
                $timestamp = strtotime("+$i days");
                $tgl_loop = date('Y-m-d', $timestamp);
                $hari_inggris = date('l', $timestamp);
                $hari_singkat = $hari_indo[$hari_inggris];
                $tanggal_angka = date('d', $timestamp);

                $activeClass = ($tgl_loop == $tgl_sekarang) ? 'active' : '';
            ?>
                <div class="date-card <?= $activeClass ?>">
                    <span class="day"><?= $hari_singkat ?></span>
                    <span class="date"><?= $tanggal_angka ?></span>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php if ($nearestTask) : ?>
        <?php

            $deadline = strtotime($nearestTask['tenggatTugas']);
            $isToday = (date('Y-m-d', $deadline) == date('Y-m-d'));
            
            $labelHari = $isToday ? "Hari ini" : date('d M', $deadline); 
            $jamDeadline = date('H.i', $deadline);
        ?>
        <div class="task-card">
            <div class="card-header">
                <span style="<?= $isToday ? 'color:#fff; font-weight:bold;' : '' ?>">
                    <?= $labelHari ?>
                </span>
            </div>
            
            <h2><?= htmlspecialchars(substr($nearestTask['namaTugas'], 0, 20)) . (strlen($nearestTask['namaTugas']) > 20 ? '...' : '') ?></h2>
            
            <p class="subject"><?= htmlspecialchars($nearestTask['matkulTugas']) ?></p>
            
            <hr class="card-divider">
            
            <div class="card-footer">
                <button class="btn-action" onclick="window.location.href='tugas.php'">Segera Kerjakan</button>
                <div class="time">
                    <i class="fa-regular fa-clock"></i> <?= $jamDeadline ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="task-card" style="border: 2px dashed #ddd; background: #f9f9f9; text-align: center;">
            <div style="padding: 20px;">
                <i class="fa-solid fa-mug-hot" style="font-size: 30px; color: #ccc; margin-bottom: 10px;"></i>
                <h2 style="font-size: 16px; color: #888;">Tugas Aman!</h2>
                <p class="subject" style="margin-bottom: 0;">Tidak ada deadline dalam waktu dekat.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="motivation-card">
        <div class="title-motivation">Semangat Sukses 🔥</div>
        <p class="desc">Siap produktif hari ini? Atau mau scrolling dulu 5 menit?</p>
        
        <blockquote class="quote">
            “<?= $random_quote['text'] ?>”
        </blockquote>
        <div class="author"><?= $random_quote['author'] ?></div>

        <div class="logo-corner">S.</div>
        <div class="bg-pattern"></div>
    </div>

</aside>