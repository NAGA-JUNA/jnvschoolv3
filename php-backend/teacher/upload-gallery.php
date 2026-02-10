<?php
require_once __DIR__.'/../includes/auth.php';
requireTeacher();
$db = getDB();
$uid = currentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $description = trim($_POST['description'] ?? '');

    if ($category === 'videos') {
        $youtubeUrl = trim($_POST['youtube_url'] ?? '');
        if ($title && $youtubeUrl) {
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $youtubeUrl, $m);
            $ytId = $m[1] ?? '';
            if ($ytId) {
                $stmt = $db->prepare("INSERT INTO gallery_items (title, category, description, file_path, file_type, uploaded_by, status) VALUES (?, ?, ?, ?, 'video', ?, 'pending')");
                $stmt->execute([$title, $category, $description, $youtubeUrl, $uid]);
                auditLog('upload_gallery', 'gallery_item', (int)$db->lastInsertId(), "Video: $title");
                setFlash('success', 'Video submitted for approval.');
            } else {
                setFlash('error', 'Invalid YouTube URL.');
            }
        } else {
            setFlash('error', 'Title and YouTube URL are required.');
        }
    } else {
        if ($title && !empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            if (!in_array($file['type'], $allowed)) {
                setFlash('error', 'Only JPG, PNG, WebP, and GIF images allowed.');
            } elseif ($file['size'] > $maxSize) {
                setFlash('error', 'Image must be under 5MB.');
            } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                setFlash('error', 'Upload failed.');
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/gallery/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $stmt = $db->prepare("INSERT INTO gallery_items (title, category, description, file_path, file_type, uploaded_by, status) VALUES (?, ?, ?, ?, 'image', ?, 'pending')");
                    $stmt->execute([$title, $category, $description, 'uploads/gallery/' . $filename, $uid]);
                    auditLog('upload_gallery', 'gallery_item', (int)$db->lastInsertId(), "Image: $title");
                    setFlash('success', 'Image submitted for approval.');
                } else {
                    setFlash('error', 'Failed to save file.');
                }
            }
        } else {
            setFlash('error', 'Title and image are required.');
        }
    }
    header('Location: /teacher/upload-gallery.php');
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$total = $db->prepare("SELECT COUNT(*) FROM gallery_items WHERE uploaded_by=?"); $total->execute([$uid]); $total = $total->fetchColumn();
$p = paginate($total, 12, $page);
$items = $db->prepare("SELECT id, title, category, file_path, file_type, status, created_at FROM gallery_items WHERE uploaded_by=? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$items->execute([$uid, $p['per_page'], $p['offset']]);
$items = $items->fetchAll();

$pageTitle = 'Upload to Gallery';
require_once __DIR__.'/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-camera-fill me-2 text-warning"></i>Upload New</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category" class="form-select" id="categorySelect" onchange="toggleUploadType()">
                            <option value="general">General</option>
                            <option value="academic">Academic</option>
                            <option value="sports">Sports</option>
                            <option value="cultural">Cultural</option>
                            <option value="infrastructure">Infrastructure</option>
                            <option value="videos">Videos (YouTube)</option>
                        </select>
                    </div>
                    <div id="imageUpload" class="mb-3">
                        <label class="form-label fw-semibold">Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Max 5MB. JPG, PNG, WebP, GIF.</div>
                    </div>
                    <div id="videoUpload" class="mb-3 d-none">
                        <label class="form-label fw-semibold">YouTube URL <span class="text-danger">*</span></label>
                        <input type="url" name="youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i>Submit for Approval</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-images me-2"></i>My Uploads (<?= $total ?>)</div>
            <div class="card-body">
                <?php if (empty($items)): ?>
                    <p class="text-muted mb-0">No uploads yet.</p>
                <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($items as $item): ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="card h-100">
                            <?php if ($item['file_type'] === 'image'): ?>
                                <img src="/<?= e($item['file_path']) ?>" class="card-img-top" style="height:120px;object-fit:cover;" alt="<?= e($item['title']) ?>">
                            <?php else: ?>
                                <div class="bg-dark text-white d-flex align-items-center justify-content-center" style="height:120px;"><i class="bi bi-play-circle-fill fs-1"></i></div>
                            <?php endif; ?>
                            <div class="card-body p-2">
                                <small class="fw-semibold d-block text-truncate"><?= e($item['title']) ?></small>
                                <?php $sc = match($item['status']) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }; ?>
                                <span class="badge bg-<?= $sc ?> mt-1"><?= e(ucfirst($item['status'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($p['total_pages'] > 1): ?>
                <div class="card-footer bg-white"><?= paginationHtml($p, '/teacher/upload-gallery.php') ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleUploadType() {
    const cat = document.getElementById('categorySelect').value;
    document.getElementById('imageUpload').classList.toggle('d-none', cat === 'videos');
    document.getElementById('videoUpload').classList.toggle('d-none', cat !== 'videos');
}
</script>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
