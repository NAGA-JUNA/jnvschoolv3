<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();
$id=isset($_GET['id'])?(int)$_GET['id']:0;
$teacher=null;
if($id){$stmt=$db->prepare("SELECT * FROM teachers WHERE id=?");$stmt->execute([$id]);$teacher=$stmt->fetch();if(!$teacher){setFlash('error','Not found.');header('Location: /admin/teachers.php');exit;}}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
if(!verifyCsrf()){$error='Invalid request.';}else{
$data=['employee_id'=>trim($_POST['employee_id']??''),'name'=>trim($_POST['name']??''),'email'=>trim($_POST['email']??''),'phone'=>trim($_POST['phone']??''),'subject'=>trim($_POST['subject']??''),'qualification'=>trim($_POST['qualification']??''),'experience_years'=>(int)($_POST['experience_years']??0),'dob'=>$_POST['dob']??null,'gender'=>$_POST['gender']??null,'address'=>trim($_POST['address']??''),'joining_date'=>$_POST['joining_date']??null,'status'=>$_POST['status']??'active'];
if(!$data['employee_id']||!$data['name']){$error='Employee ID and Name required.';}else{
if($id){$db->prepare("UPDATE teachers SET employee_id=?,name=?,email=?,phone=?,subject=?,qualification=?,experience_years=?,dob=?,gender=?,address=?,joining_date=?,status=? WHERE id=?")->execute([...array_values($data),$id]);auditLog('update_teacher','teacher',$id);setFlash('success','Updated.');}
else{$db->prepare("INSERT INTO teachers (employee_id,name,email,phone,subject,qualification,experience_years,dob,gender,address,joining_date,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")->execute(array_values($data));auditLog('create_teacher','teacher',(int)$db->lastInsertId());setFlash('success','Added.');}
header('Location: /admin/teachers.php');exit;}}}
$t=$teacher??[];$pageTitle=$id?'Edit Teacher':'Add Teacher';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3"><?= $pageTitle ?></h3>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="POST" class="row g-3">
<?= csrfField() ?>
<div class="col-md-4"><label class="form-label">Employee ID *</label><input type="text" name="employee_id" class="form-control" required value="<?= e($t['employee_id']??'') ?>"></div>
<div class="col-md-4"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required value="<?= e($t['name']??'') ?>"></div>
<div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($t['email']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($t['phone']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control" value="<?= e($t['subject']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Qualification</label><input type="text" name="qualification" class="form-control" value="<?= e($t['qualification']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Experience (years)</label><input type="number" name="experience_years" class="form-control" value="<?= e($t['experience_years']??0) ?>"></div>
<div class="col-md-3"><label class="form-label">DOB</label><input type="date" name="dob" class="form-control" value="<?= e($t['dob']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option><option value="male" <?= ($t['gender']??'')==='male'?'selected':'' ?>>Male</option><option value="female" <?= ($t['gender']??'')==='female'?'selected':'' ?>>Female</option></select></div>
<div class="col-md-3"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control" value="<?= e($t['joining_date']??'') ?>"></div>
<div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?= ($t['status']??'active')==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= ($t['status']??'')==='inactive'?'selected':'' ?>>Inactive</option></select></div>
<div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= e($t['address']??'') ?></textarea></div>
<div class="col-12"><button class="btn btn-primary">Save</button> <a href="/admin/teachers.php" class="btn btn-outline-secondary">Cancel</a></div>
</form>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
