

## Fix Sidebar Minimize Button and Differentiate Top Bar

### Issue 1: Minimize Button Getting Clipped

The sidebar has `overflow: hidden` on line 152 of `header.php`, but the collapse button is positioned at `right: -14px` (partially outside the sidebar). This causes the button to be cut off.

**Fix:** Change the sidebar `overflow: hidden` to `overflow: visible` on the main container, and keep `overflow-y: auto` only on the `.sidebar-nav` scroll area (which already has it). This lets the collapse button extend outside the sidebar boundary while still allowing the nav to scroll.

### Issue 2: Top Bar Looks Too Similar to Sidebar

Currently both the sidebar and top bar use nearly identical light backgrounds. The top bar needs more visual distinction.

**Fix:** Give the top bar a subtle bottom border with a gradient accent line using the brand color, add slightly more padding, and make the glassmorphism effect more pronounced to create a clear visual hierarchy difference from the sidebar.

### Technical Details

**File modified:** `php-backend/includes/header.php`

**Changes:**

1. **Line 152** -- Change sidebar `overflow: hidden` to `overflow: visible` so the collapse button at `right: -14px` is fully visible

2. **Lines 437-452** -- Update `.top-bar` styles:
   - Add a 2px gradient bottom border using brand colors (replaces the plain 1px border)
   - Slightly increase blur from 16px to 20px for stronger glassmorphism
   - Add a subtle brand-tinted background to differentiate from the plain white sidebar

3. **Line 196** -- Adjust collapse button position slightly if needed for better visibility

| File | Change |
|------|--------|
| `php-backend/includes/header.php` | Fix sidebar overflow + enhance top bar styling |

