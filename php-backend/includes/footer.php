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

    // ===== Greeting Logic =====
    var greetEl = document.getElementById('greetText');
    if (greetEl) {
        var h = new Date().getHours();
        greetEl.textContent = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
    }

    // ===== Top Bar Search =====
    var searchInput = document.getElementById('topbarSearchInput');
    var searchResults = document.getElementById('searchResults');
    if (searchInput && searchResults) {
        var pages = [
            {name:'Dashboard', icon:'bi-speedometer2', url:'/admin/dashboard.php'},
            {name:'Students', icon:'bi-people', url:'/admin/students.php'},
            {name:'Teachers', icon:'bi-person-workspace', url:'/admin/teachers.php'},
            {name:'Admissions', icon:'bi-person-badge', url:'/admin/admissions.php'},
            {name:'Notifications', icon:'bi-bell', url:'/admin/notifications.php'},
            {name:'Events', icon:'bi-calendar-event', url:'/admin/events.php'},
            {name:'Gallery', icon:'bi-images', url:'/admin/gallery.php'},
            {name:'Certificates', icon:'bi-award', url:'/admin/certificates.php'},
            {name:'Slider', icon:'bi-film', url:'/admin/slider.php'},
            {name:'Reports', icon:'bi-bar-chart', url:'/admin/reports.php'},
            {name:'Settings', icon:'bi-gear', url:'/admin/settings.php'},
            {name:'Audit Logs', icon:'bi-shield-check', url:'/admin/audit-logs.php'},
            {name:'Navigation', icon:'bi-signpost-split', url:'/admin/navigation-settings.php'},
            {name:'Footer Manager', icon:'bi-layout-text-sidebar', url:'/admin/footer-manager.php'},
            {name:'Page Content', icon:'bi-file-richtext', url:'/admin/page-content-manager.php'},
            {name:'Quote Highlight', icon:'bi-chat-quote', url:'/admin/quote-highlight.php'},
            {name:'Import Students', icon:'bi-upload', url:'/admin/import-students.php'},
            {name:'Import Teachers', icon:'bi-upload', url:'/admin/import-teachers.php'},
            {name:'Support', icon:'bi-question-circle', url:'/admin/support.php'}
        ];
        searchInput.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            if (!q) { searchResults.classList.remove('show'); return; }
            var matches = pages.filter(function(p){ return p.name.toLowerCase().includes(q); });
            if (matches.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No pages found</div>';
            } else {
                searchResults.innerHTML = matches.map(function(p){
                    return '<a href="'+p.url+'"><i class="bi '+p.icon+'"></i> '+p.name+'</a>';
                }).join('');
            }
            searchResults.classList.add('show');
        });
        searchInput.addEventListener('blur', function(){ setTimeout(function(){ searchResults.classList.remove('show'); }, 200); });
        searchInput.addEventListener('focus', function(){ if(this.value.trim()) this.dispatchEvent(new Event('input')); });
        // Ctrl+K shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); searchInput.focus(); }
        });
    }

    // ===== Notification Dropdown =====
    window.toggleNotifDropdown = function(e) {
        e.stopPropagation();
        var dd = document.getElementById('notifDropdown');
        if (dd) dd.classList.toggle('show');
    };
    document.addEventListener('click', function(e) {
        var dd = document.getElementById('notifDropdown');
        if (dd && !dd.closest('.topbar-bell')?.contains(e.target) && !dd.contains(e.target)) {
            dd.classList.remove('show');
        }
    });

    // ===== Fullscreen Toggle =====
    window.toggleFullScreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(function(){});
            var ic = document.getElementById('fsIcon');
            if(ic) { ic.classList.remove('bi-arrows-fullscreen'); ic.classList.add('bi-fullscreen-exit'); }
        } else {
            document.exitFullscreen();
            var ic = document.getElementById('fsIcon');
            if(ic) { ic.classList.remove('bi-fullscreen-exit'); ic.classList.add('bi-arrows-fullscreen'); }
        }
    };
})();
</script>
<?php endif; ?>
</body>
</html>
