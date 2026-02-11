

## Add "Meet Our Leadership" Section to About Us Page

### Overview

Add a new "Meet Our Leadership" section to the public About Us page with a full admin CMS panel, following the exact same AJAX + inline management pattern used for the Teachers page.

---

### 1. Database Changes

**New table: `leadership_profiles`**

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | -- | Primary key |
| `name` | VARCHAR(100) NOT NULL | -- | Full name |
| `designation` | VARCHAR(100) | -- | e.g., Correspondent, Director, Principal |
| `photo` | VARCHAR(255) | NULL | Image path in uploads/photos/ |
| `bio` | TEXT | NULL | Short bio (optional) |
| `display_order` | INT | 0 | Drag-and-drop sort order |
| `status` | ENUM('active','inactive') | 'active' | Show/hide toggle |
| `created_at` | DATETIME | CURRENT_TIMESTAMP | -- |
| `updated_at` | DATETIME | CURRENT_TIMESTAMP ON UPDATE | -- |

**New settings keys** (added to the `settings` INSERT):

- `about_leadership_show` (default: '1') -- toggle visibility
- `about_leadership_title` (default: 'Meet Our Leadership')
- `about_leadership_subtitle` (default: 'With dedication and passion, our team creates an environment where every student thrives.')

---

### 2. New File: `admin/ajax/leadership-actions.php`

An AJAX endpoint mirroring `teacher-actions.php`, with these actions:

- `list_leaders` -- return all leaders as JSON (with optional search filter)
- `save_leader` -- add or update a leader (with photo upload)
- `delete_leader` -- delete a leader by ID
- `reorder` -- update `display_order` for a list of leader IDs
- `toggle_status` -- toggle active/inactive for a leader

All actions require admin session + CSRF validation.

---

### 3. Modify: `admin/page-content-manager.php`

**Add 3 new fields to the `about` config** (after `about_core_values_show`):

```
['key' => 'about_leadership_show', 'label' => 'Show Leadership Section', 'type' => 'toggle', 'default' => '1'],
['key' => 'about_leadership_title', 'label' => 'Leadership Section Title', 'type' => 'text', 'default' => 'Meet Our Leadership'],
['key' => 'about_leadership_subtitle', 'label' => 'Leadership Subtitle / Quote', 'type' => 'textarea', 'default' => 'With dedication and passion, our team creates an environment where every student thrives.'],
```

**Add a collapsible "Leadership Profiles Manager" panel** (rendered only when `$activePage === 'about'`), inserted after the existing fields -- same pattern as the Teachers Grid Manager:

- Search bar
- Sortable list with drag handles, photo thumbnails, name, designation, active/inactive toggle
- Add / Edit via modal (fields: photo upload with preview, name, designation, bio, status)
- Delete with confirmation
- Drag-and-drop reordering via HTML5 drag events
- All AJAX-driven with toast feedback

---

### 4. Modify: `public/about.php`

**Add a new section** between Core Values and Inspirational Quote (around line 345):

- Fetch leadership settings: `about_leadership_show`, `about_leadership_title`, `about_leadership_subtitle`
- Query: `SELECT * FROM leadership_profiles WHERE status='active' ORDER BY display_order ASC`
- Render only if `about_leadership_show === '1'` and leaders exist
- Layout matching the reference image:
  - Section background: `#f8f6f3` (warm off-white)
  - Centered title in bold serif font (Playfair Display)
  - Italic subtitle/quote text below
  - Responsive grid of circular profile cards (3 cols desktop, 2 tablet, 1 mobile)
  - Each card: circular photo (200px, soft pink/light border), name below in bold, designation in accent color (theme primary / red)
  - `loading="lazy"` on all images

**CSS additions** (within existing `<style>` block):

```css
.leadership-section { background: #f8f6f3; }
.leader-photo { width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(220,180,180,0.4); }
```

---

### 5. Modify: `schema.sql`

- Add `CREATE TABLE leadership_profiles` (table 15)
- Add the 3 new settings keys to the INSERT block
- Update the header comment to reflect 15 tables

---

### 6. Files Summary

| File | Action | Purpose |
|------|--------|---------|
| `schema.sql` | Modify | Add `leadership_profiles` table + 3 settings keys |
| `admin/ajax/leadership-actions.php` | Create | AJAX CRUD endpoint for leadership profiles |
| `admin/page-content-manager.php` | Modify | Add 3 about fields + collapsible leadership manager panel |
| `public/about.php` | Modify | Render "Meet Our Leadership" section |

---

### 7. SQL Migration for Existing Database

```sql
CREATE TABLE `leadership_profiles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('about_leadership_show', '1'),
('about_leadership_title', 'Meet Our Leadership'),
('about_leadership_subtitle', 'With dedication and passion, our team creates an environment where every student thrives.');
```

---

### 8. What Does NOT Change

- All existing About Us content (History, Vision, Mission, Core Values, Quote)
- All existing Page Content Manager fields and layout
- Other page tabs
- Teachers management system

