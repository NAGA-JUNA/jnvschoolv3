# JSchoolAdmin — Modern School Management System

**JSchoolAdmin** is a premium, feature-rich school management dashboard built with React and Tailwind CSS. It provides a complete admin panel for managing students, teachers, notifications, gallery, events, admissions, and more — designed for Indian schools running on cPanel hosting with a PHP + MySQL backend.

> **Powered by [JNV Tech](https://jnvtech.in)**

---

## 🚀 Tech Stack

| Layer | Technology |
|---|---|
| Frontend | React 18, TypeScript, Vite, Tailwind CSS, shadcn/ui |
| Backend | Vanilla PHP 8.x (REST API) |
| Database | MySQL 8.0+ / MariaDB 10.3+ (InnoDB) |
| Hosting | cPanel shared hosting |
| Auth | JWT (JSON Web Tokens) |

---

## ✨ Features

### Public Website (10 Pages)
- **Home** — Dynamic hero slider (admin-managed), stats counter, faculty carousel, latest notifications, upcoming events, gallery preview, admissions CTA
- **About Us** — School history, vision & mission, principal's message, achievements, campus facilities
- **Faculty** — Public teacher directory (auto-synced from admin data), filter by subject, shows only active teachers
- **Academics** — Curriculum (CBSE), classes Nursery–12th, subject lists, exam patterns, facilities
- **Admissions** — Online admission application form with status tracking
- **Notifications** — Public notices with search/filter by urgency
- **Events** — School calendar with month-based filtering, past vs upcoming
- **Gallery** — Category-based image/video gallery with filter chips
- **Contact** — Contact form, Google Maps embed, office hours, social links
- **Staff Login Popup** — Professional login dialog accessible from any public page

### Admin Panel (Core Modules)
- **Dashboard** — KPIs, trend charts, recent activity, calendar widget, quick actions
- **Students Management** — Full CRUD, tabbed profiles (Attendance, Exams, Documents, Messages), alumni tracking, bulk promotion, Excel import/export
- **Teachers Management** — Staff records, subject/class assignments (editable chips), documents, messaging, inactive archive, Excel import/export
- **Home Banner / Slider** — Admin-managed hero slider with image upload, CTA buttons, reordering, enable/disable, live preview
- **Admissions** — Online application processing with status tracking
- **Notifications** — Multi-level approval workflow with public publishing
- **Gallery** — Category-based image/video management with approval system
- **Events** — School calendar and event management
- **Reports** — Class-wise, subject-wise, and custom report generation

### Communication
- **WhatsApp Manual Sharing** — Template-based messages (Absentee, Exam, Event, Fee alerts) with clipboard copy
- **Message History** — Track all sent messages per student/teacher

### Administration
- **Email Management** — Official school email accounts
- **Branding Settings** — School logo, colors, tagline customization
- **Audit Logs** — System-wide action tracking
- **Role-Based Access** — Super Admin, Admin, Office Staff, Teacher

### Teacher Panel
- **Teacher Dashboard** — Personal KPIs and activity
- **Post Notifications** — Submit notifications for admin approval
- **Upload Gallery** — Upload images/videos for approval
- **My Submissions** — Track submitted content status
- **Profile** — View and update personal profile

---

## 🔑 Demo Credentials

The frontend runs in demo mode with mock data. Use these credentials on the login page:

| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@school.com` | `admin123` |
| Office Staff | `office@school.com` | `office123` |
| Teacher | `priya.singh@school.com` | `teacher123` |

> Click any credential row on the login page to auto-fill the form, or use the **Staff Login** button on the public website header.

---

## 🛠️ Development Setup

### Prerequisites
- Node.js 18+ and npm (or use [bun](https://bun.sh))

### Quick Start

```bash
# Clone the repository
git clone <YOUR_GIT_URL>
cd jschooladmin

# Install dependencies
npm install

# Start dev server
npm run dev
```

The app will be available at `http://localhost:5173`

### Build for Production

```bash
npm run build
```

The production build will be in the `dist/` folder.

---

## 🗄️ Database Setup

Import the complete database schema with sample data:

1. Open **phpMyAdmin** in your cPanel
2. Select your database
3. Click the **SQL** tab
4. Paste the contents of [`schema.sql`](./schema.sql) and click **Go**

The SQL file includes:
- 20 tables with proper foreign keys and indexes
- Sample data for all modules (users, students, teachers, attendance, exams, slider, etc.)
- 3 pre-configured user accounts (see Demo Credentials above)
- IST timezone and utf8mb4 charset

---

## 📡 API Endpoints

All API endpoint definitions are in [`src/api/endpoints.ts`](./src/api/endpoints.ts).

### Endpoint Groups

| Group | Base Path | Auth Required |
|---|---|---|
| Public | `/api/public/*` | No |
| Auth | `/api/auth/*` | No (login), Yes (me/logout) |
| Admin | `/api/admin/*` | Admin/Super Admin role |
| Teacher | `/api/teacher/*` | Teacher role |
| Home Slider | `/api/home/slider` | GET: No, POST/PUT/DELETE: Admin |

For the complete API reference with request/response formats, see [`BACKEND-SETUP-README.md`](./BACKEND-SETUP-README.md).

---

## 🌐 cPanel Deployment

For full step-by-step deployment instructions including:
- MySQL database setup
- PHP backend file structure
- `.htaccess` configuration
- SSL setup
- File permissions
- Cron jobs
- Troubleshooting

👉 See **[BACKEND-SETUP-README.md](./BACKEND-SETUP-README.md)**

---

## 📁 Project Structure

```
├── schema.sql                    # Complete DB schema + sample data (20 tables)
├── BACKEND-SETUP-README.md       # cPanel deployment guide
├── src/
│   ├── api/                      # API endpoint definitions & client
│   ├── components/
│   │   ├── dashboard/            # Dashboard widgets (KPI, chart, calendar)
│   │   ├── layout/               # AppSidebar, TopHeader, PanelLayout, PublicLayout
│   │   ├── public/               # HeroSlider (dynamic carousel)
│   │   ├── shared/               # Reusable (PageHeader, StatusBadge, EmptyState, Footer)
│   │   └── ui/                   # shadcn/ui components
│   ├── contexts/                 # ThemeContext
│   ├── data/                     # Mock data (students, teachers, school info, slider)
│   ├── hooks/                    # Custom hooks (useApi, useMobile)
│   ├── pages/
│   │   ├── admin/                # Admin panel pages
│   │   │   ├── students/         # Students module (List, Form, Profile, Alumni, Import)
│   │   │   ├── teachers/         # Teachers module (List, Form, Profile, Import, Inactive)
│   │   │   └── HomeBanner.tsx    # Slider/Banner management
│   │   ├── auth/                 # Login page (professional split-screen)
│   │   ├── public/               # Public website pages (10 pages)
│   │   └── teacher/              # Teacher panel pages
│   └── types/                    # TypeScript type definitions
```

---

## 🔄 Data Flow: Admin → Public Website

### Teacher Data
```
Admin Panel (/admin/teachers)          Public Website (/faculty)
┌─────────────────────────┐           ┌─────────────────────────┐
│ Add/Edit teacher info   │           │ Shows active teachers   │
│ Upload teacher photo    │ ───────── │ Displays photo, name,   │
│ Update subjects/classes │   same    │   subjects, qualification│
│ Mark active/inactive    │   data    │ Filter by subject       │
└─────────────────────────┘  source   └─────────────────────────┘
```

### Home Slider
```
Admin Panel (/admin/home-banner)       Public Website (/ hero)
┌─────────────────────────┐           ┌─────────────────────────┐
│ Add/edit slides         │           │ Auto-playing carousel   │
│ Upload background image │ ───────── │ Fade transitions        │
│ Set CTA buttons + links │   same    │ Swipe on mobile         │
│ Reorder / enable/disable│   data    │ Dot indicators + arrows │
└─────────────────────────┘  source   └─────────────────────────┘
```

---

## 🔒 Roles & Permissions

| Feature | Super Admin | Admin | Office | Teacher |
|---|---|---|---|---|
| Dashboard | ✅ | ✅ | ✅ | ✅ (own) |
| Students CRUD | ✅ | ✅ | ✅ | ❌ |
| View Students | ✅ | ✅ | ✅ | ✅ (assigned) |
| Mark Attendance | ✅ | ✅ | ❌ | ✅ |
| Enter Marks | ✅ | ✅ | ❌ | ✅ |
| Teachers CRUD | ✅ | ✅ | ❌ | ❌ |
| Home Banner | ✅ | ✅ | ❌ | ❌ |
| Notifications | ✅ | ✅ | ✅ | ✅ (submit) |
| Gallery | ✅ | ✅ | ✅ | ✅ (upload) |
| Settings | ✅ | ✅ | ❌ | ❌ |
| Audit Logs | ✅ | ✅ | ❌ | ❌ |

---

## 📊 Excel Import Template Format

### Students Import
| Column | Required | Example |
|---|---|---|
| Admission No | ✅ | ADM2025010 |
| Full Name | ✅ | Rahul Verma |
| Class | ✅ | 10 |
| Section | | A |
| Gender | | Male |
| Date of Birth | | 2012-05-15 |
| Father Name | | Suresh Verma |
| Mother Name | | Anita Verma |
| Parent Phone | ✅ | +91-9812345678 |
| WhatsApp | | +91-9812345678 |
| Address | | 123, MG Road, Lucknow |

### Teachers Import
| Column | Required | Example |
|---|---|---|
| Employee ID | ✅ | EMP011 |
| Full Name | ✅ | Rahul Verma |
| Phone | ✅ | +91-9812345678 |
| Email | | rahul@school.com |
| Qualification | | M.Sc. Mathematics |
| Subjects | | Mathematics, Physics |
| Classes | | 10-A, 9-B |

---

## 📄 License

This project is proprietary software developed by **JNV Tech**.

---

*JSchoolAdmin v1.2.0 — Modern School Management System*
