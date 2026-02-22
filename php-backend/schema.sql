-- ============================================
-- JNV School Management System — Full Database Schema v3.3
-- Domain: jnvschool.awayindia.com
-- Run this in phpMyAdmin after creating database
-- ============================================
-- 
-- IMPORT INSTRUCTIONS:
-- 1. Log in to cPanel → phpMyAdmin
-- 2. Select your database (e.g., yshszsos_jnvschool)
-- 3. Click the "Import" tab at the top
-- 4. Click "Choose File" → select this schema.sql file
-- 5. Set "Format" to SQL (default)
-- 6. Click "Go" to import
--
-- ⚠️ WARNING: This uses DROP TABLE IF EXISTS — it will DELETE
--    all existing data if tables already exist. BACK UP FIRST!
--
-- TOTAL TABLES: 24
--   1. users                — Admin/teacher/office accounts
--   2. students             — Student records with photos
--   3. teachers             — Teacher records linked to user accounts
--   4. admissions           — Online admission applications
--   5. notifications        — Notifications with approval workflow + targeting
--   6. notification_reads   — Per-user read tracking
--   7. notification_versions — Edit history with restore
--   8. notification_attachments — Multi-file attachments
--   9. gallery_items        — Gallery uploads with approval
--  10. gallery_categories   — Gallery categories
--  11. gallery_albums       — Albums within categories
--  12. events               — School events/calendar
--  13. attendance           — Daily attendance by class
--  14. exam_results         — Exam marks with auto-grading
--  15. audit_logs           — System action logs
--  16. settings             — Key-value school settings (~80+ keys)
--  17. home_slider          — Homepage slider with animations & overlays
--  18. site_quotes          — Inspirational quotes for About page
--  19. leadership_profiles  — Leadership profiles for About page
--  20. nav_menu_items       — Admin-managed navbar menu
--  21. certificates         — School certificates & accreditations
--  22. feature_cards        — Homepage quick-link cards
--  23. fee_structures       — Class-wise fee structures
--  24. fee_components       — Fee line items
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+05:30";

