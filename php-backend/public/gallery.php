<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$schoolAddress = getSetting('school_address', '');
$whatsappNumber = getSetting('whatsapp_api_number', '');
$navLogo = getSetting('school_logo', '');
$logoPath = ($navLogo && strpos($navLogo, '/uploads/') === 0) ? $navLogo : '/uploads/logo/' . $navLogo;

// Social links
$socialFacebook = getSetting('social_facebook', '');
$socialTwitter = getSetting('social_twitter', '');
$socialInstagram = getSetting('social_instagram', '');
$socialYoutube = getSetting('social_youtube', '');
$socialLinkedin = getSetting('social_linkedin', '');

// Bell notifications
$bellNotifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
$notifCount = $db->query("SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

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
        .top-bar { background: #0a0f1a; color: #fff; padding: 0.4rem 0; font-size: 0.78rem; }
        .top-bar a { color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .marquee-text { white-space: nowrap; overflow: hidden; }
        .marquee-text span { display: inline-block; animation: marqueeScroll 20s linear infinite; }
        @keyframes marqueeScroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        .main-navbar { background: #0f172a; padding: 0.5rem 0; }
        .main-navbar .nav-link { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 0.9rem; padding: 0.5rem 0.8rem; }
        .main-navbar .nav-link:hover, .main-navbar .nav-link.active { color: #fff; }
        .notif-bell-btn { background: #dc3545; color: #fff; border: none; border-radius: 8px; padding: 0.4rem 0.9rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; position: relative; transition: background 0.2s; }
        .notif-bell-btn:hover { background: #c82333; }
        .notif-badge { position: absolute; top: -6px; right: -8px; background: #ffc107; color: #000; font-size: 0.65rem; font-weight: 700; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .login-nav-btn { background: transparent; border: 1.5px solid rgba(255,255,255,0.5); color: #fff; border-radius: 8px; padding: 0.4rem 1.2rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .login-nav-btn:hover { background: #fff; color: #0f172a; }
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s; animation: whatsappPulse 2s infinite; }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }
        @keyframes whatsappPulse { 0%, 100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); } 50% { box-shadow: 0 4px 30px rgba(37,211,102,0.7); } }
        .hero-banner { background: linear-gradient(135deg, #059669 0%, #0891b2 100%); color: #fff; padding: 3rem 0; }
        .gallery-item { border-radius: 12px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
        .gallery-item:hover { transform: scale(1.03); }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; }
        .lightbox { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: none; align-items: center; justify-content: center; }
        .lightbox.show { display: flex; }
        .lightbox img { max-width: 90%; max-height: 90vh; border-radius: 8px; }
        .lightbox .close-btn { position: absolute; top: 1rem; right: 1.5rem; color: #fff; font-size: 2rem; cursor: pointer; }
        /* Gradient Footer */
        .site-footer { background: linear-gradient(135deg, #6a11cb 0%, #1e8a7a 100%); color: #fff; border-radius: 30px 30px 0 0; margin-top: 3rem; }
        .footer-heading { text-transform: uppercase; font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 1rem; position: relative; padding-bottom: 0.5rem; }
        .footer-heading::after { content: ''; position: absolute; bottom: 0; left: 0; width: 30px; height: 2px; background: rgba(255,255,255,0.5); }
        .footer-social a { width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.4); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; font-size: 0.9rem; }
        .footer-social a:hover { background: #fff; color: #6a11cb; border-color: #fff; }
        .footer-newsletter-input { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: #fff; }
        .footer-newsletter-input::placeholder { color: rgba(255,255,255,0.6); }
        .footer-newsletter-input:focus { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5); color: #fff; box-shadow: none; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.15); }
        @media (max-width: 767.98px) { .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; } }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="marquee-text flex-grow-1 me-3"><span>🎓 Welcome to <?= e($schoolName) ?> — <?= e($schoolTagline) ?></span></div>
            <div class="d-flex gap-3 flex-shrink-0">
                <a href="/public/admission-form.php"><i class="bi bi-mortarboard me-1"></i>Admissions</a>
                <a href="/public/gallery.php"><i class="bi bi-images me-1"></i>Gallery</a>
                <a href="/public/events.php"><i class="bi bi-calendar-event me-1"></i>Events</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="main-navbar navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-white" href="/">
            <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="width:36px;height:36px;border-radius:8px;object-fit:cover;"><?php else: ?><i class="bi bi-mortarboard-fill"></i><?php endif; ?>
            <?= e($schoolName) ?>
        </a>
        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/teachers.php">Our Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="notif-bell-btn" data-bs-toggle="modal" data-bs-target="#notifBellModal">
                    <i class="bi bi-bell-fill me-1"></i> Notifications
                    <?php if ($notifCount > 0): ?><span class="notif-badge"><?= $notifCount > 9 ? '9+' : $notifCount ?></span><?php endif; ?>
                </button>
                <a href="/login.php" class="login-nav-btn"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
            </div>
        </div>
    </div>
</nav>

<!-- Bell Notification Modal -->
<div class="modal fade" id="notifBellModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-bell-fill text-danger me-2"></i>Latest Notifications</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <?php if (empty($bellNotifs)): ?>
                    <p class="text-muted text-center py-3">No recent notifications.</p>
                <?php else: ?>
                    <?php foreach ($bellNotifs as $bn):
                        $typeColors = ['urgent' => 'danger', 'exam' => 'warning', 'academic' => 'info', 'event' => 'success'];
                        $bColor = $typeColors[$bn['type']] ?? 'secondary';
                    ?>
                    <div class="d-flex justify-content-between align-items-start p-2 rounded-3 mb-2" style="background:#f8fafc;">
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;"><?= e($bn['title']) ?></div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d M Y', strtotime($bn['created_at'])) ?></small>
                        </div>
                        <span class="badge bg-<?= $bColor ?>" style="font-size:0.7rem;"><?= e(ucfirst($bn['type'])) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/public/notifications.php" class="btn btn-primary btn-sm w-100 rounded-3"><i class="bi bi-list-ul me-1"></i>View All Notifications</a>
            </div>
        </div>
    </div>
</div>

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

<!-- Gradient Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 py-5">
            <div class="col-lg-3 col-md-6">
                <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="width:50px;height:50px;border-radius:10px;object-fit:cover;margin-bottom:0.8rem;"><?php endif; ?>
                <h5 class="fw-bold mb-2"><?= e($schoolName) ?></h5>
                <p class="small opacity-75 mb-0"><?= e($schoolAddress ?: $schoolTagline) ?></p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">About Us</h6>
                <ul class="list-unstyled small">
                    <?php if ($schoolAddress): ?><li class="mb-2 opacity-75"><i class="bi bi-geo-alt me-2"></i><?= e($schoolAddress) ?></li><?php endif; ?>
                    <?php if ($schoolEmail): ?><li class="mb-2 opacity-75"><i class="bi bi-envelope me-2"></i><?= e($schoolEmail) ?></li><?php endif; ?>
                    <?php if ($schoolPhone): ?><li class="mb-2 opacity-75"><i class="bi bi-telephone me-2"></i><?= e($schoolPhone) ?></li><?php endif; ?>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="/" class="text-white text-decoration-none opacity-75">Home</a></li>
                    <li class="mb-1"><a href="/public/about.php" class="text-white text-decoration-none opacity-75">About Us</a></li>
                    <li class="mb-1"><a href="/public/teachers.php" class="text-white text-decoration-none opacity-75">Our Teachers</a></li>
                    <li class="mb-1"><a href="/public/gallery.php" class="text-white text-decoration-none opacity-75">Gallery</a></li>
                    <li class="mb-1"><a href="/public/events.php" class="text-white text-decoration-none opacity-75">Events</a></li>
                    <li class="mb-1"><a href="/public/admission-form.php" class="text-white text-decoration-none opacity-75">Apply Now</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Newsletter</h6>
                <div class="input-group mb-3">
                    <input type="email" class="form-control footer-newsletter-input" placeholder="Your email">
                    <button class="btn btn-light" type="button"><i class="bi bi-arrow-right"></i></button>
                </div>
                <div class="footer-social d-flex gap-2 flex-wrap">
                    <?php if ($socialFacebook): ?><a href="<?= e($socialFacebook) ?>" target="_blank"><i class="bi bi-facebook"></i></a><?php endif; ?>
                    <?php if ($socialTwitter): ?><a href="<?= e($socialTwitter) ?>" target="_blank"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                    <?php if ($socialInstagram): ?><a href="<?= e($socialInstagram) ?>" target="_blank"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    <?php if ($socialYoutube): ?><a href="<?= e($socialYoutube) ?>" target="_blank"><i class="bi bi-youtube"></i></a><?php endif; ?>
                    <?php if ($socialLinkedin): ?><a href="<?= e($socialLinkedin) ?>" target="_blank"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container text-center py-3">
            <small class="opacity-75">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</small>
        </div>
    </div>
</footer>

<!-- WhatsApp Float -->
<?php if ($whatsappNumber): ?>
<a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $whatsappNumber)) ?>" target="_blank" class="whatsapp-float" title="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openLightbox(src) { document.getElementById('lightboxImg').src = src; document.getElementById('lightbox').classList.add('show'); }
function closeLightbox() { document.getElementById('lightbox').classList.remove('show'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
</body>
</html>