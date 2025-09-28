<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

$errors = [];
$title = '';
$content = '';
$imagePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($content === '') {
        $errors[] = 'Content is required.';
    }

    $uploadResult = handle_news_image_upload($_FILES['news_image'] ?? []);
    if ($uploadResult['errors']) {
        $errors = array_merge($errors, $uploadResult['errors']);
    }

    if (!$errors) {
        $imagePath = $uploadResult['uploaded'] ? $uploadResult['path'] : null;

        try {
            $newsId = create_news($pdo, $title, $content, $imagePath);
            log_audit($pdo, current_user()['id'] ?? null, sprintf('Created news article #%d', $newsId));
            redirect('news.php?created=1');
        } catch (Throwable $exception) {
            $errors[] = 'Failed to create news article. Please try again.';
            if ($uploadResult['uploaded'] ?? false) {
                delete_news_image($uploadResult['path']);
            }
        }
    } else {
        if ($uploadResult['uploaded'] ?? false) {
            delete_news_image($uploadResult['path']);
        }
    }
}

$pageTitle = 'Add News';
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Add News Article</h2>
                <a class="btn btn-sm btn-outline-secondary" href="news.php">Back to list</a>
            </div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= e($title) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="8" required><?= e($content) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="news_image" class="form-label">Featured Image</label>
                        <input class="form-control" type="file" id="news_image" name="news_image" accept="image/jpeg,image/png,image/gif">
                        <div class="form-text">Upload a single JPG, PNG, or GIF image.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Create News Article</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>