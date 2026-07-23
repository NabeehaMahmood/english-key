-- EnglishKeys Academy website schema (rebuilt to match englishkeysacademy.com)
-- Import via phpMyAdmin (local XAMPP) or Hostinger's phpMyAdmin after creating
-- a database in hPanel. This DROPS and recreates the academy database.

DROP DATABASE IF EXISTS academy;
CREATE DATABASE academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE academy;

-- ---------------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login: username "admin", password "ChangeMe123!" (bcrypt hash, verified working)
-- CHANGE THIS PASSWORD after first login.
INSERT INTO admins (username, password_hash) VALUES
    ('admin', '$2y$10$JwC0V7vlsElnkHf.aXJxIuR6WTD6zn/kGpA8tsJ1MaYYlnpxtc/eG');

-- ---------------------------------------------------------------------
CREATE TABLE site_settings (
    setting_key VARCHAR(60) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB;

INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('site_name', 'EnglishKeys Academy'),
    ('logo_path', 'assets/uploads/logo/logo.png'),
    ('tagline', 'Where Words Build Futures'),
    ('kicker', 'FBISE - Class 9-12 - Online'),
    ('phone', '0311-1537563'),
    ('phone_2', '0317-5403540'),
    ('whatsapp_number', '923111537563'),
    ('email', 'englishkeysacademy@gmail.com'),
    ('address', 'Online, Pakistan Standard Time'),
    ('office_hours', 'Office hours 10 AM - 10 PM PKT'),
    ('social_facebook', 'https://www.facebook.com/share/1F4mRPencJ/'),
    ('social_instagram', 'https://www.instagram.com/englishkeysacademy'),
    ('social_youtube', 'https://youtube.com/@englishkeysacademy'),
    ('social_linkedin', 'https://www.linkedin.com/company/englishkeys/'),
    ('youtube_subscribers', '147K+'),
    ('google_reviews_url', 'https://maps.app.goo.gl/dzrcPXob5susTzSk9'),
    ('google_rating', '4.8'),
    ('google_review_count', '708'),
    ('footer_text', 'All rights reserved.'),
    ('footer_note', 'FBISE - Classes 9-12 - Online, Pakistan Standard Time'),
    ('hero_title', 'Where words build futures.'),
    ('hero_subtitle', 'Premium live coaching in English, Urdu, Islamiat & Tarjuma-tul-Quran for FBISE students, taught by Mr. Naeem Haider, the academy behind three consecutive HSSC top positions.'),
    ('hero_micro', 'Online - Pakistan Standard Time - 210,000+ learners in our community'),
    ('hero_image', 'assets/uploads/teachers/naeem-haider.jpeg'),
    ('hero_cta1_label', 'Explore Courses'),
    ('hero_cta1_link', 'courses.php'),
    ('hero_cta2_label', 'See Our Results'),
    ('hero_cta2_link', '#results'),
    ('fc_popup_card_width', '430'),
    ('fc_popup_btn1_label', 'Enroll Now'),
    ('fc_popup_btn1_link', 'contact.php'),
    ('fc_popup_btn2_label', 'See All Courses'),
    ('fc_popup_btn2_link', 'courses.php'),
    ('hero_fact4_value', '15+'),
    ('accent_color', '#EA6C1F'),
    ('stat_learners', '210K+'),
    ('stat_positions', '3x'),
    ('stat_years', '5 yrs'),
    ('stat_since', '2012'),
    ('stat_youtube_subs', '147K+'),
    ('founded_date', '18 July 2020'),
    ('bank_name', 'Askari Bank'),
    ('bank_title', 'EnglishKeys Academy'),
    ('bank_iban', 'PK95 ASCM 0001 9702 0000 2790'),
    ('easypaisa_name', 'Muhammad Naeem'),
    ('easypaisa_number', '0311-1537563'),
    ('jazzcash_name', ''),
    ('jazzcash_number', ''),
    ('qr_code_image', ''),
    ('hero_photo_name', 'Mr. Naeem Haider'),
    ('hero_photo_role', 'Co-Founder & Lead Instructor'),
    ('footer_description', 'Online coaching in English, Urdu, Islamiat & Tarjuma-tul-Quran for FBISE students, Classes 9-12, taught live across Pakistan.');

-- ---------------------------------------------------------------------
-- Editable text blocks for fixed pages not otherwise covered by a
-- dedicated CRUD screen.
CREATE TABLE content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(60) NOT NULL,
    block_key VARCHAR(60) NOT NULL,
    content TEXT,
    image_path VARCHAR(255),
    UNIQUE KEY page_block (page_slug, block_key)
) ENGINE=InnoDB;

INSERT INTO content_blocks (page_slug, block_key, content) VALUES
    ('contact', 'intro', 'Send us a message and our team will get back to you soon, usually within 3 hours. Prefer to chat now? WhatsApp is the fastest way to reach us. All classes are online, on Pakistan Standard Time.'),
    ('courses', 'how_to_enroll_steps', '01. Choose your course|Select the English Language Summer Course.\n02. Make payment|Transfer the fee to our Askari Bank or EasyPaisa account.\n03. Send proof|WhatsApp the payment screenshot to 0311-1537563.\n04. Get confirmed|Receive the class link and joining instructions.'),
    ('courses', 'featured_intro', 'English Language Summer Course, Summer Intensive 2026|Equally beneficial for all boards from Class 8th onwards. Covers essential topics to build a solid base in Grammar and Creative Writing.'),
    ('courses', 'terms_conditions', 'Fee once paid is non-refundable.\nClasses are conducted online via Zoom.\nStudents must ensure a stable internet connection.\nRecordings may be shared with enrolled students only.\nEnrollment closes once seats are filled.\nSharing class resources with others is prohibited.\nContacting classmates on their numbers is not allowed.'),
    ('home', 'quote', 'We built this academy the way we run our home, with care, discipline, and the belief that every child deserves a first-class chance.'),
    ('about', 'uzma_bio', 'Uzma Arif is the Founder and CEO of EnglishKeys Academy, a leading online educational platform dedicated to transforming the way quality education reaches students across Pakistan. She holds an M.Sc. in Psychology from Quaid-i-Azam University, a B.Ed. from the Virtual University of Pakistan, and a Diploma in TEFL from Allama Iqbal Open University. Before establishing EnglishKeys Academy, she served as a Language Instructor and Section Head at some of Pakistan''s prestigious educational institutions, where she developed extensive experience in teaching, academic leadership, and curriculum development.\n\nDuring her professional journey, Ms. Uzma Arif realized that quality education should not remain confined to conventional classrooms. Driven by the vision of making education accessible beyond geographical and financial barriers, she co-founded EnglishKeys Academy with her husband, Mr. Naeem Haider, the Lead Instructor, on 18 July 2020. Their mission was to provide affordable, high-quality education to students, particularly those from underserved and underprivileged areas of Pakistan.\n\nWhat began as a small initiative has steadily grown into one of Pakistan''s most trusted online educational brands, earning the confidence of thousands of students and parents nationwide. Today, EnglishKeys Academy specializes exclusively in Federal Board (FBISE) education, offering comprehensive preparation for Grades 9-12 compulsory subjects. Beyond SSC and HSSC education, the academy also delivers professional and competitive exam preparation programs, including MDCAT, IELTS, TEFL, PTE, and English preparation for CSS and PMS aspirants.'),
    ('about', 'naeem_bio', 'Mr. Naeem Haider, Co-Founder, Director and Lead Instructor of EnglishKeys Academy, has taught languages since 2012, guiding over 100,000 students in the last five years alone. A distinguished scholar of English linguistics and literature, he built the academy''s teaching on a simple belief: a student who understands the examiner''s mind never fears the paper.\n\nEvery class is led by him personally, no rotating panel, no stock-photo instructors. The credentials are the product.'),
    ('about', 'method_steps', 'Learn|Concepts taught live, from the ground up, in clear English or Urdu medium.\nPractise|Guided worksheets and MCQ banks that mirror the FBISE paper.\nSubmit|Written work handed in for personal marking, not self-assessment.\nFeedback|Answer-by-answer corrections that show exactly what the examiner wants.\nRevise|Smart capsule notes and model papers for focused, efficient revision.\nFinal Paper|A full-length attempt under exam conditions before the boards.'),
    ('home', 'track_record_heading', 'Three years. Three first positions.'),
    ('home', 'track_record_description', 'Not testimonials, verifiable federal board results.'),
    ('home', 'founders_heading', 'Founders’ Vision'),
    ('home', 'why_heading', 'A planned, year-round path from foundation to final paper.');

-- ---------------------------------------------------------------------
-- Reusable inner-page Hero banner. One fixed row per inner page (not
-- admin add/deletable - the set of pages is fixed in code), rendered via
-- renderPageHero() in includes/hero.php so About/Courses/Testimonials/
-- Alumni/Blog/Notes/Contact/Enroll all share one consistent hero design.
-- Admin manages these under Admin -> Page Heroes. The Home page's own
-- distinct hero (<section class="hero"> in index.php) does not use this.
CREATE TABLE page_heroes (
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
    ('contact', 'Contact Us', 'Questions?', 'We''re here to help.', 'Send us a message and our team will get back to you soon, usually within 3 hours. Prefer to chat now? WhatsApp is the fastest way to reach us. All classes are online, on Pakistan Standard Time.');

-- ---------------------------------------------------------------------
-- Repeatable stat cards shown in the dark band below the hero. Admin
-- manages these under Admin -> Homepage Stats (add/edit/delete/reorder).
CREATE TABLE home_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(40) NOT NULL,
    label VARCHAR(160) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO home_stats (value, label, sort_order) VALUES
    ('210K+', 'Learners in our community', 1),
    ('3×', 'Consecutive HSSC 1st positions', 2),
    ('5 yrs', 'Teaching FBISE online', 3),
    ('2012', 'Teaching languages since', 4),
    ('147K+', 'YouTube Subscribers', 5);

-- ---------------------------------------------------------------------
-- "Why EnglishKeys" cards. Admin manages these under Admin -> Why Us Cards.
-- icon must be one of the names defined in includes/icons.php.
CREATE TABLE home_why_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(40) NOT NULL DEFAULT 'cap',
    title VARCHAR(160) NOT NULL,
    description TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO home_why_cards (icon, title, description, sort_order) VALUES
    ('cap', 'Taught by one expert, not a rotating panel', 'Every class is led by Mr. Naeem Haider himself, an M.Phil. English Linguistics scholar with 14+ years of teaching.', 1),
    ('target', 'Mapped exactly to the FBISE syllabus', 'Nothing wasted. Smart notes, model papers and MCQ banks built around the current board pattern.', 2),
    ('people', 'A community of 210K+ learners', 'Followed across Facebook, YouTube and Instagram, a proven, trusted place to prepare.', 3);

-- ---------------------------------------------------------------------
-- Programme groups: the collapsible category sections on courses.php
-- (e.g. "Full-Syllabus Bootcamps"). Each group carries its own icon,
-- description and date-range so it can render without any hardcoded
-- copy. A programme course points at one group via programme_group_id;
-- ungrouped/NULL programmes fall back to an "Other Programmes" bucket.
CREATE TABLE programme_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    date_range VARCHAR(100),
    icon_key VARCHAR(40) NOT NULL DEFAULT 'compass',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO programme_groups (id, name, description, date_range, icon_key, sort_order) VALUES
(1, 'Foundation & Entry-Test Prep', 'Two short July intensives, a language foundation camp for every student, and a focused English prep track for medical entry tests.', 'Jul 2026 - All boards / Medical', 'compass', 1),
(2, 'Full-Syllabus Bootcamps', 'Three rolling English & Urdu bootcamps across the year, plus a specialised Deen Camp for Islamiat and Tarjuma-tul-Quran.', 'Aug 2026 - Jan 2027', 'book-open', 2),
(3, 'Final Exam Preparation', 'Full-length papers, revision marathons and last-minute crash courses timed to the run-up to board exams.', 'Feb - Mar 2027', 'bookmark', 3);

-- ---------------------------------------------------------------------
-- category: 'subject' (4 core subjects), 'featured' (currently enrolling
-- flagship course), 'programme' (seasonal/intensive courses)
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    category VARCHAR(20) NOT NULL DEFAULT 'programme',
    programme_group VARCHAR(80),
    tag_line VARCHAR(200),
    description TEXT,
    image VARCHAR(255),
    duration VARCHAR(60),
    level VARCHAR(60),
    price VARCHAR(60),
    eligibility VARCHAR(150),
    mode VARCHAR(100),
    schedule_info TEXT,
    highlights TEXT,
    modules TEXT,
    seats_info VARCHAR(100),
    accent_color VARCHAR(20),
    programme_group_id INT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (programme_group_id) REFERENCES programme_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- programme_group (free text) is deprecated in favour of programme_group_id,
-- kept only so older data isn't silently dropped; courses.php reads the FK.
INSERT INTO courses (title, slug, category, tag_line, description, duration, level, price, eligibility, mode, schedule_info, highlights, seats_info, accent_color, programme_group, programme_group_id, sort_order) VALUES
('English', 'english', 'subject', 'Live Classes - Smart Notes - Model Papers', 'Grammar, comprehension, translation and composition, taught by an M.Phil. English Linguistics scholar.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#EA6C1F', NULL, NULL, 1),
('Urdu', 'urdu', 'subject', 'Capsule Notes - MCQ Bank - Model Papers', 'Nazm, ghazal, mazmoon and grammar with full past-paper coverage and answer-writing technique.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#1B2A4A', NULL, NULL, 2),
('Islamiat', 'islamiat', 'subject', 'Both Mediums - Live Classes - Model Papers', 'Concept-first teaching of the full syllabus with smart revision notes in English and Urdu medium.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#1B2A4A', NULL, NULL, 3),
('Tarjuma-tul-Quran', 'tarjuma-tul-quran', 'subject', 'Surah-Wise - Both Mediums - MCQ Bank', 'Surah-wise translation, Shaan-e-Nuzul and MCQ preparation built around the FBISE exam format.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#EA6C1F', NULL, NULL, 4),
('English Language Summer Course', 'summer-intensive-2026', 'featured', 'Summer Intensive 2026', 'Equally beneficial for students of all boards from Class 8th onwards. It does not teach the syllabus of a specific class; instead it covers all essential topics of the broader curriculum to build a solid base in the language, focusing on Grammar and Creative Writing.', '20 live sessions - 2 hours each', 'All boards, Class 8th onwards', 'Rs. 5,000', 'All boards, Class 8th onwards', 'Online via Zoom', 'Starts 06 July 2026 - Ends 31 July 2026 - Monday-Friday - 07:00-09:00 PM (PKT)', 'All boards - Class 8th onwards\n20 live, interactive sessions\n2 hours per session\nGrammar foundation + advanced writing\nExpert feedback on all exercises\nTopic-wise assessment', 'Seats are strictly limited, register early.', '#E56A19', NULL, NULL, 5),
('Summer Camp', 'summer-camp', 'programme', 'Jul 2026 - All boards', 'A foundation course focused on grammar and creative writing, one course for every student, Class 8th onwards. Twenty live 2-hour evening sessions (7:00-9:00 PM), building a solid base in the language before the academic year begins.', '6-31 Jul 2026', 'All levels - All boards', 'Rs. 5,000', 'All boards', 'Online', NULL, 'Grammar foundation + advanced writing\nExpert feedback on every exercise\nTopic-wise assessment', 'Limited seats', '#E56A19', NULL, 1, 6),
('MDCAT / NUMS English Prep', 'mdcat-nums-english-prep', 'programme', 'Jul 2026 - Medical', 'A concept- and practice-based intensive that targets the English portion of medical entry tests, vocabulary, grammar and comprehension tuned precisely to the exam.', '15 days - Jul 2026', 'Medical aspirants', NULL, 'Medical aspirants', 'Online', NULL, '15-day focused intensive\nConcept building + heavy practice\nExam-style questions throughout', 'Limited seats', '#1B7FB4', NULL, 1, 7),
('Bootcamp 01', 'bootcamp-01', 'programme', 'Aug-Sep 2026', 'Class-specific, complete-syllabus coverage for FBISE 9th-12th in English and Urdu. Two months of structured teaching with weekly assessments and one full-length paper under real exam conditions.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', NULL, 2, 8),
('Bootcamp 02', 'bootcamp-02', 'programme', 'Oct-Nov 2026', 'The second full-syllabus cohort of the year for English and Urdu, Classes 9-12. Same rigorous format, learn, practise, submit, get feedback, revise, timed for the mid-year stretch.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', NULL, 2, 9),
('Bootcamp 03 - Final Bootcamp', 'bootcamp-03', 'programme', 'Dec 2026 - Jan 2027', 'The last complete-syllabus bootcamp before annual exams for English and Urdu, Classes 9-12, the final chance to cover everything thoroughly with assessments and a full-length paper.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', NULL, 2, 10),
('Deen Camp', 'deen-camp', 'programme', 'Jan 2027 - Islamiat & Quran', 'Specialised, class-specific coverage of Islamiat (9th & 11th) and Tarjuma-tul-Quran (9th-12th), taught with depth and clarity, complete with weekly assessments and a full-length paper.', '1 month', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, 'Islamiat: Classes 9 & 11\nTarjuma-tul-Quran: Classes 9-12\nWeekly assessments + full-length paper', 'Limited seats', '#7A3FD0', NULL, 2, 11),
('Full-Length Papers', 'full-length-papers', 'programme', 'Feb 2027', 'A month of full-length practice papers across all four subjects, English, Urdu, Islamiat and Tarjuma-tul-Quran, with detailed marking and feedback, so exam day feels familiar.', '1 month', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, 'All four subjects, Classes 9-12\nReal exam conditions & timing\nDetailed marking + feedback', 'Limited seats', '#1E2A66', NULL, 3, 12),
('Exam Marathons', 'exam-marathons', 'programme', 'Pre-Board - Marathons', 'Detailed-but-quick revision of the whole syllabus in the final stretch before papers, a 2nd-Annual Marathon in English and an Annual Marathon in English & Urdu. Revision-focused, no assessments.', '15 days', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, '2nd Annual: English - 15 days\nAnnual: English & Urdu - 15 days (Mar 2027)\nRevision only, no assessments', 'Open - unlimited', '#E56A19', NULL, 3, 13),
('Crash Courses', 'crash-courses', 'programme', 'Final Days - Crash', 'Short, high-intensity revision right before each paper. A 2-day 2nd-Annual crash in English, and a 1-day Annual crash across all four subjects, following the date sheet.', '1-2 days', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, '2nd Annual: English - 2 days\nAnnual: all 4 subjects - 1-day intensives\nHigh-yield topics & answer technique', 'Open - unlimited', '#7A3FD0', NULL, 3, 14);

-- ---------------------------------------------------------------------
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    photo VARCHAR(255),
    bio TEXT,
    detail_bio TEXT,
    subject VARCHAR(150),
    role_title VARCHAR(150),
    qualification VARCHAR(160),
    credentials TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO teachers (name, photo, bio, detail_bio, subject, role_title, qualification, credentials, sort_order) VALUES
('Uzma Arif', 'assets/uploads/teachers/uzma-arif.jpeg',
 'The vision behind EnglishKeys, an M.Sc. Psychology (Quaid-i-Azam University) educator and former section head who co-founded the academy in 2020 to carry quality education beyond geographical and financial barriers.',
 'Uzma Arif is the Founder and CEO of EnglishKeys Academy, a leading online educational platform dedicated to transforming the way quality education reaches students across Pakistan. She holds an M.Sc. in Psychology from Quaid-i-Azam University, a B.Ed. from the Virtual University of Pakistan, and a Diploma in TEFL from Allama Iqbal Open University. Before establishing EnglishKeys Academy, she served as a Language Instructor and Section Head at some of Pakistan''s prestigious educational institutions.\n\nDriven by the vision of making education accessible beyond geographical and financial barriers, she co-founded EnglishKeys Academy with her husband, Mr. Naeem Haider, on 18 July 2020.',
 'Founder & CEO', 'Founder and CEO', 'M.Sc. Psychology, Quaid-i-Azam University',
 'M.Sc. Psychology, Quaid-i-Azam University\nB.Ed., Virtual University of Pakistan\nDiploma in TEFL, Allama Iqbal Open University\nLanguage Instructor & Section Head, prestigious institutions of Pakistan\nCo-founded EnglishKeys Academy, 18 July 2020\nPrograms: SSC/HSSC (FBISE), MDCAT, IELTS, TEFL, PTE, CSS & PMS English',
 1),
('Mr. Naeem Haider', 'assets/uploads/teachers/naeem-haider.jpeg',
 'The teacher behind the results, an M.Phil. English Linguistics scholar teaching since 2012, who leads every class personally and built the method behind three consecutive HSSC first positions.',
 'Mr. Naeem Haider, Co-Founder, Director and Lead Instructor of EnglishKeys Academy, has taught languages since 2012, guiding over 100,000 students in the last five years alone. A distinguished scholar of English linguistics and literature, he built the academy''s teaching on a simple belief: a student who understands the examiner''s mind never fears the paper.\n\nEvery class is led by him personally, no rotating panel, no stock-photo instructors. The credentials are the product.',
 'Co-Founder & Lead Instructor', 'Co-Founder, Director and Lead Instructor', 'M.Phil. English Linguistics, Distinction',
 'M.Phil. English Linguistics, Distinction\nMS English, Distinction\nMA English Literature, Silver Medalist\nMA Urdu Literature\nMA Islamic Studies\nB.Ed. (Bachelor of Education)\nDiploma in TEFL\nEMI, University of Southampton\nTEYL, George Mason University, USA',
 2);

-- Home page's Founders' Vision section shows one teacher's photo/name/title/
-- credentials (admin-selectable in Homepage Content); defaults to Mr. Naeem
-- Haider, the second row inserted above.
INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('founders_vision_teacher_id', (SELECT id FROM teachers WHERE name LIKE '%Naeem%' ORDER BY sort_order, id LIMIT 1));

-- About page's dedicated Founder / Co-Founder profile sections (admin-
-- selectable in Admin -> Our Team). Independent of founders_vision_teacher_id
-- above, which only controls the Home page's quote card.
INSERT INTO site_settings (setting_key, setting_value) VALUES
    ('about_founder_teacher_id', (SELECT id FROM teachers WHERE role_title LIKE '%CEO%' ORDER BY sort_order, id LIMIT 1)),
    ('about_cofounder_teacher_id', (SELECT id FROM teachers WHERE role_title LIKE '%Co-Founder%' ORDER BY sort_order, id LIMIT 1));

-- ---------------------------------------------------------------------
-- The 5 filter tabs on testimonials.php. card_style picks the card
-- markup variant rendered by renderTestimonialCard() in includes/functions.php:
-- 'standard' = stars only, 'marks' = orange marks badge (uses testimonials.course),
-- 'parent' = left-border pull-quote card, 'tag' = navy subject/course badge.
CREATE TABLE testimonial_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    card_style VARCHAR(20) NOT NULL DEFAULT 'standard',
    heading VARCHAR(180),
    sub_text VARCHAR(300),
    cta_label VARCHAR(80),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO testimonial_categories (id, name, card_style, heading, sub_text, cta_label, sort_order) VALUES
    (1, 'Featured', 'standard', 'The reviews we''re proudest of.', 'Hand-picked stories from students whose journey changed at EnglishKeys, in their own words.', NULL, 1),
    (2, 'Results & Marks', 'marks', 'The numbers speak first.', 'Quantified outcomes reported by students after their board papers, pre-boards and send-ups.', NULL, 2),
    (3, 'From Parents', 'parent', 'Trusted by the people who trust us most.', NULL, NULL, 3),
    (4, 'By Subject', 'tag', 'Pick your subject, hear from its students.', NULL, NULL, 4),
    (5, 'By Course Type', 'tag', 'Crash courses to bootcamps, every format, reviewed.', NULL, 'See all reviews on Google', 5);

-- ---------------------------------------------------------------------
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    photo VARCHAR(255),
    quote TEXT NOT NULL,
    category_id INT NULL,
    course VARCHAR(120),
    rating TINYINT NOT NULL DEFAULT 5,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    source_label VARCHAR(100),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO testimonials (name, quote, category_id, course, source_label, sort_order, is_featured) VALUES
('Amjad Farooq', 'In my 12 years of school life I always had English teachers that were OK, not good, not bad. But my goodness, your way of teaching is superb. You don''t just dictate, you actually TAUGHT US and we ACTUALLY LEARNED.', 1, NULL, 'Verified Google Review', 1, 1),
('Qasim Mustafa', 'SSC, 73/75 in English with Sir Naeem. HSSC, I was able to attempt my paper exceptionally, and I hope for the best.', 2, '73/75 in SSC English', 'SSC 73/75 English', 2, 0),
('Aashir Usman', 'I''ve been a regular student for two years and part of almost all its bootcamps. When I joined, I barely knew grammar. Today, due to Sir Naeem''s teaching, I''ve achieved 91 marks in part one and am aiming for more in part two.', 2, '91 marks in Part I', '2-year student - 91 marks', 3, 0),
('Tabu Khan', 'It''s the most authentic platform for studying arts subjects in Pakistan. Amazing notes for every section, grammar revision, crash courses for last-minute revision, and the FLP batch improved my presentation skills and time management.', 1, NULL, 'Verified Google Review', 4, 0),
('Muhammad Mueen', 'Learning from Sir Naeem has been nothing short of phenomenal. His ability to simplify complex concepts with unmatched clarity is remarkable.', 1, NULL, 'Verified Google Review', 5, 1),
('Rubab Fatima', 'This was my first EKA course, for my 2nd-year preparation, now I regret not availing it earlier. You never feel the communication gap you usually feel in online classes.', 5, 'Bootcamp', 'HSSC-II Student', 6, 0),
('Syed Adan Ali', 'My English was average, but then I joined Sir''s crash course, and it was totally worth it. It really helped me clear my concepts and confusions.', 5, 'Crash Course', 'Verified Google Review', 7, 0),
('Atif', 'I love EnglishKeys Academy. Sir Naeem is very determined and works hard to make sure students understand well. Notes are very concise and it was so fun studying with him.', 4, 'English', 'Verified Google Review', 8, 0),
('Ayan Awais', 'I took the Crash Course for 11th Urdu, and it was really helpful. The concepts were explained clearly and in a structured way.', 4, 'Urdu', 'Class 11 Urdu', 9, 0),
('Syed Muhammad Muhaymin Ali', 'I took the Marathon course for Class 11 2nd-Annual Urdu, and the way Sir taught everything in four weeks, from grammar to letter/application writing, is exceptional.', 4, 'Urdu', 'Urdu Marathon', 10, 0),
('Eshaal Azam', 'I was concerned about my Urdu MCQs, but Alhamdulillah I got them all right. In 10th I used to learn the MCQs Sir posted a day before the exam, it really helped.', 4, 'Islamiat & TQ', 'Islamiat & Tarjuma-tul-Quran', 11, 0),
('Muhammad Hassan Asif Khan', 'The best crash-course experience I ever had. Study 10/10, fun 9/10, help 11/10. Sir is really helpful and thoughtful, and the notes really came in clutch.', 5, 'Crash Course', 'Crash Course', 12, 0),
('Shamshad Ali', 'I joined the English Marathon by Sir Naeem, and it was a truly valuable experience. The Zoom sessions were well-structured and focused on important exam topics.', 4, 'English', 'English Marathon', 13, 0),
('Hamza Sadique', 'I had a great experience. Sir explained every concept deeply and taught us more than the scheduled time. I saw a lot of improvement in English after completing the Marathon batch.', 4, 'English', 'English Marathon', 14, 0),
('Raneen Falak', 'The test series provided three full-length papers with proper time extension, notes and assessment. I went from being unconfident about MCQs to writing a 3-page report and a well-written paragraph.', 5, 'Test Series / FLP', 'Test Series / FLP', 15, 0),
('M. Amin', 'The one-pager grammar notes, where every topic is properly managed, are really helpful. It seemed impossible to complete whole-paper preparation in only two months, but EnglishKeys Academy made it possible.', 5, 'Bootcamp', 'Bootcamp', 16, 0),
('Ayesha Umer', 'I was worried about my son''s preparation. Luckily I came across the academy and followed every post. I''m highly impressed by the devotion and dedication of Sir. My son is expecting around 93 in the exams.', 3, NULL, 'Parent', 17, 0);

