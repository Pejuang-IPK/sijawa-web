<?php

// Helper: sanitize string
function clean($val) {
	return trim(htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'));
}

// Helper: escape untuk query SQL
function esc($conn, $val) {
	return $conn->real_escape_string($val ?? '');
}

// Hitung status berdasarkan tenggat
function computeStatus($dueDateStr, $today) {
	$due = new DateTime($dueDateStr);
	$now = new DateTime();

	// Jika sudah melewati waktu tenggat (termasuk jam)
	if ($due < $now) return ['Terlewat', 'overdue'];

	// Hitung selisih hari dari hari ini (tanpa jam)
	$dueDate = DateTime::createFromFormat('Y-m-d', $due->format('Y-m-d'));
	$todayDate = DateTime::createFromFormat('Y-m-d', $today->format('Y-m-d'));
	$diffDays = (int)ceil(($dueDate->getTimestamp() - $todayDate->getTimestamp()) / 86400);

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
	$statusLabelEsc = esc($conn, $statusLabel);
	$statusId = null;
	$res = $conn->query("SELECT id_status FROM StatusTugas WHERE status = '" . $statusLabelEsc . "' LIMIT 1");
	if ($res && $row = $res->fetch_assoc()) {
		$statusId = (int)$row['id_status'];
		$res->free();
	} else {
		$nextStatusId = 1;
		if ($res2 = $conn->query('SELECT COALESCE(MAX(id_status),0)+1 AS next_id FROM StatusTugas')) {
			if ($row2 = $res2->fetch_assoc()) $nextStatusId = (int)$row2['next_id'];
			$res2->free();
		}
		$conn->query("INSERT INTO StatusTugas (id_status, status) VALUES (" . (int)$nextStatusId . ", '" . $statusLabelEsc . "')");
		$statusId = $nextStatusId;
	}
	return $statusId;
}

// Tambah tugas baru
function addTask($conn, $userId, $title, $course, $dueDate, $dueTime = '23:59') {
	$dueDateTime = $dueDate . ' ' . $dueTime . ':00';
	[$statusText] = computeStatus($dueDateTime, new DateTime('today'));

	$nextId = getNextTaskId($conn);
	$statusId = getStatusId($conn, $statusText);

	$titleEsc = esc($conn, $title);
	$courseEsc = esc($conn, $course);
	$dueEsc = esc($conn, $dueDateTime);

	if ($statusId !== null) {
		$conn->query("INSERT INTO Tugas (id_tugas, id_mahasiswa, id_status, namaTugas, matkulTugas, tenggatTugas) VALUES (" . (int)$nextId . ", " . (int)$userId . ", " . (int)$statusId . ", '" . $titleEsc . "', '" . $courseEsc . "', '" . $dueEsc . "')");
	} else {
		$conn->query("INSERT INTO Tugas (id_tugas, id_mahasiswa, namaTugas, matkulTugas, tenggatTugas) VALUES (" . (int)$nextId . ", " . (int)$userId . ", '" . $titleEsc . "', '" . $courseEsc . "', '" . $dueEsc . "')");
	}
}

// Hapus tugas
function deleteTask($conn, $taskId) {
	$conn->query('DELETE FROM Tugas WHERE id_tugas = ' . (int)$taskId);
}

// Ambil semua tugas dan update status yang sudah berubah
function getAllTasks($conn) {
	$tasks = [];
	$result = $conn->query('SELECT t.id_tugas AS id, t.namaTugas AS title, t.matkulTugas AS course, t.tenggatTugas AS due_date, t.id_status AS current_status_id, s.status AS statusName FROM Tugas t LEFT JOIN StatusTugas s ON s.id_status = t.id_status ORDER BY t.tenggatTugas ASC');

	$today = new DateTime('today');
	while ($row = $result->fetch_assoc()) {
		[$actualStatusLabel] = computeStatus($row['due_date'], $today);

		if ($row['statusName'] !== $actualStatusLabel) {
			$newStatusId = getStatusId($conn, $actualStatusLabel);
			if ($newStatusId !== null) {
				$conn->query('UPDATE Tugas SET id_status = ' . (int)$newStatusId . ' WHERE id_tugas = ' . (int)$row['id']);

				$row['current_status_id'] = $newStatusId;
				$row['statusName'] = $actualStatusLabel;
			}
		}

		$tasks[] = $row;
	}
	$result->free();
	return $tasks;
}

?>
