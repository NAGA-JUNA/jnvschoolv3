<?php
require_once __DIR__.'/../includes/auth.php';
$db=getDB();
$items=$db->query("SELECT title,description,category,file_path,file_type,created_at FROM gallery_items WHERE status='approved' ORDER BY created_at DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>School Gallery</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="/">School</a>
<div><a href="/public/notifications.php" class="text-white me-3">Notifications</a><a href="/public/gallery.php" class="text-white me-3">Gallery</a><a href="/public/events.php" class="text-white me-3">Events</a><a href="/public/admission-form.php" class="text-white me-3">Apply</a><a href="/login.php" class="text-white">Login</a></div></div></nav>
<div class="container py-4">
<h2 class="mb-4">Gallery</h2>
<div class="row g-3">
<?php foreach($items as $g): ?>
<div class="col-md-4"><div class="card">
<?php if($g['file_type']==='image'): ?><img src="/<?= e($g['file_path']) ?>" class="card-img-top" alt="<?= e($g['title']) ?>" style="height:200px;object-fit:cover">
<?php else: ?><video src="/<?= e($g['file_path']) ?>" class="card-img-top" controls style="height:200px"></video><?php endif; ?>
<div class="card-body"><h6><?= e($g['title']) ?></h6><small class="text-muted"><?= e($g['category']) ?></small></div></div></div>
<?php endforeach; ?>
<?php if(empty($items)): ?><p class="text-muted">No gallery items yet.</p><?php endif; ?>
</div></div></body></html>
