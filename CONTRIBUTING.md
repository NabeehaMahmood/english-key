# Contributing to EnglishKeys Academy

This project isn't deployed anywhere public yet, so everyone works the same
way: pull the code from GitHub, run it on your own machine, test in your
browser, then push your changes back. This doc covers both the one-time
setup and the day-to-day Git workflow.

## 1. What you need installed (one-time setup)

| Tool | Why you need it | Get it |
|---|---|---|
| **Git** | To pull/push code from GitHub | [git-scm.com/downloads](https://git-scm.com/downloads) |
| **XAMPP** (Apache + MySQL + PHP 8) | Runs the site locally exactly like the real hosting will | [apachefriends.org](https://www.apachefriends.org/) |
| **A code editor** | VS Code is recommended, but any editor works | [code.visualstudio.com](https://code.visualstudio.com/) |
| **A GitHub account** | To clone the repo and open Pull Requests | [github.com](https://github.com/) |

You do **not** need to install PHP or MySQL separately — XAMPP bundles
both, plus phpMyAdmin (a web UI for the database) and Apache (the web
server). This matches the PHP + MySQL stack the site will eventually run on
in production, so "it works on my machine" reliably means it'll work for
real too.

> Alternative: if you already use Docker, a `Dockerfile` and
> `docker-compose.yml` are included in the repo — `docker compose up --build`
> gives you the app + a MySQL database with no XAMPP install needed. Either
> approach is fine; XAMPP is the simpler default for anyone new to PHP.

## 2. First-time project setup

1. **Clone the repo** (ask whoever's coordinating for the GitHub URL if you don't have it):
   ```
   git clone <repo-url>
   ```
2. **Install XAMPP**, then copy the whole project folder into XAMPP's `htdocs`
   directory, e.g. `C:\xampp\htdocs\academy` (Windows) or
   `/Applications/XAMPP/htdocs/academy` (Mac).
3. **Start Apache and MySQL** from the XAMPP Control Panel.
4. **Create the database**: open `http://localhost/phpmyadmin`, and import
   `sql/schema.sql` (Import tab → choose file → Go). This creates the
   `academy` database with all tables and starter content in one step.
5. **Visit the site**: `http://localhost/academy/`. Admin dashboard is at
   `http://localhost/academy/admin/` — log in with:
   - username: `admin`
   - password: `ChangeMe123!`
6. **Config check**: `config.php` already defaults to XAMPP's standard
   settings (`localhost`, user `root`, no password), so it should work
   immediately after import with no editing. Only touch this file if your
   local MySQL setup is non-standard.

You only need to do this setup once. After that, it's just pull → work →
test → push.

## 3. Day-to-day Git workflow

**Before starting any new work, always pull the latest changes:**
```
git checkout main
git pull
```

**Create a branch for what you're working on** (don't work directly on `main`):
```
git checkout -b your-name/short-description
```
Example: `git checkout -b nabeeha/fix-contact-form`

**Make your changes**, then test them in the browser at
`http://localhost/academy/` before pushing anything. If you changed
something in `/admin`, test that too, logged in as admin.

**Stage and commit your changes:**
```
git status              # see what changed
git add <files>          # stage specific files (avoid `git add .` if unsure what changed)
git commit -m "Short description of what changed and why"
```

**Push your branch and open a Pull Request:**
```
git push -u origin your-name/short-description
```
Then go to the repo on GitHub — it'll show a banner to open a Pull Request
(PR) from your branch into `main`. Fill in what you changed and why, then
request a review.

**After your PR is approved and merged**, switch back to `main` and pull
again before starting your next branch:
```
git checkout main
git pull
```

## 4. Git cheat sheet

| Command | What it does |
|---|---|
| `git status` | Shows what files you've changed |
| `git pull` | Downloads the latest changes from GitHub |
| `git checkout -b my-branch` | Creates and switches to a new branch |
| `git add <file>` | Stages a file to be committed |
| `git commit -m "message"` | Saves staged changes with a message |
| `git push` | Uploads your commits to GitHub |
| `git log --oneline` | Shows recent commit history |
| `git diff` | Shows exact line changes not yet committed |

## 5. Things to know before you push

- **Don't commit real credentials.** `config.php` currently holds only
  local XAMPP defaults (no real passwords). If you ever add real
  Hostinger or production credentials while testing something, don't
  commit that version of the file.
- **Uploaded images**: files you upload through the admin dashboard
  (logos, course images, gallery photos) land in `assets/uploads/...` on
  *your* machine only. They won't show up for teammates unless you
  `git add` and commit them on purpose — most day-to-day testing uploads
  don't need to be committed.
- **Database content vs. database structure**: `sql/schema.sql` is the
  shared source of truth for the table structure and starter content.
  If you change something *through the admin dashboard* (adding a course,
  editing text), that only affects your local database — it's not
  automatically shared. If you need to change the schema itself (add a
  column, a table), edit `sql/schema.sql` directly and mention it in your
  PR so others know to re-import it.
- **Merge conflicts**: if `git pull` reports a conflict, don't panic —
  open the conflicting file, look for the `<<<<<<<` / `=======` /
  `>>>>>>>` markers, decide which version (or combination) is correct,
  remove the markers, save, then `git add` the file and continue.
  Ask in the team chat if you're unsure.

## 6. Project structure, quick orientation

```
/                    public-facing pages (index.php, courses.php, ...)
/admin               admin dashboard (protected, requires login)
/includes            shared PHP (DB connection, header/footer, helpers)
/assets              CSS, JS, uploaded images
/sql/schema.sql       database structure + starter content (source of truth)
config.php           local environment settings (DB connection, mail)
```
