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

if($action==='favicon_upload'){
  if(!empty($_FILES['school_favicon']['name'])&&$_FILES['school_favicon']['error']===UPLOAD_ERR_OK){
    $ext=strtolower(pathinfo($_FILES['school_favicon']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['ico','png','svg','jpg','jpeg'])){
      @mkdir(__DIR__.'/../uploads/logo',0755,true);
      $fname='favicon.'.$ext;move_uploaded_file($_FILES['school_favicon']['tmp_name'],__DIR__.'/../uploads/logo/'.$fname);
      $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('school_favicon',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$fname,$fname]);
      auditLog('update_favicon','settings');setFlash('success','Favicon updated.');
    }else setFlash('error','Favicon must be ICO, PNG, SVG or JPG.');
  }
}

if($action==='social_links'){
  foreach(['social_facebook','social_twitter','social_instagram','social_youtube','social_linkedin'] as $k){
    $v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_social','settings');setFlash('success','Social links updated.');
}

if($action==='about_content'){
  foreach(['about_history','about_vision','about_mission'] as $k){
    $v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_about','settings');setFlash('success','About page content updated.');
}

if($action==='core_values'){
  for($i=1;$i<=4;$i++){
    foreach(['title','desc'] as $f){
      $k="core_value_{$i}_{$f}";$v=trim($_POST[$k]??'');
      $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
    }
  }auditLog('update_core_values','settings');setFlash('success','Core values updated.');
}

if($action==='sms_whatsapp'){
  foreach(['whatsapp_api_number','sms_gateway_key'] as $k){
    $v=trim($_POST[$k]??'');$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_sms_config','settings');setFlash('success','SMS/WhatsApp config updated.');
}

if($action==='popup_ad'){
  $adActive=isset($_POST['popup_ad_active'])?'1':'0';
  $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('popup_ad_active',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$adActive,$adActive]);
  if(!empty($_FILES['popup_ad_image']['name'])&&$_FILES['popup_ad_image']['error']===UPLOAD_ERR_OK){
    $ext=strtolower(pathinfo($_FILES['popup_ad_image']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['jpg','jpeg','png','webp','gif'])){
      @mkdir(__DIR__.'/../uploads/ads',0755,true);
      $fname='popup_ad.'.$ext;move_uploaded_file($_FILES['popup_ad_image']['tmp_name'],__DIR__.'/../uploads/ads/'.$fname);
      $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('popup_ad_image',?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$fname,$fname]);
    }else setFlash('error','Ad image must be JPG, PNG, WebP or GIF.');
  }
  auditLog('update_popup_ad','settings');setFlash('success','Popup ad settings updated.');
}

if($action==='feature_access'&&isSuperAdmin()){
  $features=['feature_admissions','feature_gallery','feature_events','feature_slider','feature_notifications','feature_reports','feature_audit_logs'];
  foreach($features as $k){
    $v=isset($_POST[$k])?'1':'0';
    $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$v,$v]);
  }auditLog('update_feature_access','settings');setFlash('success','Feature access updated.');
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
try{$totalStudents=$db->query("SELECT COUNT(*) FROM students")->fetchColumn();}catch(Exception $e){$totalStudents=0;}
try{$activeStudents=$db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();}catch(Exception $e){$activeStudents=0;}
try{$totalTeachers=$db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();}catch(Exception $e){$totalTeachers=0;}
try{$activeTeachers=$db->query("SELECT COUNT(*) FROM teachers WHERE is_active=1")->fetchColumn();}catch(Exception $e){$activeTeachers=0;}
$totalUsers=count($users);
try{$totalNotifications=$db->query("SELECT COUNT(*) FROM notifications")->fetchColumn();}catch(Exception $e){$totalNotifications=0;}
try{$totalEvents=$db->query("SELECT COUNT(*) FROM events")->fetchColumn();}catch(Exception $e){$totalEvents=0;}
try{$mysqlVersion=$db->query("SELECT VERSION()")->fetchColumn();}catch(Exception $e){$mysqlVersion='N/A';}
try{$dbTablesCount=$db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE()")->fetchColumn();}catch(Exception $e){$dbTablesCount='N/A';}
try{$dbSize=$db->query("SELECT ROUND(SUM(data_length + index_length)/1024/1024, 2) FROM information_schema.tables WHERE table_schema=DATABASE()")->fetchColumn();}catch(Exception $e){$dbSize='N/A';}
require_once __DIR__.'/../includes/header.php';$s=$settings;?>

<!-- Tab Navigation -->
<ul class="nav nav-pills mb-4 flex-nowrap overflow-auto" id="settingsTabs" role="tablist" style="gap:.5rem;">
  <li class="nav-item" role="presentation">
    <button class="nav-link active d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="general-tab" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
      <i class="bi bi-building"></i><span class="d-none d-md-inline">General</span>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="appearance-tab" data-bs-toggle="pill" data-bs-target="#tab-appearance" type="button" role="tab">
      <i class="bi bi-palette"></i><span class="d-none d-md-inline">Appearance</span>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="content-tab" data-bs-toggle="pill" data-bs-target="#tab-content" type="button" role="tab">
      <i class="bi bi-file-text"></i><span class="d-none d-md-inline">Content</span>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="social-tab" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">
      <i class="bi bi-share"></i><span class="d-none d-md-inline">Social & SMS</span>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="popup-tab" data-bs-toggle="pill" data-bs-target="#tab-popup" type="button" role="tab">
      <i class="bi bi-megaphone"></i><span class="d-none d-md-inline">Popup Ad</span>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="users-tab" data-bs-toggle="pill" data-bs-target="#tab-users" type="button" role="tab">
      <i class="bi bi-people"></i><span class="d-none d-md-inline">Users</span>
    </button>
  </li>
  <?php if(isSuperAdmin()):?>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="access-tab" data-bs-toggle="pill" data-bs-target="#tab-access" type="button" role="tab">
      <i class="bi bi-shield-lock"></i><span class="d-none d-md-inline">Access Control</span>
    </button>
  </li>
  <?php endif;?>
  <li class="nav-item" role="presentation">
    <button class="nav-link d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="system-tab" data-bs-toggle="pill" data-bs-target="#tab-system" type="button" role="tab">
      <i class="bi bi-cpu"></i><span class="d-none d-md-inline">System</span>
    </button>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="settingsTabContent">

<!-- ========== GENERAL TAB ========== -->
<div class="tab-pane fade show active" id="tab-general" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-building me-2"></i>School Information</h6></div><div class="card-body">
        <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="settings">
        <input type="hidden" name="primary_color" value="<?=e($s['primary_color']??'#1e40af')?>">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">School Name</label><input type="text" name="school_name" class="form-control" value="<?=e($s['school_name']??'')?>"></div>
          <div class="col-md-6"><label class="form-label">Short Name</label><input type="text" name="school_short_name" class="form-control" value="<?=e($s['school_short_name']??'')?>"></div>
          <div class="col-12"><label class="form-label">Tagline</label><input type="text" name="school_tagline" class="form-control" value="<?=e($s['school_tagline']??'')?>"></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="school_email" class="form-control" value="<?=e($s['school_email']??'')?>"></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="school_phone" class="form-control" value="<?=e($s['school_phone']??'')?>"></div>
          <div class="col-12"><label class="form-label">Address</label><textarea name="school_address" class="form-control" rows="2"><?=e($s['school_address']??'')?></textarea></div>
          <div class="col-md-6"><label class="form-label">Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?=e($s['academic_year']??'')?>"></div>
          <div class="col-md-6"><label class="form-label">Admissions</label><select name="admission_open" class="form-select"><option value="1" <?=($s['admission_open']??'1')==='1'?'selected':''?>>Open</option><option value="0" <?=($s['admission_open']??'1')==='0'?'selected':''?>>Closed</option></select></div>
          <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div>
        </div></form>
      </div></div>
    </div>
    <div class="col-lg-4">
      <div class="card border-0 rounded-3 mb-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-image me-2"></i>School Logo</h6></div><div class="card-body">
        <form method="POST" enctype="multipart/form-data"><?=csrfField()?><input type="hidden" name="form_action" value="logo_upload">
        <?php if(!empty($s['school_logo'])):?><div class="mb-3 text-center"><img src="/uploads/logo/<?=e($s['school_logo'])?>" alt="School Logo" style="max-height:80px" class="rounded"></div><?php endif;?>
        <input type="file" name="school_logo" class="form-control form-control-sm mb-2" accept=".jpg,.jpeg,.png,.webp,.svg">
        <button class="btn btn-success btn-sm w-100"><i class="bi bi-upload me-1"></i>Upload Logo</button>
        </form>
      </div></div>

      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-window-desktop me-2"></i>Favicon</h6></div><div class="card-body">
        <form method="POST" enctype="multipart/form-data"><?=csrfField()?><input type="hidden" name="form_action" value="favicon_upload">
        <?php if(!empty($s['school_favicon'])):?><div class="mb-3 text-center"><img src="/uploads/logo/<?=e($s['school_favicon'])?>" alt="Favicon" style="max-height:48px" class="rounded"></div><?php endif;?>
        <input type="file" name="school_favicon" class="form-control form-control-sm mb-2" accept=".ico,.png,.svg,.jpg,.jpeg">
        <small class="text-muted d-block mb-2" style="font-size:.7rem">Recommended: 32×32 or 64×64px. ICO, PNG, SVG, JPG.</small>
        <button class="btn btn-success btn-sm w-100"><i class="bi bi-upload me-1"></i>Upload Favicon</button>
        </form>
      </div></div>
    </div>
  </div>
</div>

<!-- ========== APPEARANCE TAB ========== -->
<div class="tab-pane fade" id="tab-appearance" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-palette me-2"></i>Theme Color</h6></div><div class="card-body">
        <form method="POST" id="colorForm"><?=csrfField()?><input type="hidden" name="form_action" value="settings">
        <!-- Hidden fields to preserve other settings -->
        <input type="hidden" name="school_name" value="<?=e($s['school_name']??'')?>">
        <input type="hidden" name="school_short_name" value="<?=e($s['school_short_name']??'')?>">
        <input type="hidden" name="school_tagline" value="<?=e($s['school_tagline']??'')?>">
        <input type="hidden" name="school_email" value="<?=e($s['school_email']??'')?>">
        <input type="hidden" name="school_phone" value="<?=e($s['school_phone']??'')?>">
        <input type="hidden" name="school_address" value="<?=e($s['school_address']??'')?>">
        <input type="hidden" name="academic_year" value="<?=e($s['academic_year']??'')?>">
        <input type="hidden" name="admission_open" value="<?=e($s['admission_open']??'1')?>">

        <p class="text-muted mb-3" style="font-size:.85rem">Choose a primary color for your school's website theme. This color is applied to the navbar, buttons, links, and accents across all pages.</p>

        <div class="d-flex align-items-center gap-3 mb-4">
          <input type="color" name="primary_color" id="primaryColorPicker" class="form-control form-control-color border-2" value="<?=e($s['primary_color']??'#1e40af')?>" style="width:64px;height:64px;cursor:pointer;">
          <div>
            <div class="fw-semibold" style="font-size:.9rem">Selected Color</div>
            <code id="colorHexDisplay" class="text-muted"><?=e($s['primary_color']??'#1e40af')?></code>
          </div>
        </div>

        <label class="form-label fw-semibold mb-2">Preset Swatches</label>
        <div class="d-flex flex-wrap gap-2 mb-4">
          <?php
          $presets = [
            'Navy' => '#1e40af', 'Emerald' => '#059669', 'Purple' => '#7c3aed', 'Rose' => '#e11d48',
            'Amber' => '#d97706', 'Slate' => '#334155', 'Teal' => '#0d9488', 'Indigo' => '#4f46e5'
          ];
          foreach ($presets as $label => $hex): ?>
          <button type="button" class="btn p-0 border-2 rounded-3 theme-swatch d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:<?=$hex?>;min-width:48px;" onclick="selectColor('<?=$hex?>')" title="<?=$label?>">
            <span class="text-white fw-bold" style="font-size:.55rem;text-shadow:0 1px 2px rgba(0,0,0,.5)"><?=$label?></span>
          </button>
          <?php endforeach; ?>
        </div>

        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Theme Color</button>
        </form>
      </div></div>
    </div>

    <!-- Live Preview -->
    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-eye me-2"></i>Live Preview</h6></div><div class="card-body">
        <p class="text-muted mb-3" style="font-size:.8rem">See how the selected color looks on your website elements in real-time.</p>

        <div id="colorPreview" class="border rounded-3 overflow-hidden">
          <!-- Preview Navbar -->
          <div class="preview-navbar d-flex align-items-center px-3 py-2" style="background:<?=e($s['primary_color']??'#1e40af')?>">
            <i class="bi bi-mortarboard-fill text-white me-2"></i>
            <span class="text-white fw-semibold" style="font-size:.85rem"><?=e($s['school_name']??'School Name')?></span>
            <div class="ms-auto d-flex gap-2">
              <span class="text-white-50" style="font-size:.75rem">Home</span>
              <span class="text-white-50" style="font-size:.75rem">About</span>
              <span class="text-white-50" style="font-size:.75rem">Contact</span>
            </div>
          </div>

          <!-- Preview Body -->
          <div class="p-3 bg-white">
            <h6 class="preview-heading fw-bold mb-2" style="font-size:.9rem;color:<?=e($s['primary_color']??'#1e40af')?>">Welcome to Our School</h6>
            <p class="text-muted mb-3" style="font-size:.75rem">This is a sample paragraph to show how body text looks alongside the theme color elements.</p>
            <div class="d-flex gap-2 mb-3">
              <button class="btn btn-sm preview-btn text-white" style="background:<?=e($s['primary_color']??'#1e40af')?>;border:none;font-size:.75rem">Primary Button</button>
              <button class="btn btn-sm btn-outline-primary preview-btn-outline" style="color:<?=e($s['primary_color']??'#1e40af')?>;border-color:<?=e($s['primary_color']??'#1e40af')?>;font-size:.75rem">Outline Button</button>
            </div>
            <div class="d-flex gap-3" style="font-size:.75rem">
              <a href="#" class="preview-link text-decoration-none" style="color:<?=e($s['primary_color']??'#1e40af')?>"><i class="bi bi-link-45deg me-1"></i>Sample Link</a>
              <a href="#" class="preview-link text-decoration-none" style="color:<?=e($s['primary_color']??'#1e40af')?>"><i class="bi bi-arrow-right me-1"></i>Learn More</a>
            </div>
          </div>

          <!-- Preview Footer -->
          <div class="preview-footer px-3 py-2" style="background:<?=e($s['primary_color']??'#1e40af')?>22">
            <div class="d-flex justify-content-between align-items-center">
              <span style="font-size:.7rem;color:<?=e($s['primary_color']??'#1e40af')?>">© 2025 <?=e($s['school_short_name']??'School')?></span>
              <div class="d-flex gap-2">
                <i class="bi bi-facebook preview-social-icon" style="font-size:.75rem;color:<?=e($s['primary_color']??'#1e40af')?>"></i>
                <i class="bi bi-instagram preview-social-icon" style="font-size:.75rem;color:<?=e($s['primary_color']??'#1e40af')?>"></i>
                <i class="bi bi-youtube preview-social-icon" style="font-size:.75rem;color:<?=e($s['primary_color']??'#1e40af')?>"></i>
              </div>
            </div>
          </div>
        </div>
      </div></div>
    </div>
  </div>
</div>

<!-- ========== CONTENT TAB ========== -->
<div class="tab-pane fade" id="tab-content" role="tabpanel">
  <div class="card border-0 rounded-3 mb-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-file-text me-2"></i>About Page Content</h6></div><div class="card-body">
    <p class="text-muted mb-3" style="font-size:.8rem">This content appears on the public About Us page. Leave empty to use default placeholder text.</p>
    <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="about_content"><div class="row g-3">
    <div class="col-12"><label class="form-label fw-semibold">School History</label><textarea name="about_history" class="form-control" rows="4" placeholder="Tell the story of your school..."><?=e($s['about_history']??'')?></textarea></div>
    <div class="col-md-6"><label class="form-label fw-semibold">Vision Statement</label><textarea name="about_vision" class="form-control" rows="3" placeholder="Your school's vision..."><?=e($s['about_vision']??'')?></textarea></div>
    <div class="col-md-6"><label class="form-label fw-semibold">Mission Statement</label><textarea name="about_mission" class="form-control" rows="3" placeholder="Your school's mission..."><?=e($s['about_mission']??'')?></textarea></div>
    <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save About Content</button></div>
    </div></form>
  </div></div>

  <!-- Core Values -->
  <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-trophy me-2"></i>Core Values</h6></div><div class="card-body">
    <p class="text-muted mb-3" style="font-size:.8rem">Edit the 4 core values displayed on the About Us page. Leave empty to use defaults.</p>
    <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="core_values"><div class="row g-3">
    <?php
    $defaultValues = [
      1 => ['Excellence', 'We strive for the highest standards in academics, character, and personal growth.'],
      2 => ['Integrity', 'We foster honesty, transparency, and ethical behavior in all our actions.'],
      3 => ['Innovation', 'We embrace creativity and modern teaching methods to inspire learning.'],
      4 => ['Community', 'We build a supportive, inclusive environment where everyone belongs.'],
    ];
    for ($i = 1; $i <= 4; $i++):
      $defTitle = $defaultValues[$i][0];
      $defDesc = $defaultValues[$i][1];
    ?>
    <div class="col-md-6">
      <div class="bg-light rounded-3 p-3">
        <label class="form-label fw-semibold mb-1">Value <?=$i?> — Title</label>
        <input type="text" name="core_value_<?=$i?>_title" class="form-control form-control-sm mb-2" value="<?=e($s['core_value_'.$i.'_title']??$defTitle)?>" placeholder="<?=$defTitle?>">
        <label class="form-label fw-semibold mb-1">Description</label>
        <textarea name="core_value_<?=$i?>_desc" class="form-control form-control-sm" rows="2" placeholder="<?=$defDesc?>"><?=e($s['core_value_'.$i.'_desc']??$defDesc)?></textarea>
      </div>
    </div>
    <?php endfor; ?>
    <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Core Values</button></div>
    </div></form>
  </div></div>
</div>

<!-- ========== SOCIAL & SMS TAB ========== -->
<div class="tab-pane fade" id="tab-social" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-share me-2"></i>Social Media Links</h6></div><div class="card-body">
        <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="social_links"><div class="row g-2">
        <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-facebook"></i></span><input type="url" name="social_facebook" class="form-control" placeholder="Facebook URL" value="<?=e($s['social_facebook']??'')?>"></div></div>
        <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-twitter-x"></i></span><input type="url" name="social_twitter" class="form-control" placeholder="Twitter/X URL" value="<?=e($s['social_twitter']??'')?>"></div></div>
        <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-instagram"></i></span><input type="url" name="social_instagram" class="form-control" placeholder="Instagram URL" value="<?=e($s['social_instagram']??'')?>"></div></div>
        <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-youtube"></i></span><input type="url" name="social_youtube" class="form-control" placeholder="YouTube URL" value="<?=e($s['social_youtube']??'')?>"></div></div>
        <div class="col-12"><div class="input-group input-group-sm"><span class="input-group-text"><i class="bi bi-linkedin"></i></span><input type="url" name="social_linkedin" class="form-control" placeholder="LinkedIn URL" value="<?=e($s['social_linkedin']??'')?>"></div></div>
        <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Links</button></div>
        </div></form>
      </div></div>
    </div>

    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-whatsapp me-2"></i>SMS / WhatsApp</h6></div><div class="card-body">
        <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="sms_whatsapp"><div class="row g-3">
        <div class="col-12"><label class="form-label">WhatsApp API Number</label><input type="text" name="whatsapp_api_number" class="form-control" placeholder="+91 9876543210" value="<?=e($s['whatsapp_api_number']??'')?>"></div>
        <div class="col-12"><label class="form-label">SMS Gateway API Key</label><input type="text" name="sms_gateway_key" class="form-control" placeholder="API key from your SMS provider" value="<?=e($s['sms_gateway_key']??'')?>"></div>
        <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Config</button></div>
        </div></form>
      </div></div>
    </div>
  </div>
</div>

<!-- ========== POPUP AD TAB ========== -->
<div class="tab-pane fade" id="tab-popup" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-megaphone me-2"></i>Popup Advertisement</h6></div><div class="card-body">
        <p class="text-muted mb-3" style="font-size:.8rem">Upload a single advertisement image that will appear as a popup when visitors first open the homepage. The popup appears once per day per visitor.</p>
        <form method="POST" enctype="multipart/form-data"><?=csrfField()?><input type="hidden" name="form_action" value="popup_ad">
        <?php if(!empty($s['popup_ad_image'])):?><div class="mb-3 text-center"><img src="/uploads/ads/<?=e($s['popup_ad_image'])?>" alt="Ad Preview" style="max-height:150px;border-radius:8px" class="border"></div><?php endif;?>
        <div class="mb-3">
          <label class="form-label">Ad Image</label>
          <input type="file" name="popup_ad_image" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,.gif">
          <small class="text-muted">Recommended: 600×800px or similar. JPG, PNG, WebP, GIF.</small>
        </div>
        <div class="mb-3 d-flex align-items-center justify-content-between bg-light rounded-3 p-3">
          <div>
            <div class="fw-semibold" style="font-size:.85rem">Enable Popup</div>
            <small class="text-muted" style="font-size:.7rem">Show ad popup on homepage</small>
          </div>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="popup_ad_active" <?=($s['popup_ad_active']??'0')==='1'?'checked':''?> style="width:2.5em;height:1.25em;">
          </div>
        </div>
        <button class="btn btn-primary btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Save Ad Settings</button>
        </form>
      </div></div>
    </div>
  </div>
</div>

<!-- ========== USERS TAB ========== -->
<div class="tab-pane fade" id="tab-users" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-5">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-person-plus me-2"></i>Create User</h6></div><div class="card-body"><form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="create_user">
      <div class="mb-2"><input type="text" name="user_name" class="form-control form-control-sm" placeholder="Name" required></div>
      <div class="mb-2"><input type="email" name="user_email" class="form-control form-control-sm" placeholder="Email" required></div>
      <div class="mb-2"><input type="password" name="user_password" class="form-control form-control-sm" placeholder="Password" required minlength="6"></div>
      <div class="mb-2"><select name="user_role" class="form-select form-select-sm"><option value="teacher">Teacher</option><option value="office">Office</option><option value="admin">Admin</option><?php if(isSuperAdmin()):?><option value="super_admin">Super Admin</option><?php endif;?></select></div>
      <button class="btn btn-success btn-sm w-100"><i class="bi bi-person-plus me-1"></i>Create User</button></form></div></div>
    </div>

    <div class="col-lg-7">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0">Users (<?=count($users)?>)</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="table-light"><tr><th>Name</th><th>Role</th><th>Status</th><th>Last Login</th><th></th></tr></thead><tbody>
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
      </tr><?php endforeach;?></tbody></table></div></div></div>
    </div>
  </div>
</div>

<!-- ========== ACCESS CONTROL TAB ========== -->
<?php if(isSuperAdmin()):?>
<div class="tab-pane fade" id="tab-access" role="tabpanel">
  <div class="card border-0 rounded-3">
    <div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-shield-lock me-2"></i>Feature Access Control <span class="badge bg-warning text-dark ms-2" style="font-size:.6rem">Super Admin</span></h6></div>
    <div class="card-body">
      <p class="text-muted mb-3" style="font-size:.8rem">Toggle modules ON/OFF for non-super-admin users. Disabled modules will be hidden from the sidebar and inaccessible.</p>
      <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="feature_access">
      <div class="row g-3">
        <?php
        $featureList = [
          'feature_admissions' => ['Admissions', 'bi-file-earmark-plus-fill', 'Manage admission applications'],
          'feature_gallery' => ['Gallery', 'bi-images', 'Photo gallery management'],
          'feature_events' => ['Events', 'bi-calendar-event-fill', 'School events calendar'],
          'feature_slider' => ['Home Slider', 'bi-collection-play-fill', 'Homepage slider management'],
          'feature_notifications' => ['Notifications', 'bi-bell-fill', 'Notification management'],
          'feature_reports' => ['Reports', 'bi-file-earmark-bar-graph-fill', 'Reports & exports'],
          'feature_audit_logs' => ['Audit Logs', 'bi-clock-history', 'System activity logs'],
        ];
        foreach ($featureList as $key => [$label, $icon, $desc]):
          $checked = getSetting($key, '1') === '1';
        ?>
        <div class="col-md-6">
          <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi <?=$icon?> text-primary"></i>
              <div>
                <div class="fw-semibold" style="font-size:.85rem"><?=$label?></div>
                <small class="text-muted" style="font-size:.7rem"><?=$desc?></small>
              </div>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="<?=$key?>" id="<?=$key?>" <?=$checked?'checked':''?> style="width:2.5em;height:1.25em;">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <div class="col-12"><button class="btn btn-primary btn-sm"><i class="bi bi-shield-check me-1"></i>Save Feature Access</button></div>
      </div>
      </form>
    </div>
  </div>
</div>
<?php endif;?>

<!-- ========== SYSTEM TAB ========== -->
<div class="tab-pane fade" id="tab-system" role="tabpanel">
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 rounded-3"><div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-cpu me-2"></i>System Information</h6></div><div class="card-body">
        <!-- Server Info -->
        <h6 class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;"><i class="bi bi-hdd-rack me-1"></i>Server</h6>
        <div class="row g-2 mb-3">
          <div class="col-6"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">PHP</small><strong style="font-size:.8rem"><?=phpversion()?></strong></div></div>
          <div class="col-6"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">MySQL</small><strong style="font-size:.8rem"><?=e($mysqlVersion)?></strong></div></div>
          <div class="col-12"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Server Software</small><strong style="font-size:.75rem"><?=e(explode(' ', $_SERVER['SERVER_SOFTWARE']??'N/A')[0])?></strong></div></div>
        </div>

        <!-- Database -->
        <h6 class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;"><i class="bi bi-database me-1"></i>Database</h6>
        <div class="row g-2 mb-3">
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Tables</small><strong style="font-size:.85rem"><?=$dbTablesCount?></strong></div></div>
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Size</small><strong style="font-size:.85rem"><?=$dbSize?> MB</strong></div></div>
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">DB Name</small><strong style="font-size:.7rem"><?=e(DB_NAME)?></strong></div></div>
        </div>

        <!-- Application Stats -->
        <h6 class="text-muted fw-semibold" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;"><i class="bi bi-bar-chart me-1"></i>Application</h6>
        <div class="mb-2">
          <div class="d-flex justify-content-between" style="font-size:.75rem"><span>Students</span><span class="fw-semibold"><?=$activeStudents?> / <?=$totalStudents?> active</span></div>
          <div class="progress" style="height:6px"><div class="progress-bar bg-primary" style="width:<?=$totalStudents?round($activeStudents/$totalStudents*100,0):0?>%"></div></div>
        </div>
        <div class="mb-2">
          <div class="d-flex justify-content-between" style="font-size:.75rem"><span>Teachers</span><span class="fw-semibold"><?=$activeTeachers?> / <?=$totalTeachers?> active</span></div>
          <div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:<?=$totalTeachers?round($activeTeachers/$totalTeachers*100,0):0?>%"></div></div>
        </div>
        <div class="row g-2 mt-1">
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Users</small><strong style="font-size:.85rem"><?=$totalUsers?></strong></div></div>
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Notices</small><strong style="font-size:.85rem"><?=$totalNotifications?></strong></div></div>
          <div class="col-4"><div class="bg-light rounded-3 p-2 text-center"><small class="text-muted d-block" style="font-size:.65rem">Events</small><strong style="font-size:.85rem"><?=$totalEvents?></strong></div></div>
        </div>

        <!-- Server Time -->
        <div class="mt-3 bg-light rounded-3 p-2 text-center">
          <small class="text-muted d-block" style="font-size:.65rem">Server Time</small>
          <strong style="font-size:.8rem"><i class="bi bi-clock me-1"></i><?=date('d M Y, h:i A T')?></strong>
        </div>
      </div></div>
    </div>

    <?php if(isSuperAdmin()):?>
    <div class="col-lg-6">
      <div class="card border-0 rounded-3 border-danger" style="border:1px solid #dc3545!important">
        <div class="card-header bg-danger bg-opacity-10 border-0"><h6 class="fw-semibold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h6></div>
        <div class="card-body d-flex flex-wrap gap-2">
          <form method="POST"><?=csrfField()?><input type="hidden" name="form_action" value="clear_audit_logs"><button class="btn btn-outline-danger btn-sm" onclick="return confirm('Clear ALL audit logs? This cannot be undone.')"><i class="bi bi-trash me-1"></i>Clear Audit Logs</button></form>
        </div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>

</div><!-- end tab-content -->

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

<style>
.theme-swatch{cursor:pointer;transition:transform .15s,box-shadow .15s}
.theme-swatch:hover{transform:scale(1.1);box-shadow:0 0 0 3px rgba(0,0,0,.2)}
.nav-pills .nav-link{color:#6c757d;font-size:.8rem;font-weight:500;white-space:nowrap;transition:all .2s}
.nav-pills .nav-link.active{background:var(--theme-primary, #1e40af);color:#fff}
.nav-pills .nav-link:not(.active):hover{background:#f8f9fa;color:#333}
#settingsTabs{scrollbar-width:none;-ms-overflow-style:none}
#settingsTabs::-webkit-scrollbar{display:none}
</style>

<script>
// Color picker live preview
const colorPicker = document.getElementById('primaryColorPicker');
const hexDisplay = document.getElementById('colorHexDisplay');
const preview = document.getElementById('colorPreview');

function updatePreview(color) {
  if (!preview) return;
  // Navbar
  const navbar = preview.querySelector('.preview-navbar');
  if (navbar) navbar.style.background = color;
  // Heading
  const heading = preview.querySelector('.preview-heading');
  if (heading) heading.style.color = color;
  // Filled button
  const btn = preview.querySelector('.preview-btn');
  if (btn) btn.style.background = color;
  // Outline button
  const btnOutline = preview.querySelector('.preview-btn-outline');
  if (btnOutline) { btnOutline.style.color = color; btnOutline.style.borderColor = color; }
  // Links
  preview.querySelectorAll('.preview-link').forEach(l => l.style.color = color);
  // Footer
  const footer = preview.querySelector('.preview-footer');
  if (footer) footer.style.background = color + '22';
  footer?.querySelectorAll('span, i').forEach(el => el.style.color = color);
  // Hex display
  if (hexDisplay) hexDisplay.textContent = color;
}

if (colorPicker) {
  colorPicker.addEventListener('input', function() { updatePreview(this.value); });
}

function selectColor(hex) {
  if (colorPicker) { colorPicker.value = hex; updatePreview(hex); }
}

// Tab persistence via URL hash
document.querySelectorAll('#settingsTabs button[data-bs-toggle="pill"]').forEach(tab => {
  tab.addEventListener('shown.bs.tab', function(e) {
    window.location.hash = e.target.getAttribute('data-bs-target').replace('#tab-', '');
  });
});

// Activate tab from hash on page load
(function() {
  const hash = window.location.hash.replace('#', '');
  if (hash) {
    const tabBtn = document.querySelector('#settingsTabs button[data-bs-target="#tab-' + hash + '"]');
    if (tabBtn) {
      const tab = new bootstrap.Tab(tabBtn);
      tab.show();
    }
  }
})();

// Set hash before form submit for tab persistence
document.querySelectorAll('.tab-pane form').forEach(form => {
  form.addEventListener('submit', function() {
    const pane = this.closest('.tab-pane');
    if (pane) {
      const tabId = pane.id.replace('tab-', '');
      window.location.hash = tabId;
    }
  });
});

// Edit user modal
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
