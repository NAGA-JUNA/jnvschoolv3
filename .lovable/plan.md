
## Plan: Page Content Manager + Footer Manager for Complete Frontend Control

### 1. Overview

This plan creates two new admin features to centralize frontend content management:

**A) Page Content Manager** — A single admin page where admins can edit all text, headings, descriptions, and toggle section visibility for every public page (Home, About, Teachers, Gallery, Events, Notifications, Admission) plus Global elements (navbar text, footer CTA, marquee content).

**B) Footer Manager** — A dedicated admin page for full control over footer columns: Quick Links, Programs, Contact Info, and Social media links with dynamic link management.

Both use the existing `settings` table (`setting_key` / `setting_value`) to store content, ensuring consistency with current architecture.

---

### 2. Database Schema (No new tables needed)

All content is stored as settings key-value pairs. Add these default settings to `schema.sql`:

**Home Page Settings:**
```sql
-- Hero/Marquee
INSERT INTO settings (setting_key, setting_value) VALUES
('home_marquee_text', '🎓 Welcome to [school_name] — [tagline]'),
('home_hero_show', '1'),

-- Stats bar
('home_stats_show', '1'),
('home_stats_students_label', 'Students'),
('home_stats_teachers_label', 'Teachers'),
('home_stats_classes_label', 'Classes'),
('home_stats_dedication_label', 'Dedication'),

-- Quick Links section
('home_quicklinks_show', '1'),
('home_quicklinks_title', 'Why Choose Us?'),
('home_quicklinks_subtitle', 'Explore our key features and offerings'),

-- Admission CTA card
('home_cta_admissions_title', 'Admissions'),
('home_cta_admissions_desc', 'Apply online for admission to our school.'),

-- Notifications CTA card
('home_cta_notifications_title', 'Notifications'),
('home_cta_notifications_desc', 'Stay updated with latest announcements.'),

-- Gallery CTA card
('home_cta_gallery_title', 'Gallery'),
('home_cta_gallery_desc', 'Explore our school moments and events.'),

-- Events CTA card
('home_cta_events_title', 'Events'),
('home_cta_events_desc', 'Upcoming school events and activities.'),

-- Footer CTA
('home_footer_cta_title', 'Become a Part of [school_name]'),
('home_footer_cta_desc', 'Give your child the gift of quality education. Contact us today.'),
('home_footer_cta_btn_text', 'Get In Touch'),
('home_footer_cta_show', '1');
```

**About Page Settings:**
```sql
INSERT INTO settings (setting_key, setting_value) VALUES
('about_hero_title', 'About Us'),
('about_hero_subtitle', 'Discover our story, vision, and the values that drive us to provide exceptional education.'),
('about_history_show', '1'),
('about_vision_mission_show', '1'),
('about_core_values_show', '1'),
('about_quote_show', '1');
```

**Teachers Page Settings:**
```sql
INSERT INTO settings (setting_key, setting_value) VALUES
('teachers_hero_title', 'Our Dedicated Educators'),
('teachers_hero_subtitle', 'Meet the passionate teachers shaping the future of our students.'),
('teachers_core_team_title', 'Our Leadership'),
('teachers_core_team_show', '1'),
('teachers_all_show', '1');
```

**Gallery & Events Pages:**
```sql
INSERT INTO settings (setting_key, setting_value) VALUES
('gallery_hero_title', 'School Gallery'),
('gallery_hero_subtitle', 'Explore memorable moments from school events and activities.'),
('events_hero_title', 'Upcoming Events'),
('events_hero_subtitle', 'Stay connected with our school calendar and important dates.'),
('notifications_hero_title', 'Announcements'),
('notifications_hero_subtitle', 'Latest updates and important notifications for parents and students.');
```

**Global Elements:**
```sql
INSERT INTO settings (setting_key, setting_value) VALUES
('global_navbar_show_top_bar', '1'),
('global_navbar_show_login', '1'),
('global_navbar_show_notif_bell', '1');
```

---

### 3. File Structure

