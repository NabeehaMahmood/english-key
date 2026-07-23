-- Redesigns the Notes page from a flat request-only list into a class (9-12)
-- x subject (admin-editable) library of sample PDFs, each with its own
-- draft/publish workflow. Replaces the old flat `notes` table entirely.
-- (schema.sql itself DROPs and recreates the whole database, so it isn't
-- safe to re-run against a live site -- run this migration once against
-- the live/staging DB instead.)

CREATE TABLE note_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL UNIQUE,
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1F2B54',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_subjects (name, slug, accent_color, sort_order) VALUES
('English', 'english', '#1B7FB4', 1),
('Urdu', 'urdu', '#E56A19', 2),
('Islamiat', 'islamiat', '#7A3FD0', 3),
('Tarjuma-tul-Quran', 'tarjuma-tul-quran', '#1F2B54', 4);

CREATE TABLE note_samples (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL,
    subject_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    chapter_label VARCHAR(40),
    content_type ENUM('prose', 'poetry', 'other') NOT NULL DEFAULT 'other',
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_note_samples_subject FOREIGN KEY (subject_id) REFERENCES note_subjects(id) ON DELETE RESTRICT,
    INDEX idx_note_samples_filter (class_level, subject_id, status)
) ENGINE=InnoDB;

-- The old `notes` rows were request-only links (no PDFs), so there is
-- nothing worth carrying over into note_samples -- just drop the table.
DROP TABLE IF EXISTS notes;
