<?php
$pageTitle='Settings';require_once __DIR__.'/../includes/auth.php';requireAdmin();$db=getDB();
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf()){$action=$_POST['form_action']??'settings';

if($action==='settings'){$keys=['school_name','school_short_name','school_tagline','school_email','school_phone','school_address','primary_color','academic_year','admission_open'];foreach($keys as $k){$v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);}auditLog('update_settings','settings');setFlash('success','Settings updated.');}

if($action==='logo_upload'){
  if(!empty($_FILES['school_logo']['name'])&&$_FILES['school_logo']['error']===UPLOAD_ERR_OK){
    $ext=strtolower(pathinfo($_FILES['school_logo']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['jpg','jpeg','png','webp','svg'])){
      @mkdir(__DIR__.'/../uploads/logo',0755,true);
      $fname='school_logo.'.$ext;move_uploaded_file($_FILES['school_logo']['tmp_name'],__DIR__.'/../uploads/logo/'.$fname);
      $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('school_logo',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$fname,$fname]);
      auditLog('update_logo','settings');setFlash('success','Logo updated.');
    }else setFlash('error','Logo must be JPG, PNG, WebP or SVG.');
  }
}

if($action==='social_links'){
  foreach(['social_facebook','social_twitter','social_instagram','social_youtube','social_linkedin'] as $k){
    $v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_social','settings');setFlash('success','Social links updated.');
}

if($action==='sms_whatsapp'){
  foreach(['whatsapp_api_number','sms_gateway_key'] as $k){
    $v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_sms_config','settings');setFlash('success','SMS/WhatsApp config updated.');
}

if($action==='create_user'){$name=trim($_POST['user_name']??'');$email=trim($_POST['user_email']??'');$pass=$_POST['user_password']??'';$role=$_POST['user_role']??'teacher';if($name&&$email&&strlen($pass)>=6){try{$db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role]);auditLog('create_user','user',(int)$db->lastInsertId(),$email);setFlash('success',"User created.");}catch(PDOException $e){if($e->getCode()==23000)setFlash('error','Email exists.');else setFlash('error',$e->getMessage());}}else setFlash('error','All fields required, password min 6.');}

if($action==='edit_user'){
  $uid=(int)($_POST['edit_user_id']??0);$uname=trim($_POST['edit_user_name']??'');$urole=$_POST['edit_user_role']??'';$uactive=(int)($_POST['edit_user_active']??1);
  if($uid&&$uname&&$urole){$db->prepare("UPDATE users SET name=?,role=?,is_active=? WHERE id=?")->execute([$uname,$urole,$uactive,$uid]);auditLog('edit_user','user',$uid);setFlash('success','User updated.');}
}

if($action==='reset_user_pass'){
  $uid=(int)($_POST['reset_user_id']??0);
  if($uid){$db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash('Reset@123',PASSWORD_DEFAULT),$uid]);auditLog('reset_password','user',$uid);setFlash('success','Password reset to Reset@123.');}
}

if($action==='delete_user'&&isSuperAdmin()){$uid=(int)($_POST['delete_user_id']??0);if($uid&&$uid!==currentUserId()){$db->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);auditLog('delete_user','user',$uid);setFlash('success','Deleted.');}}

if($action==='clear_audit_logs'&&isSuperAdmin()){$db->exec("DELETE FROM audit_logs");auditLog('clear_audit_logs','system');setFlash('success','Audit logs cleared.');}

header('Location: /admin/settings.php');exit;}

$settings=[];$stmt=$db->query("SELECT setting_key,setting_value FROM settings");while($r=$stmt->fetch())$settings[$r['setting_key']]=$r['setting_value'];
$users=$db->query("SELECT id,name,email,role,is_active,last_login FROM users ORDER BY created_at DESC")->fetchAll();
$totalStudents=$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers=$db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$totalUsers=count($users);
require_once __DIR__.'/../includes/header.php';$s=$settings;?>

