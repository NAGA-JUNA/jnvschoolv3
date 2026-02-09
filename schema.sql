-- =============================================
-- JSchoolAdmin — Complete Database Schema
-- Version: 1.2.0
-- Engine: InnoDB | Charset: utf8mb4
-- Timezone: Asia/Kolkata (IST)
--
-- HOW TO USE:
-- 1. Open phpMyAdmin in cPanel
-- 2. Select your database
-- 3. Click the "SQL" tab
-- 4. Paste this entire file and click "Go"
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+05:30";
SET NAMES utf8mb4;

-- =============================================
-- DROP EXISTING TABLES (for clean re-import)
-- =============================================
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `whatsapp_shares`;
DROP TABLE IF EXISTS `teacher_messages`;
DROP TABLE IF EXISTS `student_messages`;
DROP TABLE IF EXISTS `teacher_documents`;
DROP TABLE IF EXISTS `student_documents`;
DROP TABLE IF EXISTS `exam_results`;
DROP TABLE IF EXISTS `student_attendance`;
DROP TABLE IF EXISTS `gallery_items`;
DROP TABLE IF EXISTS `gallery_categories`;
DROP TABLE IF EXISTS `official_emails`;
DROP TABLE IF EXISTS `admissions`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `home_slider`;
DROP TABLE IF EXISTS `branding`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

-- =============================================
-- 1. USERS (Admin / Office / Teacher login)
-- =============================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin','admin','teacher','office') NOT NULL DEFAULT 'teacher',
  `phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 2. TEACHERS (detailed staff profiles)
-- =============================================
CREATE TABLE `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `employee_id` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `qualification` VARCHAR(200) DEFAULT NULL,
  `experience_years` INT DEFAULT 0,
  `joining_date` DATE DEFAULT NULL,
  `subjects` JSON DEFAULT NULL,
  `classes_assigned` JSON DEFAULT NULL,
  `employment_type` ENUM('full-time','part-time') DEFAULT 'full-time',
  `status` ENUM('active','inactive') DEFAULT 'active',
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 3. STUDENTS
-- =============================================
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admission_no` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `roll_no` INT DEFAULT NULL,
  `class` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) DEFAULT NULL,
  `academic_year` VARCHAR(20) DEFAULT '2025-2026',
  `blood_group` VARCHAR(5) DEFAULT NULL,
  `father_name` VARCHAR(100) DEFAULT NULL,
  `mother_name` VARCHAR(100) DEFAULT NULL,
  `parent_phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp_number` VARCHAR(20) DEFAULT NULL,
  `parent_email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `emergency_contact` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('active','inactive','alumni','transferred') DEFAULT 'active',
  `photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 4. STUDENT ATTENDANCE
-- =============================================
CREATE TABLE `student_attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present','absent','late','half-day') NOT NULL DEFAULT 'present',
  `marked_by` INT DEFAULT NULL,
  `remarks` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_student_date` (`student_id`, `date`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 5. EXAM RESULTS
-- =============================================
CREATE TABLE `exam_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `exam_name` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `max_marks` INT NOT NULL DEFAULT 100,
  `marks_obtained` DECIMAL(5,2) NOT NULL,
  `grade` VARCHAR(5) DEFAULT NULL,
  `academic_year` VARCHAR(20) DEFAULT '2025-2026',
  `entered_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`entered_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 6. STUDENT DOCUMENTS
-- =============================================
CREATE TABLE `student_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `type` ENUM('aadhaar','birth_certificate','transfer_certificate','photo','marksheet','other') DEFAULT 'other',
  `file_url` VARCHAR(500) NOT NULL,
  `file_size` INT DEFAULT NULL,
  `uploaded_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 7. TEACHER DOCUMENTS
-- =============================================
CREATE TABLE `teacher_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `type` ENUM('id_proof','certificate','resume','appointment_letter','contract','other') DEFAULT 'other',
  `file_url` VARCHAR(500) NOT NULL,
  `file_size` INT DEFAULT NULL,
  `uploaded_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 8. STUDENT MESSAGES (WhatsApp history)
-- =============================================
CREATE TABLE `student_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `template` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `sent_by` INT DEFAULT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 9. TEACHER MESSAGES (WhatsApp history)
-- =============================================
CREATE TABLE `teacher_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `template` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `sent_by` INT DEFAULT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sent_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 10. NOTIFICATIONS
-- =============================================
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `body` TEXT NOT NULL,
  `urgency` ENUM('normal','important','urgent') DEFAULT 'normal',
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `submitted_by` INT NOT NULL,
  `approved_by` INT DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `is_public` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 11. HOME SLIDER / BANNER (Admin-managed hero carousel)
