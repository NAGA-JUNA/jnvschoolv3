# JNV School Management System — PHP + MySQL (cPanel Ready)

## 📋 Overview

A complete school management system built with **pure PHP 8+** and **MySQL**. No Node.js, no React, no terminal commands needed. Upload directly to cPanel shared hosting.

**Domain:** `jnvschool.awayindia.com`  
**Schema Version:** v3.5 (28 tables)

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
7. ✅ This creates all **28 tables** + default admin user + school settings + sample slider data + nav menu + demo gallery categories + feature cards + popup ads + enquiries + core team

> **⚠️ WARNING:** The schema uses `DROP TABLE IF EXISTS` — importing on an existing database will **DELETE all existing data**. Always **back up first** using phpMyAdmin → Export before re-importing!

#### What gets created:

| # | Table | Description |
|---|-------|-------------|
| 1 | `users` | Admin/teacher/office accounts (bcrypt passwords, roles, reset tokens) |
| 2 | `students` | Student records with photos, class, section, admission details |
| 3 | `teachers` | Teacher records linked to user accounts (designation, core team, bio) |
| 4 | `admissions` | Online admission applications with document uploads |
| 5 | `notifications` | Notifications with approval workflow, targeting, visibility channels |
| 6 | `notification_reads` | Per-user read tracking for notifications |
| 7 | `notification_versions` | Edit history with restore support for notifications |
| 8 | `notification_attachments` | Multi-file attachment support for notifications |
| 9 | `gallery_items` | Gallery uploads with approval, batch support, compression tracking |
| 10 | `gallery_categories` | Gallery categories (Academic, Cultural, Sports, etc.) |
| 11 | `gallery_albums` | Albums within categories with cover images |
| 12 | `events` | School events/calendar with type, status, poster, featured flag, view tracking |
| 13 | `attendance` | Daily attendance by class (present/absent/late/excused) |
| 14 | `exam_results` | Exam marks with auto-grading (A+ to F scale) |
| 15 | `audit_logs` | System action logs with IP, user, entity tracking |
| 16 | `settings` | Key-value school settings (~80+ keys for all configurations) |
| 17 | `home_slider` | Homepage slider with animations (fade/slide/zoom/kenburns) & overlays |
| 18 | `site_quotes` | Inspirational quotes for About page |
| 19 | `leadership_profiles` | Leadership/principal profiles for About page |
| 20 | `nav_menu_items` | Admin-managed navbar menu with drag-and-drop ordering |
| 21 | `certificates` | School certificates & accreditations (image/PDF) |
| 22 | `feature_cards` | Homepage quick-link cards with icons, colors, badges, analytics |
| 23 | `fee_structures` | Class-wise fee structures by academic year |
| 24 | `fee_components` | Individual fee line items with frequency & optional flag |
| 25 | `popup_ads` | Homepage popup advertisements with scheduling & targeting |
| 26 | `popup_analytics` | Popup ad view/click tracking by date |
| 27 | `enquiries` | Website enquiry submissions with status tracking (new/contacted/closed) |
| 28 | `core_team` | Independent core team members with display order, visibility, featured flag |

### Step 3: Upload Files
1. Go to **cPanel** → **File Manager** → `public_html`
2. **Delete** any existing files (or move to a backup folder)
3. Upload **ALL** files and folders from the `php-backend/` directory
4. **Do NOT upload** `schema.sql` or `README.md` to the server (they are for setup only)
5. Your `public_html` structure should look like:

