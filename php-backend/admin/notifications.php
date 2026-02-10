<?php
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

// --- POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $nid = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $stmt = $db->prepare("INSERT INTO notifications (title, content, type, priority, target_audience, target_class, target_section, is_public, schedule_at, expires_at, show_popup, show_banner, show_marquee, show_dashboard, status, posted_by, approved_by, approved_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,'approved',?,?,NOW())");
        $stmt->execute([
            trim($_POST['title']), trim($_POST['content']), $_POST['type'] ?? 'general',
            $_POST['priority'] ?? 'normal', $_POST['target_audience'] ?? 'all',
            $_POST['target_class'] ?? null, $_POST['target_section'] ?? null,
            isset($_POST['is_public']) ? 1 : 0,
            $_POST['schedule_at'] ?: null, $_POST['expires_at'] ?: null,
            isset($_POST['show_popup']) ? 1 : 0, isset($_POST['show_banner']) ? 1 : 0,
            isset($_POST['show_marquee']) ? 1 : 0, isset($_POST['show_dashboard']) ? 1 : 0,
            currentUserId(), currentUserId()
        ]);
        auditLog('create_notification', 'notification', (int)$db->lastInsertId());
        setFlash('success', 'Notification created and published.');
    } elseif ($nid) {
        switch ($action) {
            case 'approve':
                $db->prepare("UPDATE notifications SET status='approved', approved_by=?, approved_at=NOW(), is_public=1 WHERE id=?")->execute([currentUserId(), $nid]);
                auditLog('approve_notification', 'notification', $nid);
                setFlash('success', 'Notification approved.');
                break;
            case 'reject':
                $reason = trim($_POST['reject_reason'] ?? '');
                $db->prepare("UPDATE notifications SET status='rejected', reject_reason=?, approved_by=?, approved_at=NOW(), is_public=0 WHERE id=?")->execute([$reason, currentUserId(), $nid]);
                auditLog('reject_notification', 'notification', $nid);
                setFlash('success', 'Notification rejected.');
                break;
            case 'delete':
                $db->prepare("UPDATE notifications SET is_deleted=1, deleted_at=NOW(), deleted_by=? WHERE id=?")->execute([currentUserId(), $nid]);
                auditLog('delete_notification', 'notification', $nid);
                setFlash('success', 'Notification deleted.');
                break;
            case 'pin':
                $db->prepare("UPDATE notifications SET is_pinned=1 WHERE id=?")->execute([$nid]);
                auditLog('pin_notification', 'notification', $nid);
                setFlash('success', 'Notification pinned.');
                break;
            case 'unpin':
                $db->prepare("UPDATE notifications SET is_pinned=0 WHERE id=?")->execute([$nid]);
                auditLog('unpin_notification', 'notification', $nid);
                setFlash('success', 'Notification unpinned.');
                break;
            case 'edit':
                $db->prepare("UPDATE notifications SET title=?, content=?, type=?, priority=?, target_audience=?, target_class=?, target_section=?, is_public=?, schedule_at=?, expires_at=?, show_popup=?, show_banner=?, show_marquee=?, show_dashboard=? WHERE id=?")->execute([
                    trim($_POST['title']), trim($_POST['content']), $_POST['type'] ?? 'general',
                    $_POST['priority'] ?? 'normal', $_POST['target_audience'] ?? 'all',
                    $_POST['target_class'] ?? null, $_POST['target_section'] ?? null,
                    isset($_POST['is_public']) ? 1 : 0,
                    $_POST['schedule_at'] ?: null, $_POST['expires_at'] ?: null,
                    isset($_POST['show_popup']) ? 1 : 0, isset($_POST['show_banner']) ? 1 : 0,
                    isset($_POST['show_marquee']) ? 1 : 0, isset($_POST['show_dashboard']) ? 1 : 0,
                    $nid
                ]);
                auditLog('edit_notification', 'notification', $nid);
                setFlash('success', 'Notification updated.');
                break;
            case 'bulk_approve':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $db->prepare("UPDATE notifications SET status='approved', approved_by=?, approved_at=NOW(), is_public=1 WHERE id IN ($placeholders)")->execute(array_merge([currentUserId()], $ids));
                    auditLog('bulk_approve_notifications', 'notification', 0, implode(',', $ids));
                    setFlash('success', count($ids) . ' notifications approved.');
                }
                break;
            case 'bulk_reject':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $db->prepare("UPDATE notifications SET status='rejected', approved_by=?, approved_at=NOW(), is_public=0 WHERE id IN ($placeholders)")->execute(array_merge([currentUserId()], $ids));
                    auditLog('bulk_reject_notifications', 'notification', 0, implode(',', $ids));
                    setFlash('success', count($ids) . ' notifications rejected.');
                }
                break;
            case 'bulk_delete':
                $ids = array_map('intval', $_POST['ids'] ?? []);
                if ($ids) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $db->prepare("UPDATE notifications SET is_deleted=1, deleted_at=NOW(), deleted_by=? WHERE id IN ($placeholders)")->execute(array_merge([currentUserId()], $ids));
                    auditLog('bulk_delete_notifications', 'notification', 0, implode(',', $ids));
                    setFlash('success', count($ids) . ' notifications deleted.');
                }
                break;
        }
    }
    header('Location: /admin/notifications.php?status=' . ($_GET['status'] ?? 'pending'));
    exit;
}

