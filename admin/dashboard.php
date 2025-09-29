<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

require_once __DIR__ . '/../includes/feedback.php';

$pageTitle = 'Dashboard Overview';

$modules = [
    'executive' => ['label' => 'Executive Issuances', 'table' => 'executive_issuances'],
    'hearings' => ['label' => 'Public Hearings', 'table' => 'public_hearings'],
    'ordinances' => ['label' => 'Ordinances', 'table' => 'ordinances'],
    'resolutions' => ['label' => 'Resolutions', 'table' => 'resolutions'],
    'announcements' => ['label' => 'Announcements', 'table' => 'announcements'],
    'news' => ['label' => 'News', 'table' => 'news'],
];

$stats = [];
foreach ($modules as $module => $meta) {
    if (!user_can_access($module)) {
        continue;
    }

    $stats[] = [
        'label' => $meta['label'],
        'count' => get_table_count($pdo, $meta['table']),
    ];
}

$canViewFeedback = user_can_access('feedback');
$departmentScope = user_department();
$feedbackSummary = [
    'total' => 0,
    'categories' => array_fill_keys(FEEDBACK_CATEGORIES, 0),
    'statuses' => array_fill_keys(FEEDBACK_STATUSES, 0),
    'departments' => array_fill_keys(FEEDBACK_DEPARTMENTS, 0),
    'recent' => [],
    'averageHours' => null,
];
$feedbackResolutionByDepartment = [];

if ($canViewFeedback) {
    if (!isset($feedbackSummary['departments']['Unassigned'])) {
        $feedbackSummary['departments']['Unassigned'] = 0;
    }

    $analyticsSql = 'SELECT ft.id, ft.ticket_number, ft.name, ft.email, ft.category, ft.assigned_to, ft.created_at,
            latest.status AS current_status,
            COALESCE(latest.assigned_to, ft.assigned_to) AS current_assigned,
            latest.updated_at AS last_update,
            resolved.first_resolved_at
        FROM feedback_tickets ft
        LEFT JOIN (
            SELECT fu1.ticket_id, fu1.status, fu1.assigned_to, fu1.updated_at, fu1.id
            FROM feedback_updates fu1
            INNER JOIN (
                SELECT ticket_id, MAX(updated_at) AS updated_at, MAX(id) AS id
                FROM feedback_updates
                GROUP BY ticket_id
            ) fu2 ON fu2.ticket_id = fu1.ticket_id AND fu2.updated_at = fu1.updated_at AND fu2.id = fu1.id
        ) latest ON latest.ticket_id = ft.id
        LEFT JOIN (
            SELECT ticket_id, MIN(updated_at) AS first_resolved_at
            FROM feedback_updates
            WHERE status IN (\'Resolved\', \'Closed\')
            GROUP BY ticket_id
        ) resolved ON resolved.ticket_id = ft.id';

    $analyticsParams = [];
    if ($departmentScope !== null && user_role() === ROLE_DEPARTMENT_ADMIN) {
        $analyticsSql .= ' WHERE COALESCE(ft.assigned_to, latest.assigned_to) = :dept';
        $analyticsParams['dept'] = $departmentScope;
    }

    $analyticsSql .= ' ORDER BY ft.created_at DESC';
    $analyticsStmt = $pdo->prepare($analyticsSql);
    $analyticsStmt->execute($analyticsParams);
    $analyticsRows = $analyticsStmt->fetchAll();

    $feedbackSummary['total'] = count($analyticsRows);

    $resolutionDurations = [];

    foreach ($analyticsRows as $row) {
        $category = $row['category'] ?? 'Others';
        if (!isset($feedbackSummary['categories'][$category])) {
            $feedbackSummary['categories'][$category] = 0;
        }
        $feedbackSummary['categories'][$category]++;

        $status = $row['current_status'] ?? 'Open';
        if (!isset($feedbackSummary['statuses'][$status])) {
            $feedbackSummary['statuses'][$status] = 0;
        }
        $feedbackSummary['statuses'][$status]++;

        $assigned = $row['current_assigned'] ?? 'Unassigned';
        if ($assigned === null || $assigned === '') {
            $assigned = 'Unassigned';
        }
        if (!isset($feedbackSummary['departments'][$assigned])) {
            $feedbackSummary['departments'][$assigned] = 0;
        }
        $feedbackSummary['departments'][$assigned]++;

        if (!empty($row['first_resolved_at'])) {
            $createdAt = new DateTimeImmutable((string) $row['created_at']);
            $resolvedAt = new DateTimeImmutable((string) $row['first_resolved_at']);
            if ($resolvedAt > $createdAt) {
                $interval = $createdAt->diff($resolvedAt);
                $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60) + ($interval->s / 3600);
                $resolutionDurations[] = $hours;
                $deptKey = $assigned;
                $feedbackResolutionByDepartment[$deptKey][] = $hours;
            }
        }
    }

    $feedbackSummary['recent'] = array_slice($analyticsRows, 0, 5);

    if ($resolutionDurations) {
        $feedbackSummary['averageHours'] = array_sum($resolutionDurations) / count($resolutionDurations);
    }
}

if (!isset($feedbackSummary['departments']['Unassigned'])) {
    $feedbackSummary['departments']['Unassigned'] = 0;
}