-- ---------------------------------------------------------------------
-- Renamed from "news" to "blog_posts" to match the live site's /blog page.
-- status replaces the old is_active flag: 'draft' posts are only visible in
-- admin/blog.php, 'published' posts appear on the public /blog page and its
-- single-post view. published_at is set the first time a post is published
-- and is not touched by later edits, so it reflects original publish date.
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    category VARCHAR(60),
    meta_description VARCHAR(300),
    primary_keyword VARCHAR(150),
    secondary_keywords VARCHAR(300),
    target_audience VARCHAR(150),
    excerpt VARCHAR(300),
    content LONGTEXT,
    image VARCHAR(255),
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO blog_posts (title, slug, category, excerpt, content, status, published_at) VALUES
('Full-Marks Paragraph Writing', 'full-marks-paragraph-writing-fbise', 'Exam Technique', 'Most students lose paragraph marks on structure, not ideas. Here''s the examiner-approved skeleton that keeps you on track.', '<h2>The real reason good students lose paragraph marks</h2>
<p>Many students believe that paragraph writing is mainly a test of ideas. They think that a student who knows more facts, uses more difficult vocabulary or writes a dramatic opening will automatically receive more marks. In practice, a paragraph is usually lost sentence by sentence: the opening does not establish a clear focus, the middle jumps between unrelated points, the examples do not support the main idea, and the final sentence simply repeats the first line. The student may have plenty to say, but the examiner has to search for the answer’s direction.</p>
<p>That problem matters even more in an SLO-based examination. FBISE’s current English assessment frameworks are designed around student learning outcomes rather than one memorised textbook answer. The model papers show that the exact task and word limit depend on the level. In the current SSC-I English model, students write approximately <strong>80 to 100 words for 6 marks</strong>. In the HSSC-I English model, students write approximately <strong>100 to 120 words for 8 marks</strong>. The topics are unseen choices, so the safest preparation is not memorising ten complete paragraphs. It is learning a structure that can control almost any topic under time pressure.</p>
<p>This article gives you that structure. It is not an official FBISE acronym, and it should not be treated as a magic formula. It is a practical method built from the qualities that strong paragraphs consistently need: unity, coherence, a clear controlling idea, adequate development, correct language and a purposeful ending. Purdue University’s writing guidance describes effective paragraphs in similar terms: unity, coherence, a topic sentence and adequate development. FBISE’s own frameworks assess the application of language, transitions, grammar and written communication. Put these together and one principle becomes clear: <strong>the examiner must be able to understand your main idea immediately and see every sentence helping to develop it.</strong></p>
<h2>First, know which paragraph you are being asked to write</h2>
<p>Before learning any skeleton, understand that “paragraph” can describe several different tasks. A paragraph on <em>A Memorable Journey</em> is not organised in exactly the same way as one on <em>The Role of Technology in Education</em>. The first is likely to be descriptive or narrative. The second is explanatory and may include a balanced opinion. A topic such as <em>The Person I Admire the Most</em> needs a clear choice, selected qualities and evidence. A topic such as <em>A Walk in the Woods</em> needs sensory detail and a meaningful response rather than a list of trees.</p>
<p>Most FBISE-style paragraph topics fall into one of five broad families:</p>
<ol><li><strong>Descriptive:</strong> a place, person, scene, event or experience.</li><li><strong>Narrative:</strong> a short sequence of events with a beginning, turning point and result.</li><li><strong>Explanatory:</strong> how or why something happens, or why something is important.</li><li><strong>Opinion-based:</strong> a clear position supported by reasons.</li><li><strong>Reflective:</strong> an experience followed by what it taught you or why it mattered.</li></ol>
<p>The same paragraph can combine two families. <em>A Memorable Journey</em> may be narrative and reflective. <em>The Person I Admire the Most</em> may be descriptive and explanatory. Your first task in the examination hall is therefore not to start writing. It is to decide what kind of thinking the topic requires.</p>
<p>A useful question is: <strong>What must the reader know, feel or believe by the end?</strong></p>
<ul><li>For a descriptive topic, the reader should be able to imagine the subject.</li><li>For a narrative topic, the reader should understand what happened and why it mattered.</li><li>For an explanatory topic, the reader should understand the main reasons or effects.</li><li>For an opinion topic, the reader should know your position and why it is reasonable.</li><li>For a reflective topic, the reader should understand the lesson or change produced by the experience.</li></ul>
<p>This single decision prevents one of the most common mistakes: writing several acceptable sentences that do not answer the same question.</p>
<h2>The examiner-friendly skeleton: Focus, Build, Prove, Close</h2>
<p>Use the four-stage structure below. You can remember it as <strong>FBPC: Focus, Build, Prove, Close</strong>.</p>
<h3>1. Focus: state the controlling idea</h3>
<p>The first sentence should establish the paragraph’s direction. It does not always need to be a formal “topic sentence,” but it must tell the reader what the paragraph is really about.</p>
<p>Weak opening:</p>
<p class="ex">Technology is very important nowadays.</p>
<p>This is not wrong, but it is too broad. The examiner cannot predict what will follow: education, medicine, business, entertainment or communication.</p>
<p>Focused opening:</p>
<p class="ex">Technology has improved education by making learning faster, more flexible and more accessible.</p>
<p>Now the paragraph has a controlling idea. Every later sentence should develop one of three elements: speed, flexibility or access.</p>
<p>For a descriptive topic, the controlling idea may be an overall impression:</p>
<p class="ex">My grandmother is the person I admire most because her courage is matched by an unusual kindness.</p>
<p>For a narrative topic, it may establish situation and significance:</p>
<p class="ex">My most memorable journey began as an ordinary school trip but ended by teaching me the value of teamwork.</p>
<p>For an opinion topic, it should show a position:</p>
<p class="ex">Schools should guide students in using social media responsibly instead of treating every online platform as a distraction.</p>
<p>A focused opening does two jobs. It answers the topic and creates a promise. The rest of the paragraph must keep that promise.</p>
<h3>2. Build: explain the idea in a logical order</h3>
<p>After the focus sentence, add two or three supporting ideas. The order should be visible. You can organise support by:</p>
<ul><li>importance: most important reason first or last;</li><li>time: first, next, finally;</li><li>space: foreground to background, outside to inside;</li><li>cause and effect: cause, immediate effect, wider effect;</li><li>comparison: one side, the other side, final judgment;</li><li>example: claim, example, explanation;</li><li>problem and response: problem, consequence, solution.</li></ul>
<p>The best order depends on the topic. A descriptive paragraph on a forest may move from sound to sight to feeling. A paragraph on technology in education may move from classroom access to independent study to teacher support. A short narrative usually follows time.</p>
<p>A common weak pattern is random addition:</p>
<p class="ex">Technology helps students. It is used in hospitals. Students watch videos. Mobile phones are common. Teachers use projectors. Technology can also be harmful.</p>
<p>Each sentence is individually understandable, but the paragraph has no internal plan. A more coherent version groups related ideas:</p>
<p class="ex">Technology has improved education by widening access to information. Students can watch demonstrations, consult digital libraries and revise lessons at their own pace. Teachers can also use presentations and simulations to explain difficult concepts more clearly. However, these benefits appear only when devices are used with discipline, because constant notifications can interrupt attention.</p>
<p>The support now develops one subject—technology in education—and the caution is directly related to that subject.</p>
<h3>3. Prove: add a concrete detail, example or effect</h3>
<p>A paragraph becomes convincing when it moves beyond general statements. “Reading is useful” is a claim. “Regular reading expands vocabulary and shows students how effective sentences are constructed” explains the claim. “For example, a student who reads editorials regularly meets transition words and argument patterns that can later improve exam answers” proves it.</p>
<p>You do not always need a statistic. In a short exam paragraph, a relevant example, specific observation, mini-incident or cause-and-effect detail is usually enough. The purpose is to make the support visible.</p>
<p>General:</p>
<p class="ex">My father is hardworking.</p>
<p>Developed:</p>
<p class="ex">My father is hardworking; even after a long day at the hospital, he reviews his patients’ reports so that no important detail is missed.</p>
<p>General:</p>
<p class="ex">The journey was exciting.</p>
<p>Developed:</p>
<p class="ex">The journey became exciting when heavy rain blocked the road and our group had to guide the bus through a safer village route.</p>
<p>General:</p>
<p class="ex">Trees are important.</p>
<p>Developed:</p>
<p class="ex">Trees cool crowded neighbourhoods, reduce dust and provide shelter for birds, so even a small urban plantation can improve daily life.</p>
<p>The “proof” sentence often earns its place by answering one of these questions:</p>
<ul><li><strong>How?</strong></li><li><strong>Why?</strong></li><li><strong>What happened?</strong></li><li><strong>What is an example?</strong></li><li><strong>What was the result?</strong></li><li><strong>What did I notice, learn or feel?</strong></li></ul>
<p>If a sentence answers none of them and merely repeats an earlier statement, it may be wasting words.</p>
<h3>4. Close: complete the thought instead of stopping</h3>
<p>The final sentence should give the paragraph a sense of completion. It may:</p>
<ul><li>restate the central idea in fresh words;</li><li>show the result of the experience;</li><li>give a final judgment;</li><li>connect the detail to a broader lesson;</li><li>recommend a sensible action.</li></ul>
<p>Weak ending:</p>
<p class="ex">Therefore, technology is important.</p>
<p>This simply repeats the obvious.</p>
<p>Purposeful ending:</p>
<p class="ex">Used with clear limits, technology can turn the classroom from a place of passive listening into a space for active learning.</p>
<p>Weak ending:</p>
<p class="ex">It was a memorable journey and I will never forget it.</p>
<p>Purposeful ending:</p>
<p class="ex">I remember the journey not because everything went smoothly, but because solving the difficulty together made our class feel like a team.</p>
<p>The closing sentence is not a separate conclusion paragraph; it is one sentence that completes the unit. In an 80–100-word response, every sentence must work hard.</p>
<h2>A two-minute planning method that prevents blank-page panic</h2>
<p>Students often skip planning because the paragraph is short. That is exactly why planning matters. A short response has little space for correction. One unrelated sentence can damage a large percentage of the whole answer.</p>
<p>Use this two-minute method before writing.</p>
<h3>Step 1: circle the exact subject</h3>
<p>For <em>The Role of Technology in Education</em>, the subject is not “technology.” It is technology <strong>in education</strong>. Do not drift into hospitals, factories or transport.</p>
<p>For <em>The Person I Admire the Most</em>, you need one person and the reasons for admiration. A biography containing dates but no qualities is off-centre.</p>
<p>For <em>A Walk in the Woods</em>, the subject is not a general essay on forests. It is the experience of a walk.</p>
<h3>Step 2: write a six-word controlling idea</h3>
<p>Examples:</p>
<ul><li>technology makes learning flexible and active;</li><li>grandmother combines courage with kindness;</li><li>journey taught us teamwork under pressure;</li><li>woods created calm through natural beauty;</li><li>social media needs discipline, not total rejection.</li></ul>
<p>This phrase is not your final sentence. It is a compass.</p>
<h3>Step 3: list three supports</h3>
<p>For technology in education:</p>
<ol><li>access to resources;</li><li>clearer explanations;</li><li>self-paced revision with responsible use.</li></ol>
<p>For a person admired:</p>
<ol><li>quality—courage;</li><li>proof—supported family during crisis;</li><li>effect—taught me persistence.</li></ol>
<p>For a walk in the woods:</p>
<ol><li>setting and sounds;</li><li>striking visual detail;</li><li>feeling or lesson.</li></ol>
<h3>Step 4: choose one concrete detail</h3>
<p>Examples:</p>
<ul><li>a digital simulation of the human heart;</li><li>grandmother opening her shop after a family setback;</li><li>rain tapping on leaves and sunlight entering through branches;</li><li>classmates guiding a bus after a blocked road.</li></ul>
<h3>Step 5: decide the final message</h3>
<p>What should the reader carry away?</p>
<ul><li>technology is useful when controlled;</li><li>admiration comes from character, not status;</li><li>difficulty revealed teamwork;</li><li>nature quietened an anxious mind.</li></ul>
<p>Now write. You are no longer inventing and organising at the same time.</p>
<h2>How many sentences should an FBISE paragraph contain?</h2>
<p>There is no official rule that a paragraph must contain exactly six, seven or eight sentences. Sentence length varies. A safe practical range is often:</p>
<ul><li><strong>SSC-I, 80–100 words:</strong> about 6–8 well-controlled sentences;</li><li><strong>HSSC-I, 100–120 words:</strong> about 7–10 well-controlled sentences.</li></ul>
<p>Do not force the count. A 95-word paragraph can have six developed sentences or nine shorter ones. The real test is whether the paragraph contains:</p>
<ol><li>a clear focus;</li><li>logically ordered support;</li><li>at least one specific detail;</li><li>a purposeful ending;</li><li>correct language within the word limit.</li></ol>
<p>Students sometimes produce twelve tiny sentences because short sentences feel safe. The result becomes childish and repetitive:</p>
<p class="ex">I went to Murree. It was cold. I saw trees. There were clouds. We ate food. We took pictures. I was happy. It was a good trip.</p>
<p>Combine related ideas and vary structure:</p>
<p class="ex">During our winter trip to Murree, cold mist covered the road and tall pine trees appeared and disappeared behind the clouds. We stopped near Kashmir Point, shared hot tea and took photographs while the valley slowly became visible below us.</p>
<p>The second version carries more information with smoother rhythm.</p>
<h2>Word count: how to stay within the limit without counting every word</h2>
<p>Word limits are part of the task. Writing far below the limit usually means the idea is underdeveloped. Writing far above it increases the chance of repetition, grammar errors and lost time.</p>
<p>You do not need to count every word during the exam. Train yourself using sentence estimates.</p>
<p>A typical exam sentence may contain 12–16 words. Therefore:</p>
<ul><li>seven medium sentences are often close to 90–105 words;</li><li>eight medium sentences are often close to 100–125 words.</li></ul>
<p>During practice, write the paragraph first, count it, and learn your personal average. Some students naturally write 10-word sentences; others write 22-word sentences. After five timed practices, you will know whether seven sentences are usually enough.</p>
<p>Use a margin mark after every 20 words during early practice if necessary. Do not do this permanently. The goal is to develop a visual sense of length.</p>
<p>When you are over the limit, cut:</p>
<ul><li>repeated adjectives;</li><li>background facts not needed for the main point;</li><li>phrases such as “in my personal opinion I think”;</li><li>empty openings such as “Since the beginning of time”;</li><li>duplicate conclusions.</li></ul>
<p>When you are under the limit, add:</p>
<ul><li>an example;</li><li>a reason;</li><li>a result;</li><li>a sensory detail;</li><li>a short contrast;</li><li>a final reflective sentence.</li></ul>
<p>Do not inflate the paragraph with unrelated material.</p>
<h2>Unity: the one-topic rule</h2>
<p>Unity means that the entire paragraph serves one central focus. This is the most important structural quality because it protects relevance.</p>
<p>Consider the topic <em>The Role of Technology in Education</em>.</p>
<p>Off-topic sentence:</p>
<p class="ex">Modern machines are also improving agriculture and transport.</p>
<p>The sentence may be true, but it belongs to a different paragraph. It does not develop education.</p>
<p>A sentence can also be technically related yet still weaken unity:</p>
<p class="ex">Many famous technology companies were founded in the United States.</p>
<p>This mentions technology but does not explain its educational role.</p>
<p>Use the “because test.” After each sentence, silently add: <strong>This belongs because…</strong></p>
<ul><li>“Students can replay recorded lessons.” This belongs because it shows flexible revision.</li><li>“Teachers can display animations of difficult processes.” This belongs because it shows clearer explanation.</li><li>“Phones can distract students during study.” This belongs because it qualifies the educational use of technology.</li><li>“Robots are used in car factories.” This does not belong.</li></ul>
<p>Unity does not mean every sentence uses the same words. It means every sentence answers the same controlling idea.</p>
<h2>Coherence: make the movement visible</h2>
<p>A unified paragraph can still feel confusing if the reader cannot see how one sentence leads to the next. Coherence comes from logical order, repeated key concepts, pronoun reference, transitions and sentence connections.</p>
<h3>Use transitions according to meaning</h3>
<p>Transitions are not decorations. Choose them for the relationship you need.</p>
<p><strong>Addition:</strong> moreover, furthermore, in addition, also<strong>Sequence:</strong> first, next, afterwards, finally<strong>Cause:</strong> because, since, as<strong>Effect:</strong> therefore, consequently, as a result<strong>Contrast:</strong> however, although, in contrast, yet<strong>Example:</strong> for example, for instance<strong>Conclusion:</strong> thus, overall, for this reason</p>
<p>Do not begin every sentence with “Moreover.” Do not use “therefore” when no cause-and-effect relationship exists. Excessive transitions make writing mechanical.</p>
<p>Mechanical:</p>
<p class="ex">Firstly, technology gives information. Secondly, technology helps teachers. Thirdly, technology helps students. Moreover, it saves time. Therefore, it is useful.</p>
<p>Natural:</p>
<p class="ex">Technology gives students immediate access to dictionaries, lectures and digital libraries. It also helps teachers explain complex ideas through diagrams and simulations. As a result, class time can be used for discussion and practice instead of copying long notes.</p>
<p>The second version uses links but does not announce every step.</p>
<h3>Use known-to-new movement</h3>
<p>Start a sentence with an idea already mentioned, then add new information.</p>
<p class="ex">Online libraries give students access to thousands of books. <strong>This access</strong> is especially valuable for learners whose schools have limited print collections. <strong>Such learners</strong> can compare sources and revise independently.</p>
<p>The repeated concepts—access and learners—create a chain.</p>
<h3>Keep pronouns clear</h3>
<p>Unclear:</p>
<p class="ex">Ahmed told Bilal that he should improve his paragraph.</p>
<p>Who should improve—Ahmed or Bilal? In exam writing, avoid ambiguous pronouns.</p>
<p>Clear:</p>
<p class="ex">Ahmed advised Bilal to improve the paragraph’s conclusion.</p>
<h3>Maintain tense and viewpoint</h3>
<p>A narrative that begins in the past should not suddenly switch into the present without a reason.</p>
<p>Incorrect:</p>
<p class="ex">We reached the river at noon and suddenly the bridge collapses.</p>
<p>Correct:</p>
<p class="ex">We reached the river at noon, and suddenly the bridge collapsed.</p>
<p>Similarly, do not shift randomly between “I,” “we,” “you” and “students.” Choose a viewpoint and control it.</p>
<h2>Development: how to turn one idea into a complete paragraph</h2>
<p>Students often understand focus and unity but still write thin paragraphs. Development means giving enough explanation and detail for the central idea to feel complete.</p>
<p>Use the <strong>Reason–Example–Effect</strong> pattern.</p>
<p>Claim:</p>
<p class="ex">Reading fiction develops empathy.</p>
<p>Reason:</p>
<p class="ex">It allows readers to experience events from viewpoints different from their own.</p>
<p>Example:</p>
<p class="ex">A story told by a refugee child, for instance, can make distant news feel personal and understandable.</p>
<p>Effect:</p>
<p class="ex">The reader may become slower to judge and more willing to consider another person’s circumstances.</p>
<p>This pattern is useful for opinion and explanatory topics. For descriptive topics, use <strong>Impression–Detail–Response</strong>.</p>
<p>Impression:</p>
<p class="ex">The woods felt calm but full of life.</p>
<p>Detail:</p>
<p class="ex">Sunlight broke through the branches, birds called from unseen nests, and wet leaves released an earthy smell.</p>
<p>Response:</p>
<p class="ex">After a stressful week, the quiet rhythm of the place made my thoughts feel ordered again.</p>
<p>For narrative topics, use <strong>Situation–Change–Result</strong>.</p>
<p>Situation:</p>
<p class="ex">Our class bus was returning from Abbottabad when heavy rain blocked the main road.</p>
<p>Change:</p>
<p class="ex">Instead of panicking, students used a map application and asked local villagers about a safer route.</p>
<p>Result:</p>
<p class="ex">Reaching home late seemed unimportant because the difficulty had taught us to cooperate.</p>
<p>These patterns make development manageable without encouraging memorised sentences.</p>
<h2>Vocabulary: precise beats difficult</h2>
<p>Examiners do not award marks simply because a word is long. A sophisticated word used incorrectly can damage meaning. Precise vocabulary is more valuable than decorative vocabulary.</p>
<p>Overwritten:</p>
<p class="ex">The resplendent and magnificent forest was extraordinarily mesmerising and exceptionally beautiful.</p>
<p>Precise:</p>
<p class="ex">The forest glowed after the rain, and drops of water shone on the pine needles.</p>
<p>The second version creates an image. The first piles up praise.</p>
<p>Use strong verbs:</p>
<ul><li>“The wind <strong>moved</strong> the leaves” becomes “The wind <strong>rustled</strong> the leaves.”</li><li>“The teacher <strong>gave</strong> an explanation” becomes “The teacher <strong>clarified</strong> the concept.”</li><li>“The crowd <strong>went</strong> towards the gate” becomes “The crowd <strong>surged</strong> towards the gate.”</li></ul>
<p>Use specific nouns:</p>
<ul><li>“things” becomes “devices,” “books,” “methods” or “problems”;</li><li>“place” becomes “valley,” “laboratory,” “library” or “market”;</li><li>“person” becomes “teacher,” “surgeon,” “volunteer” or “neighbour.”</li></ul>
<p>Avoid memorised expressions that do not fit:</p>
<p class="ex">It is an undeniable fact that every coin has two sides and technology is the backbone of a nation.</p>
<p>This sentence combines clichés without saying anything precise. Write what the topic needs.</p>
<h2>Grammar and punctuation: the errors that quietly reduce quality</h2>
<p>A strong structure cannot fully protect a paragraph filled with avoidable language errors. Focus on a small set of high-frequency checks.</p>
<h3>Subject–verb agreement</h3>
<p>Incorrect:</p>
<p class="ex">Social media platforms provides useful information.</p>
<p>Correct:</p>
<p class="ex">Social media platforms provide useful information.</p>
<h3>Tense consistency</h3>
<p>Incorrect:</p>
<p class="ex">We entered the woods and hear birds above us.</p>
<p>Correct:</p>
<p class="ex">We entered the woods and heard birds above us.</p>
<h3>Articles</h3>
<p>Incorrect:</p>
<p class="ex">Teacher showed us animation of heart.</p>
<p>Correct:</p>
<p class="ex">The teacher showed us an animation of the heart.</p>
<h3>Sentence boundaries</h3>
<p>Run-on:</p>
<p class="ex">The rain became heavier we decided to stop near a shop.</p>
<p>Correct:</p>
<p class="ex">The rain became heavier, so we decided to stop near a shop.</p>
<p>Fragment:</p>
<p class="ex">Because the road was blocked by fallen rocks.</p>
<p>Correct:</p>
<p class="ex">We changed our route because the road was blocked by fallen rocks.</p>
<h3>Commas after introductory words or clauses</h3>
<p class="ex">After the rain stopped, we continued our journey.</p>
<p class="ex">However, students must learn to control notifications.</p>
<p>Do not place commas between a subject and its verb:</p>
<p>Incorrect:</p>
<p class="ex">The greatest advantage of digital learning, is flexibility.</p>
<p>Correct:</p>
<p class="ex">The greatest advantage of digital learning is flexibility.</p>
<h3>Capitalisation and spelling</h3>
<p>Check sentence beginnings, names, places and the pronoun “I.” Avoid text-message spellings. “Because” should not become “bcz,” and “you” should not become “u.”</p>
<h2>Model SSC paragraph: The Role of Technology in Education</h2>
<p><strong>Approximate length: 96 words</strong></p>
<p class="ex">Technology has made education more flexible and engaging. Students can use digital libraries, recorded lectures and educational applications to revise difficult topics at their own pace. In the classroom, teachers can display diagrams, videos and simulations that make abstract ideas easier to understand. Technology also connects learners with courses that may not be available in their own schools. However, devices must be used with clear rules because games and notifications can interrupt concentration. When balanced with books, discussion and teacher guidance, technology becomes a powerful learning tool rather than a distraction.</p>
<h3>Why this model works</h3>
<p>The opening establishes a clear controlling idea: flexibility and engagement. The next three sentences develop access, classroom explanation and wider course availability. The caution is relevant rather than random. The final sentence gives a balanced judgment. The paragraph does not wander into medicine, business or general inventions. Vocabulary is accessible but precise.</p>
<h2>Model HSSC paragraph: The Person I Admire the Most</h2>
<p><strong>Approximate length: 116 words</strong></p>
<p class="ex">The person I admire most is my elder sister, not because of any public achievement but because of the courage she shows in ordinary life. When our father became ill, she continued her university studies while managing household responsibilities and tutoring two younger cousins. She rarely complained; instead, she planned each day carefully and asked for help when a task became too heavy. Her discipline taught me that strength does not mean pretending to have no difficulties. It means facing them honestly and continuing with purpose. Whenever I feel discouraged by study pressure, I remember her example and divide the problem into smaller steps. Her quiet resilience has influenced me more deeply than any speech.</p>
<h3>Why this model works</h3>
<p>The paragraph identifies one person and a specific basis for admiration. It proves the quality through a concrete situation. The reflection explains the writer’s personal response. The ending is fresh and meaningful. It is not a general biography.</p>
<h2>Model descriptive paragraph: A Walk in the Woods</h2>
<p class="ex">A walk in the woods can make an ordinary morning feel newly discovered. As I followed the narrow path, sunlight slipped through the branches and formed moving patterns on the ground. The air smelled of wet soil, while birds called from trees hidden by thick leaves. A small stream crossed the path, carrying pine needles over smooth stones. For a few minutes, the noise of traffic and unfinished work seemed very far away. The woods did not remove my problems, but their quiet order helped me see those problems more calmly. I returned tired, muddy and mentally refreshed.</p>
<p>Notice how the paragraph does not merely list “trees, birds, flowers and water.” It selects sensory details and connects them to an emotional effect.</p>
<h2>Model narrative-reflective paragraph: A Memorable Journey</h2>
<p class="ex">My most memorable journey was a school trip that did not follow the plan. On our return from Nathiagali, heavy rain caused a landslide and blocked the main road. At first, several students became anxious, but our teachers divided us into groups and asked us to remain inside while they contacted local officials. We shared food, checked on younger students and used a map to understand the alternative route. The delay lasted four hours, yet nobody was injured and the bus finally reached home safely. I remember the journey because an unexpected problem turned a group of classmates into a responsible team.</p>
<p>The paragraph contains a situation, a disruption, a response and a result. The reflection explains why it was memorable.</p>
<h2>Weak paragraph versus improved paragraph</h2>
<h3>Weak version</h3>
<p class="ex">Social media is very common. It has advantages and disadvantages. People use Facebook, Instagram and many other apps. Students waste time on it. It also gives information. There are fake news. People should use it carefully. Social media is a blessing and a curse. In conclusion, everything has good and bad effects.</p>
<p>Problems:</p>
<ul><li>The opening is broad and predictable.</li><li>Ideas are listed rather than developed.</li><li>“There are fake news” is grammatically incorrect because “news” is uncountable.</li><li>The final two sentences repeat the same idea.</li><li>No example or mechanism is explained.</li><li>The paragraph has weak progression.</li></ul>
<h3>Improved version</h3>
<p class="ex">Social media can support students, but only when they use it with a clear purpose. Educational pages provide explanations, current information and links to free courses, while class groups make it easier to share notices and resources. The same platforms, however, can waste hours through endless scrolling and can spread false information faster than students verify it. A learner should therefore follow reliable sources, limit screen time and pause before forwarding a claim. Social media is not automatically helpful or harmful; its effect depends largely on the habits of the person using it.</p>
<p>The improved version makes a balanced claim, explains both sides, identifies specific risks and ends with a judgment.</p>
<h2>Common paragraph mistakes and how to repair them</h2>
<h3>Mistake 1: memorised opening unrelated to the topic</h3>
<p class="ex">Since the dawn of civilisation, man has always struggled for progress.</p>
<p>This may appear in essays about science, education, discipline, tourism and health. Its vagueness consumes valuable words.</p>
<p>Repair: begin with the exact subject.</p>
<p class="ex">Regular exercise improves both physical health and mental concentration.</p>
<h3>Mistake 2: writing an essay instead of a paragraph</h3>
<p>Some students create several tiny paragraphs with headings. If the task asks for one paragraph, write one unified paragraph unless the official paper explicitly requests a different form.</p>
<p>Repair: keep all sentences in one block and use internal transitions.</p>
<h3>Mistake 3: too many ideas, none developed</h3>
<p class="ex">Pollution is caused by factories, cars, plastic, smoke, population, noise, waste and cutting trees.</p>
<p>Repair: select two or three linked causes and explain an effect or solution.</p>
<h3>Mistake 4: changing the topic midway</h3>
<p>A paragraph on a person admired begins with a teacher, shifts to the importance of education and ends with national development.</p>
<p>Repair: use the controlling-idea phrase as a test before every sentence.</p>
<h3>Mistake 5: unsupported praise</h3>
<p class="ex">My mother is great, wonderful, amazing, kind, loving and hardworking.</p>
<p>Repair: show one moment that proves two qualities.</p>
<p class="ex">During my board preparation, she adjusted household routines so that I could study quietly and still insisted that I sleep on time.</p>
<h3>Mistake 6: forced quotations</h3>
<p>A quotation can work, but an inaccurate or irrelevant quotation weakens the response. In a short paragraph, your own clear sentence is safer than a half-remembered line.</p>
<h3>Mistake 7: excessive “I think”</h3>
<p class="ex">I think technology is useful. I think it saves time. I think it helps students.</p>
<p>Repair: state the ideas directly. Your authorship already shows that they are your views.</p>
<h3>Mistake 8: conclusion introduces a new issue</h3>
<p class="ex">Therefore, technology is useful. The government should also build more roads.</p>
<p>Repair: the ending should complete the existing focus, not open another topic.</p>
<h3>Mistake 9: ignoring the word limit</h3>
<p>A 170-word answer to a 100-word task often contains repetition and consumes time needed elsewhere.</p>
<p>Repair: practise timed writing and learn your average sentence length.</p>
<h3>Mistake 10: proofreading only spelling</h3>
<p>Students search for spelling mistakes but miss missing verbs, unclear pronouns and tense changes.</p>
<p>Repair: proofread in three passes: meaning, grammar, surface accuracy.</p>
<h2>The 45-second proofreading routine</h2>
<p>When you finish, do not immediately move to the next question. Use a short routine.</p>
<h3>Pass 1: relevance and order</h3>
<p>Read only the first and last sentence. Do they express the same central idea? Then glance at each middle sentence. Does it support that idea?</p>
<h3>Pass 2: verbs and sentence boundaries</h3>
<p>Underline verbs mentally. Check tense and agreement. Look for two complete sentences joined without punctuation, and for fragments beginning with “because,” “although” or “when.”</p>
<h3>Pass 3: small accuracy checks</h3>
<p>Check capitals, full stops, common spellings, articles and repeated words. Count approximately. If the paragraph appears much too long, cut one weak or repeated sentence rather than squeezing the handwriting.</p>
<h2>A practical self-marking rubric</h2>
<p>FBISE marking details can vary by grade and task, so do not treat the grid below as an official point-by-point scheme. Use it as a training rubric aligned with the qualities examiners need to see.</p>
<h3>Content and relevance</h3>
<ul><li>Is the topic answered directly?</li><li>Is the central idea clear?</li><li>Are supporting points relevant?</li><li>Is there enough development for the word limit?</li></ul>
<h3>Organisation</h3>
<ul><li>Does the opening establish focus?</li><li>Do ideas follow a logical order?</li><li>Are transitions accurate and natural?</li><li>Does the final sentence complete the thought?</li></ul>
<h3>Language</h3>
<ul><li>Are verbs and tenses controlled?</li><li>Are sentences complete?</li><li>Is vocabulary precise rather than inflated?</li><li>Are articles, prepositions and pronouns mostly accurate?</li></ul>
<h3>Mechanics and presentation</h3>
<ul><li>Is punctuation clear?</li><li>Are spelling and capitalisation controlled?</li><li>Is handwriting legible?</li><li>Is the response close to the required length?</li></ul>
<p>Score each category from 0 to 3 during practice:</p>
<ul><li><strong>3:</strong> clear and controlled;</li><li><strong>2:</strong> generally effective with minor weakness;</li><li><strong>1:</strong> noticeable weakness affecting meaning;</li><li><strong>0:</strong> missing or seriously unclear.</li></ul>
<p>A student repeatedly scoring 2 in content but 1 in organisation knows exactly what to practise next.</p>
<h2>Seven-day paragraph improvement plan</h2>
<h3>Day 1: focus sentences</h3>
<p>Take ten topics and write only one controlling sentence for each. Do not write full paragraphs. Ask whether the sentence is narrow enough to guide 80–120 words.</p>
<h3>Day 2: support maps</h3>
<p>For five topics, write a controlling idea, three supports, one example and one ending. Limit planning to two minutes per topic.</p>
<h3>Day 3: descriptive detail</h3>
<p>Write three 90-word paragraphs about a place, person and event. Include two sensory or behavioural details in each. Remove empty adjectives.</p>
<h3>Day 4: explanatory paragraphs</h3>
<p>Write on education, health and technology. Use Reason–Example–Effect. Check whether each example actually proves the claim.</p>
<h3>Day 5: narrative-reflective paragraphs</h3>
<p>Write two short experiences. Use Situation–Change–Result and finish with a lesson that grows naturally from the event.</p>
<h3>Day 6: timed mixed practice</h3>
<p>Choose one unseen topic. Plan for two minutes, write for eight to ten minutes, proofread for one minute. Count the words afterwards.</p>
<h3>Day 7: error log</h3>
<p>Review all six days. Create a personal list of five recurring problems, such as tense shifts, repetition, weak endings, article errors or exceeding the limit. Write one final paragraph while concentrating only on those five issues.</p>
<p>Repeat the cycle with new topics. Improvement comes from targeted revision, not from producing many unreviewed paragraphs.</p>
<h2>Practice topics with planning prompts</h2>
<h3>The Importance of Time Management</h3>
<p>Controlling idea: time management reduces stress and improves quality.Supports: prioritising tasks; breaking work into sessions; leaving time for revision.Example: preparing a weekly study plan before board exams.Closing idea: a plan creates freedom rather than restriction.</p>
<h3>A Teacher Who Changed My Thinking</h3>
<p>Controlling idea: the teacher changed how I responded to mistakes.Supports: one classroom incident; feedback method; later effect on study habits.Concrete detail: returned a weak answer with questions instead of criticism.Closing idea: good teaching changes habits, not only marks.</p>
<h3>The Value of Sports</h3>
<p>Controlling idea: sports develop discipline and cooperation as well as fitness.Supports: regular practice; accepting rules; working with teammates.Example: losing a match and analysing mistakes together.Closing idea: the field teaches lessons that enter the classroom.</p>
<h3>A Rainy Day</h3>
<p>Controlling idea: rain transformed an ordinary day into a vivid experience.Supports: visual change; sound and smell; personal action or feeling.Detail: water gathering along the street while shopkeepers raised goods.Closing idea: the day revealed both beauty and inconvenience.</p>
<h3>Online Learning: Opportunity and Responsibility</h3>
<p>Controlling idea: online learning expands access but requires self-discipline.Supports: flexible schedule; wider courses; distraction and procrastination.Example: watching a recorded lesson and completing notes before opening social media.Closing idea: freedom works only with routine.</p>
<h2>Frequently asked questions</h2>
<h3>Should I write a heading?</h3>
<p>Follow the wording and layout of the paper. A short paragraph question generally does not require an elaborate heading. If you write the selected topic as a simple title, do not spend time decorating it. The quality of the body matters more.</p>
<h3>Can I use “Firstly, secondly, thirdly”?</h3>
<p>Yes, when sequence is genuinely useful. Do not use them automatically. Natural connections often sound better in a short paragraph.</p>
<h3>Can I write in the first person?</h3>
<p>Yes for personal, descriptive, narrative and reflective topics. For formal explanatory topics, first person is usually unnecessary, though a brief personal example may be relevant.</p>
<h3>Should I use difficult words to impress the examiner?</h3>
<p>Use accurate words. One precise verb is more impressive than three misused adjectives.</p>
<h3>What if I do not know facts about the topic?</h3>
<p>The paragraph task usually tests language and organisation, not specialised research knowledge. Use sensible, widely accepted reasoning and a realistic example. Do not invent extreme statistics.</p>
<h3>Is a quotation necessary?</h3>
<p>No. A relevant and accurately remembered quotation may enrich a response, but it is not a substitute for development. In a short paragraph, a specific example is often more useful.</p>
<h3>What if my paragraph is 10 words over the limit?</h3>
<p>Aim to stay close to the stated range. A small accidental variation is less serious than a badly underdeveloped or extremely long answer, but regular over-writing shows weak control. Train to fit the task.</p>
<h3>How can I improve quickly before the exam?</h3>
<p>Practise planning and revising, not only writing. Ten carefully corrected paragraphs are more useful than fifty unrevised ones. Keep an error log and rewrite weak openings and endings.</p>
<h2>Final exam checklist</h2>
<p>Before writing:</p>
<ul><li>identify the type of topic;</li><li>create a six-word controlling idea;</li><li>choose three supports and one detail;</li><li>decide the final message.</li></ul>
<p>While writing:</p>
<ul><li>answer the exact topic;</li><li>keep one central focus;</li><li>develop rather than list;</li><li>use transitions only when they show a real relationship;</li><li>stay near the word range.</li></ul>
<p>Before moving on:</p>
<ul><li>compare the first and last sentence;</li><li>check tense and subject–verb agreement;</li><li>repair fragments and run-ons;</li><li>remove one repeated idea;</li><li>confirm legibility and punctuation.</li></ul>
<h2>The principle to remember</h2>
<p>A full-mark paragraph is not a miniature essay filled with every idea you know. It is a controlled unit of thought. The opening tells the examiner where you are going. The middle develops that direction through logical support and a concrete detail. The final sentence shows why the point matters. Grammar and vocabulary then make the structure easy to read.</p>
<p>When students lose marks “because of structure,” the solution is not to memorise more paragraphs. It is to practise making decisions quickly: What is my exact focus? Which details prove it? In what order should they appear? What should the reader understand at the end? Once those decisions become automatic, unfamiliar topics stop feeling unfamiliar. The subject changes, but the thinking process remains dependable.</p>
<h2>Source and accuracy note</h2>
<p>This guide is aligned with current FBISE English assessment frameworks and model papers, including the SSC-I and HSSC-I paragraph tasks and their stated word ranges. Exact marks, word limits and formats can change by class and examination year, so students should verify the latest official model paper for their subject. Paragraph-development principles are also informed by Purdue OWL and literacy guidance from the Education Endowment Foundation.</p>
<h3>References</h3>
<ol><li>Federal Board of Intermediate and Secondary Education, <strong>Curriculum, Assessment Frameworks and Model Question Papers</strong>: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, <strong>Assessment Framework and Model Question Paper: English SSC-I, Curriculum 2022–23</strong>: https://www.fbise.edu.pk/ModelPaper/2025/Assessment%20Frameworks/SSC-I/Final%20Assessment%20Framework%20%2B%20Model%20Question%20Paper%20English%20SSC-I.pdf</li><li>FBISE, <strong>Assessment Framework and Model Question Paper: English HSSC-I, Curriculum 2022–23</strong>: https://www.fbise.edu.pk/ModelPaper/2025/Assessment%20Frameworks/HSSC-I/Final%20Assessment%20Framework%20%2B%20Model%20Question%20Paper%20English%20HSSC-I.pdf</li><li>Purdue Online Writing Lab, <strong>On Paragraphs</strong>: https://owl.purdue.edu/owl/general_writing/academic_writing/paragraphs_and_paragraphing/index.html</li><li>Education Endowment Foundation, <strong>Improving Literacy in Secondary Schools</strong>: https://educationendowmentfoundation.org.uk/education-evidence/guidance-reports/literacy-ks3-ks4</li></ol>', 'published', '2026-07-10 09:00:00'),
('Unseen Passages Without Panic', 'fbise-unseen-comprehension-passages-method', 'Comprehension', 'A repeatable method for reading, mapping and answering comprehension questions under time pressure.', '<h2>Why comprehension feels harder than it really is</h2>
<p>An unseen passage creates a special kind of pressure. The student cannot predict the topic, does not know which words will appear, and has to read, understand, select evidence, write complete answers and often produce a summary within a limited time. Even strong students panic because they treat the passage as a test of whether they already know the subject. It is not. It is mainly a test of whether they can build meaning from the text in front of them.</p>
<p>The current FBISE English assessment frameworks make that purpose clear. In the SSC-I model paper, the comprehension section uses a passage of approximately 250 to 350 words, followed by a summary task and short-answer questions. The HSSC-I framework also uses a passage of approximately 250 to 350 words, with a précis or summary and comprehension questions. The exact marks differ by class and paper, so students should always check the model paper for their own year. The common skill, however, is stable: read an unfamiliar text, identify its central and supporting ideas, and respond accurately in your own language.</p>
<p>A passage becomes frightening when a student tries to understand every word at once. Skilled readers do something different. They predict, question, clarify, connect and summarise. The Education Endowment Foundation’s guidance on reading comprehension similarly emphasises explicit strategies such as activating prior knowledge, prediction, questioning, clarifying and summarising. These strategies are not extra decoration. They reduce the amount of information the working memory has to hold and give the reader a purpose for every rereading.</p>
<p>This guide turns those principles into a practical exam method called <strong>R-M-A-C: Read, Map, Answer, Check</strong>. It is not an official FBISE acronym. It is a reusable routine designed to match the actual demands of unseen comprehension.</p>
<h2>What the examiner is really testing</h2>
<p>Comprehension questions may look different, but they usually test a small set of reading operations.</p>
<h3>1. Retrieving stated information</h3>
<p>The answer appears directly in the passage. The challenge is not invention but accurate selection.</p>
<p>Question: Why did the villagers repair the old well?</p>
<p>A weak student may write everything remembered about the well. A strong student locates the sentence that gives the reason and answers only that question.</p>
<h3>2. Explaining an idea</h3>
<p>The passage gives information, but the student must restate it clearly. Copying a full sentence may include irrelevant details or show that the meaning was not processed.</p>
<h3>3. Making an inference</h3>
<p>The answer is not written in one exact sentence. The reader combines clues.</p>
<p>For example, if a character checks the clock repeatedly, packs before dawn and refuses breakfast, the passage may never say, “She was anxious to leave.” The reader infers urgency or anxiety from the behaviour.</p>
<h3>4. Understanding vocabulary in context</h3>
<p>A familiar word may have an unfamiliar meaning in the passage. “Bright” can describe light, colour, intelligence or hope. The correct meaning comes from the surrounding sentence, not from the first dictionary definition remembered.</p>
<h3>5. Identifying purpose, tone or attitude</h3>
<p>The question may ask why the writer included an example, whether the tone is hopeful or critical, or what attitude is shown toward an issue. The answer must be supported by the language of the passage.</p>
<h3>6. Distinguishing main ideas from details</h3>
<p>A summary cannot contain everything. The student has to decide what the whole passage is doing and which points are essential to that purpose.</p>
<h3>7. Organising a concise written response</h3>
<p>Reading is only half the task. The answer must be grammatical, relevant and proportionate to the marks. A student can understand the text but still lose marks through vague pronouns, incomplete sentences or unnecessary copying.</p>
<p>Once students recognise these operations, an unseen passage stops being completely unseen. Its topic is new, but its question types are familiar.</p>
<h2>The R-M-A-C method at a glance</h2>
<p>Use this sequence every time:</p>
<ol><li><strong>Read</strong> for the overall situation and purpose.</li><li><strong>Map</strong> each paragraph in a few words and mark likely evidence.</li><li><strong>Answer</strong> the exact question using a clear claim and relevant support.</li><li><strong>Check</strong> meaning, grammar, references and word limits.</li></ol>
<p>The power of a method comes from repetition. Do not use one strategy at home and invent another in the exam. Practise the same sequence until it becomes automatic.</p>
<h2>Stage One: Read with two different speeds</h2>
<p>Many students read either too slowly or too quickly. Reading every line at maximum concentration wastes time before the questions are known. Skimming without returning to the text produces guesses. The solution is a two-speed reading process.</p>
<h3>First read: build the big picture</h3>
<p>On the first read, ask only four questions:</p>
<ul><li>What is the general topic?</li><li>Who or what is central?</li><li>What changes from the beginning to the end?</li><li>What seems to be the writer’s main message or purpose?</li></ul>
<p>Do not stop for every unknown word. Circle or lightly mark it and continue unless it blocks the entire sentence. A later sentence often explains the word indirectly.</p>
<p>At the end of the first read, force yourself to complete this sentence:</p>
<p class="ex">This passage is mainly about __________, and the writer shows/explains/argues that __________.</p>
<p>Your first answer may be rough. That is fine. Its purpose is to create a mental frame.</p>
<h3>Second read: read through the questions</h3>
<p>Now scan the questions before reading closely again. Each question gives you a search target. Underline command words such as <strong>why</strong>, <strong>how</strong>, <strong>what evidence</strong>, <strong>in your own words</strong>, <strong>according to the passage</strong>, <strong>suggest</strong>, <strong>tone</strong>, <strong>title</strong>, or <strong>summary</strong>.</p>
<p>Then reread the passage more slowly. Mark the line or paragraph connected to each question. Some students write tiny question numbers in the margin. For example, “Q2” beside the sentence that explains the cause, or “Q4” beside an example of the writer’s attitude. This simple mapping prevents repeated full readings.</p>
<h3>Why the order matters</h3>
<p>Students often read the questions first without seeing the passage at all. That can be useful for experienced readers, but beginners may create false expectations and search only for isolated words. Reading the passage quickly first gives context; scanning questions second gives purpose; close reading third joins the two.</p>
<h2>Stage Two: Map the passage paragraph by paragraph</h2>
<p>A map is a very short note that records the job of each paragraph. It should not be a full sentence copied from the text. Aim for three to seven words.</p>
<p>Imagine a passage with four paragraphs:</p>
<ul><li>Paragraph 1: city heat is increasing</li><li>Paragraph 2: concrete traps heat</li><li>Paragraph 3: trees provide cooling</li><li>Paragraph 4: community planting solution</li></ul>
<p>That map already reveals the structure: problem, cause, evidence, response. A summary can now follow the same logic without becoming a random list.</p>
<h3>Common passage structures</h3>
<p>Recognising structure helps you predict where answers are likely to be.</p>
<p>Cause and effect</p>
<p>The writer explains why something happens and what it produces.</p>
<p>Signal words: because, due to, therefore, as a result, consequently, leads to, results in.</p>
<p>Problem and solution</p>
<p>The writer presents a difficulty and possible responses.</p>
<p>Signal words: problem, challenge, however, one solution, to address this, can be improved by.</p>
<p>Comparison and contrast</p>
<p>Two ideas, systems, people or periods are compared.</p>
<p>Signal words: similarly, unlike, whereas, in contrast, both, on the other hand.</p>
<p>Chronological sequence</p>
<p>Events or stages are presented in time order.</p>
<p>Signal words: first, later, after, eventually, during, by the time.</p>
<p>Claim and evidence</p>
<p>The writer makes a point and supports it with data, examples, expert views or explanation.</p>
<p>Signal words: for example, research shows, this suggests, evidence, according to.</p>
<p>Description and significance</p>
<p>The passage describes a person, place, object or practice and then explains why it matters.</p>
<p>A passage may combine structures. Your map should reflect the main movement, not force every sentence into one category.</p>
<h2>Stage Three: Answer the exact question</h2>
<p>A good comprehension answer is not the longest answer. It is the smallest complete answer that satisfies the command and includes the necessary evidence.</p>
<p>Use the <strong>D-E-R rule: Direct answer, Evidence, Relevant explanation</strong>.</p>
<ul><li><strong>Direct answer:</strong> respond to the question immediately.</li><li><strong>Evidence:</strong> include the fact or clue from the passage.</li><li><strong>Relevant explanation:</strong> show how the evidence answers the question when needed.</li></ul>
<p>Not every one-mark or short question needs all three parts in separate sentences. The rule is a thinking guide.</p>
<h3>Example: stated information</h3>
<p>Question: Why did the school open its library before classes?</p>
<p>Passage information: Many students travelled on early buses and had nowhere quiet to study before the first lesson.</p>
<p>Weak answer:</p>
<p class="ex">The school library is very useful and contains many books for students.</p>
<p>The sentence is generally true but does not answer why it opened early.</p>
<p>Better answer:</p>
<p class="ex">The school opened the library before classes because students arriving on early buses needed a quiet place to study.</p>
<h3>Example: inference</h3>
<p>Question: What suggests that Mariam did not expect the experiment to succeed?</p>
<p>Evidence: She placed the seed tray on a forgotten shelf and laughed when her brother asked when the plants would appear.</p>
<p>Strong answer:</p>
<p class="ex">Mariam’s decision to leave the tray on a forgotten shelf and her laughter at her brother’s question suggest that she had little confidence in the experiment.</p>
<p>The answer identifies behaviour and explains the inference.</p>
<h3>Example: writer’s purpose</h3>
<p>Question: Why does the writer mention a village that reduced water loss by repairing pipes?</p>
<p>Strong answer:</p>
<p class="ex">The example shows that practical maintenance can conserve water effectively; it turns the writer’s general claim into a concrete, believable result.</p>
<h3>Avoid the “same words, no meaning” trap</h3>
<p>Students sometimes copy a sentence because it contains the same keyword as the question. Suppose the question asks, “How did the project change public attitudes?” The sentence containing “project” may only describe when it began. Search for the relationship in the question, not just the repeated noun.</p>
<h2>Writing in your own words without changing the meaning</h2>
<p>“In your own words” does not mean replacing every word with a difficult synonym. It means preserving the idea while changing the expression and sentence structure.</p>
<p>Original:</p>
<p class="ex">The sudden closure of the bridge forced commuters to seek longer alternative routes.</p>
<p>Poor paraphrase:</p>
<p class="ex">The abrupt shutting of the bridge compelled commuters to look for lengthier substitute paths.</p>
<p>This is mechanical synonym replacement. Some choices sound unnatural, and the sentence may become less clear.</p>
<p>Better paraphrase:</p>
<p class="ex">When the bridge closed unexpectedly, people travelling to work had to use routes that took more time.</p>
<p>The meaning remains, but the structure and vocabulary are natural.</p>
<h3>A three-step paraphrasing method</h3>
<ol><li>Read the sentence and look away.</li><li>Say the idea to yourself in simple language.</li><li>Write that idea, then compare it with the original to check accuracy.</li></ol>
<p>Useful transformations include:</p>
<ul><li>changing active to passive or passive to active where natural;</li><li>turning a noun phrase into a verb phrase;</li><li>combining two short ideas;</li><li>splitting a complex sentence;</li><li>replacing a phrase with a familiar equivalent;</li><li>changing the order of cause and effect.</li></ul>
<p>Do not alter technical terms, names, numbers or essential concepts simply to appear original.</p>
<h2>Vocabulary questions: context before dictionary memory</h2>
<p>When asked for the meaning of a word, read the full sentence and at least one sentence before and after it.</p>
<p>Use four clues:</p>
<h3>Definition clue</h3>
<p>The writer explains the word.</p>
<p class="ex">The material is biodegradable, meaning that natural processes can break it down.</p>
<h3>Contrast clue</h3>
<p>An opposite idea reveals the meaning.</p>
<p class="ex">Unlike the abundant rainfall of the northern region, water was scarce in the valley.</p>
<p>“Scarce” contrasts with “abundant,” so it means limited or insufficient.</p>
<h3>Example clue</h3>
<p>Examples show the category.</p>
<p class="ex">Nocturnal animals, such as owls and bats, are active after sunset.</p>
<h3>Cause-effect clue</h3>
<p>The result indicates the word’s meaning.</p>
<p class="ex">The path was treacherous; two hikers slipped within the first ten minutes.</p>
<p>The slips suggest that “treacherous” means dangerous or unsafe.</p>
<p>After choosing a meaning, replace the original word in the sentence. If the sentence still makes sense, the answer is likely correct.</p>
<h2>Pronoun reference: ask “who or what exactly?”</h2>
<p>Questions may ask what words such as <strong>it</strong>, <strong>they</strong>, <strong>this</strong>, <strong>these</strong>, <strong>which</strong> or <strong>such behaviour</strong> refer to. Do not automatically choose the nearest noun. Choose the noun or idea that makes grammatical and logical sense.</p>
<p>Example:</p>
<p class="ex">The committee rejected the proposal after reviewing its cost. This disappointed the volunteers.</p>
<p>“This” refers not merely to “cost” but to the committee’s rejection of the proposal.</p>
<p>Write a precise answer:</p>
<p class="ex">“This” refers to the committee’s decision to reject the proposal.</p>
<h2>Tone and attitude: prove the label</h2>
<p>Tone is the writer’s attitude as expressed through language. Common labels include concerned, hopeful, critical, appreciative, humorous, reflective, objective, urgent and persuasive.</p>
<p>Do not choose a tone because the topic itself is sad or serious. Look at the writer’s words.</p>
<p>A passage about pollution could be:</p>
<ul><li>objective if it neutrally explains measurements;</li><li>critical if it condemns negligence;</li><li>urgent if it calls for immediate action;</li><li>hopeful if it focuses on successful solutions.</li></ul>
<p>A complete tone answer uses this pattern:</p>
<p class="ex">The tone is <strong>concerned but hopeful</strong>. Words describing the damage create concern, while the successful community response suggests that improvement is possible.</p>
<p>The evidence does not need to be a long quotation. A short reference to the language is enough.</p>
<h2>Main idea and suitable title questions</h2>
<p>The main idea is not simply the topic. “Trees” is a topic. “Urban trees reduce heat and improve the health of crowded neighbourhoods” is a main idea.</p>
<p>Use this formula:</p>
<p class="ex">Topic + writer’s central point about the topic</p>
<p>A good title should be:</p>
<ul><li>broad enough to cover the whole passage;</li><li>specific enough to show its focus;</li><li>brief and accurate;</li><li>free from a minor detail that appears only once.</li></ul>
<p>For a passage explaining how school gardens improve nutrition, science learning and responsibility, weak titles include “Vegetables,” “My School” or “A Science Lesson.” A stronger title is “Why School Gardens Matter.”</p>
<p>When asked to justify the title, connect it to at least two major parts of the passage.</p>
<h2>Summary and précis: selection before compression</h2>
<p>A summary is not produced by shortening every paragraph equally. It is produced by selecting the central idea and the essential supporting points, then rebuilding them as one coherent text.</p>
<h3>What belongs in a summary</h3>
<p>Include:</p>
<ul><li>the central claim or situation;</li><li>major causes, effects, stages or solutions;</li><li>essential relationships between ideas;</li><li>the conclusion when it completes the argument.</li></ul>
<p>Usually exclude:</p>
<ul><li>repeated explanation;</li><li>minor examples;</li><li>decorative description;</li><li>quotations;</li><li>isolated statistics unless essential;</li><li>personal comments;</li><li>information not present in the passage.</li></ul>
<h3>The 5-S summary method</h3>
<ol><li><strong>Survey</strong> the paragraph map.</li><li><strong>Select</strong> one essential point from each major part.</li><li><strong>Shrink</strong> examples into general statements.</li><li><strong>Sequence</strong> points in the passage’s logical order.</li><li><strong>Scan</strong> for accuracy, coherence and length.</li></ol>
<h3>Turning examples into a general point</h3>
<p>Passage details:</p>
<p class="ex">Students measured rainfall, recorded plant growth and compared soil samples.</p>
<p>Summary form:</p>
<p class="ex">Students developed practical scientific skills through regular observation and measurement.</p>
<p>The summary preserves the purpose of the examples without listing all of them.</p>
<h3>Avoid the “sentence surgery” method</h3>
<p>Some students copy one sentence from each paragraph and remove a few words. The result often has broken references, repeated ideas and no flow. A summary should sound like a new, complete piece of writing.</p>
<h3>Give the summary a clear beginning</h3>
<p>A summary should not begin with a tiny detail. Start with the passage’s central subject and direction.</p>
<p>Weak opening:</p>
<p class="ex">There were three bins near the gate.</p>
<p>Stronger opening:</p>
<p class="ex">A school recycling programme succeeded because students combined simple facilities with clear responsibilities and regular monitoring.</p>
<h3>Title for a summary</h3>
<p>Where a title is required, write it after understanding the whole passage. Keep it concise and central. Do not use a sentence-length title or one based on the first paragraph only.</p>
<h2>A complete original worked passage</h2>
<p>Read the following original passage. It was written for this guide and is not taken from an examination paper.</p>
<h3>Passage: The Quiet Hour</h3>
<p>When a secondary school in Rawalpindi asked students why they rarely used the library, the answers surprised the teachers. The students did not dislike reading, and most could name books they wanted to explore. Their main complaint was that the school day offered no quiet time. Breaks were noisy, buses left soon after classes, and many homes were shared by large families. The library existed, but the timetable made it difficult to use.</p>
<p>The school responded with a small experiment called the Quiet Hour. Twice a week, the library opened forty minutes before the first lesson. Students could read, revise or complete homework, but phones and group discussions were not allowed. At first, only twelve students attended. The librarian resisted the temptation to advertise the programme with prizes. Instead, she asked regular visitors to recommend books and displayed their handwritten notes beside the shelves.</p>
<p>Within two months, attendance had more than tripled. Teachers also noticed an unexpected change: students who used the Quiet Hour began arriving in class with more specific questions. They did not necessarily earn perfect scores, but they appeared more prepared to identify what they did not understand. The programme therefore improved more than reading time; it encouraged students to take responsibility for their learning.</p>
<p>The experiment was not free of problems. Opening early required staff cooperation, and winter transport made attendance difficult for some students. The school avoided presenting the programme as a complete solution. It later added one afternoon session and trained senior students to supervise part of it. The Quiet Hour succeeded because it addressed a real barrier, listened to students and adjusted when the first plan excluded some learners.</p>
<h3>Paragraph map</h3>
<ul><li>Paragraph 1: lack of quiet time</li><li>Paragraph 2: early library experiment</li><li>Paragraph 3: growth and learning benefits</li><li>Paragraph 4: difficulties and adjustments</li></ul>
<h3>Question 1: Why were students not using the library regularly?</h3>
<p>Model answer:</p>
<p class="ex">Students were not using the library regularly because the school schedule and their home circumstances gave them little quiet time for reading or study.</p>
<p>Why it works: It combines the timetable problem and crowded-home context without copying the full paragraph.</p>
<h3>Question 2: Why did the librarian avoid offering prizes?</h3>
<p>The passage does not directly state her private thought, so the answer requires a cautious inference.</p>
<p>Model answer:</p>
<p class="ex">The librarian appears to have wanted students to develop genuine interest and peer-supported reading habits rather than attend only for rewards.</p>
<p>Why it works: “Appears” signals inference, and the answer contrasts prizes with recommendations from regular readers.</p>
<h3>Question 3: What unexpected benefit did teachers observe?</h3>
<p>Model answer:</p>
<p class="ex">Teachers observed that students came to class with more precise questions and were better able to recognise gaps in their understanding.</p>
<h3>Question 4: How did the school make the programme more inclusive?</h3>
<p>Model answer:</p>
<p class="ex">It added an afternoon session for students who could not arrive early and trained senior students to help supervise the programme.</p>
<h3>Question 5: What is the main lesson of the passage?</h3>
<p>Model answer:</p>
<p class="ex">A school initiative is more likely to succeed when it responds to a genuine student need, begins on a manageable scale and changes in response to practical difficulties.</p>
<h3>Suitable title</h3>
<p class="ex"><strong>Making Space for Quiet Learning</strong></p>
<p>This title covers both the physical opportunity and the broader learning responsibility developed by the programme.</p>
<h3>Model summary</h3>
<p class="ex">A Rawalpindi school discovered that students avoided its library because they lacked quiet study time rather than interest in books. It introduced an early-morning Quiet Hour for independent reading and revision. Attendance grew, and participants became more prepared and self-aware learners. Because early opening created staffing and transport difficulties, the school added an afternoon session and student supervision. The programme worked by addressing a real need and adapting to unequal access.</p>
<p>Notice that the summary omits the exact number of early visitors, handwritten notes and winter details. These support the passage but are not all necessary in the compressed version.</p>
<h2>Time management under examination conditions</h2>
<p>Exact timing depends on the paper and total marks, so use the current model paper for your class. A useful principle is to allocate time in proportion to marks and reserve a final checking period.</p>
<p>For a passage with summary and several questions, divide the task into five phases:</p>
<ol><li>First reading and overall meaning.</li><li>Question scan and paragraph mapping.</li><li>Short-answer responses.</li><li>Summary or précis.</li><li>Final check.</li></ol>
<p>Do not spend ten minutes fighting one vocabulary word while leaving a multi-mark summary unfinished. Mark the difficult item, continue and return later.</p>
<h3>Answer order</h3>
<p>Some students write the summary first because it appears first. Others answer questions first because the questions deepen their understanding. Both can work. For most learners, this order is efficient:</p>
<ul><li>read and map;</li><li>answer the direct comprehension questions;</li><li>write the summary after the passage is fully understood;</li><li>return to difficult inference or vocabulary items.</li></ul>
<p>However, never copy question answers into the summary without checking whether they represent central ideas.</p>
<h2>Common mistakes and how to repair them</h2>
<h3>Mistake 1: Writing from general knowledge</h3>
<p>Question: According to the passage, why are local markets valuable?</p>
<p>The student writes about jobs, culture and cheap food even though the passage discusses reduced transport distance and fresher produce.</p>
<p>Repair: Begin with “According to the passage…” mentally, even when those words are not required in the answer.</p>
<h3>Mistake 2: Copying too much</h3>
<p>A four-line copied answer often contains the correct fact but hides it among irrelevant information.</p>
<p>Repair: Underline the exact idea, then close the passage briefly and state it in one or two clean sentences.</p>
<h3>Mistake 3: Answering “what” instead of “why”</h3>
<p>Question: Why did the team change its plan?</p>
<p>Student: They moved the event indoors.</p>
<p>This states what happened, not why.</p>
<p>Repair: Circle the command word before answering.</p>
<h3>Mistake 4: Unsupported inference</h3>
<p>The student invents motives that the passage does not suggest.</p>
<p>Repair: Use at least two textual clues, and use cautious verbs such as “suggests,” “indicates” or “implies” where appropriate.</p>
<h3>Mistake 5: Vague pronouns</h3>
<p class="ex">They did this because it was difficult.</p>
<p>Who are “they”? What is “this”? What was difficult?</p>
<p>Repair: Repeat the key noun when clarity requires it.</p>
<h3>Mistake 6: A summary that is merely a list</h3>
<p class="ex">First this happened. Then this. Also this. Moreover this.</p>
<p>Repair: Group related ideas and show cause, contrast or result through accurate transitions.</p>
<h3>Mistake 7: Personal opinion in a summary</h3>
<p class="ex">I think the school made an excellent decision.</p>
<p>Repair: Keep the summary faithful to the passage unless the question explicitly asks for your view.</p>
<h3>Mistake 8: Ignoring word limits</h3>
<p>An overlong summary usually contains examples and repetition; an extremely short one may omit the central relationship.</p>
<p>Repair: Count approximately by line or sentence during practice, then learn what the expected length looks like in your handwriting.</p>
<h3>Mistake 9: Treating every unknown word as essential</h3>
<p>Repair: Ask whether the sentence’s main idea remains understandable. Continue reading and use context clues before spending time on the word.</p>
<h3>Mistake 10: Changing tense or viewpoint carelessly</h3>
<p>A summary may begin in the present tense, shift to past and then address “you.”</p>
<p>Repair: Choose a consistent reporting frame, usually present tense for what a passage explains or past tense for a completed event.</p>
<h2>Grammar for short answers</h2>
<p>Comprehension is not a separate world from grammar. Clear grammar protects meaning.</p>
<h3>Convert question form into statement form</h3>
<p>Question: Why did the villagers leave?</p>
<p>Answer:</p>
<p class="ex">The villagers left because…</p>
<p>Do not write:</p>
<p class="ex">Because why the river flooded.</p>
<h3>Use complete sentences unless instructions permit otherwise</h3>
<p>Fragments can create ambiguity.</p>
<p>Fragment:</p>
<p class="ex">Because of the heavy rain.</p>
<p>Complete answer:</p>
<p class="ex">The match was postponed because of the heavy rain.</p>
<h3>Keep subject and pronoun reference clear</h3>
<p>When two people appear in the previous sentence, avoid beginning with “he” unless the identity is obvious.</p>
<h3>Use the right tense</h3>
<p>If the passage narrates past events, answer in the past. If it describes a continuing fact, the present may be correct.</p>
<h3>Avoid unnecessary introductions</h3>
<p>Do not begin every response with “In my opinion, I think that according to the passage…” A direct answer is stronger.</p>
<h2>A diagnostic approach for teachers and self-study</h2>
<p>When a student receives low comprehension marks, “read more” is not a precise diagnosis. Identify the exact stage of failure.</p>
<p>Ask:</p>
<ul><li>Did the student misunderstand the passage’s main idea?</li><li>Could the student locate the relevant sentence?</li><li>Did the student understand the sentence but fail to paraphrase it?</li><li>Was the inference unsupported?</li><li>Did grammar make the answer unclear?</li><li>Did the student know the content but misread the command?</li><li>Was time lost through repeated reading?</li></ul>
<p>Different errors need different practice. A student who cannot locate evidence needs mapping and scanning drills. A student who copies accurately but cannot paraphrase needs oral restatement practice. A student who understands but writes fragments needs sentence-construction practice.</p>
<h3>Error log</h3>
<p>After each practice passage, create four columns:</p>
<div class="atable-wrap"><table class="atable"><thead><tr><th><strong>Question type</strong></th><th><strong>My error</strong></th><th><strong>Correct reasoning</strong></th><th><strong>Rule for next time</strong></th></tr></thead><tbody><tr><td>Inference</td><td>Guessed motive</td><td>Two clues showed urgency, not anger</td><td>Name clues before label</td></tr><tr><td>Vocabulary</td><td>Used first dictionary meaning</td><td>Contrast showed “reserved” meant quiet</td><td>Test meaning in sentence</td></tr><tr><td>Summary</td><td>Included examples</td><td>Examples supported one general point</td><td>Generalise repeated details</td></tr></tbody></table></div>
<p>The final column turns correction into a reusable lesson.</p>
<h2>Building reading stamina without wasting practice papers</h2>
<p>Do not complete full exam passages every day. Mix focused drills with complete practice.</p>
<h3>Five-minute mapping drill</h3>
<p>Take a 300-word article. Write a five-word note beside each paragraph and one sentence for the main idea.</p>
<h3>Evidence hunt</h3>
<p>Write three questions about a passage and mark the exact words that support each answer.</p>
<h3>One-sentence inference drill</h3>
<p>Choose a character action and write: clue + inference + explanation.</p>
<h3>Paraphrase ladder</h3>
<p>Rewrite one sentence three ways:</p>
<ol><li>simpler vocabulary;</li><li>changed sentence structure;</li><li>concise answer form.</li></ol>
<h3>Summary reduction</h3>
<p>Reduce a 200-word passage to 100 words, then 60, then 30. Compare what must remain at each level.</p>
<h3>Oral comprehension</h3>
<p>Explain the passage aloud as though speaking to a younger student. If the explanation becomes confused, the mental map is incomplete.</p>
<h2>A seven-day comprehension reset plan</h2>
<h3>Day 1: Main idea</h3>
<p>Read three short passages and write only the topic plus the writer’s central point.</p>
<h3>Day 2: Paragraph maps</h3>
<p>Map two passages. Do not answer questions.</p>
<h3>Day 3: Direct questions</h3>
<p>Practise locating and paraphrasing stated information.</p>
<h3>Day 4: Inference and tone</h3>
<p>For every answer, write the textual clue beside it.</p>
<h3>Day 5: Vocabulary in context</h3>
<p>Collect ten words and identify which context clue revealed each meaning.</p>
<h3>Day 6: Summary</h3>
<p>Write one timed summary, then highlight central ideas in one colour and unnecessary details in another.</p>
<h3>Day 7: Full timed practice</h3>
<p>Complete a passage under realistic conditions. Review errors by category, not just score.</p>
<p>Repeat the cycle with greater speed and harder texts.</p>
<h2>Practice passage two: The Repair Table</h2>
<p>The following passage is also original.</p>
<p>A group of college students noticed that broken household items were often discarded even when the damage was minor. A lamp with a loose wire, a chair with one unstable joint or a fan with a damaged switch could usually be repaired, but many owners lacked tools or confidence. The students therefore set up a monthly Repair Table in a community hall.</p>
<p>Visitors did not simply hand over objects and collect them later. They sat beside volunteers, watched the diagnosis and completed safe parts of the repair themselves. This rule slowed the process, yet it served the project’s larger purpose. The organisers wanted people to understand their belongings and become less dependent on replacement.</p>
<p>The project also revealed limits. Some electrical devices were unsafe to open without specialised training, and replacement parts were not always available. Volunteers learned to refuse repairs that could create danger. They also began recording which products failed repeatedly. Over time, these records helped residents make more informed purchases.</p>
<p>The Repair Table did not end waste in the neighbourhood. Its achievement was smaller but still valuable: it changed disposal from an automatic response into a decision that people questioned.</p>
<h3>Practice questions</h3>
<ol><li>What problem led to the creation of the Repair Table?</li><li>Why were visitors required to participate in repairs?</li><li>Why did the organisers sometimes refuse a repair?</li><li>What additional benefit came from recording repeated product failures?</li><li>Explain the meaning of “automatic response” in the final paragraph.</li><li>Suggest a suitable title and justify it.</li><li>Summarise the passage in approximately 70 to 90 words.</li></ol>
<h3>Answer guide</h3>
<ol><li>The table was created because people threw away items with minor, repairable faults due to a lack of tools or confidence.</li><li>Participation helped visitors learn how their belongings worked and reduced dependence on buying replacements.</li><li>Repairs were refused when opening or fixing an item required specialised skills and could be unsafe.</li><li>The records helped residents recognise unreliable products and make better purchasing decisions.</li><li>It means throwing an item away without first considering whether it could be repaired.</li><li>A suitable title is <strong>“Repair Before Replacement”</strong> because the passage describes a project that teaches residents to question unnecessary disposal and learn safe repair skills.</li><li>Model summary:</li></ol>
<p class="ex">College students created a monthly Repair Table after noticing that residents discarded easily repairable items. Visitors worked beside volunteers so they could gain practical understanding instead of depending entirely on replacement. The organisers rejected unsafe repairs and documented products that failed repeatedly, helping residents make wiser purchases. Although the project did not eliminate waste, it encouraged people to consider repair before automatically throwing damaged objects away.</p>
<h2>High-level questions: evaluation without leaving the text</h2>
<p>Some questions ask whether a solution was effective, whether an argument is convincing or which action was most important. These require evaluation, but the answer must still be grounded in the passage.</p>
<p>Use this structure:</p>
<ol><li>Make a judgement.</li><li>Give the passage-based criterion.</li><li>Cite the strongest evidence.</li><li>Acknowledge a limit if relevant.</li></ol>
<p>Example:</p>
<p class="ex">The Quiet Hour was reasonably effective because attendance increased and students became more prepared for lessons. However, its early schedule initially excluded some learners, so its success depended on the later afternoon option.</p>
<p>This answer is balanced and evidence-based. It does not turn into a general essay about libraries.</p>
<h2>Reading scientific or informational passages</h2>
<p>Scientific passages may contain unfamiliar terms, but their structure is often highly organised.</p>
<p>Look for:</p>
<ul><li>the phenomenon being explained;</li><li>variables or conditions;</li><li>a process or sequence;</li><li>evidence or observation;</li><li>limitation or uncertainty;</li><li>conclusion or application.</li></ul>
<p>Do not panic at technical nouns. Ask what each sentence is doing. A term may simply name an object; the question may test the cause-and-effect relationship around it.</p>
<p>For data mentioned in prose, distinguish exact findings from interpretation. “Attendance rose by 20 percent” is data. “The programme was popular” is an interpretation. A strong answer does not confuse the two.</p>
<h2>Reading narrative passages</h2>
<p>Narrative comprehension involves more than recalling events. Track:</p>
<ul><li>setting;</li><li>goal;</li><li>obstacle;</li><li>decision;</li><li>consequence;</li><li>change in understanding.</li></ul>
<p>For character questions, use actions, speech and choices as evidence. Avoid labels such as “good,” “bad” or “nice” unless you explain the behaviour behind them.</p>
<p>Instead of:</p>
<p class="ex">Ahmed was brave.</p>
<p>Write:</p>
<p class="ex">Ahmed showed courage by returning to warn the hikers even though the storm had already begun.</p>
<h2>Reading argumentative passages</h2>
<p>Identify:</p>
<ul><li>the writer’s main claim;</li><li>reasons supporting it;</li><li>examples or evidence;</li><li>opposing view, if present;</li><li>response to that view;</li><li>final recommendation.</li></ul>
<p>Words such as “some people argue,” “however,” and “although” often introduce or answer a counterargument. Do not mistake the opposing view for the writer’s own position.</p>
<h2>What “critical reading” means at school level</h2>
<p>Critical reading does not mean criticising everything. It means asking disciplined questions:</p>
<ul><li>What claim is being made?</li><li>What evidence supports it?</li><li>Is the example representative or isolated?</li><li>Does the conclusion follow from the evidence?</li><li>What assumption is present?</li><li>Is another explanation possible?</li><li>What limitation does the writer acknowledge or ignore?</li></ul>
<p>Answer only at the level requested. A short comprehension question rarely needs a full debate.</p>
<h2>Final exam checklist</h2>
<p>Before leaving the passage, check:</p>
<ul><li>Have I answered every question?</li><li>Did I respond to the command word?</li><li>Is each answer based on the passage?</li><li>Are pronouns clear?</li><li>Did I support inferences with clues?</li><li>Is my summary centred on main ideas rather than examples?</li><li>Did I avoid personal opinion where it was not requested?</li><li>Is the title broad enough for the whole passage?</li><li>Are tense and sentence structure correct?</li><li>Have I stayed near the required length?</li></ul>
<h2>Frequently asked questions</h2>
<h3>Should I read the questions before the passage?</h3>
<p>A quick first reading of the passage followed by a question scan is safest for most students. Experienced readers may preview questions first, but they must still build an overall understanding rather than hunt isolated keywords.</p>
<h3>Can I copy exact words from the passage?</h3>
<p>Technical terms, names and essential phrases may need to remain. For explanatory answers, paraphrase naturally when instructed and avoid copying entire sentences that contain irrelevant material.</p>
<h3>How long should a short answer be?</h3>
<p>Length should reflect the command and marks. Many answers can be completed in one or two precise sentences. More writing does not compensate for weak relevance.</p>
<h3>What should I do when I do not know one word?</h3>
<p>Continue reading, inspect contrast or examples, and decide whether the word is essential. Use context before guessing.</p>
<h3>How do I improve inference questions?</h3>
<p>Write the evidence first during practice. Then ask what conclusion a reasonable reader can draw from those clues. Avoid motives or emotions that the text does not support.</p>
<h3>Is a summary written in the same order as the passage?</h3>
<p>Usually the passage’s logical order is the clearest, but repeated points may be combined. The summary must remain coherent and faithful.</p>
<h3>Should I count every word?</h3>
<p>In practice, count accurately so you learn the visual length of your work. In an exam, use a quick count method unless the instructions require an exact total.</p>
<h3>Can I use difficult vocabulary to impress the examiner?</h3>
<p>Use precise vocabulary you control. An ordinary correct word is better than an advanced word with the wrong meaning or tone.</p>
<h2>The deeper lesson</h2>
<p>Comprehension is not a talent that some students possess and others lack. It is a sequence of decisions. First build the whole picture. Then map the parts. Locate evidence, answer the command, and compress only after understanding. Under pressure, the student with a routine is calmer than the student waiting for inspiration.</p>
<p>The topic of the next passage may be unfamiliar. The process does not have to be.</p>
<h2>Source and accuracy note</h2>
<p>This guide is based primarily on current FBISE English assessment frameworks and model papers available through the official FBISE curriculum and model-paper portal. Those documents show SLO-based comprehension tasks and the current model formats, including passage-length ranges and summary or précis components. Because marks and task wording can differ between SSC, HSSC, class and examination year, students should compare this method with the latest official model paper for their exact subject. Reading-strategy recommendations are also informed by the Education Endowment Foundation’s guidance on explicit comprehension instruction.</p>
<h3>References</h3>
<ul><li>Federal Board of Intermediate and Secondary Education, Curriculum and Model Question Papers portal: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, English Compulsory SSC-I Assessment Framework and Model Question Paper, current portal edition.</li><li>FBISE, English Compulsory HSSC-I Assessment Framework and Model Question Paper, current portal edition.</li><li>Education Endowment Foundation, <em>Improving Literacy in Secondary Schools</em> and reading-comprehension strategy guidance: https://educationendowmentfoundation.org.uk/</li><li>Purdue Online Writing Lab, guidance on paragraphs and coherent development: https://owl.purdue.edu/</li></ul>', 'published', '2026-07-08 09:00:00'),
('Tenses You Keep Getting Wrong', 'fbise-english-tenses-common-errors', 'Grammar', 'The handful of tense errors that quietly cost marks, and the simple checks that fix them.', '<h2>Tense mistakes are usually meaning mistakes</h2>
<p>Students often learn tenses as twelve separate formulas. They memorise that the present continuous uses <em>is/am/are + verb-ing</em>, the past perfect uses <em>had + past participle</em>, and the future perfect uses <em>will have + past participle</em>. Then they enter an examination and still choose the wrong tense.</p>
<p>The problem is not always the formula. It is the decision that comes before the formula. The student has not decided whether the action is finished, continuing, repeated, earlier than another past action, connected to the present, scheduled, predicted or temporarily in progress. A tense is a way of locating an event in time and showing how the speaker views that event. When that meaning is unclear, a perfectly memorised structure is applied to the wrong situation.</p>
<p>This matters in FBISE English papers because grammar is not treated only as an isolated list of names. Current assessment frameworks and model papers include language-use items in which students must select or produce forms that fit a context. SSC material explicitly identifies tense-related grammar among the areas assessed, while HSSC tasks require accurate grammar across comprehension, report, paragraph and other written responses. Even when a question is not labelled “tenses,” an incorrect time relationship can damage clarity throughout an answer.</p>
<p>This guide focuses on the tense contrasts students most frequently confuse. It does not ask you to memorise a new collection of complicated definitions. It gives you a sequence of checks:</p>
<ol><li>Find the <strong>time anchor</strong>.</li><li>Decide whether the action is <strong>complete, continuing or repeated</strong>.</li><li>Check whether another event creates an <strong>earlier-later relationship</strong>.</li><li>Look for a <strong>present result or present relevance</strong>.</li><li>Test the verb with the subject and required form.</li></ol>
<p>Use these checks before reaching for a tense name.</p>
<h2>The first question: where is the speaker standing?</h2>
<p>Every sentence has a viewpoint. Usually the speaker stands in the present and looks backward, around or forward.</p>
<ul><li><strong>Past:</strong> The action happened before now.</li><li><strong>Present:</strong> The action is true, repeated or happening around now.</li><li><strong>Future:</strong> The action is expected after now.</li></ul>
<p>But the basic time is only the beginning. Compare:</p>
<p class="ex">I studied for two hours.</p>
<p class="ex">I was studying for two hours.</p>
<p class="ex">I have studied for two hours.</p>
<p class="ex">I have been studying for two hours.</p>
<p>All four sentences mention two hours, but they present the activity differently. The first treats it as a completed past block. The second places us inside a past activity, usually needing a past context. The third emphasises completed study connected to the present. The fourth emphasises duration continuing to now or ending very recently.</p>
<p>The correct tense therefore depends on the story you are telling, not on one time phrase alone.</p>
<h2>Build a timeline before choosing the form</h2>
<p>For difficult questions, draw a tiny line mentally or on rough space.</p>
<p>Past ---------------- Now ---------------- Future</p>
<p>Then mark:</p>
<ul><li>When did the action start?</li><li>Did it finish?</li><li>Is its result important now?</li><li>Is there another event?</li><li>Which event happened first?</li></ul>
<p>Example:</p>
<p class="ex">By the time the teacher arrived, the students ___ the experiment.</p>
<p>There are two past events:</p>
<ol><li>Students completed the experiment.</li><li>Teacher arrived.</li></ol>
<p>The completion happened earlier, so the past perfect is natural:</p>
<p class="ex">By the time the teacher arrived, the students <strong>had completed</strong> the experiment.</p>
<p>You do not choose <em>had completed</em> because “by the time” mechanically demands it in every imaginable sentence. You choose it because one past event was already complete before another past reference point.</p>
<h2>Present simple versus present continuous</h2>
<p>This is one of the earliest contrasts taught and one of the most persistent sources of error.</p>
<h3>Present simple: patterns, states and general truth</h3>
<p>Use the present simple for:</p>
<ul><li>habits and repeated actions;</li><li>facts and general truths;</li><li>permanent or relatively stable situations;</li><li>timetables and scheduled events;</li><li>instructions and commentary in some contexts;</li><li>stative meanings such as knowing, believing, owning or needing.</li></ul>
<p>Examples:</p>
<p class="ex">Sara <strong>walks</strong> to college every day.</p>
<p class="ex">Water <strong>boils</strong> at a lower temperature at high altitude.</p>
<p class="ex">My uncle <strong>works</strong> in Islamabad.</p>
<p class="ex">The train <strong>leaves</strong> at 7:30 tomorrow morning.</p>
<h3>Present continuous: activity in progress or temporary change</h3>
<p>Use the present continuous for:</p>
<ul><li>an action happening at or around the moment of speaking;</li><li>a temporary situation;</li><li>a developing or changing trend;</li><li>a repeated behaviour presented as irritating, often with <em>always</em>;</li><li>a personal future arrangement.</li></ul>
<p>Examples:</p>
<p class="ex">Sara <strong>is walking</strong> to college because the bus service is suspended this week.</p>
<p class="ex">The days <strong>are becoming</strong> warmer.</p>
<p class="ex">He <strong>is always interrupting</strong> before anyone finishes.</p>
<p class="ex">We <strong>are meeting</strong> the principal on Monday.</p>
<h3>The quiet error: using continuous for a state</h3>
<p>Students often write:</p>
<p class="ex">I am knowing the answer.</p>
<p class="ex">She is believing that the plan will work.</p>
<p class="ex">This bag is belonging to me.</p>
<p>In their usual meanings, <em>know</em>, <em>believe</em> and <em>belong</em> describe states, not activities unfolding in stages. Standard forms are:</p>
<p class="ex">I <strong>know</strong> the answer.</p>
<p class="ex">She <strong>believes</strong> that the plan will work.</p>
<p class="ex">This bag <strong>belongs</strong> to me.</p>
<p>Some stative verbs can be continuous when the meaning changes.</p>
<p class="ex">I <strong>think</strong> the answer is correct. <em>(opinion)</em></p>
<p class="ex">I <strong>am thinking</strong> about the answer. <em>(mental activity in progress)</em></p>
<p class="ex">The soup <strong>tastes</strong> salty. <em>(state or perception)</em></p>
<p class="ex">The chef <strong>is tasting</strong> the soup. <em>(deliberate action)</em></p>
<p>Do not memorise “never use these verbs in continuous.” Learn the meaning.</p>
<h3>The subject-verb agreement trap</h3>
<p>Present simple changes with a third-person singular subject:</p>
<p class="ex">He <strong>writes</strong>, she <strong>studies</strong>, the machine <strong>works</strong>.</p>
<p>Common errors:</p>
<p class="ex">He write every day.</p>
<p class="ex">She don’t understand.</p>
<p>Correct:</p>
<p class="ex">He <strong>writes</strong> every day.</p>
<p class="ex">She <strong>doesn’t understand</strong>.</p>
<p>After <em>does/doesn’t</em>, use the base verb: <em>doesn’t understand</em>, not <em>doesn’t understands</em>.</p>
<h2>Past simple versus present perfect</h2>
<p>This contrast causes major difficulty because both can describe a completed action.</p>
<h3>Past simple: finished time</h3>
<p>Use the past simple when the event belongs to a completed past time or when the speaker treats it as a finished past event.</p>
<p class="ex">I <strong>visited</strong> Lahore last year.</p>
<p class="ex">She <strong>submitted</strong> the form yesterday.</p>
<p class="ex">Quaid-e-Azam <strong>addressed</strong> the Constituent Assembly in 1947.</p>
<p>Time expressions such as <em>yesterday, last week, in 2022, two days ago</em> normally create a finished past frame.</p>
<h3>Present perfect: past connected to now</h3>
<p>Use the present perfect when:</p>
<ul><li>the exact time is not stated or not important;</li><li>a past action has a result relevant now;</li><li>an experience is considered up to the present;</li><li>a situation began in the past and still continues, especially with stative verbs;</li><li>a period of time is not yet finished.</li></ul>
<p class="ex">I <strong>have lost</strong> my key. <em>(I do not have it now.)</em></p>
<p class="ex">She <strong>has visited</strong> Lahore several times. <em>(experience up to now)</em></p>
<p class="ex">We <strong>have known</strong> each other for five years. <em>(still true)</em></p>
<p class="ex">I <strong>have completed</strong> three chapters this week. <em>(the week is still continuing)</em></p>
<p>Cambridge Grammar describes the present perfect as connecting past events or states with the present and distinguishes it from the past simple used for completed past time. The practical exam check is simple:</p>
<p class="ex">Is the sentence pointing to a finished past time, or is it looking back from now?</p>
<h3>Wrong combinations</h3>
<p class="ex">I have met him yesterday.</p>
<p>The present perfect conflicts with the finished time marker <em>yesterday</em>.</p>
<p>Correct:</p>
<p class="ex">I <strong>met</strong> him yesterday.</p>
<p>Another common error:</p>
<p class="ex">I am living here since 2021.</p>
<p>For a situation beginning in 2021 and continuing now:</p>
<p class="ex">I <strong>have lived</strong> here since 2021.</p>
<p>or, when duration and ongoing activity are emphasised:</p>
<p class="ex">I <strong>have been living</strong> here since 2021.</p>
<h3>“Have been to” and “have gone to”</h3>
<p class="ex">Ali <strong>has been to</strong> Karachi.</p>
<p>This usually means he visited and returned.</p>
<p class="ex">Ali <strong>has gone to</strong> Karachi.</p>
<p>This usually means he is there now or on the way.</p>
<p>The difference is not a formula trick; it changes the present situation.</p>
<h2>Present perfect simple versus present perfect continuous</h2>
<p>Both connect past activity to the present, but the focus differs.</p>
<h3>Present perfect simple: result, completion or quantity</h3>
<p class="ex">I <strong>have written</strong> three pages.</p>
<p>The result or amount completed is central.</p>
<p class="ex">She <strong>has repaired</strong> the bicycle.</p>
<p>The bicycle is now repaired.</p>
<h3>Present perfect continuous: duration, repeated effort or visible activity</h3>
<p class="ex">I <strong>have been writing</strong> for two hours.</p>
<p>The activity and its duration are central. It may still be continuing or may have just stopped.</p>
<p class="ex">She <strong>has been repairing</strong> the bicycle.</p>
<p>We focus on the process; the repair may not be complete.</p>
<p>Cambridge’s grammar guidance notes that the present perfect continuous commonly highlights an activity continuing until now or recently and often focuses on duration. A useful contrast is:</p>
<ul><li><strong>How much/how many?</strong> often points toward the simple form.</li><li><strong>How long/what activity?</strong> often points toward the continuous form.</li></ul>
<p>Examples:</p>
<p class="ex">How many applications <strong>have you completed</strong>?</p>
<p class="ex">How long <strong>have you been completing</strong> the application? <em>(possible, but less natural unless focusing on prolonged process)</em></p>
<p>More natural:</p>
<p class="ex">How long <strong>have you been working on</strong> the application?</p>
<h3>Verbs not normally used continuously</h3>
<p>With states, use the present perfect simple:</p>
<p class="ex">I <strong>have known</strong> her for years.</p>
<p>Not normally:</p>
<p class="ex">I have been knowing her for years.</p>
<h3>Result versus evidence of activity</h3>
<p class="ex">It <strong>has rained</strong> heavily, so the match is cancelled.</p>
<p>Focus: completed amount/result.</p>
<p class="ex">It <strong>has been raining</strong>, so the ground is wet.</p>
<p>Focus: recent continuing activity and visible evidence.</p>
<p>Both can be correct in a suitable context. The question is what the speaker wants to emphasise.</p>
<h2>Past simple versus past continuous</h2>
<h3>Past simple: completed event or sequence</h3>
<p class="ex">The bell <strong>rang</strong>, the students <strong>closed</strong> their books, and the teacher <strong>collected</strong> the papers.</p>
<p>The verbs move the story forward.</p>
<h3>Past continuous: background or action in progress</h3>
<p class="ex">The students <strong>were writing</strong> when the bell <strong>rang</strong>.</p>
<p>The writing was in progress; the ringing occurred during it.</p>
<p>Use the past continuous for:</p>
<ul><li>background description;</li><li>an activity in progress at a stated past time;</li><li>a longer activity interrupted by a shorter event;</li><li>two activities happening simultaneously in the past.</li></ul>
<p class="ex">At eight o’clock, we <strong>were waiting</strong> outside the hall.</p>
<p class="ex">While I <strong>was revising</strong>, my brother <strong>was preparing</strong> dinner.</p>
<h3>The “when/while” myth</h3>
<p>Students are sometimes told that <em>while</em> always takes past continuous and <em>when</em> always takes past simple. That shortcut fails.</p>
<p class="ex">While I <strong>lived</strong> in Quetta, I walked to school.</p>
<p>Here <em>lived</em> describes a past period, not necessarily an action viewed mid-progress.</p>
<p class="ex">When I <strong>was walking</strong> home, I saw the accident.</p>
<p><em>When</em> can introduce the continuous action.</p>
<p>Choose the tense from the event relationship, not the connector alone.</p>
<h3>Interrupted action</h3>
<p class="ex">I <strong>was reading</strong> when the lights <strong>went</strong> out.</p>
<p>Do not use past continuous for both events unless both truly continued together:</p>
<p class="ex">I was reading when the lights were going out.</p>
<p>That version suggests a gradual or repeated fading and requires a special context.</p>
<h2>Past perfect: useful, but often overused</h2>
<p>The past perfect marks an event as earlier than another past reference point.</p>
<p class="ex">The bus <strong>had left</strong> before we reached the station.</p>
<p>Two events:</p>
<ol><li>bus left;</li><li>we reached.</li></ol>
<p>The past perfect makes the earlier event unmistakable.</p>
<h3>When it is especially useful</h3>
<ul><li>with <em>by the time</em>;</li><li>when a story moves backward from a past moment;</li><li>when the order could otherwise be unclear;</li><li>in reported speech after a past reporting verb, where backshift is appropriate;</li><li>in third conditional structures.</li></ul>
<p class="ex">She was nervous because she <strong>had never spoken</strong> before such a large audience.</p>
<p class="ex">He said that he <strong>had completed</strong> the assignment.</p>
<p class="ex">If they <strong>had checked</strong> the weather, they would have postponed the trip.</p>
<h3>When the past simple is enough</h3>
<p>If the order is already clear from time words, normal narrative often uses the past simple:</p>
<p class="ex">After she <strong>finished</strong> the test, she <strong>checked</strong> her answers.</p>
<p>Using past perfect is possible:</p>
<p class="ex">After she <strong>had finished</strong> the test, she checked her answers.</p>
<p>But do not write every earlier past verb in past perfect for an entire paragraph. Establish the earlier relationship, then return to past simple where the sequence is clear.</p>
<h3>Common error</h3>
<p class="ex">When I had reached the school, the assembly started.</p>
<p>This wording suggests reaching was completed before another past event, but the ordinary sequence is clearer as:</p>
<p class="ex">When I <strong>reached</strong> the school, the assembly <strong>started</strong>.</p>
<p>or, if the assembly was already in progress:</p>
<p class="ex">When I <strong>reached</strong> the school, the assembly <strong>had started</strong>.</p>
<p>The intended meaning decides the form.</p>
<h2>Future forms: “will” is not the only future</h2>
<p>English uses several structures to talk about future time.</p>
<h3>Will: prediction, spontaneous decision, promise or willingness</h3>
<p class="ex">I think the weather <strong>will improve</strong>.</p>
<p class="ex">The phone is ringing; I <strong>will answer</strong> it.</p>
<p class="ex">I <strong>will help</strong> you with the report.</p>
<h3>Be going to: prior intention or evidence-based prediction</h3>
<p class="ex">We <strong>are going to organise</strong> a reading club next month. <em>(plan already formed)</em></p>
<p class="ex">Look at those clouds. It <strong>is going to rain</strong>. <em>(present evidence)</em></p>
<h3>Present continuous: arranged future</h3>
<p class="ex">I <strong>am meeting</strong> the counsellor at 10 a.m. tomorrow.</p>
<p>The arrangement is relatively definite and often includes a time or place.</p>
<h3>Present simple: timetable or schedule</h3>
<p class="ex">The examination <strong>begins</strong> at nine.</p>
<p class="ex">Our flight <strong>leaves</strong> on Friday.</p>
<h3>Future continuous: in progress at a future time</h3>
<p class="ex">This time tomorrow, we <strong>will be travelling</strong> to Peshawar.</p>
<p>It can also politely ask about plans:</p>
<p class="ex"><strong>Will you be using</strong> the laboratory this afternoon?</p>
<h3>Future perfect: completed before a future point</h3>
<p class="ex">By June, she <strong>will have completed</strong> the course.</p>
<p>The phrase <em>by June</em> creates the future deadline.</p>
<h3>Future perfect continuous: duration up to a future point</h3>
<p class="ex">By next month, he <strong>will have been teaching</strong> here for ten years.</p>
<p>This form is less common in school writing, but the logic is straightforward: an activity continues for a duration until a future reference point.</p>
<h2>Future time clauses: do not use “will” everywhere</h2>
<p>After time conjunctions such as <em>when, before, after, until, as soon as</em> and <em>once</em>, English commonly uses a present form for future meaning.</p>
<p>Incorrect:</p>
<p class="ex">When I will finish the assignment, I will email it.</p>
<p>Correct:</p>
<p class="ex">When I <strong>finish</strong> the assignment, I <strong>will email</strong> it.</p>
<p>Incorrect:</p>
<p class="ex">We will wait until the bus will arrive.</p>
<p>Correct:</p>
<p class="ex">We <strong>will wait</strong> until the bus <strong>arrives</strong>.</p>
<p>The main clause carries the future marker; the time clause uses present simple.</p>
<h2>Since and for: start point versus duration</h2>
<p>Use <strong>since</strong> for a starting point:</p>
<p class="ex">since Monday</p>
<p class="ex">since 2023</p>
<p class="ex">since I joined the school</p>
<p>Use <strong>for</strong> for a period:</p>
<p class="ex">for two days</p>
<p class="ex">for six months</p>
<p class="ex">for a long time</p>
<p>Correct:</p>
<p class="ex">She has studied here <strong>since</strong> 2024.</p>
<p class="ex">She has studied here <strong>for</strong> two years.</p>
<p>Common errors:</p>
<p class="ex">since two years</p>
<p class="ex">for Monday</p>
<h3>Tense with since</h3>
<p>When the situation continues to the present, use present perfect or present perfect continuous in the main clause:</p>
<p class="ex">I <strong>have known</strong> him since primary school.</p>
<p class="ex">I <strong>have been preparing</strong> since dawn.</p>
<p>In the clause naming the starting event, past simple is common:</p>
<p class="ex">I have known him since we <strong>entered</strong> primary school.</p>
<h2>Already, yet, just and still</h2>
<p>These small words often reveal the speaker’s time viewpoint.</p>
<h3>Already</h3>
<p>Shows that something happened earlier than expected or before now.</p>
<p class="ex">She has <strong>already submitted</strong> the form.</p>
<h3>Yet</h3>
<p>Common in questions and negatives about something expected up to now, usually at the end.</p>
<p class="ex">Have you finished <strong>yet</strong>?</p>
<p class="ex">I haven’t finished <strong>yet</strong>.</p>
<p>Cambridge notes that <em>yet</em> often refers to a time up to the present and is especially common in questions and negatives.</p>
<h3>Just</h3>
<p>Shows very recent completion.</p>
<p class="ex">The bus has <strong>just left</strong>.</p>
<p>In some varieties of English, past simple may occur with <em>just</em>, but present perfect is a safe formal choice when emphasising a recent event connected to now.</p>
<h3>Still</h3>
<p>Shows continuation or that an expected change has not occurred.</p>
<p class="ex">She is <strong>still working</strong>.</p>
<p class="ex">He <strong>still hasn’t replied</strong>.</p>
<p>Word order matters. Avoid:</p>
<p class="ex">He hasn’t still replied.</p>
<h2>Reported speech and tense backshift</h2>
<p>When reporting what someone said, a past reporting verb often causes a shift backward.</p>
<p>Direct:</p>
<p class="ex">Ayesha said, “I am tired.”</p>
<p>Reported:</p>
<p class="ex">Ayesha said that she <strong>was</strong> tired.</p>
<p>Direct:</p>
<p class="ex">Bilal said, “I have completed the work.”</p>
<p>Reported:</p>
<p class="ex">Bilal said that he <strong>had completed</strong> the work.</p>
<p>Direct:</p>
<p class="ex">They said, “We will return.”</p>
<p>Reported:</p>
<p class="ex">They said that they <strong>would return</strong>.</p>
<p>Cambridge grammar explains this common backshift in reported speech. However, backshift is not blind. If the reported fact remains true, present tense can sometimes remain, especially in factual reporting:</p>
<p class="ex">The teacher said that water <strong>boils</strong> at 100°C at standard pressure.</p>
<p>In school transformation questions, follow the expected formal pattern unless the context clearly preserves a universal fact.</p>
<h3>Time-word changes</h3>
<p>Depending on viewpoint:</p>
<ul><li>now → then</li><li>today → that day</li><li>yesterday → the previous day/the day before</li><li>tomorrow → the following day/the next day</li><li>here → there</li><li>this → that</li></ul>
<p>Do not change these words automatically if the report occurs in the same place or time. Meaning remains the final guide.</p>
<h2>Conditionals and tense meaning</h2>
<h3>Zero conditional: general result</h3>
<p class="ex">If water <strong>reaches</strong> 0°C under suitable conditions, it <strong>freezes</strong>.</p>
<h3>First conditional: real future possibility</h3>
<p class="ex">If it <strong>rains</strong>, we <strong>will postpone</strong> the match.</p>
<p>Do not normally write <em>if it will rain</em> in this pattern.</p>
<h3>Second conditional: unreal or unlikely present/future</h3>
<p class="ex">If I <strong>had</strong> more time, I <strong>would join</strong> the club.</p>
<p>The past form does not place the situation in past time; it marks distance from present reality.</p>
<h3>Third conditional: unreal past</h3>
<p class="ex">If they <strong>had left</strong> earlier, they <strong>would have caught</strong> the train.</p>
<p>Both the condition and result belong to an unreal past. Common errors mix levels:</p>
<p class="ex">If they left earlier, they would have caught the train.</p>
<p>That mixed form needs a special meaning. For an ordinary missed past opportunity, use past perfect + would have + past participle.</p>
<h2>Sequence of tenses in longer writing</h2>
<p>A paragraph should have a stable time frame. Do not switch tense simply because a new sentence begins.</p>
<p>Weak narrative:</p>
<p class="ex">Last Sunday we visited the lake. The weather is pleasant and children are playing near the water. Suddenly, dark clouds appeared and everyone runs toward the shelter.</p>
<p>Corrected narrative:</p>
<p class="ex">Last Sunday we <strong>visited</strong> the lake. The weather <strong>was</strong> pleasant, and children <strong>were playing</strong> near the water. Suddenly, dark clouds <strong>appeared</strong>, and everyone <strong>ran</strong> toward the shelter.</p>
<p>A deliberate change is allowed when the meaning changes:</p>
<p class="ex">Last Sunday we visited the lake. I <strong>still remember</strong> how quickly the sky changed.</p>
<p>The first sentence narrates the past; the second states a present memory.</p>
<h3>Essays and reports</h3>
<p>Use present simple for general claims:</p>
<p class="ex">Social media <strong>influences</strong> how people receive news.</p>
<p>Use past tense for a completed survey or event:</p>
<p class="ex">The school <strong>conducted</strong> a survey in March.</p>
<p>Use present perfect for a development connected to now:</p>
<p class="ex">Online learning <strong>has expanded</strong> access to recorded lessons.</p>
<p>Do not force the entire essay into one tense. Maintain logical control.</p>
<h2>Subject, auxiliary and participle checks</h2>
<p>After choosing the tense meaning, verify the structure.</p>
<h3>Present perfect</h3>
<p>has/have + past participle</p>
<p class="ex">She <strong>has written</strong>, they <strong>have written</strong>.</p>
<p>Not:</p>
<p class="ex">She has wrote.</p>
<h3>Past perfect</h3>
<p>had + past participle</p>
<p class="ex">They <strong>had gone</strong>.</p>
<p>Not:</p>
<p class="ex">They had went.</p>
<h3>Continuous</h3>
<p>be + verb-ing</p>
<p class="ex">He <strong>is working</strong>, they <strong>were waiting</strong>.</p>
<p>Not:</p>
<p class="ex">He working.</p>
<h3>Passive forms</h3>
<p>be + past participle, with the tense carried by <em>be</em>.</p>
<p class="ex">The report <strong>is checked</strong> every week.</p>
<p class="ex">The report <strong>was checked</strong> yesterday.</p>
<p class="ex">The report <strong>has been checked</strong>.</p>
<p>Students sometimes confuse present perfect continuous with present perfect passive:</p>
<p class="ex">The road <strong>has been repaired</strong>. <em>(passive; repair is complete)</em></p>
<p class="ex">The workers <strong>have been repairing</strong> the road. <em>(continuous activity)</em></p>
<h2>The six-question tense check</h2>
<p>Before submitting a grammar answer, ask:</p>
<ol><li>What is the time anchor?</li><li>Is the action complete, continuing, repeated or a state?</li><li>Is the time period finished?</li><li>Is there a present result or connection?</li><li>Are two events being ordered?</li><li>Does the auxiliary agree with the subject and take the correct verb form?</li></ol>
<p>This check is faster than reciting all twelve tense names.</p>
<h2>Error clinic: twenty sentences students often write</h2>
<h3>1</h3>
<p>Wrong: I am studying in this college since 2024.</p>
<p>Correct: I <strong>have been studying</strong> in this college since 2024.</p>
<p>Reason: The activity began in the past and continues now.</p>
<h3>2</h3>
<p>Wrong: She has completed the task yesterday.</p>
<p>Correct: She <strong>completed</strong> the task yesterday.</p>
<p>Reason: <em>Yesterday</em> is a finished past time.</p>
<h3>3</h3>
<p>Wrong: He did not went to class.</p>
<p>Correct: He did not <strong>go</strong> to class.</p>
<p>Reason: <em>Did</em> carries the past marking; the main verb returns to base form.</p>
<h3>4</h3>
<p>Wrong: They are knowing the truth.</p>
<p>Correct: They <strong>know</strong> the truth.</p>
<p>Reason: <em>Know</em> is normally stative.</p>
<h3>5</h3>
<p>Wrong: By the time we arrived, the film started.</p>
<p>Better: By the time we arrived, the film <strong>had started</strong>.</p>
<p>Reason: The starting happened before the arrival.</p>
<h3>6</h3>
<p>Wrong: When I will reach home, I will call you.</p>
<p>Correct: When I <strong>reach</strong> home, I will call you.</p>
<p>Reason: Present simple is used in the future time clause.</p>
<h3>7</h3>
<p>Wrong: I have seen him two days ago.</p>
<p>Correct: I <strong>saw</strong> him two days ago.</p>
<h3>8</h3>
<p>Wrong: She is working here for ten years.</p>
<p>Correct: She <strong>has worked</strong> here for ten years, or she <strong>has been working</strong> here for ten years.</p>
<h3>9</h3>
<p>Wrong: The teacher was entered when we talked.</p>
<p>Correct: The teacher <strong>entered</strong> when we <strong>were talking</strong>.</p>
<h3>10</h3>
<p>Wrong: I was completed my homework.</p>
<p>Correct: I <strong>completed</strong> my homework, or my homework <strong>was completed</strong>.</p>
<p>Reason: The original incorrectly combines active and passive patterns.</p>
<h3>11</h3>
<p>Wrong: He has went home.</p>
<p>Correct: He has <strong>gone</strong> home.</p>
<h3>12</h3>
<p>Wrong: We did not understood the question.</p>
<p>Correct: We did not <strong>understand</strong> the question.</p>
<h3>13</h3>
<p>Wrong: She said that she is tired.</p>
<p>Formal backshift: She said that she <strong>was</strong> tired.</p>
<p>The present form may be possible if she remains tired and the reporting context is immediate, but transformation questions commonly expect backshift.</p>
<h3>14</h3>
<p>Wrong: I am having a car.</p>
<p>Correct: I <strong>have</strong> a car.</p>
<p>Reason: Possession is a state.</p>
<h3>15</h3>
<p>Wrong: Look! The child falls.</p>
<p>Correct: Look! The child <strong>is falling</strong>.</p>
<h3>16</h3>
<p>Wrong: Every student have submitted the form.</p>
<p>Correct: Every student <strong>has submitted</strong> the form.</p>
<p>Reason: <em>Every student</em> is grammatically singular.</p>
<h3>17</h3>
<p>Wrong: The news are surprising everyone.</p>
<p>Correct: The news <strong>is surprising</strong> everyone.</p>
<p>Reason: <em>News</em> is singular in standard English.</p>
<h3>18</h3>
<p>Wrong: If he will study, he will pass.</p>
<p>Correct: If he <strong>studies</strong>, he will pass.</p>
<h3>19</h3>
<p>Wrong: She has been writing five letters.</p>
<p>Better for completed quantity: She <strong>has written</strong> five letters.</p>
<p>Reason: The number completed is central.</p>
<h3>20</h3>
<p>Wrong: I had met him last week.</p>
<p>Usually correct: I <strong>met</strong> him last week.</p>
<p>Reason: Past perfect needs another past reference point or context.</p>
<h2>Original exam-style drill A: choose the best form</h2>
<ol><li>The committee ___ its final recommendation yesterday.</li></ol>
<p>   a) has issued  b) issued  c) had been issuing</p>
<ol><li>I ___ this chapter for an hour, but I still do not understand the final example.</li></ol>
<p>   a) have been reading  b) read  c) had read</p>
<ol><li>By the time the ambulance arrived, the neighbours ___ first aid.</li></ol>
<p>   a) gave  b) had given  c) have given</p>
<ol><li>The museum ___ at nine tomorrow.</li></ol>
<p>   a) will be opening always  b) opens  c) has opened</p>
<ol><li>She usually ___ the bus, but this week she ___ with her cousin.</li></ol>
<p>   a) takes/is travelling  b) is taking/travels  c) took/has travelled</p>
<ol><li>We ___ the results yet.</li></ol>
<p>   a) did not receive  b) have not received  c) had not receive</p>
<ol><li>When you ___ the form, check the spelling of your name.</li></ol>
<p>   a) will complete  b) complete  c) completed</p>
<ol><li>He ___ in Multan since his family moved there in 2022.</li></ol>
<p>   a) lived  b) has lived  c) is living since</p>
<ol><li>At 6 p.m. yesterday, the volunteers ___ food parcels.</li></ol>
<p>   a) packed  b) were packing  c) have packed</p>
<ol><li>If the driver had seen the sign, he ___ the correct road.</li></ol>
<p>    a) would take  b) would have taken  c) took</p>
<h3>Answers and reasoning</h3>
<ol><li><strong>issued</strong>: <em>yesterday</em> gives a finished past time.</li><li><strong>have been reading</strong>: duration continues to now and the task remains unresolved.</li><li><strong>had given</strong>: first aid occurred before the ambulance’s past arrival.</li><li><strong>opens</strong>: a timetable can use present simple.</li><li><strong>takes/is travelling</strong>: habitual action contrasts with a temporary situation.</li><li><strong>have not received</strong>: <em>yet</em> and present relevance point to present perfect.</li><li><strong>complete</strong>: future time clause after <em>when</em>.</li><li><strong>has lived</strong>: state beginning in the past and continuing now.</li><li><strong>were packing</strong>: activity in progress at a specific past time.</li><li><strong>would have taken</strong>: unreal past result in the third conditional.</li></ol>
<h2>Original exam-style drill B: correct the error</h2>
<ol><li>I have submitted the application last Friday.</li><li>She does not understands the final paragraph.</li><li>We are waiting since two hours.</li><li>When the guests will arrive, we will serve dinner.</li><li>The students had completed the quiz and leave the room.</li><li>He is believing every rumour he hears.</li><li>The match already started when we reached the ground.</li><li>If I knew about the change yesterday, I would have attended.</li><li>The machine has broke again.</li><li>They were discussed the plan when the manager entered.</li></ol>
<h3>Corrected versions</h3>
<ol><li>I <strong>submitted</strong> the application last Friday.</li><li>She does not <strong>understand</strong> the final paragraph.</li><li>We <strong>have been waiting for</strong> two hours.</li><li>When the guests <strong>arrive</strong>, we will serve dinner.</li><li>The students <strong>completed</strong> the quiz and <strong>left</strong> the room; or, if one action preceded another reference point, the context must be supplied.</li><li>He <strong>believes</strong> every rumour he hears.</li><li>The match <strong>had already started</strong> when we reached the ground.</li><li>If I <strong>had known</strong> about the change yesterday, I would have attended.</li><li>The machine has <strong>broken</strong> again.</li><li>They <strong>were discussing</strong> the plan when the manager entered.</li></ol>
<h2>Original exam-style drill C: paragraph tense control</h2>
<p>Correct the tense problems in this paragraph:</p>
<p class="ex">Last month our class visits a water-treatment plant. The engineer explains that the city had tested water at several stages before it reaches homes. While we walk through the laboratory, technicians checked samples and record the results. I have never seen such careful monitoring before the visit, and the experience changes the way I think about ordinary tap water.</p>
<h3>Model correction</h3>
<p class="ex">Last month our class <strong>visited</strong> a water-treatment plant. The engineer <strong>explained</strong> that the city <strong>tests</strong> water at several stages before it <strong>reaches</strong> homes. While we <strong>were walking</strong> through the laboratory, technicians <strong>were checking</strong> samples and <strong>recording</strong> the results. I <strong>had never seen</strong> such careful monitoring before the visit, and the experience <strong>changed</strong> the way I <strong>thought</strong> about ordinary tap water.</p>
<p>Why these forms work:</p>
<ul><li><em>visited, explained, changed</em> move the completed past narrative.</li><li><em>tests, reaches</em> describe the general process that remains true.</li><li><em>were walking/were checking/recording</em> show simultaneous activity in progress.</li><li><em>had never seen</em> looks back to experience before the visit.</li><li><em>thought</em> keeps the final reflection within the past narrative. A writer could deliberately add a present reflection: “The experience changed the way I <strong>think</strong> about tap water today.”</li></ul>
<h2>A practical tense revision table</h2>
<div class="atable-wrap"><table class="atable"><thead><tr><th><strong>Meaning to express</strong></th><th><strong>Likely tense/form</strong></th><th><strong>Example</strong></th></tr></thead><tbody><tr><td>Habit or general fact</td><td>Present simple</td><td>The clinic opens at eight.</td></tr><tr><td>Temporary activity now</td><td>Present continuous</td><td>The clinic is extending its hours this month.</td></tr><tr><td>Finished past time</td><td>Past simple</td><td>It opened a new unit last year.</td></tr><tr><td>Past action in progress</td><td>Past continuous</td><td>Staff were preparing the room when we arrived.</td></tr><tr><td>Earlier of two past events</td><td>Past perfect</td><td>They had sterilised the equipment before the procedure began.</td></tr><tr><td>Past result connected to now</td><td>Present perfect</td><td>The clinic has introduced online appointments.</td></tr><tr><td>Activity continuing to now</td><td>Present perfect continuous</td><td>It has been serving the area for ten years.</td></tr><tr><td>Prediction or spontaneous choice</td><td>Will</td><td>I will call the office.</td></tr><tr><td>Prior intention/evidence</td><td>Going to</td><td>They are going to expand the waiting area.</td></tr><tr><td>Fixed arrangement</td><td>Present continuous</td><td>We are meeting the doctor tomorrow.</td></tr><tr><td>Timetable</td><td>Present simple</td><td>The session starts at ten.</td></tr><tr><td>Complete before future point</td><td>Future perfect</td><td>By August, they will have completed the renovation.</td></tr></tbody></table></div>
<p>The table is a reference, not a substitute for meaning.</p>
<h2>How to study irregular verbs efficiently</h2>
<p>Tense control collapses when the past participle is unknown. Group verbs by pattern instead of memorising an unorganised page.</p>
<h3>Same in all forms</h3>
<p>cut – cut – cutput – put – puthit – hit – hit</p>
<h3>Past and participle same</h3>
<p>build – built – builtfind – found – foundteach – taught – taught</p>
<h3>Vowel change</h3>
<p>sing – sang – sungdrink – drank – drunkbegin – began – begun</p>
<h3>Completely irregular high-frequency forms</h3>
<p>go – went – gonewrite – wrote – writtensee – saw – seentake – took – takenbreak – broke – broken</p>
<p>Use each verb in three sentences instead of copying the list repeatedly.</p>
<p class="ex">I <strong>write</strong> a journal entry every day.</p>
<p class="ex">I <strong>wrote</strong> one yesterday.</p>
<p class="ex">I have <strong>written</strong> four this week.</p>
<h2>A seven-day tense repair plan</h2>
<h3>Day 1: Time anchors</h3>
<p>Underline time expressions in twenty sentences and label each as finished past, continuing period, present habit or future point.</p>
<h3>Day 2: Simple versus continuous</h3>
<p>Contrast ten pairs. Explain whether each action is a habit, state, temporary activity or action in progress.</p>
<h3>Day 3: Past simple versus present perfect</h3>
<p>Sort sentences according to finished time versus present connection. Add <em>yesterday</em> or <em>since</em> and observe how the tense changes.</p>
<h3>Day 4: Perfect simple versus perfect continuous</h3>
<p>Practise result/quantity against duration/activity.</p>
<h3>Day 5: Past sequence</h3>
<p>Draw timelines for past simple, past continuous and past perfect.</p>
<h3>Day 6: Future and conditionals</h3>
<p>Rewrite plans as intention, arrangement, timetable and prediction. Practise first and third conditional forms.</p>
<h3>Day 7: Timed mixed editing</h3>
<p>Correct a 150-word paragraph and explain every change. Explanation is essential; it prevents lucky guessing.</p>
<h2>How teachers and students should use an error log</h2>
<p>Do not write “tenses weak” after a test. That diagnosis is too broad. Record the specific contrast:</p>
<ul><li>finished past versus present perfect;</li><li>state versus continuous;</li><li>past participle form;</li><li>earlier past event;</li><li>future time clause;</li><li>subject-auxiliary agreement;</li><li>conditional sequence.</li></ul>
<p>Example:</p>
<div class="atable-wrap"><table class="atable"><thead><tr><th><strong>Error</strong></th><th><strong>Why it happened</strong></th><th><strong>Correct rule</strong></th><th><strong>New example</strong></th></tr></thead><tbody><tr><td>have went</td><td>confused past with participle</td><td>perfect uses past participle</td><td>has gone</td></tr><tr><td>when I will arrive</td><td>marked future twice</td><td>present in time clause</td><td>when I arrive</td></tr><tr><td>since two months</td><td>start point/duration confusion</td><td>for + period</td><td>for two months</td></tr></tbody></table></div>
<p>Reviewing personal errors is more efficient than rereading all twelve tense chapters equally.</p>
<h2>Tenses in paragraph, report and comprehension answers</h2>
<p>Grammar questions are not the only place tenses matter.</p>
<h3>Paragraph writing</h3>
<p>Choose a main time frame. A descriptive paragraph about a daily routine needs present simple. A memorable journey needs past narrative forms. An explanation of climate change may combine present simple facts, present perfect developments and future predictions.</p>
<h3>Report writing</h3>
<p>Use past tense for what occurred:</p>
<p class="ex">The event began at 9 a.m. and included three competitions.</p>
<p>Use present tense for continuing recommendations or general facts:</p>
<p class="ex">The report recommends a larger venue because the current hall seats only 200 people.</p>
<h3>Comprehension answers</h3>
<p>Follow the passage’s time frame. Do not shift a past event into present merely because the question is in present wording such as “What does the passage say?”</p>
<h3>Literature responses</h3>
<p>The literary present is often used to discuss a text:</p>
<p class="ex">The poet <strong>contrasts</strong> darkness with dawn.</p>
<p class="ex">The character <strong>realises</strong> that pride has isolated him.</p>
<p>Past tense may still be used for historical context or events before the text’s main action.</p>
<h2>Frequently asked questions</h2>
<h3>Do I need to learn all twelve tense names?</h3>
<p>Knowing the names helps communication, but correct use depends more on recognising time and aspect. Learn the meanings and structures together.</p>
<h3>Can present perfect be used with a specific time?</h3>
<p>It can occur with periods connected to now, such as <em>today</em> or <em>this week</em>, when the period is unfinished. It normally does not combine with a completed time such as <em>yesterday</em>.</p>
<h3>Is “I have been living here for five years” better than “I have lived here for five years”?</h3>
<p>Both can be correct. The continuous form emphasises ongoing duration; the simple form presents the continuing state as a fact.</p>
<h3>Must I always use past perfect for the first of two past actions?</h3>
<p>No. Use it when the earlier relationship needs emphasis or clarity. Time conjunctions and normal sequence can make past simple sufficient.</p>
<h3>Can stative verbs ever be continuous?</h3>
<p>Some can when their meaning changes from state to activity, as in <em>I am thinking about it</em> or <em>the chef is tasting the soup</em>.</p>
<h3>Is “going to” more certain than “will”?</h3>
<p>Certainty is not the only difference. <em>Going to</em> often expresses prior intention or prediction based on present evidence; <em>will</em> commonly expresses prediction, spontaneous decision or willingness. Context affects certainty.</p>
<h3>Why is “when I will arrive” wrong?</h3>
<p>In ordinary future time clauses introduced by <em>when</em>, English uses a present form, while the main clause carries future meaning: <em>When I arrive, I will call.</em></p>
<h3>Which tense should I use in an essay?</h3>
<p>Use the tense required by each claim: present for general truth, past for completed evidence or events, present perfect for developments connected to now, and future forms for predictions or proposals.</p>
<h2>Final examination checklist</h2>
<p>Before finalising a tense answer, confirm:</p>
<ul><li>The time expression matches the tense.</li><li>A finished past time does not incorrectly take present perfect.</li><li>A continuing situation uses an appropriate perfect form.</li><li>Past perfect marks an earlier past event only where needed.</li><li>The verb after <em>did</em> is in base form.</li><li>The verb after <em>has/have/had</em> is a past participle.</li><li>The continuous form includes the correct form of <em>be</em>.</li><li>Third-person singular present simple has the correct ending.</li><li>Future time clauses do not unnecessarily use <em>will</em>.</li><li>The paragraph does not shift time without a reason.</li></ul>
<h2>The deeper lesson</h2>
<p>A tense is not chosen because one word in the sentence “looks like” a rule. It is chosen because the writer has decided how an action relates to time, completion, duration and other events. Once that relationship is clear, the form becomes much easier.</p>
<p>Stop asking only, “Which tense is this?” Ask, “What exactly is happening in time?” That question catches the errors that formulas miss.</p>
<h2>Source and accuracy note</h2>
<p>This guide is aligned with the language-use demands visible in current FBISE English assessment frameworks and model papers. Exact question formats vary by class and examination year, so students should consult the latest official model paper for their subject. Explanations of present perfect, present perfect continuous, reported speech and time adverbs are informed by Cambridge Dictionary’s official grammar resources. All practice sentences and passages in this article are original.</p>
<h3>References</h3>
<ul><li>Federal Board of Intermediate and Secondary Education, Curriculum and Model Question Papers: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, current English Compulsory SSC-I and HSSC-I Assessment Frameworks and Model Question Papers.</li><li>Cambridge Dictionary Grammar, present perfect, present perfect continuous, reported speech and time-adverb guidance: https://dictionary.cambridge.org/grammar/</li><li>Purdue Online Writing Lab, sentence and grammar resources: https://owl.purdue.edu/</li></ul>', 'published', '2026-07-06 09:00:00'),
('Reading Poetry Without Fear', 'fbise-reading-poetry-literary-devices', 'Literature', 'Simile, metaphor and imagery spotted quickly and explained the way FBISE examiners expect.', '<h2>Poetry becomes difficult when students search for a hidden “correct meaning”</h2>
<p>Many students approach a poem as if the poet has locked one secret message inside it and the examination expects them to guess the exact sentence in the examiner’s mind. They read a line, see an unusual image, and immediately become anxious: “What does this really mean?” That anxiety produces two common responses. Some students copy a memorised explanation without connecting it to the given lines. Others write a vague emotional statement—“The poet is feeling very sad and nature is beautiful”—that could fit dozens of poems.</p>
<p>A stronger approach begins with a simpler truth: a poem is made of words arranged to create meaning, sound, image and feeling. Your job is not to read the poet’s mind. Your job is to make a reasonable interpretation and support it with evidence from the text.</p>
<p>Current FBISE English assessment frameworks reflect this evidence-based approach. At SSC level, model tasks include paraphrasing a stanza and responding to questions about meaning, imagery, rhyme or figurative language. At HSSC level, the framework includes a poetic extract followed by questions on content and poetic devices. The exact paper design varies by class and year, but the recurring expectation is clear: understand the lines, identify what the language is doing and explain the effect accurately.</p>
<p>The Academy of American Poets defines common terms such as imagery, metaphor, personification, simile and rhyme scheme in ways that reinforce the same principle. A device is not merely a label. It is a choice that shapes what the reader imagines, compares, hears or feels.</p>
<p>This guide teaches a repeatable method called <strong>L-I-F-T: Literal sense, Image, Feeling, Technique</strong>. It is not an official FBISE acronym. It is a practical reading routine that helps you move from basic meaning to an exam-ready explanation.</p>
<h2>What a strong poetry answer contains</h2>
<p>For most device or interpretation questions, use three parts:</p>
<ol><li><strong>Identify</strong> the idea or technique.</li><li><strong>Point to evidence</strong> from the line.</li><li><strong>Explain the effect</strong> or meaning created by that evidence.</li></ol>
<p>You can remember this as <strong>I-E-E: Identify, Evidence, Effect</strong>.</p>
<p>Weak answer:</p>
<p class="ex">There is a metaphor.</p>
<p>This names a technique but shows no understanding.</p>
<p>Better answer:</p>
<p class="ex">The phrase “the city is a restless drum” is a metaphor. It presents the city as continuously beating with noise and activity, creating a sense of energy that is difficult to escape.</p>
<p>The answer identifies the metaphor, uses a short piece of evidence and explains what the comparison adds.</p>
<p>Do not stop at “It makes the poem beautiful.” Almost any technique can be said to make writing attractive, but that statement does not analyse the particular line. Ask instead:</p>
<ul><li>What picture does it create?</li><li>What quality does it transfer?</li><li>What mood does it strengthen?</li><li>What contrast does it reveal?</li><li>What idea does it make easier to feel?</li><li>What sound or pace does it produce?</li></ul>
<h2>The L-I-F-T reading method</h2>
<h3>L: Literal sense</h3>
<p>Before looking for devices, say what is physically or directly happening.</p>
<p>Line:</p>
<p class="ex">Rain taps softly on the empty roof.</p>
<p>Literal sense: Rain is falling on a roof and making a light sound.</p>
<p>Do not jump to loneliness before establishing the scene.</p>
<h3>I: Image</h3>
<p>What can you see, hear, touch, smell or taste? What mental picture is formed?</p>
<p>The reader hears the repeated light tapping and sees an unoccupied building under rain.</p>
<h3>F: Feeling</h3>
<p>What mood or emotional atmosphere develops? Which words create it?</p>
<p>“Softly” and “empty” may create quietness, loneliness or reflection. More than one interpretation is possible, but it must fit the words.</p>
<h3>T: Technique</h3>
<p>Now identify the language choice. “Rain taps” personifies rain by giving it an action associated with a visitor or hand. The soft consonant sounds may also contribute to gentleness.</p>
<p>The order matters. Students who begin by hunting labels often invent devices that are not there. Meaning should lead to terminology.</p>
<h2>First skill: paraphrase without flattening the poem</h2>
<p>A paraphrase restates the sense of the lines in clear prose. It is not a word-for-word synonym exercise, and it is not a full critical appreciation.</p>
<p>Original lines written for this guide:</p>
<p class="ex">At dawn the tired street removes its cloak of shade,</p>
<p class="ex">And windows catch the gold the rising morning made.</p>
<p>Literal paraphrase:</p>
<p class="ex">At sunrise, darkness gradually disappears from the street, and the windows reflect the golden morning light.</p>
<p>This paraphrase explains the scene. It does not need to reproduce the rhyme or personification.</p>
<h3>A four-step paraphrasing process</h3>
<ol><li>Identify the speaker, subject or scene.</li><li>Replace unusual word order with normal prose order.</li><li>explain figurative language in direct language.</li><li>Preserve the relationship between the ideas.</li></ol>
<p>Poetic order:</p>
<p class="ex">Across the silent field her shadow flew.</p>
<p>Normal order:</p>
<p class="ex">Her shadow moved quickly across the silent field.</p>
<h3>What not to do</h3>
<p>Do not merely replace each word:</p>
<p class="ex">At morning the exhausted road takes off its covering of darkness.</p>
<p>That version follows the original too closely and sounds unnatural.</p>
<p>Do not add unsupported ideas:</p>
<p class="ex">The street is tired because the people have suffered through war.</p>
<p>Nothing in the lines supports war.</p>
<p>Do not turn paraphrase into device analysis unless the question asks for it. A clean paraphrase answers “What do the lines say?” Analysis answers “How do the words create meaning?”</p>
<h2>Simile: comparison made explicit</h2>
<p>A simile compares one thing with another, commonly using <em>like</em> or <em>as</em>.</p>
<p>Original line:</p>
<p class="ex">The moon hung like a lantern over the road.</p>
<p>Identification:</p>
<p class="ex">The poet uses a simile by comparing the moon to a lantern.</p>
<p>Effect:</p>
<p class="ex">The comparison emphasises the moon’s light and its position above the dark road, making it seem as though it guides travellers.</p>
<h3>Avoid the “like means simile” shortcut</h3>
<p>Not every use of <em>like</em> creates a simile.</p>
<p class="ex">I like winter evenings.</p>
<p>Here <em>like</em> is a verb expressing preference.</p>
<p class="ex">She looks like her sister.</p>
<p>This is a comparison, but in literary analysis the important question remains whether it creates a meaningful image in the poem.</p>
<h3>Explain the shared quality</h3>
<p>A simile works because two different things share a selected quality.</p>
<p class="ex">His promise was as fragile as thin ice.</p>
<p>Shared quality: both can break easily.</p>
<p>Strong explanation:</p>
<p class="ex">The simile compares the promise to thin ice to suggest that it is unreliable and may fail under the slightest pressure.</p>
<p>Weak explanation:</p>
<p class="ex">A promise is ice.</p>
<p>That wrongly turns the simile into literal identity.</p>
<h2>Metaphor: one thing presented through another</h2>
<p>A metaphor makes an implied comparison by describing one thing as another.</p>
<p>Original line:</p>
<p class="ex">Memory is a room with doors that open at night.</p>
<p>The sentence does not claim that memory is physically a room. It uses the room and doors to represent stored experiences that unexpectedly return.</p>
<p>Strong answer:</p>
<p class="ex">The metaphor presents memory as a room whose doors open at night. It suggests that past experiences are stored within the mind and may return unexpectedly during quiet or vulnerable moments.</p>
<h3>Extended metaphor</h3>
<p>A comparison may continue across several lines.</p>
<p class="ex">I planted one brave question in the class;</p>
<p class="ex">It pushed through silence, leaf by leaf,</p>
<p class="ex">Until the room was green with voices.</p>
<p>The question is treated as a seed or plant. “Planted,” “pushed,” “leaf” and “green” develop the same comparison.</p>
<p>Effect:</p>
<p class="ex">The extended metaphor shows how one question gradually encourages wider discussion. Growth imagery makes participation feel natural and spreading.</p>
<h3>Dead or everyday metaphors</h3>
<p>Expressions such as “the foot of the mountain” or “time is running out” are metaphorical in origin but may feel ordinary. In an exam extract, focus on language that actively contributes to meaning rather than labelling every conventional expression.</p>
<h2>Personification: human qualities given to the non-human</h2>
<p>Personification attributes human action, emotion or intention to an object, animal, idea or natural force.</p>
<p>Original line:</p>
<p class="ex">The old gate groaned whenever winter entered.</p>
<p>The gate can literally make a creaking sound, but “groaned” personifies it as an old person in discomfort. “Winter entered” also turns the season into a visitor.</p>
<p>Strong answer:</p>
<p class="ex">The gate is personified through “groaned,” making its creak sound like human pain and emphasising its age. Winter is also presented as an unwelcome visitor entering the place.</p>
<h3>Do not confuse vivid verbs with personification automatically</h3>
<p class="ex">The river flowed rapidly.</p>
<p>“Flowed” is a normal action of a river.</p>
<p class="ex">The river argued with the stones.</p>
<p>“Argued” gives the river human behaviour and creates personification.</p>
<h3>Why poets use personification</h3>
<p>Personification can:</p>
<ul><li>make nature feel alive;</li><li>project the speaker’s emotion onto the surroundings;</li><li>create companionship or threat;</li><li>simplify an abstract idea;</li><li>turn a setting into an active participant.</li></ul>
<p>Always select the effect that fits the line.</p>
<h2>Imagery: language that appeals to the senses</h2>
<p>Imagery is language that creates sensory experience or a vivid mental picture. It is broader than visual description.</p>
<h3>Visual imagery</h3>
<p class="ex">Blue smoke curled above the silver roofs.</p>
<p>The reader sees colour, shape and movement.</p>
<h3>Auditory imagery</h3>
<p class="ex">Cups clicked, wheels rattled, and a distant vendor called.</p>
<p>The reader hears the setting.</p>
<h3>Tactile imagery</h3>
<p class="ex">The cold railing bit into my palm.</p>
<p>The reader imagines touch and temperature.</p>
<h3>Olfactory imagery</h3>
<p class="ex">The room smelled of dust and crushed mint.</p>
<p>The reader imagines smell.</p>
<h3>Gustatory imagery</h3>
<p class="ex">The bitter tea left a smoky taste.</p>
<p>The reader imagines taste.</p>
<h3>Kinaesthetic imagery</h3>
<p>This presents movement or bodily effort.</p>
<p class="ex">The climber dragged each step through the wind.</p>
<h3>Organic imagery</h3>
<p>This presents internal sensations or feelings such as hunger, fatigue, fear or dizziness.</p>
<p class="ex">A hollow ache tightened beneath his ribs.</p>
<p>The Academy of American Poets describes imagery as elements that engage the senses. In an exam answer, naming the sense is useful, but the effect still matters.</p>
<p>Weak answer:</p>
<p class="ex">This is visual imagery.</p>
<p>Strong answer:</p>
<p class="ex">“Blue smoke curled above the silver roofs” creates visual imagery through colour and slow movement, giving the evening scene a calm, almost dreamlike quality.</p>
<h2>Symbol: a concrete detail carrying a wider meaning</h2>
<p>A symbol is an object, action, place or image that suggests a broader idea while remaining part of the poem’s literal world.</p>
<p>Common possibilities include light representing hope, a road representing choice, or winter representing decline. But these associations are not fixed rules. Context decides.</p>
<p>Original lines:</p>
<p class="ex">She kept one match inside her coat</p>
<p class="ex">Through every mile of rain.</p>
<p>The match is literally a source of fire. It may symbolise hope, preparedness or a small remaining possibility. To justify the interpretation, connect it to “every mile of rain,” which creates prolonged difficulty.</p>
<p>Strong answer:</p>
<p class="ex">The match can be read as a symbol of hope or remaining possibility. Although the journey is dominated by rain, she protects a small means of creating light and warmth.</p>
<p>Use cautious wording such as “suggests,” “may symbolise” or “can represent” when the interpretation is not explicit.</p>
<h2>Hyperbole and understatement</h2>
<h3>Hyperbole</h3>
<p>Hyperbole is deliberate exaggeration for emphasis, emotion or humour.</p>
<p class="ex">I waited a thousand winters for your reply.</p>
<p>The speaker did not literally wait for a thousand winters. The exaggeration conveys extreme impatience or emotional distance.</p>
<p>Strong answer:</p>
<p class="ex">“A thousand winters” is hyperbole. It enlarges the period of waiting to show how painfully long it felt to the speaker.</p>
<h3>Understatement</h3>
<p>Understatement presents something as less serious or intense than it is.</p>
<p>After a violent storm, a speaker might say:</p>
<p class="ex">The night was slightly unkind.</p>
<p>The contrast between “slightly unkind” and severe destruction can create irony, restraint or dark humour.</p>
<h2>Apostrophe and direct address</h2>
<p>In poetry, apostrophe occurs when the speaker directly addresses an absent person, abstract idea, object or non-human entity.</p>
<p class="ex">O Time, loosen your grip on this hour.</p>
<p>Time cannot literally hear the speaker. The direct address makes the struggle with passing time immediate and emotional.</p>
<p>Do not confuse the literary device with the punctuation mark.</p>
<h2>Sound devices: hear before you label</h2>
<p>Poetry is meant to be heard as well as seen. Read the lines quietly aloud when possible during study.</p>
<h3>Alliteration</h3>
<p>Repetition of initial consonant sounds in nearby stressed words.</p>
<p class="ex">Soft sand slipped beneath our shoes.</p>
<p>The repeated /s/ sound may create softness, whispering or smooth movement.</p>
<p>Do not count letters only. <em>City</em> and <em>cat</em> begin with different sounds; <em>phone</em> and <em>forest</em> can share an /f/ sound despite different letters.</p>
<h3>Assonance</h3>
<p>Repetition of vowel sounds.</p>
<p class="ex">The low road rolled home.</p>
<p>The repeated long /o/ sound can slow and bind the line.</p>
<h3>Consonance</h3>
<p>Repetition of consonant sounds, often within or at the ends of words.</p>
<p class="ex">The black rock cracked.</p>
<p>The /k/ sound creates hardness and abruptness.</p>
<h3>Onomatopoeia</h3>
<p>A word imitates or suggests a sound.</p>
<p class="ex">buzz, hiss, clang, crackle, thud</p>
<p>Original line:</p>
<p class="ex">Rain hissed on the waiting road.</p>
<p>“Hissed” reproduces the sound and can also make the rain seem intense or secretive.</p>
<h3>Euphony and cacophony</h3>
<p>Euphony uses smooth, pleasant sound patterns; cacophony uses harsh, clashing sounds. These labels should be supported by examples rather than asserted generally.</p>
<p class="ex">Lull the lake with low and silver light.</p>
<p>The flowing /l/ sounds create euphony.</p>
<p class="ex">Cracked carts clattered across the stones.</p>
<p>The hard clusters create cacophony and imitate rough movement.</p>
<h2>Rhyme and rhyme scheme</h2>
<p>Rhyme occurs when ending sounds correspond, while rhyme scheme records the pattern of end rhymes with letters.</p>
<p>Original stanza:</p>
<p class="ex">The evening folds the market into <strong>light</strong> (A)</p>
<p class="ex">A bicycle rings and disappears from <strong>view</strong> (B)</p>
<p class="ex">The shopkeepers pull every shutter <strong>tight</strong> (A)</p>
<p class="ex">While one last kite climbs through the fading <strong>blue</strong> (B)</p>
<p>Rhyme scheme: <strong>ABAB</strong>.</p>
<h3>How to mark rhyme scheme</h3>
<ol><li>Look at the sound of the final stressed syllable, not spelling alone.</li><li>Give the first end sound A.</li><li>Give the next different sound B.</li><li>Reuse the letter when the sound returns.</li></ol>
<p>Words such as <em>love</em> and <em>move</em> look similar but do not rhyme in standard pronunciation. Words such as <em>blue</em> and <em>through</em> do rhyme despite different spelling.</p>
<h3>Types of rhyme you may encounter</h3>
<ul><li><strong>End rhyme:</strong> rhyme at line endings.</li><li><strong>Internal rhyme:</strong> rhyme within a line.</li><li><strong>Perfect rhyme:</strong> close matching of stressed vowel and following sounds.</li><li><strong>Slant/half rhyme:</strong> approximate sound relationship.</li></ul>
<p>Only use specialised labels when you can support them from the extract.</p>
<h3>Effect of rhyme</h3>
<p>Avoid writing only “Rhyme makes the poem musical.” Ask what kind of music and why it matters.</p>
<p>Rhyme may:</p>
<ul><li>make a stanza memorable;</li><li>create order or song-like movement;</li><li>link two important words;</li><li>produce expectation and completion;</li><li>contrast with disturbing content;</li><li>become broken to signal disruption.</li></ul>
<h2>Rhythm, metre and pace without panic</h2>
<p>At school level, you may not always need to scan a poem technically. You should still notice pace.</p>
<h3>Short lines</h3>
<p>Can create speed, emphasis, hesitation or isolation.</p>
<h3>Long flowing lines</h3>
<p>Can create continuity, abundance, reflection or breathlessness.</p>
<h3>Punctuation</h3>
<p>Full stops slow and complete thoughts. Commas create smaller pauses. Dashes can interrupt or emphasise. Lack of punctuation can make movement feel continuous.</p>
<h3>Enjambment</h3>
<p>Enjambment occurs when a sentence or phrase continues beyond the line break without a strong pause.</p>
<p class="ex">We carried the lamp beyond the gate</p>
<p class="ex">where darkness gathered in the grass.</p>
<p>The continuation pulls the reader forward and can mirror movement or delay completion.</p>
<h3>End-stopped line</h3>
<p>An end-stopped line closes with punctuation or a complete syntactic unit.</p>
<p class="ex">We closed the gate.</p>
<p>Repeated end-stopping can create firmness, control or heaviness.</p>
<p>Strong answer:</p>
<p class="ex">Enjambment carries the sentence into the next line, drawing the reader forward in the same direction as the travellers and creating continuous movement.</p>
<h2>Repetition and parallelism</h2>
<p>Repetition draws attention to a sound, word, phrase or structure.</p>
<p class="ex">We waited for rain,</p>
<p class="ex">waited for footsteps,</p>
<p class="ex">waited for news.</p>
<p>The repeated “waited” stresses duration and helplessness.</p>
<p>Parallelism repeats grammatical structure:</p>
<p class="ex">To listen without judging, to speak without fear, to act without delay.</p>
<p>The balanced structure makes the ideas feel connected and forceful.</p>
<p>Do not write “repetition is used for emphasis” and stop. State what is emphasised.</p>
<h2>Contrast, juxtaposition and oxymoron</h2>
<h3>Contrast</h3>
<p>Opposing ideas are placed against each other.</p>
<p class="ex">The hall was bright, but every face was tired.</p>
<p>The contrast separates the cheerful setting from the people’s exhaustion.</p>
<h3>Juxtaposition</h3>
<p>Two images or ideas are placed close together so their relationship becomes noticeable.</p>
<p class="ex">A wedding song drifted past the closed hospital gate.</p>
<p>Joy and illness are juxtaposed, potentially highlighting the coexistence of celebration and suffering.</p>
<h3>Oxymoron</h3>
<p>Apparently contradictory words are combined.</p>
<p class="ex">deafening silence</p>
<p class="ex">bitter sweetness</p>
<p class="ex">orderly chaos</p>
<p>Strong analysis explains the tension:</p>
<p class="ex">“Deafening silence” is an oxymoron that presents the absence of sound as emotionally overwhelming.</p>
<h2>Tone, mood and speaker: do not mix them</h2>
<h3>Speaker</h3>
<p>The voice speaking in the poem. It is not automatically the poet.</p>
<h3>Tone</h3>
<p>The speaker’s or poet’s attitude toward the subject or audience: admiring, bitter, hopeful, reflective, urgent, playful, mournful.</p>
<h3>Mood</h3>
<p>The atmosphere or feeling created for the reader: calm, tense, lonely, joyful, mysterious.</p>
<p>A poem can have a critical tone and create an uneasy mood.</p>
<h3>How to identify tone</h3>
<p>Look at:</p>
<ul><li>word choice;</li><li>images;</li><li>punctuation;</li><li>sentence type;</li><li>changes between beginning and end;</li><li>direct address;</li><li>sound pattern.</li></ul>
<p>Strong answer:</p>
<p class="ex">The tone is reflective and regretful. The speaker looks back through phrases about “unused roads” and “unopened letters,” suggesting awareness of missed opportunities.</p>
<p>Avoid choosing several contradictory labels without explanation.</p>
<h2>Theme: the poem’s larger idea</h2>
<p>A theme is not a one-word topic.</p>
<p>Topic: time.</p>
<p>Theme: People often recognise the value of ordinary moments only after those moments have passed.</p>
<p>Topic: nature.</p>
<p>Theme: Contact with nature can restore a sense of calm lost in crowded urban life.</p>
<p>Use this formula:</p>
<p class="ex">The poem explores/shows/suggests that + complete idea.</p>
<p>Then support the theme through at least two parts of the poem.</p>
<h2>Worked original poem one</h2>
<h3>“After the Bell”</h3>
<p class="ex">The final bell releases every chair;</p>
<p class="ex">The corridor becomes a sudden stream.</p>
<p class="ex">One notebook waits, still open to the air,</p>
<p class="ex">Guarding the half-built doorway of a dream.</p>
<p class="ex">Outside, the buses swallow up the crowd,</p>
<p class="ex">But on the page one quiet question stays.</p>
<p class="ex">It does not call the answer out aloud;</p>
<p class="ex">It holds a lamp against the coming days.</p>
<p>This poem is original and written for this guide.</p>
<h3>Literal meaning</h3>
<p>School ends, students leave, and one notebook remains open. A question on the page continues to matter after the noisy crowd has gone.</p>
<h3>Rhyme scheme</h3>
<p>chair (A), stream (B), air (A), dream (B), crowd (C), stays (D), aloud (C), days (D): <strong>ABABCDCD</strong>.</p>
<h3>Metaphor and personification</h3>
<p class="ex">“The final bell releases every chair”</p>
<p>The bell is personified as releasing students, while “chair” stands indirectly for each student who occupied it. The line creates rapid freedom after class.</p>
<p class="ex">“The corridor becomes a sudden stream”</p>
<p>This metaphor compares moving students to water. It emphasises speed, density and shared direction.</p>
<p class="ex">“The buses swallow up the crowd”</p>
<p>The buses are personified as large creatures. The image shows the crowd disappearing quickly into them.</p>
<p class="ex">“the half-built doorway of a dream”</p>
<p>The notebook’s unfinished work is metaphorically a doorway under construction. Learning is presented as access to a future possibility that is not yet complete.</p>
<p class="ex">“It holds a lamp against the coming days”</p>
<p>The question is personified and turned into a source of light. Curiosity becomes guidance for the future.</p>
<h3>Contrast</h3>
<p>The poem contrasts public noise with private thought. The corridor and buses are crowded and active; the notebook and question are quiet. This contrast suggests that learning may continue internally after the formal school day ends.</p>
<h3>Theme</h3>
<p>The poem suggests that genuine learning is not limited to bells, classrooms or completed answers; an unanswered question can guide future growth.</p>
<h3>Model exam answer</h3>
<p>Question: How does the poet present the question in the final four lines?</p>
<p class="ex">The poet presents the question as quiet but powerful. Although it does not “call the answer out aloud,” it remains on the page after the students leave. The metaphor “holds a lamp” turns curiosity into guidance, suggesting that an unanswered question can illuminate future learning.</p>
<p>This answer explains the contrast and metaphor rather than listing devices.</p>
<h2>Worked original poem two</h2>
<h3>“The Old Well”</h3>
<p class="ex">Beneath the noon, the village paths are white;</p>
<p class="ex">The fields lie still, their thirsty mouths of clay.</p>
<p class="ex">An old well keeps one coin of patient light</p>
<p class="ex">And counts the empty buckets through the day.</p>
<p class="ex">At dusk a child arrives with careful feet,</p>
<p class="ex">Then hears, far down, a small returning sound.</p>
<p class="ex">Hope rises cool beneath the summer heat—</p>
<p class="ex">A hidden sky is breathing underground.</p>
<h3>Paraphrase</h3>
<p>At midday the village is dry and bright, and the fields are without water. The old well contains only a small reflection of light while people repeatedly lower empty buckets. In the evening, a child hears a faint echo or sign of water deep below. This small sound creates hope that water still exists underground.</p>
<h3>Imagery</h3>
<p>“Village paths are white” and “summer heat” create visual and tactile imagery of intense dryness. “Small returning sound” creates auditory imagery. “Hope rises cool” combines emotional and tactile associations.</p>
<h3>Personification</h3>
<p>The fields have “thirsty mouths,” the well “keeps” light and “counts” buckets, and the hidden sky “is breathing.” These choices make the landscape feel alive and affected by drought.</p>
<h3>Metaphor</h3>
<p>“One coin of patient light” compares the reflection in the well to a coin, suggesting something small, round and precious. “A hidden sky” metaphorically describes the water’s reflection or underground possibility as another sky.</p>
<h3>Tone shift</h3>
<p>The first half is dry, still and discouraged. The child’s arrival and “returning sound” create a shift toward cautious hope. “Small” prevents the ending from becoming unrealistically triumphant.</p>
<h3>Model question: Explain the significance of the final line.</h3>
<p class="ex">“A hidden sky is breathing underground” suggests that life and possibility remain beneath the dry surface. By presenting the underground water as a breathing sky, the poet transforms a faint sound into a sign of renewal and ends the poem with cautious hope.</p>
<h2>How to answer common FBISE-style poetry questions</h2>
<h3>“Paraphrase the stanza”</h3>
<ul><li>Write the sense in clear prose.</li><li>Preserve who does what and why.</li><li>Explain figurative language directly.</li><li>Do not list devices unless requested.</li><li>Do not add a moral that is absent.</li></ul>
<h3>“Identify and explain the figure of speech”</h3>
<p>Use I-E-E:</p>
<p class="ex">The phrase ___ is a ___. It compares/presents ___ as ___. This emphasises/creates/suggests ___ .</p>
<h3>“What image is created?”</h3>
<p>Name the sensory picture and connect it to mood or meaning.</p>
<p class="ex">The image of ___ allows the reader to see/hear/feel ___. It makes the scene seem ___ and supports the idea that ___ .</p>
<h3>“What is the rhyme scheme?”</h3>
<p>Write the letter pattern and, if asked, mention how it contributes to order, musicality or emphasis.</p>
<h3>“What is the tone?”</h3>
<p>Choose a precise label, cite key language and explain any shift.</p>
<h3>“What is the central idea/theme?”</h3>
<p>Write a complete statement, not one word, and support it from the poem’s development.</p>
<h3>“Why does the poet repeat this word?”</h3>
<p>State what the repetition emphasises and how it affects rhythm or emotion.</p>
<h3>“How does the title relate to the poem?”</h3>
<p>Explain both literal relevance and deeper significance.</p>
<h2>The evidence ladder: from weak to excellent</h2>
<p>Question: What does the image “the road stitched the villages together” suggest?</p>
<p>Level 1:</p>
<p class="ex">It is a metaphor.</p>
<p>Level 2:</p>
<p class="ex">The road is compared to stitching.</p>
<p>Level 3:</p>
<p class="ex">The road is metaphorically presented as thread stitching villages together, showing that it connects separate communities.</p>
<p>Level 4:</p>
<p class="ex">The metaphor presents the road as thread stitching separate villages into one fabric. It suggests not only physical connection but also stronger social and economic relationships between communities.</p>
<p>The final answer is stronger because it develops the transferred qualities of stitching—joining separate parts into a whole.</p>
<h2>Common mistakes that cost literature marks</h2>
<h3>Mistake 1: Device hunting without meaning</h3>
<p>The student labels every noun as imagery and every line as metaphor.</p>
<p>Repair: State the literal sense first. Then identify only techniques that can be defended.</p>
<h3>Mistake 2: “It makes the poem beautiful”</h3>
<p>Repair: Name the exact image, quality, mood, contrast or emphasis.</p>
<h3>Mistake 3: Confusing simile and metaphor</h3>
<p>Simile: one thing is <strong>like/as</strong> another.</p>
<p>Metaphor: one thing is presented <strong>as</strong> another.</p>
<h3>Mistake 4: Calling the speaker the poet automatically</h3>
<p>Repair: Write “the speaker” unless biographical context clearly connects the voice to the poet.</p>
<h3>Mistake 5: Retelling instead of analysing</h3>
<p>Repair: After stating what happens, ask how the wording shapes its significance.</p>
<h3>Mistake 6: Quoting too much</h3>
<p>Repair: Use the shortest phrase that proves the point, then spend more words explaining it.</p>
<h3>Mistake 7: Unsupported symbolic claims</h3>
<p>Repair: Use contextual evidence and cautious language. A bird does not automatically symbolise freedom in every poem.</p>
<h3>Mistake 8: One fixed tone for the whole poem</h3>
<p>Repair: Check whether the tone shifts after a contrast word, new image or final stanza.</p>
<h3>Mistake 9: Memorised answer forced onto a new extract</h3>
<p>Repair: Begin with the exact words in front of you. SLO-based assessment rewards transfer of skill, not reproduction of one guidebook paragraph.</p>
<h3>Mistake 10: Ignoring grammar</h3>
<p>A good interpretation can become unclear through fragments or vague pronouns.</p>
<p>Repair: Write complete analytical sentences with clear subjects.</p>
<h2>A device-effect bank that avoids vague writing</h2>
<p>Use these as starting points, not automatic endings.</p>
<ul><li>Simile: makes an unfamiliar feeling concrete by comparing it to ___ .</li><li>Metaphor: transfers the qualities of ___ to ___, suggesting ___ .</li><li>Personification: makes the setting feel active/threatening/companionable by giving it ___ .</li><li>Visual imagery: allows the reader to picture ___ and creates a ___ atmosphere.</li><li>Auditory imagery: makes the scene vivid through the sound of ___ and increases ___ .</li><li>Repetition: emphasises ___ and creates a rhythm of ___ .</li><li>Alliteration: draws attention to ___ and produces a smooth/harsh/rapid sound.</li><li>Enjambment: carries the thought forward, reflecting ___ or creating ___ .</li><li>End-stopping: creates firmness, pause or finality after ___ .</li><li>Contrast: highlights the difference between ___ and ___, revealing ___ .</li><li>Symbol: gives the concrete image of ___ a wider association with ___ .</li><li>Hyperbole: exaggerates ___ to communicate the intensity of ___ .</li></ul>
<p>Always fill the blanks with details from the poem.</p>
<h2>A two-minute annotation method</h2>
<p>When an extract appears:</p>
<h3>First 30 seconds</h3>
<p>Read for the speaker, scene and basic event.</p>
<h3>Next 30 seconds</h3>
<p>Circle repeated or unusual words. Mark contrast words such as <em>but, yet, although, still</em>.</p>
<h3>Next 30 seconds</h3>
<p>Underline one or two strong images and note the sense involved.</p>
<h3>Final 30 seconds</h3>
<p>Write a margin note for tone and central movement: “fear → courage,” “noise vs silence,” “loss but hope.”</p>
<p>Then answer questions. Do not decorate the poem with so many labels that the page becomes unreadable.</p>
<h2>Original practice extract three</h2>
<p class="ex">No map remembers where the footpath bends;</p>
<p class="ex">Grass writes its green revision on the stone.</p>
<p class="ex">Yet every evening one old traveller sends</p>
<p class="ex">His shadow first, then follows it alone.</p>
<h3>Questions</h3>
<ol><li>Paraphrase the extract.</li><li>Identify the personification in line 2 and explain its effect.</li><li>What does the traveller’s shadow suggest in the final line?</li><li>Identify the rhyme scheme.</li><li>Describe the tone.</li></ol>
<h3>Answer guide</h3>
<ol><li>The path is so old or unused that maps no longer show its turns, and grass has grown over the stones. Nevertheless, an elderly traveller continues to walk there alone every evening, with his shadow moving ahead of him.</li><li>“Grass writes its green revision” personifies grass as an editor rewriting the stone surface. It suggests that nature gradually changes and covers human paths.</li><li>The shadow moving first may emphasise the low evening light, but it can also suggest age, solitude or the traveller following memories into the past. The second interpretation must be presented cautiously.</li><li>bends (A), stone (B), sends (A), alone (B): <strong>ABAB</strong>.</li><li>The tone is reflective and slightly lonely. The forgotten path and solitary old traveller create a sense of time passing, while his repeated journey suggests quiet persistence.</li></ol>
<h2>Original practice extract four</h2>
<p class="ex">Speak gently, wind; the seedlings have no shield.</p>
<p class="ex">They learned the sun only an hour ago.</p>
<p class="ex">Do not rehearse your thunder in this field;</p>
<p class="ex">Let roots take hold before the hard rains grow.</p>
<h3>Questions and model points</h3>
<p><strong>Who is being addressed?</strong>The wind is directly addressed, an example of apostrophe and personification.</p>
<p><strong>What does “learned the sun” suggest?</strong>The seedlings have only recently emerged and encountered sunlight. “Learned” personifies their early growth as discovery.</p>
<p><strong>Explain “rehearse your thunder.”</strong>The wind or storm is presented as a performer practising thunder. The metaphor/personification suggests an approaching storm and makes the speaker’s warning urgent.</p>
<p><strong>Central idea:</strong>New growth is fragile and needs time and protection before facing severe difficulty.</p>
<p><strong>Possible symbolic reading:</strong>The seedlings may represent inexperienced people or new ideas. The literal agricultural scene remains the foundation for that interpretation.</p>
<h2>Writing a full paragraph on a poem</h2>
<p>When asked for a developed response, use <strong>C-E-A-L</strong>:</p>
<ul><li><strong>Claim:</strong> answer the question.</li><li><strong>Evidence:</strong> select a brief phrase.</li><li><strong>Analysis:</strong> explain language and effect.</li><li><strong>Link:</strong> return to the main idea.</li></ul>
<p>Question: How does the poet create a sense of vulnerability in “Speak gently, wind”?</p>
<p>Model paragraph:</p>
<p class="ex">The poet creates vulnerability by presenting the seedlings as newly exposed and unprotected. The direct request, “Speak gently, wind,” personifies the wind as a powerful listener who could choose restraint, while the statement that the seedlings “have no shield” emphasises their defencelessness. Their experience of sunlight is only “an hour” old, which makes their growth seem extremely recent. The speaker’s request to delay thunder and hard rain therefore presents the young plants as needing time before they can survive stronger forces.</p>
<p>The paragraph remains focused on vulnerability. It does not list every possible device.</p>
<h2>A seven-day poetry confidence plan</h2>
<h3>Day 1: Literal meaning</h3>
<p>Paraphrase four short stanzas without naming any devices.</p>
<h3>Day 2: Simile, metaphor and personification</h3>
<p>For ten examples, identify the two things connected and the quality transferred.</p>
<h3>Day 3: Imagery</h3>
<p>Sort images by sense and write one effect sentence for each.</p>
<h3>Day 4: Sound and rhyme</h3>
<p>Read stanzas aloud, mark rhyme schemes and connect sound to pace or mood.</p>
<h3>Day 5: Tone and theme</h3>
<p>Use three pieces of evidence for every tone label. Write themes as complete sentences.</p>
<h3>Day 6: I-E-E answers</h3>
<p>Answer ten device questions in two or three sentences each.</p>
<h3>Day 7: Timed extract</h3>
<p>Complete one unseen extract. Afterwards, underline claims, box evidence and circle analysis. Missing elements become the next week’s target.</p>
<h2>How to revise prescribed poems without memorising blindly</h2>
<p>For each poem, prepare a one-page map:</p>
<ul><li>speaker and situation;</li><li>movement from beginning to end;</li><li>three central themes;</li><li>five important images;</li><li>major contrasts;</li><li>tone and shifts;</li><li>rhyme/form observations;</li><li>brief quotations or phrase references allowed by your teacher;</li><li>two possible exam questions.</li></ul>
<p>Then practise applying those ideas to different extracts. A memorised appreciation that ignores the selected stanza is risky.</p>
<h2>Frequently asked questions</h2>
<h3>Can there be more than one correct interpretation?</h3>
<p>Yes, when each interpretation is reasonable and supported by textual evidence. Examiners do not reward unsupported imagination, but poetry can sustain more than one defensible reading.</p>
<h3>How many devices should I mention?</h3>
<p>Mention the ones relevant to the question. Two well-explained techniques are usually stronger than six labels with no effect.</p>
<h3>Is every comparison a metaphor?</h3>
<p>No. Some comparisons are similes, analogies or ordinary descriptive relationships. Identify the actual grammatical and figurative form.</p>
<h3>What is the difference between imagery and a figure of speech?</h3>
<p>Imagery creates sensory experience. A simile or metaphor may create imagery, but imagery can also come from direct description without comparison.</p>
<h3>How do I know the tone?</h3>
<p>Combine word choice, imagery, punctuation, sound and change across the extract. Support the label with evidence.</p>
<h3>Should a paraphrase be the same length as the stanza?</h3>
<p>It should be complete and clear, not artificially equal. Follow the question’s mark value and any instruction from the current paper.</p>
<h3>What if I cannot remember the device name?</h3>
<p>Explain what the words do. A correct explanation may still show understanding, though accurate terminology strengthens the answer when requested.</p>
<h3>Can I write “the poet wants to say”?</h3>
<p>A more precise academic form is “the poem suggests,” “the speaker presents,” or “the image implies.” This avoids claiming certainty about private intention.</p>
<h3>How do I explain rhyme?</h3>
<p>Give the pattern if asked, then connect it to organisation, musicality, emphasis or contrast in that specific stanza.</p>
<h2>Final examination checklist</h2>
<p>Before finishing a poetry response, ask:</p>
<ul><li>Have I answered the exact question?</li><li>Did I establish the literal sense first?</li><li>Is the device label accurate?</li><li>Have I included brief textual evidence?</li><li>Did I explain the effect of that exact wording?</li><li>Is my tone label supported?</li><li>Is my theme a complete idea rather than one word?</li><li>Did I avoid inventing biography or context?</li><li>Have I distinguished speaker from poet?</li><li>Is the response grammatical and focused?</li></ul>
<h2>The deeper lesson</h2>
<p>Poetry is compressed language, not a coded puzzle. Slow down enough to see the scene, then ask what each striking choice adds. A good answer does not prove that the student knows the largest number of literary terms. It proves that the student can connect words to meaning.</p>
<p>Read literally. Notice the image. Name the feeling. Explain the technique. That is how fear becomes method.</p>
<h2>Source and accuracy note</h2>
<p>This article is aligned with poetry tasks shown in current FBISE English assessment frameworks and model papers, including paraphrase, content, imagery, rhyme and figurative-language questions. Exact marks and formats differ by class and examination year, so students should consult the latest official paper for their subject. Definitions and general literary terminology are informed by the Academy of American Poets’ glossary and other established poetry references. All sample poems and lines in this guide are original, avoiding dependence on copyrighted textbook extracts.</p>
<h3>References</h3>
<ul><li>Federal Board of Intermediate and Secondary Education, Curriculum and Model Question Papers: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, current English Compulsory SSC-I and HSSC-I Assessment Frameworks and Model Question Papers.</li><li>Academy of American Poets, glossary of poetic terms: https://poets.org/glossary</li><li>Poetry Foundation, glossary of poetic terms: https://www.poetryfoundation.org/learn/glossary-terms</li><li>Purdue Online Writing Lab, literature-writing resources: https://owl.purdue.edu/</li></ul>', 'published', '2026-07-03 09:00:00'),
('Mazmoon Nigari: Structure First', 'fbise-urdu-mazmoon-nigari-structure', 'Urdu', 'How to plan an Urdu essay in two minutes so the writing flows and the marks follow.', '<h2 dir="rtl" lang="ur">اچھے خیالات کافی نہیں، ترتیب بھی ضروری ہے</h2>
<p dir="rtl" lang="ur">اردو مضمون نویسی میں بہت سے طلبہ کے پاس موضوع سے متعلق معلومات موجود ہوتی ہیں، لیکن وہ ان معلومات کو ایک مربوط تحریر میں تبدیل نہیں کر پاتے۔ امتحان میں موضوع دیکھتے ہی لکھنا شروع کر دیتے ہیں۔ پہلے پیراگراف میں تعریف، دوسرے میں کوئی واقعہ، تیسرے میں اچانک مسائل، پھر ایک شعر، پھر ذاتی رائے، اور آخر میں دو سطروں کی دعا۔ ہر جملہ الگ دیکھا جائے تو درست ہو سکتا ہے، مگر پورا مضمون ایک واضح سمت کے بغیر بکھر جاتا ہے۔</p>
<p dir="rtl" lang="ur">یہی وجہ ہے کہ <strong>ساخت</strong> مضمون کے نمبر محفوظ کرتی ہے۔ ساخت کا مطلب سخت اور مصنوعی سانچہ نہیں۔ اس کا مطلب یہ ہے کہ قاری کو معلوم رہے کہ مضمون کہاں سے شروع ہوا، کس ترتیب سے آگے بڑھا، کون سا نکتہ کس دلیل یا مثال سے واضح ہوا، اور نتیجہ پچھلی بحث سے کیسے نکلا۔</p>
<p dir="rtl" lang="ur">موجودہ FBISE Assessment Frameworks اور Model Question Papers اس بات پر زور دیتے ہیں کہ امتحان SLO-based ہے؛ یعنی طالب علم سے صرف رٹا ہوا مضمون دہرانے کے بجائے حاصل شدہ مہارت کو نئے موضوع پر استعمال کرنے کی توقع کی جاتی ہے۔ موجودہ HSSC-II Urdu model paper میں مضمون کے لیے <strong>چار سو سے پانچ سو الفاظ</strong> اور <strong>بارہ نمبر</strong> دیے گئے ہیں، اور موضوعات میں سوشل میڈیا کے مثبت و منفی پہلو، نوجوانوں میں بڑھتی ہوئی بے راہ روی، نوجوانوں کا لباس اور معاشرہ، اور ماحولیاتی تبدیلی جیسے contemporary مسائل شامل ہیں۔ تاہم کلاس، سال اور پیپر کے مطابق الفاظ اور نمبر بدل سکتے ہیں، اس لیے اپنے امتحان کے تازہ ترین official model paper کو ضرور دیکھیں۔</p>
<p dir="rtl" lang="ur">اس مضمون میں ایک practical method دیا جا رہا ہے: <strong>م-د-ث-ن</strong> — <strong>موضوع، دلائل، ثبوت، نتیجہ</strong>۔ یہ FBISE کا official acronym نہیں، بلکہ امتحانی دباؤ میں سوچ منظم کرنے کا ایک آسان طریقہ ہے۔</p>
<h2 dir="rtl" lang="ur">مضمون، پیراگراف اور تقریر میں فرق</h2>
<p dir="rtl" lang="ur">مضمون لکھتے وقت طلبہ اکثر مختلف اصناف کو ملا دیتے ہیں۔</p>
<h3 dir="rtl" lang="ur">مضمون</h3>
<p dir="rtl" lang="ur">مضمون کسی موضوع کو منظم انداز میں متعارف کراتا، واضح کرتا، تجزیہ کرتا اور نتیجہ اخذ کرتا ہے۔ اس میں عنوان کے مطابق معلومات، دلائل، مثالیں اور ربط ضروری ہوتے ہیں۔</p>
<h3 dir="rtl" lang="ur">پیراگراف</h3>
<p dir="rtl" lang="ur">پیراگراف ایک مرکزی خیال کی مختصر ترقی ہے۔ مضمون کئی مربوط پیراگرافوں سے بنتا ہے، اور ہر پیراگراف پورے مضمون میں ایک مخصوص کام کرتا ہے۔</p>
<h3 dir="rtl" lang="ur">تقریر</h3>
<p dir="rtl" lang="ur">تقریر میں سامعین سے براہِ راست خطاب، خطیبانہ سوالات، جوش اور زبانی انداز زیادہ ہو سکتا ہے: “محترم صدرِ محفل!”، “میرے عزیز ساتھیو!”۔ مضمون میں بلا ضرورت ایسا خطاب تحریر کو غیر موزوں بنا سکتا ہے۔</p>
<h3 dir="rtl" lang="ur">کہانی</h3>
<p dir="rtl" lang="ur">کہانی میں کردار، واقعات، کشمکش اور انجام مرکزی ہوتے ہیں۔ مضمون میں واقعہ مثال کے طور پر آ سکتا ہے، مگر پورا جواب داستان نہیں بننا چاہیے، جب تک عنوان خود بیانیہ نہ ہو۔</p>
<p dir="rtl" lang="ur">پہلا امتحانی فیصلہ یہ ہونا چاہیے: <strong>سوال مجھ سے کس صنف کی تحریر مانگ رہا ہے؟</strong></p>
<h2 dir="rtl" lang="ur">موضوع کو درست سمجھنا نصف کامیابی ہے</h2>
<p dir="rtl" lang="ur">موضوع کے ہر لفظ کی حد طے کریں۔</p>
<p dir="rtl" lang="ur">مثال:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا کا استعمال: مثبت اور منفی پہلو</p>
<p dir="rtl" lang="ur">یہ موضوع صرف سوشل میڈیا کی تعریف نہیں مانگتا۔ نہ ہی صرف نقصانات یا صرف فوائد کافی ہیں۔ طالب علم کو “استعمال” کے دونوں رخ دکھا کر ایک متوازن یا واضح موقف دینا ہے۔</p>
<p dir="rtl" lang="ur">موضوع:</p>
<p class="ex" dir="rtl" lang="ur">ماحولیاتی تبدیلی اور اس کے اثرات</p>
<p dir="rtl" lang="ur">یہ صرف موسم کی تعریف نہیں۔ اس میں تبدیلی کی نوعیت، اسباب کا مختصر ذکر، پاکستان یا انسانی زندگی پر اثرات، اور قابلِ عمل ردِعمل شامل ہو سکتے ہیں۔</p>
<p dir="rtl" lang="ur">موضوع:</p>
<p class="ex" dir="rtl" lang="ur">نوجوانوں میں بڑھتی ہوئی بے راہ روی</p>
<p dir="rtl" lang="ur">یہ حساس اور وسیع موضوع ہے۔ غیر ثابت شدہ الزامات، پوری نسل کی مذمت یا جذباتی مبالغہ مناسب نہیں۔ “بے راہ روی” کی واضح شکلیں، ممکنہ سماجی و تعلیمی اسباب، نتائج اور اصلاحی اقدامات منظم انداز میں بیان ہونے چاہئیں۔</p>
<h3 dir="rtl" lang="ur">موضوع توڑنے کا طریقہ</h3>
<p dir="rtl" lang="ur">عنوان کو تین سوالوں میں بدلیں:</p>
<ol dir="rtl" lang="ur"><li><strong>یہ کیا ہے؟</strong></li><li><strong>یہ کیوں اہم یا مسئلہ ہے؟</strong></li><li><strong>اس کے بارے میں کیا کیا جا سکتا ہے؟</strong></li></ol>
<p dir="rtl" lang="ur">متوازن موضوع میں چوتھا سوال شامل کریں:</p>
<ol dir="rtl" lang="ur"><li><strong>اس کے مختلف پہلو یا اعتراضات کیا ہیں؟</strong></li></ol>
<h2 dir="rtl" lang="ur">دو منٹ کا منصوبہ: لکھنے سے پہلے سوچیں</h2>
<p dir="rtl" lang="ur">امتحان میں منصوبہ بندی وقت ضائع نہیں کرتی؛ یہ بعد میں جملے کاٹنے، نکتہ دہرانے اور موضوع سے بھٹکنے کا وقت بچاتی ہے۔</p>
<h3 dir="rtl" lang="ur">پہلے 20 سیکنڈ: موضوع کی قسم</h3>
<p dir="rtl" lang="ur">کیا یہ:</p>
<ul dir="rtl" lang="ur"><li>معلوماتی ہے؟</li><li>استدلالی ہے؟</li><li>سماجی مسئلہ ہے؟</li><li>موازنہ ہے؟</li><li>شخصی/تاثراتی ہے؟</li><li>قومی یا اخلاقی موضوع ہے؟</li></ul>
<h3 dir="rtl" lang="ur">اگلے 40 سیکنڈ: مرکزی موقف</h3>
<p dir="rtl" lang="ur">ایک سادہ جملہ لکھیں:</p>
<p class="ex" dir="rtl" lang="ur">میری تحریر ثابت کرے گی کہ __________۔</p>
<p dir="rtl" lang="ur">مثال:</p>
<p class="ex" dir="rtl" lang="ur">میری تحریر ثابت کرے گی کہ سوشل میڈیا بذاتِ خود مکمل طور پر مفید یا نقصان دہ نہیں؛ اس کے اثرات استعمال کے مقصد، مدت اور ذمہ داری پر منحصر ہیں۔</p>
<p dir="rtl" lang="ur">یہ جملہ عین اسی صورت میں مضمون میں شامل کرنا ضروری نہیں۔ یہ آپ کے ذہن کا compass ہے۔</p>
<h3 dir="rtl" lang="ur">اگلے 40 سیکنڈ: چار مرکزی نکات</h3>
<p dir="rtl" lang="ur">صرف keywords لکھیں:</p>
<ul dir="rtl" lang="ur"><li>تعارف/پس منظر</li><li>فوائد</li><li>نقصانات</li><li>ذمہ دار استعمال/نتیجہ</li></ul>
<p dir="rtl" lang="ur">ماحولیاتی تبدیلی کے لیے:</p>
<ul dir="rtl" lang="ur"><li>مفہوم اور اسباب</li><li>پانی و زراعت</li><li>صحت و آفات</li><li>حکومت + فرد کی ذمہ داری</li></ul>
<h3 dir="rtl" lang="ur">آخری 20 سیکنڈ: مثال اور اختتام</h3>
<p dir="rtl" lang="ur">ایک حقیقی یا عمومی مثال، اور نتیجے کا مرکزی جملہ سوچیں۔ ایسی عددی معلومات نہ لکھیں جن کا یقین نہ ہو۔ غلط data تحریر کو مضبوط نہیں بلکہ غیر معتبر بناتا ہے۔</p>
<h2 dir="rtl" lang="ur">م-د-ث-ن طریقہ</h2>
<h3 dir="rtl" lang="ur">م: موضوع واضح کریں</h3>
<p dir="rtl" lang="ur">تمہید میں موضوع کی تعریف، اہمیت یا موجودہ پس منظر دیں۔ تمہید کا مقصد پورا مضمون پہلے ہی ختم کرنا نہیں؛ قاری کو سمت دینا ہے۔</p>
<p dir="rtl" lang="ur">کمزور آغاز:</p>
<p class="ex" dir="rtl" lang="ur">آج کل ہر طرف سوشل میڈیا ہی سوشل میڈیا ہے اور اس کے بہت فائدے اور نقصانات ہیں۔</p>
<p dir="rtl" lang="ur">یہ عام اور تکراری ہے۔</p>
<p dir="rtl" lang="ur">بہتر آغاز:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا نے رابطے، معلومات اور اظہار کے طریقوں کو تیزی سے بدل دیا ہے۔ چند لمحوں میں خبر، تعلیمی مواد اور ذاتی رائے ہزاروں افراد تک پہنچ سکتی ہے؛ اسی رفتار کے ساتھ غلط معلومات، وقت کے ضیاع اور ذہنی دباؤ کے خطرات بھی بڑھ جاتے ہیں۔</p>
<p dir="rtl" lang="ur">اس تمہید میں موضوع، اہمیت اور مرکزی کشمکش تینوں آ گئے۔</p>
<h3 dir="rtl" lang="ur">د: دلائل ترتیب دیں</h3>
<p dir="rtl" lang="ur">ہر body paragraph میں ایک مرکزی نکتہ ہونا چاہیے۔</p>
<p dir="rtl" lang="ur">پیراگراف کا اندرونی ڈھانچا:</p>
<ol dir="rtl" lang="ur"><li>مرکزی جملہ</li><li>وضاحت</li><li>مثال یا نتیجہ</li><li>اگلے نکتے سے ربط</li></ol>
<p dir="rtl" lang="ur">مثال:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا کا ایک بڑا فائدہ تعلیمی مواد تک فوری رسائی ہے۔ طلبہ ویڈیو لیکچر، ڈیجیٹل کتب، علمی مباحث اور امتحانی رہنمائی سے فائدہ اٹھا سکتے ہیں۔ دور دراز علاقوں میں رہنے والا طالب علم بھی ایسے استاد یا کورس تک پہنچ سکتا ہے جو مقامی طور پر دستیاب نہ ہو۔ تاہم یہ فائدہ اسی وقت حقیقی بنتا ہے جب طالب علم معتبر ذرائع اور غیر مصدقہ مواد میں فرق کرنا سیکھے۔</p>
<p dir="rtl" lang="ur">یہ پیراگراف صرف “تعلیم میں فائدہ” کہہ کر نہیں رکتا؛ وضاحت، مثال اور شرط بھی دیتا ہے۔</p>
<h3 dir="rtl" lang="ur">ث: ثبوت یا مثال دیں</h3>
<p dir="rtl" lang="ur">ثبوت ہمیشہ statistics نہیں ہوتے۔ امتحانی مضمون میں ثبوت کی شکلیں:</p>
<ul dir="rtl" lang="ur"><li>روزمرہ کی واضح مثال؛</li><li>پاکستان یا مقامی حالات سے مناسب تعلق؛</li><li>سبب اور نتیجے کی منطقی وضاحت؛</li><li>مختصر مشاہدہ؛</li><li>معروف تاریخی یا ادبی حوالہ، اگر درست یاد ہو؛</li><li>مناسب شعر یا قول، اگر موضوع سے براہِ راست متعلق اور صحیح ہو۔</li></ul>
<p dir="rtl" lang="ur">غلط یا گھڑا ہوا quote نقصان دہ ہے۔ شاعر کا نام یقین سے معلوم نہ ہو تو شعر شامل نہ کریں۔ ایک مضبوط تشریح دس غیر متعلقہ اشعار سے بہتر ہے۔</p>
<h3 dir="rtl" lang="ur">ن: نتیجہ نکالیں</h3>
<p dir="rtl" lang="ur">نتیجہ نئے دلائل کا گودام نہیں۔ یہ مضمون کی بحث کو جمع، موقف کو واضح اور مستقبل کی سمت کو مختصر کرتا ہے۔</p>
<p dir="rtl" lang="ur">کمزور نتیجہ:</p>
<p class="ex" dir="rtl" lang="ur">آخر میں یہی کہوں گا کہ سوشل میڈیا کے فائدے بھی ہیں اور نقصانات بھی۔ ہمیں اسے اچھا استعمال کرنا چاہیے۔</p>
<p dir="rtl" lang="ur">بہتر نتیجہ:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا کو مکمل پابندی یا بے حد آزادی کے درمیان دیکھنا مسئلے کو آسان بنا دینا ہے۔ اصل ضرورت digital literacy، وقت کی حد، source verification اور اخلاقی ذمہ داری کی ہے۔ جب صارف خبر کو جانچ کر، دوسروں کی عزت کا خیال رکھ کر اور مقصد کے مطابق وقت صرف کرتا ہے تو یہی platform تعلیم اور رابطے کا مؤثر ذریعہ بن سکتا ہے؛ بے احتیاط استعمال اسے نقصان میں بدل دیتا ہے۔</p>
<h2 dir="rtl" lang="ur">پانچ پیراگراف کا لچک دار خاکہ</h2>
<p dir="rtl" lang="ur">چار سو سے پانچ سو الفاظ کے مضمون کے لیے پانچ سے سات پیراگراف عموماً قابلِ عمل ہوتے ہیں۔ یہ لازمی formula نہیں، مگر مضبوط starting structure ہے۔</p>
<h3 dir="rtl" lang="ur">پیراگراف 1: تمہید اور مرکزی موقف</h3>
<p dir="rtl" lang="ur">موضوع کا دائرہ واضح کریں۔</p>
<h3 dir="rtl" lang="ur">پیراگراف 2: پہلا بڑا پہلو</h3>
<p dir="rtl" lang="ur">سب سے اہم سبب، فائدہ یا مسئلہ۔</p>
<h3 dir="rtl" lang="ur">پیراگراف 3: دوسرا بڑا پہلو</h3>
<p dir="rtl" lang="ur">اثر، مثال یا متبادل رخ۔</p>
<h3 dir="rtl" lang="ur">پیراگراف 4: پیچیدگی یا مخالف پہلو</h3>
<p dir="rtl" lang="ur">balanced topic میں نقصانات، اعتراض یا limitation۔</p>
<h3 dir="rtl" lang="ur">پیراگراف 5: حل، ذمہ داری اور نتیجہ</h3>
<p dir="rtl" lang="ur">عملی تجاویز اور مرکزی فیصلہ۔</p>
<p dir="rtl" lang="ur">زیادہ الفاظ میں پیراگراف 4 اور 5 کو الگ الگ تفصیل دی جا سکتی ہے۔</p>
<h2 dir="rtl" lang="ur">ربط: جملے الگ نہ لگیں</h2>
<p dir="rtl" lang="ur">مربوط مضمون میں قاری کو ہر موڑ پر معلوم ہوتا ہے کہ نیا نکتہ پچھلے سے کیسے جڑا ہے۔</p>
<h3 dir="rtl" lang="ur">اضافہ</h3>
<ul dir="rtl" lang="ur"><li>مزید برآں</li><li>اس کے علاوہ</li><li>اسی طرح</li><li>نہ صرف… بلکہ</li></ul>
<h3 dir="rtl" lang="ur">تضاد</h3>
<ul dir="rtl" lang="ur"><li>تاہم</li><li>اس کے برعکس</li><li>دوسری جانب</li><li>اگرچہ</li><li>باوجود اس کے</li></ul>
<h3 dir="rtl" lang="ur">سبب</h3>
<ul dir="rtl" lang="ur"><li>کیونکہ</li><li>اس کی بنیادی وجہ یہ ہے</li><li>چوں کہ</li><li>اسی سبب سے</li></ul>
<h3 dir="rtl" lang="ur">نتیجہ</h3>
<ul dir="rtl" lang="ur"><li>لہٰذا</li><li>نتیجتاً</li><li>یوں</li><li>اس کے باعث</li></ul>
<h3 dir="rtl" lang="ur">مثال</h3>
<ul dir="rtl" lang="ur"><li>مثال کے طور پر</li><li>بالخصوص</li><li>اس کی ایک واضح مثال</li></ul>
<h3 dir="rtl" lang="ur">ترتیب</h3>
<ul dir="rtl" lang="ur"><li>اوّل</li><li>ابتدا میں</li><li>بعد ازاں</li><li>آخرکار</li></ul>
<p dir="rtl" lang="ur">Transitions کی کثرت خود coherence نہیں بناتی۔ ہر جملے میں “مزید برآں” لکھنا مصنوعی ہے۔ اصل ربط خیالات کے منطقی تسلسل سے آتا ہے۔</p>
<h2 dir="rtl" lang="ur">تمہید لکھنے کے پانچ مؤثر طریقے</h2>
<h3 dir="rtl" lang="ur">1. واضح تعریف</h3>
<p class="ex" dir="rtl" lang="ur">ماحولیاتی تبدیلی سے مراد طویل مدت میں درجۂ حرارت، بارش، موسموں اور شدید موسمی واقعات کے pattern میں نمایاں تبدیلی ہے۔</p>
<p dir="rtl" lang="ur">تعریف کے بعد فوراً اہمیت بتائیں۔</p>
<h3 dir="rtl" lang="ur">2. تضاد</h3>
<p class="ex" dir="rtl" lang="ur">وہ technology جو چند لمحوں میں دنیا کو جوڑتی ہے، اسی رفتار سے غلط خبر اور نفرت بھی پھیلا سکتی ہے۔</p>
<h3 dir="rtl" lang="ur">3. سوال</h3>
<p class="ex" dir="rtl" lang="ur">کیا جدید لباس صرف ذاتی پسند ہے، یا اس کے انتخاب میں معاشرتی اقدار، موسم، سہولت اور شناخت بھی شامل ہیں؟</p>
<p dir="rtl" lang="ur">سوال کے بعد جواب کی سمت دیں؛ سوال کو ہوا میں نہ چھوڑیں۔</p>
<h3 dir="rtl" lang="ur">4. منظر</h3>
<p class="ex" dir="rtl" lang="ur">ایک ہی گھر میں چار افراد اپنے اپنے screen پر مصروف ہوں اور کمرے میں موجود ہونے کے باوجود بات نہ کریں تو رابطے کی نئی technology کا paradox سامنے آتا ہے۔</p>
<h3 dir="rtl" lang="ur">5. عمومی مشاہدہ</h3>
<p class="ex" dir="rtl" lang="ur">نوجوان کسی بھی معاشرے کی توانائی، تخلیقی قوت اور مستقبل ہوتے ہیں؛ اسی لیے ان کی تعلیم، کردار اور فیصلہ سازی میں خرابی پورے سماج کو متاثر کرتی ہے۔</p>
<h3 dir="rtl" lang="ur">کن آغازوں سے بچیں</h3>
<ul dir="rtl" lang="ur"><li>“جیساکہ ہم سب جانتے ہیں…”</li><li>“یہ دنیا کا سب سے اہم موضوع ہے…”</li><li>“زمانہ قدیم سے…” جب تاریخی تعلق نہ ہو</li><li>لغت کی غیر ضروری طویل تعریف</li><li>پانچ اشعار کی قطار</li><li>موضوع کے الفاظ کو صرف دوبارہ لکھ دینا</li></ul>
<h2 dir="rtl" lang="ur">مضبوط body paragraph کیسے لکھیں</h2>
<p dir="rtl" lang="ur">ہر پیراگراف کو ایک سوال کا جواب سمجھیں۔</p>
<p dir="rtl" lang="ur">موضوع: ماحولیاتی تبدیلی</p>
<p dir="rtl" lang="ur">پیراگراف سوال: اس کا زراعت پر کیا اثر ہے؟</p>
<p>Model paragraph:</p>
<p class="ex" dir="rtl" lang="ur">پاکستان کی معیشت اور غذائی تحفظ زراعت سے گہرا تعلق رکھتے ہیں، اس لیے بارش اور درجۂ حرارت کے غیر یقینی pattern براہِ راست نقصان پہنچاتے ہیں۔ شدید گرمی فصل کی نشوونما کم کر سکتی ہے، بے وقت بارش کٹائی متاثر کرتی ہے، جبکہ طویل خشک دور پانی کی طلب بڑھا دیتا ہے۔ چھوٹے کسان کے پاس مہنگی irrigation technology یا فصل کی فوری تبدیلی کے وسائل محدود ہو سکتے ہیں۔ یوں climate change صرف ماحول کا مسئلہ نہیں رہتا بلکہ خوراک کی قیمت، دیہی روزگار اور قومی معیشت کا مسئلہ بن جاتا ہے۔</p>
<p dir="rtl" lang="ur">ساخت:</p>
<ul dir="rtl" lang="ur"><li>مرکزی دعویٰ</li><li>mechanism</li><li>متاثرہ گروہ</li><li>وسیع نتیجہ</li></ul>
<h2 dir="rtl" lang="ur">دلیل اور دعوے میں فرق</h2>
<p dir="rtl" lang="ur">دعویٰ:</p>
<p class="ex" dir="rtl" lang="ur">موبائل فون طلبہ کے لیے نقصان دہ ہیں۔</p>
<p dir="rtl" lang="ur">یہ بہت مطلق ہے۔</p>
<p dir="rtl" lang="ur">دلیل:</p>
<p class="ex" dir="rtl" lang="ur">مسلسل notifications اور بے مقصد scrolling توجہ کے دورانیے کو متاثر کر سکتے ہیں، خاص طور پر جب مطالعے کے دوران phone کی حد مقرر نہ ہو۔ دوسری جانب یہی device dictionary، recorded lecture اور communication کے لیے مفید ہے؛ اس لیے مسئلہ device کے وجود سے زیادہ استعمال کے نظم کا ہے۔</p>
<p dir="rtl" lang="ur">اچھی دلیل شرط، mechanism اور دوسری جانب کو بھی دیکھتی ہے۔</p>
<h2 dir="rtl" lang="ur">متوازن مضمون کا طریقہ</h2>
<p dir="rtl" lang="ur">Balanced essay میں دونوں جانب لکھنے کا مطلب یہ نہیں کہ آخر میں کوئی موقف ہی نہ ہو۔</p>
<p dir="rtl" lang="ur">استعمال کریں:</p>
<ol dir="rtl" lang="ur"><li>موضوع کی پیچیدگی تسلیم کریں۔</li><li>ایک جانب کے اہم points دیں۔</li><li>دوسری جانب کے حقیقی concerns دیں۔</li><li>معیار طے کریں کہ فیصلہ کس بنیاد پر ہوگا۔</li><li>nuanced conclusion دیں۔</li></ol>
<p dir="rtl" lang="ur">مثال:</p>
<p class="ex" dir="rtl" lang="ur">نوجوانوں کا لباس ذاتی اظہار اور comfort کا ذریعہ ہے، مگر لباس کا انتخاب سماجی context، موقع، موسم اور احترام سے مکمل طور پر الگ نہیں۔ معاشرے کو زبردستی اور تحقیر سے گریز کرنا چاہیے، جبکہ نوجوانوں کو بھی آزادی کے ساتھ occasion اور local values کا شعور رکھنا چاہیے۔</p>
<p dir="rtl" lang="ur">یہ جواب نہ blanket condemnation ہے نہ بے حد generalisation۔</p>
<h2 dir="rtl" lang="ur">اشعار اور اقوال: کم مگر درست</h2>
<p dir="rtl" lang="ur">اچھا شعر مضمون کو ادبی قوت دے سکتا ہے، لیکن تین شرطیں ہیں:</p>
<ol dir="rtl" lang="ur"><li>شعر صحیح ہو۔</li><li>شاعر کا نام صحیح ہو، اگر لکھیں۔</li><li>شعر واقعی نکتے کی وضاحت کرے۔</li></ol>
<p dir="rtl" lang="ur">صرف شعر لکھ کر آگے نہ بڑھیں۔ ایک سطر میں تعلق واضح کریں۔</p>
<p dir="rtl" lang="ur">غلط طریقہ:</p>
<p class="ex" dir="rtl" lang="ur">ایک شعر ہے… [غیر متعلقہ شعر]</p>
<p dir="rtl" lang="ur">بہتر طریقہ:</p>
<p class="ex" dir="rtl" lang="ur">اقبال نوجوان کو محض عمر کا نام نہیں بلکہ بلند ارادے اور مسلسل عمل کی قوت سمجھتے ہیں؛ اس تصور کی روشنی میں نوجوانوں کی تربیت صرف نصابی کامیابی تک محدود نہیں رہ سکتی۔</p>
<p dir="rtl" lang="ur">یہاں direct quotation کے بغیر درست conceptual reference دیا گیا ہے۔ امتحان میں exact شعر تبھی لکھیں جب مکمل یقین ہو۔</p>
<h2 dir="rtl" lang="ur">الفاظ کا معیار: مشکل نہیں، درست</h2>
<p dir="rtl" lang="ur">اعلیٰ معیار کی اردو کا مطلب ہر سادہ لفظ کو ثقیل فارسی ترکیب سے بدلنا نہیں۔ زبان فصیح، واضح اور موضوع کے مطابق ہونی چاہیے۔</p>
<p dir="rtl" lang="ur">کمزور:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا بہت زیادہ برا مسئلہ ہے جو ہر کسی کو خراب کر رہا ہے۔</p>
<p dir="rtl" lang="ur">بہتر:</p>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا کا غیر منظم اور غیر ذمہ دار استعمال وقت، توجہ اور ذہنی سکون کو متاثر کر سکتا ہے۔</p>
<h3 dir="rtl" lang="ur">مفید علمی الفاظ</h3>
<ul dir="rtl" lang="ur"><li>اثرات</li><li>ذمہ داری</li><li>شعور</li><li>تنقیدی سوچ</li><li>معتبر ذریعہ</li><li>غیر مصدقہ معلومات</li><li>معاشرتی رویہ</li><li>عملی اقدام</li><li>پائیدار حل</li><li>اعتدال</li><li>ترجیحات</li><li>تربیت</li><li>آگاہی</li><li>جواب دہی</li><li>وسائل</li></ul>
<p dir="rtl" lang="ur">ہر لفظ درست context میں استعمال کریں۔</p>
<h2 dir="rtl" lang="ur">اردو املا کی عام غلطیاں</h2>
<p dir="rtl" lang="ur">مضمون میں repeated spelling errors پڑھنے کا flow توڑتے ہیں۔ اپنی personal error list بنائیں۔ عام confusion:</p>
<ul dir="rtl" lang="ur"><li>کیونکہ / کیو نکہ (درست: کیونکہ)</li><li>ذمہ داری / زمہ داری (درست: ذمہ داری)</li><li>ذریعہ / زریعہ (درست: ذریعہ)</li><li>مسئلہ / مسلہ (درست: مسئلہ)</li><li>معاشرہ / ماشرا (درست: معاشرہ)</li><li>ماحول / ماہول (درست: ماحول)</li><li>ضرورت / زرورت (درست: ضرورت)</li><li>اثر / اسر (درست: اثر)</li><li>تعلیم / تلیم (درست: تعلیم)</li><li>حقیقت / حکیقت (درست: حقیقت)</li></ul>
<p dir="rtl" lang="ur">یہ فہرست مکمل نہیں۔ اپنی کاپی میں استاد کی corrections سے بار بار آنے والی دس غلطیاں الگ کریں۔</p>
<h2 dir="rtl" lang="ur">واحد، جمع اور مذکر، مؤنث</h2>
<p dir="rtl" lang="ur">جملے میں agreement واضح رکھیں۔</p>
<p dir="rtl" lang="ur">غلط:</p>
<p class="ex" dir="rtl" lang="ur">یہ مسائل بہت اہم ہے۔</p>
<p dir="rtl" lang="ur">درست:</p>
<p class="ex" dir="rtl" lang="ur">یہ مسائل بہت اہم ہیں۔</p>
<p dir="rtl" lang="ur">غلط:</p>
<p class="ex" dir="rtl" lang="ur">تعلیم ایک اہم ذریعہ ہیں۔</p>
<p dir="rtl" lang="ur">درست:</p>
<p class="ex" dir="rtl" lang="ur">تعلیم ایک اہم ذریعہ ہے۔</p>
<p dir="rtl" lang="ur">غلط:</p>
<p class="ex" dir="rtl" lang="ur">نوجوان نسل اپنے ذمہ داری سمجھے۔</p>
<p dir="rtl" lang="ur">درست:</p>
<p class="ex" dir="rtl" lang="ur">نوجوان نسل اپنی ذمہ داری سمجھے۔</p>
<p dir="rtl" lang="ur">پیراگراف کے آخر میں صرف spelling نہیں، verb agreement بھی دیکھیں۔</p>
<h2 dir="rtl" lang="ur">رموزِ اوقاف</h2>
<p dir="rtl" lang="ur">اردو تحریر میں مناسب punctuation clarity بڑھاتی ہے۔</p>
<ul dir="rtl" lang="ur"><li><strong>،</strong> مختصر وقفہ</li><li><strong>۔</strong> جملے کا اختتام</li><li><strong>؟</strong> سوال</li><li><strong>:</strong> وضاحت یا فہرست سے پہلے</li><li><strong>؛</strong> قریبی مگر الگ clauses کے درمیان، ضرورت کے مطابق</li></ul>
<p dir="rtl" lang="ur">ایک پورا پیراگراف ایک ہی جملے میں نہ لکھیں۔ بہت زیادہ comma بھی sentence boundary ختم کر دیتا ہے۔</p>
<h2 dir="rtl" lang="ur">الفاظ کی حد کیسے سنبھالیں</h2>
<p dir="rtl" lang="ur">چار سو سے پانچ سو الفاظ کا مضمون planning کے بغیر آسانی سے 650 الفاظ یا صرف 250 الفاظ ہو سکتا ہے۔</p>
<h3 dir="rtl" lang="ur">عملی تقسیم</h3>
<p dir="rtl" lang="ur">تقریباً:</p>
<ul dir="rtl" lang="ur"><li>تمہید: 55–70 الفاظ</li><li>body paragraph 1: 70–85</li><li>body paragraph 2: 70–85</li><li>body paragraph 3: 70–85</li><li>solution/counterpoint: 70–85</li><li>نتیجہ: 50–65</li></ul>
<p dir="rtl" lang="ur">یہ rigid rule نہیں۔ مقصد proportional development ہے۔</p>
<h3 dir="rtl" lang="ur">کم الفاظ کی علامتیں</h3>
<ul dir="rtl" lang="ur"><li>ہر point صرف ایک sentence؛</li><li>مثال یا explanation غائب؛</li><li>نتیجہ اچانک؛</li><li>topic کے کئی حصے unanswered۔</li></ul>
<h3 dir="rtl" lang="ur">زیادہ الفاظ کی علامتیں</h3>
<ul dir="rtl" lang="ur"><li>ایک نکتہ کئی بار؛</li><li>غیر متعلقہ تاریخ؛</li><li>طویل اشعار؛</li><li>ہر مثال کی پوری کہانی؛</li><li>تمہید میں آدھا مضمون۔</li></ul>
<p dir="rtl" lang="ur">Practice میں ایک مکمل مضمون لکھ کر words count کریں۔ پھر اپنی handwriting میں اندازہ سیکھیں کہ ایک average line میں کتنے الفاظ آتے ہیں۔</p>
<h2 dir="rtl" lang="ur">Official-model topic 1: سوشل میڈیا کے مثبت اور منفی پہلو</h2>
<h3 dir="rtl" lang="ur">دو منٹ کا خاکہ</h3>
<p dir="rtl" lang="ur"><strong>موقف:</strong> فائدہ یا نقصان platform سے زیادہ استعمال پر منحصر ہے۔</p>
<p dir="rtl" lang="ur"><strong>نکات:</strong></p>
<ol dir="rtl" lang="ur"><li>رابطہ اور information access</li><li>تعلیم، business اور civic awareness</li><li>misinformation، privacy، distraction، comparison</li><li>digital literacy، time limits، ethical use</li></ol>
<h3 dir="rtl" lang="ur">ممکنہ تمہید</h3>
<p class="ex" dir="rtl" lang="ur">سوشل میڈیا نے خبر، تعلیم، کاروبار اور ذاتی رابطے کے درمیان فاصلے کم کر دیے ہیں۔ ایک معمولی phone کے ذریعے طالب علم عالمی lecture دیکھ سکتا، چھوٹا کاروبار اپنے صارف تک پہنچ سکتا اور شہری فوری اطلاع حاصل کر سکتا ہے۔ مگر یہی رفتار غلط خبر، نجی معلومات کے غلط استعمال اور مسلسل ذہنی مصروفیت کو بھی بڑھاتی ہے۔</p>
<h3>paragraph ideas</h3>
<p dir="rtl" lang="ur">تعلیم کے فائدے کو source verification کے ساتھ جوڑیں۔ کاروبار کے فائدے کو scams اور misleading advertising کے concern سے balance کریں۔ ذہنی صحت پر absolute medical claims نہ کریں؛ “دباؤ بڑھ سکتا ہے”، “موازنہ متاثر کر سکتا ہے” جیسے cautious expressions بہتر ہیں۔</p>
<h3 dir="rtl" lang="ur">نتیجہ</h3>
<p dir="rtl" lang="ur">پابندی کے بجائے literacy، moderation اور accountability۔</p>
<h2 dir="rtl" lang="ur">Official-model topic 2: نوجوانوں میں بڑھتی ہوئی بے راہ روی</h2>
<h3 dir="rtl" lang="ur">حساس زبان</h3>
<p dir="rtl" lang="ur">پورے نوجوان طبقے کو “خراب” قرار نہ دیں۔ لکھیں:</p>
<p class="ex" dir="rtl" lang="ur">بعض نوجوانوں میں…</p>
<p class="ex" dir="rtl" lang="ur">بعض حالات میں…</p>
<p class="ex" dir="rtl" lang="ur">یہ رجحان کئی باہم جڑے عوامل سے پیدا ہو سکتا ہے…</p>
<h3 dir="rtl" lang="ur">خاکہ</h3>
<ol dir="rtl" lang="ur"><li>نوجوانوں کی اہمیت اور مسئلے کی تعریف</li><li>family communication، تعلیم، peer pressure، online influence</li><li>unemployment، lack of healthy recreation، identity confusion</li><li>نتائج: تعلیم، relationships، law/order، self-discipline</li><li>حل: mentorship، sports، counselling, skill opportunities، balanced accountability</li></ol>
<h3 dir="rtl" lang="ur">اہم نکتہ</h3>
<p dir="rtl" lang="ur">صرف “والدین قصوروار ہیں” یا “موبائل قصوروار ہے” نہ لکھیں۔ سماجی مسئلے عموماً multi-causal ہوتے ہیں۔</p>
<h2 dir="rtl" lang="ur">Official-model topic 3: نوجوانوں کا لباس اور ہمارا معاشرہ</h2>
<p dir="rtl" lang="ur">یہ موضوع آسانی سے judgmental ہو سکتا ہے۔ اعلیٰ معیار کی تحریر میں dignity اور context ضروری ہے۔</p>
<h3 dir="rtl" lang="ur">خاکہ</h3>
<ol dir="rtl" lang="ur"><li>لباس: ضرورت، شناخت، culture، expression</li><li>changing fashion، global media، market</li><li>personal freedom + occasion + climate + affordability</li><li>elders and youth dialogue; no ridicule or coercion</li><li>balanced conclusion: modesty, comfort, cultural confidence, respect</li></ol>
<h3 dir="rtl" lang="ur">اچھا موقف</h3>
<p class="ex" dir="rtl" lang="ur">لباس کے مسئلے کو نسلوں کی لڑائی بنانے کے بجائے comfort، occasion، cultural continuity، dignity اور personal choice کے متوازن اصولوں سے دیکھنا چاہیے۔</p>
<h2 dir="rtl" lang="ur">Official-model topic 4: ماحولیاتی تبدیلی اور اس کے اثرات</h2>
<h3 dir="rtl" lang="ur">خاکہ</h3>
<ol><li>definition and urgency</li><li>heat, rainfall, glaciers/water</li><li>agriculture, health, floods/droughts</li><li>unequal impact on vulnerable communities</li><li>policy + community + individual action</li></ol>
<h3 dir="rtl" lang="ur">data کے بارے میں احتیاط</h3>
<p>Exact temperature rise، flood loss یا emission share تبھی لکھیں جب reliable source اور number یاد ہو۔ ورنہ logical, accurate qualitative explanation بہتر ہے۔</p>
<h2 dir="rtl" lang="ur">مکمل نمونہ مضمون: سوشل میڈیا کا استعمال — مثبت اور منفی پہلو</h2>
<p dir="rtl" lang="ur">ذیل کا مضمون original ہے اور current HSSC-II model range کو ذہن میں رکھ کر تقریباً چار سو سے پانچ سو الفاظ کے اندر لکھا گیا ہے۔ اسے لفظ بہ لفظ رٹنے کے بجائے structure، balance اور paragraph development کا نمونہ سمجھیں۔</p>
<h3 dir="rtl" lang="ur">سوشل میڈیا کا استعمال: مثبت اور منفی پہلو</h3>
<p dir="rtl" lang="ur">سوشل میڈیا نے انسانی رابطے، معلومات کے حصول اور اظہارِ رائے کے طریقوں میں نمایاں تبدیلی پیدا کی ہے۔ آج ایک طالب علم چند لمحوں میں کسی علمی lecture تک پہنچ سکتا ہے، ایک چھوٹا کاروبار اپنی مصنوعات دور دراز صارفین کو دکھا سکتا ہے، اور خاندان مختلف شہروں یا ملکوں میں رہتے ہوئے بھی رابطہ برقرار رکھ سکتے ہیں۔ تاہم اسی آسانی کے ساتھ غلط معلومات، وقت کے ضیاع، نجی زندگی میں مداخلت اور غیر ضروری ذہنی دباؤ کے خطرات بھی بڑھ گئے ہیں۔ اس لیے سوشل میڈیا کو مکمل طور پر اچھا یا برا قرار دینے کے بجائے اس کے استعمال کا معیار دیکھنا ضروری ہے۔</p>
<p dir="rtl" lang="ur">تعلیم کے میدان میں سوشل میڈیا کے فوائد واضح ہیں۔ تعلیمی صفحات، ویڈیو لیکچر، online study groups اور digital libraries طلبہ کو اضافی رہنمائی فراہم کرتے ہیں۔ کسی مشکل concept کی مختلف وضاحتیں دیکھ کر طالب علم اپنی کمزوری دور کر سکتا ہے۔ اسی طرح قدرتی آفت، traffic یا صحتِ عامہ سے متعلق فوری اطلاع لوگوں کو بروقت فیصلہ کرنے میں مدد دیتی ہے۔ سماجی مسائل پر آگاہی اور فلاحی سرگرمیوں کے لیے بھی یہ platforms مؤثر ثابت ہو سکتے ہیں۔</p>
<p dir="rtl" lang="ur">دوسری جانب ہر مقبول یا بار بار share ہونے والی بات درست نہیں ہوتی۔ غیر مصدقہ خبر خوف، بداعتمادی یا کسی فرد کی بدنامی کا سبب بن سکتی ہے۔ بعض صارفین headline پڑھ کر مکمل context کے بغیر رائے قائم کر لیتے ہیں۔ مسلسل notifications اور بے مقصد scrolling مطالعے، نیند اور گھر کے تعلقات کے لیے مقرر وقت کو متاثر کر سکتی ہے۔ تصاویر اور کامیابیوں کا مصنوعی موازنہ بھی بعض نوجوانوں میں احساسِ کمتری یا بے اطمینانی پیدا کر سکتا ہے۔</p>
<p dir="rtl" lang="ur">نجی معلومات کا تحفظ ایک اور اہم مسئلہ ہے۔ location، شناختی دستاویزات، ذاتی تصاویر یا مالی معلومات غیر محتاط انداز میں share کرنا نقصان کا باعث بن سکتا ہے۔ اسی طرح online اختلاف میں گالی، تضحیک یا کردار کشی آزادیِ اظہار نہیں بلکہ اخلاقی ذمہ داری کی خلاف ورزی ہے۔ صارف کو یہ سمجھنا چاہیے کہ screen کے دوسری جانب بھی ایک حقیقی انسان موجود ہے۔</p>
<p dir="rtl" lang="ur">سوشل میڈیا کے بہتر استعمال کے لیے digital literacy ضروری ہے۔ خبر share کرنے سے پہلے source اور date دیکھنا، privacy settings سمجھنا، روزانہ وقت کی حد مقرر کرنا اور مطالعے کے دوران notifications بند کرنا چھوٹے مگر مؤثر اقدامات ہیں۔ والدین اور اساتذہ کو صرف پابندی لگانے کے بجائے نوجوانوں کو verification، respectful communication اور cyber safety کی عملی تربیت دینی چاہیے۔</p>
<p dir="rtl" lang="ur">آخرکار سوشل میڈیا ایک طاقتور ذریعہ ہے، مگر اس طاقت کا نتیجہ صارف کی عادت اور نیت سے طے ہوتا ہے۔ ذمہ دار استعمال اسے تعلیم، رابطے اور خدمت کا وسیلہ بنا سکتا ہے، جبکہ بے احتیاطی اسے وقت، اعتماد اور سکون کے نقصان میں بدل دیتی ہے۔ اعتدال، تحقیق اور اخلاقی شعور ہی اس technology سے حقیقی فائدہ اٹھانے کی بنیاد ہیں۔</p>
<h2>Model essay کی analysis</h2>
<h3 dir="rtl" lang="ur">تمہید</h3>
<p dir="rtl" lang="ur">موضوع کا فائدہ اور نقصان دونوں سامنے آتے ہیں، پھر thesis دیا جاتا ہے: فیصلہ استعمال کے معیار پر ہوگا۔</p>
<h3>body 1</h3>
<p dir="rtl" lang="ur">تعلیم، اطلاع اور سماجی فائدہ۔ ہر claim کے ساتھ mechanism ہے۔</p>
<h3>body 2</h3>
<p>misinformation اور distraction۔ exaggeration سے گریز۔</p>
<h3>body 3</h3>
<p dir="rtl" lang="ur">privacy اور اخلاقیات؛ موضوع صرف time waste تک محدود نہیں۔</p>
<h3>body 4</h3>
<p>practical solutions: source/date, privacy, time limits, notifications, training.</p>
<h3 dir="rtl" lang="ur">نتیجہ</h3>
<p dir="rtl" lang="ur">وہی central thesis واپس آتی ہے مگر لفظی repetition نہیں۔</p>
<h2 dir="rtl" lang="ur">مختصر نمونہ: ماحولیاتی تبدیلی کی تمہید اور نتیجہ</h2>
<h3 dir="rtl" lang="ur">تمہید</h3>
<p class="ex" dir="rtl" lang="ur">ماحولیاتی تبدیلی کسی ایک گرم دن یا غیر معمولی بارش کا نام نہیں، بلکہ طویل مدت میں موسم، درجۂ حرارت اور بارش کے pattern میں ایسی تبدیلی ہے جو قدرتی نظام اور انسانی زندگی دونوں کو متاثر کرتی ہے۔ پاکستان جیسے ملک میں، جہاں زراعت، پانی اور آبادی کا بڑا حصہ موسم سے براہِ راست جڑا ہے، یہ مسئلہ مستقبل کا اندیشہ نہیں بلکہ موجودہ planning کا تقاضا ہے۔</p>
<h3 dir="rtl" lang="ur">نتیجہ</h3>
<p class="ex" dir="rtl" lang="ur">ماحولیاتی تبدیلی کا مقابلہ صرف درخت لگانے کے ایک دن یا فرد کی چھوٹی عادت تک محدود نہیں، اگرچہ یہ اقدامات اہم ہیں۔ پانی کے بہتر انتظام، resilient agriculture، early-warning systems، صاف توانائی اور شہری planning کو مسلسل policy کا حصہ بنانا ہوگا۔ فرد، ادارہ اور حکومت اپنی اپنی سطح پر ذمہ داری قبول کریں تو خطرات کو مکمل ختم نہ سہی، نمایاں حد تک کم کیا جا سکتا ہے۔</p>
<p dir="rtl" lang="ur">دیکھیں کہ نتیجہ practical اور multi-level ہے۔</p>
<h2 dir="rtl" lang="ur">کمزور اور بہتر پیراگراف</h2>
<h3 dir="rtl" lang="ur">کمزور</h3>
<p class="ex" dir="rtl" lang="ur">نوجوان آج کل بہت خراب ہو گئے ہیں۔ وہ موبائل استعمال کرتے ہیں اور بڑوں کی بات نہیں مانتے۔ معاشرے میں برائیاں بڑھ رہی ہیں۔ حکومت کو کچھ کرنا چاہیے۔</p>
<p dir="rtl" lang="ur">مسائل:</p>
<ul dir="rtl" lang="ur"><li>sweeping generalisation؛</li><li>cause کی وضاحت نہیں؛</li><li>evidence یا mechanism نہیں؛</li><li>vague solution؛</li><li>disrespectful tone۔</li></ul>
<h3 dir="rtl" lang="ur">بہتر</h3>
<p class="ex" dir="rtl" lang="ur">بعض نوجوانوں میں discipline کی کمزوری کو صرف موبائل یا ایک نسل کی خرابی سے جوڑنا درست نہیں۔ family communication کی کمی، مثبت سرگرمیوں کے محدود مواقع، peer pressure، online influence اور مستقبل کے بارے میں بے یقینی ایک دوسرے سے مل کر رویے متاثر کر سکتے ہیں۔ اصلاح کے لیے سزا کے ساتھ mentorship، کھیل، skill development اور قابلِ اعتماد counselling کی ضرورت ہے تاکہ نوجوان کو صرف غلطی سے روکا نہ جائے بلکہ بہتر متبادل بھی دیا جائے۔</p>
<h2 dir="rtl" lang="ur">revision کے تین درجے</h2>
<h3 dir="rtl" lang="ur">درجہ 1: مضمون کی سطح</h3>
<ul dir="rtl" lang="ur"><li>کیا ہر حصہ topic کو جواب دیتا ہے؟</li><li>کیا موقف واضح ہے؟</li><li>کیا ترتیب منطقی ہے؟</li><li>کیا conclusion discussion سے نکلتا ہے؟</li></ul>
<h3 dir="rtl" lang="ur">درجہ 2: پیراگراف کی سطح</h3>
<ul dir="rtl" lang="ur"><li>ہر paragraph کا ایک main point؟</li><li>explanation اور example؟</li><li>اگلے paragraph سے link؟</li></ul>
<h3 dir="rtl" lang="ur">درجہ 3: جملے کی سطح</h3>
<ul dir="rtl" lang="ur"><li>املا؟</li><li>واحد جمع؟</li><li>فعل کی مطابقت؟</li><li>punctuation؟</li><li>غیر ضروری repetition؟</li></ul>
<p dir="rtl" lang="ur">امتحان میں آخری دو سے تین منٹ اسی ترتیب سے check کریں۔ پہلے بڑے مسئلے، پھر چھوٹی spelling۔</p>
<h2 dir="rtl" lang="ur">عام غلطیاں اور ان کا حل</h2>
<h3 dir="rtl" lang="ur">غلطی 1: رٹا ہوا مضمون force کرنا</h3>
<p dir="rtl" lang="ur">Topic “سوشل میڈیا کے مثبت و منفی پہلو” ہے، مگر طالب علم “سائنس کے فوائد” والا memorised essay لکھ دیتا ہے۔</p>
<p dir="rtl" lang="ur">حل: عنوان کے key words underline کریں اور ہر paragraph کے بعد ذہنی سوال: “کیا یہ اسی عنوان کا جواب ہے؟”</p>
<h3 dir="rtl" lang="ur">غلطی 2: تمہید بہت لمبی</h3>
<p dir="rtl" lang="ur">حل: 10–15 percent words کافی۔ main discussion جلد شروع کریں۔</p>
<h3 dir="rtl" lang="ur">غلطی 3: points کی فہرست مگر explanation نہیں</h3>
<p dir="rtl" lang="ur">حل: ہر point کے بعد “کیسے؟ کیوں؟ مثال؟ نتیجہ؟” میں سے کم از کم دو سوالوں کا جواب دیں۔</p>
<h3 dir="rtl" lang="ur">غلطی 4: بے بنیاد statistics</h3>
<p>حل: uncertain number چھوڑ دیں۔ accurate qualitative reasoning لکھیں۔</p>
<h3 dir="rtl" lang="ur">غلطی 5: ہر topic میں وہی شعر</h3>
<p dir="rtl" lang="ur">حل: شعر optional ہے، structure ضروری۔</p>
<h3 dir="rtl" lang="ur">غلطی 6: بہت زیادہ English words</h3>
<p dir="rtl" lang="ur">Urdu میں accepted technical term آ سکتا ہے، خاص طور پر digital literacy یا climate context میں، مگر جہاں آسان اردو available ہو وہاں استعمال کریں۔ پہلی بار term کی مختصر وضاحت دیں۔</p>
<h3 dir="rtl" lang="ur">غلطی 7: جذباتی الزام</h3>
<p dir="rtl" lang="ur">حل: “تمام”، “ہمیشہ”، “صرف یہی وجہ” جیسے absolute words احتیاط سے استعمال کریں۔</p>
<h3 dir="rtl" lang="ur">غلطی 8: solution میں صرف “حکومت قدم اٹھائے”</h3>
<p dir="rtl" lang="ur">حل: action کو specific اور levels میں تقسیم کریں: policy، school، family، individual، media۔</p>
<h3 dir="rtl" lang="ur">غلطی 9: conclusion میں نیا موضوع</h3>
<p>حل: conclusion میں summary + judgement + forward direction۔ نیا evidence نہیں۔</p>
<h3 dir="rtl" lang="ur">غلطی 10: بدخطی اور paragraph boundaries غائب</h3>
<p dir="rtl" lang="ur">حل: readable spacing، واضح paragraphs، مناسب margin۔ خوش خطی ideal ہے، مگر readability لازمی۔</p>
<h2 dir="rtl" lang="ur">مضمون کے لیے evidence bank کیسے بنائیں</h2>
<p dir="rtl" lang="ur">رٹے ہوئے full essays کے بجائے topic folders بنائیں:</p>
<h3 dir="rtl" lang="ur">تعلیم</h3>
<ul><li>access</li><li>teacher quality</li><li>digital divide</li><li>critical thinking</li><li>assessment</li><li>skills</li></ul>
<h3 dir="rtl" lang="ur">ماحول</h3>
<ul><li>heat</li><li>water</li><li>agriculture</li><li>waste</li><li>transport</li><li>policy</li></ul>
<h3 dir="rtl" lang="ur">نوجوان</h3>
<ul><li>education</li><li>employment</li><li>identity</li><li>media</li><li>sports</li><li>mentorship</li></ul>
<h3>technology</h3>
<ul><li>access</li><li>efficiency</li><li>privacy</li><li>misinformation</li><li>distraction</li><li>ethics</li></ul>
<p dir="rtl" lang="ur">ہر folder میں:</p>
<ul dir="rtl" lang="ur"><li>پانچ useful terms؛</li><li>دو Pakistan-relevant examples؛</li><li>دو cause-effect chains؛</li><li>تین practical solutions؛</li><li>ایک balanced thesis۔</li></ul>
<p dir="rtl" lang="ur">نیا topic انہی building blocks سے تیار ہوگا۔</p>
<h2>سات دن کا Mazmoon Nigari plan</h2>
<h3 dir="rtl" lang="ur">دن 1: عنوان توڑنا</h3>
<p dir="rtl" lang="ur">دس topics کے “کیا، کیوں، اثر، حل” سوال لکھیں۔ مضمون نہ لکھیں۔</p>
<h3>دن 2: thesis practice</h3>
<p>ہر topic کے لیے ایک balanced central statement۔</p>
<h3>دن 3: outline</h3>
<p dir="rtl" lang="ur">پانچ topics کے دو منٹ outlines۔ وقت واقعی measure کریں۔</p>
<h3>دن 4: paragraph development</h3>
<p>تین body paragraphs لکھیں: claim + explanation + example + link۔</p>
<h3>دن 5: introductions and conclusions</h3>
<p dir="rtl" lang="ur">ایک topic کے تین مختلف intros اور دو conclusions۔</p>
<h3>دن 6: language edit</h3>
<p dir="rtl" lang="ur">اپنی پرانی writing سے spelling, agreement اور repetition errors نکالیں۔</p>
<h3>دن 7: full timed essay</h3>
<p dir="rtl" lang="ur">مقررہ word range میں لکھیں، پھر rubric-style self-check کریں۔</p>
<h2>Self-marking rubric</h2>
<p dir="rtl" lang="ur">Official marking scheme class/year کے مطابق ہوگی، مگر practice کے لیے یہ diagnostic rubric استعمال کیا جا سکتا ہے۔ یہ official FBISE rubric نہیں۔</p>
<h3>Content and relevance — 5</h3>
<ul dir="rtl" lang="ur"><li>topic کے تمام اہم حصے؟</li><li>accurate and relevant ideas؟</li><li>adequate development؟</li></ul>
<h3>Organisation — 4</h3>
<ul><li>clear introduction?</li><li>logical paragraph sequence?</li><li>transitions?</li><li>purposeful conclusion?</li></ul>
<h3>Language — 4</h3>
<ul><li>clear sentences?</li><li>appropriate vocabulary?</li><li>grammar/agreement?</li><li>spelling/punctuation?</li></ul>
<h3>Evidence and maturity — 2</h3>
<ul><li>examples or reasoning?</li><li>balanced, respectful judgement?</li></ul>
<p dir="rtl" lang="ur">15 میں score کر کے کمزور category identify کریں۔ مقصد official marks predict کرنا نہیں بلکہ revision کو specific بنانا ہے۔</p>
<h2>exam hall checklist</h2>
<p dir="rtl" lang="ur">مضمون شروع کرنے سے پہلے:</p>
<ul dir="rtl" lang="ur"><li>عنوان کے key words سمجھے؟</li><li>class/year کی word requirement معلوم؟</li><li>central position طے؟</li><li>four main points؟</li></ul>
<p dir="rtl" lang="ur">لکھتے ہوئے:</p>
<ul dir="rtl" lang="ur"><li>ہر paragraph one main job؟</li><li>explanation and example؟</li><li>repetition تو نہیں؟</li><li>tone respectful and relevant؟</li></ul>
<p dir="rtl" lang="ur">آخر میں:</p>
<ul dir="rtl" lang="ur"><li>introduction and conclusion connected؟</li><li>spelling of repeated key words؟</li><li>واحد جمع/فعل؟</li><li>paragraph boundaries؟</li><li>approximate word range؟</li></ul>
<h2>frequently asked questions</h2>
<h3 dir="rtl" lang="ur">کیا مضمون میں headings لکھنی چاہئیں؟</h3>
<p dir="rtl" lang="ur">عام formal essay میں title کے علاوہ internal headings ضروری نہیں ہوتیں، جب تک question یا teacher کی ہدایت نہ ہو۔ مربوط paragraphs کافی ہیں۔</p>
<h3 dir="rtl" lang="ur">کتنے اشعار ضروری ہیں؟</h3>
<p dir="rtl" lang="ur">کوئی universal number نہیں۔ درست اور متعلقہ شعر فائدہ دے سکتا ہے، مگر شعر کے بغیر بھی structured, accurate essay strong ہو سکتا ہے۔</p>
<h3 dir="rtl" lang="ur">کیا مشکل الفاظ زیادہ نمبر دیتے ہیں؟</h3>
<p dir="rtl" lang="ur">صرف اس وقت جب درست اور natural ہوں۔ clarity، relevance اور control زیادہ اہم ہیں۔</p>
<h3 dir="rtl" lang="ur">کیا ذاتی رائے لکھ سکتے ہیں؟</h3>
<p dir="rtl" lang="ur">استدلالی موضوع میں ہاں، مگر reason اور evidence کے ساتھ۔ “مجھے لگتا ہے” بار بار لکھنے کی ضرورت نہیں۔</p>
<h3 dir="rtl" lang="ur">کیا English technical words استعمال کیے جا سکتے ہیں؟</h3>
<p dir="rtl" lang="ur">ضرورت کے مطابق accepted terms استعمال ہو سکتے ہیں، مگر اردو explanation دیں اور code-switching کو حد سے نہ بڑھائیں۔</p>
<h3 dir="rtl" lang="ur">الفاظ کیسے گنیں؟</h3>
<p dir="rtl" lang="ur">Practice میں exact count کریں۔ Exam میں average words per line کا اندازہ استعمال کریں، جب تک specific instruction مختلف نہ ہو۔</p>
<h3 dir="rtl" lang="ur">کیا 400–500 الفاظ ہر FBISE Urdu essay کے لیے ہیں؟</h3>
<p dir="rtl" lang="ur">نہیں۔ یہ current HSSC-II model paper کی مثال ہے۔ SSC، HSSC-I، دوسرے سال یا revised paper میں requirement مختلف ہو سکتی ہے۔ Latest official model paper دیکھنا ضروری ہے۔</p>
<h3 dir="rtl" lang="ur">کیا memorised essay فائدہ مند ہے؟</h3>
<p dir="rtl" lang="ur">Model essays سے vocabulary اور structure سیکھیں، مگر full memorisation unseen topic پر mismatch پیدا کر سکتی ہے۔ Plans اور evidence banks زیادہ transferable ہیں۔</p>
<h3 dir="rtl" lang="ur">اگر topic پر data نہ ہو؟</h3>
<p>Definition، causes، effects، examples and solutions کے ذریعے reasoned essay لکھیں۔ uncertain statistics invent نہ کریں۔</p>
<h2 dir="rtl" lang="ur">آخری بات</h2>
<p dir="rtl" lang="ur">مضمون نویسی کا اعتماد ideas کی تعداد سے نہیں، ideas پر control سے آتا ہے۔ دو منٹ رک کر عنوان کا دائرہ، مرکزی موقف اور paragraph order طے کرنے والا طالب علم عموماً اس طالب علم سے بہتر لکھتا ہے جو پہلے جملے سے ہی پوری رفتار میں آ جائے۔</p>
<p dir="rtl" lang="ur"><strong>موضوع واضح کریں، دلائل ترتیب دیں، مثال سے مضبوط کریں، اور نتیجہ discussion سے نکالیں۔</strong> Structure first کا مطلب creativity ختم کرنا نہیں؛ structure وہ راستہ ہے جس پر creativity قاری تک پہنچتی ہے۔</p>
<h2>Source and accuracy note</h2>
<p dir="rtl" lang="ur">یہ guide FBISE کے current curriculum/model-paper portal، Urdu assessment frameworks اور question-paper setter training material کی روشنی میں تیار کی گئی ہے۔ Current HSSC-II model paper میں essay task کے لیے 400–500 words اور 12 marks دکھائے گئے ہیں، مگر paper pattern class اور year کے مطابق بدل سکتا ہے۔ اس لیے اپنے subject کا latest official framework لازماً verify کریں۔ اس article کے outlines، paragraphs اور model essay original ہیں۔</p>
<h3>References</h3>
<ul><li>Federal Board of Intermediate and Secondary Education, Curriculum and Model Question Papers: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, Urdu Compulsory HSSC-II Assessment Framework and Model Question Paper, current portal edition: https://www.fbise.edu.pk/ModelPaper/2025/Assessment%20Frameworks/HSSC-II/Final%20Assessment%20Framework%20%2B%20Model%20Question%20Paper%20Urdu%20HSSC-II.pdf</li><li>FBISE, Question Paper Setter/Item Developer Training Manual, official assessment guidance.</li><li>National Curriculum of Pakistan, Urdu curriculum documents linked through the official FBISE portal.</li></ul>', 'published', '2026-06-29 09:00:00'),
('Understanding the FBISE Marking Scheme', 'understanding-fbise-marking-scheme', 'Board Updates', 'What the marking scheme rewards, where students lose easy marks, and how to align your answers.', '<h2>The marking scheme is not a secret code</h2>
<p>Students often imagine the examiner holding an invisible ideal answer and subtracting marks whenever their wording differs from it. That belief encourages memorisation, unnecessary length and fear of original expression. A student may know the concept but still reproduce a guidebook page because it feels safer. Another may write everything remembered in the hope that the correct point is somewhere inside the answer.</p>
<p>FBISE’s official assessment material presents a more structured picture. Current frameworks are built around Student Learning Outcomes, commonly called SLOs. The paper is designed to sample what students should know, understand and apply, rather than merely repeat one textbook paragraph. Official training material for paper setters also discusses cognitive balance, difficulty balance, table of specifications, marking schemes and rubrics. In other words, a paper is supposed to align questions, expected performance and marks.</p>
<p>That does not mean every answer has only one acceptable sentence. The official item-development manual explains that marking guidance should recognise relevant alternative answers, award positive marks for demonstrated knowledge, and use criteria or descriptors where extended responses require judgement. At the same time, the answer must still satisfy the exact task. “Many answers may be acceptable” is not the same as “anything written receives marks.”</p>
<p>The practical lesson is powerful:</p>
<p class="ex">Marks are attached to evidence of the requested learning, not to the amount of ink on the page.</p>
<p>This guide explains how to read an FBISE question through the lens of assessment: the SLO, command word, mark value, cognitive demand and expected answer evidence.</p>
<h2>First distinction: curriculum, framework, model paper and marking scheme</h2>
<p>Students sometimes use these terms as though they mean the same thing.</p>
<h3>Curriculum</h3>
<p>The curriculum describes learning goals, content areas, competencies and outcomes expected over a course. It is broader than one examination paper.</p>
<h3>Assessment framework</h3>
<p>The framework translates curriculum outcomes into an examination plan. It may show the SLOs to be assessed, cognitive categories, content distribution, item type and paper structure.</p>
<h3>Table of specifications or alignment chart</h3>
<p>This maps questions or marks against content and cognitive demand. It helps paper developers avoid testing only memory or only one chapter.</p>
<h3>Model question paper</h3>
<p>The model paper demonstrates the expected pattern, style, sectioning and types of questions. The official training manual describes a model paper as setting the tone and pattern to be followed in actual examinations. It is therefore an essential preparation document, but it is not a list of exact questions that must repeat.</p>
<h3>Marking scheme or key</h3>
<p>The marking scheme guides examiners on acceptable answers, mark allocation and treatment of alternatives. For objective items, it may be a direct key. For short and extended responses, it can identify required content points, criteria or levels.</p>
<h3>Rubric</h3>
<p>A rubric describes the criteria and standards used to judge performance, especially for writing or open-ended responses. The official manual identifies components such as criteria, standards and descriptors. A rubric helps distinguish, for example, a relevant but weakly organised paragraph from a focused, coherent and accurate one.</p>
<p>Knowing the difference stops two common mistakes: treating a model answer as the only possible wording and treating the broad curriculum as though every point must appear in every paper.</p>
<h2>What SLO-based assessment actually means</h2>
<p>An SLO states what a learner should be able to know or do. Examples in different subjects might involve identifying a concept, explaining a relationship, applying a rule, analysing information or producing a structured response.</p>
<p>A textbook is one route for learning those outcomes. An SLO-based paper can use unfamiliar wording, a new passage, changed numbers, a fresh scenario or a different example while testing the same skill.</p>
<p>Consider English comprehension. A student may never have seen the passage before, but the SLOs—finding main ideas, interpreting vocabulary, making inferences or summarising—have been practised. In science, a student may understand a principle from one diagram but be asked to apply it to another. In mathematics, the values change while the concept remains.</p>
<h3>What SLO-based does not mean</h3>
<p>It does not mean:</p>
<ul><li>the textbook is useless;</li><li>questions can come from anywhere without relation to the curriculum;</li><li>factual knowledge is unimportant;</li><li>memorisation is never needed;</li><li>every answer is purely subjective.</li></ul>
<p>Knowledge supplies the material for understanding and application. The change is that the student must be able to use the knowledge rather than recognise only one memorised form.</p>
<h3>A practical SLO question</h3>
<p>Before revising a topic, ask:</p>
<p class="ex">What should I be able to do with this information?</p>
<p>For tenses:</p>
<ul><li>identify the time relationship;</li><li>select the correct form in context;</li><li>correct an error;</li><li>use tense consistently in writing.</li></ul>
<p>For poetry:</p>
<ul><li>paraphrase lines;</li><li>identify a device;</li><li>explain its effect;</li><li>infer tone or theme from evidence.</li></ul>
<p>For biology:</p>
<ul><li>label a structure;</li><li>explain function;</li><li>compare processes;</li><li>predict an outcome when a condition changes.</li></ul>
<p>This turns revision from page coverage into performance practice.</p>
<h2>Cognitive levels: knowledge, understanding and application</h2>
<p>FBISE’s official paper-setter training material gives a broad target of approximately <strong>30 percent knowledge, 50 percent understanding and 20 percent application</strong>, with a permitted variation. Current subject frameworks also display cognitive categorisation in their own tables. These proportions are a design guide for the paper, not a promise that every chapter or every individual question has the same split.</p>
<h3>Knowledge</h3>
<p>Knowledge questions ask students to recall, identify, name, define, list or recognise learned information.</p>
<p>Examples:</p>
<ul><li>Define osmosis.</li><li>Name the figure of speech.</li><li>State the formula.</li><li>Identify the author.</li></ul>
<p>Knowledge is not “easy” in every case; specialised facts can be difficult. But the mental operation is mainly retrieval.</p>
<h3>Understanding</h3>
<p>Understanding questions ask students to explain, describe, distinguish, summarise, interpret, classify or show relationships.</p>
<p>Examples:</p>
<ul><li>Explain why water moves across the membrane.</li><li>Distinguish a simile from a metaphor using the extract.</li><li>Summarise the passage in your own words.</li><li>Describe how the character’s attitude changes.</li></ul>
<p>These questions punish memorised fragments that are not connected logically.</p>
<h3>Application</h3>
<p>Application questions ask students to use knowledge or a method in a new context.</p>
<p>Examples:</p>
<ul><li>Predict what happens when concentration changes.</li><li>Correct tense errors in an unseen paragraph.</li><li>Apply a formula to unfamiliar data.</li><li>Write a paragraph on an unseen topic using appropriate organisation.</li></ul>
<p>Application does not always mean a long answer. A one-mark MCQ can test application if the student must reason through a new situation.</p>
<h3>Why students misjudge cognitive demand</h3>
<p>A familiar command word can hide a higher-level task. “Identify” may be simple recall when naming a labelled organ, but identifying the best explanation in a new scenario can require application. Similarly, “explain” may require a two-step causal chain rather than a definition.</p>
<p>Read the entire stem, not the verb alone.</p>
<h2>Difficulty level is different from cognitive level</h2>
<p>The official training manual also describes a broad difficulty balance of approximately <strong>40 percent easy, 40 percent moderate and 20 percent difficult</strong> for paper construction. Difficulty and cognition are related but not identical.</p>
<p>A knowledge question can be difficult because the fact is obscure. An application question can be easy because the context is straightforward. Difficulty depends on factors such as:</p>
<ul><li>familiarity of context;</li><li>number of reasoning steps;</li><li>complexity of language;</li><li>closeness of distractors;</li><li>amount of information to process;</li><li>integration of multiple concepts;</li><li>time required.</li></ul>
<p>Students should therefore avoid saying, “Application questions are the last difficult 20 percent.” The percentages describe different dimensions.</p>
<h2>The mark value is an instruction</h2>
<p>Marks tell you the likely amount of evidence needed. A one-mark question and a five-mark question may use the same topic but demand different depth.</p>
<h3>One mark</h3>
<p>Usually one precise element: a term, fact, label, selection or simple result.</p>
<p>Do not write half a page unless needed to make the answer unambiguous.</p>
<h3>Two marks</h3>
<p>Often two points, a point plus reason, or two linked steps.</p>
<p>Example:</p>
<p class="ex">Give two reasons…</p>
<p>Write two distinct reasons. Rephrasing the same reason does not create a second mark.</p>
<h3>Three to four marks</h3>
<p>Often requires a developed explanation, multiple elements, comparison points, evidence plus analysis, or a short sequence.</p>
<h3>Higher-mark extended response</h3>
<p>Requires selection, organisation and sufficient development. Content alone may not be enough if communication, reasoning or structure forms part of the task.</p>
<h3>The mark-to-point estimate</h3>
<p>A common practical rule is to look for roughly one assessable point per mark, but this is not universal. One developed causal explanation may earn more than one mark; a writing rubric may award marks across content, organisation and language rather than isolated bullets. Use the current marking guidance and model solution where available.</p>
<p>The safe question is:</p>
<p class="ex">What distinct evidence would allow an examiner to justify every mark?</p>
<h2>Command words: translate them into actions</h2>
<p>Many lost marks come from answering the topic but not the command.</p>
<h3>Define</h3>
<p>Give the precise meaning. Avoid examples unless they clarify and time allows.</p>
<h3>State/Name/Identify</h3>
<p>Provide the required fact, term or feature directly.</p>
<h3>List</h3>
<p>Give separate items. Extended explanation may not be required.</p>
<h3>Describe</h3>
<p>Give characteristics, sequence or what happens. Use relevant detail.</p>
<h3>Explain</h3>
<p>Show how or why. Include relationships, causes, mechanisms or reasons.</p>
<p>A useful explanation frame:</p>
<p class="ex">This happens because ___, which causes/leads to ___ .</p>
<h3>Compare</h3>
<p>Give similarities and/or differences according to wording. Organise by the same criteria.</p>
<h3>Contrast/Distinguish</h3>
<p>Emphasise differences. Pair corresponding features rather than writing two unrelated mini-essays.</p>
<h3>Analyse</h3>
<p>Break information into parts and explain relationships, patterns or effects.</p>
<h3>Evaluate</h3>
<p>Make a judgement using criteria and evidence. A balanced evaluation often recognises strengths, limitations and conditions.</p>
<h3>Justify</h3>
<p>Give reasons or evidence supporting a choice or conclusion.</p>
<h3>Discuss</h3>
<p>Develop relevant aspects, often including more than one perspective, before reaching a reasoned conclusion.</p>
<h3>Summarise</h3>
<p>Select central ideas and express them concisely without unnecessary examples or personal opinion.</p>
<h3>Paraphrase</h3>
<p>Restate meaning in clear language while preserving the original idea.</p>
<h3>Calculate</h3>
<p>Show the appropriate process, units and final result according to subject requirements.</p>
<h3>Predict</h3>
<p>State a likely outcome based on supplied principles or evidence, not a random guess.</p>
<h3>Suggest</h3>
<p>Offer a plausible answer grounded in the context; more than one response may be acceptable.</p>
<h2>Topic knowledge versus answer evidence</h2>
<p>Suppose a question asks:</p>
<p class="ex">Explain two effects of deforestation on local communities. [4]</p>
<p>A student writes:</p>
<p class="ex">Deforestation is very dangerous. Trees are important for life. We should plant trees and stop cutting forests. Forests are the beauty of nature.</p>
<p>The paragraph is related to the topic, but it may not earn the expected marks because it does not clearly identify and explain two effects on local communities.</p>
<p>A stronger response:</p>
<p class="ex">First, removing trees increases soil erosion, so fertile land can be lost and farming income may decline. Second, reduced tree cover can disturb local water retention and increase runoff, making communities more vulnerable to floods or seasonal water shortage.</p>
<p>The answer contains two effects and explains the mechanism linking each to people.</p>
<p>The examiner marks what is visible, not what the student intended.</p>
<h2>Positive marking and consequential marks</h2>
<p>The official paper-setter manual discusses positive marking: examiners should award marks for correct knowledge demonstrated rather than approach answers as opportunities to punish. It also refers to consequential marking in suitable structured problems, where a later step may be credited if it correctly follows from an earlier mistaken value, depending on the scheme.</p>
<h3>What this means for students</h3>
<p>Show your working in subjects where method matters. A wrong final number with a valid process may still demonstrate assessable skill. Conversely, writing only the final answer can hide the method and remove the possibility of process credit.</p>
<h3>What it does not mean</h3>
<p>Positive marking does not guarantee marks for irrelevant writing. Consequential marking does not make every later answer correct. It operates only where the official scheme allows the error to carry forward without destroying the tested reasoning.</p>
<p>Never deliberately leave a known error because you expect follow-through marks. Correct it when you notice it.</p>
<h2>Alternative answers and examiner flexibility</h2>
<p>Open-ended questions can have multiple valid responses. A comprehension inference may be expressed in different words; a literature interpretation may be acceptable if supported; a science explanation may use an alternative correct route.</p>
<p>The official training material advises that marking schemes should recognise acceptable alternatives and not be rigid where students demonstrate valid knowledge beyond the anticipated response.</p>
<p>Students should take confidence from this, but remain disciplined:</p>
<ul><li>alternative does not mean unrelated;</li><li>opinion must be supported when evidence is required;</li><li>terminology must be accurate where the subject demands it;</li><li>a valid method must actually reach or support the conclusion;</li><li>expressive wording cannot replace missing content.</li></ul>
<h2>How rubrics work in writing tasks</h2>
<p>A rubric judges several dimensions. Depending on the task and official scheme, these may include:</p>
<ul><li>relevance and content;</li><li>organisation and coherence;</li><li>development and evidence;</li><li>vocabulary and register;</li><li>grammar and sentence control;</li><li>spelling and punctuation;</li><li>fulfilment of purpose and format.</li></ul>
<p>A paragraph with excellent grammar but no clear answer may lose content marks. A response with strong ideas but broken structure may lose organisation or communication marks. This explains why “I wrote a lot of correct facts” does not necessarily produce full marks in writing.</p>
<h3>Criteria, standards and descriptors</h3>
<p>The official training manual describes core rubric components:</p>
<ul><li><strong>criteria:</strong> what aspect is judged;</li><li><strong>standards/levels:</strong> the quality bands;</li><li><strong>descriptors:</strong> what performance at each level looks like.</li></ul>
<p>A simplified practice rubric for an English paragraph might look like this:</p>
<div class="atable-wrap"><table class="atable"><thead><tr><th><strong>Criterion</strong></th><th><strong>Strong</strong></th><th><strong>Developing</strong></th><th><strong>Weak</strong></th></tr></thead><tbody><tr><td>Focus</td><td>Clear controlling idea throughout</td><td>Main idea present but drifts</td><td>No clear central point</td></tr><tr><td>Development</td><td>Relevant reasons/examples explained</td><td>Some support but uneven</td><td>Statements listed without support</td></tr><tr><td>Coherence</td><td>Logical order and natural links</td><td>Some abrupt movement</td><td>Sentences disconnected</td></tr><tr><td>Language</td><td>Accurate, varied and appropriate</td><td>Meaning clear with errors</td><td>Errors frequently block meaning</td></tr></tbody></table></div>
<p>This is a diagnostic example, not an official FBISE rubric. Its value is showing why improvement must target a criterion, not simply “write better.”</p>
<h2>Exact wording versus equivalent meaning</h2>
<p>For objective questions, exact selection matters. For definitions and technical terms, precision matters. For explanatory answers, equivalent wording may be acceptable when the concept remains correct.</p>
<p>Example:</p>
<p class="ex">Photosynthesis converts light energy into chemical energy stored in glucose.</p>
<p>A student may express the same relationship with different sentence structure. However, saying “plants turn sunlight into oxygen” is not an equivalent paraphrase; it changes the scientific idea.</p>
<p>In language subjects, paraphrase must preserve the original meaning. In mathematics, an alternative method must obey the relevant rules. In social sciences, an argument must still use accurate facts.</p>
<p>Do not confuse freedom of expression with freedom from accuracy.</p>
<h2>The role of model papers</h2>
<p>Model papers are among the most valuable official resources because they show:</p>
<ul><li>section structure;</li><li>item types;</li><li>approximate mark distribution;</li><li>expected response length where specified;</li><li>style of unseen material;</li><li>use of choice;</li><li>cognitive demand;</li><li>answer format.</li></ul>
<p>The official manual states that a model paper sets the tone and pattern for actual exams. Therefore, students should analyse it, not merely solve it once.</p>
<h3>A five-layer model-paper analysis</h3>
<p>Layer 1: Structure</p>
<p>How many sections? Which are compulsory? Where is choice provided?</p>
<p>Layer 2: Marks</p>
<p>Which tasks carry the most marks? Which skills deserve the most revision time?</p>
<p>Layer 3: Commands</p>
<p>List verbs: explain, identify, compare, write, summarise, justify.</p>
<p>Layer 4: SLO transfer</p>
<p>Which questions use familiar knowledge in a new passage or scenario?</p>
<p>Layer 5: Answer evidence</p>
<p>For each question, write what an examiner would need to see.</p>
<p>Do this before attempting the paper. Then solve it under time conditions.</p>
<h2>The role of model solutions</h2>
<p>A model solution demonstrates one acceptable route and expected depth. Use it to compare:</p>
<ul><li>missing content points;</li><li>level of explanation;</li><li>answer length;</li><li>organisation;</li><li>terminology;</li><li>calculation steps.</li></ul>
<p>Do not memorise its wording blindly. Ask why each sentence is present and which mark it supports.</p>
<h3>Convert a model answer into a mark map</h3>
<p>Model answer:</p>
<p class="ex">The writer’s tone is concerned because the passage describes water loss as increasing, but it becomes hopeful when community repairs reduce waste.</p>
<p>Mark map might be:</p>
<ul><li>identifies tone/shift;</li><li>evidence for concern;</li><li>evidence for hope;</li><li>explanation of change.</li></ul>
<p>Now you can reproduce the skill on a new passage.</p>
<h2>Where students lose “easy marks”</h2>
<h3>1. Ignoring part of a multi-part question</h3>
<p class="ex">Identify the device <strong>and explain its effect</strong>.</p>
<p>The student names the device but gives no effect.</p>
<p>Repair: Underline every task word and tick each after answering.</p>
<h3>2. Giving one point twice</h3>
<p class="ex">Pollution harms health. It is bad for people’s health.</p>
<p>This is one idea repeated, not two effects.</p>
<h3>3. Missing units</h3>
<p>A correct number without the required unit may be incomplete.</p>
<h3>4. Not showing steps</h3>
<p>In method-mark subjects, the examiner cannot credit invisible reasoning.</p>
<h3>5. Writing outside the answer space or wrong question number</h3>
<p>Clear numbering protects the link between response and item.</p>
<h3>6. Choosing more options than allowed</h3>
<p>When instructions say attempt one, writing both may create marking complications and waste time. Follow the current paper’s instructions exactly.</p>
<h3>7. Overwriting a low-mark question</h3>
<p>Time spent on a one-mark definition can remove time from an eight-mark response.</p>
<h3>8. Using memorised material that does not fit</h3>
<p>Related facts are not automatically relevant facts.</p>
<h3>9. Poor handwriting or layout</h3>
<p>Examiners can only mark what they can read. Readability, spacing and clear diagrams matter.</p>
<h3>10. Leaving correction unclear</h3>
<p>Cross out cleanly and write the final answer visibly. Do not create two competing answers.</p>
<h3>11. Failing to label diagrams or axes</h3>
<p>A correct drawing may still be incomplete without required labels, scale or units.</p>
<h3>12. Giving an example instead of a definition</h3>
<p>An example may illustrate but not define the concept.</p>
<h3>13. Giving a definition when explanation is required</h3>
<p>“Evaporation is…” does not necessarily explain why evaporation increases under specific conditions.</p>
<h3>14. Unsupported literature claims</h3>
<p>“Tone is sad” without evidence is weaker than a claim linked to wording.</p>
<h3>15. Summary containing personal opinion</h3>
<p>The task rewards selection of passage ideas, not commentary, unless asked.</p>
<h2>Section patterns and subject variation</h2>
<p>The item-developer training manual presents a broad three-section pattern—objective, short-response and extended-response—with a general distribution often represented as 20 percent, 50 percent and 30 percent. Subject frameworks may implement their own structure and marks, and current model papers should be treated as the direct source for the exact exam.</p>
<p>Do not assume that every FBISE paper, subject or year has identical sections. Practical subjects, language papers and revised curricula may differ. The broad policy explains design philosophy; the subject framework explains your paper.</p>
<h3>Best source hierarchy</h3>
<p>For exam-specific preparation, use:</p>
<ol><li>current official notification, if any;</li><li>current official subject assessment framework;</li><li>current official model paper and solution;</li><li>curriculum/SLO document;</li><li>older papers for additional practice, with caution;</li><li>teacher notes and commercial guides as support, not authority.</li></ol>
<p>An old paper can still provide valuable practice but may not reflect the latest word limits, choices or assessment structure.</p>
<h2>The Table of Specifications: what students can learn from it</h2>
<p>Where an assessment framework includes an alignment table, it may connect:</p>
<ul><li>SLO codes;</li><li>content domain;</li><li>cognitive level;</li><li>item number;</li><li>mark allocation.</li></ul>
<p>Students do not need to become assessment specialists, but the table answers useful questions:</p>
<ul><li>Which outcomes can appear as objective items?</li><li>Which require explanation or application?</li><li>How much weight is given to each area?</li><li>Are some skills integrated across sections?</li></ul>
<h3>Turn the table into a revision tracker</h3>
<p>Create columns:</p>
<div class="atable-wrap"><table class="atable"><thead><tr><th><strong>SLO/skill</strong></th><th><strong>I can recall</strong></th><th><strong>I can explain</strong></th><th><strong>I can apply</strong></th><th><strong>Evidence/practice</strong></th></tr></thead><tbody><tr><td>Identify imagery</td><td>Yes</td><td>Yes</td><td>Needs work</td><td>3 unseen extracts</td></tr><tr><td>Summarise passage</td><td>Yes</td><td>Partial</td><td>Needs work</td><td>timed summary</td></tr><tr><td>Use tenses in context</td><td>Yes</td><td>Yes</td><td>Partial</td><td>editing paragraph</td></tr></tbody></table></div>
<p>This is more accurate than ticking “chapter completed.”</p>
<h2>Designing answers backwards from marks</h2>
<p>Use a four-step routine in the exam.</p>
<h3>Step 1: Read the stem</h3>
<p>Identify topic, context and restrictions.</p>
<h3>Step 2: Circle command and quantity</h3>
<p>“Explain <strong>two</strong> reasons,” “compare,” “with evidence,” “in 100–120 words.”</p>
<h3>Step 3: Translate marks into evidence</h3>
<p>Plan the number of distinct points, steps or rubric dimensions.</p>
<h3>Step 4: Write in examiner-visible form</h3>
<p>Use clear sentences, logical order, labels or working as appropriate.</p>
<p>Example question:</p>
<p class="ex">Explain two ways the writer creates urgency in the final paragraph. [4]</p>
<p>Plan:</p>
<ul><li>Technique/evidence 1 + effect.</li><li>Technique/evidence 2 + effect.</li></ul>
<p>Answer:</p>
<p class="ex">First, the writer uses the command “act before the next season,” directly pressuring readers to respond immediately. Second, the short final sentence, “There is no spare river,” creates a firm ending and stresses that the resource cannot be replaced.</p>
<p>The structure makes four potential elements visible.</p>
<h2>How different subjects display evidence</h2>
<h3>English and Urdu writing</h3>
<p>Evidence appears through relevance, structure, development, accurate language and fulfilment of form.</p>
<h3>Literature</h3>
<p>Claim + textual evidence + analysis.</p>
<h3>Science</h3>
<p>Correct concept + mechanism + terminology + diagram/calculation where required.</p>
<h3>Mathematics</h3>
<p>Method, substitutions, transformations, units and final result.</p>
<h3>Pakistan Studies/Social Sciences</h3>
<p>Accurate facts, causal relationships, comparison, significance and structured judgement.</p>
<h3>Computer Science</h3>
<p>Correct logic, syntax/pseudocode, explanation of process and output.</p>
<p>The underlying principle is the same: make the assessed thinking visible.</p>
<h2>Attempt strategy under time pressure</h2>
<h3>Read instructions before questions</h3>
<p>Check compulsory items, choice, calculator status, response booklet rules and word limits.</p>
<h3>Use mark-proportional time</h3>
<p>Estimate total writing time after reading and final check. Divide by marks to obtain a rough minutes-per-mark guide. Adjust for reading-heavy sections, but do not let a low-mark item consume disproportionate time.</p>
<h3>Start with confidence, not avoidance</h3>
<p>Beginning with a manageable section can settle nerves, but do not postpone the highest-mark or reading-heavy task until there is too little time.</p>
<h3>Leave visible space when returning</h3>
<p>If stuck, mark the item and move on. Leave enough space or follow booklet rules so the later response is clear.</p>
<h3>Reserve checking time</h3>
<p>Check:</p>
<ul><li>unanswered subparts;</li><li>question numbers;</li><li>units and signs;</li><li>selected options;</li><li>word-limit compliance;</li><li>grammar in extended answers;</li><li>diagrams and labels.</li></ul>
<h2>Self-marking without fooling yourself</h2>
<p>Students often read their answer, recognise what they intended and award themselves marks that are not actually visible.</p>
<p>Use a coloured method:</p>
<ul><li>underline each distinct content point;</li><li>box evidence or calculation steps;</li><li>circle command fulfilment;</li><li>mark repetition with R;</li><li>mark unsupported claim with ?.</li></ul>
<p>Then compare with official model solution or teacher guidance.</p>
<h3>Blind delay</h3>
<p>Review the answer several hours or a day later. Distance makes missing logic easier to see.</p>
<h3>Explain the mark aloud</h3>
<p>For every mark you award yourself, complete:</p>
<p class="ex">This deserves a mark because the answer explicitly shows __________.</p>
<p>If you cannot complete the sentence, the evidence may be missing.</p>
<h2>A worked marking analysis: paragraph writing</h2>
<p>Suppose the task asks for 100–120 words on “The Role of Technology in Education.”</p>
<h3>Response A</h3>
<p class="ex">Technology is very important. It has many benefits. Students use mobile phones and computers. Online education is also useful. Technology saves time. It has some disadvantages too. Students waste time. We should use it properly. Technology is the need of the modern age.</p>
<p>Possible weaknesses:</p>
<ul><li>within topic but underdeveloped;</li><li>repetitive general statements;</li><li>limited organisation;</li><li>no specific example or relationship;</li><li>simplistic conclusion.</li></ul>
<h3>Response B</h3>
<p class="ex">Technology improves education when it expands access rather than merely adding screens to a classroom. Recorded lessons allow students to revisit difficult concepts, while digital libraries provide material beyond a single textbook. Teachers can also use quick quizzes to identify misunderstandings before an examination. However, unreliable online sources and constant notifications can weaken learning if students lack guidance. Schools should therefore combine technology with source-checking skills, time limits and active discussion. Used with a clear purpose, digital tools do not replace teachers; they give teachers and learners more flexible ways to explain, practise and review knowledge.</p>
<p>Why B is stronger:</p>
<ul><li>clear controlling idea;</li><li>developed examples;</li><li>balanced limitation;</li><li>logical solution;</li><li>purposeful conclusion;</li><li>varied and controlled language.</li></ul>
<p>The difference is not simply “better vocabulary.” It is visible performance across likely rubric criteria.</p>
<h2>A worked marking analysis: explanation question</h2>
<p>Question:</p>
<p class="ex">Explain why a metal spoon feels colder than a wooden spoon in the same room. [3]</p>
<p>Weak:</p>
<p class="ex">Metal is colder than wood.</p>
<p>This repeats the observation and may be scientifically misleading because both can be at the same room temperature.</p>
<p>Strong:</p>
<p class="ex">Both spoons may be at the same temperature, but metal conducts thermal energy away from the hand faster than wood. The hand therefore loses heat more rapidly to the metal, making it feel colder.</p>
<p>Potential evidence:</p>
<ul><li>same temperature recognised;</li><li>conductivity difference;</li><li>heat-transfer consequence linked to sensation.</li></ul>
<p>The answer explains the mechanism.</p>
<h2>A worked marking analysis: evaluation question</h2>
<p>Question:</p>
<p class="ex">Evaluate whether the school’s early library programme was successful. [4]</p>
<p>Weak:</p>
<p class="ex">Yes, it was successful because libraries are good.</p>
<p>Strong:</p>
<p class="ex">The programme was successful in increasing attendance and helping students arrive in class with more focused questions. However, the early schedule initially excluded students with transport difficulties. Its success was therefore real but incomplete until the school introduced an afternoon session.</p>
<p>The stronger response uses criteria, evidence, limitation and judgement.</p>
<h2>Why “write more” is poor advice</h2>
<p>Extra writing can help only when it adds relevant evidence. Beyond that point, it creates risks:</p>
<ul><li>contradiction;</li><li>repetition;</li><li>factual error;</li><li>loss of focus;</li><li>time shortage;</li><li>examiner difficulty locating the answer.</li></ul>
<p>A concise four-mark answer can earn full marks if it visibly contains the required elements. A two-page answer can lose marks if it avoids the command.</p>
<p>The goal is <strong>sufficient development</strong>, not maximum length.</p>
<h2>Why presentation matters without becoming decoration</h2>
<p>Good presentation helps the examiner access the answer:</p>
<ul><li>correct numbering;</li><li>readable handwriting;</li><li>clear paragraphing;</li><li>labelled diagrams;</li><li>aligned calculations;</li><li>sensible spacing;</li><li>clean corrections.</li></ul>
<p>Decorative headings, multiple colours or elaborate borders do not replace content. Follow examination rules on ink and stationery.</p>
<h2>Common myths about FBISE marking</h2>
<h3>Myth 1: Examiners only accept book wording</h3>
<p>Reality: exact terminology may matter, but official guidance allows relevant alternative responses in open-ended questions. Meaning and evidence are central.</p>
<h3>Myth 2: Longer answers always score higher</h3>
<p>Reality: marks depend on required evidence and rubric criteria.</p>
<h3>Myth 3: SLO-based means textbooks do not matter</h3>
<p>Reality: textbooks teach content and examples; the exam can transfer those outcomes to unfamiliar contexts.</p>
<h3>Myth 4: Difficult questions are always application questions</h3>
<p>Reality: difficulty and cognitive category are different dimensions.</p>
<h3>Myth 5: Every subject follows one fixed 20/50/30 paper pattern</h3>
<p>Reality: the training manual offers broad design guidance, while subject frameworks and model papers give exact current structures.</p>
<h3>Myth 6: Grammar does not matter outside English</h3>
<p>Reality: in all subjects, unclear language can hide reasoning. Technical correctness remains the main criterion, but communication affects whether that correctness is visible.</p>
<h3>Myth 7: One memorised answer can fit every related topic</h3>
<p>Reality: command words and context change the required evidence.</p>
<h3>Myth 8: The model paper predicts exact questions</h3>
<p>Reality: it demonstrates pattern and demand, not guaranteed repetition.</p>
<h2>A four-week assessment-aligned revision system</h2>
<h3>Week 1: Map outcomes</h3>
<p>Collect the official curriculum/framework/model paper. List major skills and topics. Mark recall, understanding and application confidence.</p>
<h3>Week 2: Practise by command</h3>
<p>Create sets of define, explain, compare, apply and evaluate questions. Learn how answer structure changes with the verb.</p>
<h3>Week 3: Timed model-paper work</h3>
<p>Attempt sections under realistic time. Record whether marks were lost through content, command, time or presentation.</p>
<h3>Week 4: Error-based revision</h3>
<p>Revise only the weak SLOs and repeated error patterns, then attempt a second unseen paper.</p>
<h3>Error categories</h3>
<ul><li>K: knowledge gap</li><li>U: explanation/understanding gap</li><li>A: application gap</li><li>C: command misread</li><li>T: time management</li><li>P: presentation</li><li>L: language/clarity</li></ul>
<p>A score alone tells you how much was lost. Error categories tell you what to repair.</p>
<h2>A daily ten-minute marking drill</h2>
<ol><li>Take one question.</li><li>Underline command, quantity and context.</li><li>Predict the mark points or rubric criteria.</li><li>Write a brief answer.</li><li>Compare with an official solution or reliable teacher explanation.</li><li>Rewrite only the missing part.</li></ol>
<p>This develops examiner awareness without requiring a full paper every day.</p>
<h2>How parents and teachers can give better feedback</h2>
<p>Avoid comments such as “poor,” “learn more,” or “make it long.” Use criterion-based feedback:</p>
<ul><li>“You gave the effect but not the cause.”</li><li>“Two examples repeat the same point.”</li><li>“The interpretation is plausible, but add textual evidence.”</li><li>“Your method is correct; the unit is missing.”</li><li>“The paragraph has relevant ideas but no controlling sentence.”</li><li>“You answered advantages but ignored disadvantages.”</li></ul>
<p>Feedback should identify the next action, not merely the weakness.</p>
<h2>Before-the-exam official-resource checklist</h2>
<p>Confirm from the official FBISE website:</p>
<ul><li>current assessment framework for class and subject;</li><li>current model paper;</li><li>model solution or marking guidance where provided;</li><li>any notification changing pattern, syllabus or practical requirements;</li><li>permitted choices and word limits;</li><li>curriculum/SLO document.</li></ul>
<p>Download or save the documents before the final revision period. Do not rely on a cropped social-media screenshot when the official file is available.</p>
<h2>Exam-day answer checklist</h2>
<p>For every question:</p>
<ul><li>What is the command?</li><li>How many parts or points?</li><li>What is the mark value?</li><li>Is the context familiar or new?</li><li>What evidence must be visible?</li><li>Is working required?</li><li>Have I answered, not merely discussed the topic?</li></ul>
<p>Before submission:</p>
<ul><li>All compulsory questions attempted?</li><li>Correct question numbers?</li><li>No accidental extra choices?</li><li>Units, labels and signs checked?</li><li>Extended answers within required range?</li><li>Clear corrections?</li><li>Final pages checked?</li></ul>
<h2>Frequently asked questions</h2>
<h3>Does the examiner deduct marks for every grammar error?</h3>
<p>It depends on the subject, task and marking scheme. In a language-writing rubric, grammar may be a direct criterion. In a science answer, an error matters most when it changes or obscures the scientific meaning. Write clearly in every subject.</p>
<h3>Can an answer different from the model solution receive marks?</h3>
<p>Yes, when it is correct, relevant and satisfies the question. Official marking guidance recognises acceptable alternatives, especially in open responses.</p>
<h3>How many points should I write for a four-mark question?</h3>
<p>Read the command. It may require four brief items, two explained points, a multi-step method or rubric-based development. Do not apply one mechanical rule to every subject.</p>
<h3>Are cognitive percentages exact in every paper?</h3>
<p>They are broad design targets and may include permitted variation. Subject-specific frameworks should be checked for the current distribution.</p>
<h3>Is the 40/40/20 difficulty balance guaranteed in my exact paper?</h3>
<p>It is official item-development guidance, not a way for students to identify exact questions in advance. Perceived difficulty also differs between students.</p>
<h3>Should I attempt the hardest question first?</h3>
<p>Attempt strategically. Protect high-mark tasks and time limits. Beginning with a confident response can help, but do not leave major sections until the end.</p>
<h3>Is a model paper enough for preparation?</h3>
<p>No. It reveals pattern and demand. Use curriculum outcomes, textbook learning, additional unseen practice and correction of errors.</p>
<h3>Why did I lose marks when my answer was factually related?</h3>
<p>The answer may not have followed the command, provided the required number of points, explained relationships or applied knowledge to the given context.</p>
<h3>Do headings earn marks?</h3>
<p>Headings can improve organisation when appropriate, but marks come from content and required performance. Follow the genre and instructions.</p>
<h3>How can I know the latest marking scheme?</h3>
<p>Use the official FBISE curriculum/model-paper portal and current subject documents. Avoid assuming that an older paper’s pattern remains unchanged.</p>
<h2>The deeper lesson</h2>
<p>A marking scheme rewards what an answer proves. The student’s task is therefore not to display everything known about a chapter. It is to make the requested knowledge, understanding or application visible in the clearest possible form.</p>
<p>Read the command. Respect the marks. Show the evidence. Check the official pattern. When those habits become automatic, “easy marks” stop disappearing through structure and misunderstanding.</p>
<h2>Source and accuracy note</h2>
<p>This article is based primarily on FBISE’s official curriculum/model-paper portal, current subject assessment frameworks and the official question-paper setter/item-developer training manual. The manual discusses broad cognitive targets of about 30 percent knowledge, 50 percent understanding and 20 percent application; broad difficulty guidance of about 40 percent easy, 40 percent moderate and 20 percent difficult; marking schemes, rubrics, positive and consequential marking, alternative answers and the role of model papers. These are system-level guidelines. Exact subject patterns, marks and word limits must be taken from the latest official assessment framework and model paper for the student’s class and examination year.</p>
<h3>References</h3>
<ul><li>Federal Board of Intermediate and Secondary Education, Curriculum and Model Question Papers: https://www.fbise.edu.pk/curriculum_model_paper.php</li><li>FBISE, Question Paper Setter/Item Developer Training Manual: official PDF available through the FBISE website.</li><li>FBISE, current English Compulsory SSC-I and HSSC-I Assessment Frameworks and Model Question Papers.</li><li>FBISE, current Urdu SSC/HSSC Assessment Frameworks and Model Question Papers.</li><li>National Curriculum of Pakistan documents linked through the official FBISE portal.</li></ul>', 'published', '2026-06-24 09:00:00');

-- ---------------------------------------------------------------------
-- Notes library (final structure). Every primary-nav tab on the public
-- Notes page is a row in note_classes: real classes (9-12) list their
-- subjects via note_class_subjects, while flat classes (class_level >= 13,
-- e.g. MDCAT prep, Summer Camp, Others) list samples directly with
-- subject_id NULL. Sample PDFs live in note_samples. All admin-managed
-- under Admin -> Notes Library.
CREATE TABLE note_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL UNIQUE,
    label VARCHAR(80) NOT NULL,
    has_subjects TINYINT(1) NOT NULL DEFAULT 1,
    exam_label VARCHAR(40),
    description VARCHAR(255),
    icon_key VARCHAR(40) NOT NULL DEFAULT 'document',
    cta_label VARCHAR(80) NOT NULL DEFAULT 'Enroll & Get Complete Notes',
    cta_link VARCHAR(255) NOT NULL DEFAULT 'courses.php',
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1E2A66',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_classes (class_level, label, has_subjects, exam_label, accent_color, sort_order) VALUES
(9,  'Class 9',  1, 'SSC-I',   '#1B7FB4', 1),
(10, 'Class 10', 1, 'SSC-II',  '#E56A19', 2),
(11, 'Class 11', 1, 'HSSC-I',  '#7A3FD0', 3),
(12, 'Class 12', 1, 'HSSC-II', '#1E2A66', 4);

INSERT INTO note_classes (class_level, label, has_subjects, description, icon_key, sort_order) VALUES
(16, 'Others', 0, 'Anything else that does not fit a specific class or subject.', 'folder', 30);

CREATE TABLE note_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL UNIQUE,
    accent_color VARCHAR(20) NOT NULL DEFAULT '#1F2B54',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO note_subjects (name, slug, accent_color, sort_order) VALUES
('English', 'english', '#1B7FB4', 1),
('Urdu', 'urdu', '#E56A19', 2),
('Islamiat', 'islamiat', '#7A3FD0', 3),
('Tarjuma-tul-Quran', 'tarjuma-tul-quran', '#1F2B54', 4),
('English Elective', 'english-elective', '#1B9E6B', 5),
('Pakistan Studies', 'pakistan-studies', '#2E8B57', 6);

-- Which subjects apply to which class, matching the real FBISE curriculum:
-- English/Urdu in all four classes, Islamiat and Tarjuma-tul-Quran in 9 & 11,
-- Pakistan Studies in 10 & 12, English Elective in 11 only.
CREATE TABLE note_class_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL,
    subject_id INT NOT NULL,
    CONSTRAINT fk_note_class_subjects_subject FOREIGN KEY (subject_id) REFERENCES note_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_note_class_subject (class_level, subject_id)
) ENGINE=InnoDB;

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 9 AS class_level UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) c
WHERE s.slug IN ('english', 'urdu');

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 9 AS class_level UNION SELECT 11) c
WHERE s.slug IN ('islamiat', 'tarjuma-tul-quran');

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT c.class_level, s.id FROM note_subjects s
CROSS JOIN (SELECT 10 AS class_level UNION SELECT 12) c
WHERE s.slug = 'pakistan-studies';