-- --------------------------------------------------------
-- 1. Users (admin, teacher, office roles)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notification_reads`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `exam_results`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `gallery_items`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `admissions`;
DROP TABLE IF EXISTS `home_slider`;
DROP TABLE IF EXISTS `site_quotes`;
DROP TABLE IF EXISTS `leadership_profiles`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin','admin','teacher','office') NOT NULL DEFAULT 'teacher',
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `reset_token` VARCHAR(64) DEFAULT NULL,
  `reset_expires` DATETIME DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: Admin@123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- --------------------------------------------------------
-- 2. Students
-- --------------------------------------------------------
CREATE TABLE `students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admission_no` VARCHAR(30) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `father_name` VARCHAR(100) DEFAULT NULL,
  `mother_name` VARCHAR(100) DEFAULT NULL,
  `dob` DATE DEFAULT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `class` VARCHAR(20) DEFAULT NULL,
  `section` VARCHAR(10) DEFAULT NULL,
  `roll_no` INT DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(191) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `blood_group` VARCHAR(5) DEFAULT NULL,
  `category` VARCHAR(30) DEFAULT NULL,
  `aadhar_no` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('active','inactive','alumni','tc_issued') NOT NULL DEFAULT 'active',
  `admission_date` DATE DEFAULT NULL,
  `leaving_date` DATE DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admission_no` (`admission_no`),
  KEY `idx_class` (`class`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Teachers
-- --------------------------------------------------------
CREATE TABLE `teachers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `employee_id` VARCHAR(30) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) DEFAULT 'Teacher',
  `email` VARCHAR(191) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(100) DEFAULT NULL,
  `qualification` VARCHAR(100) DEFAULT NULL,
  `experience_years` INT DEFAULT 0,
  `dob` DATE DEFAULT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `joining_date` DATE DEFAULT NULL,
  `status` ENUM('active','inactive','resigned','retired') NOT NULL DEFAULT 'active',
  `is_core_team` TINYINT(1) NOT NULL DEFAULT 0,
  `bio` TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_display_order` (`display_order`),
  CONSTRAINT `fk_teacher_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Admissions
-- --------------------------------------------------------
CREATE TABLE `admissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_name` VARCHAR(100) NOT NULL,
  `father_name` VARCHAR(100) DEFAULT NULL,
  `mother_name` VARCHAR(100) DEFAULT NULL,
  `dob` DATE DEFAULT NULL,
  `gender` ENUM('male','female','other') DEFAULT NULL,
  `class_applied` VARCHAR(20) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(191) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `previous_school` VARCHAR(200) DEFAULT NULL,
  `documents` TEXT DEFAULT NULL,
  `status` ENUM('pending','approved','rejected','waitlisted') NOT NULL DEFAULT 'pending',
  `remarks` TEXT DEFAULT NULL,
  `reviewed_by` INT UNSIGNED DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `reviewed_by` (`reviewed_by`),
  CONSTRAINT `fk_admission_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Notifications (with targeting, visibility, soft-delete)
-- --------------------------------------------------------
CREATE TABLE `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `content` TEXT NOT NULL,
  `type` ENUM('general','academic','exam','holiday','event','urgent') NOT NULL DEFAULT 'general',
  `priority` ENUM('normal','important','urgent') NOT NULL DEFAULT 'normal',
  `category` VARCHAR(50) DEFAULT 'general',
  `tags` VARCHAR(500) DEFAULT NULL,
  `target_audience` ENUM('all','students','teachers','parents','class','section') NOT NULL DEFAULT 'all',
  `target_class` VARCHAR(20) DEFAULT NULL,
  `target_section` VARCHAR(10) DEFAULT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `is_public` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('draft','pending','approved','published','expired','rejected') NOT NULL DEFAULT 'pending',
  `posted_by` INT UNSIGNED DEFAULT NULL,
  `approved_by` INT UNSIGNED DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `reject_reason` TEXT DEFAULT NULL,
  `schedule_at` DATETIME DEFAULT NULL,
  `expires_at` DATE DEFAULT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `show_popup` TINYINT(1) NOT NULL DEFAULT 0,
  `show_banner` TINYINT(1) NOT NULL DEFAULT 0,
  `show_marquee` TINYINT(1) NOT NULL DEFAULT 0,
  `show_dashboard` TINYINT(1) NOT NULL DEFAULT 0,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_at` DATETIME DEFAULT NULL,
  `deleted_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `posted_by` (`posted_by`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `fk_notif_poster` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_notif_approver` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Notification Read Tracking
-- --------------------------------------------------------
CREATE TABLE `notification_reads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `notification_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `read_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_read` (`notification_id`, `user_id`),
  CONSTRAINT `fk_nread_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_nread_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Gallery Items
-- --------------------------------------------------------
CREATE TABLE `gallery_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(50) DEFAULT 'General',
  `event_name` VARCHAR(200) DEFAULT NULL,
  `event_date` DATE DEFAULT NULL,
  `tags` VARCHAR(500) DEFAULT NULL,
  `visibility` ENUM('public','private') NOT NULL DEFAULT 'public',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` ENUM('image','video') NOT NULL DEFAULT 'image',
  `original_size` INT UNSIGNED DEFAULT NULL,
  `compressed_size` INT UNSIGNED DEFAULT NULL,
  `batch_id` VARCHAR(32) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `uploaded_by` INT UNSIGNED DEFAULT NULL,
  `approved_by` INT UNSIGNED DEFAULT NULL,
  `approved_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_batch` (`batch_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_gallery_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Events
-- --------------------------------------------------------
CREATE TABLE `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `event_time` TIME DEFAULT NULL,
  `location` VARCHAR(200) DEFAULT NULL,
  `type` ENUM('academic','cultural','sports','holiday','exam','meeting','other') NOT NULL DEFAULT 'other',
  `image` VARCHAR(255) DEFAULT NULL,
  `is_public` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`event_date`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fk_event_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 9. Attendance
-- --------------------------------------------------------
CREATE TABLE `attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `class` VARCHAR(20) NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` VARCHAR(255) DEFAULT NULL,
  `marked_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`student_id`, `date`),
  KEY `idx_class_date` (`class`, `date`),
  KEY `marked_by` (`marked_by`),
  CONSTRAINT `fk_attendance_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attendance_marker` FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 10. Exam Results
