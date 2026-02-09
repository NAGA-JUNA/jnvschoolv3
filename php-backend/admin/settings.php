<?php
require_once __DIR__.'/../includes/auth.php';
requireRole(['super_admin']);
$db=getDB();

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){setFlash('error','Invalid.');}else{
    foreach($_POST['settings']??[] as $key=>$val){
        $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$key,$val,$val]);
    }
    auditLog('update_settings','settings',0);
    setFlash('success','Settings saved.');
    header('Location: /admin/settings.php');exit;
    }
}

// Create user
if(isset($_POST['create_user'])){
    if(!verifyCsrf()){setFlash('error','Invalid.');}else{
    $name=trim($_POST['user_name']??'');$email=trim($_POST['user_email']??'');$pass=$_POST['user_password']??'';$role=$_POST['user_role']??'teacher';
    if($name&&$email&&$pass){
        $hash=password_hash($pass,PASSWORD_DEFAULT);
        $db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")->execute([$name,$email,$hash,$role]);
        auditLog('create_user','user',(int)$db->lastInsertId());
        setFlash('success','User created.');
    }else{setFlash('error','All fields required.');}
    header('Location: /admin/settings.php');exit;
    }
}

$settings=$db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$users=$db->query("SELECT id,name,email,role,is_active,last_login FROM users ORDER BY name")->fetchAll();

$pageTitle='Settings';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-4">Settings</h3>

<div class="card mb-4"><div class="card-header">School Info</div><div class="card-body">
<form method="POST" class="row g-3">
<?= csrfField() ?>
<div class="col-md-6"><label class="form-label">School Name</label><input type="text" name="settings[school_name]" class="form-control" value="<?= e($settings['school_name']??'') ?>"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="settings[school_email]" class="form-control" value="<?= e($settings['school_email']??'') ?>"></div>
<div class="col-md-4"><label class="form-label">Phone</label><input type="text" name="settings[school_phone]" class="form-control" value="<?= e($settings['school_phone']??'') ?>"></div>
<div class="col-md-4"><label class="form-label">Academic Year</label><input type="text" name="settings[academic_year]" class="form-control" value="<?= e($settings['academic_year']??'') ?>"></div>
<div class="col-12"><label class="form-label">Address</label><textarea name="settings[school_address]" class="form-control" rows="2"><?= e($settings['school_address']??'') ?></textarea></div>
<div class="col-12"><button class="btn btn-primary">Save Settings</button></div>
</form></div></div>

<div class="card mb-4"><div class="card-header">Create New User</div><div class="card-body">
<form method="POST" class="row g-2">
<?= csrfField() ?>
<input type="hidden" name="create_user" value="1">
<div class="col-md-3"><input type="text" name="user_name" class="form-control" placeholder="Name" required></div>
<div class="col-md-3"><input type="email" name="user_email" class="form-control" placeholder="Email" required></div>
<div class="col-md-2"><input type="password" name="user_password" class="form-control" placeholder="Password" required></div>
<div class="col-md-2"><select name="user_role" class="form-select"><option value="admin">Admin</option><option value="teacher">Teacher</option><option value="office">Office</option></select></div>
<div class="col-md-2"><button class="btn btn-success w-100">Create</button></div>
</form></div></div>

<div class="card"><div class="card-header">Users</div><div class="card-body p-0">
<table class="table table-sm mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Last Login</th></tr></thead>
<tbody><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= $u['is_active']?'Yes':'No' ?></td><td><?= e($u['last_login']??'Never') ?></td></tr><?php endforeach; ?></tbody>
</table></div></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
