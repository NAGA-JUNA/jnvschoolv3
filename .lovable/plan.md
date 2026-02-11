

## Plan: Favicon Upload, Inspirational Quote System, and About Page Enhancements

### 1. Favicon Upload (Settings > General Tab)

Add a favicon upload card next to the existing School Logo card in the General tab of `admin/settings.php`.

**Backend handler** (top of settings.php): New `form_action === 'favicon_upload'` block that:
- Accepts .ico, .png, .svg, .jpg files
- Saves to `uploads/logo/favicon.*`
- Stores filename in settings key `school_favicon`

**Frontend**: A new card in the `col-lg-4` area (or split the right column into two stacked cards: Logo + Favicon).

**All public pages** (index.php + 6 public pages): Add `<link rel="icon" href="/uploads/logo/<?= e($favicon) ?>" type="image/...">` in `<head>` dynamically from settings.

### 2. Inspirational Quote System

#### Database: New `site_quotes` table

```sql
CREATE TABLE `site_quotes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_text` TEXT NOT NULL,
  `author_name` VARCHAR(200) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_by` INT UNSIGNED DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_quote_user` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Insert a default quote:
```sql
INSERT INTO `site_quotes` (`quote_text`, `author_name`, `updated_by`) VALUES
('Education is the most powerful weapon which you can use to change the world.', 'Nelson Mandela', 1);
```

#### Admin Page: `admin/quote-highlight.php`

A new standalone admin page (not inside settings) with:
- Textarea for quote text (required, validated)
- Input for author name (optional)
- Live preview card showing how it will look on the About page
- Save/Update button
- Success/error flash messages
- Only accessible to Super Admin and Admin roles
- Audit log on save

Add a sidebar link under "Configuration" section: "Quote Highlight" with `bi-quote` icon.

#### About Page: Dynamic Quote Banner (`public/about.php`)

Insert a full-width highlighted quote section between "What Makes Us Special?" (Core Values) and the Footer CTA:

- Light grey background (`#f8f9fa`), premium card style with subtle left border accent
- Large quotation mark icon decoratively placed
- Center-aligned quote text in italic/serif font
- Author name below with a dash prefix
- "Last updated" small text below author
- Scroll animation using CSS `@keyframes fadeInUp` triggered by IntersectionObserver

### 3. Content Tab Enhancement (Settings > Content)

Add a "Core Values" section to the Content tab so admins can edit the 4 core values (Excellence, Integrity, Innovation, Community) that appear on the About page. Currently these are hardcoded.

New settings keys: `core_value_1_title`, `core_value_1_desc`, etc. (4 pairs).

The About page will read these dynamically instead of showing hardcoded text.

### 4. Sidebar Navigation Update

Add "Quote Highlight" link to the admin sidebar in `includes/header.php` under the Configuration section with icon `bi-quote`.

---

### Files to Create
| File | Purpose |
|------|---------|
| `admin/quote-highlight.php` | New admin page for managing the inspirational quote |

### Files to Modify
| File | Changes |
|------|---------|
| `admin/settings.php` | Add favicon upload handler + favicon upload card in General tab + core values fields in Content tab |
| `includes/header.php` | Add "Quote Highlight" sidebar link under Configuration |
| `public/about.php` | Dynamic quote banner section + dynamic core values + favicon link in head |
| `index.php` | Favicon link in head |
| `public/teachers.php` | Favicon link in head |
| `public/notifications.php` | Favicon link in head |
| `public/gallery.php` | Favicon link in head |
| `public/events.php` | Favicon link in head |
| `public/admission-form.php` | Favicon link in head |
| `schema.sql` | Add `site_quotes` table + default data + `school_favicon` setting + core value settings |

### Technical Details

**Quote banner HTML structure on About page:**
```text
+------------------------------------------------------------------+
|  (light grey bg, max-width card, centered)                       |
|                                                                  |
|          "  (large decorative quote icon)                        |
|                                                                  |
|   "Education is the most powerful weapon which you can           |
|    use to change the world."                                     |
|                                                                  |
|          -- Nelson Mandela                                       |
|     Last updated: 10 Feb 2026, 03:30 PM                         |
+------------------------------------------------------------------+
```

**Quote admin page layout:**
```text
+------------------------------------------------------------------+
|  Quote Highlight Manager                                         |
|                                                                  |
|  [Textarea: Quote Message *]                                     |
|  [Input: Author Name]                                            |
|                                                                  |
|  Preview:                                                        |
|  +------------------------------------------------------------+ |
|  |  (mini preview of the quote card as it will appear)         | |
|  +------------------------------------------------------------+ |
|                                                                  |
|  [Save Quote]                                                    |
+------------------------------------------------------------------+
```

**Favicon upload card (General tab):**
```text
+----------------------------+
|  Favicon                   |
|  [current favicon preview] |
|  [file input .ico/.png]    |
|  [Upload Favicon]          |
+----------------------------+
```

**IntersectionObserver for scroll animation:**
```javascript
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('animate-in'); });
}, { threshold: 0.2 });
document.querySelectorAll('.quote-banner').forEach(el => observer.observe(el));
```

