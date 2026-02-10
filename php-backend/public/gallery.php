<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');

$category = $_GET['category'] ?? '';
$where = "WHERE status='approved'";
$params = [];
if ($category) { $where .= " AND category=?"; $params[] = $category; }

$stmt = $db->prepare("SELECT id, title, category, description, file_path, file_type FROM gallery_items $where ORDER BY created_at DESC LIMIT 100");
$stmt->execute($params);
$items = $stmt->fetchAll();

$categories = $db->query("SELECT DISTINCT category FROM gallery_items WHERE status='approved' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery — <?= e($schoolName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        .hero-banner { background: linear-gradient(135deg, #059669 0%, #0891b2 100%); color: #fff; padding: 3rem 0; }
        .gallery-item { border-radius: 12px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
        .gallery-item:hover { transform: scale(1.03); }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; }
        .lightbox { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: none; align-items: center; justify-content: center; }
        .lightbox.show { display: flex; }
        .lightbox img { max-width: 90%; max-height: 90vh; border-radius: 8px; }
        .lightbox .close-btn { position: absolute; top: 1rem; right: 1.5rem; color: #fff; font-size: 2rem; cursor: pointer; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#0f172a;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/public/notifications.php"><i class="bi bi-mortarboard-fill me-2"></i><?= e($schoolName) ?></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#pubNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="pubNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-light ms-lg-2 px-3" href="/login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-images me-2"></i>Photo Gallery</h1>
        <p class="mb-0 opacity-75">Explore moments from <?= e($schoolName) ?></p>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/public/gallery.php" class="btn btn-sm <?= !$category ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
            <a href="/public/gallery.php?category=<?= urlencode($cat) ?>" class="btn btn-sm <?= $category === $cat ? 'btn-primary' : 'btn-outline-primary' ?>"><?= e(ucfirst($cat)) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="text-center py-5"><i class="bi bi-image display-1 text-muted"></i><p class="text-muted mt-3">No gallery items found.</p></div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <?php if ($item['file_type'] === 'video'): ?>
                <?php preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $item['file_path'], $m); $ytId = $m[1] ?? ''; ?>
                <div class="gallery-item shadow-sm">
                    <div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/<?= e($ytId) ?>" allowfullscreen></iframe></div>
                    <div class="p-2 bg-white"><small class="fw-semibold"><?= e($item['title']) ?></small></div>
                </div>
            <?php else: ?>
                <div class="gallery-item shadow-sm" onclick="openLightbox('/<?= e($item['file_path']) ?>')">
                    <img src="/<?= e($item['file_path']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                    <div class="p-2 bg-white"><small class="fw-semibold"><?= e($item['title']) ?></small></div>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="close-btn">&times;</span>
    <img id="lightboxImg" src="" alt="Gallery Image">
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</p>
        <small class="text-muted">Powered by JNV School Management System</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openLightbox(src) { document.getElementById('lightboxImg').src = src; document.getElementById('lightbox').classList.add('show'); }
function closeLightbox() { document.getElementById('lightbox').classList.remove('show'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
</body>
</html>
