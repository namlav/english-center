-- CAI MOI DAY DU 39 BANG VA 5 BO / 60 CAU HOI CO DAP AN
-- Ngay 22/10/2026. Danh cho mot database MOI, RONG.
-- phpMyAdmin: tao database english_center_online, chon database do, Import file nay.
-- Website .env: DB_CONNECTION=mysql ; DB_DATABASE=english_center_online
-- Tao ca 7 bang quan ly trung tam goc, 31 bang hoc online, va migrations.
-- 1 tai khoan quan tri mau: admin@englishcenter.vn / 123456
-- Doi mat khau tai khoan mau khi dua website vao su dung.
-- Khong nhap them cac file nang cap hoac file 60 cau sau khi nhap file nay.
-- Khong DROP TABLE/DATABASE; khong xoa du lieu o database cu.
-- MySQL 8.0.16+ / MariaDB 10.4+. Chua import tren may chu MySQL/MariaDB that.
-- Da thu cau truc, 60 cau/dap an, tai khoan admin va khoa ngoai tren SQLite trong bo nho.
-- Bang hinh anh tham khao de trong, khong can chep tep anh.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @EC_FRESH_OLD_SQL_MODE = @@SESSION.sql_mode;
SET @EC_FRESH_OLD_TIME_ZONE = @@SESSION.time_zone;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_AUTO_VALUE_ON_ZERO';
SET SESSION time_zone = '+00:00';

