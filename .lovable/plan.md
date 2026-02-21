

## Improve Admin Top Menu Bar

### Current State
The top bar is a flat white strip with minimal styling -- just a page title on the left, date/clock, theme toggle button, and avatar dropdown on the right. It feels plain compared to the premium sidebar design.

### Proposed Improvements

#### 1. Breadcrumb Navigation
- Add a subtle breadcrumb trail below the page title (e.g., "Dashboard" or "Content > Certificates")
- Gives context and makes navigation feel professional

#### 2. Search Bar
- Add a compact search input in the center of the top bar (icon + placeholder "Search...")
- Focuses on click/shortcut (Ctrl+K hint)
- Searches across admin pages (Students, Teachers, Settings, etc.) with instant suggestions dropdown

#### 3. Notification Bell with Badge
- Add a bell icon button next to the theme toggle
- Shows a red badge with pending notification + admission count
- Click opens a small dropdown with the 3 most recent notifications and a "View All" link
- Provides at-a-glance awareness without leaving the current page

#### 4. Visual Polish
- Add a subtle gradient or glassmorphism effect to the top bar (matching the sidebar's premium feel)
- Slightly increase height and padding for a more spacious, modern look
- Add a greeting message: "Good Morning, [Name]" replacing the plain page title style
- Rounded avatar with a subtle ring/border effect
- Smoother hover animations on all interactive elements

#### 5. Quick Action Buttons
- Add 1-2 small icon buttons for frequently used actions (e.g., "Add Student" shortcut, "Full Screen" toggle)
- Keeps power-user workflows fast

---

### Technical Details

**File modified:** `php-backend/includes/header.php`

**CSS changes (lines 436-511):**
- `.top-bar`: Add `backdrop-filter: blur(12px)`, semi-transparent background, increased padding to `1rem 1.75rem`, border-radius on bottom, subtle gradient border
- `.greeting-text`: New style for "Good Morning, Name" with lighter subtitle for the page title
- `.topbar-search`: Compact search input with icon, rounded pill shape, expandable on focus
- `.notification-bell`: Bell icon button with animated badge pulse
- `.notification-dropdown`: Small dropdown panel listing recent notifications
- Improved `.avatar-circle` with a gradient ring border
- Dark theme variants for all new elements

**HTML changes (lines 967-1017):**
- Replace plain `<h5>` page title with a greeting block + breadcrumb
- Add search input between the title and user-info sections
- Add notification bell button with badge before the theme toggle
- Add notification dropdown panel (fetches count from existing `$_notifCount` and `$_admissionCount` variables)

**JS additions (in footer.php or inline):**
- Greeting logic: Calculate "Good Morning/Afternoon/Evening" based on time of day
- Search: Simple page-name filter with keyboard shortcut listener
- Notification dropdown: Toggle on click, mark as read via AJAX (optional enhancement)

**No new files or database changes required** -- all data (notifications, user name, role) is already available in the header.
