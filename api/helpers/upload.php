<?php
// ============================================
// JSchoolAdmin — File Upload Helper
// ============================================

/**
 * Upload a file with validation
 *
 * @param array  $file     $_FILES['field']
 * @param string $subDir   Subdirectory inside uploads/ (e.g. 'gallery', 'students')
 * @param array  $options  [max_size, types, min_width, recommended]
 * @return string           Relative URL path to uploaded file
 */
function uploadFile(array $file, string $subDir, array $options = []): string {
    $maxSize      = $options['max_size'] ?? MAX_UPLOAD_SIZE;
    $allowedTypes = $options['types'] ?? ALLOWED_IMAGE_TYPES;

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server limit',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL    => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temp directory missing',
            UPLOAD_ERR_CANT_WRITE => 'Server write error',
        ];
        jsonError($errorMessages[$file['error']] ?? 'Upload error', 400);
    }

    // Validate size
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        jsonError("File too large. Maximum size: {$maxMB}MB", 400);
    }

    // Validate type
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        jsonError('File type not allowed. Allowed: ' . implode(', ', $allowedTypes), 400);
    }

    // Validate it's a real file (not a path traversal attack)
    if (!is_uploaded_file($file['tmp_name'])) {
        jsonError('Invalid upload', 400);
    }

    // Validate MIME type for images
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        ];
        if (!in_array($mime, $allowedMimes)) {
            jsonError('Invalid image file', 400);
        }
    }

    // Create directory if not exists
    $targetDir = UPLOAD_DIR . $subDir . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;

    // Sanitize — remove any potentially dangerous characters
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);

    $targetPath = $targetDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        jsonError('Failed to save uploaded file', 500);
    }

    // Return the relative URL path
    return "/uploads/$subDir/$filename";
}

/**
 * Delete an uploaded file
 */
function deleteUploadedFile(string $relativePath): bool {
    if (empty($relativePath) || !str_starts_with($relativePath, '/uploads/')) {
        return false;
    }
    $filePath = UPLOAD_DIR . ltrim($relativePath, '/uploads/');
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}
