<?php
$page_title = 'Municipal Services of Daet';
$page_head_includes = <<<HTML
<style>
    :root {
      --services-primary: #fd7e14;
    }

    .section-title {
      color: var(--services-primary);
    }

    .navbar-nav .nav-link.active,
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link:focus {
      color: var(--services-primary) !important;
    }

    .text-primary {
      color: var(--services-primary) !important;
    }

    .bg-primary {
      background-color: var(--services-primary) !important;
    }

    .border-primary {
      border-color: var(--services-primary) !important;
    }

    .btn-primary {
      background-color: var(--services-primary);
      border-color: var(--services-primary);
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
      background-color: #e76a03;
      border-color: #e76a03;
    }

    a {
      color: var(--services-primary);
    }

    a:hover {
      color: #e76a03;
    }

    .service-card {
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
      background-color: #ffffff;
    }

    .service-card:hover {
      transform: translateY(-8px);
      border-color: var(--services-primary);
      box-shadow: 0 1.5rem 2.5rem rgba(253, 126, 20, 0.18);
    }

    .service-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background-color: rgba(253, 126, 20, 0.1);
      color: var(--services-primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 1rem;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .service-card:hover .service-icon {
      background-color: var(--services-primary);
      color: #ffffff;
    }

    .services-hero {
      position: relative;
      background: url(assets/img/aboutdaet.png) center/cover no-repeat;
      height: 40vh;
    }

    .services-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.10));
    }

    .services-hero .container {
      position: relative;
      z-index: 1;
    }

    .services-intro {
      background-color: #fff7f0;
    }

    .services-intro .badge {
      background-color: rgba(253, 126, 20, 0.1);
      color: var(--services-primary);
      font-weight: 600;
      letter-spacing: 0.08rem;
    }

    .services-intro .highlight-card {
      border-left: 4px solid var(--services-primary);
    }

    .breadcrumb .breadcrumb-item.active {
      color: var(--services-primary);
    }

    #backToTop {
      background-color: var(--services-primary);
      border: none;
    }

    #backToTop:hover {
      background-color: #e76a03;
    }
  </style>
HTML;
$activePage = 'services';
include 'header.php';
?>


 <!-- HERO SECTION -->
            <header class="about-header services-hero text-white text-center d-flex align-items-center justify-content-center">
            <div class="container px-4 px-lg-5">
                
                <h1 class="fw-bold display-5">Municipal Services of Daet</h1>
                <p class="lead mb-0">Accessible, people-first services that uplift every Daeteño.</p>
            </div>
            </header>

<!-- INTRO SECTION -->
<section class="services-intro py-5">
  <div class="container px-4 px-lg-5">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb mb-0">

      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Municipal Services</li>
      </ol>
    </nav>
    <div class="row g-4 align-items-center">
      <div class="col-lg-7">
        <h2 class="section-title fw-bold mb-3">Serving the Community with Heart</h2>
        <p class="mb-4 text-muted">
          The Municipality of Daet is committed to delivering responsive and inclusive programs that strengthen livelihoods, promote well-being, and protect our shared future. Explore the services below to learn how the local government can support you and your community.
        </p>
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="p-4 bg-white rounded shadow-sm highlight-card h-100">
              <h5 class="fw-semibold mb-2 text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>Citizen-Centered</h5>
              <p class="mb-0 text-muted small">Programs created with Daeteños in mind, ensuring equitable access to essential services.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-4 bg-white rounded shadow-sm highlight-card h-100">
              <h5 class="fw-semibold mb-2 text-dark"><i class="bi bi-lightning-charge-fill me-2 text-primary"></i>Responsive Delivery</h5>
              <p class="mb-0 text-muted small">Coordinated efforts from municipal offices to address urgent needs and long-term priorities.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="p-4 p-lg-5 bg-white rounded-4 shadow-sm text-center h-100">
          <h5 class="fw-bold text-uppercase text-primary mb-3">Need Assistance?</h5>
          <p class="text-muted mb-4">Visit the Municipal Hall, reach us at <a href="tel:+63544111111">(054) 411-1111</a>, or email <a href="mailto:info@lgudaet.gov.ph">info@lgudaet.gov.ph</a> for service support.</p>
          <a href="#services" class="btn btn-primary px-4 py-2">View All Services</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES GRID SECTION -->
<section id="services" class="py-5">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5">
      <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: rgba(253, 126, 20, 0.1); color: var(--services-primary);">Services at a Glance</span>
      <h2 class="section-title fw-bold mt-3">Comprehensive Support for Every Daeteño</h2>
      <p class="text-muted mb-0">Navigate our key programs to discover opportunities, assistance, and partnerships offered by the LGU.</p>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-briefcase-fill"></i>
            </div>
            <h5 class="fw-semibold mb-3">Business Permits &amp; Licensing</h5>
            <p class="text-muted">Streamlined processes for new and existing enterprises, from registrations to renewals.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-heart-pulse-fill"></i>
            </div>
            <h5 class="fw-semibold mb-3">Health &amp; Medical Services</h5>
            <p class="text-muted">Primary care, maternal support, and public health initiatives that keep communities safe.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-shield-fill-check"></i>
            </div>
            <h5 class="fw-semibold mb-3">Public Safety &amp; Disaster Response</h5>
            <p class="text-muted">Preparedness programs, rescue operations, and coordinated emergency response teams.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h5 class="fw-semibold mb-3">Education &amp; Scholarships</h5>
            <p class="text-muted">Scholarships, learning hubs, and support programs that empower lifelong learners.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="fas fa-tractor"></i>
            </div>
            <h5 class="fw-semibold mb-3">Agriculture &amp; Fisheries Support</h5>
            <p class="text-muted">Capacity building, inputs, and market linkages for farmers and fisherfolk.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-building"></i>
            </div>
            <h5 class="fw-semibold mb-3">Infrastructure &amp; Engineering</h5>
            <p class="text-muted">Roads, drainage, and facility projects built to support progress and resilience.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="fas fa-hands-holding-heart"></i>
            </div>
            <h5 class="fw-semibold mb-3">Social Services &amp; Welfare</h5>
            <p class="text-muted">Assistance for vulnerable sectors, from livelihood kits to protective services.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-cash-stack"></i>
            </div>
            <h5 class="fw-semibold mb-3">Tax &amp; Treasury Services</h5>
            <p class="text-muted">Convenient payment options and guidance on fees, taxes, and revenue generation.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-recycle"></i>
            </div>
            <h5 class="fw-semibold mb-3">Environmental Protection &amp; Waste Management</h5>
            <p class="text-muted">Sustainable practices, clean-up drives, and waste collection for greener barangays.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card service-card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="service-icon mx-auto">
              <i class="bi bi-compass-fill"></i>
            </div>
            <h5 class="fw-semibold mb-3">Tourism &amp; Cultural Affairs</h5>
            <p class="text-muted">Events, promotions, and heritage conservation to celebrate Daet's vibrant culture.</p>
            <a href="#" class="btn btn-primary mt-3">Learn More</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CALL TO ACTION SECTION -->
<section class="py-5" style="background-color: #fff7f0;">
  <div class="container px-4 px-lg-5">
    <div class="row g-4 align-items-center">
      <div class="col-lg-8">
        <h3 class="fw-bold section-title mb-3">Partner with the Municipality</h3>
        <p class="text-muted mb-0">Looking to collaborate on projects or propose community programs? Our offices welcome partnerships that advance inclusive growth and sustainable development.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="mailto:info@lgudaet.gov.ph" class="btn btn-primary btn-lg px-4">Connect with Us</a>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>