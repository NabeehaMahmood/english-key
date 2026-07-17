-- ---------------------------------------------------------------------
-- Migration: Homepage stats/why-cards/founders-vision + testimonial
-- categories, ratings, featured badge.
--
-- Run this once against an EXISTING database (one that was set up before
-- this change and already has real data in it). If you are instead doing
-- a fresh install, just import sql/schema.sql, it already includes all
-- of this.
--
-- Safe to re-run: tables/columns use IF NOT EXISTS, and the one-time
-- category backfill only runs if the old `testimonials.category` text
-- column is still present. Requires MariaDB 10.0.2+ / MySQL 8.0.29+ for
-- the "ADD COLUMN IF NOT EXISTS" syntax (current XAMPP ships MariaDB,
-- which supports this).
--
-- IMPORTANT (encoding): this file contains UTF-8 characters (×, ’).
-- Import it through phpMyAdmin (handles UTF-8 correctly by default), or
-- if using the mysql CLI, pass --default-character-set=utf8mb4, e.g.:
--   mysql --default-character-set=utf8mb4 -u root academy < migration_2026-07-16_homepage_testimonials.sql
-- Importing without this on some CLI setups can mangle those characters.
-- ---------------------------------------------------------------------

USE academy;

-- ---------------------------------------------------------------------
-- 1. New site_settings keys for the hero CTA buttons.
INSERT INTO site_settings (setting_key, setting_value)
SELECT * FROM (SELECT 'hero_cta1_label' AS k, 'Explore Courses' AS v) t
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'hero_cta1_label');
INSERT INTO site_settings (setting_key, setting_value)
SELECT * FROM (SELECT 'hero_cta1_link' AS k, 'courses.php' AS v) t
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'hero_cta1_link');
INSERT INTO site_settings (setting_key, setting_value)
SELECT * FROM (SELECT 'hero_cta2_label' AS k, 'See Our Results' AS v) t
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'hero_cta2_label');
INSERT INTO site_settings (setting_key, setting_value)
SELECT * FROM (SELECT 'hero_cta2_link' AS k, '#results' AS v) t
WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'hero_cta2_link');

-- ---------------------------------------------------------------------
-- 2. New content_blocks rows (page_slug = 'home').
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'track_record_heading' AS k, 'Three years. Three first positions.' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'track_record_heading');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'track_record_description' AS k, 'Not testimonials, verifiable federal board results.' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'track_record_description');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'founders_heading' AS k, 'Founders’ Vision' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'founders_heading');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'founder1_name' AS k, 'Mr. Naeem Haider' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'founder1_name');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'founder1_title' AS k, 'Co-Founder & Lead Instructor' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'founder1_title');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'founder2_name' AS k, 'Uzma Arif' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'founder2_name');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'founder2_title' AS k, 'Founder & CEO' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'founder2_title');
INSERT INTO content_blocks (page_slug, block_key, content)
SELECT * FROM (SELECT 'home' AS p, 'why_heading' AS k, 'A planned, year-round path from foundation to final paper.' AS c) t
WHERE NOT EXISTS (SELECT 1 FROM content_blocks WHERE page_slug = 'home' AND block_key = 'why_heading');

-- ---------------------------------------------------------------------
-- 3. Repeatable homepage stats.
CREATE TABLE IF NOT EXISTS home_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(40) NOT NULL,
    label VARCHAR(160) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO home_stats (value, label, sort_order)
SELECT * FROM (SELECT '210K+' AS v, 'Learners in our community' AS l, 1 AS o) t
WHERE NOT EXISTS (SELECT 1 FROM home_stats);
INSERT INTO home_stats (value, label, sort_order)
SELECT '3×', 'Consecutive HSSC 1st positions', 2 WHERE (SELECT COUNT(*) FROM home_stats) = 1;
INSERT INTO home_stats (value, label, sort_order)
SELECT '5 yrs', 'Teaching FBISE online', 3 WHERE (SELECT COUNT(*) FROM home_stats) = 2;
INSERT INTO home_stats (value, label, sort_order)
SELECT '2012', 'Teaching languages since', 4 WHERE (SELECT COUNT(*) FROM home_stats) = 3;
INSERT INTO home_stats (value, label, sort_order)
SELECT '147K+', 'YouTube Subscribers', 5 WHERE (SELECT COUNT(*) FROM home_stats) = 4;

-- ---------------------------------------------------------------------
-- 4. "Why EnglishKeys" cards.
CREATE TABLE IF NOT EXISTS home_why_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(40) NOT NULL DEFAULT 'cap',
    title VARCHAR(160) NOT NULL,
    description TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO home_why_cards (icon, title, description, sort_order)
