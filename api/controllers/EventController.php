<?php
// ============================================
// EventController — CRUD + Public Listing
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';

class EventController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/events
    public function index(): void {
        requireRole(ADMIN_ROLES);
        [$page, $perPage, $offset] = getPagination();

        $where = ["1=1"];
        $params = [];
        if (!empty($_GET['type'])) {
            $where[] = "type = :type";
            $params[':type'] = $_GET['type'];
        }
        if (!empty($_GET['search'])) {
            $where[] = "(title LIKE :s OR description LIKE :s2)";
            $params[':s']  = '%' . $_GET['search'] . '%';
            $params[':s2'] = '%' . $_GET['search'] . '%';
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM events WHERE $whereSql");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT e.*, u.name as created_by_name FROM events e
             LEFT JOIN users u ON e.created_by = u.id
             WHERE $whereSql ORDER BY e.event_date DESC LIMIT :l OFFSET :o"
        );
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();

        jsonPaginated($stmt->fetchAll(), $total, $page, $perPage);
    }

    // POST /admin/events
    public function store(): void {
        $user = requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('title', 'Title')->required('event_date', 'Event Date')->date('event_date', 'Event Date');
        $v->validate();

        $stmt = $this->db->prepare(
            "INSERT INTO events (title, description, event_date, event_time, location, type, is_public, created_by)
             VALUES (:title, :desc, :date, :time, :loc, :type, :public, :uid)"
        );
        $stmt->execute([
            ':title'  => trim($data['title']),
            ':desc'   => $data['description'] ?? null,
            ':date'   => $data['event_date'],
            ':time'   => $data['event_time'] ?? null,
            ':loc'    => $data['location'] ?? null,
            ':type'   => $data['type'] ?? 'other',
            ':public' => (int)($data['is_public'] ?? 1),
            ':uid'    => $user['user_id'],
        ]);

        auditLog('create', 'events', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'Event created', 201);
    }

    // PUT /admin/events/{id}
    public function update(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $allowed = ['title','description','event_date','event_time','location','type','is_public'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "`$f` = :$f";
                $params[":$f"] = $data[$f];
            }
        }
        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE events SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) jsonError('Event not found', 404);

        auditLog('update', 'events', $id);
        jsonSuccess(null, 'Event updated');
    }

    // DELETE /admin/events/{id}
    public function destroy(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Event not found', 404);

        auditLog('delete', 'events', $id);
        jsonSuccess(null, 'Event deleted');
    }

    // GET /public/events
    public function publicList(): void {
        $stmt = $this->db->prepare(
            "SELECT id, title, description, event_date, event_time, location, type
             FROM events WHERE is_public = 1 AND event_date >= CURDATE()
             ORDER BY event_date ASC LIMIT 50"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }
}
