

## Add "Email / SMTP" Settings Tab to Admin Panel

### What This Does
Adds a new **Email / SMTP** tab in the Admin Settings page so you can configure email settings (SMTP host, port, username, password, sender name, encryption) directly from the browser instead of editing `config/mail.php` on the server.

Also includes a **Send Test Email** button to verify the configuration works.

### How It Works

1. SMTP settings are stored in the `settings` database table (like all other settings), using keys: `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `smtp_from_name`, `smtp_encryption`
2. `config/mail.php` reads from the database first; if no DB values exist, it falls back to the hardcoded defaults
3. Only **Super Admin** can view and edit SMTP settings (passwords are sensitive)
4. A "Send Test Email" button sends a test message to verify the configuration

### Changes

| File | Change |
|------|--------|
| `php-backend/admin/settings.php` | Add new "Email" tab with SMTP form fields, test email button, and form handler for saving SMTP settings |
| `php-backend/config/mail.php` | Load SMTP values from the `settings` table (with fallback to hardcoded defaults) so the `sendMail()` function uses admin-configured values |

### Technical Details

**New tab in settings.php (after "Popup Ad", before "Users"):**
- Tab button with envelope icon and label "Email"
- Form fields: SMTP Host, SMTP Port, SMTP Username, SMTP Password (masked input with show/hide toggle), Sender Name, Encryption (dropdown: ssl/tls/none)
- "Save Email Settings" button
- "Send Test Email" button that sends to the logged-in admin's email
- Restricted to Super Admin only (like Access Control tab)

**Form handler (POST action = `smtp_settings`):**
- Saves all 6 SMTP keys to the `settings` table using the existing INSERT...ON DUPLICATE KEY UPDATE pattern
- Password is stored as-is (not hashed, since it needs to be used for SMTP auth)
- Audit logged as `update_smtp`

**Form handler (POST action = `test_email`):**
- Calls `sendMail()` to the current admin's email with a test subject/body
- Shows success or error flash message

**config/mail.php update:**
- Before defining constants, check if settings table has SMTP values
- Use a try/catch so if DB is unavailable, hardcoded defaults still work
- The `sendMail()` function reads from these constants (no change needed to the function itself)

**Security:**
- SMTP password field uses `type="password"` with a toggle button
- Tab only visible to Super Admin
- CSRF protection on all forms (existing pattern)

