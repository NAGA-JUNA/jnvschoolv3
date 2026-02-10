<?php
// SMTP Mail Configuration for jnvschool.awayindia.com
define('SMTP_HOST', 'mail.awayindia.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'noreply@jnvschool.awayindia.com');
define('SMTP_PASS', 'YOUR_EMAIL_PASSWORD');
define('SMTP_FROM_NAME', 'JNV School');
define('SMTP_ENCRYPTION', 'ssl');

function sendMail(string $to, string $subject, string $htmlBody): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_USER . ">\r\n";
    return mail($to, $subject, $htmlBody, $headers);
}
