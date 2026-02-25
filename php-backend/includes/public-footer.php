<?php
/**
 * Shared Public Footer Include
 * 
 * Renders: Footer CTA, 4-column dark footer, WhatsApp floating button, Bootstrap JS
 * 
 * Expected variables (set by parent page):
 *   $schoolName, $navLogo, $logoPath, $schoolAddress, $schoolPhone, $schoolEmail,
 *   $socialFacebook, $socialTwitter, $socialInstagram, $socialYoutube, $socialLinkedin,
 *   $whatsappNumber
 */

// Footer CTA settings
$_ctaShow = getSetting('home_footer_cta_show', '1');
$_ctaTitle = getSetting('home_footer_cta_title', '') ?: 'Become a Part of ' . $schoolName;
$_ctaDesc = getSetting('home_footer_cta_desc', '') ?: 'Give your child the gift of quality education. Contact us today to learn more about admissions.';
$_ctaBtn = getSetting('home_footer_cta_btn_text', '') ?: 'Get In Touch';

// Footer column settings
$_fDesc = getSetting('footer_description', 'A professional and modern school with years of experience in nurturing children with senior teachers and a clean environment.');
$_fLinks = json_decode(getSetting('footer_quick_links', ''), true) ?: [
    ['label'=>'About Us','url'=>'/public/about.php'],
    ['label'=>'Our Teachers','url'=>'/public/teachers.php'],
    ['label'=>'Admissions','url'=>'/public/admission-form.php'],
    ['label'=>'Gallery','url'=>'/public/gallery.php'],
    ['label'=>'Events','url'=>'/public/events.php'],
    ['label'=>'Admin Login','url'=>'/login.php']
];
$_fProgs = json_decode(getSetting('footer_programs', ''), true) ?: [
    ['label'=>'Pre-Primary (LKG & UKG)'],
    ['label'=>'Primary School (1-5)'],
    ['label'=>'Upper Primary (6-8)'],
    ['label'=>'Co-Curricular Activities'],
    ['label'=>'Sports Programs']
];
$_fAddr = getSetting('footer_contact_address', $schoolAddress);
$_fPhone = getSetting('footer_contact_phone', $schoolPhone);
$_fEmail = getSetting('footer_contact_email', $schoolEmail);
$_fHours = getSetting('footer_contact_hours', 'Mon - Sat: 8:00 AM - 5:00 PM');
$_fSocFb = getSetting('footer_social_facebook', $socialFacebook);
$_fSocTw = getSetting('footer_social_twitter', $socialTwitter);
$_fSocIg = getSetting('footer_social_instagram', $socialInstagram);
$_fSocYt = getSetting('footer_social_youtube', $socialYoutube);
$_fSocLi = getSetting('footer_social_linkedin', $socialLinkedin);

