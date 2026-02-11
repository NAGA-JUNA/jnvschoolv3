<?php
require_once __DIR__ . '/../includes/auth.php';
$db = getDB();
$schoolName = getSetting('school_name', 'JNV School');
$schoolTagline = getSetting('school_tagline', 'Nurturing Talent, Shaping Future');
$loggedIn = isLoggedIn();
$userId = currentUserId();
$whatsappNumber = getSetting('whatsapp_api_number', '');
$schoolEmail = getSetting('school_email', '');
$schoolPhone = getSetting('school_phone', '');
$schoolAddress = getSetting('school_address', '');
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

// Mark as read
if ($loggedIn && isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    try {
        $db->prepare("INSERT IGNORE INTO notification_reads (notification_id, user_id) VALUES (?, ?)")->execute([(int)$_GET['mark_read'], $userId]);
    } catch (Exception $e) {}
    header('Location: /public/notifications.php');
    exit;
}

// Increment view count
if (isset($_GET['view_id']) && is_numeric($_GET['view_id'])) {
    $db->prepare("UPDATE notifications SET view_count = view_count + 1 WHERE id=?")->execute([(int)$_GET['view_id']]);
}

// Filters
$typeFilter = $_GET['type'] ?? '';
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$searchQ = trim($_GET['q'] ?? '');

$where = "WHERE n.status='approved' AND n.is_public=1 AND n.is_deleted=0 AND (n.schedule_at IS NULL OR n.schedule_at <= NOW()) AND (n.expires_at IS NULL OR n.expires_at >= CURDATE())";
$params = [];

if ($typeFilter && in_array($typeFilter, ['general','academic','exam','holiday','event','urgent'])) {
    $where .= " AND n.type=?";
    $params[] = $typeFilter;
}
if ($dateFrom) { $where .= " AND DATE(n.created_at) >= ?"; $params[] = $dateFrom; }
if ($dateTo) { $where .= " AND DATE(n.created_at) <= ?"; $params[] = $dateTo; }
if ($searchQ) { $where .= " AND n.title LIKE ?"; $params[] = "%$searchQ%"; }

$stmt = $db->prepare("SELECT n.* FROM notifications n $where ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT 50");
$stmt->execute($params);
$notifs = $stmt->fetchAll();

// Get read IDs for logged-in user
$readIds = [];
if ($loggedIn && $userId) {
    $r = $db->prepare("SELECT notification_id FROM notification_reads WHERE user_id=?");
    $r->execute([$userId]);
    $readIds = $r->fetchAll(PDO::FETCH_COLUMN);
}

$unreadCount = 0;
if ($loggedIn) {
    $uc = $db->prepare("SELECT COUNT(*) FROM notifications n WHERE n.status='approved' AND n.is_public=1 AND n.is_deleted=0 AND (n.schedule_at IS NULL OR n.schedule_at <= NOW()) AND (n.expires_at IS NULL OR n.expires_at >= CURDATE()) AND n.id NOT IN (SELECT notification_id FROM notification_reads WHERE user_id=?)");
    $uc->execute([$userId]);
    $unreadCount = $uc->fetchColumn();
}

