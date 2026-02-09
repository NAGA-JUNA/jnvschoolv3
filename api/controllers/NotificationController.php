<?php
// ============================================
// NotificationController — CRUD, Approve/Reject, Teacher Submit
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class NotificationController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/notifications
    public function index(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $where = ["1=1"];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = "n.status = :status";
            $params[':status'] = $_GET['status'];
        }
        if (!empty($_GET['search'])) {
            $where[] = "(n.title LIKE :s OR n.body LIKE :s2)";
            $params[':s']  = '%' . $_GET['search'] . '%';
            $params[':s2'] = '%' . $_GET['search'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications n WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT n.*, u.name as submitted_by_name, a.name as approved_by_name
             FROM notifications n
             LEFT JOIN users u ON n.submitted_by = u.id
             LEFT JOIN users a ON n.approved_by = a.id
             WHERE $whereSql ORDER BY n.created_at DESC LIMIT :l OFFSET :o"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // GET /admin/notifications/{id}
    public function show(int $id): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT n.*, u.name as submitted_by_name FROM notifications n
             LEFT JOIN users u ON n.submitted_by = u.id WHERE n.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $notification = $stmt->fetch();
        if (!$notification) jsonError('Notification not found', 404);
        jsonSuccess($notification);
    }

    // PATCH /admin/notifications/{id}/approve
    public function approve(int $id): void {
        $user = requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "UPDATE notifications SET status = 'approved', is_public = 1, approved_by = :uid, approved_at = NOW() WHERE id = :id"
        );
        $stmt->execute([':uid' => $user['user_id'], ':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Notification not found', 404);

        auditLog('approve', 'notifications', $id);
        jsonSuccess(null, 'Notification approved');
    }

    // PATCH /admin/notifications/{id}/reject
    public function reject(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();
        $reason = $data['reason'] ?? null;

        $stmt = $this->db->prepare(
            "UPDATE notifications SET status = 'rejected', rejection_reason = :reason WHERE id = :id"
        );
        $stmt->execute([':reason' => $reason, ':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Notification not found', 404);

        auditLog('reject', 'notifications', $id);
        jsonSuccess(null, 'Notification rejected');
    }

    // POST /admin/notifications/bulk-approve
    public function bulkApprove(): void {
        $user = requireRole(ADMIN_ROLES);
        $data = getJsonInput();
        $ids = $data['ids'] ?? [];
        if (empty($ids)) jsonError('No IDs provided', 422);

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_map('intval', $ids);
        $params[] = $user['user_id'];

        $stmt = $this->db->prepare(
            "UPDATE notifications SET status = 'approved', is_public = 1, approved_by = ?, approved_at = NOW() WHERE id IN ($placeholders)"
        );
        // Re-arrange params: approved_by first, then ids
        $execParams = $ids;
        array_unshift($execParams, $user['user_id']);
        // Actually we need: WHERE id IN (...) and SET approved_by = ?
        // Let's redo with named params
        $stmt = $this->db->prepare(
            "UPDATE notifications SET status = 'approved', is_public = 1, approved_by = :uid, approved_at = NOW() WHERE id IN ($placeholders)"
        );
        $stmt->bindValue(':uid', $user['user_id'], PDO::PARAM_INT);
        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, (int) $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        auditLog('bulk_approve', 'notifications', null, ['ids' => $ids]);
        jsonSuccess(['approved' => $stmt->rowCount()], 'Notifications approved');
    }

    // POST /teacher/notifications — Teacher submit
    public function teacherSubmit(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('title', 'Title')->required('body', 'Body')
          ->maxLength('title', 200, 'Title');
        $v->validate();

        $attachment = null;
        if (isset($_FILES['attachment'])) {
            $attachment = uploadFile($_FILES['attachment'], UPLOAD_NOTIFICATIONS, [
                'types' => array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES),
            ]);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO notifications (title, body, urgency, attachment, expiry_date, submitted_by)
             VALUES (:title, :body, :urgency, :attach, :expiry, :uid)"
        );
        $stmt->execute([
            ':title'   => trim($data['title']),
            ':body'    => trim($data['body']),
            ':urgency' => $data['urgency'] ?? 'normal',
            ':attach'  => $attachment ?? ($data['attachment'] ?? null),
            ':expiry'  => $data['expiry_date'] ?? null,
            ':uid'     => $user['user_id'],
        ]);

        auditLog('submit', 'notifications', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'Notification submitted for approval', 201);
    }

    // GET /public/notifications
    public function publicList(): void {
        $stmt = $this->db->prepare(
            "SELECT id, title, body, urgency, attachment, created_at, expiry_date
             FROM notifications
             WHERE status = 'approved' AND is_public = 1
               AND (expiry_date IS NULL OR expiry_date >= CURDATE())
             ORDER BY created_at DESC LIMIT 50"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }
}
