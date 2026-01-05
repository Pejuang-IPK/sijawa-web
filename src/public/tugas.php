<?php
session_start();
require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../app/controller/TaskController.php';

$DEMO_USER_ID = $_SESSION['user_id'];

$pesan = null;
$tipe_pesan = null;
if (isset($_SESSION['flash_message'])) {
	$pesan = $_SESSION['flash_message'];
	$tipe_pesan = $_SESSION['flash_type'];
	unset($_SESSION['flash_message']);
	unset($_SESSION['flash_type']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
	$title = clean($_POST['title'] ?? '');
	$course = clean($_POST['course'] ?? '');
	$dueRaw = $_POST['due_date'] ?? '';
	$dueTime = $_POST['due_time'] ?? '23:59';

	$dueDate = '';
	if ($dueRaw) {
		if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $dueRaw, $m)) {
			$dueDate = sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
		} else {
			$dueDate = date('Y-m-d', strtotime($dueRaw));
		}
	}

	if ($title && $dueDate) {
		addTask($conn, $DEMO_USER_ID, $title, $course, $dueDate, $dueTime);
		$_SESSION['flash_message'] = 'Tugas berhasil ditambahkan!';
		$_SESSION['flash_type'] = 'success';
	} else {
		$_SESSION['flash_message'] = 'Gagal menambahkan tugas. Periksa kembali input Anda.';
		$_SESSION['flash_type'] = 'error';
	}

	header('Location: tugas.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
	$taskId = intval($_POST['task_id'] ?? 0);
	if ($taskId > 0) {
		deleteTask($conn, $taskId);
		$_SESSION['flash_message'] = 'Tugas berhasil dihapus!';
		$_SESSION['flash_type'] = 'success';
	} else {
		$_SESSION['flash_message'] = 'Gagal menghapus tugas.';
		$_SESSION['flash_type'] = 'error';
	}
	header('Location: tugas.php');
	exit();
}

$tasks = getAllTasks($conn, $DEMO_USER_ID);

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
	<script src="script/tugas.js" defer></script>
</head>
<body>
	<div class="page">
		<?php
            $page = 'dashboard'; 
            include 'includes/sidebar.php'; 
        ?>

		<main class="content">
			<header class="content-header">
				<div>
					<div class="title">Dashboard Tugas</div>
					<p>Pantau dan kelola deadline tugasmu!</p>
				</div>
			</header>

			<?php if ($pesan): ?>
				<div class="alert alert-<?php echo $tipe_pesan; ?>">
					<i class="fa-solid fa-<?php echo ($tipe_pesan === 'success') ? 'check-circle' : 'circle-exclamation'; ?>"></i>
					<span><?php echo clean($pesan); ?></span>
				</div>
			<?php endif; ?>

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
						<label>Tenggat Tanggal</label>
						<input type="date" name="due_date" required>
					</div>
					<div class="field">
						<label>Tenggat Waktu</label>
						<input type="time" name="due_time" value="23:59" required>
						</div>

						<div class="actions">
							<button type="submit" class="primary">Simpan</button>
						</div>
					</div>
				</form>
			</section>

			<section class="stats">
				<div class="stat urgent<?= $countUrgent > 0 ? ' active' : '' ?>">
					<div class="label">URGENT (H-1)</div>
					<div class="value"><?= $countUrgent ?></div>
				</div>
				<div class="stat approaching<?= $countApproach > 0 ? ' active' : '' ?>">
					<div class="label">MENDEKATI (H-3)</div>
					<div class="value"><?= $countApproach ?></div>
				</div>
				<div class="stat safe<?= $countSafe > 0 ? ' active' : '' ?>">
					<div class="label">AMAN</div>
					<div class="value"><?= $countSafe ?></div>
				</div>
				<div class="stat overdue<?= $countOverdue > 0 ? ' active' : '' ?>">
					<div class="label">TERLEWAT</div>
					<div class="value"><?= $countOverdue ?></div>
				</div>
				<div class="stat total<?= count($tasks) > 0 ? ' active' : '' ?>">
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
							<i class="fa-solid fa-bell"></i>
						<?php elseif ($key === 'approaching'): ?>
							<i class="fa-solid fa-exclamation"></i>
						<?php elseif ($key === 'overdue'): ?>
							<i class="fa-solid fa-xmark"></i>
						<?php else: ?>
							<i class="fa-solid fa-thumbs-up"></i>
							<?php endif; ?>
						</div>
						<div class="task-info">
							<h3><?= clean($t['title']) ?></h3>
							<div class="meta">
								<?php if (!empty($t['course'])): ?>
								<span class="pill course <?= $key === 'overdue' ? 'overdue' : '' ?>"><?= clean($t['course']) ?></span>
							<?php endif; ?>
						<span class="pill date <?= $key === 'overdue' ? 'overdue' : '' ?>"><i class="fa-solid fa-calendar-day"></i> <?= date('d-m-Y', strtotime($t['due_date'])) ?></span>
						<span class="pill time <?= $key === 'overdue' ? 'overdue' : '' ?>"><i class="fa-solid fa-clock"></i> <?= date('H:i', strtotime($t['due_date'])) ?></span>
							</div>
						</div>
					</div>
					<div class="task-right">
						<span class="badge <?= $key ?>"><?= $label ?></span>
						<button class="icon-btn" type="button" onclick="showDeleteModal(<?= (int)$t['id'] ?>, '<?= addslashes(clean($t['title'])) ?>')" title="Hapus"><i class="fa-solid fa-trash"></i></button>
					</div>
				</article>
			<?php endforeach; ?>

			<?php if (count($tasks) === 0): ?>
				<div class="empty">Belum ada tugas. Tambahkan dari form di atas.</div>
			<?php endif; ?>

		</main>
	</div>

	<div id="deleteModal" class="modal">
		<div class="modal-content">
			<div class="modal-icon">
				<i class="fa-solid fa-triangle-exclamation"></i>
			</div>
			<h2 class="modal-title">Hapus Jadwal?</h2>
			<p class="modal-text">Apakah Anda yakin ingin menghapus jadwal ini?<br>Tindakan ini tidak dapat dibatalkan.</p>
			<div class="modal-actions">
				<button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
				<button class="btn-delete" onclick="confirmDelete()">Ya, Hapus</button>
			</div>
		</div>
	</div>

	<form id="deleteForm" method="post" action="tugas.php" style="display: none;">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="task_id" id="deleteTaskId" />
	</form>
</body>
</html>

