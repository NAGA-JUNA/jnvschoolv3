

## Fix SQL Error and Ensure Core Team Section Displays

### Problem 1: SQLSTATE[HY093] Error

**File: `php-backend/admin/teacher-form.php`** (line 25)

The UPDATE query uses PHP's array spread operator `[...array_values($d), $id]` which can cause parameter count mismatches in certain PHP configurations. The fix is to replace the spread with an explicit `array_merge` call and use a safer parameter-building approach.

**Fix:**
- Replace `[...array_values($d), $id]` with `array_merge(array_values($d), [$id])`
- This ensures reliable parameter passing across all PHP 8.x versions

### Problem 2: Core Team Section Not Showing on Homepage

**File: `php-backend/index.php`** (line 687)

The section only renders when `$coreTeam` is not empty. The query on line 56 fetches teachers with `is_core_team=1`. If no teachers are flagged as core team members, the section is hidden entirely.

**No code change needed here** -- the section logic is correct. The user needs to:
1. Go to Admin > Teachers
2. Edit a teacher
3. Enable the "Core Team Member" toggle
4. Save

Once at least one teacher has `is_core_team=1`, the section will appear on the homepage.

### Files Changed

| File | Change |
|------|--------|
| `php-backend/admin/teacher-form.php` | Replace spread operator with `array_merge` for safer SQL parameter binding |

### Technical Detail

Current (broken in some environments):
```php
->execute([...array_values($d), $id])
```

Fixed:
```php
$params = array_values($d);
$params[] = $id;
->execute($params)
```

This is a one-line fix that resolves the SQLSTATE[HY093] error reliably.

