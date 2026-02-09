<?php
// ============================================
// EmailController — List/Create Official Email Accounts
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';

class EmailController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/emails
    public function index(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT oe.id, oe.email_address, oe.display_name, oe.webmail_url, oe.status, oe.created_at,
                    u.name as user_name
             FROM official_emails oe
             LEFT JOIN users u ON oe.user_id = u.id
             ORDER BY oe.created_at DESC"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }

    // POST /admin/emails/create
    public function create(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('email_address', 'Email Address')->email('email_address')
          ->required('display_name', 'Display Name')
          ->required('password', 'Password')->minLength('password', 8, 'Password')
          ->unique('email_address', 'official_emails', 'email_address', null, 'Email');
        $v->validate();

        // Hash password — we store hash for reference, actual email creation is manual via cPanel
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            "INSERT INTO official_emails (user_id, email_address, display_name, password_hash, webmail_url, status)
             VALUES (:uid, :email, :name, :pass, :webmail, 'active')"
        );
        $stmt->execute([
            ':uid'     => $data['user_id'] ?? null,
            ':email'   => trim($data['email_address']),
            ':name'    => trim($data['display_name']),
            ':pass'    => $passwordHash,
            ':webmail' => $data['webmail_url'] ?? null,
        ]);

        auditLog('create', 'official_emails', (int)$this->db->lastInsertId());

        // Return the generated credentials (only on creation, password is NOT stored/retrievable later)
        jsonSuccess([
            'id'            => $this->db->lastInsertId(),
            'email_address' => $data['email_address'],
            'display_name'  => $data['display_name'],
            'password'      => $data['password'], // Show once, then discard
            'note'          => 'Save these credentials! The password cannot be retrieved later.',
        ], 'Email account created', 201);
    }
}
