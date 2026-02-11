<?php
require_once __DIR__ . '/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$navLogo = getSetting('school_logo', '');
$logoPath = ($navLogo && strpos($navLogo, '/uploads/') === 0) ? $navLogo : '/uploads/logo/' . $navLogo;
$whatsappNumber = getSetting('whatsapp_api_number', '');

// If logged in, redirect
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
    exit;
}

// Get all active teachers
$teachers = $db->query("SELECT * FROM teachers WHERE status='active' ORDER BY is_core_team DESC, name ASC")->fetchAll();
$totalTeachers = count($teachers);

// Get principal for message section
$principal = $db->prepare("SELECT * FROM teachers WHERE status='active' AND designation='Principal' AND bio IS NOT NULL AND bio != '' LIMIT 1");
$principal->execute();
$principal = $principal->fetch();

// Notification count for bell
$notifCount = $db->query("SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$bellNotifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Teachers — <?= e($schoolName) ?></title>
    <meta name="description" content="Meet the dedicated educators at <?= e($schoolName) ?>. Our qualified and experienced teachers are committed to academic excellence.">
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

        /* Hero Section */
        .teachers-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
            color: #fff; padding: 5rem 0 4rem; position: relative; overflow: hidden;
        }
        .teachers-hero::before {
            content: ''; position: absolute; top: -50%; right: -20%; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%); border-radius: 50%;
        }
        .teachers-hero::after {
            content: ''; position: absolute; bottom: -30%; left: -10%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%); border-radius: 50%;
        }
        .hero-badge {
            display: inline-block; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2); padding: 0.4rem 1.2rem;
            border-radius: 50px; font-size: 0.75rem; letter-spacing: 2px;
            text-transform: uppercase; font-weight: 600; margin-bottom: 1.5rem;
        }
        .hero-stat-card {
            background: rgba(255,255,255,0.08); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.12); border-radius: 16px;
            padding: 1.5rem; text-align: center; transition: transform 0.3s;
        }
        .hero-stat-card:hover { transform: translateY(-4px); }
        .hero-stat-card .num { font-size: 2.5rem; font-weight: 800; line-height: 1; }
        .hero-stat-card .label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; margin-top: 0.3rem; }

        /* Principal Message */
        .principal-section { background: #fff; padding: 4rem 0; }
        .principal-photo {
            width: 100%; max-width: 350px; aspect-ratio: 3/4; object-fit: cover;
            border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }
        .quote-box {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-left: 4px solid #1e40af; border-radius: 0 16px 16px 0;
            padding: 2rem; position: relative;
        }
        .quote-box::before {
            content: '"'; position: absolute; top: -10px; left: 15px;
            font-size: 5rem; color: #1e40af; opacity: 0.15; font-family: Georgia, serif; line-height: 1;
        }

        /* Teacher Cards */
        .teachers-grid { padding: 4rem 0; }
        .teacher-card { perspective: 1000px; height: 380px; cursor: pointer; }
        .teacher-card-inner {
            position: relative; width: 100%; height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); transform-style: preserve-3d;
        }
        .teacher-card:hover .teacher-card-inner { transform: rotateY(180deg); }
        .teacher-card-front, .teacher-card-back {
            position: absolute; width: 100%; height: 100%;
            backface-visibility: hidden; border-radius: 16px; overflow: hidden;
        }
        .teacher-card-front { background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .teacher-card-front img { width: 100%; height: 260px; object-fit: cover; }
        .teacher-card-front .no-photo {
            width: 100%; height: 260px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #64748b; font-size: 4rem;
        }
        .teacher-card-front .info { padding: 1rem 1.2rem; text-align: center; }
        .teacher-card-front .info h6 { font-weight: 700; margin-bottom: 0.2rem; font-size: 1rem; }
        .teacher-card-front .info small { color: #64748b; font-size: 0.85rem; }
        .teacher-card-back {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff; transform: rotateY(180deg);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 2rem; text-align: center;
        }
        .teacher-card-back .back-icon {
            width: 70px; height: 70px; border-radius: 50%;
            background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin-bottom: 1rem;
        }
        .teacher-card-back h6 { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .teacher-card-back .detail { opacity: 0.85; font-size: 0.85rem; margin-bottom: 0.3rem; }
        .teacher-card-back .badge-pill {
            background: rgba(255,255,255,0.2); padding: 0.3rem 1rem;
            border-radius: 50px; font-size: 0.75rem; margin-top: 0.5rem; letter-spacing: 0.5px;
        }
        .section-heading { font-weight: 800; position: relative; display: inline-block; margin-bottom: 0.5rem; }
        .section-heading::after {
            content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
            width: 60px; height: 4px; background: linear-gradient(90deg, #1e40af, #3b82f6); border-radius: 2px;
        }

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

        @media (max-width: 767.98px) {
            .teachers-hero { padding: 3rem 0 2.5rem; }
            .teachers-hero h1 { font-size: 2rem; }
            .teacher-card { height: 350px; }
            .teacher-card-front img, .teacher-card-front .no-photo { height: 230px; }
            .principal-photo { max-width: 250px; }
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
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/teachers.php">Our Teachers</a></li>
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

<!-- Hero Section -->
<section class="teachers-hero">
    <div class="container position-relative" style="z-index:2;">
        <div class="text-center mb-4">
            <div class="hero-badge"><i class="bi bi-people-fill me-2"></i>Our Educators</div>
            <h1 class="display-4 fw-900 mb-3" style="font-weight:900;">Our Teachers</h1>
            <p class="lead opacity-75 mx-auto" style="max-width:600px;">Meet our dedicated team of qualified educators who inspire, guide, and shape the future of every student.</p>
        </div>
        <div class="row g-3 justify-content-center mt-4">
            <div class="col-6 col-md-3"><div class="hero-stat-card"><div class="num"><?= $totalTeachers ?>+</div><div class="label">Teachers</div></div></div>
            <div class="col-6 col-md-3"><div class="hero-stat-card"><div class="num">15+</div><div class="label">Years Experience</div></div></div>
            <div class="col-6 col-md-3"><div class="hero-stat-card"><div class="num">12</div><div class="label">Subjects</div></div></div>
            <div class="col-6 col-md-3"><div class="hero-stat-card"><div class="num">100%</div><div class="label">Dedication</div></div></div>
        </div>
    </div>
</section>

<!-- Principal's Message -->
<?php if ($principal): ?>
<section class="principal-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-heading">Principal's Message</h2>
        </div>
        <div class="row g-4 align-items-center justify-content-center">
            <div class="col-md-4 text-center">
                <?php
                    $pPhoto = $principal['photo'] ? (str_starts_with($principal['photo'], '/uploads/') ? $principal['photo'] : '/uploads/photos/'.$principal['photo']) : '';
                ?>
                <?php if ($pPhoto): ?>
                    <img src="<?= e($pPhoto) ?>" alt="<?= e($principal['name']) ?>" class="principal-photo">
                <?php else: ?>
                    <div class="principal-photo d-flex align-items-center justify-content-center mx-auto" style="background:linear-gradient(135deg,#e2e8f0,#cbd5e1);">
                        <i class="bi bi-person-fill" style="font-size:6rem;color:#94a3b8;"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-7">
                <div class="quote-box">
                    <p class="mb-3" style="font-size:1.05rem;line-height:1.8;color:#334155;">
                        <?= nl2br(e($principal['bio'])) ?>
                    </p>
                    <div class="d-flex align-items-center gap-3 mt-3 pt-3" style="border-top:1px solid rgba(30,64,175,0.1);">
                        <div>
                            <h6 class="fw-bold mb-0" style="color:#1e40af;"><?= e($principal['name']) ?></h6>
                            <small class="text-muted"><?= e($principal['designation'] ?? 'Principal') ?></small>
                            <?php if ($principal['qualification']): ?>
                                <small class="text-muted d-block"><?= e($principal['qualification']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Teachers Grid -->
<section class="teachers-grid" style="background:#f1f5f9;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-heading">Meet Our Faculty</h2>
            <p class="text-muted mt-3">Hover on a card to learn more about each teacher</p>
        </div>
        <div class="row g-4">
            <?php foreach ($teachers as $t):
                $tPhoto = $t['photo'] ? (str_starts_with($t['photo'], '/uploads/') ? $t['photo'] : '/uploads/photos/'.$t['photo']) : '';
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="teacher-card">
                    <div class="teacher-card-inner">
                        <div class="teacher-card-front">
                            <?php if ($tPhoto): ?>
                                <img src="<?= e($tPhoto) ?>" alt="<?= e($t['name']) ?>">
                            <?php else: ?>
                                <div class="no-photo"><i class="bi bi-person-fill"></i></div>
                            <?php endif; ?>
                            <div class="info">
                                <h6><?= e($t['name']) ?></h6>
                                <small><?= e($t['designation'] ?? 'Teacher') ?></small>
                            </div>
                        </div>
                        <div class="teacher-card-back">
                            <div class="back-icon"><i class="bi bi-mortarboard-fill"></i></div>
                            <h6><?= e($t['name']) ?></h6>
                            <?php if ($t['designation']): ?>
                                <div class="detail"><i class="bi bi-briefcase me-1"></i><?= e($t['designation']) ?></div>
                            <?php endif; ?>
                            <?php if ($t['qualification']): ?>
                                <div class="detail"><i class="bi bi-award me-1"></i><?= e($t['qualification']) ?></div>
                            <?php endif; ?>
                            <?php if ($t['subject']): ?>
                                <div class="detail"><i class="bi bi-book me-1"></i><?= e($t['subject']) ?></div>
                            <?php endif; ?>
                            <?php if ($t['experience_years']): ?>
                                <div class="badge-pill"><?= e($t['experience_years']) ?> Years Experience</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($teachers)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size:3rem;"></i>
                    <h5 class="text-muted mt-3">No teachers available</h5>
                    <p class="text-muted">Teacher profiles will appear here once added by the admin.</p>
                </div>
            <?php endif; ?>
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
                    <li><a href="/" class="text-muted text-decoration-none">Home</a></li>
                    <li><a href="/public/teachers.php" class="text-muted text-decoration-none">Our Teachers</a></li>
                    <li><a href="/public/gallery.php" class="text-muted text-decoration-none">Gallery</a></li>
                    <li><a href="/public/events.php" class="text-muted text-decoration-none">Events</a></li>
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
            <small class="text-muted">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</small>
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
</body>
</html>