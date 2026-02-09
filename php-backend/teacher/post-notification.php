<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db=getDB();
$error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid.';}else{
    $title=trim($_POST['title']??'');$content=trim($_POST['content']??'');$type=$_POST['type']??'general';
    if(!$title||!$content){$error='Title and content required.';}else{
        $db->prepare("INSERT INTO notifications (title,content,type,posted_by,status) VALUES (?,?,?,?,'pending')")->execute([$title,$content,$type,currentUserId()]);
        auditLog('post_notification','notification',(int)$db->lastInsertId());
        setFlash('success','Notification submitted for approval.');header('Location: /teacher/post-notification.php');exit;
    }}
}

$myNotifs=$db->prepare("SELECT * FROM notifications WHERE posted_by=? ORDER BY created_at DESC");
$myNotifs->execute([currentUserId()]);$notifs=$myNotifs->fetchAll();

$pageTitle='Post Notification';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Post Notification</h3>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card mb-4"><div class="card-body">
<form method="POST" class="row g-3">
<?= csrfField() ?>
<div class="col-md-6"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Type</label><select name="type" class="form-select"><option value="general">General</option><option value="academic">Academic</option><option value="exam">Exam</option><option value="holiday">Holiday</option><option value="event">Event</option></select></div>
<div class="col-12"><label class="form-label">Content *</label><textarea name="content" class="form-control" rows="4" required></textarea></div>
<div class="col-12"><button class="btn btn-primary">Submit for Approval</button></div>
</form></div></div>

<h5>My Submissions</h5>
<table class="table table-sm"><thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead>
<tbody><?php foreach($notifs as $n): ?><tr><td><?= e($n['title']) ?></td><td><span class="badge bg-<?= $n['status']==='pending'?'warning':($n['status']==='approved'?'success':'danger') ?>"><?= ucfirst($n['status']) ?></span></td><td><?= e($n['created_at']) ?></td></tr><?php endforeach; ?></tbody>
</table>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
