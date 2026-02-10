<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');

$notifs = $db->query("SELECT title, content, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .hero-banner { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%); color: #fff; padding: 3rem 0; }
        .notif-card { border: none; border-radius: 12px; transition: transform 0.2s; }
        .notif-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .type-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; }
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
                <li class="nav-item"><a class="nav-link active" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-light ms-lg-2 px-3" href="/login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-bell-fill me-2"></i>Notifications</h1>
        <p class="mb-0 opacity-75">Stay updated with the latest announcements from <?= e($schoolName) ?></p>
    </div>
</div>

<!-- Content -->
<div class="container py-4">
    <?php if (empty($notifs)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bell-slash display-1 text-muted"></i>
            <p class="text-muted mt-3">No notifications at this time.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifs as $n):
            $typeColors = ['urgent' => 'danger', 'exam' => 'warning', 'academic' => 'info', 'event' => 'success', 'holiday' => 'purple'];
            $color = $typeColors[$n['type']] ?? 'secondary';
        ?>
        <div class="card notif-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="fw-semibold mb-0"><?= e($n['title']) ?></h5>
                    <span class="badge bg-<?= $color ?> type-badge"><?= e(ucfirst($n['type'])) ?></span>
                </div>
                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></small>
                <p class="mt-2 mb-0"><?= nl2br(e($n['content'])) ?></p>
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
