<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_module_access('ticket_reports');
require_once __DIR__ . '/../includes/feedback.php';

$pageTitle = 'Citizen Feedback Reports';

$period = strtolower(trim((string) ($_GET['period'] ?? 'month')));
if (!in_array($period, ['week', 'month'], true)) {
    $period = 'month';
}

$exportFormat = strtolower(trim((string) ($_GET['export'] ?? '')));

$departmentScope = user_department();
$isDepartmentAdmin = (user_role() === ROLE_DEPARTMENT_ADMIN);

$baseJoin = 'FROM feedback_tickets ft
LEFT JOIN (
    SELECT fu1.ticket_id, fu1.status, fu1.assigned_to, fu1.updated_at, fu1.id
    FROM feedback_updates fu1
    INNER JOIN (
        SELECT ticket_id, MAX(updated_at) AS updated_at, MAX(id) AS id
        FROM feedback_updates
        GROUP BY ticket_id
    ) fu2 ON fu2.ticket_id = fu1.ticket_id AND fu2.updated_at = fu1.updated_at AND fu2.id = fu1.id
) latest ON latest.ticket_id = ft.id';

$conditions = [];
$params = [];
if ($isDepartmentAdmin && $departmentScope !== null) {
    $conditions[] = 'COALESCE(latest.assigned_to, ft.assigned_to) = :dept';
    $params['dept'] = $departmentScope;
}

$periodSelect = $period === 'week'
    ? 'YEARWEEK(ft.created_at, 1)'
    : "DATE_FORMAT(ft.created_at, '%Y-%m')";

$periodQuery = "SELECT {$periodSelect} AS period_key, MIN(ft.created_at) AS period_date, COUNT(*) AS total {$baseJoin}";
if ($conditions) {
    $periodQuery .= ' WHERE ' . implode(' AND ', $conditions);
}
$periodQuery .= ' GROUP BY period_key ORDER BY period_date ASC';

$periodStmt = $pdo->prepare($periodQuery);
$periodStmt->execute($params);
$periodRows = $periodStmt->fetchAll();

$departmentQuery = "SELECT COALESCE(latest.assigned_to, ft.assigned_to, 'Unassigned') AS department, COUNT(*) AS total {$baseJoin}";
if ($conditions) {
    $departmentQuery .= ' WHERE ' . implode(' AND ', $conditions);
}
$departmentQuery .= ' GROUP BY department ORDER BY total DESC';
$departmentStmt = $pdo->prepare($departmentQuery);
$departmentStmt->execute($params);
$departmentRows = $departmentStmt->fetchAll();

$resolutionQuery = "SELECT department, AVG(resolution_hours) AS average_hours FROM (
    SELECT COALESCE(latest.assigned_to, ft.assigned_to, 'Unassigned') AS department,
           TIMESTAMPDIFF(MINUTE, ft.created_at, resolved.first_resolved_at) / 60 AS resolution_hours
    {$baseJoin}
    INNER JOIN (
        SELECT ticket_id, MIN(updated_at) AS first_resolved_at
        FROM feedback_updates
        WHERE status IN ('Resolved','Closed')
        GROUP BY ticket_id
    ) resolved ON resolved.ticket_id = ft.id
";
$resolutionConditions = $conditions;
$resolutionConditions[] = 'resolved.first_resolved_at IS NOT NULL';
$resolutionQuery .= ' WHERE ' . implode(' AND ', $resolutionConditions);
$resolutionQuery .= ') AS resolution_data GROUP BY department ORDER BY average_hours ASC';
$resolutionStmt = $pdo->prepare($resolutionQuery);
$resolutionStmt->execute($params);
$resolutionRows = $resolutionStmt->fetchAll();

$outstandingQuery = "SELECT ft.ticket_number, ft.category, ft.created_at,
       COALESCE(latest.status, 'Open') AS current_status,
       COALESCE(latest.assigned_to, ft.assigned_to, 'Unassigned') AS current_assigned
    {$baseJoin}";
$outstandingConditions = $conditions;
$outstandingConditions[] = "COALESCE(latest.status, 'Open') IN ('Open','In Progress')";
$outstandingQuery .= ' WHERE ' . implode(' AND ', $outstandingConditions);
$outstandingQuery .= ' ORDER BY ft.created_at ASC LIMIT 10';
$outstandingStmt = $pdo->prepare($outstandingQuery);
$outstandingStmt->execute($params);
$outstandingRows = $outstandingStmt->fetchAll();

