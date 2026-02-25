

## Add Database Connection Verification to Super Admin Settings

### What This Does

Adds a "Pre-Setup Checklist" section above the existing Database Setup card that verifies the database connection is working -- confirming that Step 1 (manual cPanel setup) was completed correctly before proceeding to Step 2 (schema import).

### How It Works

**File: `php-backend/admin/settings.php`**

1. **New POST handler** (`form_action === 'test_db_connection'`, Super Admin only):
   - Attempts a fresh PDO connection using the credentials from `db.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS)
   - Runs a simple `SELECT 1` query to verify the connection works
   - Checks if the user has CREATE/DROP privileges by running `SHOW GRANTS`
   - Returns success/failure via flash message with details

2. **New "Connection Status" section** added at the top of the Database Setup card:
   - Shows the configured database name (`DB_NAME`) and user (`DB_USER`) from `db.php` (passwords hidden)
   - A "Test Connection" button that verifies:
     - Can connect to the database server
     - Database exists and is accessible
     - User has proper privileges
   - Green checkmarks or red X for each step
   - A checklist showing the 3 manual cPanel steps with status indicators

### Updated UI in the Database Setup Card

```text
+------------------------------------------+
| Database Setup                    [SA]   |
|------------------------------------------|
| Pre-Setup Checklist (cPanel):            |
|  1. Database: yshszsos_jnvschool    [OK] |
|  2. User: yshszsos_Admin           [OK] |
|  3. Privileges: ALL PRIVILEGES      [OK] |
|                                          |
| [Test Connection]                        |
|------------------------------------------|
| Status: 26/28 tables found    [WARNING]  |
| ...existing table check UI...            |
| [Import / Reset Schema]                  |
+------------------------------------------+
```

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/admin/settings.php` | Add `test_db_connection` POST handler + connection checklist UI in Database Setup card |

