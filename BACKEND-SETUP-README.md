# JSchoolAdmin — PHP Backend Setup Guide (cPanel)

Complete step-by-step guide to set up the JSchoolAdmin REST API backend on a cPanel-hosted server with MySQL.

**Version:** 1.1.0 | **Last Updated:** February 2026

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Server Requirements](#server-requirements)
3. [Directory Structure](#directory-structure)
4. [Step 1: Create MySQL Database](#step-1-create-mysql-database)
5. [Step 2: Import Database Schema](#step-2-import-database-schema)
6. [Step 3: Upload Backend Files](#step-3-upload-backend-files)
7. [Step 4: Configure Environment](#step-4-configure-environment)
8. [Step 5: Configure .htaccess](#step-5-configure-htaccess)
9. [Step 6: Set File Permissions](#step-6-set-file-permissions)
10. [Step 7: Upload Frontend Build](#step-7-upload-frontend-build)
11. [Step 8: Enable SSL](#step-8-enable-ssl)
12. [Demo / Testing Mode](#demo--testing-mode)
13. [API Endpoints Reference](#api-endpoints-reference)
14. [Authentication Flow](#authentication-flow)
15. [CORS Configuration](#cors-configuration)
16. [File Upload Configuration](#file-upload-configuration)
17. [Excel Import Template Format](#excel-import-template-format)
18. [Cron Jobs](#cron-jobs)
19. [Troubleshooting](#troubleshooting)
20. [Security Checklist](#security-checklist)

---

## Prerequisites

- cPanel hosting account with:
  - PHP 8.0 or higher
  - MySQL 5.7+ or MariaDB 10.3+
  - Apache with `mod_rewrite` enabled
  - SSL certificate (free via Let's Encrypt in cPanel)
- Domain name pointed to your hosting server
- FTP client (FileZilla) or cPanel File Manager access
- phpMyAdmin access (included in cPanel)

---

## Server Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP Version | 8.0 | 8.1+ |
| MySQL | 5.7 | 8.0+ |
| Memory Limit | 128MB | 256MB |
| Upload Max Size | 10MB | 50MB |
| Post Max Size | 10MB | 50MB |
| max_execution_time | 30s | 60s |

### Required PHP Extensions
- `pdo_mysql` — Database connectivity
- `mbstring` — String handling
- `json` — JSON encoding/decoding
- `openssl` — Token encryption
- `fileinfo` — File upload validation
- `gd` or `imagick` — Image processing

> **Check in cPanel:** Go to **Select PHP Version** → **Extensions** tab to enable these.

---

## Directory Structure

```
public_html/
├── api/                          # ← Backend API root
│   ├── .htaccess                 # API routing rules
│   ├── index.php                 # API entry point / router
│   ├── config/
│   │   ├── database.php          # DB connection config
│   │   ├── constants.php         # App constants
│   │   └── cors.php              # CORS headers
│   ├── middleware/
│   │   ├── auth.php              # JWT authentication middleware
│   │   └── rate-limit.php        # Rate limiting
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── StudentController.php     # Students CRUD + bulk promote
│   │   ├── AttendanceController.php  # Student & staff attendance
│   │   ├── ExamController.php        # Exam results & marks entry
│   │   ├── TeacherController.php     # Teachers CRUD + class assignment
│   │   ├── DocumentController.php    # Student & teacher documents
│   │   ├── MessageController.php     # WhatsApp message history
│   │   ├── NotificationController.php
│   │   ├── GalleryController.php
│   │   ├── EventController.php
│   │   ├── AdmissionController.php
│   │   ├── WhatsAppController.php
│   │   ├── EmailController.php
│   │   ├── ReportController.php
│   │   ├── BrandingController.php    # School branding settings
│   │   ├── AuditLogController.php
│   │   └── SliderController.php     # Home banner/slider CRUD
│   ├── models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Attendance.php
│   │   ├── ExamResult.php
│   │   ├── Document.php
│   │   ├── Message.php
│   │   ├── Notification.php
│   │   ├── GalleryCategory.php
│   │   ├── GalleryItem.php
│   │   ├── Event.php
│   │   ├── Admission.php
│   │   ├── Branding.php
│   │   ├── AuditLog.php
│   │   └── Slider.php
│   ├── helpers/
│   │   ├── response.php          # JSON response helper
│   │   ├── validator.php         # Input validation
│   │   ├── jwt.php               # JWT token helper
│   │   ├── upload.php            # File upload helper
│   │   └── excel.php             # Excel import/export helper
│   └── uploads/                  # Uploaded files directory
│       ├── gallery/
│       ├── notifications/
│       ├── students/             # Student photos & documents
│       ├── teachers/             # Teacher photos & documents
│       └── profiles/
├── index.html                    # ← Frontend (React build)
├── assets/                       # ← Frontend assets (from build)
├── .htaccess                     # ← Root SPA routing
└── favicon.ico
```

---

## Step 1: Create MySQL Database

1. Log into **cPanel** → **MySQL® Databases**
2. Create a new database:
   - Database name: `jschooladmin_db`
3. Create a new user:
   - Username: `jschooladmin_user`
   - Password: *(use a strong password, save it)*
4. Add user to database:
   - Select the user and database
   - Grant **ALL PRIVILEGES**
   - Click **Make Changes**

> **Note:** cPanel prefixes your account name, so your actual database name will be like `cpanelusr_jschooladmin_db`.

---

## Step 2: Import Database Schema

1. Open **phpMyAdmin** from cPanel
2. Select your newly created database
3. Click the **Import** tab
4. Upload the `schema.sql` file from the project root
5. Click **Go**

Alternatively, click the **SQL** tab and paste the contents of `schema.sql` directly.

> The schema file includes all 19 tables, indexes, and sample data. See [`schema.sql`](./schema.sql) for the complete file.

### Tables Overview

| Table | Purpose |
|---|---|
| `users` | Admin/Office/Teacher login accounts |
| `teachers` | Detailed teacher profiles (qualification, subjects, classes) |
| `students` | Student records with parent/guardian info |
| `student_attendance` | Daily attendance records |
| `exam_results` | Subject-wise marks and grades |
| `student_documents` | Uploaded student documents (Aadhaar, TC, etc.) |
| `teacher_documents` | Uploaded teacher documents (ID, certificates, resume) |
| `student_messages` | WhatsApp message history for students |
| `teacher_messages` | WhatsApp message history for teachers |
| `notifications` | Notification submissions with approval workflow |
| `gallery_categories` | Gallery category metadata |
| `gallery_items` | Gallery uploads with approval |
| `events` | School events and calendar |
| `admissions` | Online admission applications |
| `official_emails` | School email accounts |
| `whatsapp_shares` | WhatsApp sharing log |
| `audit_logs` | System audit trail |
| `settings` | Key-value school settings |
| `branding` | Theme/branding configuration |
| `home_slider` | Admin-managed hero carousel slides |

---

## Step 3: Upload Backend Files

### Option A: Using cPanel File Manager
1. Go to **cPanel** → **File Manager**
2. Navigate to `public_html/`
3. Create a folder named `api`
4. Upload all PHP backend files into `public_html/api/`

### Option B: Using FTP (FileZilla)
1. Connect to your server using FTP credentials from cPanel
2. Navigate to `/public_html/`
3. Upload the `api/` folder with all contents

---

## Step 4: Configure Environment

Create `api/config/database.php`:

```php
<?php
// Database Configuration
// IMPORTANT: Update these values with your actual cPanel database credentials

define('DB_HOST', 'localhost');
define('DB_NAME', 'cpanelusr_jschooladmin_db');  // Your full database name
define('DB_USER', 'cpanelusr_jschooladmin_user'); // Your full database username
define('DB_PASS', 'YOUR_STRONG_PASSWORD_HERE');   // Database password
define('DB_CHARSET', 'utf8mb4');

// JWT Configuration
define('JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_64_CHAR_STRING');
define('JWT_EXPIRY', 86400); // 24 hours in seconds

// App Configuration
define('APP_NAME', 'JSchoolAdmin');
define('APP_URL', 'https://yourdomain.com');
define('API_URL', 'https://yourdomain.com/api');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Timezone
date_default_timezone_set('Asia/Kolkata');

// PDO Connection
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}
```

Create `api/config/cors.php`:

```php
<?php
// CORS Configuration
// Update the allowed origin to your actual frontend domain

$allowed_origins = [
    'https://yourdomain.com',
    'https://www.yourdomain.com',
    'http://localhost:5173',        // Vite dev server
    'http://localhost:8080',        // Preview
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 86400");
header("Content-Type: application/json; charset=utf-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
```

---

## Step 5: Configure .htaccess

### Root `.htaccess` (for SPA frontend + API routing)

Create `public_html/.htaccess`:

```apache
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# If request is for API, let it pass through to api/ folder
RewriteRule ^api/ - [L]

# If request is for a real file or directory, serve it
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Otherwise, serve index.html (SPA routing)
RewriteRule ^ index.html [L]
```

### API `.htaccess` (routes all API requests to index.php)

Create `public_html/api/.htaccess`:

```apache
RewriteEngine On

# Route all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]

# Deny access to config files
<FilesMatch "\.(php|ini|log)$">
  # Allow index.php only
</FilesMatch>

# Protect sensitive directories
RedirectMatch 403 ^/api/config/
RedirectMatch 403 ^/api/middleware/
RedirectMatch 403 ^/api/models/
RedirectMatch 403 ^/api/helpers/
```

---

## Step 6: Set File Permissions

In cPanel **File Manager** or via SSH:

```bash
# Directories: 755
find public_html/ -type d -exec chmod 755 {} \;

# PHP files: 644
find public_html/ -type f -name "*.php" -exec chmod 644 {} \;

# Upload directory: 755 (writable by PHP)
chmod -R 755 public_html/api/uploads/

# Config files: 600 (owner only)
chmod 600 public_html/api/config/database.php
```

---

## Step 7: Upload Frontend Build

1. In your React project, update `src/api/client.ts`:
   ```typescript
   const BASE_URL = "https://yourdomain.com/api";
   ```

2. Build the frontend:
   ```bash
   npm run build
   ```

3. Upload the contents of the `dist/` folder to `public_html/`:
   - `index.html` → `public_html/index.html`
   - `assets/` → `public_html/assets/`

> **Important:** Upload the *contents* of `dist/`, not the `dist/` folder itself.

---

## Step 8: Enable SSL

1. Go to **cPanel** → **SSL/TLS Status** or **Let's Encrypt**
2. Click **Issue** or **AutoSSL** for your domain
3. Wait for certificate provisioning (usually instant)
4. Verify by visiting `https://yourdomain.com`

---

## Demo / Testing Mode

The frontend works fully without a backend connection using mock data. This is useful for:
- UI testing and design review
- Feature demonstrations
- Client previews before backend deployment

### How It Works
- All data is loaded from `src/data/mockStudents.ts` and `src/data/mockTeachers.ts`
- The login page accepts any credentials and navigates based on selected role
- CRUD operations use local state (changes reset on page refresh)
- API endpoints are defined in `src/api/endpoints.ts` but not called until backend is connected

### Demo Login Credentials

| Role | Email | Password | Panel |
|---|---|---|---|
| Super Admin | `admin@school.com` | `admin123` | Admin Dashboard |
| Office Staff | `office@school.com` | `office123` | Admin Dashboard |
| Teacher | `priya.singh@school.com` | `teacher123` | Teacher Dashboard |

> Click any credential row on the login page to auto-fill the form.

### Switching to Live Backend
1. Update `src/api/client.ts` with your API base URL
2. Replace mock data calls with actual API calls using the `useApi` hook
3. Build and deploy the frontend

---

## API Endpoints Reference

### Authentication Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/auth/login` | Login (returns JWT token) |
| `POST` | `/api/auth/logout` | Logout / invalidate token |
| `GET` | `/api/auth/me` | Get current user profile |

### Public Endpoints (No auth required)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/public/notifications` | List approved public notifications |
| `GET` | `/api/public/gallery/categories` | List gallery categories |
| `GET` | `/api/public/gallery/items?category={slug}` | Items by category |
| `GET` | `/api/public/events` | List upcoming public events |
| `POST` | `/api/public/admissions` | Submit admission application |

### Admin Endpoints (Requires `admin` or `super_admin` role)

| Method | Endpoint | Description |
|---|---|---|
| **Dashboard** | | |
| `GET` | `/api/admin/dashboard/metrics` | Dashboard KPI data |
| `GET` | `/api/admin/dashboard/activity` | Recent activity feed |
| `GET` | `/api/admin/alerts` | System alerts |
| **Students** | | |
| `GET` | `/api/admin/students` | List students (paginated, filterable) |
| `POST` | `/api/admin/students` | Create student |
| `GET` | `/api/admin/students/{id}` | Get student details |
| `PUT` | `/api/admin/students/{id}` | Update student |
| `DELETE` | `/api/admin/students/{id}` | Soft-delete student |
| `POST` | `/api/admin/students/import` | Import students from Excel |
| `GET` | `/api/admin/students/export` | Export students (Excel/PDF) |
| `POST` | `/api/admin/students/bulk-promote` | Bulk promote selected students |
| `GET` | `/api/admin/students/alumni` | List alumni records |
| `GET` | `/api/admin/students/{id}/attendance` | Student attendance history |
| `GET` | `/api/admin/students/{id}/exams` | Student exam results |
| `GET` | `/api/admin/students/{id}/documents` | List student documents |
| `POST` | `/api/admin/students/{id}/documents` | Upload student document |
| `GET` | `/api/admin/students/{id}/messages` | Student message history |
| **Teachers** | | |
| `GET` | `/api/admin/teachers` | List teachers (paginated, filterable) |
| `POST` | `/api/admin/teachers` | Create teacher |
| `GET` | `/api/admin/teachers/{id}` | Get teacher details |
| `PUT` | `/api/admin/teachers/{id}` | Update teacher |
| `DELETE` | `/api/admin/teachers/{id}` | Soft-delete (mark inactive) |
| `POST` | `/api/admin/teachers/import` | Import teachers from Excel |
| `GET` | `/api/admin/teachers/export` | Export teachers (Excel/PDF) |
| `GET` | `/api/admin/teachers/inactive` | List inactive/former teachers |
| `PUT` | `/api/admin/teachers/{id}/assign-classes` | Update class assignments |
| `GET` | `/api/admin/teachers/{id}/attendance` | Staff attendance history |
| `GET` | `/api/admin/teachers/{id}/documents` | List teacher documents |
| `POST` | `/api/admin/teachers/{id}/documents` | Upload teacher document |
| `GET` | `/api/admin/teachers/{id}/messages` | Teacher message history |
| **Admissions** | | |
| `GET` | `/api/admin/admissions` | List admission applications |
| `PATCH` | `/api/admin/admissions/{id}` | Update admission status |
| `GET` | `/api/admin/admissions/export` | Export to CSV |
| **Notifications** | | |
| `GET` | `/api/admin/notifications` | List all notifications |
| `GET` | `/api/admin/notifications/{id}` | Get notification details |
| `PATCH` | `/api/admin/notifications/{id}/approve` | Approve notification |
| `PATCH` | `/api/admin/notifications/{id}/reject` | Reject notification |
| `POST` | `/api/admin/notifications/bulk-approve` | Bulk approve |
| **Gallery** | | |
| `GET` | `/api/admin/gallery/categories` | List categories |
| `POST` | `/api/admin/gallery/categories` | Create category |
| `PUT` | `/api/admin/gallery/categories/{id}` | Update category |
| `DELETE` | `/api/admin/gallery/categories/{id}` | Delete category |
| `GET` | `/api/admin/gallery/approvals` | Pending gallery items |
| `PATCH` | `/api/admin/gallery/items/{id}/approve` | Approve item |
| `PATCH` | `/api/admin/gallery/items/{id}/reject` | Reject item |
| **Events** | | |
| `GET` | `/api/admin/events` | List all events |
| `POST` | `/api/admin/events` | Create event |
| `PUT` | `/api/admin/events/{id}` | Update event |
| `DELETE` | `/api/admin/events/{id}` | Delete event |
| **Other** | | |
| `GET` | `/api/admin/emails` | List official emails |
| `POST` | `/api/admin/emails/create` | Create official email |
| `GET` | `/api/admin/reports` | Get report data |
| `GET` | `/api/admin/audit-logs` | List audit logs |
| `GET` | `/api/admin/settings` | Get all settings |
| `PUT` | `/api/admin/settings` | Update settings |
| `PUT` | `/api/admin/branding` | Save branding settings |
| `POST` | `/api/admin/whatsapp/log` | Log WhatsApp share |
| `GET` | `/api/admin/whatsapp/logs` | Get WhatsApp share history |
| **Home Slider** | | |
| `GET` | `/api/home/slider` | List all slider slides (public) |
| `POST` | `/api/home/slider` | Create new slide |
| `PUT` | `/api/home/slider/{id}` | Update slide |
| `DELETE` | `/api/home/slider/{id}` | Delete slide |

### Teacher Endpoints (Requires `teacher` or `office` role)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/teacher/dashboard/metrics` | Teacher dashboard data |
| `GET` | `/api/teacher/dashboard/activity` | Teacher activity feed |
| `GET` | `/api/teacher/students` | View students in assigned classes |
| `POST` | `/api/teacher/attendance/mark` | Mark student attendance |
| `POST` | `/api/teacher/exams/marks` | Enter exam marks |
| `POST` | `/api/teacher/notifications` | Submit notification |
| `POST` | `/api/teacher/gallery/upload` | Upload gallery item |
| `POST` | `/api/teacher/gallery/youtube` | Add YouTube link |
| `GET` | `/api/teacher/submissions` | My submissions list |
| `GET` | `/api/teacher/profile` | Get profile |
| `PUT` | `/api/teacher/profile` | Update profile |

### Query Parameters (Pagination & Filters)

All list endpoints support:

| Parameter | Example | Description |
|---|---|---|
| `page` | `?page=2` | Page number (default: 1) |
| `per_page` | `?per_page=25` | Items per page (default: 20) |
| `search` | `?search=rahul` | Search keyword |
| `status` | `?status=active` | Filter by status |
| `class` | `?class=10` | Filter by class (students/teachers) |
| `section` | `?section=A` | Filter by section |
| `subject` | `?subject=Mathematics` | Filter by subject (teachers) |
| `academic_year` | `?academic_year=2025-2026` | Filter by academic year |
| `sort` | `?sort=created_at` | Sort field |
| `order` | `?order=desc` | Sort direction |

### Response Format

All API responses follow this structure:

```json
// Success
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}

// Success with pagination
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "total_pages": 8
  }
}

// Error
{
  "success": false,
  "error": "Validation failed",
  "details": {
    "name": "Name is required",
    "email": "Invalid email format"
  }
}
```

---

## Authentication Flow

```
1. POST /api/auth/login
   Body: { "email": "admin@school.com", "password": "admin123" }
   Response: { "success": true, "data": { "token": "eyJ...", "user": {...} } }

2. Store token in localStorage on frontend

3. All subsequent requests include:
   Header: Authorization: Bearer eyJ...

4. Backend validates JWT on every secured request
   - Invalid/expired token → 401 Unauthorized
   - Wrong role → 403 Forbidden
```

---

## CORS Configuration

If frontend and API are on the **same domain** (recommended):
- CORS headers are optional but harmless to keep

If frontend is on a **different domain** (e.g., Lovable preview):
- Update `$allowed_origins` in `api/config/cors.php`
- Add your Lovable preview URL during development

---

## File Upload Configuration

Update `php.ini` via cPanel → **MultiPHP INI Editor**:

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 60
max_input_time = 60
memory_limit = 256M
```

Supported upload types:
- **Images:** jpg, jpeg, png, gif, webp (max 10MB)
- **Documents:** pdf, doc, docx (max 10MB)
- **Videos:** mp4 (max 50MB) — or use YouTube links
- **Excel:** xlsx, xls, csv (max 10MB) — for import

---

## Excel Import Template Format

### Students Import (`students_import_template.xlsx`)

| Column | Required | Format | Example |
|---|---|---|---|
| Admission No | ✅ | Text | ADM2025010 |
| Full Name | ✅ | Text | Rahul Verma |
| Class | ✅ | Text | 10 |
| Section | | Text | A |
| Roll No | | Number | 15 |
| Gender | | Male/Female/Other | Male |
| Date of Birth | | YYYY-MM-DD | 2012-05-15 |
| Blood Group | | Text | B+ |
| Father Name | | Text | Suresh Verma |
| Mother Name | | Text | Anita Verma |
| Parent Phone | ✅ | Text | +91-9812345678 |
| WhatsApp | | Text | +91-9812345678 |
| Email | | Text | parent@email.com |
| Address | | Text | 123, MG Road, Lucknow |
| Emergency Contact | | Text | +91-9812345679 |

**Duplicate Check:** `admission_no` must be unique. Rows with existing admission numbers will be flagged as duplicates during import preview.

### Teachers Import (`teachers_import_template.xlsx`)

| Column | Required | Format | Example |
|---|---|---|---|
| Employee ID | ✅ | Text | EMP011 |
| Full Name | ✅ | Text | Rahul Verma |
| Gender | | Male/Female/Other | Male |
| Date of Birth | | YYYY-MM-DD | 1988-05-15 |
| Phone | ✅ | Text | +91-9812345678 |
| WhatsApp | | Text | +91-9812345678 |
| Email | | Text | rahul@school.com |
| Address | | Text | 123, MG Road, Lucknow |
| Qualification | | Text | M.Sc. Mathematics, B.Ed. |
| Experience (Years) | | Number | 8 |
| Joining Date | | YYYY-MM-DD | 2017-06-01 |
| Subjects | | Comma-separated | Mathematics, Physics |
| Classes | | Comma-separated | 10-A, 9-B |
| Employment Type | | Full-time/Part-time | Full-time |

**Duplicate Check:** `employee_id` must be unique. Rows with existing employee IDs will be flagged.

---

## Cron Jobs

Set up in **cPanel** → **Cron Jobs**:

```bash
# Clean expired notifications daily at midnight
0 0 * * * /usr/local/bin/php /home/cpanelusr/public_html/api/cron/cleanup.php

# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/php /home/cpanelusr/public_html/api/cron/backup.php

# Archive alumni records at end of academic year (April 1)
0 0 1 4 * /usr/local/bin/php /home/cpanelusr/public_html/api/cron/archive-alumni.php
```

---

## Troubleshooting

| Issue | Solution |
|---|---|
| **500 Internal Server Error** | Check `error_log` in cPanel → Error Log. Common: missing PHP extension or syntax error |
| **404 on API routes** | Ensure `mod_rewrite` is enabled and `.htaccess` files are uploaded |
| **CORS errors in browser** | Verify `cors.php` has your frontend domain in `$allowed_origins` |
| **Database connection failed** | Check credentials in `database.php`. Ensure MySQL user has privileges |
| **File upload fails** | Check `php.ini` limits and `uploads/` folder permissions (755) |
| **JWT token invalid** | Ensure `JWT_SECRET` is the same across all server instances |
| **Blank page / JSON parse error** | PHP error output is mixing with JSON. Set `display_errors = Off` in production |
| **Slow queries** | Add database indexes (already included in schema). Check slow query log |
| **Excel import fails** | Verify file is .xlsx/.csv format, check column headers match template |
| **Student duplicate error** | Admission number already exists. Use different admission number or update existing |

### Enable Error Logging (Development Only)

Add to `api/config/database.php`:
```php
// REMOVE THESE IN PRODUCTION
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## Security Checklist

- [ ] Change default admin password immediately after first login
- [ ] Use a strong `JWT_SECRET` (64+ random characters)
- [ ] Set `database.php` file permission to `600`
- [ ] Enable SSL (HTTPS) — never run without it
- [ ] Set `display_errors = Off` in production
- [ ] Enable rate limiting on login endpoint
- [ ] Use prepared statements for ALL database queries (already in schema)
- [ ] Validate and sanitize all user inputs
- [ ] Set proper CORS origins (remove `localhost` in production)
- [ ] Regular database backups via cron
- [ ] Keep PHP and MySQL updated
- [ ] Block direct access to config/model/helper files via `.htaccess`
- [ ] Use `httponly` and `secure` flags for any cookies
- [ ] Implement CSRF protection for form submissions
- [ ] Validate file types and sizes on upload (server-side)
- [ ] Sanitize uploaded file names to prevent path traversal
- [ ] Hash all passwords with `bcrypt` (already in schema)
- [ ] Log all admin actions to `audit_logs` table

---

## Quick Start Summary

```
1. Create MySQL database + user in cPanel
2. Import schema.sql via phpMyAdmin
3. Upload api/ folder to public_html/
4. Edit api/config/database.php with your credentials
5. Upload frontend dist/ contents to public_html/
6. Add both .htaccess files
7. Enable SSL
8. Login at https://yourdomain.com/login
   Email: admin@school.com
   Password: admin123
9. CHANGE THE DEFAULT PASSWORD!
```

---

## Support

- **Frontend Issues:** Check browser console (F12) for errors
- **API Issues:** Check cPanel Error Log
- **Database Issues:** Use phpMyAdmin to inspect tables
- **Hosting Issues:** Contact your cPanel hosting provider
- **Documentation:** See [README.md](./README.md) for project overview

---

*JSchoolAdmin v1.1.0 — Modern School Management System — Powered by JNV Tech*
