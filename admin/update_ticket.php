
<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';
require_module_access('feedback');
require_once __DIR__ . '/../includes/feedback.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('feedback_tickets.php');
}

require_valid_csrf();

$ticketId = (int) ($_POST['ticket_id'] ?? 0);
$status = trim((string) ($_POST['status'] ?? ''));
$adminResponse = trim((string) ($_POST['admin_response'] ?? ''));
$assignedToInput = trim((string) ($_POST['assigned_to'] ?? ''));

if ($ticketId <= 0) {
    add_flash('error', 'Invalid ticket selected.');
    redirect('feedback_tickets.php');
}

$ticket = feedback_get_ticket($pdo, $ticketId);
if (!$ticket) {
    add_flash('error', 'The selected ticket could not be found.');
    redirect('feedback_tickets.php');
}

$role = user_role();
$departmentScope = user_department();
$isDepartmentAdmin = ($role === ROLE_DEPARTMENT_ADMIN);

if ($isDepartmentAdmin) {
    if ($departmentScope === null || $ticket['assigned_to'] !== $departmentScope) {
        add_flash('error', 'You are not authorized to update this ticket.');
        redirect('feedback_tickets.php');
    }
    $assignedTo = $departmentScope;
} else {
    if ($assignedToInput === '') {
        $assignedTo = null;
    } elseif (in_array($assignedToInput, FEEDBACK_DEPARTMENTS, true)) {
        $assignedTo = $assignedToInput;
    } else {
        add_flash('error', 'Please choose a valid department assignment.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }
}

if (!feedback_validate_status($status)) {
    add_flash('error', 'Please select a valid status.');
    redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
}

$attachmentPath = null;
$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
$maxFileSize = 5 * 1024 * 1024;

if (!empty($_FILES['attachment']['name'])) {
    $errorCode = (int) ($_FILES['attachment']['error'] ?? UPLOAD_ERR_OK);
    if ($errorCode !== UPLOAD_ERR_OK) {
        add_flash('error', 'Unable to upload the attachment.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }

    $tmpName = (string) ($_FILES['attachment']['tmp_name'] ?? '');
    $originalName = (string) ($_FILES['attachment']['name'] ?? '');
    $size = (int) ($_FILES['attachment']['size'] ?? 0);

    if ($size > $maxFileSize) {
        add_flash('error', 'Attachments must not exceed 5MB.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }

    if (!is_uploaded_file($tmpName)) {
        add_flash('error', 'Invalid attachment upload.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }

    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        add_flash('error', 'Unsupported attachment type.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }

    $safeName = feedback_sanitise_filename($originalName);
    if ($safeName === '') {
        $safeName = 'attachment.' . $extension;
    }

    $uploadDirectory = __DIR__ . '/../uploads/feedback/admin/';
    $uniqueName = sprintf('%s_%d_%s', $ticket['ticket_number'], time(), $safeName);
    $destination = $uploadDirectory . $uniqueName;

    if (!move_uploaded_file($tmpName, $destination)) {
        add_flash('error', 'Failed to save the attachment.');
        redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
    }

    $attachmentPath = 'uploads/feedback/admin/' . $uniqueName;
}

try {
    $pdo->beginTransaction();

    feedback_insert_update($pdo, [
        'ticket_id' => $ticketId,
        'status' => $status,
        'admin_response' => $adminResponse !== '' ? $adminResponse : null,
        'attachment_path' => $attachmentPath,
        'assigned_to' => $assignedTo,
        'updated_by' => current_user()['email'] ?? 'Administrator',
    ]);

    feedback_update_ticket_assignment($pdo, $ticketId, $assignedTo);

    $pdo->commit();
} catch (Throwable $exception) {
    $pdo->rollBack();

    if ($attachmentPath) {
        $absolute = __DIR__ . '/../' . ltrim($attachmentPath, '/');
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    add_flash('error', 'An unexpected error occurred while saving the update.');
    redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));
}

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$trackingLink = 'https://' . $host . '/track_feedback.php?ticket=' . rawurlencode((string) $ticket['ticket_number']);

$emailSubject = sprintf('Update on your ticket %s', $ticket['ticket_number']);
$emailBodyLines = [
    sprintf('Dear %s,', $ticket['name']),
    '',
    sprintf('Your feedback ticket regarding %s has been updated.', $ticket['category']),
    sprintf('Status: %s', $status),
];

if ($adminResponse !== '') {
    $emailBodyLines[] = '';
    $emailBodyLines[] = 'Response:';
    $emailBodyLines[] = $adminResponse;
}

$emailBodyLines[] = '';
$emailBodyLines[] = 'Assigned to: ' . ($assignedTo ?? 'Unassigned');

if ($attachmentPath) {
    $emailBodyLines[] = '';
    $emailBodyLines[] = 'An attachment has been added to your ticket. You can download it from the tracking page.';
}

$emailBodyLines[] = '';
$emailBodyLines[] = 'Track your ticket here:';
$emailBodyLines[] = $trackingLink;
$emailBodyLines[] = '';
$emailBodyLines[] = 'Thank you,';
$emailBodyLines[] = 'Municipal Government of Daet';

$emailHeaders = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: no-reply@' . $host,
];

if (filter_var($ticket['email'], FILTER_VALIDATE_EMAIL)) {
    @mail($ticket['email'], $emailSubject, implode("\n", $emailBodyLines), implode("\r\n", $emailHeaders));
}

add_flash('success', 'Ticket updated successfully.');
redirect('feedback_tickets.php?ticket=' . urlencode((string) $ticket['ticket_number']));