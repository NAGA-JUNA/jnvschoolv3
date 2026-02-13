<?php if (isLoggedIn()): ?>
    </main>
</div>
<?php else: ?>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<?php if (isLoggedIn()): ?>
<script>
(function() {
    // ===== Theme System =====
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('admin_theme', theme);
        syncThemePill(theme);
    }

    function syncThemePill(theme) {
        var btns = document.querySelectorAll('#themePill .theme-pill-btn');
        btns.forEach(function(btn) {
            btn.classList.toggle('active', btn.getAttribute('data-theme') === theme);
        });
    }

    var savedTheme = localStorage.getItem('admin_theme') || 'light';
    applyTheme(savedTheme);

    // Global theme functions
    window.setTheme = function(theme) {
        applyTheme(theme);
    };

    window.toggleTheme = function() {
        var current = document.documentElement.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    };

    // ===== Sidebar Collapse Persistence =====
    var sidebar = document.getElementById('sidebar');
    if (sidebar) {
        var isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (window.innerWidth >= 992 && isCollapsed) {
            sidebar.classList.add('collapsed');
        }
    }

    window.toggleCollapse = function() {
        var sb = document.getElementById('sidebar');
        if (!sb) return;
        sb.classList.toggle('collapsed');
        var nowCollapsed = sb.classList.contains('collapsed');
        localStorage.setItem('sidebar_collapsed', nowCollapsed);
        document.documentElement.classList.toggle('sidebar-is-collapsed', nowCollapsed);
        initTooltips(nowCollapsed);
    };

    // ===== Bootstrap Tooltips for Collapsed Sidebar =====
    var tooltipInstances = [];
    function initTooltips(collapsed) {
        tooltipInstances.forEach(function(t) { t.dispose(); });
        tooltipInstances = [];
        if (!collapsed) return;
        var links = document.querySelectorAll('#sidebar .nav-link[data-bs-title]');
        links.forEach(function(el) {
            el.setAttribute('data-bs-toggle', 'tooltip');
            el.setAttribute('data-bs-placement', 'right');
            el.setAttribute('title', el.getAttribute('data-bs-title'));
            tooltipInstances.push(new bootstrap.Tooltip(el, { trigger: 'hover' }));
        });
    }
    if (sidebar && sidebar.classList.contains('collapsed')) {
        initTooltips(true);
    }

    // ===== Button Ripple Effect =====
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn');
        if (!btn || btn.classList.contains('ripple-active')) return;
        btn.classList.add('ripple-active');
        var rect = btn.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height);
        var x = e.clientX - rect.left - size / 2;
        var y = e.clientY - rect.top - size / 2;
        var ripple = document.createElement('span');
        ripple.style.cssText = 'position:absolute;width:'+size+'px;height:'+size+'px;left:'+x+'px;top:'+y+'px;background:rgba(255,255,255,0.5);border-radius:50%;transform:scale(0);animation:rippleEffect 0.6s ease-out;pointer-events:none;';
        btn.appendChild(ripple);
        setTimeout(function(){ ripple.remove(); btn.classList.remove('ripple-active'); }, 600);
    });

    // ===== Header Clock =====
    var clockEl = document.getElementById('headerClock');
    if (clockEl) {
        setInterval(function() {
            var d = new Date();
            var h = d.getHours(), m = d.getMinutes();
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            clockEl.textContent = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm;
        }, 30000);
    }
})();
</script>
<?php endif; ?>
</body>
</html>
