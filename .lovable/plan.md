

## Fix Forgot Password — Email Not Sending

### Root Cause (2 Issues)

**Issue 1: `sendMail()` ignores SMTP settings**
The `config/mail.php` file defines SMTP constants (`SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`) but the `sendMail()` function uses PHP's built-in `mail()`, which completely ignores them. On shared hosting (cPanel), `mail()` typically fails silently or emails land in spam.

**Issue 2: Placeholder password**
`SMTP_PASS` is still set to `'YOUR_EMAIL_PASSWORD'` — it was never configured with the actual email password.

### Fix

**File: `php-backend/config/mail.php`**

Replace the `sendMail()` function to use proper SMTP via PHP sockets (no external library needed on cPanel), or use cPanel's built-in PHPMailer if available. The most reliable approach for cPanel shared hosting:

**Option A (Recommended): Use PHPMailer**
- Add PHPMailer (most cPanel hosts have it pre-installed, or include it manually as 3 PHP files)
- Update `sendMail()` to create a PHPMailer instance using the defined SMTP constants
- This guarantees proper SSL/TLS SMTP delivery through `mail.awayindia.com:465`

**Option B (Simpler): Use PHP `mail()` with proper server config**
- If the cPanel server's sendmail is already configured, `mail()` can work, but the current code already does this and it's failing
- Less reliable than Option A

### Implementation (Option A — PHPMailer)

1. Download PHPMailer files (`PHPMailer.php`, `SMTP.php`, `Exception.php`) into `php-backend/includes/phpmailer/`
2. Rewrite `config/mail.php` to:
   - Require the 3 PHPMailer class files
   - Keep the existing SMTP constants (update password placeholder)
   - Replace `sendMail()` to use `PHPMailer\PHPMailer\PHPMailer` with:
     - `SMTPAuth = true`
     - `Host = SMTP_HOST` (mail.awayindia.com)
     - `Port = SMTP_PORT` (465)
     - `SMTPSecure = 'ssl'`
     - `Username = SMTP_USER`
     - `Password = SMTP_PASS`
3. Update `SMTP_PASS` — you will need to provide the real email password for `noreply@jnvschool.awayindia.com` (set it up in cPanel Email Accounts)

### Action Required From You
- **Set the real email password**: Go to cPanel > Email Accounts > find `noreply@jnvschool.awayindia.com` > set/reset its password, then update `SMTP_PASS` in `config/mail.php`

### Files Modified
| File | Change |
|------|--------|
| `php-backend/config/mail.php` | Replace `mail()` with PHPMailer SMTP; update password placeholder |
| `php-backend/includes/phpmailer/PHPMailer.php` | New — PHPMailer class |
| `php-backend/includes/phpmailer/SMTP.php` | New — SMTP transport class |
| `php-backend/includes/phpmailer/Exception.php` | New — Exception class |

### No Other Files Need Changes
`forgot-password.php` and `reset-password.php` logic is correct — the only problem is that `sendMail()` never actually delivers the email.

