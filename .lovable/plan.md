

## Fix "Need Help?" Form Submission Not Saving to Database

### Root Cause

The `enquiries` table in the database has these columns: `id`, `name`, `phone`, `email`, `message`, `status`, `created_at`.

However, the public endpoint `php-backend/public/ajax/enquiry-submit.php` (line 51) tries to INSERT into a `source` column that **does not exist** in the table:

```sql
INSERT INTO enquiries (name, phone, email, message, source, status) VALUES (?, ?, ?, ?, ?, 'new')
```

This causes a database error, which is caught and returns "Something went wrong" -- so the form appears to submit but nothing is actually saved.

### Fix

**File: `php-backend/public/ajax/enquiry-submit.php`** (line 51)

Remove the `source` column from the INSERT query:

```sql
INSERT INTO enquiries (name, phone, email, message, status) VALUES (?, ?, ?, ?, 'new')
```

And update the execute parameters to remove `$source` (line 52):

```php
->execute([$name, $mobile, $email ?: null, $message ?: null]);
```

Also remove the unused `$source` variable (line 33).

### Optional Enhancement

If you want to track the source of enquiries (e.g., "need_help_popup" vs "enquiry_form"), you would need to add a `source` column to the database:

```sql
ALTER TABLE enquiries ADD COLUMN source VARCHAR(50) DEFAULT NULL AFTER message;
```

But the immediate fix is simply removing `source` from the INSERT so submissions save correctly.

### Files Summary

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/public/ajax/enquiry-submit.php` | Remove `source` column from INSERT query (lines 33, 51-52) |

