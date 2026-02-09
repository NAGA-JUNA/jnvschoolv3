<?php
// ============================================
// JSchoolAdmin — Main API Router (Entry Point)
// Version: 1.1.0
// ============================================

// Load config & CORS
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/helpers/response.php';

// Get request method and route
$method = $_SERVER['REQUEST_METHOD'];
$route  = trim($_GET['route'] ?? '', '/');

// Remove query string from route if present
$route = explode('?', $route)[0];

// Strip leading 'api/' if present
if (str_starts_with($route, 'api/')) {
    $route = substr($route, 4);
}

// Helper: match route pattern with params
function routeMatch(string $pattern, string $route, array &$params = []): bool {
    $regex = preg_replace('/\{(\w+)\}/', '(\d+)', $pattern);
    $regex = '#^' . $regex . '$#';
    if (preg_match($regex, $route, $matches)) {
        array_shift($matches);
        $params = $matches;
        return true;
    }
    return false;
}

// ============================================
// ROUTE DISPATCHER
// ============================================

try {
    $params = [];

    // ─── AUTH ROUTES ─────────────────────────────
    if ($route === 'auth/login' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->login();
    }
    elseif ($route === 'auth/logout' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
    }
    elseif ($route === 'auth/me' && $method === 'GET') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->me();
    }

    // ─── PUBLIC ROUTES ──────────────────────────
    elseif ($route === 'public/notifications' && $method === 'GET') {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->publicList();
    }
    elseif ($route === 'public/gallery/categories' && $method === 'GET') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->publicCategories();
    }
    elseif ($route === 'public/gallery/items' && $method === 'GET') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->publicItems();
    }
    elseif ($route === 'public/events' && $method === 'GET') {
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->publicList();
    }
    elseif ($route === 'public/admissions' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AdmissionController.php';
        (new AdmissionController())->publicSubmit();
    }

    // ─── HOME SLIDER ROUTES ─────────────────────
    elseif ($route === 'home/slider/reorder' && $method === 'PATCH') {
        require_once __DIR__ . '/controllers/SliderController.php';
        (new SliderController())->reorder();
    }
    elseif ($route === 'home/slider' && $method === 'GET') {
        require_once __DIR__ . '/controllers/SliderController.php';
        (new SliderController())->index();
    }
    elseif ($route === 'home/slider' && $method === 'POST') {
        require_once __DIR__ . '/controllers/SliderController.php';
        (new SliderController())->store();
    }
    elseif (routeMatch('home/slider/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/SliderController.php';
        $ctrl = new SliderController();
        match ($method) {
            'PUT'    => $ctrl->update((int) $params[0]),
            'DELETE' => $ctrl->destroy((int) $params[0]),
            default  => jsonError('Method not allowed', 405),
        };
    }

    // ─── ADMIN DASHBOARD ────────────────────────
    elseif ($route === 'admin/dashboard/metrics' && $method === 'GET') {
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->adminMetrics();
    }
    elseif ($route === 'admin/dashboard/activity' && $method === 'GET') {
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->adminActivity();
    }
    elseif ($route === 'admin/alerts' && $method === 'GET') {
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->alerts();
    }

    // ─── ADMIN STUDENTS ─────────────────────────
    elseif ($route === 'admin/students/alumni' && $method === 'GET') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->alumni();
    }
    elseif ($route === 'admin/students/export' && $method === 'GET') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->export();
    }
    elseif ($route === 'admin/students/import' && $method === 'POST') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->import();
    }
    elseif ($route === 'admin/students/bulk-promote' && $method === 'POST') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->bulkPromote();
    }
    elseif ($route === 'admin/students' && $method === 'GET') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->index();
    }
    elseif ($route === 'admin/students' && $method === 'POST') {
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->store();
    }
    elseif (routeMatch('admin/students/{id}/attendance', $route, $params)) {
        require_once __DIR__ . '/controllers/AttendanceController.php';
        (new AttendanceController())->studentHistory((int) $params[0]);
    }
    elseif (routeMatch('admin/students/{id}/exams', $route, $params)) {
        require_once __DIR__ . '/controllers/ExamController.php';
        (new ExamController())->studentResults((int) $params[0]);
    }
    elseif (routeMatch('admin/students/{id}/documents', $route, $params)) {
        require_once __DIR__ . '/controllers/DocumentController.php';
        $ctrl = new DocumentController();
        match ($method) {
            'GET'  => $ctrl->studentDocuments((int) $params[0]),
            'POST' => $ctrl->uploadStudentDocument((int) $params[0]),
            default => jsonError('Method not allowed', 405),
        };
    }
    elseif (routeMatch('admin/students/{id}/messages', $route, $params)) {
        require_once __DIR__ . '/controllers/MessageController.php';
        (new MessageController())->studentMessages((int) $params[0]);
    }
    elseif (routeMatch('admin/students/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/StudentController.php';
        $ctrl = new StudentController();
        match ($method) {
            'GET'    => $ctrl->show((int) $params[0]),
            'PUT'    => $ctrl->update((int) $params[0]),
            'DELETE' => $ctrl->destroy((int) $params[0]),
            default  => jsonError('Method not allowed', 405),
        };
    }

    // ─── ADMIN TEACHERS ─────────────────────────
    elseif ($route === 'admin/teachers/inactive' && $method === 'GET') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->inactive();
    }
    elseif ($route === 'admin/teachers/export' && $method === 'GET') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->export();
    }
    elseif ($route === 'admin/teachers/import' && $method === 'POST') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->import();
    }
    elseif ($route === 'admin/teachers' && $method === 'GET') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->index();
    }
    elseif ($route === 'admin/teachers' && $method === 'POST') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->store();
    }
    elseif (routeMatch('admin/teachers/{id}/assign-classes', $route, $params)) {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->assignClasses((int) $params[0]);
    }
    elseif (routeMatch('admin/teachers/{id}/attendance', $route, $params)) {
        require_once __DIR__ . '/controllers/AttendanceController.php';
        (new AttendanceController())->teacherHistory((int) $params[0]);
    }
    elseif (routeMatch('admin/teachers/{id}/documents', $route, $params)) {
        require_once __DIR__ . '/controllers/DocumentController.php';
        $ctrl = new DocumentController();
        match ($method) {
            'GET'  => $ctrl->teacherDocuments((int) $params[0]),
            'POST' => $ctrl->uploadTeacherDocument((int) $params[0]),
            default => jsonError('Method not allowed', 405),
        };
    }
    elseif (routeMatch('admin/teachers/{id}/messages', $route, $params)) {
        require_once __DIR__ . '/controllers/MessageController.php';
        (new MessageController())->teacherMessages((int) $params[0]);
    }
    elseif (routeMatch('admin/teachers/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/TeacherController.php';
        $ctrl = new TeacherController();
        match ($method) {
            'GET'    => $ctrl->show((int) $params[0]),
            'PUT'    => $ctrl->update((int) $params[0]),
            'DELETE' => $ctrl->destroy((int) $params[0]),
            default  => jsonError('Method not allowed', 405),
        };
    }

    // ─── ADMIN ADMISSIONS ───────────────────────
    elseif ($route === 'admin/admissions/export' && $method === 'GET') {
        require_once __DIR__ . '/controllers/AdmissionController.php';
        (new AdmissionController())->export();
    }
    elseif ($route === 'admin/admissions' && $method === 'GET') {
        require_once __DIR__ . '/controllers/AdmissionController.php';
        (new AdmissionController())->index();
    }
    elseif (routeMatch('admin/admissions/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/AdmissionController.php';
        (new AdmissionController())->updateStatus((int) $params[0]);
    }

    // ─── ADMIN NOTIFICATIONS ────────────────────
    elseif ($route === 'admin/notifications/bulk-approve' && $method === 'POST') {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->bulkApprove();
    }
    elseif ($route === 'admin/notifications' && $method === 'GET') {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->index();
    }
    elseif (routeMatch('admin/notifications/{id}/approve', $route, $params)) {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->approve((int) $params[0]);
    }
    elseif (routeMatch('admin/notifications/{id}/reject', $route, $params)) {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->reject((int) $params[0]);
    }
    elseif (routeMatch('admin/notifications/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->show((int) $params[0]);
    }

    // ─── ADMIN GALLERY ──────────────────────────
    elseif ($route === 'admin/gallery/approvals' && $method === 'GET') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->approvals();
    }
    elseif ($route === 'admin/gallery/categories' && $method === 'GET') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->adminCategories();
    }
    elseif ($route === 'admin/gallery/categories' && $method === 'POST') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->createCategory();
    }
    elseif (routeMatch('admin/gallery/categories/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/GalleryController.php';
        $ctrl = new GalleryController();
        match ($method) {
            'PUT'    => $ctrl->updateCategory((int) $params[0]),
            'DELETE' => $ctrl->deleteCategory((int) $params[0]),
            default  => jsonError('Method not allowed', 405),
        };
    }
    elseif (routeMatch('admin/gallery/items/{id}/approve', $route, $params)) {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->approveItem((int) $params[0]);
    }
    elseif (routeMatch('admin/gallery/items/{id}/reject', $route, $params)) {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->rejectItem((int) $params[0]);
    }

    // ─── ADMIN EVENTS ───────────────────────────
    elseif ($route === 'admin/events' && $method === 'GET') {
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->index();
    }
    elseif ($route === 'admin/events' && $method === 'POST') {
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->store();
    }
    elseif (routeMatch('admin/events/{id}', $route, $params)) {
        require_once __DIR__ . '/controllers/EventController.php';
        $ctrl = new EventController();
        match ($method) {
            'PUT'    => $ctrl->update((int) $params[0]),
            'DELETE' => $ctrl->destroy((int) $params[0]),
            default  => jsonError('Method not allowed', 405),
        };
    }

    // ─── ADMIN WHATSAPP ─────────────────────────
    elseif ($route === 'admin/whatsapp/log' && $method === 'POST') {
        require_once __DIR__ . '/controllers/WhatsAppController.php';
        (new WhatsAppController())->logShare();
    }
    elseif ($route === 'admin/whatsapp/logs' && $method === 'GET') {
        require_once __DIR__ . '/controllers/WhatsAppController.php';
        (new WhatsAppController())->logs();
    }

    // ─── ADMIN EMAIL ────────────────────────────
    elseif ($route === 'admin/emails' && $method === 'GET') {
        require_once __DIR__ . '/controllers/EmailController.php';
        (new EmailController())->index();
    }
    elseif ($route === 'admin/emails/create' && $method === 'POST') {
        require_once __DIR__ . '/controllers/EmailController.php';
        (new EmailController())->create();
    }

    // ─── ADMIN REPORTS ──────────────────────────
    elseif ($route === 'admin/reports' && $method === 'GET') {
        require_once __DIR__ . '/controllers/ReportController.php';
        (new ReportController())->index();
    }

    // ─── ADMIN AUDIT LOGS ───────────────────────
    elseif ($route === 'admin/audit-logs' && $method === 'GET') {
        require_once __DIR__ . '/controllers/AuditLogController.php';
        (new AuditLogController())->index();
    }

    // ─── ADMIN SETTINGS & BRANDING ──────────────
    elseif ($route === 'admin/settings' && $method === 'GET') {
        require_once __DIR__ . '/controllers/BrandingController.php';
        (new BrandingController())->getSettings();
    }
    elseif ($route === 'admin/settings' && $method === 'PUT') {
        require_once __DIR__ . '/controllers/BrandingController.php';
        (new BrandingController())->updateSettings();
    }
    elseif ($route === 'admin/branding' && ($method === 'GET' || $method === 'PUT')) {
        require_once __DIR__ . '/controllers/BrandingController.php';
        $ctrl = new BrandingController();
        $method === 'GET' ? $ctrl->getBranding() : $ctrl->updateBranding();
    }

    // ─── TEACHER ROUTES ─────────────────────────
    elseif ($route === 'teacher/dashboard/metrics' && $method === 'GET') {
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->teacherMetrics();
    }
    elseif ($route === 'teacher/dashboard/activity' && $method === 'GET') {
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->teacherActivity();
    }
    elseif ($route === 'teacher/notifications' && $method === 'POST') {
        require_once __DIR__ . '/controllers/NotificationController.php';
        (new NotificationController())->teacherSubmit();
    }
    elseif ($route === 'teacher/gallery/upload' && $method === 'POST') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->teacherUpload();
    }
    elseif ($route === 'teacher/gallery/youtube' && $method === 'POST') {
        require_once __DIR__ . '/controllers/GalleryController.php';
        (new GalleryController())->addYoutubeLink();
    }
    elseif ($route === 'teacher/submissions' && $method === 'GET') {
        // Teacher's own submissions (notifications + gallery)
        require_once __DIR__ . '/middleware/auth.php';
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);
        $db = getDB();

        $notifications = $db->prepare(
            "SELECT id, title, 'notification' as type, status, created_at FROM notifications WHERE submitted_by = :uid ORDER BY created_at DESC"
        );
        $notifications->execute([':uid' => $user['user_id']]);

        $gallery = $db->prepare(
            "SELECT gi.id, gi.title, 'gallery' as type, gi.status, gi.created_at FROM gallery_items gi WHERE gi.uploaded_by = :uid ORDER BY gi.created_at DESC"
        );
        $gallery->execute([':uid' => $user['user_id']]);

        jsonSuccess([
            'notifications' => $notifications->fetchAll(),
            'gallery'       => $gallery->fetchAll(),
        ]);
    }
    elseif ($route === 'teacher/profile' && $method === 'GET') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->myProfile();
    }
    elseif ($route === 'teacher/profile' && $method === 'PUT') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->updateMyProfile();
    }
    elseif ($route === 'teacher/students' && $method === 'GET') {
        require_once __DIR__ . '/controllers/TeacherController.php';
        (new TeacherController())->myStudents();
    }
    elseif ($route === 'teacher/attendance/mark' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AttendanceController.php';
        (new AttendanceController())->mark();
    }
    elseif ($route === 'teacher/exams/marks' && $method === 'POST') {
        require_once __DIR__ . '/controllers/ExamController.php';
        (new ExamController())->enterMarks();
    }

    // ─── 404 ────────────────────────────────────
    else {
        jsonError("Route not found: $method $route", 404);
    }

} catch (PDOException $e) {
    jsonError('Database error', 500);
} catch (Throwable $e) {
    jsonError('Internal server error', 500);
}
