

## Admin Dashboard UI Enhancement

### Overview
Upgrade the sidebar brand section and top bar with a modern, premium design while keeping everything dynamic from Admin Settings.

### Changes (Single File: `php-backend/includes/header.php`)

---

### 1. Sidebar Brand Section -- Center-Aligned Brand Card

**Current** (lines 960-975): Logo and text sit side-by-side with a collapse button.

**New Design:**
- Center the logo, school name, and tagline vertically stacked
- Logo displayed larger (56px) with a subtle glow ring using brand colors
- School Name in bold below logo
- School Tagline (loaded from `school_tagline` setting) in smaller muted text
- Wrapped in a subtle brand card with a faint gradient border
- When collapsed: show only the logo (centered, 38px), hide text gracefully

**Dynamic data:** Already loads `$schoolName` and `$schoolLogo` from settings. Will add `$schoolTagline = getSetting('school_tagline', 'Management System')` at line 3.

---

### 2. Top Bar -- Curved Highlight Card with Logo + Greeting

**Current** (lines 1124-1133): Plain greeting text with breadcrumb, no logo.

**New Design:**
- Wrap the greeting area in a pill-shaped container with:
  - Small logo (28px) on the left
  - School short name + greeting text
  - Rounded corners (50px radius), soft shadow
  - Subtle brand-colored gradient background (very light tint)
- Premium glassmorphism card feel
- Responsive: stacks nicely on mobile

---

### 3. CSS Enhancements

- **Sidebar brand card**: New `.sidebar-brand-card` class with centered layout, gradient border accent, smooth transitions
- **Collapsed state**: Logo-only mode with clean shrink animation
- **Top bar highlight pill**: `.topbar-highlight-pill` with rounded corners, soft shadow, brand tint background
- **Hover effects**: Enhanced nav-link hover with left-border accent slide-in animation
- **Sidebar expand/collapse**: Smoother CSS transitions (already using cubic-bezier, will refine timing)

---

### 4. No Backend Changes Needed

The settings already support:
- `school_name` -- loaded on line 3
- `school_tagline` -- already saved in General Settings form (line 91)
- `school_logo` -- loaded on line 4
- `school_short_name` -- already saved in settings
- `brand_primary/secondary/accent` -- loaded on lines 7-9

All values are already stored in the `settings` table and rendered as CSS variables. Changes reflect immediately on next page load.

---

### Technical Details

**File:** `php-backend/includes/header.php`

**PHP changes (top section, ~line 3):**
- Add `$schoolTagline = getSetting('school_tagline', 'Excellence in Education');`
- Add `$schoolShortName = getSetting('school_short_name', 'JNV');`

**CSS changes (~lines 156-420):**
- Replace `.sidebar-header` styles with new `.sidebar-brand-card` centered layout
- Add gradient border-bottom accent to brand card
- Add `.topbar-highlight-pill` styles (pill shape, soft shadow, brand tint)
- Enhance `.nav-link:hover` with sliding left-border accent animation
- Update collapsed state rules for the new centered brand card layout
- Add dark mode variants for all new styles

**HTML changes:**
- **Lines 960-975** (sidebar header): Restructure to vertically stack logo, name, tagline inside a centered brand card
- **Lines 1124-1133** (top bar greeting): Wrap in a pill-shaped highlight container with small logo

**No new files created. No new dependencies. Pure CSS + minimal PHP.**

