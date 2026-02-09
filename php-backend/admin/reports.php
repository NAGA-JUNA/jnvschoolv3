<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();

$type=$_GET['type']??'students';
$validTypes=['students','teachers','admissions','attendance'];
if(!in_array($type,$validTypes)) $type='students';

if($_SERVER['REQUEST_METHOD']==='POST'&&$_POST['export']??false){
    if(!verifyCsrf()){setFlash('error','Invalid.');header('Location: /admin/reports.php');exit;}

    $filename=$type.'_'.date('Y-m-d').'.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $out=fopen('php://output','w');

    switch($type){
        case 'students':
            fputcsv($out,['Adm No','Name','Father','Class','Section','Phone','Status']);
            foreach($db->query("SELECT * FROM students ORDER BY name")->fetchAll() as $r)
                fputcsv($out,[$r['admission_no'],$r['name'],$r['father_name'],$r['class'],$r['section'],$r['phone'],$r['status']]);
            break;
        case 'teachers':
            fputcsv($out,['Emp ID','Name','Subject','Phone','Status']);
            foreach($db->query("SELECT * FROM teachers ORDER BY name")->fetchAll() as $r)
                fputcsv($out,[$r['employee_id'],$r['name'],$r['subject'],$r['phone'],$r['status']]);
            break;
        case 'admissions':
            fputcsv($out,['Name','Class','Phone','Status','Date']);
            foreach($db->query("SELECT * FROM admissions ORDER BY created_at DESC")->fetchAll() as $r)
                fputcsv($out,[$r['student_name'],$r['class_applied'],$r['phone'],$r['status'],$r['created_at']]);
            break;
        case 'attendance':
            fputcsv($out,['Student','Class','Date','Status']);
            foreach($db->query("SELECT a.*,s.name FROM attendance a JOIN students s ON a.student_id=s.id ORDER BY a.date DESC")->fetchAll() as $r)
                fputcsv($out,[$r['name'],$r['class'],$r['date'],$r['status']]);
            break;
    }
    fclose($out);
    auditLog('export_'.$type,'report',0);
    exit;
}

$pageTitle='Reports';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-4">Reports & Export</h3>
<div class="row g-3">
<?php foreach($validTypes as $t): ?>
<div class="col-md-3">
  <div class="card text-center p-4">
    <h5><?= ucfirst($t) ?></h5>
    <form method="POST" action="?type=<?= $t ?>">
      <?= csrfField() ?>
      <input type="hidden" name="export" value="1">
      <button class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-download me-1"></i>Export CSV</button>
    </form>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
