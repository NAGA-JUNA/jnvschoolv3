<?php
require_once __DIR__ . '/includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$schoolAddress = getSetting('school_address', '');
$admissionOpen = getSetting('admission_open', '0');

// If logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/teacher/dashboard.php'));
    exit;
}

// Get active slides
$slides = $db->query("SELECT * FROM home_slider WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();

// Get upcoming events (next 3)
$events = $db->query("SELECT title, event_date, location FROM events WHERE is_public=1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3")->fetchAll();

// Get latest notifications (3)
$notifs = $db->query("SELECT title, type, created_at FROM notifications WHERE status='approved' AND is_public=1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Stats
$totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
$totalTeachers = $db->query("SELECT COUNT(*) FROM teachers WHERE status='active'")->fetchColumn();
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

        /* Hero Slider */
        .hero-slider { position: relative; overflow: hidden; height: 520px; }
        .hero-slide {
            position: absolute; inset: 0; opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover; background-position: center;
        }
        .hero-slide.active { opacity: 1; }
        .hero-slide::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(30,64,175,0.6) 100%);
        }
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

        /* Slider dots */
        .slider-dots {
            position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
            z-index: 10; display: flex; gap: 0.5rem;
        }
        .slider-dots .dot {
            width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.4);
            cursor: pointer; transition: all 0.3s; border: none;
        }
        .slider-dots .dot.active { background: #fff; transform: scale(1.2); }

        /* Slider arrows */
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

        /* No slider fallback */
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

        @media (max-width: 767.98px) {
            .hero-slider { height: 400px; }
            .hero-slide h1 { font-size: 1.8rem; }
            .slider-arrow { display: none; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background:#0f172a;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><i class="bi bi-mortarboard-fill me-2"></i><?= e($schoolName) ?></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline-light ms-lg-2 px-3" href="/login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Slider -->
<?php if (!empty($slides)): ?>
<div class="hero-slider" id="heroSlider">
    <?php foreach ($slides as $i => $slide): ?>
    <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>" style="background-image: url('/<?= e($slide['image_path']) ?>');" data-index="<?= $i ?>">
        <div class="container h-100">
            <div class="content">
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
            <div class="col-6 col-md-3">
                <div class="stat-item"><div class="num"><?= $totalStudents ?>+</div><div class="label">Students</div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item"><div class="num"><?= $totalTeachers ?>+</div><div class="label">Teachers</div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item"><div class="num">12</div><div class="label">Classes</div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item"><div class="num">100%</div><div class="label">Dedication</div></div>
            </div>
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
                <h6 class="fw-bold mb-2"><i class="bi bi-mortarboard-fill me-2"></i><?= e($schoolName) ?></h6>
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
