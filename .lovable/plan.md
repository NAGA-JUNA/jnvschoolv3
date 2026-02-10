

# Complete Notifications Management System

## Overview
Rebuild the notifications system across all three panels (Admin, Teacher, Public) with a modern UI, rich features, and a new `notification_reads` table for read-tracking. This covers the admin approval workflow, teacher drafting, public popup/page views, and export capabilities.

---

## Schema Changes (Migration SQL)

Add new columns to the existing `notifications` table and create a new `notification_reads` table:

```sql
-- Add new columns to notifications
ALTER TABLE notifications
  ADD COLUMN priority ENUM('normal','important','urgent') NOT NULL DEFAULT 'normal' AFTER type,
  ADD COLUMN target_audience ENUM('all','students','teachers','parents','class','section') NOT NULL DEFAULT 'all' AFTER priority,
  ADD COLUMN target_class VARCHAR(20) DEFAULT NULL AFTER target_audience,
  ADD COLUMN target_section VARCHAR(10) DEFAULT NULL AFTER target_class,
  ADD COLUMN reject_reason TEXT DEFAULT NULL AFTER approved_at,
  ADD COLUMN schedule_at DATETIME DEFAULT NULL AFTER reject_reason,
  ADD COLUMN is_pinned TINYINT(1) NOT NULL DEFAULT 0 AFTER schedule_at,
  ADD COLUMN show_popup TINYINT(1) NOT NULL DEFAULT 0 AFTER is_pinned,
  ADD COLUMN show_banner TINYINT(1) NOT NULL DEFAULT 0 AFTER show_popup,
  ADD COLUMN show_marquee TINYINT(1) NOT NULL DEFAULT 0 AFTER show_banner,
  ADD COLUMN show_dashboard TINYINT(1) NOT NULL DEFAULT 0 AFTER show_marquee,
  ADD COLUMN view_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER show_dashboard,
  ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0 AFTER view_count,
  ADD COLUMN deleted_at DATETIME DEFAULT NULL AFTER is_deleted,
  ADD COLUMN deleted_by INT UNSIGNED DEFAULT NULL AFTER deleted_at;

-- Read tracking table
CREATE TABLE notification_reads (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  notification_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_read (notification_id, user_id),
  CONSTRAINT fk_nread_notif FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
  CONSTRAINT fk_nread_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Also update `schema.sql` to include these columns for fresh installs.

---

## 1. Admin Notifications Page (`admin/notifications.php`) -- Full Rewrite

### Header Section
- Page title "Notifications" with a badge counter showing pending count
- Action buttons: "Create New" (modal), "Export" dropdown (CSV)
- Search input for filtering by title

### Tab Navigation
- **Pending** (with count badge, yellow) | **Approved** (green) | **Rejected** (red) | **Pinned** (blue) | **All**
- Active tab highlighted with pill style

### Table Columns
- Checkbox (bulk select)
- Title (with pinned icon if pinned, truncated)
- Priority (color-coded pill: Normal=gray, Important=orange, Urgent=red)
- Type (badge: general, academic, exam, etc.)
- Posted By (name + role badge)
- Target Audience (icon + label)
- Visibility icons (popup, banner, marquee, dashboard -- filled if active)
- Status (pill: Pending=warning, Approved=success, Rejected=danger)
- View Count
- Posted Date
- Actions dropdown: View, Edit, Approve, Reject, Pin/Unpin, Delete

### Modal: View Notification
- Full content display with metadata (posted by, date, type, target, priority)
- Approval info if approved (who, when)
- Rejection reason if rejected

### Modal: Edit Notification
- Editable fields: Title, Content, Type, Priority, Target Audience (with conditional class/section selectors), Schedule date/time, Expiry date
- Visibility toggles: Website Popup, Notifications Page, Home Banner, Marquee, Dashboard Alert
- Save button

### Modal: Reject with Reason
- Textarea for rejection reason
- Submit button

### Modal: Create New Notification (Admin direct post)
- Same form as Edit but creates with status='approved' directly
- All fields available

### Bulk Actions
- Select all checkbox in header
- Bulk approve, bulk reject, bulk delete buttons

### POST Handler Actions
- `approve` -- set status=approved, approved_by, approved_at, is_public=1
- `reject` -- set status=rejected, store reject_reason
- `delete` -- soft delete (is_deleted=1, deleted_at, deleted_by)
- `pin` / `unpin` -- toggle is_pinned
- `edit` -- update all editable fields
- `create` -- insert new notification as approved
- `bulk_approve`, `bulk_reject`, `bulk_delete`
- `export` -- generate CSV download

### Pagination
- Use existing `paginate()` and `paginationHtml()` helpers, 20 per page

---

## 2. Teacher Post Notification (`teacher/post-notification.php`) -- Enhanced

### Left Column: Create Form (Enhanced)
- Title (required)
- Content (required, textarea)
- Type dropdown (General, Academic, Exam, Event, Holiday, Urgent)
- Priority (Normal, Important, Urgent) -- new radio buttons
- Target Audience (All, Students, Teachers, Parents, Class, Section) -- new dropdown
  - If "Class" selected: show class input
  - If "Section" selected: show class + section inputs
- Attachment upload (optional, max 5MB, PDF/DOC/IMG)
- "Show on public website" checkbox
- Submit for Approval button

### Right Column: My Submissions (Enhanced)
- Add priority column with color pills
- Add target audience column
- Add a "View" button to see full content + rejection reason if rejected
- Keep pagination

---

## 3. Public Notifications Page (`public/notifications.php`) -- Enhanced

### Hero Banner
- Keep existing gradient style
- Add unread badge counter (for logged-in users)

### Filters Bar
- Filter by Type (dropdown: All, General, Academic, Exam, etc.)
- Filter by Date range (From / To date inputs)
- Search by title

### Notification Cards
- Highlight unread notifications (left blue border + subtle background)
- Show priority badge for Important/Urgent
- Pinned notifications shown first with a pin icon
- "Mark as Read" button (for logged-in users)
- View count display
- Click to expand full content (or modal)
- Respect `expires_at` -- don't show expired notifications
- Respect `schedule_at` -- don't show before scheduled time

### Notification Popup (for website visitors)
- On page load, check for notifications with `show_popup=1` and `status='approved'`
- Show a Bootstrap modal with scrollable content
- "Open Full Page" button links to `/public/notifications.php`
- Close button
- Remember dismissed popups in localStorage to not show again

---

## 4. Export Feature

### CSV Export (in admin page)
- Export currently filtered notifications as CSV
- Columns: ID, Title, Type, Priority, Target, Posted By, Status, Created Date, Approved By, Approved Date
- Triggered via GET parameter `?action=export&status=...`

---

## Technical Details

### Files to Modify
1. **`php-backend/schema.sql`** -- Add new columns to notifications table, add notification_reads table
2. **`php-backend/admin/notifications.php`** -- Complete rewrite with all admin features
3. **`php-backend/teacher/post-notification.php`** -- Enhanced with priority, target audience, attachment
4. **`php-backend/public/notifications.php`** -- Enhanced with filters, read tracking, popup

### Files to Create
5. **`php-backend/admin/notification-export.php`** -- CSV export endpoint

### Key Patterns Followed
- Uses existing `requireAdmin()`, `requireTeacher()`, `verifyCsrf()`, `csrfField()` auth patterns
- Uses existing `paginate()` and `paginationHtml()` helpers
- Uses existing `auditLog()` for all actions
- Uses existing `e()` for output escaping
- Uses existing `setFlash()` / `getFlash()` for user feedback
- Bootstrap 5.3 modals, pills, badges consistent with existing UI
- Soft delete pattern (is_deleted flag) instead of hard delete
- All queries use PDO prepared statements

### Migration Note
The ALTER TABLE statements should be run on the production database before deploying the new code. They are backward-compatible (all new columns have defaults) so existing data won't break.