-- PHAN 1: TOAN BO CAU TRUC
create table `migrations` (`id` int unsigned not null auto_increment primary key, `migration` varchar(255) not null, `batch` int not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

create table `users` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `email` varchar(255) not null, `password` varchar(255) not null, `role` enum('admin', 'teacher', 'student') not null default 'student', `phone` varchar(20) null, `is_active` tinyint(1) not null default '1', `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `users` add unique `users_email_unique`(`email`);

create table `teachers` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `teacher_code` varchar(30) not null, `specialization` varchar(255) not null, `degree` varchar(255) null, `bio` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `teachers` add constraint `teachers_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

alter table `teachers` add unique `teachers_user_id_unique`(`user_id`);

alter table `teachers` add unique `teachers_teacher_code_unique`(`teacher_code`);

create table `students` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `student_code` varchar(30) not null, `date_of_birth` date null, `gender` enum('male', 'female', 'other') null, `address` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `students` add constraint `students_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

alter table `students` add unique `students_user_id_unique`(`user_id`);

alter table `students` add unique `students_student_code_unique`(`student_code`);

create table `courses` (`id` bigint unsigned not null auto_increment primary key, `code` varchar(30) not null, `name` varchar(255) not null, `level` varchar(255) null, `duration_hours` int not null default '48', `description` text null, `is_active` tinyint(1) not null default '1', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `courses` add unique `courses_code_unique`(`code`);

create table `class_rooms` (`id` bigint unsigned not null auto_increment primary key, `code` varchar(30) not null, `name` varchar(255) not null, `course_id` bigint unsigned not null, `teacher_id` bigint unsigned not null, `start_date` date not null, `schedule` varchar(255) not null, `tuition_fee` decimal(12, 2) not null default '0', `max_students` int unsigned not null default '25', `status` enum('upcoming', 'ongoing', 'completed', 'cancelled') not null default 'upcoming', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `class_rooms` add constraint `class_rooms_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete cascade;

alter table `class_rooms` add constraint `class_rooms_teacher_id_foreign` foreign key (`teacher_id`) references `teachers` (`id`) on delete restrict;

alter table `class_rooms` add unique `class_rooms_code_unique`(`code`);

create table `enrollments` (`id` bigint unsigned not null auto_increment primary key, `class_room_id` bigint unsigned not null, `student_id` bigint unsigned not null, `enrollment_date` date not null, `paid_tuition` decimal(12, 2) not null default '0', `payment_status` enum('paid', 'unpaid') not null default 'paid', `midterm_score` decimal(5, 2) null, `final_score` decimal(5, 2) null, `result_status` enum('studying', 'passed', 'failed') not null default 'studying', `remarks` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `enrollments` add constraint `enrollments_class_room_id_foreign` foreign key (`class_room_id`) references `class_rooms` (`id`) on delete cascade;

alter table `enrollments` add constraint `enrollments_student_id_foreign` foreign key (`student_id`) references `students` (`id`) on delete cascade;

alter table `enrollments` add unique `enrollments_class_room_id_student_id_unique`(`class_room_id`, `student_id`);

create table `attendances` (`id` bigint unsigned not null auto_increment primary key, `class_room_id` bigint unsigned not null, `student_id` bigint unsigned not null, `attendance_date` date not null, `status` enum('present', 'absent', 'late') not null default 'present', `note` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `attendances` add constraint `attendances_class_room_id_foreign` foreign key (`class_room_id`) references `class_rooms` (`id`) on delete cascade;

alter table `attendances` add constraint `attendances_student_id_foreign` foreign key (`student_id`) references `students` (`id`) on delete cascade;

alter table `attendances` add unique `uniq_class_student_date`(`class_room_id`, `student_id`, `attendance_date`);

alter table `teachers` drop foreign key `teachers_user_id_foreign`;

alter table `teachers` add constraint `teachers_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `students` drop foreign key `students_user_id_foreign`;

alter table `students` add constraint `students_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `class_rooms` drop foreign key `class_rooms_course_id_foreign`;

alter table `class_rooms` add constraint `class_rooms_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `enrollments` drop foreign key `enrollments_class_room_id_foreign`;

alter table `enrollments` add constraint `enrollments_class_room_id_foreign` foreign key (`class_room_id`) references `class_rooms` (`id`) on delete restrict;

alter table `enrollments` drop foreign key `enrollments_student_id_foreign`;

alter table `enrollments` add constraint `enrollments_student_id_foreign` foreign key (`student_id`) references `students` (`id`) on delete restrict;

alter table `attendances` drop foreign key `attendances_class_room_id_foreign`;

alter table `attendances` add constraint `attendances_class_room_id_foreign` foreign key (`class_room_id`) references `class_rooms` (`id`) on delete restrict;

alter table `attendances` drop foreign key `attendances_student_id_foreign`;

alter table `attendances` add constraint `attendances_student_id_foreign` foreign key (`student_id`) references `students` (`id`) on delete restrict;

alter table `attendances` add constraint `attendance_enrolled_student_fk` foreign key (`class_room_id`, `student_id`) references `enrollments` (`class_room_id`, `student_id`) on delete restrict;

ALTER TABLE courses ADD CONSTRAINT courses_training_check CHECK (duration_hours > 0);

ALTER TABLE class_rooms ADD CONSTRAINT class_rooms_training_check CHECK (tuition_fee >= 0 AND max_students > 0);

ALTER TABLE enrollments ADD CONSTRAINT enrollments_training_check CHECK (paid_tuition >= 0 AND (midterm_score IS NULL OR midterm_score BETWEEN 0 AND 10) AND (final_score IS NULL OR final_score BETWEEN 0 AND 10));

alter table `class_rooms` add `expected_end_date` date null after `start_date`;

create table `learning_categories` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `slug` varchar(255) not null, `description` text null, `is_active` tinyint(1) not null default '1', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `learning_categories` add unique `learning_categories_slug_unique`(`slug`);

alter table `courses` add `learning_category_id` bigint unsigned null;

alter table `courses` add constraint `courses_learning_category_id_foreign` foreign key (`learning_category_id`) references `learning_categories` (`id`) on delete restrict;

alter table `courses` add `slug` varchar(255) null;

alter table `courses` add `delivery_mode` enum('classroom', 'online') not null default 'classroom';

alter table `courses` add `summary` text null;

alter table `courses` add `thumbnail_path` varchar(255) null;

alter table `courses` add `price` decimal(12, 2) not null default '0';

alter table `courses` add `sale_price` decimal(12, 2) null;

alter table `courses` add `access_days` int unsigned not null default '365';

alter table `courses` add `published_at` timestamp null;

alter table `courses` add index `courses_catalog_idx`(`delivery_mode`, `is_active`, `published_at`);

alter table `courses` add unique `courses_slug_unique`(`slug`);

create table `course_sections` (`id` bigint unsigned not null auto_increment primary key, `course_id` bigint unsigned not null, `title` varchar(255) not null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_sections` add constraint `course_sections_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `course_sections` add unique `course_sections_course_id_position_unique`(`course_id`, `position`);

create table `lessons` (`id` bigint unsigned not null auto_increment primary key, `course_section_id` bigint unsigned not null, `title` varchar(255) not null, `slug` varchar(255) not null, `type` enum('text', 'video', 'audio') not null default 'text', `content` longtext null, `media_path` varchar(255) null, `duration_seconds` int unsigned not null default '0', `position` int unsigned not null, `is_preview` tinyint(1) not null default '0', `is_published` tinyint(1) not null default '0', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `lessons` add constraint `lessons_course_section_id_foreign` foreign key (`course_section_id`) references `course_sections` (`id`) on delete restrict;

alter table `lessons` add unique `lessons_course_section_id_slug_unique`(`course_section_id`, `slug`);

alter table `lessons` add unique `lessons_course_section_id_position_unique`(`course_section_id`, `position`);

create table `course_enrollments` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `course_id` bigint unsigned not null, `source` enum('free', 'manual', 'purchase') not null default 'manual', `status` enum('active', 'revoked') not null default 'active', `started_at` timestamp not null, `expires_at` timestamp null, `completed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_enrollments` add constraint `course_enrollments_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `course_enrollments` add constraint `course_enrollments_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `course_enrollments` add unique `course_enrollments_user_id_course_id_unique`(`user_id`, `course_id`);

alter table `course_enrollments` add index `course_access_idx`(`user_id`, `status`, `expires_at`);

create table `lesson_progress` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `lesson_id` bigint unsigned not null, `position_seconds` int unsigned not null default '0', `last_accessed_at` timestamp not null, `completed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `lesson_progress` add constraint `lesson_progress_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `lesson_progress` add constraint `lesson_progress_lesson_id_foreign` foreign key (`lesson_id`) references `lessons` (`id`) on delete restrict;

alter table `lesson_progress` add unique `lesson_progress_user_id_lesson_id_unique`(`user_id`, `lesson_id`);

alter table `lesson_progress` add index `lesson_progress_user_id_last_accessed_at_index`(`user_id`, `last_accessed_at`);

create table `exam_papers` (`id` bigint unsigned not null auto_increment primary key, `learning_category_id` bigint unsigned not null, `created_by` bigint unsigned null, `title` varchar(255) not null, `slug` varchar(255) not null, `description` text null, `skill` enum('listening', 'reading', 'writing', 'speaking', 'mixed') not null, `duration_seconds` int unsigned not null, `status` enum('draft', 'published', 'archived') not null default 'draft', `published_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_papers` add constraint `exam_papers_learning_category_id_foreign` foreign key (`learning_category_id`) references `learning_categories` (`id`) on delete restrict;

alter table `exam_papers` add constraint `exam_papers_created_by_foreign` foreign key (`created_by`) references `users` (`id`) on delete restrict;

alter table `exam_papers` add index `exam_catalog_idx`(`status`, `learning_category_id`, `skill`);

alter table `exam_papers` add unique `exam_papers_slug_unique`(`slug`);

create table `exam_sections` (`id` bigint unsigned not null auto_increment primary key, `exam_paper_id` bigint unsigned not null, `title` varchar(255) not null, `skill` enum('listening', 'reading', 'writing', 'speaking') not null, `position` int unsigned not null, `duration_seconds` int unsigned null, `instructions` longtext null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_sections` add constraint `exam_sections_exam_paper_id_foreign` foreign key (`exam_paper_id`) references `exam_papers` (`id`) on delete restrict;

alter table `exam_sections` add unique `exam_sections_exam_paper_id_position_unique`(`exam_paper_id`, `position`);

alter table `exam_sections` add unique `exam_section_paper_unique`(`id`, `exam_paper_id`);

create table `question_groups` (`id` bigint unsigned not null auto_increment primary key, `exam_section_id` bigint unsigned not null, `title` varchar(255) null, `position` int unsigned not null, `instructions` longtext null, `passage` longtext null, `transcript` text null, `audio_path` varchar(255) null, `image_path` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `question_groups` add constraint `question_groups_exam_section_id_foreign` foreign key (`exam_section_id`) references `exam_sections` (`id`) on delete restrict;

alter table `question_groups` add unique `question_groups_exam_section_id_position_unique`(`exam_section_id`, `position`);

alter table `question_groups` add unique `question_group_section_unique`(`id`, `exam_section_id`);

create table `questions` (`id` bigint unsigned not null auto_increment primary key, `question_group_id` bigint unsigned not null, `exam_section_id` bigint unsigned not null, `position` int unsigned not null, `prompt` longtext not null, `type` enum('single_choice', 'multiple_choice', 'short_answer', 'true_false', 'essay', 'speaking') not null, `max_points` decimal(8, 2) not null default '1', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `questions` add constraint `question_group_section_fk` foreign key (`question_group_id`, `exam_section_id`) references `question_groups` (`id`, `exam_section_id`) on delete restrict;

alter table `questions` add unique `questions_question_group_id_position_unique`(`question_group_id`, `position`);

alter table `questions` add unique `question_section_unique`(`id`, `exam_section_id`);

create table `question_options` (`id` bigint unsigned not null auto_increment primary key, `question_id` bigint unsigned not null, `label` varchar(16) not null, `content` text not null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `question_options` add constraint `question_options_question_id_foreign` foreign key (`question_id`) references `questions` (`id`) on delete restrict;

alter table `question_options` add unique `question_options_question_id_label_unique`(`question_id`, `label`);

alter table `question_options` add unique `question_options_question_id_position_unique`(`question_id`, `position`);

create table `question_answer_keys` (`id` bigint unsigned not null auto_increment primary key, `question_id` bigint unsigned not null, `accepted_answers` json null, `case_sensitive` tinyint(1) not null default '0', `rubric` json null, `explanation` longtext null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `question_answer_keys` add constraint `question_answer_keys_question_id_foreign` foreign key (`question_id`) references `questions` (`id`) on delete restrict;

alter table `question_answer_keys` add unique `question_answer_keys_question_id_unique`(`question_id`);

create table `exam_attempts` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `exam_paper_id` bigint unsigned not null, `mode` enum('practice', 'full') not null, `status` enum('in_progress', 'submitted', 'graded', 'abandoned') not null default 'in_progress', `title_snapshot` varchar(255) not null, `started_at` timestamp not null, `time_limit_seconds` int unsigned null, `submitted_at` timestamp null, `graded_at` timestamp null, `elapsed_seconds` int unsigned not null default '0', `earned_points` decimal(10, 2) null, `max_points` decimal(10, 2) not null default '0', `scaled_score` decimal(7, 2) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_attempts` add constraint `exam_attempts_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `exam_attempts` add constraint `exam_attempts_exam_paper_id_foreign` foreign key (`exam_paper_id`) references `exam_papers` (`id`) on delete restrict;

alter table `exam_attempts` add unique `attempt_paper_unique`(`id`, `exam_paper_id`);

alter table `exam_attempts` add index `attempt_history_idx`(`user_id`, `started_at`);

alter table `exam_attempts` add index `exam_attempts_exam_paper_id_status_index`(`exam_paper_id`, `status`);

create table `exam_attempt_sections` (`id` bigint unsigned not null auto_increment primary key, `exam_attempt_id` bigint unsigned not null, `exam_paper_id` bigint unsigned not null, `exam_section_id` bigint unsigned not null, `title_snapshot` varchar(255) not null, `instructions_snapshot` longtext null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_attempt_sections` add unique `attempt_section_unique`(`exam_attempt_id`, `exam_section_id`);

alter table `exam_attempt_sections` add unique `attempt_section_position_unique`(`exam_attempt_id`, `position`);

alter table `exam_attempt_sections` add constraint `attempt_section_attempt_fk` foreign key (`exam_attempt_id`, `exam_paper_id`) references `exam_attempts` (`id`, `exam_paper_id`) on delete cascade;

alter table `exam_attempt_sections` add constraint `attempt_section_paper_fk` foreign key (`exam_section_id`, `exam_paper_id`) references `exam_sections` (`id`, `exam_paper_id`) on delete restrict;

create table `attempt_answers` (`id` bigint unsigned not null auto_increment primary key, `exam_attempt_id` bigint unsigned not null, `exam_section_id` bigint unsigned not null, `question_id` bigint unsigned not null, `position` int unsigned not null, `type_snapshot` enum('single_choice', 'multiple_choice', 'short_answer', 'true_false', 'essay', 'speaking') not null, `prompt_snapshot` longtext not null, `group_snapshot` json null, `options_snapshot` json null, `grading_snapshot` json null, `response` json null, `is_correct` tinyint(1) null, `awarded_points` decimal(8, 2) null, `max_points` decimal(8, 2) not null, `answered_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `attempt_answers` add unique `attempt_answer_unique`(`exam_attempt_id`, `question_id`);

alter table `attempt_answers` add unique `attempt_answer_position_unique`(`exam_attempt_id`, `position`);

alter table `attempt_answers` add constraint `answer_attempt_section_fk` foreign key (`exam_attempt_id`, `exam_section_id`) references `exam_attempt_sections` (`exam_attempt_id`, `exam_section_id`) on delete cascade;

alter table `attempt_answers` add constraint `answer_question_section_fk` foreign key (`question_id`, `exam_section_id`) references `questions` (`id`, `exam_section_id`) on delete restrict;

create table `flashcard_decks` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned null, `course_id` bigint unsigned null, `title` varchar(255) not null, `slug` varchar(255) not null, `description` text null, `visibility` enum('private', 'public', 'course') not null default 'private', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `flashcard_decks` add constraint `flashcard_decks_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `flashcard_decks` add constraint `flashcard_decks_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `flashcard_decks` add index `flashcard_decks_visibility_created_at_index`(`visibility`, `created_at`);

alter table `flashcard_decks` add unique `flashcard_decks_slug_unique`(`slug`);

create table `flashcards` (`id` bigint unsigned not null auto_increment primary key, `flashcard_deck_id` bigint unsigned not null, `front` text not null, `back` text not null, `pronunciation` varchar(255) null, `example` text null, `audio_path` varchar(255) null, `image_path` varchar(255) null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `flashcards` add constraint `flashcards_flashcard_deck_id_foreign` foreign key (`flashcard_deck_id`) references `flashcard_decks` (`id`) on delete restrict;

alter table `flashcards` add unique `flashcards_flashcard_deck_id_position_unique`(`flashcard_deck_id`, `position`);

create table `flashcard_reviews` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `flashcard_id` bigint unsigned not null, `ease_factor` decimal(5, 2) not null default '2.5', `interval_days` int unsigned not null default '0', `repetitions` int unsigned not null default '0', `last_rating` tinyint unsigned null, `due_at` timestamp not null, `last_reviewed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `flashcard_reviews` add constraint `flashcard_reviews_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `flashcard_reviews` add constraint `flashcard_reviews_flashcard_id_foreign` foreign key (`flashcard_id`) references `flashcards` (`id`) on delete restrict;

alter table `flashcard_reviews` add unique `flashcard_reviews_user_id_flashcard_id_unique`(`user_id`, `flashcard_id`);

alter table `flashcard_reviews` add index `flashcard_due_idx`(`user_id`, `due_at`);

create table `course_reviews` (`id` bigint unsigned not null auto_increment primary key, `course_id` bigint unsigned not null, `user_id` bigint unsigned not null, `rating` tinyint unsigned not null, `content` text null, `status` enum('pending', 'published', 'hidden') not null default 'pending', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_reviews` add constraint `course_reviews_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `course_reviews` add constraint `course_reviews_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `course_reviews` add unique `course_reviews_course_id_user_id_unique`(`course_id`, `user_id`);

alter table `course_reviews` add index `course_reviews_course_id_status_index`(`course_id`, `status`);

create table `exam_comments` (`id` bigint unsigned not null auto_increment primary key, `exam_paper_id` bigint unsigned not null, `user_id` bigint unsigned not null, `content` text not null, `status` enum('pending', 'published', 'hidden') not null default 'pending', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_comments` add constraint `exam_comments_exam_paper_id_foreign` foreign key (`exam_paper_id`) references `exam_papers` (`id`) on delete restrict;

alter table `exam_comments` add constraint `exam_comments_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `exam_comments` add index `exam_comments_feed_idx`(`exam_paper_id`, `status`, `created_at`);

ALTER TABLE courses ADD CONSTRAINT courses_learning_check CHECK (price >= 0 AND (sale_price IS NULL OR (sale_price >= 0 AND sale_price <= price)) AND access_days > 0 AND (delivery_mode <> 'online' OR slug IS NOT NULL));

ALTER TABLE course_sections ADD CONSTRAINT course_sections_learning_check CHECK (position > 0);

ALTER TABLE lessons ADD CONSTRAINT lessons_learning_check CHECK (position > 0 AND duration_seconds >= 0);

ALTER TABLE course_enrollments ADD CONSTRAINT course_enrollments_learning_check CHECK ((expires_at IS NULL OR expires_at > started_at) AND (completed_at IS NULL OR completed_at >= started_at));

ALTER TABLE lesson_progress ADD CONSTRAINT lesson_progress_learning_check CHECK (position_seconds >= 0);

ALTER TABLE exam_papers ADD CONSTRAINT exam_papers_learning_check CHECK (duration_seconds > 0 AND (status <> 'published' OR published_at IS NOT NULL));

ALTER TABLE exam_sections ADD CONSTRAINT exam_sections_learning_check CHECK (position > 0 AND (duration_seconds IS NULL OR duration_seconds > 0));

ALTER TABLE question_groups ADD CONSTRAINT question_groups_learning_check CHECK (position > 0);

ALTER TABLE questions ADD CONSTRAINT questions_learning_check CHECK (position > 0 AND max_points > 0);

ALTER TABLE question_options ADD CONSTRAINT question_options_learning_check CHECK (position > 0);

ALTER TABLE exam_attempts ADD CONSTRAINT exam_attempts_learning_check CHECK (max_points >= 0 AND elapsed_seconds >= 0 AND (time_limit_seconds IS NULL OR time_limit_seconds > 0) AND (mode <> 'full' OR time_limit_seconds IS NOT NULL) AND (earned_points IS NULL OR (earned_points >= 0 AND earned_points <= max_points)) AND (scaled_score IS NULL OR scaled_score >= 0) AND (submitted_at IS NULL OR submitted_at >= started_at) AND (status NOT IN ('submitted', 'graded') OR submitted_at IS NOT NULL) AND (status <> 'graded' OR (graded_at IS NOT NULL AND earned_points IS NOT NULL)) AND (graded_at IS NULL OR (submitted_at IS NOT NULL AND graded_at >= submitted_at)));

ALTER TABLE exam_attempt_sections ADD CONSTRAINT exam_attempt_sections_learning_check CHECK (position > 0);

ALTER TABLE attempt_answers ADD CONSTRAINT attempt_answers_learning_check CHECK (position > 0 AND max_points > 0 AND (awarded_points IS NULL OR (awarded_points >= 0 AND awarded_points <= max_points)));

ALTER TABLE flashcard_decks ADD CONSTRAINT flashcard_decks_learning_check CHECK ((visibility <> 'course' OR course_id IS NOT NULL) AND (visibility <> 'private' OR user_id IS NOT NULL));

ALTER TABLE flashcards ADD CONSTRAINT flashcards_learning_check CHECK (position > 0);

ALTER TABLE flashcard_reviews ADD CONSTRAINT flashcard_reviews_learning_check CHECK (ease_factor >= 1.3 AND interval_days >= 0 AND repetitions >= 0 AND (last_rating IS NULL OR last_rating BETWEEN 0 AND 5));

ALTER TABLE course_reviews ADD CONSTRAINT course_reviews_learning_check CHECK (rating BETWEEN 1 AND 5);

create table `learner_profiles` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `occupation` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `learner_profiles` add constraint `learner_profiles_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

alter table `learner_profiles` add unique `learner_profiles_user_id_unique`(`user_id`);

create table `course_learning_profiles` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `course_id` bigint unsigned not null, `learning_reason` text null, `consultation_opt_in` tinyint(1) not null default '0', `consented_at` timestamp null, `profile_completed_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_learning_profiles` add constraint `course_learning_profiles_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `course_learning_profiles` add constraint `course_learning_profiles_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `course_learning_profiles` add unique `course_learning_profiles_user_id_course_id_unique`(`user_id`, `course_id`);

create table `learning_score_scales` (`code` varchar(40) not null, `label` varchar(255) not null, `min_score` decimal(6, 2) not null, `max_score` decimal(6, 2) not null, `score_step` decimal(6, 2) not null, `is_active` tinyint(1) not null default '1', `created_at` timestamp null, `updated_at` timestamp null, primary key (`code`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `learning_score_scales` add unique `score_scale_bounds_unique`(`code`, `min_score`, `max_score`, `score_step`);

insert into `learning_score_scales` (`code`, `label`, `max_score`, `min_score`, `score_step`) values ('toeic_lr', 'TOEIC Listening & Reading', 990, 10, 5), ('toeic_speaking', 'TOEIC Speaking', 200, 0, 10), ('toeic_writing', 'TOEIC Writing', 200, 0, 10), ('ielts_band', 'IELTS band', 9, 0, 0.5);

create table `course_learning_goals` (`id` bigint unsigned not null auto_increment primary key, `course_learning_profile_id` bigint unsigned not null, `score_scale_code` varchar(40) not null, `min_score` decimal(6, 2) not null, `max_score` decimal(6, 2) not null, `score_step` decimal(6, 2) not null, `current_score` decimal(6, 2) null, `target_score` decimal(6, 2) null, `target_exam_date` date null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_learning_goals` add constraint `course_learning_goals_course_learning_profile_id_foreign` foreign key (`course_learning_profile_id`) references `course_learning_profiles` (`id`) on delete restrict;

alter table `course_learning_goals` add unique `course_goal_scale_unique`(`course_learning_profile_id`, `score_scale_code`);

alter table `course_learning_goals` add constraint `course_goal_scale_bounds_fk` foreign key (`score_scale_code`, `min_score`, `max_score`, `score_step`) references `learning_score_scales` (`code`, `min_score`, `max_score`, `score_step`) on delete restrict;

create table `course_instructors` (`course_id` bigint unsigned not null, `user_id` bigint unsigned not null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`course_id`, `user_id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_instructors` add constraint `course_instructors_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete restrict;

alter table `course_instructors` add constraint `course_instructors_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;

alter table `course_instructors` add unique `course_instructors_course_id_position_unique`(`course_id`, `position`);

create table `course_skills` (`course_id` bigint unsigned not null, `skill` enum('listening', 'reading', 'speaking', 'writing', 'grammar', 'vocabulary', 'pronunciation') not null, `position` int unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`course_id`, `skill`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `course_skills` add constraint `course_skills_course_id_foreign` foreign key (`course_id`) references `courses` (`id`) on delete cascade;

alter table `course_skills` add unique `course_skills_course_id_position_unique`(`course_id`, `position`);

alter table `courses` add `learning_outcomes` json null;

create table `exam_collections` (`id` bigint unsigned not null auto_increment primary key, `title` varchar(255) not null, `slug` varchar(255) not null, `publication_year` smallint unsigned null, `description` text null, `is_active` tinyint(1) not null default '1', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_collections` add unique `exam_collections_slug_unique`(`slug`);

alter table `exam_papers` add `exam_collection_id` bigint unsigned null;

alter table `exam_papers` add constraint `exam_papers_exam_collection_id_foreign` foreign key (`exam_collection_id`) references `exam_collections` (`id`) on delete restrict;

alter table `exam_papers` add index `exam_papers_exam_collection_id_index`(`exam_collection_id`);

alter table `exam_papers` add index `exam_latest_idx`(`status`, `published_at`);

create table `exam_tags` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `slug` varchar(255) not null, `kind` enum('question_type', 'topic', 'level') not null default 'question_type', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_tags` add unique `exam_tags_slug_unique`(`slug`);

create table `exam_section_tags` (`exam_section_id` bigint unsigned not null, `exam_tag_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`exam_section_id`, `exam_tag_id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `exam_section_tags` add constraint `exam_section_tags_exam_section_id_foreign` foreign key (`exam_section_id`) references `exam_sections` (`id`) on delete cascade;

alter table `exam_section_tags` add constraint `exam_section_tags_exam_tag_id_foreign` foreign key (`exam_tag_id`) references `exam_tags` (`id`) on delete restrict;

create table `lesson_exam_papers` (`lesson_id` bigint unsigned not null, `exam_paper_id` bigint unsigned not null, `position` int unsigned not null, `is_required` tinyint(1) not null default '0', `created_at` timestamp null, `updated_at` timestamp null, primary key (`lesson_id`, `exam_paper_id`)) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `lesson_exam_papers` add constraint `lesson_exam_papers_lesson_id_foreign` foreign key (`lesson_id`) references `lessons` (`id`) on delete restrict;

alter table `lesson_exam_papers` add constraint `lesson_exam_papers_exam_paper_id_foreign` foreign key (`exam_paper_id`) references `exam_papers` (`id`) on delete restrict;

alter table `lesson_exam_papers` add unique `lesson_exam_papers_lesson_id_position_unique`(`lesson_id`, `position`);

alter table `question_groups` add `response_format` varchar(50) null;

alter table `question_groups` add `answer_word_limit` smallint unsigned null;

alter table `question_groups` add `rendering_data` json null;

ALTER TABLE learning_score_scales ADD CONSTRAINT learning_score_scales_profile_check CHECK (min_score >= 0 AND max_score >= min_score AND score_step > 0);

ALTER TABLE course_learning_profiles ADD CONSTRAINT course_learning_profiles_profile_check CHECK ((consultation_opt_in = 0 AND consented_at IS NULL) OR (consultation_opt_in = 1 AND consented_at IS NOT NULL));

ALTER TABLE course_learning_goals ADD CONSTRAINT course_learning_goals_profile_check CHECK (score_step > 0 AND (current_score IS NOT NULL OR target_score IS NOT NULL) AND (current_score IS NULL OR (current_score BETWEEN min_score AND max_score AND ROUND(1.0 * (current_score - min_score) / score_step, 0) = 1.0 * (current_score - min_score) / score_step)) AND (target_score IS NULL OR (target_score BETWEEN min_score AND max_score AND ROUND(1.0 * (target_score - min_score) / score_step, 0) = 1.0 * (target_score - min_score) / score_step)));

ALTER TABLE course_instructors ADD CONSTRAINT course_instructors_profile_check CHECK (position > 0);

ALTER TABLE course_skills ADD CONSTRAINT course_skills_profile_check CHECK (position > 0);

ALTER TABLE lesson_exam_papers ADD CONSTRAINT lesson_exam_papers_profile_check CHECK (position > 0);

ALTER TABLE question_groups ADD CONSTRAINT question_groups_profile_check CHECK (answer_word_limit IS NULL OR answer_word_limit > 0);

create table `reference_pages` (`id` bigint unsigned not null auto_increment primary key, `provider` varchar(50) not null, `source_key` varchar(100) not null, `source_url` varchar(500) not null, `content_type` enum('course', 'exam') not null, `title` varchar(255) not null, `summary` text not null, `metadata` json null, `captured_at` timestamp not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `reference_pages` add unique `reference_pages_provider_source_key_unique`(`provider`, `source_key`);

alter table `reference_pages` add unique `reference_pages_source_url_unique`(`source_url`);

create table `reference_images` (`id` bigint unsigned not null auto_increment primary key, `reference_page_id` bigint unsigned not null, `asset_key` char(64) not null, `source_url` text not null, `local_path` varchar(255) not null, `alt_text` varchar(255) not null, `role` enum('cover', 'illustration') not null, `position` int unsigned not null, `mime_type` varchar(40) not null, `width` int unsigned not null, `height` int unsigned not null, `file_size` int unsigned not null, `content_sha256` char(64) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB;

alter table `reference_images` add constraint `reference_images_reference_page_id_foreign` foreign key (`reference_page_id`) references `reference_pages` (`id`) on delete cascade;

alter table `reference_images` add unique `reference_images_reference_page_id_asset_key_unique`(`reference_page_id`, `asset_key`);

alter table `reference_images` add unique `reference_images_reference_page_id_position_unique`(`reference_page_id`, `position`);

alter table `reference_images` add unique `reference_images_local_path_unique`(`local_path`);

-- PHAN 2: TAI KHOAN QUAN TRI MAU VA PHIEN BAN DATABASE
START TRANSACTION;
INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES ('Quản trị viên', 'admin@englishcenter.vn', '$2y$12$sNJK8GXLmmLISkNtnWXL9u7SlQfQBrsN0rKkH8WMiczyuYBqlDs1m', 'admin', 1, '2026-10-22 03:05:40', '2026-10-22 03:05:40');
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000001_create_users_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000002_create_teachers_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000003_create_students_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000004_create_courses_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000005_create_class_rooms_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000006_create_enrollments_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_01_01_000007_create_attendances_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_16_034412_enforce_training_data_rules', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_20_000001_add_expected_end_date_to_class_rooms_table', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_21_000001_create_online_learning_tables', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_21_000002_create_exam_tables', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_21_000003_create_flashcards_and_feedback_tables', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_21_000004_enforce_learning_data_rules', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_22_000001_create_learning_profile_tables', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_22_000002_extend_exam_catalog_and_lesson_links', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_22_000003_enforce_learning_profile_rules', 1);
INSERT INTO migrations (migration, batch) VALUES ('2026_10_22_000004_create_public_reference_tables', 1);
COMMIT;

-- PHAN 3: 5 BO CAU HOI VA DAP AN

-- BC Reads: 5 bo luyen, 60 cau hoi, 60 dap an, 63 lua chon.
-- Import vao database du an da co cac bang online (goi 37/39 bang).
-- Chi them noi dung; khong DROP/DELETE, khong chen tai khoan hay lich su hoc.
-- Nhap lai se bo qua ca bo de da ton tai theo slug, giu noi dung da bien tap.
-- Shantel Ivits (2015), BCcampus: BC Reads – Course Pack 6. CC BY 4.0.
-- https://opentextbc.ca/abealf6/ ; https://creativecommons.org/licenses/by/4.0/
-- Download for free from the B.C. Open Collection: https://collection.bccampus.ca/
-- Adapted blanks/choices; added Vietnamese instructions/explanations and spelling variants.
-- Chua thu import tren may chu MySQL/MariaDB that; da kiem thu seeder bang SQLite.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @BC_OLD_SQL_MODE = @@SESSION.sql_mode;
SET @BC_OLD_TIME_ZONE = @@SESSION.time_zone;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_AUTO_VALUE_ON_ZERO';
SET SESSION time_zone = '+00:00';
START TRANSACTION;
INSERT INTO `learning_categories` (`name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES ('Tiếng Anh nền tảng', 'english-foundation', NULL, 1, '2026-10-21 16:04:03', '2026-10-21 16:04:03') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);
SET @BC_CATEGORY = (SELECT id FROM learning_categories WHERE slug = 'english-foundation');
INSERT INTO `exam_collections` (`title`, `slug`, `publication_year`, `description`, `is_active`, `created_at`, `updated_at`) VALUES ('BC Reads — 5 bộ luyện ngữ pháp và từ vựng', 'bc-reads-english-practice', 2015, 'BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);
SET @BC_COLLECTION = (SELECT id FROM exam_collections WHERE slug = 'bc-reads-english-practice');

-- Bộ 01 — Đại từ chủ ngữ và tân ngữ
SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = 'bc-reads-01-pronouns');
INSERT INTO `exam_papers` (`learning_category_id`, `created_by`, `title`, `slug`, `description`, `skill`, `duration_seconds`, `status`, `published_at`, `created_at`, `updated_at`, `exam_collection_id`) SELECT @BC_CATEGORY, NULL, 'Bộ 01 — Đại từ chủ ngữ và tân ngữ', 'bc-reads-01-pronouns', '13 câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ Selected exercises and published answer keys from Chapters 1–2. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS/TOEIC or CEFR score conversion.', 'reading', 900, 'published', '2026-10-22 02:38:29', '2026-10-22 02:38:29', '2026-10-22 02:38:29', @BC_COLLECTION WHERE @BC_NEW_PAPER = 1;
SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = 'bc-reads-01-pronouns');
INSERT INTO `exam_sections` (`exam_paper_id`, `title`, `skill`, `position`, `duration_seconds`, `instructions`, `created_at`, `updated_at`) SELECT @BC_PAPER, 'Bộ 01 — Đại từ chủ ngữ và tân ngữ', 'reading', 1, NULL, 'Điền một đại từ vào mỗi chỗ trống. Câu 1–7 thay chủ ngữ; câu 8–13 dùng đại từ tân ngữ. Không phân biệt chữ hoa và chữ thường.', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);
INSERT INTO `question_groups` (`exam_section_id`, `title`, `position`, `instructions`, `passage`, `transcript`, `audio_path`, `image_path`, `created_at`, `updated_at`, `response_format`, `answer_word_limit`, `rendering_data`) SELECT @BC_SECTION, 'Pronoun Reference', 1, 'Điền một đại từ vào mỗi chỗ trống. Câu 1–7 thay chủ ngữ; câu 8–13 dùng đại từ tân ngữ. Không phân biệt chữ hoa và chữ thường.\n\nBC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/chapter-1/', 'Đại từ chủ ngữ: he, she, it, we, they. Đại từ tân ngữ: me, him, her, it, us, them. Chọn đại từ dựa vào người hoặc vật được nhắc đến và vị trí trong câu.', NULL, NULL, NULL, '2026-10-22 02:38:29', '2026-10-22 02:38:29', NULL, NULL, '{"source":{"title":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6","author":"Shantel Ivits","publisher":"BCcampus","year":2015,"url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","license":"CC BY 4.0","license_url":"https:\\/\\/creativecommons.org\\/licenses\\/by\\/4.0\\/","download_notice":"Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","attribution":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","adaptation":"Selected exercises and published answer keys from Chapters 1\\u20132. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS\\/TOEIC or CEFR score conversion.","checked_on":"2026-10-22"},"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/","exercise":"Pronoun Reference","items":[{"source_item":1,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":2,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":3,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":4,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":5,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":6,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":7,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":8,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":9,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":10,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":11,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":12,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":13,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"}]}' WHERE @BC_NEW_PAPER = 1;
SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);
INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 1, 'Tanya Tagaq is a singer from Nunavut. ___ performs a style of music called Inuit throat singing.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 1);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["she"]', 0, NULL, 'Theo đáp án gốc, She thay cho Tanya ở vị trí chủ ngữ. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 1. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 2, 'Taté BlackBear is from the Lakota Nation. ___ knows seven words in the Lakota language.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 2);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["he"]', 0, NULL, 'Theo đáp án gốc, He thay cho Taté ở vị trí chủ ngữ. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 2. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 3, 'Evan Adams and Adam Beach starred in a movie called Smoke Signals. ___ are both actors from Canada.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 3);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["they"]', 0, NULL, 'Hai người được thay bằng đại từ chủ ngữ They. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 3. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 4, 'Thomas King and I have something in common. ___ are both writers.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 4);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["we"]', 0, NULL, 'Thomas King and I gồm cả người nói nên dùng We. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 4. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 5, 'Louis Riel was the father of Manitoba. ___ was also the leader of the Métis people on the Canadian prairies.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 5);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["he"]', 0, NULL, 'Theo đáp án gốc, He thay cho Louis Riel. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 5. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 6, 'Coyote and Raven are found in many Aboriginal stories. ___ are famous for playing tricks on others.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 6);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["they"]', 0, NULL, 'Coyote and Raven là hai nhân vật nên dùng They. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 6. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 7, 'Sweetgrass is a sacred plant in First Nations cultures. ___ is known for its ability to heal and get rid of negative thoughts.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 7);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["it"]', 0, NULL, 'It thay cho danh từ số ít Sweetgrass. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 7. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 8, 'Jack ate the apple, even though it did not belong to ___.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 8);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["him"]', 0, NULL, 'Sau giới từ to cần đại từ tân ngữ; him chỉ Jack. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 8. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 9, 'The kids had to go to bed by 8:00 p.m. Their parents would not let ___ stay up any later.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 9);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["them"]', 0, NULL, 'Them là tân ngữ của let, thay cho the kids. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 9. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 10, 'The shirt fits me, but I probably won’t wear ___.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 10);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["it"]', 0, NULL, 'It thay cho the shirt và làm tân ngữ của wear. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 10. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 11, 'Billie and I are going to the movies. Do you want to come with ___?', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 11);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["us"]', 0, NULL, 'Sau with dùng đại từ tân ngữ us, thay cho Billie and I. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 11. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 12, 'I want to know how to get to Nanaimo. Will you tell ___?', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 12);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["me"]', 0, NULL, 'Me là đại từ tân ngữ tương ứng với I. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 12. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 13, 'Sue just got an iPhone. Sam gave it to ___.', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 13);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["her"]', 0, NULL, 'Theo đáp án gốc, her thay cho Sue sau giới từ to. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Pronoun Reference, item 13. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

-- Bộ 02 — Câu hoàn chỉnh và câu thiếu thành phần
SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = 'bc-reads-02-sentence-fragments');
INSERT INTO `exam_papers` (`learning_category_id`, `created_by`, `title`, `slug`, `description`, `skill`, `duration_seconds`, `status`, `published_at`, `created_at`, `updated_at`, `exam_collection_id`) SELECT @BC_CATEGORY, NULL, 'Bộ 02 — Câu hoàn chỉnh và câu thiếu thành phần', 'bc-reads-02-sentence-fragments', '14 câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ Selected exercises and published answer keys from Chapters 1–2. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS/TOEIC or CEFR score conversion.', 'reading', 900, 'published', '2026-10-22 02:38:29', '2026-10-22 02:38:29', '2026-10-22 02:38:29', @BC_COLLECTION WHERE @BC_NEW_PAPER = 1;
SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = 'bc-reads-02-sentence-fragments');
INSERT INTO `exam_sections` (`exam_paper_id`, `title`, `skill`, `position`, `duration_seconds`, `instructions`, `created_at`, `updated_at`) SELECT @BC_PAPER, 'Bộ 02 — Câu hoàn chỉnh và câu thiếu thành phần', 'reading', 1, NULL, 'Câu 1–9: chọn câu hoàn chỉnh hay câu chưa hoàn chỉnh. Câu 10–14: chọn thành phần còn thiếu.', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);
INSERT INTO `question_groups` (`exam_section_id`, `title`, `position`, `instructions`, `passage`, `transcript`, `audio_path`, `image_path`, `created_at`, `updated_at`, `response_format`, `answer_word_limit`, `rendering_data`) SELECT @BC_SECTION, 'Sentence Fragments', 1, 'Câu 1–9: chọn câu hoàn chỉnh hay câu chưa hoàn chỉnh. Câu 10–14: chọn thành phần còn thiếu.\n\nBC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/', 'Một câu hoàn chỉnh cần có chủ ngữ, động từ và ý trọn vẹn. Cụm từ riêng lẻ hoặc mệnh đề phụ đứng một mình thường chưa tạo thành câu hoàn chỉnh.', NULL, NULL, NULL, '2026-10-22 02:38:29', '2026-10-22 02:38:29', NULL, NULL, '{"source":{"title":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6","author":"Shantel Ivits","publisher":"BCcampus","year":2015,"url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","license":"CC BY 4.0","license_url":"https:\\/\\/creativecommons.org\\/licenses\\/by\\/4.0\\/","download_notice":"Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","attribution":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","adaptation":"Selected exercises and published answer keys from Chapters 1\\u20132. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS\\/TOEIC or CEFR score conversion.","checked_on":"2026-10-22"},"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/","exercise":"Sentence Fragments","items":[{"source_item":1,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":2,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":3,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":4,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":5,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":6,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":7,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":8,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":9,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":10,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":11,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":12,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":13,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":14,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"}]}' WHERE @BC_NEW_PAPER = 1;
SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);
INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 1, 'The photo on the wall.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 1);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Cụm danh từ này chưa có động từ chính. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 1. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 2, 'The car had a flat tire.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 2);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'The car là chủ ngữ, had là động từ và câu diễn đạt ý trọn vẹn. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 2. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 3, 'In a hurry.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 3);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Đây là cụm giới từ, chưa có chủ ngữ và động từ chính. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 3. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 4, 'Sleeping until noon.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 4);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Cụm này thiếu chủ ngữ và động từ chia thì hoàn chỉnh. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 4. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 5, 'I think I lost a $50.00 bill.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 5);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Câu có chủ ngữ, động từ và diễn đạt ý trọn vẹn. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 5. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 6, 'On Tuesday morning.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 6);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Đây chỉ là cụm chỉ thời gian. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 6. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 7, 'A sale on tomatoes.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 7);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Đây là cụm danh từ, chưa có động từ chính. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 7. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 8, 'Sofia’s ice cream melted in the sun.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 8);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Sofia’s ice cream là chủ ngữ, melted là động từ. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 8. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 9, 'We are cheering for the Toronto Blue Jays.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 9);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Complete sentence — Câu hoàn chỉnh', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Fragment — Câu chưa hoàn chỉnh', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'We là chủ ngữ, are cheering là cụm động từ hoàn chỉnh. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 9. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 10, 'What is missing? If you have a sunburn.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 10);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Subject — Chủ ngữ', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Verb — Động từ', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'C', 'Complete thought — Ý trọn vẹn', 3, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["C"]', 0, NULL, 'Mệnh đề bắt đầu bằng If cần mệnh đề chính để có ý trọn vẹn. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 10. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 11, 'What is missing? Will send you an email.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 11);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Subject — Chủ ngữ', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Verb — Động từ', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'C', 'Complete thought — Ý trọn vẹn', 3, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Chưa biết ai thực hiện hành động gửi email. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 11. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 12, 'What is missing? Since it is a holiday.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 12);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Subject — Chủ ngữ', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Verb — Động từ', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'C', 'Complete thought — Ý trọn vẹn', 3, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["C"]', 0, NULL, 'Mệnh đề phụ bắt đầu bằng Since cần mệnh đề chính. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 12. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 13, 'What is missing? The tiger at the zoo.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 13);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Subject — Chủ ngữ', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Verb — Động từ', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'C', 'Complete thought — Ý trọn vẹn', 3, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Cụm danh từ đã có chủ ngữ nhưng thiếu động từ. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 13. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 14, 'What is missing? When we walk the dog.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 14);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'Subject — Chủ ngữ', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'Verb — Động từ', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'C', 'Complete thought — Ý trọn vẹn', 3, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["C"]', 0, NULL, 'Mệnh đề bắt đầu bằng When cần thêm mệnh đề chính. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Sentence Fragments, item 14. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

