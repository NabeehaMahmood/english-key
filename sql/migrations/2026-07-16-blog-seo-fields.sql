-- Brings an EXISTING blog_posts table (pre-redesign) up to the shape
-- sql/schema.sql defines for fresh installs: adds the SEO/excerpt fields,
-- replaces is_active with status + nullable published_at, and adds
-- created_at/updated_at. (schema.sql itself DROPs and recreates the whole
-- database, so it isn't safe to re-run against a live site — run this
-- migration once against the live/staging DB instead.)
ALTER TABLE blog_posts
    ADD COLUMN meta_description VARCHAR(300) AFTER category,
    ADD COLUMN primary_keyword VARCHAR(150) AFTER meta_description,
    ADD COLUMN secondary_keywords VARCHAR(300) AFTER primary_keyword,
    ADD COLUMN target_audience VARCHAR(150) AFTER secondary_keywords,
    ADD COLUMN excerpt VARCHAR(300) AFTER target_audience,
    ADD COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft' AFTER image,
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER published_at,
    ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
    MODIFY COLUMN content LONGTEXT,
    MODIFY COLUMN published_at DATETIME NULL;

-- Carry over the old is_active flag into status before dropping it.
UPDATE blog_posts SET status = IF(is_active = 1, 'published', 'draft');

-- Drafts shouldn't have a publish date yet — only rows that were actually
-- live under the old is_active flag keep their existing published_at as a
-- best-effort original-publish-date.
UPDATE blog_posts SET published_at = NULL WHERE status = 'draft';

ALTER TABLE blog_posts DROP COLUMN is_active;
