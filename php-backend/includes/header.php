<?php
// Header include — requires auth.php already loaded
$schoolName = getSetting('school_name', 'JNV School');
$schoolLogo = getSetting('school_logo', '');
$_logoVer = getSetting('logo_updated_at', '0');
$primaryColor = getSetting('primary_color', '#1e40af');
$pageTitle = $pageTitle ?? 'Dashboard';
$flash = getFlash();

// Determine active nav
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
function navActive(string $path): string {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'active' : '';
}

// User info for profile dropdown
$_currentUser = currentUser();
$_userName = $_currentUser['name'] ?? 'User';
$_userRole = currentRole() ?? 'user';
$_userInitials = '';
$_nameParts = explode(' ', $_userName);
$_userInitials .= strtoupper(substr($_nameParts[0] ?? '', 0, 1));
if (count($_nameParts) > 1) $_userInitials .= strtoupper(substr(end($_nameParts), 0, 1));
if (!$_userInitials) $_userInitials = 'U';

$_roleBadgeMap = [
    'super_admin' => ['Super Admin', 'bg-danger'],
    'admin' => ['Admin', 'bg-primary'],
    'office' => ['Office', 'bg-info'],
    'teacher' => ['Teacher', 'bg-success'],
];
$_roleLabel = $_roleBadgeMap[$_userRole][0] ?? ucfirst($_userRole);
$_roleBadgeClass = $_roleBadgeMap[$_userRole][1] ?? 'bg-secondary';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Theme init (prevent flash) -->
    <script>
    (function(){
        var t=localStorage.getItem('admin_theme')||'light';
        document.documentElement.setAttribute('data-theme',t);
        var c=localStorage.getItem('sidebar_collapsed')==='true';
        if(c) document.documentElement.classList.add('sidebar-is-collapsed');
    })();
    </script>
    <style>
        :root {
            --primary: <?= e($primaryColor) ?>;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            /* Light theme (default) */
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --bg-topbar: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        }
        html[data-theme="dark"] {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-topbar: #1e293b;
            --text-primary: #e2e8f0;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: rgba(255,255,255,0.08);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.4);
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--bg-body); min-height: 100vh; color: var(--text-primary); transition: background 0.3s ease, color 0.3s ease; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: width 0.3s ease, transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar .brand {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 0.75rem;
            position: relative;
            min-height: 85px;
        }
        .sidebar .brand img { width: 64px; height: 64px; border-radius: 8px; object-fit: contain; background: #fff; padding: 4px; border: 2px solid rgba(255,255,255,0.2); transition: all 0.3s ease; flex-shrink: 0; }
        .sidebar .brand-text { overflow: hidden; transition: opacity 0.2s ease, width 0.2s ease; white-space: nowrap; }
        .sidebar .brand h5 { color: #fff; margin: 0; font-size: 1rem; font-weight: 600; }
        .sidebar .brand small { color: #94a3b8; font-size: 0.7rem; }

        /* Collapse toggle */
        .collapse-toggle {
            position: absolute; right: -14px; top: 50%; transform: translateY(-50%);
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--primary); border: 2px solid #1e293b;
            color: #fff; display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 0.75rem; z-index: 1050;
            transition: transform 0.3s ease, background 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .collapse-toggle:hover { background: #2563eb; }

        .sidebar .nav-section { padding: 0.5rem 0; }
        .sidebar .nav-section-title { color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; padding: 0.75rem 1.25rem 0.25rem; font-weight: 600; white-space: nowrap; overflow: hidden; transition: opacity 0.2s ease; }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 0.55rem 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.75rem;
            border-radius: 0; transition: all 0.2s; white-space: nowrap; overflow: hidden; position: relative;
        }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .sidebar .nav-link.active { color: #fff; background: var(--primary); font-weight: 500; }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar .nav-link span { transition: opacity 0.2s ease; }

        /* Collapsed sidebar */
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar.collapsed .brand { justify-content: center; padding: 1rem 0.5rem; }
        .sidebar.collapsed .brand img { width: 40px; height: 40px; }
        .sidebar.collapsed .brand-text { opacity: 0; width: 0; overflow: hidden; }
        .sidebar.collapsed .nav-section-title { opacity: 0; height: 0; padding: 0; margin: 0; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 0.7rem 0; }
        .sidebar.collapsed .nav-link span { opacity: 0; width: 0; position: absolute; }
        .sidebar.collapsed .nav-link i { margin: 0; font-size: 1.2rem; }
        .sidebar.collapsed .collapse-toggle { right: -14px; }
        .sidebar.collapsed .collapse-toggle i { transform: rotate(180deg); }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .nav-link[data-bs-toggle="tooltip"] { overflow: visible; }

        /* Main content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; transition: margin-left 0.3s ease; }
        .sidebar.collapsed ~ .main-content { margin-left: var(--sidebar-collapsed-width); }
        html.sidebar-is-collapsed .main-content { margin-left: var(--sidebar-collapsed-width); }

        .top-bar {
            background: var(--bg-topbar); border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030;
            transition: background 0.3s ease, border-color 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        .top-bar .page-title { margin: 0; font-weight: 600; font-size: 1.1rem; color: var(--text-primary); }
        .top-bar .user-info { display: flex; align-items: center; gap: 0.75rem; }
        .content-area { padding: 1.5rem; }

        /* Profile Avatar */
        .profile-avatar-btn {
            background: none; border: none; cursor: pointer; position: relative;
            display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem; border-radius: 50px;
            transition: background 0.2s ease;
        }
        .profile-avatar-btn:hover { background: rgba(0,0,0,0.05); }
        html[data-theme="dark"] .profile-avatar-btn:hover { background: rgba(255,255,255,0.08); }
        .avatar-circle {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(99,102,241,0.3);
        }
        .online-dot {
            position: absolute; bottom: 2px; right: 2px;
            width: 10px; height: 10px; border-radius: 50%;
            background: #22c55e; border: 2px solid var(--bg-topbar);
        }

        /* Profile Dropdown */
        .profile-dropdown {
            width: 280px; border: 1px solid var(--border-color);
            background: var(--bg-card); border-radius: 12px;
            box-shadow: var(--shadow-md); padding: 0; overflow: hidden;
            animation: dropdownFadeIn 0.2s ease;
        }
        @keyframes dropdownFadeIn { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
        .profile-dropdown .dropdown-header-custom {
            padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;
            border-bottom: 1px solid var(--border-color); background: var(--bg-card);
        }
        .profile-dropdown .dropdown-header-custom .avatar-lg {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem; flex-shrink: 0;
        }
        .profile-dropdown .dropdown-header-custom .user-meta h6 { margin: 0; font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
        .profile-dropdown .dropdown-header-custom .user-meta small { color: var(--text-muted); font-size: 0.75rem; }
        .profile-dropdown .dropdown-body { padding: 0.5rem 0; }
        .profile-dropdown .dropdown-item {
            padding: 0.6rem 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem;
            color: var(--text-secondary); transition: background 0.15s ease;
        }
        .profile-dropdown .dropdown-item:hover { background: rgba(0,0,0,0.04); color: var(--text-primary); }
        html[data-theme="dark"] .profile-dropdown .dropdown-item:hover { background: rgba(255,255,255,0.06); }
        .profile-dropdown .dropdown-item i { font-size: 1rem; width: 20px; text-align: center; color: var(--text-muted); }
        .profile-dropdown .dropdown-divider { border-color: var(--border-color); margin: 0.25rem 0; }
        .profile-dropdown .dropdown-item.text-danger { color: #ef4444 !important; }
        .profile-dropdown .dropdown-item.text-danger i { color: #ef4444 !important; }

        /* Theme toggle in dropdown */
        .theme-switch-item { cursor: pointer; }
        .theme-switch-track {
            width: 40px; height: 22px; border-radius: 11px; position: relative;
            background: #cbd5e1; transition: background 0.3s ease; flex-shrink: 0; margin-left: auto;
        }
        html[data-theme="dark"] .theme-switch-track { background: var(--primary); }
        .theme-switch-track::after {
            content: ''; position: absolute; top: 2px; left: 2px;
            width: 18px; height: 18px; border-radius: 50%; background: #fff;
            transition: transform 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        html[data-theme="dark"] .theme-switch-track::after { transform: translateX(18px); }

        /* Theme toggle button in header */
        .theme-toggle-btn {
            background: none; border: 1px solid var(--border-color); cursor: pointer;
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); transition: all 0.2s ease; font-size: 1.1rem;
        }
        .theme-toggle-btn:hover { background: rgba(0,0,0,0.05); color: var(--text-primary); }
        html[data-theme="dark"] .theme-toggle-btn:hover { background: rgba(255,255,255,0.08); }
        .theme-toggle-btn .bi-sun-fill { display: none; }
        .theme-toggle-btn .bi-moon-fill { display: inline; }
        html[data-theme="dark"] .theme-toggle-btn .bi-sun-fill { display: inline; }
        html[data-theme="dark"] .theme-toggle-btn .bi-moon-fill { display: none; }

        /* Mobile */
        .sidebar-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: var(--text-primary); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1035; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
            .sidebar.collapsed { width: var(--sidebar-width) !important; }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content, .sidebar.collapsed ~ .main-content, html.sidebar-is-collapsed .main-content { margin-left: 0 !important; }
            .sidebar-toggle { display: inline-block; }
            .collapse-toggle { display: none !important; }
            /* Restore labels on mobile even if collapsed */
            .sidebar.collapsed .brand-text { opacity: 1; width: auto; }
            .sidebar.collapsed .nav-section-title { opacity: 1; height: auto; padding: 0.75rem 1.25rem 0.25rem; }
            .sidebar.collapsed .nav-link { justify-content: flex-start; padding: 0.55rem 1.25rem; }
            .sidebar.collapsed .nav-link span { opacity: 1; width: auto; position: static; }
            .sidebar.collapsed .brand { justify-content: flex-start; padding: 1.25rem; }
            .sidebar.collapsed .brand img { width: 64px; height: 64px; }
        }

        /* Cards — themed */
        .kpi-card { border: none; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; background: var(--bg-card); box-shadow: var(--shadow-sm); }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }

        /* Table — themed */
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; }
        .badge { font-weight: 500; }

        /* Themed elements */
        html[data-theme="dark"] .card, html[data-theme="dark"] .kpi-card { background: var(--bg-card); border-color: var(--border-color); }
        html[data-theme="dark"] .table { color: var(--text-primary); --bs-table-bg: transparent; }
        html[data-theme="dark"] .table th, html[data-theme="dark"] .table td { border-color: var(--border-color); }
        html[data-theme="dark"] .form-control, html[data-theme="dark"] .form-select {
            background: #0f172a; border-color: var(--border-color); color: var(--text-primary);
        }
        html[data-theme="dark"] .form-control:focus, html[data-theme="dark"] .form-select:focus {
            background: #0f172a; border-color: var(--primary); color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.25);
        }
        html[data-theme="dark"] .modal-content { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        html[data-theme="dark"] .alert { border-color: var(--border-color); }
        html[data-theme="dark"] .list-group-item { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        html[data-theme="dark"] .btn-light { background: var(--bg-card); color: var(--text-primary); border-color: var(--border-color); }
        html[data-theme="dark"] .text-muted { color: var(--text-muted) !important; }
        html[data-theme="dark"] .bg-white { background: var(--bg-card) !important; }
        html[data-theme="dark"] .bg-light { background: var(--bg-body) !important; }
        html[data-theme="dark"] .border { border-color: var(--border-color) !important; }
        html[data-theme="dark"] .dropdown-menu { background: var(--bg-card); border-color: var(--border-color); }
        html[data-theme="dark"] .dropdown-item { color: var(--text-secondary); }
        html[data-theme="dark"] .dropdown-item:hover { background: rgba(255,255,255,0.06); color: var(--text-primary); }
        html[data-theme="dark"] h1,html[data-theme="dark"] h2,html[data-theme="dark"] h3,html[data-theme="dark"] h4,html[data-theme="dark"] h5,html[data-theme="dark"] h6 { color: var(--text-primary); }
        html[data-theme="dark"] .nav-tabs .nav-link { color: var(--text-muted); }
        html[data-theme="dark"] .nav-tabs .nav-link.active { color: var(--text-primary); background: var(--bg-card); border-color: var(--border-color); }
        html[data-theme="dark"] .page-link { background: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        html[data-theme="dark"] .breadcrumb-item a { color: var(--text-muted); }
    </style>
</head>
<body>
<?php if (isLoggedIn()): ?>
<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="brand">
        <?php if ($schoolLogo):
            $_sidebarLogoPath = (strpos($schoolLogo, '/uploads/') === 0) ? $schoolLogo : (file_exists(__DIR__.'/../uploads/branding/'.$schoolLogo) ? '/uploads/branding/'.$schoolLogo : '/uploads/logo/'.$schoolLogo);
        ?>
            <img src="<?= e($_sidebarLogoPath) ?>?v=<?= e($_logoVer) ?>" alt="Logo">
        <?php else: ?>
            <div style="width:64px;height:64px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                <?= strtoupper(substr($schoolName, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="brand-text">
            <h5><?= e($schoolName) ?></h5>
            <small>Management System</small>
        </div>
        <button class="collapse-toggle d-none d-lg-flex" onclick="toggleCollapse()" title="Toggle sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <?php if (isAdmin()): ?>
    <div class="nav-section">
        <div class="nav-section-title">Administration</div>
        <a href="/admin/dashboard.php" class="nav-link <?= navActive('/admin/dashboard') ?>" data-bs-title="Dashboard"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
        <a href="/admin/students.php" class="nav-link <?= navActive('/admin/students') ?><?= navActive('/admin/student-form') ?>" data-bs-title="Students"><i class="bi bi-mortarboard-fill"></i> <span>Students</span></a>
        <a href="/admin/teachers.php" class="nav-link <?= navActive('/admin/teachers') ?><?= navActive('/admin/teacher-form') ?>" data-bs-title="Teachers"><i class="bi bi-person-badge-fill"></i> <span>Teachers</span></a>
        <?php if (isSuperAdmin() || getSetting('feature_admissions', '1') === '1'): ?>
        <a href="/admin/admissions.php" class="nav-link <?= navActive('/admin/admissions') ?>" data-bs-title="Admissions"><i class="bi bi-file-earmark-plus-fill"></i> <span>Admissions</span></a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_notifications', '1') === '1'): ?>
        <a href="/admin/notifications.php" class="nav-link <?= navActive('/admin/notifications') ?>" data-bs-title="Notifications"><i class="bi bi-bell-fill"></i> <span>Notifications</span></a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_gallery', '1') === '1'): ?>
        <a href="/admin/gallery.php" class="nav-link <?= navActive('/admin/gallery') ?>" data-bs-title="Gallery"><i class="bi bi-images"></i> <span>Gallery</span></a>
        <a href="/admin/upload-gallery.php" class="nav-link <?= navActive('/admin/upload-gallery') ?>" data-bs-title="Upload Gallery"><i class="bi bi-cloud-arrow-up"></i> <span>Upload Gallery</span></a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_events', '1') === '1'): ?>
        <a href="/admin/events.php" class="nav-link <?= navActive('/admin/events') ?>" data-bs-title="Events"><i class="bi bi-calendar-event-fill"></i> <span>Events</span></a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_slider', '1') === '1'): ?>
        <a href="/admin/slider.php" class="nav-link <?= navActive('/admin/slider') ?>" data-bs-title="Home Slider"><i class="bi bi-collection-play-fill"></i> <span>Home Slider</span></a>
        <?php endif; ?>
    </div>
    <div class="nav-section">
        <div class="nav-section-title">Reports & Logs</div>
        <?php if (isSuperAdmin() || getSetting('feature_reports', '1') === '1'): ?>
        <a href="/admin/reports.php" class="nav-link <?= navActive('/admin/reports') ?>" data-bs-title="Reports"><i class="bi bi-file-earmark-bar-graph-fill"></i> <span>Reports</span></a>
        <?php endif; ?>
        <?php if (isSuperAdmin() || getSetting('feature_audit_logs', '1') === '1'): ?>
        <a href="/admin/audit-logs.php" class="nav-link <?= navActive('/admin/audit-logs') ?>" data-bs-title="Audit Logs"><i class="bi bi-clock-history"></i> <span>Audit Logs</span></a>
        <?php endif; ?>
    </div>
    <div class="nav-section">
        <div class="nav-section-title">Configuration</div>
        <a href="/admin/settings.php" class="nav-link <?= navActive('/admin/settings') ?>" data-bs-title="Settings"><i class="bi bi-gear-fill"></i> <span>Settings</span></a>
        <a href="/admin/page-content-manager.php" class="nav-link <?= navActive('/admin/page-content-manager') ?>" data-bs-title="Page Content"><i class="bi bi-file-earmark-text"></i> <span>Page Content</span></a>
        <a href="/admin/footer-manager.php" class="nav-link <?= navActive('/admin/footer-manager') ?>" data-bs-title="Footer Manager"><i class="bi bi-diagram-3"></i> <span>Footer Manager</span></a>
        <a href="/admin/navigation-settings.php" class="nav-link <?= navActive('/admin/navigation-settings') ?>" data-bs-title="Navigation"><i class="bi bi-menu-button-wide"></i> <span>Navigation</span></a>
        <a href="/admin/quote-highlight.php" class="nav-link <?= navActive('/admin/quote-highlight') ?>" data-bs-title="Quote Highlight"><i class="bi bi-quote"></i> <span>Quote Highlight</span></a>
        <a href="/admin/support.php" class="nav-link <?= navActive('/admin/support') ?>" data-bs-title="Support"><i class="bi bi-headset"></i> <span>Support</span></a>
    </div>
    <?php else: ?>
    <div class="nav-section">
        <div class="nav-section-title">Teacher Panel</div>
        <a href="/teacher/dashboard.php" class="nav-link <?= navActive('/teacher/dashboard') ?>" data-bs-title="Dashboard"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
        <a href="/teacher/attendance.php" class="nav-link <?= navActive('/teacher/attendance') ?>" data-bs-title="Attendance"><i class="bi bi-check2-square"></i> <span>Attendance</span></a>
        <a href="/teacher/exams.php" class="nav-link <?= navActive('/teacher/exams') ?>" data-bs-title="Exam Results"><i class="bi bi-journal-text"></i> <span>Exam Results</span></a>
        <a href="/teacher/post-notification.php" class="nav-link <?= navActive('/teacher/post-notification') ?>" data-bs-title="Post Notification"><i class="bi bi-megaphone-fill"></i> <span>Post Notification</span></a>
        <a href="/teacher/upload-gallery.php" class="nav-link <?= navActive('/teacher/upload-gallery') ?>" data-bs-title="Upload Gallery"><i class="bi bi-camera-fill"></i> <span>Upload Gallery</span></a>
    </div>
    <?php endif; ?>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <h5 class="page-title"><?= e($pageTitle) ?></h5>
        </div>
        <div class="user-info">
            <span class="text-muted d-none d-md-inline" style="font-size:0.8rem;">
                <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
                <i class="bi bi-clock ms-2 me-1"></i><span id="headerClock"><?= date('h:i A') ?></span>
            </span>

            <!-- Theme Toggle -->
            <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle theme">
                <i class="bi bi-moon-fill"></i>
                <i class="bi bi-sun-fill"></i>
            </button>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <button class="profile-avatar-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-circle"><?= e($_userInitials) ?></div>
                    <span class="online-dot"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <div class="dropdown-header-custom">
                        <div class="avatar-lg"><?= e($_userInitials) ?></div>
                        <div class="user-meta">
                            <h6><?= e($_userName) ?></h6>
                            <small><span class="badge <?= e($_roleBadgeClass) ?> rounded-pill" style="font-size:0.7rem;"><?= e($_roleLabel) ?></span></small>
                        </div>
                    </div>
                    <div class="dropdown-body">
                        <a class="dropdown-item" href="<?= isAdmin() ? '/admin/settings.php' : '/teacher/dashboard.php' ?>"><i class="bi bi-person"></i> My Profile</a>
                        <a class="dropdown-item" href="<?= isAdmin() ? '/admin/settings.php#security' : '#' ?>"><i class="bi bi-key"></i> Change Password</a>
                        <?php if (isSuperAdmin()): ?>
                        <a class="dropdown-item" href="/admin/settings.php"><i class="bi bi-gear"></i> System Settings</a>
                        <?php endif; ?>
                        <hr class="dropdown-divider">
                        <div class="dropdown-item theme-switch-item" onclick="toggleTheme()">
                            <i class="bi bi-moon-fill"></i> <span>Dark Mode</span>
                            <div class="theme-switch-track"></div>
                        </div>
                        <hr class="dropdown-divider">
                        <a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                </div>
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
