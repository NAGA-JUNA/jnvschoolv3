<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/mail.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) { $error = 'Invalid request.'; }
    else {
        $email = trim($_POST['email'] ?? '');
        if (!$email) { $error = 'Email is required.'; }
        else {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Always show success to prevent email enumeration
            $success = 'If an account exists with that email, a reset link has been sent.';

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")->execute([$token, $expires, $user['id']]);

                $resetUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/reset-password.php?token=' . $token;
                $body = "<h2>Password Reset</h2><p>Click below to reset your password:</p><p><a href='{$resetUrl}'>Reset Password</a></p><p>This link expires in 1 hour.</p>";
                sendMail($email, 'Password Reset', $body);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow" style="width:400px">
  <div class="card-body p-4">
    <h4 class="text-center mb-4">Forgot Password</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <?= csrfField() ?>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Send Reset Link</button>
      <div class="text-center mt-3"><a href="/login.php">Back to Login</a></div>
    </form>
  </div>
</div>
</body></html>
