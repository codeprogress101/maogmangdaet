<?php
// Helper functions shared across the admin interface.
declare(strict_types=1);

require_once __DIR__ . '/config.php';

const SLUGGABLE_TABLES = [
    'news',
    'announcements',
    'executive_issuances',
    'ordinances',
    'resolutions',
    'public_hearings',
];


/**
 * Build (and memoise) the PDO connection using secure defaults.
 */
function get_pdo(): PDO
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
        // Never expose sensitive connection details to the user.
        http_response_code(500);
        exit('Database connection failed.');
    }

    return $pdo;
}

/**
 * Initialise a hardened session cookie.
 */
function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);

    $cookiePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/admin');
    $cookiePath = str_replace('\\', '/', $cookiePath);
    $cookiePath = rtrim($cookiePath, '/');
    if ($cookiePath === '') {
        $cookiePath = '/';
    } else {
        $cookiePath .= '/';
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookiePath,
        'secure' => $isHttps,  // Ensures cookies are only sent via HTTPS when available.
        'httponly' => true,    // Mitigates JavaScript access to the session.
        'samesite' => 'Strict' // Prevents CSRF via cross-site requests.
    ]);

    session_start();

    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true); // Prevent session fixation.
        $_SESSION['initiated'] = true;
        $_SESSION['created_at'] = time();
    }
}

/**
 * Enforce idle and absolute timeout requirements for the session.
 */
function enforce_session_security(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $now = time();

    if (isset($_SESSION['last_activity']) && ($now - (int) $_SESSION['last_activity']) > SESSION_IDLE_TIMEOUT) {
        $userId = current_user()['id'] ?? null;
        if ($userId) {
            log_audit(get_pdo(), $userId, 'Session expired due to inactivity');
        }
        logout_user();
        redirect('login.php?timeout=1');
    }

    if (isset($_SESSION['created_at']) && ($now - (int) $_SESSION['created_at']) > SESSION_ABSOLUTE_TIMEOUT) {
        $userId = current_user()['id'] ?? null;
        if ($userId) {
            log_audit(get_pdo(), $userId, 'Session exceeded absolute lifetime');
        }
        logout_user();
        redirect('login.php?timeout=1');
    }

    $_SESSION['last_activity'] = $now;
}

/**
 * Destroy the session securely.
 */
function logout_user(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    // Clear session array.
    $_SESSION = [];

    // Delete session cookie.
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'] ?? '/', $params['domain'] ?? '', $params['secure'] ?? true, $params['httponly'] ?? true);

    session_destroy();
}

/**
 * Redirect helper to centralise header usage.
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit();
}

/**
 * Return the currently authenticated user information.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Require that the current session is authenticated.
 */
function require_login(): void
{
    if (!current_user()) {
        redirect('login.php');
    }
}

/**
 * Generate a CSRF token and cache it in the session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate incoming CSRF tokens using a timing-safe comparison.
 */
function validate_csrf_token(?string $token): bool
{
    return $token !== null && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Fetch a user record by email address for authentication.
 */
function find_user_by_email(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    return $user ?: null;
}

/**
 * Record an audit event for accountability.
 */
function log_audit(PDO $pdo, ?int $userId, string $event): void
{
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, event, ip, ua, created_at) VALUES (:user_id, :event, :ip, :ua, NOW())');
    $stmt->execute([
        'user_id' => $userId,
        'event' => $event,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255),
    ]);
}

/**
 * Attempt to authenticate the user and enforce brute-force protection.
 */
function attempt_login(PDO $pdo, string $email, string $password): array
{
    $user = find_user_by_email($pdo, $email);
    $now = new DateTimeImmutable();

    if (!$user) {
        // Log the failed attempt without revealing details to the user.
        log_audit($pdo, null, 'Failed login for unknown account: ' . $email);
        return ['success' => false, 'message' => 'Invalid credentials.'];
    }

    if (!empty($user['locked_until']) && strtotime($user['locked_until']) > $now->getTimestamp()) {
        return ['success' => false, 'message' => 'Account locked due to too many failed attempts. Please try again later.'];
    }

    if (!password_verify($password, $user['password_hash'])) {
        $failedAttempts = ((int) $user['failed_attempts']) + 1;
        $lockedUntil = null;

        if ($failedAttempts >= FAILED_LOGIN_LIMIT) {
            $lockedUntil = $now->modify('+' . ACCOUNT_LOCK_DURATION . ' seconds')->format('Y-m-d H:i:s');
            $failedAttempts = 0; // Reset counter when lock engages.
        }

        $stmt = $pdo->prepare('UPDATE users SET failed_attempts = :failed_attempts, locked_until = :locked_until WHERE id = :id');
        $stmt->execute([
            'failed_attempts' => $failedAttempts,
            'locked_until' => $lockedUntil,
            'id' => $user['id'],
        ]);

        log_audit($pdo, (int) $user['id'], 'Failed login attempt');

        if ($lockedUntil) {
            log_audit($pdo, (int) $user['id'], 'Account locked until ' . $lockedUntil);
        }

        return ['success' => false, 'message' => 'Invalid credentials.'];
    }

    if (password_needs_rehash($user['password_hash'], PASSWORD_ARGON2ID)) {
        // Transparently upgrade hash algorithms if the configuration changes.
        $newHash = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $newHash, 'id' => $user['id']]);
    }

    $stmt = $pdo->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id = :id');
    $stmt->execute(['id' => $user['id']]);

    session_regenerate_id(true); // Enforce new session ID on login.
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'login_time' => time(),
    ];
    $_SESSION['last_activity'] = time();

    log_audit($pdo, (int) $user['id'], 'Successful login');

    return ['success' => true, 'message' => 'Login successful.'];
}

