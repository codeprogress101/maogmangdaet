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

$defaultStatusMessage = 'Browse municipal highlights, announcements, and community stories.';
$statusMessageHtml = $searchQuery !== ''
    ? 'Showing results for <strong>&ldquo;' . htmlspecialchars($searchQuery, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '&rdquo;</strong>.'
    : htmlspecialchars($defaultStatusMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$countLabel = $totalArticles === 1 ? 'article' : 'articles';
$countText = sprintf('%d %s found', $totalArticles, $countLabel);

$page_footer_scripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('newsSearchInput');
  const resultsContainer = document.getElementById('newsResults');
  const statusText = document.getElementById('newsStatusText');
  const countBadge = document.getElementById('newsCountBadge');
  const paginationWrapper = document.getElementById('newsPagination');

  if (!searchInput || !resultsContainer) {
    return;
  }

  const initialState = {
    resultsHtml: resultsContainer.innerHTML,
    statusHtml: statusText ? statusText.innerHTML : '',
    countText: countBadge ? countBadge.textContent : '',
    paginationHtml: paginationWrapper ? paginationWrapper.innerHTML : '',
    paginationHidden: paginationWrapper ? paginationWrapper.classList.contains('d-none') : false,
  };

  let activeController = null;
  let debounceTimer = null;

  function resetBadge(count, query) {
    if (!countBadge) {
      return;
    }

    if (!query) {
      const defaultText = countBadge.dataset.defaultText || initialState.countText;
      if (defaultText) {
        countBadge.textContent = defaultText;
      }
      return;
    }

    const singular = countBadge.dataset.singularLabel || 'article';
    const plural = countBadge.dataset.pluralLabel || 'articles';
    const label = count === 1 ? singular : plural;
    countBadge.textContent = count + ' ' + label + ' found';
  }

  function escapeHtml(value) {
    return value.replace(/[&<>"']/g, function (char) {
      switch (char) {
        case '&': return '&amp;';
        case '<': return '&lt;';
        case '>': return '&gt;';
        case '"': return '&quot;';
        case "'": return '&#39;';
        default: return char;
      }
    });
  }

  function updateStatus(count, query) {
    if (!statusText) {
      return;
    }

    if (!query) {
      const defaultText = statusText.dataset.defaultText || '';
      if (defaultText) {
        statusText.textContent = defaultText;
      } else {
        statusText.innerHTML = initialState.statusHtml;
      }
      return;
    }

    const safeQuery = escapeHtml(query);
    const prefix = count > 0 ? 'Showing top ' + count + ' result' + (count === 1 ? '' : 's') : 'No results';
    statusText.innerHTML = prefix + ' for <strong>&ldquo;' + safeQuery + '&rdquo;</strong>.';
  }

  function restoreInitialState() {
    if (activeController) {
      activeController.abort();
      activeController = null;
    }

    if (debounceTimer) {
      clearTimeout(debounceTimer);
      debounceTimer = null;
    }

    resultsContainer.innerHTML = initialState.resultsHtml;
    if (statusText) {
      statusText.innerHTML = initialState.statusHtml;
    }
    if (countBadge) {
      countBadge.textContent = initialState.countText;
    }
    if (paginationWrapper) {
      paginationWrapper.innerHTML = initialState.paginationHtml;
      if (initialState.paginationHidden) {
        paginationWrapper.classList.add('d-none');
      } else {
        paginationWrapper.classList.remove('d-none');
      }
    }
  }

  function performSearch(query) {
    if (!query) {
      restoreInitialState();
      return;
    }

    if (activeController) {
      activeController.abort();
    }

    activeController = new AbortController();

    const endpoint = new URL('api/news_search.php', window.location.origin);
    endpoint.searchParams.set('q', query);

    if (paginationWrapper) {
      paginationWrapper.classList.add('d-none');
    }

    fetch(endpoint.toString(), { signal: activeController.signal, headers: { Accept: 'application/json' } })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function (payload) {
        resultsContainer.innerHTML = payload.html || '';
        resetBadge(payload.total || 0, query);
        updateStatus(payload.total || 0, query);
      })
      .catch(function (error) {
        if (error.name === 'AbortError') {
          return;
        }

        resultsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger mb-0" role="alert">Unable to load search results. Please try again in a moment.</div></div>';
        resetBadge(0, query);
        updateStatus(0, query);
      });
  }

  searchInput.addEventListener('input', function () {
    const query = this.value.trim();

    if (debounceTimer) {
      clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(function () {
      performSearch(query);
    }, 300);
  });

  if (searchInput.value.trim() !== '') {
    performSearch(searchInput.value.trim());
  }
});
</script>
HTML;

