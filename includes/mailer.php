<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/config.php';

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
} else {
    require_once __DIR__ . '/phpmailer/PHPMailer.php';
}

/**
 * Sends an email using the configured SMTP settings.
 */
function sendMail(string $to, string $subject, string $body): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $cleanSubject = trim($subject);
    $cleanBody = normalize_email_text($body);

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host = SMTP_HOST;
        $mailer->Port = SMTP_PORT;
        $mailer->SMTPAuth = SMTP_USERNAME !== '' && SMTP_PASSWORD !== '';
        $mailer->SMTPSecure = SMTP_ENCRYPTION !== '' ? SMTP_ENCRYPTION : null;
        $mailer->Username = SMTP_USERNAME;
        $mailer->Password = SMTP_PASSWORD;
        $mailer->CharSet = 'UTF-8';
        $mailer->isHTML(false);

        $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mailer->addAddress($to);
        $mailer->Subject = $cleanSubject;
        $mailer->Body = $cleanBody;
        $mailer->AltBody = $cleanBody;

        $mailer->send();
        return true;
    } catch (Exception $exception) {
        $errorMessage = $mailer->ErrorInfo ?: $exception->getMessage();
        error_log('[mailer] Email failed: ' . $errorMessage);
        return false;
    }
}

/**
 * Normalizes potentially unsafe text for email bodies.
 */
function normalize_email_text(string $value): string
{
    $normalized = preg_replace("/\r\n|\r/", "\n", $value) ?? '';
    $normalized = strip_tags($normalized);
    $normalized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $normalized) ?? '';
    return trim($normalized);
}