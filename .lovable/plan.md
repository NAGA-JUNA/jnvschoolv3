

## Automatic Brand Color Extraction from School Logo

### Overview
When the Super Admin uploads a new logo, the system will automatically extract 2-3 dominant colors and apply them as CSS variables across the entire Admin Dashboard UI for both Light and Dark themes. Colors are saved in the database and can be manually overridden.

### How It Works

1. **On logo upload** (in `admin/settings.php`), PHP GD analyzes the image pixels to find the top 3 dominant colors, skipping white/transparent/near-white pixels
2. Colors are saved as `brand_primary`, `brand_secondary`, `brand_accent` in the `settings` table
3. `includes/header.php` reads these settings and injects them as CSS variables (`--brand-primary`, `--brand-secondary`, `--brand-accent`) plus auto-computed light/dark variants
4. All UI elements (sidebar active state, buttons, avatar, badges, highlights) reference these brand variables instead of hardcoded values

### File Changes

#### 1. `php-backend/admin/settings.php`

**Backend color extraction** (added after logo upload succeeds, ~line 25):

```php
// Extract dominant colors from logo using GD
function extractDominantColors($imagePath, $count = 3) {
    $img = imagecreatefromstring(file_get_contents($imagePath));
    if (!$img) return [];
    $w = imagesx($img); $h = imagesy($img);
    // Sample pixels (resize to 50x50 for speed)
    $sample = imagecreatetruecolor(50, 50);
    imagecopyresampled($sample, $img, 0,0,0,0, 50,50, $w,$h);
    imagedestroy($img);
    $colors = [];
    for ($y=0; $y<50; $y++) {
        for ($x=0; $x<50; $x++) {
            $rgb = imagecolorat($sample, $x, $y);
            $r = ($rgb>>16)&0xFF; $g = ($rgb>>8)&0xFF; $b = $rgb&0xFF;
            $a = ($rgb>>24)&0x7F;
            // Skip transparent, white, near-white, near-black
            if ($a > 60) continue;
            $brightness = ($r*299 + $g*587 + $b*114) / 1000;
            if ($brightness > 240 || $brightness < 15) continue;
            // Quantize to reduce similar colors
            $qr = round($r/32)*32; $qg = round($g/32)*32; $qb = round($b/32)*32;
            $key = "$qr,$qg,$qb";
            $colors[$key] = ($colors[$key] ?? 0) + 1;
        }
    }
    imagedestroy($sample);
    arsort($colors);
    $result = [];
    foreach (array_slice(array_keys($colors), 0, $count) as $c) {
        list($r,$g,$b) = explode(',', $c);
        $result[] = sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    // If single-color logo, generate complementary colors
    if (count($result) === 1) {
        // Shift hue for secondary and accent
        $result[] = adjustHue($result[0], 30);
        $result[] = adjustHue($result[0], 180);
    } elseif (count($result) === 2) {
        $result[] = adjustHue($result[0], 180);
    }
    return $result;
}
```

After the logo upload saves successfully, call this function and store results:
```php
$extracted = extractDominantColors($uploadedPath);
if (!empty($extracted)) {
    foreach (['brand_primary','brand_secondary','brand_accent'] as $i => $key) {
        $val = $extracted[$i] ?? $extracted[0];
        $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")
           ->execute([$key, $val, $val]);
    }
    $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES ('brand_colors_auto','1') ON DUPLICATE KEY UPDATE setting_value=?")->execute(['1','1']);
}
```

**UI additions in the Appearance tab** (~after the color picker section):

Add a "Brand Colors" card showing:
- Three color swatches showing extracted primary/secondary/accent
- "Re-extract from Logo" button (form with `form_action=reextract_colors`)
- Manual override: three color input fields for primary/secondary/accent
- "Auto" badge when colors are auto-extracted, "Custom" when manually overridden
- "Reset to Auto" button to re-extract

**New form handler** for `reextract_colors` action and `brand_colors_manual` action.

#### 2. `php-backend/includes/header.php`

**Read brand colors** (add after line 6):
```php
$brandPrimary = getSetting('brand_primary', '#1e40af');
$brandSecondary = getSetting('brand_secondary', '#6366f1');
$brandAccent = getSetting('brand_accent', '#f59e0b');
```

**Update CSS variables** in the `:root` block (replace hardcoded `--primary`):
```css
:root {
    --brand-primary: <?= e($brandPrimary) ?>;
    --brand-secondary: <?= e($brandSecondary) ?>;
    --brand-accent: <?= e($brandAccent) ?>;
    /* Light variants (softer) */
    --brand-primary-light: <?= e($brandPrimary) ?>22;
    --brand-secondary-light: <?= e($brandSecondary) ?>22;
    --brand-accent-light: <?= e($brandAccent) ?>22;
}
html[data-theme="dark"] {
    /* Brighter for dark mode contrast */
    --brand-primary-light: <?= e($brandPrimary) ?>33;
    --brand-secondary-light: <?= e($brandSecondary) ?>33;
    --brand-accent-light: <?= e($brandAccent) ?>33;
}
```

**Apply brand colors to UI elements:**

| Element | Current | New |
|---------|---------|-----|
| Sidebar `.nav-link.active` | `var(--primary)` | `var(--brand-primary)` |
| `.btn-primary` background | Bootstrap default | `var(--brand-primary)` |
| `.avatar-circle` gradient | `var(--primary), #6366f1` | `var(--brand-primary), var(--brand-secondary)` |
| `.collapse-toggle` background | `var(--primary)` | `var(--brand-primary)` |
| `.online-dot` | `#22c55e` | `var(--brand-accent)` |
| `.badge.bg-primary` | Bootstrap blue | `var(--brand-primary)` |
| `.theme-switch-track` (dark active) | `var(--primary)` | `var(--brand-primary)` |
| Form focus ring | `rgba(37,99,235,0.25)` | brand-primary with alpha |

Add overrides:
```css
.btn-primary { background: var(--brand-primary) !important; border-color: var(--brand-primary) !important; }
.btn-primary:hover { filter: brightness(1.1); }
.badge.bg-primary { background: var(--brand-primary) !important; }
.nav-pills .nav-link.active { background: var(--brand-primary) !important; }
```

#### 3. `php-backend/includes/footer.php`

No changes needed -- existing theme persistence JS remains the same.

### Smart Color Rules

- **Single-color logo**: Auto-generate secondary (hue +30 degrees) and accent (complementary hue +180 degrees) using HSL color math in PHP
- **Too dark colors** (brightness below 30): Lighten by 20%
- **Too light colors** (brightness above 220): Darken by 20%
- **Contrast check**: Ensure text readability by computing relative luminance

### Database Settings Added

| Key | Example Value | Description |
|-----|---------------|-------------|
| `brand_primary` | `#1e40af` | Primary brand color |
| `brand_secondary` | `#6366f1` | Secondary brand color |
| `brand_accent` | `#f59e0b` | Accent color |
| `brand_colors_auto` | `1` or `0` | Whether colors are auto-extracted |

### Permissions
- Only Super Admin can manually override brand colors (same `isSuperAdmin()` check as logo upload)
- Auto-extraction happens automatically on any logo upload by Super Admin

### Implementation Order
1. Add color extraction function and form handlers to `admin/settings.php`
2. Add Brand Colors UI card in the Appearance tab of `admin/settings.php`
3. Update `includes/header.php` CSS variables to use brand colors
4. Replace all hardcoded color references with brand CSS variables

