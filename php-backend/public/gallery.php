<?php
require_once __DIR__.'/../includes/auth.php';
$db = getDB();

// ── AJAX endpoint for images (must be before any output) ──
if (isset($_GET['ajax']) && $_GET['ajax'] === 'images') {
    header('Content-Type: application/json');
    $stmt = $db->query("SELECT id, title, description, category, file_path, file_type, event_name, is_featured FROM gallery_items WHERE status='approved' AND visibility='public' ORDER BY is_featured DESC, created_at DESC");
    $items = $stmt->fetchAll();
    $cats = $db->query("SELECT slug, name FROM gallery_categories WHERE status='active' ORDER BY sort_order")->fetchAll();
    $catNameToSlug = [];
    foreach ($cats as $c) { $catNameToSlug[strtolower($c['name'])] = $c['slug']; }
    $grouped = [];
    foreach ($items as $item) {
        $catName = strtolower($item['category'] ?? 'general');
        $slug = $catNameToSlug[$catName] ?? $catName;
        if (!isset($grouped[$slug])) $grouped[$slug] = [];
        $grouped[$slug][] = $item;
    }
    echo json_encode(['categories' => $grouped, 'total' => count($items)]);
    exit;
}
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

// Gallery settings
$layoutStyle = getSetting('gallery_layout_style', 'premium');
$bgStyle = getSetting('gallery_bg_style', 'dark');
$showParticles = getSetting('gallery_particles_show', '1');

// Load categories
$categories = $db->query("SELECT * FROM gallery_categories WHERE status='active' ORDER BY sort_order ASC")->fetchAll();

