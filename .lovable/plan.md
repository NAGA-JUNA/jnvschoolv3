

# Public School Website Enhancement Plan

## Current State Analysis

Your current public website has **5 pages** with basic content:

| Page | Current State | What's Missing |
|---|---|---|
| **Home** (`/`) | Simple hero + 4 quick-access cards | No school info, stats, principal message, teacher showcase, testimonials, campus photos |
| **Notifications** (`/notifications`) | 3 hardcoded mock notices | Works but needs real-looking data |
| **Gallery** (`/gallery`) | Basic tabs with stock photos | Needs more categories and better layout |
| **Events** (`/events`) | 4 mock events, minimal design | Needs richer content |
| **Admissions** (`/admissions`) | Form works, basic fields | OK as-is, minor polish |
| **Public Layout** (Header/Footer) | Says "SchoolAdmin", no school branding | Needs school name, contact info, social links |

### Missing Pages (that a real school website needs)

| New Page | Purpose |
|---|---|
| **About Us** (`/about`) | School history, vision, mission, principal's message, achievements |
| **Faculty / Teachers** (`/faculty`) | Public teacher directory with photos, subjects, qualifications -- pulled from admin teacher data |
| **Contact Us** (`/contact`) | School address, map, phone, email, contact form |
| **Academics** (`/academics`) | Classes offered, curriculum, exam schedule, facilities |

---

## Pages to Update/Create -- Summary

**6 existing files to update:**
1. `src/pages/public/Home.tsx` -- Full redesign with rich content
2. `src/pages/public/Notifications.tsx` -- More realistic data
3. `src/pages/public/Events.tsx` -- More events, better layout
4. `src/pages/public/Gallery.tsx` -- More categories, better grid
5. `src/components/layout/PublicLayout.tsx` -- Branded header + rich footer
6. `src/App.tsx` -- Add new routes

**4 new pages to create:**
1. `src/pages/public/About.tsx` -- About Us page
2. `src/pages/public/Faculty.tsx` -- Teachers page (reads from mock teacher data)
3. `src/pages/public/Contact.tsx` -- Contact page
4. `src/pages/public/Academics.tsx` -- Academics info page

**1 new data file:**
1. `src/data/mockSchoolData.ts` -- Centralized school info (name, address, stats, facilities, etc.)

---

## How Teacher Data Flows from Admin to Public Website

The **Faculty/Teachers page** on the public website will pull data from the same `mockTeachers` data source used by the admin panel. This means:

- When the backend is connected, the admin panel manages teacher records (name, photo, subjects, qualification)
- The public Faculty page calls the same `/api/public/teachers` endpoint to display only **active** teachers
- For now (demo mode), both admin and public pages read from `src/data/mockTeachers.ts`
- Teacher photos uploaded via Admin Panel will automatically appear on the public website

---

## Detailed Plan

### Step 1: Create School Data File (`src/data/mockSchoolData.ts`)

Centralized data for the public website including:
- School name, address, phone, email
- Principal name and message
- Vision, mission, values
- Statistics (students count, teachers, years established, etc.)
- Facilities list
- Achievements / milestones
- Upcoming academic calendar
- Curriculum info

### Step 2: Redesign Home Page (`src/pages/public/Home.tsx`)

Transform from a basic hero + 4 cards into a full school landing page:

**Sections:**
1. **Hero Banner** -- School name, tagline, campus background image, CTA buttons
2. **Welcome / About Snippet** -- Principal's message with photo
3. **Stats Counter** -- Students, Teachers, Years, Achievements (animated counters)
4. **Why Choose Us** -- Facilities and USPs with icons
5. **Our Faculty** -- Top 4 featured teachers with photos and subjects (from mockTeachers)
6. **Latest Notifications** -- 3 most recent notices
7. **Upcoming Events** -- Next 3 events
8. **Gallery Preview** -- 6 recent photos in a grid
9. **Admissions CTA** -- Call-to-action banner for new admissions
10. **Contact Strip** -- Address, phone, email, map location

### Step 3: Create About Us Page (`src/pages/public/About.tsx`)

**Sections:**
- School history and overview
- Vision and Mission
- Principal's message (with photo)
- Core values
- Achievements and awards
- Infrastructure / Campus facilities