-- =============================================
CREATE TABLE `home_slider` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `subtitle` VARCHAR(300) DEFAULT NULL,
  `badge_text` VARCHAR(100) DEFAULT NULL,
  `cta_primary_text` VARCHAR(50) DEFAULT 'Apply Now',
  `cta_primary_link` VARCHAR(255) DEFAULT '/admissions',
  `cta_secondary_text` VARCHAR(50) DEFAULT 'Learn More',
  `cta_secondary_link` VARCHAR(255) DEFAULT '/about',
  `image_url` VARCHAR(500) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 12. GALLERY CATEGORIES
-- =============================================
CREATE TABLE `gallery_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) UNIQUE NOT NULL,
  `type` ENUM('images','videos') DEFAULT 'images',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `item_count` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 13. GALLERY ITEMS
-- =============================================
CREATE TABLE `gallery_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `title` VARCHAR(200) DEFAULT NULL,
  `file_url` VARCHAR(500) NOT NULL,
  `thumbnail_url` VARCHAR(500) DEFAULT NULL,
  `type` ENUM('image','video','youtube') DEFAULT 'image',
  `youtube_id` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `uploaded_by` INT NOT NULL,
  `approved_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `gallery_categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 14. EVENTS
-- =============================================
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME DEFAULT NULL,
  `location` VARCHAR(200) DEFAULT NULL,
  `type` ENUM('academic','cultural','sports','meeting','holiday','other') DEFAULT 'other',
  `is_public` TINYINT(1) DEFAULT 1,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 15. ADMISSIONS (Online Applications)
-- =============================================
CREATE TABLE `admissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_name` VARCHAR(100) NOT NULL,
  `class_applied` VARCHAR(20) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `gender` ENUM('male','female','other') NOT NULL,
  `parent_name` VARCHAR(100) NOT NULL,
  `parent_phone` VARCHAR(20) NOT NULL,
  `parent_email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT NOT NULL,
  `previous_school` VARCHAR(200) DEFAULT NULL,
  `documents` JSON DEFAULT NULL,
  `status` ENUM('pending','approved','rejected','waitlisted') DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `reviewed_by` INT DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 16. OFFICIAL EMAILS
-- =============================================
CREATE TABLE `official_emails` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `email_address` VARCHAR(150) UNIQUE NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `webmail_url` VARCHAR(300) DEFAULT NULL,
  `status` ENUM('active','suspended','deleted') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 17. WHATSAPP SHARE LOG
-- =============================================
CREATE TABLE `whatsapp_shares` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_type` ENUM('notification','event','admission','student','teacher') NOT NULL,
  `item_id` INT NOT NULL,
  `shared_by` INT NOT NULL,
  `shared_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`shared_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 18. AUDIT LOGS
-- =============================================
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT DEFAULT NULL,
  `details` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 19. SETTINGS (Key-Value Store)
-- =============================================
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 20. BRANDING
-- =============================================
CREATE TABLE `branding` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `school_logo` VARCHAR(500) DEFAULT NULL,
  `primary_color` VARCHAR(20) DEFAULT '#1e40af',
  `secondary_color` VARCHAR(20) DEFAULT '#f59e0b',
  `font_family` VARCHAR(100) DEFAULT 'Inter',
  `login_bg_image` VARCHAR(500) DEFAULT NULL,
  `favicon` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================