INSERT INTO note_class_subjects (class_level, subject_id)
SELECT 11, id FROM note_subjects WHERE slug = 'english-elective';

CREATE TABLE note_samples (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_level TINYINT UNSIGNED NOT NULL,
    subject_id INT NULL,
    title VARCHAR(200) NOT NULL,
    chapter_label VARCHAR(40),
    content_type ENUM('prose', 'poetry', 'other') NOT NULL DEFAULT 'other',
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_note_samples_subject FOREIGN KEY (subject_id) REFERENCES note_subjects(id) ON DELETE RESTRICT,
    INDEX idx_note_samples_filter (class_level, subject_id, status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Alumni story wall (public alumni.php + Admin -> Alumni Stories).
-- status is the moderation state: public "Share Your Story" submissions
-- insert as 'pending' with is_active = 0 and only appear on the site once
-- approved. The dark achiever band on the Alumni page comes from
-- track_records (Admin -> Results & Toppers), not from this table.
CREATE TABLE alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    achievement VARCHAR(200),
    batch_info VARCHAR(100),
    photo VARCHAR(255),
    story TEXT,
    contact VARCHAR(150),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved'
) ENGINE=InnoDB;

INSERT INTO alumni (name, achievement, batch_info, sort_order, is_active, status, story) VALUES
('Ayesha Siddiqui', 'A+ in FBISE HSSC-II', 'Class 12, 2025', 1, 1, 'approved',
'I joined the English Marathon three months before my HSSC-II paper with only 68 in my send-up.

What changed everything was the structured paper practice and continuous worksheets.

I scored 91 in English and secured an overall A+.

To anyone starting late:
It is never too late if you consistently follow the plan.'),
('Muhammad Hamza', 'Federal Board position holder', 'Class 10, 2025', 2, 1, 'approved',
'Urdu was always my weakest subject. The tashreeh notes and the hawala-e-sher sheets from EnglishKeys were the first material that actually made sense to me because everything was written exactly the way examiners expect.

Alhamdulillah I secured a position in the Federal Board. The marked worksheets every week are what kept me consistent.'),
('Fatima Noor', 'A grade, HSSC-I', 'Class 11, 2024', 3, 1, 'approved',
'I was terrified of unseen comprehension passages. In the Crash Course we did one passage every single day and sir showed us how to find the answer inside the passage instead of guessing.

By the exam it had become the easiest part of my paper. The doubt sessions late at night before the paper were a lifesaver.'),
('Bilal Ahmed', 'Studying at NUST', 'Class 12, 2024', 4, 1, 'approved',
'I came for English but the Tarjuma-tul-Quran notes ended up being my favourite part of the course. Surah-wise translation with Shaan-e-Nuzul made the subject feel meaningful instead of something to cram.

I am now in my first year at NUST. The writing practice from this academy still helps me in university assignments.');

-- ---------------------------------------------------------------------
-- FAQ accordion on the public Courses page (Admin -> Courses FAQs).
-- page_slug is kept general so FAQs can later be added to other pages.
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(60) NOT NULL DEFAULT 'courses',
    question VARCHAR(300) NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_faqs_page (page_slug, is_active)
) ENGINE=InnoDB;

INSERT INTO faqs (page_slug, question, answer, sort_order) VALUES
('courses', 'How do I register for a course at EnglishKeys Academy?', 'You can register by calling/texting our registration number (0311-1537563), or through advertisement posters shared on our WhatsApp/Facebook/Instagram socials. Get the payment details, pay the prescribed fee, and share the receipt on the academy''s number.', 1),
('courses', 'Is there an entry test before joining a Bootcamp or Marathon?', 'No formal entry test is required for most programs. However, a short assessment may be conducted at the start of each course to help us understand each student''s current level and group them accordingly.', 2),
('courses', 'What are the class timings for each program?', 'Class timings vary by program and batch and are shared with registered students when a course is announced. Our courses are usually conducted in the evening; please ask our front desk representative for the current schedule.', 3),
('courses', 'What is the fee for each course, and how can I pay?', 'Fee details for each program are shared at the time of registration and may vary by class level and course duration. Fees can be paid online via bank transfer / mobile wallet — receipts are provided for all payments.', 4),
('courses', 'Are seats really limited? How can I confirm if a seat is available?', 'Yes, most Bootcamps and special courses (Summer Camp, MDCAT Prep, Deen Camp, Full-Length Papers) have limited seats to maintain teaching quality. Please call 0311-1537563 to confirm seat availability before making payment.', 5),
('courses', 'What happens if my child misses a class?', 'Make-up notes and recordings (where applicable) can be arranged for students who miss a class due to genuine reasons. Please inform the academy in advance where possible.', 6),
('courses', 'Do you provide study material, notes, or past papers?', 'Yes, class-specific notes, practice worksheets, and past papers are provided as part of the course material for Bootcamps, Marathons, and Crash Courses.', 7),
('courses', 'How are students assessed, and how are results communicated to parents?', 'Students are assessed through weekly quizzes, class tests, and full-length papers depending on the program. Results and performance feedback are shared with parents periodically through report cards, calls, or WhatsApp updates.', 8),
('courses', 'What is the policy on fee refunds or transfers between batches?', 'Fees once paid are generally non-refundable; however, students may be allowed to transfer to another batch of the same program (subject to seat availability) by informing the administration in advance, before the commencement of the course and issuance of the resource pack.', 9),
('courses', 'Are online classes available for students who cannot attend in person?', 'All our classes are online. We don''t offer physical, on-campus classes. Please contact the academy directly to check for the available slots.', 10),
('courses', 'What measures are in place for student safety and discipline?', 'EnglishKeys Academy maintains a disciplined, respectful learning environment, with attendance monitoring and direct communication with parents in case of any concerns regarding a student''s conduct or wellbeing.', 11);

-- ---------------------------------------------------------------------
-- "Proven Track Record" achiever cards. Single source of truth for the
-- dark achiever band shown on Home ("Proven Track Record"), Testimonials
-- ("Alumnus Corner") and Alumni (top band), rendered everywhere via
-- renderTrackRecordCard() in includes/functions.php. Admin manages these
-- under Admin -> Homepage Track Record.
CREATE TABLE track_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(10) NOT NULL,
    position_badge VARCHAR(60) NOT NULL DEFAULT '1st Position',
    student_name VARCHAR(150) NOT NULL,
    achievement_title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO track_records (year, position_badge, student_name, achievement_title, sort_order) VALUES
('2023', '1ST POSITION', 'Hafiza Tanzeela Sahar', 'HSSC 1st Position - Federal Board', 1),
('2024', '1ST POSITION', 'Seerat Fatima', 'HSSC 1st Position - Federal Board', 2),
('2025', '1ST POSITION', 'Aleena Tahir', 'HSSC 1st Position - RMU MBBS Merit #2', 3);

-- ---------------------------------------------------------------------
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(60),
    subject VARCHAR(150),
    message TEXT NOT NULL,
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

