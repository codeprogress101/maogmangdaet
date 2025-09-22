// Play/pause video when visible
const video = document.getElementById("tourismVideo");
if (video) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        video.play();
      } else {
        video.pause();
      }
    });
  }, { threshold: 0.5 });
  observer.observe(video);
}

// Toggle mute/unmute with floating button
const toggleSoundBtn = document.getElementById("toggleSound");
if (video && toggleSoundBtn) {
  toggleSoundBtn.addEventListener("click", () => {
    if (video.muted) {
      video.muted = false;
      toggleSoundBtn.textContent = "🔇";
    } else {
      video.muted = true;
      toggleSoundBtn.textContent = "🔊";
    }
  });
}
