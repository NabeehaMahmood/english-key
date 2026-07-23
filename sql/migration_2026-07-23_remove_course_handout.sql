-- ---------------------------------------------------------------------
-- Removes the Student Course Handout feature (single downloadable PDF
-- behind "View/Download Course Outline" on the public Courses page,
-- managed in Admin -> Course Handout). The feature and its admin screen
-- have been removed entirely from the codebase; this drops the table
-- that backed it. Delete assets/uploads/handouts/ manually afterwards
-- if you want to reclaim the disk space - nothing references it anymore.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it never creates this table.
-- ---------------------------------------------------------------------

USE academy;

DROP TABLE IF EXISTS course_handouts;
