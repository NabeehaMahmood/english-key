-- Adds the Student Course Handout admin module: a single admin-managed PDF
-- resource behind the "View Course Outline" / "Download Course Outline"
-- buttons on the public Courses page. Previously those two buttons pointed
-- at site_settings keys (course_outline_view_url / course_outline_download_url)
-- that were never actually wired up to any admin UI and defaulted to '#'.
-- This table replaces that dead placeholder with a real upload-backed
-- resource; no existing table is altered.
CREATE TABLE IF NOT EXISTS course_handouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL DEFAULT 'Course Handout',
    description VARCHAR(500),
    file_path VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
