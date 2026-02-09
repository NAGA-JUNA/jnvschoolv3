<?php
// SMTP Mail Configuration for cPanel
// Update these with your cPanel email settings
define('SMTP_HOST', 'mail.yourdomain.com');  // Usually mail.yourdomain.com
define('SMTP_PORT', 465);                     // 465 for SSL, 587 for TLS
define('SMTP_USER', 'noreply@yourdomain.com');
define('SMTP_PASS', 'YOUR_EMAIL_PASSWORD');
define('SMTP_FROM_NAME', 'School Admin');
define('SMTP_ENCRYPTION', 'ssl');             // 'ssl' or 'tls'

/**
 * Send email using PHP mail() — works on most cPanel hosts.
 * For PHPMailer, download it into /vendor and use that instead.
 */
function sendMail(string $to, string $subject, string $htmlBody): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_USER . ">\r\n";
    return mail($to, $subject, $htmlBody, $headers);
}
