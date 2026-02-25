

## Show Uploaded School Logo on Login Page

### What Changes

Replace the graduation cap icon on the login page's left panel with the **actual school logo** uploaded via Admin Settings (stored at `uploads/branding/school_logo.png`).

### File: `php-backend/login.php`

1. **Fetch logo path from database** (alongside school_name and school_tagline): Query the `school_logo` setting so it uses whatever logo is uploaded via the admin panel.

2. **Replace the icon with an `<img>` tag** (line 90): Change from:
   ```html
   <div class="school-icon"><i class="bi bi-mortarboard-fill"></i></div>
   ```
   To:
   ```html
   <div class="school-icon">
     <img src="/<?= htmlspecialchars($schoolLogo) ?>" alt="School Logo"
          style="max-width:60px;max-height:60px;object-fit:contain">
   </div>
   ```
   With a fallback to `uploads/branding/school_logo.png` if no setting is found.

3. **Adjust CSS** for `.school-icon`: Update to use a white/semi-transparent background so the logo is visible against the blue gradient, and allow slightly larger sizing.

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/login.php` | Fetch logo setting, replace icon with `<img>` tag |

