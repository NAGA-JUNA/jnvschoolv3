

## Update README.md and schema.sql — Complete v3.3 Documentation

### Overview
Rewrite the README.md with full documentation of every admin/teacher/public feature, complete setup guide, and accurate file structure. Update schema.sql to v3.3 with the missing `maintenance_mode` setting and correct table count.

---

### 1. README.md — Full Rewrite

**Version bump:** v3.2 to v3.3, table count 13 to 24

**New/updated sections:**

- **Overview** — v3.3, 24 tables
- **Deployment Guide** — Same 9 steps but with corrected table count (24), updated directory tree including all new files:
  - `admin/certificates.php`
  - `admin/feature-cards.php`
  - `admin/fee-structure.php`
  - `admin/footer-manager.php`
  - `admin/navigation-settings.php`
  - `admin/page-content-manager.php`
  - `admin/quote-highlight.php`
  - `admin/upload-gallery.php`
  - `admin/ajax/` folder (7 AJAX handlers)
  - `public/certificates.php`
  - `public/fee-structure.php`
  - `uploads/branding/` subfolder
  - `uploads/certificates/` subfolder
  - `uploads/feature-cards/` subfolder
- **Full table listing** — All 24 tables with descriptions:
  1. users
  2. students
  3. teachers
  4. admissions
  5. notifications
  6. notification_reads
  7. notification_versions
  8. notification_attachments
  9. gallery_items
  10. gallery_categories
  11. gallery_albums
  12. events
  13. attendance
  14. exam_results
  15. audit_logs
  16. settings
  17. home_slider
  18. site_quotes
  19. leadership_profiles
  20. nav_menu_items
  21. certificates
  22. feature_cards
  23. fee_structures
  24. fee_components

- **Admin Panel features** — Document every admin page:
  - Dashboard, Students, Teachers, Admissions, Notifications, Gallery, Events, Slider
  - **Certificates** — Upload/manage school certificates and accreditations
  - **Feature Cards** — Manage homepage quick-link cards with icons, colors, badges, analytics
  - **Fee Structure** — Class-wise fee management with components and frequencies
  - **Footer Manager** — Edit footer description, quick links, programs, contact info, social links
  - **Navigation Settings** — Drag-and-drop navbar menu editor with visibility, CTA, icons
  - **Page Content Manager** — Per-page hero text, section toggles, inline quote editing
  - **Quote Highlight** — Standalone inspirational quote editor (also available inline in Page Content Manager)
  - **Settings** — General (school info, logo, favicon, maintenance mode), Theme Color (with brand extraction), User Management, SMTP/Email, Danger Zone
  - **Reports** — CSV exports
  - **Audit Logs** — Searchable log viewer
  - **Support** — Help/support page

- **Teacher Panel** — Same as before

- **Public Website** — Add new public pages:
  - **Certificates** — Public certificates showcase
  - **Fee Structure** — Public fee information

- **Maintenance Mode** — New section explaining:
  - Toggle in Settings > General tab
  - Shows branded maintenance page to non-logged-in visitors
  - Admins/teachers can still access the site
  - Login page always accessible

- **Settings Keys Reference** — Complete list of all ~80+ settings keys with descriptions

- **Upgrade section** — Add v3.2 to v3.3 migration (maintenance_mode setting + any new tables)

- **Troubleshooting** — Keep existing + add maintenance mode troubleshooting

---

### 2. schema.sql — Update to v3.3

**Changes:**
- Update header comment from v3.2 to v3.3 with 24 tables
- Add missing `maintenance_mode` setting:
  ```sql
  ('maintenance_mode', '0'),
  ```
- Ensure all settings keys used by the codebase are present in the INSERT statements

---

### Technical Details

**Files modified:**
- `php-backend/README.md` — Full rewrite with comprehensive documentation
- `php-backend/schema.sql` — Version bump + add `maintenance_mode` setting

**No structural or logic changes** — documentation and schema defaults only.

