<?php
$pageTitle='Admissions';require_once __DIR__.'/../includes/auth.php';requireAdmin();$db=getDB();
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$aid=(int)($_POST['id']??0);$action=$_POST['action']??'';$remarks=trim($_POST['remarks']??'');if($aid&&in_array($action,['approved','rejected','waitlisted'])){$db->prepare("UPDATE admissions SET status=?,remarks=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?")->execute([$action,$remarks,currentUserId(),$aid]);auditLog("admission_$action",'admission',$aid);setFlash('success',"Admission $action.");}header('Location: /admin/admissions.php?status='.($_GET['status']??'pending'));exit;}
$statusFilter=$_GET['status']??'pending';$page=max(1,(int)($_GET['page']??1));$where=$statusFilter?"WHERE a.status=?":"";$params=$statusFilter?[$statusFilter]:[];
$total=$db->prepare("SELECT COUNT(*) FROM admissions a $where");$total->execute($params);$total=$total->fetchColumn();$p=paginate($total,20,$page);
$stmt=$db->prepare("SELECT a.*,u.name as reviewer_name FROM admissions a LEFT JOIN users u ON a.reviewed_by=u.id $where ORDER BY a.created_at DESC LIMIT {$p['per_page']} OFFSET {$p['offset']}");$stmt->execute($params);$admissions=$stmt->fetchAll();
require_once __DIR__.'/../includes/header.php';?>
<ul class="nav nav-pills mb-3"><?php foreach(['pending'=>'warning','approved'=>'success','rejected'=>'danger','waitlisted'=>'info',''=>'secondary'] as $s=>$c):?><li class="nav-item"><a href="/admin/admissions.php?status=<?=$s?>" class="nav-link <?=$statusFilter===$s?'active':''?> btn-sm"><?=$s?ucfirst($s):'All'?></a></li><?php endforeach;?></ul>
<div class="card border-0 rounded-3"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>#</th><th>Student</th><th>Class</th><th>Phone</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($admissions)):?><tr><td colspan="7" class="text-center text-muted py-4">No admissions</td></tr>
<?php else:foreach($admissions as $a):?><tr><td><?=$a['id']?></td><td style="font-size:.85rem"><strong><?=e($a['student_name'])?></strong><br><small class="text-muted">F: <?=e($a['father_name']??'-')?></small></td><td><?=e($a['class_applied'])?></td><td style="font-size:.85rem"><?=e($a['phone']??'-')?></td><td><?php $c=['pending'=>'warning','approved'=>'success','rejected'=>'danger','waitlisted'=>'info'];?><span class="badge bg-<?=$c[$a['status']]?>-subtle text-<?=$c[$a['status']]?>"><?=ucfirst($a['status'])?></span></td><td style="font-size:.8rem"><?=date('M d, Y',strtotime($a['created_at']))?></td><td>
<?php if($a['status']==='pending'):?><div class="btn-group btn-group-sm">
<form method="POST" class="d-inline"><input type="hidden" name="id" value="<?=$a['id']?>"><input type="hidden" name="action" value="approved"><?=csrfField()?><button class="btn btn-outline-success py-0 px-2"><i class="bi bi-check-lg"></i></button></form>
<form method="POST" class="d-inline"><input type="hidden" name="id" value="<?=$a['id']?>"><input type="hidden" name="action" value="rejected"><?=csrfField()?><button class="btn btn-outline-danger py-0 px-2"><i class="bi bi-x-lg"></i></button></form>
<form method="POST" class="d-inline"><input type="hidden" name="id" value="<?=$a['id']?>"><input type="hidden" name="action" value="waitlisted"><?=csrfField()?><button class="btn btn-outline-info py-0 px-2"><i class="bi bi-hourglass"></i></button></form></div>
<?php else:?><small class="text-muted"><?=e($a['reviewer_name']??'')?></small><?php endif;?></td></tr>
<?php endforeach;endif;?></tbody></table></div></div></div>
<?=paginationHtml($p,'/admin/admissions.php?status='.$statusFilter)?>
<?php require_once __DIR__.'/../includes/footer.php';?>