-- --------------------------------------------------------
CREATE TABLE `exam_results` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `exam_name` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `class` VARCHAR(20) NOT NULL,
  `max_marks` INT NOT NULL DEFAULT 100,
  `obtained_marks` INT NOT NULL DEFAULT 0,
  `grade` VARCHAR(5) DEFAULT NULL,
  `remarks` VARCHAR(255) DEFAULT NULL,
  `entered_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_result` (`student_id`, `exam_name`, `subject`),
  KEY `idx_exam_class` (`exam_name`, `class`),
  KEY `entered_by` (`entered_by`),
  CONSTRAINT `fk_result_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_result_teacher` FOREIGN KEY (`entered_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 11. Audit Logs
-- --------------------------------------------------------
CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) DEFAULT NULL,
  `entity_id` INT UNSIGNED DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 12. Settings
-- --------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('school_name', 'Jawahar Navodaya Vidyalaya'),
('school_short_name', 'JNV'),
('school_tagline', 'Nurturing Talent, Shaping Future'),
('school_email', 'info@jnvschool.awayindia.com'),
('school_phone', '+91-XXXXXXXXXX'),
('school_address', 'India'),
('school_logo', ''),
('primary_color', '#1e40af'),
('secondary_color', '#3b82f6'),
('academic_year', '2025-2026'),
('admission_open', '1'),
('facebook_url', ''),
('twitter_url', ''),
('instagram_url', ''),
('youtube_url', ''),
('popup_ad_image', ''),
('popup_ad_active', '0'),
('social_facebook', ''),
('social_twitter', ''),
('social_instagram', ''),
('social_youtube', ''),
('social_linkedin', ''),
('about_history', ''),
('about_vision', ''),
('about_mission', ''),
('whatsapp_api_number', ''),
('sms_gateway_key', ''),
('school_favicon', ''),
('core_value_1_title', 'Excellence'),
('core_value_1_desc', 'We strive for the highest standards in academics, character, and personal growth.'),
('core_value_2_title', 'Integrity'),
('core_value_2_desc', 'We foster honesty, transparency, and ethical behavior in all our actions.'),
('core_value_3_title', 'Innovation'),
('core_value_3_desc', 'We embrace creativity and modern teaching methods to inspire learning.'),
('core_value_4_title', 'Community'),
('core_value_4_desc', 'We build a supportive, inclusive environment where everyone belongs.'),
-- Page Content Manager settings
('home_marquee_text', ''),
('home_hero_show', '1'),
('home_stats_show', '1'),
('home_stats_students_label', 'Students'),
('home_stats_teachers_label', 'Teachers'),
('home_stats_classes_label', 'Classes'),
('home_stats_classes_value', '12'),
('home_stats_dedication_label', 'Dedication'),
('home_stats_dedication_value', '100%'),
('home_quicklinks_show', '1'),
('home_cta_admissions_title', 'Admissions'),
('home_cta_admissions_desc', 'Apply online for admission to JNV School.'),
('home_cta_notifications_title', 'Notifications'),
('home_cta_notifications_desc', 'Stay updated with latest announcements.'),
('home_cta_gallery_title', 'Gallery'),
('home_cta_gallery_desc', 'Explore photos & videos from school life.'),
('home_cta_events_title', 'Events'),
('home_cta_events_desc', 'Check upcoming school events & dates.'),
('home_core_team_show', '1'),
('home_core_team_title', 'Our Core Team'),
('home_core_team_subtitle', 'Meet the dedicated leaders guiding our school''s vision and mission.'),
('home_contact_show', '1'),
('home_footer_cta_show', '1'),
('home_footer_cta_title', ''),
('home_footer_cta_desc', ''),
('home_footer_cta_btn_text', 'Get In Touch'),
('about_hero_title', 'About Us'),
('about_hero_subtitle', 'Discover our story, vision, and the values that drive us to provide exceptional education.'),
('about_hero_badge', 'About Our School'),
('about_history_show', '1'),
('about_vision_mission_show', '1'),
('about_core_values_show', '1'),
('about_quote_show', '1'),
('about_leadership_show', '1'),
('about_leadership_title', 'Meet Our Leadership'),
('about_leadership_subtitle', 'With dedication and passion, our team creates an environment where every student thrives.'),
('about_footer_cta_show', '1'),
('teachers_hero_title', 'Our Teachers'),
('teachers_hero_subtitle', 'Meet our dedicated team of qualified educators who inspire, guide, and shape the future of every student.'),
('teachers_hero_badge', 'Our Educators'),
('teachers_core_team_show', '1'),
('teachers_grid_title', 'Meet Our Faculty'),
('teachers_grid_subtitle', 'Hover on a card to learn more about each teacher'),
('teachers_all_show', '1'),
('teachers_footer_cta_show', '1'),
('gallery_hero_title', 'Photo Gallery'),
('gallery_hero_subtitle', ''),
('gallery_hero_icon', 'bi-images'),
('gallery_footer_cta_show', '1'),
('events_hero_title', 'Events'),
('events_hero_subtitle', ''),
('events_hero_icon', 'bi-calendar-event-fill'),
('events_footer_cta_show', '1'),
('notifications_hero_title', 'Notifications'),
('notifications_hero_subtitle', ''),
('notifications_hero_icon', 'bi-bell-fill'),
('notifications_footer_cta_show', '1'),
('admission_hero_title', 'Apply for Admission'),
('admission_hero_subtitle', ''),
('admission_hero_icon', 'bi-file-earmark-plus-fill'),
('admission_footer_cta_show', '1'),
('global_navbar_show_top_bar', '1'),
('global_navbar_show_login', '1'),
('global_navbar_show_notif_bell', '1'),
('global_footer_cta_title', ''),
('global_footer_cta_desc', ''),
('global_footer_cta_btn_text', 'Get In Touch'),
-- Footer Manager settings
('footer_description', 'A professional and modern school with years of experience in nurturing children with senior teachers and a clean environment.'),
('footer_quick_links', '[{"label":"About Us","url":"/public/about.php"},{"label":"Our Teachers","url":"/public/teachers.php"},{"label":"Admissions","url":"/public/admission-form.php"},{"label":"Gallery","url":"/public/gallery.php"},{"label":"Events","url":"/public/events.php"},{"label":"Admin Login","url":"/login.php"}]'),
('footer_programs', '[{"label":"Pre-Primary (LKG & UKG)"},{"label":"Primary School (1-5)"},{"label":"Upper Primary (6-8)"},{"label":"Co-Curricular Activities"},{"label":"Sports Programs"}]'),
('footer_contact_address', ''),
('footer_contact_phone', ''),
('footer_contact_email', ''),
('footer_contact_hours', 'Mon - Sat: 8:00 AM - 5:00 PM'),
('footer_social_facebook', ''),
('footer_social_twitter', ''),
('footer_social_instagram', ''),
('footer_social_youtube', ''),
('footer_social_linkedin', ''),
-- Maintenance mode
('maintenance_mode', '0');

-- --------------------------------------------------------
-- 13. Leadership Profiles (About Us page)
-- --------------------------------------------------------
-- NOTE: gallery_categories and gallery_albums are created AFTER leadership/slider/quotes


CREATE TABLE `leadership_profiles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 14. Home Slider (with animations, overlays, text position)
-- --------------------------------------------------------
CREATE TABLE `home_slider` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) DEFAULT NULL,
  `subtitle` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `link_url` VARCHAR(255) DEFAULT NULL,
  `badge_text` VARCHAR(50) DEFAULT NULL,
  `cta_text` VARCHAR(50) DEFAULT NULL,
  `animation_type` VARCHAR(20) NOT NULL DEFAULT 'fade',
  `overlay_style` VARCHAR(20) NOT NULL DEFAULT 'gradient-dark',
  `text_position` VARCHAR(10) NOT NULL DEFAULT 'left',
  `overlay_opacity` INT NOT NULL DEFAULT 70,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 14. Site Quotes (Inspirational Quote on About page)