```
public_html/
├── .htaccess                  ← Security rules (blocks config/, includes/ access)
├── index.php                  ← Public homepage with dynamic slider, feature cards, core team
├── login.php                  ← Login page (split-screen design)
├── logout.php                 ← Logout handler
├── forgot-password.php        ← Password reset request (email-based)
├── reset-password.php         ← Password reset form (token-validated)
├── config/
│   ├── db.php                 ← Database credentials (DB_HOST, DB_NAME, DB_USER, DB_PASS)
│   └── mail.php               ← SMTP email config (host, user, pass, port, encryption)
├── includes/
│   ├── auth.php               ← Session auth, CSRF tokens, role checks, maintenance mode gate
│   ├── file-handler.php       ← File upload handler (validation, directory creation)
│   ├── header.php             ← Admin/teacher layout header (sidebar nav)
│   ├── footer.php             ← Admin/teacher layout footer
│   ├── public-navbar.php      ← Public website navbar (dynamic menu from nav_menu_items)
│   ├── public-footer.php      ← Public website footer (dynamic from footer settings)
│   └── phpmailer/             ← PHPMailer library
│       ├── Exception.php
│       ├── PHPMailer.php
│       └── SMTP.php
├── admin/
│   ├── dashboard.php          ← Admin dashboard with KPI cards, Chart.js trends, quick actions
│   ├── students.php           ← Student list (search, filter, paginate, CSV export)
│   ├── student-form.php       ← Add/edit student with photo upload
│   ├── import-students.php    ← Bulk CSV import for students
│   ├── sample-students-csv.php ← Download student CSV template
│   ├── teachers.php           ← Teacher list with search, pagination
│   ├── teacher-form.php       ← Add/edit teacher (auto user-account creation)
│   ├── import-teachers.php    ← Bulk CSV import for teachers
│   ├── sample-teachers-csv.php ← Download teacher CSV template
│   ├── admissions.php         ← Approve/reject admission applications
│   ├── notifications.php      ← Multi-tab notification management
│   ├── gallery.php            ← Approve/reject gallery uploads, category/album management
│   ├── upload-gallery.php     ← Direct gallery upload from admin
│   ├── events.php             ← CRUD events with type, date, location
│   ├── enquiries.php          ← Enquiry management (list, filter, search, status, CSV export)
│   ├── slider.php             ← Advanced home slider management
│   ├── certificates.php       ← Upload/manage school certificates & accreditations
│   ├── feature-cards.php      ← Manage homepage quick-link cards
│   ├── fee-structure.php      ← Class-wise fee management with components
│   ├── popup-ad.php           ← Popup advertisement management with scheduling
│   ├── footer-manager.php     ← Edit footer description, links, programs, contact, socials
│   ├── navigation-settings.php ← Drag-and-drop navbar menu editor
│   ├── page-content-manager.php ← Per-page hero text, section toggles, quote editing
│   ├── school-location.php    ← Google Maps embed URL, coordinates, address management
│   ├── quote-highlight.php    ← Standalone inspirational quote editor
│   ├── reports.php            ← CSV exports (students, teachers, admissions, attendance)
│   ├── audit-logs.php         ← Searchable, filterable audit log viewer
│   ├── settings.php           ← General, Appearance, Content, Users, Email, Danger Zone tabs
│   ├── support.php            ← Support/help page
│   └── ajax/                  ← AJAX action handlers (JSON responses)
│       ├── certificate-actions.php  ← Certificate CRUD actions
│       ├── enquiry-actions.php      ← Enquiry status updates & deletion
│       ├── event-actions.php        ← Event CRUD actions
│       ├── feature-card-actions.php ← Feature card CRUD + reorder + analytics
│       ├── gallery-actions.php      ← Gallery category/album/item actions
│       ├── leadership-actions.php   ← Leadership profile CRUD + reorder
│       ├── nav-actions.php          ← Navigation menu CRUD + reorder
│       ├── notification-actions.php ← Notification version/attachment actions
│       ├── popup-analytics.php      ← Popup ad view/click tracking
│       └── teacher-actions.php      ← Teacher reorder/visibility/feature actions
├── teacher/
│   ├── dashboard.php          ← Teacher overview with stats & quick actions
│   ├── attendance.php         ← Mark attendance by class (bulk, "All Present" shortcut)
│   ├── exams.php              ← Enter exam marks by class/exam/subject (auto-grading)
│   ├── post-notification.php  ← Submit notification with targeting & attachments
│   └── upload-gallery.php     ← Upload photos/videos for admin approval
├── public/
│   ├── about.php              ← About page (History, Vision, Mission, Core Values, Leadership, Quote)
│   ├── teachers.php           ← Public teachers page with flip cards & principal's message
│   ├── notifications.php      ← Public notification board with type badges & priority
│   ├── gallery.php            ← Public gallery with lightbox, filters, YouTube embeds
│   ├── events.php             ← Upcoming + past events with date cards
│   ├── admission-form.php     ← Online admission application with document upload
│   ├── certificates.php       ← Public certificates & accreditations showcase
│   └── fee-structure.php      ← Public fee information by class
└── uploads/                   ← Must be created manually (see Step 4)
    ├── photos/                ← Student profile photos
    ├── gallery/               ← Gallery images
    ├── slider/                ← Slider images
    ├── documents/             ← Admission & notification documents
    ├── logo/                  ← Legacy school logo path
    ├── ads/                   ← Popup advertisement images
    ├── branding/              ← School logo, favicon, brand assets
    │   └── school_logo.png    ← Current school logo (auto-favicon generated)
    ├── certificates/          ← Certificate images & PDFs
    └── feature-cards/         ← Feature card custom images (if any)
```

### Step 4: Create Upload Directories
**CRITICAL:** You must manually create the upload directories in cPanel File Manager:

1. Navigate to `public_html/`
2. Create folder: `uploads`
3. Inside `uploads/`, create these subfolders:
   - `photos` — Student profile photos
   - `gallery` — Gallery images
   - `slider` — Homepage slider images
   - `documents` — Admission form & notification attachments
   - `logo` — Legacy school logo path
   - `ads` — Popup advertisement images
   - `branding` — School logo, favicon, brand assets
   - `certificates` — Certificate images & PDF files
   - `feature-cards` — Feature card images (optional)

### Step 5: Set File Permissions
In **cPanel** → **File Manager**, right-click each item → **Change Permissions**:

| Path | Permission | Why |
|------|-----------|-----|
| `uploads/` | **755** | Writable for file uploads |
| `uploads/photos/` | **755** | Student photo uploads |
| `uploads/gallery/` | **755** | Gallery image uploads |
| `uploads/slider/` | **755** | Slider image uploads |
| `uploads/documents/` | **755** | Document uploads |
| `uploads/logo/` | **755** | Legacy logo path |
| `uploads/ads/` | **755** | Advertisement images |
| `uploads/branding/` | **755** | Logo, favicon, brand assets |
| `uploads/certificates/` | **755** | Certificate files |
| `uploads/feature-cards/` | **755** | Feature card images |
| `config/` | **755** | Readable by PHP |
| All `.php` files | **644** | Standard PHP permissions |
| `.htaccess` | **644** | Apache config file |

