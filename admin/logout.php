<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$user = current_user();
if ($user) {
    log_audit($pdo, $user['id'], 'Logged out');
}

logout_user();

redirect('login.php');