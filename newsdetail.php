<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    exit('Article not found.');
}

$article = fetchNewsById($id);
if (!$article) {
    http_response_code(404);
    exit('Article not found.');
}

$requestedSlug = $_GET['slug'] ?? '';
$canonicalSlug = $article['slug'];
$canonicalUrl = sprintf('/news/%d/%s', $article['id'], $canonicalSlug);

if ($requestedSlug === '' || $requestedSlug !== $canonicalSlug) {
    header('Location: ' . $canonicalUrl, true, 301);
    exit;
}

$page_title = $article['title'] . ' - News & Updates';
$activePage = 'news';
include __DIR__ . 'header.php';

$publishedDate = date('F j, Y', strtotime($article['created_at']));
?>
<section class="py-5 bg-light">
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="/news_update.php">News &amp; Updates</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
            </ol>
        </nav>
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="bg-white shadow-sm rounded-3 p-4">
                    <header class="mb-4">
                        <h1 class="mb-3"><?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
                        <div class="text-muted">Published on <?= htmlspecialchars($publishedDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                    </header>
                    <?php if (!empty($article['image_path'])): ?>
                        <figure class="mb-4">
                            <img src="<?= htmlspecialchars($article['image_path'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        </figure>
                    <?php endif; ?>
                    <div class="article-content fs-5">
                        <?= nl2br(htmlspecialchars($article['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) ?>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <aside class="bg-white shadow-sm rounded-3 p-4 h-100">
                    <h2 class="h5 mb-3">Recent News</h2>
                    <ul class="list-unstyled mb-0">
                        <?php foreach (fetchLatestNews(5) as $recent): ?>
                            <?php
                            $recentUrl = sprintf('/news/%d/%s', $recent['id'], $recent['slug']);
                            $recentTitle = htmlspecialchars($recent['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                            ?>
                            <li class="mb-3">
                                <a class="fw-semibold" href="<?= htmlspecialchars($recentUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= $recentTitle ?></a>
                                <div class="text-muted small"><?= htmlspecialchars(date('M j, Y', strtotime($recent['created_at'])), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . 'footer.php'; ?>