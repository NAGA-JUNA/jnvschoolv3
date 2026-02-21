

## Improve .htaccess Security Headers

### Problem
The current `.htaccess` has basic directory protection but is missing critical HTTP security headers. This can cause:
- ModSecurity flagging/deleting PHP files (like the `slider.php` issue)
- Clickjacking, XSS, and MIME-sniffing vulnerabilities
- Missing HTTPS enforcement

### Current State
The file only has:
- Directory access blocking (config/, includes/)
- SQL file blocking
- Directory listing disabled
- Self-protection rule

### What Will Be Added

**1. HTTP Security Headers**
- `X-Content-Type-Options: nosniff` -- Prevents MIME-type sniffing attacks
- `X-Frame-Options: SAMEORIGIN` -- Blocks clickjacking (embedding your site in iframes)
- `X-XSS-Protection: 1; mode=block` -- Legacy XSS filter for older browsers
- `Referrer-Policy: strict-origin-when-cross-origin` -- Controls referrer leakage
- `Permissions-Policy` -- Disables camera, microphone, geolocation access from the browser
- `Content-Security-Policy` -- Controls which scripts/styles/images can load

**2. HTTPS Enforcement**
- Force all traffic to HTTPS via RewriteRule redirect

**3. File Upload Protection**
- Block execution of PHP files inside `uploads/` directory -- this prevents uploaded malicious scripts from running (likely why ModSecurity was deleting `slider.php`)

**4. Additional File Type Blocking**
- Block access to `.env`, `.git`, `.log`, `.bak`, `.ini` files that could leak secrets

**5. Rate Limiting Hint**
- Add `LimitRequestBody` to cap upload size at server level (10MB)

**6. Caching Headers**
- Add cache-control for static assets (images, CSS, JS) to improve performance

### Technical Details

**File modified:** `php-backend/.htaccess`

The updated file will contain these sections in order:

```text
1. RewriteEngine + HTTPS redirect
2. Sensitive directory blocking (existing, cleaned up)
3. SQL/env/git/log file blocking
4. Directory listing disabled (existing)
5. Self-protection (existing, modernized syntax)
6. PHP execution blocked in uploads/
7. HTTP Security Headers block
8. Upload size limit
9. Static asset caching
```

**Key addition -- uploads protection** (this likely solves the slider.php deletion issue):
```
<Directory "uploads">
    php_flag engine off
    RemoveHandler .php .phtml
    <FilesMatch "\.ph(p|tml)$">
        Require all denied
    </FilesMatch>
</Directory>
```

### No Breaking Changes
- All existing rules are preserved
- Headers are additive and don't affect PHP functionality
- Upload protection only targets the `uploads/` folder, not admin pages

### Files Modified
| File | Description |
|------|-------------|
| `php-backend/.htaccess` | Complete security headers upgrade |

