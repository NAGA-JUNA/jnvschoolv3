

# Fix Settings Page 500 Error and Support Page Header Colors

## Overview
Two issues to fix: (1) Settings page crashes with HTTP 500 due to `information_schema` queries that may be restricted on shared hosting, and (2) Support page header has poor text contrast (white text barely visible on dark gradient).

---

## 1. Settings Page 500 Error

**Root Cause**: Lines 67-68 in `admin/settings.php` query `information_schema.tables` for table count and database size. On many cPanel shared hosting environments, access to `information_schema` is restricted, causing PDO to throw an exception that results in a 500 error.

Additionally, queries on lines 64-65 for `notifications` and `events` tables could also fail if those tables haven't been created yet.

**Fix**: Wrap all the system info queries (lines 59-68) in try-catch blocks with fallback values so the page loads even if some queries fail.

```php
try { $totalStudents = $db->query("SELECT COUNT(*) FROM students")->fetchColumn(); } catch(Exception $e) { $totalStudents = 0; }
try { $activeStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn(); } catch(Exception $e) { $activeStudents = 0; }
// ... same pattern for all stat queries
try { $dbTablesCount = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE()")->fetchColumn(); } catch(Exception $e) { $dbTablesCount = 'N/A'; }
try { $dbSize = $db->query("SELECT ROUND(SUM(data_length+index_length)/1024/1024,2) FROM information_schema.tables WHERE table_schema=DATABASE()")->fetchColumn(); } catch(Exception $e) { $dbSize = 'N/A'; }
```

---

## 2. Support Page Header Color Fix

**Problem**: The header gradient background is dark (`#0f172a` to `#1e40af`) and while the text has `text-white`, the subtitle text uses `opacity-75` and `opacity-50` making it nearly invisible.

**Fix**: In `admin/support.php`, change the header gradient to use the branding primary color with better contrast, and increase text opacity:
- Change subtitle from `opacity-75` to `opacity-90`
- Change version line from `opacity-50` to `opacity-75`
- Use a lighter gradient that ensures white text is clearly readable (e.g., `linear-gradient(135deg, #1e3a5f, #2563eb)`)

---

## Technical Details

### Files Modified
1. `php-backend/admin/settings.php` -- Wrap system info queries in try-catch blocks (lines 59-68)
2. `php-backend/admin/support.php` -- Fix header gradient colors and text opacity (lines 8-16)

