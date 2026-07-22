-- Two changes:
-- 1. note_resource_groups gets a nav_style column: 'own_tab' groups (MDCAT,
--    Summer Camp) become their own button in the primary Class nav on the
--    Notes page, keyed by slug instead of a class number; 'others' groups
--    (MCQ Practice Bank) share one "Others" tab.
-- 2. MCQs that clearly belong to a specific class move from
--    note_extra_resources into note_samples -- MCQs are notes too, they
--    just weren't classified that way originally. Ambiguous SSC/HSSC-only
--    files (the source doesn't commit to one specific class) get a row in
--    both classes of that tier, reusing the same uploaded file. Only
--    genuinely cross-syllabus/full-book MCQ sets stay in the Others group.

ALTER TABLE note_resource_groups
  ADD COLUMN nav_style ENUM('own_tab', 'others') NOT NULL DEFAULT 'others' AFTER cta_link;

UPDATE note_resource_groups SET nav_style = 'own_tab' WHERE slug IN ('mdcat-english-prep', 'summer-camp');
UPDATE note_resource_groups SET nav_style = 'others' WHERE slug = 'mcq-practice-bank';

-- sort_order also drives primary-nav position (see notes.php), on the same
-- scale note_classes.sort_order uses for Classes 9-12 (1-4) -- push these
-- well above that range so the default nav is Class 9-12, then MDCAT,
-- Summer Camp, then Others, without colliding with a class's sort_order
-- and interleaving unintentionally.
UPDATE note_resource_groups SET sort_order = 10 WHERE slug = 'mdcat-english-prep';
UPDATE note_resource_groups SET sort_order = 11 WHERE slug = 'summer-camp';
UPDATE note_resource_groups SET sort_order = 20 WHERE slug = 'mcq-practice-bank';

-- Move classifiable MCQ files into note_samples (English subject), then
-- delete them from note_extra_resources. Titles match exactly what
-- resources/import-notes.php produced for these files.
INSERT INTO note_samples (class_level, subject_id, title, file_path, content_type, sort_order, status)
SELECT c.class_level, (SELECT id FROM note_subjects WHERE slug = 'english'), r.title, r.file_path, 'other', 500, r.status
FROM note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id AND g.slug = 'mcq-practice-bank'
CROSS JOIN (SELECT 11 AS class_level) c
WHERE r.title = '11th MCQs Grand Check';

INSERT INTO note_samples (class_level, subject_id, title, file_path, content_type, sort_order, status)
SELECT c.class_level, (SELECT id FROM note_subjects WHERE slug = 'english'), r.title, r.file_path, 'other', 500, r.status
FROM note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id AND g.slug = 'mcq-practice-bank'
CROSS JOIN (SELECT 12 AS class_level) c
WHERE r.title IN ('12th English 100 MCQs 20 wise', '12th MCQS Grand Check');

INSERT INTO note_samples (class_level, subject_id, title, file_path, content_type, sort_order, status)
SELECT c.class_level, (SELECT id FROM note_subjects WHERE slug = 'english'), r.title, r.file_path, 'other', 500, r.status
FROM note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id AND g.slug = 'mcq-practice-bank'
CROSS JOIN (SELECT 11 AS class_level UNION SELECT 12) c
WHERE r.title IN (
    'HSSC English 50 MCQs P.1', 'HSSC English 50 MCQs P.2', 'HSSC English 100 MCQs P.3',
    'HSSC Grand Objective Test EnglishKeys Academy', 'HSSC Parts of speech Bootcamp test 01 100 MCQS',
    'Literary Devices MCQs for HSSC'
);

INSERT INTO note_samples (class_level, subject_id, title, file_path, content_type, sort_order, status)
SELECT c.class_level, (SELECT id FROM note_subjects WHERE slug = 'english'), r.title, r.file_path, 'other', 500, r.status
FROM note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id AND g.slug = 'mcq-practice-bank'
CROSS JOIN (SELECT 9 AS class_level UNION SELECT 10) c
WHERE r.title IN (
    'SSC English GOT Part 01', 'SSC English GOT Part 02', 'SSC Parts of speech Bootcamp test 01 100 MCQS'
);

DELETE r FROM note_extra_resources r
JOIN note_resource_groups g ON g.id = r.group_id AND g.slug = 'mcq-practice-bank'
WHERE r.title IN (
    '11th MCQs Grand Check', '12th English 100 MCQs 20 wise', '12th MCQS Grand Check',
    'HSSC English 50 MCQs P.1', 'HSSC English 50 MCQs P.2', 'HSSC English 100 MCQs P.3',
    'HSSC Grand Objective Test EnglishKeys Academy', 'HSSC Parts of speech Bootcamp test 01 100 MCQS',
    'Literary Devices MCQs for HSSC',
    'SSC English GOT Part 01', 'SSC English GOT Part 02', 'SSC Parts of speech Bootcamp test 01 100 MCQS'
);
