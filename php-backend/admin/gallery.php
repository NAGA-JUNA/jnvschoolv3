<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();

if(isset($_GET['action'])&&isset($_GET['id'])&&is_numeric($_GET['id'])){
    if(verifyCsrf()){
    $action=$_GET['action'];$gid=(int)$_GET['id'];
    if(in_array($action,['approved','rejected'])){
        $db->prepare("UPDATE gallery_items SET status=?,approved_by=? WHERE id=?")->execute([$action,currentUserId(),$gid]);
        auditLog('gallery_'.$action,'gallery',$gid);setFlash('success','Gallery item '.$action.'.');
    }}header('Location: /admin/gallery.php');exit;
}
if(isset($_GET['delete'])&&is_numeric($_GET['delete'])){
    if(verifyCsrf()){$db->prepare("DELETE FROM gallery_items WHERE id=?")->execute([$_GET['delete']]);setFlash('success','Deleted.');}
    header('Location: /admin/gallery.php');exit;
}

$items=$db->query("SELECT g.*,u.name as uploader FROM gallery_items g LEFT JOIN users u ON g.uploaded_by=u.id ORDER BY g.created_at DESC")->fetchAll();
$pageTitle='Gallery';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Gallery Approvals</h3>
<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Title</th><th>Category</th><th>Uploaded By</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($items as $g): ?>
<tr><td><?= e($g['title']) ?></td><td><?= e($g['category']) ?></td><td><?= e($g['uploader']??'—') ?></td>
<td><span class="badge bg-<?= $g['status']==='pending'?'warning':($g['status']==='approved'?'success':'danger') ?>"><?= ucfirst($g['status']) ?></span></td>
<td>
<?php if($g['status']==='pending'): ?>
<a href="?action=approved&id=<?= $g['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-success">Approve</a>
<a href="?action=rejected&id=<?= $g['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-danger">Reject</a>
<?php endif; ?>
<a href="?delete=<?= $g['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</a>
</td></tr>
<?php endforeach; ?>
<?php if(empty($items)): ?><tr><td colspan="5" class="text-center text-muted py-3">No gallery items</td></tr><?php endif; ?>
</tbody></table></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
