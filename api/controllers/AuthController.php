<?php
// ============================================
// AuthController — Login, Logout, Me
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/rate-limit.php';

class AuthController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // POST /auth/login
    public function login(): void {
        rateLimit('login', 5, 300);

        $data = getJsonInput();
        $v = new Validator($data);
        $v->required('email', 'Email')->email('email')
          ->required('password', 'Password');
        $v->validate();

        $stmt = $this->db->prepare(
            "SELECT id, name, email, password, role, phone, whatsapp, avatar, is_active
             FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => trim($data['email'])]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            jsonError('Invalid email or password', 401);
        }

        if (!$user['is_active']) {
            jsonError('Account is deactivated. Contact administrator.', 403);
        }

        // Update last login
        $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")
            ->execute([':id' => $user['id']]);

        clearRateLimit('login');

        // Generate JWT
        $token = jwtEncode([
            'user_id' => $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ]);

        unset($user['password']);

        auditLog('login', 'users', $user['id']);

        jsonSuccess([
            'token' => $token,
            'user'  => $user,
        ], 'Login successful');
    }

    // POST /auth/logout
    public function logout(): void {
        $user = requireAuth();
        auditLog('logout', 'users', $user['user_id']);
        jsonSuccess(null, 'Logged out');
    }

    // GET /auth/me
    public function me(): void {
        $user = requireAuth();

        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, phone, whatsapp, avatar, is_active, last_login, created_at
             FROM users WHERE id = :id"
        );
        $stmt->execute([':id' => $user['user_id']]);
        $profile = $stmt->fetch();

        if (!$profile) {
            jsonError('User not found', 404);
        }

        jsonSuccess($profile);
    }
}
