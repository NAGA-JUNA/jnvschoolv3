

# Add Bulk Import Feature for Students and Teachers

## Overview
Add an "Import" button next to Export/Add on both the Students and Teachers pages. Clicking it opens a Bootstrap modal popup with:
- A downloadable sample CSV file
- A file upload area (CSV only)
- A progress bar during upload/processing
- A results summary showing how many records were added, skipped, or failed

---

## 1. Students Page - Import Modal

### Button
Add an "Import" button between Export and Add Student in `admin/students.php` (line 22):
```php
<button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
  <i class="bi bi-upload me-1"></i>Import
</button>
```

### Modal Content
- **Step 1 (Upload)**: Instructions, download sample CSV link, file input for CSV, Upload button
- **Step 2 (Progress)**: Progress bar with percentage, status text ("Processing row 5 of 120...")
- **Step 3 (Results)**: Summary card showing added/skipped/failed counts with a table of errors if any

### Sample CSV
The "Download Sample" link triggers a PHP endpoint that generates a CSV with headers:
`admission_no, name, father_name, mother_name, dob, gender, class, section, roll_no, phone, email, address, blood_group, category, aadhar_no, status, admission_date`

And 2 example rows.

### Backend Processing (`admin/import-students.php`)
- Accepts POST with CSV file upload
- Validates CSV headers match expected columns
- Loops through rows, inserting each student with try-catch for duplicates
- Returns JSON response: `{ total: 50, added: 45, skipped: 3, failed: 2, errors: ["Row 5: Duplicate admission_no TEST123"] }`
- Uses transactions for safety

### JavaScript (AJAX Upload with Progress)
- FormData upload via `fetch()` to `import-students.php`
- Since CSV processing is server-side, progress is simulated: starts at 10% on upload, jumps to 90% when response arrives, 100% on complete
- On success, parses JSON response and displays the results summary
- On error, shows error message

---

## 2. Teachers Page - Import Modal

Same pattern as students but with teacher-specific columns:
`employee_id, name, email, phone, subject, qualification, experience_years, dob, gender, address, joining_date, status`

### Backend Processing (`admin/import-teachers.php`)
- Same pattern as student import
- Also creates a user account for teachers with email (password: Teacher@123)

---

## 3. Sample CSV Download Endpoints

### `admin/sample-students-csv.php`
Generates and streams a CSV file with proper headers and 2 example rows.

### `admin/sample-teachers-csv.php`
Same for teachers with teacher-specific columns.

---

## Technical Details

### Files Modified
1. `php-backend/admin/students.php` -- Add Import button and Import modal HTML/JS
2. `php-backend/admin/teachers.php` -- Add Import button and Import modal HTML/JS

### Files Created
3. `php-backend/admin/import-students.php` -- Backend CSV processing endpoint (returns JSON)
4. `php-backend/admin/import-teachers.php` -- Backend CSV processing endpoint (returns JSON)
5. `php-backend/admin/sample-students-csv.php` -- Sample CSV download
6. `php-backend/admin/sample-teachers-csv.php` -- Sample CSV download

### Import Modal UI Flow

```text
+----------------------------------+
|  Import Students           [X]   |
|----------------------------------|
|  Step 1: Upload                  |
|                                  |
|  [Download Sample CSV]           |
|                                  |
|  Instructions:                   |
|  - CSV format only               |
|  - First row must be headers     |
|  - admission_no & name required  |
|                                  |
|  [Choose File...]                |
|                                  |
|  [Cancel]  [Upload & Process]    |
+----------------------------------+

         ↓ (after upload)

+----------------------------------+
|  Import Students           [X]   |
|----------------------------------|
|  Step 2: Processing...           |
|                                  |
|  ████████████░░░░░░  75%         |
|  Processing records...           |
+----------------------------------+

         ↓ (after complete)

+----------------------------------+
|  Import Students           [X]   |
|----------------------------------|
|  Step 3: Results                 |
|                                  |
|  [✓] 45 Added                    |
|  [!] 3  Skipped (duplicates)     |
|  [✗] 2  Failed                   |
|                                  |
|  Errors:                         |
|  Row 5: Invalid date format      |
|  Row 12: Missing admission_no    |
|                                  |
|  [Close]  [Import More]          |
+----------------------------------+
```

### No Schema Changes Required
Uses existing `students` and `teachers` tables as-is.

