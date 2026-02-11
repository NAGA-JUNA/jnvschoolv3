# JNV School Management System — PHP + MySQL (cPanel Ready)

## 📋 Overview
A complete school management system built with **pure PHP 8+** and **MySQL**. No Node.js, no React, no terminal commands needed. Upload directly to cPanel shared hosting.

**Domain:** `jnvschool.awayindia.com`
**Schema Version:** v3.0 (13 tables)

---

## 🚀 Deployment Guide (cPanel)

### Step 1: Create Database
1. Log in to **cPanel** → **MySQL® Databases**
2. Create a new database: `yshszsos_jnvschool`
3. Create a database user: `yshszsos_Admin`
4. Add the user to the database with **ALL PRIVILEGES**
5. Note: If the database already exists, skip to Step 2

### Step 2: Import Schema
1. Go to **cPanel** → **phpMyAdmin**
2. Select your database (`yshszsos_jnvschool`)
3. Click the **Import** tab at the top
4. Click **Choose File** → select `schema.sql`
5. Leave format as **SQL** (default)
6. Click **Go** to import
7. ✅ This creates all **13 tables** + default admin user + school settings + sample slider data

> **⚠️ WARNING:** The schema uses `DROP TABLE IF EXISTS` — importing on an existing database will **DELETE all existing data**. Always **back up first** using phpMyAdmin → Export before re-importing!

#### What gets created:
| # | Table | Description |
|---|-------|-------------|
| 1 | `users` | Admin/teacher/office accounts |
| 2 | `students` | Student records with photos |
| 3 | `teachers` | Teacher records linked to user accounts |
| 4 | `admissions` | Online admission applications |
| 5 | `notifications` | Notifications with approval workflow, targeting & visibility |
| 6 | `notification_reads` | Per-user read tracking for notifications |
| 7 | `gallery_items` | Gallery uploads with approval |
| 8 | `events` | School events/calendar |
| 9 | `attendance` | Daily attendance by class |
| 10 | `exam_results` | Exam marks with auto-grading |
| 11 | `audit_logs` | System action logs |
| 12 | `settings` | Key-value school settings |
| 13 | `home_slider` | Homepage slider with animations & overlays |

### Step 3: Upload Files
1. Go to **cPanel** → **File Manager** → `public_html`
2. **Delete** any existing files (or move to a backup folder)
3. Upload **ALL** files and folders from the `php-backend/` directory
4. **Do NOT upload** `schema.sql` or `README.md` to the server (they are for setup only)
5. Your `public_html` structure should look like:

```
public_html/
├── .htaccess              ← Security rules
├── index.php              ← Public homepage with dynamic slider
├── login.php              ← Login page
├── logout.php             ← Logout handler
├── forgot-password.php    ← Password reset request
├── reset-password.php     ← Password reset form
├── config/
│   ├── db.php             ← Database credentials
│   └── mail.php           ← SMTP email config
├── includes/
│   ├── auth.php           ← Session auth, CSRF, roles
│   ├── header.php         ← Admin/teacher layout header
│   └── footer.php         ← Layout footer
├── admin/
│   ├── dashboard.php      ← Admin dashboard with charts
│   ├── students.php       ← Student list (search/filter/paginate)
│   ├── student-form.php   ← Add/edit student with photo
│   ├── import-students.php ← Bulk CSV import for students
│   ├── sample-students-csv.php ← Download student CSV template
│   ├── teachers.php       ← Teacher list
│   ├── teacher-form.php   ← Add/edit teacher
│   ├── import-teachers.php ← Bulk CSV import for teachers
│   ├── sample-teachers-csv.php ← Download teacher CSV template
│   ├── admissions.php     ← Approve/reject admissions
│   ├── notifications.php  ← Multi-tab notification management
│   ├── gallery.php        ← Approve/reject gallery uploads
│   ├── events.php         ← CRUD events
│   ├── slider.php         ← Advanced home slider management
│   ├── reports.php        ← CSV exports
│   ├── audit-logs.php     ← Searchable audit log viewer
│   ├── settings.php       ← School settings + user management
│   └── support.php        ← Support/help page
├── teacher/
│   ├── dashboard.php      ← Teacher overview
│   ├── attendance.php     ← Mark attendance by class
│   ├── exams.php          ← Enter exam marks
│   ├── post-notification.php ← Submit notification with targeting
│   └── upload-gallery.php ← Upload photos/videos
├── public/
│   ├── notifications.php  ← Public notification board
│   ├── gallery.php        ← Public gallery with lightbox
│   ├── events.php         ← Upcoming events
│   └── admission-form.php ← Online admission application
└── uploads/               ← Must be created manually
    ├── photos/            ← Student photos
    ├── gallery/           ← Gallery images
    ├── slider/            ← Slider images
    ├── documents/         ← Admission & notification documents
    └── logo/              ← School logo upload
```

### Step 4: Create Upload Directories
**CRITICAL:** You must manually create the upload directories in cPanel File Manager:

