<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_login();

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

$auditLogs = get_latest_audit_logs($pdo, 10);
$flashMessages = get_flash_messages();

include __DIR__ . '/partials/header.php';
?>
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
<?php include __DIR__ . '/partials/footer.php'; ?>