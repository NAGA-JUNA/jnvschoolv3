

## Redesign Admin Sidebar to Premium SaaS Style

### Overview
Completely redesign the admin sidebar in `header.php` to match the reference image's modern, minimal SaaS aesthetic. The sidebar becomes a floating, rounded card with soft shadows, featuring a clean icon set, rounded active-state pills, notification badges, a bottom-docked theme switcher (Light/Dark toggle pill), a profile card, and a polished collapsed/mini mode.

### Key Visual Changes

| Element | Current | New (matching reference) |
|---------|---------|--------------------------|
| Sidebar container | Full-height flush dark panel | Floating rounded card with `border-radius: 20px`, margin from edges, soft shadow |
| Background (Light) | N/A (always dark) | Soft cream/white `#faf8f5` with subtle shadow |
| Background (Dark) | `#0f172a` gradient | Deep charcoal `#1a1a1a` with soft warm glow |
| Active menu item | Full-width colored background | Rounded pill highlight with brand-primary bg, bold text |
| Menu icons | Bootstrap Icons inline | Same icons but softer weight, centered in collapsed mode |
| Notification badges | None | Red circular counters (e.g., 24, 99+) aligned right on Activity/Notifications |
| Section dividers | Uppercase tiny title | Thin line divider + collapsible group header with chevron |
| Theme switcher | Header button + dropdown toggle | Bottom-docked pill toggle: `[Sun Light | Moon Dark]` segmented control |
| Profile card | Only in top-bar dropdown | Sticky bottom card: avatar + name + role badge + logout button |
| Collapsed mode | Narrow 70px strip | Floating mini sidebar with rounded icons, tooltips, avatar at bottom |
| Logo | Square 64px with border | Circular avatar-style logo (56px) with subtle shadow |

### Detailed Changes

#### 1. Sidebar Container Styles
```css
.sidebar {
    width: 260px;
    margin: 16px;
    height: calc(100vh - 32px);
    border-radius: 20px;
    background: var(--sidebar-bg);
    box-shadow: var(--sidebar-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
```

Light theme sidebar variables:
```css
:root {
    --sidebar-bg: #faf8f5;
    --sidebar-text: #1a1a1a;
    --sidebar-text-muted: #9ca3af;
    --sidebar-hover: rgba(0,0,0,0.04);
    --sidebar-active-bg: var(--brand-primary);
    --sidebar-active-text: #fff;
    --sidebar-shadow: 0 4px 24px rgba(0,0,0,0.08);
    --sidebar-border: rgba(0,0,0,0.06);
}
```

Dark theme sidebar variables:
```css
html[data-theme="dark"] {
    --sidebar-bg: #1a1a1a;
    --sidebar-text: #e5e5e5;
    --sidebar-text-muted: #6b7280;
    --sidebar-hover: rgba(255,255,255,0.06);
    --sidebar-shadow: 0 4px 24px rgba(0,0,0,0.4), 0 0 60px rgba(var(--brand-primary-rgb),0.05);
    --sidebar-border: rgba(255,255,255,0.08);
}
```

#### 2. Logo Area (Top)
- Circular logo avatar: `width: 56px; height: 56px; border-radius: 50%` with white background and subtle shadow
- Collapse toggle button: small `>>` chevron icon, top-right of header area
- In collapsed mode: logo shrinks to 40px circle, centered

#### 3. Navigation Items
- Clean layout: `padding: 10px 16px; border-radius: 12px; margin: 2px 12px`
- Active state: rounded pill with `background: var(--sidebar-active-bg)` and white text
- Hover: subtle `background: var(--sidebar-hover)` with `border-radius: 12px`
- Icons: `font-size: 1.15rem; width: 22px`
- Notification badges: red circular pills aligned to the right of the item (for Notifications, Activity items)

#### 4. Section Groups
- Collapsible section headers with chevron (like "Setting" in reference) using `<details>` or JS toggle
- Thin divider line between sections
- Sub-items with colored dots (red, green, gray, yellow) matching the reference for settings sub-menu

#### 5. Theme Switcher (Bottom-Docked)
- Segmented control pill at bottom: `[Sun Light | Moon Dark]`
- Active segment gets white background with shadow in light mode, dark-card bg in dark mode
- Container: rounded pill with `background: var(--sidebar-hover)`
- Replaces the header theme toggle button entirely (keep the dropdown toggle too for convenience)

#### 6. Profile Card (Bottom-Docked)
- Sticky at bottom of sidebar
- Shows: circular avatar (initials), full name, email/role badge
- "Log out" button with arrow icon below
- In collapsed mode: just the avatar circle

#### 7. Collapsed/Mini Mode
- Width: `70px` with same `border-radius: 20px` and margin
- Icons centered, no text
- Tooltips on hover (existing behavior, kept)
- Bottom: settings gear icon, avatar circle, logout icon (stacked vertically)
- Active item: circular/square highlight instead of pill

#### 8. Main Content Adjustment
- `margin-left` accounts for sidebar width + margins (260px + 32px = 292px expanded, 70px + 32px = 102px collapsed)

#### 9. Mobile
- Sidebar slides in as overlay (existing behavior)
- Full-width rounded card appearance maintained
- Overlay backdrop click to close

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/includes/header.php` | Complete sidebar CSS rewrite (styles section), updated sidebar HTML structure with new classes, profile card at bottom, theme switcher pill, notification badges, collapsible setting sections, circular logo |
| `php-backend/includes/footer.php` | Updated JS for new theme switcher pill state sync, settings section toggle |

### Implementation Order
1. Rewrite all sidebar CSS in `header.php` `<style>` block with new variables and classes
2. Restructure sidebar HTML: circular logo, nav items with badges, collapsible sections, bottom-docked theme switcher + profile card
3. Update collapsed mode styles for mini floating sidebar look
4. Update `footer.php` JS to handle theme pill toggle state and section collapse
5. Adjust main-content margins for new sidebar dimensions
6. Ensure mobile overlay still works with new rounded design