$auditLogs = get_latest_audit_logs($pdo, 10);
$flashMessages = get_flash_messages();

include __DIR__ . '/partials/header.php';
?>

<?php if (!empty($flashMessages['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?php foreach ($flashMessages['success'] as $message): ?>
            <div><?= e($message) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($flashMessages['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($flashMessages['error'] as $message): ?>
            <div><?= e($message) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($stats): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($stats as $stat): ?>
            <div class="col-md-4 col-lg-2">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-2"><?= e($stat['label']) ?></h5>
                        <p class="display-6 fw-bold text-primary mb-0"><?= (int) $stat['count'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No modules are assigned to your role yet.</div>
<?php endif; ?>

<?php if ($canViewFeedback): ?>
    <?php
    $categoryChartLabels = array_keys($feedbackSummary['categories']);
    $categoryChartData = array_values($feedbackSummary['categories']);
    $departmentChartLabels = array_keys($feedbackSummary['departments']);
    $departmentChartData = array_values($feedbackSummary['departments']);
    $statusChartLabels = array_keys($feedbackSummary['statuses']);
    $statusChartData = array_values($feedbackSummary['statuses']);
    $averageResolutionDisplay = 'N/A';
    if ($feedbackSummary['averageHours'] !== null) {
        $avgHours = $feedbackSummary['averageHours'];
        $days = (int) floor($avgHours / 24);
        $hoursRemainder = $avgHours - ($days * 24);
        $hours = (int) floor($hoursRemainder);
        $minutes = (int) round(($hoursRemainder - $hours) * 60);
        if ($minutes === 60) {
            $hours++;
            $minutes = 0;
        }
        if ($hours === 24) {
            $days++;
            $hours = 0;
        }
        $parts = [];
        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($minutes > 0 && $days === 0) {
            $parts[] = $minutes . 'm';
        }
        if (!$parts) {
            $parts[] = 'Under 1 hour';
        }
        $averageResolutionDisplay = implode(' ', $parts);
    }
    ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-2">Total Feedback Tickets</h2>
                    <p class="display-6 fw-bold text-primary mb-0"><?= (int) $feedbackSummary['total'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-2">Average Resolution Time</h2>
                    <p class="display-6 fw-bold text-primary mb-0"><?= e($averageResolutionDisplay) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-2">Open or In Progress</h2>
                    <p class="display-6 fw-bold text-primary mb-0"><?= (int) (($feedbackSummary['statuses']['Open'] ?? 0) + ($feedbackSummary['statuses']['In Progress'] ?? 0)) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h3 class="h6 text-uppercase mb-0">Tickets by Category</h3>
                </div>
                <div class="card-body">
                    <canvas id="feedbackCategoryChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h3 class="h6 text-uppercase mb-0">Tickets by Department</h3>
                </div>
                <div class="card-body">
                    <canvas id="feedbackDepartmentChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h3 class="h6 text-uppercase mb-0">Tickets by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="feedbackStatusChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0">
            <h3 class="h6 text-uppercase mb-0">Most Recent Tickets</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$feedbackSummary['recent']): ?>
                            <tr><td colspan="4" class="text-center py-4">No tickets submitted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($feedbackSummary['recent'] as $recent): ?>
                                <tr>
                                    <td><?= e($recent['ticket_number']) ?></td>
                                    <td><?= e($recent['category']) ?></td>
                                    <td><?= e($recent['current_status'] ?? 'Open') ?></td>
                                    <td><?= e(date('M j, Y g:i A', strtotime((string) $recent['created_at']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h2 class="h5 mb-0">Recent Audit Trail</h2>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Timestamp</th>
                        <th scope="col">User</th>
                        <th scope="col">Event</th>
                        <th scope="col">IP</th>
                        <th scope="col">User Agent</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$auditLogs): ?>
                    <tr><td colspan="5" class="text-center py-4">No audit logs recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td><?= e($log['created_at']) ?></td>
                            <td><?= e($log['email'] ?? 'System') ?></td>
                            <td><?= e($log['event']) ?></td>
                            <td><?= e($log['ip']) ?></td>
                            <td><?= e($log['ua']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($canViewFeedback): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" integrity="sha384-BAHd3UTB9Z+Ke3tDx7KAGARIwBrlqvOpn/87+nF9sZD1PH6Z41FmBfDNcdOr3L+a" crossorigin="anonymous"></script>
    <script>
        const categoryCtx = document.getElementById('feedbackCategoryChart');
        const departmentCtx = document.getElementById('feedbackDepartmentChart');
        const statusCtx = document.getElementById('feedbackStatusChart');
        const chartOptions = { responsive: true, plugins: { legend: { position: 'bottom' } } };

        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($categoryChartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map('intval', $categoryChartData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                        backgroundColor: ['#fd7e14', '#ffb347', '#6c757d', '#198754', '#0d6efd']
                    }]
                },
                options: chartOptions
            });
        }

        if (departmentCtx) {
            new Chart(departmentCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($departmentChartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    datasets: [{
                        label: 'Tickets',
                        data: <?= json_encode(array_map('intval', $departmentChartData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
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

        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: <?= json_encode($statusChartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map('intval', $statusChartData), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                        backgroundColor: ['#198754', '#0d6efd', '#fd7e14', '#6c757d']
                    }]
                },
                options: chartOptions
            });
        }
    </script>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>