

## Add Database Setup and Schema Import to Super Admin

### What This Does

Adds a "Database Setup" card in the **System tab** of Settings (Super Admin only) that lets you import/update the database schema directly from the admin panel -- no need to go to cPanel or phpMyAdmin.

### How It Works

**File: `php-backend/admin/settings.php`**

1. **New POST handler** (`form_action === 'import_schema'`, Super Admin only):
   - Reads the `schema.sql` file from disk
   - Splits it into individual SQL statements
   - Executes each statement one by one inside a transaction
   - Shows success with count of statements executed, or shows error with the specific failing statement
   - Logs the action to audit logs

2. **New UI card** in the System tab (inside the Super Admin section, next to the Danger Zone):
   - Shows a "Database Setup" card with:
     - Current table count vs expected 28 tables
     - A status indicator (green if all 28 tables exist, yellow if some missing)
     - List of any missing tables detected
     - "Import/Reset Schema" button with a confirmation dialog warning that it will DROP and recreate all tables (data loss)
     - "Check Tables" button that shows which tables exist vs which are expected
   - The import button requires typing "CONFIRM" to proceed (extra safety since this is destructive)

3. **Safety measures**:
   - Only Super Admin can access this
   - Double confirmation: JavaScript confirm + typed confirmation word
   - CSRF token protection
   - Audit log entry recorded
   - Clear warning about data loss (DROP TABLE statements in schema.sql)

### UI in the System Tab

```text
+------------------------------------------+
| Database Setup                    [SA]   |
|------------------------------------------|
| Status: 26/28 tables found    [WARNING]  |
|                                          |
| Missing tables:                          |
|  - popup_analytics                       |
|  - core_team                             |
|                                          |
| [Check Tables]  [Import/Reset Schema]    |
|                                          |
| WARNING: Importing schema will DROP all  |
| existing tables and recreate them.       |
| ALL DATA WILL BE LOST. Back up first!    |
+------------------------------------------+
```

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/admin/settings.php` | Add schema import handler + Database Setup card in System tab |

