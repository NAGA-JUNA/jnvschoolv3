<?php
// ============================================
// BrandingController — Settings & Branding Config
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../middleware/auth.php';

class BrandingController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/settings
    public function getSettings(): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->query("SELECT key_name, value FROM settings ORDER BY key_name");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key_name']] = $row['value'];
        }
        jsonSuccess($settings);
    }

    // PUT /admin/settings
    public function updateSettings(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data = getJsonInput();

        $stmt = $this->db->prepare(
            "INSERT INTO settings (key_name, value) VALUES (:key, :val)
             ON DUPLICATE KEY UPDATE value = VALUES(value)"
        );

        foreach ($data as $key => $value) {
            $stmt->execute([':key' => $key, ':val' => $value]);
        }

        auditLog('update', 'settings', null);
        jsonSuccess(null, 'Settings updated');
    }

    // GET /admin/branding
    public function getBranding(): void {
        $stmt = $this->db->query("SELECT * FROM branding LIMIT 1");
        $branding = $stmt->fetch();
        jsonSuccess($branding ?: []);
    }

    // PUT /admin/branding
    public function updateBranding(): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        $data = getJsonInput();

        // Check if record exists
        $exists = (int)$this->db->query("SELECT COUNT(*) FROM branding")->fetchColumn();

        if ($exists) {
            $allowed = ['school_logo','primary_color','secondary_color','font_family','login_bg_image','favicon'];
            $sets = [];
            $params = [];
            foreach ($allowed as $f) {
                if (array_key_exists($f, $data)) {
                    $sets[] = "`$f` = :$f";
                    $params[":$f"] = $data[$f];
                }
            }
            if (empty($sets)) jsonError('No fields to update', 422);

            $this->db->prepare("UPDATE branding SET " . implode(', ', $sets) . " WHERE id = 1")->execute($params);
        } else {
            $this->db->prepare(
                "INSERT INTO branding (school_logo, primary_color, secondary_color, font_family, login_bg_image, favicon)
                 VALUES (:logo, :primary, :secondary, :font, :bg, :fav)"
            )->execute([
                ':logo'      => $data['school_logo'] ?? null,
                ':primary'   => $data['primary_color'] ?? '#1e40af',
                ':secondary' => $data['secondary_color'] ?? '#f59e0b',
                ':font'      => $data['font_family'] ?? 'Inter',
                ':bg'        => $data['login_bg_image'] ?? null,
                ':fav'       => $data['favicon'] ?? null,
            ]);
        }

        auditLog('update', 'branding', 1);
        jsonSuccess(null, 'Branding updated');
    }
}
