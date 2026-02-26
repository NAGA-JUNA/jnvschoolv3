

## Advanced Admissions Management System -- Complete Overhaul

This is a major feature upgrade that transforms the basic 4-status admission flow into a full-featured, professional admissions pipeline with 8 statuses, multi-step public form, seat management, admin notes/timeline, auto-student-creation, and more.

---

### Phase 1: Database Schema Updates

**File: `php-backend/schema.sql`**

1. **Expand the `admissions` table** with new columns:
   - `application_id` (VARCHAR 20, unique) -- e.g., "ADM-2026-00042"
   - `mother_name`, `aadhar_no`, `blood_group`, `category` (already partially exists)
   - `father_phone`, `father_occupation`, `mother_occupation`
   - `documents` becomes JSON (multiple file paths)
   - `status` ENUM expanded: `new`, `contacted`, `documents_verified`, `interview_scheduled`, `approved`, `rejected`, `waitlisted`, `converted`
   - `interview_date` DATETIME
   - `follow_up_date` DATE
   - `converted_student_id` INT (FK to students)
   - `source` VARCHAR (walk-in, online, referral)
   - `priority` ENUM (normal, high, urgent)

2. **New table: `admission_notes`**
   - `id`, `admission_id` (FK), `user_id` (FK), `note` TEXT, `created_at`
   - Tracks all internal admin notes with timeline

3. **New table: `admission_status_history`**
   - `id`, `admission_id` (FK), `old_status`, `new_status`, `changed_by` (FK), `remarks`, `created_at`
   - Full audit trail of every status change

4. **New table: `class_seat_capacity`**
   - `id`, `class` VARCHAR, `section` VARCHAR, `total_seats` INT, `academic_year` VARCHAR
   - Manages seat availability per class

---

### Phase 2: Public Multi-Step Admission Form

**File: `php-backend/public/admission-form.php`** (complete rewrite)

Multi-step wizard with 4 steps and a visual progress bar:

- **Step 1 -- Student Information**: Name, DOB (with age-class validation), gender, blood group, category, Aadhar, class applied, previous school. Shows **seat availability badge** per class (green/yellow/red).
- **Step 2 -- Parent/Guardian Details**: Father name, occupation, phone; Mother name, occupation, phone; Guardian details; Email; Address (village, district, state, pin).
- **Step 3 -- Document Upload**: Multiple file upload (PDF/Image, max 5MB each) with client-side preview thumbnails. Fields: Birth certificate, Transfer certificate, Report card, Photo, Aadhar card.
- **Step 4 -- Review and Submit**: Summary of all entered data with edit buttons per section. Final submit button.

On success: Shows Application ID (e.g., "ADM-2026-00042") prominently with a printable receipt.

Client-side validation includes:
- Phone format (10 digits)
- DOB vs class age appropriateness warnings
- File type/size validation with preview
- Required field highlighting

All done with vanilla JS (no frameworks), matching the project's existing pattern.

**New file: `php-backend/admin/ajax/admission-actions.php`** -- AJAX handler for:
- Status updates
- Add notes
- Delete admission
- Export single PDF
- Duplicate check (phone/email)
- Seat count queries

---

### Phase 3: Admin Admissions Module

**File: `php-backend/admin/admissions.php`** (complete rewrite)

#### 3a. Dashboard KPI Cards (top section)
Six metric cards at the top:
- New Today | Pending (new + contacted) | Approved | Rejected | Waitlisted | Conversion % (approved/total)

#### 3b. Filter Bar
- Search by name, phone, email
- Class dropdown filter
- Status dropdown filter
- Date range picker (from/to)
- Clear filters button

#### 3c. Status Tabs
Eight tabs with counts: New | Contacted | Docs Verified | Interview Scheduled | Approved | Rejected | Waitlisted | All

#### 3d. Table with Card-Style Rows
Each row shows:
- Application ID
- Student name + father name
- Class applied + seat availability indicator
- Phone
- Status badge (color-coded)
- Date
- Action buttons (view, quick status change)
- Duplicate warning icon if phone/email matches existing records

#### 3e. Slide-In Detail Drawer (Off-Canvas)
Clicking a row opens a Bootstrap off-canvas panel showing:
- **Header**: Application ID, status badge, submitted date
- **Tabs within drawer**: Details | Documents | Notes | Timeline
  - **Details tab**: Student info, parent info, address in organized cards
  - **Documents tab**: Uploaded document previews with download links
  - **Notes tab**: Admin notes with add-note form, chronological list
  - **Timeline tab**: Status change history with timestamps and user names
- **Action bar at bottom**: Status workflow buttons (contextual based on current status), Follow-up date picker

#### 3f. Auto-Create Student on Approval
When status changes to "approved," a confirmation modal appears asking to create a student record. Pre-fills the student form from admission data and inserts into the `students` table. Links back via `converted_student_id`.

#### 3g. Export Features
- **CSV/Excel export** of filtered admissions list
- **PDF download** of individual application (formatted printable version)

#### 3h. Duplicate Detection
On form load, AJAX checks for existing admissions/students with same phone or email. Shows warning banner in the detail drawer.

---

### Phase 4: Seat Capacity Management

**New file: `php-backend/admin/seat-capacity.php`**
- Simple admin page to set total seats per class per academic year
- Grid view showing class, total seats, filled (from approved admissions + active students), available
- Auto-waitlist logic: when a class reaches capacity, new approvals trigger a "seats full" warning

The public form queries available seats and shows colored badges:
- Green: 10+ seats available
- Yellow: 1-9 seats available
- Red: Full (auto-waitlist message)

---

### Phase 5: Email Notifications

**Updates to `php-backend/config/mail.php`** (use existing `sendMail` function)

Trigger emails on status changes:
- **Approved**: Congratulatory email with next steps
- **Rejected**: Polite rejection with feedback
- **Waitlisted**: Waitlist notification with position info
- **Interview Scheduled**: Date/time/venue details

Email templates are simple HTML using the school branding from settings.

---

### Phase 6: UI/UX Enhancements

All new pages follow the existing design system:
- CSS custom properties for theming (light/dark mode automatic)
- Soft shadows, rounded containers (matches existing `border-0 rounded-3` pattern)
- Bootstrap 5.3 components throughout
- Responsive design with mobile breakpoints
- Card-style table rows with hover effects
- Smooth transitions and subtle animations
- No heavy JS frameworks -- vanilla JS only

---

### Files Created/Modified Summary

| File | Action | Description |
|------|--------|-------------|
| `php-backend/schema.sql` | Modify | Add new tables + expand admissions columns |
| `php-backend/public/admission-form.php` | Rewrite | Multi-step wizard with progress bar |
| `php-backend/admin/admissions.php` | Rewrite | Full admin module with KPIs, filters, drawer |
| `php-backend/admin/ajax/admission-actions.php` | Create | AJAX handler for all admission operations |
| `php-backend/admin/seat-capacity.php` | Create | Seat management page |
| `php-backend/admin/dashboard.php` | Modify | Update admission KPI to use new statuses |
| `php-backend/includes/header.php` | Modify | Update sidebar pending count query for new statuses |

