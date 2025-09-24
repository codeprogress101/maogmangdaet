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
<?php
$page_title = 'News &amp; Updates';
$page_head_includes = <<<HTML
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

HTML;
$activePage = 'news';
include 'header.php';
?>

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
         <form class="news-search" method="get" action="news_update.php">
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
<?php include 'footer.php'; ?>