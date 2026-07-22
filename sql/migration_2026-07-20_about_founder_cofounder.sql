-- ---------------------------------------------------------------------
-- Migration: dedicated Founder / Co-Founder designation for the About
-- page, mirroring the existing 'founders_vision_teacher_id' pattern
-- (site_settings pointer into the teachers table) so About never depends
-- on fragile Role Title text matching or Sort Order to know who gets the
-- dedicated Founder/Co-Founder profile treatment vs. the plain Faculty
-- grid. Editable in Admin -> Our Team ("Founder" / "Co-Founder" tabs).
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already includes this.
-- Safe to re-run: INSERT ... ON DUPLICATE KEY UPDATE.
-- ---------------------------------------------------------------------

USE academy;

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('about_founder_teacher_id', (SELECT id FROM teachers WHERE role_title LIKE '%CEO%' ORDER BY sort_order, id LIMIT 1)),
    ('about_cofounder_teacher_id', (SELECT id FROM teachers WHERE role_title LIKE '%Co-Founder%' ORDER BY sort_order, id LIMIT 1))
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
