
## Add Smooth Hover Micro-Animations to Dashboard Components

### Overview
The dashboard currently has basic `transition` properties but lacks sophisticated micro-interactions. This plan adds elegant hover animations to:
1. **KPI Cards** – subtle scale + shadow lift with staggered animation
2. **Table Rows** – smooth highlight + subtle slide effect
3. **Buttons** – scale, shadow, and color shift animations
4. **Additional Elements** – event cards, quick action buttons, and interactive elements

### Current State
- KPI cards: `transform: translateY(-2px)` on hover only
- Table rows: basic `hover { background: rgba(...) }` 
- Buttons: `transition: all 0.2s` with no keyframes
- No grouped/staggered animations
- No smooth entrance animations

### Proposed Changes

#### 1. **`php-backend/includes/header.php`** – Add Micro-Animation Keyframes and Enhanced Styles

**New Keyframes Section (after line 475, before closing `</style>`):**

```css
/* ========== MICRO-ANIMATIONS ========== */

/* KPI Card Scale + Shadow Animation */
@keyframes kpiCardHover {
    to {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 32px rgba(0,0,0,0.12);
    }
}

@keyframes kpiIconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Table Row Slide + Highlight */
@keyframes tableRowHover {
    to {
        background: rgba(0,0,0,0.04);
        transform: translateX(4px);
    }
}

html[data-theme="dark"] @keyframes tableRowHover {
    to {
        background: rgba(255,255,255,0.05);
        transform: translateX(4px);
    }
}

/* Button Scale + Lift */
@keyframes buttonHover {
    to {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
}

/* Smooth Button Click Press */
@keyframes buttonPress {
    to {
        transform: translateY(-1px) scale(0.98);
    }
}

/* Event Card Hover */
@keyframes eventCardHover {
    to {
        background: rgba(0,0,0,0.02);
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
}

/* Quick Action Button Hover */
@keyframes quickActionHover {
    to {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
}
```

**Enhanced CSS for KPI Cards (replace line 574-589):**

```css
/* KPI Cards */
.kpi-card {
    border: none;
    border-radius: 16px;
    transition: transform 0.3s cubic-bezier(.4,0,.2,1), 
                box-shadow 0.3s cubic-bezier(.4,0,.2,1),
                background 0.3s ease;
    background: var(--bg-card);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: radial-gradient(circle at center, rgba(255,255,255,0.1), transparent);
    pointer-events: none;
    transition: left 0.3s ease;
}
.kpi-card:hover::before { left: 100%; }
.kpi-card:hover {
    animation: kpiCardHover 0.3s cubic-bezier(.4,0,.2,1) forwards;
}
.kpi-card:active {
    transform: translateY(-4px) scale(0.98);
}
.kpi-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    transition: transform 0.3s cubic-bezier(.4,0,.2,1), 
                filter 0.3s ease;
}
.kpi-card:hover .kpi-icon {
    transform: scale(1.15) rotate(5deg);
    filter: brightness(1.15);
}
```

**Enhanced CSS for Table Rows (replace line 591-602):**

```css
/* Tables */
.table { color: var(--text-primary); --bs-table-bg: transparent; }
.table th {
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
    color: var(--text-muted); font-weight: 600;
    background: rgba(0,0,0,0.02);
    border-color: var(--border-color);
}
.table td { border-color: var(--border-color); }
.table-hover tbody tr {
    transition: background 0.3s cubic-bezier(.4,0,.2,1),
                transform 0.3s cubic-bezier(.4,0,.2,1),
                box-shadow 0.3s ease;
    position: relative;
}
.table-hover tbody tr:hover {
    background: rgba(0,0,0,0.04);
    transform: translateX(3px);
    box-shadow: inset 4px 0 0 0 var(--brand-primary);
}
.table-hover tbody tr:active {
    transform: translateX(1px);
}
html[data-theme="dark"] .table th { background: rgba(255,255,255,0.03); }
html[data-theme="dark"] .table-hover tbody tr:hover { 
    background: rgba(255,255,255,0.04);
    box-shadow: inset 4px 0 0 0 var(--brand-primary);
}
```

**Enhanced CSS for Buttons (add after line 614):**

