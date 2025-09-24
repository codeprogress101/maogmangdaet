<footer class="site-footer">
      <div class="footer-container">
        <div class="footer-col">
          <img src="assets/img/lgu logo.png" alt="LGU Logo" class="footer-logo" />
          <img src="assets/img/md logo.png" alt="Maogmang Daet Logo" class="footer-logo" />
          <p>
            <strong>OFFICIAL WEBSITE OF THE<br />MUNICIPALITY OF DAET</strong>
          </p>
          <p>
            About this website<br />
            Contact us at
            <a href="mailto:info@lgudaet.gov.ph">info@lgudaet.gov.ph</a><br />
            iGovernance Team | Local Government Unit of Daet
          </p>
          <div class="social-icons">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="X (formerly Twitter)"><i class="fab fa-x-twitter"></i></a>
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          </div>
        </div>

        <div class="footer-col">
          <h4>Municipal Government Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Resident</a></li>
            <li><a href="#">Visitor</a></li>
            <li><a href="#">Investor</a></li>
            <li><a href="#">Supplier</a></li>
            <li><a href="#">Student</a></li>
            <li><a href="#">Municipal Officials</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Government Links</h4>
          <ul>
            <li><a href="#">Office of the President</a></li>
            <li><a href="#">Office of the Vice President</a></li>
            <li><a href="#">Senate of the Philippines</a></li>
            <li><a href="#">House of Representatives</a></li>
            <li><a href="#">Supreme Court</a></li>
            <li><a href="#">Court of Appeals</a></li>
            <li><a href="#">Sandiganbayan</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>About GOVPH</h4>
          <p>
            Learn more about the Philippine government, its structure, how
            government works and the people behind it.
          </p>
          <ul>
            <li><a href="#">Open Data Portal</a></li>
            <li><a href="#">Official Gazette</a></li>
          </ul>
        </div>

        <div class="footer-col footer-logos">
          <div class="footer-logo-box">
            <img
              src="assets/img/dpstatement1.png"
              alt="Data Privacy Office"
              class="footer-badge"
            />
            <p>
              <a href="#">Data Privacy Policy</a><br />
              <a href="#">Terms and Conditions</a>
            </p>
          </div>
          <div class="footer-logo-box">
            <img
              src="assets/img/coa-footerv2.svg"
              alt="Commission on Audit"
              class="footer-badge"
            />
            <p><strong>Republic of the Philippines</strong></p>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>
          REPUBLIC OF THE PHILIPPINES — All content is in the public domain
          unless otherwise stated.
        </p>
        <p>
          <a href="#">Data Privacy Policy</a> |
          <a href="#">Terms and Conditions</a>
        </p>
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