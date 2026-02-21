

## Upgrade Home Page Feature Cards — Premium UI + Admin Control

### Current State
The "Quick Links" section (lines 340-380 in `index.php`) displays 4 static Bootstrap cards (Admissions, Notifications, Gallery, Events) with basic icons, text from the `settings` table, and simple outline buttons. No dynamic stats, no badges, no glassmorphism, no mobile carousel.

### What Changes

#### Phase 1: New Database Table
Create a `feature_cards` table to store card configuration instead of relying solely on key-value settings:

```text
feature_cards
-------------------------------------------------
id              INT AUTO_INCREMENT
slug            VARCHAR(50) UNIQUE  -- admissions, notifications, gallery, events
title           VARCHAR(100)
description     VARCHAR(500)
icon_class      VARCHAR(100)       -- Bootstrap Icons class
accent_color    VARCHAR(20)        -- hex color override (or 'auto' for logo-based)
btn_text        VARCHAR(50)
btn_link        VARCHAR(255)
badge_text      VARCHAR(50)        -- "New", "3 Updates", "Live", etc.
badge_color     VARCHAR(20)        -- badge bg color
is_visible      TINYINT(1) DEFAULT 1
is_featured     TINYINT(1) DEFAULT 0  -- glow border highlight
show_stats      TINYINT(1) DEFAULT 1
sort_order      INT DEFAULT 0
created_at      DATETIME
updated_at      DATETIME
```

Default rows inserted for the 4 existing cards with current settings migrated.

#### Phase 2: Frontend — Glassmorphism Cards (`index.php`)

**CSS additions (~120 lines):**
- `.fcard` -- glassmorphism container: `background: rgba(255,255,255,0.6)`, `backdrop-filter: blur(16px)`, `border: 1px solid rgba(255,255,255,0.3)`, `border-radius: 1rem`, subtle gradient border via pseudo-element
- `.fcard:hover` -- lift animation: `transform: translateY(-8px)`, enhanced shadow, border glow
- `.fcard.featured` -- animated glow border using `box-shadow` pulse keyframe
- `.fcard-icon` -- 64px circle with accent-colored gradient background, icon bounce animation on card hover
- `.fcard-badge` -- positioned top-right, small pill with pulse animation for "New"/"Live"
- `.fcard-stats` -- mini stat row at bottom with muted text and live count
- Responsive: 4-col grid on desktop, horizontal swipeable carousel on mobile (`overflow-x: auto`, `scroll-snap-type`)
- Click ripple effect via CSS `::after` pseudo-element with `@keyframes ripple`
- Skeleton loader placeholder class `.fcard-skeleton` with shimmer animation
- Dark theme variants using CSS custom properties

**HTML changes (replace lines 340-380):**
- PHP queries to fetch live stats:
  - Notifications: `SELECT COUNT(*) FROM notifications WHERE status='approved' AND is_public=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)` (already have `$notifCount`)
  - Gallery: `SELECT COUNT(*) FROM gallery_items WHERE status='approved'`
  - Events: `SELECT title, event_date FROM events WHERE is_public=1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 1`
  - Admissions: Use existing `$admissionOpen` setting
- Loop through `feature_cards` table ordered by `sort_order`
- Each card renders: badge (if set), animated icon, title, description, mini-stat line, CTA button
- Accent colors applied via inline CSS variable `--card-accent`
- Mobile wrapper uses `d-flex overflow-auto` with `scroll-snap-align: start` on each card

**JS additions (~40 lines):**
- Click ripple effect handler
- Touch swipe enhancement for mobile carousel
- Intersection Observer for fade-in-on-scroll animation
- ARIA labels on all interactive elements

#### Phase 3: Admin Panel — Feature Cards Manager

**New file: `php-backend/admin/feature-cards.php`**

A dedicated admin page (similar in style to existing managers) with:
- Card list showing all 4+ cards in a sortable grid
- Each card shows: icon preview, title, badge, visibility toggle, featured toggle
- Drag-and-drop reordering (using HTML5 drag API, same pattern as footer manager)
- Click to edit opens an inline form or modal with fields:
  - Title, Description, Icon class (with icon picker dropdown showing common Bootstrap Icons)
  - Accent color picker (with "Auto from Logo" option)
  - Button text + link
  - Badge text + badge color
  - Visibility toggle, Featured toggle, Show Stats toggle
- Save via POST form submission (same pattern as page-content-manager)
- Admission status toggle (updates `admission_open` setting)

**New AJAX file: `php-backend/admin/ajax/feature-card-actions.php`**
- `reorder` action: Update `sort_order` for drag-and-drop
- `toggle_visibility`: Quick toggle `is_visible`
- `toggle_featured`: Quick toggle `is_featured`

**Sidebar integration:** Add "Feature Cards" link under existing Content menu group in `header.php`

#### Phase 4: Click Analytics (Lightweight)

- Add `click_count` column to `feature_cards` table
- On the public page, each card link includes `onclick="trackCardClick('slug')"` calling a tiny AJAX endpoint
- New AJAX endpoint `php-backend/admin/ajax/feature-card-actions.php?action=track_click` increments the counter
- Admin panel shows click count per card as a small stat

---

### Files Modified / Created

| File | Action | Description |
|------|--------|-------------|
| `php-backend/schema.sql` | Modified | Add `feature_cards` table + default data |
| `php-backend/index.php` | Modified | Replace Quick Links section with premium glassmorphism cards |
| `php-backend/admin/feature-cards.php` | **New** | Admin CRUD interface for managing feature cards |
| `php-backend/admin/ajax/feature-card-actions.php` | **New** | AJAX handlers for reorder, toggle, click tracking |
| `php-backend/includes/header.php` | Modified | Add sidebar link for Feature Cards manager |
| `php-backend/admin/page-content-manager.php` | Modified | Keep existing Quick Link fields as fallback defaults |

### Database Changes
- **New table:** `feature_cards` (with 4 default rows)
- **New column concept:** `click_count` in `feature_cards`
- No existing tables altered

### Accessibility
- All cards get `role="article"` and `aria-label` with card title
- Buttons get descriptive `aria-label` attributes
- Keyboard navigation supported (tab through cards)
- Focus-visible outlines on all interactive elements

### Dark/Light Theme
- Cards use CSS custom properties that adapt to the existing site theme
- Glass effect adjusts opacity for dark backgrounds
- Badge colors maintain WCAG contrast in both modes

### No Breaking Changes
- Existing `home_quicklinks_show` toggle still controls section visibility
- Existing `home_cta_*` settings used as defaults when `feature_cards` table is empty
- Falls back gracefully if the new table doesn't exist yet

