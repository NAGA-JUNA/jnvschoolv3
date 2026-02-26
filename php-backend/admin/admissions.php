<?php
$pageTitle = 'Admissions';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

// Status definitions
$allStatuses = ['new','contacted','documents_verified','interview_scheduled','approved','rejected','waitlisted','converted'];
$statusColors = [
    'new'=>'primary', 'contacted'=>'info', 'documents_verified'=>'secondary',
    'interview_scheduled'=>'warning', 'approved'=>'success', 'rejected'=>'danger',
    'waitlisted'=>'dark', 'converted'=>'success'
];
$statusIcons = [
    'new'=>'bi-plus-circle', 'contacted'=>'bi-telephone', 'documents_verified'=>'bi-file-check',
    'interview_scheduled'=>'bi-calendar-event', 'approved'=>'bi-check-circle', 'rejected'=>'bi-x-circle',
    'waitlisted'=>'bi-hourglass', 'converted'=>'bi-person-check'
];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $aid = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status' && $aid) {
        $newStatus = $_POST['new_status'] ?? '';
        $remarks = trim($_POST['remarks'] ?? '');
        if (in_array($newStatus, $allStatuses)) {
            $oldStatus = $db->prepare("SELECT status FROM admissions WHERE id=?");
            $oldStatus->execute([$aid]);
            $oldStatus = $oldStatus->fetchColumn();

            $db->prepare("UPDATE admissions SET status=?, remarks=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?")->execute([$newStatus, $remarks, currentUserId(), $aid]);
            $db->prepare("INSERT INTO admission_status_history (admission_id, old_status, new_status, changed_by, remarks) VALUES (?,?,?,?,?)")->execute([$aid, $oldStatus, $newStatus, currentUserId(), $remarks]);
            auditLog("admission_$newStatus", 'admission', $aid);
            
            // Send email on key status changes
            if (in_array($newStatus, ['approved','rejected','waitlisted','interview_scheduled'])) {
                try {
                    $adm = $db->prepare("SELECT * FROM admissions WHERE id=?");
                    $adm->execute([$aid]);
                    $adm = $adm->fetch();
                    if ($adm && $adm['email']) {
                        require_once __DIR__.'/../config/mail.php';
                        $schoolName = getSetting('school_name','JNV School');
                        $subjects = [
                            'approved' => "Admission Approved — $schoolName",
                            'rejected' => "Admission Update — $schoolName",
                            'waitlisted' => "Admission Waitlisted — $schoolName",
                            'interview_scheduled' => "Interview Scheduled — $schoolName"
                        ];
                        $bodies = [
                            'approved' => "<h2>Congratulations!</h2><p>Dear {$adm['student_name']},</p><p>Your admission application <strong>{$adm['application_id']}</strong> for Class {$adm['class_applied']} has been <strong style='color:#22c55e'>APPROVED</strong>.</p><p>Please visit the school office to complete the admission process.</p>",
                            'rejected' => "<h2>Admission Update</h2><p>Dear {$adm['student_name']},</p><p>After careful review, we regret to inform you that your application <strong>{$adm['application_id']}</strong> could not be accepted at this time.</p>".($remarks ? "<p><strong>Remarks:</strong> $remarks</p>" : ""),
                            'waitlisted' => "<h2>Application Waitlisted</h2><p>Dear {$adm['student_name']},</p><p>Your application <strong>{$adm['application_id']}</strong> has been placed on the <strong>waitlist</strong> for Class {$adm['class_applied']}. We will notify you if a seat becomes available.</p>",
                            'interview_scheduled' => "<h2>Interview Scheduled</h2><p>Dear {$adm['student_name']},</p><p>An interview has been scheduled for your admission application <strong>{$adm['application_id']}</strong>.</p>".($adm['interview_date'] ? "<p><strong>Date:</strong> ".date('M d, Y h:i A', strtotime($adm['interview_date']))."</p>" : "")
                        ];
                        $emailBody = "<div style='font-family:Inter,sans-serif;max-width:600px;margin:0 auto;padding:2rem;'>".$bodies[$newStatus]."<hr><p style='color:#64748b;font-size:0.8rem;'>$schoolName | Application: {$adm['application_id']}</p></div>";
                        sendMail($adm['email'], $subjects[$newStatus], $emailBody);
                    }
                } catch (Exception $e) { /* silent */ }
            }

            // Auto-create student on approval
            if ($newStatus === 'approved' && !empty($_POST['create_student'])) {
                $adm = $db->prepare("SELECT * FROM admissions WHERE id=?");
                $adm->execute([$aid]);
                $adm = $adm->fetch();
                if ($adm) {
                    $admNo = 'STU-'.date('Y').'-'.str_pad($aid, 5, '0', STR_PAD_LEFT);
                    $db->prepare("INSERT INTO students (admission_no, name, father_name, mother_name, dob, gender, class, phone, email, address, blood_group, category, aadhar_no, status, admission_date, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'active',CURDATE(),?)")
                        ->execute([$admNo, $adm['student_name'], $adm['father_name'], $adm['mother_name'], $adm['dob'], $adm['gender'], $adm['class_applied'], $adm['phone'], $adm['email'], $adm['address'], $adm['blood_group'], $adm['category'], $adm['aadhar_no'], currentUserId()]);
                    $studentId = (int)$db->lastInsertId();
                    $db->prepare("UPDATE admissions SET status='converted', converted_student_id=? WHERE id=?")->execute([$studentId, $aid]);
                    $db->prepare("INSERT INTO admission_status_history (admission_id, old_status, new_status, changed_by, remarks) VALUES (?,'approved','converted',?,'Student record created')")->execute([$aid, currentUserId()]);
                    auditLog('admission_converted', 'admission', $aid, "Student ID: $studentId");
                }
            }

            setFlash('success', "Status updated to " . ucfirst(str_replace('_', ' ', $newStatus)) . ".");
        }
    } elseif ($action === 'add_note' && $aid) {
        $note = trim($_POST['note'] ?? '');
        if ($note) {
            $db->prepare("INSERT INTO admission_notes (admission_id, user_id, note) VALUES (?,?,?)")->execute([$aid, currentUserId(), $note]);
            auditLog('admission_note_added', 'admission', $aid);
            setFlash('success', 'Note added.');
        }
    } elseif ($action === 'set_followup' && $aid) {
        $followUp = $_POST['follow_up_date'] ?? null;
        $db->prepare("UPDATE admissions SET follow_up_date=? WHERE id=?")->execute([$followUp ?: null, $aid]);
        setFlash('success', 'Follow-up date updated.');
    } elseif ($action === 'set_interview' && $aid) {
        $intDate = $_POST['interview_date'] ?? null;
        // Fetch old status first to avoid self-referencing subquery issue
        $oldSt = $db->prepare("SELECT status FROM admissions WHERE id=?");
        $oldSt->execute([$aid]);
        $oldSt = $oldSt->fetchColumn();
        $db->prepare("UPDATE admissions SET interview_date=?, status='interview_scheduled', reviewed_by=?, reviewed_at=NOW() WHERE id=?")->execute([$intDate, currentUserId(), $aid]);
        $db->prepare("INSERT INTO admission_status_history (admission_id, old_status, new_status, changed_by, remarks) VALUES (?,'interview_scheduled',?,'Interview scheduled')")->execute([$aid, $oldSt ?: 'new', currentUserId()]);
        setFlash('success', 'Interview scheduled.');
    } elseif ($action === 'delete' && $aid && isSuperAdmin()) {
        $db->prepare("DELETE FROM admissions WHERE id=?")->execute([$aid]);
        auditLog('admission_deleted', 'admission', $aid);
        setFlash('success', 'Admission deleted.');
    }

    header('Location: /admin/admissions.php?' . http_build_query(array_filter(['status'=>$_GET['status']??'','search'=>$_GET['search']??'','class'=>$_GET['class']??''])));
    exit;
}

