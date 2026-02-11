

## Plan: Core Team Section + Public Teachers Page

### What This Plan Does

1. **Home Page "Our Core Team" Section** -- Adds a section on the homepage (like your reference image) showing key school leaders (Principal, Director, etc.) with photos, names, designations, and emails. Includes a "View Our Teachers" button linking to the full teachers page.

2. **New Public Teachers Page (`/public/teachers.php`)** -- A beautifully designed page matching the Aryan School reference site with:
   - Hero banner with gradient background, title "Our Teachers", and stats (teacher count, years)
   - Principal's Message section with photo and quote
   - Teacher cards grid with hover flip effects showing name, designation, qualification, and subject
   - Responsive design for mobile

3. **Admin Backend Updates** -- Adds fields so admin can control which teachers appear in the Core Team and upload their details:
   - New `designation` field (e.g., Principal, Director, Correspondent, Teacher)
   - New `is_core_team` flag to mark core team members
   - New `bio` field for Principal's Message or short description
   - Updated teacher form with these new fields

4. **Navigation Update** -- Adds "Our Teachers" link to the main navbar

---

### Technical Details

#### A. Database Changes (ALTER TABLE)

Add 3 new columns to the `teachers` table:

```sql
ALTER TABLE `teachers`
  ADD COLUMN `designation` VARCHAR(100) DEFAULT 'Teacher' AFTER `name`,
  ADD COLUMN `is_core_team` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `bio` TEXT DEFAULT NULL AFTER `is_core_team`;
```

Update `schema.sql` to include these columns in the CREATE TABLE definition.

#### B. Files to Create

| File | Purpose |
|------|---------|
| `public/teachers.php` | Public teachers page with hero, principal message, teacher grid |

#### C. Files to Modify

| File | Changes |
|------|---------|
| `index.php` | Add "Our Core Team" section before Contact section; query teachers where `is_core_team=1` |
| `admin/teacher-form.php` | Add designation, is_core_team checkbox, and bio textarea fields |
| `admin/teachers.php` | Show designation column; add core team badge indicator |
| `schema.sql` | Add new columns to teachers table definition |
| `README.md` | Add ALTER TABLE upgrade snippet for v3.0 users |

#### D. Home Page Core Team Section Design

- Section title "Our Core Team" with a yellow "VIEW OUR TEACHERS" button (matching reference image)
- Card grid (3 columns on desktop) showing:
  - Teacher photo (square, with subtle shadow)
  - Name, designation (e.g., "Principal"), and email
- Data pulled dynamically from `teachers` table where `is_core_team = 1`

#### E. Public Teachers Page Design (matching reference)

- **Hero**: Dark gradient background with "Our Educators" badge, large "Our Teachers" heading, description text, and stat cards (teacher count, experience)
- **Principal's Message**: Left side photo, right side quote box with name/designation/qualification
- **Teachers Grid**: 3-4 column responsive cards with:
  - Photo with overlay on hover showing qualification and subject
  - Name and designation below
  - Smooth hover animation (scale + shadow)
- **Footer**: Reuse existing footer from index.php

#### F. Admin Teacher Form Updates

- Add "Designation" text input (default: "Teacher")
- Add "Core Team Member" checkbox toggle
- Add "Bio / Message" textarea (used for Principal's Message section)

