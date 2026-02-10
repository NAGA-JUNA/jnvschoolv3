<?php
require_once __DIR__.'/../includes/auth.php';
requireAdmin();
$db = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $linkUrl = trim($_POST['link_url'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $badgeText = trim($_POST['badge_text'] ?? '');
        $ctaText = trim($_POST['cta_text'] ?? '');

        // Handle image upload
        $imagePath = $_POST['existing_image'] ?? '';
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5 * 1024 * 1024;

            if (in_array($file['type'], $allowed) && $file['size'] <= $maxSize) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'slider_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/slider/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    // Delete old image if replacing
                    if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                        @unlink(__DIR__ . '/../' . $imagePath);
                    }
                    $imagePath = 'uploads/slider/' . $filename;
                }
            } else {
                setFlash('error', 'Image must be JPG/PNG/WebP, max 5MB.');
                header('Location: /admin/slider.php');
                exit;
            }
        }

        if ($action === 'add') {
            if (!$imagePath) {
                setFlash('error', 'Image is required for new slides.');
                header('Location: /admin/slider.php');
                exit;
            }
            $stmt = $db->prepare("INSERT INTO home_slider (title, subtitle, image_path, link_url, sort_order, is_active, badge_text, cta_text) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title ?: null, $subtitle ?: null, $imagePath, $linkUrl ?: null, $sortOrder, $isActive, $badgeText ?: null, $ctaText ?: null]);
            auditLog('add_slider', 'home_slider', (int)$db->lastInsertId(), "Title: $title");
            setFlash('success', 'Slide added successfully.');
        } else {
            $stmt = $db->prepare("UPDATE home_slider SET title=?, subtitle=?, image_path=?, link_url=?, sort_order=?, is_active=?, badge_text=?, cta_text=? WHERE id=?");
            $stmt->execute([$title ?: null, $subtitle ?: null, $imagePath, $linkUrl ?: null, $sortOrder, $isActive, $badgeText ?: null, $ctaText ?: null, $id]);
            auditLog('edit_slider', 'home_slider', $id, "Title: $title");
            setFlash('success', 'Slide updated successfully.');
        }
        header('Location: /admin/slider.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $slide = $db->prepare("SELECT image_path FROM home_slider WHERE id=?");
        $slide->execute([$id]);
        $slide = $slide->fetch();
        if ($slide) {
            if ($slide['image_path'] && file_exists(__DIR__ . '/../' . $slide['image_path'])) {
                @unlink(__DIR__ . '/../' . $slide['image_path']);
            }
            $db->prepare("DELETE FROM home_slider WHERE id=?")->execute([$id]);
            auditLog('delete_slider', 'home_slider', $id);
            setFlash('success', 'Slide deleted.');
        }
        header('Location: /admin/slider.php');
        exit;
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("UPDATE home_slider SET is_active = NOT is_active WHERE id=?")->execute([$id]);
        auditLog('toggle_slider', 'home_slider', $id);
        setFlash('success', 'Slide visibility toggled.');
        header('Location: /admin/slider.php');
        exit;
    }

    if ($action === 'reorder') {
        $orders = $_POST['orders'] ?? [];
        $stmt = $db->prepare("UPDATE home_slider SET sort_order=? WHERE id=?");
        foreach ($orders as $id => $order) {
            $stmt->execute([(int)$order, (int)$id]);
        }
        setFlash('success', 'Slide order updated.');
        header('Location: /admin/slider.php');
        exit;
    }
}

// Get editing slide
$editSlide = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM home_slider WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editSlide = $stmt->fetch();
}

// Get all slides
$slides = $db->query("SELECT * FROM home_slider ORDER BY sort_order ASC, id ASC")->fetchAll();

$pageTitle = 'Home Slider';
require_once __DIR__.'/../includes/header.php';
?>

