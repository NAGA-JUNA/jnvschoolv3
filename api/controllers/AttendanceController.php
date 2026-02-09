<?php
// ============================================
// AttendanceController — Mark & View Attendance
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';

class AttendanceController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // POST /teacher/attendance/mark
    public function mark(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE, ROLE_ADMIN, ROLE_SUPER_ADMIN]);
        $data = getJsonInput();

        $records = $data['records'] ?? [];
        $date    = $data['date'] ?? date('Y-m-d');

        if (empty($records)) jsonError('Attendance records are required', 422);

        $stmt = $this->db->prepare(
            "INSERT INTO student_attendance (student_id, date, status, marked_by, remarks)
             VALUES (:sid, :date, :status, :uid, :remarks)
             ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by), remarks = VALUES(remarks)"
        );

        $count = 0;
        foreach ($records as $rec) {
            if (empty($rec['student_id']) || empty($rec['status'])) continue;
            $stmt->execute([
                ':sid'     => (int) $rec['student_id'],
                ':date'    => $date,
                ':status'  => $rec['status'],
                ':uid'     => $user['user_id'],
                ':remarks' => $rec['remarks'] ?? null,
            ]);
            $count++;
        }

        auditLog('mark_attendance', 'student_attendance', null, ['date' => $date, 'count' => $count]);
        jsonSuccess(['marked' => $count], 'Attendance marked');
    }

    // GET /admin/students/{id}/attendance
    public function studentHistory(int $studentId): void {
        requireRole(array_merge(ADMIN_ROLES, [ROLE_TEACHER]));

        $month = $_GET['month'] ?? date('Y-m');

        $stmt = $this->db->prepare(
            "SELECT sa.id, sa.date, sa.status, sa.remarks, u.name as marked_by_name
             FROM student_attendance sa
             LEFT JOIN users u ON sa.marked_by = u.id
             WHERE sa.student_id = :sid AND sa.date LIKE :month
             ORDER BY sa.date DESC"
        );
        $stmt->execute([':sid' => $studentId, ':month' => $month . '%']);
        jsonSuccess($stmt->fetchAll());
    }

    // GET /admin/teachers/{id}/attendance
    public function teacherHistory(int $teacherId): void {
        requireRole(ADMIN_ROLES);
        // Teacher attendance can be tracked via audit logs or a separate table
        // For now, return placeholder
        jsonSuccess([], 'Teacher attendance tracking available via audit logs');
    }
}