### Step 6: Configure Database Connection
Open `config/db.php` and update credentials to match your cPanel MySQL setup:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'yshszsos_jnvschool');   // your_cpanel_prefix_dbname
define('DB_USER', 'yshszsos_Admin');        // your_cpanel_prefix_username
define('DB_PASS', 'your_password_here');    // the password you set in cPanel
```

### Step 7: Configure Email (Optional)
Open `config/mail.php` and update with your cPanel email:
```php
define('SMTP_HOST', 'mail.yourdomain.com');
define('SMTP_USER', 'noreply@yourdomain.com');
define('SMTP_PASS', 'your_email_password');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
```
Or configure SMTP directly from Admin → Settings → Email/SMTP tab (stored in database).

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
1. Visit `https://yourdomain.com` → Should show public homepage with slider
2. Visit `https://yourdomain.com/login.php` → Login page
3. Login with default credentials (see below)
4. **⚠️ Immediately change the default admin password!**
5. Go to Admin → Settings to upload your school logo and configure settings
6. Go to Admin → Home Slider to manage slides
7. Go to Admin → Navigation Settings to customize the navbar
8. Go to Admin → Page Content Manager to edit hero text and section visibility

---

## 🔑 Default Login Credentials

| Field | Value |
|-------|-------|
| **Email** | `admin@school.com` |
| **Password** | `Admin@123` |
| **Role** | Super Admin |

> **⚠️ SECURITY:** Change this password immediately after first login via Admin → Settings → User Management tab

---

## 👥 User Roles

| Role | Access Level |
|------|-------------|
| **super_admin** | Full access: all modules + settings + user creation + danger zone |
| **admin** | Full access to all modules (no danger zone) |
| **office** | Same as admin (front-office staff) |
| **teacher** | Teacher panel only: dashboard, notifications, gallery, attendance, exams |

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
- ✅ Maintenance mode gate (blocks public access when enabled)

---

## 📊 Feature Summary

### Admin Panel

| Page | File | Description |
|------|------|-------------|
| **Dashboard** | `admin/dashboard.php` | 6 KPI cards (students, teachers, admissions, notifications, events, gallery), Chart.js monthly trends (admissions + attendance), recent activity feed, quick action buttons |
| **Students** | `admin/students.php` | Full CRUD with search, class/status filters, photo upload, pagination, CSV export, bulk CSV import |
| **Student Form** | `admin/student-form.php` | Add/edit student with all fields (admission no, name, parents, DOB, class, section, roll no, phone, email, address, photo, blood group, category, Aadhaar, status, dates) |
| **Import Students** | `admin/import-students.php` | Bulk CSV import with duplicate detection, error logging, progress tracking |
| **Teachers** | `admin/teachers.php` | Full CRUD with auto user-account creation, search, pagination, bulk CSV import, core team toggle, designation & bio fields |
| **Teacher Form** | `admin/teacher-form.php` | Add/edit teacher (auto-creates login account, links to `users` table) |
| **Import Teachers** | `admin/import-teachers.php` | Bulk CSV import with duplicate detection, error logging |
| **Admissions** | `admin/admissions.php` | Status tabs (Pending/Approved/Rejected/Waitlisted), approve/reject actions, document preview, remarks |
| **Notifications** | `admin/notifications.php` | Multi-tab management (Pending/Approved/Rejected/Pinned/All), targeting (all/students/teachers/parents/class/section), visibility channels (popup/banner/marquee/dashboard), priority levels (normal/important/urgent), soft-delete with restore, edit history with version restore, multi-file attachments, CSV export |
| **Gallery** | `admin/gallery.php` | Approve/reject uploads, image preview, category/album management, batch operations, delete |
| **Upload Gallery** | `admin/upload-gallery.php` | Direct gallery upload from admin panel with category/album selection |
| **Events** | `admin/events.php` | Add/edit/delete events with date, end date, time, location, type (academic/cultural/sports/holiday/exam/meeting/other), image |
| **Enquiries** | `admin/enquiries.php` | Website enquiry management. Status tabs (All/New/Contacted/Closed), search by name or phone, status update actions, delete, CSV export, pagination |
| **Home Slider** | `admin/slider.php` | Advanced management with animations (Fade/Slide/Zoom/Ken Burns), overlay styles (gradient-dark/gradient-primary/solid-dark/none), text positioning (left/center/right), overlay opacity, badge text, CTA buttons, live preview, duplicate slides, stats dashboard |
| **Certificates** | `admin/certificates.php` | Upload/manage school certificates and accreditations. Supports images & PDFs, categories (recognition/academic/sports/cultural/infrastructure/other), featured flag, allow download toggle, drag-and-drop reordering, soft-delete |
| **Feature Cards** | `admin/feature-cards.php` | Manage homepage quick-link cards. Each card has: icon (Bootstrap Icons), accent color, button text/link, badge (text + color), visibility toggle, featured flag, click analytics. Drag-and-drop reordering |
| **Core Team** | `admin/core-team.php` | Independent core team member management (separate from Teachers). Full CRUD with photo upload, display order control (1st, 2nd, 3rd...), visibility/featured toggles, drag-and-drop reordering. Homepage shows centered flip-card grid |
| **Fee Structure** | `admin/fee-structure.php` | Class-wise fee management by academic year. Each class has multiple fee components with name, amount, frequency (one-time/monthly/quarterly/yearly), optional flag. Visibility toggle per class. Auto-totals |
| **Popup Ad** | `admin/popup-ad.php` | Popup advertisement management with image upload, scheduling (start/end date), redirect URL, button text, targeting (home only, once per day, disable on mobile), view/click analytics |
| **Footer Manager** | `admin/footer-manager.php` | Edit footer description, quick links (JSON array of label/url pairs), programs list, contact info (address/phone/email/hours), social media links (Facebook/Twitter/Instagram/YouTube/LinkedIn) |
| **Navigation Settings** | `admin/navigation-settings.php` | Drag-and-drop navbar menu editor. Each item has: label, URL, icon (Bootstrap Icons), link type (internal/external), visibility toggle, CTA flag (highlighted button style). Add/edit/delete/reorder |
| **School Location** | `admin/school-location.php` | Google Maps embed URL, latitude/longitude, school address, nearby landmark. Toggle map visibility on homepage (`school_map_enabled`) |
| **Page Content Manager** | `admin/page-content-manager.php` | Per-page hero text editing (title, subtitle, badge, icon), section show/hide toggles, inline quote editing. Supports pages: Home, About, Teachers, Gallery, Events, Notifications, Admission. Global settings for navbar (top bar, login button, notification bell) and footer CTA |
| **Quote Highlight** | `admin/quote-highlight.php` | Standalone inspirational quote editor. Edit quote text and author name. Also available inline in Page Content Manager under About page |
| **Reports** | `admin/reports.php` | CSV exports for students, teachers, admissions, attendance records |
| **Audit Logs** | `admin/audit-logs.php` | Searchable, date-filterable, paginated log of all admin/teacher actions with user, action, entity, IP address |
| **Settings** | `admin/settings.php` | Multi-tab settings panel (see Settings section below) |
| **Support** | `admin/support.php` | Help/support page with documentation links |

