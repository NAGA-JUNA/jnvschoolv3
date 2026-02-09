<?php
// ============================================
// SliderController — Home Banner/Slider CRUD + Reorder
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class SliderController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /home/slider (Public — no auth)
    public function index(): void {
        $stmt = $this->db->prepare(
            "SELECT id, title, subtitle, badge_text,
                    cta_primary_text, cta_primary_link,
                    cta_secondary_text, cta_secondary_link,
                    image_url, is_active, sort_order
             FROM home_slider
             WHERE is_active = 1
             ORDER BY sort_order ASC"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }

    // GET all slides for admin (includes inactive)
    public function adminIndex(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $stmt = $this->db->prepare("SELECT * FROM home_slider ORDER BY sort_order ASC");
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }

    // POST /home/slider
    public function store(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $data  = getJsonInput();
        $title = trim($data['title'] ?? '');
        if ($title === '') jsonError('Title is required', 422);

        $imageUrl = $data['image_url'] ?? '';
        if (isset($_FILES['image'])) {
            $imageUrl = uploadFile($_FILES['image'], UPLOAD_SLIDER, [
                'max_size' => 2 * 1024 * 1024,
                'types'    => ['jpg', 'jpeg', 'png', 'webp'],
            ]);
        }
        if ($imageUrl === '') jsonError('Background image is required', 422);

        $stmt = $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM home_slider");
        $nextOrder = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "INSERT INTO home_slider
                (title, subtitle, badge_text, cta_primary_text, cta_primary_link,
                 cta_secondary_text, cta_secondary_link, image_url, is_active, sort_order, created_by)
             VALUES (:title, :sub, :badge, :cta1t, :cta1l, :cta2t, :cta2l, :img, :active, :sort, :uid)"
        );
        $stmt->execute([
            ':title'  => $title,
            ':sub'    => $data['subtitle'] ?? null,
            ':badge'  => $data['badge_text'] ?? null,
            ':cta1t'  => $data['cta_primary_text'] ?? 'Apply Now',
            ':cta1l'  => $data['cta_primary_link'] ?? '/admissions',
            ':cta2t'  => $data['cta_secondary_text'] ?? 'Learn More',
            ':cta2l'  => $data['cta_secondary_link'] ?? '/about',
            ':img'    => $imageUrl,
            ':active' => (int)($data['is_active'] ?? 1),
            ':sort'   => $nextOrder,
            ':uid'    => currentUserId(),
        ]);

        auditLog('create', 'home_slider', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'Slide created', 201);
    }

    // PUT /home/slider/{id}
    public function update(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data = getJsonInput();

        $allowed = ['title','subtitle','badge_text','cta_primary_text','cta_primary_link',
            'cta_secondary_text','cta_secondary_link','image_url','is_active','sort_order'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "`$f` = :$f";
                $params[":$f"] = $data[$f];
            }
        }

        if (isset($_FILES['image'])) {
            $imageUrl = uploadFile($_FILES['image'], UPLOAD_SLIDER, [
                'max_size' => 2 * 1024 * 1024,
                'types'    => ['jpg', 'jpeg', 'png', 'webp'],
            ]);
            $sets[] = "`image_url` = :image_url";
            $params[':image_url'] = $imageUrl;
        }

        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE home_slider SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) jsonError('Slide not found', 404);

        auditLog('update', 'home_slider', $id);
        jsonSuccess(null, 'Slide updated');
    }

    // DELETE /home/slider/{id}
    public function destroy(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $stmt = $this->db->prepare("SELECT image_url FROM home_slider WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $slide = $stmt->fetch();
        if (!$slide) jsonError('Slide not found', 404);

        deleteUploadedFile($slide['image_url']);

        $this->db->prepare("DELETE FROM home_slider WHERE id = :id")->execute([':id' => $id]);
        auditLog('delete', 'home_slider', $id);
        jsonSuccess(null, 'Slide deleted');
    }

    // PATCH /home/slider/reorder
    public function reorder(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data  = getJsonInput();
        $order = $data['order'] ?? [];

        $stmt = $this->db->prepare("UPDATE home_slider SET sort_order = :sort WHERE id = :id");
        foreach ($order as $item) {
            $stmt->execute([':id' => (int) $item['id'], ':sort' => (int) $item['sort_order']]);
        }

        auditLog('reorder', 'home_slider', null);
        jsonSuccess(null, 'Slides reordered');
    }
}
