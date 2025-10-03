<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/feedback.php';
require_once __DIR__ . '/includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$pdo = getDatabaseConnection();

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$category = trim((string) ($_POST['category'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$errors = [];
$maxMessageLength = 5000;
$allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
$maxFileSize = 5 * 1024 * 1024; // 5 MB per file.
$uploadedFiles = [];
$savedFiles = [];

if ($name === '') {
    $errors[] = 'Please provide your name.';
} elseif (mb_strlen($name, 'UTF-8') > 150) {
    $errors[] = 'Names must be 150 characters or fewer.';
}

if ($email === '') {
    $errors[] = 'Please provide your email address.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
} elseif (mb_strlen($email, 'UTF-8') > 150) {
    $errors[] = 'Email addresses must be 150 characters or fewer.';
}

if (!feedback_validate_category($category)) {
    $errors[] = 'Please choose a valid category.';
}

if ($message === '') {
    $errors[] = 'Please enter your message.';
} elseif (mb_strlen($message, 'UTF-8') > $maxMessageLength) {
    $errors[] = sprintf('Messages must be %d characters or fewer.', $maxMessageLength);
}

if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
    $fileCount = count($_FILES['attachments']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        $errorCode = (int) ($_FILES['attachments']['error'][$i] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $originalName = (string) ($_FILES['attachments']['name'][$i] ?? '');
        $tmpName = (string) ($_FILES['attachments']['tmp_name'][$i] ?? '');
        $size = (int) ($_FILES['attachments']['size'][$i] ?? 0);

        if ($errorCode !== UPLOAD_ERR_OK) {
            $errors[] = sprintf('File "%s" could not be uploaded (error code %d).', htmlspecialchars($originalName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $errorCode);
            continue;
        }

        if ($size > $maxFileSize) {
            $errors[] = sprintf('File "%s" exceeds the 5MB limit.', htmlspecialchars($originalName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            continue;
        }

        if (!is_uploaded_file($tmpName)) {
            $errors[] = sprintf('File "%s" is not a valid upload.', htmlspecialchars($originalName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            continue;
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            $errors[] = sprintf('File "%s" has an unsupported file type.', htmlspecialchars($originalName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            continue;
        }

        $uploadedFiles[] = [
            'original_name' => $originalName,
            'tmp_name' => $tmpName,
            'extension' => $extension,
        ];
    }
}

if ($errors) {
    http_response_code(400);
    include __DIR__ . '/header.php';
    ?>
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="alert alert-danger" role="alert">
                <h2 class="h5 fw-semibold">We could not submit your feedback</h2>
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="services.php#citizen-feedback" class="btn btn-outline-primary">Go back to the feedback form</a>
        </div>
    </section>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

try {
    $pdo->beginTransaction();

    $ticketNumber = feedback_generate_ticket_number($pdo);
    $ticketId = feedback_insert_ticket($pdo, [
        'ticket_number' => $ticketNumber,
        'name' => $name,
        'email' => $email,
        'category' => $category,
        'message' => $message,
    ]);

    feedback_insert_update($pdo, [
        'ticket_id' => $ticketId,
        'status' => 'Open',
        'admin_response' => null,
        'attachment_path' => null,
        'assigned_to' => null,
        'updated_by' => 'Citizen Submission',
    ]);

    $uploadDirectory = __DIR__ . '/uploads/feedback/citizen/';
    $publicPrefix = 'uploads/feedback/citizen/';
    $timestamp = time();

    foreach ($uploadedFiles as $index => $file) {
        $safeOriginal = feedback_sanitise_filename($file['original_name']);
        $safeOriginal = $safeOriginal !== '' ? $safeOriginal : ('attachment.' . $file['extension']);
        $uniqueName = sprintf('%s_%d_%d.%s', $ticketNumber, $timestamp, $index + 1, $file['extension']);
        $destination = $uploadDirectory . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        $relativePath = $publicPrefix . $uniqueName;
        $savedFiles[] = $relativePath;
        feedback_store_attachment($pdo, $ticketId, $relativePath);
    }

    $pdo->commit();
} catch (Throwable $exception) {
    $pdo->rollBack();

    foreach ($savedFiles as $path) {
        $absolute = __DIR__ . '/' . ltrim($path, '/');
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    http_response_code(500);
    include __DIR__ . '/header.php';
    ?>
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="alert alert-danger" role="alert">
                <h2 class="h5 fw-semibold">We encountered a problem</h2>
                <p class="mb-0">Your feedback could not be saved at this time. Please try again later.</p>
            </div>
            <a href="services.php#citizen-feedback" class="btn btn-outline-primary">Go back to the feedback form</a>
        </div>
    </section>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

$savedFileNames = array_map(static function (string $path): string {
    return basename($path);
}, $savedFiles);

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$trackingLink = 'https://' . $host . '/track_feedback.php?ticket=' . rawurlencode($ticketNumber);

$safeNameForEmail = normalize_email_text($name);
$safeCategory = normalize_email_text($category);
$safeMessage = normalize_email_text($message);
$safeTrackingLink = normalize_email_text($trackingLink);

$emailSubject = sprintf('Your feedback has been submitted (Ticket %s)', $ticketNumber);
$emailBodyLines = [
    sprintf('Dear %s,', $safeNameForEmail !== '' ? $safeNameForEmail : 'Citizen'),
    '',
    'Thank you for reaching out to the Municipal Government of Daet. Your feedback has been received with the following details:',
    sprintf('Ticket Number: %s', normalize_email_text((string) $ticketNumber)),
    sprintf('Category: %s', $safeCategory),
    '',
    'Message:',
    $safeMessage !== '' ? $safeMessage : '[No message provided]',
];

if ($savedFileNames) {
    $emailBodyLines[] = '';
    $emailBodyLines[] = 'Attachments:';
    foreach ($savedFileNames as $fileName) {
        $emailBodyLines[] = ' - ' . normalize_email_text($fileName);
    }
}

$emailBodyLines[] = '';
$emailBodyLines[] = 'You can track the status of your ticket at any time using the link below:';
$emailBodyLines[] = $safeTrackingLink;
$emailBodyLines[] = '';
$emailBodyLines[] = 'This is an automated confirmation email. Our team will review your submission and keep you updated.';
$emailBodyLines[] = '';
$emailBodyLines[] = 'Sincerely,';
$emailBodyLines[] = 'Municipal Government of Daet';

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailBody = implode(PHP_EOL, $emailBodyLines);
    sendMail($email, $emailSubject, $emailBody);
}

$page_title = 'Feedback Submitted';
include __DIR__ . '/header.php';
?>
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h1 class="h3 fw-bold mb-3">Thank you for your feedback!</h1>
                        <p class="text-muted mb-4">Your ticket has been created successfully. Please keep the reference number below for your records.</p>
                        <div class="bg-light rounded-4 py-3 px-4 mb-4">
                            <div class="text-uppercase text-muted small">Ticket Number</div>
                            <div class="display-6 fw-bold text-primary mb-0"><?= htmlspecialchars($ticketNumber, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                        </div>
                        <p class="mb-1">Track your submission here:</p>
                        <p class="mb-4"><a href="<?= htmlspecialchars($trackingLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="fw-semibold"><?= htmlspecialchars($trackingLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a></p>
                        <?php if ($savedFileNames): ?>
                            <div class="mb-4">
                                <h2 class="h5 fw-semibold">Attachments Submitted</h2>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($savedFileNames as $fileName): ?>
                                        <li><?= htmlspecialchars($fileName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <a href="services.php" class="btn btn-primary">Return to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/footer.php';