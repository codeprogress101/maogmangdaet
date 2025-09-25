<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

secure_session_start();
enforce_session_security();

$pdo = get_pdo();