
## Add Custom Email Input to Test Email Section

### Current Behavior
The Email tab's "Test Email" section (lines 867-886) currently sends a test email **only to the current logged-in admin's email address** (`$_SESSION['user_email']`). There's no way to test with a different email address.

### Proposed Change
Add an **email input field** in the "Test Email" card where Super Admins can enter any email address to test SMTP configuration with. This is useful for:
- Testing delivery to different email providers (Gmail, Outlook, Yahoo, etc.)
- Verifying email reaches external addresses (not just the admin account)
- Testing before sending bulk emails to students/teachers

### Implementation Details

**File: `php-backend/admin/settings.php`**

1. **Add form input field** (lines 870-874):
   - Add a text input field with type="email" named `test_email_recipient`
   - Pre-fill with current admin's email as placeholder
   - Add label: "Test Email Recipient"
   - Position it above the "Send Test Email" button

2. **Update backend handler** (lines 212-220):
   - Modify the `test_email` action to check for a custom email recipient from `$_POST['test_email_recipient']`
   - Use custom email if provided, otherwise fall back to `$_SESSION['user_email']`
   - Validate the email format using PHP's `filter_var()` with `FILTER_VALIDATE_EMAIL`
   - Show appropriate error message if custom email is invalid

3. **Update confirmation dialog**:
   - Change the JavaScript confirmation to show the actual recipient email address being used

### Security Considerations
- Email validation on both client and server side to prevent injection attacks
- CSRF token already in place (existing form protection)
- No email logging to prevent exposure in logs
- Only Super Admins can access this feature (already enforced)

### Visual Changes
The Test Email card will now have:
```
Test Email
[Input field: "Enter test email address" with placeholder showing current admin email]
[Send Test Email button]
[Tips section - unchanged]
```

### Files Modified
| File | Change |
|------|--------|
| `php-backend/admin/settings.php` | Add email input field in Test Email form + update backend handler to accept custom email address |

### No Breaking Changes
- Existing functionality preserved - if no custom email is provided, defaults to admin's email
- All existing validations and error handling maintained
- SMTP settings form unchanged
