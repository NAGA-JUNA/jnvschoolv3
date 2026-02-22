

## Fix HTTP 500 Error — Update Old Column References

### Root Cause
The homepage (`index.php`) and admin dashboard (`dashboard.php`) still reference the **old column name** `event_date` from the events table, which was renamed to `start_date` in the events system overhaul. This causes a fatal MySQL error and HTTP 500.

---

### Files to Fix

**1. `php-backend/index.php`** — 6 references to `event_date` on the `events` table:
- Line 34: `SELECT title, event_date, location FROM events` → change to `start_date`
- Line 353: `SELECT title, event_date FROM events WHERE ... event_date >= CURDATE() ORDER BY event_date` → change all to `start_date`
- Line 361: `strtotime($nextEvent['event_date'])` → `strtotime($nextEvent['start_date'])`
- Line 520: `strtotime($nextEvent['event_date'])` → `strtotime($nextEvent['start_date'])`
- Line 599: `strtotime($ev['event_date'])` → `strtotime($ev['start_date'])`
- Line 600: `strtotime($ev['event_date'])` → `strtotime($ev['start_date'])`

**2. `php-backend/admin/dashboard.php`** — 2 references:
- Line 14: `WHERE event_date >= CURDATE()` → `WHERE start_date >= CURDATE()`
- Line 27: `WHERE event_date >= CURDATE() ORDER BY event_date` → `WHERE start_date >= CURDATE() ORDER BY start_date`

### Not Affected
The `event_date` references in gallery files (`gallery.php`, `gallery-actions.php`, `upload-gallery.php`, `page-content-manager.php`) are for the `gallery_items` and `gallery_albums` tables, which have their own `event_date` column — these are **not** related to the events table and should NOT be changed.

---

### Technical Summary
- **2 files** modified: `index.php`, `dashboard.php`
- **8 total replacements**: `event_date` to `start_date` only where querying the `events` table
- No schema or structural changes needed

