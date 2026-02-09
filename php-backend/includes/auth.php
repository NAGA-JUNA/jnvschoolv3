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
        echo '<h1>403 — Access Denied</h1>';
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

// Audit log helper
function auditLog(string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([currentUserId(), $action, $entityType, $entityId, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
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