ALTER TABLE `students` ADD INDEX `idx_class_section` (`class`, `section`);
ALTER TABLE `students` ADD INDEX `idx_status` (`status`);
ALTER TABLE `students` ADD INDEX `idx_academic_year` (`academic_year`);
ALTER TABLE `students` ADD INDEX `idx_admission_no` (`admission_no`);
ALTER TABLE `teachers` ADD INDEX `idx_status` (`status`);
ALTER TABLE `teachers` ADD INDEX `idx_employee_id` (`employee_id`);
ALTER TABLE `student_attendance` ADD INDEX `idx_date` (`date`);
ALTER TABLE `exam_results` ADD INDEX `idx_student_exam` (`student_id`, `exam_name`);
ALTER TABLE `exam_results` ADD INDEX `idx_academic_year` (`academic_year`);
ALTER TABLE `notifications` ADD INDEX `idx_status` (`status`);
ALTER TABLE `notifications` ADD INDEX `idx_submitted_by` (`submitted_by`);
ALTER TABLE `gallery_items` ADD INDEX `idx_category_status` (`category_id`, `status`);
ALTER TABLE `events` ADD INDEX `idx_event_date` (`event_date`);
ALTER TABLE `admissions` ADD INDEX `idx_status` (`status`);
ALTER TABLE `audit_logs` ADD INDEX `idx_user_action` (`user_id`, `action`);
ALTER TABLE `audit_logs` ADD INDEX `idx_created_at` (`created_at`);
ALTER TABLE `home_slider` ADD INDEX `idx_active_order` (`is_active`, `sort_order`);


-- =============================================
-- SAMPLE DATA
-- =============================================

-- -------------------------------------------
-- Users (passwords hashed with bcrypt)
-- admin123 / office123 / teacher123
-- -------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `whatsapp`) VALUES
('Super Admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', '+91-9876543210', '+91-9876543210'),
('Office Manager', 'office@school.com', '$2y$10$Dqz2F2gFUbkP5UxMN5EYEeC3eN3F6o7kqHOOqX6KJ9yLVw1bxU6Oe', 'office', '+91-9876543211', '+91-9876543211'),
('Priya Singh', 'priya.singh@school.com', '$2y$10$hJtF2nE1K2fW3jNBKx2YIOTnK3mHGePJQv1qb3YB5WnLgVRfXkL0y', 'teacher', '+91-9876543212', '+91-9876543212'),
('Rajesh Kumar', 'rajesh.kumar@school.com', '$2y$10$hJtF2nE1K2fW3jNBKx2YIOTnK3mHGePJQv1qb3YB5WnLgVRfXkL0y', 'teacher', '+91-9876543213', '+91-9876543213'),
('Anita Sharma', 'anita.sharma@school.com', '$2y$10$hJtF2nE1K2fW3jNBKx2YIOTnK3mHGePJQv1qb3YB5WnLgVRfXkL0y', 'teacher', '+91-9876543214', '+91-9876543214');

-- -------------------------------------------
-- Teachers (10 records)
-- -------------------------------------------
INSERT INTO `teachers` (`user_id`, `employee_id`, `name`, `gender`, `date_of_birth`, `phone`, `whatsapp`, `email`, `address`, `qualification`, `experience_years`, `joining_date`, `subjects`, `classes_assigned`, `employment_type`, `status`) VALUES
(3, 'EMP001', 'Priya Singh', 'female', '1988-05-15', '+91-9876543212', '+91-9876543212', 'priya.singh@school.com', '12, MG Road, Lucknow, UP', 'M.Sc. Mathematics, B.Ed.', 8, '2017-06-01', '["Mathematics"]', '["10-A","10-B","9-A"]', 'full-time', 'active'),
(4, 'EMP002', 'Rajesh Kumar', 'male', '1985-11-22', '+91-9876543213', '+91-9876543213', 'rajesh.kumar@school.com', '45, Civil Lines, Kanpur, UP', 'M.A. English, B.Ed.', 12, '2013-04-15', '["English","Literature"]', '["10-A","9-B","8-A"]', 'full-time', 'active'),
(5, 'EMP003', 'Anita Sharma', 'female', '1990-03-08', '+91-9876543214', '+91-9876543214', 'anita.sharma@school.com', '78, Gomti Nagar, Lucknow, UP', 'M.Sc. Physics, B.Ed.', 6, '2019-07-01', '["Physics","Science"]', '["10-A","10-B"]', 'full-time', 'active'),
(NULL, 'EMP004', 'Vikram Patel', 'male', '1982-09-14', '+91-9876543215', '+91-9876543215', 'vikram.patel@school.com', '34, Hazratganj, Lucknow, UP', 'M.Sc. Chemistry, Ph.D.', 15, '2010-03-01', '["Chemistry","Science"]', '["10-A","9-A","9-B"]', 'full-time', 'active'),
(NULL, 'EMP005', 'Sunita Devi', 'female', '1991-07-25', '+91-9876543216', '+91-9876543216', 'sunita.devi@school.com', '56, Aliganj, Lucknow, UP', 'M.A. Hindi, B.Ed.', 5, '2020-04-01', '["Hindi"]', '["8-A","8-B","7-A"]', 'full-time', 'active'),
(NULL, 'EMP006', 'Amit Verma', 'male', '1987-01-30', '+91-9876543217', '+91-9876543217', 'amit.verma@school.com', '89, Indira Nagar, Lucknow, UP', 'M.Sc. Computer Science', 10, '2015-06-15', '["Computer Science","IT"]', '["10-A","10-B","9-A","9-B"]', 'full-time', 'active'),
(NULL, 'EMP007', 'Meera Joshi', 'female', '1993-12-18', '+91-9876543218', '+91-9876543218', 'meera.joshi@school.com', '23, Mahanagar, Lucknow, UP', 'M.A. Social Studies, B.Ed.', 4, '2021-07-01', '["Social Studies","History"]', '["8-A","8-B","7-B"]', 'full-time', 'active'),
(NULL, 'EMP008', 'Ravi Tiwari', 'male', '1984-06-05', '+91-9876543219', '+91-9876543219', 'ravi.tiwari@school.com', '67, Rajajipuram, Lucknow, UP', 'M.P.Ed., B.P.Ed.', 14, '2011-08-01', '["Physical Education"]', '["10-A","10-B","9-A","9-B","8-A","8-B"]', 'full-time', 'active'),
(NULL, 'EMP009', 'Kavita Mishra', 'female', '1995-04-12', '+91-9876543220', '+91-9876543220', 'kavita.mishra@school.com', '90, Vikas Nagar, Lucknow, UP', 'M.Sc. Biology, B.Ed.', 3, '2022-04-01', '["Biology","Science"]', '["9-A","9-B"]', 'part-time', 'active'),
(NULL, 'EMP010', 'Deepak Gupta', 'male', '1980-08-20', '+91-9876543221', '+91-9876543221', 'deepak.gupta@school.com', '11, Chowk, Lucknow, UP', 'M.A. Sanskrit, Acharya', 18, '2007-06-01', '["Sanskrit"]', '["7-A","7-B","8-A"]', 'full-time', 'inactive');