$periodLabels = [];
$periodData = [];
foreach ($periodRows as $row) {
    if ($period === 'week') {
        $yearWeek = (string) $row['period_key'];
        $year = substr($yearWeek, 0, 4);
        $week = substr($yearWeek, 4);
        $periodLabels[] = sprintf('Week %d %s', (int) $week, $year);
    } else {
        $date = DateTimeImmutable::createFromFormat('Y-m', (string) $row['period_key']);
        $periodLabels[] = $date ? $date->format('M Y') : (string) $row['period_key'];
    }
    $periodData[] = (int) ($row['total'] ?? 0);
}

$departmentLabels = [];
$departmentData = [];
foreach ($departmentRows as $row) {
    $dept = $row['department'] ?? 'Unassigned';
    $departmentLabels[] = $dept;
    $departmentData[] = (int) ($row['total'] ?? 0);
}

$resolutionLabels = [];
$resolutionData = [];
foreach ($resolutionRows as $row) {
    $dept = $row['department'] ?? 'Unassigned';
    $resolutionLabels[] = $dept;
    $resolutionData[] = round((float) ($row['average_hours'] ?? 0), 2);
}

if ($exportFormat === 'csv') {
    $filename = 'ticket_reports_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'wb');
    fputcsv($output, ['Tickets per ' . ucfirst($period)]);
    fputcsv($output, ['Period', 'Total']);
    foreach ($periodLabels as $index => $label) {
        fputcsv($output, [$label, $periodData[$index] ?? 0]);
    }
    fputcsv($output, []);
    fputcsv($output, ['Tickets by Department']);
    fputcsv($output, ['Department', 'Total']);
    foreach ($departmentLabels as $index => $label) {
        fputcsv($output, [$label, $departmentData[$index] ?? 0]);
    }
    fputcsv($output, []);
    fputcsv($output, ['Average Resolution Time (hours)']);
    fputcsv($output, ['Department', 'Average Hours']);
    foreach ($resolutionLabels as $index => $label) {
        fputcsv($output, [$label, $resolutionData[$index] ?? 0]);
    }
    fputcsv($output, []);
    fputcsv($output, ['Oldest Outstanding Tickets']);
    fputcsv($output, ['Ticket', 'Category', 'Status', 'Assigned To', 'Created At']);
    foreach ($outstandingRows as $row) {
        fputcsv($output, [
            $row['ticket_number'],
            $row['category'],
            $row['current_status'],
            $row['current_assigned'],
            $row['created_at'],
        ]);
    }
    fclose($output);
    exit;
}

if ($exportFormat === 'pdf') {
    $lines = [];
    $lines[] = 'Tickets per ' . ucfirst($period) . ':';
    foreach ($periodLabels as $index => $label) {
        $lines[] = sprintf('  %s - %d', $label, $periodData[$index] ?? 0);
    }
    $lines[] = '';
    $lines[] = 'Tickets by Department:';
    foreach ($departmentLabels as $index => $label) {
        $lines[] = sprintf('  %s - %d', $label, $departmentData[$index] ?? 0);
    }
    $lines[] = '';
    $lines[] = 'Average Resolution Time (hours):';
    foreach ($resolutionLabels as $index => $label) {
        $lines[] = sprintf('  %s - %.2f', $label, $resolutionData[$index] ?? 0.0);
    }
    $lines[] = '';
    $lines[] = 'Oldest Outstanding Tickets:';
    foreach ($outstandingRows as $row) {
        $lines[] = sprintf('  %s (%s) - %s - %s', $row['ticket_number'], $row['category'], $row['current_status'], date('M j, Y', strtotime((string) $row['created_at'])));
    }

    $pdfContent = build_simple_pdf('Citizen Feedback Report', $lines);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=ticket_reports_' . date('Ymd_His') . '.pdf');
    echo $pdfContent;
    exit;
}

