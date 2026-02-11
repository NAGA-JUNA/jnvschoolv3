

## Enhance Page Content Manager: Principal and Teachers Grid Inline Management

### Overview

This plan adds expandable inline management panels within the existing Page Content Manager "Our Teachers" tab, directly beneath the "Show Principal Section" and "Show All Teachers Grid" toggles. The existing teacher CRUD system (`admin/teachers.php`, `admin/teacher-form.php`) remains untouched -- these new panels provide a convenient inline editing experience within the content manager.

---

### Current State

- The `teachers` table already has all required fields: name, designation, qualification, bio, photo, subject, experience_years, is_core_team, status
- Full teacher CRUD already exists at `admin/teachers.php` and `admin/teacher-form.php`
- The public page (`public/teachers.php`) already renders the Principal section (teacher with designation='Principal' + bio) and a teachers grid
- Page Content Manager has toggles for `teachers_core_team_show` and `teachers_all_show` but no inline editing

### What This Plan Adds

Two collapsible management panels inside `page-content-manager.php` (only visible when on the "teachers" tab):

---

### 1. Database Changes

**Alter `teachers` table** -- add two columns:

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| `display_order` | INT | 0 | Controls sort order in grid (drag-and-drop) |
| `is_visible` | TINYINT(1) | 1 | Per-teacher show/hide toggle |
| `is_featured` | TINYINT(1) | 0 | Featured teacher badge |

Update `schema.sql` to include these columns in the CREATE TABLE statement.

---

### 2. New File: `admin/ajax/teacher-actions.php`

A single AJAX endpoint handling all inline teacher management actions (admin-only, CSRF-protected):

- `action=get_principal` -- fetch current principal record
- `action=save_principal` -- update/create principal (photo upload, name, designation, qualification, bio)
- `action=list_teachers` -- return all teachers as JSON (with search filter)
- `action=save_teacher` -- add or update a teacher (with photo upload)
- `action=delete_teacher` -- delete a teacher
- `action=reorder` -- update `display_order` for a list of teacher IDs
- `action=toggle_visibility` -- toggle `is_visible` for a teacher
- `action=toggle_featured` -- toggle `is_featured` for a teacher

All actions validate admin session and CSRF token.

---

### 3. Modify `admin/page-content-manager.php`

Add two expandable panels that render **only** when `$activePage === 'teachers'`, inserted after the existing toggle fields (no existing fields/layout are changed):

**Panel A: Principal Section Manager** (below `teachers_core_team_show` toggle)
- Collapsible card with header "Principal Profile Editor"
- Shows current principal photo (with preview), name, designation (pre-filled "Principal"), qualification, bio/message
- Upload/replace/remove photo button
- Image preview before save
- Save button with AJAX submission and toast feedback
- If no principal exists, shows "No principal set -- assign one" with a teacher selector dropdown

**Panel B: Teachers Grid Manager** (below `teachers_all_show` toggle)
- Collapsible card with header "Manage Teachers"
- Search bar at top
- Sortable table/list of all teachers showing: photo thumbnail, name, subject, show/hide toggle, featured toggle, drag handle
- Drag-and-drop reordering (using HTML5 drag events, no library needed)
- Add Teacher button opens a modal form (same fields as existing teacher-form.php)
- Edit button opens the same modal pre-filled
- Delete button with confirmation
- All operations via AJAX with toast feedback

---

### 4. Modify `public/teachers.php`

- Update teacher query to respect `is_visible=1` and `ORDER BY display_order ASC, name ASC`
- Add `is_featured` badge display on teacher cards
- Add `loading="lazy"` to teacher card images
- Grid already uses `col-6 col-md-4 col-lg-3` (4/3/2 cols) -- adjust to ensure proper responsive layout
- Principal section already works from the `teachers` table -- no changes needed there

---

### 5. Files Summary

| File | Action | Purpose |
|------|--------|---------|
| `admin/ajax/teacher-actions.php` | Create | AJAX endpoint for all inline teacher operations |
| `admin/page-content-manager.php` | Modify | Add two collapsible panels below the teachers toggles |
| `public/teachers.php` | Modify | Respect `is_visible`, `display_order`, `is_featured`; lazy-load images |
| `schema.sql` | Modify | Add `display_order`, `is_visible`, `is_featured` columns to `teachers` |

---

### 6. Technical Details

**AJAX Pattern** -- All inline operations use `fetch()` to `admin/ajax/teacher-actions.php`:
```php
// Example: save principal via AJAX
fetch('/admin/ajax/teacher-actions.php', {
    method: 'POST',
    body: formData  // includes action, csrf_token, photo file, fields
})
.then(r => r.json())
.then(data => { /* show toast, refresh panel */ });
```

**Drag-and-Drop Reorder** -- Uses native HTML5 `draggable` attribute and `dragstart`/`dragover`/`drop` events. On drop, sends the new order array to the AJAX endpoint.

**Image Preview** -- Uses `FileReader.readAsDataURL()` to show a preview thumbnail before uploading.

**Collapsible Panels** -- Uses Bootstrap 5 `collapse` component, triggered by the toggle switch state. Panel shows/hides based on whether the section toggle is ON.

**Permissions** -- `teacher-actions.php` calls `requireAdmin()` at the top to restrict access.

**No New Dependencies** -- Everything uses native browser APIs and Bootstrap 5 components already in the project.

---

### 7. What Does NOT Change

- Existing Page Content Manager layout, tabs, and fields
- Existing `admin/teachers.php` and `admin/teacher-form.php` (the standalone teacher management pages remain fully functional)
- Other page tabs in the content manager
- Public page layout structure (only query logic and lazy-load attributes added)

