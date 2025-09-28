<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                delete_contact_message($pdo, $id);
                log_audit($pdo, current_user()['id'] ?? null, sprintf('Deleted contact message #%d', $id));
                $success = 'Contact message deleted successfully.';
            } catch (Throwable $exception) {
                $errors[] = 'Failed to delete the selected message. Please try again.';
            }
        }
    }
}

$messages = [];
try {
    $messages = list_contact_messages($pdo);
} catch (Throwable $exception) {
    $errors[] = 'Unable to load contact messages at this time.';
}

$pageTitle = 'Contact Messages';
include __DIR__ . '/partials/header.php';
?>
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Citizen Contact Messages</h2>
            </div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger mb-3" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <div><?= e($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mb-3" role="alert"><?= e($success) ?></div>
                <?php endif; ?>

                <?php if (!$messages): ?>
                    <p class="mb-0">No messages have been submitted yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 60px;">ID</th>
                                    <th scope="col" style="width: 200px;">Name</th>
                                    <th scope="col" style="width: 220px;">Email</th>
                                    <th scope="col">Message</th>
                                    <th scope="col" style="width: 180px;">Submitted</th>
                                    <th scope="col" style="width: 110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?= (int) $message['id'] ?></td>
                                    <td><?= e($message['name']) ?></td>
                                    <td>
                                        <a href="mailto:<?= e($message['email']) ?>" class="text-decoration-none">
                                            <?= e($message['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="small text-muted">
                                            <?= nl2br(e($message['message'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $timestamp = strtotime($message['created_at'] ?? '') ?: null;
                                            echo $timestamp ? e(date('M j, Y g:i A', $timestamp)) : '—';
                                        ?>
                                    </td>
                                    <td>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
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