#### **A. Page Content Manager** — `admin/page-content-manager.php`

**Features:**
- Page selector dropdown: Home, About, Teachers, Gallery, Events, Notifications, Admission
- Dynamic form fields based on selected page
- Each field shows a textarea/input paired with a "Preview" button to see live changes
- Enable/Disable toggles for major sections (e.g., "Show Core Values on About page")
- Save button with flash message feedback
- Permission: Admin only

**UI Layout:**
```
┌─────────────────────────────────────────────────────┐
│ Page Content Manager                                │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Select Page: [Dropdown: Home ▼]                    │
│              [About ▼ / Teachers ▼ / ...]          │
│                                                     │
│ ═══════════════════════════════════════════════    │
│ Page: HOME                                          │
│ ═══════════════════════════════════════════════    │
│                                                     │
│ 📋 Marquee Text (Top Bar)                          │
│ [Textarea: current marquee text...]                │
│ [Preview]                                          │
│                                                     │
│ ☑ Show Hero Section                               │
│ ☑ Show Stats Bar                                  │
│ ☑ Show Quick Links Section                        │
│                                                     │
│ Quick Links Title:                                 │
│ [Input: "Why Choose Us?"]                          │
│                                                     │
│ Quick Links Subtitle:                              │
│ [Input: subtitle text...]                          │
│                                                     │
│ ───────────────────────────────────────────        │
│ FOOTER CTA SECTION                                 │
│ ───────────────────────────────────────────        │
│                                                     │
│ CTA Title: [Input: Become a Part...]               │
│ CTA Description: [Textarea: Description...]        │
│ CTA Button Text: [Input: Get In Touch]             │
│ ☑ Show Footer CTA                                 │
│                                                     │
│ [Save Changes] [Reset to Default]                  │
│                                                     │
└─────────────────────────────────────────────────────┘
```

**Key Implementation Details:**
- Form fields dynamically populate based on selected page
- Each page has its own set of settings keys
- Submit handler validates input lengths (no XSS)
- Flash message confirms save and shows which page was updated
- "Reset to Default" button reads from hard-coded defaults and re-inserts into settings

---

#### **B. Footer Manager** — `admin/footer-manager.php`

**Features:**
- Manage 4 footer columns independently
- **Column 1: Logo + Description** — Edit description text, social links (Facebook, Twitter, Instagram, YouTube, LinkedIn)
- **Column 2: Quick Links** — Add/Edit/Delete custom links (label + URL) with drag-to-reorder
- **Column 3: Programs** — Add/Edit/Delete program names with drag-to-reorder
- **Column 4: Contact Info** — Edit address, phone, email, operating hours
- Live preview panel showing how footer will look on desktop/mobile
- Save all changes at once

