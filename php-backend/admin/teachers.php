<?php
$pageTitle='Teachers';require_once __DIR__.'/../includes/auth.php';requireAdmin();$db=getDB();
if(isset($_GET['delete'])&&verifyCsrf()){$db->prepare("DELETE FROM teachers WHERE id=?")->execute([(int)$_GET['delete']]);auditLog('delete_teacher','teacher',(int)$_GET['delete']);setFlash('success','Deleted.');header('Location: /admin/teachers.php');exit;}
$search=trim($_GET['search']??'');$statusFilter=$_GET['status']??'active';$page=max(1,(int)($_GET['page']??1));
$where=[];$params=[];if($search){$where[]="(name LIKE ? OR employee_id LIKE ? OR subject LIKE ?)";$params=array_merge($params,["%$search%","%$search%","%$search%"]);}if($statusFilter){$where[]="status=?";$params[]=$statusFilter;}$w=$where?'WHERE '.implode(' AND ',$where):'';
$total=$db->prepare("SELECT COUNT(*) FROM teachers $w");$total->execute($params);$total=$total->fetchColumn();$p=paginate($total,25,$page);
$stmt=$db->prepare("SELECT * FROM teachers $w ORDER BY created_at DESC LIMIT {$p['per_page']} OFFSET {$p['offset']}");$stmt->execute($params);$teachers=$stmt->fetchAll();
require_once __DIR__.'/../includes/header.php';?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <span class="text-muted" style="font-size:.85rem"><?=$total?> teacher(s)</span>
  <a href="/admin/teacher-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Teacher</a>
</div>
<div class="card border-0 rounded-3 mb-3"><div class="card-body py-2"><form class="row g-2 align-items-end" method="GET"><div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?=e($search)?>"></div><div class="col-md-3"><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach(['active','inactive','resigned','retired'] as $st):?><option value="<?=$st?>" <?=$statusFilter===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select></div><div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Filter</button></div><div class="col-md-2"><a href="/admin/teachers.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a></div></form></div></div>
<div class="card border-0 rounded-3"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Emp ID</th><th>Name</th><th>Subject</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($teachers)):?><tr><td colspan="6" class="text-center text-muted py-4">No teachers</td></tr>
<?php else:foreach($teachers as $t):
  $tPhotoUrl = $t['photo'] ? '/uploads/photos/'.$t['photo'] : '';
?><tr>
  <td style="font-size:.85rem" class="fw-medium"><?=e($t['employee_id'])?></td>
  <td style="font-size:.85rem">
    <div class="d-flex align-items-center gap-2">
      <?php if($tPhotoUrl):?><img src="<?=$tPhotoUrl?>" class="rounded-circle" style="width:32px;height:32px;object-fit:cover" alt=""><?php else:?><i class="bi bi-person-circle text-muted" style="font-size:1.5rem"></i><?php endif;?>
      <?=e($t['name'])?>
    </div>
  </td>
  <td style="font-size:.85rem"><?=e($t['subject']??'-')?></td>
  <td style="font-size:.85rem"><?=e($t['phone']??'-')?></td>
  <td><span class="badge bg-<?=$t['status']==='active'?'success':'secondary'?>-subtle text-<?=$t['status']==='active'?'success':'secondary'?>"><?=ucfirst($t['status'])?></span></td>
  <td>
    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-teacher" data-bs-toggle="modal" data-bs-target="#teacherModal"
      data-id="<?=$t['id']?>"
      data-name="<?=e($t['name'])?>"
      data-employee_id="<?=e($t['employee_id'])?>"
      data-photo="<?=e($tPhotoUrl)?>"
      data-status="<?=e($t['status'])?>"
      data-subject="<?=e($t['subject']??'')?>"
      data-qualification="<?=e($t['qualification']??'')?>"
      data-experience_years="<?=e($t['experience_years']??'')?>"
      data-joining_date="<?=e($t['joining_date']??'')?>"
      data-dob="<?=e($t['dob']??'')?>"
      data-gender="<?=e($t['gender']??'')?>"
      data-phone="<?=e($t['phone']??'')?>"
      data-email="<?=e($t['email']??'')?>"
      data-address="<?=e($t['address']??'')?>"
    ><i class="bi bi-eye"></i></button>
    <a href="/admin/teacher-form.php?id=<?=$t['id']?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-pencil"></i></a>
    <a href="/admin/teachers.php?delete=<?=$t['id']?>&csrf_token=<?=csrfToken()?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
  </td>
