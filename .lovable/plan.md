

# Complete PHP + MySQL School Management System Rebuild

## Overview
Rebuild the entire `php-backend/` directory with a modern, production-ready school management system featuring split-screen auth pages, Chart.js dashboard with trends, audit logs viewer, and your real database credentials -- all ready to upload directly to cPanel at `jnvschool.awayindia.com`.

---

## Design Decisions

### Auth Pages (Split Screen)
- Left panel: school branding with gradient background, school name, tagline, and decorative illustration
- Right panel: clean login/forgot-password/reset-password form
- Responsive: on mobile, left panel becomes a top banner

### Dashboard
- 6 KPI cards with icons (matching the React layout: Students, Teachers, Pending Admissions, Pending Notifications, Pending Gallery, Upcoming Events)
- Chart.js bar/line chart showing monthly admissions and attendance trends for the current academic year
- Recent Activity table (last 15 audit logs)
- Quick Actions panel with icon links to common tasks
- Calendar showing today's date and upcoming events

### All Admin & Teacher Pages
- Every existing page will be rewritten with the updated header/footer, consistent Bootstrap 5.3 styling, and working CRUD
- New page: `admin/audit-logs.php` with search, date filter, and pagination

---

## Database

### Updated schema.sql
- Same 10 tables as current (users, students, teachers, admissions, notifications, gallery_items, events, attendance, exam_results, audit_logs, settings)
- Add a `home_slider` table (exists in the main schema.sql but missing from php-backend)
- Default admin user: `admin@school.com` / `Admin@123`
- Default school settings pre-filled for JNV School

### db.php
- Will include your production credentials:
  - Host: localhost
  - DB: yshszsos_jnvschool
  - User: yshszsos_Admin
  - Password: (your provided password)

---

## Files to Create/Rewrite (30+ files)

### Config
1. `config/db.php` -- production credentials
2. `config/mail.php` -- SMTP config with jnvschool.awayindia.com domain

### Core Includes
3. `includes/auth.php` -- session management, CSRF, role checks, audit logging, flash messages (enhanced)
4. `includes/header.php` -- modern sidebar with active state detection, mobile hamburger menu, top bar with user dropdown, school branding from DB
5. `includes/footer.php` -- close layout + Bootstrap JS + Chart.js CDN

### Auth Pages (Split Screen Design)
6. `login.php` -- split-screen, CSRF, session regeneration
7. `forgot-password.php` -- split-screen, email reset link
8. `reset-password.php` -- split-screen, token validation
9. `logout.php` -- session destroy + audit log
10. `index.php` -- redirect to dashboard or login

### Admin Pages
11. `admin/dashboard.php` -- KPI cards, Chart.js trends, recent activity, quick actions
12. `admin/students.php` -- list with search, status/class filters, pagination, delete
13. `admin/student-form.php` -- add/edit with all fields, photo upload
14. `admin/teachers.php` -- list with search, pagination, delete
15. `admin/teacher-form.php` -- add/edit with all fields
16. `admin/admissions.php` -- list with status tabs, approve/reject/waitlist
17. `admin/notifications.php` -- list with approve/reject, delete
18. `admin/gallery.php` -- list with approve/reject, delete, image preview
19. `admin/events.php` -- inline add/edit form + list
20. `admin/reports.php` -- CSV export for students, teachers, admissions, attendance
21. `admin/settings.php` -- school info, create user, user list
22. `admin/audit-logs.php` -- NEW: searchable, date-filterable, paginated audit log viewer

### Teacher Pages
23. `teacher/dashboard.php` -- welcome card, KPI stats
24. `teacher/post-notification.php` -- form + my submissions list
25. `teacher/upload-gallery.php` -- file upload form
26. `teacher/attendance.php` -- class/date picker, mark attendance
27. `teacher/exams.php` -- class picker, enter marks with auto-grading

### Public Pages
28. `public/notifications.php` -- approved public notifications
29. `public/gallery.php` -- approved gallery grid with lightbox
30. `public/events.php` -- upcoming public events
31. `public/admission-form.php` -- online application

### Root Files
32. `schema.sql` -- complete database schema with home_slider table
33. `README.md` -- deployment guide for cPanel
34. `.htaccess` -- security rules (block config/, includes/ access)

---

## Technical Details

### Header/Sidebar Improvements
- Collapsible sidebar with hamburger toggle on mobile
- Active link highlighting based on current URL path
- Top bar showing logged-in user name, role badge, and logout button
- School name/logo pulled dynamically from settings table

### Dashboard Chart Implementation
- Chart.js loaded via CDN (no npm needed)
- Two datasets: Monthly Admissions count and Monthly Attendance rate
- Data queried via PHP `GROUP BY MONTH()` for the current year
- Responsive canvas that works on all screen sizes

### Audit Logs Page
- Search by action, user name, or entity type
- Date range filter (from/to)
- Pagination (25 per page)
- Shows: User, Action, Entity, Details, IP Address, Timestamp

### Security
- All forms use CSRF tokens
- `password_hash()` / `password_verify()` for auth
- `session_regenerate_id(true)` on login
- All output escaped with `htmlspecialchars()`
- `.htaccess` to deny direct access to config/ and includes/
- Prepared statements (PDO) for all queries
- Role-based middleware on every admin/teacher page

### Photo Upload Support
- Student form gets file upload for photo
- Uploads stored in `uploads/photos/` with unique filenames
- Max 5MB, accepts jpg/jpeg/png/webp

