

## Fix Gallery Mobile Layout

### Problem
The 3-panel desktop layout breaks on mobile because inline styles (`flex: 0 0 25%; max-width: 25%`) on the hero and category panels override the CSS media query that sets `flex-direction: column`. Categories are tiny and truncated instead of becoming a horizontal scrollable strip.

### Changes (1 file: `php-backend/public/gallery.php`)

#### 1. Fix responsive CSS (lines 234-261)
Update the mobile media queries to:
- Force `flex: 1 1 100% !important; max-width: 100% !important;` on all three panels
- Convert the hero panel to a compact horizontal banner on mobile
- Transform the category grid into a **horizontal scrollable strip** (`display: flex; overflow-x: auto; flex-wrap: nowrap`)
- Make category cards fixed-width pill/chips instead of a 2x4 grid
- Make slider panel full-width with no padding constraints

#### 2. Compact hero on mobile
- Hide the scroll arrow on mobile
- Reduce hero to a slim section with title + stats inline
- Smaller font sizes and tighter padding

#### 3. Horizontal category strip (mobile)
- Categories display as a single horizontal scrollable row
- Each category is a rounded pill/chip (~80px wide) with icon + label
- Active category has accent border/glow
- Hide the "Browse Categories" heading on small screens
- Snap scrolling for smooth swipe between categories

#### 4. Full-width slider
- Slider takes full viewport width on mobile
- Image min-height adjusted for mobile (200-250px)
- Thumbnail strip scrolls horizontally
- Touch swipe already works (existing JS)

### Technical Details

**CSS additions in the `@media (max-width: 991.98px)` block:**
```css
.gallery-3col > * {
  flex: 1 1 100% !important;
  max-width: 100% !important;
}
.hero-panel .scroll-arrow { display: none; }
.category-grid-inner {
  display: flex !important;
  overflow-x: auto;
  flex-wrap: nowrap;
  gap: 0.5rem;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
}
.cat-card {
  flex: 0 0 80px !important;
  height: 80px !important;
  aspect-ratio: unset !important;
  scroll-snap-align: start;
}
```

**CSS additions in the `@media (max-width: 767.98px)` block:**
- Further reduce hero padding and font sizes
- Category cards become 70px chips

No HTML structure changes needed — purely CSS fixes to override the inline styles.
