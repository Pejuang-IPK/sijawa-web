<?php

require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../app/controller/TaskController.php';

// DEMO: tanpa session. Gunakan ID mahasiswa contoh untuk insert.
$DEMO_USER_ID = 456574;


// Handle: tambah tugas manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
	$title = clean($_POST['title'] ?? '');
	$course = clean($_POST['course'] ?? '');
	$dueRaw = $_POST['due_date'] ?? '';

	$dueDate = '';
	if ($dueRaw) {
		if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $dueRaw, $m)) {
			$dueDate = sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
		} else {
			$dueDate = date('Y-m-d', strtotime($dueRaw));
		}
	}

	if ($title && $dueDate) {
		addTask($conn, $DEMO_USER_ID, $title, $course, $dueDate);
	}

	header('Location: tugas.php');
	exit();
}

// Handle: hapus tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
	$taskId = intval($_POST['task_id'] ?? 0);
	if ($taskId > 0) {
		deleteTask($conn, $taskId);
	}
	header('Location: tugas.php');
	exit();
}

// Ambil semua tugas
$tasks = getAllTasks($conn);

// Hitung status counter
$today = new DateTime('today');
$countUrgent = 0;
$countApproach = 0;
$countSafe = 0;
$countOverdue = 0;

foreach ($tasks as $t) {
	[$label, $key] = computeStatus($t['due_date'], $today);
	if ($key === 'overdue') $countOverdue++;
	elseif ($key === 'urgent') $countUrgent++;
	elseif ($key === 'approaching') $countApproach++;
	else $countSafe++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard Tugas - SIJAWA</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="style/tugas.css?v=<?php echo time(); ?>">
</head>
<body>
	<div class="page">
		<?php
            // 1. Definisikan nama halaman ini
            $page = 'dashboard'; 

            // 2. Baru panggil sidebar
            include 'includes/sidebar.php'; 
        ?>

		<main class="content">
			<header class="content-header">
				<div>
					<div class="title">Dashboard Tugas</div>
					<p>Pantau dan kelola deadline tugasmu!</p>
				</div>
			</header>

			<section class="add-box">
				<form class="add-form" method="post" action="tugas.php">
					<input type="hidden" name="action" value="add" />

					<div class="add-title">
						<i class="fa-solid fa-plus"></i> Tambah Tugas Manual
					</div>

					<div class="fields">
						<div class="field">
							<label>Judul Tugas</label>
							<input type="text" name="title" placeholder="Contoh: Tugas MPTI" required>
						</div>
						<div class="field">
							<label>Mata Kuliah</label>
							<input type="text" name="course" placeholder="Nama Matkul">
						</div>
						<div class="field">
							<label>Tenggat Waktu</label>
							<input type="date" name="due_date" required>
						</div>
						<div class="field small">
							<label>&nbsp;</label>
							<div class="checkbox">
								<input type="checkbox" id="notify" name="notify">
								<label for="notify">Ingatkan</label>
							</div>
						</div>
						<div class="actions">
							<button type="submit" class="primary">Simpan</button>
						</div>
					</div>
				</form>
			</section>

			<section class="stats">
				<div class="stat urgent">
					<div class="label">URGENT (H-1)</div>
					<div class="value"><?= $countUrgent ?></div>
				</div>
				<div class="stat approaching">
					<div class="label">MENDEKATI (H-3)</div>
					<div class="value"><?= $countApproach ?></div>
				</div>
				<div class="stat safe">
					<div class="label">AMAN</div>
					<div class="value"><?= $countSafe ?></div>
				</div>
				<div class="stat overdue">
					<div class="label">TERLEWAT</div>
					<div class="value"><?= $countOverdue ?></div>
				</div>
				<div class="stat total">
					<div class="label">TOTAL TUGAS</div>
					<div class="value"><?= count($tasks) ?></div>
				</div>
			</section>

			<?php foreach ($tasks as $t): ?>
				<?php [$label, $key] = computeStatus($t['due_date'], $today); ?>
				<article class="task-card <?= $key ?>">
					<div class="task-left">
						<div class="icon <?= $key ?>">
							<?php if ($key === 'urgent'): ?>
								<i class="fa-solid fa-radiation"></i>
							<?php elseif ($key === 'approaching'): ?>
								<i class="fa-solid fa-hourglass-half"></i>
							<?php elseif ($key === 'overdue'): ?>
								<i class="fa-solid fa-skull-crossbones"></i>
							<?php else: ?>
								<i class="fa-solid fa-shield-heart"></i>
							<?php endif; ?>
						</div>
						<div class="task-info">
							<h3><?= clean($t['title']) ?></h3>
							<div class="meta">
								<?php if (!empty($t['course'])): ?>
									<span class="pill course"><?= clean($t['course']) ?></span>
								<?php endif; ?>
								<span class="pill date"><i class="fa-solid fa-calendar-day"></i> <?= date('d-m-Y', strtotime($t['due_date'])) ?></span>
							</div>
						</div>
					</div>
					<div class="task-right">
						<span class="badge <?= $key ?>"><?= $label ?></span>
						<form method="post" action="tugas.php" onsubmit="return confirm('Hapus tugas ini?');">
							<input type="hidden" name="action" value="delete" />
							<input type="hidden" name="task_id" value="<?= (int)$t['id'] ?>" />
							<button class="icon-btn" type="submit" title="Hapus"><i class="fa-solid fa-trash"></i></button>
						</form>
					</div>
				</article>
			<?php endforeach; ?>

			<?php if (count($tasks) === 0): ?>
				<div class="empty">Belum ada tugas. Tambahkan dari form di atas.</div>
			<?php endif; ?>

		</main>
	</div>

	<script>
		// Placeholder untuk interaksi kecil bila diperlukan
	</script>
</body>
</html>