<div class="row g-4">
    <!-- Add/Edit Form -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-<?= $editSlide ? 'pencil-square' : 'plus-circle' ?> me-2 text-primary"></i>
                <?= $editSlide ? 'Edit Slide' : 'Add New Slide' ?>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="<?= $editSlide ? 'edit' : 'add' ?>">
                    <?php if ($editSlide): ?>
                        <input type="hidden" name="id" value="<?= $editSlide['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= e($editSlide['image_path']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slide Image <?= $editSlide ? '' : '<span class="text-danger">*</span>' ?></label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" <?= $editSlide ? '' : 'required' ?>>
                        <div class="form-text">JPG/PNG/WebP, max 5MB. Recommended: 1920×600px</div>
                        <?php if ($editSlide && $editSlide['image_path']): ?>
                            <img src="/<?= e($editSlide['image_path']) ?>" class="mt-2 rounded" style="max-height:100px;max-width:100%;" alt="Current">
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Badge Text</label>
                        <input type="text" name="badge_text" class="form-control" maxlength="50" placeholder="e.g. Admissions Open" value="<?= e($editSlide['badge_text'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Heading</label>
                        <input type="text" name="title" class="form-control" maxlength="200" placeholder="e.g. Welcome to JNV School" value="<?= e($editSlide['title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subtitle</label>
                        <textarea name="subtitle" class="form-control" rows="2" maxlength="500" placeholder="Short description..."><?= e($editSlide['subtitle'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CTA Button Text</label>
                            <input type="text" name="cta_text" class="form-control" maxlength="50" placeholder="e.g. Apply Now" value="<?= e($editSlide['cta_text'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CTA Link URL</label>
                            <input type="text" name="link_url" class="form-control" maxlength="255" placeholder="/public/admission-form.php" value="<?= e($editSlide['link_url'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $editSlide['sort_order'] ?? count($slides) ?>" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" <?= ($editSlide['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Active (visible on homepage)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-<?= $editSlide ? 'check-lg' : 'plus-lg' ?> me-1"></i><?= $editSlide ? 'Update Slide' : 'Add Slide' ?>
                        </button>
                        <?php if ($editSlide): ?>
                            <a href="/admin/slider.php" class="btn btn-outline-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Slides List -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-images me-2"></i>Slides (<?= count($slides) ?>)</span>
                <?php if (count($slides) > 1): ?>
                <form method="POST" class="d-inline" id="reorderForm">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="reorder">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-sort-numeric-up me-1"></i>Save Order</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($slides)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-image display-4 text-muted"></i>
                        <p class="text-muted mt-2">No slides yet. Add your first slide to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead><tr><th style="width:50px">Order</th><th style="width:100px">Preview</th><th>Details</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php foreach ($slides as $s): ?>
                                <tr>
                                    <td>
                                        <input type="number" name="orders[<?= $s['id'] ?>]" value="<?= $s['sort_order'] ?>" class="form-control form-control-sm" style="width:60px" form="reorderForm" min="0">
                                    </td>
                                    <td>
                                        <img src="/<?= e($s['image_path']) ?>" class="rounded" style="width:90px;height:50px;object-fit:cover;" alt="Slide">
                                    </td>
                                    <td>
                                        <strong class="d-block"><?= e($s['title'] ?: '(No title)') ?></strong>
                                        <?php if ($s['badge_text']): ?><span class="badge bg-info me-1"><?= e($s['badge_text']) ?></span><?php endif; ?>
                                        <?php if ($s['cta_text']): ?><span class="badge bg-primary"><?= e($s['cta_text']) ?></span><?php endif; ?>
                                        <?php if ($s['subtitle']): ?><small class="text-muted d-block text-truncate" style="max-width:200px;"><?= e($s['subtitle']) ?></small><?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <button type="submit" class="btn btn-sm <?= $s['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>">
                                                <?= $s['is_active'] ? '<i class="bi bi-eye"></i> Active' : '<i class="bi bi-eye-slash"></i> Hidden' ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/admin/slider.php?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this slide?')">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__.'/../includes/footer.php'; ?>
