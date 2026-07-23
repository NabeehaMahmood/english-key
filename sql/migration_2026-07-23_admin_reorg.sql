-- ---------------------------------------------------------------------
-- Admin-panel reorganization only - no public-site behaviour changes.
-- A few fields were being edited in a generic "Page Text Blocks" screen
-- despite belonging to a specific dedicated screen (Founders' Quote is
-- part of the Home page's Founders' Vision section, not About; Terms &
-- Conditions and the Courses Featured heading were already/better edited
-- elsewhere). This migrates the one row whose page_slug actually changed
-- (Founders' Quote: about -> home) so the Home page keeps showing it.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already reflects this.
-- ---------------------------------------------------------------------

USE academy;

UPDATE content_blocks SET page_slug = 'home' WHERE page_slug = 'about' AND block_key = 'quote';