-- Bộ 03 — Cấu tạo từ với tiền tố và hậu tố
SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = 'bc-reads-03-word-formation');
INSERT INTO `exam_papers` (`learning_category_id`, `created_by`, `title`, `slug`, `description`, `skill`, `duration_seconds`, `status`, `published_at`, `created_at`, `updated_at`, `exam_collection_id`) SELECT @BC_CATEGORY, NULL, 'Bộ 03 — Cấu tạo từ với tiền tố và hậu tố', 'bc-reads-03-word-formation', '8 câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/ Selected exercises and published answer keys from Chapters 1–2. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS/TOEIC or CEFR score conversion.', 'reading', 600, 'published', '2026-10-22 02:38:29', '2026-10-22 02:38:29', '2026-10-22 02:38:29', @BC_COLLECTION WHERE @BC_NEW_PAPER = 1;
SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = 'bc-reads-03-word-formation');
INSERT INTO `exam_sections` (`exam_paper_id`, `title`, `skill`, `position`, `duration_seconds`, `instructions`, `created_at`, `updated_at`) SELECT @BC_PAPER, 'Bộ 03 — Cấu tạo từ với tiền tố và hậu tố', 'reading', 1, NULL, 'Điền từ được tạo từ gợi ý trong ngoặc. Dùng tiền tố hoặc hậu tố phù hợp với nghĩa. Không phân biệt chữ hoa và chữ thường.', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);
INSERT INTO `question_groups` (`exam_section_id`, `title`, `position`, `instructions`, `passage`, `transcript`, `audio_path`, `image_path`, `created_at`, `updated_at`, `response_format`, `answer_word_limit`, `rendering_data`) SELECT @BC_SECTION, 'Word Patterns, Chapters 1–2', 1, 'Điền từ được tạo từ gợi ý trong ngoặc. Dùng tiền tố hoặc hậu tố phù hợp với nghĩa. Không phân biệt chữ hoa và chữ thường.\n\nBC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/', 'extra- diễn đạt vượt mức thông thường; -or chỉ người thực hiện; -tion thường tạo danh từ chỉ hành động hoặc quá trình; -al tạo tính từ; -ize tạo động từ.', NULL, NULL, NULL, '2026-10-22 02:38:29', '2026-10-22 02:38:29', NULL, NULL, '{"source":{"title":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6","author":"Shantel Ivits","publisher":"BCcampus","year":2015,"url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","license":"CC BY 4.0","license_url":"https:\\/\\/creativecommons.org\\/licenses\\/by\\/4.0\\/","download_notice":"Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","attribution":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","adaptation":"Selected exercises and published answer keys from Chapters 1\\u20132. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS\\/TOEIC or CEFR score conversion.","checked_on":"2026-10-22"},"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","exercise":"Word Patterns, Chapters 1\\u20132","items":[{"source_item":"Chapter 1, Vocabulary 11","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":"Chapter 1, Vocabulary 12","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":"Chapter 1, Vocabulary 13","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":"Chapter 1, Vocabulary 14","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":"Chapter 2, Vocabulary 11","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":"Chapter 2, Vocabulary 12","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":"Chapter 2, Vocabulary 13","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":"Chapter 2, Vocabulary 14","source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"}]}' WHERE @BC_NEW_PAPER = 1;
SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);
INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 1, 'One who invents (invent): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 1);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["inventor"]', 0, NULL, 'Thêm -or vào invent để tạo inventor: nhà phát minh. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Word Patterns, Chapters 1–2, item Chapter 1, Vocabulary 11. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 2, 'Related to a virus (virus): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 2);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["viral"]', 0, NULL, 'Viral là tính từ mang nghĩa liên quan đến virus. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Word Patterns, Chapters 1–2, item Chapter 1, Vocabulary 12. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 3, 'The act of combining (combine): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 3);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["combination"]', 0, NULL, 'Combination là danh từ chỉ sự kết hợp. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Word Patterns, Chapters 1–2, item Chapter 1, Vocabulary 13. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 4, 'Beyond ordinary (ordinary): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 4);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["extraordinary"]', 0, NULL, 'Tiền tố extra- tạo extraordinary: khác thường hoặc phi thường. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Word Patterns, Chapters 1–2, item Chapter 1, Vocabulary 14. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 5, 'The act of participating (participate): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 5);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["participation"]', 0, NULL, 'Participation là danh từ chỉ sự tham gia. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Word Patterns, Chapters 1–2, item Chapter 2, Vocabulary 11. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 6, 'To make categories (category): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 6);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["categorize","categorise"]', 0, NULL, 'Đáp án gốc: categorize. Chấp nhận thêm cách viết categorise. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Word Patterns, Chapters 1–2, item Chapter 2, Vocabulary 12. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 7, 'The act of identifying (identify): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 7);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["identification"]', 0, NULL, 'Identification là danh từ chỉ sự nhận diện hoặc xác định. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Word Patterns, Chapters 1–2, item Chapter 2, Vocabulary 13. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 8, 'To make a drama (drama): ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 8);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["dramatize","dramatise"]', 0, NULL, 'Đáp án gốc: dramatize. Chấp nhận thêm cách viết dramatise. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Word Patterns, Chapters 1–2, item Chapter 2, Vocabulary 14. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

