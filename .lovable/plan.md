

## Fix Admission Form 500 Error

### Root Cause
The admission form's SQL INSERT statement references two column names that don't match the actual database table schema, causing a MySQL error and HTTP 500 on submission.

| Code Uses | Actual Column in DB |
|-----------|-------------------|
| `date_of_birth` | `dob` |
| `document_path` | `documents` |

### Fix (1 file)

**`php-backend/public/admission-form.php` (line 67):**

Change the INSERT statement column names from `date_of_birth` to `dob` and from `document_path` to `documents`:

```sql
-- Before:
INSERT INTO admissions (student_name, date_of_birth, ..., document_path, status)

-- After:
INSERT INTO admissions (student_name, dob, ..., documents, status)
```

That single line change fixes the 500 error. No other files need modification.

