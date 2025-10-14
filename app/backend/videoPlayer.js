document.addEventListener("DOMContentLoaded", () => {
  const video = document.querySelector("video");
  const playButton = document.getElementById("play-pause");
  const progressBar = document.querySelector(".progress-bar");
  const volumeButton = document.getElementById("volume");
  const fullscreenButton = document.getElementById("fullscreen");
  const volumeControl = document.querySelector(".volume-control");
  const volumeSlider = document.querySelector(".volume-slider input");
  const volumeControlContainer = document.querySelector(".volume-control");
  let previousVolume = parseFloat(localStorage.getItem("previousVolume")) || 1;
  let hideCursorTimeout;
  let hideControlsTimeout;

  // Load saved volume
  const savedVolume = parseFloat(localStorage.getItem("volume")) || 1;
  video.volume = savedVolume;
  volumeSlider.value = savedVolume;
  updateVolumeIcon(video.volume);

  video.addEventListener("volumechange", () => {
    localStorage.setItem("volume", video.volume);
  });

  // Play/Pause functionality
  playButton.addEventListener("click", togglePlayPause);
  video.addEventListener("click", togglePlayPause);

  function togglePlayPause() {
    if (video.paused) {
      video
        .play()
        .then(() => {
          playButton.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M555-200v-560h175v560H555Zm-325 0v-560h175v560H230Z"/></svg>';
        })
        .catch((e) => {
          console.warn("Playback failed:", e);
        });
    } else {
      video.pause();
      playButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M320-203v-560l440 280-440 280Z"/></svg>';
    }
  }

  // Progress bar
  video.addEventListener("timeupdate", () => {
    const progress = (video.currentTime / video.duration) * 100;
    progressBar.style.setProperty("--progress", `${progress}%`);
  });

  progressBar.addEventListener("click", (e) => {
    const rect = progressBar.getBoundingClientRect();
    const pos = (e.clientX - rect.left) / rect.width;
    video.currentTime = pos * video.duration;
  });

  // Volume controls
  volumeButton.addEventListener("click", (e) => {
    if (video.volume > 0) {
      previousVolume = video.volume;
      localStorage.setItem("previousVolume", previousVolume);
      video.volume = 0;
    } else {
      video.volume = previousVolume;
    }
    updateVolumeIcon(video.volume);
  });

  volumeSlider.addEventListener("input", (e) => {
    const volume = parseFloat(e.target.value);
    video.volume = volume;
    updateVolumeIcon(volume);
  });

  function updateVolumeIcon(volume) {
    if (volume === 0) {
      volumeButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M813-56 681-188q-28 20-60.5 34.5T553-131v-62q23-7 44.5-15.5T638-231L473-397v237L273-360H113v-240h156L49-820l43-43 764 763-43 44Zm-36-232-43-43q20-34 29.5-72t9.5-78q0-103-60-184.5T553-769v-62q124 28 202 125.5T833-481q0 51-14 100t-42 93ZM643-422l-90-90v-130q47 22 73.5 66t26.5 96q0 15-2.5 29.5T643-422ZM473-592 369-696l104-104v208Z"/></svg>';
    } else if (volume < 0.5) {
      volumeButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M200-360v-240h160l200-200v640L360-360H200Zm420 48v-337q54 17 87 64t33 105q0 59-33 105t-87 63Z"/></svg>';
    } else {
      volumeButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M560-131v-62q97-28 158.5-107.5T780-481q0-101-61-181T560-769v-62q124 28 202 125.5T840-481q0 127-78 224.5T560-131ZM120-360v-240h160l200-200v640L280-360H120Zm420 48v-337q55 17 87.5 64T660-480q0 57-33 104t-87 64Z"/></svg>';
    }
  }

  // Cursor & Controls visibility logic
  const wrapper = document.querySelector(".video-wrapper");
  const controls = wrapper.querySelector(".controls");

  function resetCursorAndControls() {
    // Show cursor
    wrapper.classList.add("show-cursor");

    // Show controls
    controls.style.opacity = "1";
    controls.style.transform = "translateY(0)";

    // Clear timeouts
    clearTimeout(hideCursorTimeout);
    clearTimeout(hideControlsTimeout);

    // Schedule hiding after inactivity
    hideCursorTimeout = setTimeout(() => {
      wrapper.classList.remove("show-cursor");
    }, 2000);

    hideControlsTimeout = setTimeout(() => {
      controls.style.opacity = "0";
      controls.style.transform = "translateY(10px)";
    }, 2000);
  }

  function hideControlsAndCursor() {
    wrapper.classList.remove("show-cursor");
    controls.style.opacity = "0";
    controls.style.transform = "translateY(10px)";
    clearTimeout(hideCursorTimeout);
    clearTimeout(hideControlsTimeout);
  }

  // Mouse move -> show cursor and controls
  wrapper.addEventListener("mousemove", () => {
    resetCursorAndControls();
  });

  wrapper.addEventListener("click", () => {
    resetCursorAndControls();
  });

  wrapper.addEventListener("mouseleave", () => {
    resetCursorAndControls(); // will trigger hide after timeout
  });

  // Keep controls visible while hovering over them
  controls.addEventListener("mouseenter", () => {
    clearTimeout(hideCursorTimeout);
    clearTimeout(hideControlsTimeout);
  });

  controls.addEventListener("mouseleave", () => {
    resetCursorAndControls();
  });

  // Hide controls when mouse leaves the browser window
  document.addEventListener("mouseleave", (e) => {
    if (
      !document.fullscreenElement ||
      !document.body.classList.contains("fullscreen")
    ) {
      hideControlsAndCursor();
    }
  });

  // Also hide on tab/window blur
  window.addEventListener("blur", () => {
    hideControlsAndCursor();
  });

  // Fullscreen handling
  fullscreenButton.addEventListener("click", toggleFullscreen);

  function toggleFullscreen() {
    const elem = document.documentElement;
    if (!document.fullscreenElement) {
      elem.requestFullscreen().catch(console.error);
      document.body.classList.add("fullscreen");
      fullscreenButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M253-120v-133H120v-60h193v193h-60Zm394 0v-193h193v60H707v133h-60ZM120-647v-60h133v-133h60v193H120Zm527 0v-193h60v133h133v60H647Z"/></svg>'; // Exit icon
    } else {
      document.exitFullscreen().catch(console.error);
      document.body.classList.remove("fullscreen");
      fullscreenButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M120-120v-193h60v133h133v60H120Zm527 0v-60h133v-133h60v193H647ZM120-647v-193h193v60H180v133h-60Zm660 0v-133H647v-60h193v193h-60Z"/></svg>'; // Enter icon
    }
  }

  // Exit fullscreen with ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && document.fullscreenElement) {
      document.exitFullscreen();
      document.body.classList.remove("fullscreen");
    }
  });

  // Optional: Listen for system fullscreen change events
  document.addEventListener("fullscreenchange", () => {
    if (!document.fullscreenElement) {
      document.body.classList.remove("fullscreen");
      fullscreenButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M120-120v-193h60v133h133v60H120Zm527 0v-60h133v-133h60v193H647ZM120-647v-193h193v60H180v133h-60Zm660 0v-133H647v-60h193v193h-60Z"/></svg>';
    }
  });

  // Double click to toggle fullscreen
  wrapper.addEventListener("dblclick", () => {
    if (!document.fullscreenElement) {
      wrapper.requestFullscreen().catch(console.error);
      document.body.classList.add("fullscreen");
      fullscreenButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M253-120v-133H120v-60h193v193h-60Zm394 0v-193h193v60H707v133h-60ZM120-647v-60h133v-133h60v193H120Zm527 0v-193h60v133h133v60H647Z"/></svg>';
    } else {
      document.exitFullscreen().catch(console.error);
      document.body.classList.remove("fullscreen");
      fullscreenButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M120-120v-193h60v133h133v60H120Zm527 0v-60h133v-133h60v193H647ZM120-647v-193h193v60H180v133h-60Zm660 0v-133H647v-60h193v193h-60Z"/></svg>';
    }
  });

  // Volume hover behavior
  volumeControlContainer.addEventListener("mouseenter", () => {
    volumeControl.classList.add("visible");
  });

  volumeControlContainer.addEventListener("mouseleave", () => {
    setTimeout(() => {
      if (!volumeSlider.matches(":focus")) {
        volumeControl.classList.remove("visible");
      }
    }, 3000);
  });

  // Buffer bar
  function updateBufferBar() {
    const duration = video.duration;
    if (isNaN(duration)) return;

    const bufferBar = document.querySelector(".progress-bar .buffer-bar");
    if (!bufferBar) {
      console.error("Buffer bar element not found!");
      return;
    }

    bufferBar.innerHTML = "";

    const ranges = video.buffered;
    for (let i = 0; i < ranges.length; i++) {
      const start = ranges.start(i);
      const end = ranges.end(i);
      const percentStart = (start / duration) * 100;
      const percentEnd = (end / duration) * 100;

      const bufferSegment = document.createElement("div");
      bufferSegment.style.left = `${percentStart}%`;
      bufferSegment.style.width = `${percentEnd - percentStart}%`;

      bufferBar.appendChild(bufferSegment);
    }
  }

  video.addEventListener("loadedmetadata", updateBufferBar);
  setInterval(updateBufferBar, 1000); // Update every second

  // Autoplay when enough has buffered
  video.muted = true;

  video.addEventListener("canplaythrough", () => {
    if (!video.paused) return;
    togglePlayPause(); // Uses same logic as user click
  });

  // Optional: Unmute after play starts
  video.addEventListener("play", () => {
    video.muted = false;
  });
});
