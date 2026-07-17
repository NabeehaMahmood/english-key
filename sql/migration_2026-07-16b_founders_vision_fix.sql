-- ---------------------------------------------------------------------
-- Correction pass: Founders' Vision now reuses the existing `teachers`
-- table (name/role_title/photo/credentials) instead of the 6 duplicate
-- content_blocks rows added in the previous migration. Run this once
-- after migration_2026-07-16_homepage_testimonials.sql. Safe to re-run.
-- Import with UTF-8 (phpMyAdmin default, or `mysql --default-character-set=utf8mb4`).
-- ---------------------------------------------------------------------

USE academy;

-- 1. New site_settings keys.
INSERT INTO site_settings (setting_key, setting_value)
SELECT 'founders_vision_teacher_id',
       COALESCE(
         (SELECT id FROM teachers WHERE name LIKE '%Naeem%' ORDER BY sort_order, id LIMIT 1),
         (SELECT id FROM teachers ORDER BY sort_order, id LIMIT 1)
       )
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'founders_vision_teacher_id');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'hero_fact4_value', '15+'
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'hero_fact4_value');

-- 2. Remove the 6 orphaned content_blocks rows from the previous pass
-- (founder identity now comes from `teachers`, selected via
-- founders_vision_teacher_id above, so these duplicate the same data).
DELETE FROM content_blocks WHERE page_slug = 'home' AND block_key IN
  ('founder1_name', 'founder1_title', 'founder1_image', 'founder2_name', 'founder2_title', 'founder2_image');
