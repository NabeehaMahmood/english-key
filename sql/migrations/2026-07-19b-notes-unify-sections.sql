-- Merges note_classes and note_resource_groups into one note_sections
-- table. Both were "a tab in the primary nav on the Notes page" -- classes
-- (with subjects underneath) and resource groups (flat resource lists,
-- e.g. MDCAT/Summer Camp) -- but lived in separate tables/admin screens,
-- which meant MDCAT/Summer Camp didn't show up anywhere near "Manage
-- Classes" even though they sit in the exact same nav bar on the public
-- page. One table now covers both (section_type = 'class' or 'group'),
-- with one shared sort_order scale for primary-nav position.

CREATE TABLE note_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_type ENUM('class', 'group') NOT NULL DEFAULT 'class',
    class_level TINYINT UNSIGNED NULL UNIQUE,
    slug VARCHAR(120) NULL UNIQUE,
    label VARCHAR(80) NOT NULL,
    exam_label VARCHAR(40),
    description VARCHAR(255),
    icon_key VARCHAR(40) NOT NULL DEFAULT 'document',
    cta_label VARCHAR(80) NOT NULL DEFAULT 'Enroll & Get Complete Notes',
    cta_link VARCHAR(255) NOT NULL DEFAULT 'courses.php',
    nav_style ENUM('own_tab', 'others') NOT NULL DEFAULT 'own_tab',
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1E2A66',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_sections (section_type, class_level, label, exam_label, accent_color, sort_order, is_active)
SELECT 'class', class_level, label, exam_label, accent_color, sort_order, is_active FROM note_classes;

INSERT INTO note_sections (section_type, slug, label, description, icon_key, cta_label, cta_link, nav_style, sort_order, is_active)
SELECT 'group', slug, name, description, icon_key, cta_label, cta_link, 'others', sort_order, is_active FROM note_resource_groups;

-- Repoint note_extra_resources at note_sections (new ids won't match the
-- old note_resource_groups ids), then drop the old FK/column/table.
ALTER TABLE note_extra_resources ADD COLUMN section_id INT NULL AFTER group_id;

UPDATE note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id
JOIN note_sections s ON s.section_type = 'group' AND s.slug = g.slug
SET r.section_id = s.id;

ALTER TABLE note_extra_resources DROP FOREIGN KEY fk_note_extra_resources_group;
-- Dropping group_id leaves the old (group_id, status) index degraded to
-- just (status) rather than removed -- drop it explicitly before adding
-- the replacement, same name, on (section_id, status).
ALTER TABLE note_extra_resources DROP INDEX idx_note_extra_resources_filter;
ALTER TABLE note_extra_resources DROP COLUMN group_id;
ALTER TABLE note_extra_resources MODIFY COLUMN section_id INT NOT NULL;
ALTER TABLE note_extra_resources ADD CONSTRAINT fk_note_extra_resources_section
    FOREIGN KEY (section_id) REFERENCES note_sections(id) ON DELETE CASCADE;
ALTER TABLE note_extra_resources ADD INDEX idx_note_extra_resources_filter (section_id, status);

DROP TABLE note_resource_groups;
DROP TABLE note_classes;
