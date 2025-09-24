<?php
$page_title = 'About Daet';
$activePage = 'about';
include 'header.php';
?>
<!-- Reusable Image Modal -->
                <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0 text-center">
                    <img id="modalImage" src="" class="img-fluid rounded shadow" alt="Zoomed image">
                    </div>
                </div>
                </div>

             <!-- HERO SECTION -->
            <header class="about-header text-white text-center d-flex align-items-center justify-content-center"
            style="background: url(assets/img/aboutdaet.png); height: 40vh;">
            <div class="container px-4 px-lg-5">
                <h1 class="fw-bold display-4">SOCIO-ECONOMIC PROFILE OF DAET</h1>
            </div>
            </header>

          
<!-- INTRO SECTION -->
<section class="about-daet py-5" style="background-color: #fff;">
  <div class="container px-4 px-lg-5">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="about.php">Know Daet</a></li>
        <li class="breadcrumb-item active" aria-current="page">Socio-Economic Profile of Daet</li>
      </ol>
    </nav>

    <div class="row gx-5"> <!-- Added gx-5 for spacing between columns -->
      
      <!-- Left Column -->
      <div class="about-left col-lg-8">
       
        <p class="text-muted mb-5">
          Learn more about the history, culture, and governance of Daet.
        </p>

      </div>

        <!-- Socio-Economic Profile -->
    <div class="socio-economic-profile">

      <!-- Title -->
      <h2 class="fw-bold mb-4">Socio-Economic Profile of Daet</h2>

      <!-- Key Stats (Cards) -->
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <div class="card shadow-sm text-center h-100">
            <div class="card-body">
              <h3 class="fw-bold">111,700</h3>
              <p class="mb-0 text-muted">Population (2020)</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card shadow-sm text-center h-100">
            <div class="card-body">
              <h3 class="fw-bold">46</h3>
              <p class="mb-0 text-muted">Barangays</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card shadow-sm text-center h-100">
            <div class="card-body">
              <h3 class="fw-bold">44.3 km²</h3>
              <p class="mb-0 text-muted">Land Area</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card shadow-sm text-center h-100">
            <div class="card-body">
              <h3 class="fw-bold">₱X M</h3>
              <p class="mb-0 text-muted">Annual Income</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Demographics -->
      <div class="mb-5">
        <h3 class="fw-bold mb-3">Population & Demographics</h3>
        <p class="text-muted">
          Daet has a diverse population with varied age groups and household structures.
          It serves as the cultural and administrative hub of Camarines Norte.
        </p>

        <div class="row g-4">
          <div class="col-md-6">
            <canvas id="ageChart"></canvas>
          </div>
          <div class="col-md-6">
            <canvas id="genderChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Economy -->
      <div class="mb-5">
        <h3 class="fw-bold mb-3">Economy & Livelihood</h3>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="card shadow-sm p-3 h-100">
              <h5>Agriculture</h5>
              <p class="text-muted mb-0">Coconut, pineapple, rice, and root crops are among the major products of Daet.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card shadow-sm p-3 h-100">
              <h5>Commerce & Services</h5>
              <p class="text-muted mb-0">As the provincial capital, Daet is home to thriving businesses, trade, and service sectors.</p>
            </div>
          </div>
        </div>

        <!-- Economic Chart -->
        <div class="mt-4">
          <canvas id="economyChart"></canvas>
        </div>
      </div>

      <!-- Education & Infrastructure -->
<div class="mb-5">
  <h3 class="fw-bold mb-4">Education & Infrastructure</h3>

  <div class="row g-4">
    <!-- Education -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-mortarboard text-primary me-2"></i> Education</h5>
          <p class="text-muted">
            Daet is home to various educational institutions providing quality education from elementary to higher levels.
          </p>
          <ul class="list-unstyled mb-0">
            <li>🎓 <strong>Camarines Norte State College</strong> – leading higher education in the province</li>
            <li>🏫 <strong>Daet Central School</strong> – major public elementary school</li>
            <li>🏫 <strong>Private schools</strong> – such as La Consolacion College of Daet, Daet Parochial School</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Health -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-hospital text-danger me-2"></i> Health Facilities</h5>
          <p class="text-muted">
            The municipality is supported by hospitals and health centers ensuring access to healthcare services.
          </p>
          <ul class="list-unstyled mb-0">
            <li>🏥 <strong>Camarines Norte Provincial Hospital</strong> – main government hospital</li>
            <li>🏥 <strong>Our Lady of Lourdes Hospital</strong> – private healthcare facility</li>
            <li>🏥 <strong>Municipal Health Centers</strong> – serving barangays</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Transportation -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-bus-front text-success me-2"></i> Transportation</h5>
          <p class="text-muted">
            Daet is connected by land and air transportation, making it accessible to nearby towns and cities.
          </p>
          <ul class="list-unstyled mb-0">
            <li>🚌 Public utility jeepneys and tricycles for local mobility</li>
            <li>🚍 Bus terminals connecting Daet to Metro Manila and Bicol cities</li>
            <li>✈️ <strong>Daet Airport</strong> (for future rehabilitation)</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Utilities -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i> Utilities & Communication</h5>
          <p class="text-muted">
            Essential utilities and services support the daily needs of residents and businesses.
          </p>
          <ul class="list-unstyled mb-0">
            <li>⚡ <strong>Camarines Norte Electric Cooperative (CANORECO)</strong> – electricity provider</li>
            <li>💧 <strong>Daet Water District</strong> – potable water supply</li>
            <li>📱 Major telecom providers (Globe, Smart, PLDT) for communication and internet</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
   
    </div>
  </div>
</section>

<?php
$page_footer_scripts = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
HTML;
include 'footer.php';
?>