if ($_ctaShow === '1'):
?>
<!-- Footer CTA -->
<section class="footer-cta">
    <div class="container">
        <h2><?= e(str_replace('[school_name]', $schoolName, $_ctaTitle)) ?></h2>
        <p><?= e($_ctaDesc) ?></p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="/public/admission-form.php" class="btn btn-danger rounded-pill px-4 fw-semibold"><?= e($_ctaBtn) ?> <i class="bi bi-arrow-right ms-1"></i></a>
            <a href="/public/about.php" class="btn btn-outline-light rounded-pill px-4 fw-semibold">Learn More</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Dark Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 py-5">
            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="max-width:120px;height:auto;border-radius:8px;object-fit:contain;background:#fff;padding:4px;"><?php else: ?><i class="bi bi-mortarboard-fill" style="font-size:1.8rem;"></i><?php endif; ?>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:0.95rem;"><?= e($schoolName) ?></h6>
                        <?php if ($_fAddr): ?><small class="opacity-50" style="font-size:0.75rem;"><?= e(explode(',', $_fAddr)[0] ?? '') ?></small><?php endif; ?>
                    </div>
                </div>
                <p class="small opacity-60 mb-3"><?= e($_fDesc) ?></p>
                <div class="footer-social d-flex gap-2 flex-wrap">
                    <?php if ($_fSocFb): ?><a href="<?= e($_fSocFb) ?>" target="_blank"><i class="bi bi-facebook"></i></a><?php endif; ?>
                    <?php if ($_fSocTw): ?><a href="<?= e($_fSocTw) ?>" target="_blank"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                    <?php if ($_fSocIg): ?><a href="<?= e($_fSocIg) ?>" target="_blank"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    <?php if ($_fSocYt): ?><a href="<?= e($_fSocYt) ?>" target="_blank"><i class="bi bi-youtube"></i></a><?php endif; ?>
                    <?php if ($_fSocLi): ?><a href="<?= e($_fSocLi) ?>" target="_blank"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="list-unstyled">
                    <?php foreach ($_fLinks as $_fl): ?>
                    <li class="mb-2"><a href="<?= e($_fl['url']) ?>" class="footer-link"><?= e($_fl['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Programs</h6>
                <ul class="list-unstyled">
                    <?php foreach ($_fProgs as $_fp): ?>
                    <li class="mb-2"><span class="footer-link"><?= e($_fp['label']) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Contact Info</h6>
                <ul class="list-unstyled">
                    <?php if ($_fAddr): ?><li class="mb-2"><i class="bi bi-geo-alt text-danger me-2"></i><span class="footer-link"><?= e($_fAddr) ?></span></li><?php endif; ?>
                    <?php if ($_fPhone): ?><li class="mb-2"><i class="bi bi-telephone text-success me-2"></i><a href="tel:<?= e($_fPhone) ?>" class="footer-link"><?= e($_fPhone) ?></a></li><?php endif; ?>
                    <?php if ($_fEmail): ?><li class="mb-2"><i class="bi bi-envelope text-warning me-2"></i><a href="mailto:<?= e($_fEmail) ?>" class="footer-link"><?= e($_fEmail) ?></a></li><?php endif; ?>
                    <?php if ($_fHours): ?><li class="mb-2"><i class="bi bi-clock text-info me-2"></i><span class="footer-link"><?= e($_fHours) ?></span></li><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container text-center py-3">
            <small class="opacity-50">&copy; <?= date('Y') ?> <?= e($schoolName) ?>. All rights reserved.</small>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<?php if ($whatsappNumber): 
    $_waNum = preg_replace('/[^0-9]/', '', $whatsappNumber);
    $_waText = urlencode('Hi, I need help regarding ' . $schoolName);
?>
<a href="https://wa.me/<?= e($_waNum) ?>?text=<?= $_waText ?>" target="_blank" class="wa-float-btn" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
    <span class="wa-float-text">Chat with us</span>
</a>
<?php endif; ?>

<!-- Need Help Sidebar Tab -->
<div class="need-help-tab" data-bs-toggle="modal" data-bs-target="#needHelpModal" title="Need Help?">
    <i class="bi bi-journal-text"></i>
    <span>Need Help?</span>
</div>

<!-- Need Help Modal -->
<div class="modal fade" id="needHelpModal" tabindex="-1" aria-labelledby="needHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:#fff;border-bottom:1px solid #eee;padding:20px 24px;">
                <div style="border-left:4px solid #DC3545;padding-left:12px;">
                    <h5 class="modal-title fw-bold mb-0" id="needHelpModalLabel" style="color:#DC3545;">Need Help?</h5>
                    <small class="text-muted">We're here to assist you</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p class="text-muted small mb-3">Share your details below and our admissions experts will get in touch with you to guide you personally.</p>
                <form id="needHelpForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Parent's Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="parent_name" required maxlength="100" placeholder="Enter your full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mobile Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#f8f9fa;font-weight:600;">+91</span>
                            <input type="tel" class="form-control" name="mobile" required maxlength="10" pattern="[0-9]{10}" placeholder="Enter 10 digit mobile number">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" class="form-control" name="email" maxlength="255" placeholder="Enter your email (optional)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Your Message / Query</label>
                        <textarea class="form-control" name="message" rows="3" maxlength="500" placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold" style="background:#DC3545;color:#fff;padding:12px;border-radius:8px;" id="needHelpSubmitBtn">
                        <i class="bi bi-telephone-fill me-2"></i>Request a Call Back
                    </button>
                </form>
                <div id="needHelpSuccess" class="text-center py-4" style="display:none;">
                    <div style="width:64px;height:64px;background:#d4edda;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <i class="bi bi-check-lg" style="font-size:2rem;color:#28a745;"></i>
                    </div>
                    <h5 class="fw-bold text-success">Thank You!</h5>
                    <p class="text-muted small">We'll contact you soon to assist you.</p>
                </div>
                <p class="text-center text-muted small mt-3 mb-0"><i class="bi bi-shield-check me-1"></i>Our admissions team will contact you shortly.</p>
            </div>
        </div>
    </div>
</div>

<!-- Floating Elements CSS -->
<style>
/* WhatsApp Floating Button */
.wa-float-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    background: #25D366;
    color: #fff;
    border-radius: 50px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: 0 4px 15px rgba(37,211,102,0.4);
    transition: transform 0.2s, box-shadow 0.2s;
    animation: waPulse 2s infinite;
}
.wa-float-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(37,211,102,0.5);
    color: #fff;
}
.wa-float-btn i { font-size: 1.4rem; }
@keyframes waPulse {
    0%, 100% { box-shadow: 0 4px 15px rgba(37,211,102,0.4); }
    50% { box-shadow: 0 4px 25px rgba(37,211,102,0.7); }
}
@media (max-width: 576px) {
    .wa-float-btn {
        width: 52px;
        height: 52px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        bottom: 16px;
        right: 16px;
    }
    .wa-float-text { display: none; }
    .wa-float-btn i { font-size: 1.5rem; }
}

