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
  <div class="d-flex gap-2">
    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importTeacherModal"><i class="bi bi-upload me-1"></i>Import</button>
    <a href="/admin/teacher-form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Teacher</a>
  </div>
</div>
<div class="card border-0 rounded-3 mb-3"><div class="card-body py-2"><form class="row g-2 align-items-end" method="GET"><div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="<?=e($search)?>"></div><div class="col-md-3"><select name="status" class="form-select form-select-sm"><option value="">All</option><?php foreach(['active','inactive','resigned','retired'] as $st):?><option value="<?=$st?>" <?=$statusFilter===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select></div><div class="col-md-2"><button class="btn btn-sm btn-dark w-100">Filter</button></div><div class="col-md-2"><a href="/admin/teachers.php" class="btn btn-sm btn-outline-secondary w-100">Clear</a></div></form></div></div>
<div class="card border-0 rounded-3"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Emp ID</th><th>Name</th><th>Designation</th><th>Subject</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($teachers)):?><tr><td colspan="7" class="text-center text-muted py-4">No teachers</td></tr>
<?php else:foreach($teachers as $t):
  $tPhotoUrl = $t['photo'] ? (str_starts_with($t['photo'], '/uploads/') ? $t['photo'] : '/uploads/photos/'.$t['photo']) : '';
