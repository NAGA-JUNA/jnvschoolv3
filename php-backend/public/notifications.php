<?php
// Public Notifications
require_once __DIR__.'/../includes/auth.php';
$db=getDB();
$notifs=$db->query("SELECT title,content,type,created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 50")->fetchAll();
$pageTitle='Notifications';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>School Notifications</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="/">School</a>
<div><a href="/public/notifications.php" class="text-white me-3">Notifications</a><a href="/public/gallery.php" class="text-white me-3">Gallery</a><a href="/public/events.php" class="text-white me-3">Events</a><a href="/public/admission-form.php" class="text-white me-3">Apply</a><a href="/login.php" class="text-white">Login</a></div></div></nav>
<div class="container py-4">
<h2 class="mb-4">Notifications</h2>
<?php foreach($notifs as $n): ?>
<div class="card mb-3"><div class="card-body"><h5><?= e($n['title']) ?></h5><span class="badge bg-info"><?= e($n['type']) ?></span> <small class="text-muted"><?= e($n['created_at']) ?></small><p class="mt-2"><?= nl2br(e($n['content'])) ?></p></div></div>
<?php endforeach; ?>
<?php if(empty($notifs)): ?><p class="text-muted">No notifications at this time.</p><?php endif; ?>
</div></body></html>
