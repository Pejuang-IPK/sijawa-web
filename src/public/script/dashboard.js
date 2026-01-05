
let timeLeft = 58;
let timerId = null;
let isRunning = false;
let isAlarmEnabled = true;

const timerDisplay = document.getElementById("timer");
const playIcon = document.getElementById("icon-play-pause");
const audioMusic = document.getElementById("audio-music");
const audioAlarm = document.getElementById("audio-alarm");
const btnMusic = document.getElementById("btn-music");
const btnAlarm = document.getElementById("btn-alarm");

audioMusic.volume = 0.5;

function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m.toString().padStart(2, "0")} : ${s.toString().padStart(2, "0")}`;
}

function parseTimeFromDisplay(text) {

  const cleanText = text.replace(/[^0-9:]/g, "");

  let parts = cleanText.split(":");

  if (parts.length === 1) {
    return parseInt(parts[0]) * 60;
  }

  let m = parseInt(parts[0]) || 0;
  let s = parseInt(parts[1]) || 0;
  return m * 60 + s;
}

function startTimer() {
  if (timerId) return;

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

  timerDisplay.contentEditable = "true";
  timerDisplay.style.cursor = "pointer";
}

function timeIsUp() {
  clearInterval(timerId);
  timerId = null;
  isRunning = false;
  playIcon.classList.replace("fa-pause", "fa-play");
  timerDisplay.contentEditable = "true";

  if (isAlarmEnabled) audioAlarm.play();
}

function toggleTimer() {
  if (isRunning) {
    pauseTimer();
    playIcon.classList.replace("fa-pause", "fa-play");
    isRunning = false;
  } else {

    validateInput();
    startTimer();
    playIcon.classList.replace("fa-play", "fa-pause");
    isRunning = true;
  }
}

function pauseTimerManual() {
  if (isRunning) {
    toggleTimer();
  }
}

function validateInput() {
  let newTime = parseTimeFromDisplay(timerDisplay.innerText);

  if (newTime > 5999) newTime = 5999;
  if (newTime < 0) newTime = 0;

  timeLeft = newTime;
  timerDisplay.innerText = formatTime(timeLeft);
}

function handleEnter(e) {
  if (e.key === "Enter") {
    e.preventDefault();
    timerDisplay.blur();
  }
}

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

btnAlarm.classList.add("active");
