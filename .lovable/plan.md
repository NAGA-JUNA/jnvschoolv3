

## Upgrade Gallery Upload System (Admin + Teacher) with Advanced Upload Options

This is a significant feature upgrade that adds multi-image upload, ZIP bulk upload, image compression, and enhanced metadata to the gallery system across both Admin and Teacher panels.

### Overview of Changes

The gallery upload system will be enhanced with three upload modes, server-side image compression, richer metadata fields, and improved admin approval workflow -- all while preserving existing functionality.

---

### 1. Database Schema Changes (`schema.sql`)

Add new columns to the existing `gallery_items` table:

```sql
ALTER TABLE gallery_items ADD COLUMN `event_name` VARCHAR(200) DEFAULT NULL AFTER `description`;
ALTER TABLE gallery_items ADD COLUMN `event_date` DATE DEFAULT NULL AFTER `event_name`;
ALTER TABLE gallery_items ADD COLUMN `tags` VARCHAR(500) DEFAULT NULL AFTER `event_date`;
ALTER TABLE gallery_items ADD COLUMN `visibility` ENUM('public','private') NOT NULL DEFAULT 'public' AFTER `tags`;
ALTER TABLE gallery_items ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `visibility`;
ALTER TABLE gallery_items ADD COLUMN `original_size` INT UNSIGNED DEFAULT NULL AFTER `is_featured`;
ALTER TABLE gallery_items ADD COLUMN `compressed_size` INT UNSIGNED DEFAULT NULL AFTER `original_size`;
ALTER TABLE gallery_items ADD COLUMN `batch_id` VARCHAR(32) DEFAULT NULL AFTER `compressed_size`;
```

- `event_name`, `event_date`, `tags` -- metadata per image
- `visibility` -- public/private toggle
- `is_featured` -- admin-only featured flag
- `original_size` / `compressed_size` -- track compression savings
- `batch_id` -- groups images uploaded together (multi/ZIP)

---

### 2. Teacher Upload Page (`teacher/upload-gallery.php`)

**Restructure the upload form** with a 3-mode tab switcher:

| Tab | Icon | Behavior |
|-----|------|----------|
| Single | bi-image | Current behavior, enhanced with drag-drop area + preview thumbnail |
| Multiple | bi-images | `<input type="file" multiple>` with thumbnail previews for all selected files |
| ZIP | bi-file-zip | Single ZIP upload, shows filename + extracted file count after selection |

**New form fields** (shared across all modes):
- Category dropdown (existing)
- Event Name (text input, optional)
- Event Date (date input, optional)
- Tags (text input, comma-separated, optional)
- Visibility (radio: Public / Private)
- Compression checkbox: "Compress images for faster loading (Recommended)" -- checked by default
- Description textarea (existing)

**Upload flow**:
- All teacher uploads go to `status = 'pending'` (unchanged)
- Multiple images generate a shared `batch_id` so they appear grouped in "My Uploads"
- ZIP files are extracted server-side; each valid image becomes a separate `gallery_items` row with shared `batch_id`

**My Uploads section** enhancements:
- Group items by `batch_id` as collapsible album cards
- Show upload count per batch and individual statuses

**Client-side JavaScript**:
- Tab switching shows/hides the appropriate file input area
- Drag-and-drop zone with visual feedback (border highlight on dragover)
- Preview thumbnails via `FileReader` API for single/multiple modes
- ZIP mode shows filename and "Processing on server..." text
- File validation: only JPG/PNG/WebP/GIF accepted; ZIP max 50MB

---

### 3. Admin Gallery Page (`admin/gallery.php`)

**Add an upload section** at the top (collapsible card) with the same 3-mode upload form as the teacher page, but with these differences:
- Admin uploads are `status = 'approved'` automatically
- Admin gets a "Featured" toggle checkbox
- Admin can set visibility

**Approval enhancements**:
- "Approve All" and "Reject All" buttons for batch operations on pending items
- When viewing a batch (same `batch_id`), show a grouped card with bulk approve/reject
- Show compressed vs. original size saved per image (e.g., "Saved 45% -- 2.1MB to 1.2MB")

