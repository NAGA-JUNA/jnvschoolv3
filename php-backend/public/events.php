<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');

$events = $db->query("SELECT * FROM events WHERE is_public=1 AND event_date>=CURDATE() ORDER BY event_date ASC")->fetchAll();
$pastEvents = $db->query("SELECT * FROM events WHERE is_public=1 AND event_date<CURDATE() ORDER BY event_date DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .hero-banner { background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); color: #fff; padding: 3rem 0; }
        .event-card { border: none; border-radius: 12px; border-left: 4px solid #3b82f6; transition: transform 0.2s; }
        .event-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .date-box { width: 60px; height: 60px; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 700; }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f172a;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/public/notifications.php"><i class="bi bi-mortarboard-fill me-2"></i><?= e($schoolName) ?></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#pubNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="pubNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-light ms-lg-2 px-3" href="/login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-calendar-event-fill me-2"></i>Events</h1>
        <p class="mb-0 opacity-75">Upcoming and past events at <?= e($schoolName) ?></p>
    </div>
</div>

<div class="container py-4">
    <!-- Upcoming Events -->
    <h4 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2 text-success"></i>Upcoming Events</h4>
    <?php if (empty($events)): ?>
        <div class="text-center py-4">
            <i class="bi bi-calendar-x display-4 text-muted"></i>
            <p class="text-muted mt-2">No upcoming events scheduled.</p>
        </div>
    <?php else: ?>
        <?php foreach ($events as $ev): ?>
        <div class="card event-card mb-3">
            <div class="card-body d-flex gap-3 align-items-start">
                <div class="date-box bg-primary-subtle text-primary flex-shrink-0">
                    <span style="font-size:1.2rem;line-height:1;"><?= date('d', strtotime($ev['event_date'])) ?></span>
                    <span style="font-size:0.65rem;text-transform:uppercase;"><?= date('M', strtotime($ev['event_date'])) ?></span>
                </div>
                <div>
                    <h5 class="fw-semibold mb-1"><?= e($ev['title']) ?></h5>
                    <div class="text-muted small mb-1">
                        <i class="bi bi-clock me-1"></i><?= $ev['event_time'] ? date('h:i A', strtotime($ev['event_time'])) : 'All Day' ?>
                        <?php if ($ev['location']): ?> <span class="mx-1">|</span> <i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?><?php endif; ?>
                    </div>
                    <?php if ($ev['description']): ?><p class="mb-0 mt-1"><?= nl2br(e($ev['description'])) ?></p><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Past Events -->
    <?php if (!empty($pastEvents)): ?>
    <h4 class="fw-bold mb-3 mt-5"><i class="bi bi-clock-history me-2 text-muted"></i>Past Events</h4>
    <?php foreach ($pastEvents as $ev): ?>
    <div class="card event-card mb-3" style="opacity:0.7; border-left-color:#94a3b8;">
        <div class="card-body d-flex gap-3 align-items-start">
            <div class="date-box bg-secondary-subtle text-secondary flex-shrink-0">
                <span style="font-size:1.2rem;line-height:1;"><?= date('d', strtotime($ev['event_date'])) ?></span>
                <span style="font-size:0.65rem;text-transform:uppercase;"><?= date('M Y', strtotime($ev['event_date'])) ?></span>
            </div>
            <div>
                <h5 class="fw-semibold mb-1"><?= e($ev['title']) ?></h5>
                <?php if ($ev['location']): ?><small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?></small><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</p>
        <small class="text-muted">Powered by JNV School Management System</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
