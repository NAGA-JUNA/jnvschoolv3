

## Add Fee Structure Module

### Overview
Add a complete Fee Structure management system with an admin CRUD page, two new database tables, and a public-facing display page -- all following the existing PHP + MySQL patterns exactly.

### Database Schema (2 New Tables)

**Table 1: `fee_structures`** -- One row per class + academic year combination

| Column | Type | Purpose |
|--------|------|---------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| class | VARCHAR(20) | e.g. LKG, UKG, Class 1-10 |
| academic_year | VARCHAR(20) | e.g. 2025-2026 |
| is_visible | TINYINT(1) DEFAULT 1 | Toggle public visibility |
| notes | TEXT | Optional remarks |
| created_by | INT UNSIGNED | FK to users |
| created_at / updated_at | DATETIME | Timestamps |

Unique constraint on (class, academic_year) to prevent duplicates.

**Table 2: `fee_components`** -- Multiple rows per fee structure

| Column | Type | Purpose |
|--------|------|---------|
| id | INT UNSIGNED AUTO_INCREMENT | Primary key |
| fee_structure_id | INT UNSIGNED | FK to fee_structures |
| component_name | VARCHAR(100) | e.g. "Tuition Fee", "Transport Fee" |
| amount | DECIMAL(10,2) | Fee amount |
| frequency | ENUM('one-time','monthly','quarterly','yearly') | Payment frequency |
| is_optional | TINYINT(1) DEFAULT 0 | Mark optional fees |
| display_order | INT DEFAULT 0 | Sort order |

### New Files

| File | Purpose |
|------|---------|
| `php-backend/admin/fee-structure.php` | Admin CRUD page (create, edit, delete fee structures + components) |
| `php-backend/public/fee-structure.php` | Public page with class/year selector and responsive fee table |

### Admin Page (`admin/fee-structure.php`)

Follows the exact same pattern as `admin/events.php`:
- `requireAdmin()` + CSRF protection on all actions
- Left panel: Form to add/edit a fee structure (select class, academic year, toggle visibility)
- Below the form: Dynamic component builder -- add rows for each fee component (name, amount, frequency, optional toggle)
- Right panel: Table listing all fee structures with edit/delete actions
- Audit logging on create, update, delete
- Custom component support: admin types any component name (not limited to presets)
- Preset buttons for quick-add of standard components (Admission, Tuition, Transport, Books, Activity/Lab)

### Public Page (`public/fee-structure.php`)

Follows the same pattern as `public/certificates.php`:
- Loads school branding from settings
- Uses public-navbar.php and public-footer.php includes
- Two dropdown filters: Class and Academic Year
- Responsive table showing component name, amount, frequency
- Total row at the bottom
- Only shows structures where `is_visible = 1`
- Print-friendly CSS via `@media print` rules

### Sidebar Navigation

Add a "Fee Structure" link in the admin sidebar under the "Main" nav group in `header.php` (after Admissions):
```
<a href="/admin/fee-structure.php" class="nav-link">
  <i class="bi bi-cash-stack"></i> <span>Fee Structure</span>
</a>
```

### Public Navbar

Add "Fee Structure" to the default fallback menu items in `public-navbar.php`, and admin can also add it via Navigation Settings.

### Schema Update

Add the CREATE TABLE statements to `schema.sql` for documentation. For the live database, run the SQL directly in phpMyAdmin.

### Optional Enhancements Included

- **Print-friendly view**: `@media print` styles hide navbar/footer, format table cleanly
- **Visibility toggle**: `is_visible` column lets admin hide/show fee structure on public site
- **PDF export**: A "Print / Save as PDF" button using `window.print()` (browser-native, no libraries needed)

### Technical Details

- All queries use PDO prepared statements (SQL injection safe)
- All forms include CSRF tokens via `csrfField()` / `verifyCsrf()`
- Output escaped with `e()` helper
- Audit logging via `auditLog()` on all mutations
- No new dependencies -- pure PHP + HTML + CSS + vanilla JS
- FileHandler not needed (no file uploads in this module)

### Files Modified / Created

| File | Action |
|------|--------|
| `php-backend/admin/fee-structure.php` | **NEW** -- Admin CRUD page |
| `php-backend/public/fee-structure.php` | **NEW** -- Public display page |
| `php-backend/schema.sql` | **MODIFIED** -- Add fee_structures + fee_components tables |
| `php-backend/includes/header.php` | **MODIFIED** -- Add sidebar nav link |
| `php-backend/includes/public-navbar.php` | **MODIFIED** -- Add to default menu fallback |

