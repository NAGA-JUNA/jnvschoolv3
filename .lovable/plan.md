

## Fix Logo Management System with Full Sync

### Problem
When the Super Admin uploads a new logo, it does not reliably update across the site due to:
1. **No cache-busting** -- browsers cache the old logo image at the same URL
2. **No `logo_updated_at` timestamp** in the database to track changes
3. **Logo sizes don't match the requested specs** (navbar should be 42px height, not 160px width)
4. **Favicon is not auto-generated** from the uploaded logo
5. **Admin sidebar logo** reads directly from `school_logo` setting without cache-busting
6. **No "last updated" display** or "clear cache" button in admin

### Changes

#### 1. Database: Add `logo_updated_at` setting
In `admin/settings.php` logo upload handler, after saving the logo file, also save a `logo_updated_at` timestamp:
```php
$db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('logo_updated_at',?) ON DUPLICATE KEY UPDATE setting_value=?")
   ->execute([time(), time()]);
```
Same for favicon upload -- save `favicon_updated_at`.

#### 2. Cache-busting across all logo references
Create a helper variable `$logoVersion` based on `logo_updated_at` and append `?v=$logoVersion` to all logo URLs.

**Files affected:**
- `includes/public-navbar.php` -- update `$logoPath` usage to append `?v=`
- `includes/public-footer.php` -- same
- `includes/header.php` -- admin sidebar logo gets `?v=`
- All 7 public pages (`index.php`, `about.php`, etc.) -- set `$logoVersion` when constructing `$logoPath`

#### 3. Update logo display sizes per spec

| Location | Current | New |
|----------|---------|-----|
| Navbar desktop | `width: 160px` | `height: 42px; width: auto` |
| Navbar mobile | `width: 120px` | `height: 34px; width: auto` |
| Footer | `width: 140px` | `max-width: 120px; height: auto` |
| Admin sidebar | `width: 140px` | `width: 64px; height: 64px; object-fit: contain` |
| Favicon | manual upload | auto-generated 32x32 from logo |

**CSS changes in `includes/public-navbar.php`:**
- `.pn-logo-wrap img` changes from `width:160px;height:auto` to `height:42px;width:auto;max-width:200px`
- Mobile media query changes from `width:120px!important` to `height:34px!important;width:auto!important`

**CSS changes in `includes/public-footer.php`:**
- Footer logo style changes to `max-width:120px;height:auto`

**CSS changes in `includes/header.php`:**
- `.sidebar .brand img` changes to `width:64px;height:64px;object-fit:contain`

#### 4. Auto-generate favicon from uploaded logo
In `admin/settings.php`, after logo upload, use PHP GD library to create a 32x32 favicon PNG from the uploaded image and save it as `uploads/branding/favicon.png`. Also update `school_favicon` setting automatically.

```php
// Auto-generate favicon
$srcImg = imagecreatefromstring(file_get_contents($uploadedPath));
if ($srcImg) {
    $favicon = imagecreatetruecolor(32, 32);
    imagealphablending($favicon, false);
    imagesavealpha($favicon, true);
    imagecopyresampled($favicon, $srcImg, 0, 0, 0, 0, 32, 32, imagesx($srcImg), imagesy($srcImg));
    @mkdir(__DIR__.'/../uploads/branding', 0755, true);
    imagepng($favicon, __DIR__.'/../uploads/branding/favicon.png');
    imagedestroy($favicon); imagedestroy($srcImg);
    $db->prepare("INSERT INTO settings ... VALUES ('school_favicon','branding/favicon.png') ON DUPLICATE KEY UPDATE ...");
}
```

#### 5. Store logo in `/uploads/branding/` directory
Change upload destination from `uploads/logo/` to `uploads/branding/` for new uploads. Keep backward compatibility by checking both paths.

Update the `$logoPath` construction in all pages:
```php
$navLogo = getSetting('school_logo', '');
$logoVersion = getSetting('logo_updated_at', '0');
if ($navLogo) {
    // Support both old (uploads/logo/) and new (uploads/branding/) paths
    $logoPath = (strpos($navLogo, '/uploads/') === 0) ? $navLogo : '/uploads/branding/' . $navLogo;
    $logoPath .= '?v=' . $logoVersion;
}
```

#### 6. Dynamic favicon in HTML head
All pages with `<link rel="icon">` will use the database favicon path with cache-busting:
```php
$favicon = getSetting('school_favicon', '');
$favVer = getSetting('favicon_updated_at', '0');
if ($favicon):
    $favPath = (strpos($favicon, '/') === 0) ? $favicon : '/uploads/branding/' . $favicon;
?>
<link rel="icon" href="<?= e($favPath) ?>?v=<?= e($favVer) ?>">
<?php endif; ?>
```

#### 7. Admin Settings UI improvements
In `admin/settings.php`:
- Add "Last updated: [timestamp]" below logo preview
- Add "Clear Cache and Refresh Logo" button (JS that appends random `?v=` and reloads)
- Update preview sizes to match new specs (42px navbar, 64px sidebar, 120px footer, 32px favicon)
- Add file validation: minimum 160px width, accepted types only
- Show success toast after upload

#### 8. Super Admin permission check
The logo upload handler already requires `requireAdmin()`. Add an explicit `isSuperAdmin()` check:
```php
if ($action === 'logo_upload') {
    if (!isSuperAdmin()) { setFlash('error', 'Only Super Admin can change the logo.'); }
    else { /* existing upload logic */ }
}
```

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/admin/settings.php` | Logo upload to branding dir, auto-favicon, logo_updated_at, Super Admin check, updated preview UI, cache-clear button, last-updated display |
| `php-backend/includes/public-navbar.php` | Logo size 42px height (34px mobile), cache-bust `?v=` on logo src |
| `php-backend/includes/public-footer.php` | Logo max-width 120px, cache-bust `?v=` |
| `php-backend/includes/header.php` | Sidebar logo 64x64, cache-bust `?v=` |
| `php-backend/index.php` | `$logoPath` uses branding dir + `?v=` version, dynamic favicon |
| `php-backend/public/about.php` | Same logo path + version update |
| `php-backend/public/teachers.php` | Same |
| `php-backend/public/notifications.php` | Same |
| `php-backend/public/events.php` | Same |
| `php-backend/public/gallery.php` | Same |
| `php-backend/public/admission-form.php` | Same |

### Implementation Order
1. Update `admin/settings.php` -- new upload logic, branding dir, auto-favicon, timestamps, UI
2. Update `includes/public-navbar.php` -- new sizes + cache-bust
3. Update `includes/public-footer.php` -- new size + cache-bust
4. Update `includes/header.php` -- sidebar logo size + cache-bust
5. Update all 7 public pages -- logo path construction with version

