<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db=getDB();
$error='';$success='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid.';}else{
    $class=$_POST['class']??'';$date=$_POST['date']??date('Y-m-d');
    $students=$db->prepare("SELECT id,name,admission_no FROM students WHERE class=? AND status='active' ORDER BY name");
    $students->execute([$class]);$list=$students->fetchAll();
    if(isset($_POST['attendance'])){
        foreach($_POST['attendance'] as $sid=>$status){
            $db->prepare("INSERT INTO attendance (student_id,class,date,status,marked_by) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status=?,marked_by=?")
               ->execute([(int)$sid,$class,$date,$status,currentUserId(),$status,currentUserId()]);
        }
        auditLog('mark_attendance','attendance',0,"Class: $class, Date: $date");
        $success="Attendance saved for $class on $date.";
    }}
}

$classes=$db->query("SELECT DISTINCT class FROM students WHERE status='active' AND class IS NOT NULL ORDER BY class")->fetchAll(PDO::FETCH_COLUMN);
$selClass=$_POST['class']??'';$selDate=$_POST['date']??date('Y-m-d');
$studentList=[];
if($selClass){$stmt=$db->prepare("SELECT id,name,admission_no FROM students WHERE class=? AND status='active' ORDER BY name");$stmt->execute([$selClass]);$studentList=$stmt->fetchAll();}

// Get existing attendance for the selected date/class
$existing=[];
if($selClass&&$selDate){
    $stmt=$db->prepare("SELECT student_id,status FROM attendance WHERE class=? AND date=?");$stmt->execute([$selClass,$selDate]);
    foreach($stmt->fetchAll() as $a) $existing[$a['student_id']]=$a['status'];
}

$pageTitle='Attendance';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Mark Attendance</h3>
<?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<form method="POST" class="row g-2 mb-3">
<?= csrfField() ?>
<div class="col-md-3"><select name="class" class="form-select" required><option value="">Select Class</option><?php foreach($classes as $c): ?><option value="<?= e($c) ?>" <?= $selClass===$c?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><input type="date" name="date" class="form-control" value="<?= e($selDate) ?>"></div>
<div class="col-md-2"><button class="btn btn-outline-primary w-100">Load Students</button></div>
</form>

<?php if($studentList): ?>
<form method="POST">
<?= csrfField() ?>
<input type="hidden" name="class" value="<?= e($selClass) ?>">
<input type="hidden" name="date" value="<?= e($selDate) ?>">
<table class="table table-sm">
<thead><tr><th>#</th><th>Adm No</th><th>Name</th><th>Status</th></tr></thead>
<tbody>
<?php foreach($studentList as $i=>$s): $cur=$existing[$s['id']]??'present'; ?>
<tr><td><?= $i+1 ?></td><td><?= e($s['admission_no']) ?></td><td><?= e($s['name']) ?></td>
<td>
<select name="attendance[<?= $s['id'] ?>]" class="form-select form-select-sm" style="width:120px">
<option value="present" <?= $cur==='present'?'selected':'' ?>>Present</option>
<option value="absent" <?= $cur==='absent'?'selected':'' ?>>Absent</option>
<option value="late" <?= $cur==='late'?'selected':'' ?>>Late</option>
<option value="excused" <?= $cur==='excused'?'selected':'' ?>>Excused</option>
</select></td></tr>
<?php endforeach; ?>
</tbody></table>
<button class="btn btn-primary">Save Attendance</button>
</form>
<?php endif; ?>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