---

### 4. Server-Side Image Compression (PHP)

Create a helper function in a new utility or inline in both upload pages:

```php
function compressGalleryImage($sourcePath, $destPath, $maxWidth = 1600) {
    // Uses GD library (standard PHP)
    // 1. Read image with imagecreatefromjpeg/png/webp/gif
    // 2. Calculate new dimensions maintaining aspect ratio
    // 3. Resize with imagecopyresampled
    // 4. Save as WebP if supported (imagewebp), else original format
    // 5. Return [original_size, compressed_size]
}
```

- When compression is enabled, images are resized to max 1600px width and converted to WebP (if GD supports it), otherwise saved as optimized JPEG at 80% quality
- Original aspect ratio is always preserved
- Original and compressed file sizes are stored in the database

---

### 5. ZIP Extraction Handler (PHP)

```php
function extractGalleryZip($zipPath, $uploadDir, $compress, $maxWidth) {
    // Uses ZipArchive (standard PHP extension)
    // 1. Open ZIP, validate size <= 50MB
    // 2. Iterate entries, filter by extension (jpg/png/webp/gif)
    // 3. Extract each valid image to temp, then compress if enabled
    // 4. Sanitize filenames (remove special chars, add unique prefix)
    // 5. Return array of [filename, original_size, compressed_size]
    // 6. Reject ZIP if 0 valid images found
}
```

- Filenames are sanitized: special characters removed, prefixed with `gallery_` + timestamp + random hex
- Non-image files in the ZIP are silently skipped
- If no valid images found, flash error "ZIP contains no valid images"

---

### 6. Validation Rules

| Rule | Value |
|------|-------|
| Allowed image types | JPG, PNG, WebP, GIF |
| Max single image size | 5MB |
| Max ZIP size | 50MB |
| Max images per multi-upload | 20 |
| Compression max width | 1600px |
| Compression format | WebP (fallback: JPEG 80%) |

---

### 7. UI/UX Details

**Drag-and-drop zone** (all modes):
```text
+---------------------------------------------+
|                                             |
|      [cloud-upload icon]                    |
|      Drag & drop files here                 |
|      or click to browse                     |
|                                             |
|      (accepted: JPG, PNG, WebP, GIF)        |
+---------------------------------------------+
```

**Preview thumbnails** (single/multiple):
- Small 80x80px thumbnail grid below the drop zone
- Each thumbnail has a remove (X) button
- File name and size shown below each thumbnail

**ZIP mode display**:
- After selecting ZIP: shows filename, file size, and "Will be extracted on upload"
- After upload: server returns count of extracted images

**Compression toggle**:
```text
[x] Compress images for faster loading (Recommended)
    Resizes to max 1600px width, converts to WebP format
```

**Mobile considerations**:
- Drag-drop area becomes a large tap-to-browse button on mobile
- ZIP upload tab hidden on screens < 576px (optional, keeps it simple)
- All form fields stack vertically on mobile
- Preview thumbnails wrap in a scrollable row

---

### Files to Create

| File | Purpose |
|------|---------|
| (none) | All changes go into existing files |

### Files to Modify

| File | Changes |
|------|---------|
| `teacher/upload-gallery.php` | Complete rewrite of upload form with 3-mode tabs, drag-drop, compression, metadata fields, batch grouping in My Uploads |
| `admin/gallery.php` | Add collapsible upload form (same 3 modes with admin extras), bulk approve/reject, compression stats display |
| `schema.sql` | Add new columns to `gallery_items` table (event_name, event_date, tags, visibility, is_featured, original_size, compressed_size, batch_id) |

### Technical Notes

- PHP GD library is required for compression (standard on most hosts)
- PHP ZipArchive extension is required for ZIP handling (standard on most hosts)
- The compression function and ZIP extraction logic will be defined at the top of each upload page (or in a shared include if preferred, but keeping it self-contained per the existing pattern)
- All file operations use `move_uploaded_file()` and server-side validation -- no client-side-only checks
- `batch_id` is generated as `bin2hex(random_bytes(16))` for uniqueness

