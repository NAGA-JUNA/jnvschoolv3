<?php
$pageTitle = 'Seat Capacity';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

$academicYear = getSetting('academic_year', date('Y').'-'.(date('Y')+1));

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $classes = $_POST['classes'] ?? [];
    $seats = $_POST['seats'] ?? [];
    
    foreach ($classes as $idx => $cls) {
        $cls = trim($cls);
        $s = (int)($seats[$idx] ?? 40);
        if (!$cls || $s < 0) continue;
        
        $existing = $db->prepare("SELECT id FROM class_seat_capacity WHERE class=? AND academic_year=?");
        $existing->execute([$cls, $academicYear]);
        
        if ($existing->fetchColumn()) {
            $db->prepare("UPDATE class_seat_capacity SET total_seats=? WHERE class=? AND academic_year=?")->execute([$s, $cls, $academicYear]);
        } else {
            $db->prepare("INSERT INTO class_seat_capacity (class, total_seats, academic_year) VALUES (?,?,?)")->execute([$cls, $s, $academicYear]);
        }
    }
    auditLog('seat_capacity_updated', 'seat_capacity', null, "Year: $academicYear");
    setFlash('success', 'Seat capacity updated.');
    header('Location: /admin/seat-capacity.php');
    exit;
}

// Load current capacity
$capacityData = [];
$stmt = $db->prepare("SELECT * FROM class_seat_capacity WHERE academic_year=? ORDER BY CAST(class AS UNSIGNED)");
$stmt->execute([$academicYear]);
while ($r = $stmt->fetch()) {
    $capacityData[$r['class']] = $r;
}

// Get filled counts
$filledCounts = [];
$fStmt = $db->query("SELECT class_applied, COUNT(*) as c FROM admissions WHERE status IN ('approved','converted') GROUP BY class_applied");
while ($r = $fStmt->fetch()) {
    $filledCounts[$r['class_applied']] = (int)$r['c'];
}

// Active students per class
$studentCounts = [];
$sStmt = $db->query("SELECT class, COUNT(*) as c FROM students WHERE status='active' GROUP BY class");
while ($r = $sStmt->fetch()) {
    $studentCounts[$r['class']] = (int)$r['c'];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-grid-3x3-gap me-2"></i>Seat Capacity Management</h5>
        <small class="text-muted">Academic Year: <strong><?= e($academicYear) ?></strong></small>
    </div>
</div>

<form method="POST">
    <?= csrfField() ?>
    <div class="row g-3">
        <?php for ($i = 1; $i <= 12; $i++):
            $cls = (string)$i;
            $cap = $capacityData[$cls] ?? null;
            $totalSeats = $cap ? (int)$cap['total_seats'] : 40;
            $filled = ($filledCounts[$cls] ?? 0) + ($studentCounts[$cls] ?? 0);
            $available = max(0, $totalSeats - $filled);
            $pct = $totalSeats > 0 ? round(($filled / $totalSeats) * 100) : 0;
            $barColor = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Class <?= $i ?></h6>
                        <span class="badge bg-<?= $barColor ?>-subtle text-<?= $barColor ?>"><?= $available ?> free</span>
                    </div>
                    <input type="hidden" name="classes[]" value="<?= $cls ?>">
                    <div class="mb-2">
                        <label class="form-label mb-0" style="font-size:.72rem;color:var(--text-muted)">Total Seats</label>
                        <input type="number" name="seats[]" class="form-control form-control-sm" value="<?= $totalSeats ?>" min="0" max="500">
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:.72rem;color:var(--text-muted)">
                        <span>Filled: <?= $filled ?></span>
                        <span>Available: <?= $available ?></span>
                    </div>
                    <div class="progress mt-1" style="height:6px;">
                        <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= min(100,$pct) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Seat Capacity</button>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
