

## Plan: Major UI Overhaul — Navbar, Footer, Core Team Carousel, and Mobile Fixes

This plan covers a comprehensive UI upgrade across all public pages based on the reference screenshots and the Aryan School design.

### Summary of Changes

1. **Navbar redesign** — Show only logo (no school name) in the brand area; cleaner layout
2. **Mobile improvements** — Hide top bar links on mobile, better hamburger menu with just logo + menu icon
3. **Footer upgrade** — Match header style with logo + school info in a branded card (purple background with border)
4. **"Our Core Team" carousel** on homepage with left/right navigation arrows
5. **Frontend color theme** controlled from admin settings
6. **Teachers page** styling improvements to match the reference design

---

### 1. Navbar Redesign (All Public Pages)

**Current**: Logo image + full school name text in navbar brand
**New**: Only logo image in navbar brand (no school name text); school name removed from brand area

Changes across all 7 public pages + `index.php`:
- Remove `<?= e($schoolName) ?>` from `.navbar-brand` — show only the logo `<img>`
- On mobile: the navbar brand shows just the logo, and the hamburger becomes a clean `<i class="bi bi-list">` icon
- The top bar (marquee + Admissions/Gallery/Events links) will be **completely hidden on mobile** (`d-none d-lg-block`)
- The sticky navbar remains with: Logo | hamburger toggle (mobile) or Logo | nav links | bell + login (desktop)

### 2. Mobile Top Bar & Menu Improvements

- **Hide entire top bar on mobile**: Add `d-none d-lg-block` class to `.top-bar`
- **Hamburger menu**: Replace default Bootstrap toggler icon with a cleaner custom `<i class="bi bi-list"></i>` icon, white color, larger size
- **Navbar brand on mobile**: Only logo (40px), no text truncation needed since text is removed
- **Slider adjustments**: Ensure hero slider fits properly on all mobile sizes without overflow

### 3. Footer Redesign (All Pages)

Based on the screenshot, the footer's first column should have the logo inside a **branded card** with purple/gradient background and a visible border:

- Column 1: A card with gradient purple background, school logo centered, school name below, and location text (e.g., "India") — matching the screenshot's red-bordered section
- Columns 2-4: Stay the same (About Us, Quick Links, Newsletter)
- The footer first column content (school name, address/tagline) is already from settings — no new settings needed

### 4. "Our Core Team" Horizontal Carousel (Homepage)

**Current**: The Core Team section shows cards in a standard grid (`row g-4`)
**New**: A horizontally scrollable carousel with left/right arrow buttons

Design (matching screenshot):
- Section heading: *"Our Core Team"* in italic/serif font, with subtitle *"Meet the dedicated leaders guiding our school's vision and mission."*
- Cards: Large photo on top (square/portrait), name below, designation in red/primary color, email with icon
- If more than 3 members, show left/right arrow buttons to scroll
- "View Our Teachers" button below the carousel
- Uses CSS `overflow-x: auto` with `scroll-snap` or JavaScript scroll for smooth left/right navigation
- Sample data for now (3 members: Correspondent, Director, Principal) — actual data comes from the existing `is_core_team=1` query

### 5. Frontend Color Theme from Settings

Add a new setting `primary_color` (already exists in settings form!) and use it as a CSS custom property:
- In `<style>`, define `:root { --primary: <?= e($primaryColor ?: '#1e40af') ?>; }`
- Replace hardcoded blue values (`#1e40af`, `#3b82f6`) with `var(--primary)` in key places (navbar, buttons, links, hero gradient accents)
- Admin can change the primary color from Settings page — it already has `primary_color` field

### 6. Teachers Page Improvements

Match the Aryan School reference:
- Hero section: Keep current gradient background, use a serif/italic heading font like the reference
- Stat cards: Show only 2 (Expert Teachers + Years Experience) like the reference instead of 4
- Principal's Message: Add a "Principal's Message" badge above the heading
- Teacher cards grid: Keep existing flip cards but improve spacing

---

### Technical Details

**Files to modify:**

| File | Changes |
|------|---------|
| `index.php` | Navbar brand (logo only), hide top bar mobile, Core Team carousel, footer col-1 card, CSS variables for theme color |
| `public/teachers.php` | Navbar brand (logo only), hide top bar mobile, hero heading font, reduce stat cards to 2, footer col-1 card |
| `public/notifications.php` | Navbar brand (logo only), hide top bar mobile, footer col-1 card |
| `public/gallery.php` | Navbar brand (logo only), hide top bar mobile, footer col-1 card |
| `public/events.php` | Navbar brand (logo only), hide top bar mobile, footer col-1 card |
| `public/admission-form.php` | Navbar brand (logo only), hide top bar mobile, footer col-1 card |
| `public/about.php` | Navbar brand (logo only), hide top bar mobile, footer col-1 card |
| `admin/settings.php` | No new fields needed (primary_color already exists) |

**Core Team Carousel HTML structure (index.php):**

```text
+------------------------------------------------------------------+
|         Our Core Team (italic heading)                           |
|  Meet the dedicated leaders guiding our school's vision...       |
|                                                                  |
|  [<]  [ Photo Card 1 ] [ Photo Card 2 ] [ Photo Card 3 ]  [>]  |
|        Name              Name              Name                  |
|        Designation        Designation        Designation          |
|        email              email              email               |
|                                                                  |
|              [ View Our Teachers button ]                        |
+------------------------------------------------------------------+
```

**Carousel JavaScript**: Smooth scroll left/right by card width on arrow click, with `scroll-behavior: smooth` and `scroll-snap-align: start`.

**Footer Column 1 redesign:**

```text
+----------------------------+
|  [Gradient purple card]    |
|     [Logo 60px]            |
|     School Name (bold)     |
|     India (subtitle)       |
+----------------------------+
```

Uses `background: linear-gradient(135deg, #6a11cb, #8b5cf6)` with `border: 2px solid rgba(255,255,255,0.2)` and `border-radius: 16px`.

**Theme color CSS variable approach:**

```php
<?php $primaryColor = getSetting('primary_color', '#1e40af'); ?>
<style>
:root { --theme-primary: <?= e($primaryColor) ?>; }
/* Use var(--theme-primary) in navbar accents, buttons, section-title underlines, etc. */
</style>
```

