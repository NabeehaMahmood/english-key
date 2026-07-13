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
    ('accent_color', '#E56A19'),
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
    ('easypaisa_number', '0311-1537563');

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
    ('courses', 'how_to_enrol_steps', '01. Choose your course|Select the English Language Summer Course.\n02. Make payment|Transfer the fee to our Askari Bank or EasyPaisa account.\n03. Send proof|WhatsApp the payment screenshot to 0311-1537563.\n04. Get confirmed|Receive the class link and joining instructions.'),
    ('courses', 'terms_conditions', 'Fee once paid is non-refundable.\nClasses are conducted online via Zoom.\nStudents must ensure a stable internet connection.\nRecordings may be shared with enrolled students only.\nEnrolment closes once seats are filled.\nSharing class resources with others is prohibited.\nContacting classmates on their numbers is not allowed.'),
    ('about', 'quote', 'We built this academy the way we run our home, with care, discipline, and the belief that every child deserves a first-class chance.'),
    ('about', 'uzma_bio', 'Uzma Arif is the Founder and CEO of EnglishKeys Academy, a leading online educational platform dedicated to transforming the way quality education reaches students across Pakistan. She holds an M.Sc. in Psychology from Quaid-i-Azam University, a B.Ed. from the Virtual University of Pakistan, and a Diploma in TEFL from Allama Iqbal Open University. Before establishing EnglishKeys Academy, she served as a Language Instructor and Section Head at some of Pakistan''s prestigious educational institutions, where she developed extensive experience in teaching, academic leadership, and curriculum development.\n\nDuring her professional journey, Ms. Uzma Arif realized that quality education should not remain confined to conventional classrooms. Driven by the vision of making education accessible beyond geographical and financial barriers, she co-founded EnglishKeys Academy with her husband, Mr. Naeem Haider, the Lead Instructor, on 18 July 2020. Their mission was to provide affordable, high-quality education to students, particularly those from underserved and underprivileged areas of Pakistan.\n\nWhat began as a small initiative has steadily grown into one of Pakistan''s most trusted online educational brands, earning the confidence of thousands of students and parents nationwide. Today, EnglishKeys Academy specializes exclusively in Federal Board (FBISE) education, offering comprehensive preparation for Grades 9-12 compulsory subjects. Beyond SSC and HSSC education, the academy also delivers professional and competitive exam preparation programs, including MDCAT, IELTS, TEFL, PTE, and English preparation for CSS and PMS aspirants.'),
    ('about', 'naeem_bio', 'Mr. Naeem Haider, Co-Founder, Director and Lead Instructor of EnglishKeys Academy, has taught languages since 2012, guiding over 100,000 students in the last five years alone. A distinguished scholar of English linguistics and literature, he built the academy''s teaching on a simple belief: a student who understands the examiner''s mind never fears the paper.\n\nEvery class is led by him personally, no rotating panel, no stock-photo instructors. The credentials are the product.'),
    ('about', 'method_steps', 'Learn|Concepts taught live, from the ground up, in clear English or Urdu medium.\nPractise|Guided worksheets and MCQ banks that mirror the FBISE paper.\nSubmit|Written work handed in for personal marking, not self-assessment.\nFeedback|Answer-by-answer corrections that show exactly what the examiner wants.\nRevise|Smart capsule notes and model papers for focused, efficient revision.\nFinal Paper|A full-length attempt under exam conditions before the boards.');

-- ---------------------------------------------------------------------
-- category: 'subject' (4 core subjects), 'featured' (currently enrolling
-- flagship course), 'programme' (seasonal/intensive courses)
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    category VARCHAR(20) NOT NULL DEFAULT 'programme',
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
    seats_info VARCHAR(100),
    accent_color VARCHAR(20),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO courses (title, slug, category, tag_line, description, duration, level, price, eligibility, mode, schedule_info, highlights, seats_info, accent_color, sort_order) VALUES