**UI Layout:**
```
┌───────────────────────────────────────────────────────┐
│ Footer Manager                                        │
├───────────────────────────────────────────────────────┤
│                                                       │
│ ═══════════════════════════════════════════════════  │
│ COLUMN 1: LOGO & DESCRIPTION                         │
│ ═══════════════════════════════════════════════════  │
│                                                       │
│ Description Text:                                    │
│ [Textarea: "A professional modern school..."]        │
│                                                       │
│ SOCIAL LINKS:                                        │
│ Facebook URL:   [Input: https://...]                │
│ Twitter URL:    [Input: https://...]                │
│ Instagram URL:  [Input: https://...]                │
│ YouTube URL:    [Input: https://...]                │
│ LinkedIn URL:   [Input: https://...]                │
│                                                       │
│ ═══════════════════════════════════════════════════  │
│ COLUMN 2: QUICK LINKS                                │
│ ═══════════════════════════════════════════════════  │
│                                                       │
│ [+ Add Link]                                         │
│                                                       │
│ ☰ About Us        [Edit] [Delete]                   │
│ ☰ Our Teachers    [Edit] [Delete]                   │
│ ☰ Admissions      [Edit] [Delete]                   │
│ ☰ Gallery         [Edit] [Delete]                   │
│ ☰ Events          [Edit] [Delete]                   │
│ ☰ Admin Login     [Edit] [Delete]                   │
│                                                       │
│ ═══════════════════════════════════════════════════  │
│ COLUMN 3: PROGRAMS                                   │
│ ═══════════════════════════════════════════════════  │
│                                                       │
│ [+ Add Program]                                      │
│                                                       │
│ ☰ Pre-Primary (LKG & UKG)    [Edit] [Delete]        │
│ ☰ Primary School (1-5)       [Edit] [Delete]        │
│ ☰ Upper Primary (6-8)        [Edit] [Delete]        │
│                                                       │
│ ═══════════════════════════════════════════════════  │
│ COLUMN 4: CONTACT INFO                               │
│ ═══════════════════════════════════════════════════  │
│                                                       │
│ Address: [Textarea: full address...]                │
│ Phone: [Input: phone number...]                     │
│ Email: [Input: email@school.com]                    │
│ Hours: [Input: Mon - Sat: 8:00 AM - 5:00 PM]       │
│                                                       │
│ ───────────────────────────────────────────────────  │
│ 📱 LIVE PREVIEW (Desktop & Mobile)                   │
│ ───────────────────────────────────────────────────  │
│                                                       │
│ [Save Footer Changes] [Reset to Default]            │
│                                                       │
└───────────────────────────────────────────────────────┘
```

**Database Structure for Footer Content:**

Links and programs are stored as JSON in settings:
```sql
-- Footer Quick Links
INSERT INTO settings (setting_key, setting_value) VALUES
('footer_quick_links', '[
  {"label":"About Us","url":"/public/about.php"},
  {"label":"Our Teachers","url":"/public/teachers.php"},
  {"label":"Admissions","url":"/public/admission-form.php"},
  {"label":"Gallery","url":"/public/gallery.php"},
  {"label":"Events","url":"/public/events.php"},
  {"label":"Admin Login","url":"/login.php"}
]');

-- Footer Programs
INSERT INTO settings (setting_key, setting_value) VALUES
('footer_programs', '[
  {"label":"Pre-Primary (LKG & UKG)"},
  {"label":"Primary School (1-5)"},
  {"label":"Upper Primary (6-8)"},
  {"label":"Co-Curricular Activities"},
  {"label":"Sports Programs"}
]');

-- Footer Contact Info
INSERT INTO settings (setting_key, setting_value) VALUES
('footer_contact_address', 'Address here'),
('footer_contact_phone', '+91-XXXX-XXXX-XXX'),
('footer_contact_email', 'contact@school.com'),
('footer_contact_hours', 'Mon - Sat: 8:00 AM - 5:00 PM'),
('footer_description', 'A professional and modern school...');
```

**Key Implementation Details:**
- Use `json_encode()` / `json_decode()` for storing/retrieving lists
- Drag-to-reorder uses simple HTML5 drag events (no external library)
- Add/Edit modals for Quick Links and Programs with validation
- Live preview updates on-the-fly as user types (JavaScript preview)
- Permission: Admin only

---

### 4. Integration with Existing Pages

**Update `index.php`, `about.php`, `teachers.php`, `gallery.php`, `events.php`, `notifications.php`, `admission-form.php`:**

Replace hardcoded content with dynamic settings. Examples:

**Before (hardcoded):**
```php
<h2>Become a Part of <?= e($schoolName) ?></h2>
<p>Give your child the gift of quality education...</p>
```

**After (dynamic):**
```php
$footerCtaTitle = getSetting('home_footer_cta_title', 'Become a Part of ' . $schoolName);
$footerCtaDesc = getSetting('home_footer_cta_desc', 'Give your child the gift of...');
$footerCtaShow = getSetting('home_footer_cta_show', '1');

if ($footerCtaShow === '1'): ?>
  <h2><?= e($footerCtaTitle) ?></h2>
  <p><?= e($footerCtaDesc) ?></p>
<?php endif; ?>
```

