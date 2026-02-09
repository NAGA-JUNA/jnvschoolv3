<?php
// ============================================
// DashboardController — Metrics & Activity
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class DashboardController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/dashboard/metrics
    public function adminMetrics(): void {
        requireRole(ADMIN_ROLES);

        $metrics = [];

        $metrics['total_students']  = (int) $this->db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
        $metrics['total_teachers']  = (int) $this->db->query("SELECT COUNT(*) FROM teachers WHERE status='active'")->fetchColumn();
        $metrics['pending_admissions'] = (int) $this->db->query("SELECT COUNT(*) FROM admissions WHERE status='pending'")->fetchColumn();
        $metrics['pending_notifications'] = (int) $this->db->query("SELECT COUNT(*) FROM notifications WHERE status='pending'")->fetchColumn();
        $metrics['pending_gallery'] = (int) $this->db->query("SELECT COUNT(*) FROM gallery_items WHERE status='pending'")->fetchColumn();
        $metrics['upcoming_events'] = (int) $this->db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
        $metrics['total_alumni']    = (int) $this->db->query("SELECT COUNT(*) FROM students WHERE status='alumni'")->fetchColumn();

        // Gender breakdown
        $stmt = $this->db->query("SELECT gender, COUNT(*) as count FROM students WHERE status='active' GROUP BY gender");
        $metrics['gender_breakdown'] = $stmt->fetchAll();

        // Class-wise count
        $stmt = $this->db->query("SELECT class, COUNT(*) as count FROM students WHERE status='active' GROUP BY class ORDER BY CAST(class AS UNSIGNED)");
        $metrics['class_wise'] = $stmt->fetchAll();

        jsonSuccess($metrics);
    }

    // GET /admin/dashboard/activity
    public function adminActivity(): void {
        requireRole(ADMIN_ROLES);

        $stmt = $this->db->prepare(
            "SELECT al.id, al.action, al.entity_type, al.entity_id, al.created_at,
                    u.name as user_name, u.role as user_role
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC LIMIT 20"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }

    // GET /admin/alerts
    public function alerts(): void {
        requireRole(ADMIN_ROLES);

        $alerts = [];

        // Pending notifications
        $count = (int) $this->db->query("SELECT COUNT(*) FROM notifications WHERE status='pending'")->fetchColumn();
        if ($count > 0) {
            $alerts[] = ['type' => 'warning', 'message' => "$count notifications awaiting approval", 'link' => '/admin/notifications'];
        }

        // Pending gallery
        $count = (int) $this->db->query("SELECT COUNT(*) FROM gallery_items WHERE status='pending'")->fetchColumn();
        if ($count > 0) {
            $alerts[] = ['type' => 'info', 'message' => "$count gallery uploads pending review", 'link' => '/admin/gallery-approvals'];
        }

        // Pending admissions
        $count = (int) $this->db->query("SELECT COUNT(*) FROM admissions WHERE status='pending'")->fetchColumn();
        if ($count > 0) {
            $alerts[] = ['type' => 'warning', 'message' => "$count admission applications pending", 'link' => '/admin/admissions'];
        }

        // Expiring notifications
        $count = (int) $this->db->query("SELECT COUNT(*) FROM notifications WHERE expiry_date IS NOT NULL AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) AND status='approved'")->fetchColumn();
        if ($count > 0) {
            $alerts[] = ['type' => 'info', 'message' => "$count notifications expiring soon"];
        }

        jsonSuccess($alerts);
    }

    // GET /teacher/dashboard/metrics
    public function teacherMetrics(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);

        // Get teacher record
        $stmt = $this->db->prepare("SELECT id, classes_assigned FROM teachers WHERE user_id = :uid");
        $stmt->execute([':uid' => $user['user_id']]);
        $teacher = $stmt->fetch();

        $metrics = [];
        $metrics['my_classes'] = $teacher ? json_decode($teacher['classes_assigned'] ?? '[]', true) : [];

        // My submissions count
        $metrics['my_notifications'] = (int) $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE submitted_by = :uid")
            ->execute([':uid' => $user['user_id']]) ? $this->db->query("SELECT FOUND_ROWS()")->fetchColumn() : 0;

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE submitted_by = :uid");
        $stmt->execute([':uid' => $user['user_id']]);
        $metrics['my_notifications'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM gallery_items WHERE uploaded_by = :uid");
        $stmt->execute([':uid' => $user['user_id']]);
        $metrics['my_gallery_uploads'] = (int) $stmt->fetchColumn();

        $metrics['upcoming_events'] = (int) $this->db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();

        jsonSuccess($metrics);
    }

    // GET /teacher/dashboard/activity
    public function teacherActivity(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);

        $stmt = $this->db->prepare(
            "SELECT al.id, al.action, al.entity_type, al.entity_id, al.created_at
             FROM audit_logs al
             WHERE al.user_id = :uid
             ORDER BY al.created_at DESC LIMIT 15"
        );
        $stmt->execute([':uid' => $user['user_id']]);
        jsonSuccess($stmt->fetchAll());
    }
}
