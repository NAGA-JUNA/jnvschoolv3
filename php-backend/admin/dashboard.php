<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db = getDB();
$students   = $db->query("SELECT COUNT(*) as c FROM students WHERE status='active'")->fetch()['c'];
$teachers   = $db->query("SELECT COUNT(*) as c FROM teachers WHERE status='active'")->fetch()['c'];
$admissions = $db->query("SELECT COUNT(*) as c FROM admissions WHERE status='pending'")->fetch()['c'];
$notifs     = $db->query("SELECT COUNT(*) as c FROM notifications WHERE status='pending'")->fetch()['c'];
$gallery    = $db->query("SELECT COUNT(*) as c FROM gallery_items WHERE status='pending'")->fetch()['c'];
$events     = $db->query("SELECT COUNT(*) as c FROM events WHERE event_date >= CURDATE()")->fetch()['c'];

$recentLogs = $db->query("SELECT al.*, u.name as user_name FROM audit_logs al LEFT JOIN users u ON al.user_id=u.id ORDER BY al.created_at DESC LIMIT 10")->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-4">Dashboard</h3>
<div class="row g-3 mb-4">
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-primary"><?= $students ?></h2><small>Active Students</small></div></div>
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-success"><?= $teachers ?></h2><small>Active Teachers</small></div></div>
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-warning"><?= $admissions ?></h2><small>Pending Admissions</small></div></div>
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-info"><?= $notifs ?></h2><small>Pending Notifications</small></div></div>
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-secondary"><?= $gallery ?></h2><small>Pending Gallery</small></div></div>
  <div class="col-md-4 col-lg-2"><div class="card text-center p-3"><h2 class="text-danger"><?= $events ?></h2><small>Upcoming Events</small></div></div>
</div>

<div class="card">
  <div class="card-header">Recent Activity</div>
  <div class="card-body p-0">
    <table class="table table-sm mb-0">
      <thead><tr><th>User</th><th>Action</th><th>Entity</th><th>Time</th></tr></thead>
      <tbody>
      <?php foreach ($recentLogs as $log): ?>
        <tr>
          <td><?= e($log['user_name'] ?? 'System') ?></td>
          <td><?= e($log['action']) ?></td>
          <td><?= e($log['entity_type'] ?? '') ?> #<?= $log['entity_id'] ?? '' ?></td>
          <td><?= e($log['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($recentLogs)): ?><tr><td colspan="4" class="text-center text-muted py-3">No recent activity</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
