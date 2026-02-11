

## Update README.md and schema.sql

### 1. README.md Updates

**Version bump**: v3.0 to v3.2 throughout the document.

**Feature Summary updates** to reflect all recent changes:
- Navbar redesign: logo-only brand (no school name text), hidden top bar on mobile, custom hamburger icon
- Footer redesign: dark gradient footer (#1a1a2e) with branded logo card, 4-column layout (Logo/Socials, Quick Links, Programs, Contact), "Become a Part of..." CTA section
- Core Team carousel on homepage with horizontal scroll, left/right arrows, sample data from `is_core_team=1` teachers
- Dynamic theme color via `--theme-primary` CSS variable controlled from admin settings `primary_color`
- Mobile responsiveness: hidden top bar on mobile, tap-to-flip teacher cards, swipe-to-close lightbox, touch-friendly tap targets
- Teachers page: Playfair Display serif headings, "Principal's Message" badge, reduced stat cards
- About page: content-managed History, Vision, Mission, Core Values sections

**Public Website feature list** rewritten to include:
- Two-tier navbar (top bar hidden on mobile) with logo-only brand
- Dynamic hero slider with animations
- Core Team horizontal carousel with arrow navigation
- Dark gradient footer with branded card and social links
- WhatsApp floating button
- Notification bell popup
- Dynamic color theming from admin settings

**Settings section** updated to mention:
- `primary_color` controls frontend theme
- `whatsapp_api_number` for WhatsApp button
- SMS/WhatsApp configuration section in admin

### 2. schema.sql Updates

Add missing default settings that are used in the codebase but not in the schema's INSERT:

```sql
-- Add to the settings INSERT block:
('whatsapp_api_number', ''),
('sms_gateway_key', ''),
```

Update the schema header comment version from v3.0 to v3.2.

Update the upgrade section at the bottom of README to include v3.1 to v3.2 migration notes (the new settings keys).

### Files to modify
- `php-backend/README.md` -- Full content update with new features, version bump, updated file tree and feature descriptions
- `php-backend/schema.sql` -- Add missing settings keys, update version in header comment