#### Settings Tabs Detail

| Tab | Contents |
|-----|----------|
| **General** | School name, short name, tagline, email, phone, address, academic year, admission open toggle, **Maintenance Mode toggle** |
| **Appearance** | Logo upload (auto-generates favicon + extracts brand colors), favicon upload, theme color (`primary_color`), brand colors (auto-extracted or manual override: primary/secondary/accent) |
| **Content** | About page content (history, vision, mission), core values (4 cards with title + description), social media links, popup ad settings |
| **Users** | Create new user (name, email, password, role), edit existing users, reset passwords, delete users, feature access toggles |
| **Email/SMTP** | SMTP host, port, user, password, from name, encryption. Send test email to verify configuration |
| **SMS/WhatsApp** | WhatsApp API number, SMS gateway key |
| **Certificates** | Homepage certificate section toggle, max display count, public certificates page toggle |
| **Danger Zone** | Clear all audit logs (Super Admin only) |

### Teacher Panel

| Page | File | Description |
|------|------|-------------|
| **Dashboard** | `teacher/dashboard.php` | Personal stats, recent submissions, quick actions |
| **Attendance** | `teacher/attendance.php` | Select class + date, bulk mark present/absent/late with "All Present" shortcut |
| **Exams** | `teacher/exams.php` | Enter marks by class/exam/subject with auto-grading (A+ ≥90, A ≥80, B+ ≥70, B ≥60, C ≥50, D ≥40, F <40) |
| **Notifications** | `teacher/post-notification.php` | Submit for admin approval with targeting, file attachments, view history |
| **Gallery** | `teacher/upload-gallery.php` | Upload images or YouTube videos for admin approval |

### Public Website

| Page | File | Description |
|------|------|-------------|
| **Homepage** | `index.php` | Two-tier navbar (top bar with marquee, hidden on mobile), logo-only brand, dynamic hero slider with animations, feature cards section (quick links), "Our Core Team" horizontal carousel, notification bell popup, ad popup, stats bar, enquiry form (name/phone/email/message with WhatsApp button), full-width Google Maps embed, WhatsApp floating button |
| **About Us** | `public/about.php` | Content-managed sections: School History, Vision, Mission, Core Values (4 cards), Inspirational Quote, Leadership Profiles. All editable from admin |
| **Our Teachers** | `public/teachers.php` | Hero section with Playfair Display headings, "Principal's Message" badge, 2 stat cards (Expert Teachers + Years Experience), teacher flip-cards with tap-to-flip on mobile |
| **Notifications** | `public/notifications.php` | Public notification board with type badges, priority indicators, attachment downloads |
| **Gallery** | `public/gallery.php` | Filterable grid with lightbox viewer (swipe-to-close on mobile), YouTube embeds, category/album navigation |
| **Events** | `public/events.php` | Upcoming + past events with date cards, type badges, location info |
| **Admission Form** | `public/admission-form.php` | Full online application with document upload, validation, confirmation |
| **Certificates** | `public/certificates.php` | Public certificates & accreditations showcase with category filters, download support |
| **Fee Structure** | `public/fee-structure.php` | Public fee information by class with component breakdown |

#### Public Website Features
- **Dynamic Theme Color** — `--theme-primary` CSS variable controlled from admin `primary_color` setting, applied to navbar accents, buttons, links, section-title underlines
- **Dark Gradient Footer** — Background `#1a1a2e`, branded logo card with purple gradient, 4-column layout (Logo/Socials, Quick Links, Programs, Contact Info), CTA section with admission button. Fully editable from Footer Manager
- **WhatsApp Button** — Floating green button on all public pages, links to admin-configured `whatsapp_api_number`
- **Mobile Responsive** — Hidden top bar on mobile, custom hamburger icon (`bi-list`), touch-friendly tap targets, logo-only navbar brand
- **Login Page** — Split-screen design with "Back to Website" link

### Bulk Import (CSV)
- **Student Import** — Upload CSV with headers: `admission_no, name, father_name, mother_name, dob, gender, class, section, roll_no, phone, email, address, blood_group, category, aadhar_no, status, admission_date`
- **Teacher Import** — Upload CSV with headers: `employee_id, name, email, phone, subject, qualification, experience_years, dob, gender, address, joining_date, status`
- Both include: download sample template, duplicate detection, error logging, progress tracking

