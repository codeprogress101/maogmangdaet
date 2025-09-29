<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
$user = current_user();
$currentFile = basename($_SERVER['PHP_SELF']); // detect current page
$navItems = [
    ['module' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
    ['module' => 'executive', 'label' => 'Executive', 'href' => 'executive.php'],
    ['module' => 'hearings', 'label' => 'Hearings', 'href' => 'hearings.php'],
    ['module' => 'ordinances', 'label' => 'Ordinances', 'href' => 'ordinances.php'],
    ['module' => 'resolutions', 'label' => 'Resolutions', 'href' => 'resolutions.php'],
    ['module' => 'announcements', 'label' => 'Announcements', 'href' => 'announcements.php'],
    ['module' => 'news', 'label' => 'News', 'href' => 'news.php'],
    ['module' => 'contact_messages', 'label' => 'Messages', 'href' => 'contact_messages.php'],
    ['module' => 'manage_users', 'label' => 'Users', 'href' => 'manage_users.php'],
];

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
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
      <img src="../assets/img/lgu logo.png" alt="LGU Logo" class="img-fluid" style="width:36px;height:36px;object-fit:contain;">
      <span>Maogmang Daet</span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" 
            aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
        <?php foreach ($navItems as $item): ?>
            <?php
                $module = $item['module'];
                $shouldDisplay = $module === 'dashboard'
                    ? (bool) $user
                    : ($user && user_can_access($module));

                if (!$shouldDisplay) {
                    continue;
                }

                $isActive = $currentFile === basename($item['href']);
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= e($item['href']) ?>"><?= e($item['label']) ?></a>
            </li>
        <?php endforeach; ?>
        <?php if ($user): ?>
          <!-- User Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?= e($user['email']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container mt-5 pt-5">
