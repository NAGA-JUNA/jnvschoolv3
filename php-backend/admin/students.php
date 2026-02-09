<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!verifyCsrf()) { setFlash('error', 'Invalid request.'); }
    else {
        $db->prepare("DELETE FROM students WHERE id = ?")->execute([$_GET['delete']]);
        auditLog('delete_student', 'student', (int)$_GET['delete']);
        setFlash('success', 'Student deleted.');
    }
    header('Location: /admin/students.php'); exit;
}

// Filters
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$class  = $_GET['class'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($search) { $where[] = "(name LIKE ? OR admission_no LIKE ? OR phone LIKE ?)"; $s = "%$search%"; $params = array_merge($params, [$s,$s,$s]); }
if ($status) { $where[] = "status = ?"; $params[] = $status; }
if ($class)  { $where[] = "class = ?"; $params[] = $class; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $db->prepare("SELECT COUNT(*) FROM students $whereSQL");
$total->execute($params);
$totalRows = $total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$stmt = $db->prepare("SELECT * FROM students $whereSQL ORDER BY name ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$students = $stmt->fetchAll();

$pageTitle = 'Students';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Students (<?= $totalRows ?>)</h3>
  <a href="/admin/student-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Student</a>
</div>

<form class="row g-2 mb-3" method="GET">
  <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?= e($search) ?>"></div>
  <div class="col-md-2">
    <select name="status" class="form-select form-select-sm"><option value="">All Status</option>
      <?php foreach(['active','inactive','alumni','transferred'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2"><input type="text" name="class" class="form-control form-control-sm" placeholder="Class" value="<?= e($class) ?>"></div>
  <div class="col-md-2"><button class="btn btn-sm btn-outline-primary w-100">Filter</button></div>
</form>

<div class="table-responsive">
<table class="table table-hover table-sm">
  <thead><tr><th>Adm No</th><th>Name</th><th>Class</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($students as $s): ?>
    <tr>
      <td><?= e($s['admission_no']) ?></td>
      <td><?= e($s['name']) ?></td>
      <td><?= e($s['class']) ?> <?= e($s['section']) ?></td>
      <td><?= e($s['phone']) ?></td>
      <td><span class="badge bg-<?= $s['status']==='active'?'success':($s['status']==='inactive'?'secondary':'info') ?>"><?= ucfirst($s['status']) ?></span></td>
      <td>
        <a href="/admin/student-form.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
        <a href="/admin/students.php?delete=<?= $s['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student?')">Del</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (empty($students)): ?><tr><td colspan="6" class="text-center text-muted py-3">No students found</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php if ($totalPages > 1): ?>
<nav><ul class="pagination pagination-sm">
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&class=<?= urlencode($class) ?>"><?= $i ?></a></li>
  <?php endfor; ?>
</ul></nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
