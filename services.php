<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/feedback.php';

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
.service-accordion .accordion-item {
      border-radius: 1rem;
      border: 1px solid rgba(0, 0, 0, 0.08);
      overflow: hidden;
      margin-bottom: 1rem;
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
    }

    .service-accordion .accordion-button {
      font-weight: 600;
      gap: 0.75rem;
      padding: 1.25rem 1.5rem;
    }

    .service-accordion .accordion-button:not(.collapsed) {
      color: #fff;
      background-color: var(--services-primary);
      box-shadow: none;
    }

    .service-accordion .accordion-button:focus {
      box-shadow: none;
      border-color: rgba(253, 126, 20, 0.25);
    }

    .service-accordion .accordion-body {
      padding: 1.5rem;
      background-color: #fffdf9;
    }

    .service-accordion .list-icon {
      color: var(--services-primary);
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
$errors = [];
$successMessage = '';
$formValues = [
    'name' => '',
    'email' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues['name'] = trim((string) ($_POST['name'] ?? ''));
    $formValues['email'] = trim((string) ($_POST['email'] ?? ''));
    $formValues['message'] = trim((string) ($_POST['message'] ?? ''));

    if ($formValues['name'] === '') {
        $errors[] = 'Please provide your name.';
    } elseif (mb_strlen($formValues['name'], 'UTF-8') > 150) {
        $errors[] = 'Names must be 150 characters or less.';
    }

    if ($formValues['email'] === '') {
        $errors[] = 'Please provide your email address.';
    } elseif (!filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } elseif (mb_strlen($formValues['email'], 'UTF-8') > 150) {
        $errors[] = 'Email addresses must be 150 characters or less.';
    }

    if ($formValues['message'] === '') {
        $errors[] = 'Please enter a message for the LGU team.';
    } elseif (mb_strlen($formValues['message'], 'UTF-8') > 5000) {
        $errors[] = 'Messages must be 5,000 characters or less.';
    }

    if (!$errors) {
        try {
            $saved = saveContactMessage($formValues['name'], $formValues['email'], $formValues['message']);
            if ($saved) {
                $successMessage = 'Thank you for contacting us. Your message has been sent successfully.';
                $formValues = ['name' => '', 'email' => '', 'message' => ''];
            } else {
                $errors[] = 'We were unable to save your message. Please try again later.';
            }
        } catch (Throwable $exception) {
            $errors[] = 'We encountered an unexpected error while sending your message. Please try again later.';
        }
    }
}
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
            <p class="text-muted">Coming Soon.</p>
            <a href="#" class="btn btn-primary mt-3 disabled" tabindex="-1" aria-disabled="true">Coming Soon</a>
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
             <a href="#service-requirements" class="btn btn-primary mt-3">View Requirements</a>
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

<!-- SERVICE REQUIREMENTS SECTION -->
<section id="service-requirements" class="py-5" style="background-color: #fff7f0;">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-5">
      <span class="badge rounded-pill px-3 py-2 text-uppercase" style="background-color: rgba(253, 126, 20, 0.1); color: var(--services-primary);">Service Requirements</span>
      <h2 class="section-title fw-bold mt-3">Guides for Assistance and Support</h2>
      <p class="text-muted mb-0">Review the documentary requirements for our social services programs to help you prepare ahead of your visit.</p>
    </div>
    <div class="service-accordion">
      <div class="accordion" id="servicesAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingBusiness">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBusiness" aria-expanded="false" aria-controls="collapseBusiness">
              <i class="bi bi-briefcase-fill"></i>
              <span>Business Permit &amp; Licensing</span>
            </button>
          </h2>
          <div id="collapseBusiness" class="accordion-collapse collapse" aria-labelledby="headingBusiness" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <p class="mb-0 text-muted">Coming Soon.</p>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingSoloParent">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSoloParent" aria-expanded="false" aria-controls="collapseSoloParent">
              <i class="bi bi-person-heart"></i>
              <span>Requirements for Livelihood Assistance for Solo Parent</span>
            </button>
          </h2>
          <div id="collapseSoloParent" class="accordion-collapse collapse" aria-labelledby="headingSoloParent" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Updated Solo Parent ID (Xerox Copy)</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Sulat Kahilingan – Address to Mayor</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Project Proposal</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Voter’s Certification (COMELEC)</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Simplified Action Plan Form (DAET NEGOSYO CENTER)</li>
                <li class="mb-0"><i class="bi bi-check-circle-fill list-icon me-2"></i>Picture</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingSeniorCitizen">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeniorCitizen" aria-expanded="false" aria-controls="collapseSeniorCitizen">
              <i class="bi bi-people-fill"></i>
              <span>Requirements for Deceased Indigent Senior Citizen</span>
            </button>
          </h2>
          <div id="collapseSeniorCitizen" class="accordion-collapse collapse" aria-labelledby="headingSeniorCitizen" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Certified Xerox Copy of Death Certificate</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Certified Xerox Copy of OSCA ID</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Xerox Copy ID of Claimant</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>SB Resolution 074-2014</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Barangay Certificate of Indigency</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Voter’s Certification</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Certificate of Indigency – MSWDO</li>
                <li class="mb-0"><i class="bi bi-check-circle-fill list-icon me-2"></i>CE-MSWDO (For Interview)</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingLivelihood">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLivelihood" aria-expanded="false" aria-controls="collapseLivelihood">
              <i class="bi bi-cash-coin"></i>
              <span>Requirements for Livelihood Assistance</span>
            </button>
          </h2>
          <div id="collapseLivelihood" class="accordion-collapse collapse" aria-labelledby="headingLivelihood" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ul class="list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Sulat Kahilingan – Address to Mayor</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Project Proposal</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Voter’s Certification (COMELEC)</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill list-icon me-2"></i>Simplified Action Plan Form (DAET NEGOSYO CENTER)</li>
                <li class="mb-0"><i class="bi bi-check-circle-fill list-icon me-2"></i>Picture</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingBurial">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBurial" aria-expanded="false" aria-controls="collapseBurial">
              <i class="bi bi-emoji-tear"></i>
              <span>Requirements for Burial Assistance</span>
            </button>
          </h2>
          <div id="collapseBurial" class="accordion-collapse collapse" aria-labelledby="headingBurial" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ol class="mb-0 ps-3">
                <li class="mb-2">Barangay Certificate of Indigency (original copy)
                  <p class="mb-2 small text-muted">Note: Indicate the name and relationship of the claimant to the deceased</p>
                </li>
                <li class="mb-2">Voter’s Certification (original copy)</li>
                <li class="mb-0">Death Certificate (certified Xerox copy)</li>
              </ol>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingMedicalOfficials">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedicalOfficials" aria-expanded="false" aria-controls="collapseMedicalOfficials">
              <i class="bi bi-hospital"></i>
              <span>Requirements for Medical Assistance (Barangay Elected/Appointed Officials)</span>
            </button>
          </h2>
          <div id="collapseMedicalOfficials" class="accordion-collapse collapse" aria-labelledby="headingMedicalOfficials" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ol class="mb-0 ps-3">
                <li class="mb-2">Medical Certificate (original copy)</li>
                <li class="mb-2">Reseta (photocopy)</li>
                <li class="mb-2">Certificate of No Available Medicine (issued by RHU)</li>
                <li class="mb-2">Appointment for Appointed Officials / Oath of Office for Elected Officials</li>
                <li class="mb-2">Liga Certification</li>
                <li class="mb-0">SB Resolution</li>
              </ol>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingMedicalGeneral">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedicalGeneral" aria-expanded="false" aria-controls="collapseMedicalGeneral">
              <i class="bi bi-heart-pulse-fill"></i>
              <span>Requirements for Medical Assistance (General/Public)</span>
            </button>
          </h2>
          <div id="collapseMedicalGeneral" class="accordion-collapse collapse" aria-labelledby="headingMedicalGeneral" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
              <ol class="mb-0 ps-3">
                <li class="mb-2">Medical Certificate (original copy)</li>
                <li class="mb-2">Reseta / Laboratory Request (Xerox copy)</li>
                <li class="mb-2">Certification from RHU that medicine/laboratory request is not available (original copy)</li>
                <li class="mb-2">Voter’s Certification (original copy) of claimant</li>
                <li class="mb-2">Barangay Certificate of Indigency of Claimant (original copy)
                  <p class="mb-2 small text-muted">Note: Indicate the name and relationship of the claimant to the patient</p>
                </li>
                <li class="mb-0">Interview at MSWDO</li>
              </ol>
            </div>
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

<section class="py-5" id="citizen-feedback">
  <div class="container px-4 px-lg-5">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <h2 class="section-title fw-bold mb-3">Citizen Feedback Portal</h2>
        <p class="text-muted mb-3">
          Share health, permit, social services, or other community concerns directly with the LGU. Submit your feedback and receive a tracking number so you can follow progress anytime.
        </p>
        <ul class="list-unstyled text-muted small mb-0">
          <li class="mb-2"><i class="bi bi-shield-check text-primary me-2"></i>Secure ticket tracking with email notifications.</li>
          <li class="mb-2"><i class="bi bi-paperclip text-primary me-2"></i>Attach supporting documents (JPG, PNG, PDF, DOCX up to 5&nbsp;MB each).</li>
          <li class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Assigned to the right department with full update history.</li>
        </ul>
      </div>
      <div class="col-lg-7">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h3 class="h5 fw-semibold mb-3">Submit your feedback</h3>
            <form action="submit_feedback.php" method="post" enctype="multipart/form-data" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="feedbackName" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="feedbackName" name="name" maxlength="150" required>
                </div>
                <div class="col-md-6">
                  <label for="feedbackEmail" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="feedbackEmail" name="email" maxlength="150" required>
                </div>
                <div class="col-12">
                  <label for="feedbackCategory" class="form-label">Category</label>
                  <select class="form-select" id="feedbackCategory" name="category" required>
                    <option value="" selected disabled>Select a category</option>
                    <?php foreach (FEEDBACK_CATEGORIES as $category): ?>
                      <option value="<?= htmlspecialchars($category, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($category, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <label for="feedbackMessage" class="form-label">Message</label>
                  <textarea class="form-control" id="feedbackMessage" name="message" rows="5" maxlength="5000" required></textarea>
                </div>
                <div class="col-12">
                  <label for="feedbackFiles" class="form-label">Attachments</label>
                  <input class="form-control" type="file" id="feedbackFiles" name="attachments[]" accept=".jpg,.jpeg,.png,.pdf,.docx" multiple>
                  <div class="form-text">Optional. Up to 5&nbsp;MB per file. Allowed types: JPG, JPEG, PNG, PDF, DOCX.</div>
                </div>
                <div class="col-12 text-end">
                  <button type="submit" class="btn btn-primary px-4">Submit Feedback</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-light" id="contact-us">
  <div class="container px-4 px-lg-5">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <h2 class="section-title fw-bold mb-3">Contact Us</h2>
        <p class="text-muted mb-4">
          Share your questions, feedback, or service requests with the Municipal Government of Daet. Our support team will respond using the email address you provide.
        </p>
        <ul class="list-unstyled text-muted">
          <li class="mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Municipal Hall, Ninoy Aquino Ave., Daet, Camarines Norte</li>
          <li class="mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i>(054) 411-1111</li>
          <li class="mb-0"><i class="bi bi-envelope-fill text-primary me-2"></i><a href="mailto:info@lgudaet.gov.ph" class="text-decoration-none">info@lgudaet.gov.ph</a></li>
        </ul>
      </div>
      <div class="col-lg-7">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h3 class="h5 fw-semibold mb-3">Send us a message</h3>
            <?php if ($successMessage): ?>
              <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($successMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
              </div>
            <?php endif; ?>
            <?php if ($errors): ?>
              <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                  <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <form method="post" novalidate>
              <div class="mb-3">
                <label for="contactName" class="form-label">Full Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="contactName"
                  name="name"
                  maxlength="150"
                  required
                  value="<?= htmlspecialchars($formValues['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                />
              </div>
              <div class="mb-3">
                <label for="contactEmail" class="form-label">Email Address</label>
                <input
                  type="email"
                  class="form-control"
                  id="contactEmail"
                  name="email"
                  maxlength="150"
                  required
                  value="<?= htmlspecialchars($formValues['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                />
              </div>
              <div class="mb-3">
                <label for="contactMessage" class="form-label">Message</label>
                <textarea
                  class="form-control"
                  id="contactMessage"
                  name="message"
                  rows="5"
                  maxlength="5000"
                  required><?= htmlspecialchars($formValues['message'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>
              </div>
              <div class="d-grid d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary px-4">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>