// KPI counts
$kpiNew = $db->query("SELECT COUNT(*) FROM admissions WHERE status='new' AND DATE(created_at)=CURDATE()")->fetchColumn();
$kpiPending = $db->query("SELECT COUNT(*) FROM admissions WHERE status IN ('new','contacted')")->fetchColumn();
$kpiApproved = $db->query("SELECT COUNT(*) FROM admissions WHERE status='approved'")->fetchColumn();
$kpiRejected = $db->query("SELECT COUNT(*) FROM admissions WHERE status='rejected'")->fetchColumn();
$kpiWaitlisted = $db->query("SELECT COUNT(*) FROM admissions WHERE status='waitlisted'")->fetchColumn();
$kpiTotal = $db->query("SELECT COUNT(*) FROM admissions")->fetchColumn();
$kpiConversion = $kpiTotal > 0 ? round(($kpiApproved / $kpiTotal) * 100, 1) : 0;

// Status counts for tabs
$statusCounts = [];
$scStmt = $db->query("SELECT status, COUNT(*) as c FROM admissions GROUP BY status");
while ($r = $scStmt->fetch()) $statusCounts[$r['status']] = (int)$r['c'];

// Filters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = trim($_GET['search'] ?? '');
$classFilter = $_GET['class'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));

$where = [];
$params = [];
if ($statusFilter && in_array($statusFilter, $allStatuses)) { $where[] = "a.status=?"; $params[] = $statusFilter; }
if ($searchQuery) { $where[] = "(a.student_name LIKE ? OR a.phone LIKE ? OR a.email LIKE ? OR a.application_id LIKE ? OR a.father_name LIKE ?)"; $s = "%$searchQuery%"; $params = array_merge($params, [$s,$s,$s,$s,$s]); }
if ($classFilter) { $where[] = "a.class_applied=?"; $params[] = $classFilter; }
if ($dateFrom) { $where[] = "DATE(a.created_at)>=?"; $params[] = $dateFrom; }
if ($dateTo) { $where[] = "DATE(a.created_at)<=?"; $params[] = $dateTo; }

