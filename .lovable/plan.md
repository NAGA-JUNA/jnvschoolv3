

## Add Dedicated "Upload Gallery" Page to Admin Panel

### Overview

Create a new `admin/upload-gallery.php` page that mirrors the Teacher panel's "Upload to Gallery" experience — a full-page, two-column layout with the upload form on the left and the admin's own uploads on the right. A new sidebar link will be added for easy access.

### What Changes

**1. New file: `php-backend/admin/upload-gallery.php`**

A dedicated admin upload page based on the teacher's `upload-gallery.php`, with these differences:
- Uses `requireAdmin()` instead of `requireTeacher()`
- Uploads are **auto-approved** (status = 'approved', approved_by = current admin, approved_at = NOW())
- Includes the "Featured" checkbox option (already available in admin gallery)
- Redirects back to `/admin/upload-gallery.php`
- "My Uploads" sidebar on the right shows the admin's own uploads with pagination
- Same Single / Multiple / ZIP upload tabs, drag-and-drop zones, compression toggle, metadata fields (event name, date, tags, visibility, description)

**2. Modify: `php-backend/includes/header.php`**

Add a new sidebar nav link under the existing "Gallery" link:

```
Upload Gallery
```

This appears for admins who have gallery feature access, placed right after the Gallery link.

**3. No changes to existing `admin/gallery.php`**

The existing gallery management page (approval, filtering, batch actions) remains completely untouched. The new page is purely for uploading.

### Files Summary

| File | Action | Purpose |
|------|--------|---------|
| `php-backend/admin/upload-gallery.php` | Create | Dedicated admin upload page matching teacher layout |
| `php-backend/includes/header.php` | Modify | Add "Upload Gallery" sidebar link for admins |

### Technical Details

- Reuses the same `compressGalleryImage()` and `extractGalleryZip()` helper functions (copied into the new file, same pattern as existing admin/gallery.php)
- Admin uploads are auto-approved with `status='approved'`
- Layout: `col-lg-8` for upload form, `col-lg-4` for "My Uploads" panel (matching teacher layout proportions)
- All existing gallery management features (approve/reject/delete/batch) stay in `admin/gallery.php`

