<?php
require_once __DIR__.'/../includes/auth.php';
checkMaintenance();
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$schoolAddress = getSetting('school_address', '');
$whatsappNumber = getSetting('whatsapp_api_number', '');
$navLogo = getSetting('school_logo', '');
$logoVersion = getSetting('logo_updated_at', '0');
$logoPath = '';
if ($navLogo) { $logoPath = (strpos($navLogo, '/uploads/') === 0) ? $navLogo : (file_exists(__DIR__.'/../uploads/branding/'.$navLogo) ? '/uploads/branding/'.$navLogo : '/uploads/logo/'.$navLogo); $logoPath .= '?v=' . $logoVersion; }

// Social links
$socialFacebook = getSetting('social_facebook', '');
$socialTwitter = getSetting('social_twitter', '');
$socialInstagram = getSetting('social_instagram', '');
$socialYoutube = getSetting('social_youtube', '');
$socialLinkedin = getSetting('social_linkedin', '');

// Bell notifications
$bellNotifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
$notifCount = $db->query("SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

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

        $stmt = $db->prepare("INSERT INTO admissions (student_name, dob, gender, class_applied, father_name, mother_name, phone, email, address, previous_school, remarks, documents, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
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
    <?php $favicon = getSetting('school_favicon', ''); if ($favicon): $favVer = getSetting('favicon_updated_at', '0'); $favPath = (strpos($favicon, '/uploads/') === 0) ? $favicon : (file_exists(__DIR__.'/../uploads/branding/'.$favicon) ? '/uploads/branding/'.$favicon : '/uploads/logo/'.$favicon); ?><link rel="icon" href="<?= e($favPath) ?>?v=<?= e($favVer) ?>"><?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .top-bar { background: #0a0f1a; color: #fff; padding: 0.4rem 0; font-size: 0.78rem; }
        .top-bar a { color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .marquee-text { white-space: nowrap; overflow: hidden; }
        .marquee-text span { display: inline-block; animation: marqueeScroll 20s linear infinite; }
        @keyframes marqueeScroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        .main-navbar { background: #0f172a; padding: 0.5rem 0; }
        .main-navbar .nav-link { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 0.9rem; padding: 0.5rem 0.8rem; }
        .main-navbar .nav-link:hover, .main-navbar .nav-link.active { color: #fff; }
        .notif-bell-btn { background: #dc3545; color: #fff; border: none; border-radius: 8px; padding: 0.4rem 0.9rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; position: relative; transition: background 0.2s; }
        .notif-bell-btn:hover { background: #c82333; }
        .notif-badge { position: absolute; top: -6px; right: -8px; background: #ffc107; color: #000; font-size: 0.65rem; font-weight: 700; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .login-nav-btn { background: transparent; border: 1.5px solid rgba(255,255,255,0.5); color: #fff; border-radius: 8px; padding: 0.4rem 1.2rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .login-nav-btn:hover { background: #fff; color: #0f172a; }
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s; animation: whatsappPulse 2s infinite; }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }
        @keyframes whatsappPulse { 0%, 100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); } 50% { box-shadow: 0 4px 30px rgba(37,211,102,0.7); } }
        .hero-banner { background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%); color: #fff; padding: 3rem 0; }
        /* Dark Footer */
        .site-footer { background: #1a1a2e; color: #fff; margin-top: 0; }
        .footer-cta { background: #0f2557; padding: 4rem 0; text-align: center; }
        .footer-cta h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2.2rem; color: #fff; margin-bottom: 1rem; }
        .footer-cta p { color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto 1.5rem; }
        .footer-heading { text-transform: uppercase; font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 1rem; position: relative; padding-bottom: 0.5rem; color: #fff; }
        .footer-heading::after { content: ''; position: absolute; bottom: 0; left: 0; width: 30px; height: 2px; background: var(--theme-primary, #1e40af); }
        .footer-link { color: rgba(255,255,255,0.65); text-decoration: none; transition: color 0.2s; font-size: 0.9rem; }
        .footer-link:hover { color: #fff; }
        .footer-social a { width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.3); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; font-size: 0.9rem; }
        .footer-social a:hover { background: var(--theme-primary, #1e40af); border-color: var(--theme-primary, #1e40af); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); }
        @media (max-width: 767.98px) {
            .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; }
        }
        @media (max-width: 575.98px) {
            .navbar-brand { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .navbar-collapse .d-flex { flex-direction: column; width: 100%; gap: 0.5rem; margin-top: 0.75rem; }
            .notif-bell-btn, .login-nav-btn { width: 100%; text-align: center; display: block; }
            .top-bar .d-flex.gap-3 { font-size: 0.7rem; gap: 0.4rem !important; }
            .hero-banner { padding: 2rem 0; }
            .hero-banner h1 { font-size: 1.5rem; }
            .col-md-3, .col-md-4 { flex: 0 0 100%; max-width: 100%; }
            .site-footer .row > div { text-align: center; }
            .footer-heading::after { left: 50%; transform: translateX(-50%); }
            .footer-social { justify-content: center; }
            .site-footer { border-radius: 20px 20px 0 0; }
            .whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 16px; right: 16px; }
        }
    </style>
</head>
<body>

<?php $currentPage = 'apply'; include __DIR__ . '/../includes/public-navbar.php'; ?>

<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-file-earmark-plus-fill me-2"></i><?= e(getSetting('admission_hero_title', 'Apply for Admission')) ?></h1>
        <p class="mb-0 opacity-75"><?= e(getSetting('admission_hero_subtitle', 'Submit your application to ' . $schoolName)) ?></p>
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

<?php include __DIR__ . '/../includes/public-footer.php'; ?>
</body>
</html>