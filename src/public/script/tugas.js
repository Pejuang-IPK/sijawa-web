
let currentDeleteId = null;

function showDeleteModal(taskId, taskTitle) {
	currentDeleteId = taskId;
	document.getElementById('deleteModal').style.display = 'flex';
	document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
	document.getElementById('deleteModal').style.display = 'none';
	document.body.style.overflow = 'auto';
	currentDeleteId = null;
}

function confirmDelete() {
	if (currentDeleteId) {
		document.getElementById('deleteTaskId').value = currentDeleteId;
		document.getElementById('deleteForm').submit();
	}
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeDeleteModal();
	}
});

document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeDeleteModal();
	}
});

document.addEventListener('DOMContentLoaded', function() {
	const alert = document.querySelector('.alert');
	if (alert) {
		setTimeout(function() {
			alert.style.opacity = '0';
			alert.style.transition = 'opacity 0.3s ease';
			setTimeout(function() {
				alert.remove();
			}, 300);
		}, 4000);
	}
});
