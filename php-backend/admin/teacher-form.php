<?php
$pageTitle='Teacher Form';require_once __DIR__.'/../includes/auth.php';requireAdmin();$db=getDB();
$id=(int)($_GET['id']??0);$teacher=null;
if($id){$stmt=$db->prepare("SELECT * FROM teachers WHERE id=?");$stmt->execute([$id]);$teacher=$stmt->fetch();if(!$teacher){setFlash('error','Not found.');header('Location: /admin/teachers.php');exit;}$pageTitle='Edit Teacher';}else{$pageTitle='Add Teacher';}
if($_SERVER['REQUEST_METHOD']==='POST'){if(!verifyCsrf()){setFlash('error','Invalid.');header("Location:/admin/teacher-form.php?id=$id");exit;}
$d=['employee_id'=>trim($_POST['employee_id']??''),'name'=>trim($_POST['name']??''),'email'=>trim($_POST['email']??''),'phone'=>trim($_POST['phone']??''),'subject'=>trim($_POST['subject']??''),'qualification'=>trim($_POST['qualification']??''),'experience_years'=>(int)($_POST['experience_years']??0),'dob'=>$_POST['dob']?:null,'gender'=>$_POST['gender']?:null,'address'=>trim($_POST['address']??''),'joining_date'=>$_POST['joining_date']?:null,'status'=>$_POST['status']??'active'];
if(!$d['employee_id']||!$d['name'])setFlash('error','Employee ID and Name required.');
else{try{if($id){$db->prepare("UPDATE teachers SET employee_id=?,name=?,email=?,phone=?,subject=?,qualification=?,experience_years=?,dob=?,gender=?,address=?,joining_date=?,status=? WHERE id=?")->execute([...array_values($d),$id]);auditLog('update_teacher','teacher',$id);setFlash('success','Updated.');}else{$db->prepare("INSERT INTO teachers (employee_id,name,email,phone,subject,qualification,experience_years,dob,gender,address,joining_date,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")->execute(array_values($d));auditLog('create_teacher','teacher',(int)$db->lastInsertId());setFlash('success','Added.');if($d['email']){try{$db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'teacher')")->execute([$d['name'],$d['email'],password_hash('Teacher@123',PASSWORD_DEFAULT)]);}catch(Exception $e){}}}header('Location: /admin/teachers.php');exit;}catch(PDOException $e){if($e->getCode()==23000)setFlash('error','Employee ID exists.');else setFlash('error',$e->getMessage());}}}
$t=$teacher??[];require_once __DIR__.'/../includes/header.php';?>
<div class="card border-0 rounded-3"><div class="card-body"><form method="POST"><?=csrfField()?><div class="row g-3">
<div class="col-md-4"><label class="form-label">Employee ID *</label><input type="text" name="employee_id" class="form-control" required value="<?=e($t['employee_id']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" required value="<?=e($t['name']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><?php foreach(['male','female','other'] as $g):?><option value="<?=$g?>" <?=($t['gender']??'')===$g?'selected':''?>><?=ucfirst($g)?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?=e($t['email']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" value="<?=e($t['phone']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control" value="<?=e($t['subject']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Qualification</label><input type="text" name="qualification" class="form-control" value="<?=e($t['qualification']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Experience (yrs)</label><input type="number" name="experience_years" class="form-control" value="<?=e($t['experience_years']??0)?>"></div>
<div class="col-md-4"><label class="form-label">DOB</label><input type="date" name="dob" class="form-control" value="<?=e($t['dob']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control" value="<?=e($t['joining_date']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach(['active','inactive','resigned','retired'] as $st):?><option value="<?=$st?>" <?=($t['status']??'active')===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select></div>
<div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?=e($t['address']??'')?></textarea></div>
<div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?=$id?'Update':'Add'?></button><a href="/admin/teachers.php" class="btn btn-outline-secondary">Cancel</a></div>
</div></form></div></div>
<?php require_once __DIR__.'/../includes/footer.php';?>
