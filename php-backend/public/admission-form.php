<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = trim($_POST['student_name'] ?? '');
    $dob = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $classApplied = $_POST['class_applied'] ?? '';
    $fatherName = trim($_POST['father_name'] ?? '');
    $motherName = trim($_POST['mother_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $previousSchool = trim($_POST['previous_school'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    if (!$studentName) $errors[] = 'Student name is required.';
    if (!$dob) $errors[] = 'Date of birth is required.';
    if (!$gender) $errors[] = 'Gender is required.';
    if (!$classApplied) $errors[] = 'Class applied for is required.';
    if (!$fatherName) $errors[] = "Father's name is required.";
    if (!$phone) $errors[] = 'Phone number is required.';
    if ($phone && !preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = 'Invalid phone number.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

    if (empty($errors)) {
        $docPath = null;
        if (!empty($_FILES['document']['name']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
            $maxSize = 5 * 1024 * 1024;
            if (in_array($_FILES['document']['type'], $allowed) && $_FILES['document']['size'] <= $maxSize) {
                $ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                $filename = 'admission_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/documents/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $filename)) {
                    $docPath = 'uploads/documents/' . $filename;
                }
            }
        }

        $stmt = $db->prepare("INSERT INTO admissions (student_name, date_of_birth, gender, class_applied, father_name, mother_name, phone, email, address, previous_school, remarks, document_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$studentName, $dob, $gender, $classApplied, $fatherName, $motherName, $phone, $email ?: null, $address, $previousSchool, $remarks, $docPath]);
        auditLog('public_admission_submit', 'admission', (int)$db->lastInsertId(), "Student: $studentName, Class: $classApplied");
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply for Admission — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .hero-banner { background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%); color: #fff; padding: 3rem 0; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f172a;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/public/notifications.php"><i class="bi bi-mortarboard-fill me-2"></i><?= e($schoolName) ?></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#pubNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="pubNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/admission-form.php">Apply Now</a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-light ms-lg-2 px-3" href="/login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-file-earmark-plus-fill me-2"></i>Apply for Admission</h1>
        <p class="mb-0 opacity-75">Submit your application to <?= e($schoolName) ?></p>
    </div>
</div>

<div class="container py-4">
    <?php if ($success): ?>
        <div class="card border-0 shadow-sm" style="max-width:600px;margin:2rem auto;">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                <h3 class="fw-bold text-success mt-3">Application Submitted!</h3>
                <p class="text-muted">Your admission application has been received. Our team will review it and contact you soon.</p>
                <a href="/public/admission-form.php" class="btn btn-primary">Submit Another</a>
            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm" style="max-width:800px;margin:0 auto;">
            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-person me-2"></i>Student Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="student_name" class="form-control" required maxlength="100" value="<?= e($_POST['student_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" required value="<?= e($_POST['date_of_birth'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select</option>
                                <option value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Class Applied For <span class="text-danger">*</span></label>
                            <select name="class_applied" class="form-select" required>
                                <option value="">Select Class</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($_POST['class_applied'] ?? '') == $i ? 'selected' : '' ?>>Class <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Previous School</label>
                            <input type="text" name="previous_school" class="form-control" maxlength="200" value="<?= e($_POST['previous_school'] ?? '') ?>">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-people me-2"></i>Parent / Guardian Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Father's Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" required maxlength="100" value="<?= e($_POST['father_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control" maxlength="100" value="<?= e($_POST['mother_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required maxlength="15" pattern="[0-9]{10,15}" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" maxlength="100" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Document (optional)</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF or image, max 5MB</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2" maxlength="500"><?= e($_POST['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" maxlength="500"><?= e($_POST['remarks'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-send me-2"></i>Submit Application</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</p>
        <small class="text-muted">Powered by JNV School Management System</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