-- --------------------------------------------------------
CREATE TABLE `site_quotes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_text` TEXT NOT NULL,
  `author_name` VARCHAR(200) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_by` INT UNSIGNED DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_quote_user` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_quotes` (`quote_text`, `author_name`, `updated_by`) VALUES
('Education is the most powerful weapon which you can use to change the world.', 'Nelson Mandela', 1);

-- Admission page hero settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('admission_hero_title', 'Apply for Admission'),
('admission_hero_subtitle', 'Submit your application to join our school');

-- --------------------------------------------------------
-- Sample Slider Data (5 slides)
-- Replace image_path with actual uploaded images
-- --------------------------------------------------------
INSERT INTO `home_slider` (`title`, `subtitle`, `image_path`, `badge_text`, `cta_text`, `animation_type`, `overlay_style`, `text_position`, `overlay_opacity`, `sort_order`, `is_active`) VALUES
('Welcome to Jawahar Navodaya Vidyalaya', 'Nurturing young minds with quality education, discipline, and values since establishment.', 'uploads/slider/slide1.jpg', 'Welcome', 'Learn More', 'fade', 'gradient-dark', 'left', 70, 1, 1),
('Academic Excellence', 'Our students consistently achieve outstanding results in board examinations and competitive tests.', 'uploads/slider/slide2.jpg', 'Academics', 'View Results', 'slide', 'gradient-primary', 'center', 65, 2, 1),
('State-of-the-Art Campus', 'Modern classrooms, science labs, computer labs, library, sports grounds, and hostel facilities.', 'uploads/slider/slide3.jpg', 'Campus', 'Take a Tour', 'zoom', 'gradient-dark', 'left', 70, 3, 1),
('Sports & Co-Curricular Activities', 'Developing well-rounded individuals through athletics, cultural events, and extracurricular programs.', 'uploads/slider/slide4.jpg', 'Activities', 'Explore', 'kenburns', 'solid-dark', 'right', 60, 4, 1),
('Admissions Open 2025-26', 'Apply now for the upcoming academic session. Limited seats available for Classes VI to XII.', 'uploads/slider/slide5.jpg', 'Admissions', 'Apply Now', 'fade', 'gradient-primary', 'center', 75, 5, 1);

-- --------------------------------------------------------
-- 16. Gallery Categories
-- --------------------------------------------------------
DROP TABLE IF EXISTS `gallery_albums`;
DROP TABLE IF EXISTS `gallery_categories`;

CREATE TABLE `gallery_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 17. Gallery Albums
-- --------------------------------------------------------
CREATE TABLE `gallery_albums` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATE DEFAULT NULL,
  `year` VARCHAR(10) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_category` (`category_id`),
  CONSTRAINT `fk_album_category` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed demo categories
INSERT INTO `gallery_categories` (`name`, `slug`, `sort_order`) VALUES
('Academic', 'academic', 1),
('Cultural', 'cultural', 2),
('Sports', 'sports', 3),
('Events', 'events', 4),
('Infrastructure', 'infrastructure', 5),
('Students', 'students', 6),
('Teachers', 'teachers', 7),
('Campus Life', 'campus-life', 8);

-- Gallery settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('gallery_layout_style', 'premium'),
('gallery_bg_style', 'dark'),
('gallery_particles_show', '1');

-- ALTER gallery_items for album support (run on existing DB):
-- ALTER TABLE `gallery_items`
--   ADD COLUMN `album_id` INT UNSIGNED DEFAULT NULL AFTER `category`,
--   ADD COLUMN `caption` VARCHAR(500) DEFAULT NULL AFTER `description`,
--   ADD COLUMN `position` INT NOT NULL DEFAULT 0 AFTER `caption`,
--   ADD KEY `idx_album` (`album_id`),
--   ADD CONSTRAINT `fk_item_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums`(`id`) ON DELETE SET NULL;

-- --------------------------------------------------------
-- 18. Notification Versions (Edit history with restore)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notification_versions`;

