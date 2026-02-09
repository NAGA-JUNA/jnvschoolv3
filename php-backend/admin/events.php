<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db=getDB();

if(isset($_GET['delete'])&&is_numeric($_GET['delete'])){
    if(verifyCsrf()){$db->prepare("DELETE FROM events WHERE id=?")->execute([$_GET['delete']]);auditLog('delete_event','event',(int)$_GET['delete']);setFlash('success','Deleted.');}
    header('Location: /admin/events.php');exit;
}

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verifyCsrf()){$error='Invalid.';}else{
    $data=['title'=>trim($_POST['title']??''),'description'=>trim($_POST['description']??''),'event_date'=>$_POST['event_date']??'','event_time'=>$_POST['event_time']??null,'location'=>trim($_POST['location']??''),'is_public'=>isset($_POST['is_public'])?1:0];
    $eid=isset($_POST['event_id'])?(int)$_POST['event_id']:0;
    if(!$data['title']||!$data['event_date']){$error='Title and date required.';}else{
    if($eid){$db->prepare("UPDATE events SET title=?,description=?,event_date=?,event_time=?,location=?,is_public=? WHERE id=?")->execute([...array_values($data),$eid]);auditLog('update_event','event',$eid);}
    else{$db->prepare("INSERT INTO events (title,description,event_date,event_time,location,is_public,created_by) VALUES (?,?,?,?,?,?,?)")->execute([...array_values($data),currentUserId()]);auditLog('create_event','event',(int)$db->lastInsertId());}
    setFlash('success','Event saved.');header('Location: /admin/events.php');exit;}}}

$editEvent=null;
if(isset($_GET['edit'])&&is_numeric($_GET['edit'])){$stmt=$db->prepare("SELECT * FROM events WHERE id=?");$stmt->execute([$_GET['edit']]);$editEvent=$stmt->fetch();}

$events=$db->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$pageTitle='Events';
require_once __DIR__.'/../includes/header.php';
$ev=$editEvent??[];
?>
<h3 class="mb-3">Events</h3>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card mb-4"><div class="card-body">
<h5><?= $editEvent?'Edit Event':'Add Event' ?></h5>
<form method="POST" class="row g-2">
<?= csrfField() ?>
<?php if($editEvent): ?><input type="hidden" name="event_id" value="<?= $editEvent['id'] ?>"><?php endif; ?>
<div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="Title *" required value="<?= e($ev['title']??'') ?>"></div>
<div class="col-md-2"><input type="date" name="event_date" class="form-control" required value="<?= e($ev['event_date']??'') ?>"></div>
<div class="col-md-2"><input type="time" name="event_time" class="form-control" value="<?= e($ev['event_time']??'') ?>"></div>
<div class="col-md-3"><input type="text" name="location" class="form-control" placeholder="Location" value="<?= e($ev['location']??'') ?>"></div>
<div class="col-md-1"><div class="form-check mt-2"><input type="checkbox" name="is_public" class="form-check-input" <?= ($ev['is_public']??1)?'checked':'' ?>><label class="form-check-label">Public</label></div></div>
<div class="col-12"><textarea name="description" class="form-control" rows="2" placeholder="Description"><?= e($ev['description']??'') ?></textarea></div>
<div class="col-12"><button class="btn btn-primary btn-sm">Save</button></div>
</form></div></div>

<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Public</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($events as $ev): ?>
<tr><td><?= e($ev['title']) ?></td><td><?= e($ev['event_date']) ?></td><td><?= e($ev['location']) ?></td><td><?= $ev['is_public']?'Yes':'No' ?></td>
<td><a href="?edit=<?= $ev['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
<a href="?delete=<?= $ev['id'] ?>&csrf_token=<?= csrfToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Del</a></td></tr>
<?php endforeach; ?>
<?php if(empty($events)): ?><tr><td colspan="5" class="text-center text-muted py-3">No events</td></tr><?php endif; ?>
</tbody></table></div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
