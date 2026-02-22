

## Admin Dashboard UI Enhancement

### What Changes

Update the **Top Header Highlight Pill** to display the School Name and Tagline alongside the greeting, matching the visual style of the Sidebar Brand Card. Add minor UI polish for consistency.

---

### 1. Top Header Highlight Pill Redesign

**Current**: Shows logo + greeting text + breadcrumb only.

**New design**: A rounded pill card containing:
- **Left side**: School Logo (28px, circular, white background)
- **Middle**: School Name (bold, 0.85rem) on first line, School Tagline (muted, 0.68rem) on second line
- **Right side (separated by subtle divider)**: Greeting ("Good Evening, Super") with wave emoji, breadcrumb below

This creates visual consistency with the Sidebar Brand Card while keeping the greeting.

**File**: `php-backend/includes/header.php`

**CSS changes** (around line 484-520):
- Widen `.topbar-highlight-pill` to accommodate school info
- Add `.topbar-pill-brand` section for school name/tagline
- Add a subtle vertical divider between brand info and greeting
- Responsive: on mobile (<768px), hide school name/tagline, show only logo + greeting

**HTML changes** (around line 1188-1200):
- After the logo image, add a `<div class="topbar-pill-brand">` with school name (bold) and tagline (muted)
- Add a `<div class="topbar-pill-divider">` vertical separator
- Keep existing greeting and breadcrumb

---

### 2. Sidebar Brand Card Polish

The sidebar brand card already matches the requested design (centered logo, bold name, muted tagline, rounded corners, shadow). Minor polish:

- Add a subtle `box-shadow` to the brand card area for more depth
- Ensure the gradient bottom border is slightly thicker for prominence

---

### 3. Sidebar Menu Hover Effects

The current hover effects (left accent bar + background tint) are already implemented. Add:

- Slight `translateX(2px)` on hover for a subtle slide effect on nav links
- Smooth icon color transition on hover

---

### Technical Summary

| Item | Detail |
|------|--------|
| **File modified** | `php-backend/includes/header.php` |
| **CSS additions** | ~30 lines for topbar pill brand section, divider, responsive rules |
| **HTML additions** | ~8 lines for school name/tagline in topbar pill |
| **No new files** | All changes in existing header |
| **No JS changes** | Pure CSS enhancements |
| **Mobile responsive** | Brand info hidden on small screens, greeting remains |

