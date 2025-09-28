<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id <= 0) {
    http_response_code(404);
    exit('Article not found.');
}

$article = fetchNewsById($id);
if (!$article) {
    http_response_code(404);
    exit('Article not found.');
}

$requestedSlug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$canonicalSlug = (string) ($article['slug'] ?? '');
$canonicalUrl = buildNewsDetailUrl($article);

if ($canonicalSlug !== '' && $requestedSlug !== $canonicalSlug) {
    header('Location: ' . $canonicalUrl, true, 301);
    exit;
}

$page_title = $article['title'] . ' - News & Updates';
$activePage = 'news';
$canonicalLinkTag = sprintf('<link rel="canonical" href="%s">', htmlspecialchars($canonicalUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
$page_head_includes = ($page_head_includes ?? '') . "\n    " . $canonicalLinkTag;

include __DIR__ . '/header.php';

$publishedDate = formatNewsDatePublic($article['created_at']);
$imagePath = publicUploadPath($article['image_path'] ?? null);
$articleContent = nl2br(htmlspecialchars($article['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

$recentNews = [];
foreach (fetchLatestNews(6) as $recent) {
    if ((int) $recent['id'] === (int) $article['id']) {
        continue;
    }
    $recentNews[] = $recent;
    if (count($recentNews) === 5) {
        break;
    }
}
?>
<section class="py-5 bg-light">
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="/news_update.php">News &amp; Updates</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
            </ol>
        </nav>
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="bg-white shadow-sm rounded-3 p-4">
                    <header class="mb-4">
                        <a class="btn btn-outline-primary btn-sm mb-3" href="/news_update.php">
                            <i class="bi bi-arrow-left"></i> Back to News
                        </a>
                        <h1 class="mb-3"><?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
                        <?php if ($publishedDate !== ''): ?>
                            <div class="text-muted">Published on <?= htmlspecialchars($publishedDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </header>
                    <?php if ($imagePath): ?>
                        <figure class="mb-4">
                            <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                        </figure>
                    <?php endif; ?>
                    <div class="article-content fs-5">
                        <?= $articleContent ?>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <aside class="bg-white shadow-sm rounded-3 p-4 h-100">
                    <h2 class="h5 mb-3">Recent News</h2>
                    <?php if ($recentNews): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($recentNews as $recent): ?>
                                <?php
                                    $recentUrl = buildNewsDetailUrl($recent);
                                    $recentTitle = htmlspecialchars($recent['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                    $recentDate = htmlspecialchars(formatNewsDatePublic($recent['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                ?>
                                <li class="mb-3">
                                    <a class="fw-semibold" href="<?= htmlspecialchars($recentUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"><?= $recentTitle ?></a>
                                    <?php if ($recentDate !== ''): ?>
                                        <div class="text-muted small"><?= $recentDate ?></div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No other articles available.</p>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>