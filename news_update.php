<?php
// Simulated news records for demonstration. In production, replace this array with a database query
// (e.g., SELECT * FROM news ORDER BY published_at DESC) and hydrate the dataset accordingly.
$newsArticles = [
    [
        'id' => 1,
        'title' => 'Daet Launches Community Coastal Cleanup Drive',
        'excerpt' => 'Volunteers gathered along Bagasbas Beach to remove debris and raise awareness on marine conservation.',
        'date' => 'April 18, 2024',
        'category' => 'Community',
        'image' => 'assets/img/news1.png',
    ],
    [
        'id' => 2,
        'title' => 'Municipal Council Approves New Public Market Renovation',
        'excerpt' => 'The renovation project aims to modernize stalls, improve ventilation, and provide better facilities for vendors.',
        'date' => 'April 14, 2024',
        'category' => 'Government',
        'image' => 'assets/img/news2.JPG',
    ],
    [
        'id' => 3,
        'title' => 'Daet Tourism Week Draws Record-Breaking Crowd',
        'excerpt' => "Local attractions and culinary fairs welcomed tourists celebrating Daet's vibrant culture and heritage.",
        'date' => 'April 9, 2024',
        'category' => 'Tourism',
        'image' => 'assets/img/daet1.jpg',
    ],
    [
        'id' => 4,
        'title' => 'Scholarship Program Opens for Senior High Students',
        'excerpt' => 'Qualified students can now apply for the municipal scholarship covering tuition and school supplies.',
        'date' => 'April 2, 2024',
        'category' => 'Education',
        'image' => 'assets/img/news3.JPG',
    ],
    [
        'id' => 5,
        'title' => 'Farmers Receive New Irrigation Support in Barangay Lag-on',
        'excerpt' => 'The Municipal Agriculture Office distributed irrigation pumps and provided training on sustainable farming.',
        'date' => 'March 26, 2024',
        'category' => 'Agriculture',
        'image' => 'assets/img/daet2.jpg',
    ],
    [
        'id' => 6,
        'title' => 'Local Entrepreneurs Highlighted in Daet Trade Expo',
        'excerpt' => 'MSMEs showcased handcrafted products, processed foods, and technology innovations at the civic center.',
        'date' => 'March 20, 2024',
        'category' => 'Business',
        'image' => 'assets/img/daet3.jpg',
    ],
    [
        'id' => 7,
        'title' => 'New Health Center Opens to Serve Riverside Communities',
        'excerpt' => 'A fully equipped facility now provides maternal care, immunization, and telemedicine consultations.',
        'date' => 'March 14, 2024',
        'category' => 'Health',
        'image' => 'assets/img/daet4.jpg',
    ],
    [
        'id' => 8,
        'title' => 'Bagasbas Surf Rescue Team Trains New Volunteers',
        'excerpt' => 'The Municipal Disaster Risk Reduction Office conducted advanced rescue drills and water safety workshops.',
        'date' => 'March 8, 2024',
        'category' => 'Safety',
        'image' => 'assets/img/bagasbas.jpg',
    ],
    [
        'id' => 9,
        'title' => 'Pasyar Daet Mobile App Rolls Out New Tourist Trails',
        'excerpt' => 'Updated itineraries guide visitors through historical landmarks and culinary hotspots around town.',
        'date' => 'March 1, 2024',
        'category' => 'Technology',
        'image' => 'assets/img/daet5.jpg',
    ],
    [
        'id' => 10,
        'title' => 'Youth Council Hosts Climate Action Summit',
        'excerpt' => 'Student leaders drafted barangay-level action plans focusing on mangrove protection and waste segregation.',
        'date' => 'February 24, 2024',
        'category' => 'Environment',
        'image' => 'assets/img/oldbantayog.jpg',
    ],
    [
        'id' => 11,
        'title' => 'Daet Sports Complex Undergoes Major Upgrade',
        'excerpt' => 'Track oval resurfacing and LED lighting installation prepare the venue for regional athletic meets.',
        'date' => 'February 17, 2024',
        'category' => 'Sports',
        'image' => 'assets/img/demo-image-01.jpg',
    ],
    [
        'id' => 12,
        'title' => 'Senior Citizens Receive Digital Literacy Training',
        'excerpt' => 'Free workshops equip elders with skills to access online services and connect with family abroad.',
        'date' => 'February 10, 2024',
        'category' => 'Community',
        'image' => 'assets/img/demo-image-02.jpg',
    ],
];

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$filteredArticles = array_filter($newsArticles, function (array $article) use ($searchQuery): bool {
    if ($searchQuery === '') {
        return true;
    }

    $haystack = $article['title'] . ' ' . $article['excerpt'] . ' ' . $article['category'];
    return stripos($haystack, $searchQuery) !== false;
});

