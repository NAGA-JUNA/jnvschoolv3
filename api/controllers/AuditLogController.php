<?php
// ============================================
// AuditLogController — List Audit Logs with Filters
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class AuditLogController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/audit-logs
    public function index(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        [$page, $perPage, $offset] = getPagination();

        $where = ["1=1"];
        $params = [];

        if (!empty($_GET['action'])) {
            $where[] = "al.action = :action";
            $params[':action'] = $_GET['action'];
        }
        if (!empty($_GET['entity_type'])) {
            $where[] = "al.entity_type = :etype";
            $params[':etype'] = $_GET['entity_type'];
        }
        if (!empty($_GET['user_id'])) {
            $where[] = "al.user_id = :uid";
            $params[':uid'] = (int)$_GET['user_id'];
        }
        if (!empty($_GET['from'])) {
            $where[] = "al.created_at >= :from";
            $params[':from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $where[] = "al.created_at <= :to";
            $params[':to'] = $_GET['to'] . ' 23:59:59';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM audit_logs al WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT al.*, u.name as user_name, u.role as user_role
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE $whereSql ORDER BY al.created_at DESC LIMIT :l OFFSET :o"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }
}
