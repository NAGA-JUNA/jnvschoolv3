

## Add Quick Action Buttons to Admissions Table

Currently, each admission row only shows a "view" (eye) button. All status-change actions are hidden inside the drawer panel. The user needs visible Approve/Reject/Contact buttons directly in the table for faster workflow.

---

### Changes to `php-backend/admin/admissions.php`

**1. Expand the Actions column in the table (around line 264-266)**

Replace the single eye button with a button group that includes:
- **View** (eye icon) -- opens the drawer (existing)
- **Approve** (check icon, green) -- quick approve with confirmation
- **Reject** (X icon, red) -- quick reject with confirmation
- **Next logical status** button based on current status (e.g., "Contacted" for new applications, "Docs Verified" for contacted ones)

The buttons will use the existing `ajaxAction()` function to perform AJAX status updates without page reload.

Each row will show contextual buttons based on the current status:
- `new` -> View, Contact, Reject
- `contacted` -> View, Docs Verified, Reject
- `documents_verified` -> View, Schedule Interview, Approve, Reject
- `interview_scheduled` -> View, Approve, Reject, Waitlist
- `waitlisted` -> View, Approve, Reject
- `approved` -> View, Create Student
- `rejected` -> View, Reopen (set back to New)
- `converted` -> View only

**2. Add a remarks prompt for Reject action**

When clicking Reject from the table, show a simple `prompt()` dialog asking for optional rejection remarks before executing.

**3. Add tooltip labels to all action buttons**

Each small icon button gets a `title` attribute so hovering reveals the action name.

---

### Technical Details

Only one file is modified: `php-backend/admin/admissions.php`

The actions column markup (lines 264-266) will be expanded from:
```html
<button class="btn btn-outline-primary btn-sm" onclick="openDrawer(id)"><i class="bi bi-eye"></i></button>
```
To a button group with 2-4 contextual action buttons per row, all using the existing `ajaxAction()` JS function that already handles AJAX calls, CSRF tokens, drawer reload, and page refresh.

No new files, no schema changes, no new endpoints needed -- all backend handling already exists in `admission-actions.php`.