1. Navigate to `public_html/`
2. Create folder: `uploads`
3. Inside `uploads/`, create these subfolders:
   - `photos` — Student profile photos
   - `gallery` — Gallery images
   - `slider` — Homepage slider images (upload 5 images named slide1.jpg to slide5.jpg for sample data)
   - `documents` — Admission form & notification attachments
   - `logo` — School logo upload

### Step 5: Set File Permissions
In **cPanel** → **File Manager**, right-click each item → **Change Permissions**:

| Path | Permission | Why |
|------|-----------|-----|
| `uploads/` | **755** | Writable for file uploads |
| `uploads/photos/` | **755** | Writable for student photos |
| `uploads/gallery/` | **755** | Writable for gallery images |
| `uploads/slider/` | **755** | Writable for slider images |
| `uploads/documents/` | **755** | Writable for documents |
| `uploads/logo/` | **755** | Writable for school logo |
| `config/` | **755** | Readable by PHP |
| All `.php` files | **644** | Standard PHP permissions |
| `.htaccess` | **644** | Apache config file |

### Step 6: Configure Database Connection
Open `config/db.php` and verify credentials match your cPanel MySQL setup:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'yshszsos_jnvschool');
define('DB_USER', 'yshszsos_Admin');
define('DB_PASS', 'your_password_here');
```

### Step 7: Configure Email (Optional)
Open `config/mail.php` and update with your cPanel email:
```php
define('SMTP_HOST', 'mail.jnvschool.awayindia.com');
define('SMTP_USER', 'noreply@jnvschool.awayindia.com');
define('SMTP_PASS', 'your_email_password');
```

### Step 8: Upload Slider Images
The schema includes 5 sample slider entries. Upload corresponding images:

1. Go to `public_html/uploads/slider/`
2. Upload 5 images with these exact names:
   - `slide1.jpg` — Welcome/hero image
   - `slide2.jpg` — Academic excellence
   - `slide3.jpg` — Campus/facilities
   - `slide4.jpg` — Sports/activities
   - `slide5.jpg` — Admissions banner
3. Recommended size: **1920×800px** or wider (landscape)
4. Or add slides via Admin → Home Slider after setup

### Step 9: Test & Verify
1. Visit `https://jnvschool.awayindia.com` → Should show public homepage with slider
2. Visit `https://jnvschool.awayindia.com/login.php` → Login page
3. Login with default credentials (see below)
4. **⚠️ Immediately change the default admin password!**
5. Go to Admin → Settings to upload your school logo
6. Go to Admin → Home Slider to manage slides

---

## 🔑 Default Login Credentials
| Field | Value |
|-------|-------|
| **Email** | `admin@school.com` |
| **Password** | `Admin@123` |
| **Role** | Super Admin |

> **⚠️ SECURITY:** Change this password immediately after first login via Admin → Settings → User Management

---

## 👥 User Roles

| Role | Access Level |
|------|-------------|
| **super_admin** | Full access: all modules + settings + user creation |
| **admin** | Full access to all modules |
| **office** | Same as admin (front-office staff) |
| **teacher** | Dashboard, notifications, gallery, attendance, exams only |

---

## 🔒 Security Features
- ✅ `password_hash()` / `password_verify()` — bcrypt password hashing
- ✅ CSRF tokens on all forms
- ✅ `session_regenerate_id(true)` on login
- ✅ `htmlspecialchars()` output escaping (XSS prevention)
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ Role-based access control middleware
- ✅ `.htaccess` blocks direct access to `config/` and `includes/`
- ✅ Audit logging for all admin/teacher actions
- ✅ Soft-delete pattern for notifications (data preservation)

---

## 📊 Feature Summary

### Admin Panel
- **Dashboard** — 6 KPI cards, Chart.js monthly trends (admissions + attendance), recent activity feed, quick actions
- **Students** — Full CRUD with search, class/status filters, photo upload, pagination, CSV export, bulk CSV import
- **Teachers** — Full CRUD with auto user-account creation, search, pagination, bulk CSV import
- **Admissions** — Status tabs (pending/approved/rejected/waitlisted), approve/reject actions
- **Notifications** — Multi-tab management (Pending/Approved/Rejected/Pinned/All), targeting, visibility channels (popup/banner/marquee/dashboard), priority levels, soft-delete, CSV export
- **Gallery** — Approve/reject uploads, image preview, delete
- **Events** — Add/edit/delete events with date, time, location
- **Home Slider** — Advanced management with animations (Fade/Slide/Zoom/Ken Burns), overlay styles, text positioning, live preview, duplicate slides, stats dashboard
- **Reports** — CSV export for students, teachers, admissions, attendance
- **Audit Logs** — Searchable, date-filterable, paginated log of all system actions
- **Settings** — School info, logo upload, social media links, user management, password reset, danger zone (clear logs)

