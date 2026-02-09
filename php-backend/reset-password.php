<?php
require_once __DIR__ . '/includes/auth.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) { header('Location: /login.php'); exit; }

$db = getDB();
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() AND is_active = 1");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $error = 'Invalid or expired reset link.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    if (!verifyCsrf()) { $error = 'Invalid request.'; }
    else {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (strlen($pass) < 6) { $error = 'Password must be at least 6 characters.'; }
        elseif ($pass !== $confirm) { $error = 'Passwords do not match.'; }
        else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")->execute([$hash, $user['id']]);
            auditLog('password_reset', 'user', $user['id']);
            $success = 'Password reset successfully. You can now login.';
            $user = null; // hide form
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow" style="width:400px">
  <div class="card-body p-4">
    <h4 class="text-center mb-4">Reset Password</h4>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="/login.php">Login</a></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <?php if ($user): ?>
    <form method="POST">
      <?= csrfField() ?>
      <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
      <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
      <button class="btn btn-primary w-100">Reset Password</button>
    </form>
    <?php endif; ?>
  </div>
</div>
</body></html>
