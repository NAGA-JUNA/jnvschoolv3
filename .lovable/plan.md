

# Rebrand Footer + Sidebar + Create JNV Tech Developer Page

This plan covers three main changes: updating the branding text across the app, embedding the JNV Tech logo, and creating a new developer info page.

---

## 1. What's Changing

### Footer Redesign
The current footer shows "SchoolAdmin -- Modern School Management". It will be updated to:
- **Left side**: JNV Tech logo image + "**JSchoolAdmin** -- Modern School Management"
- **Right side**: `v1.0.0` badge, then "(c) 2026 JSchoolAdmin. All rights reserved. @ JNV Tech - Journey to New Value"
- "Powered by JNV Tech" replaces "Powered by SchoolAdmin" in the sidebar bottom
- The JNV Tech name in the footer and sidebar will be a **clickable link** that navigates to the new `/developer` page

### Sidebar Bottom Update
- The text "Powered by SchoolAdmin" below the Sign Out button changes to **"Powered by JNV Tech"**
- Clicking it opens the `/developer` page
- The JNV Tech logo will also appear as a small icon next to the text

### Default Branding Update
- In ThemeContext, the default `schoolName` changes from `"SchoolAdmin"` to `"JSchoolAdmin"`

---

## 2. JNV Tech Developer Page (New Page)

A new public-accessible page at `/developer` with the following content:

**Hero Section**
- Large JNV Tech logo (the uploaded image)
- Tagline: "Journey to New Value"
- Brief intro paragraph

**About Section**
- Heading: "JNV Tech -- Journey to New Value"
- Body text: "We help businesses move from ideas to impact with modern web design, development, and reliable support. Our mission is to create real digital value that helps you grow."

**Services Section** (placeholder cards the user can update later)
- Web Design and Development
- School Management Systems
- Digital Solutions and Support

**Contact Section**
- **WhatsApp button**: Green button with WhatsApp icon, links to `https://wa.me/918106811171` (opens WhatsApp chat directly)
- **Email button**: Blue button with mail icon (placeholder email, user can update)
- A note: "Reach out to us for custom solutions and support"

**Footer on the page**
- "(c) 2026 JNV Tech. All rights reserved."
- "Journey to New Value"

The page will have a clean, professional design using existing Tailwind/shadcn styling with a gradient hero section.

---

## 3. Technical Details

### New Files to Create

| File | Purpose |
|---|---|
| `src/assets/jnvtech-logo.png` | Copy the uploaded JNV Tech logo into project assets |
| `src/pages/public/Developer.tsx` | JNV Tech developer info page with hero, about, services, contact |

### Files to Modify

| File | Changes |
|---|---|
| `src/contexts/ThemeContext.tsx` | Change default `schoolName` from "SchoolAdmin" to "JSchoolAdmin" |
| `src/components/shared/Footer.tsx` | Replace logo with JNV Tech logo, update all text to JSchoolAdmin, add JNV Tech attribution with link to `/developer` |
| `src/components/layout/AppSidebar.tsx` | Change "Powered by" text to "JNV Tech", add clickable link to `/developer`, show small JNV Tech logo |
| `src/App.tsx` | Add route `/developer` pointing to the new Developer page |

### No New Dependencies
Everything uses existing React Router, Lucide icons, and Tailwind CSS.
