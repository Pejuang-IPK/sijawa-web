// Smooth Scroll sederhana
function scrollToFeatures() {
  document.getElementById("features").scrollIntoView({ behavior: "smooth" });
}

// Fungsi Navigasi Mockup sesuai Flowchart
// Flow: Landing Page -> Check Account -> Login OR Register

// Fungsi Slider Fitur
function scrollSlider(direction) {
  const slider = document.getElementById("featureSlider");
  const cardWidth = 300 + 25; // Lebar kartu + gap
  const scrollAmount = direction * cardWidth;

  slider.scrollBy({
    left: scrollAmount,
    behavior: "smooth",
  });
}

// Opsional: Sembunyikan tombol navigasi jika di awal/akhir (UX Enhancement)
const slider = document.getElementById("featureSlider");
const prevBtn = document.querySelector(".prev-btn");
const nextBtn = document.querySelector(".next-btn");

slider.addEventListener("scroll", () => {
  // Sembunyikan tombol kiri jika scroll paling kiri
  if (slider.scrollLeft <= 0) {
    prevBtn.style.opacity = "0.5";
    prevBtn.style.pointerEvents = "none";
  } else {
    prevBtn.style.opacity = "1";
    prevBtn.style.pointerEvents = "all";
  }

  // Sembunyikan tombol kanan jika scroll mentok kanan
  if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 10) {
    nextBtn.style.opacity = "0.5";
    nextBtn.style.pointerEvents = "none";
  } else {
    nextBtn.style.opacity = "1";
    nextBtn.style.pointerEvents = "all";
  }
});

// Panggil sekali saat load untuk set state awal tombol
slider.dispatchEvent(new Event("scroll"));
