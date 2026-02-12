

## Widen Logo Display Across All Pages

### Problem
The logo is currently displayed as a 48x48px square, but the actual school logo is rectangular/wide. The user wants the logo container to be **160px wide** (auto height) so the full logo text is visible, matching the reference screenshot showing a wide white-bordered box containing the complete logo.

### Changes

#### 1. Public Navbar Logo (6 files)
**Files**: `index.php`, `public/about.php`, `public/teachers.php`, `public/notifications.php`, `public/events.php`, `public/gallery.php`

Change the logo `<img>` style from:
```
width:48px;height:48px;border-radius:8px;object-fit:contain;background:#fff;padding:2px;
```
To:
```
width:160px;height:auto;border-radius:8px;object-fit:contain;background:#fff;padding:4px;border:2px solid rgba(255,255,255,0.3);
```

On mobile (below 576px), reduce to `width:120px` via a CSS rule:
```css
@media (max-width: 575.98px) {
  .navbar-brand img { width: 120px !important; }
}
```

#### 2. Admin Sidebar Logo
**File**: `includes/header.php`

Update the sidebar logo `<img>` to use `width:140px;height:auto;object-fit:contain;background:#fff;padding:4px;border-radius:8px;border:2px solid rgba(255,255,255,0.2);`

Also update the fallback placeholder from 40x40 to a wider box.

#### 3. Footer Logo
**File**: `includes/public-footer.php`

Update the footer logo from `width:48px;height:48px` to `width:140px;height:auto;object-fit:contain;background:#fff;padding:4px;border-radius:8px;`

#### 4. Admin Settings Logo Preview
**File**: `admin/settings.php`

Update the preview sizes section to reflect the new 160px navbar size, 140px sidebar size, and 140px footer size.

### Files to Modify

| File | Change |
|------|--------|
| `php-backend/index.php` | Logo width 160px, auto height, border |
| `php-backend/public/about.php` | Same |
| `php-backend/public/teachers.php` | Same |
| `php-backend/public/notifications.php` | Same |
| `php-backend/public/events.php` | Same |
| `php-backend/public/gallery.php` | Same |
| `php-backend/includes/header.php` | Sidebar logo 140px wide, auto height |
| `php-backend/includes/public-footer.php` | Footer logo 140px wide, auto height |
| `php-backend/admin/settings.php` | Update preview sizes to match new widths |

### Summary
- Navbar logo: **160px wide** (120px on mobile)
- Sidebar logo: **140px wide**
- Footer logo: **140px wide**
- All use `height:auto` with `object-fit:contain` so the logo scales proportionally
- White background with subtle border for visual emphasis