$filteredArticles = array_values($filteredArticles);

$articlesPerPage = 6;
$totalArticles = count($filteredArticles);
$totalPages = max(1, (int) ceil($totalArticles / $articlesPerPage));
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));
$offset = ($currentPage - 1) * $articlesPerPage;
$paginatedArticles = array_slice($filteredArticles, $offset, $articlesPerPage);

$startRecord = $totalArticles > 0 ? $offset + 1 : 0;
$endRecord = min($offset + $articlesPerPage, $totalArticles);

if (!function_exists('buildNewsQuery')) {
    function buildNewsQuery(array $params, string $searchQuery): string
    {
        if ($searchQuery !== '') {
            $params['q'] = $searchQuery;
        }

        $query = http_build_query($params);

        return $query === '' ? '' : '?' . $query;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>News &amp; Updates</title>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <!-- Font Awesome icons -->
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- Google fonts -->
  <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Core theme CSS -->
  <link href="css/styles.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    :root {
      --news-primary: #fd7e14;
    }

    .news-hero {
      background: url('assets/img/aboutdaet.png') center/cover no-repeat;
      height: 40vh;
    }

    .news-section {
      padding: 4rem 0;
      background-color: #f8f9fa;
    }

    .news-section .section-title {
      color: #1b1b1b;
      font-weight: 700;
    }

    .news-search .form-control {
      border-radius: 2rem 0 0 2rem;
      border-color: var(--news-primary);
      box-shadow: none;
    }

    .news-search .form-control:focus {
      border-color: var(--news-primary);
      box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.2);
    }

    .news-search .btn-news {
      border-radius: 0 2rem 2rem 0;
      padding: 0.65rem 1.5rem;
    }

    .news-card {
      border: 1px solid transparent;
      border-radius: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
      overflow: hidden;
      background-color: #ffffff;
      height: 100%;
    }

    .news-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 1.2rem 2.5rem rgba(0, 0, 0, 0.12);
      border-color: var(--news-primary);
    }

    .news-card img {
      object-fit: cover;
      height: 220px;
      width: 100%;
    }

    .news-card .badge {
      background-color: var(--news-primary);
    }

    .btn-news {
      background-color: var(--news-primary);
      border-color: var(--news-primary);
      color: #fff;
      transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
    }

    .btn-news:hover,
    .btn-news:focus {
      background-color: #e56f0e;
      border-color: #e56f0e;
      color: #fff;
      transform: translateY(-2px);
    }

    .news-card .card-title {
      min-height: 3.5rem;
      font-weight: 700;
      color: #1b1b1b;
    }

    .news-card .card-text {
      color: #555;
    }

    .news-meta {
      font-size: 0.875rem;
      color: #6c757d;
    }

    .pagination .page-link {
      color: var(--news-primary);
      border-radius: 50px;
      border: 1px solid var(--news-primary);
      margin: 0 0.25rem;
    }

    .pagination .page-item.active .page-link,
    .pagination .page-link:hover {
      background-color: var(--news-primary);
      color: #fff;
      border-color: var(--news-primary);
    }

    #backToTop {
      background-color: var(--news-primary);
      border-color: var(--news-primary);
    }

    #backToTop:hover {
      background-color: #e56f0e;
      border-color: #e56f0e;
    }
  </style>
