<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_module_access('manage_users');

$currentUser = current_user();
$currentUserId = $currentUser['id'] ?? null;
$roleLabels = [
    ROLE_ADMIN => 'Administrator',
    ROLE_MIO => 'MIO',
    ROLE_SB => 'SB',
];

$errors = [];
$successMessages = [];
$newUserEmail = '';
$newUserRole = ROLE_MIO;
$minimumPasswordLength = 8;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $newUserEmail = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            if (!filter_var($newUserEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email address is required.';
            }

            if ($password === '' || strlen($password) < $minimumPasswordLength) {
                $errors[] = sprintf('Passwords must be at least %d characters long.', $minimumPasswordLength);
            }

            if (!is_valid_role($role)) {
                $errors[] = 'Please choose a valid role.';
            }

            if (!$errors) {
                $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

                try {
                    $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role, failed_attempts, locked_until, last_login_at, created_at) VALUES (:email, :password_hash, :role, 0, NULL, NULL, NOW())');
                    $stmt->execute([
                        'email' => $newUserEmail,
                        'password_hash' => $passwordHash,
                        'role' => $role,
                    ]);

                    $successMessages[] = 'User account created successfully.';
                    $logRole = $roleLabels[$role] ?? $role;
                    log_audit($pdo, $currentUserId, sprintf('Created user %s with role %s', $newUserEmail, $logRole));

                    $newUserEmail = '';
                    $newUserRole = ROLE_MIO;
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        $errors[] = 'A user with that email already exists.';
                    } else {
                        $errors[] = 'Unable to create the user account. Please try again.';
                    }
                }
            } else {
                $newUserRole = is_valid_role($role) ? $role : $newUserRole;
            }
            break;

        case 'update':
            $userId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $role = $_POST['role'] ?? '';
            $newPassword = $_POST['password'] ?? '';

            if ($userId <= 0) {
                $errors[] = 'Invalid user selected.';
                break;
            }

            $stmt = $pdo->prepare('SELECT id, email, role FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            $userRecord = $stmt->fetch();

            if (!$userRecord) {
                $errors[] = 'The selected user could not be found.';
                break;
            }

            if (!is_valid_role($role)) {
                $errors[] = 'Please choose a valid role.';
                break;
            }

            $updateFields = [
                'role' => $role,
                'id' => $userId,
            ];
            $updateSql = 'UPDATE users SET role = :role';
            $passwordReset = false;

            if ($newPassword !== '') {
                if (strlen($newPassword) < $minimumPasswordLength) {
                    $errors[] = sprintf('New passwords must be at least %d characters long.', $minimumPasswordLength);
                    break;
                }

                $updateSql .= ', password_hash = :password_hash, failed_attempts = 0, locked_until = NULL';
                $updateFields['password_hash'] = password_hash($newPassword, PASSWORD_ARGON2ID);
                $passwordReset = true;
            }

            $updateSql .= ' WHERE id = :id';

            $stmt = $pdo->prepare($updateSql);
            $stmt->execute($updateFields);

            if ($userId === $currentUserId) {
                $_SESSION['user']['role'] = $role;
            }

            $message = 'User details updated successfully.';
            if ($passwordReset) {
                $message .= ' Password reset completed.';
            }
            $successMessages[] = $message;

            $logMessage = sprintf('Updated user %s (ID %d) role to %s', $userRecord['email'], $userId, $roleLabels[$role] ?? $role);
            if ($passwordReset) {
                $logMessage .= ' and reset password';
            }
            log_audit($pdo, $currentUserId, $logMessage);
            break;

        case 'delete':
            $userId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            if ($userId <= 0) {
                $errors[] = 'Invalid user selected.';
                break;
            }

            $stmt = $pdo->prepare('SELECT id, email, role FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            $userRecord = $stmt->fetch();

            if (!$userRecord) {
                $errors[] = 'The selected user could not be found.';
                break;
            }

            if ($userId === $currentUserId) {
                $errors[] = 'You cannot delete the account that is currently signed in.';
                break;
            }

            if ($userRecord['role'] === ROLE_ADMIN) {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
                $adminCount = (int) $countStmt->fetchColumn();
                if ($adminCount <= 1) {
                    $errors[] = 'At least one administrator account must remain.';
                    break;
                }
            }

            $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);

            $successMessages[] = 'User account deleted successfully.';
            log_audit($pdo, $currentUserId, sprintf('Deleted user %s (ID %d)', $userRecord['email'], $userId));
            break;

        default:
            $errors[] = 'Unrecognised action requested.';
            break;
    }
}