---

## 🔧 Maintenance Mode

Maintenance Mode allows you to temporarily take the public website offline while you make changes. When enabled, all public visitors see a branded "Site Under Maintenance" page.

### How to Enable
1. Go to **Admin → Settings → General** tab
2. Toggle the **Maintenance Mode** switch ON
3. Click **Save Changes**

### How It Works
- When **ON**: All public pages (`/`, `/public/*`) show a full-screen maintenance page with your school logo and name
- **Admins & teachers** who are logged in can still access the entire site normally
- The **login page** (`/login.php`) is always accessible — you can never lock yourself out
- When **OFF**: The website functions normally for all visitors

### Maintenance Page Shows
- School logo (if uploaded)
- School name
- "We'll be back soon!" heading
- "Our website is currently undergoing scheduled maintenance" message
- Professional branded design

### Troubleshooting
- **Locked out?** The login page is never blocked — go to `/login.php` and log in as admin
- **Still showing maintenance?** Clear your browser cache or check the `maintenance_mode` setting in the database (`settings` table)
- **Want to test?** Open an incognito/private browser window to see what public visitors see

---

## ⚙️ Settings Keys Reference

All settings are stored in the `settings` table as key-value pairs. Below is the complete list:

### General
| Key | Default | Description |
|-----|---------|-------------|
| `school_name` | `Jawahar Navodaya Vidyalaya` | School full name |
| `school_short_name` | `JNV` | Short name/abbreviation |
| `school_tagline` | `Nurturing Talent, Shaping Future` | School tagline/motto |
| `school_email` | `info@jnvschool.awayindia.com` | Contact email |
| `school_phone` | `+91-XXXXXXXXXX` | Contact phone |
| `school_address` | `India` | Physical address |
| `academic_year` | `2025-2026` | Current academic year |
| `admission_open` | `1` | Whether admissions are open (1/0) |
| `maintenance_mode` | `0` | Enable maintenance mode (1/0) |

### Branding & Appearance
| Key | Default | Description |
|-----|---------|-------------|
| `school_logo` | _(empty)_ | Logo filename in `uploads/branding/` |
| `school_favicon` | _(empty)_ | Favicon filename in `uploads/branding/` |
| `logo_updated_at` | _(empty)_ | Timestamp of last logo upload |
| `favicon_updated_at` | _(empty)_ | Timestamp of last favicon upload |
| `primary_color` | `#1e40af` | Theme primary color (hex) |
| `secondary_color` | `#3b82f6` | Theme secondary color (hex) |
| `brand_primary` | _(auto)_ | Auto-extracted brand primary color |
| `brand_secondary` | _(auto)_ | Auto-extracted brand secondary color |
| `brand_accent` | _(auto)_ | Auto-extracted brand accent color |
| `brand_colors_auto` | `1` | Whether brand colors are auto-extracted (1) or manual (0) |

### Social Media
| Key | Default | Description |
|-----|---------|-------------|
| `social_facebook` | _(empty)_ | Facebook page URL |
| `social_twitter` | _(empty)_ | Twitter/X profile URL |
| `social_instagram` | _(empty)_ | Instagram profile URL |
| `social_youtube` | _(empty)_ | YouTube channel URL |
| `social_linkedin` | _(empty)_ | LinkedIn page URL |
| `facebook_url` | _(empty)_ | Legacy Facebook URL |
| `twitter_url` | _(empty)_ | Legacy Twitter URL |
| `instagram_url` | _(empty)_ | Legacy Instagram URL |
| `youtube_url` | _(empty)_ | Legacy YouTube URL |

### Communication
| Key | Default | Description |
|-----|---------|-------------|
| `whatsapp_api_number` | _(empty)_ | WhatsApp number for floating button |
| `sms_gateway_key` | _(empty)_ | SMS gateway API key |

### SMTP Email
| Key | Default | Description |
|-----|---------|-------------|
| `smtp_host` | _(empty)_ | SMTP server hostname |
| `smtp_port` | _(empty)_ | SMTP server port (587/465) |
| `smtp_user` | _(empty)_ | SMTP username/email |
| `smtp_pass` | _(empty)_ | SMTP password |
| `smtp_from_name` | _(empty)_ | Sender display name |
| `smtp_encryption` | _(empty)_ | Encryption type (tls/ssl) |

### Popup Advertisement
| Key | Default | Description |
|-----|---------|-------------|
| `popup_ad_image` | _(empty)_ | Ad image filename in `uploads/ads/` |
| `popup_ad_active` | `0` | Whether popup ad is active (1/0) |

### About Page Content
| Key | Default | Description |
|-----|---------|-------------|
| `about_history` | _(empty)_ | School history HTML content |
| `about_vision` | _(empty)_ | School vision statement |
| `about_mission` | _(empty)_ | School mission statement |
| `core_value_1_title` | `Excellence` | Core value 1 title |
| `core_value_1_desc` | _(text)_ | Core value 1 description |
| `core_value_2_title` | `Integrity` | Core value 2 title |
| `core_value_2_desc` | _(text)_ | Core value 2 description |
| `core_value_3_title` | `Innovation` | Core value 3 title |
| `core_value_3_desc` | _(text)_ | Core value 3 description |
| `core_value_4_title` | `Community` | Core value 4 title |
| `core_value_4_desc` | _(text)_ | Core value 4 description |

