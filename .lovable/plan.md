

## Plan: Improve Mobile Responsiveness Across All Public Pages

### Issues Found

After reviewing all 7 public pages plus the login page, here are the mobile responsiveness problems identified:

1. **Navbar collapsed menu** -- When the hamburger menu opens on mobile, the notification bell button and login button stack awkwardly without proper spacing or alignment
2. **Top bar quick links** -- On very small screens (under 360px), the top bar links can overflow or look cramped
3. **School name in navbar** -- Long school names overflow on small screens, pushing the hamburger button off-screen
4. **Hero sections** -- Text sizes and padding need refinement for small phones (320-375px width)
5. **Footer columns** -- The 4-column gradient footer doesn't stack cleanly on mobile; newsletter input can overflow
6. **Teacher flip cards** -- Hover-based flip doesn't work on touch devices; cards need a tap-to-flip mechanism
7. **Gallery grid** -- col-6 works but images at 200px height are very small on phones
8. **Admission form** -- Some form rows (3-column layout for DOB/Gender) get too narrow on mobile
9. **WhatsApp button** -- Can overlap with footer content when scrolled to bottom
10. **Ad popup** -- Close button can be hard to tap on small screens
11. **Core team cards on homepage** -- Image heights (280px) are large relative to mobile width
12. **About page value cards** -- 2-column (col-md-6) grid doesn't break to single column on very small screens

---

### Changes Per File

#### All Public Pages (shared CSS improvements)

Enhanced `@media` breakpoints added to every page:

- **Small phones (max-width: 575.98px)**:
  - Navbar brand: truncate long names with `max-width` and `text-overflow: ellipsis`
  - Navbar collapsed: bell button and login button get `width: 100%` and proper vertical spacing
  - Top bar: hide quick links text, show icons only
  - Footer: single column stacking, centered text, reduce padding
  - WhatsApp button: smaller size (50px), positioned to not overlap footer
  - Section padding reduced from `py-5` to `py-4`

- **Medium phones (max-width: 767.98px)**:
  - Keep existing rules (hero height reduction, slider arrows hidden)
  - Add: footer newsletter input group better sizing
  - Add: core team card image height reduced to 200px

#### 1. `index.php` (Homepage)

- Add small-screen media queries for hero slider content, stats bar, quick link cards, core team images
- Reduce hero slide heading to `1.5rem` on phones
- Make stats bar numbers smaller (`1.2rem`) on mobile
- Ad popup close button enlarged to 44px for easier tapping on mobile

#### 2. `public/teachers.php`

- Add JavaScript for tap-to-flip on touch devices (detect `touchstart`, toggle a `.flipped` class)
- Add `.flipped .teacher-card-inner { transform: rotateY(180deg); }` CSS rule
- Reduce card heights on small screens (300px instead of 380px)
- Principal photo: center and reduce max-width on mobile

#### 3. `public/gallery.php`

- Change grid to `col-4` on tablets, keep `col-6` on phones
- Increase image height to `150px` minimum on phones for better visibility
- Add swipe-to-close on lightbox for touch devices

#### 4. `public/events.php`

- Date box size reduction on small screens
- Event card padding adjustments

#### 5. `public/notifications.php`

- Notification cards: reduce horizontal padding on mobile
- Pagination (if any) touch-friendly sizing

#### 6. `public/admission-form.php`

- Force single-column layout on mobile for DOB/Gender/Class row (`col-12` overrides)
- Document upload field: full width on mobile
- Submit button padding adjustment

#### 7. `public/about.php`

- Hero heading reduced to `1.8rem` on small phones
- Value cards: `col-6` on mobile (2 per row, compact)
- About icon size reduced on small screens
- Vision/Mission cards: full width (`col-12`) on small phones

#### 8. `login.php`

- Already mostly responsive; minor fix: increase left-panel min-height on tablet landscape
- Ensure input groups don't overflow on 320px screens

---

### Technical Details

**Approach**: Add enhanced `@media` queries to each page's existing `<style>` block. No new files or dependencies needed.

**Key CSS additions per page** (added inside existing `@media` blocks or as new breakpoints):

```css
/* Extra small devices */
@media (max-width: 575.98px) {
    /* Navbar brand truncation */
    .navbar-brand { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    /* Collapsed menu buttons */
    .navbar-collapse .d-flex { flex-direction: column; width: 100%; gap: 0.5rem; margin-top: 0.75rem; }
    .notif-bell-btn, .login-nav-btn { width: 100%; text-align: center; display: block; }
    
    /* Top bar icons only */
    .top-bar .d-flex.gap-3 a span.link-text { display: none; }
    
    /* Footer single column */
    .site-footer .row > div { text-align: center; }
    .footer-heading::after { left: 50%; transform: translateX(-50%); }
    .footer-social { justify-content: center; }
    
    /* WhatsApp button */
    .whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 16px; right: 16px; }
}
```

**Teacher card tap-to-flip JavaScript** (added to teachers.php):

```javascript
if ('ontouchstart' in window) {
    document.querySelectorAll('.teacher-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.teacher-card.flipped').forEach(c => {
                if (c !== this) c.classList.remove('flipped');
            });
            this.classList.toggle('flipped');
        });
    });
}
```

With corresponding CSS: `.teacher-card.flipped .teacher-card-inner { transform: rotateY(180deg); }`

**Files to modify**: `index.php`, `public/teachers.php`, `public/gallery.php`, `public/events.php`, `public/notifications.php`, `public/admission-form.php`, `public/about.php`, `login.php` (8 files total)

