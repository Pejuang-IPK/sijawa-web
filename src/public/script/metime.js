function openLogModal(kegiatan, stress) {
  const modal = document.getElementById("logModal");

  if (modal) {
    // 1. Set Display Flex dulu agar elemen ada
    modal.style.display = "flex";

    // 2. Beri sedikit delay agar transisi opacity CSS berjalan (Fade In)
    setTimeout(() => {
      modal.classList.add("show");
    }, 10);

    // 3. Isi Data ke Form
    document.getElementById("input_kegiatan").value = kegiatan;
    document.getElementById("text_kegiatan").innerText = kegiatan;

    document.getElementById("input_stress").value = stress;
    document.getElementById("text_stress").innerText = stress + "%";
  }
}

function closeLogModal() {
  const modal = document.getElementById("logModal");

  if (modal) {
    // 1. Hapus class show (Fade Out)
    modal.classList.remove("show");

    // 2. Tunggu animasi selesai (300ms) baru hilangkan display
    setTimeout(() => {
      modal.style.display = "none";
    }, 300);
  }
}

// Tutup Modal jika klik di luar area putih (Background Gelap)
window.onclick = function (event) {
  const modal = document.getElementById("logModal");
  if (event.target == modal) {
    closeLogModal();
  }
};
