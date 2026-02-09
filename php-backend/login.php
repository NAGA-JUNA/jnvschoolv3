<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) { $error = 'Invalid request.'; }
    else {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (!$email || !$pass) {
            $error = 'Email and password are required.';
        } else {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($pass, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ];
                $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                auditLog('login', 'user', $user['id']);

                header('Location: ' . (in_array($user['role'], ['super_admin','admin','office']) ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — School Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow" style="width:400px">
  <div class="card-body p-4">
    <h4 class="text-center mb-4">School Admin Login</h4>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="POST">
      <?= csrfField() ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Login</button>
      <div class="text-center mt-3"><a href="/forgot-password.php">Forgot Password?</a></div>
    </form>
  </div>
</div>
</body></html>
