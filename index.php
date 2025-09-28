<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';

$page_title = 'Maogmang Daet';
$activePage = 'home';

function documentRouteSegment(string $table): string
{
  switch ($table) {
        case 'announcements':
            return 'announcements';
        case 'executive_issuances':
            return 'executive-issuances';
        case 'ordinances':
            return 'ordinances';
        case 'resolutions':
            return 'resolutions';
        case 'public_hearings':
            return 'public-hearings';
        default:
            throw new InvalidArgumentException('Unsupported document type.');
    }
}

function buildDocumentUrl(string $table, array $document): string
{
    $slug = isset($document['slug']) ? rawurlencode((string) $document['slug']) : '';

    return sprintf('/%s/%d/%s', documentRouteSegment($table), (int) $document['id'], $slug);
}

function buildDocumentExcerpt(string $description, int $length = 160): string
{
  $plain = trim(preg_replace('/\s+/', ' ', strip_tags($description)) ?? '');

    if (mb_strlen($plain) <= $length) {
        return $plain;
    }

    return rtrim(mb_substr($plain, 0, $length - 3)) . '...';
}

function buildDocumentFileUrl(?string $path): ?string
{
   return publicUploadPath($path);
}

function formatDocumentDate(string $date): string
{
    return date('F j, Y', strtotime($date));
}

$executiveIssuances = fetchLatestDocuments('executive_issuances', 3);
$publicHearings = fetchLatestDocuments('public_hearings', 3);
$recentOrdinances = fetchLatestDocuments('ordinances', 3);
$resolutions = fetchLatestDocuments('resolutions', 3);
$announcements = fetchLatestDocuments('announcements', 2);

$latestNews = fetchLatestNews(3);
$featuredNews = $latestNews[0] ?? null;
$secondaryNews = array_slice($latestNews, 1);

include 'header.php';

?>

 <!-- Masthead-->
    <header class="masthead">
      <div
        class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center"
      >
        <div class="d-flex justify-content-center">
          <div class="text-center">
        <div class="masthead-button">
              <a class="btn btn-primary" href="#about">Get Started</a>
            </div>
          </div>
        </div>
      </div>
    </header>


    <!-- About-->
    <section class="about-section text-center" id="about">
      <video autoplay muted loop playsinline id="aboutVideo">
        <source src="assets/video/BACKGROUND VIDEO.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
      <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
          <div class="col-lg-8">
            <h2 class="text-white mb-4">
              W E L C O M E &nbsp T O &nbsp D A E T
            </h2>
            <p>
              Ang Bayan ng Daet ang kabisera ng lalawigan ng Camarines Norte, na
              nagsisilbing sentro ng pamahalaan, kalakalan, at edukasyon sa
              rehiyon. Kilala ang bayan sa kasaysayan bilang tahanan ng
              kauna-unahang bantayog para kay Dr. Jose Rizal sa bansa, na
              sumasalamin sa makabayang kultura ng mga mamamayan nito.
            </p>
          </div>
        </div>

        </div>
    </section>


<!-- WHAT'S NEW SECTION -->
<section
  class="updates-section bg-light py-4"
  id="updates"
  style="min-height: 100vh"
