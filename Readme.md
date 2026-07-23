# EnglishKeys Academy

The website for **EnglishKeys Academy**, an online coaching platform that
prepares FBISE (Federal Board) students in Pakistan for Classes 9-12. Classes
are taught live by the academy's founders, Uzma Arif (Founder & CEO) and
Mr. Naeem Haider (Co-Founder, Director & Lead Instructor), who have been
teaching since 2012 and built the platform in 2020.

The academy specializes in English, Urdu, Islamiat and Tarjuma-tul-Quran for
Classes 9-12, plus exam-prep programmes for MDCAT, IELTS, TEFL, PTE, and the
English components of CSS/PMS. It is best known for producing three
consecutive HSSC 1st positions on the Federal Board (2023, 2024, 2025), and
has a community of 210K+ learners and 147K+ YouTube subscribers.

This repository is the full public website plus the admin panel used to run
it day to day.

## What's on the site

- **Home, About, Courses** - the academy's story, its founders, and every
  course/programme on offer.
- **Notes** - a public library of free sample notes, with premium notes
  unlocked for enrolled students.
- **Blog** - exam tips and study guidance articles.
- **Testimonials & Alumni** - genuine student/parent reviews and alumni
  success stories (including a public "share your story" submission form).
- **Contact** - the single place to reach the academy: a contact form,
  payment details, How-to-Enrol steps, and FAQs.

## Admin panel

Every piece of content above is editable from `/admin` without touching
code - courses, notes, blog posts, testimonials, alumni stories, team
members, homepage sections, page banners, contact/payment info, and more.
Log in at `/admin/login.php`.

## Running it locally

This project ships with Docker so it runs the same everywhere:

```bash
docker compose up -d --build
```

This starts the app (PHP/Apache) on `http://localhost:8080` and a MySQL
database seeded from `sql/schema.sql`. If you're setting up an existing
database instead of a fresh one, apply any `sql/migration_*.sql` files added
since your last import, in date order.

## Deploying

On a shared host (e.g. Hostinger) without Docker, upload the files as-is,
point `config.php` at your MySQL database (or set the equivalent `MYSQL*`
environment variables), and import `sql/schema.sql`. Set `APP_DEBUG=false`
before going live so raw errors are never shown to visitors.
