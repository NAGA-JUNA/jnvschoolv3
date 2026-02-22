

## Premium Contact & Location Module with Admin Controls

### What Already Exists (No Changes Needed for Toggles)

Both homepage sections already have admin on/off controls:

- **Contact section**: `home_contact_show` toggle in Page Content Manager
- **Map section**: `school_map_enabled` toggle in School Location admin page

These toggles work correctly -- when off, the section is hidden from the homepage.

---

### What This Plan Adds

#### 1. Enquiry Form (replaces static CTA card)

**Replace** the right-side "Apply for Admission" card (index.php lines 839-850) with a compact enquiry form:

- Fields: Parent Name (required), Phone (required), Email (optional), Message (optional)
- "Send Enquiry" primary button + "WhatsApp" secondary button
- CSRF-protected POST submission, stores in new `enquiries` table
- Success message after submission
- Privacy note: "We respect your privacy. No spam."
- Inherits the existing `home_contact_show` toggle -- when contact section is off, the form is hidden too

#### 2. Enquiries Admin Page

**New file: `php-backend/admin/enquiries.php`**

- Table listing all enquiries: Name, Phone, Email, Message (truncated), Status badge, Date
- Status filter tabs: All / New / Contacted / Closed
- Search by name or phone
- Actions: Mark as Contacted, Mark as Closed, Delete
- CSV Export button
- Pagination

**New file: `php-backend/admin/ajax/enquiry-actions.php`**

- AJAX handler for status updates and deletions
- CSRF protected, JSON responses

#### 3. Upgraded Map Section

**Enhance** the existing map section (index.php lines 856-914):

- Hover shadow/lift effect on map card
- Add "Open in Google Maps" button (links to maps URL)
- Add "Copy Address" button (clipboard JS)
- Subtle CSS transitions on hover
- Inherits the existing `school_map_enabled` toggle -- no new toggle needed

#### 4. Sidebar Link

**Modify: `php-backend/includes/header.php`**

- Add "Enquiries" menu item with `bi-chat-dots` icon, linking to `/admin/enquiries.php`

#### 5. Database

**New table: `enquiries`**

| Column | Type | Description |
|--------|------|-------------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) NOT NULL | Parent name |
| phone | VARCHAR(20) NOT NULL | Phone number |
| email | VARCHAR(255) DEFAULT NULL | Optional email |
| message | TEXT DEFAULT NULL | Feedback/message |
| status | ENUM('new','contacted','closed') DEFAULT 'new' | Tracking status |
| created_at | DATETIME DEFAULT CURRENT_TIMESTAMP | Submission time |

Added to `schema.sql`. Migration SQL:

```sql
CREATE TABLE IF NOT EXISTS `enquiries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### Admin Toggle Summary

| Section | Toggle Setting Key | Managed From |
|---------|-------------------|--------------|
| Contact + Enquiry Form | `home_contact_show` | Page Content Manager |
| Map / Location | `school_map_enabled` | School Location page |

Both toggles already exist and work. No new toggle settings are needed.

---

### Files Summary

| Action | File |
|--------|------|
| Create | `php-backend/admin/enquiries.php` |
| Create | `php-backend/admin/ajax/enquiry-actions.php` |
| Modify | `php-backend/index.php` (enquiry form + map upgrade) |
| Modify | `php-backend/includes/header.php` (sidebar link) |
| Modify | `php-backend/schema.sql` (enquiries table) |

