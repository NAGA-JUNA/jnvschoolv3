<?php
// SMTP Mail Configuration for jnvschool.awayindia.com
define('SMTP_HOST', 'mail.awayindia.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'noreply@jnvschool.awayindia.com');
define('SMTP_PASS', 'YOUR_EMAIL_PASSWORD'); // ← CHANGE THIS to the real password from cPanel Email Accounts
define('SMTP_FROM_NAME', 'JNV School');
define('SMTP_ENCRYPTION', 'ssl');

// Include PHPMailer classes
require_once __DIR__ . '/../includes/phpmailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Send an HTML email using SMTP authentication.
 *
 * @param string $to      Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML content of the email
 * @return bool True on success, false on failure
 */
function sendMail(string $to, string $subject, string $htmlBody): bool {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;

    if ($mail->send()) {
        return true;
    } else {
        error_log('Mail send failed to ' . $to . ': ' . $mail->ErrorInfo);
        return false;
    }
}
