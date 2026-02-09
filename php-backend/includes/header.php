<?php
$flash = getFlash();
$schoolName = 'School Admin';
try {
    $db = getDB();
    $s = $db->query("SELECT setting_value FROM settings WHERE setting_key='school_name' LIMIT 1");
    $r = $s->fetch();
    if ($r) $schoolName = $r['setting_value'];
} catch (Exception $ex) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Dashboard') ?> — <?= e($schoolName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  .sidebar{min-height:100vh;background:#1e293b;color:#fff}
  .sidebar a{color:#cbd5e1;text-decoration:none;padding:10px 20px;display:block}
  .sidebar a:hover,.sidebar a.active{background:#334155;color:#fff}
  .content{padding:24px}
</style>
</head>
<body>
<?php if (isLoggedIn()): ?>
<div class="d-flex">
  <nav class="sidebar d-none d-md-block" style="width:240px">
    <div class="p-3 fw-bold fs-5"><?= e($schoolName) ?></div>
    <hr class="border-secondary mx-3">
    <?php if (isAdmin()): ?>
      <a href="/admin/dashboard.php" class="<?= str_contains($_SERVER['PHP_SELF'],'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="/admin/students.php"><i class="bi bi-people me-2"></i>Students</a>
      <a href="/admin/teachers.php"><i class="bi bi-person-badge me-2"></i>Teachers</a>
      <a href="/admin/admissions.php"><i class="bi bi-person-plus me-2"></i>Admissions</a>
      <a href="/admin/notifications.php"><i class="bi bi-bell me-2"></i>Notifications</a>
      <a href="/admin/gallery.php"><i class="bi bi-images me-2"></i>Gallery</a>
      <a href="/admin/events.php"><i class="bi bi-calendar-event me-2"></i>Events</a>
      <a href="/admin/reports.php"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</a>
      <a href="/admin/settings.php"><i class="bi bi-gear me-2"></i>Settings</a>
    <?php else: ?>
      <a href="/teacher/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="/teacher/post-notification.php"><i class="bi bi-megaphone me-2"></i>Post Notification</a>
      <a href="/teacher/upload-gallery.php"><i class="bi bi-upload me-2"></i>Upload Gallery</a>
      <a href="/teacher/attendance.php"><i class="bi bi-check2-square me-2"></i>Attendance</a>
      <a href="/teacher/exams.php"><i class="bi bi-journal-text me-2"></i>Exams</a>
    <?php endif; ?>
    <hr class="border-secondary mx-3">
    <a href="/logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
  </nav>
  <main class="content flex-grow-1">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
<?php else: ?>
<div class="container py-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
      <?= e($flash['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>
