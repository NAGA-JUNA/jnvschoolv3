

## Apply Premium SaaS Style to Full Dashboard + Mobile Optimization

### Overview
Extend the premium SaaS aesthetic from the sidebar to the entire dashboard content area: KPI cards, charts, tables, modals, forms, quick action panels, and all admin pages. Also optimize the full layout for mobile/tablet devices with touch-friendly sizing and responsive breakpoints.

### Problem Areas (Current State)
The sidebar has been redesigned with the premium floating style, but the main content still uses hardcoded Bootstrap colors that break in dark mode and lack the modern feel:
- Cards use `bg-white`, `card-header bg-white` -- invisible in dark mode
- KPI text uses `text-dark` -- unreadable in dark mode
- Tables use `table-light` class -- doesn't adapt to theme
- Chart.js has hardcoded grid/text colors
- Event borders use `#f1f5f9` hardcode
- Modals use `bg-primary bg-opacity-10` and `text-primary` hardcodes
- Mobile content area has no spacing/padding adjustments
- Top-bar is not optimized for small screens

### Changes

#### 1. `php-backend/includes/header.php` -- Global CSS Additions

**New CSS variables for content cards:**
```css
:root {
    --card-bg: #ffffff;
    --card-border: transparent;
    --card-shadow: 0 1px 4px rgba(0,0,0,0.04);
    --card-radius: 16px;
    --card-header-bg: transparent;
    --table-header-bg: rgba(0,0,0,0.02);
    --table-hover-bg: rgba(0,0,0,0.02);
    --table-border: #f1f5f9;
    --input-bg: #ffffff;
    --input-border: #e2e8f0;
    --modal-bg: var(--bg-card);
    --chart-grid: #f1f5f9;
    --chart-text: #64748b;
}
html[data-theme="dark"] {
    --card-bg: #1c1c1c;
    --card-shadow: 0 1px 4px rgba(0,0,0,0.3);
    --table-header-bg: rgba(255,255,255,0.03);
    --table-hover-bg: rgba(255,255,255,0.03);
    --table-border: rgba(255,255,255,0.06);
    --input-bg: #161616;
    --input-border: rgba(255,255,255,0.1);
    --chart-grid: rgba(255,255,255,0.06);
    --chart-text: #9ca3af;
}
```

**Premium card styles:**
```css
.card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    transition: transform 0.2s, box-shadow 0.2s, background 0.3s;
}
.card:hover { box-shadow: var(--shadow-md); }
.card-header {
    background: var(--card-header-bg);
    border-bottom: 1px solid var(--table-border);
    color: var(--text-primary);
    padding: 1rem 1.25rem;
}
```

**Table dark mode fixes:**
```css
.table { color: var(--text-primary); }
.table thead th {
    background: var(--table-header-bg);
    color: var(--text-muted);
    border-color: var(--table-border);
}
.table td { border-color: var(--table-border); }
.table-hover tbody tr:hover { background: var(--table-hover-bg); }
```

**KPI card enhancements:**
```css
.kpi-card {
    border-radius: var(--card-radius);
    background: var(--card-bg);
    box-shadow: var(--card-shadow);
    border: none;
}
.kpi-card .fs-3, .kpi-card .fs-4 {
    color: var(--text-primary); /* replace hardcoded text-dark */
}
.kpi-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    font-size: 1.3rem;
}
```

**Form inputs:**
```css
.form-control, .form-select {
    background: var(--input-bg);
    border-color: var(--input-border);
    color: var(--text-primary);
    border-radius: 10px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
```

**Buttons refinement:**
```css
.btn { border-radius: 10px; font-weight: 500; transition: all 0.2s; }
.btn-sm { border-radius: 8px; }
.btn-outline-primary, .btn-outline-success, etc. {
    /* Already handled but add subtle hover lift */
}
```

**Modal premium styling:**
```css
.modal-content {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    color: var(--text-primary);
}
.modal-header { border-bottom-color: var(--table-border); }
.modal-footer { border-top-color: var(--table-border); }
```

**Alert/badge dark mode:**
```css
html[data-theme="dark"] .alert { background: var(--card-bg); }
html[data-theme="dark"] .badge.bg-light { background: rgba(255,255,255,0.08) !important; }
html[data-theme="dark"] .bg-primary.bg-opacity-10 { background: var(--brand-primary-light) !important; }
```