**Footer Integration:**
Replace hardcoded footer columns with:
```php
$quickLinks = json_decode(getSetting('footer_quick_links', '[]'), true) ?? [];
$programs = json_decode(getSetting('footer_programs', '[]'), true) ?? [];
$footerDesc = getSetting('footer_description', '...');

// Loop through $quickLinks and $programs dynamically
foreach ($quickLinks as $link): 
  // Output link
endforeach;
```

---

### 5. Sidebar Navigation Updates

**Update `includes/header.php`:**

Add two new sidebar links under Configuration section:
```php
<a href="/admin/page-content-manager.php" class="nav-link <?= navActive('/admin/page-content-manager') ?>"><i class="bi bi-file-earmark-text"></i> Page Content Manager</a>
<a href="/admin/footer-manager.php" class="nav-link <?= navActive('/admin/footer-manager') ?>"><i class="bi bi-diagram-3"></i> Footer Manager</a>
```

---

### 6. Files to Create

| File | Purpose |
|------|---------|
| `admin/page-content-manager.php` | Single admin page for managing text/content on all public pages |
| `admin/footer-manager.php` | Dedicated admin page for footer columns and links |

---

### 7. Files to Modify

| File | Changes |
|------|---------|
| `schema.sql` | Add 40+ new settings key-value pairs as defaults for all pages and footer content |
| `includes/header.php` | Add two new sidebar navigation links under Configuration |
| `index.php` | Replace hardcoded content (marquee, CTA sections) with dynamic settings |
| `public/about.php` | Replace hardcoded hero title/subtitle with settings |
| `public/teachers.php` | Replace hardcoded hero title/subtitle with settings |
| `public/gallery.php` | Replace hardcoded hero title/subtitle with settings |
| `public/events.php` | Replace hardcoded hero title/subtitle with settings |
| `public/notifications.php` | Replace hardcoded hero title/subtitle with settings |
| `public/admission-form.php` | Replace hardcoded hero title/subtitle with settings |
| Footer section (all pages) | Replace hardcoded footer columns with JSON-driven dynamic rendering |

---

### 8. Technical Notes

**Page Content Manager (`admin/page-content-manager.php`):**
- PHP: Process form submissions via `$_POST['form_action']` and update settings table
- JavaScript: Toggle form fields based on selected page using event listeners
- HTML: Use Bootstrap tabs or accordion for page sections
- CSRF protection: Include form token verification
- Audit log: Log all content changes via `auditLog()`

**Footer Manager (`admin/footer-manager.php`):**
- Use modal dialogs for Add/Edit operations on Quick Links and Programs
- Store as JSON: `json_encode()` for saving, `json_decode()` for loading
- Drag-to-reorder: Simple JavaScript with array reordering on drop
- Live preview: Update preview HTML on-the-fly using JavaScript
- Permission: Only admins can edit

**Security:**
- Sanitize all input with `trim()` and `htmlspecialchars()`
- Validate URLs in footer links with `filter_var($url, FILTER_VALIDATE_URL)`
- Validate input lengths (e.g., max 500 chars for titles, 2000 for descriptions)
- Prevent XSS by using `e()` helper in all output

**Performance:**
- All settings are cached in memory once fetched via `getSetting()`
- No N+1 queries — load all settings once at page start
- JSON decoding happens only when needed for footer content

---

### 9. User Workflow

**For Home Page Content:**
1. Admin opens Page Content Manager
2. Selects "Home" from dropdown
3. Edits marquee text, enables/disables sections, updates CTA text
4. Clicks Save
5. Visits home page — sees updated content immediately

**For Footer:**
1. Admin opens Footer Manager
2. Adds new Quick Link via modal: "Careers" → "/careers.php"
3. Reorders programs by dragging
4. Updates contact hours
5. Watches live preview update in real-time
6. Clicks Save Footer Changes
7. Visits any page — footer reflects changes

---

### 10. Default Values & Backward Compatibility

All `getSetting()` calls include sensible defaults matching current hardcoded text. If a setting is missing, the page renders with the default, ensuring no broken pages during migration.