</tr>
<?php endforeach;endif;?></tbody></table></div></div></div>
<?=paginationHtml($p,'/admin/teachers.php?'.http_build_query(array_filter(['search'=>$search,'status'=>$statusFilter])))?>

<!-- Teacher Profile Modal -->
<div class="modal fade" id="teacherModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content border-0 rounded-3">
  <div class="modal-header border-0 bg-primary bg-opacity-10">
    <div class="d-flex align-items-center gap-3">
      <div>
        <i class="bi bi-person-circle text-primary" style="font-size:3rem" id="tm-avatar-icon"></i>
        <img id="tm-avatar-img" class="rounded-circle d-none" style="width:64px;height:64px;object-fit:cover" alt="">
      </div>
      <div>
        <h5 class="mb-0 fw-bold" id="tm-name"></h5>
        <small class="text-muted" id="tm-employee_id"></small>
        <span class="badge ms-2" id="tm-status"></span>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>
  <div class="modal-body">
    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="fw-semibold text-primary mb-3"><i class="bi bi-briefcase me-2"></i>Professional Info</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Subject</td><td class="fw-medium" id="tm-subject"></td></tr>
          <tr><td class="text-muted">Qualification</td><td class="fw-medium" id="tm-qualification"></td></tr>
          <tr><td class="text-muted">Experience</td><td class="fw-medium" id="tm-experience_years"></td></tr>
          <tr><td class="text-muted">Joining Date</td><td class="fw-medium" id="tm-joining_date"></td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6 class="fw-semibold text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Info</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">DOB</td><td class="fw-medium" id="tm-dob"></td></tr>
          <tr><td class="text-muted">Gender</td><td class="fw-medium" id="tm-gender"></td></tr>
          <tr><td class="text-muted">Phone</td><td class="fw-medium" id="tm-phone"></td></tr>
          <tr><td class="text-muted">Email</td><td class="fw-medium" id="tm-email"></td></tr>
          <tr><td class="text-muted">Address</td><td class="fw-medium" id="tm-address"></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    <a id="tm-edit-link" href="#" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
  </div>
</div></div></div>

<script>
document.querySelectorAll('.btn-view-teacher').forEach(btn => {
  btn.addEventListener('click', function() {
    const d = this.dataset;
    document.getElementById('tm-name').textContent = d.name;
    document.getElementById('tm-employee_id').textContent = d.employee_id;
    const statusEl = document.getElementById('tm-status');
    statusEl.textContent = d.status.charAt(0).toUpperCase() + d.status.slice(1);
    statusEl.className = 'badge ms-2 bg-'+(d.status==='active'?'success':'secondary')+'-subtle text-'+(d.status==='active'?'success':'secondary');
    if (d.photo) { document.getElementById('tm-avatar-img').src = d.photo; document.getElementById('tm-avatar-img').classList.remove('d-none'); document.getElementById('tm-avatar-icon').classList.add('d-none'); }
    else { document.getElementById('tm-avatar-img').classList.add('d-none'); document.getElementById('tm-avatar-icon').classList.remove('d-none'); }
    ['subject','qualification','joining_date','dob','gender','phone','email','address'].forEach(k => {
      const el = document.getElementById('tm-'+k);
      if(el) el.textContent = d[k] || '-';
    });
    document.getElementById('tm-experience_years').textContent = d.experience_years ? d.experience_years+' years' : '-';
    document.getElementById('tm-edit-link').href = '/admin/teacher-form.php?id='+d.id;
  });
});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
