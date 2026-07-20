-- ---------------------------------------------------------------------
-- Migration: reusable inner-page Hero banner.
--
-- Previously every inner page (About, Testimonials, Alumni, Blog, Notes,
-- Contact, Enroll, Courses) hardcoded its own "phero" kicker/title/sub
-- markup, and the dark-navy hero styling only applied to Testimonials and
-- Courses (page-scoped CSS) while the rest fell back to an older plain
-- light-orange band. This migration introduces one `page_heroes` table
-- (one fixed row per page - not admin add/deletable, the set of inner
-- pages is fixed in code) rendered everywhere via renderPageHero() in
-- includes/hero.php, so every inner page looks the same and is editable
-- from Admin -> Page Heroes. The Home page's own distinct hero
-- (<section class="hero"> in index.php) is untouched by this migration.
--
-- Run this once against an EXISTING database. If you are instead doing a
-- fresh install, just import sql/schema.sql, it already includes this.
--
-- Safe to re-run: table creation uses IF NOT EXISTS and the seed rows use
-- INSERT ... ON DUPLICATE KEY UPDATE, matching each page's current exact
-- wording so nothing changes visually until an admin edits a field.
-- ---------------------------------------------------------------------

USE academy;

CREATE TABLE IF NOT EXISTS page_heroes (
    page_slug VARCHAR(60) PRIMARY KEY,
    kicker VARCHAR(120),
    title VARCHAR(255) NOT NULL,
    title_highlight VARCHAR(255),
    subtitle TEXT,
    breadcrumb VARCHAR(255),
    description TEXT,
    show_description TINYINT(1) NOT NULL DEFAULT 0,
    background_image VARCHAR(255)
) ENGINE=InnoDB;

INSERT INTO page_heroes (page_slug, kicker, title, title_highlight, subtitle) VALUES
    ('courses', 'Courses', 'Built around the FBISE syllabus,', 'nothing wasted.', 'Complete preparation for Classes 9-12 across four subjects, plus seasonal intensives, bootcamps and crash courses.'),
    ('about', 'About Us', 'Where words', 'build futures.', 'EnglishKeys Academy exists to bring first-position-quality preparation to every FBISE student in Pakistan, taught live, with the discipline and care of a single expert instructor.'),
    ('testimonials', 'Testimonials', 'Real students. Real results.', 'Real words.', 'Every quote on this page is a genuine, permission-granted review from students, parents and alumni.'),
    ('alumni', 'Alumnus Corner', 'Once EnglishKeys,', 'always EnglishKeys.', 'Our alumni carry the academy''s standard into medical colleges, universities and careers. This corner belongs to them, their journeys, milestones and advice for the students following behind.'),
    ('blog', 'Blog', 'Exam tips, study routines &', 'board updates.', 'Short, practical articles on exam technique and grammar, written to help FBISE students score higher. New pieces published through the term.'),
    ('notes', 'Free Resources', 'Notes that', 'open doors.', 'A selection from the EnglishKeys notes portal, free for every visitor, no login required. Premium notes and model papers unlock with an active subscription.'),
    ('contact', 'Contact Us', 'Questions?', 'We''re here to help.', 'Send us a message and our team will get back to you soon, usually within 3 hours. Prefer to chat now? WhatsApp is the fastest way to reach us. All classes are online, on Pakistan Standard Time.'),
    ('enroll', 'Enrolment', 'Enrol at EnglishKeys,', 'start this week.', 'Tell us who''s enrolling and which subjects you want. We reply within 3 hours to confirm your seat and share payment details. All classes are online, on Pakistan Standard Time.')
ON DUPLICATE KEY UPDATE
    kicker = VALUES(kicker), title = VALUES(title), title_highlight = VALUES(title_highlight), subtitle = VALUES(subtitle);