// --- Export CSV ---
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $statusFilter = $_GET['status'] ?? '';
    $where = "WHERE n.is_deleted=0";
    $params = [];
    if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
        $where .= " AND n.status=?";
        $params[] = $statusFilter;
    }
    $rows = $db->prepare("SELECT n.id, n.title, n.type, n.priority, n.target_audience, n.status, n.view_count, n.created_at, u.name as posted_by, a.name as approved_by_name, n.approved_at FROM notifications n LEFT JOIN users u ON n.posted_by=u.id LEFT JOIN users a ON n.approved_by=a.id $where ORDER BY n.created_at DESC");
    $rows->execute($params);
    $rows = $rows->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="notifications_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Title', 'Type', 'Priority', 'Target', 'Status', 'Views', 'Posted By', 'Created', 'Approved By', 'Approved At']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $r['title'], $r['type'], $r['priority'], $r['target_audience'], $r['status'], $r['view_count'], $r['posted_by'], $r['created_at'], $r['approved_by_name'], $r['approved_at']]);
    }
    fclose($out);
    exit;
}

// --- Fetch Data ---
$statusFilter = $_GET['status'] ?? 'pending';
$search = trim($_GET['q'] ?? '');
$where = "WHERE n.is_deleted=0";
$params = [];

if ($statusFilter === 'pinned') {
    $where .= " AND n.is_pinned=1";
} elseif ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
    $where .= " AND n.status=?";
    $params[] = $statusFilter;
}

if ($search) {
    $where .= " AND n.title LIKE ?";
    $params[] = "%$search%";
}

// Counts for tabs
$counts = [];
foreach (['pending', 'approved', 'rejected'] as $s) {
    $c = $db->prepare("SELECT COUNT(*) FROM notifications WHERE is_deleted=0 AND status=?");
    $c->execute([$s]);
    $counts[$s] = $c->fetchColumn();
}
$pinnedCount = $db->query("SELECT COUNT(*) FROM notifications WHERE is_deleted=0 AND is_pinned=1")->fetchColumn();
$allCount = $db->query("SELECT COUNT(*) FROM notifications WHERE is_deleted=0")->fetchColumn();

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$countStmt = $db->prepare("SELECT COUNT(*) FROM notifications n $where");
$countStmt->execute($params);
$totalFiltered = $countStmt->fetchColumn();
$p = paginate($totalFiltered, 20, $page);

$stmt = $db->prepare("SELECT n.*, u.name as poster_name, u.role as poster_role, a.name as approver_name FROM notifications n LEFT JOIN users u ON n.posted_by=u.id LEFT JOIN users a ON n.approved_by=a.id $where ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT ? OFFSET ?");
$stmt->execute(array_merge($params, [$p['per_page'], $p['offset']]));
$notifications = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';

$priorityColors = ['normal' => 'secondary', 'important' => 'warning', 'urgent' => 'danger'];
$statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
$tabs = [
    'pending' => ['label' => 'Pending', 'color' => 'warning', 'count' => $counts['pending']],
    'approved' => ['label' => 'Approved', 'color' => 'success', 'count' => $counts['approved']],
    'rejected' => ['label' => 'Rejected', 'color' => 'danger', 'count' => $counts['rejected']],
    'pinned' => ['label' => 'Pinned', 'color' => 'primary', 'count' => $pinnedCount],
    '' => ['label' => 'All', 'color' => 'secondary', 'count' => $allCount],
];
?>

