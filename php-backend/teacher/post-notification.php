<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db = getDB();
$uid = currentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $type = $_POST['type'] ?? 'general';
    $isPublic = isset($_POST['is_public']) ? 1 : 0;

    if ($title && $content) {
        $stmt = $db->prepare("INSERT INTO notifications (title, content, type, is_public, posted_by, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$title, $content, $type, $isPublic, $uid]);
        auditLog('post_notification', 'notification', (int)$db->lastInsertId(), "Title: $title");
        setFlash('success', 'Notification submitted for admin approval.');
        header('Location: /teacher/post-notification.php');
        exit;
    } else {
        setFlash('error', 'Title and content are required.');
    }
}

$page = max(1, (int)($_GET['page'] ?? 1));
$total = $db->prepare("SELECT COUNT(*) FROM notifications WHERE posted_by=?"); $total->execute([$uid]); $total = $total->fetchColumn();
$p = paginate($total, 15, $page);
$notifs = $db->prepare("SELECT id, title, type, is_public, status, created_at FROM notifications WHERE posted_by=? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$notifs->execute([$uid, $p['per_page'], $p['offset']]);
$notifs = $notifs->fetchAll();

$pageTitle = 'Post Notification';
require_once __DIR__.'/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-megaphone-fill me-2 text-primary"></i>New Notification</div>
            <div class="card-body">
                <form method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required maxlength="200" placeholder="Enter notification title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select">
                            <option value="general">General</option>
                            <option value="academic">Academic</option>
                            <option value="exam">Exam</option>
                            <option value="event">Event</option>
                            <option value="holiday">Holiday</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="5" required maxlength="2000" placeholder="Write your notification..."></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_public" class="form-check-input" id="isPublic" value="1">
                        <label class="form-check-label" for="isPublic">Show on public website</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Submit for Approval</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-clock-history me-2"></i>My Submissions (<?= $total ?>)</div>
            <div class="card-body p-0">
                <?php if (empty($notifs)): ?>
                    <p class="text-muted p-3 mb-0">No submissions yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Type</th><th>Public</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php foreach ($notifs as $n): ?>
                            <tr>
                                <td><?= e($n['title']) ?></td>
                                <td><span class="badge bg-secondary"><?= e(ucfirst($n['type'])) ?></span></td>
                                <td><?= $n['is_public'] ? '<i class="bi bi-globe text-success"></i>' : '<i class="bi bi-lock text-muted"></i>' ?></td>
                                <td>
                                    <?php $sc = match($n['status']) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }; ?>
                                    <span class="badge bg-<?= $sc ?>"><?= e(ucfirst($n['status'])) ?></span>
                                </td>
                                <td><small><?= date('d M Y', strtotime($n['created_at'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($p['total_pages'] > 1): ?>
                <div class="card-footer bg-white"><?= paginationHtml($p, '/teacher/post-notification.php') ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
