<?php
require_once __DIR__.'/../includes/auth.php';
$db=getDB();
$events=$db->query("SELECT * FROM events WHERE is_public=1 AND event_date>=CURDATE() ORDER BY event_date ASC")->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>School Events</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand" href="/">School</a>
<div><a href="/public/notifications.php" class="text-white me-3">Notifications</a><a href="/public/gallery.php" class="text-white me-3">Gallery</a><a href="/public/events.php" class="text-white me-3">Events</a><a href="/public/admission-form.php" class="text-white me-3">Apply</a><a href="/login.php" class="text-white">Login</a></div></div></nav>
<div class="container py-4">
<h2 class="mb-4">Upcoming Events</h2>
<?php foreach($events as $ev): ?>
<div class="card mb-3"><div class="card-body">
<h5><?= e($ev['title']) ?></h5>
<p><i class="bi bi-calendar"></i> <?= e($ev['event_date']) ?> <?= $ev['event_time']?'at '.e($ev['event_time']):'' ?>
<?php if($ev['location']): ?> | <i class="bi bi-geo-alt"></i> <?= e($ev['location']) ?><?php endif; ?></p>
<?php if($ev['description']): ?><p><?= nl2br(e($ev['description'])) ?></p><?php endif; ?>
</div></div>
<?php endforeach; ?>
<?php if(empty($events)): ?><p class="text-muted">No upcoming events.</p><?php endif; ?>
</div></body></html>
