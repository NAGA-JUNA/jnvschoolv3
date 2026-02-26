<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: Detail view for drawer
if ($action === 'get_detail' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['success'=>false,'error'=>'Invalid ID']); exit; }

    $adm = $db->prepare("SELECT a.*, u.name as reviewer_name FROM admissions a LEFT JOIN users u ON a.reviewed_by=u.id WHERE a.id=?");
    $adm->execute([$id]);
    $adm = $adm->fetch();
    if (!$adm) { echo json_encode(['success'=>false,'error'=>'Not found']); exit; }

    $notes = $db->prepare("SELECT n.*, u.name as user_name FROM admission_notes n LEFT JOIN users u ON n.user_id=u.id WHERE n.admission_id=? ORDER BY n.created_at DESC");
    $notes->execute([$id]);
    $notes = $notes->fetchAll();

    $history = $db->prepare("SELECT h.*, u.name as user_name FROM admission_status_history h LEFT JOIN users u ON h.changed_by=u.id WHERE h.admission_id=? ORDER BY h.created_at DESC");
    $history->execute([$id]);
    $history = $history->fetchAll();

    $documents = [];
    if ($adm['documents']) {
        $docs = json_decode($adm['documents'], true);
        if (is_array($docs)) $documents = $docs;
        elseif (is_string($adm['documents']) && !empty($adm['documents'])) {
            $documents = ['document' => $adm['documents']];
        }
    }

    echo json_encode([
        'success' => true,
        'admission' => $adm,
        'notes' => $notes,
        'history' => $history,
        'documents' => $documents
    ]);
    exit;
}

// GET: Export CSV
if ($action === 'export_csv' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $where = [];
    $params = [];
    if (!empty($_GET['status'])) { $where[] = "a.status=?"; $params[] = $_GET['status']; }
    if (!empty($_GET['search'])) { $s = '%'.$_GET['search'].'%'; $where[] = "(a.student_name LIKE ? OR a.phone LIKE ? OR a.email LIKE ? OR a.application_id LIKE ?)"; $params = array_merge($params, [$s,$s,$s,$s]); }
    if (!empty($_GET['class'])) { $where[] = "a.class_applied=?"; $params[] = $_GET['class']; }
    if (!empty($_GET['date_from'])) { $where[] = "DATE(a.created_at)>=?"; $params[] = $_GET['date_from']; }
    if (!empty($_GET['date_to'])) { $where[] = "DATE(a.created_at)<=?"; $params[] = $_GET['date_to']; }
    $whereClause = $where ? 'WHERE '.implode(' AND ', $where) : '';

    $stmt = $db->prepare("SELECT a.application_id, a.student_name, a.father_name, a.mother_name, a.dob, a.gender, a.class_applied, a.phone, a.email, a.address, a.status, a.source, a.priority, a.created_at FROM admissions a $whereClause ORDER BY a.created_at DESC");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="admissions_'.date('Y-m-d').'.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Application ID','Student Name','Father Name','Mother Name','DOB','Gender','Class','Phone','Email','Address','Status','Source','Priority','Applied Date']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['application_id'],$r['student_name'],$r['father_name'],$r['mother_name'],$r['dob'],$r['gender'],$r['class_applied'],$r['phone'],$r['email'],$r['address'],$r['status'],$r['source'],$r['priority'],$r['created_at']]);
    }
    fclose($out);
    exit;
}

// GET: Duplicate check
if ($action === 'check_duplicate' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $phone = $_GET['phone'] ?? '';
    $email = $_GET['email'] ?? '';
    $duplicates = [];

    if ($phone) {
        $stmt = $db->prepare("SELECT id, application_id, student_name, status FROM admissions WHERE phone=? LIMIT 5");
        $stmt->execute([$phone]);
        $duplicates = array_merge($duplicates, $stmt->fetchAll());
    }
    if ($email) {
        $stmt = $db->prepare("SELECT id, application_id, student_name, status FROM admissions WHERE email=? LIMIT 5");
        $stmt->execute([$email]);
        $duplicates = array_merge($duplicates, $stmt->fetchAll());
    }

    echo json_encode(['success'=>true, 'duplicates'=>$duplicates]);
    exit;
}

// GET: Seat count
if ($action === 'seat_count' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $class = $_GET['class'] ?? '';
    $year = getSetting('academic_year', date('Y').'-'.(date('Y')+1));
    
    $stmt = $db->prepare("SELECT total_seats FROM class_seat_capacity WHERE class=? AND academic_year=?");
    $stmt->execute([$class, $year]);
    $capacity = $stmt->fetchColumn();
    
    $filled = $db->prepare("SELECT COUNT(*) FROM admissions WHERE class_applied=? AND status IN ('approved','converted')");
    $filled->execute([$class]);
    $filled = $filled->fetchColumn();
    
    echo json_encode(['success'=>true, 'total'=>(int)$capacity, 'filled'=>(int)$filled, 'available'=>max(0,(int)$capacity-(int)$filled)]);
    exit;
}

echo json_encode(['success'=>false,'error'=>'Invalid action']);
