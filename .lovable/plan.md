

## Make Mobile Header Always Sticky (No Hide on Scroll)

### Problem

The navbar currently hides when scrolling down on mobile (the JS adds a `nav-hidden` class that slides it up with `transform: translateY(-100%)`). This makes it harder for mobile users to navigate back quickly.

### Solution

Disable the "hide on scroll down" behavior on mobile (below 992px) so the navbar stays fixed/sticky at the top at all times. On desktop, the current hide-on-scroll-down behavior is preserved.

### File: `php-backend/includes/public-navbar.php`

**CSS change** (~line 88): Add a media query so `.nav-hidden` only applies on desktop:

```css
/* Only hide navbar on scroll for desktop */
@media (min-width: 992px) {
  .premium-navbar.nav-hidden { transform: translateY(-100%); }
}
```

**JS change** (~line 403): Wrap the hide logic in a width check so it only triggers on screens wider than 992px:

```js
if (scroll > lastScroll && scroll > 200 && window.innerWidth >= 992) {
    nav.classList.add('nav-hidden');
}
```

This ensures the navbar always stays visible and sticky on mobile/tablet while keeping the subtle auto-hide effect on desktop.

### Files Summary

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/includes/public-navbar.php` | Restrict nav-hidden to desktop only (CSS + JS) |

One small, targeted change -- no other files affected.

