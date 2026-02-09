<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db=getDB();
$uid=currentUserId();

$myNotifs=$db->query("SELECT COUNT(*) FROM notifications WHERE posted_by=$uid")->fetchColumn();
$myGallery=$db->query("SELECT COUNT(*) FROM gallery_items WHERE uploaded_by=$uid")->fetchColumn();
$pendingNotifs=$db->query("SELECT COUNT(*) FROM notifications WHERE posted_by=$uid AND status='pending'")->fetchColumn();

$pageTitle='Teacher Dashboard';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-4">Welcome, <?= e(currentUser()['name']) ?></h3>
<div class="row g-3">
  <div class="col-md-4"><div class="card text-center p-3"><h2 class="text-primary"><?= $myNotifs ?></h2><small>My Notifications</small></div></div>
  <div class="col-md-4"><div class="card text-center p-3"><h2 class="text-success"><?= $myGallery ?></h2><small>My Gallery Uploads</small></div></div>
  <div class="col-md-4"><div class="card text-center p-3"><h2 class="text-warning"><?= $pendingNotifs ?></h2><small>Pending Approval</small></div></div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
