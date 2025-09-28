<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

secure_session_start();
$pdo = get_pdo();

if (current_user()) {
    redirect('dashboard.php');
} 

$errors = [];
$message = '';

if (isset($_GET['timeout'])) {
    $message = 'Your session has expired. Please sign in again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security verification failed. Please refresh and try again.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || $password === '') {
            $errors[] = 'Email and password are required.';
        } else {
            $result = attempt_login($pdo, $email, $password);
            if ($result['success']) {
                redirect('dashboard.php');
            }
            $message = $result['message'];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        :root {
            --bs-primary: #fd7e14;
        }
        body {
            background: linear-gradient(135deg, #fd7e14, #ff9f50);
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 0;
        }
        .card {
            border: none;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175);
        }
        /* ✅ Logo Fix */
        .login-logo-wrapper {
            max-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            text-align: center;
        }
       .login-logo {
        max-height: 80px;   /* limit height instead of width */
        width: auto;        /* let width adjust proportionally */
        object-fit: contain; /* ensures it fits nicely */
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="d-flex justify-content-center gap-4 mb-3 align-items-center">
                                <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                                    <img src="../assets/img/lgu logo.png" class="login-logo img-fluid" alt="LGU Logo">
                                    <img src="../assets/img/md logo.png" class="login-logo img-fluid" alt="Municipality Logo">
                                </div>
                            </div>
                            <h1 class="h4 fw-bold text-dark mb-0">Maogmang Daet Admin</h1>
                        </div>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= e($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($message): ?>
                        <div class="alert alert-warning mb-3"><?= e($message) ?></div>
                    <?php endif; ?>
                    <form method="post" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                    <p class="text-muted small mt-3 mb-0">Passwords are secured using Argon2id hashing and sessions enforce strict timeouts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
