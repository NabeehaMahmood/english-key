-- ---------------------------------------------------------------------
-- Courses page conversion to match the client-approved html folders/courses.html.
-- Adds 2 nullable columns to the existing `courses` table (no new table):
--   programme_group - accordion category label for 'programme' rows
--                      (e.g. "Full Syllabus", "Exam Prep"). NULL = falls
--                      back to a single "Programmes" group in courses.php.
--   modules          - optional 2-module curriculum breakdown shown on a
--                       'featured' course's detail card. Format:
--                       "Label|Title|bullet1\nbullet2" blocks separated by
--                       a line containing only "---". Empty/NULL = the
--                       modules block simply doesn't render.
-- schedule_info's existing free-text convention is extended (not changed
-- in shape) to optionally hold "Label:Value|Label:Value" pairs, parsed
-- into the featured card's schedule grid; a plain old-style sentence still
-- renders fine as a single cell.
--
-- Safe to re-run. Import with UTF-8 (phpMyAdmin default, or
-- `mysql --default-character-set=utf8mb4`).
-- ---------------------------------------------------------------------

USE academy;

ALTER TABLE courses
    ADD COLUMN IF NOT EXISTS programme_group VARCHAR(80) NULL AFTER category,
    ADD COLUMN IF NOT EXISTS modules TEXT NULL AFTER highlights;

-- One-time backfill matching the reference's own grouping, only touching
-- rows that don't already have a group set (safe to re-run).
UPDATE courses SET programme_group = 'Full Syllabus'
  WHERE category = 'programme' AND programme_group IS NULL
  AND slug IN ('summer-camp', 'bootcamp-01', 'bootcamp-02', 'bootcamp-03');
UPDATE courses SET programme_group = 'Exam Prep'
  WHERE category = 'programme' AND programme_group IS NULL
  AND slug IN ('mdcat-nums-english-prep', 'full-length-papers');
UPDATE courses SET programme_group = 'Islamiat & Quran'
  WHERE category = 'programme' AND programme_group IS NULL
  AND slug IN ('deen-camp');
UPDATE courses SET programme_group = 'Marathons & Crash'
  WHERE category = 'programme' AND programme_group IS NULL
  AND slug IN ('exam-marathons', 'crash-courses');

-- Backfill the featured course's schedule_info to the new Label:Value
-- convention and add its curriculum modules, matching the reference
-- exactly. Only touches the one known seed row, only if still unset.
UPDATE courses SET
    schedule_info = 'Starts:06 July 2026|Ends:31 July 2026|Schedule:Monday-Friday|Time:07:00-09:00 PM (PKT)|Sessions:20 live, 2 hours each',
    modules = 'Days 01-10 - Module 1|English Grammar|Nouns, Pronouns, Verbs & Verbals\nAdverbs, Adjectives, Prepositions\nArticles, Phrases & Clauses\nTenses, Narration, Voices\nError Correction\n---\nDays 11-20 - Module 2|Creative Writing|Paragraph, Essay & Narrative Writing\nReport, Application & Letter Writing\nComprehension & Poetry Analysis'
WHERE slug = 'summer-intensive-2026' AND (modules IS NULL OR modules = '');
