

## Separate Core Team Module with Admin CRUD and Flip-Card Display

### Overview

Create an independent "Core Team" admin module (separate from Teachers), with its own database table, full CRUD (add/edit/delete), drag-and-drop reordering, visibility/featured toggles, and display order control. On the homepage, replace the current horizontal-scroll cards with a centered flip-card grid matching the "Meet Our Faculty" style.

---

### 1. New Database Table: `core_team`

Add table #28 to `schema.sql`:

| Column | Type | Purpose |
|--------|------|---------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) NOT NULL | Member name |
| designation | VARCHAR(100) | Role (Principal, Director, etc.) |
| qualification | VARCHAR(255) | Degrees |
| subject | VARCHAR(100) | Subject area |
| experience_years | INT DEFAULT 0 | Years of experience |
| email | VARCHAR(255) | Contact email |
| phone | VARCHAR(50) | Phone |
| photo | VARCHAR(500) | Photo path |
| bio | TEXT | Short bio |
| display_order | INT DEFAULT 0 | Controls 1st, 2nd, 3rd position |
| is_visible | TINYINT(1) DEFAULT 1 | Show/hide toggle |
| is_featured | TINYINT(1) DEFAULT 0 | Featured highlight |
| created_at | DATETIME DEFAULT CURRENT_TIMESTAMP | Created date |

---

### 2. New File: `php-backend/admin/core-team.php`

Full admin page (modeled after Feature Cards) with:

- **Add New Member** button opening a modal form (name, designation, qualification, subject, experience, email, phone, photo upload, bio, display order number, visibility, featured toggles)
- **Card grid** showing each member: photo thumbnail, name, designation, display order number, visibility/featured badges
- **Edit** button per card opening the same modal pre-filled
- **Delete** with confirmation
- **Drag-and-drop reorder** (same pattern as feature-cards.php)
- **Toggle visibility** (eye icon, AJAX)
- **Toggle featured** (star icon, AJAX)
- **Display Order field**: Admin can set explicit position (1, 2, 3, 4...) so they control who appears first, second, third, etc.

---

### 3. New File: `php-backend/admin/ajax/core-team-actions.php`

AJAX handler for:
- `reorder` -- update display_order from drag-and-drop
- `toggle_visibility` -- flip is_visible
- `toggle_featured` -- flip is_featured
- `delete` -- remove member (with photo cleanup)

---

### 4. Modified File: `php-backend/includes/header.php`

Add sidebar link right after "Feature Cards" (line ~1134):

```
<div class="nav-item">
    <a href="/admin/core-team.php" class="nav-link" ...>
        <i class="bi bi-people-fill"></i> <span>Core Team</span>
    </a>
</div>
```

---

### 5. Modified File: `php-backend/index.php`

**Lines 56**: Change query from `teachers WHERE is_core_team=1` to:

```sql
SELECT * FROM core_team WHERE is_visible=1 ORDER BY display_order ASC, name ASC
```

**Lines 686-735**: Replace the horizontal scroll carousel with a **centered flip-card grid**:

- Responsive grid: `col-lg-3 col-md-4 col-sm-6` (4 per row desktop, 3 tablet, 2 mobile)
- Cards are **center-aligned** using `justify-content-center` on the row
- Each card uses the flip-card CSS from `public/teachers.php`:
  - **Front**: Photo (260px height), name, designation overlay at bottom
  - **Back**: Gradient background using `--theme-primary`, centered details (qualification, subject, experience badge, bio excerpt)
  - Hover triggers `rotateY(180deg)` with 0.6s cubic-bezier transition
  - `backface-visibility: hidden` on both faces
  - Tap-to-flip JS for mobile/touch devices
- Remove the `scrollTeam()` JS function and scroll arrows
- Keep the section header with admin-controlled title/subtitle
- Keep the "View Our Teachers" button

---

### 6. Modified Files: `schema.sql` and `README.md`

- Add `core_team` table definition to schema.sql, update count to 28
- Add core_team to README.md table listing and upgrade SQL section

---

### Files Summary

| Action | File |
|--------|------|
| Create | `php-backend/admin/core-team.php` |
| Create | `php-backend/admin/ajax/core-team-actions.php` |
| Modify | `php-backend/index.php` (new query + flip-card grid, centered) |
| Modify | `php-backend/includes/header.php` (sidebar link) |
| Modify | `php-backend/schema.sql` (add table, update count to 28) |
| Modify | `php-backend/README.md` (docs update) |

