-- ---------------------------------------------------------------------
-- Adds admin-configurable settings for the featured-course popup on the
-- Home page (card width + 2 buttons). The popup's content (title,
-- description, schedule/price/etc.) already comes from the Featured
-- course row in `courses` - only presentation settings are added here.
-- The optional background image reuses the existing `content_blocks`
-- table (page_slug='home', block_key='fc_popup_bg') - no new table.
--
-- Safe to re-run. Import with UTF-8 (phpMyAdmin default, or
-- `mysql --default-character-set=utf8mb4`).
-- ---------------------------------------------------------------------

USE academy;

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'fc_popup_card_width', '430'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'fc_popup_card_width');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'fc_popup_btn1_label', 'Enroll Now'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'fc_popup_btn1_label');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'fc_popup_btn1_link', 'enroll.php'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'fc_popup_btn1_link');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'fc_popup_btn2_label', 'See All Courses'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'fc_popup_btn2_label');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'fc_popup_btn2_link', 'courses.php'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'fc_popup_btn2_link');
