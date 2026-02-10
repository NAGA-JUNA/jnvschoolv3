<?php
$pageTitle = 'Students';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();
if (isset($_GET['delete']) && verifyCsrf()) { $db->prepare("DELETE FROM students WHERE id=?")->execute([(int)$_GET['delete']]); auditLog('delete_student','student',(int)$_GET['delete']); setFlash('success','Deleted.'); header('Location: /admin/students.php'); exit; }
$search=trim($_GET['search']??'');$classFilter=$_GET['class']??'';$statusFilter=$_GET['status']??'active';$page=max(1,(int)($_GET['page']??1));
$where=[];$params=[];
if($search){$where[]="(s.name LIKE ? OR s.admission_no LIKE ?)";$params[]="%$search%";$params[]="%$search%";}
if($classFilter){$where[]="s.class=?";$params[]=$classFilter;}
if($statusFilter){$where[]="s.status=?";$params[]=$statusFilter;}
$w=$where?'WHERE '.implode(' AND ',$where):'';
$total=$db->prepare("SELECT COUNT(*) FROM students s $w");$total->execute($params);$total=$total->fetchColumn();
$p=paginate($total,25,$page);
$stmt=$db->prepare("SELECT * FROM students s $w ORDER BY created_at DESC LIMIT {$p['per_page']} OFFSET {$p['offset']}");$stmt->execute($params);$students=$stmt->fetchAll();
$classes=$db->query("SELECT DISTINCT class FROM students ORDER BY class+0")->fetchAll(PDO::FETCH_COLUMN);
require_once __DIR__.'/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2"><span class="text-muted" style="font-size:.85rem"><?=$total?> student(s)</span><a href="/admin/student-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Student</a></div>
<div class="card border-0 rounded-3 mb-3"><div class="card-body py-2"><form class="row g-2 align-items-end" method="GET"><div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?=e($search)?>"></div><div class="col-md-2"><select name="class" class="form-select form-select-sm"><option value="">All Classes</option><?php foreach($classes as $c):?><option value="<?=e($c)?>" <?=$classFilter===$c?'selected':''?>><?=e($c)?></option><?php endforeach;?></select></div><div class="col-md-2"><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach(['active','inactive','alumni','tc_issued'] as $st):?><option value="<?=$st?>" <?=$statusFilter===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select></div><div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Filter</button></div><div class="col-md-2"><a href="/admin/students.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a></div></form></div></div>
<div class="card border-0 rounded-3"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Adm No</th><th>Name</th><th>Class</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($students)):?><tr><td colspan="6" class="text-center text-muted py-4">No students</td></tr>
<?php else:foreach($students as $s):?><tr><td style="font-size:.85rem" class="fw-medium"><?=e($s['admission_no'])?></td><td style="font-size:.85rem"><?=e($s['name'])?></td><td style="font-size:.85rem"><?=e($s['class'])?><?=$s['section']?'-'.e($s['section']):''?></td><td style="font-size:.85rem"><?=e($s['phone']??'-')?></td><td><?php $c=['active'=>'success','inactive'=>'secondary','alumni'=>'info','tc_issued'=>'warning'];?><span class="badge bg-<?=$c[$s['status']]??'light'?>-subtle text-<?=$c[$s['status']]??'dark'?>"><?=ucfirst($s['status'])?></span></td><td><a href="/admin/student-form.php?id=<?=$s['id']?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-pencil"></i></a> <a href="/admin/students.php?delete=<?=$s['id']?>&csrf_token=<?=csrfToken()?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a></td></tr>
<?php endforeach;endif;?></tbody></table></div></div></div>
<?=paginationHtml($p,'/admin/students.php?'.http_build_query(array_filter(['search'=>$search,'class'=>$classFilter,'status'=>$statusFilter])))?>
<?php require_once __DIR__.'/../includes/footer.php';?>