-- -------------------------------------------
-- Students (8 records)
-- -------------------------------------------
INSERT INTO `students` (`admission_no`, `name`, `gender`, `date_of_birth`, `roll_no`, `class`, `section`, `academic_year`, `blood_group`, `father_name`, `mother_name`, `parent_phone`, `whatsapp_number`, `parent_email`, `address`, `emergency_contact`, `status`) VALUES
('ADM2025001', 'Aarav Sharma', 'male', '2012-03-15', 1, '10', 'A', '2025-2026', 'B+', 'Ramesh Sharma', 'Sunita Sharma', '+91-9812345001', '+91-9812345001', 'ramesh.sharma@email.com', '12, MG Road, Lucknow, UP 226001', '+91-9812345099', 'active'),
('ADM2025002', 'Priya Patel', 'female', '2012-07-22', 2, '10', 'A', '2025-2026', 'A+', 'Suresh Patel', 'Meena Patel', '+91-9812345002', '+91-9812345002', 'suresh.patel@email.com', '45, Civil Lines, Kanpur, UP 208001', '+91-9812345098', 'active'),
('ADM2025003', 'Rohit Kumar', 'male', '2012-11-08', 3, '10', 'A', '2025-2026', 'O+', 'Vinod Kumar', 'Rekha Kumar', '+91-9812345003', '+91-9812345003', NULL, '78, Gomti Nagar, Lucknow, UP 226010', '+91-9812345097', 'active'),
('ADM2025004', 'Sneha Gupta', 'female', '2012-01-30', 4, '10', 'B', '2025-2026', 'AB+', 'Manoj Gupta', 'Neha Gupta', '+91-9812345004', '+91-9812345004', 'manoj.gupta@email.com', '34, Hazratganj, Lucknow, UP 226001', '+91-9812345096', 'active'),
('ADM2025005', 'Arjun Mishra', 'male', '2013-05-14', 1, '9', 'A', '2025-2026', 'B-', 'Alok Mishra', 'Kavita Mishra', '+91-9812345005', '+91-9812345005', NULL, '56, Aliganj, Lucknow, UP 226024', '+91-9812345095', 'active'),
('ADM2025006', 'Ananya Singh', 'female', '2013-09-03', 2, '9', 'A', '2025-2026', 'A-', 'Deepak Singh', 'Pooja Singh', '+91-9812345006', '+91-9812345006', 'deepak.singh@email.com', '89, Indira Nagar, Lucknow, UP 226016', '+91-9812345094', 'active'),
('ADM2024050', 'Vikas Yadav', 'male', '2011-02-28', 5, '10', 'B', '2025-2026', 'O-', 'Sunil Yadav', 'Rani Yadav', '+91-9812345007', '+91-9812345007', NULL, '23, Mahanagar, Lucknow, UP 226006', '+91-9812345093', 'inactive'),
('ADM2023025', 'Ritu Verma', 'female', '2010-12-10', NULL, '10', 'A', '2024-2025', 'A+', 'Prakash Verma', 'Suman Verma', '+91-9812345008', '+91-9812345008', 'prakash.verma@email.com', '67, Rajajipuram, Lucknow, UP 226017', '+91-9812345092', 'alumni');