$whereClause = $where ? 'WHERE '.implode(' AND ', $where) : '';
$total = $db->prepare("SELECT COUNT(*) FROM admissions a $whereClause");
$total->execute($params);
$total = $total->fetchColumn();
$p = paginate($total, 20, $page);

$stmt = $db->prepare("SELECT a.*, u.name as reviewer_name FROM admissions a LEFT JOIN users u ON a.reviewed_by=u.id $whereClause ORDER BY a.created_at DESC LIMIT {$p['per_page']} OFFSET {$p['offset']}");
$stmt->execute($params);
$admissions = $stmt->fetchAll();

// Check duplicates (phone numbers that appear more than once)
$dupPhones = [];
try {
    $dupStmt = $db->query("SELECT phone, COUNT(*) as c FROM admissions WHERE phone IS NOT NULL GROUP BY phone HAVING c > 1");
    while ($r = $dupStmt->fetch()) $dupPhones[$r['phone']] = (int)$r['c'];
} catch (Exception $e) {}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <?php
    $kpis = [
        ['New Today', $kpiNew, 'bi-plus-circle-fill', 'primary'],
        ['Pending', $kpiPending, 'bi-clock-fill', 'warning'],
        ['Approved', $kpiApproved, 'bi-check-circle-fill', 'success'],
        ['Rejected', $kpiRejected, 'bi-x-circle-fill', 'danger'],
        ['Waitlisted', $kpiWaitlisted, 'bi-hourglass-split', 'dark'],
        ['Conversion', $kpiConversion.'%', 'bi-graph-up-arrow', 'info'],
    ];
    foreach ($kpis as $k): ?>
    <div class="col-6 col-md-4 col-xl-2">
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
    </div>
    <?php endforeach; ?>
</div>

