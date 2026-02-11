<?php
$pageTitle = 'Page Content Manager';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

// Define all page content settings with defaults
$pageConfigs = [
    'home' => [
        'label' => 'Home Page',
        'icon' => 'bi-house-fill',
        'fields' => [
            ['key' => 'home_marquee_text', 'label' => 'Marquee Text (Top Bar)', 'type' => 'textarea', 'default' => '🎓 Welcome to [school_name] — [tagline]', 'hint' => 'Use [school_name] and [tagline] as placeholders'],
            ['key' => 'home_hero_show', 'label' => 'Show Hero Slider', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_stats_show', 'label' => 'Show Stats Bar', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_stats_students_label', 'label' => 'Stats: Students Label', 'type' => 'text', 'default' => 'Students'],
            ['key' => 'home_stats_teachers_label', 'label' => 'Stats: Teachers Label', 'type' => 'text', 'default' => 'Teachers'],
            ['key' => 'home_stats_classes_label', 'label' => 'Stats: Classes Label', 'type' => 'text', 'default' => 'Classes'],
            ['key' => 'home_stats_classes_value', 'label' => 'Stats: Classes Value', 'type' => 'text', 'default' => '12'],
            ['key' => 'home_stats_dedication_label', 'label' => 'Stats: Dedication Label', 'type' => 'text', 'default' => 'Dedication'],
            ['key' => 'home_stats_dedication_value', 'label' => 'Stats: Dedication Value', 'type' => 'text', 'default' => '100%'],
            ['key' => 'home_quicklinks_show', 'label' => 'Show Quick Links Section', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_cta_admissions_title', 'label' => 'Quick Link: Admissions Title', 'type' => 'text', 'default' => 'Admissions'],
            ['key' => 'home_cta_admissions_desc', 'label' => 'Quick Link: Admissions Description', 'type' => 'textarea', 'default' => 'Apply online for admission to JNV School.'],
            ['key' => 'home_cta_notifications_title', 'label' => 'Quick Link: Notifications Title', 'type' => 'text', 'default' => 'Notifications'],
            ['key' => 'home_cta_notifications_desc', 'label' => 'Quick Link: Notifications Description', 'type' => 'textarea', 'default' => 'Stay updated with latest announcements.'],
            ['key' => 'home_cta_gallery_title', 'label' => 'Quick Link: Gallery Title', 'type' => 'text', 'default' => 'Gallery'],
            ['key' => 'home_cta_gallery_desc', 'label' => 'Quick Link: Gallery Description', 'type' => 'textarea', 'default' => 'Explore photos & videos from school life.'],
            ['key' => 'home_cta_events_title', 'label' => 'Quick Link: Events Title', 'type' => 'text', 'default' => 'Events'],
            ['key' => 'home_cta_events_desc', 'label' => 'Quick Link: Events Description', 'type' => 'textarea', 'default' => 'Check upcoming school events & dates.'],
            ['key' => 'home_core_team_show', 'label' => 'Show Core Team Section', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_core_team_title', 'label' => 'Core Team Section Title', 'type' => 'text', 'default' => 'Our Core Team'],
            ['key' => 'home_core_team_subtitle', 'label' => 'Core Team Subtitle', 'type' => 'textarea', 'default' => 'Meet the dedicated leaders guiding our school\'s vision and mission.'],
            ['key' => 'home_contact_show', 'label' => 'Show Contact Section', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'home_footer_cta_title', 'label' => 'Footer CTA Title', 'type' => 'text', 'default' => 'Become a Part of [school_name]'],
            ['key' => 'home_footer_cta_desc', 'label' => 'Footer CTA Description', 'type' => 'textarea', 'default' => 'Give your child the gift of quality education. Contact us today to learn more about admissions.'],
            ['key' => 'home_footer_cta_btn_text', 'label' => 'Footer CTA Button Text', 'type' => 'text', 'default' => 'Get In Touch'],
        ],
    ],
    'about' => [
        'label' => 'About Us',
        'icon' => 'bi-info-circle-fill',
        'fields' => [
            ['key' => 'about_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'About Us'],
            ['key' => 'about_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Discover our story, vision, and the values that drive us to provide exceptional education.'],
            ['key' => 'about_hero_badge', 'label' => 'Hero Badge Text', 'type' => 'text', 'default' => 'About Our School'],
            ['key' => 'about_history_show', 'label' => 'Show History Section', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'about_vision_mission_show', 'label' => 'Show Vision & Mission', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'about_core_values_show', 'label' => 'Show Core Values', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'about_quote_show', 'label' => 'Show Inspirational Quote', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'about_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'teachers' => [
        'label' => 'Our Teachers',
        'icon' => 'bi-person-badge-fill',
        'fields' => [
            ['key' => 'teachers_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Our Teachers'],
            ['key' => 'teachers_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Meet our dedicated team of qualified educators who inspire, guide, and shape the future of every student.'],
            ['key' => 'teachers_hero_badge', 'label' => 'Hero Badge Text', 'type' => 'text', 'default' => 'Our Educators'],
            ['key' => 'teachers_core_team_show', 'label' => 'Show Principal Section', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'teachers_grid_title', 'label' => 'Faculty Grid Title', 'type' => 'text', 'default' => 'Meet Our Faculty'],
            ['key' => 'teachers_grid_subtitle', 'label' => 'Faculty Grid Subtitle', 'type' => 'text', 'default' => 'Hover on a card to learn more about each teacher'],
            ['key' => 'teachers_all_show', 'label' => 'Show All Teachers Grid', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'teachers_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'gallery' => [
        'label' => 'Gallery',
        'icon' => 'bi-images',
        'fields' => [
            ['key' => 'gallery_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Photo Gallery'],
            ['key' => 'gallery_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Explore moments from [school_name]'],
            ['key' => 'gallery_hero_icon', 'label' => 'Hero Icon (Bootstrap Icons class)', 'type' => 'text', 'default' => 'bi-images'],
            ['key' => 'gallery_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'events' => [
        'label' => 'Events',
        'icon' => 'bi-calendar-event-fill',
        'fields' => [
            ['key' => 'events_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Events'],
            ['key' => 'events_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Upcoming and past events at [school_name]'],
            ['key' => 'events_hero_icon', 'label' => 'Hero Icon', 'type' => 'text', 'default' => 'bi-calendar-event-fill'],
            ['key' => 'events_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'notifications' => [
        'label' => 'Notifications',
        'icon' => 'bi-bell-fill',
        'fields' => [
            ['key' => 'notifications_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Notifications'],
            ['key' => 'notifications_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Stay updated with the latest announcements from [school_name]'],
            ['key' => 'notifications_hero_icon', 'label' => 'Hero Icon', 'type' => 'text', 'default' => 'bi-bell-fill'],
            ['key' => 'notifications_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'admission' => [
        'label' => 'Admission Form',
        'icon' => 'bi-file-earmark-plus-fill',
        'fields' => [
            ['key' => 'admission_hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Apply for Admission'],
            ['key' => 'admission_hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Submit your application to [school_name]'],
            ['key' => 'admission_hero_icon', 'label' => 'Hero Icon', 'type' => 'text', 'default' => 'bi-file-earmark-plus-fill'],
            ['key' => 'admission_footer_cta_show', 'label' => 'Show Footer CTA', 'type' => 'toggle', 'default' => '1'],
        ],
    ],
    'global' => [
        'label' => 'Global Elements',
        'icon' => 'bi-globe2',
        'fields' => [
            ['key' => 'global_navbar_show_top_bar', 'label' => 'Show Top Bar (Marquee)', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'global_navbar_show_login', 'label' => 'Show Login Button', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'global_navbar_show_notif_bell', 'label' => 'Show Notification Bell', 'type' => 'toggle', 'default' => '1'],
            ['key' => 'global_footer_cta_title', 'label' => 'Default Footer CTA Title (all pages)', 'type' => 'text', 'default' => 'Become a Part of [school_name]'],
            ['key' => 'global_footer_cta_desc', 'label' => 'Default Footer CTA Description', 'type' => 'textarea', 'default' => 'Give your child the gift of quality education. Contact us today to learn more about admissions.'],
            ['key' => 'global_footer_cta_btn_text', 'label' => 'Default Footer CTA Button Text', 'type' => 'text', 'default' => 'Get In Touch'],
        ],
    ],
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'save_page_content') {
    if (!verifyCsrf()) { setFlash('error', 'Invalid CSRF token.'); header('Location: /admin/page-content-manager.php?page=' . e($_POST['page_key'] ?? 'home')); exit; }
    
    $pageKey = $_POST['page_key'] ?? 'home';
    if (!isset($pageConfigs[$pageKey])) { setFlash('error', 'Invalid page.'); header('Location: /admin/page-content-manager.php'); exit; }
    
    $updated = 0;
    foreach ($pageConfigs[$pageKey]['fields'] as $field) {
        $key = $field['key'];
        if ($field['type'] === 'toggle') {
            $value = isset($_POST[$key]) ? '1' : '0';
        } else {
            $value = trim($_POST[$key] ?? '');
            // Validate length
            if (strlen($value) > 2000) $value = substr($value, 0, 2000);
        }
        
        // Upsert setting
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$key, $value]);
        $updated++;
    }
    
    auditLog('page_content_update', 'page_content', null, "Updated {$updated} settings for page: {$pageConfigs[$pageKey]['label']}");
    setFlash('success', "✅ {$pageConfigs[$pageKey]['label']} content updated successfully! ({$updated} fields saved)");
    header('Location: /admin/page-content-manager.php?page=' . $pageKey);
    exit;
}

// Handle reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'reset_defaults') {
    if (!verifyCsrf()) { setFlash('error', 'Invalid CSRF token.'); header('Location: /admin/page-content-manager.php'); exit; }
    
    $pageKey = $_POST['page_key'] ?? 'home';
    if (!isset($pageConfigs[$pageKey])) { setFlash('error', 'Invalid page.'); header('Location: /admin/page-content-manager.php'); exit; }
    
    foreach ($pageConfigs[$pageKey]['fields'] as $field) {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$field['key'], $field['default']]);
    }
    
    auditLog('page_content_reset', 'page_content', null, "Reset defaults for page: {$pageConfigs[$pageKey]['label']}");
    setFlash('success', "🔄 {$pageConfigs[$pageKey]['label']} content reset to defaults.");
    header('Location: /admin/page-content-manager.php?page=' . $pageKey);
    exit;
}

$activePage = $_GET['page'] ?? 'home';
if (!isset($pageConfigs[$activePage])) $activePage = 'home';

require_once __DIR__ . '/../includes/header.php';
?>

<style>
.page-tab { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; color: #64748b; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; border: 1px solid transparent; }
.page-tab:hover { color: #1e293b; background: #f1f5f9; }
.page-tab.active { color: #fff; background: var(--primary, #1e40af); border-color: var(--primary, #1e40af); }
.field-group { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 0.75rem; transition: box-shadow 0.2s; }
.field-group:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.field-group label { font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 0.3rem; }
.field-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; }
.toggle-switch { display: flex; align-items: center; gap: 0.75rem; }
.section-divider { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; padding: 0.5rem 0; margin-top: 0.5rem; border-bottom: 1px solid #e2e8f0; margin-bottom: 0.75rem; }
</style>

<!-- Page Tabs -->
<div class="card border-0 rounded-3 mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($pageConfigs as $key => $config): ?>
            <a href="/admin/page-content-manager.php?page=<?= $key ?>" class="page-tab <?= $activePage === $key ? 'active' : '' ?>">
                <i class="bi <?= $config['icon'] ?>"></i> <?= e($config['label']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Content Editor -->
<div class="card border-0 rounded-3">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi <?= $pageConfigs[$activePage]['icon'] ?> me-2"></i><?= e($pageConfigs[$activePage]['label']) ?> — Content Settings</h5>
            <small class="text-muted">Edit text, headings, and toggle section visibility for this page</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= $activePage === 'home' ? '/' : '/public/' . ($activePage === 'admission' ? 'admission-form' : $activePage) . '.php' ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i>Preview Page
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="form_action" value="save_page_content">
            <input type="hidden" name="page_key" value="<?= e($activePage) ?>">
            
            <?php
            $lastCategory = '';
            foreach ($pageConfigs[$activePage]['fields'] as $field):
                // Auto-detect category from key prefix
                $parts = explode('_', str_replace($activePage . '_', '', $field['key']), 2);
                $category = $parts[0] ?? '';
                
                $currentValue = getSetting($field['key'], $field['default']);
            ?>
            
            <div class="field-group">
                <?php if ($field['type'] === 'toggle'): ?>
                <div class="toggle-switch">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="<?= e($field['key']) ?>" id="<?= e($field['key']) ?>" <?= $currentValue === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="<?= e($field['key']) ?>" style="font-size:0.85rem;"><?= e($field['label']) ?></label>
                    </div>
                </div>
                <?php elseif ($field['type'] === 'textarea'): ?>
                <label for="<?= e($field['key']) ?>"><?= e($field['label']) ?></label>
                <textarea class="form-control form-control-sm" name="<?= e($field['key']) ?>" id="<?= e($field['key']) ?>" rows="2" maxlength="2000"><?= e($currentValue) ?></textarea>
                <?php if (isset($field['hint'])): ?><div class="field-hint"><?= e($field['hint']) ?></div><?php endif; ?>
                <?php else: ?>
                <label for="<?= e($field['key']) ?>"><?= e($field['label']) ?></label>
                <input type="text" class="form-control form-control-sm" name="<?= e($field['key']) ?>" id="<?= e($field['key']) ?>" value="<?= e($currentValue) ?>" maxlength="500">
                <?php if (isset($field['hint'])): ?><div class="field-hint"><?= e($field['hint']) ?></div><?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-dark px-4"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#resetModal"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset to Default</button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size:3rem;"></i>
                <h6 class="fw-bold mt-3">Reset to Defaults?</h6>
                <p class="text-muted small">This will overwrite all current values for <strong><?= e($pageConfigs[$activePage]['label']) ?></strong> with factory defaults.</p>
                <form method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="form_action" value="reset_defaults">
                    <input type="hidden" name="page_key" value="<?= e($activePage) ?>">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-warning px-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