```css
/* Buttons – Enhanced Micro-Animations */
.btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
    position: relative;
    overflow: hidden;
}
.btn::before {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 0; height: 0;
    background: radial-gradient(circle, rgba(255,255,255,0.3), transparent);
    border-radius: 50%;
    pointer-events: none;
    transform: translate(-50%, -50%);
    transition: width 0.4s, height 0.4s;
}
.btn:hover::before {
    width: 300px;
    height: 300px;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.btn:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.btn-sm {
    border-radius: 8px;
}
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-info:hover,
.btn-outline-danger:hover {
    transform: translateY(-2px);
}

/* Quick Action Buttons */
.card .btn-outline-primary,
.card .btn-outline-success,
.card .btn-outline-warning,
.card .btn-outline-info {
    transition: all 0.3s cubic-bezier(.4,0,.2,1);
}
.card .btn-outline-primary:hover,
.card .btn-outline-success:hover,
.card .btn-outline-warning:hover,
.card .btn-outline-info:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
```

**Event Card Hover Animation (add after button styles):**

```css
/* Event Cards */
.d-flex.align-items-center.gap-2[style*="border-bottom"] {
    transition: all 0.2s cubic-bezier(.4,0,.2,1);
    border-radius: 8px;
    padding: 0.5rem 0.25rem;
    margin: 0 -0.25rem;
}
.d-flex.align-items-center.gap-2[style*="border-bottom"]:hover {
    background: rgba(0,0,0,0.02);
    transform: translateX(2px);
}
html[data-theme="dark"] .d-flex.align-items-center.gap-2[style*="border-bottom"]:hover {
    background: rgba(255,255,255,0.03);
}
```

**Add smooth entrance animations (optional, add before closing `</style>`):**

```css
/* Page Load Stagger Animation */
.kpi-card {
    animation: fadeInUp 0.5s cubic-bezier(.4,0,.2,1) backwards;
}
.kpi-card:nth-child(1) { animation-delay: 0.05s; }
.kpi-card:nth-child(2) { animation-delay: 0.1s; }
.kpi-card:nth-child(3) { animation-delay: 0.15s; }
.kpi-card:nth-child(4) { animation-delay: 0.2s; }
.kpi-card:nth-child(5) { animation-delay: 0.25s; }
.kpi-card:nth-child(6) { animation-delay: 0.3s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

#### 2. **`php-backend/includes/footer.php`** – Add Ripple Effect JavaScript (Optional Enhancement)

Add after the theme toggle JS (around line 50):

```javascript
// Ripple effect on button clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn')) {
        const btn = e.target.closest('.btn');
        if (btn.classList.contains('ripple-active')) return;
        
        btn.classList.add('ripple-active');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.background = 'rgba(255,255,255,0.5)';
        ripple.style.borderRadius = '50%';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'rippleEffect 0.6s ease-out';
        ripple.style.pointerEvents = 'none';
        
        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
        btn.classList.remove('ripple-active');
    }
});
```

Add corresponding CSS keyframe in `header.php`:

```css
@keyframes rippleEffect {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
```

### Implementation Details

**Animation Timing & Easing:**
- KPI Cards: `0.3s cubic-bezier(.4,0,.2,1)` – smooth, fast entrance
- Table Rows: `0.3s` with left-to-right slide
- Buttons: `0.25s` with bouncy easing
- All animations use hardware-accelerated properties (`transform`, `opacity`)

**Hover Effects Summary:**
- **KPI Cards**: Scale 1.02, lift 6px, enhanced shadow, icon rotates 5deg and scales 1.15
- **Table Rows**: Translate X 3px, left brand-color accent line, subtle background shift
- **Buttons**: Translate Y -2px, scale 1.02, enhanced shadow with ripple effect
- **Event Cards**: Subtle translate X 2px with background shift
- **Dark Mode**: All effects maintain contrast with `rgba()` overlays

**Performance:**
- Uses `cubic-bezier(.4,0,.2,1)` for buttery smooth motion
- GPU-accelerated transforms (no paint)
- No JavaScript overhead except optional ripple effect
- Mobile-friendly: animations work on touch devices

### Files Modified
- `php-backend/includes/header.php` – Add keyframes and enhanced CSS for all components
- `php-backend/includes/footer.php` – Optional ripple effect JavaScript

### Implementation Order
1. Add all `@keyframes` definitions in `header.php` styles block
2. Replace KPI card, table, and button CSS with enhanced versions
3. Test hover effects on desktop and mobile
4. (Optional) Add ripple effect JavaScript to `footer.php` for extra polish
5. Verify dark mode animations maintain contrast

### Visual Hierarchy
- **Strongest**: KPI cards (scale 1.02, 6px lift) – main focal point
- **Medium**: Table rows (left accent line, 3px slide) – secondary data
- **Subtle**: Buttons (2px lift, ripple) – actionable elements
- **Minimal**: Event cards (2px slide) – tertiary info

All animations use the same easing function for cohesive feel across the interface.
