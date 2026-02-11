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
                    <?php if ($navLogo): ?><img src="<?= e($logoPath) ?>" alt="Logo" style="width:42px;height:42px;border-radius:10px;object-fit:cover;"><?php else: ?><i class="bi bi-mortarboard-fill" style="font-size:1.8rem;"></i><?php endif; ?>
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

<!-- WhatsApp Float -->
<?php if ($whatsappNumber): ?>
<a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $whatsappNumber)) ?>" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