function buildNewsQuery(array $params, string $searchQuery): string
{
    if ($searchQuery !== '') {
        $params['q'] = $searchQuery;
    }

    $query = http_build_query($params);

    return $query === '' ? '' : '?' . $query;
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
             <input type="text" id="newsSearchInput" name="q" class="form-control" placeholder="Search news, events, or categories" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Search news" autocomplete="off" />
            <button class="btn btn-news" type="submit">
              <i class="bi bi-search me-1"></i> Search
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <div>
        <p class="mb-0 text-muted" id="newsStatusText" data-default-text="<?= htmlspecialchars($defaultStatusMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
          <?= $statusMessageHtml ?>
        </p>
      </div>
      <div>
          <span id="newsCountBadge" class="badge rounded-pill text-bg-light text-dark border border-1 border-warning-subtle" data-default-text="<?= htmlspecialchars($countText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" data-singular-label="article" data-plural-label="articles">
          <?= htmlspecialchars($countText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </span>
      </div>
    </div>

     <div id="newsResults" class="row g-4">
      <?php if (empty($paginatedArticles)): ?>
        <div class="col-12">
          <div class="text-center py-5">
            <i class="bi bi-newspaper text-muted display-5 mb-3"></i>
            <h5>No news articles matched your search.</h5>
            <p class="text-muted">Try different keywords or <a class="text-decoration-none" href="news_update.php">view all news</a>.</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($paginatedArticles as $article): ?>
          <?php
            $detailUrl = buildNewsDetailUrl($article);
            $articleTitle = htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $articleExcerpt = htmlspecialchars(newsExcerpt($article['content']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $articleDate = htmlspecialchars(formatNewsDatePublic($article['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $articleImagePath = publicUploadPath($article['image_path'] ?? null);
          ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card news-card">
              <?php if ($articleImagePath): ?>
                <img src="<?= htmlspecialchars($articleImagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" alt="<?= $articleTitle ?> thumbnail" class="card-img-top">
              <?php endif; ?>
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="news-meta"><i class="bi bi-calendar3 me-1"></i><?= $articleDate ?></span>
                </div>
                <h5 class="card-title"><?= $articleTitle ?></h5>
                <p class="card-text mb-4"><?= $articleExcerpt ?></p>
                <div class="mt-auto">
                  <a class="btn btn-news btn-sm" href="<?= htmlspecialchars($detailUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">Read More</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if (!empty($paginatedArticles)): ?>
      <div id="newsPagination" class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-5">
        <div class="text-muted small">
          Showing <?= $startRecord; ?> - <?= $endRecord; ?> of <?= $totalArticles; ?> <?= $countLabel; ?>
        </div>
        <?php if ($totalPages > 1): ?>
          <nav aria-label="News pagination">
            <ul class="pagination mb-0">
              <li class="page-item <?= $currentPage === 1 ? 'disabled' : ''; ?>">
                <?php if ($currentPage === 1): ?>
                  <span class="page-link" aria-hidden="true">&laquo;</span>
                <?php else: ?>
                  <a class="page-link" href="news_update.php<?= buildNewsQuery(['page' => $currentPage - 1], $searchQuery); ?>" aria-label="Previous">&laquo;</a>
                <?php endif; ?>
              </li>
              <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <li class="page-item <?= $page === $currentPage ? 'active' : ''; ?>">
                  <?php if ($page === $currentPage): ?>
                    <span class="page-link"><?= $page; ?></span>
                  <?php else: ?>
                    <a class="page-link" href="news_update.php<?= buildNewsQuery(['page' => $page], $searchQuery); ?>"><?= $page; ?></a>
                  <?php endif; ?>
                </li>
              <?php endfor; ?>
              <li class="page-item <?= $currentPage === $totalPages ? 'disabled' : ''; ?>">
                <?php if ($currentPage === $totalPages): ?>
                  <span class="page-link" aria-hidden="true">&raquo;</span>
                <?php else: ?>
                  <a class="page-link" href="news_update.php<?= buildNewsQuery(['page' => $currentPage + 1], $searchQuery); ?>" aria-label="Next">&raquo;</a>
                <?php endif; ?>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div id="newsPagination" class="d-none"></div>
    <?php endif; ?>
  </div>
</section>
<?php include 'footer.php'; ?>
