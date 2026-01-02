function openModal(modalId) {
  const id = modalId || "addModal";
  const modal = document.getElementById(id);

  if (modal) {
    modal.style.display = "flex";
  } else {
    console.error("Error: Modal dengan ID '" + id + "' tidak ditemukan.");
  }
}

// --- FUNGSI TUTUP MODAL ---
function closeModal(modalId) {
  // Jika ID tidak dikirim, coba cari modal yang sedang terbuka (display: flex)
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

// --- FUNGSI ISI FORM EDIT OTOMATIS ---
// Saya tambahkan parameter 'sks' karena di database sudah kita tambah
function openEditModal(id, hari, matkul, ruang, sks, mulai, selesai, dosen) {
  // Pastikan ID element HTML di modal_edit.php sesuai dengan ini:
  document.getElementById("edit_id").value = id;

  // Set dropdown hari (auto select)
  document.getElementById("edit_hari").value = hari;

  document.getElementById("edit_matkul").value = matkul;
  document.getElementById("edit_ruangan").value = ruang;
  document.getElementById("edit_sks").value = sks; // Tambahan SKS
  document.getElementById("edit_mulai").value = mulai;
  document.getElementById("edit_selesai").value = selesai;
  document.getElementById("edit_dosen").value = dosen;

  // Buka Modal Edit
  openModal("editModal");
}

function openDeleteModal(id) {
  // 1. Ambil elemen tombol "Ya, Hapus" di dalam modal
  const deleteBtn = document.getElementById("confirmDeleteBtn");

  // 2. Ubah link href-nya agar membawa ID yang benar
  // Sesuaikan path '../controllers/' jika struktur foldernya berbeda
  deleteBtn.href = "kalender.php?hapus=" + id;

  // 3. Tampilkan Modal
  openModal("deleteModal");
}

// --- LOGIKA KLIK DI LUAR MODAL (OVERLAY) ---
window.onclick = function (event) {
  // Cek apakah elemen yang diklik memiliki class "modal-overlay"
  // Class ini ada di pembungkus hitam transparan
  if (event.target.classList.contains("modal-overlay")) {
    // Tutup elemen tersebut (apapun ID-nya, entah addModal atau editModal)
    event.target.style.display = "none";
  }
};