-- Bộ 04 — Hòa hợp chủ ngữ và động từ
SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = 'bc-reads-04-subject-verb-agreement');
INSERT INTO `exam_papers` (`learning_category_id`, `created_by`, `title`, `slug`, `description`, `skill`, `duration_seconds`, `status`, `published_at`, `created_at`, `updated_at`, `exam_collection_id`) SELECT @BC_CATEGORY, NULL, 'Bộ 04 — Hòa hợp chủ ngữ và động từ', 'bc-reads-04-subject-verb-agreement', '15 câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ Selected exercises and published answer keys from Chapters 1–2. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS/TOEIC or CEFR score conversion.', 'reading', 900, 'published', '2026-10-22 02:38:29', '2026-10-22 02:38:29', '2026-10-22 02:38:29', @BC_COLLECTION WHERE @BC_NEW_PAPER = 1;
SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = 'bc-reads-04-subject-verb-agreement');
INSERT INTO `exam_sections` (`exam_paper_id`, `title`, `skill`, `position`, `duration_seconds`, `instructions`, `created_at`, `updated_at`) SELECT @BC_PAPER, 'Bộ 04 — Hòa hợp chủ ngữ và động từ', 'reading', 1, NULL, 'Chọn động từ phù hợp với chủ ngữ. Bộ này chọn 15 câu từ bài gốc; số câu nguồn được lưu riêng để đối chiếu.', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);
INSERT INTO `question_groups` (`exam_section_id`, `title`, `position`, `instructions`, `passage`, `transcript`, `audio_path`, `image_path`, `created_at`, `updated_at`, `response_format`, `answer_word_limit`, `rendering_data`) SELECT @BC_SECTION, 'Subject Verb Agreement (selected items)', 1, 'Chọn động từ phù hợp với chủ ngữ. Bộ này chọn 15 câu từ bài gốc; số câu nguồn được lưu riêng để đối chiếu.\n\nBC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/', 'Ở hiện tại đơn, chủ ngữ ngôi ba số ít thường đi với động từ thêm -s. Danh từ không đếm được thường dùng động từ số ít. Xác định danh từ chính trong cụm, ví dụ pair hoặc book.', NULL, NULL, NULL, '2026-10-22 02:38:29', '2026-10-22 02:38:29', NULL, NULL, '{"source":{"title":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6","author":"Shantel Ivits","publisher":"BCcampus","year":2015,"url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","license":"CC BY 4.0","license_url":"https:\\/\\/creativecommons.org\\/licenses\\/by\\/4.0\\/","download_notice":"Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","attribution":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","adaptation":"Selected exercises and published answer keys from Chapters 1\\u20132. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS\\/TOEIC or CEFR score conversion.","checked_on":"2026-10-22"},"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/","exercise":"Subject Verb Agreement (selected items)","items":[{"source_item":1,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":3,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":5,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":7,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":8,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":9,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":11,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":13,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":15,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":16,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":17,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":18,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":19,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":20,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"},{"source_item":21,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/whats-your-story\\/"}]}' WHERE @BC_NEW_PAPER = 1;
SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);
INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 1, 'This coffee ___ very strong.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 1);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'looks', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'look', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Coffee là danh từ không đếm được nên dùng looks. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 1. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 2, 'We ___ to get married this summer.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 2);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'plans', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'plan', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'We đi với động từ nguyên thể plan ở hiện tại đơn. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 3. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 3, 'Juan and Ted ___ two cats.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 3);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'has', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'have', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Juan and Ted là chủ ngữ số nhiều nên dùng have. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 5. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 4, 'My new pair of shoes ___ gone missing.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 4);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'have', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'has', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Danh từ chính là pair ở số ít nên dùng has. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 7. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 5, 'The water ___ so warm, I jumped right in.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 5);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'was', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'were', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Water là danh từ không đếm được, dùng was trong quá khứ. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 8. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 6, 'The fruit ___ ripe and ready to eat.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 6);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Trong câu nguồn, fruit được dùng như danh từ không đếm được nên dùng is. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 9. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 7, 'The caves ___ fun to explore.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 7);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Caves là danh từ số nhiều nên dùng are. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 11. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 8, 'Each chapter ___ better than the one before it.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 8);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'was', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'were', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Each chapter là chủ ngữ số ít nên dùng was. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 13. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 9, 'One of these tickets ___ the winner.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 9);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Danh từ chính là One, không phải tickets, nên dùng is. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 15. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 10, 'The buses ___ very crowded at around 5:00 p.m.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 10);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'get', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'gets', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Buses là chủ ngữ số nhiều nên dùng get. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 16. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 11, 'No one ___ sitting next to me on the plane.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 11);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'No one đi với động từ số ít is. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 17. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 12, 'Somebody ___ left me a phone message.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 12);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'have', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'has', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Somebody đi với trợ động từ số ít has. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 18. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 13, 'Everybody ___ late for the party.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 13);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'was', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'were', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Everybody đi với động từ số ít was. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 19. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 14, 'Stamps ___ expensive now.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 14);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["B"]', 0, NULL, 'Stamps là danh từ số nhiều nên dùng are. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 20. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 15, 'A book of stamps ___ $10.00.', 'single_choice', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 15);
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'A', 'is', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_options` (`question_id`, `label`, `content`, `position`, `created_at`, `updated_at`) SELECT @BC_QUESTION, 'B', 'are', 2, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["A"]', 0, NULL, 'Danh từ chính là book ở số ít nên dùng is. Source: https://opentextbc.ca/abealf6/chapter/whats-your-story/ — Subject Verb Agreement (selected items), item 21. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

-- Bộ 05 — Từ vựng theo định nghĩa
SET @BC_NEW_PAPER = (SELECT COUNT(*) = 0 FROM exam_papers WHERE slug = 'bc-reads-05-vocabulary');
INSERT INTO `exam_papers` (`learning_category_id`, `created_by`, `title`, `slug`, `description`, `skill`, `duration_seconds`, `status`, `published_at`, `created_at`, `updated_at`, `exam_collection_id`) SELECT @BC_CATEGORY, NULL, 'Bộ 05 — Từ vựng theo định nghĩa', 'bc-reads-05-vocabulary', '10 câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ Selected exercises and published answer keys from Chapters 1–2. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS/TOEIC or CEFR score conversion.', 'reading', 900, 'published', '2026-10-22 02:38:29', '2026-10-22 02:38:29', '2026-10-22 02:38:29', @BC_COLLECTION WHERE @BC_NEW_PAPER = 1;
SET @BC_PAPER = (SELECT id FROM exam_papers WHERE slug = 'bc-reads-05-vocabulary');
INSERT INTO `exam_sections` (`exam_paper_id`, `title`, `skill`, `position`, `duration_seconds`, `instructions`, `created_at`, `updated_at`) SELECT @BC_PAPER, 'Bộ 05 — Từ vựng theo định nghĩa', 'reading', 1, NULL, 'Chọn từ hoặc cụm từ trong ngân hàng từ để điền theo định nghĩa. Mỗi từ dùng một lần. Không phân biệt chữ hoa và chữ thường.', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_SECTION = (SELECT id FROM exam_sections WHERE exam_paper_id = @BC_PAPER AND position = 1);
INSERT INTO `question_groups` (`exam_section_id`, `title`, `position`, `instructions`, `passage`, `transcript`, `audio_path`, `image_path`, `created_at`, `updated_at`, `response_format`, `answer_word_limit`, `rendering_data`) SELECT @BC_SECTION, 'Vocabulary 1–10', 1, 'Chọn từ hoặc cụm từ trong ngân hàng từ để điền theo định nghĩa. Mỗi từ dùng một lần. Không phân biệt chữ hoa và chữ thường.\n\nBC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ https://creativecommons.org/licenses/by/4.0/ Source: https://opentextbc.ca/abealf6/chapter/chapter-1/', 'Ngân hàng từ: point of view; virus; online; ordinary; professional; invention; role; combine; recite; honour. Ngân hàng từ được bổ sung để làm bài độc lập, không cần mở bài đọc gốc.', NULL, NULL, NULL, '2026-10-22 02:38:29', '2026-10-22 02:38:29', NULL, NULL, '{"source":{"title":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6","author":"Shantel Ivits","publisher":"BCcampus","year":2015,"url":"https:\\/\\/opentextbc.ca\\/abealf6\\/","license":"CC BY 4.0","license_url":"https:\\/\\/creativecommons.org\\/licenses\\/by\\/4.0\\/","download_notice":"Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","attribution":"BC Reads: Adult Literacy Fundamental English \\u2013 Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https:\\/\\/collection.bccampus.ca\\/","adaptation":"Selected exercises and published answer keys from Chapters 1\\u20132. Converted to blanks and choice questions; instructions and explanations in Vietnamese added. Original source item numbers are retained. No official IELTS\\/TOEIC or CEFR score conversion.","checked_on":"2026-10-22"},"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/","exercise":"Vocabulary 1\\u201310","items":[{"source_item":1,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":2,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":3,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":4,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":5,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":6,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":7,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":8,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":9,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"},{"source_item":10,"source_url":"https:\\/\\/opentextbc.ca\\/abealf6\\/chapter\\/chapter-1\\/"}]}' WHERE @BC_NEW_PAPER = 1;
SET @BC_GROUP = (SELECT id FROM question_groups WHERE exam_section_id = @BC_SECTION AND position = 1);
INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 1, 'A way of seeing and understanding the world: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 1);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["point of view"]', 0, NULL, 'Point of view nghĩa là quan điểm hoặc cách nhìn nhận thế giới. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 1. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 2, 'An extremely small living thing that can spread from one animal to another and can cause illness: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 2);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["virus"]', 0, NULL, 'Đáp án nguồn là virus. Đây là bài nhận diện từ vựng theo định nghĩa của tài liệu. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 2. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 3, 'Connected to the Internet: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 3);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["online"]', 0, NULL, 'Online nghĩa là trực tuyến, có kết nối Internet. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 3. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 4, 'Normal or usual: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 4);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["ordinary"]', 0, NULL, 'Ordinary nghĩa là thông thường hoặc bình thường. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 4. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 5, 'Someone who has a lot of experience using a skill for their job: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 5);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["professional"]', 0, NULL, 'Professional trong định nghĩa này chỉ người có chuyên môn nghề nghiệp. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 5. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 6, 'Something that is created by people: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 6);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["invention"]', 0, NULL, 'Trong ngân hàng từ, invention là từ chỉ phát minh. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 6. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 7, 'The part or job someone has in a family, community, or group: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 7);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["role"]', 0, NULL, 'Role nghĩa là vai trò của một người trong tập thể. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 7. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 8, 'To cause two or more things to be together: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 8);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["combine"]', 0, NULL, 'Combine nghĩa là kết hợp hai hoặc nhiều thứ. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 8. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 9, 'To say something out loud for an audience: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 9);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["recite"]', 0, NULL, 'Recite nghĩa là đọc hoặc kể thành tiếng trước người nghe. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 9. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

INSERT INTO `questions` (`question_group_id`, `exam_section_id`, `position`, `prompt`, `type`, `max_points`, `created_at`, `updated_at`) SELECT @BC_GROUP, @BC_SECTION, 10, 'To show respect for someone or something: ___', 'short_answer', 1, '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;
SET @BC_QUESTION = (SELECT id FROM questions WHERE question_group_id = @BC_GROUP AND position = 10);
INSERT INTO `question_answer_keys` (`question_id`, `accepted_answers`, `case_sensitive`, `rubric`, `explanation`, `created_at`, `updated_at`) SELECT @BC_QUESTION, '["honour","honor"]', 0, NULL, 'Đáp án gốc là honour; chấp nhận thêm cách viết Mỹ honor. Source: https://opentextbc.ca/abealf6/chapter/chapter-1/ — Vocabulary 1–10, item 10. BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0. Download for free from the B.C. Open Collection: https://collection.bccampus.ca/', '2026-10-22 02:38:29', '2026-10-22 02:38:29' WHERE @BC_NEW_PAPER = 1;

COMMIT;
SET SESSION time_zone = @BC_OLD_TIME_ZONE;
SET SESSION sql_mode = @BC_OLD_SQL_MODE;

SET SESSION time_zone = @EC_FRESH_OLD_TIME_ZONE;
SET SESSION sql_mode = @EC_FRESH_OLD_SQL_MODE;
SELECT COUNT(*) AS so_bang FROM information_schema.tables WHERE table_schema=DATABASE() AND table_type='BASE TABLE';
SELECT COUNT(*) AS so_cau FROM questions;
SELECT COUNT(*) AS so_dap_an FROM question_answer_keys;