### Step 4: Create Faculty Page (`src/pages/public/Faculty.tsx`)

**Key Feature:** Reads teacher data from `mockTeachers.ts` (same data as admin panel)

**Sections:**
- Page header with school education philosophy
- Teacher grid cards showing:
  - Photo (placeholder if not uploaded)
  - Name
  - Subjects
  - Qualification
  - Experience
- Filter by subject
- Only shows `status: "active"` teachers

**Admin Connection:** When teacher photos/info are updated in Admin Panel, they appear here automatically.

### Step 5: Create Academics Page (`src/pages/public/Academics.tsx`)

**Sections:**
- Classes offered (Nursery to 12th)
- Curriculum (CBSE affiliation details)
- Subject list per class group
- Exam pattern and schedule
- Facilities (labs, library, sports, computer lab)
- Extra-curricular activities

### Step 6: Create Contact Page (`src/pages/public/Contact.tsx`)

**Sections:**
- Contact information cards (phone, email, address)
- Google Maps embed (placeholder coordinates)
- Contact form (name, email, phone, subject, message)
- Office hours
- Social media links

### Step 7: Update Notifications Page (`src/pages/public/Notifications.tsx`)

- Add 6-8 realistic notifications instead of 3
- Add date formatting
- Add search/filter by urgency
- Better empty state

### Step 8: Update Events Page (`src/pages/public/Events.tsx`)

- Add 8-10 realistic events
- Add month-based filtering
- Better visual calendar-style layout
- Past vs upcoming separation

### Step 9: Update Gallery Page (`src/pages/public/Gallery.tsx`)

- Add more categories (5-6 categories)
- Better masonry-style grid
- Category filter chips
- Image counter per category

### Step 10: Update Public Layout (`src/components/layout/PublicLayout.tsx`)

**Header updates:**
- Read school name from ThemeContext branding
- Add navigation for new pages (About, Faculty, Academics, Contact)
- Better mobile menu with school branding

**Footer updates:**
- Rich footer with 4 columns:
  - Column 1: School logo, name, tagline, brief description
  - Column 2: Quick Links (all pages)
  - Column 3: Contact Info (address, phone, email)
  - Column 4: Office Hours + Social media
- Copyright bar at bottom

### Step 11: Update Routes (`src/App.tsx`)

Add 4 new routes:
- `/about` -- About Us
- `/faculty` -- Faculty / Teachers
- `/academics` -- Academics
- `/contact` -- Contact Us

---

## Technical Notes

### Files to Create (4 files)

| File | Purpose |
|---|---|
| `src/data/mockSchoolData.ts` | School info, stats, facilities, principal details |
| `src/pages/public/About.tsx` | About Us page |
| `src/pages/public/Faculty.tsx` | Public teacher directory (imports from mockTeachers) |
| `src/pages/public/Academics.tsx` | Academics information page |
| `src/pages/public/Contact.tsx` | Contact page with form |

### Files to Update (6 files)

| File | Changes |
|---|---|
| `src/pages/public/Home.tsx` | Full redesign with 10 sections |
| `src/pages/public/Notifications.tsx` | More mock data, search filter |
| `src/pages/public/Events.tsx` | More events, month filter |
| `src/pages/public/Gallery.tsx` | More categories, better grid |
| `src/components/layout/PublicLayout.tsx` | Branded header + rich footer |
| `src/App.tsx` | Add 4 new routes |

### No New Dependencies
All changes use existing React, Tailwind, Lucide icons, and shadcn/ui components.

### Data Flow for Teachers
```text
Admin Panel                          Public Website
+---------------------------+        +---------------------------+
| /admin/teachers            |        | /faculty                  |
| - Add/Edit teacher info   |        | - Shows active teachers   |
| - Upload teacher photo    | -----> | - Displays photo, name,   |
| - Update subjects/classes |  same  |   subjects, qualification |
| - Mark active/inactive    |  data  | - Filters by subject      |
+---------------------------+ source +---------------------------+
        |                                      |
        v                                      v
  mockTeachers.ts (demo)            mockTeachers.ts (demo)
  /api/admin/teachers (live)        /api/public/teachers (live)
```

