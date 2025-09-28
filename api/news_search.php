<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET', true, 405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$query = trim((string) ($_GET['q'] ?? ''));
$results = fetchNewsSearchResults($query, 20);

$total = count($results);
$html = '';

if ($total === 0) {
    $message = $query === ''
        ? 'No news articles are available right now.'
        : 'No news articles matched your search.';

    $linkHtml = $query === ''
        ? ''
        : '<p class="text-muted">Try a different keyword or <a class="text-decoration-none" href="news_update.php">view all news</a>.</p>';

    $html = '<div class="col-12"><div class="text-center py-5">'
        . '<i class="bi bi-newspaper text-muted display-5 mb-3"></i>'
        . '<h5>' . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</h5>'
        . $linkHtml
        . '</div></div>';
} else {
    foreach ($results as $article) {
        $detailUrl = buildNewsDetailUrl($article);
        $title = htmlspecialchars($article['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $excerpt = htmlspecialchars(newsExcerpt($article['content'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $date = htmlspecialchars(formatNewsDatePublic($article['created_at'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $imagePath = publicUploadPath($article['image_path'] ?? null);

        $html .= '<div class="col-12 col-md-6 col-lg-4">';
        $html .= '<div class="card news-card">';
        if ($imagePath) {
            $html .= '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" alt="' . $title . ' thumbnail" class="card-img-top">';
        }
        $html .= '<div class="card-body d-flex flex-column">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
        $html .= '<span class="news-meta"><i class="bi bi-calendar3 me-1"></i>' . $date . '</span>';
        $html .= '</div>';
        $html .= '<h5 class="card-title">' . $title . '</h5>';
        $html .= '<p class="card-text mb-4">' . $excerpt . '</p>';
        $html .= '<div class="mt-auto">';
        $html .= '<a class="btn btn-news btn-sm" href="' . htmlspecialchars($detailUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">Read More</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
}

echo json_encode([
    'html' => $html,
    'total' => $total,
]);