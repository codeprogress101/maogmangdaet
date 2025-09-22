document.addEventListener("DOMContentLoaded", () => {
  const navbar = document.getElementById("mainNav");
  const header = document.getElementById("hero");
  const intro = document.getElementById("intro");

  function adjustIntroHeight() {
    if (navbar && header && intro) {
      const navHeight = navbar.offsetHeight;
      const headerHeight = header.offsetHeight;
      const total = navHeight + headerHeight;
      const remaining = window.innerHeight - total;
      intro.style.minHeight = remaining + "px";
    }
  }

  adjustIntroHeight();
  window.addEventListener("resize", adjustIntroHeight);
  window.addEventListener("load", adjustIntroHeight);
});