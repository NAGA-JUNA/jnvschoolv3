# SchoolAdmin — PHP Backend Setup Guide (cPanel)

Complete step-by-step guide to set up the SchoolAdmin REST API backend on a cPanel-hosted server with MySQL.

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
12. [API Endpoints Reference](#api-endpoints-reference)
13. [Database Schema](#database-schema)
14. [Authentication Flow](#authentication-flow)
15. [CORS Configuration](#cors-configuration)
16. [File Upload Configuration](#file-upload-configuration)
17. [Cron Jobs](#cron-jobs)
18. [Troubleshooting](#troubleshooting)
19. [Security Checklist](#security-checklist)

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
│   │   ├── StudentController.php
│   │   ├── TeacherController.php
│   │   ├── NotificationController.php
│   │   ├── GalleryController.php
│   │   ├── EventController.php
│   │   ├── AdmissionController.php
│   │   ├── WhatsAppController.php
│   │   ├── EmailController.php
│   │   ├── ReportController.php
│   │   └── AuditLogController.php
│   ├── models/
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Notification.php
│   │   ├── GalleryCategory.php
│   │   ├── GalleryItem.php
│   │   ├── Event.php
│   │   ├── Admission.php
│   │   └── AuditLog.php
│   ├── helpers/
│   │   ├── response.php          # JSON response helper
│   │   ├── validator.php         # Input validation
│   │   ├── jwt.php               # JWT token helper
│   │   └── upload.php            # File upload helper
│   └── uploads/                  # Uploaded files directory
│       ├── gallery/
│       ├── notifications/
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
   - Database name: `schooladmin_db`
3. Create a new user:
   - Username: `schooladmin_user`
   - Password: *(use a strong password, save it)*
4. Add user to database:
   - Select the user and database
   - Grant **ALL PRIVILEGES**
   - Click **Make Changes**

> **Note:** cPanel prefixes your account name, so your actual database name will be like `cpanelusr_schooladmin_db`.

---

## Step 2: Import Database Schema

1. Open **phpMyAdmin** from cPanel
2. Select your newly created database
3. Click the **SQL** tab
4. Paste and execute the following schema:

```sql
-- =============================================
-- SchoolAdmin Database Schema
-- Version: 1.0.0
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+05:30";

-- -------------------------------------------
-- Users (Admin & Teacher login)
-- -------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'teacher', 'office') NOT NULL DEFAULT 'teacher',
  `phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(100) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Students
-- -------------------------------------------
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admission_no` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `class` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) DEFAULT NULL,
  `roll_no` INT DEFAULT NULL,
  `parent_name` VARCHAR(100) DEFAULT NULL,
  `parent_phone` VARCHAR(20) DEFAULT NULL,
  `parent_email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `gender` ENUM('male', 'female', 'other') DEFAULT NULL,
  `blood_group` VARCHAR(5) DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'graduated', 'transferred') DEFAULT 'active',
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Notifications
-- -------------------------------------------
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `body` TEXT NOT NULL,
  `urgency` ENUM('normal', 'important', 'urgent') DEFAULT 'normal',
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `submitted_by` INT NOT NULL,
  `approved_by` INT DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `is_public` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Gallery Categories
-- -------------------------------------------
CREATE TABLE `gallery_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) UNIQUE NOT NULL,
  `type` ENUM('images', 'videos') DEFAULT 'images',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `item_count` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Gallery Items
-- -------------------------------------------
CREATE TABLE `gallery_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `title` VARCHAR(200) DEFAULT NULL,
  `file_url` VARCHAR(500) NOT NULL,
  `thumbnail_url` VARCHAR(500) DEFAULT NULL,
  `type` ENUM('image', 'video', 'youtube') DEFAULT 'image',
  `youtube_id` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `uploaded_by` INT NOT NULL,
  `approved_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `gallery_categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Events
-- -------------------------------------------
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME DEFAULT NULL,
  `location` VARCHAR(200) DEFAULT NULL,
  `type` ENUM('academic', 'cultural', 'sports', 'meeting', 'holiday', 'other') DEFAULT 'other',
  `is_public` TINYINT(1) DEFAULT 1,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Admissions (Online Applications)
-- -------------------------------------------
CREATE TABLE `admissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_name` VARCHAR(100) NOT NULL,
  `class_applied` VARCHAR(20) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `gender` ENUM('male', 'female', 'other') NOT NULL,
  `parent_name` VARCHAR(100) NOT NULL,
  `parent_phone` VARCHAR(20) NOT NULL,
  `parent_email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT NOT NULL,
  `previous_school` VARCHAR(200) DEFAULT NULL,
  `documents` JSON DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected', 'waitlisted') DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `reviewed_by` INT DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Official Emails
-- -------------------------------------------
CREATE TABLE `official_emails` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `email_address` VARCHAR(150) UNIQUE NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `webmail_url` VARCHAR(300) DEFAULT NULL,
  `status` ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- WhatsApp Share Log
-- -------------------------------------------
CREATE TABLE `whatsapp_shares` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_type` ENUM('notification', 'event', 'admission') NOT NULL,
  `item_id` INT NOT NULL,
  `shared_by` INT NOT NULL,
  `shared_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`shared_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Audit Logs
-- -------------------------------------------
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT DEFAULT NULL,
  `details` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Settings (Key-Value Store)
-- -------------------------------------------
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------
-- Indexes for Performance
-- -------------------------------------------
ALTER TABLE `students` ADD INDEX `idx_class_section` (`class`, `section`);
ALTER TABLE `students` ADD INDEX `idx_status` (`status`);
ALTER TABLE `notifications` ADD INDEX `idx_status` (`status`);
ALTER TABLE `notifications` ADD INDEX `idx_submitted_by` (`submitted_by`);
ALTER TABLE `gallery_items` ADD INDEX `idx_category_status` (`category_id`, `status`);
ALTER TABLE `events` ADD INDEX `idx_event_date` (`event_date`);
ALTER TABLE `admissions` ADD INDEX `idx_status` (`status`);
ALTER TABLE `audit_logs` ADD INDEX `idx_user_action` (`user_id`, `action`);
ALTER TABLE `audit_logs` ADD INDEX `idx_created_at` (`created_at`);

-- -------------------------------------------
-- Default Admin User (password: admin123)
-- Change this immediately after first login!
-- -------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- -------------------------------------------
-- Default Settings
-- -------------------------------------------
INSERT INTO `settings` (`key_name`, `value`) VALUES
('school_name', 'My School'),
('school_tagline', 'Excellence in Education'),
('school_phone', '+91-XXXXXXXXXX'),
('school_email', 'info@school.com'),
('school_address', 'School Address Here'),
('academic_year', '2025-2026'),
('whatsapp_groups', '[]');

-- -------------------------------------------
-- Sample Gallery Categories
-- -------------------------------------------
INSERT INTO `gallery_categories` (`name`, `slug`, `type`) VALUES
('Annual Day', 'annual-day', 'images'),
('Sports Day', 'sports-day', 'images'),
('Classroom Activities', 'classroom-activities', 'images'),
('Videos', 'videos', 'videos');
```

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
define('DB_NAME', 'cpanelusr_schooladmin_db');  // Your full database name
define('DB_USER', 'cpanelusr_schooladmin_user'); // Your full database username
define('DB_PASS', 'YOUR_STRONG_PASSWORD_HERE');  // Database password
define('DB_CHARSET', 'utf8mb4');

// JWT Configuration
define('JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_64_CHAR_STRING');
define('JWT_EXPIRY', 86400); // 24 hours in seconds

// App Configuration
define('APP_NAME', 'SchoolAdmin');
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

## API Endpoints Reference

### Public Endpoints (No auth required)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/public/notifications` | List approved public notifications |
| `GET` | `/api/public/gallery/categories` | List gallery categories |
| `GET` | `/api/public/gallery/items?category={slug}` | Items by category |
| `GET` | `/api/public/events` | List upcoming public events |
| `POST` | `/api/public/admissions` | Submit admission application |

### Auth Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/auth/login` | Login (returns JWT token) |
| `POST` | `/api/auth/logout` | Logout / invalidate token |
| `GET` | `/api/auth/me` | Get current user profile |

### Admin Endpoints (Requires `admin` role)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/admin/dashboard/metrics` | Dashboard KPI data |
| `GET` | `/api/admin/students` | List students (paginated) |
| `POST` | `/api/admin/students` | Create student |
| `PUT` | `/api/admin/students/{id}` | Update student |
| `DELETE` | `/api/admin/students/{id}` | Delete student |
| `GET` | `/api/admin/students/export` | Export students to CSV |
| `GET` | `/api/admin/teachers` | List teachers |
| `POST` | `/api/admin/teachers` | Create teacher/staff |
| `PUT` | `/api/admin/teachers/{id}` | Update teacher |
| `DELETE` | `/api/admin/teachers/{id}` | Delete teacher |
| `GET` | `/api/admin/notifications` | List all notifications |
| `PATCH` | `/api/admin/notifications/{id}/approve` | Approve notification |
| `PATCH` | `/api/admin/notifications/{id}/reject` | Reject notification |
| `POST` | `/api/admin/notifications/bulk-approve` | Bulk approve |
| `GET` | `/api/admin/gallery/categories` | List categories |
| `POST` | `/api/admin/gallery/categories` | Create category |
| `PUT` | `/api/admin/gallery/categories/{id}` | Update category |
| `DELETE` | `/api/admin/gallery/categories/{id}` | Delete category |
| `GET` | `/api/admin/gallery/approvals` | Pending gallery items |
| `PATCH` | `/api/admin/gallery/items/{id}/approve` | Approve item |
| `PATCH` | `/api/admin/gallery/items/{id}/reject` | Reject item |
| `GET` | `/api/admin/events` | List all events |
| `POST` | `/api/admin/events` | Create event |
| `PUT` | `/api/admin/events/{id}` | Update event |
| `DELETE` | `/api/admin/events/{id}` | Delete event |
| `GET` | `/api/admin/admissions` | List admission applications |
| `PATCH` | `/api/admin/admissions/{id}` | Update admission status |
| `GET` | `/api/admin/admissions/export` | Export to CSV |
| `POST` | `/api/admin/emails` | Create official email |
| `GET` | `/api/admin/emails` | List official emails |
| `GET` | `/api/admin/reports` | Get report data |
| `GET` | `/api/admin/audit-logs` | List audit logs |
| `GET` | `/api/admin/settings` | Get all settings |
| `PUT` | `/api/admin/settings` | Update settings |
| `POST` | `/api/admin/whatsapp/mark-shared` | Mark item as shared |

### Teacher Endpoints (Requires `teacher` or `office` role)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/teacher/dashboard/metrics` | Teacher dashboard data |
| `POST` | `/api/teacher/notifications` | Submit notification |
| `POST` | `/api/teacher/gallery/upload` | Upload gallery item |
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
| `status` | `?status=pending` | Filter by status |
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

---

## Cron Jobs

Set up in **cPanel** → **Cron Jobs**:

```bash
# Clean expired notifications daily at midnight
0 0 * * * /usr/local/bin/php /home/cpanelusr/public_html/api/cron/cleanup.php

# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/php /home/cpanelusr/public_html/api/cron/backup.php
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

---

## Quick Start Summary

```
1. Create MySQL database + user in cPanel
2. Import SQL schema via phpMyAdmin
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

---

*SchoolAdmin v1.0.0 — Modern School Management System*
