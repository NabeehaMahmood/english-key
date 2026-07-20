-- ---------------------------------------------------------------------
-- Migration: Qualification field for Teachers (About page Faculty cards).
--
-- The redesigned About page (matching the approved Courses page design
-- language) shows a Founder highlight plus a grid of Faculty cards, each
-- with Photo / Name / Designation / Qualification / short description.
-- The `teachers` table already had everything except Qualification -
-- this adds that one column. Existing rows are backfilled from the first
-- line of their `credentials` list so the field isn't blank.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already includes this.
-- Safe to re-run: ADD COLUMN IF NOT EXISTS (MySQL 8.0.29+ / MariaDB
-- 10.5+); the backfill UPDATE only touches rows still NULL/empty.
-- ---------------------------------------------------------------------

USE academy;

ALTER TABLE teachers ADD COLUMN IF NOT EXISTS qualification VARCHAR(160) NULL AFTER role_title;

UPDATE teachers
SET qualification = SUBSTRING_INDEX(credentials, '\n', 1)
WHERE (qualification IS NULL OR qualification = '') AND credentials IS NOT NULL AND credentials != '';
