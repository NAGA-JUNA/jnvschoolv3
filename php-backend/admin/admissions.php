<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();

// Approve/Reject
if(isset($_GET['action'])&&isset($_GET['id'])&&is_numeric($_GET['id'])){
    if(!verifyCsrf()){setFlash('error','Invalid.');}else{
    $action=$_GET['action'];$aid=(int)$_GET['id'];
    if(in_array($action,['approved','rejected','waitlisted'])){
        $db->prepare("UPDATE admissions SET status=?,reviewed_by=? WHERE id=?")->execute([$action,currentUserId(),$aid]);
        auditLog('admission_'.$action,'admission',$aid);
        setFlash('success','Admission '.$action.'.');
    }}
    header('Location: /admin/admissions.php');exit;
}

$status=$_GET['status']??'';
$where=$status?"WHERE status=?":"";
$params=$status?[$status]:[];
$stmt=$db->prepare("SELECT * FROM admissions $where ORDER BY created_at DESC");
$stmt->execute($params);
$admissions=$stmt->fetchAll();

$pageTitle='Admissions';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Admissions</h3>
<div class="mb-3">
  <a href="?status=" class="btn btn-sm btn-outline-secondary <?= !$status?'active':'' ?>">All</a>
  <a href="?status=pending" class="btn btn-sm btn-outline-warning <?= $status==='pending'?'active':'' ?>">Pending</a>
  <a href="?status=approved" class="btn btn-sm btn-outline-success <?= $status==='approved'?'active':'' ?>">Approved</a>
  <a href="?status=rejected" class="btn btn-sm btn-outline-danger <?= $status==='rejected'?'active':'' ?>">Rejected</a>
</div>
<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Name</th><th>Class</th><th>Phone</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($admissions as $a): ?>
<tr><td><?= e($a['student_name']) ?></td><td><?= e($a['class_applied']) ?></td><td><?= e($a['phone']) ?></td>
<td><span class="badge bg-<?= $a['status']==='pending'?'warning':($a['status']==='approved'?'success':'danger') ?>"><?= ucfirst($a['status']) ?></span></td>
<td><?= e($a['created_at']) ?></td>
<td>
<?php if($a['status']==='pending'): ?>
<a href="?action=approved&id=<?= $a['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-success">Approve</a>
<a href="?action=rejected&id=<?= $a['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-danger">Reject</a>
<?php endif; ?>
</td></tr>
<?php endforeach; ?>
<?php if(empty($admissions)): ?><tr><td colspan="6" class="text-center text-muted py-3">No admissions</td></tr><?php endif; ?>
</tbody></table></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