/**
 * Fetch aggregate counts for dashboard statistics.
 */
function get_table_count(PDO $pdo, string $table): int
{
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `{$table}`");
    return (int) $stmt->fetchColumn();
}

/**
 * Retrieve latest audit log entries.
 */
function get_latest_audit_logs(PDO $pdo, int $limit = 10): array
{
    $stmt = $pdo->prepare('SELECT audit_logs.*, users.email FROM audit_logs LEFT JOIN users ON users.id = audit_logs.user_id ORDER BY audit_logs.created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Small helper for escaping output safely.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Persist a content record and log the operation.
 */
function save_content(PDO $pdo, string $table, array $data, ?int $id = null): int
{
    if ($id) {
        if (!in_array($table, SLUGGABLE_TABLES, true)) {
        throw new InvalidArgumentException('Unsupported content table.');
        }   

        $stmt = $pdo->prepare("UPDATE `{$table}` SET title = :title, slug = :slug, description = :description, pdf_path = :pdf_path, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'pdf_path' => $data['pdf_path'],
            'id' => $id,
        ]);
        return $id;
    }

    $stmt = $pdo->prepare("INSERT INTO `{$table}` (title, slug, description, pdf_path, created_at, updated_at) VALUES (:title, :slug, :description, :pdf_path, NOW(), NOW())");
    $stmt->execute([
        'title' => $data['title'],
        'slug' => $data['slug'],
        'description' => $data['description'],
        'pdf_path' => $data['pdf_path'],
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Remove a content record and clean up uploaded files.
 */
function delete_content(PDO $pdo, string $table, int $id): void
{
    $stmt = $pdo->prepare("SELECT pdf_path FROM `{$table}` WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $file = $stmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM `{$table}` WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($file) {
        $filePath = __DIR__ . '/' . ltrim($file, '/');
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}

/**
 * Validate and persist an uploaded PDF.
 */
function handle_pdf_upload(array $file): array
{
    if (empty($file['name'])) {
        return ['path' => null, 'errors' => []];
    }

    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed.';
        return ['path' => null, 'errors' => $errors];
    }

    if ($file['size'] > MAX_PDF_SIZE) {
        $errors[] = 'PDF exceeds size limit.';
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        $errors[] = 'Only PDF files are allowed.';
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        $errors[] = 'File must have a .pdf extension.';
    }

    if ($errors) {
        return ['path' => null, 'errors' => $errors];
    }

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
        $errors[] = 'Failed to create upload directory.';
        return ['path' => null, 'errors' => $errors];
    }

    $filename = sprintf('%s_%s.pdf', date('YmdHis'), bin2hex(random_bytes(8)));
    $destination = UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'Failed to move uploaded file.';
        return ['path' => null, 'errors' => $errors];
    }

    // Return the relative path (from admin directory) for storage.
    return ['path' => 'uploads/' . $filename, 'errors' => []];
}

/**
 * Load a single content record by id.
 */
function get_content(PDO $pdo, string $table, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM `{$table}` WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return $row ?: null;
}
/**
 * Generate a unique, URL-friendly slug for the news title.
 */
function generateSlug(string $title, PDO $conn, ?int $ignoreId = null, string $table = 'news'): string
{
    if (!in_array($table, SLUGGABLE_TABLES, true)) {
        throw new InvalidArgumentException('Unsupported table for slug generation.');
    }

   $workingTitle = trim($title);
    if ($workingTitle === '') {
        $slug = '';
    } else {
        $workingTitle = preg_replace('/\s+/u', ' ', $workingTitle) ?? '';

        if (class_exists('Transliterator')) {
            /** @var Transliterator $transliterator */
            $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
            if ($transliterator instanceof Transliterator) {
                $workingTitle = $transliterator->transliterate($workingTitle);
            }
        }

        $slug = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $workingTitle);
        if ($slug === false) {
            $slug = $workingTitle;
        }

        $slug = mb_strtolower($slug, 'UTF-8');

        if (class_exists('Normalizer')) {
            $normalized = Normalizer::normalize($slug, Normalizer::FORM_D);
            if ($normalized !== false) {
                $slug = preg_replace('/\p{Mn}+/u', '', $normalized) ?? $slug;
            }
        }

        $slug = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $slug) ?? '';
        $slug = trim($slug, '-');
    }

    if ($slug === '') {
        $fallback = preg_replace('/[^a-z0-9]+/i', '-', $table) ?? 'item';
        $slug = trim(mb_strtolower($fallback, 'UTF-8'), '-') ?: 'item';
    }

    if (mb_strlen($slug, 'UTF-8') > 190) {
        $slug = rtrim(mb_substr($slug, 0, 190, 'UTF-8'), '-');
    }

    $baseSlug = $slug;
    $suffix = 2;
    $query = sprintf('SELECT COUNT(*) FROM `%s` WHERE slug = :slug', $table);
    if ($ignoreId !== null) {
        $query .= ' AND id <> :id';
    }
    $stmt = $conn->prepare($query);

    while (true) {
        $params = ['slug' => $slug];
        if ($ignoreId !== null) {
            $params['id'] = $ignoreId;
        }

        $stmt->execute($params);
        $exists = (int) $stmt->fetchColumn();
        $stmt->closeCursor();

        if ($exists === 0) {
            break;
        }

        $slug = sprintf('%s-%d', $baseSlug, $suffix);
        if (mb_strlen($slug, 'UTF-8') > 200) {
            $slug = mb_substr($slug, 0, 200, 'UTF-8');
        }
        $suffix++;
    }

    return $slug;
}