<!-- Filters -->
<div class="card border-0 mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, phone, email, app ID..." value="<?= e($searchQuery) ?>">
            </div>
            <div class="col-md-2">
                <select name="class" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    <?php for ($i=1;$i<=12;$i++): ?><option value="<?= $i ?>" <?= $classFilter==(string)$i?'selected':'' ?>>Class <?= $i ?></option><?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach ($allStatuses as $s): ?><option value="<?= $s ?>" <?= $statusFilter===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($dateFrom) ?>" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($dateTo) ?>" placeholder="To">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i></button>
                <a href="/admin/admissions.php" class="btn btn-outline-secondary btn-sm" title="Clear"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Status Tabs -->
<ul class="nav nav-pills mb-3 flex-nowrap overflow-auto" style="gap:4px;">
    <li class="nav-item"><a href="/admin/admissions.php?<?= http_build_query(array_merge($_GET, ['status'=>''])) ?>" class="nav-link <?= !$statusFilter?'active':'' ?> btn-sm">All <span class="badge bg-light text-dark ms-1"><?= $kpiTotal ?></span></a></li>
    <?php foreach ($allStatuses as $s): if ($s==='converted') continue; ?>
    <li class="nav-item"><a href="/admin/admissions.php?<?= http_build_query(array_merge($_GET, ['status'=>$s])) ?>" class="nav-link <?= $statusFilter===$s?'active':'' ?> btn-sm"><?= ucfirst(str_replace('_',' ',$s)) ?> <span class="badge bg-light text-dark ms-1"><?= $statusCounts[$s] ?? 0 ?></span></a></li>
    <?php endforeach; ?>
</ul>

<!-- Export -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <small class="text-muted"><?= $total ?> admission(s) found</small>
    <a href="/admin/ajax/admission-actions.php?action=export_csv&<?= http_build_query(array_filter(['status'=>$statusFilter,'search'=>$searchQuery,'class'=>$classFilter,'date_from'=>$dateFrom,'date_to'=>$dateTo])) ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>Export CSV</a>
</div>

<!-- Table -->
<div class="card border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>App ID</th><th>Student</th><th>Class</th><th>Phone</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($admissions)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>No admissions found</td></tr>
                <?php else: foreach ($admissions as $a):
                    $isDup = isset($dupPhones[$a['phone']]);
                    $sc = $statusColors[$a['status']] ?? 'secondary';
                ?>
                    <tr style="cursor:pointer" onclick="openDrawer(<?= $a['id'] ?>)" class="admission-row">
                        <td><?= $a['id'] ?></td>
                        <td><code style="font-size:.8rem"><?= e($a['application_id'] ?? 'N/A') ?></code></td>
                        <td style="font-size:.85rem">
                            <strong><?= e($a['student_name']) ?></strong>
                            <?php if ($isDup): ?><i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Duplicate phone detected" style="font-size:.75rem"></i><?php endif; ?>
                            <br><small class="text-muted">F: <?= e($a['father_name'] ?? '-') ?></small>
                        </td>
                        <td>Class <?= e($a['class_applied']) ?></td>
                        <td style="font-size:.85rem"><?= e($a['phone'] ?? '-') ?></td>
                        <td><span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span></td>
                        <td style="font-size:.8rem"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm py-0 px-2" onclick="event.stopPropagation();openDrawer(<?= $a['id'] ?>)"><i class="bi bi-eye"></i></button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= paginationHtml($p, '/admin/admissions.php?' . http_build_query(array_filter(['status'=>$statusFilter,'search'=>$searchQuery,'class'=>$classFilter,'date_from'=>$dateFrom,'date_to'=>$dateTo]))) ?>

<!-- Slide-In Detail Drawer (Off-Canvas) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="admissionDrawer" style="width:560px;max-width:95vw;">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="drawerTitle">Loading...</h5>
            <small class="text-muted" id="drawerSubtitle"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0" id="drawerBody">
        <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
    </div>
</div>