### Page Content Manager — Home Page
| Key | Default | Description |
|-----|---------|-------------|
| `home_marquee_text` | _(empty)_ | Scrolling marquee text in top bar |
| `home_hero_show` | `1` | Show hero slider section |
| `home_stats_show` | `1` | Show stats bar section |
| `home_stats_students_label` | `Students` | Stats bar label 1 |
| `home_stats_teachers_label` | `Teachers` | Stats bar label 2 |
| `home_stats_classes_label` | `Classes` | Stats bar label 3 |
| `home_stats_classes_value` | `12` | Static classes count |
| `home_stats_dedication_label` | `Dedication` | Stats bar label 4 |
| `home_stats_dedication_value` | `100%` | Static dedication value |
| `home_quicklinks_show` | `1` | Show feature cards section |
| `home_cta_admissions_title` | `Admissions` | Feature card 1 title (legacy) |
| `home_cta_admissions_desc` | _(text)_ | Feature card 1 description (legacy) |
| `home_cta_notifications_title` | `Notifications` | Feature card 2 title (legacy) |
| `home_cta_notifications_desc` | _(text)_ | Feature card 2 description (legacy) |
| `home_cta_gallery_title` | `Gallery` | Feature card 3 title (legacy) |
| `home_cta_gallery_desc` | _(text)_ | Feature card 3 description (legacy) |
| `home_cta_events_title` | `Events` | Feature card 4 title (legacy) |
| `home_cta_events_desc` | _(text)_ | Feature card 4 description (legacy) |
| `home_core_team_show` | `1` | Show core team carousel |
| `home_core_team_title` | `Our Core Team` | Core team section title |
| `home_core_team_subtitle` | _(text)_ | Core team section subtitle |
| `home_contact_show` | `1` | Show contact section |
| `home_footer_cta_show` | `1` | Show footer CTA section |
| `home_footer_cta_title` | _(empty)_ | Footer CTA title |
| `home_footer_cta_desc` | _(empty)_ | Footer CTA description |
| `home_footer_cta_btn_text` | `Get In Touch` | Footer CTA button text |
| `home_certificates_show` | `1` | Show certificates on homepage |
| `home_certificates_max` | `6` | Max certificates to show on homepage |

### Page Content Manager — About Page
| Key | Default | Description |
|-----|---------|-------------|
| `about_hero_title` | `About Us` | About page hero title |
| `about_hero_subtitle` | _(text)_ | About page hero subtitle |
| `about_hero_badge` | `About Our School` | About page hero badge text |
| `about_history_show` | `1` | Show history section |
| `about_vision_mission_show` | `1` | Show vision/mission section |
| `about_core_values_show` | `1` | Show core values section |
| `about_quote_show` | `1` | Show inspirational quote |
| `about_leadership_show` | `1` | Show leadership section |
| `about_leadership_title` | `Meet Our Leadership` | Leadership section title |
| `about_leadership_subtitle` | _(text)_ | Leadership section subtitle |
| `about_footer_cta_show` | `1` | Show footer CTA on about page |

### Page Content Manager — Teachers Page
| Key | Default | Description |
|-----|---------|-------------|
| `teachers_hero_title` | `Our Teachers` | Teachers page hero title |
| `teachers_hero_subtitle` | _(text)_ | Teachers page hero subtitle |
| `teachers_hero_badge` | `Our Educators` | Teachers page hero badge |
| `teachers_core_team_show` | `1` | Show core team section |
| `teachers_grid_title` | `Meet Our Faculty` | Faculty grid title |
| `teachers_grid_subtitle` | _(text)_ | Faculty grid subtitle |
| `teachers_all_show` | `1` | Show all teachers grid |
| `teachers_footer_cta_show` | `1` | Show footer CTA |

### Page Content Manager — Other Pages
| Key | Default | Description |
|-----|---------|-------------|
| `gallery_hero_title` | `Photo Gallery` | Gallery page hero title |
| `gallery_hero_subtitle` | _(empty)_ | Gallery page hero subtitle |
| `gallery_hero_icon` | `bi-images` | Gallery page hero icon |
| `gallery_footer_cta_show` | `1` | Show footer CTA |
| `events_hero_title` | `Events` | Events page hero title |
| `events_hero_subtitle` | _(empty)_ | Events page hero subtitle |
| `events_hero_icon` | `bi-calendar-event-fill` | Events page hero icon |
| `events_footer_cta_show` | `1` | Show footer CTA |
| `notifications_hero_title` | `Notifications` | Notifications page hero title |
| `notifications_hero_subtitle` | _(empty)_ | Notifications hero subtitle |
| `notifications_hero_icon` | `bi-bell-fill` | Notifications page hero icon |
| `notifications_footer_cta_show` | `1` | Show footer CTA |
| `admission_hero_title` | `Apply for Admission` | Admission page hero title |
| `admission_hero_subtitle` | _(empty)_ | Admission page hero subtitle |
| `admission_hero_icon` | `bi-file-earmark-plus-fill` | Admission page hero icon |
| `admission_footer_cta_show` | `1` | Show footer CTA |
| `certificates_page_enabled` | `1` | Enable public certificates page |

### Global/Navbar Settings
| Key | Default | Description |
|-----|---------|-------------|
| `global_navbar_show_top_bar` | `1` | Show top bar with marquee |
| `global_navbar_show_login` | `1` | Show login button in navbar |
| `global_navbar_show_notif_bell` | `1` | Show notification bell icon |
| `global_footer_cta_title` | _(empty)_ | Global footer CTA title |
| `global_footer_cta_desc` | _(empty)_ | Global footer CTA description |
| `global_footer_cta_btn_text` | `Get In Touch` | Global footer CTA button text |

