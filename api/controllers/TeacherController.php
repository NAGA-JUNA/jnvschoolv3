<?php
// ============================================
// TeacherController — Full CRUD, Class Assignment, Inactive, Export/Import
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/excel.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class TeacherController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/teachers
    public function index(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();
        [$sort, $order] = getSortParams(['name','employee_id','joining_date','created_at']);

        $where = ["status = 'active'"];
        $params = [];

        if (!empty($_GET['search'])) {
            $where[] = "(name LIKE :s OR employee_id LIKE :s2 OR email LIKE :s3)";
            $params[':s']  = '%' . $_GET['search'] . '%';
            $params[':s2'] = '%' . $_GET['search'] . '%';
            $params[':s3'] = '%' . $_GET['search'] . '%';
        }
        if (!empty($_GET['subject'])) {
            $where[] = "JSON_CONTAINS(subjects, :subj)";
            $params[':subj'] = json_encode($_GET['subject']);
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM teachers WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE $whereSql ORDER BY `$sort` $order LIMIT :l OFFSET :o");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // POST /admin/teachers
    public function store(): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('employee_id', 'Employee ID')
          ->required('name', 'Full Name')
          ->unique('employee_id', 'teachers', 'employee_id', null, 'Employee ID')
          ->phone('phone', 'Phone')
          ->email('email', 'Email')
          ->date('date_of_birth', 'Date of Birth')
          ->date('joining_date', 'Joining Date');
        $v->validate();

        $subjects = isset($data['subjects']) ? (is_array($data['subjects']) ? json_encode($data['subjects']) : $data['subjects']) : null;
        $classes  = isset($data['classes_assigned']) ? (is_array($data['classes_assigned']) ? json_encode($data['classes_assigned']) : $data['classes_assigned']) : null;

        $stmt = $this->db->prepare(
            "INSERT INTO teachers (employee_id, name, gender, date_of_birth, phone, whatsapp, email,
                address, qualification, experience_years, joining_date, subjects, classes_assigned,
                employment_type, status, photo)
             VALUES (:eid, :name, :gender, :dob, :phone, :whatsapp, :email,
                :addr, :qual, :exp, :join, :subjects, :classes,
                :emp_type, :status, :photo)"
        );
        $stmt->execute([
            ':eid'      => trim($data['employee_id']),
            ':name'     => trim($data['name']),
            ':gender'   => $data['gender'] ?? null,
            ':dob'      => $data['date_of_birth'] ?? null,
            ':phone'    => $data['phone'] ?? null,
            ':whatsapp' => $data['whatsapp'] ?? null,
            ':email'    => $data['email'] ?? null,
            ':addr'     => $data['address'] ?? null,
            ':qual'     => $data['qualification'] ?? null,
            ':exp'      => (int)($data['experience_years'] ?? 0),
            ':join'     => $data['joining_date'] ?? null,
            ':subjects' => $subjects,
            ':classes'  => $classes,
            ':emp_type' => $data['employment_type'] ?? 'full-time',
            ':status'   => TEACHER_ACTIVE,
            ':photo'    => $data['photo'] ?? null,
        ]);

        $id = (int) $this->db->lastInsertId();
        auditLog('create', 'teachers', $id);
        jsonSuccess(['id' => $id], 'Teacher created', 201);
    }

    // GET /admin/teachers/{id}
    public function show(int $id): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $teacher = $stmt->fetch();
        if (!$teacher) jsonError('Teacher not found', 404);
        jsonSuccess($teacher);
    }

    // PUT /admin/teachers/{id}
    public function update(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        // Convert arrays to JSON
        if (isset($data['subjects']) && is_array($data['subjects'])) {
            $data['subjects'] = json_encode($data['subjects']);
        }
        if (isset($data['classes_assigned']) && is_array($data['classes_assigned'])) {
            $data['classes_assigned'] = json_encode($data['classes_assigned']);
        }

        $allowed = ['employee_id','name','gender','date_of_birth','phone','whatsapp','email',
            'address','qualification','experience_years','joining_date','subjects','classes_assigned',
            'employment_type','status','photo'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`$field` = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE teachers SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) jsonError('Teacher not found', 404);

        auditLog('update', 'teachers', $id);
        jsonSuccess(null, 'Teacher updated');
    }

    // DELETE /admin/teachers/{id} (soft delete)
    public function destroy(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $stmt = $this->db->prepare("UPDATE teachers SET status = 'inactive' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Teacher not found', 404);

        auditLog('soft_delete', 'teachers', $id);
        jsonSuccess(null, 'Teacher deactivated');
    }

    // PUT /admin/teachers/{id}/assign-classes
    public function assignClasses(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();
        $classes = isset($data['classes']) ? json_encode($data['classes']) : '[]';

        $stmt = $this->db->prepare("UPDATE teachers SET classes_assigned = :classes WHERE id = :id");
        $stmt->execute([':classes' => $classes, ':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Teacher not found', 404);

        auditLog('assign_classes', 'teachers', $id);
        jsonSuccess(null, 'Classes assigned');
    }

    // GET /admin/teachers/inactive
    public function inactive(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM teachers WHERE status = 'inactive'");
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE status = 'inactive' ORDER BY name LIMIT :l OFFSET :o");
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // GET /admin/teachers/export
    public function export(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->query("SELECT employee_id, name, gender, date_of_birth, phone, whatsapp, email, address, qualification, experience_years, joining_date, employment_type, status FROM teachers WHERE status = 'active' ORDER BY name");

        exportCSV($stmt->fetchAll(), 'teachers_export_' . date('Ymd') . '.csv', [
            'Employee ID','Name','Gender','Date of Birth','Phone','WhatsApp','Email','Address',
            'Qualification','Experience Years','Joining Date','Employment Type','Status'
        ]);
    }

    // POST /admin/teachers/import
    public function import(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        if (!isset($_FILES['file'])) jsonError('CSV file is required', 400);

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) jsonError('Only CSV/Excel files allowed', 400);

        $result = importTeachersFromCSV($_FILES['file']['tmp_name']);
        auditLog('import', 'teachers', null, $result);
        jsonSuccess($result, 'Import completed');
    }

    // GET /teacher/profile
    public function myProfile(): void {
        $user = requireRole([ROLE_TEACHER]);
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE user_id = :uid");
        $stmt->execute([':uid' => $user['user_id']]);
        $teacher = $stmt->fetch();
        if (!$teacher) jsonError('Teacher profile not found', 404);
        jsonSuccess($teacher);
    }

    // PUT /teacher/profile
    public function updateMyProfile(): void {
        $user = requireRole([ROLE_TEACHER]);
        $data = getJsonInput();

        // Teachers can only update limited fields
        $allowed = ['phone', 'whatsapp', 'address'];
        $sets = [];
        $params = [':uid' => $user['user_id']];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`$field` = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE teachers SET " . implode(', ', $sets) . " WHERE user_id = :uid");
        $stmt->execute($params);

        auditLog('update_profile', 'teachers', null);
        jsonSuccess(null, 'Profile updated');
    }

    // GET /teacher/students
    public function myStudents(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);

        $stmt = $this->db->prepare("SELECT classes_assigned FROM teachers WHERE user_id = :uid");
        $stmt->execute([':uid' => $user['user_id']]);
        $teacher = $stmt->fetch();

        if (!$teacher || empty($teacher['classes_assigned'])) {
            jsonSuccess([]);
            return;
        }

        $classes = json_decode($teacher['classes_assigned'], true) ?: [];
        if (empty($classes)) {
            jsonSuccess([]);
            return;
        }

        // Parse class-section pairs (e.g. "10-A")
        $conditions = [];
        $params = [];
        foreach ($classes as $i => $cs) {
            $parts = explode('-', $cs, 2);
            if (count($parts) === 2) {
                $conditions[] = "(class = :c$i AND section = :s$i)";
                $params[":c$i"] = $parts[0];
                $params[":s$i"] = $parts[1];
            } else {
                $conditions[] = "class = :c$i";
                $params[":c$i"] = $parts[0];
            }
        }

        $whereSql = implode(' OR ', $conditions);
        $stmt = $this->db->prepare("SELECT * FROM students WHERE status = 'active' AND ($whereSql) ORDER BY class, section, roll_no");
        $stmt->execute($params);
        jsonSuccess($stmt->fetchAll());
    }
}
