<?php
// Header include — requires auth.php already loaded
$schoolName = getSetting('school_name', 'JNV School');
$schoolLogo = getSetting('school_logo', '');
$primaryColor = getSetting('primary_color', '#1e40af');
$pageTitle = $pageTitle ?? 'Dashboard';
$flash = getFlash();

// Determine active nav
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
function navActive(string $path): string {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: <?= e($primaryColor) ?>;
            --sidebar-width: 260px;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar .brand {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .sidebar .brand img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; }
        .sidebar .brand h5 { color: #fff; margin: 0; font-size: 1rem; font-weight: 600; }
        .sidebar .brand small { color: #94a3b8; font-size: 0.7rem; }
        .sidebar .nav-section { padding: 0.5rem 0; }
        .sidebar .nav-section-title { color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; padding: 0.75rem 1.25rem 0.25rem; font-weight: 600; }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 0.55rem 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.75rem;
            border-radius: 0; transition: all 0.2s;
        }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .sidebar .nav-link.active { color: #fff; background: var(--primary); font-weight: 500; }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }

        /* Main content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-bar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030;
        }
        .top-bar .user-info { display: flex; align-items: center; gap: 0.5rem; }
        .content-area { padding: 1.5rem; }

        /* Mobile */
        .sidebar-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: #334155; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1035; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: inline-block; }
        }

        /* Cards */
        .kpi-card { border: none; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }

        /* Table styles */
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
        .badge { font-weight: 500; }
    </style>
</head>
<body>
<?php if (isLoggedIn()): ?>
<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="brand">
        <?php if ($schoolLogo): ?>
            <img src="/uploads/logo/<?= e($schoolLogo) ?>" alt="Logo">
        <?php else: ?>
            <div style="width:40px;height:40px;border-radius:10px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;">
                <?= strtoupper(substr($schoolName, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h5><?= e($schoolName) ?></h5>
            <small>Management System</small>
        </div>
    </div>

    <?php if (isAdmin()): ?>
    <div class="nav-section">
        <div class="nav-section-title">Administration</div>
        <a href="/admin/dashboard.php" class="nav-link <?= navActive('/admin/dashboard') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/admin/students.php" class="nav-link <?= navActive('/admin/students') ?><?= navActive('/admin/student-form') ?>"><i class="bi bi-mortarboard-fill"></i> Students</a>
        <a href="/admin/teachers.php" class="nav-link <?= navActive('/admin/teachers') ?><?= navActive('/admin/teacher-form') ?>"><i class="bi bi-person-badge-fill"></i> Teachers</a>
        <?php if (isSuperAdmin() || getSetting('feature_admissions', '1') === '1'): ?>
        <a href="/admin/admissions.php" class="nav-link <?= navActive('/admin/admissions') ?>"><i class="bi bi-file-earmark-plus-fill"></i> Admissions</a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_notifications', '1') === '1'): ?>
        <a href="/admin/notifications.php" class="nav-link <?= navActive('/admin/notifications') ?>"><i class="bi bi-bell-fill"></i> Notifications</a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_gallery', '1') === '1'): ?>
        <a href="/admin/gallery.php" class="nav-link <?= navActive('/admin/gallery') ?>"><i class="bi bi-images"></i> Gallery</a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_events', '1') === '1'): ?>
        <a href="/admin/events.php" class="nav-link <?= navActive('/admin/events') ?>"><i class="bi bi-calendar-event-fill"></i> Events</a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_slider', '1') === '1'): ?>
        <a href="/admin/slider.php" class="nav-link <?= navActive('/admin/slider') ?>"><i class="bi bi-collection-play-fill"></i> Home Slider</a>
        <?php endif; ?>
    </div>
    <div class="nav-section">
        <div class="nav-section-title">Reports & Logs</div>
        <?php if (isSuperAdmin() || getSetting('feature_reports', '1') === '1'): ?>
        <a href="/admin/reports.php" class="nav-link <?= navActive('/admin/reports') ?>"><i class="bi bi-file-earmark-bar-graph-fill"></i> Reports</a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_audit_logs', '1') === '1'): ?>
        <a href="/admin/audit-logs.php" class="nav-link <?= navActive('/admin/audit-logs') ?>"><i class="bi bi-clock-history"></i> Audit Logs</a>
        <?php endif; ?>
    </div>
    <div class="nav-section">
        <div class="nav-section-title">Configuration</div>
        <a href="/admin/settings.php" class="nav-link <?= navActive('/admin/settings') ?>"><i class="bi bi-gear-fill"></i> Settings</a>
        <a href="/admin/page-content-manager.php" class="nav-link <?= navActive('/admin/page-content-manager') ?>"><i class="bi bi-file-earmark-text"></i> Page Content</a>
        <a href="/admin/footer-manager.php" class="nav-link <?= navActive('/admin/footer-manager') ?>"><i class="bi bi-diagram-3"></i> Footer Manager</a>
        <a href="/admin/quote-highlight.php" class="nav-link <?= navActive('/admin/quote-highlight') ?>"><i class="bi bi-quote"></i> Quote Highlight</a>
        <a href="/admin/support.php" class="nav-link <?= navActive('/admin/support') ?>"><i class="bi bi-headset"></i> Support</a>
    </div>
    <?php else: ?>
    <div class="nav-section">
        <div class="nav-section-title">Teacher Panel</div>
        <a href="/teacher/dashboard.php" class="nav-link <?= navActive('/teacher/dashboard') ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="/teacher/attendance.php" class="nav-link <?= navActive('/teacher/attendance') ?>"><i class="bi bi-check2-square"></i> Attendance</a>
        <a href="/teacher/exams.php" class="nav-link <?= navActive('/teacher/exams') ?>"><i class="bi bi-journal-text"></i> Exam Results</a>
        <a href="/teacher/post-notification.php" class="nav-link <?= navActive('/teacher/post-notification') ?>"><i class="bi bi-megaphone-fill"></i> Post Notification</a>
        <a href="/teacher/upload-gallery.php" class="nav-link <?= navActive('/teacher/upload-gallery') ?>"><i class="bi bi-camera-fill"></i> Upload Gallery</a>
    </div>
    <?php endif; ?>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <h5 class="mb-0 fw-semibold" style="font-size: 1.1rem;"><?= e($pageTitle) ?></h5>
        </div>
        <div class="user-info">
            <span class="text-muted d-none d-md-inline" style="font-size:0.8rem;">
                <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
                <i class="bi bi-clock ms-2 me-1"></i><span id="headerClock"><?= date('h:i A') ?></span>
            </span>
            <span class="badge bg-primary-subtle text-primary rounded-pill"><?= e(ucfirst(str_replace('_', ' ', currentRole()))) ?></span>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> <?= e(currentUser()['name'] ?? 'User') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/admin/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <main class="content-area">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
<?php else: ?>
<div>
<?php endif; ?>

<script>
function toggleSidebar() {
    document.getElementById('sidebar')?.classList.toggle('show');
    document.getElementById('sidebarOverlay')?.classList.toggle('show');
}
</script>
