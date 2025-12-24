<?php
require_once __DIR__ . '/../config/database.php';

// DEMO: tanpa session. Gunakan ID mahasiswa contoh untuk insert.
$DEMO_USER_ID = 456574;

// Helper: sanitize string
function clean($val) {
	return trim(htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'));
}

// Hitung status berdasarkan tenggat
function computeStatus($dueDateStr, $today) {
	$due = DateTime::createFromFormat('Y-m-d', $dueDateStr);
	if (!$due) $due = new DateTime($dueDateStr);

	$diffDays = (int)ceil(($due->getTimestamp() - $today->getTimestamp()) / 86400);

	if ($diffDays <= 1) return ['Harus Dikerjakan', 'urgent'];
	if ($diffDays <= 3) return ['Tolong Dikerjakan', 'approaching'];
	return ['Masih Bisa Ditunda', 'safe'];
}

// Dapatkan ID berikutnya untuk tugas
function getNextTaskId($conn) {
	$nextId = 1;
	if ($res = $conn->query('SELECT COALESCE(MAX(id_tugas),0)+1 AS next_id FROM Tugas')) {
		if ($row = $res->fetch_assoc()) $nextId = (int)$row['next_id'];
		$res->free();
	}
	return $nextId;
}

// Dapatkan id_status dari label status (auto-create jika belum ada)
function getStatusId($conn, $statusLabel) {
	$stmt = $conn->prepare('SELECT id_status FROM StatusTugas WHERE status = ? LIMIT 1');
	$stmt->bind_param('s', $statusLabel);
	$stmt->execute();
	$res = $stmt->get_result();
	$statusId = null;
	if ($row = $res->fetch_assoc()) {
		$statusId = (int)$row['id_status'];
	} else {
		// Hitung id_status berikutnya
		$nextStatusId = 1;
		if ($res2 = $conn->query('SELECT COALESCE(MAX(id_status),0)+1 AS next_id FROM StatusTugas')) {
			if ($row2 = $res2->fetch_assoc()) $nextStatusId = (int)$row2['next_id'];
			$res2->free();
		}
		
		// Auto-insert dengan id_status manual
		$insertStmt = $conn->prepare('INSERT INTO StatusTugas (id_status, status) VALUES (?, ?)');
		$insertStmt->bind_param('is', $nextStatusId, $statusLabel);
		$insertStmt->execute();
		$statusId = $nextStatusId;
		$insertStmt->close();
	}
	$stmt->close();
	return $statusId;
}

// Tambah tugas baru
function addTask($conn, $userId, $title, $course, $dueDate) {
	$dueDateTime = $dueDate . ' 23:59:59';
	[$statusText] = computeStatus($dueDate, new DateTime('today'));
	
	$nextId = getNextTaskId($conn);
	$statusId = getStatusId($conn, $statusText);

	if ($statusId !== null) {
		$stmt = $conn->prepare('INSERT INTO Tugas (id_tugas, id_mahasiswa, id_status, namaTugas, matkulTugas, tenggatTugas) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('iiisss', $nextId, $userId, $statusId, $title, $course, $dueDateTime);
	} else {
		$stmt = $conn->prepare('INSERT INTO Tugas (id_tugas, id_mahasiswa, namaTugas, matkulTugas, tenggatTugas) VALUES (?, ?, ?, ?, ?)');
		$stmt->bind_param('iisss', $nextId, $userId, $title, $course, $dueDateTime);
	}
	$stmt->execute();
	$stmt->close();
}

// Hapus tugas
function deleteTask($conn, $taskId) {
	$stmt = $conn->prepare('DELETE FROM Tugas WHERE id_tugas = ?');
	$stmt->bind_param('i', $taskId);
	$stmt->execute();
	$stmt->close();
}

// Ambil semua tugas dan update status yang sudah berubah
function getAllTasks($conn) {
	$tasks = [];
	$stmt = $conn->prepare('SELECT t.id_tugas AS id, t.namaTugas AS title, t.matkulTugas AS course, t.tenggatTugas AS due_date, t.id_status AS current_status_id, s.status AS statusName FROM Tugas t LEFT JOIN StatusTugas s ON s.id_status = t.id_status ORDER BY t.tenggatTugas ASC');
	$stmt->execute();
	$result = $stmt->get_result();
	
	$today = new DateTime('today');
	while ($row = $result->fetch_assoc()) {
		// Hitung status terkini berdasarkan deadline
		[$actualStatusLabel] = computeStatus($row['due_date'], $today);
		
		// Cek apakah status di DB berbeda dengan status aktual
		if ($row['statusName'] !== $actualStatusLabel) {
			// Update status di database
			$newStatusId = getStatusId($conn, $actualStatusLabel);
			if ($newStatusId !== null) {
				$updateStmt = $conn->prepare('UPDATE Tugas SET id_status = ? WHERE id_tugas = ?');
				$updateStmt->bind_param('ii', $newStatusId, $row['id']);
				$updateStmt->execute();
				$updateStmt->close();
				
				// Update row data dengan status baru
				$row['current_status_id'] = $newStatusId;
				$row['statusName'] = $actualStatusLabel;
			}
		}
		
		$tasks[] = $row;
	}
	$stmt->close();
	return $tasks;
}

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

foreach ($tasks as $t) {
	[$label, $key] = computeStatus($t['due_date'], $today);
	if ($key === 'urgent') $countUrgent++;
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
	<link rel="stylesheet" href="/style/tugas.css">
</head>
<body>
	<div class="page">
		<aside class="sidebar">
			<div class="brand">S.</div>
			<nav class="side-nav">
				<a href="index.php" title="Beranda"><i class="fa-solid fa-house"></i></a>
				<a class="active" href="tugas.php" title="Tugas"><i class="fa-solid fa-list-check"></i></a>
				<a href="#" title="Kalender"><i class="fa-solid fa-calendar-days"></i></a>
				<a href="#" title="Setting"><i class="fa-solid fa-gear"></i></a>
			</nav>
			<div class="logout">
				<form action="logout.php" method="post">
					<button type="submit" class="icon-btn" title="Keluar"><i class="fa-solid fa-right-from-bracket"></i></button>
				</form>
			</div>
		</aside>

		<main class="content">
			<header class="content-header">
				<div>
					<h1>Dashboard Tugas</h1>
					<p>Pantau dan kelola deadline tugasmu!</p>
				</div>
				<a class="classroom-btn" href="#" onclick="alert('Integrasi Classroom belum tersedia');return false;">
					<i class="fa-solid fa-chalkboard-user"></i> Ambil dari Classroom
				</a>
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

