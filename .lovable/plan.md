

## Fix Seat Capacity + Admissions Issues

Three problems identified and their fixes:

---

### 1. Seat Capacity: Add New Class + Enable/Disable Toggle

**Current state**: Hardcoded loop for classes 1-12 only. No way to add custom classes (like "Nursery", "LKG", "UKG") or disable a class.

**Changes to `php-backend/admin/seat-capacity.php`**:
- Add an "is_active" column support (requires DB migration) so admins can toggle classes on/off
- Add an "Add New Class" button/form at the bottom to create custom class entries (e.g., Nursery, LKG, UKG, or Class 13)
- Replace the hardcoded `for ($i=1; $i<=12)` loop with dynamic rendering from the database
- Each class card gets a toggle switch (on/off) that controls whether the class appears on the public admission form
- Cards for disabled classes will appear with a muted/greyed-out style

**Schema migration** (to run in phpMyAdmin):
```sql
ALTER TABLE class_seat_capacity ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER total_seats;
```

---

### 2. Fix Admission Form Submit Button

**Current state**: The form uses `method="POST"` but has no CSRF token (unlike admin forms). The real issue is likely that validation on hidden steps blocks submission. When the form submits, `required` fields on hidden panels (step 1 & 2) can prevent the browser from submitting since those panels are set to `display:none`.

**Changes to `php-backend/public/admission-form.php`**:
- On the final submit, temporarily make all wizard panels visible (or remove `required` from validated fields) so the browser's native validation doesn't block hidden inputs
- Add a submit handler that ensures all panels are briefly shown before submission
- Alternatively, add a JS form submit handler that validates all steps before calling `form.submit()`

---

### 3. Fix Admin Admissions View/Actions

**Current state**: The detail drawer loads via AJAX from `admission-actions.php`. The status update and notes forms inside the drawer use `method="POST"` but submit back to the main page. The issue is that the POST action handler at line 104 redirects with `$_GET` params, but when submitting from inside the drawer, the page URL may not have the right query params. Also, the `admission_status_history` INSERT at line 96 has a subquery referencing the same table being updated, which can fail in MySQL.

**Changes to `php-backend/admin/admissions.php`**:
- Fix the interview scheduling query (line 96) that has a problematic self-referencing subquery -- fetch old status first, then update
- Ensure POST redirect preserves the current page state correctly

**Changes to `php-backend/admin/ajax/admission-actions.php`**:
- Add POST handlers for `update_status`, `add_note`, `set_followup`, `set_interview`, and `delete` that return JSON responses
- Convert the drawer's action forms to use AJAX (fetch) instead of traditional form POST, so actions work without leaving/reloading the page
- After successful AJAX action, reload the drawer content and update the table row dynamically

---

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/admin/seat-capacity.php` | Dynamic class rendering, add new class form, on/off toggle per class |
| `php-backend/public/admission-form.php` | Fix submit button blocked by hidden required fields |
| `php-backend/admin/admissions.php` | Fix self-referencing subquery, convert drawer actions to AJAX |
| `php-backend/admin/ajax/admission-actions.php` | Add POST action handlers returning JSON |
| `php-backend/schema.sql` | Add `is_active` column to `class_seat_capacity` |

