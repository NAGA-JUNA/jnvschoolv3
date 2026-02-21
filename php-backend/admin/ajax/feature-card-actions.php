<?php
require_once __DIR__ . '/../../includes/auth.php';
$db = getDB();
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'reorder':
            requireAdmin();
            $order = json_decode($_POST['order'] ?? '[]', true);
            if (is_array($order)) {
                $stmt = $db->prepare("UPDATE feature_cards SET sort_order=? WHERE id=?");
                foreach ($order as $i => $id) {
                    $stmt->execute([$i + 1, (int)$id]);
                }
            }
            echo json_encode(['success' => true]);
            break;

        case 'toggle_visibility':
            requireAdmin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $db->prepare("UPDATE feature_cards SET is_visible = NOT is_visible WHERE id=?")->execute([$id]);
                auditLog('feature_card_toggle_vis', 'feature_cards', $id, 'Toggled visibility');
            }
            echo json_encode(['success' => true]);
            break;

        case 'toggle_featured':
            requireAdmin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $db->prepare("UPDATE feature_cards SET is_featured = NOT is_featured WHERE id=?")->execute([$id]);
                auditLog('feature_card_toggle_feat', 'feature_cards', $id, 'Toggled featured');
            }
            echo json_encode(['success' => true]);
            break;

        case 'track_click':
            $slug = $_GET['slug'] ?? $_POST['slug'] ?? '';
            if ($slug) {
                $db->prepare("UPDATE feature_cards SET click_count = click_count + 1 WHERE slug=?")->execute([$slug]);
            }
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
