

## Fix School Logo Path and Remove White Box on Auth Pages

### Problem

1. **Logo path is wrong**: The database stores only the filename (e.g., `school_logo.png`), but the code treats it as a full path. When the DB value is loaded, the `src` becomes `/school_logo.png` instead of `/uploads/branding/school_logo.png`.

2. **White box behind logo**: The `.school-icon` container has a white background (`rgba(255,255,255,0.95)`) which the user wants removed -- the logo should display directly on the blue gradient background without a container box.

### Root Cause (Path)

In `settings.php` line 100, the logo is saved to DB as just the filename:
```php
$db->prepare("INSERT INTO settings ... VALUES ('school_logo',?)")->execute([$fname,$fname]);
// $fname = 'school_logo.png' (filename only)
```

But in `login.php` line 46, when the DB value is fetched, `$schoolLogo` becomes `school_logo.png`, and the `src` outputs `/school_logo.png` -- missing the `uploads/branding/` directory prefix.

The settings page itself handles this correctly on line 423:
```php
$_logoUrl = strpos($_logoFile, '/uploads/') === 0 ? $_logoFile : '/uploads/branding/' . $_logoFile;
```

### Fix

Apply the same path-building logic across all three auth pages:

```php
$logoSrc = (strpos($schoolLogo, '/uploads/') === 0 || strpos($schoolLogo, 'uploads/') === 0)
    ? '/' . ltrim($schoolLogo, '/')
    : '/uploads/branding/' . $schoolLogo;
```

This handles both cases: if the DB stores a full path (`uploads/branding/school_logo.png`) or just a filename (`school_logo.png`).

### Changes

**All 3 files** (`login.php`, `forgot-password.php`, `reset-password.php`):

1. **Fix logo path**: Add `$logoSrc` variable with proper path logic after fetching settings
2. **Remove white box**: Change `.school-icon` from a white rounded-box container to transparent with no background -- just a simple wrapper for the `<img>`
3. **Increase logo size**: Since there's no box constraint, allow the logo to display larger (e.g., `max-width:120px; max-height:120px`)

### CSS Change (`.school-icon`)

From:
```css
.school-icon { width:90px; height:90px; background:rgba(255,255,255,0.95); border-radius:20px; ... padding:10px }
```

To:
```css
.school-icon { width:auto; height:auto; background:none; border-radius:0; margin:0 auto 1.5rem; }
.school-icon img { max-width:120px; max-height:120px; object-fit:contain; }
```

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/login.php` | Fix logo path logic, remove white box styling |
| Modify | `php-backend/forgot-password.php` | Fix logo path logic, remove white box styling |
| Modify | `php-backend/reset-password.php` | Fix logo path logic, remove white box styling |

