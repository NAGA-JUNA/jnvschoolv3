<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();

if(isset($_GET['action'])&&isset($_GET['id'])&&is_numeric($_GET['id'])){
    if(!verifyCsrf()){setFlash('error','Invalid.');}else{
    $action=$_GET['action'];$nid=(int)$_GET['id'];
    if(in_array($action,['approved','rejected'])){
        $db->prepare("UPDATE notifications SET status=?,approved_by=? WHERE id=?")->execute([$action,currentUserId(),$nid]);
        if($action==='approved') $db->prepare("UPDATE notifications SET is_public=1 WHERE id=?")->execute([$nid]);
        auditLog('notification_'.$action,'notification',$nid);
        setFlash('success','Notification '.$action.'.');
    }}
    header('Location: /admin/notifications.php');exit;
}

if(isset($_GET['delete'])&&is_numeric($_GET['delete'])){
    if(verifyCsrf()){$db->prepare("DELETE FROM notifications WHERE id=?")->execute([$_GET['delete']]);auditLog('delete_notification','notification',(int)$_GET['delete']);setFlash('success','Deleted.');}
    header('Location: /admin/notifications.php');exit;
}

$stmt=$db->query("SELECT n.*,u.name as poster_name FROM notifications n LEFT JOIN users u ON n.posted_by=u.id ORDER BY n.created_at DESC");
$notifs=$stmt->fetchAll();
$pageTitle='Notifications';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Notifications</h3>
<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Title</th><th>Type</th><th>Posted By</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($notifs as $n): ?>
<tr><td><?= e($n['title']) ?></td><td><?= e($n['type']) ?></td><td><?= e($n['poster_name']??'—') ?></td>
<td><span class="badge bg-<?= $n['status']==='pending'?'warning':($n['status']==='approved'?'success':'danger') ?>"><?= ucfirst($n['status']) ?></span></td>
<td><?= e($n['created_at']) ?></td>
<td>
<?php if($n['status']==='pending'): ?>
<a href="?action=approved&id=<?= $n['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-success">Approve</a>
<a href="?action=rejected&id=<?= $n['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-danger">Reject</a>
<?php endif; ?>
<a href="?delete=<?= $n['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</a>
</td></tr>
<?php endforeach; ?>
<?php if(empty($notifs)): ?><tr><td colspan="6" class="text-center text-muted py-3">No notifications</td></tr><?php endif; ?>
</tbody></table></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