<!-- Header Actions -->
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-lg me-1"></i>Create New</button>
        <a href="/admin/notifications.php?action=export&status=<?= e($statusFilter) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>
    <form class="d-flex gap-2" method="GET">
        <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search title..." value="<?= e($search) ?>" style="width:200px;">
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-3 flex-wrap gap-1">
    <?php foreach ($tabs as $key => $tab): ?>
        <li class="nav-item">
            <a href="/admin/notifications.php?status=<?= $key ?>" class="nav-link <?= $statusFilter === $key ? 'active' : '' ?> btn-sm px-3 py-1" style="font-size:.85rem;">
                <?= $tab['label'] ?>
                <span class="badge bg-<?= $tab['color'] ?>-subtle text-<?= $tab['color'] ?> ms-1"><?= $tab['count'] ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Bulk Actions Bar -->
<div class="mb-2 d-none" id="bulkBar">
    <div class="d-flex gap-2 align-items-center bg-light rounded p-2 border">
        <span class="text-muted small"><span id="selectedCount">0</span> selected</span>
        <button class="btn btn-sm btn-outline-success" onclick="bulkAction('bulk_approve')"><i class="bi bi-check-all me-1"></i>Approve</button>
        <button class="btn btn-sm btn-outline-danger" onclick="bulkAction('bulk_reject')"><i class="bi bi-x-lg me-1"></i>Reject</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="bulkAction('bulk_delete')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>

