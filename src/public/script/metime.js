function openLogModal(kegiatan, stress) {
  const modal = document.getElementById("logModal");

  if (modal) {

    modal.style.display = "flex";

    setTimeout(() => {
      modal.classList.add("show");
    }, 10);

    document.getElementById("input_kegiatan").value = kegiatan;
    document.getElementById("text_kegiatan").innerText = kegiatan;

    document.getElementById("input_stress").value = stress;
    document.getElementById("text_stress").innerText = stress + "%";
  }
}

function closeLogModal() {
  const modal = document.getElementById("logModal");

  if (modal) {

    modal.classList.remove("show");

    setTimeout(() => {
      modal.style.display = "none";
    }, 300);
  }
}

window.onclick = function (event) {
  const modal = document.getElementById("logModal");
  if (event.target == modal) {
    closeLogModal();
  }
};

document.addEventListener('DOMContentLoaded', function() {
  const alert = document.querySelector('.alert');
  if (alert) {
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => {
        alert.remove();
      }, 300);
    }, 4000);
  }
});