CREATE TABLE `notification_versions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `notification_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200),
  `content` TEXT,
  `type` VARCHAR(20),
  `priority` VARCHAR(20),
  `target_audience` VARCHAR(20),
  `category` VARCHAR(50),
  `tags` VARCHAR(500),
  `changed_by` INT UNSIGNED,
  `changed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif` (`notification_id`),
  CONSTRAINT `fk_nver_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_nver_user` FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 19. Notification Attachments (Multi-file support)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notification_attachments`;

CREATE TABLE `notification_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `notification_id` INT UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50),
  `file_size` INT UNSIGNED DEFAULT 0,
  `uploaded_by` INT UNSIGNED,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_attach` (`notification_id`),
  CONSTRAINT `fk_natt_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_natt_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 20. Navigation Menu Items (Admin-managed navbar)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `nav_menu_items`;

CREATE TABLE `nav_menu_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(100) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(50) DEFAULT NULL,
  `link_type` ENUM('internal','external') NOT NULL DEFAULT 'internal',
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `is_cta` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `nav_menu_items` (`label`, `url`, `icon`, `link_type`, `is_visible`, `is_cta`, `sort_order`) VALUES
('Home', '/', 'bi-house-fill', 'internal', 1, 0, 1),
('About Us', '/public/about.php', 'bi-info-circle', 'internal', 1, 0, 2),
('Our Teachers', '/public/teachers.php', 'bi-person-badge', 'internal', 1, 0, 3),
('Notifications', '/public/notifications.php', 'bi-bell', 'internal', 1, 0, 4),
('Gallery', '/public/gallery.php', 'bi-images', 'internal', 1, 0, 5),
('Certificates', '/public/certificates.php', 'bi-award', 'internal', 1, 0, 6),
('Events', '/public/events.php', 'bi-calendar-event', 'internal', 1, 0, 7),
('Apply Now', '/public/admission-form.php', 'bi-pencil-square', 'internal', 1, 1, 8);

-- --------------------------------------------------------
-- 21. Certificates & Accreditations
-- --------------------------------------------------------
DROP TABLE IF EXISTS `certificates`;

CREATE TABLE `certificates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'recognition',
  `year` SMALLINT DEFAULT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `thumb_path` VARCHAR(255) DEFAULT NULL,
  `file_type` ENUM('image','pdf') NOT NULL DEFAULT 'image',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_download` TINYINT(1) NOT NULL DEFAULT 1,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted_at` DATETIME DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_category` (`category`),
  KEY `idx_order` (`display_order`),
  KEY `idx_deleted` (`is_deleted`),
  CONSTRAINT `fk_cert_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Certificate settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('home_certificates_show', '1'),
