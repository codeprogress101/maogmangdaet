<?php
require_once __DIR__ . '/includes/database.php';
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

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$articlesPerPage = 6;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPage = max(1, $currentPage);

$newsArticles = searchNews($searchQuery);
$totalArticles = count($newsArticles);
$totalPages = max(1, (int) ceil($totalArticles / $articlesPerPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $articlesPerPage;
$paginatedArticles = array_slice($newsArticles, $offset, $articlesPerPage);

$startRecord = $totalArticles > 0 ? $offset + 1 : 0;
$endRecord = min($offset + $articlesPerPage, $totalArticles);

function buildNewsQuery(array $params, string $searchQuery): string
{
    if ($searchQuery !== '') {
        $params['q'] = $searchQuery;
    }

    $query = http_build_query($params);

    return $query === '' ? '' : '?' . $query;
}

function buildNewsUrl(array $article): string
{
    return sprintf('/news/%d/%s', (int) $article['id'], $article['slug']);
}

function formatNewsDatePublic(?string $date): string
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('F j, Y', $timestamp) : '';
}

function newsExcerpt(?string $content, int $length = 160): string
{
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content ?? '')) ?? '');

    if (mb_strlen($plain) <= $length) {
        return $plain;
    }

    return rtrim(mb_substr($plain, 0, $length - 3)) . '...';
}
function buildDocumentFileUrl(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    // remove the first "/" if it exists
    return ltrim($path, '/');
}

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
          <p class="text-muted">Try different keywords or <a class="text-decoration-none" href="news_update.php">view all news</a>.</p>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($paginatedArticles as $article): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card news-card">
               <?php if (!empty($article['image_path'])): ?>
                <img src="<?= buildDocumentFileUrl($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?> thumbnail" class="card-img-top">
              <?php endif; ?>
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                   <span class="news-meta"><i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars(formatNewsDatePublic($article['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></span>
                </div>
                  <h5 class="card-title"><?php echo htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h5>
                <p class="card-text mb-4"><?php echo htmlspecialchars(newsExcerpt($article['content']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
                <div class="mt-auto">
                  <a class="btn btn-news btn-sm" href="<?php echo htmlspecialchars(buildNewsUrl($article), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">Read More</a>
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