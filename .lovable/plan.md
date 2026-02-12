

## Update Logo Crop Tool to Support Rectangular/Wide Logos

### Problem
The admin settings crop tool currently forces a **square crop** (400x400px output), which destroys the wide rectangular format of logos like the uploaded "ANANTAPUR" school logo. When a user uploads a wide logo, the square crop cuts off the sides, making it unreadable in the navbar/sidebar/footer.

### Changes (1 file: `php-backend/admin/settings.php`)

#### 1. Change crop selection from square to rectangular
- Replace the square `cropBox` (x, y, size) with a rectangular `cropBox` (x, y, width, height)
- Initialize crop area to match the full image proportions instead of forcing a square
- Allow the user to drag a rectangle that matches the logo's natural aspect ratio
- Draw rectangular overlay and corner handles instead of square ones

#### 2. Update crop output to preserve aspect ratio
- Change the output canvas from fixed `400x400` to a proportional size (e.g., max 400px wide, height calculated from aspect ratio)
- This ensures the uploaded logo retains its wide rectangular shape

#### 3. Update recommendation text
- Change "Recommended: 200x200px or larger, square format" to "Recommended: Wide/rectangular format supported. Min 160px wide. JPG, PNG, WebP, SVG."

#### 4. Update preview cards
- Previews already show `width:160px; height:auto` which is correct for wide logos
- No changes needed to preview section

### Technical Details

**Current crop output (square):**
```js
outCanvas.width = 400; outCanvas.height = 400;
outCtx.drawImage(img, cropBox.x*scaleX, cropBox.y*scaleY, cropBox.size*scaleX, cropBox.size*scaleY, 0, 0, 400, 400);
```

**New crop output (rectangular, preserving aspect ratio):**
```js
const cropW = cropBox.w * scaleX;
const cropH = cropBox.h * scaleY;
const maxDim = 400;
const outScale = Math.min(maxDim / cropW, maxDim / cropH, 1);
outCanvas.width = Math.round(cropW * outScale);
outCanvas.height = Math.round(cropH * outScale);
outCtx.drawImage(img, cropBox.x*scaleX, cropBox.y*scaleY, cropW, cropH, 0, 0, outCanvas.width, outCanvas.height);
```

**Crop box initialization (full image instead of square):**
```js
// Before: cropBox.size = Math.min(canvas.width, canvas.height);
// After: use full canvas area
cropBox = { x: 0, y: 0, w: canvas.width, h: canvas.height };
```

**Updated dimming overlay** -- draw 4 rectangles around a rectangular (not square) crop area.

**Drag behavior** -- same as before but tracks `cropBox.w` and `cropBox.h` separately instead of a single `cropBox.size`.

### Summary
- Crop tool now supports **rectangular** selections instead of forcing square
- Output preserves the logo's natural aspect ratio (wide logos stay wide)
- Recommendation text updated to clarify rectangular logos are supported
- No changes needed to navbar/sidebar/footer display (already set to `width:160px/140px; height:auto`)
