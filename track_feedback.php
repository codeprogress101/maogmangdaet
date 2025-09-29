<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/feedback.php';

$pdo = getDatabaseConnection();
$ticketNumber = strtoupper(trim((string) ($_GET['ticket'] ?? '')));
$ticket = null;
$updates = [];
$attachments = [];
$errorMessage = '';

if ($ticketNumber !== '') {
    $ticket = feedback_get_ticket_by_number($pdo, $ticketNumber);
    if ($ticket) {
        $updates = feedback_get_ticket_updates($pdo, (int) $ticket['id']);
        $attachments = feedback_get_ticket_attachments($pdo, (int) $ticket['id']);
    } else {
        $errorMessage = 'We could not find a ticket with that number. Please double-check and try again.';
    }
}

$page_title = 'Track Feedback Ticket';
include __DIR__ . '/header.php';
?>
<section class="py-5 about-header" style="background-color: #fff7f0;">
  <div class="container px-4 px-lg-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">Citizen Feedback Tracker</h1>
            <p class="text-muted mb-4">Enter your ticket number to review your original submission, follow department updates, and download shared files.</p>
            <form method="get" class="row g-3">
              <div class="col-md-8">
                <label for="ticketNumber" class="form-label">Ticket Number</label>
                <input type="text" class="form-control" id="ticketNumber" name="ticket" value="<?= htmlspecialchars($ticketNumber, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="e.g., FB-2025-0001" maxlength="20" required>
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Track Ticket</button>
              </div>
            </form>
            <?php if ($errorMessage): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?= htmlspecialchars($errorMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <?php if ($ticket): ?>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
              <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3">
                <div>
                  <h2 class="h4 fw-semibold mb-1">Ticket <?= htmlspecialchars($ticket['ticket_number'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
                  <p class="mb-0 text-muted">Submitted on <?= htmlspecialchars(date('F j, Y g:i A', strtotime((string) $ticket['created_at'])), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                </div>
                <div class="mt-3 mt-lg-0">
                  <?php
                  $latestStatus = feedback_latest_status($pdo, (int) $ticket['id']);
                  $statusLabel = $latestStatus['status'] ?? 'Open';
                  $statusClass = match ($statusLabel) {
                      'Resolved', 'Closed' => 'success',
                      'In Progress' => 'info',
                      default => 'secondary',
                  };
                  ?>
                  <span class="badge bg-<?= $statusClass ?> px-3 py-2 fs-6">Current Status: <?= htmlspecialchars($statusLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                </div>
              </div>
              <div class="mb-3">
                <h3 class="h5 fw-semibold">Category</h3>
                <p class="text-muted mb-0"><?= htmlspecialchars($ticket['category'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
              </div>
              <div class="mb-3">
                <h3 class="h5 fw-semibold">Your Message</h3>
                <p class="text-muted" style="white-space: pre-line;"><?= htmlspecialchars($ticket['message'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
              </div>
              <?php if ($attachments): ?>
                <div class="mb-4">
                  <h3 class="h5 fw-semibold">Submitted Attachments</h3>
                  <ul class="list-group list-group-flush">
                    <?php foreach ($attachments as $file): ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars(basename((string) $file['file_path']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                        <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($file['file_path'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" download>Download</a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
              <h3 class="h5 fw-semibold mb-0">Update Timeline</h3>
            </div>
            <div class="card-body">
              <?php if (!$updates): ?>
                <p class="text-muted mb-0">No updates have been recorded yet. Please check again soon.</p>
              <?php else: ?>
                <div class="timeline">
                  <?php foreach ($updates as $update): ?>
                    <div class="timeline-item pb-4">
                      <div class="d-flex flex-column flex-md-row align-items-md-start gap-3">
                        <div class="timeline-icon text-primary">
                          <i class="bi bi-chat-dots-fill"></i>
                        </div>
                        <div class="timeline-content flex-grow-1">
                          <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 mb-2">
                            <span class="badge bg-primary text-uppercase">Status: <?= htmlspecialchars($update['status'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                            <span class="text-muted small">Updated <?= htmlspecialchars(date('F j, Y g:i A', strtotime((string) $update['updated_at'])), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                          </div>
                          <?php if (!empty($update['assigned_to'])): ?>
                            <p class="mb-1"><strong>Assigned Department:</strong> <?= htmlspecialchars($update['assigned_to'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                          <?php endif; ?>
                          <?php if (!empty($update['admin_response'])): ?>
                            <p class="mb-2" style="white-space: pre-line;"><?= htmlspecialchars($update['admin_response'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                          <?php else: ?>
                            <p class="text-muted mb-2">No additional notes provided.</p>
                          <?php endif; ?>
                          <p class="text-muted small mb-2">Updated by: <?= htmlspecialchars($update['updated_by'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                          <?php if (!empty($update['attachment_path'])): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($update['attachment_path'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" download>Download attachment</a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>
<style>
  .timeline {
    position: relative;
    padding-left: 1.5rem;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: rgba(253, 126, 20, 0.3);
  }
  .timeline-item {
    position: relative;
  }
  .timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(253, 126, 20, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }
  .timeline-item::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #fd7e14;
  }
  @media (min-width: 768px) {
    .timeline {
      padding-left: 2.5rem;
    }
    .timeline::before {
      left: 32px;
    }
    .timeline-item::before {
      left: 31px;
    }
    .timeline-icon {
      width: 48px;
      height: 48px;
      font-size: 1.5rem;
    }
  }
</style>
<script src="js/autoadjustheight.js"></script>
<?php include __DIR__ . '/footer.php';
