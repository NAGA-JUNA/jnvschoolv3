<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db=getDB();
$error='';$success='';

if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['save_marks'])){
    if(!verifyCsrf()){$error='Invalid.';}else{
    $examName=trim($_POST['exam_name']??'');$subject=trim($_POST['subject']??'');$maxMarks=(int)($_POST['max_marks']??100);
    foreach($_POST['marks']??[] as $sid=>$obtained){
        if($obtained==='') continue;
        $grade='';$pct=($obtained/$maxMarks)*100;
        if($pct>=90)$grade='A+';elseif($pct>=80)$grade='A';elseif($pct>=70)$grade='B+';elseif($pct>=60)$grade='B';elseif($pct>=50)$grade='C';elseif($pct>=40)$grade='D';else $grade='F';
        $db->prepare("INSERT INTO exam_results (student_id,exam_name,subject,max_marks,obtained_marks,grade,entered_by) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE obtained_marks=?,grade=?,entered_by=?")
           ->execute([(int)$sid,$examName,$subject,$maxMarks,(int)$obtained,$grade,currentUserId(),(int)$obtained,$grade,currentUserId()]);
    }
    auditLog('enter_marks','exam',0,"$examName - $subject");
    $success="Marks saved.";
    }}

$classes=$db->query("SELECT DISTINCT class FROM students WHERE status='active' AND class IS NOT NULL ORDER BY class")->fetchAll(PDO::FETCH_COLUMN);
$selClass=$_POST['class']??$_GET['class']??'';
$studentList=[];
if($selClass){$stmt=$db->prepare("SELECT id,name,admission_no FROM students WHERE class=? AND status='active' ORDER BY name");$stmt->execute([$selClass]);$studentList=$stmt->fetchAll();}

$pageTitle='Exam Marks';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Enter Exam Marks</h3>
<?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<form method="GET" class="row g-2 mb-3">
<div class="col-md-3"><select name="class" class="form-select"><option value="">Select Class</option><?php foreach($classes as $c): ?><option value="<?= e($c) ?>" <?= $selClass===$c?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><button class="btn btn-outline-primary">Load</button></div>
</form>

<?php if($studentList): ?>
<form method="POST">
<?= csrfField() ?>
<input type="hidden" name="save_marks" value="1">
<input type="hidden" name="class" value="<?= e($selClass) ?>">
<div class="row g-2 mb-3">
<div class="col-md-3"><input type="text" name="exam_name" class="form-control" placeholder="Exam Name *" required value="<?= e($_POST['exam_name']??'') ?>"></div>
<div class="col-md-3"><input type="text" name="subject" class="form-control" placeholder="Subject *" required value="<?= e($_POST['subject']??'') ?>"></div>
<div class="col-md-2"><input type="number" name="max_marks" class="form-control" placeholder="Max Marks" value="<?= e($_POST['max_marks']??100) ?>"></div>
</div>
<table class="table table-sm"><thead><tr><th>#</th><th>Adm No</th><th>Name</th><th>Marks</th></tr></thead>
<tbody>
<?php foreach($studentList as $i=>$s): ?>
<tr><td><?= $i+1 ?></td><td><?= e($s['admission_no']) ?></td><td><?= e($s['name']) ?></td>
<td><input type="number" name="marks[<?= $s['id'] ?>]" class="form-control form-control-sm" style="width:100px" min="0"></td></tr>
<?php endforeach; ?>
</tbody></table>
<button class="btn btn-primary">Save Marks</button>
</form>
<?php endif; ?>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
