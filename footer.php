<footer class="site-footer bg-dark text-light pt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="assets/img/lgu logo.png" alt="LGU Logo" class="img-fluid" style="max-height: 60px;" />
          <img src="assets/img/md logo.png" alt="Maogmang Daet Logo" class="img-fluid" style="max-height: 60px;" />
        </div>
        <p class="fw-semibold mb-2">OFFICIAL WEBSITE OF THE MUNICIPALITY OF DAET</p>
        <p class="mb-3 text-secondary">
          About this website<br />
          Contact us at <a class="text-decoration-none text-white-50" href="mailto:info@lgudaet.gov.ph">info@lgudaet.gov.ph</a><br />
          iGovernance Team | Local Government Unit of Daet
        </p>
        <div class="d-flex gap-3">
          <a class="text-white-50" href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a class="text-white-50" href="#" aria-label="X (formerly Twitter)"><i class="fab fa-x-twitter"></i></a>
          <a class="text-white-50" href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          <a class="text-white-50" href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
          <a class="text-white-50" href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <h4 class="h6 text-uppercase fw-bold">Explore</h4>
        <ul class="list-unstyled text-secondary mb-0">
          <li><a class="text-decoration-none text-white-50" href="index.php">Home</a></li>
          <li><a class="text-decoration-none text-white-50" href="about.php">About</a></li>
          <li><a class="text-decoration-none text-white-50" href="citizens-charter.php">Citizen's Charter</a></li>
          <li><a class="text-decoration-none text-white-50" href="news_update.php">News &amp; Updates</a></li>
          <li><a class="text-decoration-none text-white-50" href="transparency.php">Transparency</a></li>
          <li><a class="text-decoration-none text-white-50" href="services.php">Services</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4 col-lg-3">
        <h4 class="h6 text-uppercase fw-bold">Citizen Services</h4>
        <ul class="list-unstyled text-secondary mb-0">
          <li><a class="text-decoration-none text-white-50" href="downloads.php">Downloads</a></li>
          <li><a class="text-decoration-none text-white-50" href="feedback.php">Feedback Form</a></li>
          <li><a class="text-decoration-none text-white-50" href="citizens-charter.php">Citizen's Charter</a></li>
          <li><a class="text-decoration-none text-white-50" href="emergency-contacts.php">Emergency Contacts</a></li>
          <li><a class="text-decoration-none text-white-50" href="privacy-policy.php">Privacy Policy</a></li>
          <li><a class="text-decoration-none text-white-50" href="terms.php">Terms of Use</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <h4 class="h6 text-uppercase fw-bold">Government</h4>
        <ul class="list-unstyled text-secondary mb-0">
          <li><a class="text-decoration-none text-white-50" href="https://dict.gov.ph" target="_blank" rel="noopener">Department of Information and Communications Technology</a></li>
          <li><a class="text-decoration-none text-white-50" href="https://www.dilg.gov.ph" target="_blank" rel="noopener">Department of the Interior and Local Government</a></li>
          <li><a class="text-decoration-none text-white-50" href="https://www.gov.ph" target="_blank" rel="noopener">GOV.PH</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4 col-lg-1 d-flex align-items-start">
        <a href="transparency.php" class="d-inline-block">
          <img src="assets/img/transparency-seal.svg" alt="Transparency Seal – Full Disclosure Portal" class="img-fluid" style="max-height: 120px;" />
        </a>
      </div>
    </div>
    <div class="border-top border-secondary-subtle mt-5 pt-3 small text-secondary d-flex flex-column flex-lg-row justify-content-lg-between gap-2">
      <div>
        REPUBLIC OF THE PHILIPPINES — All content is in the public domain unless otherwise stated.
      </div>
      <div class="d-flex flex-wrap gap-3">
        <a class="text-decoration-none text-white-50" href="privacy-policy.php">Privacy Policy</a>
        <a class="text-decoration-none text-white-50" href="terms.php">Terms of Use</a>
        <a class="text-decoration-none text-white-50" href="downloads.php">Downloads</a>
        <a class="text-decoration-none text-white-50" href="feedback.php">Feedback</a>
        <a class="text-decoration-none text-white-50" href="emergency-contacts.php">Emergency Contacts</a>
      </div>
    </div>
  </div>
</footer>

<button id="backToTop" class="btn btn-primary rounded-circle shadow" aria-label="Back to top">
  <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="js/three-loader.js" type="module"></script>
<script>
  const backToTopBtn = document.getElementById('backToTop');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTopBtn.classList.add('show');
    } else {
      backToTopBtn.classList.remove('show');
    }
  });

  backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
<?php
  if (!empty($page_footer_scripts)) {
    echo $page_footer_scripts . "\n";
  }
?>
</body>
</html>