?><tr>
  <td style="font-size:.85rem" class="fw-medium"><?=e($t['employee_id'])?></td>
  <td style="font-size:.85rem">
    <div class="d-flex align-items-center gap-2">
      <?php if($tPhotoUrl):?><img src="<?=$tPhotoUrl?>" class="rounded-circle" style="width:32px;height:32px;object-fit:cover" alt=""><?php else:?><i class="bi bi-person-circle text-muted" style="font-size:1.5rem"></i><?php endif;?>
      <?=e($t['name'])?>
      <?php if(!empty($t['is_core_team'])):?><span class="badge bg-warning-subtle text-warning" style="font-size:.65rem"><i class="bi bi-star-fill me-1"></i>Core</span><?php endif;?>
    </div>
  </td>
  <td style="font-size:.85rem"><?=e($t['designation']??'Teacher')?></td>
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
<style>
@media print {
  .sidebar, .sidebar-overlay, .top-bar, .content-area > *:not(#teacherModal) { display: none !important; }
  .main-content { margin-left: 0 !important; }
  .modal { position: static !important; display: block !important; }
  .modal-dialog { max-width: 100% !important; margin: 0 !important; }
  .modal-content { border: none !important; box-shadow: none !important; }
  .modal-footer { display: none !important; }
  .modal-backdrop { display: none !important; }
  body { background: #fff !important; }
}
</style>
<!-- Import Teacher Modal -->
<div class="modal fade" id="importTeacherModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 rounded-3">
  <div class="modal-header border-0">
    <h5 class="modal-title fw-bold"><i class="bi bi-upload me-2"></i>Import Teachers</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>
  <div class="modal-body">
    <div id="t-import-step1">
      <div class="alert alert-info py-2" style="font-size:.85rem">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Instructions:</strong>
        <ul class="mb-0 mt-1 ps-3">
          <li>CSV format only</li>
          <li>First row must be column headers</li>
          <li><strong>employee_id</strong> &amp; <strong>name</strong> are required</li>
          <li>If email is provided, a login account will be created (password: Teacher@123)</li>
          <li>Duplicate employee IDs will be skipped</li>
        </ul>
      </div>
      <a href="/admin/sample-teachers-csv.php" class="btn btn-outline-success btn-sm mb-3"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Download Sample CSV</a>
      <div class="mb-3">
        <label class="form-label fw-medium">Select CSV File</label>
        <input type="file" class="form-control" id="tImportFile" accept=".csv">
      </div>
    </div>
    <div id="t-import-step2" class="d-none text-center py-4">
      <div class="spinner-border text-primary mb-3" role="status"></div>
      <h6 class="fw-semibold">Processing...</h6>
      <div class="progress mt-3" style="height:8px"><div class="progress-bar progress-bar-striped progress-bar-animated" id="tImportProgress" style="width:10%"></div></div>
      <small class="text-muted mt-2 d-block" id="tImportStatusText">Uploading file...</small>
    </div>
    <div id="t-import-step3" class="d-none">
      <div class="text-center mb-3">
        <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
        <h5 class="fw-bold mt-2">Import Complete</h5>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-4"><div class="card border-0 bg-success bg-opacity-10 text-center p-2"><h4 class="mb-0 text-success" id="t-res-added">0</h4><small class="text-muted">Added</small></div></div>
        <div class="col-4"><div class="card border-0 bg-warning bg-opacity-10 text-center p-2"><h4 class="mb-0 text-warning" id="t-res-skipped">0</h4><small class="text-muted">Skipped</small></div></div>
        <div class="col-4"><div class="card border-0 bg-danger bg-opacity-10 text-center p-2"><h4 class="mb-0 text-danger" id="t-res-failed">0</h4><small class="text-muted">Failed</small></div></div>
      </div>
      <div id="t-res-errors" class="d-none">
        <h6 class="fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Errors</h6>
        <div class="border rounded p-2" style="max-height:150px;overflow-y:auto;font-size:.8rem" id="t-res-errors-list"></div>
      </div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="tImportCancelBtn">Cancel</button>
    <button type="button" class="btn btn-primary btn-sm" id="tImportUploadBtn" disabled><i class="bi bi-upload me-1"></i>Upload &amp; Process</button>
    <button type="button" class="btn btn-outline-primary btn-sm d-none" id="tImportMoreBtn">Import More</button>
  </div>
</div></div></div>

<script>
(function(){
  const fileInput = document.getElementById('tImportFile');
  const uploadBtn = document.getElementById('tImportUploadBtn');
  const cancelBtn = document.getElementById('tImportCancelBtn');
  const moreBtn = document.getElementById('tImportMoreBtn');
  const step1 = document.getElementById('t-import-step1');
  const step2 = document.getElementById('t-import-step2');
  const step3 = document.getElementById('t-import-step3');
  const progress = document.getElementById('tImportProgress');

  fileInput.addEventListener('change', () => { uploadBtn.disabled = !fileInput.files.length; });

  function resetModal() {
    step1.classList.remove('d-none'); step2.classList.add('d-none'); step3.classList.add('d-none');
    uploadBtn.classList.remove('d-none'); uploadBtn.disabled = true; moreBtn.classList.add('d-none');
    cancelBtn.textContent = 'Cancel'; fileInput.value = '';
    progress.style.width = '10%';
  }

  document.getElementById('importTeacherModal').addEventListener('hidden.bs.modal', resetModal);
  moreBtn.addEventListener('click', resetModal);

  uploadBtn.addEventListener('click', function() {
    if (!fileInput.files.length) return;
    step1.classList.add('d-none'); step2.classList.remove('d-none');
    uploadBtn.classList.add('d-none');

    const fd = new FormData();
    fd.append('csv_file', fileInput.files[0]);

    progress.style.width = '30%';
    document.getElementById('tImportStatusText').textContent = 'Processing records...';

    let prog = 30;
    const iv = setInterval(() => { if (prog < 85) { prog += 5; progress.style.width = prog+'%'; } }, 300);

    fetch('/admin/import-teachers.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        clearInterval(iv);
        progress.style.width = '100%';
        setTimeout(() => {
          step2.classList.add('d-none'); step3.classList.remove('d-none');
          moreBtn.classList.remove('d-none'); cancelBtn.textContent = 'Close';
          document.getElementById('t-res-added').textContent = data.added || 0;
          document.getElementById('t-res-skipped').textContent = data.skipped || 0;
          document.getElementById('t-res-failed').textContent = data.failed || 0;
          if (data.errors && data.errors.length) {
            document.getElementById('t-res-errors').classList.remove('d-none');
            document.getElementById('t-res-errors-list').innerHTML = data.errors.map(e => '<div class="text-danger">'+e+'</div>').join('');
          } else {
            document.getElementById('t-res-errors').classList.add('d-none');
          }
        }, 500);
      })
      .catch(err => {
        clearInterval(iv);
        step2.classList.add('d-none'); step1.classList.remove('d-none');
        uploadBtn.classList.remove('d-none');
        alert('Import failed: ' + err.message);
      });
  });
})();
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