('English', 'english', 'subject', 'Live Classes - Smart Notes - Model Papers', 'Grammar, comprehension, translation and composition, taught by an M.Phil. English Linguistics scholar.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#1E2A66', 1),
('Urdu', 'urdu', 'subject', 'Capsule Notes - MCQ Bank - Model Papers', 'Nazm, ghazal, mazmoon and grammar with full past-paper coverage and answer-writing technique.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#E56A19', 2),
('Islamiat', 'islamiat', 'subject', 'Both Mediums - Live Classes - Model Papers', 'Concept-first teaching of the full syllabus with smart revision notes in English and Urdu medium.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#7A3FD0', 3),
('Tarjuma-tul-Quran', 'tarjuma-tul-quran', 'subject', 'Surah-Wise - Both Mediums - MCQ Bank', 'Surah-wise translation, Shaan-e-Nuzul and MCQ preparation built around the FBISE exam format.', NULL, 'Classes 9-12', NULL, 'Classes 9-12', NULL, NULL, NULL, NULL, '#1B7FB4', 4),
('English Language Summer Course', 'summer-intensive-2026', 'featured', 'Summer Intensive 2026', 'Equally beneficial for students of all boards from Class 8th onwards. It does not teach the syllabus of a specific class; instead it covers all essential topics of the broader curriculum to build a solid base in the language, focusing on Grammar and Creative Writing.', '20 live sessions - 2 hours each', 'All boards, Class 8th onwards', 'Rs. 5,000', 'All boards, Class 8th onwards', 'Online via Zoom', 'Starts 06 July 2026 - Ends 31 July 2026 - Monday-Friday - 07:00-09:00 PM (PKT)', 'All boards - Class 8th onwards\n20 live, interactive sessions\n2 hours per session\nGrammar foundation + advanced writing\nExpert feedback on all exercises\nTopic-wise assessment', 'Seats are strictly limited, register early.', '#E56A19', 5),
('Summer Camp', 'summer-camp', 'programme', 'Jul 2026 - All boards', 'A foundation course focused on grammar and creative writing, one course for every student, Class 8th onwards. Twenty live 2-hour evening sessions (7:00-9:00 PM), building a solid base in the language before the academic year begins.', '6-31 Jul 2026', 'All levels - All boards', 'Rs. 5,000', 'All boards', 'Online', NULL, 'Grammar foundation + advanced writing\nExpert feedback on every exercise\nTopic-wise assessment', 'Limited seats', '#E56A19', 6),
('MDCAT / NUMS English Prep', 'mdcat-nums-english-prep', 'programme', 'Jul 2026 - Medical', 'A concept- and practice-based intensive that targets the English portion of medical entry tests, vocabulary, grammar and comprehension tuned precisely to the exam.', '15 days - Jul 2026', 'Medical aspirants', NULL, 'Medical aspirants', 'Online', NULL, '15-day focused intensive\nConcept building + heavy practice\nExam-style questions throughout', 'Limited seats', '#1B7FB4', 7),
('Bootcamp 01', 'bootcamp-01', 'programme', 'Aug-Sep 2026', 'Class-specific, complete-syllabus coverage for FBISE 9th-12th in English and Urdu. Two months of structured teaching with weekly assessments and one full-length paper under real exam conditions.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', 8),
('Bootcamp 02', 'bootcamp-02', 'programme', 'Oct-Nov 2026', 'The second full-syllabus cohort of the year for English and Urdu, Classes 9-12. Same rigorous format, learn, practise, submit, get feedback, revise, timed for the mid-year stretch.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', 9),
('Bootcamp 03 - Final Bootcamp', 'bootcamp-03', 'programme', 'Dec 2026 - Jan 2027', 'The last complete-syllabus bootcamp before annual exams for English and Urdu, Classes 9-12, the final chance to cover everything thoroughly with assessments and a full-length paper.', '2 months', 'Classes 9-12 - English & Urdu', NULL, 'Classes 9-12', 'Online', NULL, 'Complete syllabus, class by class\nWeekly assessments\nOne full-length paper', 'Limited seats', '#26346F', 10),
('Deen Camp', 'deen-camp', 'programme', 'Jan 2027 - Islamiat & Quran', 'Specialised, class-specific coverage of Islamiat (9th & 11th) and Tarjuma-tul-Quran (9th-12th), taught with depth and clarity, complete with weekly assessments and a full-length paper.', '1 month', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, 'Islamiat: Classes 9 & 11\nTarjuma-tul-Quran: Classes 9-12\nWeekly assessments + full-length paper', 'Limited seats', '#7A3FD0', 11),
('Full-Length Papers', 'full-length-papers', 'programme', 'Feb 2027', 'A month of full-length practice papers across all four subjects, English, Urdu, Islamiat and Tarjuma-tul-Quran, with detailed marking and feedback, so exam day feels familiar.', '1 month', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, 'All four subjects, Classes 9-12\nReal exam conditions & timing\nDetailed marking + feedback', 'Limited seats', '#1E2A66', 12),
('Exam Marathons', 'exam-marathons', 'programme', 'Pre-Board - Marathons', 'Detailed-but-quick revision of the whole syllabus in the final stretch before papers, a 2nd-Annual Marathon in English and an Annual Marathon in English & Urdu. Revision-focused, no assessments.', '15 days', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, '2nd Annual: English - 15 days\nAnnual: English & Urdu - 15 days (Mar 2027)\nRevision only, no assessments', 'Open - unlimited', '#E56A19', 13),
('Crash Courses', 'crash-courses', 'programme', 'Final Days - Crash', 'Short, high-intensity revision right before each paper. A 2-day 2nd-Annual crash in English, and a 1-day Annual crash across all four subjects, following the date sheet.', '1-2 days', 'Classes 9-12 (FBISE)', NULL, 'Classes 9-12', 'Online', NULL, '2nd Annual: English - 2 days\nAnnual: all 4 subjects - 1-day intensives\nHigh-yield topics & answer technique', 'Open - unlimited', '#7A3FD0', 14);

