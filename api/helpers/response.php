<?php
// ============================================
// JSchoolAdmin — JSON Response Helpers
// ============================================

/**
 * Send a success JSON response
 */
function jsonSuccess($data = null, string $message = 'Success', int $code = 200): void {
    http_response_code($code);
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send a paginated JSON response
 */
function jsonPaginated(array $data, int $total, int $page, int $perPage, string $message = 'Success'): void {
    http_response_code(200);
    echo json_encode([
        'success'    => true,
        'message'    => $message,
        'data'       => $data,
        'pagination' => [
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'total_pages'  => (int) ceil($total / $perPage),
        ],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send an error JSON response
 */
function jsonError(string $error, int $code = 400, $details = null): void {
    http_response_code($code);
    $response = ['success' => false, 'error' => $error];
    if ($details !== null) {
        $response['details'] = $details;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get pagination parameters from query string
 */
function getPagination(): array {
    $page    = max(1, (int) ($_GET['page'] ?? DEFAULT_PAGE));
    $perPage = min(MAX_PER_PAGE, max(1, (int) ($_GET['per_page'] ?? DEFAULT_PER_PAGE)));
    $offset  = ($page - 1) * $perPage;
    return [$page, $perPage, $offset];
}

/**
 * Get sort parameters from query string
 */
function getSortParams(array $allowedFields, string $defaultField = 'created_at', string $defaultOrder = 'DESC'): array {
    $sort  = $_GET['sort'] ?? $defaultField;
    $order = strtoupper($_GET['order'] ?? $defaultOrder);
    
    if (!in_array($sort, $allowedFields)) {
        $sort = $defaultField;
    }
    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = $defaultOrder;
    }
    return [$sort, $order];
}
