-- ---------------------------------------------------------------------
-- Adds per-category heading text to the Testimonials page tabs, matching
-- html folders/testimonials.html exactly: each tab panel (Featured,
-- Results & Marks, From Parents, By Subject, By Course Type) has its own
-- kick (existing `name` column) + heading + optional sub-paragraph above
-- the review cards. Two of the five also need nothing extra; "By Course
-- Type" additionally shows a "See all reviews on Google" button using the
-- existing site_settings `google_reviews_url` (no new link column needed).
--
-- Adds 3 nullable columns to the existing `testimonial_categories` table
-- (no new table): heading, sub_text, cta_label.
--
-- Safe to re-run. Import with UTF-8 (phpMyAdmin default, or
-- `mysql --default-character-set=utf8mb4`).
-- ---------------------------------------------------------------------

USE academy;

ALTER TABLE testimonial_categories
    ADD COLUMN IF NOT EXISTS heading VARCHAR(180) NULL AFTER card_style,
    ADD COLUMN IF NOT EXISTS sub_text VARCHAR(300) NULL AFTER heading,
    ADD COLUMN IF NOT EXISTS cta_label VARCHAR(80) NULL AFTER sub_text;

UPDATE testimonial_categories SET
    heading = 'The reviews we''re proudest of.',
    sub_text = 'Hand-picked stories from students whose journey changed at EnglishKeys, in their own words.'
WHERE id = 1 AND heading IS NULL;

UPDATE testimonial_categories SET
    heading = 'The numbers speak first.',
    sub_text = 'Quantified outcomes reported by students after their board papers, pre-boards and send-ups.'
WHERE id = 2 AND heading IS NULL;

UPDATE testimonial_categories SET
    heading = 'Trusted by the people who trust us most.'
WHERE id = 3 AND heading IS NULL;

UPDATE testimonial_categories SET
    heading = 'Pick your subject, hear from its students.'
WHERE id = 4 AND heading IS NULL;

UPDATE testimonial_categories SET
    heading = 'Crash courses to bootcamps, every format, reviewed.',
    cta_label = 'See all reviews on Google'
WHERE id = 5 AND heading IS NULL;
