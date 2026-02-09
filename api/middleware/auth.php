<?php
// ============================================
// JSchoolAdmin — Authentication Middleware
// ============================================

require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../helpers/response.php';

// Global variable to hold current user data
$GLOBALS['__current_user'] = null;

/**
 * Extract and verify JWT from Authorization header.
 * Returns user payload or null.
 */
function extractUser(): ?array {
    $header = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        return null;
    }

    $token = substr($header, 7);
    $payload = jwtDecode($token);

    if (!$payload || empty($payload['user_id'])) {
        return null;
    }

    return $payload;
}

/**
 * Require authenticated user. Exits with 401 if not valid.
 */
function requireAuth(): array {
    $user = extractUser();
    if (!$user) {
        jsonError('Unauthorized. Please login.', 401);
    }
    $GLOBALS['__current_user'] = $user;
    return $user;
}

/**
 * Require specific role(s). Exits with 403 if wrong role.
 */
function requireRole(array $roles): array {
    $user = requireAuth();
    if (!in_array($user['role'], $roles)) {
        jsonError('Forbidden. Insufficient permissions.', 403);
    }
    return $user;
}

/**
 * Get current authenticated user's ID
 */
function currentUserId(): ?int {
    $user = $GLOBALS['__current_user'] ?? extractUser();
    return $user ? (int) $user['user_id'] : null;
}

/**
 * Get current user's role
 */
function currentUserRole(): ?string {
    $user = $GLOBALS['__current_user'] ?? extractUser();
    return $user['role'] ?? null;
}

/**
 * Log an action to the audit_logs table
 */
function auditLog(string $action, string $entityType, ?int $entityId = null, ?array $details = null): void {
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details, ip_address)
             VALUES (:uid, :action, :type, :eid, :details, :ip)"
        );
        $stmt->execute([
            ':uid'     => currentUserId(),
            ':action'  => $action,
            ':type'    => $entityType,
            ':eid'     => $entityId,
            ':details' => $details ? json_encode($details) : null,
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (PDOException $e) {
        // Silently fail — audit should never break the main request
    }
}
