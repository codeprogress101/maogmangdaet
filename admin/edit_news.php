<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('Invalid news article.');
}

$news = find_news($pdo, $id);
if (!$news) {
    http_response_code(404);
    exit('News article not found.');
}

$errors = [];
$success = '';
$title = $news['title'];
$content = $news['content'];
$imagePath = $news['image_path'];

if (isset($_GET['updated'])) {
    $success = 'News article updated successfully.';
}

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

    $uploadResult = handle_news_image_upload($_FILES['news_image'] ?? [], $news['image_path']);
    if ($uploadResult['errors']) {
        $errors = array_merge($errors, $uploadResult['errors']);
    }

    if (!$errors) {
        $newImagePath = $uploadResult['uploaded'] ? $uploadResult['path'] : $news['image_path'];
        $regenerateSlug = $title !== $news['title'];

        try {
            update_news($pdo, $id, $title, $content, $newImagePath, $regenerateSlug);
            if ($uploadResult['uploaded'] && $news['image_path'] && $news['image_path'] !== $newImagePath) {
                delete_news_image($news['image_path']);
            }
            redirect('edit_news.php?id=' . $id . '&updated=1');
        } catch (Throwable $exception) {
            $errors[] = 'Failed to update news article. Please try again.';
            if ($uploadResult['uploaded']) {
                delete_news_image($uploadResult['path']);
            }
        }
    } else {
        if ($uploadResult['uploaded']) {
            delete_news_image($uploadResult['path']);
        }
    }
}

$pageTitle = 'Edit News';
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Edit News Article</h2>
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
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
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
                        <div class="form-text">Uploading a new image replaces the current one.</div>
                        <?php if ($imagePath): ?>
                            <div class="mt-2">
                                <img src="<?= e($imagePath) ?>" alt="Current image" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>