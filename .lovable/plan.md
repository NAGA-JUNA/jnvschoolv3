

## Maintenance Mode Toggle

### Overview
Add a Maintenance Mode on/off toggle in the admin Settings page. When enabled, all public visitors will see a professional "Site Under Maintenance" page. Admins and teachers who are logged in can still access the backend normally.

### How It Works
- A new toggle switch in the **General** tab of Settings lets you turn Maintenance Mode on or off
- When ON, any visitor who is **not logged in** will see a full-screen maintenance page instead of the website
- Logged-in admins and teachers can browse the site normally (so you never lock yourself out)
- The maintenance page will show the school name, logo, and a friendly message

---

### Changes

**1. Settings Page -- Add toggle (General tab)**
File: `php-backend/admin/settings.php`

- Add a "Maintenance Mode" on/off toggle switch in the General tab (near the top, after School Name or before the Save button)
- The toggle saves a setting key called `maintenance_mode` with value `1` (on) or `0` (off)
- Add a small warning text: "When enabled, public visitors will see a maintenance page. Admins remain unaffected."

**2. Save handler -- Handle the new toggle**
File: `php-backend/admin/settings.php`

- Add `maintenance_mode` to the list of keys saved in the `settings` action handler (the `$keys` array on line 91)
- Use checkbox logic: `isset($_POST['maintenance_mode']) ? '1' : '0'`

**3. Auth include -- Add maintenance check**
File: `php-backend/includes/auth.php`

- At the bottom of the file, add a new function `checkMaintenance()` that:
  - Reads `getSetting('maintenance_mode', '0')`
  - If `1` AND the user is NOT logged in, display a full-screen maintenance HTML page and `exit`
  - Logged-in users pass through without interruption

**4. Public pages -- Call the check**
Files: `php-backend/index.php` and all files in `php-backend/public/` (about.php, teachers.php, gallery.php, events.php, notifications.php, fee-structure.php, admission-form.php, certificates.php)

- Add `checkMaintenance();` near the top of each file (right after the `require_once` for auth.php)
- This single line activates the gate on every public page

**5. Login page -- Allow access**
File: `php-backend/login.php`

- No maintenance check here -- admins must still be able to log in

---

### Maintenance Page Design
The maintenance page will be a self-contained HTML page (no external dependencies needed) showing:
- School logo (if available)
- School name
- "We'll be back soon!" heading
- A short message: "Our website is currently undergoing scheduled maintenance."
- A subtle animation or icon for visual appeal

---

### Technical Details

**New setting key:** `maintenance_mode` (values: `0` or `1`, default: `0`)

**`checkMaintenance()` function logic:**
```text
if maintenance_mode == '1' AND user is NOT logged in:
    show maintenance HTML page
    exit
```

**Files modified:** 
- `php-backend/includes/auth.php` (add function)
- `php-backend/admin/settings.php` (add toggle + save logic)
- `php-backend/index.php` (add one-line call)
- 8 public page files (add one-line call each)

**No database schema changes needed** -- uses the existing `settings` table.

