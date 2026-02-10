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
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <span class="text-muted" style="font-size:.85rem"><?=$total?> student(s)</span>
  <div class="d-flex gap-2">
    <a href="/admin/reports.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Export</a>
    <a href="/admin/student-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Student</a>
  </div>
</div>
<div class="card border-0 rounded-3 mb-3"><div class="card-body py-2"><form class="row g-2 align-items-end" method="GET"><div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?=e($search)?>"></div><div class="col-md-2"><select name="class" class="form-select form-select-sm"><option value="">All Classes</option><?php foreach($classes as $c):?><option value="<?=e($c)?>" <?=$classFilter===$c?'selected':''?>><?=e($c)?></option><?php endforeach;?></select></div><div class="col-md-2"><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach(['active','inactive','alumni','tc_issued'] as $st):?><option value="<?=$st?>" <?=$statusFilter===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select></div><div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Filter</button></div><div class="col-md-2"><a href="/admin/students.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a></div></form></div></div>
<div class="card border-0 rounded-3"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Adm No</th><th>Name</th><th>Class</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($students)):?><tr><td colspan="6" class="text-center text-muted py-4">No students</td></tr>
<?php else:foreach($students as $s):
  $photoUrl = $s['photo'] ? '/uploads/photos/'.$s['photo'] : '';
?><tr>
  <td style="font-size:.85rem" class="fw-medium"><?=e($s['admission_no'])?></td>
  <td style="font-size:.85rem">
    <div class="d-flex align-items-center gap-2">
      <?php if($photoUrl):?><img src="<?=$photoUrl?>" class="rounded-circle" style="width:32px;height:32px;object-fit:cover" alt=""><?php else:?><i class="bi bi-person-circle text-muted" style="font-size:1.5rem"></i><?php endif;?>
      <?=e($s['name'])?>
    </div>
  </td>
  <td style="font-size:.85rem"><?=e($s['class'])?><?=$s['section']?'-'.e($s['section']):''?></td>
  <td style="font-size:.85rem"><?=e($s['phone']??'-')?></td>
  <td><?php $c=['active'=>'success','inactive'=>'secondary','alumni'=>'info','tc_issued'=>'warning'];?><span class="badge bg-<?=$c[$s['status']]??'light'?>-subtle text-<?=$c[$s['status']]??'dark'?>"><?=ucfirst($s['status'])?></span></td>
  <td>
    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-student" data-bs-toggle="modal" data-bs-target="#studentModal"
      data-id="<?=$s['id']?>"
      data-name="<?=e($s['name'])?>"
      data-admission_no="<?=e($s['admission_no'])?>"
      data-photo="<?=e($photoUrl)?>"
      data-status="<?=e($s['status'])?>"
      data-class="<?=e($s['class'])?>"
      data-section="<?=e($s['section']??'')?>"
      data-roll_no="<?=e($s['roll_no']??'')?>"
      data-father_name="<?=e($s['father_name']??'')?>"
      data-mother_name="<?=e($s['mother_name']??'')?>"
      data-dob="<?=e($s['dob']??'')?>"
      data-gender="<?=e($s['gender']??'')?>"
      data-blood_group="<?=e($s['blood_group']??'')?>"
      data-category="<?=e($s['category']??'')?>"
      data-aadhar="<?=e($s['aadhar_no']??'')?>"
      data-phone="<?=e($s['phone']??'')?>"
      data-email="<?=e($s['email']??'')?>"
      data-address="<?=e($s['address']??'')?>"
      data-admission_date="<?=e($s['admission_date']??'')?>"
    ><i class="bi bi-eye"></i></button>
    <a href="/admin/student-form.php?id=<?=$s['id']?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-pencil"></i></a>
    <a href="/admin/students.php?delete=<?=$s['id']?>&csrf_token=<?=csrfToken()?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
  </td>
</tr>
<?php endforeach;endif;?></tbody></table></div></div></div>
<?=paginationHtml($p,'/admin/students.php?'.http_build_query(array_filter(['search'=>$search,'class'=>$classFilter,'status'=>$statusFilter])))?>

