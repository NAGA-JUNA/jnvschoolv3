<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db = getDB();

// KPI counts
$totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
$totalTeachers = $db->query("SELECT COUNT(*) FROM teachers WHERE status='active'")->fetchColumn();
$totalEnquiries = $db->query("SELECT COUNT(*) FROM enquiries WHERE status='new'")->fetchColumn();
$pendingAdmissions = $db->query("SELECT COUNT(*) FROM admissions WHERE status='pending'")->fetchColumn();
$pendingNotifications = $db->query("SELECT COUNT(*) FROM notifications WHERE status='pending'")->fetchColumn();
$pendingGallery = $db->query("SELECT COUNT(*) FROM gallery_items WHERE status='pending'")->fetchColumn();
$upcomingEvents = $db->query("SELECT COUNT(*) FROM events WHERE start_date >= CURDATE()")->fetchColumn();

// Chart data — monthly admissions
$year = date('Y');
$monthlyAdmissions = array_fill(0, 12, 0);
$stmt = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as c FROM admissions WHERE YEAR(created_at) = $year GROUP BY MONTH(created_at)");
while ($r = $stmt->fetch()) { $monthlyAdmissions[$r['m'] - 1] = (int)$r['c']; }

$monthlyAttendance = array_fill(0, 12, 0);
$stmt = $db->query("SELECT MONTH(date) as m, ROUND(SUM(CASE WHEN status='present' THEN 1 ELSE 0 END)/COUNT(*)*100, 1) as rate FROM attendance WHERE YEAR(date) = $year GROUP BY MONTH(date)");
while ($r = $stmt->fetch()) { $monthlyAttendance[$r['m'] - 1] = (float)$r['rate']; }

$recentLogs = $db->query("SELECT a.*, u.name as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 15")->fetchAll();
$events = $db->query("SELECT * FROM events WHERE start_date >= CURDATE() ORDER BY start_date LIMIT 5")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
    <?php
    $kpis = [
        ['Students', $totalStudents, 'bi-mortarboard-fill', 'primary', '/admin/students.php'],
        ['Teachers', $totalTeachers, 'bi-person-badge-fill', 'success', '/admin/teachers.php'],
        ['Enquiries', $totalEnquiries, 'bi-envelope-fill', 'purple', '/admin/enquiries.php'],
        ['Pending Admissions', $pendingAdmissions, 'bi-file-earmark-plus-fill', 'warning', '/admin/admissions.php'],
        ['Pending Notifications', $pendingNotifications, 'bi-bell-fill', 'info', '/admin/notifications.php'],
        ['Pending Gallery', $pendingGallery, 'bi-images', 'danger', '/admin/gallery.php'],
        ['Upcoming Events', $upcomingEvents, 'bi-calendar-event-fill', 'secondary', '/admin/events.php'],
    ];
    foreach ($kpis as $k): ?>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="<?= $k[4] ?>" class="text-decoration-none">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-<?= $k[3] ?>-subtle text-<?= $k[3] ?>"><i class="bi <?= $k[2] ?>"></i></div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $k[1] ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= $k[0] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0">
            <div class="card-header border-0 pb-0"><h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart-line-fill me-2" style="color:var(--brand-primary)"></i>Monthly Trends (<?= $year ?>)</h6></div>
            <div class="card-body"><canvas id="trendChart" height="260"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 mb-3">
            <div class="card-header border-0 pb-0"><h6 class="fw-semibold mb-0"><i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions</h6></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/student-form.php" class="btn btn-outline-primary btn-sm text-start"><i class="bi bi-plus-circle me-2"></i>Add Student</a>
                    <a href="/admin/teacher-form.php" class="btn btn-outline-success btn-sm text-start"><i class="bi bi-plus-circle me-2"></i>Add Teacher</a>
                    <a href="/admin/admissions.php" class="btn btn-outline-warning btn-sm text-start"><i class="bi bi-inbox me-2"></i>Review Admissions</a>
                    <a href="/admin/reports.php" class="btn btn-outline-info btn-sm text-start"><i class="bi bi-download me-2"></i>Export Reports</a>
                </div>
            </div>
        </div>
        <div class="card border-0">
            <div class="card-header border-0 pb-0"><h6 class="fw-semibold mb-0"><i class="bi bi-calendar3 me-2" style="color:var(--brand-primary)"></i>Upcoming Events</h6></div>
            <div class="card-body p-2">
                <?php if (empty($events)): ?>
                    <p class="text-muted text-center py-3 mb-0" style="font-size:.85rem">No upcoming events</p>
                <?php else: foreach ($events as $ev): ?>
                    <div class="d-flex align-items-center gap-2 p-2 rounded" style="border-bottom:1px solid var(--border-color)">
                        <div class="text-center" style="min-width:40px">
                            <div class="fw-bold" style="font-size:1.1rem;color:var(--brand-primary)"><?= date('d', strtotime($ev['start_date'])) ?></div>
                            <div style="font-size:.65rem;color:var(--text-muted)"><?= date('M', strtotime($ev['start_date'])) ?></div>
                        </div>
                        <div>
                            <div class="fw-medium" style="font-size:.85rem"><?= e($ev['title']) ?></div>
                            <?php if($ev['location']): ?><div style="font-size:.7rem;color:var(--text-muted)"><i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0">
    <div class="card-header border-0 pb-0"><h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Recent Activity</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>User</th><th>Action</th><th>Entity</th><th>Details</th><th>Time</th></tr></thead>
                <tbody>
                <?php if (empty($recentLogs)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No activity recorded yet</td></tr>
                <?php else: foreach ($recentLogs as $log): ?>
                    <tr>
                        <td style="font-size:.85rem"><?= e($log['user_name'] ?? 'System') ?></td>
                        <td><span class="badge bg-light" style="color:var(--text-primary)"><?= e($log['action']) ?></span></td>
                        <td style="font-size:.85rem"><?= e($log['entity_type'] ?? '-') ?></td>
                        <td style="font-size:.8rem;max-width:200px" class="text-truncate"><?= e($log['details'] ?? '-') ?></td>
                        <td style="font-size:.8rem;color:var(--text-muted)"><?= date('M d, H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart')?.getContext('2d');
    if (!ctx) return;
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : '#f1f5f9';
    const tickColor = isDark ? '#9ca3af' : '#64748b';
    const legendColor = isDark ? '#e5e5e5' : '#374151';
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Admissions', data: <?= json_encode($monthlyAdmissions) ?>,
                backgroundColor: 'rgba(30,64,175,0.8)', borderRadius: 6, barPercentage: 0.6
            },{
                label: 'Attendance %', data: <?= json_encode($monthlyAttendance) ?>,
                type: 'line', borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)',
                fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#10b981', yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, color: legendColor } } },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Admissions', color: tickColor }, grid: { color: gridColor }, ticks: { color: tickColor } },
                y1: { position: 'right', beginAtZero: true, max: 100, title: { display: true, text: 'Attendance %', color: tickColor }, grid: { display: false }, ticks: { color: tickColor } },
                x: { grid: { display: false }, ticks: { color: tickColor } }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
