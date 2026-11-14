-- ============================================================================
-- SCMIPS Gallery Admin Panel - Database Schema Initialization
-- Database: scmips_db
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `scmips_db` 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `scmips_db`;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Gallery Sessions Table
CREATE TABLE IF NOT EXISTS `gallery_sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_name` VARCHAR(100) NOT NULL,            -- e.g. "Session 2021-2022"
    `academic_year` VARCHAR(20) NOT NULL UNIQUE,      -- e.g. "2021-2022"
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sessions_year` (`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Gallery Events Table
CREATE TABLE IF NOT EXISTS `gallery_events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,                    -- e.g. "Annual Function"
    `slug` VARCHAR(220) NOT NULL,
    `description` TEXT NULL,
    `event_date` DATE NULL,
    `cover_image` VARCHAR(255) NULL,
    `is_published` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_events_session` (`session_id`),
    INDEX `idx_events_published` (`is_published`),
    CONSTRAINT `fk_events_session`
        FOREIGN KEY (`session_id`) REFERENCES `gallery_sessions` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Gallery Images Table
CREATE TABLE IF NOT EXISTS `gallery_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT UNSIGNED NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,                -- e.g. "img_65fa89b21a8d9_1710928306.jpg"
    `file_path` VARCHAR(255) NOT NULL,                -- e.g. "admin/uploads/gallery/..."
    `original_name` VARCHAR(255) NOT NULL,            -- e.g. "DSC_0045.JPG"
    `file_size` INT UNSIGNED NOT NULL DEFAULT 0,      -- In bytes
    `mime_type` VARCHAR(50) NOT NULL,                 -- e.g. "image/jpeg"
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_published` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_images_event` (`event_id`),
    INDEX `idx_images_published` (`is_published`),
    CONSTRAINT `fk_images_event`
        FOREIGN KEY (`event_id`) REFERENCES `gallery_events` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Initial Default Admin Seed
-- Username: admin
-- Password: Admin@123
-- ----------------------------------------------------------------------------
INSERT INTO `admins` (`username`, `password_hash`, `full_name`, `email`)
VALUES (
    'admin',
    '$2y$10$o7i9t0h3F5QJBwguDJI96eRZjnDKMDv2nXqs5a30/6C10jvJzOWCC',
    'System Administrator',
    'admin@scmips.in'
)
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);
