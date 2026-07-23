-- Notes page, second section: resources that aren't shaped like Class x
-- Subject (MCQ practice banks, MDCAT/entry-test prep, seasonal programme
-- booklets, ...). Each group is admin-creatable/extensible, and its own
-- CTA can point at whichever course actually sells that resource instead
-- of the generic "browse courses" link.

CREATE TABLE note_resource_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description VARCHAR(255),
    icon_key VARCHAR(40) NOT NULL DEFAULT 'document',
    cta_label VARCHAR(80) NOT NULL DEFAULT 'Enroll & Get Complete Notes',
    cta_link VARCHAR(255) NOT NULL DEFAULT 'courses.php',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_resource_groups (name, slug, description, icon_key, cta_label, cta_link, sort_order) VALUES
('MCQ Practice Bank', 'mcq-practice-bank', 'Cross-topic MCQ sets and grand objective tests for extra practice beyond your class notes.', 'list', 'Enroll & Get Complete Notes', 'courses.php', 1),
('MDCAT English Prep', 'mdcat-english-prep', 'Daily-drill vocabulary and grammar for the English portion of MDCAT/NUMS entry tests.', 'lightning', 'Enroll in MDCAT English Prep', 'courses.php#programmes', 2),
('Summer Camp', 'summer-camp', 'Foundation grammar and writing booklets from the Summer Camp programme, Class 8th onwards.', 'compass', 'Enroll in Summer Camp', 'courses.php#programmes', 3);

CREATE TABLE note_extra_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_note_extra_resources_group FOREIGN KEY (group_id) REFERENCES note_resource_groups(id) ON DELETE CASCADE,
    INDEX idx_note_extra_resources_filter (group_id, status)
) ENGINE=InnoDB;
