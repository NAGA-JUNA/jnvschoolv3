

## Popup Advertisement System — Complete Overhaul

### Overview

Replace the current basic popup ad (2 settings keys, no scheduling, no analytics) with a full-featured Popup Advertisement system including a dedicated admin page, date scheduling, redirect URLs, analytics tracking, and a polished frontend popup.

---

### Database Changes

**New table: `popup_ads`**

| Column | Type | Description |
|--------|------|-------------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| image_path | VARCHAR(255) | Path to uploaded image |
| is_enabled | TINYINT(1) DEFAULT 0 | Master on/off toggle |
| start_date | DATE | Schedule start |
| end_date | DATE | Schedule end |
| redirect_url | VARCHAR(500) DEFAULT NULL | Click destination URL |
| button_text | VARCHAR(100) DEFAULT NULL | CTA button label |
| show_on_home | TINYINT(1) DEFAULT 1 | Show only on homepage |
| show_once_per_day | TINYINT(1) DEFAULT 1 | Cookie-based daily limit |
| disable_on_mobile | TINYINT(1) DEFAULT 0 | Hide on mobile devices |
| created_at | DATETIME | Auto timestamp |
| updated_at | DATETIME | Auto timestamp on update |

**New table: `popup_analytics`**

| Column | Type | Description |
|--------|------|-------------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| popup_id | INT UNSIGNED | FK to popup_ads |
| view_date | DATE NOT NULL | Day of tracking |
| views_count | INT UNSIGNED DEFAULT 0 | Daily view count |
| clicks_count | INT UNSIGNED DEFAULT 0 | Daily click count |
| UNIQUE KEY | (popup_id, view_date) | One row per day per popup |

SQL will be provided in a standalone migration file and also added to `schema.sql`.

---

### New Files

#### 1. `php-backend/admin/popup-ad.php` — Dedicated Admin Page

A standalone admin page (not inside settings tabs) with:

- **Status badge** — Active (green) / Disabled (red) / Scheduled (amber) based on enable flag and date range
- **Image upload** with live preview (JS FileReader, no framework)
- **Enable/Disable toggle** (form-switch)
- **Schedule fields** — Start Date, End Date (HTML date inputs)
- **Redirect URL** — text input with https:// placeholder
- **Button Text** — text input (e.g., "Apply Now")
- **Display Rules card** — checkboxes for: Show on Home only, Show once per day, Disable on mobile
- **Analytics card** — Total Views, Total Clicks, CTR % displayed as stat badges; plus a simple daily stats table (last 14 days) showing date/views/clicks
- **Admin preview** — A framed mock popup showing how the popup will look to visitors
- CSRF-protected form, file upload via FileHandler utility

Styling matches existing admin card-based layout (border-0, rounded-3, same typography).

#### 2. `php-backend/admin/ajax/popup-analytics.php` — Analytics Endpoint

A lightweight PHP endpoint that accepts POST requests to increment view or click counts:
- `action=view` — Upserts `views_count` for today
- `action=click` — Upserts `clicks_count` for today
- Validates popup_id exists and is enabled
- Returns JSON response

#### 3. Migration SQL added to `schema.sql`

Add the two new tables after existing table definitions.

---

### Modified Files

#### 1. `php-backend/index.php` — Frontend Popup Overhaul

Replace the current basic popup (lines 242-261) with an enhanced version:

- Query `popup_ads` table instead of settings keys for popup data
- Check `is_enabled`, date range (`start_date <= CURDATE() AND end_date >= CURDATE()`), `show_on_home`
- **Popup UI**: Centered modal with rounded corners (16px), soft box-shadow, smooth fade+scale CSS animation (keyframes), close (X) button
- Clickable image linking to `redirect_url` (opens in new tab)
- Optional CTA button below image with `button_text`
- **Cookie logic**: If `show_once_per_day` is on, use localStorage with date-based key
- **Mobile check**: If `disable_on_mobile` is on, skip popup on screens <= 768px (JS `window.innerWidth` check)
- **Analytics**: Fire fetch POST to `/admin/ajax/popup-analytics.php?action=view` when popup shown, `action=click` when clicked
- Remove old `popup_ad_active` / `popup_ad_image` settings references from index.php

#### 2. `php-backend/includes/header.php` — Add Sidebar Menu Item

Add "Popup Ad" link in the sidebar under the SETTINGS section, with a megaphone icon (`bi-megaphone`), linking to `/admin/popup-ad.php`.

#### 3. `php-backend/admin/settings.php` — Remove Old Popup Tab

Remove the "Popup Ad" tab from settings tabs and the associated form/handler code (lines 190-201 for handler, and the HTML around lines 860-877). The popup feature is now managed on its own dedicated page.

---

### Technical Details

```text
Files Created:
  - php-backend/admin/popup-ad.php         (~250 lines)
  - php-backend/admin/ajax/popup-analytics.php (~40 lines)

Files Modified:
  - php-backend/schema.sql                 (add 2 tables)
  - php-backend/index.php                  (replace popup section)
  - php-backend/includes/header.php        (add sidebar link)
  - php-backend/admin/settings.php         (remove old popup tab)

No JS frameworks — vanilla JS only
FileHandler used for image uploads
CSRF protection on all forms
cPanel shared hosting compatible
```

---

### Migration SQL (for existing installations)

Users with an existing database need to run this before uploading new files:

```sql
CREATE TABLE IF NOT EXISTS `popup_ads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `redirect_url` VARCHAR(500) DEFAULT NULL,
  `button_text` VARCHAR(100) DEFAULT NULL,
  `show_on_home` TINYINT(1) NOT NULL DEFAULT 1,
  `show_once_per_day` TINYINT(1) NOT NULL DEFAULT 1,
  `disable_on_mobile` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed a default row
INSERT INTO `popup_ads` (`id`, `is_enabled`) VALUES (1, 0)
  ON DUPLICATE KEY UPDATE `id`=`id`;

CREATE TABLE IF NOT EXISTS `popup_analytics` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `popup_id` INT UNSIGNED NOT NULL,
  `view_date` DATE NOT NULL,
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `clicks_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_popup_date` (`popup_id`, `view_date`),
  CONSTRAINT `fk_analytics_popup` FOREIGN KEY (`popup_id`)
    REFERENCES `popup_ads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