$stmt = $pdo->query('SELECT id, email, role, last_login_at, created_at FROM users ORDER BY email');
$users = $stmt->fetchAll();

$pageTitle = 'Manage Users';
include __DIR__ . '/partials/header.php';
?>
<style>
    .manage-users-page .card-header span {
        white-space: normal;
    }

    @media (max-width: 767.98px) {
        .manage-users-page .card-header {
            text-align: center;
        }

        .manage-users-page .card-header span {
            display: block;
            margin-top: 0.5rem;
        }
    }

    .manage-users-page .table td,
    .manage-users-page .table th {
        vertical-align: middle;
    }

    .manage-users-page .table-responsive {
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
    }

    .manage-users-page .update-form .role-select {
        flex: 1 1 160px;
        min-width: 150px;
        max-width: 220px;
    }

    .manage-users-page .update-form .password-input {
        flex: 2 1 220px;
        min-width: 200px;
    }

    .manage-users-page .update-form .submit-btn {
        flex: 0 0 110px;
    }

    @media (max-width: 1199.98px) {
        .manage-users-page .update-form .role-select,
        .manage-users-page .update-form .password-input,
        .manage-users-page .update-form .submit-btn {
            max-width: 100%;
            min-width: 0;
        }

        .manage-users-page .update-form .submit-btn {
            flex-basis: auto;
        }
    }
</style>
<div class="manage-users-page">
<div class="row g-4 mb-4">
    <div class="col-12">
        <?php if ($errors): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($successMessages): ?>
            <div class="alert alert-success" role="alert">
                <?php foreach ($successMessages as $message): ?>
                    <div><?= e($message) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row gy-4 gx-xl-4 manage-users-layout">
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h2 class="h5 mb-0 fw-semibold">Add New User</h2>
            </div>
            <div class="card-body">
                <form method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="new-email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="new-email" name="email" value="<?= e($newUserEmail) ?>" required autocomplete="email">
                    </div>
                    <div class="mb-3">
                        <label for="new-password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="new-password" name="password" required autocomplete="new-password">
                        <div class="form-text">Minimum <?= $minimumPasswordLength ?> characters.</div>
                    </div>
                    <div class="mb-4">
                        <label for="new-role" class="form-label fw-semibold">Role</label>
                        <select class="form-select" id="new-role" name="role" required>
                            <?php foreach ($roleLabels as $roleValue => $label): ?>
                                <option value="<?= e($roleValue) ?>" <?= $newUserRole === $roleValue ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create User</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0 fw-semibold">Existing Users</h2>
                <span class="text-muted small">Reset password field is optional.</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col" class="text-nowrap">Last Login</th>
                                <th scope="col" class="text-nowrap">Created</th>
                                <th scope="col" class="text-center">Update</th>
                                <th scope="col" class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$users): ?>
                            <tr><td colspan="6" class="text-center py-4">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $userRow): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($userRow['email']) ?></strong>
                                    </td>
                                    <td><?= e($roleLabels[$userRow['role']] ?? ucfirst($userRow['role'])) ?></td>
                                    <td class="text-nowrap">
                                        <?php if (!empty($userRow['last_login_at'])): ?>
                                            <?= e(date('M j, Y g:i A', strtotime($userRow['last_login_at']))) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap"><?= e(date('M j, Y g:i A', strtotime($userRow['created_at']))) ?></td>
                                    <td class="align-middle">
                                        <form method="post" class="update-form d-flex flex-column flex-xl-row gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= (int) $userRow['id'] ?>">
                                            <div class="role-select">
                                                <select class="form-select form-select-sm" name="role">
                                                    <?php foreach ($roleLabels as $roleValue => $label): ?>
                                                        <option value="<?= e($roleValue) ?>" <?= $userRow['role'] === $roleValue ? 'selected' : '' ?>><?= e($label) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="password-input">
                                                <input type="password" class="form-control form-control-sm" name="password" placeholder="New password (optional)" autocomplete="new-password">
                                            </div>
                                            <div class="submit-btn">
                                                <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form method="post" class="d-grid" onsubmit="return confirm('Delete this user account?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $userRow['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" <?= $userRow['id'] === $currentUserId ? 'disabled' : '' ?>>Delete</button>
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
</div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>