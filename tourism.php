<?php
$page_title = 'Tourism of Daet';
$page_head_includes = <<<HTML
    <script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer/dist/model-viewer.min.js"></script>
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/3.15.0/mapbox-gl.css"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.1/mapbox-gl-directions.css"
  />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/3.15.0/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.1/mapbox-gl-directions.js"></script>
HTML;
$activePage = 'tourism';
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
            style="background: url(assets/img/aboutdaet.png); height: 40vh;" id="hero">
            <div class="container px-4 px-lg-5">
                <h1 class="fw-bold display-4">WELCOME TO MAOGMANG DAET!</h1>
            </div>
            </header>

            <!-- INSERT CODE HERE -->

            <!-- Intro Section with 6 Cards -->
<!-- Intro Section with Clickable Cards -->

 <section class="py-5">
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-lg-10 text-center">
                    <h2 class="display-5 fw-semibold">Explore the Bantayog Monument</h2>
                    <p class="lead text-muted mb-4">
                      Immerse yourself in the historic Bantayog Monument in rich 3D—rotate, zoom, and step into augmented reality.
                    </p>

                    <model-viewer
                      id="bantayog-viewer"
                      src="assets/3d/bantayog.glb"
                      camera-controls
                      auto-rotate
                      shadow-intensity="1"
                      shadow-softness="0.4"
                      exposure="0.50"
                      tone-mapping="aces"
                      environment-image="neutral"
                      ar
                      ar-modes="webxr scene-viewer quick-look"
                      loading="lazy"
                      style="width: 100%; height: 500px; background-color: #f8f9fb; border-radius: 1rem; box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);">
                    </model-viewer>

                    <button type="button" class="btn btn-primary btn-lg mt-4" id="bantayog-ar-button">
                      👀 View in AR
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <script>
              document.addEventListener("DOMContentLoaded", function () {
                const viewer = document.getElementById("bantayog-viewer");
                const arButton = document.getElementById("bantayog-ar-button");

                if (viewer && arButton) {
                  arButton.addEventListener("click", () => {
                    if (viewer.canActivateAR) {
                      viewer.activateAR();
                    } else {
                      alert("AR viewing is not supported on this device.");
                    }
                  });
                }
              });
            </script>


<section id="intro" class="py-5">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Discover Daet</h2>
    <div class="row g-4">

      <!-- Card 1 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Bagasbas Beach"
             data-description="Famous for surfing and sunrise views along golden sands."
             data-img="assets/img/bagasbas.jpg">
          <img src="assets/img/bagasbas.jpg" class="card-img-top" alt="Bagasbas Beach">
          <div class="card-body"><h6 class="card-title">Bagasbas Beach</h6></div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Rizal Monument"
             data-description="The first monument built to honor Dr. Jose Rizal in the Philippines, 1898."
             data-img="assets/img/rizal_monument.jpg">
          <img src="assets/img/rizal_monument.jpg" class="card-img-top" alt="Rizal Monument">
          <div class="card-body"><h6 class="card-title">Rizal Monument</h6></div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Pinyasan Festival"
             data-description="An annual June festival celebrating Daet’s sweet pineapples with parades and cultural shows."
             data-img="assets/img/pinyasan.jpg">
          <img src="assets/img/pinyasan.jpg" class="card-img-top" alt="Pinyasan Festival">
          <div class="card-body"><h6 class="card-title">Pinyasan Festival</h6></div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Native Products"
             data-description="Handcrafted goods and delicacies unique to Daet’s culture and heritage."
             data-img="assets/img/daet_products.jpg">
          <img src="assets/img/daet_products.jpg" class="card-img-top" alt="Native Products">
          <div class="card-body"><h6 class="card-title">Native Products</h6></div>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Bicol Cuisine"
             data-description="Experience the rich and spicy flavors of authentic Bicolano dishes."
             data-img="assets/img/daet_food.jpg">
          <img src="assets/img/daet_food.jpg" class="card-img-top" alt="Bicol Cuisine">
          <div class="card-body"><h6 class="card-title">Bicol Cuisine</h6></div>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="col-md-2 col-sm-6">
        <div class="card card-3d shadow-sm h-100 text-center intro-card"
             data-title="Historic Church"
             data-description="Old Spanish-era church symbolizing Daet’s deep faith and history."
             data-img="assets/img/church.jpg">
          <img src="assets/img/church.jpg" class="card-img-top" alt="Historic Church">
          <div class="card-body"><h6 class="card-title">Historic Church</h6></div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Modal -->
<div class="modal fade" id="tourismModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="row g-0">
          <!-- Left Side (Details) -->
          <div class="col-md-4 p-4 d-flex flex-column justify-content-center bg-light">
            <h4 id="tourismModalTitle"></h4>
            <p id="tourismModalDescription" class="text-muted"></p>
          </div>
          <!-- Right Side (Image) -->
          <div class="col-md-8 d-flex">
            <img id="tourismModalImage" src="" alt="Preview" class="img-fluid flex-grow-1">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="introModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row g-0">
          <!-- Left Side (Details) -->
          <div class="col-md-4 p-4 d-flex flex-column justify-content-center bg-light">
            <h4 id="modalTitle"></h4>
            <p id="modalDescription" class="text-muted"></p>
          </div>
          <!-- Right Side (Image) -->
          <div class="col-md-8">
            <img id="modalImage" src="" alt="" class="img-fluid w-100">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<!-- Tourism Video Section -->
<section id="tourism-video">
  <video id="tourismVideo" muted loop playsinline>
    <source src="assets/video/tourism video.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="video-overlay">
    <div>
      <h2 class="fw-bold display-4">Experience Daet</h2>
      <p class="lead">Discover its beauty, culture, and people</p>
    </div>
  </div>
  <!-- Floating Unmute Button -->
  <button id="toggleSound" class="btn btn-light shadow">🔊</button>
</section>




 <!-- Map Section -->
  <section id="tourism-map" class="py-5 bg-light">
    <div class="container-fluid">
      <h2 class="fw-bold text-center mb-4">Explore Daet on the Map</h2>
      
      <!-- Map style switcher -->
      <div class="mb-3 text-end">
        <select id="mapStyleSelector" class="form-select w-auto d-inline-block">
          <option value="mapbox://styles/mapbox/streets-v12">Streets</option>
          <option value="mapbox://styles/mapbox/satellite-streets-v12">Satellite</option>
          <option value="mapbox://styles/mapbox/light-v11">Light</option>
          <option value="mapbox://styles/mapbox/dark-v11">Dark</option>
          <option value="mapbox://styles/mapbox/outdoors-v12">Outdoors</option>
        </select>
      </div>

      <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
          <div class="list-group shadow-sm" id="map-spots">
            <button class="list-group-item fw-bold text-center bg-primary text-white" disabled>
              Tourist Spots
            </button>
            <button class="list-group-item list-group-item-action" data-coords="122.9556,14.1222">
              📍 Bagasbas Beach
            </button>
            <button class="list-group-item list-group-item-action" data-coords="122.9558,14.1144">
              📍 Rizal Monument
            </button>
            <button class="list-group-item list-group-item-action" data-coords="122.9577,14.1165">
              📍 Pinyasan Festival Grounds
            </button>
          </div>
        </div>
        <!-- Map -->
        <div class="col-md-9">
          <div id="tourismMap"></div>
        </div>
      </div>
    </div>
  </section>



  <?php
$page_footer_scripts = <<<'HTML'
<script src="js/autoadjustheight.js"></script>
<script src="js/tourism.js"></script>
<script src="js/video.js"></script>
HTML;
include 'footer.php';
?>