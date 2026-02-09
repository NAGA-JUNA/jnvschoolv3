<?php
// ============================================
// AdmissionController — Public Submit, Admin List, Status Update, Export
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/excel.php';
require_once __DIR__ . '/../middleware/auth.php';

class AdmissionController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // POST /public/admissions
    public function publicSubmit(): void {
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('student_name', 'Student Name')
          ->required('class_applied', 'Class')
          ->required('date_of_birth', 'Date of Birth')->date('date_of_birth')
          ->required('gender', 'Gender')->inList('gender', ['male','female','other'])
          ->required('parent_name', 'Parent Name')
          ->required('parent_phone', 'Parent Phone')->phone('parent_phone')
          ->required('address', 'Address')
          ->email('parent_email', 'Parent Email');
        $v->validate();

        $stmt = $this->db->prepare(
            "INSERT INTO admissions (student_name, class_applied, date_of_birth, gender, parent_name,
                parent_phone, parent_email, address, previous_school, documents)
             VALUES (:name, :class, :dob, :gender, :parent, :phone, :email, :addr, :prev, :docs)"
        );
        $stmt->execute([
            ':name'   => trim($data['student_name']),
            ':class'  => trim($data['class_applied']),
            ':dob'    => $data['date_of_birth'],
            ':gender' => $data['gender'],
            ':parent' => trim($data['parent_name']),
            ':phone'  => trim($data['parent_phone']),
            ':email'  => $data['parent_email'] ?? null,
            ':addr'   => trim($data['address']),
            ':prev'   => $data['previous_school'] ?? null,
            ':docs'   => isset($data['documents']) ? json_encode($data['documents']) : null,
        ]);

        jsonSuccess(['id' => $this->db->lastInsertId()], 'Application submitted successfully', 201);
    }

    // GET /admin/admissions
    public function index(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $where = ["1=1"];
        $params = [];
        if (!empty($_GET['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $_GET['status'];
        }
        if (!empty($_GET['search'])) {
            $where[] = "(student_name LIKE :s OR parent_name LIKE :s2 OR parent_phone LIKE :s3)";
            $params[':s']  = '%' . $_GET['search'] . '%';
            $params[':s2'] = '%' . $_GET['search'] . '%';
            $params[':s3'] = '%' . $_GET['search'] . '%';
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM admissions WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT a.*, u.name as reviewed_by_name FROM admissions a
             LEFT JOIN users u ON a.reviewed_by = u.id
             WHERE $whereSql ORDER BY a.created_at DESC LIMIT :l OFFSET :o"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // PATCH /admin/admissions/{id}
    public function updateStatus(int $id): void {
        $user = requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $status = $data['status'] ?? '';
        $notes  = $data['notes'] ?? null;

        if (!in_array($status, ['approved','rejected','waitlisted','pending'])) {
            jsonError('Invalid status', 422);
        }

        $stmt = $this->db->prepare(
            "UPDATE admissions SET status = :status, notes = :notes, reviewed_by = :uid, reviewed_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':status' => $status, ':notes' => $notes, ':uid' => $user['user_id'], ':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Application not found', 404);

        auditLog('update_status', 'admissions', $id, ['status' => $status]);
        jsonSuccess(null, 'Application status updated');
    }

    // GET /admin/admissions/export
    public function export(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->query("SELECT student_name, class_applied, date_of_birth, gender, parent_name, parent_phone, parent_email, address, previous_school, status, created_at FROM admissions ORDER BY created_at DESC");

        exportCSV($stmt->fetchAll(), 'admissions_export_' . date('Ymd') . '.csv', [
            'Student Name','Class Applied','Date of Birth','Gender','Parent Name','Parent Phone',
            'Parent Email','Address','Previous School','Status','Created At'
        ]);
    }
}