-- -------------------------------------------
-- Student Attendance (sample records)
-- -------------------------------------------
INSERT INTO `student_attendance` (`student_id`, `date`, `status`, `marked_by`, `remarks`) VALUES
(1, '2026-02-03', 'present', 3, NULL),
(1, '2026-02-04', 'present', 3, NULL),
(1, '2026-02-05', 'absent', 3, 'Medical leave'),
(1, '2026-02-06', 'present', 3, NULL),
(1, '2026-02-07', 'present', 3, NULL),
(2, '2026-02-03', 'present', 3, NULL),
(2, '2026-02-04', 'late', 3, 'Arrived 15 mins late'),
(2, '2026-02-05', 'present', 3, NULL),
(2, '2026-02-06', 'present', 3, NULL),
(2, '2026-02-07', 'absent', 3, 'Family function'),
(3, '2026-02-03', 'present', 3, NULL),
(3, '2026-02-04', 'present', 3, NULL),
(3, '2026-02-05', 'present', 3, NULL),
(3, '2026-02-06', 'half-day', 3, 'Left early — fever'),
(3, '2026-02-07', 'absent', 3, 'Sick leave'),
(4, '2026-02-03', 'present', 3, NULL),
(4, '2026-02-04', 'present', 3, NULL),
(4, '2026-02-05', 'present', 3, NULL),
(4, '2026-02-06', 'present', 3, NULL),
(4, '2026-02-07', 'present', 3, NULL),
(5, '2026-02-03', 'present', 4, NULL),
(5, '2026-02-04', 'absent', 4, 'Not informed'),
(5, '2026-02-05', 'present', 4, NULL),
(6, '2026-02-03', 'present', 4, NULL),
(6, '2026-02-04', 'present', 4, NULL),
(6, '2026-02-05', 'late', 4, 'Bus delay');

-- -------------------------------------------
-- Exam Results (sample records)
-- -------------------------------------------
INSERT INTO `exam_results` (`student_id`, `exam_name`, `subject`, `max_marks`, `marks_obtained`, `grade`, `academic_year`, `entered_by`) VALUES
(1, 'Mid-Term 2025', 'Mathematics', 100, 92.00, 'A+', '2025-2026', 3),
(1, 'Mid-Term 2025', 'English', 100, 85.00, 'A', '2025-2026', 4),
(1, 'Mid-Term 2025', 'Physics', 100, 88.00, 'A', '2025-2026', 5),
(1, 'Mid-Term 2025', 'Chemistry', 100, 78.00, 'B+', '2025-2026', 1),
(1, 'Mid-Term 2025', 'Hindi', 100, 90.00, 'A+', '2025-2026', 1),
(2, 'Mid-Term 2025', 'Mathematics', 100, 95.00, 'A+', '2025-2026', 3),
(2, 'Mid-Term 2025', 'English', 100, 91.00, 'A+', '2025-2026', 4),
(2, 'Mid-Term 2025', 'Physics', 100, 82.00, 'A', '2025-2026', 5),
(2, 'Mid-Term 2025', 'Chemistry', 100, 88.00, 'A', '2025-2026', 1),
(3, 'Mid-Term 2025', 'Mathematics', 100, 72.00, 'B+', '2025-2026', 3),
(3, 'Mid-Term 2025', 'English', 100, 68.00, 'B', '2025-2026', 4),
(3, 'Mid-Term 2025', 'Physics', 100, 75.00, 'B+', '2025-2026', 5),
(4, 'Mid-Term 2025', 'Mathematics', 100, 98.00, 'A+', '2025-2026', 3),
(4, 'Mid-Term 2025', 'English', 100, 94.00, 'A+', '2025-2026', 4),
(5, 'Mid-Term 2025', 'Mathematics', 100, 65.00, 'B', '2025-2026', 3),
(5, 'Mid-Term 2025', 'English', 100, 70.00, 'B+', '2025-2026', 4);

