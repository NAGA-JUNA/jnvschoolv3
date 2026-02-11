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

$events = $db->query("SELECT * FROM events WHERE is_public=1 AND event_date>=CURDATE() ORDER BY event_date ASC")->fetchAll();
$pastEvents = $db->query("SELECT * FROM events WHERE is_public=1 AND event_date<CURDATE() ORDER BY event_date DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events — <?= e($schoolName) ?></title>
    <?php $favicon = getSetting('school_favicon', ''); if ($favicon): ?><link rel="icon" href="/uploads/logo/<?= e($favicon) ?>"><?php endif; ?>
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
        .hero-banner { background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); color: #fff; padding: 3rem 0; }
        .event-card { border: none; border-radius: 12px; border-left: 4px solid #3b82f6; transition: transform 0.2s; }
        .event-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .date-box { width: 60px; height: 60px; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 700; }
        /* Dark Footer */
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
        @media (max-width: 767.98px) {
            .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; }
        }
        @media (max-width: 575.98px) {
            .navbar-brand { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .navbar-collapse .d-flex { flex-direction: column; width: 100%; gap: 0.5rem; margin-top: 0.75rem; }
            .notif-bell-btn, .login-nav-btn { width: 100%; text-align: center; display: block; }
            .top-bar .d-flex.gap-3 { font-size: 0.7rem; gap: 0.4rem !important; }
            .hero-banner { padding: 2rem 0; }
            .hero-banner h1 { font-size: 1.5rem; }
            .date-box { width: 48px; height: 48px; }
            .date-box span:first-child { font-size: 1rem !important; }
            .event-card .card-body { padding: 0.75rem; }
            .event-card h5 { font-size: 1rem; }
            .site-footer .row > div { text-align: center; }
            .footer-heading::after { left: 50%; transform: translateX(-50%); }
            .footer-social { justify-content: center; }
            .site-footer { border-radius: 20px 20px 0 0; }
            .whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 16px; right: 16px; }
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
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/events.php">Events</a></li>
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

<!-- Hero -->
<div class="hero-banner">
    <div class="container">
        <h1 class="fw-bold mb-2"><i class="bi bi-calendar-event-fill me-2"></i><?= e(getSetting('events_hero_title', 'Events')) ?></h1>
        <p class="mb-0 opacity-75"><?= e(getSetting('events_hero_subtitle', 'Upcoming and past events at ' . $schoolName)) ?></p>
    </div>
</div>

<div class="container py-4">
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

<?php include __DIR__ . '/../includes/public-footer.php'; ?>
</body>
</html>