### Footer Manager
| Key | Default | Description |
|-----|---------|-------------|
| `footer_description` | _(text)_ | Footer about text |
| `footer_quick_links` | _(JSON)_ | Quick links array `[{"label":"...","url":"..."}]` |
| `footer_programs` | _(JSON)_ | Programs list `[{"label":"..."}]` |
| `footer_contact_address` | _(empty)_ | Footer contact address |
| `footer_contact_phone` | _(empty)_ | Footer contact phone |
| `footer_contact_email` | _(empty)_ | Footer contact email |
| `footer_contact_hours` | `Mon - Sat: 8:00 AM - 5:00 PM` | Footer office hours |
| `footer_social_facebook` | _(empty)_ | Footer Facebook URL |
| `footer_social_twitter` | _(empty)_ | Footer Twitter URL |
| `footer_social_instagram` | _(empty)_ | Footer Instagram URL |
| `footer_social_youtube` | _(empty)_ | Footer YouTube URL |
| `footer_social_linkedin` | _(empty)_ | Footer LinkedIn URL |

### Gallery Settings
| Key | Default | Description |
|-----|---------|-------------|
| `gallery_layout_style` | `premium` | Gallery layout (premium/grid/masonry) |
| `gallery_bg_style` | `dark` | Gallery background style |
| `gallery_particles_show` | `1` | Show particle effects |

### Feature Access Toggles
| Key | Default | Description |
|-----|---------|-------------|
| `feature_admissions` | `1` | Enable admissions module |
| `feature_gallery` | `1` | Enable gallery module |
| `feature_events` | `1` | Enable events module |
| `feature_slider` | `1` | Enable slider module |
| `feature_notifications` | `1` | Enable notifications module |
| `feature_reports` | `1` | Enable reports module |
| `feature_audit_logs` | `1` | Enable audit logs module |

---

## 🔄 Upgrading

### From Schema v2.0 to v3.0

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

-- Add popup ad settings (v3.1)
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('popup_ad_image', ''),
('popup_ad_active', '0');
```

### From Schema v3.1 to v3.2

```sql
-- WhatsApp & SMS settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('whatsapp_api_number', ''),
('sms_gateway_key', '');
```

### From Schema v3.2 to v3.3

Run these in phpMyAdmin to add missing settings and tables for v3.3:

```sql
-- 1. Add maintenance mode setting
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('maintenance_mode', '0');

-- 2. Create certificates table (if not exists)
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'recognition',
  `year` SMALLINT DEFAULT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `thumb_path` VARCHAR(255) DEFAULT NULL,
  `file_type` ENUM('image','pdf') NOT NULL DEFAULT 'image',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_download` TINYINT(1) NOT NULL DEFAULT 1,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_at` DATETIME DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create feature_cards table (if not exists)
CREATE TABLE IF NOT EXISTS `feature_cards` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(50) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `icon_class` VARCHAR(100) NOT NULL DEFAULT 'bi-star',
  `accent_color` VARCHAR(20) NOT NULL DEFAULT 'auto',
  `btn_text` VARCHAR(50) NOT NULL DEFAULT 'Learn More',
  `btn_link` VARCHAR(255) NOT NULL DEFAULT '#',
  `badge_text` VARCHAR(50) DEFAULT NULL,
  `badge_color` VARCHAR(20) DEFAULT '#ef4444',
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `show_stats` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Seed default feature cards (if table was just created)
INSERT IGNORE INTO `feature_cards` (`slug`, `title`, `description`, `icon_class`, `accent_color`, `btn_text`, `btn_link`, `badge_text`, `badge_color`, `is_featured`, `sort_order`) VALUES
('admissions', 'Admissions', 'Apply online for admission to JNV School.', 'bi-mortarboard-fill', '#3b82f6', 'Apply Now', '/public/admission-form.php', 'Open', '#22c55e', 1, 1),
('notifications', 'Notifications', 'Stay updated with latest announcements.', 'bi-bell-fill', '#f59e0b', 'View All', '/public/notifications.php', NULL, '#ef4444', 0, 2),
('gallery', 'Gallery', 'Explore photos & videos from school life.', 'bi-images', '#10b981', 'Browse', '/public/gallery.php', NULL, '#8b5cf6', 0, 3),
('events', 'Events', 'Check upcoming school events & dates.', 'bi-calendar-event-fill', '#ef4444', 'View Events', '/public/events.php', NULL, '#3b82f6', 0, 4);

