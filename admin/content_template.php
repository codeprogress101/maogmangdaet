<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

if (!isset($tableName, $pageTitle)) {
    http_response_code(500);
    exit('Content configuration missing.');
}

$errors = [];
$success = '';
$editingItem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            delete_content($pdo, $tableName, $id);
            log_audit($pdo, current_user()['id'] ?? null, sprintf('Deleted %s record #%d', $pageTitle, $id));
            $success = 'Record deleted successfully.';
        }
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        $existingItem = null;
        if ($id) {
            $existingItem = get_content($pdo, $tableName, $id);
            if (!$existingItem) {
                $errors[] = 'Record not found.';
            }
        }

        $pdfPath = $existingItem['pdf_path'] ?? null;
        $uploadResult = handle_pdf_upload($_FILES['pdf'] ?? []);
        if ($uploadResult['errors']) {
            $errors = array_merge($errors, $uploadResult['errors']);
        }

        if ($uploadResult['path']) {
            if ($pdfPath) {
                $old = __DIR__ . '/' . ltrim($pdfPath, '/');
                if (is_file($old)) {
                    unlink($old);
                }
            }
            $pdfPath = $uploadResult['path'];
        }

        if (!$errors) {
            $data = [
                'title' => $title,
                'description' => $description,
                'pdf_path' => $pdfPath,
            ];
            $savedId = save_content($pdo, $tableName, $data, $id);
            if ($id) {
                log_audit($pdo, current_user()['id'] ?? null, sprintf('Updated %s record #%d', $pageTitle, $savedId));
                $success = 'Record updated successfully.';
            } else {
                log_audit($pdo, current_user()['id'] ?? null, sprintf('Created %s record #%d', $pageTitle, $savedId));
                $success = 'Record created successfully.';
            }
        } else {
            $editingItem = $existingItem ?: ['id' => $id, 'title' => $title, 'description' => $description, 'pdf_path' => $pdfPath];
        }
    }
}

if (!$editingItem && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $editingItem = get_content($pdo, $tableName, $editId);
        if (!$editingItem) {
            $errors[] = 'Unable to load the requested record.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM `{$tableName}` ORDER BY created_at DESC");
$items = $stmt->fetchAll();

include __DIR__ . '/partials/header.php';
?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Existing Records</h2>
                <a class="btn btn-sm btn-primary" href="<?= e(basename($_SERVER['PHP_SELF'])) ?>">Add New</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col" style="width: 120px;">PDF</th>
                                <th scope="col" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$items): ?>
                            <tr><td colspan="3" class="text-center py-4">No records yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($item['title']) ?></strong>
                                        <div class="text-muted small">Updated <?= e($item['updated_at']) ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['pdf_path'])): ?>
                                            <a class="btn btn-outline-primary btn-sm" href="<?= e($item['pdf_path']) ?>" target="_blank">View PDF</a>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-secondary" href="<?= e(basename($_SERVER['PHP_SELF'])) ?>?edit=<?= (int) $item['id'] ?>">Edit</a>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0"><?= $editingItem ? 'Edit Record' : 'Add New Record' ?></h2>
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
                    <input type="hidden" name="action" value="save">
                    <?php if ($editingItem): ?>
                        <input type="hidden" name="id" value="<?= (int) $editingItem['id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required value="<?= e($editingItem['title'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?= e($editingItem['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pdf" class="form-label">PDF Document</label>
                        <input class="form-control" type="file" id="pdf" name="pdf" accept="application/pdf">
                        <div class="form-text">Upload a PDF file (optional). Existing files will be replaced.</div>
                        <?php if (!empty($editingItem['pdf_path'])): ?>
                            <div class="small mt-2">Current file: <a href="<?= e($editingItem['pdf_path']) ?>" target="_blank">View</a></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>