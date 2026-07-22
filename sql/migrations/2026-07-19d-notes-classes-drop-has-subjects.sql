-- Drops has_subjects -- it was an unnecessary extra toggle. A class either
-- has subjects assigned in note_class_subjects or it doesn't; whether it
-- shows a subject sub-nav on the public page and a Subject field on Add
-- Sample is now derived from that directly, not a separate admin flag.
-- Every class (Class 9, MDCAT English Prep, ...) is managed identically;
-- assigning it a subject later (e.g. if MDCAT grows a "Reading"/"Grammar"
-- split) just works, no flag to flip first.

ALTER TABLE note_classes DROP COLUMN has_subjects;
