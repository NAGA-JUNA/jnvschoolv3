

# Fix Image Display, Print Layout, and Header Date/Time + Cleanup

## Overview
Fix three bugs in the student profile modal (broken photo, print layout), add date/time to the header, and remove legacy files outside `php-backend/`.

---

## 1. Fix Profile Photo in Student Modal

**Problem**: The photo `data-photo` attribute stores `/uploads/photos/filename.jpg` but the student-form saves the full path including `/uploads/photos/` in the database. When the table row builds `$photoUrl`, it prepends `/uploads/photos/` again if `$s['photo']` already contains the full path, resulting in a double-path like `/uploads/photos//uploads/photos/file.jpg`.

**Fix in `admin/students.php`**:
- Change line 30 from:
  ```php
  $photoUrl = $s['photo'] ? '/uploads/photos/'.$s['photo'] : '';
  ```
  to:
  ```php
  $photoUrl = $s['photo'] ? (str_starts_with($s['photo'], '/uploads/') ? $s['photo'] : '/uploads/photos/'.$s['photo']) : '';
  ```
- This handles both cases: if the photo column stores just the filename or the full path.

**Same fix needed in `admin/teachers.php`** for teacher photos.

---

## 2. Fix Print Layout

**Problem**: `window.print()` prints the entire page (sidebar, top bar, table) instead of just the modal content.

**Fix**: Add a `@media print` CSS block in `students.php` (and `teachers.php`) that:
- Hides the sidebar, top bar, overlay, and main page content
- Shows only the modal content full-width
- Removes the modal backdrop
- Ensures the modal body is visible and properly laid out

```css
@media print {
  .sidebar, .sidebar-overlay, .top-bar, .content-area > *:not(.modal) { display: none !important; }
  .main-content { margin-left: 0 !important; }
  .modal { position: static !important; display: block !important; }
  .modal-dialog { max-width: 100% !important; margin: 0 !important; }
  .modal-content { border: none !important; box-shadow: none !important; }
  .modal-footer { display: none !important; }
  .modal-backdrop { display: none !important; }
}
```

---

## 3. Add Date/Time to Header

**Fix in `includes/header.php`**: Add a date/time display in the top bar between the page title and user info:
```php
<span class="text-muted" style="font-size:0.8rem;">
  <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
  <i class="bi bi-clock ms-2 me-1"></i><?= date('h:i A') ?>
</span>
```

---

## 4. Remove Legacy Files

Delete all non-`php-backend` project files that belong to the old React/REST API system:

**Directories to remove**:
- `api/` (old REST API)
- `src/` (old React frontend)
- `public/` (old React public assets)
- `.lovable/` (old plan data)

**Root files to remove**:
- `schema.sql` (duplicate; the one in php-backend is the canonical copy)
- `BACKEND-SETUP-README.md`
- `README.md` (root level; php-backend has its own)
- `index.html`, `vite.config.ts`, `tsconfig*.json`, `tailwind.config.ts`, `postcss.config.js`, `eslint.config.js`, `vitest.config.ts`, `components.json`
- `package.json`, `package-lock.json`, `bun.lockb`, `bun.lock`

**Keep**: `php-backend/` folder (the entire working system) and `.gitignore`.

---

## Files Modified
1. `php-backend/admin/students.php` -- fix photo path, add print CSS
2. `php-backend/admin/teachers.php` -- fix photo path, add print CSS
3. `php-backend/includes/header.php` -- add date and time display

## Files Deleted
- `api/` directory (all files)
- `src/` directory (all files)
- `public/` directory (all files)
- `.lovable/` directory
- Root-level config and build files (package.json, vite.config.ts, etc.)
- Root-level schema.sql, README.md, BACKEND-SETUP-README.md, index.html