### Teacher Panel
- **Dashboard** — Personal stats, recent submissions, quick actions
- **Attendance** — Select class + date, bulk mark present/absent/late with "All Present" shortcut
- **Exams** — Enter marks by class/exam/subject with auto-grading (A+ to F)
- **Notifications** — Submit for admin approval with targeting, file attachments, view history
- **Gallery** — Upload images or YouTube videos for approval

### Public Website
- **Homepage** — Dynamic hero slider with animations & overlays, school logo, stats bar, latest notifications, upcoming events, contact info
- **Notifications** — Public notification board with type badges and priority indicators
- **Gallery** — Filterable grid with lightbox viewer, YouTube embeds
- **Events** — Upcoming + past events with date cards
- **Admission Form** — Full online application with document upload

### Bulk Import (CSV)
- **Student Import** — Upload CSV with headers: `admission_no, name, father_name, mother_name, dob, gender, class, section, roll_no, phone, email, address, blood_group, category, aadhar_no, status, admission_date`
- **Teacher Import** — Upload CSV with headers: `employee_id, name, email, phone, subject, qualification, experience_years, dob, gender, address, joining_date, status`
- Both include: download sample template, duplicate detection, error logging, progress tracking

---

## 🔄 Upgrading from Schema v2.0

If you already have v2.0 running and **don't want to lose data**, run these ALTER statements in phpMyAdmin instead of re-importing the full schema:

```sql
-- Add new columns to home_slider
ALTER TABLE `home_slider`
  ADD COLUMN `animation_type` VARCHAR(20) NOT NULL DEFAULT 'fade' AFTER `cta_text`,
  ADD COLUMN `overlay_style` VARCHAR(20) NOT NULL DEFAULT 'gradient-dark' AFTER `animation_type`,
  ADD COLUMN `text_position` VARCHAR(10) NOT NULL DEFAULT 'left' AFTER `overlay_style`,
  ADD COLUMN `overlay_opacity` INT NOT NULL DEFAULT 70 AFTER `text_position`;

-- Add teacher core team fields
ALTER TABLE `teachers`
  ADD COLUMN `designation` VARCHAR(100) DEFAULT 'Teacher' AFTER `name`,
  ADD COLUMN `is_core_team` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `bio` TEXT DEFAULT NULL AFTER `is_core_team`;

-- Add social media settings (if not already present)
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('facebook_url', ''),
('twitter_url', ''),
('instagram_url', ''),
('youtube_url', '');
```

> If the notifications columns (priority, target_audience, etc.) already exist, skip those — they were added in v2.0.

---

## 🔧 Troubleshooting

### "500 Internal Server Error"
- Check PHP version: requires **PHP 8.0+**
- Verify `config/db.php` has correct credentials
- Check `.htaccess` is uploaded (enable "Show Hidden Files" in File Manager)

### "Access Denied" on login
- Verify the database was imported correctly
- Check that the `users` table has the default admin record
- Try re-importing `schema.sql`

### File uploads not working
- Verify `uploads/` subdirectories exist with **755** permissions
- Check PHP `upload_max_filesize` in cPanel → **MultiPHP INI Editor** (set to at least 10M)
- Check PHP `post_max_size` (set to at least 12M)

### Pages show blank or broken layout
- Ensure ALL files were uploaded, including `includes/` folder
- Check that `config/db.php` has the right database name
- Verify the database tables were created (check phpMyAdmin)

### Slider images not showing
- Ensure images are uploaded to `uploads/slider/`
- Check that image filenames match the `image_path` in the `home_slider` table
- Recommended image size: **1920×800px** (landscape)
- Supported formats: JPG, PNG, WebP

### School logo not showing
- Upload logo via Admin → Settings → School Logo
- Ensure `uploads/logo/` directory exists with **755** permissions
- Logo appears in navbar and footer automatically

### Email not sending
- Use cPanel email accounts for SMTP
- Verify SMTP credentials in `config/mail.php`
- Check if your hosting provider blocks port 587/465

---

## 📧 Email Setup (cPanel)

1. **Create email account** in cPanel → Email Accounts (e.g., `noreply@jnvschool.awayindia.com`)
2. Update `config/mail.php` with the credentials
3. For PHPMailer support: download PHPMailer from GitHub, place in `vendor/`, update mail config

---

## 🗄️ Database Schema (v3.0)

13 tables total:
1. `users` — Admin/teacher/office accounts
2. `students` — Student records with photos
3. `teachers` — Teacher records linked to user accounts
4. `admissions` — Online admission applications
5. `notifications` — Notifications with approval workflow, targeting & visibility channels
6. `notification_reads` — Per-user read tracking
7. `gallery_items` — Gallery uploads with approval
8. `events` — School events/calendar
9. `attendance` — Daily attendance by class
10. `exam_results` — Exam marks with auto-grading
11. `audit_logs` — System action logs
12. `settings` — Key-value school settings (including logo, social links)
13. `home_slider` — Homepage slider with animations, overlays & text positioning

---

*Built for JNV School — jnvschool.awayindia.com*