<!-- Row 1: School Info + Logo & Branding -->
<div class="row g-3 mb-3">
<div class="col-lg-7"><div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-building me-2"></i>School Info</h6></div><div class="card-body"><form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="settings"><div class="row g-3">
<div class="col-md-6"><label class="form-label">School Name</label><input type="text" name="school_name" class="form-control" value="<?=e($s['school_name']??'')?>"></div>
<div class="col-md-6"><label class="form-label">Short Name</label><input type="text" name="school_short_name" class="form-control" value="<?=e($s['school_short_name']??'')?>"></div>
<div class="col-12"><label class="form-label">Tagline</label><input type="text" name="school_tagline" class="form-control" value="<?=e($s['school_tagline']??'')?>"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="school_email" class="form-control" value="<?=e($s['school_email']??'')?>"></div>
<div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="school_phone" class="form-control" value="<?=e($s['school_phone']??'')?>"></div>
<div class="col-12"><label class="form-label">Address</label><textarea name="school_address" class="form-control" rows="2"><?=e($s['school_address']??'')?></textarea></div>
<div class="col-md-4"><label class="form-label">Color</label><input type="color" name="primary_color" class="form-control form-control-color" value="<?=e($s['primary_color']??'#1e40af')?>"></div>
<div class="col-md-4"><label class="form-label">Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?=e($s['academic_year']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Admissions</label><select name="admission_open" class="form-select"><option value="1" <?=($s['admission_open']??'1')==='1'?'selected':''?>>Open</option><option value="0" <?=($s['admission_open']??'1')==='0'?'selected':''?>>Closed</option></select></div>
<div class="col-12"><button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div></div></form></div></div></div>

<div class="col-lg-5">
  <!-- School Logo -->
  <div class="card border-0 rounded-3 mb-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-image me-2"></i>School Logo</h6></div><div class="card-body">
    <form method="POST" enctype="multipart/form-data"><?=csrfField()?><input type="hidden" name="form_action" value="logo_upload">
    <?php if(!empty($s['school_logo'])):?><div class="mb-3 text-center"><img src="/uploads/logo/<?=e($s['school_logo'])?>" alt="School Logo" style="max-height:80px" class="rounded"></div><?php endif;?>
    <input type="file" name="school_logo" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.webp,.svg">
    <button class="btn btn-success btn-sm w-100"><i class="bi bi-upload me-1"></i>Upload Logo</button>
    </form>
  </div></div>

  <!-- System Information -->
  <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-2"></i>System Info</h6></div><div class="card-body p-0">
    <table class="table table-sm mb-0">
      <tr><td class="text-muted ps-3">PHP Version</td><td class="fw-medium"><?=phpversion()?></td></tr>
      <tr><td class="text-muted ps-3">Total Students</td><td class="fw-medium"><?=$totalStudents?></td></tr>
      <tr><td class="text-muted ps-3">Total Teachers</td><td class="fw-medium"><?=$totalTeachers?></td></tr>
      <tr><td class="text-muted ps-3">Total Users</td><td class="fw-medium"><?=$totalUsers?></td></tr>
      <tr><td class="text-muted ps-3">Server</td><td class="fw-medium"><?=$_SERVER['SERVER_SOFTWARE']??'N/A'?></td></tr>
    </table>
  </div></div>
</div>
</div>

<!-- Row 2: Social Media + SMS/WhatsApp -->
<div class="row g-3 mb-3">
<div class="col-lg-6"><div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-share me-2"></i>Social Media Links</h6></div><div class="card-body">
  <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="social_links"><div class="row g-2">
  <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-facebook"></i></span><input type="url" name="social_facebook" class="form-control" placeholder="Facebook URL" value="<?=e($s['social_facebook']??'')?>"></div></div>
  <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-twitter-x"></i></span><input type="url" name="social_twitter" class="form-control" placeholder="Twitter/X URL" value="<?=e($s['social_twitter']??'')?>"></div></div>
  <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-instagram"></i></span><input type="url" name="social_instagram" class="form-control" placeholder="Instagram URL" value="<?=e($s['social_instagram']??'')?>"></div></div>
  <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-youtube"></i></span><input type="url" name="social_youtube" class="form-control" placeholder="YouTube URL" value="<?=e($s['social_youtube']??'')?>"></div></div>
  <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-linkedin"></i></span><input type="url" name="social_linkedin" class="form-control" placeholder="LinkedIn URL" value="<?=e($s['social_linkedin']??'')?>"></div></div>
  <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Links</button></div>
  </div></form>
</div></div></div>

<div class="col-lg-6"><div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-whatsapp me-2"></i>SMS / WhatsApp</h6></div><div class="card-body">
  <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="sms_whatsapp"><div class="row g-3">
  <div class="col-12"><label class="form-label">WhatsApp API Number</label><input type="text" name="whatsapp_api_number" class="form-control" placeholder="+91 9876543210" value="<?=e($s['whatsapp_api_number']??'')?>"></div>
  <div class="col-12"><label class="form-label">SMS Gateway API Key</label><input type="text" name="sms_gateway_key" class="form-control" placeholder="API key from your SMS provider" value="<?=e($s['sms_gateway_key']??'')?>"></div>
  <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Config</button></div>
  </div></form>
</div></div></div>
</div>

<!-- Row 3: User Management -->
<div class="row g-3 mb-3">
<div class="col-lg-5"><div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-person-plus me-2"></i>Create User</h6></div><div class="card-body"><form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="create_user">
<div class="mb-2"><input type="text" name="user_name" class="form-control form-control-sm" placeholder="Name" required></div>
<div class="mb-2"><input type="email" name="user_email" class="form-control form-control-sm" placeholder="Email" required></div>
<div class="mb-2"><input type="password" name="user_password" class="form-control form-control-sm" placeholder="Password" required minlength="6"></div>
<div class="mb-2"><select name="user_role" class="form-select form-select-sm"><option value="teacher">Teacher</option><option value="office">Office</option><option value="admin">Admin</option><?php if(isSuperAdmin()):?><option value="super_admin">Super Admin</option><?php endif;?></select></div>
<button class="btn btn-success btn-sm w-100"><i class="bi bi-person-plus me-1"></i>Create User</button></form></div></div></div>

<div class="col-lg-7"><div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0">Users (<?=count($users)?>)</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="table-light"><tr><th>Name</th><th>Role</th><th>Status</th><th>Last Login</th><th></th></tr></thead><tbody>
<?php foreach($users as $u):?><tr>
  <td style="font-size:.8rem"><strong><?=e($u['name'])?></strong><br><small class="text-muted"><?=e($u['email'])?></small></td>
  <td><span class="badge bg-primary-subtle text-primary"><?=e($u['role'])?></span></td>
  <td><span class="badge bg-<?=$u['is_active']?'success':'danger'?>-subtle text-<?=$u['is_active']?'success':'danger'?>"><?=$u['is_active']?'Active':'Inactive'?></span></td>
  <td style="font-size:.75rem"><?=$u['last_login']?date('M d, H:i',strtotime($u['last_login'])):'Never'?></td>
  <td class="text-nowrap">
    <?php if($u['id']!==currentUserId()):?>
    <button class="btn btn-sm btn-outline-primary py-0 px-1 btn-edit-user" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?=$u['id']?>" data-name="<?=e($u['name'])?>" data-role="<?=e($u['role'])?>" data-active="<?=$u['is_active']?>"><i class="bi bi-pencil" style="font-size:.7rem"></i></button>
    <form method="POST" class="d-inline"><input type="hidden" name="form_action" value="reset_user_pass"><input type="hidden" name="reset_user_id" value="<?=$u['id']?>"><?=csrfField()?><button class="btn btn-sm btn-outline-warning py-0 px-1" onclick="return confirm('Reset password to Reset@123?')" title="Reset Password"><i class="bi bi-key" style="font-size:.7rem"></i></button></form>
    <?php if(isSuperAdmin()):?><form method="POST" class="d-inline"><input type="hidden" name="form_action" value="delete_user"><input type="hidden" name="delete_user_id" value="<?=$u['id']?>"><?=csrfField()?><button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="return confirm('Delete?')"><i class="bi bi-trash" style="font-size:.7rem"></i></button></form><?php endif;?>
    <?php endif;?>
  </td>
</tr><?php endforeach;?></tbody></table></div></div></div></div>
</div>

<!-- Danger Zone -->
<?php if(isSuperAdmin()):?>
<div class="card border-0 rounded-3 border-danger" style="border:1px solid #dc3545!important">
  <div class="card-header bg-danger bg-opacity-10 border-0"><h6 class="fw-semibold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h6></div>
  <div class="card-body d-flex flex-wrap gap-2">
    <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="clear_audit_logs"><button class="btn btn-outline-danger btn-sm" onclick="return confirm('Clear ALL audit logs? This cannot be undone.')"><i class="bi bi-trash me-1"></i>Clear Audit Logs</button></form>
  </div>
</div>
<?php endif;?>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 rounded-3">
  <div class="modal-header"><h6 class="modal-title fw-semibold">Edit User</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <form method="POST" id="editUserForm"><?=csrfField()?><input type="hidden" name="form_action" value="edit_user"><input type="hidden" name="edit_user_id" id="eu-id">
    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="edit_user_name" id="eu-name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Role</label><select name="edit_user_role" id="eu-role" class="form-select"><option value="teacher">Teacher</option><option value="office">Office</option><option value="admin">Admin</option><?php if(isSuperAdmin()):?><option value="super_admin">Super Admin</option><?php endif;?></select></div>
    <div class="mb-3"><label class="form-label">Status</label><select name="edit_user_active" id="eu-active" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
    <button class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Update User</button>
    </form>
  </div>
</div></div></div>

<script>
document.querySelectorAll('.btn-edit-user').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('eu-id').value = this.dataset.id;
    document.getElementById('eu-name').value = this.dataset.name;
    document.getElementById('eu-role').value = this.dataset.role;
    document.getElementById('eu-active').value = this.dataset.active;
  });
});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
