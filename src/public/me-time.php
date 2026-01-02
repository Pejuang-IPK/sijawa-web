<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Me Time - SIJAWA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/me-time.css?v=<?php echo time(); ?>"> 
</head>
<body>
    <div class="page">
        <?php include 'includes/sidebar.php'; ?>
        <main class="content">
            <header class="content-header">
                    <div>
                        <h1>Dashboard Me-Time</h1>
                        <p>Analisis Jadwalmu dan Kelola Waktumu untuk Me-Time</p>
                    </div>
            </header>
            <div class="mental-health-card">
                <div class="card-content-wrapper">
                    
                    <div class="mh-text">
                        <h2>Indikator Kesehatan Mental</h2>
                        <p>Berdasarkan kepadatan jadwal minggu ini</p>
                    </div>

                    <div class="mh-stats">
                        <span class="percent">70 %</span>
                        <span class="label">Stress Level</span>
                    </div>
                </div>

                <div class="mh-progress-track">
                    <div class="mh-progress-fill" style="width: 70%;"></div>
                </div>
            </div>

            <div class="metime-section">
            <div class="metime-header">
                <h2>Rekomendasi Me Time</h2>
                <span class="badge-count">3 opsi tersedia saat ini</span>
            </div>
            <div class="metime-grid">
                <div class="metime-card bg-blue">
                    <div class="icon-illustration">
                        <i class="fa-solid fa-film"></i>
                        <div class="decor-circle"></div>
                        <div class="decor-star"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="card-text">
                        <h3>Menonton Film</h3>
                        <p>Siapkan cemilan, saatnya nonton film favorit</p>
                    </div>
                </div>

                <div class="metime-card bg-green">
                    <div class="icon-illustration">
                        <i class="fa-solid fa-person-running"></i>
                        <div class="decor-circle"></div>
                        <div class="decor-star"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="card-text">
                        <h3>Olahraga Ringan</h3>
                        <p>Gerakkan badan sebentar agar pikiran segar kembali</p>
                    </div>
                </div>

                <div class="metime-card bg-pink">
                    <div class="icon-illustration">
                        <i class="fa-solid fa-gamepad"></i>
                        <div class="decor-circle"></div>
                        <div class="decor-star"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="card-text">
                        <h3>Main Game</h3>
                        <p>Login sebentar, push rank atau sekedar having fun</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>