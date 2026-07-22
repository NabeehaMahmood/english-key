-- Makes the Notes page's subject list vary per class instead of one global
-- set applied to all four classes. Adds `note_class_subjects` (which
-- subjects are enabled for which class, admin-editable) and seeds it to
-- match the real curriculum: Islamiat and Tarjuma-tul-Quran are only
-- examined in Classes 9 & 11, Pakistan Studies only in Classes 10 & 12,
-- and 11th also offers English Elective alongside the compulsory English
-- (NC) track. A class+subject pairing shows on the public page even with
-- zero samples uploaded -- no free preview doesn't mean the notes don't
-- exist, students still get them by enrolling.

CREATE TABLE note_class_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL,
    subject_id INT NOT NULL,
    CONSTRAINT fk_note_class_subjects_subject FOREIGN KEY (subject_id) REFERENCES note_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_note_class_subject (class_level, subject_id)
) ENGINE=InnoDB;

INSERT INTO note_subjects (name, slug, accent_color, sort_order, is_active) VALUES
('English Elective', 'english-elective', '#1B9E6B', 5, 1),
('Pakistan Studies', 'pakistan-studies', '#2E8B57', 6, 1);

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 9 AS class_level UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) c
WHERE s.slug IN ('english', 'urdu');

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 9 AS class_level UNION SELECT 11) c
WHERE s.slug IN ('islamiat', 'tarjuma-tul-quran');

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 10 AS class_level UNION SELECT 12) c
WHERE s.slug = 'pakistan-studies';

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT 11, id FROM note_subjects WHERE slug = 'english-elective';