// Load all approved gallery images grouped by category name (backward compat)
$selectedCat = $_GET['category'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery — <?= e($schoolName) ?></title>
    <meta name="description" content="Photo Gallery of <?= e($schoolName) ?> — Explore moments from school life including academic, cultural, sports, and campus events.">
    <?php $favicon = getSetting('school_favicon', ''); if ($favicon): ?><link rel="icon" href="/uploads/logo/<?= e($favicon) ?>"><?php endif; ?>
    <link rel="canonical" href="https://<?= $_SERVER['HTTP_HOST'] ?>/public/gallery.php">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0f1a; color: #fff; overflow-x: hidden; }

        /* ── Top Bar ── */
        .top-bar { background: #060a12; color: #fff; padding: 0.4rem 0; font-size: 0.78rem; }
        .top-bar a { color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .marquee-text { white-space: nowrap; overflow: hidden; }
        .marquee-text span { display: inline-block; animation: marqueeScroll 20s linear infinite; }
        @keyframes marqueeScroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* ── Navbar ── */
        .main-navbar { background: #0f172a; padding: 0.5rem 0; }
        .main-navbar .nav-link { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 0.9rem; padding: 0.5rem 0.8rem; }
        .main-navbar .nav-link:hover, .main-navbar .nav-link.active { color: #fff; }
        .notif-bell-btn { background: #dc3545; color: #fff; border: none; border-radius: 8px; padding: 0.4rem 0.9rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; position: relative; transition: background 0.2s; }
        .notif-bell-btn:hover { background: #c82333; }
        .notif-badge { position: absolute; top: -6px; right: -8px; background: #ffc107; color: #000; font-size: 0.65rem; font-weight: 700; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .login-nav-btn { background: transparent; border: 1.5px solid rgba(255,255,255,0.5); color: #fff; border-radius: 8px; padding: 0.4rem 1.2rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .login-nav-btn:hover { background: #fff; color: #0f172a; }

        /* ── Gallery Layout ── */
        .gallery-container { min-height: calc(100vh - 120px); position: relative; }

        /* Particles */
        .particles { position: absolute; inset: 0; pointer-events: none; overflow: hidden; z-index: 0; }
        .particle { position: absolute; width: 2px; height: 2px; background: rgba(255,255,255,0.4); border-radius: 50%; animation: particleFloat 6s ease-in-out infinite; }
        @keyframes particleFloat {
            0%, 100% { opacity: 0; transform: translateY(0) scale(1); }
            50% { opacity: 1; transform: translateY(-60px) scale(1.5); }
        }

        /* Left Hero Panel */
        .hero-panel {
            background: linear-gradient(180deg, #0d1b3e 0%, #0a1628 50%, #060f1f 100%);
            padding: 3rem 2rem;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; overflow: hidden; min-height: 500px;
        }
        .hero-panel h1 { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 1rem; }
        .hero-panel p { color: rgba(255,255,255,0.6); font-size: 0.95rem; line-height: 1.6; }
        .hero-panel .scroll-arrow {
            width: 48px; height: 48px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: all 0.3s; margin-top: 2rem; color: rgba(255,255,255,0.5);
        }
        .hero-panel .scroll-arrow:hover { border-color: var(--theme-primary, #3b82f6); color: #fff; background: rgba(59,130,246,0.1); }

        /* Category Grid */
        .category-grid { padding: 2rem 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
        .category-grid-inner { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
        .cat-card {
            position: relative; border-radius: 16px; overflow: hidden; cursor: pointer;
            aspect-ratio: 1; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .cat-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .cat-card .cat-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%);
            display: flex; align-items: flex-end; padding: 0.75rem;
            transition: background 0.3s;
        }
        .cat-card .cat-label { color: #fff; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px; }
        .cat-card:hover { transform: scale(1.05); box-shadow: 0 8px 30px rgba(59,130,246,0.3); }
        .cat-card:hover img { transform: scale(1.1); }
        .cat-card.active { box-shadow: 0 0 0 3px var(--theme-primary, #3b82f6), 0 8px 30px rgba(59,130,246,0.4); }
        .cat-card .cat-count {
            position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px); color: #fff; font-size: 0.7rem; padding: 2px 8px;
            border-radius: 12px; font-weight: 600;
        }
        .cat-card-placeholder {
            background: linear-gradient(135deg, #1a2744 0%, #0f1d36 100%);
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.3); font-size: 2rem;
        }

        /* Slider Panel */
        .slider-panel {
            padding: 2rem;
            background: linear-gradient(135deg, #0d1529 0%, #111d35 100%);
            display: flex; flex-direction: column; position: relative; min-height: 500px;
        }
        .slider-title {
            font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700;
            margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .slider-title .cat-badge {
            background: var(--theme-primary, #3b82f6); padding: 4px 12px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
            font-family: 'Inter', sans-serif;
        }
        .slider-main {
            flex: 1; position: relative; border-radius: 20px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .slider-main img {
            width: 100%; height: 100%; object-fit: cover; min-height: 350px;
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .slider-main .caption-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            padding: 2rem 1.5rem 1rem; color: #fff;
        }
        .slider-main .caption-bar h6 { font-weight: 600; margin-bottom: 0.25rem; }
        .slider-main .caption-bar small { color: rgba(255,255,255,0.6); }
        .slider-nav {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 1rem; gap: 0.5rem;
        }
        .slider-nav button {
            width: 44px; height: 44px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05); color: #fff; cursor: pointer;
            transition: all 0.3s; display: flex; align-items: center; justify-content: center;
        }
        .slider-nav button:hover { background: var(--theme-primary, #3b82f6); border-color: var(--theme-primary, #3b82f6); }
        .slider-counter { color: rgba(255,255,255,0.5); font-size: 0.85rem; font-weight: 500; }

        /* Thumbnail strip */
        .thumb-strip {
            display: flex; gap: 0.5rem; margin-top: 1rem; overflow-x: auto;
            padding-bottom: 0.5rem; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        .thumb-strip img {
            width: 60px; height: 60px; object-fit: cover; border-radius: 10px;
            cursor: pointer; opacity: 0.5; transition: all 0.3s; border: 2px solid transparent;
            flex-shrink: 0;
        }
        .thumb-strip img:hover { opacity: 0.8; }
        .thumb-strip img.active { opacity: 1; border-color: var(--theme-primary, #3b82f6); }

        /* Skeleton loader */
        .skeleton { background: linear-gradient(90deg, #1a2744 25%, #243556 50%, #1a2744 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 12px; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

        /* Lightbox */
        .gallery-lightbox {
            position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 10000;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(8px);
        }
        .gallery-lightbox.show { display: flex; }
        .gallery-lightbox img { max-width: 90%; max-height: 85vh; border-radius: 12px; object-fit: contain; }
        .gallery-lightbox .lb-close { position: absolute; top: 1rem; right: 1.5rem; color: #fff; font-size: 2rem; cursor: pointer; background: none; border: none; z-index: 10; }
        .gallery-lightbox .lb-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); background: rgba(0,0,0,0.5); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.3s; }
        .gallery-lightbox .lb-nav:hover { background: var(--theme-primary, #3b82f6); border-color: var(--theme-primary, #3b82f6); }
        .gallery-lightbox .lb-prev { left: 1rem; }
        .gallery-lightbox .lb-next { right: 1rem; }

        /* WhatsApp */
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s; }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }

        /* Footer */
        .site-footer { background: #1a1a2e; color: #fff; margin-top: 0; }
        .footer-cta { background: #0f2557; padding: 4rem 0; text-align: center; }
        .footer-cta h2 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2.2rem; color: #fff; margin-bottom: 1rem; }
        .footer-cta p { color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto 1.5rem; }
        .footer-heading { text-transform: uppercase; font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 1rem; position: relative; padding-bottom: 0.5rem; color: #fff; }
        .footer-heading::after { content: ''; position: absolute; bottom: 0; left: 0; width: 30px; height: 2px; background: var(--theme-primary, #1e40af); }
        .footer-link { color: rgba(255,255,255,0.65); text-decoration: none; transition: color 0.2s; font-size: 0.9rem; }
        .footer-link:hover { color: #fff; }
        .footer-social a { width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.3); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; font-size: 0.9rem; }
        .footer-social a:hover { background: var(--theme-primary, #1e40af); border-color: var(--theme-primary, #1e40af); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .gallery-3col { flex-direction: column !important; }
            .hero-panel { min-height: auto; padding: 2rem 1.5rem; }
            .hero-panel h1 { font-size: 1.8rem; }
            .category-grid { padding: 1rem; }
            .category-grid-inner { grid-template-columns: repeat(4, 1fr); }
            .cat-card { aspect-ratio: auto; height: 100px; }
            .slider-panel { min-height: auto; padding: 1rem; }
        }
        @media (max-width: 767.98px) {
            .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; }
            .category-grid-inner { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
            .hero-panel { padding: 1.5rem 1rem; min-height: auto; }
            .hero-panel h1 { font-size: 1.5rem; }
            .slider-main img { min-height: 250px; }
        }
        @media (max-width: 575.98px) {
            .navbar-brand { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .navbar-collapse .d-flex { flex-direction: column; width: 100%; gap: 0.5rem; margin-top: 0.75rem; }
            .notif-bell-btn, .login-nav-btn { width: 100%; text-align: center; display: block; }
            .category-grid-inner { grid-template-columns: repeat(2, 1fr); }
            .whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 16px; right: 16px; }
            .site-footer .row > div { text-align: center; }
            .footer-heading::after { left: 50%; transform: translateX(-50%); }
            .footer-social { justify-content: center; }
            .site-footer { border-radius: 20px 20px 0 0; }
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar d-none d-lg-block">
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
        <a class="navbar-brand d-flex align-items-center text-white" href="/">
            <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="width:40px;height:40px;border-radius:8px;object-fit:cover;"><?php else: ?><i class="bi bi-mortarboard-fill" style="font-size:1.5rem;"></i><?php endif; ?>
        </a>
        <button class="navbar-toggler border-0 p-1" data-bs-toggle="collapse" data-bs-target="#mainNav"><i class="bi bi-list text-white" style="font-size:1.8rem;"></i></button>
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
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background:#1a1a2e;color:#fff;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-bell-fill text-danger me-2"></i>Latest Notifications</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <?php if (empty($bellNotifs)): ?>
                    <p class="text-muted text-center py-3">No recent notifications.</p>
                <?php else: ?>
                    <?php foreach ($bellNotifs as $bn):
                        $typeColors = ['urgent' => 'danger', 'exam' => 'warning', 'academic' => 'info', 'event' => 'success'];
                        $bColor = $typeColors[$bn['type']] ?? 'secondary';
                    ?>
                    <div class="d-flex justify-content-between align-items-start p-2 rounded-3 mb-2" style="background:rgba(255,255,255,0.05);">
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
                <a href="/public/notifications.php" class="btn btn-primary btn-sm w-100 rounded-3"><i class="bi bi-list-ul me-1"></i>View All</a>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ PREMIUM GALLERY ═══════════ -->
<section class="gallery-container">
    <?php if ($showParticles === '1'): ?>
    <div class="particles" id="particles"></div>
    <?php endif; ?>

    <div class="d-flex gallery-3col" style="min-height: calc(100vh - 120px);">
        <!-- Left Hero Panel -->
        <div class="hero-panel" style="flex: 0 0 25%; max-width: 25%;">
            <div style="position:relative; z-index:1;">
                <div class="mb-3">
                    <span style="background: rgba(59,130,246,0.15); color: var(--theme-primary, #3b82f6); padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">Gallery</span>
                </div>
                <h1><?= e(getSetting('gallery_hero_title', 'Photo Gallery')) ?></h1>
                <p><?= e(getSetting('gallery_hero_subtitle', 'Explore moments from ' . $schoolName)) ?></p>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div style="font-size:0.8rem; color: rgba(255,255,255,0.4);">
                        <span id="totalPhotos">0</span> Photos · <span id="totalCategories"><?= count($categories) ?></span> Categories
                    </div>
                </div>
                <div class="scroll-arrow" onclick="document.querySelector('.category-grid').scrollIntoView({behavior:'smooth'})">
                    <i class="bi bi-arrow-down" style="font-size:1.2rem;"></i>
                </div>
            </div>
        </div>

        <!-- Middle: Category Grid -->
        <div class="category-grid" style="flex: 0 0 30%; max-width: 30%;">
            <h6 style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1.5px; color:rgba(255,255,255,0.4); font-weight:600;">Browse Categories</h6>
            <div class="category-grid-inner">
                <?php foreach ($categories as $cat): ?>
                <div class="cat-card <?= ($selectedCat === $cat['slug'] || (!$selectedCat && $cat === reset($categories))) ? 'active' : '' ?>"
                     data-slug="<?= e($cat['slug']) ?>" data-name="<?= e($cat['name']) ?>" onclick="selectCategory(this)">
                    <?php if ($cat['cover_image']): ?>
                        <img src="/<?= e($cat['cover_image']) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="cat-card-placeholder" style="width:100%;height:100%;">
                            <i class="bi bi-images"></i>
                        </div>
                    <?php endif; ?>
                    <div class="cat-overlay">
                        <span class="cat-label"><?= e($cat['name']) ?></span>
                    </div>
                    <span class="cat-count" data-slug="<?= e($cat['slug']) ?>">0</span>
                </div>
                <?php endforeach; ?>

                <?php if (empty($categories)): ?>
                <div class="text-center py-4" style="grid-column: span 2; color: rgba(255,255,255,0.3);">
                    <i class="bi bi-images" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0" style="font-size:0.85rem;">No categories yet</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Featured Slider -->
        <div class="slider-panel" style="flex: 1;">
            <div class="slider-title">
                <span>Featured</span>
                <span class="cat-badge" id="sliderCatLabel"><?= e(!empty($categories) ? $categories[0]['name'] : 'Gallery') ?></span>
            </div>

            <div class="slider-main" id="sliderMain">
                <div class="skeleton" style="width:100%;height:100%;min-height:350px;" id="sliderSkeleton"></div>
                <img src="" alt="Gallery" id="sliderImg" style="display:none;" onclick="openLightbox(this.src)">
                <div class="caption-bar" id="sliderCaption" style="display:none;">
                    <h6 id="sliderImgTitle"></h6>
                    <small id="sliderImgDesc"></small>
                </div>
            </div>

            <div class="slider-nav">
                <button onclick="prevSlide()" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <span class="slider-counter"><span id="slideIdx">1</span> / <span id="slideTotal">0</span></span>
                <button onclick="nextSlide()" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="thumb-strip" id="thumbStrip"></div>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="gallery-lightbox" id="galleryLightbox">
    <button class="lb-close" onclick="closeLightbox()">&times;</button>
    <button class="lb-nav lb-prev" onclick="lbPrev()"><i class="bi bi-chevron-left"></i></button>
    <img id="lbImg" src="" alt="Gallery">
    <button class="lb-nav lb-next" onclick="lbNext()"><i class="bi bi-chevron-right"></i></button>
</div>

<?php include __DIR__ . '/../includes/public-footer.php'; ?>

<script>
// ── Data & State ──
let allImages = {};
let currentCategory = '<?= e(!empty($categories) ? $categories[0]['slug'] : '') ?>';
let currentSlide = 0;
let currentImages = [];

// ── Init: Fetch all approved images ──
(async function() {
    try {
        const resp = await fetch('/public/gallery.php?ajax=images');
        const data = await resp.json();
        allImages = data.categories || {};
        document.getElementById('totalPhotos').textContent = data.total || 0;

        // Update category counts
        document.querySelectorAll('.cat-count').forEach(el => {
            const slug = el.dataset.slug;
            el.textContent = (allImages[slug] || []).length;
        });

        // Load first category
        if (currentCategory) loadCategoryImages(currentCategory);
    } catch(e) { console.error('Gallery load failed', e); }
})();

function selectCategory(el) {
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    const slug = el.dataset.slug;
    const name = el.dataset.name;
    currentCategory = slug;
    document.getElementById('sliderCatLabel').textContent = name;
    loadCategoryImages(slug);
}

function loadCategoryImages(slug) {
    currentImages = allImages[slug] || [];
    currentSlide = 0;
    renderSlider();
    renderThumbs();
}

function renderSlider() {
    const skeleton = document.getElementById('sliderSkeleton');
    const img = document.getElementById('sliderImg');
    const caption = document.getElementById('sliderCaption');
    const total = document.getElementById('slideTotal');
    const idx = document.getElementById('slideIdx');

    if (currentImages.length === 0) {
        skeleton.style.display = 'block';
        img.style.display = 'none';
        caption.style.display = 'none';
        total.textContent = '0';
        idx.textContent = '0';
        return;
    }

    skeleton.style.display = 'none';
    const item = currentImages[currentSlide];
    img.style.opacity = '0';
    img.src = '/' + item.file_path;
    img.alt = item.title;
    img.style.display = 'block';
    setTimeout(() => { img.style.opacity = '1'; }, 50);

    document.getElementById('sliderImgTitle').textContent = item.title || '';
    document.getElementById('sliderImgDesc').textContent = item.event_name || item.category || '';
    caption.style.display = 'block';

    total.textContent = currentImages.length;
    idx.textContent = currentSlide + 1;

    // Update active thumb
    document.querySelectorAll('.thumb-strip img').forEach((t, i) => {
        t.classList.toggle('active', i === currentSlide);
    });
}

function renderThumbs() {
    const strip = document.getElementById('thumbStrip');
    strip.innerHTML = '';
    currentImages.forEach((item, i) => {
        if (item.file_type === 'video') return;
        const img = document.createElement('img');
        img.src = '/' + item.file_path;
        img.alt = item.title;
        img.loading = 'lazy';
        if (i === currentSlide) img.classList.add('active');
        img.onclick = () => { currentSlide = i; renderSlider(); };
        strip.appendChild(img);
    });
}

function nextSlide() { if (currentImages.length) { currentSlide = (currentSlide + 1) % currentImages.length; renderSlider(); } }
function prevSlide() { if (currentImages.length) { currentSlide = (currentSlide - 1 + currentImages.length) % currentImages.length; renderSlider(); } }

// ── Lightbox ──
function openLightbox(src) {
    document.getElementById('lbImg').src = src;
    document.getElementById('galleryLightbox').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('galleryLightbox').classList.remove('show');
    document.body.style.overflow = '';
}
function lbNext() { nextSlide(); document.getElementById('lbImg').src = '/' + currentImages[currentSlide].file_path; }
function lbPrev() { prevSlide(); document.getElementById('lbImg').src = '/' + currentImages[currentSlide].file_path; }

// Keyboard
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') { if (document.getElementById('galleryLightbox').classList.contains('show')) lbNext(); else nextSlide(); }
    if (e.key === 'ArrowLeft') { if (document.getElementById('galleryLightbox').classList.contains('show')) lbPrev(); else prevSlide(); }
});

// Touch swipe
(function() {
    let startX = 0;
    const lb = document.getElementById('galleryLightbox');
    lb.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', e => {
        const diff = e.changedTouches[0].clientX - startX;
        if (Math.abs(diff) > 60) { diff > 0 ? lbPrev() : lbNext(); }
    });
    // Slider swipe
    const slider = document.getElementById('sliderMain');
    slider.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', e => {
        const diff = e.changedTouches[0].clientX - startX;
        if (Math.abs(diff) > 60) { diff > 0 ? prevSlide() : nextSlide(); }
    });
})();

// ── Particles ──
<?php if ($showParticles === '1'): ?>
(function() {
    const container = document.getElementById('particles');
    if (!container) return;
    for (let i = 0; i < 40; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left = Math.random() * 100 + '%';
        p.style.top = Math.random() * 100 + '%';
        p.style.animationDelay = Math.random() * 6 + 's';
        p.style.animationDuration = (4 + Math.random() * 4) + 's';
        container.appendChild(p);
    }
})();
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
