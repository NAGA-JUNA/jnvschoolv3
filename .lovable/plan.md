

## Add Popup Modal for Admission Details with Approve/Reject

Currently, clicking "View" opens an off-canvas drawer that slides in from the right. You want a centered popup modal instead, with clear Approve and Reject buttons visible.

---

### What Will Change

**File: `php-backend/admin/admissions.php`**

1. **Replace the off-canvas drawer with a Bootstrap modal popup**
   - Convert the `offcanvas` element to a `modal modal-lg` element
   - The popup will be centered on screen and clearly show all admission details
   - Keep all existing tabs (Details, Docs, Notes, Timeline)

2. **Add prominent Approve and Reject buttons in the modal footer**
   - A green "Approve" button and a red "Reject" button always visible at the bottom of the popup
   - These buttons will only show when the status allows it (hide for already approved/rejected/converted)
   - Additional contextual status buttons (Contact, Docs Verified, Schedule Interview, Waitlist) shown as smaller buttons

3. **Fix missing action buttons on some rows**
   - Looking at your screenshot, rows 2 and 3 (ADM-2026-00002 and ADM-2026-00003) only show the eye icon -- this means their status value in the database doesn't match any of the expected values (new, contacted, documents_verified, etc.). I'll add a fallback so every non-converted row always shows at minimum View + Approve + Reject buttons.

4. **Update JavaScript functions**
   - Change `openDrawer()` to `openModal()` using `bootstrap.Modal` instead of `bootstrap.Offcanvas`
   - Move Approve/Reject into the modal footer as large, clearly labeled buttons
   - Keep the AJAX action handling exactly the same

### Visual Layout of the Popup

```text
+------------------------------------------+
|  ADM-2026-00004          [Status Badge] X |
|  Submitted Feb 26, 2026                  |
|------------------------------------------|
| Details | Docs | Notes | Timeline        |
|------------------------------------------|
|                                          |
|  Student Information                     |
|  Name: Nagarjuna Y                       |
|  Class: 7     DOB: ...                   |
|                                          |
|  Parent Details                          |
|  Father: Nagarjuna   Phone: 8106811171   |
|  ...                                     |
|                                          |
|------------------------------------------|
| [Reject]              [Next Step] [Approve] |
+------------------------------------------+
```

### Technical Notes
- Only `php-backend/admin/admissions.php` is modified
- No schema or backend API changes needed
- All AJAX action endpoints remain the same
