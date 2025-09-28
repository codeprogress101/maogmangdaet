<?php
  $activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Maogmang Daet'; ?></title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Varela+Round"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
      rel="stylesheet"
    />

    <!-- Global Styles -->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />

    <!-- Three.js Import Map (Loader Only) -->
    <script type="importmap">
      {
        "imports": {
          "three": "https://cdn.jsdelivr.net/npm/three@0.155.0/build/three.module.js",
          "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.155.0/examples/jsm/"
        }
      }
    </script>

    <?php
      if (!empty($page_head_includes)) {
        echo $page_head_includes . "\n";
      }
      if (!empty($page_head_scripts)) {
        echo $page_head_scripts . "\n";
      }
    ?>
  </head>
  <body id="page-top">
    <?php include 'loader.html'; ?>

    <!-- Navigation -->
    <nav
      class="navbar navbar-expand-lg navbar-dark bg-white fixed-top shadow-sm"
      id="mainNav"
    >
      <div class="container px-4 px-lg-5">
          <a class="navbar-brand d-flex align-items-center" href="index.php">
          <img
            src="assets/img/lgu logo.png"
            alt="LGU Logo"
            class="me-2"
          />
          Maogmang Daet
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarResponsive"
          aria-controls="navbarResponsive"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'about' ? ' active' : ''; ?>"
                <?php echo $activePage === 'about' ? 'aria-current="page"' : ''; ?>
                href="about.php"
              >
                About
              </a>
            </li>
            

            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'government' ? ' active' : ''; ?>"
                <?php echo $activePage === 'government' ? 'aria-current="page"' : ''; ?>
                href="government.php"
              >
                Government
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'news' ? ' active' : ''; ?>"
                <?php echo $activePage === 'news' ? 'aria-current="page"' : ''; ?>
                href="news_update.php"
              >
                News
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'transparency' ? ' active' : ''; ?>"
                <?php echo $activePage === 'transparency' ? 'aria-current="page"' : ''; ?>
                href="transparency.php"
              >
                Transparency
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'tourism' ? ' active' : ''; ?>"
                <?php echo $activePage === 'tourism' ? 'aria-current="page"' : ''; ?>
                href="tourism.php"
              >
                Tourism
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link<?php echo $activePage === 'services' ? ' active' : ''; ?>"
                <?php echo $activePage === 'services' ? 'aria-current="page"' : ''; ?>
                href="services.php"
              >
                Services
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>