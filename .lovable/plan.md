

## Add School Logo to Forgot Password and Reset Password Pages + Fix Logo Path

### Problem

1. The **forgot-password** and **reset-password** pages still show Bootstrap icons instead of the school logo
2. The logo on the **login page** is not displaying (showing alt text instead) -- likely a path issue where the database value already contains `uploads/` so prepending `/` creates an incorrect path

### Fix: Logo Path (login.php)

The `src="/<?= $schoolLogo ?>"` outputs something like `/uploads/branding/school_logo.png`. But if the DB stores a value that already starts with `uploads/branding/...`, it should work. The real issue may be that the DB stores a full path like `/uploads/branding/school_logo.png` (with leading slash), causing a double slash `//uploads/...`. We need to normalize the path by trimming leading slashes:

```php
$logoSrc = '/' . ltrim($schoolLogo, '/');
```

### Changes

**`php-backend/forgot-password.php`**:
- Fetch `school_logo` and `school_tagline` from settings (alongside `school_name`)
- Replace the `<i class="bi bi-shield-lock-fill"></i>` icon with an `<img>` tag showing the school logo
- Update `.school-icon` CSS to white background with padding (same as login page)

**`php-backend/reset-password.php`**:
- Fetch `school_logo` and `school_tagline` from settings (alongside `school_name`)
- Replace the `<i class="bi bi-key-fill"></i>` icon with an `<img>` tag showing the school logo
- Update `.school-icon` CSS to white background with padding (same as login page)

**`php-backend/login.php`**:
- Fix the logo `src` path to normalize slashes: `ltrim($schoolLogo, '/')` to prevent double-slash issues

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/login.php` | Fix logo path normalization |
| Modify | `php-backend/forgot-password.php` | Add school logo + fetch from DB |
| Modify | `php-backend/reset-password.php` | Add school logo + fetch from DB |

