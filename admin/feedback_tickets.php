<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_module_access('feedback');
require_once __DIR__ . '/../includes/feedback.php';

$pageTitle = 'Citizen Feedback Tickets';

$role = user_role();
$departmentScope = user_department();
$isDepartmentAdmin = ($role === ROLE_DEPARTMENT_ADMIN);

$categoryFilter = trim((string) ($_GET['category'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
$departmentFilter = trim((string) ($_GET['department'] ?? ''));
$exportFormat = strtolower(trim((string) ($_GET['export'] ?? '')));
$selectedTicketNumber = trim((string) ($_GET['ticket'] ?? ''));

if ($isDepartmentAdmin) {
    $departmentFilter = $departmentScope ?? '';
}

if ($categoryFilter !== '' && !feedback_validate_category($categoryFilter)) {
    $categoryFilter = '';
}

if ($statusFilter !== '' && !feedback_validate_status($statusFilter)) {
    $statusFilter = '';
}

$departmentOptions = FEEDBACK_DEPARTMENTS;
if (!in_array('Unassigned', $departmentOptions, true)) {
    $departmentOptions[] = 'Unassigned';
}

if ($departmentFilter !== '' && !in_array($departmentFilter, $departmentOptions, true)) {
    $departmentFilter = '';
}

$params = [];
$sql = 'SELECT * FROM feedback_tickets WHERE 1=1';

if ($categoryFilter !== '') {
    $sql .= ' AND category = :category';
    $params['category'] = $categoryFilter;
}

if ($isDepartmentAdmin && $departmentScope !== null) {
    $sql .= ' AND assigned_to = :deptScope';
    $params['deptScope'] = $departmentScope;
} elseif ($departmentFilter !== '') {
    if ($departmentFilter === 'Unassigned') {
        $sql .= ' AND assigned_to IS NULL';
    } else {
        $sql .= ' AND assigned_to = :department';
        $params['department'] = $departmentFilter;
    }
}

$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

$ticketSummaries = [];
foreach ($tickets as $ticket) {
    $latest = feedback_latest_status($pdo, (int) $ticket['id']);
    $currentStatus = $latest['status'] ?? 'Open';

    if ($statusFilter !== '' && $currentStatus !== $statusFilter) {
        continue;
    }

    $currentAssignment = $latest['assigned_to'] ?? $ticket['assigned_to'] ?? 'Unassigned';

    $ticketSummaries[] = [
        'id' => (int) $ticket['id'],
        'ticket_number' => $ticket['ticket_number'],
        'name' => $ticket['name'],
        'email' => $ticket['email'],
        'category' => $ticket['category'],
        'assigned_to' => $currentAssignment,
        'created_at' => $ticket['created_at'],
        'status' => $currentStatus,
        'last_update' => $latest['updated_at'] ?? $ticket['created_at'],
    ];
}

if ($exportFormat === 'csv' || $exportFormat === 'excel') {
    $filename = 'feedback_tickets_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'wb');
    fputcsv($output, ['Ticket Number', 'Name', 'Email', 'Category', 'Assigned To', 'Status', 'Created At']);
    foreach ($ticketSummaries as $row) {
        fputcsv($output, [
            $row['ticket_number'],
            $row['name'],
            $row['email'],
            $row['category'],
            $row['assigned_to'] ?? 'Unassigned',
            $row['status'],
            date('Y-m-d H:i', strtotime((string) $row['created_at'])),
        ]);
    }
    fclose($output);
    exit;
}

$selectedTicket = null;
$selectedUpdates = [];
$selectedAttachments = [];
$selectedLatest = null;

if ($selectedTicketNumber !== '') {
    $selectedTicket = feedback_get_ticket_by_number($pdo, $selectedTicketNumber);
    if (!$selectedTicket) {
        add_flash('error', 'The requested ticket could not be found.');
        redirect('feedback_tickets.php');
    }

    if ($isDepartmentAdmin && $departmentScope !== null && $selectedTicket['assigned_to'] !== $departmentScope) {
        add_flash('error', 'You are not authorized to view that ticket.');
        redirect('feedback_tickets.php');
    }

    $selectedUpdates = feedback_get_ticket_updates($pdo, (int) $selectedTicket['id']);
    $selectedAttachments = feedback_get_ticket_attachments($pdo, (int) $selectedTicket['id']);
    $selectedLatest = feedback_latest_status($pdo, (int) $selectedTicket['id']);
}

$flashMessages = get_flash_messages();
include __DIR__ . '/partials/header.php';
?>
<div class="row gy-4">
  <div class="col-xl-7">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <h1 class="h4 fw-bold mb-3">Citizen Feedback Tickets</h1>
        <form method="get" class="row g-3 align-items-end mb-3">
          <div class="col-md-4">
            <label for="filterCategory" class="form-label">Category</label>
            <select class="form-select" id="filterCategory" name="category">
              <option value="">All</option>
              <?php foreach (FEEDBACK_CATEGORIES as $category): ?>
                <option value="<?= e($category) ?>" <?= $categoryFilter === $category ? 'selected' : '' ?>><?= e($category) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterStatus" class="form-label">Status</label>
            <select class="form-select" id="filterStatus" name="status">
              <option value="">All</option>
              <?php foreach (FEEDBACK_STATUSES as $status): ?>
                <option value="<?= e($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= e($status) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterDepartment" class="form-label">Department</label>
            <select class="form-select" id="filterDepartment" name="department" <?= $isDepartmentAdmin ? 'disabled' : '' ?>>
              <option value="">All</option>
              <?php foreach ($departmentOptions as $department): ?>
                <option value="<?= e($department) ?>" <?= $departmentFilter === $department ? 'selected' : '' ?>><?= e($department) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="feedback_tickets.php" class="btn btn-outline-secondary">Reset</a>
            <div class="ms-auto d-flex gap-2">
              <a href="feedback_tickets.php?<?= http_build_query(array_filter([
                  'category' => $categoryFilter,
                  'status' => $statusFilter,
                  'department' => $isDepartmentAdmin ? null : $departmentFilter,
                  'export' => 'csv',
              ])) ?>" class="btn btn-outline-primary">Export CSV</a>
              <a href="feedback_tickets.php?<?= http_build_query(array_filter([
                  'category' => $categoryFilter,
                  'status' => $statusFilter,
                  'department' => $isDepartmentAdmin ? null : $departmentFilter,
                  'export' => 'excel',
              ])) ?>" class="btn btn-outline-success">Export Excel</a>
            </div>
          </div>
        </form>
        <?php if (!empty($flashMessages['success'])): ?>
          <div class="alert alert-success">
            <?php foreach ($flashMessages['success'] as $message): ?>
              <div><?= e($message) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($flashMessages['error'])): ?>
          <div class="alert alert-danger">
            <?php foreach ($flashMessages['error'] as $message): ?>
              <div><?= e($message) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Ticket</th>
                <th>Name</th>
                <th>Email</th>
                <th>Category</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
            <?php if (!$ticketSummaries): ?>
              <tr><td colspan="7" class="text-center py-4">No tickets found for the selected filters.</td></tr>
            <?php else: ?>
              <?php foreach ($ticketSummaries as $summary): ?>
                <tr>
                  <td><a href="feedback_tickets.php?<?= http_build_query(array_filter([
                      'category' => $categoryFilter,
                      'status' => $statusFilter,
                      'department' => $isDepartmentAdmin ? null : $departmentFilter,
                      'ticket' => $summary['ticket_number'],
                  ])) ?>" class="fw-semibold"><?= e($summary['ticket_number']) ?></a></td>
                  <td><?= e($summary['name']) ?></td>
                  <td><a href="mailto:<?= e($summary['email']) ?>" class="text-decoration-none"><?= e($summary['email']) ?></a></td>
                  <td><?= e($summary['category']) ?></td>
                  <td><?= e($summary['assigned_to'] ?? 'Unassigned') ?></td>
                  <td>
                    <?php
                    $badge = 'secondary';
                    if ($summary['status'] === 'Resolved' || $summary['status'] === 'Closed') {
                        $badge = 'success';
                    } elseif ($summary['status'] === 'In Progress') {
                        $badge = 'info';
                    }
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= e($summary['status']) ?></span>
                  </td>
                  <td><?= e(date('M j, Y g:i A', strtotime((string) $summary['created_at']))) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-5">
    <?php if ($selectedTicket): ?>
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-3">
            <div>
              <h2 class="h5 fw-bold mb-1">Ticket <?= e($selectedTicket['ticket_number']) ?></h2>
              <p class="text-muted mb-0">Submitted <?= e(date('F j, Y g:i A', strtotime((string) $selectedTicket['created_at']))) ?></p>
            </div>
            <div>
              <?php
              $currentStatus = $selectedLatest['status'] ?? 'Open';
              $badgeClass = 'secondary';
              if ($currentStatus === 'Resolved' || $currentStatus === 'Closed') {
                  $badgeClass = 'success';
              } elseif ($currentStatus === 'In Progress') {
                  $badgeClass = 'info';
              }
              ?>
              <span class="badge bg-<?= $badgeClass ?> px-3 py-2">Status: <?= e($currentStatus) ?></span>
            </div>
          </div>
          <div class="mb-3">
            <h3 class="h6 text-uppercase text-muted">Citizen Details</h3>
            <p class="mb-1"><strong>Name:</strong> <?= e($selectedTicket['name']) ?></p>
            <p class="mb-1"><strong>Email:</strong> <a href="mailto:<?= e($selectedTicket['email']) ?>" class="text-decoration-none"><?= e($selectedTicket['email']) ?></a></p>
            <p class="mb-1"><strong>Category:</strong> <?= e($selectedTicket['category']) ?></p>
            <p class="mb-0"><strong>Assigned To:</strong> <?= e($selectedTicket['assigned_to'] ?? 'Unassigned') ?></p>
          </div>
          <div class="mb-3">
            <h3 class="h6 text-uppercase text-muted">Citizen Message</h3>
            <p style="white-space: pre-line;" class="mb-0"><?= e($selectedTicket['message']) ?></p>
          </div>
          <?php if ($selectedAttachments): ?>
            <div class="mb-4">
              <h3 class="h6 text-uppercase text-muted">Citizen Attachments</h3>
              <ul class="list-unstyled mb-0">
                <?php foreach ($selectedAttachments as $file): ?>
                  <li class="mb-2">
                    <a href="<?= e($file['file_path']) ?>" class="text-decoration-none" download><?= e(basename((string) $file['file_path'])) ?></a>
                    <span class="text-muted small">(Uploaded <?= e(date('M j, Y g:i A', strtotime((string) $file['uploaded_at']))) ?>)</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
          <h3 class="h6 text-uppercase mb-0">Update Timeline</h3>
        </div>
        <div class="card-body">
          <?php if (!$selectedUpdates): ?>
            <p class="text-muted mb-0">No updates recorded yet.</p>
          <?php else: ?>
            <div class="timeline">
              <?php foreach ($selectedUpdates as $update): ?>
                <div class="timeline-item pb-4">
                  <div class="d-flex flex-column gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <span class="badge bg-primary"><?= e($update['status']) ?></span>
                      <span class="text-muted small">Updated <?= e(date('M j, Y g:i A', strtotime((string) $update['updated_at']))) ?></span>
                    </div>
                    <?php if (!empty($update['assigned_to'])): ?>
                      <p class="mb-1"><strong>Assigned To:</strong> <?= e($update['assigned_to']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($update['admin_response'])): ?>
                      <p class="mb-1" style="white-space: pre-line;"><?= e($update['admin_response']) ?></p>
                    <?php endif; ?>
                    <p class="text-muted small mb-1">Updated by <?= e($update['updated_by']) ?></p>
                    <?php if (!empty($update['attachment_path'])): ?>
                      <a class="btn btn-sm btn-outline-secondary" href="<?= e($update['attachment_path']) ?>" download>Download attachment</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <h3 class="h6 text-uppercase mb-0">Add Update</h3>
        </div>
        <div class="card-body">
          <form action="update_ticket.php" method="post" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="ticket_id" value="<?= (int) $selectedTicket['id'] ?>">
            <div class="col-12">
              <label for="updateStatus" class="form-label">Status</label>
              <select class="form-select" id="updateStatus" name="status" required>
                <?php foreach (FEEDBACK_STATUSES as $status): ?>
                  <option value="<?= e($status) ?>" <?= ($selectedLatest['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label for="updateResponse" class="form-label">Response / Notes</label>
              <textarea class="form-control" id="updateResponse" name="admin_response" rows="4" maxlength="5000" placeholder="Provide the latest actions, next steps, or resolution details."></textarea>
            </div>
            <div class="col-12">
              <label for="updateAttachment" class="form-label">Attachment</label>
              <input class="form-control" type="file" id="updateAttachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx">
              <div class="form-text">Optional. Maximum size 5&nbsp;MB. Allowed types: JPG, JPEG, PNG, PDF, DOCX.</div>
            </div>
            <div class="col-12">
              <label for="updateDepartment" class="form-label">Assign to Department</label>
              <select class="form-select" id="updateDepartment" name="assigned_to" <?= $isDepartmentAdmin ? 'disabled' : '' ?>>
                <option value="">Unassigned</option>
                <?php foreach (FEEDBACK_DEPARTMENTS as $department): ?>
                  <?php if ($isDepartmentAdmin && $department !== ($departmentScope ?? '')) { continue; } ?>
                  <option value="<?= e($department) ?>" <?= ($selectedLatest['assigned_to'] ?? $selectedTicket['assigned_to'] ?? '') === $department ? 'selected' : '' ?>><?= e($department) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if ($isDepartmentAdmin && $departmentScope !== null): ?>
                <input type="hidden" name="assigned_to" value="<?= e($departmentScope) ?>">
              <?php endif; ?>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">Save Update</button>
            </div>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Select a ticket to view its details and add updates.</div>
    <?php endif; ?>
  </div>
</div>

<style>
  .timeline {
    position: relative;
    padding-left: 1rem;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: rgba(253, 126, 20, 0.3);
  }
  .timeline-item::before {
    content: '';
    position: absolute;
    left: 9px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #fd7e14;
  }
</style>
<?php include __DIR__ . '/partials/footer.php';