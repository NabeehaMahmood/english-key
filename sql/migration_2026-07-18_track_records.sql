-- ---------------------------------------------------------------------
-- Migration: "Proven Track Record" as its own reusable entity.
--
-- Previously the achiever band shown on Home ("Proven Track Record"),
-- Testimonials ("Alumnus Corner") and Alumni (top band) each queried the
-- `alumni` table directly and duplicated the card markup in three files.
-- This migration introduces a dedicated `track_records` table (managed
-- under Admin -> Homepage Track Record) that all three pages now pull
-- from via the shared renderTrackRecordCard() helper in
-- includes/functions.php, so adding/editing/hiding/reordering a record
-- once updates every page that shows it.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already includes this.
--
-- Safe to re-run: table creation uses IF NOT EXISTS, and the one-time
-- backfill from `alumni` only inserts rows the first time (guarded by a
-- row-count check), so re-running this file is a no-op after the first
-- successful run.
--
-- IMPORTANT (encoding): if this file's comments contain non-ASCII
-- characters, import it through phpMyAdmin (handles UTF-8 correctly by
-- default), or via the mysql CLI with --default-character-set=utf8mb4.
-- ---------------------------------------------------------------------

USE academy;

CREATE TABLE IF NOT EXISTS track_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(10) NOT NULL,
    position_badge VARCHAR(60) NOT NULL DEFAULT '1st Position',
    student_name VARCHAR(150) NOT NULL,
    achievement_title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- One-time backfill: carry over the 3 achiever-band rows from `alumni`
-- (the entries with no story text, previously used for this band) so the
-- site looks identical immediately after migrating. Only runs if
-- track_records is still empty, so it's safe to re-run this file.
INSERT INTO track_records (year, position_badge, student_name, achievement_title, sort_order)
SELECT RIGHT(a.batch_info, 4), '1ST POSITION', a.name, a.achievement, a.sort_order
FROM alumni a
WHERE (a.story IS NULL OR a.story = '')
  AND NOT EXISTS (SELECT 1 FROM track_records)
ORDER BY a.sort_order, a.id
LIMIT 3;