**Top-bar refinement:**
```css
.top-bar {
    background: var(--bg-topbar);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-color);
    padding: 0.6rem 1.25rem;
    border-radius: 0;
}
```

#### 2. `php-backend/includes/header.php` -- Mobile CSS

```css
@media (max-width: 767.98px) {
    .content-area { padding: 0.75rem; }
    .top-bar { padding: 0.5rem 0.75rem; }
    .top-bar .page-title { font-size: 0.95rem; }
    .kpi-card .card-body { padding: 0.75rem !important; }
    .kpi-card .fs-3 { font-size: 1.5rem !important; }
    .kpi-card .fs-4 { font-size: 1.25rem !important; }
    .kpi-icon { width: 40px; height: 40px; font-size: 1rem; border-radius: 10px; }
    .card { border-radius: 14px; }
    .card-header { padding: 0.75rem 1rem; }
    .card-body { padding: 0.75rem; }
    .table { font-size: 0.8rem; }
    .btn-sm { font-size: 0.75rem; padding: 0.3rem 0.6rem; }
    .modal-dialog { margin: 0.5rem; }
    .modal-content { border-radius: 16px; }
    /* Stack action buttons vertically */
    .d-flex.gap-2 { flex-wrap: wrap; }
}

@media (max-width: 575.98px) {
    .content-area { padding: 0.5rem; }
    .col-6 { flex: 0 0 50%; } /* Keep KPI 2-per-row on small phones */
    .kpi-card .card-body { padding: 0.5rem !important; }
    .kpi-icon { width: 36px; height: 36px; font-size: 0.9rem; }
    .top-bar .user-info .theme-toggle-btn { display: none; } /* Hide in mobile, use sidebar pill */
}

/* Tablet landscape */
@media (min-width: 768px) and (max-width: 991.98px) {
    .content-area { padding: 1rem; }
    .card { border-radius: 16px; }
}
```

#### 3. `php-backend/admin/dashboard.php` -- Theme-Aware Updates

**Replace hardcoded color classes:**
- `text-dark` on KPI values becomes no class (inherits `var(--text-primary)`)
- `bg-white` on card headers removed (uses `var(--card-header-bg)`)
- `border-bottom:1px solid #f1f5f9` on events becomes `border-bottom:1px solid var(--table-border)`
- `table-light` on thead becomes no class (uses CSS variable)
- `text-primary` hardcodes become `color: var(--brand-primary)` or use the existing branded class

**Chart.js dark mode:**
Add JS that reads the current theme and configures chart colors:
```javascript
const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
// Grid color, text color, tooltip bg adapt to theme
options: {
    scales: {
        y: { grid: { color: isDark ? 'rgba(255,255,255,0.06)' : '#f1f5f9' },
             ticks: { color: isDark ? '#9ca3af' : '#64748b' } },
        x: { ticks: { color: isDark ? '#9ca3af' : '#64748b' } }
    },
    plugins: { legend: { labels: { color: isDark ? '#e5e5e5' : '#374151' } } }
}
```

#### 4. `php-backend/teacher/dashboard.php` -- Same Treatment

- Remove `bg-white` from card headers
- Remove `text-dark` from values (no longer needed)
- Use theme-aware table styles

#### 5. `php-backend/admin/students.php` -- Modal + Table Fixes

- Replace `bg-primary bg-opacity-10` on modal header with theme-aware variable
- Replace `text-primary` hardcodes with `color: var(--brand-primary)`
- Table already uses `table-hover` which will be styled by global CSS

### Files Modified

| File | Changes |
|------|---------|
| `php-backend/includes/header.php` | Add new CSS variables for cards/tables/inputs/modals, add comprehensive mobile breakpoints, refine global element styles |
| `php-backend/admin/dashboard.php` | Replace hardcoded `text-dark`, `bg-white`, `#f1f5f9`, `table-light` with theme variables; make Chart.js theme-aware |
| `php-backend/teacher/dashboard.php` | Replace `bg-white` card headers, remove hardcoded `text-dark` |
| `php-backend/admin/students.php` | Fix modal header colors for dark mode |

### Implementation Order
1. Add all new CSS variables and global card/table/form/modal styles to `header.php`
2. Add mobile responsive breakpoints to `header.php`
3. Update `dashboard.php` -- remove hardcoded colors, make chart theme-aware
4. Update `teacher/dashboard.php` -- same treatment
5. Update `students.php` -- fix modal for dark mode

