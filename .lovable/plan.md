

## Reorganize Admin Settings into Tabbed Sub-Pages with Enhanced Color Picker

### Overview

The current `settings.php` is a single long page with all sections stacked vertically. This plan splits it into organized **tab-based navigation** using Bootstrap 5 pills/tabs, so each section is its own "page" within settings. The color picker will also be enhanced with a live preview.

### New Settings Page Layout

The settings page will have a horizontal tab bar at the top with these tabs:

| Tab | Icon | Contents |
|-----|------|----------|
| General | bi-building | School info, academic year, admissions toggle |
| Appearance | bi-palette | Theme color picker with live preview, logo upload, preset swatches |
| Content | bi-file-text | About page content (History, Vision, Mission) |
| Social & SMS | bi-share | Social media links + WhatsApp/SMS config |
| Popup Ad | bi-megaphone | Popup advertisement settings |
| Users | bi-people | Create user + user list table |
| Access Control | bi-shield-lock | Feature toggles (Super Admin only) |
| System | bi-cpu | System info, database stats, danger zone |

Each tab shows/hides its content area using Bootstrap's built-in `nav-pills` and `tab-content` with `tab-pane` -- no page reloads needed.

### Enhanced Color Picker (Appearance Tab)

The Appearance tab will include:
- Native HTML5 `<input type="color">` picker (already exists)
- 8 preset color swatches (already exists) -- will be enlarged and improved
- **Live preview panel**: A small card showing how the selected color looks on a navbar, button, and link in real-time as the user picks colors
- The preview updates instantly via JavaScript `oninput` event on the color picker

### What Changes

**Single file modified**: `php-backend/admin/settings.php`

The file will be restructured as follows:

1. **Tab navigation bar** at the top using Bootstrap `nav nav-pills` with icons for each section
2. **Tab content area** with `tab-pane` divs wrapping each existing form section
3. **Appearance tab** gets the enhanced color picker with:
   - Larger color input
   - Preset swatches in a grid
   - Live preview mini-card showing navbar bar, button, and accent text in the selected color
   - JavaScript: `document.getElementById('primaryColorPicker').addEventListener('input', ...)` updates preview elements in real-time
4. **System tab** gets the system info card + danger zone (for super admins)
5. All existing form handlers (PHP POST processing at top of file) remain unchanged -- only the HTML layout is reorganized
6. URL hash support: clicking a tab updates `window.location.hash`, and on page load, the correct tab is activated from the hash (so after form submit + redirect, the user returns to the same tab)

### Technical Details

**Tab persistence after form submit**: Since each form POSTs and redirects back to `settings.php`, a hash fragment will be used:
- Each form's submit button gets an `onclick` that sets `window.location.hash` before submit
- On page load, JavaScript reads `window.location.hash` and activates the matching tab
- Example: submitting the Social Links form sets `#social` hash, and on reload, the Social tab is auto-selected

**Live color preview JavaScript**:
```javascript
colorInput.addEventListener('input', function() {
    preview.querySelector('.preview-navbar').style.background = this.value;
    preview.querySelector('.preview-btn').style.background = this.value;
    preview.querySelector('.preview-link').style.color = this.value;
});
```

**Tab navigation HTML structure**:
```text
[General] [Appearance] [Content] [Social & SMS] [Popup Ad] [Users] [Access] [System]
+---------------------------------------------------------------------------+
|                                                                           |
|   (Active tab content shown here -- only one visible at a time)          |
|                                                                           |
+---------------------------------------------------------------------------+
```

**Files to modify**: `php-backend/admin/settings.php` (single file, layout reorganization only)