// Popup notifications
$popupNotifs = $db->query("SELECT id, title, content, type FROM notifications WHERE status='approved' AND is_public=1 AND is_deleted=0 AND show_popup=1 AND (schedule_at IS NULL OR schedule_at <= NOW()) AND (expires_at IS NULL OR expires_at >= CURDATE()) ORDER BY created_at DESC LIMIT 3")->fetchAll();
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
        /* WhatsApp */
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.8rem; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform 0.3s; animation: whatsappPulse 2s infinite; }
        .whatsapp-float:hover { transform: scale(1.1); color: #fff; }
        @keyframes whatsappPulse { 0%, 100% { box-shadow: 0 4px 20px rgba(37,211,102,0.4); } 50% { box-shadow: 0 4px 30px rgba(37,211,102,0.7); } }
        .hero-banner { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%); color: #fff; padding: 3rem 0; }
        .notif-card { border: none; border-radius: 12px; transition: transform 0.2s; border-left: 4px solid transparent; }
        .notif-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .notif-card.unread { border-left-color: #3b82f6; background: #eff6ff; }
        .notif-card.pinned { border-left-color: #f59e0b; }
        .type-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .unread-badge { position: relative; top: -1px; }
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
                <li class="nav-item"><a class="nav-link" href="/public/about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/teachers.php">Our Teachers</a></li>
                <li class="nav-item"><a class="nav-link active" href="/public/notifications.php">Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="/public/admission-form.php">Apply Now</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="notif-bell-btn" data-bs-toggle="modal" data-bs-target="#notifBellModal">
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
        <h1 class="fw-bold mb-2">
            <i class="bi bi-bell-fill me-2"></i>Notifications
            <?php if ($unreadCount > 0): ?>
                <span class="badge bg-danger fs-6"><?= $unreadCount ?> new</span>
            <?php endif; ?>
        </h1>
        <p class="mb-0 opacity-75">Stay updated with the latest announcements from <?= e($schoolName) ?></p>
    </div>
</div>

<!-- Filters -->
<div class="container py-3">
    <form class="card border-0 shadow-sm mb-3" method="GET">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search title..." value="<?= e($searchQ) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <?php foreach (['general','academic','exam','event','holiday','urgent'] as $t): ?>
                            <option value="<?= $t ?>" <?= $typeFilter === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="<?= e($dateFrom) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="<?= e($dateTo) ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="/public/notifications.php" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <?php if (empty($notifs)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bell-slash display-1 text-muted"></i>
            <p class="text-muted mt-3">No notifications at this time.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifs as $n):
            $typeColors = ['urgent' => 'danger', 'exam' => 'warning', 'academic' => 'info', 'event' => 'success', 'holiday' => 'purple'];
            $color = $typeColors[$n['type']] ?? 'secondary';
            $isUnread = $loggedIn && !in_array($n['id'], $readIds);
            $isPinned = $n['is_pinned'] ?? 0;
            $prColor = ['normal' => 'secondary', 'important' => 'warning', 'urgent' => 'danger'];
        ?>
        <div class="card notif-card mb-3 <?= $isUnread ? 'unread' : '' ?> <?= $isPinned ? 'pinned' : '' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <?php if ($isPinned): ?><span class="badge bg-warning-subtle text-warning me-1"><i class="bi bi-pin-fill"></i> Pinned</span><?php endif; ?>
                        <?php if ($isUnread): ?><span class="badge bg-primary-subtle text-primary me-1">New</span><?php endif; ?>
                        <h5 class="fw-semibold mb-0 d-inline"><?= e($n['title']) ?></h5>
                    </div>
                    <div class="d-flex gap-1">
                        <?php if (($n['priority'] ?? 'normal') !== 'normal'): ?>
                            <span class="badge bg-<?= $prColor[$n['priority']] ?>-subtle text-<?= $prColor[$n['priority']] ?> type-badge"><?= ucfirst($n['priority']) ?></span>
                        <?php endif; ?>
                        <span class="badge bg-<?= $color ?> type-badge"><?= ucfirst(e($n['type'])) ?></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?>
                        <span class="ms-2"><i class="bi bi-eye me-1"></i><?= $n['view_count'] ?? 0 ?> views</span>
                    </small>
                    <div class="d-flex gap-2">
                        <?php if ($loggedIn && $isUnread): ?>
                            <a href="/public/notifications.php?mark_read=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-check2 me-1"></i>Mark Read</a>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="toggleContent(<?= $n['id'] ?>)"><i class="bi bi-chevron-down"></i></button>
                    </div>
                </div>
                <div class="mt-2 collapse" id="content-<?= $n['id'] ?>">
                    <hr>
                    <p class="mb-0" style="white-space:pre-wrap;"><?= nl2br(e($n['content'])) ?></p>
                    <?php if ($n['attachment'] ?? ''): ?>
                        <div class="mt-2"><a href="/uploads/documents/<?= e($n['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-paperclip me-1"></i>Attachment</a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Popup Modal -->
<?php if (!empty($popupNotifs)): ?>
<div class="modal fade" id="notifPopup" tabindex="-1"><div class="modal-dialog modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-bell-fill me-2"></i>Important Notifications</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?php foreach ($popupNotifs as $pn): ?>
            <div class="mb-3 pb-3 border-bottom">
                <h6 class="fw-bold"><?= e($pn['title']) ?></h6>
                <span class="badge bg-<?= ($typeColors[$pn['type']] ?? 'secondary') ?> mb-2"><?= ucfirst(e($pn['type'])) ?></span>
                <p class="mb-0 small"><?= nl2br(e(substr($pn['content'], 0, 200))) ?><?= strlen($pn['content']) > 200 ? '...' : '' ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="modal-footer">
        <a href="/public/notifications.php" class="btn btn-primary btn-sm"><i class="bi bi-arrow-right me-1"></i>View All</a>
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
</div></div></div>
<?php endif; ?>

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
<a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $whatsappNumber)) ?>" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleContent(id) {
    const el = document.getElementById('content-' + id);
    if (el) new bootstrap.Collapse(el, {toggle: true});
}

// Show popup if not dismissed
<?php if (!empty($popupNotifs)): ?>
document.addEventListener('DOMContentLoaded', function() {
    const popupIds = <?= json_encode(array_column($popupNotifs, 'id')) ?>;
    const dismissKey = 'notif_popup_dismissed_' + popupIds.join('_');
    if (!localStorage.getItem(dismissKey)) {
        const m = new bootstrap.Modal(document.getElementById('notifPopup'));
        m.show();
        document.getElementById('notifPopup').addEventListener('hidden.bs.modal', function() {
            localStorage.setItem(dismissKey, '1');
        });
    }
});
<?php endif; ?>
</script>
</body>
</html>