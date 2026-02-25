

## Update Backup to Include All Site Files (Not Just Uploads)

### What Changes

Currently the **Files Backup** and **Full Backup** only zip the `uploads/` folder. You want it to include **everything** shown in cPanel -- all PHP files, config, admin, includes, etc. -- so you can fully migrate to a new hosting.

### Changes to `php-backend/admin/ajax/backup-download.php`

1. **Files backup (`type=files`)**: Change from zipping only `uploads/` to zipping the **entire site root** (the `php-backend/` directory equivalent on the server). This includes:
   - `admin/`, `config/`, `includes/`, `public/`, `teacher/`, `uploads/`
   - `.htaccess`, `index.php`, `login.php`, `logout.php`, `forgot-password.php`, `reset-password.php`, `schema.sql`, `README.md`

2. **Exclusions** (for safety and size):
   - Skip `error_log` files
   - Skip `.well-known/` and `cgi-bin/` (cPanel system folders)
   - Skip any `.zip` or temporary backup files to avoid recursion

3. **Full backup (`type=full`)**: Same update -- the ZIP will contain the SQL dump + all site files (not just uploads)

### Changes to `php-backend/admin/settings.php`

4. Update the "Files Backup" label/description from "All uploaded images, logos, documents" to **"All site files (PHP, config, uploads, everything)"** so it's clear what's included.

5. Update the folder size calculation to measure the entire site root instead of just `uploads/`.

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/admin/ajax/backup-download.php` | Zip entire site directory instead of just uploads/ |
| Modify | `php-backend/admin/settings.php` | Update labels and size calculation |