-- -------------------------------------------
-- Student Documents (sample)
-- -------------------------------------------
INSERT INTO `student_documents` (`student_id`, `name`, `type`, `file_url`, `file_size`, `uploaded_by`) VALUES
(1, 'Aadhaar Card', 'aadhaar', '/uploads/students/1/aadhaar.pdf', 245000, 1),
(1, 'Birth Certificate', 'birth_certificate', '/uploads/students/1/birth_cert.pdf', 180000, 1),
(2, 'Aadhaar Card', 'aadhaar', '/uploads/students/2/aadhaar.pdf', 250000, 1),
(2, 'Transfer Certificate', 'transfer_certificate', '/uploads/students/2/tc.pdf', 320000, 1),
(3, 'Aadhaar Card', 'aadhaar', '/uploads/students/3/aadhaar.pdf', 240000, 1);

-- -------------------------------------------
-- Teacher Documents (sample)
-- -------------------------------------------
INSERT INTO `teacher_documents` (`teacher_id`, `name`, `type`, `file_url`, `file_size`, `uploaded_by`) VALUES
(1, 'PAN Card', 'id_proof', '/uploads/teachers/1/pan.pdf', 200000, 1),
(1, 'B.Ed. Certificate', 'certificate', '/uploads/teachers/1/bed_cert.pdf', 350000, 1),
(1, 'Resume', 'resume', '/uploads/teachers/1/resume.pdf', 420000, 1),
(2, 'Aadhaar Card', 'id_proof', '/uploads/teachers/2/aadhaar.pdf', 245000, 1),
(2, 'Appointment Letter', 'appointment_letter', '/uploads/teachers/2/appointment.pdf', 180000, 1),
(3, 'PAN Card', 'id_proof', '/uploads/teachers/3/pan.pdf', 210000, 1);

-- -------------------------------------------
-- Student Messages (WhatsApp history)
-- -------------------------------------------
INSERT INTO `student_messages` (`student_id`, `template`, `message`, `sent_by`) VALUES
(1, 'Absentee Alert', 'Dear Parent, your ward Aarav Sharma of Class 10-A was absent on 05-Feb-2026. Please inform the school for any leave. — JSchoolAdmin', 3),
(3, 'Exam Info', 'Dear Parent, Mid-Term exams for Class 10-A start from 15-Mar-2026. Please ensure Rohit Kumar prepares well. — JSchoolAdmin', 1),
(5, 'Absentee Alert', 'Dear Parent, your ward Arjun Mishra of Class 9-A was absent on 04-Feb-2026. Please inform the school. — JSchoolAdmin', 4);

-- -------------------------------------------
-- Teacher Messages (WhatsApp history)
-- -------------------------------------------
INSERT INTO `teacher_messages` (`teacher_id`, `template`, `message`, `sent_by`) VALUES
(1, 'Meeting Notice', 'Dear Priya Singh, a staff meeting is scheduled for 10-Feb-2026 at 3:00 PM in the conference hall. Attendance is mandatory. — JSchoolAdmin', 1),
(2, 'Timetable Update', 'Dear Rajesh Kumar, the timetable for Class 10-A has been updated. Please check the admin portal. — JSchoolAdmin', 1);

-- -------------------------------------------
-- Notifications (sample)
-- -------------------------------------------
INSERT INTO `notifications` (`title`, `body`, `urgency`, `status`, `submitted_by`, `approved_by`, `approved_at`, `is_public`) VALUES
('Annual Day Celebration', 'Annual Day will be celebrated on 15th March 2026. All parents are invited.', 'important', 'approved', 1, 1, '2026-01-20 10:30:00', 1),
('Winter Vacation Notice', 'School will remain closed from 25th Dec to 5th Jan for winter break.', 'normal', 'approved', 3, 1, '2025-12-15 09:00:00', 1),
('PTM Schedule', 'Parent-Teacher meeting is scheduled for 20th Feb 2026 from 10 AM to 1 PM.', 'important', 'pending', 4, NULL, NULL, 0);

