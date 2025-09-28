<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
$user = current_user();
$currentFile = basename($_SERVER['PHP_SELF']); // detect current page
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    <style>
        :root {
             --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
                --bs-body-font-family: Nunito, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;

            --bs-primary: #fd7e14;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .navbar-brand span {
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        /* Make nav links more compact */
        .navbar-nav .nav-link {
            padding: 0.25rem 0.75rem;
            font-size: 0.9rem;
        }
        .navbar-nav .nav-link.active {
            color: var(--bs-primary) !important;
            font-weight: 600;
            border-bottom: 2px solid var(--bs-primary);
        }
        .navbar-nav .btn {
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <img src="../assets/img/lgu logo.png" alt="LGU Logo" class="img-fluid">
            <span>Maogmang Daet</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" 
                aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
                <li class="nav-item"><a class="nav-link <?= $currentFile==='dashboard.php'?'active':'' ?>" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='executive.php'?'active':'' ?>" href="executive.php">Executive</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='hearings.php'?'active':'' ?>" href="hearings.php">Hearings</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='ordinances.php'?'active':'' ?>" href="ordinances.php">Ordinances</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='resolutions.php'?'active':'' ?>" href="resolutions.php">Resolutions</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='announcements.php'?'active':'' ?>" href="announcements.php">Announcements</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentFile==='news.php'?'active':'' ?>" href="news.php">News</a></li>
                 <li class="nav-item"><a class="nav-link <?= $currentFile==='contact_messages.php'?'active':'' ?>" href="contact_messages.php">Contact Messages</a></li>
                <?php if ($user): ?>
                    <li class="nav-item small text-muted px-2">Hi, <?= e($user['email']) ?></li>
                    <li class="nav-item">
                        <a class="btn btn-primary fw-semibold" href="logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container mt-5 pt-5">
