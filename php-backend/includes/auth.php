<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// CSRF token generation
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): bool {
    return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

// Auth helpers
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function currentRole(): ?string {
    return $_SESSION['user']['role'] ?? null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array(currentRole(), $roles)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>Access Denied</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="d-flex align-items-center justify-content-center vh-100 bg-light"><div class="text-center"><h1 class="display-1 text-danger">403</h1><p class="lead">Access Denied — You don\'t have permission to view this page.</p><a href="/" class="btn btn-primary">Go Home</a></div></body></html>';
        exit;
    }
}

function requireAdmin(): void {
    requireRole(['super_admin', 'admin', 'office']);
}

function requireTeacher(): void {
    requireRole(['super_admin', 'admin', 'office', 'teacher']);
}

function isAdmin(): bool {
    return in_array(currentRole(), ['super_admin', 'admin', 'office']);
}

function isSuperAdmin(): bool {
    return currentRole() === 'super_admin';
}

// Settings helper
function getSetting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $settings = [];
        }
    }
    return $settings[$key] ?? $default;
}

// Audit log helper
function auditLog(string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([currentUserId(), $action, $entityType, $entityId, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception $e) {
        // Silently fail — don't break the app if audit logging fails
    }
}

// Flash messages
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Sanitize
function e(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

// Pagination helper
function paginate(int $total, int $perPage = 25, int $currentPage = 1): array {
    $totalPages = max(1, ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
    ];
}

// Render pagination HTML
function paginationHtml(array $p, string $baseUrl): string {
    if ($p['total_pages'] <= 1) return '';
    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    $html = '<nav><ul class="pagination justify-content-center">';
    // Prev
    $html .= '<li class="page-item ' . ($p['current_page'] <= 1 ? 'disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . ($p['current_page'] - 1) . '">&laquo;</a></li>';
    // Pages
    for ($i = max(1, $p['current_page'] - 2); $i <= min($p['total_pages'], $p['current_page'] + 2); $i++) {
        $html .= '<li class="page-item ' . ($i === $p['current_page'] ? 'active' : '') . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . $i . '">' . $i . '</a></li>';
    }
    // Next
    $html .= '<li class="page-item ' . ($p['current_page'] >= $p['total_pages'] ? 'disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . $sep . 'page=' . ($p['current_page'] + 1) . '">&raquo;</a></li>';
    $html .= '</ul></nav>';
    return $html;
}
