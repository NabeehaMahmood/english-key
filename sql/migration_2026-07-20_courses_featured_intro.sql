-- ---------------------------------------------------------------------
-- Migration: Featured section intro copy on the Courses page.
--
-- The redesigned Featured section (courses.php, matching the approved
-- html folders/client_courses_final.html) has one section-level heading
-- and description above the grid of featured-course cards, separate from
-- any single course's own title/description. Adds a `courses/
-- featured_intro` content_blocks row (format: "Title|Description",
-- editable under Admin -> Page Content) so that copy isn't hardcoded.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already includes this.
-- Safe to re-run: INSERT ... ON DUPLICATE KEY UPDATE.
-- ---------------------------------------------------------------------

USE academy;

INSERT INTO content_blocks (page_slug, block_key, content) VALUES
    ('courses', 'featured_intro', 'English Language Summer Course, Summer Intensive 2026|Equally beneficial for all boards from Class 8th onwards. Covers essential topics to build a solid base in Grammar and Creative Writing.')
ON DUPLICATE KEY UPDATE content = VALUES(content);
