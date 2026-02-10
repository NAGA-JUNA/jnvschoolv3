

# Enhanced Students, Teachers & Settings Pages

## Overview
Upgrade the Students and Teachers list pages with a "View" button that opens a Bootstrap modal popup showing the full profile (including photo), and enhance the Settings page with additional management features like logo upload, social media links, SMS/WhatsApp config, backup, and user profile editing.

---

## 1. Students Page (`admin/students.php`) Enhancements

### New "View" Button in Actions Column
- Add an eye icon button next to Edit and Delete
- Clicking it opens a Bootstrap modal with all student details

### Student Profile Modal
- **Header**: Profile photo (or default avatar icon), student name, admission number, status badge
- **Personal Info Section**: Father's name, Mother's name, DOB, Gender, Blood Group, Category, Aadhar
- **Academic Info Section**: Class-Section, Roll No, Admission Date
- **Contact Section**: Phone, Email, Address
- **Footer**: Edit button (links to student-form.php) and Close button
- Modal is populated via data attributes on the view button (no AJAX needed -- all data is already fetched in the PHP query)

### Additional Improvements
- Add a "Print" button in the modal to print student profile
- Show photo thumbnail in the table row (small 32px avatar next to name)
- Add export button at top (link to reports page)

---

## 2. Teachers Page (`admin/teachers.php`) Enhancements

### Same Modal Pattern as Students
- Add "View" eye icon button in Actions column
- Bootstrap modal showing full teacher profile:
  - **Header**: Photo/avatar, name, employee ID, status badge
  - **Professional Info**: Subject, Qualification, Experience, Joining Date
  - **Personal Info**: DOB, Gender, Phone, Email, Address
  - **Footer**: Edit and Close buttons

### Additional Improvements
- Show small photo thumbnail next to name in table (if photo exists)
- Add photo upload support to `teacher-form.php` (currently missing -- teachers table has a `photo` column in schema but the form doesn't handle it)

---

## 3. Settings Page (`admin/settings.php`) Enhancements

### New Sections (in addition to existing School Info and User Management):

**School Logo Upload**
- File upload field for school logo
- Stored in `uploads/logo/` directory
- Saved as `school_logo` setting key
- Preview of current logo shown

**Social Media Links**
- Fields for: Facebook URL, Twitter/X URL, Instagram URL, YouTube URL, LinkedIn URL
- Stored as settings keys (`social_facebook`, `social_twitter`, etc.)

**SMS/WhatsApp Configuration**
- WhatsApp API number field
- SMS gateway API key field
- Stored as settings keys

**System Information Card**
- Show PHP version, MySQL version, disk usage
- Show total students, teachers, users count
- Last backup date (if tracked)

**User Management Improvements**
- Add "Edit User" capability (inline modal to change name, role, toggle active/inactive)
- Show active/inactive status with toggle switch
- Password reset button for each user (resets to default `Reset@123`)

**Danger Zone**
- Clear all audit logs (with confirmation)
- Reset settings to default

---

## 4. Teacher Form (`admin/teacher-form.php`) Update
- Add photo upload field (matching student form pattern)
- Upload to `uploads/photos/` with `teacher_` prefix
- Include `photo` in the INSERT/UPDATE queries

---

## Technical Details

### Modal Implementation
- Uses Bootstrap 5.3 native modal component (already loaded via CDN in footer)
- Data passed via `data-*` attributes on the view button, then JavaScript populates the modal fields
- No additional AJAX calls needed -- all record data is in the PHP-rendered page
- Photo displayed with fallback to a Bootstrap Icons person-circle icon if no photo exists

### Files Modified
1. `php-backend/admin/students.php` -- Add view modal, photo thumbnail in table, view button
2. `php-backend/admin/teachers.php` -- Add view modal, photo thumbnail in table, view button
3. `php-backend/admin/teacher-form.php` -- Add photo upload field and handling
4. `php-backend/admin/settings.php` -- Add logo upload, social links, SMS config, system info, enhanced user management

### No Schema Changes Required
- All new settings use the existing `settings` key-value table
- Teachers `photo` column already exists in the schema
- No new tables needed