-- ---------------------------------------------------------------------
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    photo VARCHAR(255),
    bio TEXT,
    detail_bio TEXT,
    subject VARCHAR(150),
    role_title VARCHAR(150),
    credentials TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO teachers (name, photo, bio, detail_bio, subject, role_title, credentials, sort_order) VALUES
('Uzma Arif', 'assets/uploads/teachers/uzma-arif.jpeg',
 'The vision behind EnglishKeys, an M.Sc. Psychology (Quaid-i-Azam University) educator and former section head who co-founded the academy in 2020 to carry quality education beyond geographical and financial barriers.',
 'Uzma Arif is the Founder and CEO of EnglishKeys Academy, a leading online educational platform dedicated to transforming the way quality education reaches students across Pakistan. She holds an M.Sc. in Psychology from Quaid-i-Azam University, a B.Ed. from the Virtual University of Pakistan, and a Diploma in TEFL from Allama Iqbal Open University. Before establishing EnglishKeys Academy, she served as a Language Instructor and Section Head at some of Pakistan''s prestigious educational institutions.\n\nDriven by the vision of making education accessible beyond geographical and financial barriers, she co-founded EnglishKeys Academy with her husband, Mr. Naeem Haider, on 18 July 2020.',
 'Founder & CEO', 'Founder and CEO',
 'M.Sc. Psychology, Quaid-i-Azam University\nB.Ed., Virtual University of Pakistan\nDiploma in TEFL, Allama Iqbal Open University\nLanguage Instructor & Section Head, prestigious institutions of Pakistan\nCo-founded EnglishKeys Academy, 18 July 2020\nPrograms: SSC/HSSC (FBISE), MDCAT, IELTS, TEFL, PTE, CSS & PMS English',
 1),
('Mr. Naeem Haider', 'assets/uploads/teachers/naeem-haider.jpeg',
 'The teacher behind the results, an M.Phil. English Linguistics scholar teaching since 2012, who leads every class personally and built the method behind three consecutive HSSC first positions.',
 'Mr. Naeem Haider, Co-Founder, Director and Lead Instructor of EnglishKeys Academy, has taught languages since 2012, guiding over 100,000 students in the last five years alone. A distinguished scholar of English linguistics and literature, he built the academy''s teaching on a simple belief: a student who understands the examiner''s mind never fears the paper.\n\nEvery class is led by him personally, no rotating panel, no stock-photo instructors. The credentials are the product.',
 'Co-Founder & Lead Instructor', 'Co-Founder, Director and Lead Instructor',
 'M.Phil. English Linguistics, Distinction\nMS English, Distinction\nMA English Literature, Silver Medalist\nMA Urdu Literature\nMA Islamic Studies\nB.Ed. (Bachelor of Education)\nDiploma in TEFL\nEMI, University of Southampton\nTEYL, George Mason University, USA',
 2);

