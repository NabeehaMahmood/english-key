-- ---------------------------------------------------------------------
-- Removes the standalone Enroll page and its multi-step enrollment form.
-- The Contact page (contact.php) is now the single way to reach out -
-- it already carried the same "How to Enroll" steps, payment details and
-- terms & conditions, so nothing there was lost; the Enroll page's FAQs
-- moved onto Contact too. This drops the `enrollments` table that backed
-- the removed form/admin inbox, and repoints the homepage featured-course
-- popup's default button link away from the now-deleted enroll.php.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already reflects this.
-- ---------------------------------------------------------------------

USE academy;

DROP TABLE IF EXISTS enrollments;

DELETE FROM page_heroes WHERE page_slug = 'enroll';

UPDATE site_settings SET setting_value = 'contact.php'
WHERE setting_key = 'fc_popup_btn1_link' AND setting_value = 'enroll.php';
