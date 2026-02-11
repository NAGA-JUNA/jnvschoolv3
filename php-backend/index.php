<?php
require_once __DIR__ . '/includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$schoolAddress = getSetting('school_address', '');
$admissionOpen = getSetting('admission_open', '0');
$whatsappNumber = getSetting('whatsapp_api_number', '');

// If logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
    exit;
}

// Get active slides
$slides = $db->query("SELECT * FROM home_slider WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();

// Get core team members
$coreTeam = $db->query("SELECT * FROM teachers WHERE status='active' AND is_core_team=1 ORDER BY FIELD(designation,'Principal','Director','Correspondent','Vice Principal','Teacher'), name ASC")->fetchAll();

// Get upcoming events (next 3)
$events = $db->query("SELECT title, event_date, location FROM events WHERE is_public=1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3")->fetchAll();

// Get latest notifications (3 for section)
$notifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Get latest 5 notifications for bell popup
$bellNotifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Notification count (last 7 days)
$notifCount = $db->query("SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Stats
$totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
$totalTeachers = $db->query("SELECT COUNT(*) FROM teachers WHERE status='active'")->fetchColumn();

// Ad popup
$popupAdActive = getSetting('popup_ad_active', '0');
$popupAdImage = getSetting('popup_ad_image', '');

// Nav logo
$navLogo = getSetting('school_logo', '');
$logoPath = ($navLogo && strpos($navLogo, '/uploads/') === 0) ? $navLogo : '/uploads/logo/' . $navLogo;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($schoolName) ?> — <?= e($schoolTagline) ?></title>
    <meta name="description" content="<?= e($schoolName) ?> — <?= e($schoolTagline) ?>. Official school website for admissions, notifications, gallery, and events.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }

        /* Top Bar */
        .top-bar { background: #0a0f1a; color: #fff; padding: 0.4rem 0; font-size: 0.78rem; }
        .top-bar a { color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .marquee-text { white-space: nowrap; overflow: hidden; }
        .marquee-text span { display: inline-block; animation: marqueeScroll 20s linear infinite; }
        @keyframes marqueeScroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* Main Navbar */
        .main-navbar { background: #0f172a; padding: 0.5rem 0; }
        .main-navbar .nav-link { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 0.9rem; padding: 0.5rem 0.8rem; }
        .main-navbar .nav-link:hover, .main-navbar .nav-link.active { color: #fff; }
        .notif-bell-btn { background: #dc3545; color: #fff; border: none; border-radius: 8px; padding: 0.4rem 0.9rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; position: relative; transition: background 0.2s; }
        .notif-bell-btn:hover { background: #c82333; }
        .notif-badge { position: absolute; top: -6px; right: -8px; background: #ffc107; color: #000; font-size: 0.65rem; font-weight: 700; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .login-nav-btn { background: transparent; border: 1.5px solid rgba(255,255,255,0.5); color: #fff; border-radius: 8px; padding: 0.4rem 1.2rem; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .login-nav-btn:hover { background: #fff; color: #0f172a; }

        /* Hero Slider */
        .hero-slider { position: relative; overflow: hidden; height: 520px; }
        .hero-slide {
            position: absolute; inset: 0; opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover; background-position: center;
        }
        .hero-slide.active { opacity: 1; }
        .hero-slide.anim-slide-left { transform: translateX(100%); opacity: 1; transition: transform 0.8s ease, opacity 0.5s ease; }
        .hero-slide.anim-slide-left.active { transform: translateX(0); opacity: 1; }
        .hero-slide.anim-slide-up { transform: translateY(30px); transition: transform 0.8s ease, opacity 0.6s ease; }
        .hero-slide.anim-slide-up.active { transform: translateY(0); opacity: 1; }
        .hero-slide.anim-zoom-in { transform: scale(0.95); transition: transform 1s ease, opacity 0.8s ease; }
        .hero-slide.anim-zoom-in.active { transform: scale(1); opacity: 1; }
        .hero-slide.anim-zoom-out { transform: scale(1.1); transition: transform 1s ease, opacity 0.8s ease; }
        .hero-slide.anim-zoom-out.active { transform: scale(1); opacity: 1; }
        @keyframes kenBurns { 0% { transform: scale(1); } 100% { transform: scale(1.08); } }
        .hero-slide.anim-ken-burns.active { animation: kenBurns 8s ease forwards; opacity: 1; }
        .hero-slide .content {
            position: relative; z-index: 2; color: #fff; height: 100%;
            display: flex; flex-direction: column; justify-content: center;
            padding: 0 2rem; max-width: 700px;
        }
        .hero-slide .badge-text {
            display: inline-block; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25); padding: 0.35rem 1rem;
            border-radius: 50px; font-size: 0.75rem; letter-spacing: 1px;
            text-transform: uppercase; font-weight: 600; margin-bottom: 1rem; width: fit-content;
        }
        .hero-slide h1 { font-size: 2.8rem; font-weight: 800; line-height: 1.15; margin-bottom: 1rem; }
        .hero-slide p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 1.5rem; }
        .hero-slide .cta-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #fff; color: #1e40af; padding: 0.7rem 1.8rem;
            border-radius: 50px; font-weight: 600; text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s; width: fit-content;
        }
        .hero-slide .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .slider-dots {
            position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
            z-index: 10; display: flex; gap: 0.5rem;
        }
        .slider-dots .dot {
            width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.4);
            cursor: pointer; transition: all 0.3s; border: none;
        }
        .slider-dots .dot.active { background: #fff; transform: scale(1.2); }
        .slider-arrow {
            position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
            background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.2); color: #fff;
            width: 48px; height: 48px; border-radius: 50%; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; transition: all 0.3s;
        }
        .slider-arrow:hover { background: rgba(255,255,255,0.3); }
        .slider-arrow.prev { left: 1.5rem; }
        .slider-arrow.next { right: 1.5rem; }
        .hero-fallback {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
            color: #fff; padding: 5rem 0; text-align: center;
        }

        /* Stats bar */
        .stats-bar { background: #0f172a; color: #fff; padding: 1rem 0; }
        .stat-item { text-align: center; }
        .stat-item .num { font-size: 1.5rem; font-weight: 700; }
        .stat-item .label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; }

        /* Section styling */
        .section-title { font-weight: 700; position: relative; display: inline-block; margin-bottom: 1.5rem; }
        .section-title::after {
            content: ''; position: absolute; bottom: -6px; left: 0;
            width: 50px; height: 3px; background: #1e40af; border-radius: 2px;
        }
        .feature-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .info-card { border: none; border-radius: 14px; transition: transform 0.2s, box-shadow 0.2s; }
        .info-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); }

        /* WhatsApp floating button */
        .whatsapp-float {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            width: 60px; height: 60px; border-radius: 50%; background: #25D366;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.8rem; text-decoration: none;
            box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s;
            animation: whatsappPulse 2s infinite;
        }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }
        @keyframes whatsappPulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); }
            50% { box-shadow: 0 4px 30px rgba(37,211,102,0.7); }
        }

        /* Ad popup */
        .ad-popup-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 10000;
            display: flex; align-items: center; justify-content: center;
        }
        .ad-popup-content { position: relative; max-width: 600px; width: 90%; }
        .ad-popup-content img { width: 100%; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .ad-popup-close {
            position: absolute; top: -12px; right: -12px; width: 36px; height: 36px;
            border-radius: 50%; background: #dc3545; color: #fff; border: 3px solid #fff;
            font-size: 1.1rem; cursor: pointer; display: flex; align-items: center;
            justify-content: center; z-index: 10001; transition: transform 0.2s;
        }
        .ad-popup-close:hover { transform: scale(1.1); }

        @media (max-width: 767.98px) {
            .hero-slider { height: 400px; }
            .hero-slide h1 { font-size: 1.8rem; }
            .slider-arrow { display: none; }
            .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; }
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="marquee-text flex-grow-1 me-3">
                <span>🎓 Welcome to <?= e($schoolName) ?> — <?= e($schoolTagline) ?></span>
            </div>
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
            <?php if ($navLogo): ?>
                <img src="<?= e($logoPath) ?>" alt="Logo" style="width:36px;height:36px;border-radius:8px;object-fit:cover;">
            <?php else: ?>
                <i class="bi bi-mortarboard-fill"></i>
            <?php endif; ?>
            <?= e($schoolName) ?>
        </a>
        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/teachers.php">Our Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="notif-bell-btn" data-bs-toggle="modal" data-bs-target="#notifModal">
                    <i class="bi bi-bell-fill me-1"></i> Notifications
                    <?php if ($notifCount > 0): ?>
                        <span class="notif-badge"><?= $notifCount > 9 ? '9+' : $notifCount ?></span>
                    <?php endif; ?>
                </button>
                <a href="/login.php" class="login-nav-btn"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
            </div>
        </div>
    </div>
</nav>

<!-- Notification Modal -->
<div class="modal fade" id="notifModal" tabindex="-1">
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
                        $color = $typeColors[$bn['type']] ?? 'secondary';
                    ?>
                    <div class="d-flex justify-content-between align-items-start p-2 rounded-3 mb-2" style="background:#f8fafc;">
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;"><?= e($bn['title']) ?></div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d M Y', strtotime($bn['created_at'])) ?></small>
                        </div>
                        <span class="badge bg-<?= $color ?>" style="font-size:0.7rem;"><?= e(ucfirst($bn['type'])) ?></span>
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

<!-- Ad Popup -->
<?php if ($popupAdActive === '1' && $popupAdImage): ?>
<div class="ad-popup-overlay" id="adPopup" style="display:none;">
    <div class="ad-popup-content">
        <button class="ad-popup-close" onclick="closeAdPopup()"><i class="bi bi-x"></i></button>
        <img src="/uploads/ads/<?= e($popupAdImage) ?>" alt="Advertisement">
    </div>
</div>
<script>
(function(){
    var key = 'popup_ad_dismissed_' + new Date().toISOString().slice(0,10);
    if (!localStorage.getItem(key)) {
        setTimeout(function(){ document.getElementById('adPopup').style.display = 'flex'; }, 1000);
    }
    window.closeAdPopup = function(){
        document.getElementById('adPopup').style.display = 'none';
        localStorage.setItem(key, '1');
    };
})();
</script>
<?php endif; ?>

<!-- Hero Slider -->
<?php if (!empty($slides)): ?>
<div class="hero-slider" id="heroSlider">
    <?php foreach ($slides as $i => $slide): ?>
    <?php
        $anim = $slide['animation_type'] ?? 'fade';
        $overlay = $slide['overlay_style'] ?? 'gradient-dark';
        $textPos = $slide['text_position'] ?? 'left';
        $opacity = ($slide['overlay_opacity'] ?? 70) / 100;
        $overlayMap = [
            'gradient-dark' => "linear-gradient(135deg, rgba(15,23,42,{$opacity}) 0%, rgba(30,64,175," . ($opacity * 0.7) . ") 100%)",
            'gradient-blue' => "linear-gradient(135deg, rgba(30,64,175,{$opacity}) 0%, rgba(59,130,246," . ($opacity * 0.7) . ") 100%)",
            'gradient-warm' => "linear-gradient(135deg, rgba(180,83,9,{$opacity}) 0%, rgba(234,88,12," . ($opacity * 0.7) . ") 100%)",
            'solid-dark' => "rgba(15,23,42,{$opacity})",
            'none' => 'transparent',
        ];
        $overlayBg = $overlayMap[$overlay] ?? $overlayMap['gradient-dark'];
        $alignMap = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'];
        $textAlign = $textPos === 'center' ? 'text-align:center;' : ($textPos === 'right' ? 'text-align:right;' : '');
        $contentAlign = $alignMap[$textPos] ?? 'flex-start';
    ?>
    <div class="hero-slide <?= $i === 0 ? 'active' : '' ?> anim-<?= e($anim) ?>" style="background-image: url('/<?= e($slide['image_path']) ?>');" data-index="<?= $i ?>">
        <div style="position:absolute;inset:0;background:<?= $overlayBg ?>;"></div>
        <div class="container h-100">
            <div class="content" style="align-items:<?= $contentAlign ?>;<?= $textAlign ?>">
                <?php if ($slide['badge_text']): ?>
                    <div class="badge-text"><?= e($slide['badge_text']) ?></div>
                <?php endif; ?>
                <?php if ($slide['title']): ?>
                    <h1><?= e($slide['title']) ?></h1>
                <?php endif; ?>
                <?php if ($slide['subtitle']): ?>
                    <p><?= e($slide['subtitle']) ?></p>
                <?php endif; ?>
                <?php if ($slide['cta_text'] && $slide['link_url']): ?>
                    <a href="<?= e($slide['link_url']) ?>" class="cta-btn"><?= e($slide['cta_text']) ?> <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (count($slides) > 1): ?>
    <button class="slider-arrow prev" onclick="changeSlide(-1)"><i class="bi bi-chevron-left"></i></button>
    <button class="slider-arrow next" onclick="changeSlide(1)"><i class="bi bi-chevron-right"></i></button>
    <div class="slider-dots">
        <?php for ($i = 0; $i < count($slides); $i++): ?>
            <button class="dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $i ?>)"></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="hero-fallback">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= e($schoolName) ?></h1>
        <p class="lead opacity-75 mb-4"><?= e($schoolTagline) ?></p>
        <?php if ($admissionOpen === '1'): ?>
            <a href="/public/admission-form.php" class="btn btn-light btn-lg rounded-pill px-4 fw-semibold">Apply for Admission <i class="bi bi-arrow-right ms-1"></i></a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3"><div class="stat-item"><div class="num"><?= $totalStudents ?>+</div><div class="label">Students</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-item"><div class="num"><?= $totalTeachers ?>+</div><div class="label">Teachers</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-item"><div class="num">12</div><div class="label">Classes</div></div></div>
            <div class="col-6 col-md-3"><div class="stat-item"><div class="num">100%</div><div class="label">Dedication</div></div></div>
        </div>
    </div>
</div>

<!-- Quick Links Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card info-card h-100 text-center p-4">
                    <div class="feature-icon bg-primary-subtle text-primary mx-auto mb-3"><i class="bi bi-mortarboard-fill"></i></div>
                    <h6 class="fw-bold">Admissions</h6>
                    <p class="text-muted small mb-3">Apply online for admission to JNV School.</p>
                    <a href="/public/admission-form.php" class="btn btn-sm btn-outline-primary mt-auto">Apply Now</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card info-card h-100 text-center p-4">
                    <div class="feature-icon bg-warning-subtle text-warning mx-auto mb-3"><i class="bi bi-bell-fill"></i></div>
                    <h6 class="fw-bold">Notifications</h6>
                    <p class="text-muted small mb-3">Stay updated with latest announcements.</p>
                    <a href="/public/notifications.php" class="btn btn-sm btn-outline-warning mt-auto">View All</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card info-card h-100 text-center p-4">
                    <div class="feature-icon bg-success-subtle text-success mx-auto mb-3"><i class="bi bi-images"></i></div>
                    <h6 class="fw-bold">Gallery</h6>
                    <p class="text-muted small mb-3">Explore photos & videos from school life.</p>
                    <a href="/public/gallery.php" class="btn btn-sm btn-outline-success mt-auto">Browse</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card info-card h-100 text-center p-4">
                    <div class="feature-icon bg-danger-subtle text-danger mx-auto mb-3"><i class="bi bi-calendar-event-fill"></i></div>
                    <h6 class="fw-bold">Events</h6>
                    <p class="text-muted small mb-3">Check upcoming school events & dates.</p>
                    <a href="/public/events.php" class="btn btn-sm btn-outline-danger mt-auto">View Events</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Notifications & Upcoming Events -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <h4 class="section-title">Latest Notifications</h4>
                <?php if (empty($notifs)): ?>
                    <p class="text-muted">No recent notifications.</p>
                <?php else: ?>
                    <?php foreach ($notifs as $n):
                        $typeColors = ['urgent' => 'danger', 'exam' => 'warning', 'academic' => 'info', 'event' => 'success'];
                        $color = $typeColors[$n['type']] ?? 'secondary';
                    ?>
                    <div class="card info-card mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-semibold mb-1"><?= e($n['title']) ?></h6>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($n['created_at'])) ?></small>
                                </div>
                                <span class="badge bg-<?= $color ?>"><?= e(ucfirst($n['type'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <a href="/public/notifications.php" class="btn btn-sm btn-outline-primary">View All Notifications →</a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <h4 class="section-title">Upcoming Events</h4>
                <?php if (empty($events)): ?>
                    <p class="text-muted">No upcoming events.</p>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                    <div class="card info-card mb-3">
                        <div class="card-body py-3 d-flex gap-3 align-items-center">
                            <div class="text-center flex-shrink-0" style="width:50px;">
                                <div class="fw-bold text-primary" style="font-size:1.3rem;line-height:1;"><?= date('d', strtotime($ev['event_date'])) ?></div>
                                <small class="text-muted text-uppercase" style="font-size:0.65rem;"><?= date('M', strtotime($ev['event_date'])) ?></small>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0"><?= e($ev['title']) ?></h6>
                                <?php if ($ev['location']): ?><small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?></small><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <a href="/public/events.php" class="btn btn-sm btn-outline-primary">View All Events →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Our Core Team -->
<?php if (!empty($coreTeam)): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="section-title fw-bold mb-0" style="font-size:1.6rem;">Our Core Team</h4>
            <a href="/public/teachers.php" class="btn btn-warning fw-bold px-4 rounded-1 text-uppercase" style="font-size:0.8rem;letter-spacing:1px;">View Our Teachers</a>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($coreTeam as $ct):
                $ctPhoto = $ct['photo'] ? (str_starts_with($ct['photo'], '/uploads/') ? $ct['photo'] : '/uploads/photos/'.$ct['photo']) : '';
            ?>
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="card border-0 shadow-sm text-center h-100" style="border-radius:14px;">
                    <div class="card-body p-4">
                        <?php if ($ctPhoto): ?>
                            <img src="<?= e($ctPhoto) ?>" alt="<?= e($ct['name']) ?>" class="rounded mb-3" style="width:100%;height:280px;object-fit:cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:100%;height:280px;background:linear-gradient(135deg,#e2e8f0,#cbd5e1);border-radius:10px;">
                                <i class="bi bi-person-fill" style="font-size:5rem;color:#94a3b8;"></i>
                            </div>
                        <?php endif; ?>
                        <h6 class="fw-bold mb-1"><?= e($ct['name']) ?></h6>
                        <small class="text-primary d-block mb-2"><?= e($ct['designation'] ?? 'Teacher') ?></small>
                        <?php if ($ct['email']): ?>
                            <hr class="my-2">
                            <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= e($ct['email']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Info -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h4 class="section-title">Contact Us</h4>
                <p class="text-muted mb-4">Have questions? Reach out to us anytime.</p>
                <div class="d-flex flex-column gap-3">
                    <?php if ($schoolAddress): ?>
                    <div class="d-flex gap-3"><div class="feature-icon bg-primary-subtle text-primary flex-shrink-0" style="width:40px;height:40px;border-radius:10px;font-size:1rem;"><i class="bi bi-geo-alt-fill"></i></div><div><strong>Address</strong><br><span class="text-muted"><?= e($schoolAddress) ?></span></div></div>
                    <?php endif; ?>
                    <?php if ($schoolPhone): ?>
                    <div class="d-flex gap-3"><div class="feature-icon bg-success-subtle text-success flex-shrink-0" style="width:40px;height:40px;border-radius:10px;font-size:1rem;"><i class="bi bi-telephone-fill"></i></div><div><strong>Phone</strong><br><a href="tel:<?= e($schoolPhone) ?>" class="text-muted text-decoration-none"><?= e($schoolPhone) ?></a></div></div>
                    <?php endif; ?>
                    <?php if ($schoolEmail): ?>
                    <div class="d-flex gap-3"><div class="feature-icon bg-warning-subtle text-warning flex-shrink-0" style="width:40px;height:40px;border-radius:10px;font-size:1rem;"><i class="bi bi-envelope-fill"></i></div><div><strong>Email</strong><br><a href="mailto:<?= e($schoolEmail) ?>" class="text-muted text-decoration-none"><?= e($schoolEmail) ?></a></div></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-mortarboard-fill text-primary" style="font-size:3rem;"></i>
                        <h5 class="fw-bold mt-3"><?= e($schoolName) ?></h5>
                        <p class="text-muted"><?= e($schoolTagline) ?></p>
                        <?php if ($admissionOpen === '1'): ?>
                            <a href="/public/admission-form.php" class="btn btn-primary rounded-pill px-4">Apply for Admission</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                    <?php if ($navLogo): ?>
                        <img src="<?= e($logoPath) ?>" alt="Logo" style="width:28px;height:28px;border-radius:6px;object-fit:cover;">
                    <?php else: ?>
                        <i class="bi bi-mortarboard-fill"></i>
                    <?php endif; ?>
                    <?= e($schoolName) ?>
                </h6>
                <p class="text-muted small mb-0"><?= e($schoolTagline) ?></p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-2">Quick Links</h6>
                <ul class="list-unstyled mb-0 small">
                    <li><a href="/public/notifications.php" class="text-muted text-decoration-none">Notifications</a></li>
                    <li><a href="/public/gallery.php" class="text-muted text-decoration-none">Gallery</a></li>
                    <li><a href="/public/events.php" class="text-muted text-decoration-none">Events</a></li>
                    <li><a href="/public/admission-form.php" class="text-muted text-decoration-none">Admissions</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-2">Contact</h6>
                <ul class="list-unstyled mb-0 small text-muted">
                    <?php if ($schoolEmail): ?><li><i class="bi bi-envelope me-1"></i><?= e($schoolEmail) ?></li><?php endif; ?>
                    <?php if ($schoolPhone): ?><li><i class="bi bi-telephone me-1"></i><?= e($schoolPhone) ?></li><?php endif; ?>
                </ul>
            </div>
        </div>
        <hr class="border-secondary mt-3 mb-2">
        <div class="text-center">
            <small class="text-muted">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved. | Powered by JNV School Management System</small>
        </div>
    </div>
</footer>

<!-- WhatsApp Float -->
<?php if ($whatsappNumber): ?>
<a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $whatsappNumber)) ?>" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (count($slides) > 1): ?>
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.slider-dots .dot');
const totalSlides = slides.length;
let autoPlay;

function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    if (dots.length) dots[currentSlide].classList.remove('active');
    currentSlide = (n + totalSlides) % totalSlides;
    slides[currentSlide].classList.add('active');
    if (dots.length) dots[currentSlide].classList.add('active');
}

function changeSlide(dir) {
    goToSlide(currentSlide + dir);
    resetAutoPlay();
}

function resetAutoPlay() {
    clearInterval(autoPlay);
    autoPlay = setInterval(() => changeSlide(1), 5000);
}

autoPlay = setInterval(() => changeSlide(1), 5000);
</script>
<?php endif; ?>
</body>
</html>