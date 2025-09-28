<?php
declare(strict_types=1);

require_once __DIR__ . '/../admin/config.php';

const DOCUMENT_TABLES = [
    'announcements',
    'executive_issuances',
    'ordinances',
    'resolutions',
    'public_hearings',
];

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        http_response_code(500);
        exit('Unable to connect to the database.');
    }

    return $pdo;
}

function fetchLatestNews(int $limit = 3): array
{
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT id, title, slug, content, image_path, created_at FROM news ORDER BY created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetchNewsById(int $id): ?array
{
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT id, title, slug, content, image_path, created_at, updated_at FROM news WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function searchNews(?string $searchQuery = null): array
{
    $pdo = getDatabaseConnection();
    $sql = 'SELECT id, title, slug, content, image_path, created_at FROM news';
    $params = [];

    if ($searchQuery !== null && $searchQuery !== '') {
        $sql .= ' WHERE title LIKE :search1 OR content LIKE :search2';
        $params[':search1'] = '%' . $searchQuery . '%';
        $params[':search2'] = '%' . $searchQuery . '%';
    }

    $sql .= ' ORDER BY created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function fetchNewsSearchResults(?string $searchQuery = null, int $limit = 20): array
{
    $pdo = getDatabaseConnection();
    $sql = 'SELECT id, title, slug, content, image_path, created_at FROM news';
    $params = [];

    if ($searchQuery !== null && $searchQuery !== '') {
        $sql .= ' WHERE title LIKE :search OR content LIKE :search';
        $params[':search'] = '%' . $searchQuery . '%';
    }

    $sql .= ' ORDER BY created_at DESC LIMIT :limit';

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}



function fetchLatestDocuments(string $table, int $limit = 5): array
{
    if (!in_array($table, DOCUMENT_TABLES, true)) {
        throw new InvalidArgumentException('Unsupported document table.');
    }

    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT id, title, slug, description, pdf_path, created_at, updated_at FROM `{$table}` ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetchDocumentById(string $table, int $id): ?array
{
    if (!in_array($table, DOCUMENT_TABLES, true)) {
        throw new InvalidArgumentException('Unsupported document table.');
    }

    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT id, title, slug, description, pdf_path, created_at, updated_at FROM `{$table}` WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    return $row ?: null;
}
function buildNewsDetailUrl(array $article): string
{
    $id = (int) ($article['id'] ?? 0);
    $slug = trim((string) ($article['slug'] ?? ''));

    if ($id <= 0) {
        return '#';
    }

     $seoEnabled = defined('USE_SEO_NEWS_ROUTES') && USE_SEO_NEWS_ROUTES === true;

    if (!$seoEnabled || $slug === '') {
        return sprintf('newsdetail.php?id=%d', $id);
    }

    return sprintf('/news/%d/%s', $id, rawurlencode($slug));
}

function publicUploadPath(?string $path): ?string
{
    if ($path === null || $path === '') {
        return null;
    }

    return ltrim($path, '/');
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

    if ($plain === '') {
        return '';
    }

    if (mb_strlen($plain, 'UTF-8') <= $length) {
        return $plain;
    }

    return rtrim(mb_substr($plain, 0, $length - 3, 'UTF-8')) . '...';
}