/**
 * Delete a previously uploaded news image from storage.
 */
function delete_news_image(?string $path): void
{
    if (!$path) {
        return;
    }

    $filePath = __DIR__ . '/../' . ltrim($path, '/');
    $realFilePath = realpath($filePath);
    $uploadsPath = realpath(NEWS_UPLOAD_DIR);

    if ($realFilePath && $uploadsPath && strpos($realFilePath, $uploadsPath) === 0 && is_file($realFilePath)) {
        unlink($realFilePath);
    }
}

/**
 * Handle the upload of a news image, enforcing format and size constraints.
 */
function handle_news_image_upload(array $file, ?string $currentImagePath = null): array
{
    if (empty($file['name']) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['path' => $currentImagePath, 'errors' => [], 'uploaded' => false];
    }

    $errors = [];

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Image upload failed.';
        return ['path' => $currentImagePath, 'errors' => $errors, 'uploaded' => false];
    }

    if ((int) $file['size'] > MAX_NEWS_IMAGE_SIZE) {
        $errors[] = 'Image exceeds the maximum size of 5 MB.';
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: '';

    if (!isset(NEWS_IMAGE_ALLOWED_TYPES[$mime])) {
        $errors[] = 'Only JPG, PNG, or GIF images are allowed.';
    }

    if ($errors) {
        return ['path' => $currentImagePath, 'errors' => $errors];
    }

    if (!is_dir(NEWS_UPLOAD_DIR) && !mkdir(NEWS_UPLOAD_DIR, 0755, true) && !is_dir(NEWS_UPLOAD_DIR)) {
        $errors[] = 'Failed to prepare upload directory.';
        return ['path' => $currentImagePath, 'errors' => $errors, 'uploaded' => false];
    }

    $extension = NEWS_IMAGE_ALLOWED_TYPES[$mime];
    $filename = sprintf('%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(6)), $extension);
    $destination = rtrim(NEWS_UPLOAD_DIR, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'Failed to store uploaded image.';
        return ['path' => $currentImagePath, 'errors' => $errors, 'uploaded' => false];
    }

    $publicPath = rtrim(NEWS_IMAGE_PUBLIC_PATH, '/') . '/' . $filename;

    return ['path' => $publicPath, 'errors' => [], 'uploaded' => true];
}

/**
 * Retrieve a single news record by its identifier.
 */
function find_news(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * Insert a new news article into the database.
 */
function create_news(PDO $pdo, string $title, string $content, ?string $imagePath): int
{
    $slug = generateSlug($title, $pdo);

    $stmt = $pdo->prepare('INSERT INTO news (title, slug, content, image_path, created_at, updated_at) VALUES (:title, :slug, :content, :image_path, NOW(), NOW())');
    $stmt->execute([
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'image_path' => $imagePath,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Update an existing news article.
 */
function update_news(PDO $pdo, int $id, string $title, string $content, ?string $imagePath, bool $regenerateSlug = false): void
{
    $news = find_news($pdo, $id);
    if (!$news) {
        throw new RuntimeException('News record not found.');
    }

    $slug = $news['slug'];
    if ($regenerateSlug) {
        $slug = generateSlug($title, $pdo, $id);
    }

    $stmt = $pdo->prepare('UPDATE news SET title = :title, slug = :slug, content = :content, image_path = :image_path, updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'image_path' => $imagePath,
        'id' => $id,
    ]);
}

/**
 * Remove a news article, including its associated image.
 */
function delete_news(PDO $pdo, int $id): void
{
    $news = find_news($pdo, $id);
    if ($news && !empty($news['image_path'])) {
        delete_news_image($news['image_path']);
    }

    $stmt = $pdo->prepare('DELETE FROM news WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

/**
 * Fetch all news articles ordered by recency.
 */
function list_news(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM news ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

/**
 * Ensure incoming POST requests are protected by CSRF.
 */
function require_valid_csrf(): void
{
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }
}