<!-- Student Profile Modal -->
<div class="modal fade" id="studentModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content border-0 rounded-3">
  <div class="modal-header border-0 bg-primary bg-opacity-10">
    <div class="d-flex align-items-center gap-3">
      <div id="sm-photo-wrap">
        <i class="bi bi-person-circle text-primary" style="font-size:3rem" id="sm-avatar-icon"></i>
        <img id="sm-avatar-img" class="rounded-circle d-none" style="width:64px;height:64px;object-fit:cover" alt="">
      </div>
      <div>
        <h5 class="mb-0 fw-bold" id="sm-name"></h5>
        <small class="text-muted" id="sm-admission_no"></small>
        <span class="badge ms-2" id="sm-status"></span>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>
  <div class="modal-body">
    <div class="row g-4">
      <div class="col-md-6">
        <h6 class="fw-semibold text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Info</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Father's Name</td><td class="fw-medium" id="sm-father_name"></td></tr>
          <tr><td class="text-muted">Mother's Name</td><td class="fw-medium" id="sm-mother_name"></td></tr>
          <tr><td class="text-muted">DOB</td><td class="fw-medium" id="sm-dob"></td></tr>
          <tr><td class="text-muted">Gender</td><td class="fw-medium" id="sm-gender"></td></tr>
          <tr><td class="text-muted">Blood Group</td><td class="fw-medium" id="sm-blood_group"></td></tr>
          <tr><td class="text-muted">Category</td><td class="fw-medium" id="sm-category"></td></tr>
          <tr><td class="text-muted">Aadhar No</td><td class="fw-medium" id="sm-aadhar"></td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6 class="fw-semibold text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Academic Info</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Class</td><td class="fw-medium" id="sm-class"></td></tr>
          <tr><td class="text-muted">Roll No</td><td class="fw-medium" id="sm-roll_no"></td></tr>
          <tr><td class="text-muted">Admission Date</td><td class="fw-medium" id="sm-admission_date"></td></tr>
        </table>
        <h6 class="fw-semibold text-primary mb-3 mt-4"><i class="bi bi-telephone me-2"></i>Contact</h6>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Phone</td><td class="fw-medium" id="sm-phone"></td></tr>
          <tr><td class="text-muted">Email</td><td class="fw-medium" id="sm-email"></td></tr>
          <tr><td class="text-muted">Address</td><td class="fw-medium" id="sm-address"></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    <a id="sm-edit-link" href="#" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
  </div>
</div></div></div>

<script>
document.querySelectorAll('.btn-view-student').forEach(btn => {
  btn.addEventListener('click', function() {
    const d = this.dataset;
    document.getElementById('sm-name').textContent = d.name;
    document.getElementById('sm-admission_no').textContent = d.admission_no;
    const statusEl = document.getElementById('sm-status');
    statusEl.textContent = d.status.charAt(0).toUpperCase() + d.status.slice(1);
    const sc = {active:'success',inactive:'secondary',alumni:'info',tc_issued:'warning'};
    statusEl.className = 'badge ms-2 bg-'+(sc[d.status]||'light')+'-subtle text-'+(sc[d.status]||'dark');
    if (d.photo) { document.getElementById('sm-avatar-img').src = d.photo; document.getElementById('sm-avatar-img').classList.remove('d-none'); document.getElementById('sm-avatar-icon').classList.add('d-none'); }
    else { document.getElementById('sm-avatar-img').classList.add('d-none'); document.getElementById('sm-avatar-icon').classList.remove('d-none'); }
    ['father_name','mother_name','dob','gender','blood_group','category','aadhar','roll_no','admission_date','phone','email','address'].forEach(k => {
      const el = document.getElementById('sm-'+k);
      if(el) el.textContent = d[k] || '-';
    });
    document.getElementById('sm-class').textContent = d.class + (d.section ? '-'+d.section : '');
    document.getElementById('sm-edit-link').href = '/admin/student-form.php?id='+d.id;
  });
});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
