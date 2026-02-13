

## Certificates and Accreditations Module

### What You Get

A complete module that lets you **upload, manage, and showcase** your school's certificates (CBSE Affiliation, Government Approvals, ISO Certifications, Awards, etc.) on both the **Home Page** and a dedicated **Certificates page** -- all manageable from the Admin Panel.

---

### Feature Breakdown

#### 1. Home Page -- Certificate Showcase Section
- A premium section titled **"Our Certifications and Accreditations"** appears on the Home Page (below existing sections)
- Displays up to 4-6 featured certificates in a responsive card grid
- Each card shows: thumbnail, title, category badge (e.g. "Govt Approved", "CBSE Affiliation", "ISO Certified"), and year
- Hover animation reveals description; click opens a fullscreen lightbox preview
- Lazy loading for images
- Admin can toggle the entire section on/off and control how many certificates show (via Settings)

#### 2. Public Certificates Page (`/public/certificates.php`)
- Full grid/masonry layout of all active certificates
- Category filter pills: Government Approval, Board Affiliation, Recognition, Awards
- Search by title
- Click any certificate to open a fullscreen lightbox modal
- Download button (if admin has enabled downloads for that certificate)
- Pagination (12 per page)
- SEO meta tags
- Reuses existing navbar and footer includes for consistency

#### 3. Admin Panel -- Certificate Management (`/admin/certificates.php`)
- Full CRUD interface matching existing admin UI patterns (Bootstrap 5.3, dark/light theme compatible)
- **Upload modes**: Single image/PDF, multi-file, and bulk ZIP upload (reuses the proven gallery compression and ZIP extraction helpers)
- **Auto thumbnail generation**: Creates a 400px-wide WebP thumbnail on upload; PDFs get a placeholder thumbnail
- **Fields per certificate**: Title, Description, Category (dropdown), Year, Status (Active/Inactive), Featured on Home (Yes/No), Allow Download (Yes/No)
- **Drag-and-drop reorder** via AJAX (same pattern as nav menu items)
- Edit / Replace file, Preview before publish
- **Soft delete** with restore option (is_deleted flag + trash view)
- Toggle visibility inline
- Audit logging for all actions

#### 4. Admin Settings Integration
Two new toggles added to the existing Settings page (Content tab or a new sub-section):
- **Show Certificates on Home Page** (On/Off) -- stored as `home_certificates_show`
- **Max certificates on Home** (number) -- stored as `home_certificates_max`
- **Enable Public Certificates Page** (On/Off) -- stored as `certificates_page_enabled`

#### 5. Navigation Integration
- Add "Certificates" link to the public navbar (via nav_menu_items table, same as existing menu items)

---

### Advanced Features (Bonus)

Beyond your core requirements, here are advanced features that can be added later:

1. **Certificate Verification Portal** -- Public page where visitors enter a certificate number to verify authenticity (useful for affiliation numbers)
2. **Expiry Tracking and Alerts** -- Add `valid_from` / `valid_until` dates; admin dashboard shows expiring certificates with email alerts
3. **Certificate Timeline** -- Interactive chronological timeline view showing the school's accreditation journey
4. **QR Code Generation** -- Auto-generate QR codes linking to the certificate's public verification page
5. **Watermarking** -- Auto-apply a semi-transparent school logo watermark on certificate images to prevent misuse
6. **Multi-language Support** -- Store certificate titles/descriptions in multiple languages
7. **Analytics Dashboard** -- Track views and downloads per certificate with charts
8. **Bulk Category Management** -- Admin interface to add/rename/reorder certificate categories
9. **Certificate Sharing** -- Social media share buttons (WhatsApp, Facebook) for individual certificates
10. **Print-Ready View** -- Optimized print stylesheet for individual certificate detail pages

---

### Database Schema

New table `certificates`:

```text
+------------------+-------------------+-------------------------------------------+
| Column           | Type              | Details                                   |
+------------------+-------------------+-------------------------------------------+
| id               | INT UNSIGNED PK   | Auto-increment                            |
| title            | VARCHAR(255)      | Certificate name                          |
| description      | TEXT              | Optional description                      |
| category         | VARCHAR(100)      | govt_approval / board / recognition /     |
|                  |                   | awards                                    |
| year             | SMALLINT          | Year of issue                             |
| file_path        | VARCHAR(255)      | Path to full-size file                    |
| thumb_path       | VARCHAR(255)      | Path to thumbnail                         |
| file_type        | ENUM(image,pdf)   | Type of uploaded file                     |
| is_featured      | TINYINT(1)        | Show on Home page                         |
| is_active        | TINYINT(1)        | Visible on public site                    |
| allow_download   | TINYINT(1)        | Enable download button                   |
| display_order    | INT               | Drag-and-drop sort order                  |
| is_deleted       | TINYINT(1)        | Soft delete flag                          |
| deleted_at       | DATETIME          | When soft-deleted                         |
| created_by       | INT UNSIGNED FK   | References users(id)                      |
| created_at       | DATETIME          | Auto timestamp                            |
| updated_at       | DATETIME          | Auto-updated timestamp                    |
+------------------+-------------------+-------------------------------------------+
```

New settings keys: `home_certificates_show`, `home_certificates_max`, `certificates_page_enabled`

---

### Files to Create / Modify

| File | Action | Purpose |
|------|--------|---------|
| `php-backend/schema.sql` | Modify | Add `certificates` table definition and new settings rows |
| `php-backend/admin/certificates.php` | Create | Full admin CRUD page (list, add, edit, delete, reorder, trash) |
| `php-backend/admin/ajax/certificate-actions.php` | Create | AJAX endpoints for reorder, toggle, delete, restore |
| `php-backend/public/certificates.php` | Create | Public certificates page with filters, search, lightbox |
| `php-backend/index.php` | Modify | Add certificates showcase section to Home Page |
| `php-backend/admin/settings.php` | Modify | Add certificate toggle settings to Content tab |
| `php-backend/includes/header.php` | Modify | Add "Certificates" to admin sidebar nav |

### Security
- Admin-only access for all management endpoints (existing `requireAdmin()` pattern)
- File type validation: jpg, jpeg, png, webp, pdf only
- MIME type validation via `finfo_file()`
- Max file size: 10MB per file, 50MB for ZIP
- CSRF protection on all forms (existing `verifyCsrf()` / `csrfField()`)
- PDO prepared statements for all queries (existing pattern)
- `.htaccess` rules prevent direct execution of uploaded files

### Performance
- Image compression to WebP (reuses gallery's `compressGalleryImage()` helper)
- Auto thumbnail generation (400px width)
- Lazy loading via `loading="lazy"` attribute
- Pagination on public page (12 per page)
- Database indexes on `is_active`, `is_featured`, `display_order`, `category`

