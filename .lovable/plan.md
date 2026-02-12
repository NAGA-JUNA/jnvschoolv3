

## Premium Glassmorphism Navbar Redesign

### Overview
Replace the current basic dark navbar (duplicated across 7+ PHP files) with a modern, premium glassmorphism navbar. This involves creating a shared include file to eliminate code duplication, adding a new database table for admin-managed menu items, and implementing advanced scroll behaviors.

### Architecture Change: Shared Navbar Include

**Problem**: The navbar HTML and CSS are currently copy-pasted across 7 files (`index.php`, `about.php`, `teachers.php`, `notifications.php`, `events.php`, `gallery.php`, `admission-form.php`). Any change requires editing all 7 files.

**Solution**: Create `php-backend/includes/public-navbar.php` -- a single shared include that all public pages use.

### New Files

#### 1. `php-backend/includes/public-navbar.php`
The shared navbar component containing all HTML, CSS, and JS for the premium navbar. Expected variables set by the parent page: `$navLogo`, `$logoPath`, `$schoolName`, `$bellNotifs`, `$notifCount`, `$currentPage` (for active state).

#### 2. Database: `nav_menu_items` table
```sql
CREATE TABLE nav_menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(100) NOT NULL,
  url VARCHAR(255) NOT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  link_type ENUM('internal','external') DEFAULT 'internal',
  is_visible TINYINT(1) DEFAULT 1,
  is_cta TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Default rows: Home, About Us, Our Teachers, Notifications, Gallery, Events, Apply Now (CTA).

### Navbar Design Specs

**Desktop (1024px+)**:
- Glassmorphism: `background: rgba(15,23,42,0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.08);`
- Sticky with shrink on scroll (padding reduces from 1rem to 0.5rem)
- Hide on scroll down, show on scroll up (smart hide behavior)
- Left: Logo (160px wide, with `.logo-container` border)
- Center: Menu items from database, with smooth underline hover animation
- Right: Notification bell icon (not full button) with red badge count, Login button, gradient "Apply Now" CTA button
- Active page auto-highlighted based on `$currentPage`

**Mobile (below 992px)**:
- Hamburger icon triggers a slide-in drawer (off-canvas from right)
- Drawer: full-height overlay with large touch-friendly items, icons next to labels
- Notification badge visible in drawer
- "Apply Now" CTA at bottom of drawer
- Sticky bottom CTA bar on mobile: "Apply Now" gradient button

**Animations**:
- Underline hover: CSS `::after` pseudo-element scales from 0 to 100% width
- Logo hover: subtle scale(1.03) with glow
- CTA button: gradient glow animation (pulsing shadow)
- Bell shake: CSS keyframe shake when `$notifCount > 0`
- Scroll shrink: JS adds `.scrolled` class, CSS transitions padding/shadow

**Accessibility**:
- ARIA labels on hamburger, bell, drawer
- Keyboard navigation (focusable menu items)
- Proper `role` attributes

### Admin Control Panel

#### 3. `php-backend/admin/navigation-settings.php`
A new admin page for managing navbar menu items:
- List all menu items in a sortable table
- Add / Edit / Remove items via modal form
- Fields: Label, URL, Icon (Bootstrap icon picker), Link Type (internal/external), Visibility toggle, CTA flag
- Drag-and-drop reorder (using existing pattern from Footer Manager)
- Setting for CTA button text (uses the item marked `is_cta=1`)
- AJAX endpoint for reorder and CRUD operations

#### 4. `php-backend/admin/ajax/nav-actions.php`
AJAX handler for:
- `action=list` -- return all menu items
- `action=save` -- create/update a menu item
- `action=delete` -- remove a menu item
- `action=reorder` -- update sort_order for all items

#### 5. Add sidebar link
In `includes/header.php`, add a "Navigation" link under the Configuration section.

### Files Modified

| File | Change |
|------|--------|
| `php-backend/index.php` | Remove inline navbar HTML/CSS, add `include 'includes/public-navbar.php'`, set `$currentPage='home'` |
| `php-backend/public/about.php` | Same -- remove navbar, include shared, set `$currentPage='about'` |
| `php-backend/public/teachers.php` | Same, `$currentPage='teachers'` |
| `php-backend/public/notifications.php` | Same, `$currentPage='notifications'` |
| `php-backend/public/events.php` | Same, `$currentPage='events'` |
| `php-backend/public/gallery.php` | Same, `$currentPage='gallery'` |
| `php-backend/public/admission-form.php` | Same, `$currentPage='apply'` |
| `php-backend/includes/header.php` | Add "Navigation" sidebar link under Configuration |
| `php-backend/schema.sql` | Add `nav_menu_items` table with default data |

### New Files

| File | Purpose |
|------|---------|
| `php-backend/includes/public-navbar.php` | Shared premium navbar component |
| `php-backend/admin/navigation-settings.php` | Admin page for managing menu items |
| `php-backend/admin/ajax/nav-actions.php` | AJAX handler for menu CRUD and reorder |

### Technical Details

**Smart scroll behavior (JS in public-navbar.php)**:
```javascript
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const nav = document.querySelector('.premium-navbar');
    const scroll = window.scrollY;
    // Shrink mode
    nav.classList.toggle('scrolled', scroll > 50);
    // Hide/show on scroll direction
    if (scroll > lastScroll && scroll > 200) nav.classList.add('nav-hidden');
    else nav.classList.remove('nav-hidden');
    lastScroll = scroll;
});
```

**Mobile drawer**: Uses Bootstrap 5 Offcanvas component (already available) for the slide-in menu, avoiding custom JS.

**Menu items loading**: The shared navbar queries `nav_menu_items` table. If the table doesn't exist yet (before schema update), it falls back to the hardcoded default menu array.

**Gradient CTA button**:
```css
.nav-cta-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff; border: none; border-radius: 50px;
    padding: 0.5rem 1.5rem; font-weight: 600;
    box-shadow: 0 4px 15px rgba(239,68,68,0.4);
    animation: ctaGlow 2s ease-in-out infinite alternate;
}
@keyframes ctaGlow {
    0% { box-shadow: 0 4px 15px rgba(239,68,68,0.3); }
    100% { box-shadow: 0 4px 25px rgba(239,68,68,0.6); }
}
```

### Implementation Order
1. Add `nav_menu_items` table to schema.sql
2. Create `includes/public-navbar.php` with full premium navbar
3. Create `admin/navigation-settings.php` and `admin/ajax/nav-actions.php`
4. Update all 7 public pages to use the shared include
5. Add sidebar link in `includes/header.php`

