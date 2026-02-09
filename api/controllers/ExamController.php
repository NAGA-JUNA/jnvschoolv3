<?php
// ============================================
// ExamController — Enter Marks, View Results
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';

class ExamController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // POST /teacher/exams/marks
    public function enterMarks(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE, ROLE_ADMIN, ROLE_SUPER_ADMIN]);
        $data = getJsonInput();

        $records = $data['records'] ?? [];
        if (empty($records)) jsonError('Marks records are required', 422);

        $stmt = $this->db->prepare(
            "INSERT INTO exam_results (student_id, exam_name, subject, max_marks, marks_obtained, grade, academic_year, entered_by)
             VALUES (:sid, :exam, :subject, :max, :marks, :grade, :ay, :uid)"
        );

        $count = 0;
        foreach ($records as $rec) {
            if (empty($rec['student_id']) || empty($rec['exam_name']) || empty($rec['subject'])) continue;

            $marks = (float) ($rec['marks_obtained'] ?? 0);
            $max   = (int) ($rec['max_marks'] ?? 100);
            $grade = $rec['grade'] ?? $this->calculateGrade($marks, $max);

            $stmt->execute([
                ':sid'     => (int) $rec['student_id'],
                ':exam'    => trim($rec['exam_name']),
                ':subject' => trim($rec['subject']),
                ':max'     => $max,
                ':marks'   => $marks,
                ':grade'   => $grade,
                ':ay'      => $rec['academic_year'] ?? '2025-2026',
                ':uid'     => $user['user_id'],
            ]);
            $count++;
        }

        auditLog('enter_marks', 'exam_results', null, ['count' => $count]);
        jsonSuccess(['entered' => $count], 'Marks entered');
    }

    // GET /admin/students/{id}/exams
    public function studentResults(int $studentId): void {
        requireRole(array_merge(ADMIN_ROLES, [ROLE_TEACHER]));

        $ay = $_GET['academic_year'] ?? '';

        $sql = "SELECT er.*, u.name as entered_by_name
                FROM exam_results er
                LEFT JOIN users u ON er.entered_by = u.id
                WHERE er.student_id = :sid";
        $params = [':sid' => $studentId];

        if ($ay) {
            $sql .= " AND er.academic_year = :ay";
            $params[':ay'] = $ay;
        }

        $sql .= " ORDER BY er.exam_name, er.subject";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        jsonSuccess($stmt->fetchAll());
    }

    private function calculateGrade(float $marks, int $max): string {
        $pct = ($max > 0) ? ($marks / $max) * 100 : 0;
        if ($pct >= 90) return 'A+';
        if ($pct >= 80) return 'A';
        if ($pct >= 70) return 'B+';
        if ($pct >= 60) return 'B';
        if ($pct >= 50) return 'C';
        if ($pct >= 40) return 'D';
        return 'F';
    }
}
