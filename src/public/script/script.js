
function scrollToFeatures() {
  document.getElementById("features").scrollIntoView({ behavior: "smooth" });
}

function handleNavigation(action) {
  if (action === "login") {

    window.location.href = "login.php";
  } else if (action === "register") {
    window.location.href = "register.php";
  }
}

function scrollSlider(direction) {
  const slider = document.getElementById("featureSlider");
  const cardWidth = 300 + 25;
  const scrollAmount = direction * cardWidth;

  slider.scrollBy({
    left: scrollAmount,
    behavior: "smooth",
  });
}

const slider = document.getElementById("featureSlider");
const prevBtn = document.querySelector(".prev-btn");
const nextBtn = document.querySelector(".next-btn");

slider.addEventListener("scroll", () => {

  if (slider.scrollLeft <= 0) {
    prevBtn.style.opacity = "0.5";
    prevBtn.style.pointerEvents = "none";
  } else {
    prevBtn.style.opacity = "1";
    prevBtn.style.pointerEvents = "all";
  }

  if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 10) {
    nextBtn.style.opacity = "0.5";
    nextBtn.style.pointerEvents = "none";
  } else {
    nextBtn.style.opacity = "1";
    nextBtn.style.pointerEvents = "all";
  }
});

slider.dispatchEvent(new Event("scroll"));
