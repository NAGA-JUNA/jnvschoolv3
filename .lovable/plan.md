

## Premium Gallery Redesign: Public UI + Admin Category/Album Management

### Overview

Complete redesign of the Photo Gallery system with a premium dark-themed public page featuring a 3-panel layout (hero panel, category grid, featured image slider), plus a structured category/album management system in the admin backend. The existing `gallery_items` table is extended with foreign keys to new `gallery_categories` and `gallery_albums` tables.

---

### 1. Database Changes

**New table: `gallery_categories`**

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| id | INT UNSIGNED AUTO_INCREMENT | -- | Primary key |
| name | VARCHAR(100) NOT NULL | -- | Category name (Academic, Sports, etc.) |
| slug | VARCHAR(100) NOT NULL | -- | URL-friendly slug |
| cover_image | VARCHAR(255) | NULL | Cover image path |
| description | TEXT | NULL | Category description |
| sort_order | INT | 0 | Display order |
| status | ENUM('active','inactive') | 'active' | Visibility toggle |
| created_at | DATETIME | CURRENT_TIMESTAMP | -- |

**New table: `gallery_albums`**

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| id | INT UNSIGNED AUTO_INCREMENT | -- | Primary key |
| category_id | INT UNSIGNED NOT NULL | -- | FK to gallery_categories |
| title | VARCHAR(200) NOT NULL | -- | Album title |
| slug | VARCHAR(200) NOT NULL | -- | URL-friendly slug |
| cover_image | VARCHAR(255) | NULL | Album cover |
| description | TEXT | NULL | Album description |
| event_date | DATE | NULL | Event date |
| year | VARCHAR(10) | NULL | Year label |
| sort_order | INT | 0 | Display order |
| status | ENUM('active','inactive') | 'active' | Visibility toggle |
| created_at | DATETIME | CURRENT_TIMESTAMP | -- |

**Alter `gallery_items` table** -- add:

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| album_id | INT UNSIGNED | NULL | FK to gallery_albums (nullable for backwards compat) |
| caption | VARCHAR(500) | NULL | Image caption |
| position | INT | 0 | Sort order within album |

**New settings keys:**

- `gallery_layout_style` (default: 'premium') -- premium vs classic toggle
- `gallery_bg_style` (default: 'dark') -- dark/light theme
- `gallery_particles_show` (default: '1') -- particles background effect

**Demo category seed data** -- Insert 8 default categories: Academic, Cultural, Sports, Events, Infrastructure, Students, Teachers, Campus Life.

---

### 2. New File: `admin/ajax/gallery-actions.php`

AJAX endpoint for category and album management (admin-only, CSRF-protected):

**Category actions:**
- `list_categories` -- return all categories as JSON
- `save_category` -- create/update category (with cover image upload)
- `delete_category` -- delete category (cascade albums + unlink items)
- `reorder_categories` -- update sort_order for drag-and-drop

**Album actions:**
- `list_albums` -- return albums for a given category (or all)
- `save_album` -- create/update album (with cover image upload)
- `delete_album` -- delete album (unlink items from album)
- `reorder_albums` -- update sort_order
- `assign_images` -- assign existing gallery_items to an album
- `reorder_images` -- update position of images within album

---

### 3. Modify: `admin/page-content-manager.php`

Add new fields to the `gallery` config section:

```
['key' => 'gallery_layout_style', 'label' => 'Gallery Layout Style', 'type' => 'select', 'options' => ['premium' => 'Premium Dark', 'classic' => 'Classic Grid'], 'default' => 'premium'],
['key' => 'gallery_bg_style', 'label' => 'Background Style', 'type' => 'select', 'options' => ['dark' => 'Dark Gradient', 'light' => 'Light'], 'default' => 'dark'],
['key' => 'gallery_particles_show', 'label' => 'Show Particle Effects', 'type' => 'toggle', 'default' => '1'],
```

Add a collapsible **"Gallery Categories Manager"** panel (rendered only when `$activePage === 'gallery'`), following the same pattern as Teachers Grid Manager:

- Sortable list of categories with cover image thumbnails, name, active/inactive toggle, drag handle
- Add/Edit via modal (fields: name, cover image upload with preview, description, status)
- Delete with confirmation
- Under each category: expandable sub-list of albums with same CRUD controls
- All AJAX-driven with toast feedback

---

### 4. Redesign: `public/gallery.php`

Complete rewrite of the public gallery page with a premium 3-panel layout:

**Desktop Layout (3-column):**

