<?php 
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!-- Sidebar -->
<aside class="sidebar">
    <nav class="side-nav">
        <div class="brand">S.</div>
        <a class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>" href="dashboard.php" title="Beranda"><i class="fa-solid fa-house"></i></a>
        <a class="<?= ($current_page == 'tugas.php') ? 'active' : '' ?>" href="tugas.php" title="Tugas"><i class="fa-solid fa-list-check"></i></a>
        <a class="<?= ($current_page == 'kalender.php') ? 'active' : '' ?>" href="kalender.php" title="Kalender"><i class="fa-solid fa-calendar-days"></i></a>
        <a class="<?= ($current_page == 'keuangan.php') ? 'active' : '' ?>" href="keuangan.php" title="Keuangan"><i class="fa-solid fa-wallet"></i></a>
        <a class="<?= ($current_page == 'me-time.php') ? 'active' : '' ?>" href="me-time.php" title="Me Time">
            <i class="fa-solid fa-mug-hot"></i>
        </a>
    </nav>
    <div class="bottom">
        <a class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>" href="profile.php" title="Setting"><i class="fa-solid fa-circle-user"></i></a>
        <div class="logout">
            <form action="logout.php" method="post">
                <button type="submit" class="icon-btn-logout" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></button>
            </form>
        </div>
    </div>
</aside>
