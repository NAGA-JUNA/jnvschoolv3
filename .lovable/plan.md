

## Shared Footer Include + Dynamic Hero Sections for All Public Pages

### Overview

This plan eliminates ~480 lines of duplicated footer code across 6 public pages by extracting it into a single shared include file (`includes/public-footer.php`), and updates all hero sections to read from the Page Content Manager settings.

---

### 1. Create `includes/public-footer.php`

A new shared include file that renders the Footer CTA, 4-column footer, WhatsApp button, and Bootstrap JS. It expects certain variables to already be set by the parent page (they all already set these variables identically).

The file will:
- Read footer settings dynamically using `getSetting()` (same pattern as `index.php` lines 622-709)
- Render the Footer CTA section with dynamic title, description, button text, and show/hide toggle
- Render the 4-column footer with JSON-driven Quick Links and Programs from Footer Manager
- Render the WhatsApp floating button
- Include Bootstrap JS

**Variables expected** (already set by every page): `$schoolName`, `$navLogo`, `$logoPath`, `$schoolAddress`, `$schoolPhone`, `$schoolEmail`, `$socialFacebook`, `$socialTwitter`, `$socialInstagram`, `$socialYoutube`, `$socialLinkedin`, `$whatsappNumber`

---

### 2. Update Hero Sections to Use Dynamic Settings

Each page's hardcoded hero title and subtitle will be replaced with `getSetting()` calls:

| Page | Setting Keys | Current Hardcoded Text |
|------|-------------|----------------------|
| About | `about_hero_title`, `about_hero_subtitle` | "About Us", "Discover our story..." |
| Teachers | `teachers_hero_title`, `teachers_hero_subtitle` | "Our Teachers", "Meet our dedicated team..." |
| Gallery | `gallery_hero_title`, `gallery_hero_subtitle` | "Photo Gallery", "Explore moments from..." |
| Events | `events_hero_title`, `events_hero_subtitle` | "Events", "Upcoming and past events at..." |
| Notifications | `notifications_hero_title`, `notifications_hero_subtitle` | "Notifications", "Stay updated with..." |
| Admission | `admission_hero_title`, `admission_hero_subtitle` | "Apply for Admission", "Submit your application..." |

---

### 3. Files to Create

| File | Purpose |
|------|---------|
| `includes/public-footer.php` | Shared footer CTA + footer columns + WhatsApp + Bootstrap JS |

---

### 4. Files to Modify

| File | What Changes |
|------|-------------|
| `public/about.php` | Replace hero with dynamic settings; replace ~85 lines of footer/CTA/WhatsApp with single `include` |
| `public/teachers.php` | Replace hero with dynamic settings; replace ~90 lines of footer block with single `include` |
| `public/gallery.php` | Replace hero with dynamic settings; replace ~95 lines of footer block with single `include` |
| `public/events.php` | Replace hero with dynamic settings; replace ~75 lines of footer block with single `include` |
| `public/notifications.php` | Replace hero with dynamic settings; replace ~97 lines of footer block with single `include` |
| `public/admission-form.php` | Replace hero with dynamic settings; replace ~84 lines of footer block with single `include` |
| `index.php` | Replace its already-dynamic footer block (~90 lines) with the shared include for consistency |
| `schema.sql` | Add `admission_hero_title` and `admission_hero_subtitle` default settings (the others already exist) |

---

### 5. Technical Details

**Shared footer include usage** -- each page replaces its entire footer block (from `<!-- Footer CTA -->` to `</html>`) with:

```php
<?php include __DIR__ . '/../includes/public-footer.php'; ?>
```

For `index.php` (which is in the root):
```php
<?php include __DIR__ . '/includes/public-footer.php'; ?>
```

**Hero section example** (about.php before/after):

Before:
```php
<h1 class="display-4 mb-3" style="font-weight:900;">About Us</h1>
<p class="lead opacity-75 mx-auto" style="max-width:600px;">Discover our story...</p>
```

After:
```php
<h1 class="display-4 mb-3" style="font-weight:900;"><?= e(getSetting('about_hero_title', 'About Us')) ?></h1>
<p class="lead opacity-75 mx-auto" style="max-width:600px;"><?= e(getSetting('about_hero_subtitle', 'Discover our story, vision, and the values that drive us to provide exceptional education.')) ?></p>
```

**The shared footer file** will contain the complete dynamic footer matching `index.php`'s current implementation:
- Footer CTA with `getSetting('home_footer_cta_show')`, `getSetting('home_footer_cta_title')`, etc.
- JSON-decoded Quick Links and Programs from `getSetting('footer_quick_links')` and `getSetting('footer_programs')`
- Dynamic contact info from `getSetting('footer_contact_address')`, etc.
- Dynamic social links from `getSetting('footer_social_facebook')`, etc.
- WhatsApp floating button
- Bootstrap JS script tag
- Closing `</body></html>` tags

**Note:** Each page may have page-specific scripts (e.g., teachers.php has tap-to-flip, about.php has IntersectionObserver, notifications.php has popup logic). These scripts will remain in their respective pages BEFORE the footer include. The footer include will only contain the closing `<script src="bootstrap.bundle.min.js">`, `</body>`, and `</html>` tags.

---

### 6. Impact

- Eliminates ~480 lines of duplicated code across 7 pages
- Any footer change via the Footer Manager admin page now instantly applies to ALL pages
- Any CTA text change via the Page Content Manager instantly applies everywhere
- Hero sections on all public pages become editable from the admin panel
- Adding new pages in the future only requires a single `include` line for the footer

