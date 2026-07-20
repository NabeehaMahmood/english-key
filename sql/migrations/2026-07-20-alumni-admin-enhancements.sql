-- Extends the existing `alumni` table (shared by achiever-band entries and
-- alumni-story-wall entries) so the admin can manage both kinds with a
-- proper moderation workflow, instead of inferring the row's kind purely
-- from whether `story` happens to be empty and instead of overloading
-- `is_active` as both "moderation decision" and "visible on site".
--
-- `is_active` itself is left untouched on purpose: index.php and about.php
-- both already read `WHERE is_active = 1 ORDER BY sort_order LIMIT 3` for
-- the homepage/about "Proven Track Record" band, and neither page is being
-- touched by this change, so its meaning ("shows publicly") must keep
-- working exactly as before.
--
-- New columns:
--   type          'achiever' (Alumni Achievers band) or 'story' (Alumni
--                 Stories wall). Replaces the old story-emptiness inference.
--   status        Moderation state for story submissions: 'pending' (just
--                 submitted, awaiting review), 'approved' (reviewed and
--                 allowed to publish), 'rejected' (reviewed and declined).
--                 Achiever rows are always 'approved' - they're admin-authored
--                 and never go through the public moderation queue.
--   passing_year  Explicit passing year for achiever cards, shown instead of
--                 the old substr(batch_info, -4) guess when present.
ALTER TABLE alumni
    ADD COLUMN type ENUM('achiever', 'story') NOT NULL DEFAULT 'achiever' AFTER name,
    ADD COLUMN passing_year VARCHAR(20) NULL AFTER batch_info,
    ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved' AFTER is_active;

-- Backfill type from the existing story-emptiness convention.
UPDATE alumni SET type = 'story' WHERE story IS NOT NULL AND story != '';

-- Backfill status from the existing is_active-as-moderation-flag convention:
-- rows that were already live (is_active = 1) were implicitly approved;
-- rows waiting on the old "Pending Story Submissions" admin table
-- (is_active = 0) were implicitly pending, not rejected - rejection wasn't
-- previously representable, so no existing row should become 'rejected'.
UPDATE alumni SET status = IF(is_active = 1, 'approved', 'pending');

-- Restore the 4 alumni stories that were accidentally removed from the
-- public site. Seeded as ordinary admin-managed 'story' rows (approved,
-- visible, no photo yet) so they behave exactly like any other
-- administrator-created story - editable/removable from the Alumni Stories
-- tab like everything else.
INSERT INTO alumni (name, type, achievement, batch_info, sort_order, is_active, status, story) VALUES
('Bilal Ahmed', 'story', 'Studying at NUST', 'Class 12, 2024', 4, 1, 'approved',
'I came for English but the Tarjuma-tul-Quran notes ended up being my favourite part of the course. Surah-wise translation with Shaan-e-Nuzul made the subject feel meaningful instead of something to cram.

I am now in my first year at NUST. The writing practice from this academy still helps me in university assignments.'),
('Fatima Noor', 'story', 'A grade, HSSC-I', 'Class 11, 2024', 3, 1, 'approved',
'I was terrified of unseen comprehension passages. In the Crash Course we did one passage every single day and sir showed us how to find the answer inside the passage instead of guessing.

By the exam it had become the easiest part of my paper. The doubt sessions late at night before the paper were a lifesaver.'),
('Muhammad Hamza', 'story', 'Federal Board position holder', 'Class 10, 2025', 2, 1, 'approved',
'Urdu was always my weakest subject. The tashreeh notes and the hawala-e-sher sheets from EnglishKeys were the first material that actually made sense to me because everything was written exactly the way examiners expect.

Alhamdulillah I secured a position in the Federal Board. The marked worksheets every week are what kept me consistent.'),
('Ayesha Siddiqui', 'story', 'A+ in FBISE HSSC-II', 'Class 12, 2025', 1, 1, 'approved',
'I joined the English Marathon three months before my HSSC-II paper with only 68 in my send-up.

What changed everything was the structured paper practice and continuous worksheets.

I scored 91 in English and secured an overall A+.

To anyone starting late:
It is never too late if you consistently follow the plan.');
