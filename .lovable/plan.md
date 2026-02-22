

## Update Manage Teachers to Match Core Team Manager Style

### Overview

Redesign the "Manage Teachers" admin page (`teachers.php`) to use the same card-based grid layout as the Core Team Manager, with drag-and-drop reordering, visibility/featured toggles, and inline modals for add/edit. Also fully decouple the two modules by removing the `is_core_team` references from the Teachers module.

### What Changes

---

### 1. Redesign `php-backend/admin/teachers.php` -- Card Grid Layout

Replace the current table-based layout with a card grid matching Core Team Manager:

- **Header**: Same style -- title "Manage Teachers", subtitle, member count badge, "Add Teacher" button
- **Search/Filter bar**: Keep existing search + status filter (Core Team doesn't have this, but Teachers needs it due to volume)
- **Card grid** (`row g-3`): Each teacher shown as a card with:
  - Toggle buttons for visibility (eye icon) and featured (star icon) at top
  - Display order badge
  - Circular photo (or placeholder icon)
  - Name, designation, subject
  - Edit button (opens modal) and Delete button
  - "Drag to reorder" footer
  - Draggable attribute for drag-and-drop reordering
- **Edit Modal**: Per-teacher modal with all fields (employee_id, name, designation, gender, email, phone, subject, qualification, experience, DOB, joining date, status, photo upload, address, bio, visibility, featured toggles)
- **Add Modal**: Same form but empty, opens from the header button
- **Import button**: Keep CSV import functionality (modal unchanged)
- **View Modal**: Keep the existing teacher profile view modal
- **Drag-and-drop JS**: Same pattern as Core Team Manager -- reorder via AJAX to `/admin/ajax/teacher-actions.php?action=reorder`
- **Toggle JS**: AJAX calls for `toggle_visibility` and `toggle_featured` (already exist in teacher-actions.php)
- **Delete JS**: AJAX call for `delete_teacher` with confirmation
- **Pagination**: Keep pagination for large teacher lists (Core Team doesn't need it but Teachers might)
- Remove the `is_core_team` badge display from teacher cards

---

### 2. Update `php-backend/admin/teacher-form.php` -- Remove Core Team Toggle

- Remove the "Core Team Member" checkbox toggle (lines 46-52) since Core Team is now managed independently
- Remove `is_core_team` from the `$d` data array (line 6)
- Update the SQL INSERT/UPDATE statements to remove the `is_core_team` column
- Keep all other fields (employee_id, name, designation, email, phone, subject, qualification, experience, dob, gender, address, joining_date, status, bio, photo)
- This form may still be used as a fallback for direct navigation

---

### 3. Update `php-backend/public/teachers.php` -- Remove Core Team Sorting

- Line 31: Remove `is_core_team DESC` from the ORDER BY clause
- Change query to: `SELECT * FROM teachers WHERE status='active' AND is_visible=1 ORDER BY display_order ASC, name ASC`
- This ensures the public Teachers page shows only teachers ordered by their display order, completely independent of core team

---

### 4. Keep `php-backend/admin/ajax/teacher-actions.php` As-Is

- The AJAX handler already supports: `reorder`, `toggle_visibility`, `toggle_featured`, `delete_teacher`, `save_teacher`, `save_principal`, `list_teachers`
- No changes needed -- it already has all the endpoints the new card-based UI will use

---

### 5. Keep Principal Profile Editor Unchanged

- The Principal Profile Editor functionality in `teacher-actions.php` (`get_principal`, `save_principal`) remains untouched
- No changes to the Principal section on homepage or teachers page

---

### Files Summary

| Action | File | Change |
|--------|------|--------|
| Rewrite | `php-backend/admin/teachers.php` | Table layout to card grid with modals, toggles, drag-and-drop (matching Core Team Manager style) |
| Modify | `php-backend/admin/teacher-form.php` | Remove `is_core_team` toggle and field from form/SQL |
| Modify | `php-backend/public/teachers.php` | Remove `is_core_team` from ORDER BY query |
| No change | `php-backend/admin/ajax/teacher-actions.php` | Already has all needed endpoints |
| No change | Principal Profile Editor | Kept as-is |

---

### Key Design Decisions

- **Teachers keeps search/filter and pagination** because teacher count can be large (unlike Core Team which is typically small)
- **Import CSV button** stays available in the header alongside "Add Teacher"
- **The two modules are fully independent**: adding a teacher does NOT affect Core Team, and vice versa
- **`is_core_team` column stays in database** for backward compatibility but is no longer used or displayed in the admin UI

