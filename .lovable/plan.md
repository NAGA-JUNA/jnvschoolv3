

## Plan: Multi-Feature Update — Navbar, Notifications Popup, Ad Popup, WhatsApp Button, Login Back Link

This plan covers 6 enhancements across the public website, homepage, and login page.

---

### 1. Enhanced Navbar (Two-Tier Style)

Matching the reference screenshot, the navbar will be updated to a **two-tier design**:
- **Top bar**: Dark background with welcome marquee text on the left, quick links (Admissions, Gallery) on the right
- **Main nav bar**: School logo + name on left, navigation links in center, a **red Notifications bell button** with unread count badge, and a **Login button** with arrow icon on the right

This navbar pattern will be applied to `index.php`, `public/teachers.php`, and all other public pages.

---

### 2. Notification Bell Popup

When users click the red **Notifications** button in the navbar:
- A **modal popup** appears showing the latest 5 approved notifications
- Each notification shows title, date, and type badge
- A **notification count badge** (red circle) on the bell icon shows unread/new count
- Popup has a **Close (X)** button and a **"View All Notifications"** button that links to `/public/notifications.php`
- Uses Bootstrap modal, no page reload needed

---

### 3. Login Page — "Back to Website" Link

Add a clearly visible **"Back to Website"** link/button on the login page:
- Positioned at the top of the right panel (form side)
- Links back to `/` (home page)
- Styled as a subtle link with a left-arrow icon for easy navigation

---

### 4. Advertisement Popup on Homepage

Admin can post **one advertisement image** that shows as a fullscreen-overlay popup when visitors first land on the homepage:
- **Popup design**: Centered modal with the ad image, a visible **Close (X)** button in the top-right corner
- Uses `localStorage` so the popup only shows once per session/day (won't annoy repeat visitors)
- **Admin management**: New section in `admin/settings.php` to upload 1 ad image and toggle it ON/OFF
- Database: 2 new settings keys: `popup_ad_image` and `popup_ad_active`

---

### 5. WhatsApp Floating Button

A **green WhatsApp floating icon** in the bottom-right corner of every public page:
- Clicking it opens `https://wa.me/<number>` in a new tab
- The WhatsApp number is pulled from the existing `whatsapp_api_number` setting (already in admin settings)
- Shows on homepage, teachers page, notifications, gallery, events pages
- Styled as a circular green button with the WhatsApp icon, subtle bounce animation

---

### 6. Teachers Page Refinements

Minor updates to match the reference screenshot more closely:
- Adjust card sizing and spacing to match the reference layout
- Ensure the "Meet Our Teachers" section heading style matches

---

### Technical Details

#### A. Database Changes (settings table only)

```sql
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('popup_ad_image', ''),
('popup_ad_active', '0');
```

No new tables needed. WhatsApp number already exists as `whatsapp_api_number`.

#### B. Files to Modify

| File | Changes |
|------|---------|
| `index.php` | New two-tier navbar, notification popup modal, ad popup modal, WhatsApp floating button |
| `public/teachers.php` | Updated navbar style, WhatsApp button |
| `login.php` | Add "Back to Website" link at top of form |
| `admin/settings.php` | Add "Popup Advertisement" section with image upload and ON/OFF toggle |
| `schema.sql` | Add popup_ad settings to INSERT |
| `README.md` | Document new features |

#### C. Navbar HTML Structure

```text
+----------------------------------------------------------+
| Welcome To JNV School          Admissions | Gallery | ... |  <-- top bar (dark)
+----------------------------------------------------------+
| [Logo] School Name   Home | Teachers | ... | [Bell] Login |  <-- main nav (white/dark)
+----------------------------------------------------------+
```

#### D. Notification Popup (Bootstrap Modal)

- Fetches latest 5 approved notifications via PHP query on page load
- Count badge shows number of notifications from last 7 days
- Modal triggered by clicking the red Notifications button
- Contains list of notifications with close and "View All" buttons

#### E. Ad Popup (Bootstrap Modal)

- Admin uploads image via settings page (stored in `uploads/ads/`)
- On homepage load, if `popup_ad_active = '1'` and image exists, shows modal
- `localStorage` key `popup_ad_dismissed_<date>` prevents showing again same day
- Large centered image with close button overlay

#### F. WhatsApp Button CSS

- Fixed position bottom-right (bottom: 20px, right: 20px)
- Green circular button (#25D366) with white WhatsApp icon
- z-index: 9999 to stay above everything
- Subtle pulse animation to draw attention

