

## Plan: About Us Page + Modern Gradient Footer

### What This Plan Does

1. **New "About Us" Page (`public/about.php`)** — A dedicated page with the two-tier navbar, featuring sections for School History, Vision, Mission, and core values. Uses the same design language as the existing teachers page.

2. **Modern Gradient Footer (All Public Pages)** — Replaces the current simple dark footer with a professional gradient footer (purple-to-teal, matching the reference screenshot) with 4 columns:
   - **Column 1**: School logo + address
   - **Column 2**: About Us — address, email, phone
   - **Column 3**: Services/Quick Links — Home, About, Teachers, Gallery, Events, Contact
   - **Column 4**: Newsletter email input + social media icons (Facebook, Twitter/X, Instagram, YouTube, LinkedIn)
   - **Bottom bar**: Copyright text

3. **Navigation Update** — Add "About Us" link to the navbar on all public pages.

---

### Technical Details

#### A. Database Changes

Add social media and about page settings:

```sql
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('about_history', ''),
('about_vision', ''),
('about_mission', ''),
('social_facebook', ''),
('social_twitter', ''),
('social_instagram', ''),
('social_youtube', ''),
('social_linkedin', '');
```

Note: Some social settings may already exist — we use INSERT IGNORE.

#### B. Files to Create

| File | Purpose |
|------|---------|
| `public/about.php` | Public About Us page with history, vision, mission sections |

#### C. Files to Modify

| File | Changes |
|------|---------|
| `index.php` | Replace footer with gradient footer, add "About Us" nav link |
| `public/teachers.php` | Replace footer with gradient footer, add "About Us" nav link |
| `public/notifications.php` | Replace footer with gradient footer, add "About Us" nav link |
| `public/gallery.php` | Replace footer with gradient footer, add "About Us" nav link |
| `public/events.php` | Replace footer with gradient footer, add "About Us" nav link |
| `public/admission-form.php` | Replace footer with gradient footer, add "About Us" nav link |
| `admin/settings.php` | Add fields for About page content (history, vision, mission) and social links if not already present |
| `schema.sql` | Add new setting keys |

#### D. About Us Page Design

- **Hero Section**: Gradient background (same as teachers hero), "About Us" heading with badge
- **History Section**: Card with icon, school founding story and milestones
- **Vision Section**: Card with eye icon, the school's vision statement
- **Mission Section**: Card with target icon, the school's mission statement
- **Values Grid**: 4 cards showing core values (Excellence, Integrity, Innovation, Community)
- All content pulled dynamically from settings so admin can update via backend

#### E. Gradient Footer Design (matching reference screenshot)

```text
+-------------------------------------------------------------------+
|  [gradient: purple (#6a11cb) -> teal (#1e8a7a)]                   |
|                                                                   |
|  [Logo]              ABOUT US           SERVICES     NEWSLETTER   |
|  School Name         Address            Home         [Email input] |
|  Address line 1      Email              About        [->]         |
|  City, State         Phone              Teachers                  |
|                                         Gallery      [Social Icons]|
|                                         Events       f x ig yt in |
|                                         Contact                   |
+-------------------------------------------------------------------+
|          (c) 2026 School Name. All Rights Reserved.               |
+-------------------------------------------------------------------+
```

- CSS: `background: linear-gradient(135deg, #6a11cb 0%, #1e8a7a 100%);`
- White text, rounded top corners on the footer container
- Social icons as circular white-bordered buttons
- Newsletter input with arrow submit button (decorative, no backend needed initially)

#### F. Footer CSS (shared across all pages)

The footer CSS will be added inline in each page's `<style>` block (following existing pattern). Key styles:
- `.site-footer`: gradient background, white text, padding, rounded-top corners
- `.footer-heading`: uppercase, underlined headings
- `.footer-social a`: circular bordered icons
- `.footer-newsletter input`: transparent-bordered input with submit button
- `.footer-bottom`: border-top separator with copyright

#### G. Admin Settings Updates

Add a new "About Page Content" section in `admin/settings.php` with:
- School History textarea
- Vision Statement textarea  
- Mission Statement textarea
- Social media URL fields (if not already present)

