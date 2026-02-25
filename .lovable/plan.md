

## Fix Enquiry Form, WhatsApp Circle, Feature Cards Mobile, and Core Team Slider

### Issues Identified

1. **"Need Help?" form fails with "Something went wrong"**: The form posts to `/admin/ajax/enquiry-actions.php` which requires admin login (`requireAdmin()`) and CSRF token. Public visitors can never use this endpoint. A new public endpoint is needed.
2. **WhatsApp button not a perfect circle on mobile**: The padding and icon sizing create a slightly rectangular shape instead of a true circle.
3. **Feature cards look broken on mobile**: Extra blank/grey space appears above the first feature card (visible in the screenshot).
4. **Core Team needs a swipe slider on mobile**: Currently cards stack vertically on mobile; user wants a horizontal swipeable carousel.

---

### 1. Create New File: `php-backend/public/ajax/enquiry-submit.php`

A public-facing endpoint (no admin auth required) that:
- Accepts POST with `parent_name`, `mobile`, `email`, `message`, `source`
- Validates required fields (name, mobile 10-digit)
- Inserts into `enquiries` table with `status='new'`
- Returns JSON success/error
- Includes basic rate-limiting (session-based, max 3 submissions per 10 minutes)
- No CSRF required since it's a public form (but has honeypot field for spam protection)

---

### 2. Modify: `php-backend/includes/public-footer.php`

**Fix form AJAX URL** (line 297):
- Change from: `fetch('/admin/ajax/enquiry-actions.php', ...)`
- Change to: `fetch('/public/ajax/enquiry-submit.php', ...)`

**Fix WhatsApp button mobile circle** (lines 215-224):
- Set explicit `width: 52px; height: 52px;` with `padding: 0;` and `display: flex; align-items: center; justify-content: center;` to ensure a perfect circle
- Center the icon inside

---

### 3. Modify: `php-backend/index.php` -- Feature Cards Mobile Fix

**Lines 523-531** (feature card mobile CSS):
- Remove extra padding/margin that causes the grey space above cards on mobile
- Ensure the horizontal scroll carousel starts flush with no leading gap
- Add `padding-left: 0` to the grid on mobile to eliminate the empty space shown in the screenshot

---

### 4. Modify: `php-backend/index.php` -- Core Team Mobile Slider

**Lines 700-756** (Core Team section):
- On mobile (below 576px), convert the grid into a horizontal swipe carousel similar to feature cards
- Add CSS: On mobile, the `.row` becomes a flex horizontal scroll container with `overflow-x: auto; scroll-snap-type: x mandatory; flex-wrap: nowrap;`
- Each card gets `min-width: 280px; scroll-snap-align: center;`
- Add dot indicators below the slider for visual pagination
- Add touch swipe support (native CSS scroll-snap handles this)
- On desktop/tablet, keep the existing centered grid layout unchanged

---

### Files Summary

| Action | File | Change |
|--------|------|--------|
| Create | `php-backend/public/ajax/enquiry-submit.php` | New public endpoint for enquiry form submissions |
| Modify | `php-backend/includes/public-footer.php` | Fix AJAX URL + WhatsApp circle sizing |
| Modify | `php-backend/index.php` | Feature cards mobile gap fix + Core Team mobile slider |

