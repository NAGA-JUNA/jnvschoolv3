
# Dashboard UI Redesign — Match Reference Style

This plan redesigns the Admin (and Teacher) dashboard to closely match the reference screenshot you shared. Here's what will change and the features being added:

---

## What's Changing (Visual Summary)

### 1. Top Header Bar (NEW)
Currently there is no top bar. The reference shows a prominent header across the top with:
- **Left side**: Greeting message with time-of-day logic ("Good Morning / Afternoon / Evening, Admin")
- **Right side**: Search input, Mail icon with badge, Bell/notification icon with badge, and Admin avatar with name/role dropdown
- **Live date and time display** updating in real-time

### 2. Sidebar Redesign
The current dark sidebar will be restyled to match the reference:
- **Deep blue gradient background** (navy to dark blue, matching the reference)
- **School logo area** at the top with a decorative illustration/icon zone
- **Menu items** in white text with icons, bold blue highlight on the active item
- **Grouped navigation** with collapsible section headers (e.g., "Academics", "Collaboration", "Administration")
- **Bottom branding** area with school/product logo and tagline
- White text on dark blue, active link highlighted in bright blue

### 3. KPI Cards Row
Restyle the current KPI cards to match the reference:
- **Horizontal card layout** with a circular colored icon on the left
- Each card shows: colored round icon, label text on top, large bold number below
- Cards for: Students, Teachers (Employees), Inflows/Fee, Messages
- Cards have white backgrounds with subtle shadows and rounded corners

### 4. Alert Banner
The existing alert banner will be restyled to match the **orange/amber full-width banner** in the reference, with a warning icon on the left and a close (X) button on the right.

### 5. Calendar and Chart Section
- **Calendar widget** stays as-is but label changes to "Calendar and Activities" with a "Today" dropdown
- **Session/Trend chart** becomes a bar chart style (matching "Session Recorded" in the reference)
- Both sit side-by-side in the middle row

### 6. Quick Actions Panel
Currently in a right column card. Will be restyled to match the reference:
- Each action is a **white bordered card with icon + label**, stacked vertically on the right
- Icons get individual colors (pink, blue, green, orange, purple)
- Actions: New Announcement, Send Message, View Report, Human Resource, Admit Students

### 7. Announcements Section
The "Recent Activity" section will be restyled as an **"Announcement" section** at the bottom with:
- Author avatar, name, and department
- Announcement body text with a colored left border accent
- Clean card styling

### 8. Footer Redesign
The footer will be updated to match the reference:
- **Left**: School/product logo with tagline
- **Right**: Copyright text
- Light background with subtle top border

---

## Feature Highlights You Get

| Feature | Description |
|---|---|
| **Time-aware greeting** | "Good Morning/Afternoon/Evening" based on user's local time |
| **Live clock** | Real-time date and time display in the top bar |
| **Admin profile widget** | Avatar, name, role shown in top-right corner |
| **Notification badges** | Red badge counters on mail and bell icons |
| **Grouped sidebar navigation** | Collapsible menu groups (Academics, Collaboration, etc.) |
| **Sidebar branding** | Logo and tagline at the top and bottom of sidebar |
| **Redesigned KPI cards** | Circular colored icons with large numbers |
| **Orange alert banner** | Full-width dismissible system alert |
| **Bar chart widget** | Session/trend data in bar chart format |
| **Announcement feed** | Author info with department and message body |
| **Branded footer** | Logo, tagline, and copyright in footer |

---

## Technical Details

### Files to Create
- `src/components/layout/TopHeader.tsx` — New top header bar component with greeting, search, icons, avatar, and live clock

### Files to Modify

1. **`src/index.css`**
   - Add new CSS variables for the deep blue sidebar gradient
   - Add orange alert banner color tokens

2. **`src/components/layout/PanelLayout.tsx`**
   - Insert `<TopHeader />` between sidebar and main content
   - Restructure layout to have sidebar on left, then a vertical stack of TopHeader + main + footer

3. **`src/components/layout/AppSidebar.tsx`**
   - Change background to deep navy-blue gradient
   - Add school logo/illustration area at top
   - Group menu items under collapsible section headers (Academics, Collaboration, Data and Reports, Administration, Settings)
   - Add bottom branding with logo and tagline
   - White text styling, blue highlight on active item

4. **`src/components/dashboard/KPICard.tsx`**
   - Redesign to show circular colored icon on the left, label on top, big number below
   - Match the horizontal card style from the reference

5. **`src/components/dashboard/AlertBanner.tsx`**
   - Change the warning variant to use a solid orange/amber background with white text
   - Close button styled as a circle with X

6. **`src/components/dashboard/TrendChart.tsx`**
   - Switch from area chart to bar chart to match "Session Recorded" style in reference
   - Update title to "Session Recorded" or keep as "Trends Overview"

7. **`src/components/dashboard/QuickActions.tsx`**
   - Restyle each action as a bordered white card with colored icon and label
   - Vertical stack layout matching the reference

8. **`src/components/dashboard/RecentActivity.tsx`**
   - Rename to "Announcement" section
   - Show author avatar, name, department, and announcement text
   - Add colored left border accent on the message body

9. **`src/components/shared/Footer.tsx`**
   - Add logo icon on left with product name and tagline
   - Copyright on the right
   - Match the reference footer style

10. **`src/pages/admin/Dashboard.tsx`**
    - Update layout grid to match reference arrangement
    - Adjust KPI card props and ordering

11. **`tailwind.config.ts`**
    - Add any additional color tokens needed for the redesign

### No New Dependencies Required
All changes use existing libraries (Lucide icons, Recharts, Tailwind CSS, Radix UI).