function build_simple_pdf(string $title, array $lines): string
{
    $lineHeight = 14;
    $cursorY = 770;
    $contentLines = [];

    $escape = static function (string $text): string {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    };

    $contentLines[] = 'BT';
    $contentLines[] = '/F1 14 Tf';
    $contentLines[] = sprintf('72 %.2f Td', $cursorY / 1.0);
    $contentLines[] = '(' . $escape($title) . ') Tj';
    $contentLines[] = 'T*';
    $contentLines[] = '/F1 12 Tf';

    foreach ($lines as $line) {
        $contentLines[] = '(' . $escape($line) . ') Tj';
        $contentLines[] = 'T*';
    }

    $contentLines[] = 'ET';
    $stream = implode("\n", $contentLines);
    $length = strlen($stream);

    $objects = [];
    $objects[] = '1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj';
    $objects[] = '2 0 obj<< /Type /Pages /Count 1 /Kids [3 0 R] >>endobj';
    $objects[] = '3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj';
    $objects[] = sprintf("4 0 obj<< /Length %d >>stream\n%s\nendstreamendobj", $length, $stream);
    $objects[] = '5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj';

    $offsets = [];
    $buffer = "%PDF-1.4\n";
    foreach ($objects as $object) {
        $offsets[] = strlen($buffer);
        $buffer .= $object . "\n";
    }

    $xrefPosition = strlen($buffer);
    $buffer .= "xref\n0 " . (count($objects) + 1) . "\n";
    $buffer .= "0000000000 65535 f \n";
    foreach ($offsets as $offset) {
        $buffer .= sprintf('%010d 00000 n %s', $offset, "\n");
    }
    $buffer .= "trailer<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPosition . "\n%%EOF";

    return $buffer;
}

include __DIR__ . '/partials/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h4 fw-bold mb-0">Citizen Feedback Reports</h1>
    <div class="d-flex gap-2">
        <a href="ticket_reports.php?period=<?= e($period) ?>&export=csv" class="btn btn-outline-primary">Export CSV</a>
        <a href="ticket_reports.php?period=<?= e($period) ?>&export=pdf" class="btn btn-outline-success">Export PDF</a>
    </div>
</div>

<form method="get" class="row g-3 align-items-end mb-4">
    <div class="col-sm-6 col-md-4">
        <label for="period" class="form-label">Interval</label>
        <select class="form-select" id="period" name="period">
            <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Monthly</option>
            <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Weekly</option>
        </select>
    </div>
    <div class="col-sm-6 col-md-3">
        <button type="submit" class="btn btn-primary">Apply</button>
    </div>
</form>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 text-uppercase mb-0">Tickets per <?= e($period === 'week' ? 'Week' : 'Month') ?></h2>
            </div>
            <div class="card-body">
                <canvas id="ticketsPeriodChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 text-uppercase mb-0">Tickets by Department</h2>
            </div>
            <div class="card-body">
                <canvas id="ticketsDepartmentChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 text-uppercase mb-0">Average Resolution Time (hours)</h2>
            </div>
            <div class="card-body">
                <canvas id="resolutionDepartmentChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0">
                <h2 class="h6 text-uppercase mb-0">Oldest Outstanding Tickets</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$outstandingRows): ?>
                                <tr><td colspan="5" class="text-center py-4">No outstanding tickets 🎉</td></tr>
                            <?php else: ?>
                                <?php foreach ($outstandingRows as $row): ?>
                                    <tr>
                                        <td><?= e($row['ticket_number']) ?></td>
                                        <td><?= e($row['category']) ?></td>
                                        <td><?= e($row['current_status']) ?></td>
                                        <td><?= e($row['current_assigned']) ?></td>
                                        <td><?= e(date('M j, Y g:i A', strtotime((string) $row['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" integrity="sha384-BAHd3UTB9Z+Ke3tDx7KAGARIwBrlqvOpn/87+nF9sZD1PH6Z41FmBfDNcdOr3L+a" crossorigin="anonymous"></script>
<script>
    const periodCtx = document.getElementById('ticketsPeriodChart');
    const departmentCtx = document.getElementById('ticketsDepartmentChart');
    const resolutionCtx = document.getElementById('resolutionDepartmentChart');

    const periodData = {
        labels: <?= json_encode($periodLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        datasets: [{
            label: 'Tickets',
            data: <?= json_encode(array_map('intval', $periodData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            backgroundColor: '#0d6efd',
            borderColor: '#0d6efd',
            tension: 0.3,
            fill: false
        }]
    };

    if (periodCtx) {
        new Chart(periodCtx, {
            type: 'line',
            data: periodData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    if (departmentCtx) {
        new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($departmentLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [{
                    label: 'Tickets',
                    data: <?= json_encode(array_map('intval', $departmentData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    backgroundColor: '#fd7e14'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    if (resolutionCtx) {
        new Chart(resolutionCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($resolutionLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [{
                    label: 'Average Hours',
                    data: <?= json_encode($resolutionData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    backgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
<?php include __DIR__ . '/partials/footer.php';