SELECT 'cap', 'Taught by one expert, not a rotating panel', 'Every class is led by Mr. Naeem Haider himself, an M.Phil. English Linguistics scholar with 14+ years of teaching.', 1
WHERE NOT EXISTS (SELECT 1 FROM home_why_cards);
INSERT INTO home_why_cards (icon, title, description, sort_order)
SELECT 'target', 'Mapped exactly to the FBISE syllabus', 'Nothing wasted. Smart notes, model papers and MCQ banks built around the current board pattern.', 2
WHERE (SELECT COUNT(*) FROM home_why_cards) = 1;
INSERT INTO home_why_cards (icon, title, description, sort_order)
SELECT 'people', 'A community of 210K+ learners', 'Followed across Facebook, YouTube and Instagram, a proven, trusted place to prepare.', 3
WHERE (SELECT COUNT(*) FROM home_why_cards) = 2;

-- ---------------------------------------------------------------------
-- 5. Testimonial categories (the 5 tabs on testimonials.php).
CREATE TABLE IF NOT EXISTS testimonial_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    card_style VARCHAR(20) NOT NULL DEFAULT 'standard',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO testimonial_categories (id, name, card_style, sort_order)
SELECT 1, 'Featured', 'standard', 1 WHERE NOT EXISTS (SELECT 1 FROM testimonial_categories WHERE id = 1);
INSERT INTO testimonial_categories (id, name, card_style, sort_order)
SELECT 2, 'Results & Marks', 'marks', 2 WHERE NOT EXISTS (SELECT 1 FROM testimonial_categories WHERE id = 2);
INSERT INTO testimonial_categories (id, name, card_style, sort_order)
SELECT 3, 'From Parents', 'parent', 3 WHERE NOT EXISTS (SELECT 1 FROM testimonial_categories WHERE id = 3);
INSERT INTO testimonial_categories (id, name, card_style, sort_order)
SELECT 4, 'By Subject', 'tag', 4 WHERE NOT EXISTS (SELECT 1 FROM testimonial_categories WHERE id = 4);
INSERT INTO testimonial_categories (id, name, card_style, sort_order)
SELECT 5, 'By Course Type', 'tag', 5 WHERE NOT EXISTS (SELECT 1 FROM testimonial_categories WHERE id = 5);

-- ---------------------------------------------------------------------
-- 6. Extend testimonials with category_id / course / rating / is_featured.
ALTER TABLE testimonials
    ADD COLUMN IF NOT EXISTS category_id INT NULL AFTER quote,
    ADD COLUMN IF NOT EXISTS course VARCHAR(120) NULL AFTER category_id,
    ADD COLUMN IF NOT EXISTS rating TINYINT NOT NULL DEFAULT 5 AFTER course,
    ADD COLUMN IF NOT EXISTS is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER rating;

-- 6a. One-time backfill of category_id from the old free-text `category`
-- column, only if that column still exists (skips silently on a DB that
-- has already been migrated).
SET @dbname = DATABASE();
SET @has_old_category = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'testimonials' AND COLUMN_NAME = 'category'
);

SET @backfill_sql = IF(@has_old_category > 0,
    'UPDATE testimonials SET
        category_id = CASE
            WHEN category = ''Parent'' THEN 3
            WHEN category IN (''English'', ''Urdu'', ''Islamiat & TQ'') THEN 4
            WHEN category IN (''Bootcamp'', ''Crash Course'', ''Test Series / FLP'') THEN 5
            ELSE 1
        END,
        course = CASE
            WHEN category IN (''English'', ''Urdu'', ''Islamiat & TQ'', ''Bootcamp'', ''Crash Course'', ''Test Series / FLP'') THEN category
            ELSE course
        END
     WHERE category_id IS NULL',
    'SELECT 1'
);
PREPARE backfillStmt FROM @backfill_sql;
EXECUTE backfillStmt;
DEALLOCATE PREPARE backfillStmt;

-- 6b. Drop the old free-text category column now that category_id is the
-- single source of truth (avoids two parallel category concepts).
SET @drop_sql = IF(@has_old_category > 0,
    'ALTER TABLE testimonials DROP COLUMN category',
    'SELECT 1'
);
PREPARE dropStmt FROM @drop_sql;
EXECUTE dropStmt;
DEALLOCATE PREPARE dropStmt;

-- Any remaining rows with no category assigned default to "Featured".
UPDATE testimonials SET category_id = 1 WHERE category_id IS NULL;
