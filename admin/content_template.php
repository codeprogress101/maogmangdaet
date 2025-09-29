<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
if (!isset($requiredModule)) {
    http_response_code(500);
    exit('Module configuration missing.');
}

require_module_access($requiredModule);

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
            $slug = $existingItem && $title === ($existingItem['title'] ?? '')
                ? $existingItem['slug']
                : generateSlug($title, $pdo, $id, $tableName);
            $data = [
                'title' => $title,
                'slug' => $slug,
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
             $editingItem = $existingItem ?: [
                'id' => $id,
                'title' => $title,
                'slug' => null,
                'description' => $description,
                'pdf_path' => $pdfPath,
            ];
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
<header class="mb-4">
    <h1 class="h2 fw-bold text-dark"><?= e($pageTitle) ?></h1>
    <p class="text-muted">Manage <?= e(strtolower($pageTitle)) ?> content displayed on the public website.</p>
</header>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center border-0 py-3">
                <h2 class="h5 mb-0 fw-semibold">Existing Records</h2>
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
       <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h2 class="h5 mb-0 fw-semibold"><?= $editingItem ? 'Edit Record' : 'Add New Record' ?></h2>
            </div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger rounded-3">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                   <div class="alert alert-success rounded-3"><?= e($success) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="save">
                    <?php if ($editingItem): ?>
                        <input type="hidden" name="id" value="<?= (int) $editingItem['id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required value="<?= e($editingItem['title'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?= e($editingItem['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="pdf" class="form-label fw-semibold">PDF Document</label>
                        <input class="form-control" type="file" id="pdf" name="pdf" accept="application/pdf">
                        <div class="form-text">Upload a PDF file (optional). Existing files will be replaced.</div>
                        <?php if (!empty($editingItem['pdf_path'])): ?>
                            <div class="small mt-2">Current file: <a href="<?= e($editingItem['pdf_path']) ?>" target="_blank">View</a></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="<?= e(basename($_SERVER['PHP_SELF'])) ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>