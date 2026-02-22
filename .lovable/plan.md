

## Remove Info Card, Make Map Full Width

### Change

**File: `php-backend/index.php`** (lines 940-986)

Remove the two-column layout (map on left, info card on right) and replace with a single full-width map embed. The map will span the entire container width with a taller height for better visibility.

**What gets removed:**
- The right-side info card showing school name, address, landmark, phone
- The "Get Directions", "Open in Google Maps", and "Copy Address" buttons
- The two-column `row` / `col-lg-7` / `col-lg-5` grid

**What stays:**
- Section header ("Our Location" with subtitle)
- Full-width Google Maps iframe embed in a rounded card with hover effect
- The background gradient and overall section styling
- The `copyAddress` JS and hover CSS can be removed since the card is gone

**Result:** A clean, full-width embedded map section -- similar to the screenshot reference but without the side panel.

### Files Changed

| File | Change |
|------|--------|
| `php-backend/index.php` | Replace two-column layout (lines 940-985) with single full-width map; remove `copyAddress` JS (lines 992-1003) |

