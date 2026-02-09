<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db=getDB();
$error='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid.';}else{
    $title=trim($_POST['title']??'');$desc=trim($_POST['description']??'');$cat=$_POST['category']??'general';
    if(!$title){$error='Title required.';}
    elseif(!isset($_FILES['file'])||$_FILES['file']['error']!==UPLOAD_ERR_OK){$error='Please select a file.';}
    else{
        $file=$_FILES['file'];
        $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,['jpg','jpeg','png','gif','webp','mp4'])){$error='Invalid file type.';}
        elseif($file['size']>10*1024*1024){$error='File too large (max 10MB).';}
        else{
            $uploadDir=__DIR__.'/../uploads/gallery/';
            if(!is_dir($uploadDir))mkdir($uploadDir,0755,true);
            $newName=uniqid().'.'.$ext;
            move_uploaded_file($file['tmp_name'],$uploadDir.$newName);
            $fileType=in_array($ext,['mp4'])?'video':'image';
            $db->prepare("INSERT INTO gallery_items (title,description,category,file_path,file_type,uploaded_by,status) VALUES (?,?,?,?,?,?,'pending')")
               ->execute([$title,$desc,$cat,'uploads/gallery/'.$newName,$fileType,currentUserId()]);
            auditLog('upload_gallery','gallery',(int)$db->lastInsertId());
            setFlash('success','Uploaded for approval.');header('Location: /teacher/upload-gallery.php');exit;
        }}
    }
}

$pageTitle='Upload Gallery';
require_once __DIR__.'/../includes/header.php';
?>
<h3 class="mb-3">Upload to Gallery</h3>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card mb-4"><div class="card-body">
<form method="POST" enctype="multipart/form-data" class="row g-3">
<?= csrfField() ?>
<div class="col-md-4"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Category</label><select name="category" class="form-select"><option value="general">General</option><option value="sports">Sports</option><option value="cultural">Cultural</option><option value="academic">Academic</option></select></div>
<div class="col-md-4"><label class="form-label">File *</label><input type="file" name="file" class="form-control" accept="image/*,video/mp4" required></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
<div class="col-12"><button class="btn btn-primary">Upload</button></div>
</form></div></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
