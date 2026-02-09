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

### Core Modules
- **Dashboard** — KPIs, trend charts, recent activity, calendar widget, quick actions
- **Students Management** — Full CRUD, tabbed profiles (Attendance, Exams, Documents, Messages), alumni tracking, bulk promotion, Excel import/export
- **Teachers Management** — Staff records, subject/class assignments, documents, messaging, inactive archive, Excel import/export
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

---

## 🔑 Demo Credentials

The frontend runs in demo mode with mock data. Use these credentials on the login page:

| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@school.com` | `admin123` |
| Office Staff | `office@school.com` | `office123` |
| Teacher | `priya.singh@school.com` | `teacher123` |

> Click any credential row on the login page to auto-fill the form.

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
- 19 tables with proper foreign keys and indexes
- Sample data for all modules (users, students, teachers, attendance, exams, etc.)
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
├── schema.sql                    # Complete DB schema + sample data
├── BACKEND-SETUP-README.md       # cPanel deployment guide
├── src/
│   ├── api/                      # API endpoint definitions & client
│   ├── components/
│   │   ├── dashboard/            # Dashboard widgets (KPI, chart, calendar)
│   │   ├── layout/               # AppSidebar, TopHeader, PanelLayout
│   │   ├── shared/               # Reusable (PageHeader, StatusBadge, EmptyState)
│   │   └── ui/                   # shadcn/ui components
│   ├── contexts/                 # ThemeContext
│   ├── data/                     # Mock data (students, teachers)
│   ├── hooks/                    # Custom hooks (useApi, useMobile)
│   ├── pages/
│   │   ├── admin/                # Admin panel pages
│   │   │   ├── students/         # Students module (List, Form, Profile, Alumni, Import)
│   │   │   └── teachers/         # Teachers module (List, Form, Profile, Import, Inactive)
│   │   ├── auth/                 # Login page
│   │   ├── public/               # Public website pages
│   │   └── teacher/              # Teacher panel pages
│   └── types/                    # TypeScript type definitions
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

*JSchoolAdmin v1.1.0 — Modern School Management System*
