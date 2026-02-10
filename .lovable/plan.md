

# Fix Logo, Add Themes, Enhance Settings & Add Support Page

## Overview
Fix the school logo not displaying after upload, add theme color presets, enhance the System Info card, add super admin feature access controls, and create a Support page for JNV Tech.

---

## 1. Fix School Logo Not Updating

**Root Cause**: In `includes/header.php` line 102, the logo is rendered as `<img src="<?= e($schoolLogo) ?>">` but the database stores only the filename (e.g., `school_logo.png`), not the full path. The settings page correctly uses `/uploads/logo/` prefix when displaying, but the header does not.

**Fix**: Update `includes/header.php` line 102 to prepend the path:
```php
<img src="/uploads/logo/<?= e($schoolLogo) ?>" alt="Logo">
```

---

## 2. Theme Color Presets

Add a row of clickable color swatches below the existing color picker in the School Info form. Clicking a swatch sets the color picker value. Preset colors:
- Navy Blue (#1e40af), Emerald (#059669), Purple (#7c3aed), Rose (#e11d48), Amber (#d97706), Slate (#334155), Teal (#0d9488), Indigo (#4f46e5)

Implementation: Simple JavaScript `onclick` that sets the color input value.

---

## 3. Enhanced System Info Card

Replace the basic table with a richer card showing:
- **Server**: PHP version, Server software, MySQL version (via `$db->query("SELECT VERSION()")->fetchColumn()`)
- **Database**: DB name, total tables count, disk usage estimate
- **Application Stats**: Total students (with active count), total teachers (with active count), total users, total notifications, total events
- **Uptime**: Server uptime and current server time with timezone
- Progress bars for visual representation of active vs total records

---

## 4. Super Admin Feature Access Controls

Add a new "Feature Access Control" card (visible only to super_admin) in the Settings page. This uses the existing `settings` table with keys like `feature_admissions`, `feature_gallery`, `feature_events`, etc.

Each feature gets a Bootstrap toggle switch. When OFF, the corresponding sidebar link is hidden for non-super-admin users and the page shows an "Access Denied" message.

**Features to control**:
- Admissions module
- Gallery module
- Events module
- Home Slider
- Notifications
- Reports
- Audit Logs

**Implementation**:
- Settings page: Add a card with toggle switches for each feature, saved via a single form POST
- `includes/header.php`: Check `getSetting('feature_xxx', '1')` before rendering each sidebar link
- Each module page: Add a check at the top: if feature is OFF and user is not super_admin, show access denied

---

## 5. Support Page (`admin/support.php`)

A new page accessible from the sidebar under Configuration, showing JNV Tech support information:

- **Header**: JNV Tech logo/branding with tagline "Journey to New Value"
- **Contact Cards**:
  - WhatsApp: +91 8106811171 (with click-to-chat link: `https://wa.me/918106811171`)
  - Email: contact@jnvtech.com (with mailto link)
  - Website: jnvtech.com (if applicable)
- **Support Hours**: Business hours information
- **Quick Links**: Documentation, FAQ, Report a Bug
- **About Section**: Brief description of JNV Tech and the school management system
- **Version Info**: Current system version (e.g., v2.0)

---

## Technical Details

### Files Modified
1. `php-backend/includes/header.php` -- Fix logo path, add feature access checks to sidebar links, add Support link
2. `php-backend/admin/settings.php` -- Add theme presets, enhanced system info, feature access controls

### Files Created
3. `php-backend/admin/support.php` -- New support/contact page for JNV Tech

### No Schema Changes Required
All feature toggles use the existing `settings` key-value table.