('home_certificates_max', '6'),
('certificates_page_enabled', '1');

-- --------------------------------------------------------
-- 22. Feature Cards (Home page quick-link cards)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `feature_cards`;

CREATE TABLE `feature_cards` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(50) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `icon_class` VARCHAR(100) NOT NULL DEFAULT 'bi-star',
  `accent_color` VARCHAR(20) NOT NULL DEFAULT 'auto',
  `btn_text` VARCHAR(50) NOT NULL DEFAULT 'Learn More',
  `btn_link` VARCHAR(255) NOT NULL DEFAULT '#',
  `badge_text` VARCHAR(50) DEFAULT NULL,
  `badge_color` VARCHAR(20) DEFAULT '#ef4444',
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `show_stats` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `click_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `feature_cards` (`slug`, `title`, `description`, `icon_class`, `accent_color`, `btn_text`, `btn_link`, `badge_text`, `badge_color`, `is_featured`, `sort_order`) VALUES
('admissions', 'Admissions', 'Apply online for admission to JNV School.', 'bi-mortarboard-fill', '#3b82f6', 'Apply Now', '/public/admission-form.php', 'Open', '#22c55e', 1, 1),
('notifications', 'Notifications', 'Stay updated with latest announcements.', 'bi-bell-fill', '#f59e0b', 'View All', '/public/notifications.php', NULL, '#ef4444', 0, 2),
('gallery', 'Gallery', 'Explore photos & videos from school life.', 'bi-images', '#10b981', 'Browse', '/public/gallery.php', NULL, '#8b5cf6', 0, 3),
('events', 'Events', 'Check upcoming school events & dates.', 'bi-calendar-event-fill', '#ef4444', 'View Events', '/public/events.php', NULL, '#3b82f6', 0, 4);

-- --------------------------------------------------------
-- 23. Fee Structures
-- --------------------------------------------------------
DROP TABLE IF EXISTS `fee_components`;
DROP TABLE IF EXISTS `fee_structures`;

CREATE TABLE `fee_structures` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` VARCHAR(20) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_class_year` (`class`, `academic_year`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fk_fee_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 24. Fee Components
-- --------------------------------------------------------
CREATE TABLE `fee_components` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fee_structure_id` INT UNSIGNED NOT NULL,
  `component_name` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `frequency` ENUM('one-time','monthly','quarterly','yearly') NOT NULL DEFAULT 'yearly',
  `is_optional` TINYINT(1) NOT NULL DEFAULT 0,
  `display_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fee_structure_id` (`fee_structure_id`),
  CONSTRAINT `fk_comp_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;