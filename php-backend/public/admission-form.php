<?php
require_once __DIR__.'/../includes/auth.php';
$db=getDB();
$success='';$error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid request.';}else{
    $data=[
        'student_name'=>trim($_POST['student_name']??''),
        'father_name'=>trim($_POST['father_name']??''),
        'mother_name'=>trim($_POST['mother_name']??''),
        'dob'=>$_POST['dob']??null,
        'gender'=>$_POST['gender']??null,
        'class_applied'=>trim($_POST['class_applied']??''),
        'phone'=>trim($_POST['phone']??''),
        'email'=>trim($_POST['email']??''),
        'address'=>trim($_POST['address']??''),
        'previous_school'=>trim($_POST['previous_school']??''),
    ];
    if(!$data['student_name']||!$data['class_applied']||!$data['phone']){$error='Name, class, and phone are required.';}
    else{
        $db->prepare("INSERT INTO admissions (student_name,father_name,mother_name,dob,gender,class_applied,phone,email,address,previous_school) VALUES (?,?,?,?,?,?,?,?,?,?)")
           ->execute(array_values($data));
        $success='Your admission application has been submitted successfully!';
    }}
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Apply for Admission</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="/">School</a>
<div><a href="/public/notifications.php" class="text-white me-3">Notifications</a><a href="/public/gallery.php" class="text-white me-3">Gallery</a><a href="/public/events.php" class="text-white me-3">Events</a><a href="/public/admission-form.php" class="text-white me-3">Apply</a><a href="/login.php" class="text-white">Login</a></div></div></nav>
<div class="container py-4" style="max-width:700px">
<h2 class="mb-4">Apply for Admission</h2>
<?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if(!$success): ?>
<form method="POST" class="row g-3">
<?= csrfField() ?>
<div class="col-md-6"><label class="form-label">Student Name *</label><input type="text" name="student_name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Class Applied For *</label><input type="text" name="class_applied" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Father's Name</label><input type="text" name="father_name" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Mother's Name</label><input type="text" name="mother_name" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
<div class="col-md-4"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Previous School</label><input type="text" name="previous_school" class="form-control"></div>
<div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
<div class="col-12"><button class="btn btn-primary">Submit Application</button></div>
</form>
<?php endif; ?>
</div></body></html>
