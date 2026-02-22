

## School Location / Map Section

### Overview

Add an embedded Google Map section between the Contact section and Footer on the homepage, with a dedicated admin page to manage location settings. Uses the existing `settings` table (key-value pairs) -- no new database table needed, keeping it consistent with how school name, address, phone, etc. are already stored.

---

### Settings Keys (stored in existing `settings` table)

| Key | Default | Purpose |
|-----|---------|---------|
| `school_map_enabled` | `0` | Show/hide map section |
| `school_map_embed_url` | (empty) | Google Maps iframe embed URL |
| `school_latitude` | (empty) | Latitude for "Get Directions" link |
| `school_longitude` | (empty) | Longitude for "Get Directions" link |
| `school_landmark` | (empty) | Nearby landmark text |

School name, address, and phone are already stored as `school_name`, `school_address`, `school_phone`.

---

### New Files

#### 1. `php-backend/admin/school-location.php` -- Admin Page

A dedicated admin page with:

- **Card-based layout** matching existing admin pages
- **Fields**: Google Maps Embed URL (textarea), Latitude, Longitude, Landmark
- **Enable/Disable toggle** (form-switch)
- **Live map preview** -- renders the embed URL in an iframe instantly on paste (vanilla JS)
- **Helpful tip** for how to get Google Maps embed URL (right-click map -> Share -> Embed)
- **Save button** with CSRF protection, success/error alerts
- All values saved via existing `settings` table using `setSetting()` or direct upsert

---

### Modified Files

#### 1. `php-backend/index.php` -- Map Section on Homepage

Insert a new section between the Contact section (ends at line 854) and the footer include (line 856):

- Conditionally rendered when `school_map_enabled` is `1`
- **Left column**: Google Maps iframe embed (responsive, rounded corners, shadow)
- **Right column**: Card with school name, address, landmark, phone, and a "Get Directions" button that opens `https://www.google.com/maps/dir/?api=1&destination={lat},{lng}` in a new tab
- Fully responsive: stacks vertically on mobile, map takes full width
- Clean card design with subtle shadow and rounded corners

#### 2. `php-backend/includes/header.php` -- Sidebar Link

Add "School Location" menu item in the Settings nav group (after "Quote Highlight", before "Popup Ad") with `bi-geo-alt` icon.

---

### Frontend Map Section Design

```text
+------------------------------------------------------+
|  [Google Maps Embed iframe]  |  School Name (bold)    |
|  (responsive, rounded,      |  Address               |
|   16:9 aspect ratio)        |  Landmark              |
|                              |  Phone                 |
|                              |  [Get Directions btn]  |
+------------------------------------------------------+
```

- Background: light gradient (`#f8fafc` to `#f0f4ff`)
- Map iframe: `border-radius: 16px`, `box-shadow`, `100%` width
- Info card: `border-0`, `rounded-3`, `shadow-sm`
- "Get Directions" button: primary color, rounded-pill, opens Google Maps
- Mobile: map stacks on top, info card below, both full width

---

### Technical Summary

| Item | Detail |
|------|--------|
| Files created | `php-backend/admin/school-location.php` |
| Files modified | `php-backend/index.php`, `php-backend/includes/header.php` |
| Database | No new tables -- uses existing `settings` table (5 new keys) |
| No JS frameworks | Vanilla JS for live map preview only |
| CSRF protected | Admin form uses existing CSRF pattern |
| Mobile responsive | Stacked layout on small screens |

