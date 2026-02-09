<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!verifyCsrf()) { setFlash('error', 'Invalid request.'); }
    else { $db->prepare("DELETE FROM teachers WHERE id = ?")->execute([$_GET['delete']]); auditLog('delete_teacher', 'teacher', (int)$_GET['delete']); setFlash('success', 'Teacher deleted.'); }
    header('Location: /admin/teachers.php'); exit;
}

$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20; $offset = ($page-1)*$perPage;
$where = []; $params = [];
if ($search) { $where[] = "(name LIKE ? OR employee_id LIKE ?)"; $s="%$search%"; $params=[$s,$s]; }
$whereSQL = $where ? 'WHERE '.implode(' AND ',$where) : '';
$total = $db->prepare("SELECT COUNT(*) FROM teachers $whereSQL"); $total->execute($params); $totalRows=$total->fetchColumn(); $totalPages=max(1,ceil($totalRows/$perPage));
$stmt=$db->prepare("SELECT * FROM teachers $whereSQL ORDER BY name LIMIT $perPage OFFSET $offset"); $stmt->execute($params); $teachers=$stmt->fetchAll();

$pageTitle='Teachers';
require_once __DIR__.'/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Teachers (<?= $totalRows ?>)</h3>
  <a href="/admin/teacher-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Teacher</a>
</div>
<form class="row g-2 mb-3" method="GET">
  <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= e($search) ?>"></div>
  <div class="col-md-2"><button class="btn btn-sm btn-outline-primary">Search</button></div>
</form>
<div class="table-responsive"><table class="table table-hover table-sm">
<thead><tr><th>ID</th><th>Name</th><th>Subject</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($teachers as $t): ?>
<tr><td><?= e($t['employee_id']) ?></td><td><?= e($t['name']) ?></td><td><?= e($t['subject']) ?></td><td><?= e($t['phone']) ?></td>
<td><span class="badge bg-<?= $t['status']==='active'?'success':'secondary' ?>"><?= ucfirst($t['status']) ?></span></td>
<td><a href="/admin/teacher-form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
<a href="/admin/teachers.php?delete=<?= $t['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</a></td></tr>
<?php endforeach; ?>
<?php if(empty($teachers)): ?><tr><td colspan="6" class="text-center text-muted py-3">No teachers found</td></tr><?php endif; ?>
</tbody></table></div>
<?php if($totalPages>1): ?><nav><ul class="pagination pagination-sm"><?php for($i=1;$i<=$totalPages;$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
