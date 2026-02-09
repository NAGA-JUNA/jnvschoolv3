# School Management System — PHP + MySQL (cPanel Ready)

## 📋 Overview
A complete school management system built with pure PHP 8+ and MySQL. No Node.js, no React, no terminal commands needed. Upload directly to cPanel shared hosting.

## 🚀 Installation (cPanel)

### Step 1: Create Database
1. Log in to **cPanel** → **MySQL Databases**
2. Create a new database (e.g., `youruser_schooldb`)
3. Create a new database user with a strong password
4. Add the user to the database with **ALL PRIVILEGES**

### Step 2: Import Schema
1. Go to **phpMyAdmin** in cPanel
2. Select your new database
3. Click **Import** tab
4. Upload `schema.sql` and click **Go**

### Step 3: Configure Database Connection
1. Open `config/db.php`
2. Update these values:
   ```php
   define('DB_NAME', 'youruser_schooldb');
   define('DB_USER', 'youruser_schooluser');
   define('DB_PASS', 'your_strong_password');
   ```

### Step 4: Configure Email (Optional)
1. Open `config/mail.php`
2. Update SMTP settings with your cPanel email credentials

### Step 5: Upload Files
1. Go to **cPanel → File Manager → public_html**
2. Upload ALL files and folders from this `php-backend/` directory
3. The structure should be:
   ```
   public_html/
   ├── index.php
   ├── login.php
   ├── config/
   ├── includes/
   ├── admin/
   ├── teacher/
   ├── public/
   ├── uploads/
   ```

### Step 6: Set Permissions
- `uploads/` folder: **755** (writable)
- All PHP files: **644**

## 🔑 Default Login
- **Email:** `admin@school.com`
- **Password:** `Admin@123`
- **⚠️ Change this immediately after first login!**

## 📁 File Structure
```
├── config/
│   ├── db.php          — Database connection
│   └── mail.php        — Email/SMTP settings
├── includes/
│   ├── auth.php        — Session auth, CSRF, role checks
│   ├── header.php      — Shared layout header + sidebar
│   └── footer.php      — Shared layout footer
├── admin/
│   ├── dashboard.php   — KPI counts, recent activity
│   ├── students.php    — Student list with search/filter/pagination
│   ├── student-form.php— Add/edit student
│   ├── teachers.php    — Teacher list
│   ├── teacher-form.php— Add/edit teacher
│   ├── admissions.php  — Approve/reject admissions
│   ├── notifications.php— Approve/reject notifications
│   ├── gallery.php     — Approve/reject gallery uploads
│   ├── events.php      — CRUD events
│   ├── reports.php     — CSV exports
│   └── settings.php    — School settings + user management
├── teacher/
│   ├── dashboard.php   — Teacher overview
│   ├── post-notification.php — Submit notification
│   ├── upload-gallery.php    — Upload photos/videos
│   ├── attendance.php  — Mark attendance by class
│   └── exams.php       — Enter exam marks
├── public/
│   ├── notifications.php — Public notification board
│   ├── gallery.php     — Public gallery
│   ├── events.php      — Upcoming events
│   └── admission-form.php — Online admission application
├── schema.sql          — Full database schema
├── login.php           — Login page
├── logout.php          — Logout handler
├── forgot-password.php — Password reset request
├── reset-password.php  — Password reset form
└── index.php           — Redirect to dashboard/login
```

## 🔒 Security Features
- `password_hash()` / `password_verify()` for passwords
- CSRF token on all forms
- Session regeneration on login
- `htmlspecialchars()` output escaping
- PDO prepared statements (SQL injection prevention)
- Role-based access control (super_admin, admin, teacher, office)

## 👥 User Roles
| Role | Access |
|------|--------|
| super_admin | Full access including settings & user creation |
| admin | Full access to all modules |
| office | Same as admin |
| teacher | Dashboard, notifications, gallery, attendance, exams |

## 📧 Email Setup (cPanel SMTP)
Uses PHP `mail()` by default. For PHPMailer:
1. Download PHPMailer from GitHub
2. Place in a `vendor/` folder
3. Update `config/mail.php` to use PHPMailer classes