-- 5. Create fee_structures table (if not exists)
CREATE TABLE IF NOT EXISTS `fee_structures` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` VARCHAR(20) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_class_year` (`class`, `academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Create fee_components table (if not exists)
CREATE TABLE IF NOT EXISTS `fee_components` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fee_structure_id` INT UNSIGNED NOT NULL,
  `component_name` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `frequency` ENUM('one-time','monthly','quarterly','yearly') NOT NULL DEFAULT 'yearly',
  `is_optional` TINYINT(1) NOT NULL DEFAULT 0,
  `display_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fee_structure_id` (`fee_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Certificate settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('home_certificates_show', '1'),
('home_certificates_max', '6'),
('certificates_page_enabled', '1');

-- 8. Upgrade events table (rename columns, add new fields)
ALTER TABLE `events`
  CHANGE COLUMN `event_date` `start_date` DATE NOT NULL,
  ADD COLUMN `end_date` DATE DEFAULT NULL AFTER `start_date`,
  CHANGE COLUMN `event_time` `start_time` TIME DEFAULT NULL,
  ADD COLUMN `end_time` TIME DEFAULT NULL AFTER `start_time`,
  ADD COLUMN `status` ENUM('active','draft','cancelled','completed') NOT NULL DEFAULT 'active' AFTER `type`,
  ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_public`,
  CHANGE COLUMN `image` `poster` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `views` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `poster`,
  ADD COLUMN `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  MODIFY COLUMN `type` ENUM('sports','cultural','exam','holiday','activity','academic','meeting','other') NOT NULL DEFAULT 'activity',
  ADD KEY `idx_events_status` (`status`),
  ADD KEY `idx_events_featured` (`is_featured`);

-- 9. Create upload directories:
--    uploads/branding/
--    uploads/certificates/
--    uploads/feature-cards/
--    uploads/events/
```

No existing table structures are changed — only new tables, columns, and settings are added.

### From Schema v3.3 to v3.4

```sql
-- 1. Create popup_ads table
CREATE TABLE IF NOT EXISTS `popup_ads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `redirect_url` VARCHAR(500) DEFAULT NULL,
  `button_text` VARCHAR(100) DEFAULT NULL,
  `show_on_home` TINYINT(1) NOT NULL DEFAULT 1,
  `show_once_per_day` TINYINT(1) NOT NULL DEFAULT 1,
  `disable_on_mobile` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO `popup_ads` (`id`, `is_enabled`) VALUES (1, 0);

-- 2. Create popup_analytics table
CREATE TABLE IF NOT EXISTS `popup_analytics` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `popup_id` INT UNSIGNED NOT NULL,
  `view_date` DATE NOT NULL,
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `clicks_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_popup_date` (`popup_id`, `view_date`),
  CONSTRAINT `fk_analytics_popup` FOREIGN KEY (`popup_id`)
    REFERENCES `popup_ads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create enquiries table
CREATE TABLE IF NOT EXISTS `enquiries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

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
- Upload logo via Admin → Settings → Appearance tab
- Ensure `uploads/branding/` directory exists with **755** permissions
- Logo appears in navbar and footer automatically

### Email not sending
- Use cPanel email accounts for SMTP
- Verify SMTP credentials in Admin → Settings → Email/SMTP tab (or `config/mail.php`)
- Check if your hosting provider blocks port 587/465

### Maintenance mode — can't access site
- Go directly to `/login.php` — it is never blocked
- Log in as admin and disable maintenance mode in Settings → General tab
- Or manually update the database: `UPDATE settings SET setting_value='0' WHERE setting_key='maintenance_mode'`

### Certificates not showing
- Ensure `uploads/certificates/` directory exists with **755** permissions
- Check that certificates are marked as active in Admin → Certificates
- Verify `certificates_page_enabled` is set to `1` in settings

### Feature cards not showing on homepage
- Check Admin → Feature Cards — cards must be visible (toggle on)
- Verify `home_quicklinks_show` is `1` in Page Content Manager

---

## 📧 Email Setup (cPanel)

### Option 1: Configure via Admin Panel (Recommended)
1. Go to Admin → Settings → Email/SMTP tab
2. Enter your SMTP server details
3. Click "Send Test Email" to verify
4. Settings are stored in the database and used automatically

### Option 2: Configure via File
1. **Create email account** in cPanel → Email Accounts (e.g., `noreply@yourdomain.com`)
2. Update `config/mail.php` with the credentials
3. PHPMailer library is included in `includes/phpmailer/`

---

## 🗄️ Database Schema (v3.4)

27 tables total:

1. `users` — Admin/teacher/office accounts (bcrypt passwords, roles)
2. `students` — Student records with photos, class/section, admission details
3. `teachers` — Teacher records linked to user accounts (designation, core team, bio, display order)
4. `admissions` — Online admission applications with document uploads
5. `notifications` — Notifications with approval workflow, targeting, visibility channels, soft-delete
6. `notification_reads` — Per-user read tracking
7. `notification_versions` — Edit history with restore support
8. `notification_attachments` — Multi-file attachment support
9. `gallery_items` — Gallery uploads with approval, batches, compression
10. `gallery_categories` — Gallery categories (Academic, Cultural, Sports, etc.)
11. `gallery_albums` — Albums within categories
12. `events` — School events/calendar with types, locations, images
13. `attendance` — Daily attendance by class
14. `exam_results` — Exam marks with auto-grading (A+ to F)
15. `audit_logs` — System action logs with IP tracking
16. `settings` — Key-value school settings (~80+ configuration keys)
17. `home_slider` — Homepage slider with animations, overlays, text positioning
18. `site_quotes` — Inspirational quotes for About page
19. `leadership_profiles` — Leadership/principal profiles for About page
20. `nav_menu_items` — Admin-managed navbar menu items
21. `certificates` — School certificates & accreditations (image/PDF)
22. `feature_cards` — Homepage quick-link cards with icons, colors, analytics
23. `fee_structures` — Class-wise fee structures by academic year
24. `fee_components` — Individual fee line items with frequency
25. `popup_ads` — Homepage popup advertisements with scheduling & targeting
26. `popup_analytics` — Popup ad view/click tracking by date
27. `enquiries` — Website enquiry submissions with status tracking

---

*Built for JNV School — jnvschool.awayindia.com — v3.4*
