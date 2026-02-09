<?php
// ============================================
// MessageController — WhatsApp Message History
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class MessageController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/students/{id}/messages
    public function studentMessages(int $studentId): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT sm.*, u.name as sent_by_name
             FROM student_messages sm
             LEFT JOIN users u ON sm.sent_by = u.id
             WHERE sm.student_id = :sid ORDER BY sm.sent_at DESC"
        );
        $stmt->execute([':sid' => $studentId]);
        jsonSuccess($stmt->fetchAll());
    }

    // GET /admin/teachers/{id}/messages
    public function teacherMessages(int $teacherId): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT tm.*, u.name as sent_by_name
             FROM teacher_messages tm
             LEFT JOIN users u ON tm.sent_by = u.id
             WHERE tm.teacher_id = :tid ORDER BY tm.sent_at DESC"
        );
        $stmt->execute([':tid' => $teacherId]);
        jsonSuccess($stmt->fetchAll());
    }
}
