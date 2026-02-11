

## Fix: Sidebar Not Scrollable / Menu Items Cut Off

### Problem
The admin sidebar uses `min-height: 100vh` combined with `position: fixed`. For a fixed-position element, `overflow-y: auto` only triggers scrolling when there's a defined **maximum height** — `min-height` sets a floor but no ceiling, so the browser never considers the content to be overflowing. This causes the bottom menu items (Configuration section: Settings, Page Content, Footer Manager, Quote Highlight, Support) to be unreachable.

### Fix
In `php-backend/includes/header.php`, change one CSS property on the `.sidebar` class:

- **Before:** `min-height: 100vh;`
- **After:** `height: 100vh;`

This gives the sidebar a fixed height equal to the viewport, so when the nav links exceed that height, `overflow-y: auto` kicks in and enables scrolling.

### File to Modify
| File | Change |
|------|--------|
| `php-backend/includes/header.php` | Line 37: change `min-height: 100vh;` to `height: 100vh;` in the `.sidebar` CSS rule |

### Technical Detail
The complete `.sidebar` rule after the fix:
```css
.sidebar {
    width: var(--sidebar-width);
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    height: 100vh;          /* was: min-height: 100vh */
    position: fixed;
    top: 0; left: 0;
    z-index: 1040;
    transition: transform 0.3s ease;
    overflow-y: auto;
}
```

This is a one-line CSS fix. No other files need changes.

