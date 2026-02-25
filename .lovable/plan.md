

## Add Backup & Download Feature to Super Admin

### What This Does

Adds a **"Backup & Migration"** card in the Super Admin System tab that lets you download:

1. **Database Backup** -- exports all 28 tables as a `.sql` file (complete with `CREATE TABLE` and `INSERT` statements)
2. **Files Backup** -- zips the entire `uploads/` folder (logos, gallery images, slider images, etc.)
3. **Full Backup** -- combines both into a single `.zip` file

This gives you everything needed to migrate your school system to a new cPanel hosting.

### How It Works

**New file: `php-backend/admin/ajax/backup-download.php`**

A dedicated AJAX/download handler (Super Admin only) that:

- **Database export (`type=database`)**: Iterates all tables using `SHOW TABLES`, then for each table runs `SHOW CREATE TABLE` and `SELECT *` to generate a complete `.sql` dump file. Downloads as `jnvschool_db_backup_YYYY-MM-DD.sql`
- **Files export (`type=files`)**: Uses PHP's `ZipArchive` class to recursively zip the `uploads/` directory. Downloads as `jnvschool_files_backup_YYYY-MM-DD.zip`
- **Full export (`type=full`)**: Creates a `.zip` containing both the SQL dump and the uploads folder. Downloads as `jnvschool_full_backup_YYYY-MM-DD.zip`

Safety: Super Admin check, CSRF token, audit log entry for each backup.

**Modified file: `php-backend/admin/settings.php`**

Adds a "Backup & Migration" card in the System tab with:

- Three download buttons (Database, Files, Full Backup)
- Estimated sizes shown (DB row count, uploads folder size)
- A note explaining what each backup contains
- Visual layout matching the existing Database Setup card style

### UI in the System Tab

```text
+------------------------------------------+
| Backup & Migration                [SA]   |
|------------------------------------------|
| Download backups for migrating to a      |
| new cPanel or for safekeeping.           |
|                                          |
| Database Backup          (~28 tables)    |
| All tables with data as .sql file        |
| [Download SQL]                           |
|                                          |
| Files Backup             (~XX MB)        |
| All uploaded images, logos, documents     |
| [Download ZIP]                           |
|                                          |
| Full Backup (DB + Files)                 |
| Everything in one ZIP for migration      |
| [Download Full ZIP]                      |
+------------------------------------------+
```

### Migration Workflow

Once downloaded, to set up on new cPanel:
1. Create database + user on new cPanel (manual step)
2. Import the `.sql` file via phpMyAdmin
3. Upload the `uploads/` folder contents via File Manager
4. Update `db.php` with new credentials
5. Done -- same site on new hosting

### Technical Details

- Uses PHP's built-in `ZipArchive` (available on most cPanel hosts)
- SQL export is done in PHP (not `mysqldump` which may not be available)
- Large uploads folders are streamed to avoid memory issues
- Temporary files are created in `/tmp` and cleaned up after download
- Each table export includes `DROP TABLE IF EXISTS` for easy re-import

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Create | `php-backend/admin/ajax/backup-download.php` | New download handler for DB, files, and full backups |
| Modify | `php-backend/admin/settings.php` | Add "Backup & Migration" card in System tab |