</head>
<body id="page-top">

            <!-- ================= NAVBAR ================= -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-white fixed-top shadow-sm" id="mainNav">
              <div class="container px-4 px-lg-5">
                <!-- Logo + Brand -->
                <a class="navbar-brand d-flex align-items-center" href="index.html">
                  <img
                    src="assets/img/lgu logo.png"
                    alt="LGU Logo"
                    class="me-2"/>
                  Maogmang Daet
                </a>

                <!-- Mobile Toggle -->
                <button
                  class="navbar-toggler"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#navbarResponsive"
                  aria-controls="navbarResponsive"
                  aria-expanded="false"
                  aria-label="Toggle navigation"
                >
                  <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Nav Links -->
                <div class="collapse navbar-collapse" id="navbarResponsive">
                  <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                      <a class="nav-link" href="about.html">About</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="government.html">Government</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" aria-current="page" href="news_update.php">News &amp; Updates</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#transparency">Transparency</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="tourism.html">Tourism</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="services.html">Services</a>
                    </li>
                  </ul>
                </div>
              </div>
            </nav>

            <!-- HERO SECTION -->
            <header class="about-header text-white text-center d-flex align-items-center justify-content-center news-hero">
            <div class="container px-4 px-lg-5">
                <h1 class="fw-bold display-4">News &amp; Updates</h1>
            </div>
            </header>

<section class="news-section" id="news">
  <div class="container px-4 px-lg-5">
    <div class="row justify-content-between align-items-end mb-4">
      <div class="col-lg-7">
        <h2 class="section-title">Latest from the Municipality</h2>
        <p class="text-muted mb-0">Stay informed with the most recent announcements, community stories, and development projects around Daet.</p>
      </div>
      <div class="col-lg-5 mt-3 mt-lg-0">
        <form class="news-search" method="get" action="news_updates.php">
          <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search news, events, or categories" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Search news" />
            <button class="btn btn-news" type="submit">
              <i class="bi bi-search me-1"></i> Search
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <?php if ($searchQuery !== ''): ?>
          <p class="mb-0 text-muted">Showing results for <strong>&ldquo;<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>&rdquo;</strong>.</p>
        <?php else: ?>
          <p class="mb-0 text-muted">Browse municipal highlights, announcements, and community stories.</p>
        <?php endif; ?>
      </div>
      <div>
        <span class="badge rounded-pill text-bg-light text-dark border border-1 border-warning-subtle">
          <?php echo $totalArticles; ?> article<?php echo $totalArticles === 1 ? '' : 's'; ?> found
        </span>
      </div>
    </div>

    <?php if (empty($paginatedArticles)): ?>
      <div class="text-center py-5">
        <i class="bi bi-newspaper text-muted display-5 mb-3"></i>
        <h5>No news articles matched your search.</h5>
        <p class="text-muted">Try different keywords or <a class="text-decoration-none" href="news_updates.php">view all news</a>.</p>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($paginatedArticles as $article): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card news-card">
              <?php if (!empty($article['image'])): ?>
                <img src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?> thumbnail" class="card-img-top">
              <?php endif; ?>
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge"><?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="news-meta"><i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars($article['date'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <h5 class="card-title"><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <p class="card-text mb-4"><?php echo htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="mt-auto">
                  <a class="btn btn-news btn-sm" href="<?php echo 'news_detail.php?id=' . urlencode((string) $article['id']); ?>">Read More</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-5">
        <div class="text-muted small">
          Showing <?php echo $startRecord; ?> - <?php echo $endRecord; ?> of <?php echo $totalArticles; ?> article<?php echo $totalArticles === 1 ? '' : 's'; ?>
        </div>
        <?php if ($totalPages > 1): ?>
          <nav aria-label="News pagination">
            <ul class="pagination mb-0">
              <li class="page-item <?php echo $currentPage === 1 ? 'disabled' : ''; ?>">
                <?php if ($currentPage === 1): ?>
                  <span class="page-link" aria-hidden="true">&laquo;</span>
                <?php else: ?>
                  <a class="page-link" href="news_update.php<?php echo buildNewsQuery(['page' => $currentPage - 1], $searchQuery); ?>" aria-label="Previous">&laquo;</a>
                <?php endif; ?>
              </li>
              <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <li class="page-item <?php echo $page === $currentPage ? 'active' : ''; ?>">
                  <?php if ($page === $currentPage): ?>
                    <span class="page-link"><?php echo $page; ?></span>
                  <?php else: ?>
                    <a class="page-link" href="news_update.php<?php echo buildNewsQuery(['page' => $page], $searchQuery); ?>"><?php echo $page; ?></a>
                  <?php endif; ?>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo $currentPage === $totalPages ? 'disabled' : ''; ?>">
                <?php if ($currentPage === $totalPages): ?>
                  <span class="page-link" aria-hidden="true">&raquo;</span>
                <?php else: ?>
                  <a class="page-link" href="news_update.php<?php echo buildNewsQuery(['page' => $currentPage + 1], $searchQuery); ?>" aria-label="Next">&raquo;</a>
                <?php endif; ?>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Footer Section -->
