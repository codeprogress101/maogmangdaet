<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';

$page_title = 'Transparency';
$activePage = 'transparency';

// -----------------------------------------------------------------------------
// Fetch latest records for transparency-related sections
// -----------------------------------------------------------------------------
$latestOrdinances = [];
$latestResolutions = [];
$latestIssuances = [];

try {
    $latestOrdinances = fetchLatestDocuments('ordinances', 5);
} catch (Throwable $exception) {
    $latestOrdinances = [];
}

try {
    $latestResolutions = fetchLatestDocuments('resolutions', 5);
} catch (Throwable $exception) {
    $latestResolutions = [];
}

try {
    $latestIssuances = fetchLatestDocuments('executive_issuances', 5);
} catch (Throwable $exception) {
    $latestIssuances = [];
}

/**
 * Format a document date for display.
 */
function transparencyFormatDate(?string $date): string
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    return $timestamp ? date('F j, Y', $timestamp) : '';
}

/**
 * Resolve the public URL for a stored PDF file.
 */
function transparencyDocumentUrl(?string $path): ?string
{
    $publicPath = publicUploadPath($path);

    return $publicPath !== null ? htmlspecialchars($publicPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null;
}

include 'header.php';
?>

<main class="py-5 mt-5 bg-light">
  <div class="container py-5">
    <!-- Transparency breadcrumb and page heading -->
    <div class="row mb-4">
      <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transparency</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-bold">Transparency</h1>
        <p class="lead text-muted">
          Access key public documents and updates in compliance with the Department of Information and Communications Technology (DICT) transparency requirements.
        </p>
      </div>
    </div>

    <div class="row g-4">
      <!-- Annual Budget section -->
      <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Annual Budget</h2>
            <p class="card-text text-muted">Detailed information on the Local Government Unit's annual budget allocations.</p>
            <div class="alert alert-info mb-0" role="status">
              Coming Soon
            </div>
          </div>
        </div>
      </div>

      <!-- Procurement Opportunities / Bids and Awards section -->
      <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Procurement Opportunities / Bids and Awards</h2>
            <p class="card-text text-muted">Announcements and documents related to procurement activities, bidding schedules, and awards.</p>
            <div class="alert alert-info mb-0" role="status">
              Coming Soon
            </div>
          </div>
        </div>
      </div>

      <!-- Full Disclosure of Ordinances and Resolutions section -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Full Disclosure of Ordinances and Resolutions</h2>
            <p class="card-text text-muted">Latest approved ordinances and resolutions of the LGU.</p>
            <div class="row g-4">
              <div class="col-12 col-xl-6">
                <div class="border rounded h-100">
                  <div class="p-3 bg-light border-bottom">
                    <h3 class="h5 mb-0">Ordinances</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                      <thead class="table-secondary">
                        <tr>
                          <th scope="col">Title</th>
                          <th scope="col" style="width: 160px">Date</th>
                          <th scope="col" class="text-center" style="width: 160px">Document</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($latestOrdinances)): ?>
                          <?php foreach ($latestOrdinances as $document): ?>
                            <?php
                              $title = htmlspecialchars($document['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                              $createdAt = transparencyFormatDate($document['created_at'] ?? null);
                              $downloadUrl = transparencyDocumentUrl($document['pdf_path'] ?? null);
                            ?>
                            <tr>
                              <td><?= $title; ?></td>
                              <td><?= $createdAt !== '' ? htmlspecialchars($createdAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '<span class="text-muted">Not available</span>'; ?></td>
                              <td class="text-center">
                                <?php if ($downloadUrl !== null): ?>
                                  <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl; ?>" target="_blank" rel="noopener">
                                    <i class="bi bi-file-earmark-arrow-down me-1" aria-hidden="true"></i>
                                    <span class="visually-hidden">Download PDF for <?= $title; ?></span>
                                    Download PDF
                                  </a>
                                <?php else: ?>
                                  <span class="text-muted">Not available</span>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="3" class="text-center text-muted">No ordinances have been published yet.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-6">
                <div class="border rounded h-100">
                  <div class="p-3 bg-light border-bottom">
                    <h3 class="h5 mb-0">Resolutions</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                      <thead class="table-secondary">
                        <tr>
                          <th scope="col">Title</th>
                          <th scope="col" style="width: 160px">Date</th>
                          <th scope="col" class="text-center" style="width: 160px">Document</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($latestResolutions)): ?>
                          <?php foreach ($latestResolutions as $document): ?>
                            <?php
                              $title = htmlspecialchars($document['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                              $createdAt = transparencyFormatDate($document['created_at'] ?? null);
                              $downloadUrl = transparencyDocumentUrl($document['pdf_path'] ?? null);
                            ?>
                            <tr>
                              <td><?= $title; ?></td>
                              <td><?= $createdAt !== '' ? htmlspecialchars($createdAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '<span class="text-muted">Not available</span>'; ?></td>
                              <td class="text-center">
                                <?php if ($downloadUrl !== null): ?>
                                  <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl; ?>" target="_blank" rel="noopener">
                                    <i class="bi bi-file-earmark-arrow-down me-1" aria-hidden="true"></i>
                                    <span class="visually-hidden">Download PDF for <?= $title; ?></span>
                                    Download PDF
                                  </a>
                                <?php else: ?>
                                  <span class="text-muted">Not available</span>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="3" class="text-center text-muted">No resolutions have been published yet.</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Executive Orders / Issuances section -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Executive Orders / Issuances</h2>
            <p class="card-text text-muted">Recent executive orders and issuances from the Office of the Mayor.</p>
            <div class="table-responsive">
              <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-secondary">
                  <tr>
                    <th scope="col">Title</th>
                    <th scope="col" style="width: 160px">Date</th>
                    <th scope="col" class="text-center" style="width: 160px">Document</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($latestIssuances)): ?>
                    <?php foreach ($latestIssuances as $document): ?>
                      <?php
                        $title = htmlspecialchars($document['title'] ?? 'Untitled', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $createdAt = transparencyFormatDate($document['created_at'] ?? null);
                        $downloadUrl = transparencyDocumentUrl($document['pdf_path'] ?? null);
                      ?>
                      <tr>
                        <td><?= $title; ?></td>
                        <td><?= $createdAt !== '' ? htmlspecialchars($createdAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '<span class="text-muted">Not available</span>'; ?></td>
                        <td class="text-center">
                          <?php if ($downloadUrl !== null): ?>
                            <a class="btn btn-sm btn-outline-primary" href="<?= $downloadUrl; ?>" target="_blank" rel="noopener">
                              <i class="bi bi-file-earmark-arrow-down me-1" aria-hidden="true"></i>
                              <span class="visually-hidden">Download PDF for <?= $title; ?></span>
                              Download PDF
                            </a>
                          <?php else: ?>
                            <span class="text-muted">Not available</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="text-center text-muted">No executive issuances have been published yet.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Reports section -->
      <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Financial Reports (Statement of Receipts and Expenditures)</h2>
            <p class="card-text text-muted">Quarterly and annual Statements of Receipts and Expenditures.</p>
            <div class="alert alert-info mb-0" role="status">
              Coming Soon
            </div>
          </div>
        </div>
      </div>

      <!-- Downloadable Documents section -->
      <div class="col-12 col-lg-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">Downloadable Documents</h2>
            <p class="card-text text-muted">Centralized repository of publicly accessible PDF files.</p>
            <div class="alert alert-info mb-0" role="status">
              Coming Soon
            </div>
          </div>
        </div>
      </div>

      <!-- External portals section -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="h4 card-title">External Transparency Portals</h2>
            <p class="card-text text-muted">Access official national government portals for procurement, transparency, and public information.</p>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="fw-semibold">Philippine Government Electronic Procurement System (PhilGEPS)</span>
                  <div class="text-muted small">Official portal for government procurement opportunities.</div>
                </div>
                <a class="btn btn-outline-primary" href="https://philgeps.gov.ph/" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>
                  <span class="visually-hidden">Open PhilGEPS in a new tab</span>
                  Visit Portal
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="fw-semibold">Full Disclosure Policy Portal</span>
                  <div class="text-muted small">Department of the Interior and Local Government transparency resources.</div>
                </div>
                <a class="btn btn-outline-primary" href="https://fdpp.dilg.gov.ph/" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>
                  <span class="visually-hidden">Open the Full Disclosure Policy Portal in a new tab</span>
                  Visit Portal
                </a>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="fw-semibold">Official Gazette of the Republic of the Philippines (Gov.ph)</span>
                  <div class="text-muted small">National government updates, issuances, and public advisories.</div>
                </div>
                <a class="btn btn-outline-primary" href="https://www.officialgazette.gov.ph/" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>
                  <span class="visually-hidden">Open the Official Gazette in a new tab</span>
                  Visit Portal
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>