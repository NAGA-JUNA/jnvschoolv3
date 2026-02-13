

## Fix: Reset Password "Invalid or expired" Error

### Root Cause
**Timezone mismatch between PHP and MySQL.** The forgot-password page stores `reset_expires` using PHP's `date()` function, but the reset-password page validates it using MySQL's `NOW()`. If the server's PHP timezone and MySQL timezone differ, the token can appear expired the moment it's created.

### Fix (2 changes in 1 file)

**File: `php-backend/reset-password.php`**

Replace the MySQL `NOW()` comparison with a PHP-generated timestamp so both sides use the same clock:

**Current code (line 10):**
```php
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() AND is_active = 1");
```

**Fixed code:**
```php
$now = date('Y-m-d H:i:s');
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > ? AND is_active = 1");
$stmt->execute([$token, $now]);
```

This ensures the same PHP clock is used for both generating the expiry (in `forgot-password.php`) and checking it (in `reset-password.php`).

### Additional Safety: Increase Token Expiry

**File: `php-backend/forgot-password.php`** (line 19)

Change the expiry window from 1 hour to 2 hours to add buffer:

```php
$expires = date('Y-m-d H:i:s', strtotime('+2 hours'));
```

### Files Modified

| File | Change |
|------|--------|
| `php-backend/reset-password.php` | Use PHP `date()` instead of MySQL `NOW()` for token expiry check |
| `php-backend/forgot-password.php` | Increase token expiry from 1 hour to 2 hours |

### After Deploying
Request a new forgot-password email and click the link again — it should now show the password form instead of the error.

