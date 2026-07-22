-- Classes (9-12) become admin-editable, same pattern as note_subjects --
-- label, exam badge, accent color, and a sort_order that also drives their
-- position in the primary Class nav on the Notes page (interleaved with
-- resource-group tabs, see notes.php). note_samples.class_level and
-- note_class_subjects.class_level keep storing the raw class number
-- directly rather than switching to a foreign key -- note_classes.class_level
-- is just the canonical lookup key, kept unique.

CREATE TABLE note_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL UNIQUE,
    label VARCHAR(40) NOT NULL,
    exam_label VARCHAR(40),
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1E2A66',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_classes (class_level, label, exam_label, accent_color, sort_order) VALUES
(9, 'Class 9', 'SSC-I', '#3D68B0', 1),
(10, 'Class 10', 'SSC-II', '#E56A19', 2),
(11, 'Class 11', 'HSSC-I', '#5B2BA6', 3),
(12, 'Class 12', 'HSSC-II', '#1E2A66', 4);
