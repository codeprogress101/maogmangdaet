<?php
declare(strict_types=1);

const FEEDBACK_CATEGORIES = ['Health', 'Permit', 'Social Services', 'Others'];
const FEEDBACK_STATUSES = ['Open', 'In Progress', 'Resolved', 'Closed'];
const FEEDBACK_DEPARTMENTS = [
    'Health Services Office',
    'Business Permits and Licensing Office',
    'Municipal Social Welfare & Development',
    'Public Information Office',
    'Municipal Engineering Office',
    'Environmental Management Services',
    "Mayor's Office",
    'Unassigned',
];

function feedback_generate_ticket_number(PDO $pdo): string
{
    $year = date('Y');

    do {
        $random = random_int(0, 9999);
        $ticketNumber = sprintf('FB-%s-%04d', $year, $random);

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM feedback_tickets WHERE ticket_number = :ticket_number');
        $stmt->execute(['ticket_number' => $ticketNumber]);
    } while ((int) $stmt->fetchColumn() > 0);

    return $ticketNumber;
}

function feedback_insert_ticket(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO feedback_tickets (ticket_number, name, email, category, message, assigned_to, created_at)
         VALUES (:ticket_number, :name, :email, :category, :message, :assigned_to, NOW())'
    );

    $stmt->execute([
        'ticket_number' => $data['ticket_number'],
        'name' => $data['name'],
        'email' => $data['email'],
        'category' => $data['category'],
        'message' => $data['message'],
        'assigned_to' => $data['assigned_to'] ?? null,
    ]);

    return (int) $pdo->lastInsertId();
}

function feedback_insert_update(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO feedback_updates (ticket_id, status, admin_response, attachment_path, assigned_to, updated_at, updated_by)
         VALUES (:ticket_id, :status, :admin_response, :attachment_path, :assigned_to, NOW(), :updated_by)'
    );

    $stmt->execute([
        'ticket_id' => $data['ticket_id'],
        'status' => $data['status'],
        'admin_response' => $data['admin_response'],
        'attachment_path' => $data['attachment_path'],
        'assigned_to' => $data['assigned_to'],
        'updated_by' => $data['updated_by'],
    ]);

    return (int) $pdo->lastInsertId();
}

function feedback_store_attachment(PDO $pdo, int $ticketId, string $path): void
{
    $stmt = $pdo->prepare('INSERT INTO feedback_attachments (ticket_id, file_path, uploaded_at) VALUES (:ticket_id, :file_path, NOW())');
    $stmt->execute([
        'ticket_id' => $ticketId,
        'file_path' => $path,
    ]);
}

function feedback_get_ticket_by_number(PDO $pdo, string $ticketNumber): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM feedback_tickets WHERE ticket_number = :ticket_number LIMIT 1');
    $stmt->execute(['ticket_number' => $ticketNumber]);
    $ticket = $stmt->fetch();

    return $ticket ?: null;
}

function feedback_get_ticket(PDO $pdo, int $ticketId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM feedback_tickets WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $ticketId]);
    $ticket = $stmt->fetch();

    return $ticket ?: null;
}

function feedback_get_ticket_updates(PDO $pdo, int $ticketId): array
{
    $stmt = $pdo->prepare('SELECT * FROM feedback_updates WHERE ticket_id = :ticket_id ORDER BY updated_at ASC, id ASC');
    $stmt->execute(['ticket_id' => $ticketId]);

    return $stmt->fetchAll();
}

function feedback_get_ticket_attachments(PDO $pdo, int $ticketId): array
{
    $stmt = $pdo->prepare('SELECT id, file_path, uploaded_at FROM feedback_attachments WHERE ticket_id = :ticket_id ORDER BY uploaded_at ASC, id ASC');
    $stmt->execute(['ticket_id' => $ticketId]);

    return $stmt->fetchAll();
}

function feedback_latest_status(PDO $pdo, int $ticketId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT status, updated_at, assigned_to FROM feedback_updates WHERE ticket_id = :ticket_id ORDER BY updated_at DESC, id DESC LIMIT 1'
    );
    $stmt->execute(['ticket_id' => $ticketId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function feedback_update_ticket_assignment(PDO $pdo, int $ticketId, ?string $assignedTo): void
{
    $stmt = $pdo->prepare('UPDATE feedback_tickets SET assigned_to = :assigned_to WHERE id = :id');
    $stmt->execute([
        'assigned_to' => $assignedTo,
        'id' => $ticketId,
    ]);
}

function feedback_validate_category(string $category): bool
{
    return in_array($category, FEEDBACK_CATEGORIES, true);
}

function feedback_validate_status(string $status): bool
{
    return in_array($status, FEEDBACK_STATUSES, true);
}

function feedback_sanitise_filename(string $filename): string
{
    $filename = preg_replace('/[^A-Za-z0-9.\- _]/', '', $filename) ?? '';
    $filename = str_replace('..', '', $filename);

    return trim($filename, " \t\n\r\0\x0B");
}