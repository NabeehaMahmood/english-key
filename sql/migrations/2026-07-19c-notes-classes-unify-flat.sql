-- Drops the class/group distinction entirely -- MDCAT English Prep, Summer
-- Camp, MCQ Practice Bank, and a new "Others" class are now just classes
-- like Class 9-12, each with its own admin-assigned class_level and its
-- own nav tab (no more shared "Others" bucket via nav_style). Classes
-- without subjects (has_subjects = 0) list note_samples directly with
-- subject_id left NULL, so there's one content table (note_samples) and
-- one registry table (note_classes) for everything, not two of each.

CREATE TABLE note_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL UNIQUE,
    label VARCHAR(80) NOT NULL,
    has_subjects TINYINT(1) NOT NULL DEFAULT 1,
    exam_label VARCHAR(40),
    description VARCHAR(255),
    icon_key VARCHAR(40) NOT NULL DEFAULT 'document',
    cta_label VARCHAR(80) NOT NULL DEFAULT 'Enroll & Get Complete Notes',
    cta_link VARCHAR(255) NOT NULL DEFAULT 'courses.php',
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1E2A66',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_classes (class_level, label, has_subjects, exam_label, accent_color, sort_order, is_active)
SELECT class_level, label, 1, exam_label, accent_color, sort_order, is_active
FROM note_sections WHERE section_type = 'class';

-- Existing group-type sections get real class_level numbers, continuing
-- past the real academic classes (13, 14, 15 in slug order below).
SET @mdcat := (SELECT id FROM note_sections WHERE slug = 'mdcat-english-prep');
SET @summer := (SELECT id FROM note_sections WHERE slug = 'summer-camp');
SET @mcq := (SELECT id FROM note_sections WHERE slug = 'mcq-practice-bank');

INSERT INTO note_classes (class_level, label, has_subjects, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active)
SELECT 13, label, 0, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active FROM note_sections WHERE id = @mdcat;
INSERT INTO note_classes (class_level, label, has_subjects, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active)
SELECT 14, label, 0, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active FROM note_sections WHERE id = @summer;
INSERT INTO note_classes (class_level, label, has_subjects, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active)
SELECT 15, label, 0, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active FROM note_sections WHERE id = @mcq;

-- A new, empty "Others" class for anything that doesn't fit elsewhere.
INSERT INTO note_classes (class_level, label, has_subjects, description, icon_key, cta_label, cta_link, sort_order) VALUES
(16, 'Others', 0, 'Anything else that does not fit a specific class or subject.', 'folder', 'Enroll & Get Complete Notes', 'courses.php', 30);

-- Move note_extra_resources rows into note_samples (subject_id NULL),
-- matching each old section to its new class_level via the @mdcat/etc ids
-- captured above.
ALTER TABLE note_samples MODIFY COLUMN subject_id INT NULL;

INSERT INTO note_samples (class_level, subject_id, title, chapter_label, content_type, description, file_path, sort_order, status)
SELECT 13, NULL, title, NULL, 'other', description, file_path, sort_order, status FROM note_extra_resources WHERE section_id = @mdcat;
INSERT INTO note_samples (class_level, subject_id, title, chapter_label, content_type, description, file_path, sort_order, status)
SELECT 14, NULL, title, NULL, 'other', description, file_path, sort_order, status FROM note_extra_resources WHERE section_id = @summer;
INSERT INTO note_samples (class_level, subject_id, title, chapter_label, content_type, description, file_path, sort_order, status)
SELECT 15, NULL, title, NULL, 'other', description, file_path, sort_order, status FROM note_extra_resources WHERE section_id = @mcq;

DROP TABLE note_extra_resources;
DROP TABLE note_sections;
