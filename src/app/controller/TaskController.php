<?php

function clean($val) {
	return trim(htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'));
}

function esc($conn, $val) {
	return $conn->real_escape_string($val ?? '');
}

function computeStatus($dueDateStr, $today) {
	$due = new DateTime($dueDateStr);
	$now = new DateTime();

	if ($due < $now) return ['Terlewat', 'overdue'];

	$dueDate = DateTime::createFromFormat('Y-m-d', $due->format('Y-m-d'));
	$todayDate = DateTime::createFromFormat('Y-m-d', $today->format('Y-m-d'));
	$diffDays = (int)ceil(($dueDate->getTimestamp() - $todayDate->getTimestamp()) / 86400);

	if ($diffDays <= 1) return ['Harus Dikerjakan', 'urgent'];
	if ($diffDays <= 3) return ['Tolong Dikerjakan', 'approaching'];
	return ['Masih Bisa Ditunda', 'safe'];
}

function getNextTaskId($conn) {
	$nextId = 1;
	if ($res = $conn->query('SELECT COALESCE(MAX(id_tugas),0)+1 AS next_id FROM Tugas')) {
		if ($row = $res->fetch_assoc()) $nextId = (int)$row['next_id'];
		$res->free();
	}
	return $nextId;
}

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

function deleteTask($conn, $taskId) {
	$conn->query('DELETE FROM Tugas WHERE id_tugas = ' . (int)$taskId);
}

function getAllTasks($conn, $userId) {
	$tasks = [];
	$result = $conn->query('SELECT t.id_tugas AS id, t.namaTugas AS title, t.matkulTugas AS course, t.tenggatTugas AS due_date, t.id_status AS current_status_id, s.status AS statusName FROM Tugas t LEFT JOIN StatusTugas s ON s.id_status = t.id_status WHERE t.id_mahasiswa = ' . (int)$userId . ' ORDER BY t.tenggatTugas ASC');

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
