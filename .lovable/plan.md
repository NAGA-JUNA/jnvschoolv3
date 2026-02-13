

## Premium Admin Dashboard UI Enhancement

### Overview
Enhance the admin dashboard layout with three key features: a collapsible sidebar with smooth animations, a premium admin profile dropdown with status and quick actions, and a dark/light theme toggle with persistence. All changes are in `php-backend/includes/header.php` and `php-backend/includes/footer.php`.

### 1. Collapsible Sidebar

**Behavior:**
- Toggle button (chevron icon) added to the sidebar header, next to the brand area
- **Expanded** (default, 260px): Logo + school name + full menu labels
- **Collapsed** (70px): Only icons visible, logo shrinks to 40x40
- Smooth CSS `width` transition (300ms ease)
- Tooltips appear on hover for menu items when collapsed (using Bootstrap tooltips)
- Collapsed state saved to `localStorage` key `sidebar_collapsed` and restored on page load

**CSS changes:**
- Add `.sidebar.collapsed` styles: `width: 70px`, hide text labels, center icons, shrink brand area
- `.main-content` margin-left transitions to match sidebar width
- Collapse toggle button styled as a small circular button

### 2. Admin Profile Dropdown (Top Right Header)

**Replace** the current simple dropdown with a premium version:
- **Avatar**: Circular div with user initials (first letter of first + last name), colored background
- **Green dot**: Online status indicator (absolute positioned)
- **Dropdown menu** (Bootstrap 5 dropdown with custom styling):
  - User name + role badge at top (non-clickable header)
  - My Profile (links to settings)
  - Change Password (links to settings with hash)
  - System Settings (visible only to Super Admin, links to `/admin/settings.php`)
  - Divider
  - Switch Theme toggle (sun/moon icon, inline toggle)
  - Divider
  - Logout (red text)
- Smooth animation via Bootstrap + custom CSS transitions

### 3. Dark / Light Theme System

**Implementation:**
- CSS variables approach: define `--bg-body`, `--bg-card`, `--text-primary`, `--text-muted`, `--border-color` etc. for both themes
- `html[data-theme="dark"]` overrides all variables
- Dark theme colors: body `#0f172a`, cards `#1e293b`, text `#e2e8f0`, borders `rgba(255,255,255,0.08)`
- Light theme: current colors (body `#f1f5f9`, cards `#fff`, text `#334155`)
- Toggle via JS: reads/writes `localStorage` key `admin_theme`, applies `data-theme` attribute on `<html>`
- Theme icon in header (sun/moon) toggles on click
- All existing elements updated to use CSS variables instead of hardcoded colors

**Elements themed:**
- Body background, sidebar (already dark -- stays dark in both themes), top-bar, cards, tables, forms, badges, modals, alerts

### 4. Mobile Optimization

- Sidebar overlay behavior stays the same (slide in/out)
- Collapsed state disabled on mobile (always use full overlay sidebar)
- Profile dropdown works as normal on mobile
- Theme toggle accessible from both header icon and dropdown

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/includes/header.php` | Add collapsible sidebar logic, premium profile dropdown, theme system CSS variables, theme toggle button, collapse toggle button, tooltip initialization |
| `php-backend/includes/footer.php` | Add JS for sidebar collapse persistence, theme persistence, tooltip init, clock update |

### Technical Details

**Sidebar collapse CSS:**
```css
.sidebar { width: var(--sidebar-width); transition: width 0.3s ease; }
.sidebar.collapsed { width: 70px; }
.sidebar.collapsed .brand h5,
.sidebar.collapsed .brand small,
.sidebar.collapsed .nav-section-title,
.sidebar.collapsed .nav-link span { display: none; }
.sidebar.collapsed .nav-link { justify-content: center; padding: 0.7rem; }
.sidebar.collapsed .nav-link i { margin: 0; font-size: 1.2rem; }
.sidebar.collapsed .brand img { width: 40px; height: 40px; }
.main-content { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease; }
.sidebar.collapsed ~ .main-content { margin-left: 70px; }
```

**Theme variables:**
```css
:root { --bg-body: #f1f5f9; --bg-card: #fff; --text-primary: #1e293b; --text-muted: #64748b; --border-color: #e2e8f0; --bg-topbar: #fff; }
html[data-theme="dark"] { --bg-body: #0f172a; --bg-card: #1e293b; --text-primary: #e2e8f0; --text-muted: #94a3b8; --border-color: rgba(255,255,255,0.08); --bg-topbar: #1e293b; }
```

**Profile dropdown HTML:**
```html
<div class="dropdown">
    <button class="profile-avatar-btn" data-bs-toggle="dropdown">
        <div class="avatar-circle">AB</div>
        <span class="online-dot"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
        <div class="dropdown-header">Admin Name <span class="badge">Super Admin</span></div>
        <a class="dropdown-item"><i class="bi bi-person"></i> My Profile</a>
        <a class="dropdown-item"><i class="bi bi-key"></i> Change Password</a>
        <!-- Super Admin only -->
        <a class="dropdown-item"><i class="bi bi-gear"></i> System Settings</a>
        <hr>
        <div class="dropdown-item theme-switch">
            <i class="bi bi-moon"></i> Dark Mode <toggle>
        </div>
        <hr>
        <a class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
```

**JS persistence (in footer.php):**
```javascript
// Theme
const savedTheme = localStorage.getItem('admin_theme') || 'light';
document.documentElement.setAttribute('data-theme', savedTheme);

// Sidebar collapse
const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
if (collapsed) document.getElementById('sidebar')?.classList.add('collapsed');

function toggleCollapse() {
    const sb = document.getElementById('sidebar');
    sb.classList.toggle('collapsed');
    localStorage.setItem('sidebar_collapsed', sb.classList.contains('collapsed'));
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('admin_theme', next);
}
```

### Implementation Order
1. Update `header.php` -- add CSS variables for theming, collapsible sidebar styles, profile dropdown markup, theme toggle button, collapse toggle button
2. Update `footer.php` -- add JS for theme persistence, sidebar collapse persistence, tooltip initialization
3. Wrap all nav-link text in `<span>` tags for hide/show on collapse
4. Update all color references to use CSS variables

