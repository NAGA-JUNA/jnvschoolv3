<?php
// ============================================
// JSchoolAdmin — Application Constants
// ============================================

// User Roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_OFFICE', 'office');

// Admin roles shorthand
define('ADMIN_ROLES', [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_OFFICE]);
define('ALL_STAFF_ROLES', [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_OFFICE, ROLE_TEACHER]);

// Student statuses
define('STUDENT_ACTIVE', 'active');
define('STUDENT_INACTIVE', 'inactive');
define('STUDENT_ALUMNI', 'alumni');
define('STUDENT_TRANSFERRED', 'transferred');

// Teacher statuses
define('TEACHER_ACTIVE', 'active');
define('TEACHER_INACTIVE', 'inactive');

// Notification statuses
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');

// Admission statuses
define('ADMISSION_PENDING', 'pending');
define('ADMISSION_APPROVED', 'approved');
define('ADMISSION_REJECTED', 'rejected');
define('ADMISSION_WAITLISTED', 'waitlisted');

// Pagination defaults
define('DEFAULT_PAGE', 1);
define('DEFAULT_PER_PAGE', 20);
define('MAX_PER_PAGE', 100);

// Upload directories
define('UPLOAD_GALLERY', 'gallery/');
define('UPLOAD_NOTIFICATIONS', 'notifications/');
define('UPLOAD_STUDENTS', 'students/');
define('UPLOAD_TEACHERS', 'teachers/');
define('UPLOAD_PROFILES', 'profiles/');
define('UPLOAD_SLIDER', 'slider/');

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);
define('ALLOWED_EXCEL_TYPES', ['xlsx', 'xls', 'csv']);
define('ALLOWED_VIDEO_TYPES', ['mp4']);
