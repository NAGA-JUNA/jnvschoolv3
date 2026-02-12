

## Fix Logo Display Across All Pages + Admin Logo Preview/Crop

### Problem
From the screenshot, the school logo in the public navbar is clipped at `40x40px`, showing only partial text ("ARA NO SCHO"). The same small size is used in the admin sidebar and footer. The admin settings page lacks a proper logo preview and crop tool.

### Changes Overview

#### 1. Fix Public Navbar Logo (all public pages)
**Files**: `index.php`, `public/about.php`, `public/teachers.php`, `public/notifications.php`, `public/events.php`, `public/gallery.php`

- Increase logo from `40x40px` to `48x48px` (desktop) with `object-fit: contain` instead of `cover` so the full logo is visible without cropping
- Add the school short name text next to the logo (e.g., "JNV") for brand visibility
- Mobile: keep logo at `40x40px` but still use `contain`

#### 2. Fix Admin Sidebar Logo
**File**: `includes/header.php`

- Increase sidebar logo from `40x40px` to `48x48px`
- Change `object-fit` from `cover` to `contain` to prevent cropping
- Add a white/light background circle behind the logo for better contrast on the dark sidebar

#### 3. Fix Footer Logo
**File**: `includes/public-footer.php`

- Increase footer logo from `42x42px` to `48x48px`
- Change `object-fit` to `contain`

#### 4. Enhanced Logo Section in Admin Settings
**File**: `admin/settings.php`

- Add a larger logo preview card (200x200px display area with contained fit)
- Add recommended size note: "Recommended: 200x200px or larger, square format"
- Add a simple client-side crop/resize tool using HTML5 Canvas:
  - When a file is selected, show a preview with a square crop overlay
  - User can drag/resize the crop area
  - On upload, the cropped version is sent as the file
- Add a "Current Logo" section showing the logo at multiple sizes (sidebar preview at 48px, navbar preview at 48px, favicon size at 32px) so admins can see how it looks everywhere

#### 5. Mobile Navbar Logo Check
- On screens below 576px, logo stays at `36x36px` with `object-fit: contain`
- Remove `max-width: 200px; overflow: hidden; text-overflow: ellipsis` constraint on `.navbar-brand` that clips the logo

### Technical Details

**Logo `object-fit` fix** (applied to all 8 files with logo rendering):
```php
<!-- Before -->
<img src="..." style="width:40px;height:40px;border-radius:8px;object-fit:cover;">

<!-- After -->
<img src="..." style="width:48px;height:48px;border-radius:8px;object-fit:contain;background:#fff;padding:2px;">
```

**Admin settings crop tool** -- lightweight Canvas-based cropper:
- Uses vanilla JS (no external library needed)
- Shows file preview when selected
- Draws a draggable square crop overlay
- Exports cropped image as a Blob and submits via FormData
- Falls back to standard upload if JS is disabled

**Logo preview sizes card in settings**:
```html
<div class="card">
  <div class="card-header">Logo Preview (All Sizes)</div>
  <div class="card-body d-flex align-items-end gap-4">
    <div class="text-center">
      <img src="..." style="width:48px;height:48px;object-fit:contain;">
      <small>Navbar</small>
    </div>
    <div class="text-center">
      <img src="..." style="width:48px;height:48px;object-fit:contain;">
      <small>Sidebar</small>
    </div>
    <div class="text-center">
      <img src="..." style="width:32px;height:32px;object-fit:contain;">
      <small>Favicon</small>
    </div>
  </div>
</div>
```

### Files to Modify

| File | Change |
|------|--------|
| `index.php` | Navbar logo size 48px, object-fit:contain, add short name |
| `public/about.php` | Same navbar logo fix |
| `public/teachers.php` | Same navbar logo fix |
| `public/notifications.php` | Same navbar logo fix |
| `public/events.php` | Same navbar logo fix |
| `public/gallery.php` | Same navbar logo fix |
| `includes/header.php` | Admin sidebar logo 48px, object-fit:contain, light bg |
| `includes/public-footer.php` | Footer logo 48px, object-fit:contain |
| `admin/settings.php` | Enhanced logo card with preview sizes + crop tool |

