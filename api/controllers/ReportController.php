<?php
// ============================================
// ReportController — Generate Report Data
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class ReportController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/reports
    public function index(): void {
        requireRole(ADMIN_ROLES);

        $type = $_GET['type'] ?? 'overview';

        switch ($type) {
            case 'students':
                $this->studentReport();
                break;
            case 'teachers':
                $this->teacherReport();
                break;
            case 'attendance':
                $this->attendanceReport();
                break;
            case 'admissions':
                $this->admissionReport();
                break;
            default:
                $this->overview();
        }
    }

    private function overview(): void {
        $report = [];
        $report['students'] = [
            'total'     => (int)$this->db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn(),
            'alumni'    => (int)$this->db->query("SELECT COUNT(*) FROM students WHERE status='alumni'")->fetchColumn(),
            'inactive'  => (int)$this->db->query("SELECT COUNT(*) FROM students WHERE status='inactive'")->fetchColumn(),
        ];
        $report['teachers'] = [
            'total'    => (int)$this->db->query("SELECT COUNT(*) FROM teachers WHERE status='active'")->fetchColumn(),
            'inactive' => (int)$this->db->query("SELECT COUNT(*) FROM teachers WHERE status='inactive'")->fetchColumn(),
        ];
        $report['admissions'] = [
            'pending'    => (int)$this->db->query("SELECT COUNT(*) FROM admissions WHERE status='pending'")->fetchColumn(),
            'approved'   => (int)$this->db->query("SELECT COUNT(*) FROM admissions WHERE status='approved'")->fetchColumn(),
            'rejected'   => (int)$this->db->query("SELECT COUNT(*) FROM admissions WHERE status='rejected'")->fetchColumn(),
            'waitlisted' => (int)$this->db->query("SELECT COUNT(*) FROM admissions WHERE status='waitlisted'")->fetchColumn(),
        ];
        $report['notifications'] = [
            'pending'  => (int)$this->db->query("SELECT COUNT(*) FROM notifications WHERE status='pending'")->fetchColumn(),
            'approved' => (int)$this->db->query("SELECT COUNT(*) FROM notifications WHERE status='approved'")->fetchColumn(),
        ];
        $report['events_upcoming'] = (int)$this->db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
        $report['gallery_items'] = (int)$this->db->query("SELECT COUNT(*) FROM gallery_items WHERE status='approved'")->fetchColumn();

        jsonSuccess($report);
    }

    private function studentReport(): void {
        $data = [];
        $data['by_class'] = $this->db->query(
            "SELECT class, COUNT(*) as count FROM students WHERE status='active' GROUP BY class ORDER BY CAST(class AS UNSIGNED)"
        )->fetchAll();
        $data['by_gender'] = $this->db->query(
            "SELECT gender, COUNT(*) as count FROM students WHERE status='active' GROUP BY gender"
        )->fetchAll();
        $data['by_status'] = $this->db->query(
            "SELECT status, COUNT(*) as count FROM students GROUP BY status"
        )->fetchAll();
        $data['monthly_admissions'] = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM students GROUP BY month ORDER BY month DESC LIMIT 12"
        )->fetchAll();
        jsonSuccess($data);
    }

    private function teacherReport(): void {
        $data = [];
        $data['by_employment_type'] = $this->db->query(
            "SELECT employment_type, COUNT(*) as count FROM teachers WHERE status='active' GROUP BY employment_type"
        )->fetchAll();
        $data['by_gender'] = $this->db->query(
            "SELECT gender, COUNT(*) as count FROM teachers WHERE status='active' GROUP BY gender"
        )->fetchAll();
        $data['experience_distribution'] = $this->db->query(
            "SELECT CASE
                WHEN experience_years < 2 THEN '0-1 years'
                WHEN experience_years < 5 THEN '2-4 years'
                WHEN experience_years < 10 THEN '5-9 years'
                ELSE '10+ years'
             END as range_label, COUNT(*) as count
             FROM teachers WHERE status='active' GROUP BY range_label"
        )->fetchAll();
        jsonSuccess($data);
    }

    private function attendanceReport(): void {
        $month = $_GET['month'] ?? date('Y-m');
        $data = [];
        $data['daily_summary'] = $this->db->prepare(
            "SELECT date, status, COUNT(*) as count FROM student_attendance WHERE date LIKE :m GROUP BY date, status ORDER BY date"
        );
        $data['daily_summary']->execute([':m' => $month . '%']);
        $data['daily_summary'] = $data['daily_summary']->fetchAll();
        jsonSuccess($data);
    }

    private function admissionReport(): void {
        $data = [];
        $data['by_status'] = $this->db->query(
            "SELECT status, COUNT(*) as count FROM admissions GROUP BY status"
        )->fetchAll();
        $data['by_class'] = $this->db->query(
            "SELECT class_applied, COUNT(*) as count FROM admissions GROUP BY class_applied ORDER BY CAST(class_applied AS UNSIGNED)"
        )->fetchAll();
        $data['monthly_trend'] = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM admissions GROUP BY month ORDER BY month DESC LIMIT 12"
        )->fetchAll();
        jsonSuccess($data);
    }
}