- **Left Panel (col-lg-3)**: Dark gradient background (navy to midnight blue), title "Photo Gallery", subtitle text, subtle CSS particle/star animation, floating scroll arrow
- **Middle Panel (col-lg-4)**: Category grid with rounded square cards (2x4 grid), each card has a background thumbnail, dark overlay, white text label, hover glow effect, active category highlight with accent border
- **Right Panel (col-lg-5)**: Featured image slider for the selected category, large rounded image with smooth slide animation, arrow navigation, category title at top, optional thumbnail strip below

**Interactions:**
- Clicking a category filters the right-side slider with smooth CSS transitions
- Keyboard arrow navigation for slider
- Lightbox on image click (enhanced with prev/next navigation)
- Lazy loading with skeleton loaders (CSS shimmer animation)

**Mobile Layout:**
- Horizontal scrollable category strip at the top
- Full-width image carousel below
- Swipe gestures for navigation
- Bottom sheet for category details (optional)

**CSS Effects:**
- Soft glow on active category (`box-shadow` with primary color)
- Image depth shadow (`box-shadow: 0 20px 60px rgba(0,0,0,0.3)`)
- Micro-animations on hover (`transform: scale(1.05)` + `box-shadow` transition)
- Blur glass effect on panels (`backdrop-filter: blur(10px)`)
- Smooth slide transitions (`transition: transform 0.5s cubic-bezier(...)`)
- CSS-only star/particle background animation

**Data Flow:**
- Page loads all active categories from `gallery_categories`
- First active category auto-selected
- AJAX fetch images for selected category from `gallery_items` (filtered by category name for backward compat, or by album_id for new albums)
- Falls back to existing `gallery_items.category` field for images not yet assigned to albums

---

### 5. Modify: `admin/gallery.php`

Add category and album filter dropdowns to the existing gallery management page:
- New "Category" dropdown filter (from `gallery_categories`)
- New "Album" dropdown filter (from `gallery_albums`, filtered by selected category)
- "Assign to Album" bulk action for selected images
- No other changes to existing approval/delete/batch functionality

---

### 6. Modify: `admin/upload-gallery.php`

Add an "Album" dropdown to the upload form (populated via AJAX based on selected category), so new uploads can be directly assigned to an album. The `album_id` is saved along with the existing category field.

---

### 7. Files Summary

| File | Action | Purpose |
|------|--------|---------|
| `schema.sql` | Modify | Add `gallery_categories`, `gallery_albums` tables; alter `gallery_items`; add settings keys; seed demo categories |
| `admin/ajax/gallery-actions.php` | Create | AJAX CRUD for categories and albums |
| `admin/page-content-manager.php` | Modify | Add gallery settings fields + Categories Manager panel |
| `public/gallery.php` | Rewrite | Premium 3-panel dark-themed gallery UI |
| `admin/gallery.php` | Modify | Add category/album filter dropdowns + assign-to-album action |
| `admin/upload-gallery.php` | Modify | Add album dropdown to upload form |
| `teacher/upload-gallery.php` | Modify | Add album dropdown to upload form |

---

### 8. SQL Migration for Existing Database

```sql
-- New tables
CREATE TABLE `gallery_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gallery_albums` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATE DEFAULT NULL,
  `year` VARCHAR(10) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_category` (`category_id`),
  CONSTRAINT `fk_album_category` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alter gallery_items
ALTER TABLE `gallery_items`
  ADD COLUMN `album_id` INT UNSIGNED DEFAULT NULL AFTER `category`,
  ADD COLUMN `caption` VARCHAR(500) DEFAULT NULL AFTER `description`,
  ADD COLUMN `position` INT NOT NULL DEFAULT 0 AFTER `caption`,
  ADD KEY `idx_album` (`album_id`),
  ADD CONSTRAINT `fk_item_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums`(`id`) ON DELETE SET NULL;

-- Seed demo categories
INSERT INTO `gallery_categories` (`name`, `slug`, `sort_order`) VALUES
('Academic', 'academic', 1),
('Cultural', 'cultural', 2),
('Sports', 'sports', 3),
('Events', 'events', 4),
('Infrastructure', 'infrastructure', 5),
('Students', 'students', 6),
('Teachers', 'teachers', 7),
('Campus Life', 'campus-life', 8);

-- New settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('gallery_layout_style', 'premium'),
('gallery_bg_style', 'dark'),
('gallery_particles_show', '1');
```

---

### 9. What Does NOT Change

- Existing `gallery_items` data remains intact (new columns are nullable/defaulted)
- Existing admin gallery approval/delete workflow unchanged
- Existing teacher upload workflow unchanged (just adds optional album dropdown)
- All other public pages unaffected
- Existing upload-gallery pages remain functional

