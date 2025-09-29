<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_module_access('news');

$success = '';
$errors = [];

if (isset($_GET['created'])) {
    $success = 'News article created successfully.';
} elseif (isset($_GET['updated'])) {
    $success = 'News article updated successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                delete_news($pdo, $id);
                log_audit($pdo, current_user()['id'] ?? null, sprintf('Deleted news article #%d', $id));
                $success = 'News article deleted successfully.';
            } catch (Throwable $exception) {
                $errors[] = 'Failed to delete the requested news article.';
            }
        }
    }
}

$newsItems = list_news($pdo);

$pageTitle = 'News Management';
include __DIR__ . '/partials/header.php';
?>
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">News Articles</h2>
                <a class="btn btn-sm btn-primary" href="add_news.php">Add News</a>
            </div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger mb-3">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mb-3"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if (!$newsItems): ?>
                    <p class="mb-0">No news articles have been created yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col" style="width: 160px;">Created</th>
                                    <th scope="col" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($newsItems as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($item['title']) ?></strong>
                                        <div class="text-muted small">Last updated <?= e($item['updated_at']) ?></div>
                                    </td>
                                    <td><code><?= e($item['slug']) ?></code></td>
                                    <td><?= e(date('M j, Y', strtotime($item['created_at']))) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-secondary" href="edit_news.php?id=<?= (int) $item['id'] ?>">Edit</a>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this news article?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>