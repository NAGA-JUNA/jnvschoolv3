<?php
// ============================================
// GalleryController — Categories, Approvals, Upload, YouTube
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class GalleryController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ─── CATEGORIES ─────────────────────────────

    // GET /admin/gallery/categories
    public function adminCategories(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->query("SELECT * FROM gallery_categories ORDER BY name");
        jsonSuccess($stmt->fetchAll());
    }

    // POST /admin/gallery/categories
    public function createCategory(): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('name', 'Name')->required('slug', 'Slug')
          ->unique('slug', 'gallery_categories', 'slug', null, 'Slug');
        $v->validate();

        $stmt = $this->db->prepare(
            "INSERT INTO gallery_categories (name, slug, type, cover_image, is_active)
             VALUES (:name, :slug, :type, :cover, :active)"
        );
        $stmt->execute([
            ':name'   => trim($data['name']),
            ':slug'   => trim($data['slug']),
            ':type'   => $data['type'] ?? 'images',
            ':cover'  => $data['cover_image'] ?? null,
            ':active' => (int)($data['is_active'] ?? 1),
        ]);

        auditLog('create', 'gallery_categories', (int)$this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'Category created', 201);
    }

    // PUT /admin/gallery/categories/{id}
    public function updateCategory(int $id): void {
        requireRole(ADMIN_ROLES);
        $data = getJsonInput();

        $allowed = ['name','slug','type','cover_image','is_active'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "`$f` = :$f";
                $params[":$f"] = $data[$f];
            }
        }
        if (empty($sets)) jsonError('No fields to update', 422);

        $stmt = $this->db->prepare("UPDATE gallery_categories SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) jsonError('Category not found', 404);

        auditLog('update', 'gallery_categories', $id);
        jsonSuccess(null, 'Category updated');
    }

    // DELETE /admin/gallery/categories/{id}
    public function deleteCategory(int $id): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $stmt = $this->db->prepare("DELETE FROM gallery_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Category not found', 404);

        auditLog('delete', 'gallery_categories', $id);
        jsonSuccess(null, 'Category deleted');
    }

    // ─── APPROVALS ──────────────────────────────

    // GET /admin/gallery/approvals
    public function approvals(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT gi.*, gc.name as category_name, u.name as uploaded_by_name
             FROM gallery_items gi
             LEFT JOIN gallery_categories gc ON gi.category_id = gc.id
             LEFT JOIN users u ON gi.uploaded_by = u.id
             WHERE gi.status = 'pending'
             ORDER BY gi.created_at DESC"
        );
        $stmt->execute();
        jsonSuccess($stmt->fetchAll());
    }

    // PATCH /admin/gallery/items/{id}/approve
    public function approveItem(int $id): void {
        $user = requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare("UPDATE gallery_items SET status = 'approved', approved_by = :uid WHERE id = :id");
        $stmt->execute([':uid' => $user['user_id'], ':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Item not found', 404);

        // Update category item count
        $this->db->prepare(
            "UPDATE gallery_categories gc SET item_count = (SELECT COUNT(*) FROM gallery_items WHERE category_id = gc.id AND status = 'approved')"
        )->execute();

        auditLog('approve', 'gallery_items', $id);
        jsonSuccess(null, 'Gallery item approved');
    }

    // PATCH /admin/gallery/items/{id}/reject
    public function rejectItem(int $id): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare("UPDATE gallery_items SET status = 'rejected' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) jsonError('Item not found', 404);

        auditLog('reject', 'gallery_items', $id);
        jsonSuccess(null, 'Gallery item rejected');
    }

    // ─── TEACHER UPLOADS ────────────────────────

    // POST /teacher/gallery/upload
    public function teacherUpload(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);

        if (!isset($_FILES['file'])) jsonError('Image file is required', 400);
        $categoryId = $_POST['category_id'] ?? null;
        if (!$categoryId) jsonError('Category is required', 422);

        $fileUrl = uploadFile($_FILES['file'], UPLOAD_GALLERY, [
            'max_size' => MAX_UPLOAD_SIZE,
            'types' => ALLOWED_IMAGE_TYPES,
        ]);

        $stmt = $this->db->prepare(
            "INSERT INTO gallery_items (category_id, title, file_url, type, uploaded_by)
             VALUES (:cid, :title, :url, 'image', :uid)"
        );
        $stmt->execute([
            ':cid'   => (int) $categoryId,
            ':title' => $_POST['title'] ?? null,
            ':url'   => $fileUrl,
            ':uid'   => $user['user_id'],
        ]);

        auditLog('upload', 'gallery_items', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'Upload submitted for approval', 201);
    }

    // POST /teacher/gallery/youtube
    public function addYoutubeLink(): void {
        $user = requireRole([ROLE_TEACHER, ROLE_OFFICE]);
        $data = getJsonInput();

        $v = new Validator($data);
        $v->required('category_id', 'Category')->required('youtube_id', 'YouTube Video ID');
        $v->validate();

        $stmt = $this->db->prepare(
            "INSERT INTO gallery_items (category_id, title, file_url, type, youtube_id, uploaded_by)
             VALUES (:cid, :title, :url, 'youtube', :ytid, :uid)"
        );
        $stmt->execute([
            ':cid'   => (int) $data['category_id'],
            ':title' => $data['title'] ?? null,
            ':url'   => 'https://www.youtube.com/watch?v=' . $data['youtube_id'],
            ':ytid'  => $data['youtube_id'],
            ':uid'   => $user['user_id'],
        ]);

        auditLog('add_youtube', 'gallery_items', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId()], 'YouTube link submitted for approval', 201);
    }

    // ─── PUBLIC ─────────────────────────────────

    // GET /public/gallery/categories
    public function publicCategories(): void {
        $stmt = $this->db->query(
            "SELECT id, name, slug, type, cover_image, item_count
             FROM gallery_categories WHERE is_active = 1 ORDER BY name"
        );
        jsonSuccess($stmt->fetchAll());
    }

    // GET /public/gallery/items?category={slug}
    public function publicItems(): void {
        $slug = $_GET['category'] ?? '';
        if (!$slug) jsonError('Category slug is required', 400);

        $stmt = $this->db->prepare(
            "SELECT gi.id, gi.title, gi.file_url, gi.thumbnail_url, gi.type, gi.youtube_id, gi.created_at
             FROM gallery_items gi
             JOIN gallery_categories gc ON gi.category_id = gc.id
             WHERE gc.slug = :slug AND gi.status = 'approved'
             ORDER BY gi.created_at DESC"
        );
        $stmt->execute([':slug' => $slug]);
        jsonSuccess($stmt->fetchAll());
    }
}
