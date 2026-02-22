

## Complete Events Management System Overhaul

### What Changes
Replace the basic single-file events system with a full-featured Events Management System including poster uploads, status management, featured events, pagination, public detail pages, filters, WhatsApp sharing, and view tracking.

---

### 1. Database Schema Update

**Drop and recreate the `events` table** with new columns:

| Column | Type | Purpose |
|--------|------|---------|
| id | INT AUTO_INCREMENT | Primary key |
| title | VARCHAR(255) | Event title |
| description | TEXT | Full description |
| start_date | DATE | Start date (replaces `event_date`) |
| end_date | DATE | End date |
| start_time | TIME | Start time (replaces `event_time`) |
| end_time | TIME | End time (new) |
| location | VARCHAR(255) | Venue |
| type | ENUM | sports, cultural, exam, holiday, activity, academic, meeting, other |
| status | ENUM | active, draft, cancelled, completed |
| is_public | TINYINT(1) | Visibility toggle |
| is_featured | TINYINT(1) | Featured flag (new) |
| poster | VARCHAR(255) | Poster image path (new) |
| views | INT | View counter (new) |
| created_by | INT UNSIGNED | Creator reference |
| created_at | TIMESTAMP | Auto timestamp |
| updated_at | TIMESTAMP | Auto update timestamp |

**Migration SQL** will be provided for existing installations to run before deploying.

---

### 2. Admin Panel — Rebuilt `admin/events.php`

Replace the current compact single-page layout with a professional full-featured admin page:

**Listing View (default):**
- Stats row: Total Events, Active, Upcoming, Featured (KPI cards)
- Action bar: "Add Event" button + search input + type filter dropdown + status filter
- Paginated table with columns:
  - Poster thumbnail (small)
  - Title
  - Date range (formatted nicely, multi-day shown as range)
  - Type (colored badge)
  - Status (colored badge: green=active, yellow=draft, red=cancelled, blue=completed)
  - Public toggle (inline switch)
  - Featured star (clickable)
  - Views count
  - Actions: Edit / Delete
- Pagination at bottom
- "Today / Tomorrow" badges on relevant events
- Confirm-before-delete modal

**Add/Edit Form (separate section or modal):**
- Accessed via `?action=add` or `?action=edit&id=X`
- Fields: Title, Description (textarea), Start Date, End Date, Start Time, End Time, Location, Type dropdown, Status dropdown, Poster upload (with preview), Public checkbox, Featured checkbox
- Client-side validation: end_date >= start_date
- CSRF protection
- Uses `FileHandler::uploadImage()` for poster (stored in `uploads/events/`)
- On edit, shows current poster with option to change/remove
- Cancel button returns to listing

**AJAX Toggles:**
- New `admin/ajax/event-actions.php` for:
  - Toggle public on/off
  - Toggle featured on/off
  - These use CSRF verification and return JSON

**Auto-complete past events:**
- When loading admin page, auto-UPDATE events where `start_date < CURDATE()` AND `status = 'active'` to `status = 'completed'`

---

### 3. Public Events Page — Rebuilt `public/events.php`

**Hero section** (uses existing Page Content Manager settings `events_hero_title`, etc.)

**Filter bar:**
- Search input (by title)
- Type filter dropdown
- Upcoming / Past toggle buttons

**Featured events section** (top, only if any featured upcoming events exist):
- Large card layout with poster image, title, date, location, type badge

**Upcoming Events:**
- Card-based layout (not table)
- Each card shows: poster image (if any), title, date range, time, location, type badge, short description (truncated)
- "Today" / "Tomorrow" badge on relevant cards
- WhatsApp share button on each card
- Click card to go to detail page

**Past Events:**
- Separate section below, slightly muted styling
- Same card layout but with reduced opacity
- Limited to 12 initially, "Load more" or pagination

---

### 4. Public Event Detail Page — New `public/event-view.php`

**New file** that shows a single event:
- Full poster image (if any)
- Title, full description
- Date range with day names
- Time range
- Location with icon
- Type badge
- WhatsApp share button
- View count incremented on load (via simple UPDATE query)
- "Back to Events" link
- Related events sidebar or bottom section (same type, upcoming)

---

### 5. Admin Sidebar Update

The events link in the sidebar already exists — no changes needed there.

---

### Technical Details

**Files created:**
- `php-backend/admin/ajax/event-actions.php` — AJAX handler for public/featured toggles
- `php-backend/public/event-view.php` — Public event detail page

**Files modified:**
- `php-backend/schema.sql` — Updated events table with new columns
- `php-backend/admin/events.php` — Complete rewrite with full admin UI
- `php-backend/public/events.php` — Complete rewrite with filters, cards, featured section

**Directories created:**
- `uploads/events/` — Created automatically by FileHandler on first poster upload

**Patterns followed:**
- `FileHandler::uploadImage()` for poster uploads (same as gallery/slider)
- `paginate()` + `paginationHtml()` from auth.php for pagination
- `verifyCsrf()` on all mutations
- `auditLog()` on create/update/delete
- `checkMaintenance()` on public page
- `e()` for all output escaping
- Bootstrap 5.3 + Bootstrap Icons for UI
- Dark mode compatible admin styles (inherits from header.php)
- Mobile responsive with existing breakpoints

**Security:**
- All inputs sanitized with prepared statements
- CSRF on all forms and AJAX calls
- File upload validation via FileHandler (JPG/PNG/WebP only, 5MB max)
- Role check via `requireAdmin()`
- Public pages filter by `is_public=1` AND `status IN ('active','completed')`

**Migration for existing data:**
Since the column names change (`event_date` to `start_date`, `event_time` to `start_time`), the schema.sql will include the new structure, and a migration SQL block will be documented for existing installations.