-- ---------------------------------------------------------------------
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    photo VARCHAR(255),
    quote TEXT NOT NULL,
    category VARCHAR(60),
    source_label VARCHAR(100),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO testimonials (name, quote, category, source_label, sort_order) VALUES
('Amjad Farooq', 'In my 12 years of school life I always had English teachers that were OK, not good, not bad. But my goodness, your way of teaching is superb. You don''t just dictate, you actually TAUGHT US and we ACTUALLY LEARNED.', 'English', 'Verified Google Review', 1),
('Qasim Mustafa', 'SSC, 73/75 in English with Sir Naeem. HSSC, I was able to attempt my paper exceptionally, and I hope for the best.', 'English', 'SSC 73/75 English', 2),
('Aashir Usman', 'I''ve been a regular student for two years and part of almost all its bootcamps. When I joined, I barely knew grammar. Today, due to Sir Naeem''s teaching, I''ve achieved 91 marks in part one and am aiming for more in part two.', 'Bootcamp', '2-year student - 91 marks', 3),
('Tabu Khan', 'It''s the most authentic platform for studying arts subjects in Pakistan. Amazing notes for every section, grammar revision, crash courses for last-minute revision, and the FLP batch improved my presentation skills and time management.', 'Test Series / FLP', 'Verified Google Review', 4),
('Muhammad Mueen', 'Learning from Sir Naeem has been nothing short of phenomenal. His ability to simplify complex concepts with unmatched clarity is remarkable.', 'English', 'Verified Google Review', 5),
('Rubab Fatima', 'This was my first EKA course, for my 2nd-year preparation, now I regret not availing it earlier. You never feel the communication gap you usually feel in online classes.', 'Bootcamp', 'HSSC-II Student', 6),
('Syed Adan Ali', 'My English was average, but then I joined Sir''s crash course, and it was totally worth it. It really helped me clear my concepts and confusions.', 'Crash Course', 'Verified Google Review', 7),
('Atif', 'I love EnglishKeys Academy. Sir Naeem is very determined and works hard to make sure students understand well. Notes are very concise and it was so fun studying with him.', 'English', 'Verified Google Review', 8),
('Ayan Awais', 'I took the Crash Course for 11th Urdu, and it was really helpful. The concepts were explained clearly and in a structured way.', 'Urdu', 'Class 11 Urdu', 9),
('Syed Muhammad Muhaymin Ali', 'I took the Marathon course for Class 11 2nd-Annual Urdu, and the way Sir taught everything in four weeks, from grammar to letter/application writing, is exceptional.', 'Urdu', 'Urdu Marathon', 10),
('Eshaal Azam', 'I was concerned about my Urdu MCQs, but Alhamdulillah I got them all right. In 10th I used to learn the MCQs Sir posted a day before the exam, it really helped.', 'Islamiat & TQ', 'Islamiat & Tarjuma-tul-Quran', 11),
('Muhammad Hassan Asif Khan', 'The best crash-course experience I ever had. Study 10/10, fun 9/10, help 11/10. Sir is really helpful and thoughtful, and the notes really came in clutch.', 'Crash Course', 'Crash Course', 12),
('Shamshad Ali', 'I joined the English Marathon by Sir Naeem, and it was a truly valuable experience. The Zoom sessions were well-structured and focused on important exam topics.', 'English', 'English Marathon', 13),
('Hamza Sadique', 'I had a great experience. Sir explained every concept deeply and taught us more than the scheduled time. I saw a lot of improvement in English after completing the Marathon batch.', 'English', 'English Marathon', 14),
('Raneen Falak', 'The test series provided three full-length papers with proper time extension, notes and assessment. I went from being unconfident about MCQs to writing a 3-page report and a well-written paragraph.', 'Test Series / FLP', 'Test Series / FLP', 15),
('M. Amin', 'The one-pager grammar notes, where every topic is properly managed, are really helpful. It seemed impossible to complete whole-paper preparation in only two months, but EnglishKeys Academy made it possible.', 'Bootcamp', 'Bootcamp', 16),
('Ayesha Umer', 'I was worried about my son''s preparation. Luckily I came across the academy and followed every post. I''m highly impressed by the devotion and dedication of Sir. My son is expecting around 93 in the exams.', 'Parent', 'Parent', 17);

