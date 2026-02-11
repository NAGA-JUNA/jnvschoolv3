

## Plan: Sync Public Page Headers + Improve Ad Popup

### Problem
The four public pages (Notifications, Gallery, Events, Apply Now) still use the old single-row navbar, while the homepage has the updated two-tier navbar with top bar, logo, notification bell, and WhatsApp button. The ad popup also needs better sizing.

### Changes

#### 1. Update all 4 public pages with the two-tier navbar

Each of these files will get the same header structure as `index.php`:
- `public/notifications.php`
- `public/gallery.php`
- `public/events.php`
- `public/admission-form.php`

For each page, the changes include:
- Add PHP queries at the top for: school logo, tagline, WhatsApp number, notification count, bell notifications (5 latest)
- Replace the single `<nav>` with the **two-tier layout**: dark top bar (marquee + quick links) and main navbar (logo, menu links, red notification bell with badge, login button)
- Add the **Notification Modal** (Bootstrap modal with 5 latest notifications, close button, "View All" link)
- Add the **WhatsApp floating button** at the bottom
- Add matching CSS for top-bar, main-navbar, notification bell, WhatsApp button
- Add "Home" and "Our Teachers" links to the navigation menu
- Update footer to include the school logo

#### 2. Improve Ad Popup sizing

Update the ad popup CSS in `index.php` to be better centered and sized:
- Change `max-width` from `600px` to `550px` for a more compact, professional look
- Add `max-height: 80vh` with `object-fit: contain` on the image so it doesn't overflow on smaller screens
- Add a subtle entrance animation (fade + scale) for a polished feel

### Technical Details

**Files to modify:**

| File | Changes |
|------|---------|
| `public/notifications.php` | Replace old navbar (lines 83-105) with two-tier navbar, add notification modal, WhatsApp button, add PHP queries for logo/tagline/whatsapp/bell notifs |
| `public/gallery.php` | Replace old navbar (lines 40-54) with two-tier navbar, add notification modal, WhatsApp button, add PHP queries |
| `public/events.php` | Replace old navbar (lines 29-43) with two-tier navbar, add notification modal, WhatsApp button, add PHP queries |
| `public/admission-form.php` | Replace old navbar (lines 70-84) with two-tier navbar, add notification modal, WhatsApp button, add PHP queries |
| `index.php` | Tweak ad popup CSS for better sizing and animation |

**New navbar HTML structure (applied to all 4 pages):**

```text
+----------------------------------------------------------+
| Welcome marquee text          Admissions | Gallery | Events |  <-- top bar
+----------------------------------------------------------+
| [Logo] School   Home | Teachers | Notifs... | [Bell] Login |  <-- main nav
+----------------------------------------------------------+
```

**Ad popup improvements:**
- Max-width: 550px (from 600px)
- Max-height on image: 80vh with object-fit: contain
- Fade-in + scale animation on appearance
- Slightly rounded corners and shadow refinements