<!-- Convert to Student Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h6 class="modal-title">Create Student Record?</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="mb-0" style="font-size:.85rem">This will create a new student record from the admission data and mark it as converted.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="convertForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" id="convertId">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="new_status" value="approved">
                    <input type="hidden" name="create_student" value="1">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-person-plus me-1"></i>Create Student</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const drawerEl = document.getElementById('admissionDrawer');
const drawer = new bootstrap.Offcanvas(drawerEl);

function openDrawer(id) {
    drawer.show();
    document.getElementById('drawerBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    
    fetch('/admin/ajax/admission-actions.php?action=get_detail&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { document.getElementById('drawerBody').innerHTML = '<div class="p-4 text-danger">Error loading data</div>'; return; }
            const a = data.admission;
            const notes = data.notes || [];
            const history = data.history || [];
            const docs = data.documents || {};
            
            document.getElementById('drawerTitle').textContent = a.application_id || 'Application #' + a.id;
            document.getElementById('drawerSubtitle').textContent = 'Submitted ' + a.created_at;
            
            const sc = statusColor(a.status);
            const statusLabel = a.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            
            let html = '';
            // Status badge + quick actions
            html += '<div class="p-3 border-bottom d-flex align-items-center justify-content-between">';
            html += '<span class="badge bg-'+sc+'-subtle text-'+sc+' fs-6">'+statusLabel+'</span>';
            if (a.priority !== 'normal') html += '<span class="badge bg-danger ms-1">'+a.priority.toUpperCase()+'</span>';
            html += '<div class="d-flex gap-1">';
            if (a.status !== 'converted') {
                const nextStatuses = getNextStatuses(a.status);
                nextStatuses.forEach(ns => {
                    html += '<button class="btn btn-outline-'+statusColor(ns)+' btn-sm py-0 px-2" title="'+ns.replace(/_/g,' ')+'" onclick="ajaxAction(\'update_status\',{id:'+a.id+',new_status:\''+ns+'\'},\'Change to '+ns.replace(/_/g,' ')+'?\')"><i class="bi '+statusIcon(ns)+'"></i></button>';
                });
            }
            html += '</div></div>';
            
            // Tabs
            html += '<ul class="nav nav-tabs px-3 pt-2" role="tablist">';
            html += '<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#dtDetails">Details</a></li>';
            html += '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#dtDocs">Docs <span class="badge bg-secondary-subtle text-secondary">'+Object.keys(docs).length+'</span></a></li>';
            html += '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#dtNotes">Notes <span class="badge bg-secondary-subtle text-secondary">'+notes.length+'</span></a></li>';
            html += '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#dtTimeline">Timeline</a></li>';
            html += '</ul>';
            html += '<div class="tab-content p-3">';
            
            // Details tab
            html += '<div class="tab-pane fade show active" id="dtDetails">';
            html += detailCard('Student Information', [
                ['Name', a.student_name], ['DOB', a.dob], ['Gender', a.gender],
                ['Blood Group', a.blood_group], ['Category', a.category], ['Aadhar', a.aadhar_no],
                ['Class Applied', 'Class '+a.class_applied], ['Previous School', a.previous_school]
            ]);
            html += detailCard('Parent Details', [
                ['Father', a.father_name], ['Father Phone', a.father_phone], ['Father Occupation', a.father_occupation],
                ['Mother', a.mother_name], ['Mother Occupation', a.mother_occupation]
            ]);
            html += detailCard('Contact & Address', [
                ['Phone', a.phone], ['Email', a.email],
                ['Address', a.address], ['Village', a.village], ['District', a.district],
                ['State', a.state], ['PIN', a.pincode]
            ]);
            if (a.interview_date) html += detailCard('Interview', [['Date', a.interview_date]]);
            if (a.follow_up_date) html += detailCard('Follow-up', [['Date', a.follow_up_date]]);
            if (a.remarks) html += detailCard('Remarks', [['', a.remarks]]);
            
            // Follow-up & interview forms
            if (a.status !== 'converted') {
                html += '<div class="row g-2 mt-2">';
                html += '<div class="col-6"><label class="form-label fw-semibold" style="font-size:.75rem">Follow-up Date</label><input type="date" id="drawerFollowup" class="form-control form-control-sm" value="'+(a.follow_up_date||'')+'"><button class="btn btn-outline-primary btn-sm mt-1 w-100" onclick="ajaxAction(\'set_followup\',{id:'+a.id+',follow_up_date:document.getElementById(\'drawerFollowup\').value})">Set</button></div>';
                html += '<div class="col-6"><label class="form-label fw-semibold" style="font-size:.75rem">Interview Date</label><input type="datetime-local" id="drawerInterview" class="form-control form-control-sm" value="'+(a.interview_date ? a.interview_date.replace(' ','T') : '')+'"><button class="btn btn-outline-warning btn-sm mt-1 w-100" onclick="ajaxAction(\'set_interview\',{id:'+a.id+',interview_date:document.getElementById(\'drawerInterview\').value})">Schedule</button></div>';
                html += '</div>';
            }
            html += '</div>';
            
            // Documents tab
            html += '<div class="tab-pane fade" id="dtDocs">';
            if (Object.keys(docs).length === 0) {
                html += '<p class="text-muted text-center py-4">No documents uploaded</p>';
            } else {
                for (const [label, path] of Object.entries(docs)) {
                    const isImg = /\.(jpg|jpeg|png|webp)$/i.test(path);
                    html += '<div class="d-flex align-items-center gap-2 p-2 rounded mb-2" style="background:var(--bg-body)">';
                    if (isImg) html += '<img src="/'+path+'" class="rounded" style="width:60px;height:60px;object-fit:cover">';
                    else html += '<div style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#fee2e2;border-radius:8px"><i class="bi bi-file-pdf text-danger" style="font-size:1.5rem"></i></div>';
                    html += '<div><div class="fw-semibold" style="font-size:.82rem">'+label.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase())+'</div><a href="/'+path+'" target="_blank" class="text-primary" style="font-size:.75rem">View / Download</a></div>';
                    html += '</div>';
                }
            }
            html += '</div>';
            
            // Notes tab
            html += '<div class="tab-pane fade" id="dtNotes">';
            html += '<form onsubmit="event.preventDefault();ajaxAction(\'add_note\',{id:'+a.id+',note:this.note.value});this.note.value=\'\';" class="mb-3"><div class="input-group input-group-sm"><input type="text" name="note" class="form-control" placeholder="Add a note..." required><button class="btn btn-primary"><i class="bi bi-plus"></i></button></div></form>';
            if (notes.length === 0) {
                html += '<p class="text-muted text-center py-3" style="font-size:.85rem">No notes yet</p>';
            } else {
                notes.forEach(n => {
                    html += '<div class="p-2 rounded mb-2" style="background:var(--bg-body);font-size:.82rem"><div class="d-flex justify-content-between"><strong>'+(n.user_name||'System')+'</strong><small class="text-muted">'+n.created_at+'</small></div><div class="mt-1">'+escHtml(n.note)+'</div></div>';
                });
            }
            html += '</div>';
            
            // Timeline tab
            html += '<div class="tab-pane fade" id="dtTimeline">';
            if (history.length === 0) {
                html += '<p class="text-muted text-center py-3" style="font-size:.85rem">No status changes recorded</p>';
            } else {
                html += '<div class="position-relative" style="padding-left:20px">';
                history.forEach(h => {
                    html += '<div class="mb-3 position-relative"><div style="position:absolute;left:-20px;top:4px;width:10px;height:10px;border-radius:50%;background:var(--brand-primary)"></div>';
                    html += '<div style="font-size:.82rem"><strong>'+(h.old_status?h.old_status.replace(/_/g,' '):'—')+' → '+h.new_status.replace(/_/g,' ')+'</strong></div>';
                    html += '<div style="font-size:.72rem;color:var(--text-muted)">'+(h.user_name||'System')+' • '+h.created_at+'</div>';
                    if (h.remarks) html += '<div style="font-size:.75rem;color:var(--text-secondary);margin-top:2px">'+escHtml(h.remarks)+'</div>';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '</div>';
            
            html += '</div>'; // tab-content
            
            // Bottom actions
            if (a.status === 'approved') {
                html += '<div class="p-3 border-top"><button class="btn btn-success btn-sm w-100" onclick="showConvertModal('+a.id+')"><i class="bi bi-person-plus me-1"></i>Create Student Record</button></div>';
            }
            <?php if (isSuperAdmin()): ?>
            if (a.status !== 'converted') {
                html += '<div class="p-3 pt-0"><button class="btn btn-outline-danger btn-sm w-100" onclick="ajaxAction(\'delete\',{id:'+a.id+'},\'Delete this admission permanently?\')"><i class="bi bi-trash me-1"></i>Delete</button></div>';
            }
            <?php endif; ?>
            
            document.getElementById('drawerBody').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('drawerBody').innerHTML = '<div class="p-4 text-danger">Failed to load: '+err.message+'</div>';
        });
}

function showConvertModal(id) {
    document.getElementById('convertId').value = id;
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}

function detailCard(title, rows) {
    let html = '<div class="mb-3"><h6 class="fw-semibold mb-2" style="font-size:.78rem;text-transform:uppercase;color:var(--text-muted);letter-spacing:.5px">'+title+'</h6>';
    html += '<div class="rounded p-2" style="background:var(--bg-body);border:1px solid var(--border-color)">';
    rows.forEach(([l,v]) => {
        if (!v) return;
        html += '<div class="d-flex justify-content-between py-1" style="font-size:.82rem;border-bottom:1px solid var(--border-color)"><span style="color:var(--text-muted)">'+l+'</span><span class="fw-medium">'+escHtml(v)+'</span></div>';
    });
    html += '</div></div>';
    return html;
}

function statusColor(s) {
    const m = {new:'primary',contacted:'info',documents_verified:'secondary',interview_scheduled:'warning',approved:'success',rejected:'danger',waitlisted:'dark',converted:'success'};
    return m[s]||'secondary';
}
function statusIcon(s) {
    const m = {new:'bi-plus-circle',contacted:'bi-telephone',documents_verified:'bi-file-check',interview_scheduled:'bi-calendar-event',approved:'bi-check-circle',rejected:'bi-x-circle',waitlisted:'bi-hourglass',converted:'bi-person-check'};
    return m[s]||'bi-circle';
}
function getNextStatuses(current) {
    const flow = {
        'new': ['contacted','rejected'],
        'contacted': ['documents_verified','rejected'],
        'documents_verified': ['interview_scheduled','approved','rejected'],
        'interview_scheduled': ['approved','rejected','waitlisted'],
        'approved': ['converted','waitlisted'],
        'waitlisted': ['approved','rejected'],
        'rejected': ['new']
    };
    return flow[current] || [];
}
function escHtml(s) {
    if (!s) return '—';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

let _currentDrawerId = null;

function ajaxAction(action, params, confirmMsg) {
    if (confirmMsg && !confirm(confirmMsg)) return;
    const fd = new FormData();
    fd.append('action', action);
    fd.append('csrf_token', '<?= csrfToken() ?>');
    for (const [k,v] of Object.entries(params)) fd.append(k, v);
    
    fetch('/admin/ajax/admission-actions.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Reload drawer
                if (_currentDrawerId && action !== 'delete') {
                    openDrawer(_currentDrawerId);
                } else {
                    drawer.hide();
                }
                // Reload page to update table/KPIs
                setTimeout(() => location.reload(), 300);
            } else {
                alert(data.error || 'Action failed');
            }
        })
        .catch(err => alert('Error: ' + err.message));
}

// Track current drawer ID
const origOpenDrawer = openDrawer;
openDrawer = function(id) {
    _currentDrawerId = id;
    origOpenDrawer(id);
};
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
