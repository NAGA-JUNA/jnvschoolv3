<?php
// ============================================
// DocumentController — Upload/List/Delete Documents
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../middleware/auth.php';

class DocumentController
{
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // GET /admin/students/{id}/documents
    public function studentDocuments(int $studentId): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT sd.*, u.name as uploaded_by_name
             FROM student_documents sd
             LEFT JOIN users u ON sd.uploaded_by = u.id
             WHERE sd.student_id = :sid ORDER BY sd.created_at DESC"
        );
        $stmt->execute([':sid' => $studentId]);
        jsonSuccess($stmt->fetchAll());
    }

    // POST /admin/students/{id}/documents
    public function uploadStudentDocument(int $studentId): void {
        requireRole(ADMIN_ROLES);

        if (!isset($_FILES['file'])) jsonError('File is required', 400);

        $name = $_POST['name'] ?? $_FILES['file']['name'];
        $type = $_POST['type'] ?? 'other';

        $fileUrl = uploadFile($_FILES['file'], UPLOAD_STUDENTS, [
            'max_size' => MAX_UPLOAD_SIZE,
            'types' => array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES),
        ]);

        $stmt = $this->db->prepare(
            "INSERT INTO student_documents (student_id, name, type, file_url, file_size, uploaded_by)
             VALUES (:sid, :name, :type, :url, :size, :uid)"
        );
        $stmt->execute([
            ':sid'  => $studentId,
            ':name' => $name,
            ':type' => $type,
            ':url'  => $fileUrl,
            ':size' => $_FILES['file']['size'],
            ':uid'  => currentUserId(),
        ]);

        auditLog('upload_document', 'student_documents', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId(), 'file_url' => $fileUrl], 'Document uploaded', 201);
    }

    // DELETE /admin/students/{id}/documents/{docId} — handled via generic delete
    public function deleteStudentDocument(int $studentId, int $docId): void {
        requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN]);

        $stmt = $this->db->prepare("SELECT file_url FROM student_documents WHERE id = :id AND student_id = :sid");
        $stmt->execute([':id' => $docId, ':sid' => $studentId]);
        $doc = $stmt->fetch();
        if (!$doc) jsonError('Document not found', 404);

        deleteUploadedFile($doc['file_url']);

        $this->db->prepare("DELETE FROM student_documents WHERE id = :id")->execute([':id' => $docId]);
        auditLog('delete_document', 'student_documents', $docId);
        jsonSuccess(null, 'Document deleted');
    }

    // GET /admin/teachers/{id}/documents
    public function teacherDocuments(int $teacherId): void {
        requireRole(ADMIN_ROLES);
        $stmt = $this->db->prepare(
            "SELECT td.*, u.name as uploaded_by_name
             FROM teacher_documents td
             LEFT JOIN users u ON td.uploaded_by = u.id
             WHERE td.teacher_id = :tid ORDER BY td.created_at DESC"
        );
        $stmt->execute([':tid' => $teacherId]);
        jsonSuccess($stmt->fetchAll());
    }

    // POST /admin/teachers/{id}/documents
    public function uploadTeacherDocument(int $teacherId): void {
        requireRole(ADMIN_ROLES);

        if (!isset($_FILES['file'])) jsonError('File is required', 400);

        $name = $_POST['name'] ?? $_FILES['file']['name'];
        $type = $_POST['type'] ?? 'other';

        $fileUrl = uploadFile($_FILES['file'], UPLOAD_TEACHERS, [
            'max_size' => MAX_UPLOAD_SIZE,
            'types' => array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES),
        ]);

        $stmt = $this->db->prepare(
            "INSERT INTO teacher_documents (teacher_id, name, type, file_url, file_size, uploaded_by)
             VALUES (:tid, :name, :type, :url, :size, :uid)"
        );
        $stmt->execute([
            ':tid'  => $teacherId,
            ':name' => $name,
            ':type' => $type,
            ':url'  => $fileUrl,
            ':size' => $_FILES['file']['size'],
            ':uid'  => currentUserId(),
        ]);

        auditLog('upload_document', 'teacher_documents', (int) $this->db->lastInsertId());
        jsonSuccess(['id' => $this->db->lastInsertId(), 'file_url' => $fileUrl], 'Document uploaded', 201);
    }
}
