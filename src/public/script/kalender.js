function openModal(modalId) {
  const id = modalId || "addModal";
  const modal = document.getElementById(id);

  if (modal) {
    modal.style.display = "flex";
  } else {
    console.error("Error: Modal dengan ID '" + id + "' tidak ditemukan.");
  }
}

function closeModal(modalId) {

  if (!modalId) {
    const modals = document.querySelectorAll(".modal-overlay");
    modals.forEach((m) => (m.style.display = "none"));
    return;
  }

  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = "none";
  }
}

function openEditModal(id, hari, matkul, ruang, sks, mulai, selesai, dosen) {

  document.getElementById("edit_id").value = id;

  document.getElementById("edit_hari").value = hari;

  document.getElementById("edit_matkul").value = matkul;
  document.getElementById("edit_ruangan").value = ruang;
  document.getElementById("edit_sks").value = sks;
  document.getElementById("edit_mulai").value = mulai;
  document.getElementById("edit_selesai").value = selesai;
  document.getElementById("edit_dosen").value = dosen;

  openModal("editModal");
}

function openDeleteModal(id) {

  const deleteBtn = document.getElementById("confirmDeleteBtn");

  deleteBtn.href = "kalender.php?hapus=" + id;

  openModal("deleteModal");
}

window.onclick = function (event) {

  if (event.target.classList.contains("modal-overlay")) {

    event.target.style.display = "none";
  }
};
