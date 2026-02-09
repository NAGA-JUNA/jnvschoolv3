

# Complete PHP Backend Files for JSchoolAdmin

## What I'll Create

I'll write every PHP file needed for the backend -- **30+ files** organized into the directory structure from your setup guide. Once done, you can download them all from Lovable and upload directly to your `public_html/api/` folder.

## Files to Create

### Config Files (3 files)
- `api/config/database.php` -- DB connection, JWT config, PDO singleton
- `api/config/constants.php` -- App-wide constants (roles, statuses, pagination defaults)
- `api/config/cors.php` -- CORS headers and preflight handling

### Helpers (5 files)
- `api/helpers/response.php` -- `jsonSuccess()`, `jsonError()`, `jsonPaginated()` helpers
- `api/helpers/jwt.php` -- JWT encode/decode/verify using HMAC-SHA256
- `api/helpers/validator.php` -- Input validation (required, email, phone, length, etc.)
- `api/helpers/upload.php` -- File upload with type/size validation, unique naming
- `api/helpers/excel.php` -- CSV/Excel import/export for students and teachers

### Middleware (2 files)
- `api/middleware/auth.php` -- JWT token extraction, `requireAuth()`, `requireRole()`, `currentUserId()`, `auditLog()`
- `api/middleware/rate-limit.php` -- Simple file-based rate limiting for login endpoint

### Controllers (18 files)
- `api/controllers/AuthController.php` -- Login (bcrypt verify + JWT), logout, me
- `api/controllers/DashboardController.php` -- Admin/teacher dashboard metrics and activity
- `api/controllers/StudentController.php` -- Full CRUD, search/filter/paginate, bulk promote, alumni, export/import
- `api/controllers/TeacherController.php` -- Full CRUD, class assignment, inactive list, export/import, teacher profile
- `api/controllers/AttendanceController.php` -- Mark attendance, student/teacher attendance history
- `api/controllers/ExamController.php` -- Enter marks, student exam results
- `api/controllers/DocumentController.php` -- Upload/list/delete documents for students and teachers
- `api/controllers/MessageController.php` -- Student and teacher WhatsApp message history
- `api/controllers/NotificationController.php` -- CRUD, approve/reject, bulk approve, teacher submit, public listing
- `api/controllers/GalleryController.php` -- Categories CRUD, approvals, teacher upload, YouTube links, public listing
- `api/controllers/EventController.php` -- CRUD, public listing
- `api/controllers/AdmissionController.php` -- Public submit, admin list, status update, export
- `api/controllers/WhatsAppController.php` -- Log shares, view history
- `api/controllers/EmailController.php` -- List/create official email accounts
- `api/controllers/ReportController.php` -- Generate report data (counts, trends)
- `api/controllers/BrandingController.php` -- Settings key-value CRUD, branding config
- `api/controllers/AuditLogController.php` -- List audit logs with filters
- `api/controllers/SliderController.php` -- Already documented, full CRUD + reorder

### Router and Access Control (2 files)
- `api/index.php` -- Main router (already documented, will be the actual file)
- `api/.htaccess` -- API rewrite rules

### Root (1 file)
- `.htaccess` (root) -- SPA routing + API passthrough

**Total: ~31 PHP files**

## Technical Approach

- All controllers use **PDO prepared statements** (SQL injection safe)
- **JWT authentication** using HMAC-SHA256 (no external libraries needed)
- **Role-based access** via `requireRole()` middleware
- **Pagination** helper reused across all list endpoints
- **Audit logging** on all create/update/delete actions
- **File uploads** with type validation, size limits, and unique filenames
- **Consistent JSON responses** via `jsonSuccess()` / `jsonError()` helpers
- All files are **vanilla PHP 8.0+** -- no Composer dependencies required

## After Implementation

1. Download all `api/` files from Lovable
2. Upload to `public_html/api/` on your server
3. Edit `api/config/database.php` with your MySQL credentials
4. Import `schema.sql` into your database
5. Set permissions (755 for directories, 644 for PHP files)
6. You're live