-- -------------------------------------------
-- Home Slider (sample slides)
-- -------------------------------------------
INSERT INTO `home_slider` (`title`, `subtitle`, `badge_text`, `cta_primary_text`, `cta_primary_link`, `cta_secondary_text`, `cta_secondary_link`, `image_url`, `is_active`, `sort_order`, `created_by`) VALUES
('Welcome to JNV Model School', 'Nurturing young minds with quality education, strong values, and a commitment to excellence since 2005.', 'CBSE Affiliated • Est. 2005', 'Apply Now', '/admissions', 'Learn More', '/about', 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920&q=80', 1, 1, 1),
('Building Tomorrow\'s Leaders', 'Empowering students with modern education, critical thinking skills, and holistic development.', 'Admissions Open 2025-26', 'Apply Now', '/admissions', 'Our Faculty', '/faculty', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920&q=80', 1, 2, 1),
('Excellence in Every Field', 'From academics to sports, arts to technology — our students shine in every arena.', 'State-Level Achievers', 'View Gallery', '/gallery', 'Academics', '/academics', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1920&q=80', 1, 3, 1);

-- -------------------------------------------
-- Gallery Categories
-- -------------------------------------------
INSERT INTO `gallery_categories` (`name`, `slug`, `type`, `item_count`) VALUES
('Annual Day 2025', 'annual-day-2025', 'images', 0),
('Sports Day', 'sports-day', 'images', 0),
('Classroom Activities', 'classroom-activities', 'images', 0),
('School Tour', 'school-tour', 'videos', 0);

-- -------------------------------------------
-- Events (sample)
-- -------------------------------------------
INSERT INTO `events` (`title`, `description`, `event_date`, `event_time`, `location`, `type`, `is_public`, `created_by`) VALUES
('Annual Day Celebration', 'Annual cultural program featuring student performances, awards, and chief guest address.', '2026-03-15', '10:00:00', 'School Auditorium', 'cultural', 1, 1),
('Mid-Term Examinations', 'Mid-term exams for classes 6 to 10.', '2026-03-20', '09:00:00', 'Exam Hall', 'academic', 1, 1),
('Parent-Teacher Meeting', 'Quarterly PTM for progress discussion.', '2026-02-20', '10:00:00', 'Respective Classrooms', 'meeting', 0, 1),
('Republic Day', 'Flag hoisting ceremony and patriotic program.', '2026-01-26', '08:00:00', 'School Ground', 'cultural', 1, 1),
('Sports Day', 'Annual athletics and sports competition.', '2026-04-10', '08:30:00', 'Sports Ground', 'sports', 1, 1),
('Holi Holiday', 'School closed for Holi festival.', '2026-03-10', NULL, NULL, 'holiday', 1, 1);

-- -------------------------------------------
-- Admissions (sample applications)
-- -------------------------------------------
INSERT INTO `admissions` (`student_name`, `class_applied`, `date_of_birth`, `gender`, `parent_name`, `parent_phone`, `parent_email`, `address`, `previous_school`, `status`) VALUES
('Aditya Raj', '6', '2015-08-20', 'male', 'Sanjay Raj', '+91-9900112233', 'sanjay.raj@email.com', '101, Aashiana, Lucknow, UP', 'St. Xavier\'s School', 'pending'),
('Kavya Nair', '7', '2014-04-11', 'female', 'Suresh Nair', '+91-9900112234', NULL, '202, Gomti Nagar, Lucknow, UP', 'DPS Lucknow', 'approved');

-- -------------------------------------------
-- Settings
-- -------------------------------------------
INSERT INTO `settings` (`key_name`, `value`) VALUES
('school_name', 'JNV Model School'),
('school_tagline', 'Excellence in Education'),
('school_phone', '+91-522-2345678'),
('school_email', 'info@jnvmodelschool.edu.in'),
('school_address', '123, Education Lane, Lucknow, Uttar Pradesh 226001'),
('academic_year', '2025-2026'),
('whatsapp_groups', '[]'),
('principal_name', 'Dr. R.K. Tripathi'),
('school_affiliation', 'CBSE — Affiliation No. 2131234'),
('session_start_month', 'April'),
('session_end_month', 'March');

-- -------------------------------------------
-- Branding (default)
-- -------------------------------------------
INSERT INTO `branding` (`primary_color`, `secondary_color`, `font_family`) VALUES
('#1e40af', '#f59e0b', 'Inter');


-- =============================================
-- END OF SCHEMA
-- =============================================
