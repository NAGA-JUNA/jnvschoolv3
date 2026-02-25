

## Add WhatsApp Floating Button and "Need Help?" Sidebar with Popup

### Overview

Add two fixed floating elements to the homepage (and all public pages via the public footer), matching the reference images:

1. **WhatsApp floating button** -- bottom-right corner, green pill/circle with WhatsApp icon and "Chat with us" text. Clicking opens `wa.me/{whatsapp_number}` in a new tab. Fully mobile-compatible.
2. **"Need Help?" sidebar tab** -- fixed on the right edge (vertically oriented), red/crimson background. Clicking opens a modal/popup with a callback request form (parent name, mobile with +91 prefix, email, city select, branch select). Submits as an enquiry to the existing `enquiries` table.

Both elements use the existing `whatsapp_api_number` setting and are visible on all public pages.

---

### File: `php-backend/includes/public-footer.php`

Add the following before the closing `</body>` tag (so it appears on ALL public pages, not just the homepage):

#### A. WhatsApp Floating Button (bottom-right)
- Fixed position: `bottom: 24px; right: 24px; z-index: 9999`
- Green pill button (`#25D366` background) with WhatsApp Bootstrap icon and "Chat with us" text
- Links to `https://wa.me/{whatsapp_number}?text=Hi, I need help regarding {school_name}`
- On mobile: slightly smaller, text hidden (icon-only circle) below 576px for space
- Pulse animation to draw attention
- Only shown if `$whatsappNumber` is set

#### B. "Need Help?" Sidebar Tab (right edge)
- Fixed position: `right: 0; top: 50%; transform: translateY(-50%); z-index: 9998`
- Rotated 90 degrees, red/crimson background (`#DC3545`), white text
- Contains text "Need Help?" with a notepad icon
- On click: opens a Bootstrap modal popup

#### C. "Need Help?" Modal Popup
- Header: Red left-border accent, "Need Help?" title, close (X) button
- Subtitle: "Share your details below and our admissions experts will get in touch with you to guide you personally."
- Form fields:
  - Parent's full name (required, maxlength 100)
  - Mobile number with "91" country code prefix (required, maxlength 15)
  - Email address (optional, maxlength 255)
  - Message/query (optional textarea)
- Submit button: Full-width crimson "Request a Call Back"
- Footer note: "Our admissions team will contact you shortly to assist you."
- On submit: AJAX POST to a small inline handler or reuse the existing enquiry form logic -- saves to `enquiries` table
- Success state: Show checkmark animation and "We'll contact you soon!" message
- Validation: Client-side required fields + server-side via existing enquiry handler

---

### Changes Summary

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/includes/public-footer.php` | Add WhatsApp button, Need Help tab, modal, CSS, and JS |

No new files or database changes needed -- reuses existing `enquiries` table and `whatsapp_api_number` setting.

---

### Technical Details

- WhatsApp button uses `position:fixed` with responsive breakpoints (`@media (max-width:576px)` hides text, shrinks button)
- "Need Help?" tab uses `writing-mode: vertical-rl` or CSS rotation for vertical text on the right edge
- Modal uses Bootstrap 5 modal component (already loaded on all pages)
- AJAX submission via `fetch()` POST to avoid page reload -- posts to existing enquiry endpoint or inline PHP handler
- Pulse animation via `@keyframes pulse` on the WhatsApp button
- Both elements have high `z-index` to stay above all content
- Mobile: WhatsApp button stays as small circle, "Need Help?" tab stays but slightly narrower