>
  <div class="container px-4 px-lg-5">
    <!-- Section Title -->
    <h2
      class="text-center mb-4 animate-on-scroll"
      data-animate="animate__fadeInDown"
    >
      What's New in Daet?
    </h2>

  <div class="row">
      <!-- Left column -->
      <div class="col-lg-8">
        <!-- Executive Issuances -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInLeft" id="executive-issuances">
          <h6 class="fw-bold mb-1">Executive Issuances</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($executiveIssuances)): ?>
              <?php foreach ($executiveIssuances as $document): ?>
                <?php
                  $detailUrl = htmlspecialchars(buildDocumentUrl('executive_issuances', $document), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $pdfUrl = $document['pdf_path'] ? htmlspecialchars(buildDocumentFileUrl($document['pdf_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
                  $title = htmlspecialchars($document['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $description = htmlspecialchars(buildDocumentExcerpt($document['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $createdAtText = '';
                  if (!empty($document['created_at'])) {
                      $createdAtText = htmlspecialchars(formatDocumentDate($document['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  }
                ?>
                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                  <div>
                    <div class="fw-semibold">
                      <?= $title ?>
                    </div>
                    <?php if ($createdAtText): ?>
                      <p class="text-muted small mb-1">
                        <?= $createdAtText ?>
                      </p>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                      <?= $description ?>
                    </p>
                  </div>
                  <div class="text-md-end">
                   
                    <?php if ($pdfUrl): ?>
                      <a href="<?= $pdfUrl ?>" class="btn btn-outline-secondary btn-sm ms-md-2 mt-2 mt-md-0" target="_blank" rel="noopener">Download PDF</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Public Hearings -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInRight" id="public-hearings">
          <h6 class="fw-bold mb-1">Public Hearings</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($publicHearings)): ?>
              <?php foreach ($publicHearings as $hearing): ?>
                <?php
                  $detailUrl = htmlspecialchars(buildDocumentUrl('public_hearings', $hearing), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $pdfUrl = $hearing['pdf_path'] ? htmlspecialchars(buildDocumentFileUrl($hearing['pdf_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
                  $title = htmlspecialchars($hearing['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $description = htmlspecialchars(buildDocumentExcerpt($hearing['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $createdAtText = '';
                  if (!empty($hearing['created_at'])) {
                      $createdAtText = htmlspecialchars(formatDocumentDate($hearing['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  }
                ?>
                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                  <div>
                    <div class="fw-semibold">
                      <?= $title ?>
                    </div>
                    <?php if ($createdAtText): ?>
                      <p class="text-muted small mb-1">
                        <?= $createdAtText ?>
                      </p>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                      <?= $description ?>
                    </p>
                  </div>
                  <div class="text-md-end">
                   
                    <?php if ($pdfUrl): ?>
                      <a href="<?= $pdfUrl ?>" class="btn btn-outline-secondary btn-sm ms-md-2 mt-2 mt-md-0" target="_blank" rel="noopener">Download PDF</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Ordinances -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInLeft" id="ordinances">
          <h6 class="fw-bold mb-1">Recent Ordinances</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($recentOrdinances)): ?>
              <?php foreach ($recentOrdinances as $ordinance): ?>
                <?php
                  $detailUrl = htmlspecialchars(buildDocumentUrl('ordinances', $ordinance), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $pdfUrl = $ordinance['pdf_path'] ? htmlspecialchars(buildDocumentFileUrl($ordinance['pdf_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
                  $title = htmlspecialchars($ordinance['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $description = htmlspecialchars(buildDocumentExcerpt($ordinance['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $createdAtText = '';
                  if (!empty($ordinance['created_at'])) {
                      $createdAtText = htmlspecialchars(formatDocumentDate($ordinance['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  }
                ?>
                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                  <div>
                    <div class="fw-semibold">
                      <?= $title ?>
                    </div>
                    <?php if ($createdAtText): ?>
                      <p class="text-muted small mb-1">
                        <?= $createdAtText ?>
                      </p>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                      <?= $description ?>
                    </p>
                  </div>
                  <div class="text-md-end">
                   
                    <?php if ($pdfUrl): ?>
                      <a href="<?= $pdfUrl ?>" class="btn btn-outline-secondary btn-sm ms-md-2 mt-2 mt-md-0" target="_blank" rel="noopener">Download PDF</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Resolutions -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInRight" id="resolutions">
          <h6 class="fw-bold mb-1">Resolutions</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($resolutions)): ?>
              <?php foreach ($resolutions as $resolution): ?>
                <?php
                  $detailUrl = htmlspecialchars(buildDocumentUrl('resolutions', $resolution), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $pdfUrl = $resolution['pdf_path'] ? htmlspecialchars(buildDocumentFileUrl($resolution['pdf_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
                  $title = htmlspecialchars($resolution['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $description = htmlspecialchars(buildDocumentExcerpt($resolution['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $createdAtText = '';
                  if (!empty($resolution['created_at'])) {
                      $createdAtText = htmlspecialchars(formatDocumentDate($resolution['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  }
                ?>
                <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                  <div>
                    <div class="fw-semibold">
                      <?= $title ?>
                    </div>
                    <?php if ($createdAtText): ?>
                      <p class="text-muted small mb-1">
                        <?= $createdAtText ?>
                      </p>
                    <?php endif; ?>
                    <p class="text-muted small mb-0">
                      <?= $description ?>
                    </p>
                  </div>
                  <div class="text-md-end">
                   
                    <?php if ($pdfUrl): ?>
                      <a href="<?= $pdfUrl ?>" class="btn btn-outline-secondary btn-sm ms-md-2 mt-2 mt-md-0" target="_blank" rel="noopener">Download PDF</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="col-lg-4">
        <!-- Weather -->
        <div class="card text-center mb-3 animate-on-scroll" data-animate="animate__fadeInUp">
          <div class="card-body bg-primary text-white p-3">
            <h6 class="card-title mb-2">Daet Weather</h6>
            <div id="weather-today"><p class="mb-2">Loading...</p></div>
            <div id="weather-forecast" class="d-flex justify-content-around small flex-wrap"></div>
          </div>
        </div>

        <!-- Map -->
        <div class="card mb-3 animate-on-scroll" data-animate="animate__fadeInUp">
          <div class="card-header bg-primary text-white text-center py-2">📍 Daet</div>
          <div class="ratio ratio-16x9">
            <iframe src="https://www.google.com/maps?q=Daet,Camarines%20Norte&output=embed" style="border:0" allowfullscreen loading="lazy"></iframe>
          </div>
        </div>

        <!-- Announcements -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInUp" id="announcements">
          <h6 class="fw-bold mb-2">Announcements</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($announcements)): ?>
              <?php foreach ($announcements as $announcement): ?>
                <?php
                  $detailUrl = htmlspecialchars(buildDocumentUrl('announcements', $announcement), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $pdfUrl = $announcement['pdf_path'] ? htmlspecialchars(buildDocumentFileUrl($announcement['pdf_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
                  $title = htmlspecialchars($announcement['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $description = htmlspecialchars(buildDocumentExcerpt($announcement['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $createdAtText = '';
                  if (!empty($announcement['created_at'])) {
                      $createdAtText = htmlspecialchars(formatDocumentDate($announcement['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  }
                ?>
                <div class="list-group-item border-start border-4 border-warning d-flex flex-column gap-2">
                  <div>
                    <div class="fw-semibold text-warning mb-1"><?= $title ?></div>
                    <?php if ($createdAtText): ?>
                      <div class="text-muted small mb-1">Published <?= $createdAtText ?></div>
                    <?php endif; ?>
                    <p class="text-muted small mb-0"><?= $description ?></p>
                  </div>
                  <div>
                    <a href="<?= $detailUrl ?>" class="btn btn-warning btn-sm text-white">Read more</a>
                    <?php if ($pdfUrl): ?>
                      <a href="<?= $pdfUrl ?>" class="btn btn-outline-secondary btn-sm ms-2" target="_blank" rel="noopener">Download PDF</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No announcements available.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Socials -->
        <div class="text-center animate-on-scroll" data-animate="animate__fadeInUp">
          <h6>Follow Us</h6>
          <a href="https://www.facebook.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="https://www.youtube.com/@lgudaet" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>




<!-- Projects-->
    <section class="projects-section bg-light" id="projects">
      <div class="container px-4 px-lg-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between mb-4">
          <h2 class="mx-auto mx-lg-0 mt-2 mb-3 mb-lg-0">News and Updates</h2>
          <a class="btn btn-outline-primary" href="news_update.php">View all news</a>
        </div>
        <?php if ($featuredNews): ?>
          <?php
            $featuredUrl = buildNewsDetailUrl($featuredNews);
            $featuredTitle = htmlspecialchars($featuredNews['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $featuredExcerpt = htmlspecialchars(newsExcerpt($featuredNews['content'], 260), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $featuredDate = htmlspecialchars(formatNewsDatePublic($featuredNews['created_at']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
          ?>
          <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
            <div class="col-xl-8 col-lg-7">
              
              
              <?php $featuredImagePath = publicUploadPath($featuredNews['image_path'] ?? null); ?>
              <?php if ($featuredImagePath): ?>
                <img class="img-fluid mb-3 mb-lg-0 w-100 rounded shadow-sm" src="<?= htmlspecialchars($featuredImagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"  alt="<?= $featuredTitle ?>">
              <?php else: ?>
                <div class="bg-secondary-subtle rounded mb-3 mb-lg-0 w-100 d-flex align-items-center justify-content-center" style="min-height: 320px;">
                  <span class="text-muted">No image available</span>
                </div>
              <?php endif; ?>
            </div>
            <div class="col-xl-4 col-lg-5">
              <div class="featured-text text-center text-lg-left">
                <h4><?= $featuredTitle ?></h4>
                <p class="text-black-50 mb-2"><?= $featuredDate ?></p>
                <p class="text-black-50 mb-0"><?= $featuredExcerpt ?></p>
                <a href="<?= htmlspecialchars($featuredUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="text-primary fw-semibold d-inline-flex align-items-center gap-2 mt-3">
                  Read more
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <p class="text-muted">No news articles have been published yet.</p>
        <?php endif; ?>
<?php if ($secondaryNews): ?>
  <?php foreach ($secondaryNews as $index => $news): ?>
    <?php
      $newsUrl = buildNewsDetailUrl($news);
      $newsTitle = htmlspecialchars($news['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $newsExcerptText = htmlspecialchars(newsExcerpt($news['content']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $newsImagePath = publicUploadPath($news['image_path'] ?? null) ?: 'assets/img/default.jpg';

      // alternate classes
      $rowClass   = ($index % 2 === 0) ? 'row gx-0 mb-5 mb-lg-0 justify-content-center' : 'row gx-0 justify-content-center';
      $textAlign  = ($index % 2 === 0) ? 'text-lg-left' : 'text-lg-right';
      $orderClass = ($index % 2 === 0) ? '' : ' order-lg-first';
    ?>
    <div class="<?= $rowClass ?>">
      <!-- Image column -->
      <div class="col-lg-6">
        <img class="img-fluid" src="<?= htmlspecialchars($newsImagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" alt="<?= $newsTitle ?>" />
      </div>

      <!-- Text column -->
      <div class="col-lg-6<?= $orderClass ?>">
        <div class="bg-black text-center h-100 project">
          <div class="d-flex h-100">
            <div class="project-text w-100 my-auto text-center <?= $textAlign ?>">
              <h4 class="text-white"><?= $newsTitle ?></h4>
              <p class="mb-0 text-white-50"><?= $newsExcerptText ?></p>
              <a href="<?= htmlspecialchars($newsUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="text-primary">
                See more...
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>



      </div>
    </section>
   <!-- OPPORTUNITIES SECTION -->
    <section
      class="opportunities-section d-flex flex-column justify-content-center align-items-center text-center"
      id="opportunities"
      style="min-height: 100vh; background-color: #f8f9fa"
    >
      <div class="container">
        <!-- Title -->
        <h2 class="mb-4 animate__animated animate__fadeInDown">
          STARTUPS & OTHER OPPORTUNITIES
        </h2>

        <!-- Video -->
        <div class="video-container mb-4 animate__animated animate__fadeInUp">
          <video
            id="opportunityVideo"
            loop
            playsinline
            class="w-100 rounded shadow"
          >
            <source src="assets/video/Seminar.mp4" type="video/mp4" />
            Your browser does not support the video tag.
          </video>
        </div>

        <!-- Button -->
        <a
          href="#more-opportunities"
          class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-1s"
        >
          Learn More
        </a>
      </div>
    </section>

    <section class="daet-section" id="daet">
      <div class="container">
        <h5 class="section-subtitle">
          THE CAPITAL TOWN OF THE GATEWAY TO BICOLANDIA
        </h5>
        <h2 class="section-title">MAOGMANG DAET</h2>
        <p class="section-desc">
          The Municipality of Daet is a vibrant town known for its history,
          culture, and warm community spirit. Famous for the first Rizal
          monument, its golden sandy beaches, and delicious local delicacies,
          Daet is a blend of heritage and modern progress, making it a unique
          place to explore in Camarines Norte.
        </p>

        <!-- Gallery -->
        <div class="gallery-layout">
          <!-- Large Image -->
          <div class="gallery-large">
            <img src="assets/img/surf.jpg" alt="Daet Large" id="large-photo" />
          </div>

          <!-- Small Images -->
          <div class="gallery-small">
            <img src="assets/img/daet2.jpg" alt="Daet Small" />
            <img src="assets/img/daet3.jpg" alt="Daet Small" />
            <img src="assets/img/daet4.jpg" alt="Daet Small" />
            <img src="assets/img/daet5.jpg" alt="Daet Small" />
          </div>
        </div>

        <!-- Explore Daet Button -->
        <div class="explore-btn-wrapper">
          <a href="tourism.php" class="explore-btn">Explore Daet</a>
        </div>
      </div>
    </section>


<section class="py-5 bg-light" id="quick-links">
  <div class="container px-4 px-lg-5">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Quick Links</h2>
      <p class="text-muted mb-0">Access key citizen services and transparency resources in one tap.</p>
    </div>
    <div class="row g-4">
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex flex-column">
            <div class="mb-3 text-primary"><i class="bi bi-people-fill fs-1"></i></div>
            <h3 class="h5">Citizen's Charter</h3>
            <p class="text-muted flex-grow-1">Review frontline service commitments and processing times mandated by the LGU.</p>
            <a class="btn btn-outline-primary mt-3" href="citizens-charter.php">View Charter</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex flex-column">
            <div class="mb-3 text-primary"><i class="bi bi-shield-check fs-1"></i></div>
            <h3 class="h5">Transparency Portal</h3>
            <p class="text-muted flex-grow-1">Browse disclosure reports, budgets, and procurement updates for public accountability.</p>
            <a class="btn btn-outline-primary mt-3" href="transparency.php">Go to Transparency</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex flex-column">
            <div class="mb-3 text-primary"><i class="bi bi-chat-text fs-1"></i></div>
            <h3 class="h5">Feedback Form</h3>
            <p class="text-muted flex-grow-1">Send suggestions or report service gaps to help us improve municipal programs.</p>
            <a class="btn btn-outline-primary mt-3" href="feedback.php">Send Feedback</a>
          </div>
        </div>
      </div>
      
      
    </div>
  </div>
</section>


 <!-- Emergency Hotlines -->
    <section class="hotlines-section">
      <div class="container px-4 px-lg-5">
        <!-- Section Title -->
        <h2 class="hotlines-title text-center">EMERGENCY HOTLINES</h2>

        <div class="row gx-4 gx-lg-5 text-center">
          <!-- Mobile -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-mobile-alt hotline-icon"></i>
            <h4 class="hotline-heading">Mobile (COMCEN)</h4>
            <p>+63 908-525-3000 | Smart</p>
            <p>+63 963-220-9700 | TNT</p>
          </div>

          <!-- Landline -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-phone-alt hotline-icon"></i>
            <h4 class="hotline-heading">Landline (COMCEN)</h4>
            <p>(054) 472-3000</p>
            <p>(054) 8712050 local 3000 (PLDT)</p>
          </div>

          <!-- Fire Department -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-fire-alt hotline-icon"></i>
            <h4 class="hotline-heading">Bureau of Fire Protection</h4>
            <p>(+63) 923-0839429 | (054) 473-8472</p>
            <p>(054) 473-2633 | (054) 8716454</p>
          </div>
        </div>

        <!-- Button -->
        <div class="text-center mt-4">
          <a href="#important-numbers" class="hotlines-btn">
            List of Important Numbers
          </a>
        </div>
      </div>
    </section>

    <?php
$page_footer_scripts = <<<'HTML'
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    HTML;
include 'footer.php';
?>