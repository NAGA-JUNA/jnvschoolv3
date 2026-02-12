<?php
$pageTitle = 'Navigation Settings';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();
include __DIR__ . '/../includes/header.php';

// Load items
$items = [];
try {
    $items = $db->query("SELECT * FROM nav_menu_items ORDER BY sort_order ASC, id ASC")->fetchAll();
} catch (Exception $e) {}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-menu-button-wide me-2"></i>Navigation Menu</h4>
        <p class="text-muted mb-0">Manage the public website's top navigation bar items</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#navItemModal" onclick="resetNavForm()">
        <i class="bi bi-plus-lg me-1"></i> Add Item
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="navTable">
                <thead>
                    <tr>
                        <th style="width:40px;"><i class="bi bi-grip-vertical text-muted"></i></th>
                        <th>Label</th>
                        <th>URL</th>
                        <th>Icon</th>
                        <th>Type</th>
                        <th>Visible</th>
                        <th>CTA</th>
                        <th style="width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="navSortable">
                    <?php foreach ($items as $item): ?>
                    <tr data-id="<?= $item['id'] ?>">
                        <td><i class="bi bi-grip-vertical text-muted" style="cursor:grab;"></i></td>
                        <td class="fw-semibold"><?= e($item['label']) ?></td>
                        <td><code class="small"><?= e($item['url']) ?></code></td>
                        <td><?php if($item['icon']): ?><i class="bi <?= e($item['icon']) ?>"></i> <small class="text-muted"><?= e($item['icon']) ?></small><?php else: ?>—<?php endif; ?></td>
                        <td><span class="badge bg-<?= $item['link_type']==='external' ? 'warning' : 'info' ?>-subtle text-<?= $item['link_type']==='external' ? 'warning' : 'info' ?>"><?= e($item['link_type']) ?></span></td>
                        <td><?= $item['is_visible'] ? '<i class="bi bi-eye-fill text-success"></i>' : '<i class="bi bi-eye-slash text-muted"></i>' ?></td>
                        <td><?= $item['is_cta'] ? '<span class="badge bg-danger">CTA</span>' : '—' ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick='editNavItem(<?= json_encode($item) ?>)'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNavItem(<?= $item['id'] ?>, '<?= e($item['label']) ?>')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No menu items found. Click "Add Item" to get started.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Tips</h6>
        <ul class="small text-muted mb-0">
            <li>Drag rows to reorder menu items</li>
            <li>Mark one item as <strong>CTA</strong> — it will appear as a gradient button on the right side of the navbar</li>
            <li>Use Bootstrap Icons names for icons (e.g., <code>bi-house-fill</code>, <code>bi-images</code>)</li>
            <li>External links open in a new tab</li>
        </ul>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="navItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="navModalTitle">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="navItemId" value="0">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="navLabel" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="navUrl" placeholder="/public/about.php" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Icon (Bootstrap Icons)</label>
                    <input type="text" class="form-control" id="navIcon" placeholder="bi-house-fill">
                    <small class="text-muted">Browse icons at <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></small>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link Type</label>
                        <select class="form-select" id="navLinkType">
                            <option value="internal">Internal</option>
                            <option value="external">External</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Visible</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="navVisible" checked>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">CTA</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="navIsCta">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="saveNavItem()"><i class="bi bi-check-lg me-1"></i>Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const sortable = new Sortable(document.getElementById('navSortable'), {
    handle: '.bi-grip-vertical',
    animation: 200,
    onEnd: function() {
        const order = [...document.querySelectorAll('#navSortable tr[data-id]')].map(r => r.dataset.id);
        const fd = new FormData();
        fd.append('action', 'reorder');
        fd.append('order', JSON.stringify(order));
        fetch('/admin/ajax/nav-actions.php', { method:'POST', body: fd });
    }
});

function resetNavForm() {
    document.getElementById('navModalTitle').textContent = 'Add Menu Item';
    document.getElementById('navItemId').value = 0;
    document.getElementById('navLabel').value = '';
    document.getElementById('navUrl').value = '';
    document.getElementById('navIcon').value = '';
    document.getElementById('navLinkType').value = 'internal';
    document.getElementById('navVisible').checked = true;
    document.getElementById('navIsCta').checked = false;
}

function editNavItem(item) {
    document.getElementById('navModalTitle').textContent = 'Edit Menu Item';
    document.getElementById('navItemId').value = item.id;
    document.getElementById('navLabel').value = item.label;
    document.getElementById('navUrl').value = item.url;
    document.getElementById('navIcon').value = item.icon || '';
    document.getElementById('navLinkType').value = item.link_type;
    document.getElementById('navVisible').checked = !!parseInt(item.is_visible);
    document.getElementById('navIsCta').checked = !!parseInt(item.is_cta);
    new bootstrap.Modal(document.getElementById('navItemModal')).show();
}

function saveNavItem() {
    const fd = new FormData();
    fd.append('action', 'save');
    fd.append('id', document.getElementById('navItemId').value);
    fd.append('label', document.getElementById('navLabel').value);
    fd.append('url', document.getElementById('navUrl').value);
    fd.append('icon', document.getElementById('navIcon').value);
    fd.append('link_type', document.getElementById('navLinkType').value);
    if (document.getElementById('navVisible').checked) fd.append('is_visible', '1');
    if (document.getElementById('navIsCta').checked) fd.append('is_cta', '1');

    fetch('/admin/ajax/nav-actions.php', { method:'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) location.reload();
            else alert(d.error || 'Error saving');
        });
}

function deleteNavItem(id, label) {
    if (!confirm('Delete "' + label + '"?')) return;
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id', id);
    fetch('/admin/ajax/nav-actions.php', { method:'POST', body: fd })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
