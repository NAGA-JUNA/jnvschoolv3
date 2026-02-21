

## Convert Certificates Section to Slider/Carousel

### Current State
The "Our Certifications & Accreditations" section on the Home Page displays certificates in a static grid (`col-6 col-md-4 col-lg-3 col-xl-2`). On mobile it shows 2 cards per row, on desktop up to 6.

### Proposed Change
Replace the static grid with an **auto-sliding carousel** that matches the premium look shown in your screenshots:
- **Desktop**: Show 3 certificates at a time with left/right arrow navigation
- **Tablet**: Show 2 certificates
- **Mobile**: Show 1-2 certificates (swipeable)
- Auto-play every 4 seconds with pause on hover
- Smooth CSS transition sliding effect
- Green accent navigation arrows (matching your theme)
- Dot indicators below the slider

### Implementation Approach
Use a **pure CSS + vanilla JavaScript** slider (no external libraries needed) -- same pattern as the existing Hero Slider on the site, keeping everything lightweight and cPanel-compatible.

### Technical Details

**File: `php-backend/index.php`** (lines 496-546)

1. **Replace the grid layout** (`<div class="row g-4">`) with a slider container:
   - Outer wrapper with `overflow: hidden` and relative positioning
   - Inner track div containing all certificate cards in a horizontal flex row
   - CSS `transform: translateX()` for smooth sliding transitions

2. **Add navigation arrows** (left/right chevrons):
   - Positioned on either side of the slider
   - Green accent color matching the screenshots
   - Hidden on mobile (touch/swipe instead)

3. **Add dot indicators** below the slider for visual progress

4. **Add CSS styles** for the certificate slider:
   - `.cert-slider` -- outer container with overflow hidden
   - `.cert-slider-track` -- flex row with transition
   - `.cert-slider-card` -- each card taking 33.33% width on desktop, 50% on tablet, 100% on mobile
   - Arrow button styles with hover effects
   - Responsive breakpoints

5. **Add JavaScript** (below the existing lightbox script):
   - `certSlideIndex` tracking current position
   - `certSlideNext()` / `certSlidePrev()` functions
   - Auto-play interval (4 seconds) with `clearInterval` on hover
   - Touch/swipe support for mobile via `touchstart`/`touchend` events
   - Responsive recalculation on window resize

6. **Keep existing features intact**:
   - Lightbox on click still works
   - Category badges, titles, year labels unchanged
   - "View All Certificates" button below slider remains
   - Lazy loading images preserved

### Visual Result
```text
        <  [ Card 1 ]  [ Card 2 ]  [ Card 3 ]  >
                      * * o o o
              [ VIEW ALL CERTIFICATES ]
```

### Files Modified
| File | Change |
|------|--------|
| `php-backend/index.php` | Replace certificate grid (lines 503-523) with slider markup, add slider CSS and JS |

### No Breaking Changes
- All existing certificate data, lightbox, and settings integration remain unchanged
- Only the display layout changes from grid to slider

