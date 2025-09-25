<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --bs-primary: #fd7e14;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="executive.php">Executive Issuances</a></li>
                <li class="nav-item"><a class="nav-link" href="hearings.php">Public Hearings</a></li>
                <li class="nav-item"><a class="nav-link" href="ordinances.php">Ordinances</a></li>
                <li class="nav-item"><a class="nav-link" href="resolutions.php">Resolutions</a></li>
                <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="news.php">News</a></li>
            </ul>
            <?php if ($user): ?>
                <span class="navbar-text me-3">Logged in as <?= e($user['email']) ?></span>
                <a class="btn btn-light" href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">