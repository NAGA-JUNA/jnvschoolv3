<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) { setFlash('error', 'Student not found.'); header('Location: /admin/students.php'); exit; }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) { $error = 'Invalid request.'; }
    else {
        $data = [
            'admission_no' => trim($_POST['admission_no'] ?? ''),
            'name'         => trim($_POST['name'] ?? ''),
            'father_name'  => trim($_POST['father_name'] ?? ''),
            'mother_name'  => trim($_POST['mother_name'] ?? ''),
            'dob'          => $_POST['dob'] ?? null,
            'gender'       => $_POST['gender'] ?? null,
            'class'        => trim($_POST['class'] ?? ''),
            'section'      => trim($_POST['section'] ?? ''),
            'roll_no'      => $_POST['roll_no'] ? (int)$_POST['roll_no'] : null,
            'phone'        => trim($_POST['phone'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'address'      => trim($_POST['address'] ?? ''),
            'blood_group'  => trim($_POST['blood_group'] ?? ''),
            'status'       => $_POST['status'] ?? 'active',
            'admission_date' => $_POST['admission_date'] ?? null,
        ];

        if (!$data['admission_no'] || !$data['name']) { $error = 'Admission No and Name are required.'; }
        else {
            if ($id) {
                $db->prepare("UPDATE students SET admission_no=?,name=?,father_name=?,mother_name=?,dob=?,gender=?,class=?,section=?,roll_no=?,phone=?,email=?,address=?,blood_group=?,status=?,admission_date=? WHERE id=?")
                   ->execute([...array_values($data), $id]);
                auditLog('update_student', 'student', $id);
                setFlash('success', 'Student updated.');
            } else {
                $db->prepare("INSERT INTO students (admission_no,name,father_name,mother_name,dob,gender,class,section,roll_no,phone,email,address,blood_group,status,admission_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
                   ->execute(array_values($data));
                auditLog('create_student', 'student', (int)$db->lastInsertId());
                setFlash('success', 'Student added.');
            }
            header('Location: /admin/students.php'); exit;
        }
    }
}

$s = $student ?? [];
$pageTitle = $id ? 'Edit Student' : 'Add Student';
require_once __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3"><?= $pageTitle ?></h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="POST" class="row g-3">
  <?= csrfField() ?>
  <div class="col-md-4"><label class="form-label">Admission No *</label><input type="text" name="admission_no" class="form-control" required value="<?= e($s['admission_no']??'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?= e($s['name']??'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control" value="<?= e($s['father_name']??'') ?>"></div>
  <div class="col-md-4"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control" value="<?= e($s['mother_name']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">DOB</label><input type="date" name="dob" class="form-control" value="<?= e($s['dob']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option><option value="male" <?= ($s['gender']??'')==='male'?'selected':'' ?>>Male</option><option value="female" <?= ($s['gender']??'')==='female'?'selected':'' ?>>Female</option><option value="other" <?= ($s['gender']??'')==='other'?'selected':'' ?>>Other</option></select></div>
  <div class="col-md-2"><label class="form-label">Class</label><input type="text" name="class" class="form-control" value="<?= e($s['class']??'') ?>"></div>
  <div class="col-md-2"><label class="form-label">Section</label><input type="text" name="section" class="form-control" value="<?= e($s['section']??'') ?>"></div>
  <div class="col-md-2"><label class="form-label">Roll No</label><input type="number" name="roll_no" class="form-control" value="<?= e($s['roll_no']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($s['phone']??'') ?>"></div>
  <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($s['email']??'') ?>"></div>
  <div class="col-md-2"><label class="form-label">Blood Group</label><input type="text" name="blood_group" class="form-control" value="<?= e($s['blood_group']??'') ?>"></div>
  <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach(['active','inactive','alumni','transferred'] as $st): ?><option value="<?= $st ?>" <?= ($s['status']??'active')===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-2"><label class="form-label">Admission Date</label><input type="date" name="admission_date" class="form-control" value="<?= e($s['admission_date']??'') ?>"></div>
  <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= e($s['address']??'') ?></textarea></div>
  <div class="col-12"><button class="btn btn-primary">Save Student</button> <a href="/admin/students.php" class="btn btn-outline-secondary">Cancel</a></div>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