-- ---------------------------------------------------------------------
-- Renamed from "news" to "blog_posts" to match the live site's /blog page.
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    category VARCHAR(60),
    content TEXT,
    image VARCHAR(255),
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO blog_posts (title, slug, category, content) VALUES
('Full-Marks Paragraph Writing', 'full-marks-paragraph-writing', 'Exam Technique', 'Most students lose paragraph marks on structure, not ideas. Here''s the examiner-approved skeleton that keeps you on track. Coming soon.'),
('Unseen Passages Without Panic', 'unseen-passages-without-panic', 'Comprehension', 'A repeatable method for reading, mapping and answering comprehension questions under time pressure. Coming soon.'),
('Tenses You Keep Getting Wrong', 'tenses-you-keep-getting-wrong', 'Grammar', 'The handful of tense errors that quietly cost marks, and the simple checks that fix them. Coming soon.'),
('Reading Poetry Without Fear', 'reading-poetry-without-fear', 'Literature', 'Simile, metaphor and imagery spotted quickly and explained the way FBISE examiners expect. Coming soon.'),
('Mazmoon Nigari: Structure First', 'mazmoon-nigari-structure-first', 'Urdu', 'How to plan an Urdu essay in two minutes so the writing flows and the marks follow. Coming soon.'),
('Understanding the FBISE Marking Scheme', 'understanding-fbise-marking-scheme', 'Board Updates', 'What the marking scheme rewards, where students lose easy marks, and how to align your answers. Coming soon.');

-- ---------------------------------------------------------------------
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subject_tag VARCHAR(100),
    description TEXT,
    link VARCHAR(255),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO notes (title, subject_tag, description, link, sort_order) VALUES
('Smart Notes for Revision', 'Class 9 - Islamiat', 'Complete revision notes in English and Urdu medium, built on the FBISE pattern.', 'https://wa.me/923111537563', 1),
('Surah-Wise Complete Notes', 'Class 9 - Tarjuma-tul-Quran', 'Full translation notes with Shaan-e-Nuzul and surah-wise MCQs for all surahs.', 'https://wa.me/923111537563', 2),
('Model Papers & MCQ Bank', 'Class 12 - Urdu', 'Full-length model papers, past-paper MCQs and hawala-e-sher notes for HSSC-II.', 'https://wa.me/923111537563', 3),
('Grammar & Composition Pack', 'Class 10 - English', 'Tenses, voice, narration and paragraph writing with solved examples.', 'https://wa.me/923111537563', 4),
('Prose & Poetry Explanations', 'Class 11 - English', 'Chapter summaries, reference-to-context and important questions for HSSC-I.', 'https://wa.me/923111537563', 5),
('Concept Notes + Past Papers', 'Class 12 - Islamiat', 'Concept-first notes with topic-wise past-paper questions and answers.', 'https://wa.me/923111537563', 6);

-- ---------------------------------------------------------------------
-- is_active doubles as the moderation flag: publicly-submitted stories
-- insert with is_active = 0 and wait for admin approval.
CREATE TABLE alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    achievement VARCHAR(200),
    batch_info VARCHAR(100),
    photo VARCHAR(255),
    story TEXT,
    contact VARCHAR(150),
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO alumni (name, achievement, batch_info, sort_order) VALUES
('Hafiza Tanzeela Sahar', 'HSSC 1st Position - Federal Board', 'Class of 2023', 1),
('Seerat Fatima', 'HSSC 1st Position - Federal Board', 'Class of 2024', 2),
('Aleena Tahir', 'HSSC 1st Position - RMU MBBS Merit #2', 'Class of 2025', 3);

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

-- ---------------------------------------------------------------------
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(150) NOT NULL,
    guardian_name VARCHAR(150),
    phone VARCHAR(60),
    whatsapp VARCHAR(60),
    email VARCHAR(150),
    city VARCHAR(100),
    class_level VARCHAR(30),
    board VARCHAR(100),
    subjects VARCHAR(255),
    programme VARCHAR(100),
    preferred_start VARCHAR(100),
    notes TEXT,
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'new'
) ENGINE=InnoDB;
