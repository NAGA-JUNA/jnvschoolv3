<?php
require_once __DIR__ . '/../includes/auth.php';
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

// About content
$aboutHistory = getSetting('about_history', 'Our school was established with a vision to provide quality education to students from diverse backgrounds. Over the years, we have grown into a premier educational institution known for academic excellence and holistic development.');
$aboutVision = getSetting('about_vision', 'To be a center of excellence in education, nurturing future leaders who are academically proficient, morally upright, and socially responsible.');
$aboutMission = getSetting('about_mission', 'To provide quality education through innovative teaching methods, foster critical thinking, and develop well-rounded individuals who contribute positively to society.');

// Bell notifications
$bellNotifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
$notifCount = $db->query("SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us — <?= e($schoolName) ?></title>
    <meta name="description" content="Learn about <?= e($schoolName) ?> — our history, vision, mission, and core values. <?= e($schoolTagline) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        /* Hero */
        .about-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
            color: #fff; padding: 5rem 0 4rem; position: relative; overflow: hidden;
        }
        .about-hero::before {
            content: ''; position: absolute; top: -50%; right: -20%; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%); border-radius: 50%;
        }
        .about-hero::after {
            content: ''; position: absolute; bottom: -30%; left: -10%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%); border-radius: 50%;
        }
        .hero-badge {
            display: inline-block; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2); padding: 0.4rem 1.2rem;
            border-radius: 50px; font-size: 0.75rem; letter-spacing: 2px;
            text-transform: uppercase; font-weight: 600; margin-bottom: 1.5rem;
        }

        /* Content cards */
        .about-card {
            border: none; border-radius: 16px; overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .about-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
        .about-icon {
            width: 60px; height: 60px; border-radius: 14px; display: flex;
            align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .value-card {
            border: none; border-radius: 16px; text-align: center; padding: 2rem 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .value-card:hover { transform: translateY(-6px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
        .value-icon {
            width: 70px; height: 70px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 1rem;
        }
        .section-heading {
            font-weight: 800; position: relative; display: inline-block; margin-bottom: 0.5rem;
        }
        .section-heading::after {
            content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
            width: 60px; height: 4px; background: linear-gradient(90deg, #1e40af, #3b82f6); border-radius: 2px;
        }

        /* WhatsApp */
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s; animation: whatsappPulse 2s infinite; }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }
        @keyframes whatsappPulse { 0%, 100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); } 50% { box-shadow: 0 4px 30px rgba(37,211,102,0.7); } }

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

        @media (max-width: 767.98px) {
            .about-hero { padding: 3rem 0 2.5rem; }
            .about-hero h1 { font-size: 2rem; }
            .top-bar .d-flex { flex-direction: column; gap: 0.3rem; text-align: center; }
        }
        @media (max-width: 575.98px) {
            .navbar-brand { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .navbar-collapse .d-flex { flex-direction: column; width: 100%; gap: 0.5rem; margin-top: 0.75rem; }
            .notif-bell-btn, .login-nav-btn { width: 100%; text-align: center; display: block; }
            .top-bar .d-flex.gap-3 { font-size: 0.7rem; gap: 0.4rem !important; }
            .about-hero { padding: 2.5rem 0 2rem; }
            .about-hero h1 { font-size: 1.8rem; }
            .about-icon { width: 48px; height: 48px; font-size: 1.2rem; }
            .value-card { padding: 1.2rem 1rem; }
            .value-icon { width: 56px; height: 56px; font-size: 1.4rem; }
            .value-card h5 { font-size: 1rem; }
            .col-lg-5.col-md-6 { flex: 0 0 100%; max-width: 100%; }
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
                <li class="nav-item"><a class="nav-link active" href="/public/about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/teachers.php">Our Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
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

<!-- Hero Section -->
<section class="about-hero">
    <div class="container position-relative text-center" style="z-index:2;">
        <div class="hero-badge"><i class="bi bi-info-circle me-2"></i>About Our School</div>
        <h1 class="display-4 mb-3" style="font-weight:900;">About Us</h1>
        <p class="lead opacity-75 mx-auto" style="max-width:600px;">Discover our story, vision, and the values that drive us to provide exceptional education.</p>
    </div>
</section>

<!-- History Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card about-card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-start gap-4 flex-column flex-md-row">
                            <div class="about-icon bg-primary-subtle text-primary flex-shrink-0">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-3">Our History</h3>
                                <p class="text-muted mb-0" style="line-height:1.8;"><?= nl2br(e($aboutHistory)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="py-5" style="background:#f1f5f9;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-heading">Our Purpose</h2>
            <p class="text-muted mt-3">Guided by our vision and driven by our mission</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-5 col-md-6">
                <div class="card about-card shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="about-icon bg-info-subtle text-info mx-auto mb-3">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Our Vision</h4>
                        <p class="text-muted mb-0" style="line-height:1.8;"><?= nl2br(e($aboutVision)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="card about-card shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="about-icon bg-success-subtle text-success mx-auto mb-3">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Our Mission</h4>
                        <p class="text-muted mb-0" style="line-height:1.8;"><?= nl2br(e($aboutMission)) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-heading">Our Core Values</h2>
            <p class="text-muted mt-3">The principles that guide everything we do</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card value-card shadow-sm h-100">
                    <div class="value-icon bg-warning-subtle text-warning"><i class="bi bi-trophy"></i></div>
                    <h5 class="fw-bold">Excellence</h5>
                    <p class="text-muted small mb-0">We strive for the highest standards in academics, character, and personal growth.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card value-card shadow-sm h-100">
                    <div class="value-icon bg-danger-subtle text-danger"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold">Integrity</h5>
                    <p class="text-muted small mb-0">We foster honesty, transparency, and ethical behavior in all our actions.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card value-card shadow-sm h-100">
                    <div class="value-icon bg-primary-subtle text-primary"><i class="bi bi-lightbulb"></i></div>
                    <h5 class="fw-bold">Innovation</h5>
                    <p class="text-muted small mb-0">We embrace creativity and modern teaching methods to inspire learning.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card value-card shadow-sm h-100">
                    <div class="value-icon bg-success-subtle text-success"><i class="bi bi-people"></i></div>
                    <h5 class="fw-bold">Community</h5>
                    <p class="text-muted small mb-0">We build a supportive, inclusive environment where everyone belongs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gradient Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 py-5">
            <div class="col-lg-3 col-md-6">
                <div style="background:linear-gradient(135deg,rgba(106,17,203,0.3),rgba(139,92,246,0.3));border:2px solid rgba(255,255,255,0.2);border-radius:16px;padding:1.5rem;text-align:center;">
                    <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="width:60px;height:60px;border-radius:12px;object-fit:cover;margin-bottom:0.8rem;"><?php else: ?><i class="bi bi-mortarboard-fill" style="font-size:2.5rem;display:block;margin-bottom:0.5rem;"></i><?php endif; ?>
                    <h5 class="fw-bold mb-1" style="font-size:1rem;"><?= e($schoolName) ?></h5>
                    <small class="opacity-75">India</small>
                </div>
                <?php if ($schoolAddress): ?><p class="small opacity-75 mt-3 mb-0"><?= e($schoolAddress) ?></p><?php endif; ?>
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
                    <li class="mb-1"><a href="/" class="text-white text-decoration-none opacity-75 footer-link">Home</a></li>
                    <li class="mb-1"><a href="/public/about.php" class="text-white text-decoration-none opacity-75 footer-link">About Us</a></li>
                    <li class="mb-1"><a href="/public/teachers.php" class="text-white text-decoration-none opacity-75 footer-link">Our Teachers</a></li>
                    <li class="mb-1"><a href="/public/gallery.php" class="text-white text-decoration-none opacity-75 footer-link">Gallery</a></li>
                    <li class="mb-1"><a href="/public/events.php" class="text-white text-decoration-none opacity-75 footer-link">Events</a></li>
                    <li class="mb-1"><a href="/public/admission-form.php" class="text-white text-decoration-none opacity-75 footer-link">Apply Now</a></li>
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
</body>
</html>