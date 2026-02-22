

## Fix: Google Maps Iframe Blocked by Content Security Policy

### Problem

The `.htaccess` file (line 50) has a `Content-Security-Policy` header with `default-src 'self'` but no explicit `frame-src` directive. This means iframes can only load content from the same domain. Google Maps embeds from `https://www.google.com` are blocked by the browser, causing the "not allowed" icon.

### Solution

**File: `php-backend/.htaccess`** (line 50)

Add `frame-src 'self' https://www.google.com https://maps.google.com;` to the Content-Security-Policy header. This allows Google Maps embed iframes to load while keeping the rest of the security policy intact.

The updated CSP line will include:
- `frame-src 'self' https://www.google.com https://maps.google.com;` -- allows Google Maps embeds
- All other directives remain unchanged

### Files Changed

| File | Change |
|------|--------|
| `php-backend/.htaccess` | Add `frame-src` directive to CSP header (1 line modified) |

No other files need changes -- the PHP code and iframe markup are correct. The map will appear once the CSP allows Google Maps iframes.

