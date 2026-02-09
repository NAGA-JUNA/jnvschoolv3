<?php
// ============================================
// StudentController — Full CRUD, Search, Bulk Promote, Alumni, Export/Import
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/excel.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class StudentController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/students
    public function index(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();
        [$sort, $order] = getSortParams(['name','class','section','admission_no','created_at','roll_no']);

        $where  = ["status != 'alumni'"];
        $params = [];

        if (!empty($_GET['search'])) {
            $where[]         = "(name LIKE :search OR admission_no LIKE :search2 OR parent_phone LIKE :search3)";
            $params[':search']  = '%' . $_GET['search'] . '%';
            $params[':search2'] = '%' . $_GET['search'] . '%';
            $params[':search3'] = '%' . $_GET['search'] . '%';
        }
        if (!empty($_GET['class'])) {
            $where[]           = "class = :class";
            $params[':class']  = $_GET['class'];
        }
        if (!empty($_GET['section'])) {
            $where[]             = "section = :section";
            $params[':section']  = $_GET['section'];
        }
        if (!empty($_GET['status'])) {
            $where[]            = "status = :status";
            $params[':status']  = $_GET['status'];
        }
        if (!empty($_GET['academic_year'])) {
            $where[]                   = "academic_year = :ay";
            $params[':ay'] = $_GET['academic_year'];
        }

        $whereSql = implode(' AND ', $where);

        // Count
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        // Fetch
        $stmt = $this->db->prepare(
            "SELECT * FROM students WHERE $whereSql ORDER BY `$sort` $order LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // POST /admin/students
    public function store(): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('admission_no', 'Admission No')
          ->required('name', 'Full Name')
          ->required('class', 'Class')
          ->unique('admission_no', 'students', 'admission_no', null, 'Admission No')
          ->phone('parent_phone', 'Parent Phone')
          ->email('parent_email', 'Parent Email')
          ->date('date_of_birth', 'Date of Birth')
          ->inList('gender', ['male','female','other'], 'Gender');
        $v->validate();

        $stmt = $this->db->prepare(
            "INSERT INTO students (admission_no, name, gender, date_of_birth, roll_no, class, section,
                academic_year, blood_group, father_name, mother_name, parent_phone, whatsapp_number,
                parent_email, address, emergency_contact, status, photo)
             VALUES (:adm, :name, :gender, :dob, :roll, :class, :section,
                :ay, :blood, :father, :mother, :phone, :whatsapp,
                :email, :addr, :emergency, :status, :photo)"
        );
        $stmt->execute([
            ':adm'       => trim($data['admission_no']),
            ':name'      => trim($data['name']),
            ':gender'    => $data['gender'] ?? null,
            ':dob'       => $data['date_of_birth'] ?? null,
            ':roll'      => !empty($data['roll_no']) ? (int)$data['roll_no'] : null,
            ':class'     => trim($data['class']),
            ':section'   => $data['section'] ?? null,
            ':ay'        => $data['academic_year'] ?? '2025-2026',
            ':blood'     => $data['blood_group'] ?? null,
            ':father'    => $data['father_name'] ?? null,
            ':mother'    => $data['mother_name'] ?? null,
            ':phone'     => $data['parent_phone'] ?? null,
            ':whatsapp'  => $data['whatsapp_number'] ?? null,
            ':email'     => $data['parent_email'] ?? null,
            ':addr'      => $data['address'] ?? null,
            ':emergency' => $data['emergency_contact'] ?? null,
            ':status'    => $data['status'] ?? STUDENT_ACTIVE,
            ':photo'     => $data['photo'] ?? null,
        ]);

        $id = (int) $this->db->lastInsertId();
        auditLog('create', 'students', $id);
        jsonSuccess(['id' => $id], 'Student created', 201);
    }

    // GET /admin/students/{id}
    public function show(int $id): void {
        requireRole(ADMIN_ROLES);

        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        if (!$student) jsonError('Student not found', 404);
        jsonSuccess($student);
    }

    // PUT /admin/students/{id}
    public function update(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('name', 'Full Name')
          ->required('class', 'Class')
          ->unique('admission_no', 'students', 'admission_no', $id, 'Admission No')
          ->phone('parent_phone', 'Parent Phone')
          ->email('parent_email', 'Parent Email')
          ->date('date_of_birth', 'Date of Birth');
        $v->validate();

        $allowed = [
            'admission_no','name','gender','date_of_birth','roll_no','class','section',
            'academic_year','blood_group','father_name','mother_name','parent_phone',
            'whatsapp_number','parent_email','address','emergency_contact','status','photo'
        ];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`$field` = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE students SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) jsonError('Student not found', 404);

        auditLog('update', 'students', $id);
        jsonSuccess(null, 'Student updated');
    }

    // DELETE /admin/students/{id} (soft delete → set inactive)
    public function destroy(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $stmt = $this->db->prepare("UPDATE students SET status = 'inactive' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) jsonError('Student not found', 404);

        auditLog('soft_delete', 'students', $id);
        jsonSuccess(null, 'Student deactivated');
    }

    // GET /admin/students/alumni
    public function alumni(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $where = "status = 'alumni'";
        $params = [];
        if (!empty($_GET['search'])) {
            $where .= " AND (name LIKE :s OR admission_no LIKE :s2)";
            $params[':s']  = '%' . $_GET['search'] . '%';
            $params[':s2'] = '%' . $_GET['search'] . '%';
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE $where");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM students WHERE $where ORDER BY name ASC LIMIT :l OFFSET :o");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // POST /admin/students/bulk-promote
    public function bulkPromote(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data = getJsonInput();

        $studentIds = $data['student_ids'] ?? [];
        $newClass   = $data['new_class'] ?? '';
        $newAY      = $data['academic_year'] ?? '';

        if (empty($studentIds) || $newClass === '') {
            jsonError('student_ids and new_class are required', 422);
        }

        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $stmt = $this->db->prepare(
            "UPDATE students SET class = ?, academic_year = ?, section = NULL WHERE id IN ($placeholders) AND status = 'active'"
        );
        $params = [$newClass, $newAY ?: '2025-2026'];
        foreach ($studentIds as $sid) $params[] = (int) $sid;
        $stmt->execute($params);

        auditLog('bulk_promote', 'students', null, ['count' => $stmt->rowCount(), 'new_class' => $newClass]);
        jsonSuccess(['promoted' => $stmt->rowCount()], 'Students promoted');
    }

    // GET /admin/students/export
    public function export(): void {
        requireRole(ADMIN_ROLES);

        $stmt = $this->db->query("SELECT admission_no, name, class, section, roll_no, gender, date_of_birth, blood_group, father_name, mother_name, parent_phone, whatsapp_number, parent_email, address, status FROM students WHERE status = 'active' ORDER BY class, section, roll_no");

        exportCSV($stmt->fetchAll(), 'students_export_' . date('Ymd') . '.csv', [
            'Admission No','Name','Class','Section','Roll No','Gender','Date of Birth','Blood Group',
            'Father Name','Mother Name','Parent Phone','WhatsApp Number','Parent Email','Address','Status'
        ]);
    }

    // POST /admin/students/import
    public function import(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        if (!isset($_FILES['file'])) {
            jsonError('CSV file is required', 400);
        }

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
            jsonError('Only CSV/Excel files are allowed', 400);
        }

        $result = importStudentsFromCSV($_FILES['file']['tmp_name']);
        auditLog('import', 'students', null, $result);
        jsonSuccess($result, 'Import completed');
    }
}
