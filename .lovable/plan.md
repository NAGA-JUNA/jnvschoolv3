

## Advanced Admin Notifications Management Module

### Overview
Complete rebuild of the notifications management page with advanced features including enhanced filtering, version history, multi-attachments, WhatsApp sharing, engagement analytics, expanded status lifecycle, and a modern SaaS-grade UI with sticky filters, floating toolbar, and slide-out preview drawer.

### Current State
The existing `notifications.php` already has: basic CRUD, approve/reject/delete, bulk actions, CSV export, pin/unpin, status tabs, search, visibility toggles, scheduling/expiry, view count, single attachment, and audit logging.

### What Gets Added

| Feature | Description |
|---------|-------------|
| Advanced Filter Bar | Sticky collapsible panel with date range, type, priority, target, visibility, posted-by filters |
| Expanded Status Lifecycle | Draft, Pending, Approved, Published, Expired (auto-expire via PHP check) |
| Version History | New `notification_versions` table; every edit saves a snapshot; restore button |
| Categories and Tags | New `category` and `tags` columns on `notifications`; filterable chip UI |
| Multi-Attachment | New `notification_attachments` table; multi-upload, image compression, PDF inline preview |
| WhatsApp Sharing Panel | Slide-out panel with formatted template, copy-to-clipboard, deep-link to WhatsApp API |
| Engagement Analytics | Per-notification analytics panel: total views, unique readers (students/teachers), first-seen timestamp from `notification_reads` |
| Preview Drawer | Right-side off-canvas drawer showing notification as Student UI and Teacher UI would see it |
| Floating Bulk Toolbar | Fixed-bottom toolbar appearing on row selection with approve, reject, delete, pin, export CSV, export PDF, copy WhatsApp |
| Column Visibility Toggle | Dropdown to show/hide table columns, saved to `localStorage` |
| Saved Filter Views | Save/load named filter combinations to `localStorage` |
| PDF Export | Client-side PDF generation using `jsPDF + autoTable` CDN |
| Role-Based Permissions | Super Admin: full access; Admin: all except settings; Teacher: create (draft/pending only) |

### Database Schema Changes

**New table: `notification_versions`**
```sql
CREATE TABLE notification_versions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_id INT UNSIGNED NOT NULL,
    title VARCHAR(200),
    content TEXT,
    type VARCHAR(20),
    priority VARCHAR(20),
    target_audience VARCHAR(20),
    category VARCHAR(50),
    tags VARCHAR(500),
    changed_by INT UNSIGNED,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

**New table: `notification_attachments`**
```sql
CREATE TABLE notification_attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_id INT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT UNSIGNED DEFAULT 0,
    uploaded_by INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);
```

**ALTER notifications table:**
```sql
ALTER TABLE notifications
    ADD COLUMN category VARCHAR(50) DEFAULT 'general' AFTER priority,
    ADD COLUMN tags VARCHAR(500) DEFAULT NULL AFTER category,
    MODIFY COLUMN status ENUM('draft','pending','approved','published','expired','rejected') DEFAULT 'pending';
```

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/schema.sql` | Add `notification_versions` and `notification_attachments` tables; alter `notifications` to add `category`, `tags`, expanded `status` enum |
| `php-backend/admin/notifications.php` | Complete rewrite: advanced filter bar, expanded status tabs (Draft/Pending/Approved/Published/Expired/Rejected), version history on edit, multi-attachment upload, WhatsApp panel, preview drawer, floating bulk toolbar, column toggle, saved filters, PDF export, engagement analytics modal, role-based action visibility |
| `php-backend/admin/ajax/notification-actions.php` | New AJAX endpoint for: fetch version history, restore version, delete attachment, fetch engagement analytics, upload attachments |
| `php-backend/teacher/post-notification.php` | Update to support draft status, categories, tags, multi-attachment |

### Implementation Order

1. **Schema**: Add new tables and alter `notifications` columns
2. **AJAX endpoint**: Create `notification-actions.php` for version history, attachments, analytics
3. **Main page rewrite** (`notifications.php`):
   - PHP backend: advanced filter query builder, version save on edit, attachment handling, auto-expire check, engagement data fetch
   - HTML: sticky filter bar, expanded tabs, table with column toggle, floating bulk toolbar, off-canvas preview drawer, WhatsApp panel, version history modal, analytics modal, multi-upload in create/edit modals
   - JavaScript: column visibility toggle with localStorage, saved filter views, PDF export via jsPDF CDN, WhatsApp template copy, bulk action toolbar show/hide, preview drawer population, version history restore
4. **Teacher page update**: Draft support, categories, tags, multi-attachment
5. **Test end-to-end**

### UI Layout (Top to Bottom)

```text
+----------------------------------------------------------+
| Page Title: Notifications     [+ Create] [Export CSV/PDF] |
+----------------------------------------------------------+
| [Advanced Filters]  (collapsible sticky bar)              |
|  Date Range | Type | Priority | Category | Target |      |
|  Posted By | Visibility | [Apply] [Reset] [Save View]    |
+----------------------------------------------------------+
| Tabs: Draft(0) Pending(0) Approved(0) Published(0)       |
|       Expired(0) Rejected(0) Pinned(0) All(0)            |
+----------------------------------------------------------+
| [Column Toggle Dropdown]                                  |
+----------------------------------------------------------+
| Table: [] Title | Priority | Type | Category | Tags |    |
|         Posted By | Target | Visibility | Status |       |
|         Views | Date | Actions(3-dot menu)                |
+----------------------------------------------------------+
| Pagination                                                |
+----------------------------------------------------------+
| [Floating Bulk Bar - appears on selection]                |
| (x selected) [Approve] [Reject] [Delete] [Pin]           |
|              [Export CSV] [Export PDF] [WhatsApp]          |
+----------------------------------------------------------+

Off-canvas right drawer: Preview (Student view / Teacher view tabs)
Modal: Version History (table of past versions + restore buttons)
Modal: Engagement Analytics (views chart, reader breakdown)
Modal: WhatsApp Share (formatted text + copy button)
```

### Technical Details

**Auto-Expire Logic**: At the top of the PHP page, run:
```sql
UPDATE notifications SET status='expired'
WHERE status IN ('approved','published')
AND expires_at IS NOT NULL AND expires_at < CURDATE()
```

**Version History**: Before every edit, INSERT the current state into `notification_versions`.

**PDF Export**: Load jsPDF + autoTable from CDN. Generate table from current filtered data client-side.

**WhatsApp Template**: Format notification as plain text with title, content, date, and generate `https://wa.me/?text=...` link.

**Column Toggle**: Store visible columns array in `localStorage('notif_columns')`. On page load, hide columns based on saved preference.

**Saved Filter Views**: Store named filter objects in `localStorage('notif_saved_filters')`. Dropdown to load/delete saved views.

**Engagement Analytics**: Query `notification_reads` joined with `users` to get breakdown by role, with timestamps.

**Multi-Attachment Upload**: Use `enctype="multipart/form-data"` with `<input type="file" multiple>`. Process each file server-side, store in `uploads/documents/`, insert rows into `notification_attachments`.

**Role Permissions**:
- Super Admin: all actions
- Admin: all except system settings
- Teacher: can only create (as draft/pending), view own submissions