<!-- Table -->
<div class="card border-0 rounded-3 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:30px"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Type</th>
                        <th>Posted By</th>
                        <th>Target</th>
                        <th class="text-center">Visibility</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($notifications)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4"><i class="bi bi-inbox display-6 d-block mb-2"></i>No notifications found</td></tr>
                <?php else: foreach ($notifications as $n): ?>
                    <tr>
                        <td><input type="checkbox" class="form-check-input row-check" value="<?= $n['id'] ?>"></td>
                        <td style="max-width:200px">
                            <div class="d-flex align-items-center gap-1">
                                <?php if ($n['is_pinned']): ?><i class="bi bi-pin-fill text-primary" title="Pinned"></i><?php endif; ?>
                                <span class="text-truncate" style="font-size:.85rem;"><?= e($n['title']) ?></span>
                            </div>
                        </td>
                        <td><span class="badge bg-<?= $priorityColors[$n['priority'] ?? 'normal'] ?>-subtle text-<?= $priorityColors[$n['priority'] ?? 'normal'] ?>" style="font-size:.7rem;"><?= ucfirst($n['priority'] ?? 'normal') ?></span></td>
                        <td><span class="badge bg-light text-dark" style="font-size:.7rem;"><?= ucfirst(e($n['type'])) ?></span></td>
                        <td style="font-size:.8rem;">
                            <?= e($n['poster_name'] ?? 'System') ?>
                            <span class="badge bg-info-subtle text-info" style="font-size:.6rem;"><?= ucfirst(str_replace('_', ' ', $n['poster_role'] ?? '')) ?></span>
                        </td>
                        <td><span class="badge bg-secondary-subtle text-secondary" style="font-size:.7rem;"><?= ucfirst($n['target_audience'] ?? 'all') ?></span></td>
                        <td class="text-center">
                            <?php
                            $vis = [];
                            if ($n['show_popup'] ?? 0) $vis[] = '<i class="bi bi-window-stack text-primary" title="Popup"></i>';
                            if ($n['show_banner'] ?? 0) $vis[] = '<i class="bi bi-flag-fill text-success" title="Banner"></i>';
                            if ($n['show_marquee'] ?? 0) $vis[] = '<i class="bi bi-broadcast text-warning" title="Marquee"></i>';
                            if ($n['show_dashboard'] ?? 0) $vis[] = '<i class="bi bi-grid-fill text-info" title="Dashboard"></i>';
                            echo $vis ? implode(' ', $vis) : '<span class="text-muted">—</span>';
                            ?>
                        </td>
                        <td><span class="badge bg-<?= $statusColors[$n['status']] ?>-subtle text-<?= $statusColors[$n['status']] ?>"><?= ucfirst($n['status']) ?></span></td>
                        <td style="font-size:.8rem;"><?= $n['view_count'] ?? 0 ?></td>
                        <td style="font-size:.75rem;"><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index:1060;">
                                    <li><a class="dropdown-item small" href="#" onclick="viewNotif(<?= $n['id'] ?>);return false;"><i class="bi bi-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item small" href="#" onclick="editNotif(<?= $n['id'] ?>);return false;"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <?php if ($n['status'] === 'pending'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><form method="POST" class="d-inline"><input type="hidden" name="id" value="<?= $n['id'] ?>"><input type="hidden" name="action" value="approve"><?= csrfField() ?><button class="dropdown-item small text-success"><i class="bi bi-check-lg me-2"></i>Approve</button></form></li>
                                        <li><a class="dropdown-item small text-danger" href="#" onclick="rejectNotif(<?= $n['id'] ?>);return false;"><i class="bi bi-x-lg me-2"></i>Reject</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if ($n['is_pinned']): ?>
                                        <li><form method="POST"><input type="hidden" name="id" value="<?= $n['id'] ?>"><input type="hidden" name="action" value="unpin"><?= csrfField() ?><button class="dropdown-item small"><i class="bi bi-pin-angle me-2"></i>Unpin</button></form></li>
                                    <?php else: ?>
                                        <li><form method="POST"><input type="hidden" name="id" value="<?= $n['id'] ?>"><input type="hidden" name="action" value="pin"><?= csrfField() ?><button class="dropdown-item small"><i class="bi bi-pin-fill me-2"></i>Pin</button></form></li>
                                    <?php endif; ?>
                                    <li><form method="POST" onsubmit="return confirm('Delete this notification?')"><input type="hidden" name="id" value="<?= $n['id'] ?>"><input type="hidden" name="action" value="delete"><?= csrfField() ?><button class="dropdown-item small text-danger"><i class="bi bi-trash me-2"></i>Delete</button></form></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($p['total_pages'] > 1): ?>
        <div class="card-footer bg-white"><?= paginationHtml($p, '/admin/notifications.php?status=' . e($statusFilter)) ?></div>
    <?php endif; ?>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Notification Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body" id="viewBody"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
    <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="reject">
        <input type="hidden" name="id" id="rejectId">
        <div class="modal-header bg-danger-subtle"><h5 class="modal-title text-danger"><i class="bi bi-x-circle me-2"></i>Reject Notification</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <label class="form-label fw-semibold">Reason for rejection</label>
            <textarea name="reject_reason" class="form-control" rows="3" placeholder="Enter reason..." required></textarea>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger btn-sm">Reject</button></div>
    </form>
</div></div></div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create">
        <div class="modal-header bg-primary-subtle"><h5 class="modal-title text-primary"><i class="bi bi-plus-circle me-2"></i>Create Notification</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" required maxlength="200"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Type</label><select name="type" class="form-select"><option value="general">General</option><option value="academic">Academic</option><option value="exam">Exam</option><option value="event">Event</option><option value="holiday">Holiday</option><option value="urgent">Urgent</option></select></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Priority</label><select name="priority" class="form-select"><option value="normal">Normal</option><option value="important">Important</option><option value="urgent">Urgent</option></select></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Target Audience</label><select name="target_audience" class="form-select" onchange="toggleTarget(this)"><option value="all">All</option><option value="students">Students</option><option value="teachers">Teachers</option><option value="parents">Parents</option><option value="class">Specific Class</option><option value="section">Specific Section</option></select></div>
                <div class="col-md-6 d-none target-class-field"><label class="form-label fw-semibold">Class</label><input type="text" name="target_class" class="form-control" placeholder="e.g. 10"></div>
                <div class="col-md-6 d-none target-section-field"><label class="form-label fw-semibold">Section</label><input type="text" name="target_section" class="form-control" placeholder="e.g. A"></div>
                <div class="col-12"><label class="form-label fw-semibold">Content <span class="text-danger">*</span></label><textarea name="content" class="form-control" rows="5" required></textarea></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Schedule Date/Time</label><input type="datetime-local" name="schedule_at" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Expiry Date</label><input type="date" name="expires_at" class="form-control"></div>
                <div class="col-12">
                    <label class="form-label fw-semibold d-block">Visibility</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_public" id="cPublic" value="1" checked><label class="form-check-label" for="cPublic">Public Page</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_popup" id="cPopup" value="1"><label class="form-check-label" for="cPopup">Popup</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_banner" id="cBanner" value="1"><label class="form-check-label" for="cBanner">Banner</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_marquee" id="cMarquee" value="1"><label class="form-check-label" for="cMarquee">Marquee</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_dashboard" id="cDash" value="1"><label class="form-check-label" for="cDash">Dashboard Alert</label></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i>Create & Publish</button></div>
    </form>
</div></div></div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="editId">
        <div class="modal-header bg-warning-subtle"><h5 class="modal-title text-warning"><i class="bi bi-pencil-square me-2"></i>Edit Notification</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Title</label><input type="text" name="title" id="editTitle" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Type</label><select name="type" id="editType" class="form-select"><option value="general">General</option><option value="academic">Academic</option><option value="exam">Exam</option><option value="event">Event</option><option value="holiday">Holiday</option><option value="urgent">Urgent</option></select></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Priority</label><select name="priority" id="editPriority" class="form-select"><option value="normal">Normal</option><option value="important">Important</option><option value="urgent">Urgent</option></select></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Target</label><select name="target_audience" id="editTarget" class="form-select" onchange="toggleTarget(this)"><option value="all">All</option><option value="students">Students</option><option value="teachers">Teachers</option><option value="parents">Parents</option><option value="class">Specific Class</option><option value="section">Specific Section</option></select></div>
                <div class="col-md-6 d-none target-class-field"><label class="form-label fw-semibold">Class</label><input type="text" name="target_class" id="editTargetClass" class="form-control"></div>
                <div class="col-md-6 d-none target-section-field"><label class="form-label fw-semibold">Section</label><input type="text" name="target_section" id="editTargetSection" class="form-control"></div>
                <div class="col-12"><label class="form-label fw-semibold">Content</label><textarea name="content" id="editContent" class="form-control" rows="5" required></textarea></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Schedule</label><input type="datetime-local" name="schedule_at" id="editSchedule" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Expiry</label><input type="date" name="expires_at" id="editExpiry" class="form-control"></div>
                <div class="col-12">
                    <label class="form-label fw-semibold d-block">Visibility</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_public" id="ePublic" value="1"><label class="form-check-label" for="ePublic">Public</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_popup" id="ePopup" value="1"><label class="form-check-label" for="ePopup">Popup</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_banner" id="eBanner" value="1"><label class="form-check-label" for="eBanner">Banner</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_marquee" id="eMarquee" value="1"><label class="form-check-label" for="eMarquee">Marquee</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="show_dashboard" id="eDash" value="1"><label class="form-check-label" for="eDash">Dashboard</label></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button class="btn btn-warning btn-sm"><i class="bi bi-save me-1"></i>Save Changes</button></div>
    </form>
</div></div></div>

<!-- Notification Data (for JS) -->
<script>
const notifsData = <?= json_encode(array_map(function($n) {
    return [
        'id' => $n['id'], 'title' => $n['title'], 'content' => $n['content'],
        'type' => $n['type'], 'priority' => $n['priority'] ?? 'normal',
        'target_audience' => $n['target_audience'] ?? 'all',
        'target_class' => $n['target_class'] ?? '', 'target_section' => $n['target_section'] ?? '',
        'status' => $n['status'], 'poster_name' => $n['poster_name'] ?? 'System',
        'poster_role' => $n['poster_role'] ?? '', 'approver_name' => $n['approver_name'] ?? '',
        'approved_at' => $n['approved_at'] ?? '', 'reject_reason' => $n['reject_reason'] ?? '',
        'is_public' => $n['is_public'], 'is_pinned' => $n['is_pinned'],
        'show_popup' => $n['show_popup'] ?? 0, 'show_banner' => $n['show_banner'] ?? 0,
        'show_marquee' => $n['show_marquee'] ?? 0, 'show_dashboard' => $n['show_dashboard'] ?? 0,
        'schedule_at' => $n['schedule_at'] ?? '', 'expires_at' => $n['expires_at'] ?? '',
        'view_count' => $n['view_count'] ?? 0, 'created_at' => $n['created_at'],
        'attachment' => $n['attachment'] ?? ''
    ];
}, $notifications)) ?>;

function findNotif(id) { return notifsData.find(n => n.id == id); }

function viewNotif(id) {
    const n = findNotif(id);
    if (!n) return;
    const priorityBadge = {'normal':'secondary','important':'warning','urgent':'danger'};
    const statusBadge = {'pending':'warning','approved':'success','rejected':'danger'};
    let html = `
        <div class="mb-3">
            <h5 class="fw-bold">${escHtml(n.title)}</h5>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-${priorityBadge[n.priority]}-subtle text-${priorityBadge[n.priority]}">${n.priority.charAt(0).toUpperCase()+n.priority.slice(1)}</span>
                <span class="badge bg-light text-dark">${n.type.charAt(0).toUpperCase()+n.type.slice(1)}</span>
                <span class="badge bg-${statusBadge[n.status]}-subtle text-${statusBadge[n.status]}">${n.status.charAt(0).toUpperCase()+n.status.slice(1)}</span>
                <span class="badge bg-secondary-subtle text-secondary">Target: ${n.target_audience}</span>
            </div>
        </div>
        <div class="mb-3 p-3 bg-light rounded" style="white-space:pre-wrap;">${escHtml(n.content)}</div>
        <div class="row g-2 text-muted small">
            <div class="col-md-6"><i class="bi bi-person me-1"></i>Posted by: ${escHtml(n.poster_name)} (${n.poster_role})</div>
            <div class="col-md-6"><i class="bi bi-calendar me-1"></i>Created: ${n.created_at}</div>
            <div class="col-md-6"><i class="bi bi-eye me-1"></i>Views: ${n.view_count}</div>
            ${n.schedule_at ? `<div class="col-md-6"><i class="bi bi-clock me-1"></i>Scheduled: ${n.schedule_at}</div>` : ''}
            ${n.expires_at ? `<div class="col-md-6"><i class="bi bi-hourglass me-1"></i>Expires: ${n.expires_at}</div>` : ''}
        </div>`;
    if (n.status === 'approved' && n.approver_name) {
        html += `<div class="alert alert-success mt-3 small mb-0"><i class="bi bi-check-circle me-1"></i>Approved by ${escHtml(n.approver_name)} on ${n.approved_at}</div>`;
    }
    if (n.status === 'rejected' && n.reject_reason) {
        html += `<div class="alert alert-danger mt-3 small mb-0"><i class="bi bi-x-circle me-1"></i>Rejected: ${escHtml(n.reject_reason)}</div>`;
    }
    if (n.attachment) {
        html += `<div class="mt-3"><a href="/uploads/documents/${escHtml(n.attachment)}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-paperclip me-1"></i>Attachment</a></div>`;
    }
    document.getElementById('viewBody').innerHTML = html;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function editNotif(id) {
    const n = findNotif(id);
    if (!n) return;
    document.getElementById('editId').value = n.id;
    document.getElementById('editTitle').value = n.title;
    document.getElementById('editContent').value = n.content;
    document.getElementById('editType').value = n.type;
    document.getElementById('editPriority').value = n.priority;
    document.getElementById('editTarget').value = n.target_audience;
    document.getElementById('editTargetClass').value = n.target_class;
    document.getElementById('editTargetSection').value = n.target_section;
    document.getElementById('editSchedule').value = n.schedule_at ? n.schedule_at.replace(' ', 'T').substring(0, 16) : '';
    document.getElementById('editExpiry').value = n.expires_at || '';
    document.getElementById('ePublic').checked = !!n.is_public;
    document.getElementById('ePopup').checked = !!n.show_popup;
    document.getElementById('eBanner').checked = !!n.show_banner;
    document.getElementById('eMarquee').checked = !!n.show_marquee;
    document.getElementById('eDash').checked = !!n.show_dashboard;
    toggleTarget(document.getElementById('editTarget'));
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function rejectNotif(id) {
    document.getElementById('rejectId').value = id;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function toggleTarget(sel) {
    const form = sel.closest('form') || sel.closest('.modal-body');
    const v = sel.value;
    form.querySelectorAll('.target-class-field').forEach(el => el.classList.toggle('d-none', v !== 'class' && v !== 'section'));
    form.querySelectorAll('.target-section-field').forEach(el => el.classList.toggle('d-none', v !== 'section'));
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

// Bulk selection
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});
document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBulkBar));

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulkBar');
    document.getElementById('selectedCount').textContent = checked.length;
    bar.classList.toggle('d-none', checked.length === 0);
}

function bulkAction(action) {
    if (!confirm('Are you sure?')) return;
    const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
    if (!ids.length) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `<input type="hidden" name="action" value="${action}"><input type="hidden" name="id" value="0"><?= csrfField() ?>`;
    ids.forEach(id => { const i = document.createElement('input'); i.type = 'hidden'; i.name = 'ids[]'; i.value = id; form.appendChild(i); });
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
