// --- KONFIGURASI AWAL ---
let timeLeft = 58;
let timerId = null;
let isRunning = false;
let isAlarmEnabled = true;

// --- ELEMENT HTML ---
const timerDisplay = document.getElementById("timer");
const playIcon = document.getElementById("icon-play-pause");
const audioMusic = document.getElementById("audio-music");
const audioAlarm = document.getElementById("audio-alarm");
const btnMusic = document.getElementById("btn-music");
const btnAlarm = document.getElementById("btn-alarm");

audioMusic.volume = 0.5;

// --- 1. FORMAT & PARSING WAKTU ---

// Mengubah detik ke format "MM : SS"
function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m.toString().padStart(2, "0")} : ${s.toString().padStart(2, "0")}`;
}

// Mengubah string "MM : SS" (input user) kembali menjadi detik
function parseTimeFromDisplay(text) {
  // Hapus semua karakter selain angka dan titik dua
  const cleanText = text.replace(/[^0-9:]/g, "");

  let parts = cleanText.split(":");

  // Jika user cuma ketik angka (misal "5"), anggap menit
  if (parts.length === 1) {
    return parseInt(parts[0]) * 60;
  }

  let m = parseInt(parts[0]) || 0;
  let s = parseInt(parts[1]) || 0;
  return m * 60 + s;
}

// --- 2. CORE TIMER LOGIC ---

function startTimer() {
  if (timerId) return;

  // Matikan mode edit saat timer jalan
  timerDisplay.contentEditable = "false";
  timerDisplay.style.cursor = "default";

  timerId = setInterval(() => {
    if (timeLeft > 0) {
      timeLeft--;
      timerDisplay.innerText = formatTime(timeLeft);
    } else {
      timeIsUp();
    }
  }, 1000);
}

function pauseTimer() {
  clearInterval(timerId);
  timerId = null;

  // Aktifkan mode edit saat pause
  timerDisplay.contentEditable = "true";
  timerDisplay.style.cursor = "pointer";
}

function timeIsUp() {
  clearInterval(timerId);
  timerId = null;
  isRunning = false;
  playIcon.classList.replace("fa-pause", "fa-play");
  timerDisplay.contentEditable = "true"; // Izinkan edit lagi

  if (isAlarmEnabled) audioAlarm.play();
}

function toggleTimer() {
  if (isRunning) {
    pauseTimer();
    playIcon.classList.replace("fa-pause", "fa-play");
    isRunning = false;
  } else {
    // Sebelum start, pastikan validasi input user terakhir
    validateInput();
    startTimer();
    playIcon.classList.replace("fa-play", "fa-pause");
    isRunning = true;
  }
}

// --- 3. FITUR EDIT (Ubah Waktu) ---

// Dipanggil saat user klik angka (onfocus)
function pauseTimerManual() {
  if (isRunning) {
    toggleTimer(); // Otomatis pause jika user klik angka
  }
}

// Dipanggil saat user klik di luar angka (onblur)
function validateInput() {
  let newTime = parseTimeFromDisplay(timerDisplay.innerText);

  // Batas maksimal waktu (misal 99 menit)
  if (newTime > 5999) newTime = 5999;
  if (newTime < 0) newTime = 0;

  timeLeft = newTime;
  timerDisplay.innerText = formatTime(timeLeft); // Rapikan formatnya
}

// Agar tombol Enter berfungsi sebagai "Selesai Edit"
function handleEnter(e) {
  if (e.key === "Enter") {
    e.preventDefault(); // Jangan buat baris baru
    timerDisplay.blur(); // Lepas fokus (memicu validateInput)
  }
}

// --- 4. AUDIO & ALARM (Tetap sama) ---
function toggleMusic() {
  if (audioMusic.paused) {
    audioMusic.play();
    btnMusic.classList.add("active");
  } else {
    audioMusic.pause();
    btnMusic.classList.remove("active");
  }
}

function toggleAlarm() {
  isAlarmEnabled = !isAlarmEnabled;
  if (isAlarmEnabled) {
    btnAlarm.classList.add("active");
    btnAlarm.querySelector("i").classList.replace("fa-bell-slash", "fa-bell");
  } else {
    btnAlarm.classList.remove("active");
    btnAlarm.querySelector("i").classList.replace("fa-bell", "fa-bell-slash");
  }
}

// --- INISIALISASI ---
// startTimer(); // Mulai otomatis
btnAlarm.classList.add("active");