<footer class="site-footer">
  <div class="footer-container">

    <!-- Column 1 -->
    <div class="footer-col">
      <img src="assets/img/lgu logo.png" alt="City Logo" class="footer-logo">
      <img src="assets/img/md logo.png" alt="City Logo" class="footer-logo">
      <p><strong>OFFICIAL WEBSITE OF THE<br>MUNICIPALITY OF DAET</strong></p>
      <p>
        About this website <br>
        Contact us at <a href="mailto:info@naga.gov.ph">info@lgudaet.gov.ph</a><br>
        iGovernance Team | Local Government Unit of Daet
      </p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-x-twitter"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
      </div>
    </div>

    <!-- Column 2 -->
    <div class="footer-col">
      <h4>Municipal Government Links</h4>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Resident</a></li>
        <li><a href="#">Visitor</a></li>
        <li><a href="#">Investor</a></li>
        <li><a href="#">Supplier</a></li>
        <li><a href="#">Student</a></li>
        <li><a href="#">Municipal Officials</a></li>
      </ul>
    </div>

    <!-- Column 3 -->
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

    <!-- Column 4 -->
    <div class="footer-col">
      <h4>About GOVPH</h4>
      <p>
        Learn more about the Philippine government, its structure,
        how government works and the people behind it.
      </p>
      <ul>
        <li><a href="#">Open Data Portal</a></li>
        <li><a href="#">Official Gazette</a></li>
      </ul>
    </div>

    <!-- Column 5 -->
    <div class="footer-col footer-logos">
      <!-- First Logo + Links -->
      <div class="footer-logo-box">
        <img src="assets/img/dpstatement1.png" alt="DPO/DPS Logo" class="footer-badge">
        <p>
          <a href="#">Data Privacy Policy</a><br>
          <a href="#">Terms and Conditions</a>
        </p>
      </div>

      <!-- Second Logo + Text -->
      <div class="footer-logo-box">
        <img src="assets/img/coa-footerv2.svg" alt="E-Governance Logo" class="footer-badge">
        <p><strong>Republic of the Philippines</strong></p>
      </div>
    </div>
    </div>


  </div>

  <div class="footer-bottom">
    <p>REPUBLIC OF THE PHILIPPINES — All content is in the public domain unless otherwise stated.</p>
    <p><a href="#">Data Privacy Policy</a> | <a href="#">Terms and Conditions</a></p>
  </div>
</footer>

<button id="backToTop" class="btn btn-primary rounded-circle shadow">
            <i class="bi bi-arrow-up"></i> <!-- using Bootstrap Icon -->
            </button>

             <script>
    const backToTopBtn = document.getElementById("backToTop");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    });

    backToTopBtn.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    });
  </script>



  <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS -->
  <script src="js/scripts.js"></script>
</body>
</html>