/* Need Help Sidebar Tab */
.need-help-tab {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 9998;
    background: #DC3545;
    color: #fff;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    padding: 14px 10px;
    border-radius: 8px 0 0 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: -2px 0 10px rgba(220,53,69,0.3);
    transition: padding-right 0.2s, background 0.2s;
}
.need-help-tab:hover {
    padding-right: 14px;
    background: #c82333;
}
.need-help-tab i { font-size: 1rem; }
@media (max-width: 576px) {
    .need-help-tab {
        padding: 10px 7px;
        font-size: 0.75rem;
    }
}
</style>

<!-- Need Help Form JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('needHelpForm');
    const successDiv = document.getElementById('needHelpSuccess');
    const submitBtn = document.getElementById('needHelpSubmitBtn');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(form);
        const name = (fd.get('parent_name') || '').toString().trim();
        const mobile = (fd.get('mobile') || '').toString().trim();
        const email = (fd.get('email') || '').toString().trim();
        const message = (fd.get('message') || '').toString().trim();

        if (!name || !mobile || !/^[0-9]{10}$/.test(mobile)) {
            alert('Please enter your name and a valid 10-digit mobile number.');
            return;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

        const body = new URLSearchParams();
        body.append('action', 'save');
        body.append('parent_name', name);
        body.append('mobile', '91' + mobile);
        body.append('email', email);
        body.append('message', message);
        body.append('source', 'need_help_popup');

        fetch('/public/ajax/enquiry-submit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        })
        .then(r => r.json())
        .then(data => {
            form.style.display = 'none';
            successDiv.style.display = 'block';
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('needHelpModal'));
                if (modal) modal.hide();
                setTimeout(() => {
                    form.reset();
                    form.style.display = 'block';
                    successDiv.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-telephone-fill me-2"></i>Request a Call Back';
                }, 500);
            }, 2500);
        })
        .catch(() => {
            alert('Something went wrong. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-telephone-fill me-2"></i>Request a Call Back';
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
