<?php
// ============================================
// WhatsAppController — Log Shares, View History
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class WhatsAppController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // POST /admin/whatsapp/log
    public function logShare(): void {
        $user = requireRole(ALL_STAFF_ROLES);
        $data = getJsonInput();

        if (empty($data['item_type']) || empty($data['item_id'])) {
            jsonError('item_type and item_id are required', 422);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO whatsapp_shares (item_type, item_id, shared_by)
             VALUES (:type, :iid, :uid)"
        );
        $stmt->execute([
            ':type' => $data['item_type'],
            ':iid'  => (int) $data['item_id'],
            ':uid'  => $user['user_id'],
        ]);

        // Also log to student/teacher messages if applicable
        if ($data['item_type'] === 'student' && !empty($data['message'])) {
            $this->db->prepare(
                "INSERT INTO student_messages (student_id, template, message, sent_by) VALUES (:sid, :tpl, :msg, :uid)"
            )->execute([
                ':sid' => (int) $data['item_id'],
                ':tpl' => $data['template'] ?? 'custom',
                ':msg' => $data['message'],
                ':uid' => $user['user_id'],
            ]);
        } elseif ($data['item_type'] === 'teacher' && !empty($data['message'])) {
            $this->db->prepare(
                "INSERT INTO teacher_messages (teacher_id, template, message, sent_by) VALUES (:tid, :tpl, :msg, :uid)"
            )->execute([
                ':tid' => (int) $data['item_id'],
                ':tpl' => $data['template'] ?? 'custom',
                ':msg' => $data['message'],
                ':uid' => $user['user_id'],
            ]);
        }

        jsonSuccess(null, 'Share logged', 201);
    }

    // GET /admin/whatsapp/logs
    public function logs(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM whatsapp_shares");
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT ws.*, u.name as shared_by_name
             FROM whatsapp_shares ws
             LEFT JOIN users u ON ws.shared_by = u.id
             ORDER BY ws.shared_at DESC LIMIT :l OFFSET :o"
        );
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }
}
