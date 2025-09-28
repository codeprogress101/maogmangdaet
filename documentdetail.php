<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';

$documentMeta = [
    'announcements' => [
        'singular' => 'Announcement',
        'plural' => 'Announcements',
        'segment' => 'announcements',
        'anchor' => '/index.php#announcements',
    ],
    'executive_issuances' => [
        'singular' => 'Executive Issuance',
        'plural' => 'Executive Issuances',
        'segment' => 'executive-issuances',
        'anchor' => '/index.php#executive-issuances',
    ],
    'ordinances' => [
        'singular' => 'Ordinance',
        'plural' => 'Ordinances',
        'segment' => 'ordinances',
        'anchor' => '/index.php#ordinances',
    ],
    'resolutions' => [
        'singular' => 'Resolution',
        'plural' => 'Resolutions',
        'segment' => 'resolutions',
        'anchor' => '/index.php#resolutions',
    ],
    'public_hearings' => [
        'singular' => 'Public Hearing',
        'plural' => 'Public Hearings',
        'segment' => 'public-hearings',
        'anchor' => '/index.php#public-hearings',
    ],
];

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!isset($documentMeta[$type]) || $id <= 0) {
    http_response_code(404);
    exit('Document not found.');
}

$metadata = $documentMeta[$type];
$document = fetchDocumentById($type, $id);

if (!$document) {
    http_response_code(404);
    exit('Document not found.');
}

$requestedSlug = $_GET['slug'] ?? '';
$canonicalSlug = $document['slug'];
$canonicalUrl = sprintf('/%s/%d/%s', $metadata['segment'], $document['id'], $canonicalSlug);

if ($requestedSlug === '' || $requestedSlug !== $canonicalSlug) {
    header('Location: ' . $canonicalUrl, true, 301);
    exit;
}

function buildDocumentFileUrl(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    return '/admin/' . ltrim($path, '/');
}

function formatDocumentDate(?string $date): ?string
{
    if (empty($date)) {
        return null;
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return null;
    }

    return date('F j, Y', $timestamp);
}

$page_title = $document['title'] . ' - ' . $metadata['plural'];
$activePage = 'home';
include __DIR__ . '/header.php';

$pdfUrl = buildDocumentFileUrl($document['pdf_path'] ?? null);
$publishedOn = formatDocumentDate($document['created_at'] ?? null);
?>
<section class="py-5 bg-light">
  <div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars($metadata['anchor'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($metadata['plural'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($document['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
      </ol>
    </nav>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <article class="bg-white shadow-sm rounded-3 p-4">
          <header class="mb-4">
            <h1 class="h2 mb-3"><?= htmlspecialchars($document['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
            <?php if ($publishedOn): ?>
              <div class="text-muted">Published on <?= htmlspecialchars($publishedOn, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
            <?php endif; ?>
          </header>
          <div class="fs-5 mb-4">
            <?= nl2br(htmlspecialchars($document['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) ?>
          </div>
          <?php if ($pdfUrl): ?>
            <a class="btn btn-primary" href="<?= htmlspecialchars($pdfUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" target="_blank" rel="noopener">
              View attached PDF
            </a>
          <?php else: ?>
            <div class="alert alert-info mb-0">No PDF file has been uploaded for this <?= htmlspecialchars(strtolower($metadata['singular']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> yet.</div>
          <?php endif; ?>
        </article>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>