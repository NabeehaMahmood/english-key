---
name: verify
description: Run this PHP/MySQL app locally to observe real behavior (no Docker available in this sandbox)
---

# Verifying english-key locally

No `docker` binary in this sandbox, so `docker-compose.yml` can't be used
directly. Use PHP's built-in server against a local MySQL/MariaDB instead.

## 1. Get a MySQL instance

Check first — a throwaway instance may already be running from a prior
session:

```bash
ps aux | grep mariadbd | grep -v grep
mysql -h127.0.0.1 -P33061 -uroot -e "SELECT 1"   # try the conventional port used before
```

If none exists, start one against a scratch datadir and load `sql/schema.sql`
(it does `DROP DATABASE IF EXISTS academy; CREATE DATABASE ...`, so it's
self-contained):

```bash
mysql_install_db --datadir=/path/to/scratch/data ...   # or mariadb-install-db
mysqld_safe --datadir=... --socket=/tmp/ekablog.sock --port=33061 --bind-address=127.0.0.1 &
mysql -h127.0.0.1 -P33061 -uroot -e "SOURCE sql/schema.sql"
```

## 2. Run the app

```bash
cd english-key
MYSQLHOST=127.0.0.1 MYSQLPORT=33061 MYSQLDATABASE=academy MYSQLUSER=root \
MYSQLPASSWORD= APP_DEBUG=true SITE_URL=http://127.0.0.1:PORT \
php -S 127.0.0.1:PORT [router.php]
```

`SITE_URL` matters: `blog.php`/`blog-post.php` derive a `<base href>` from
its path component, so set it to match whatever host:port you're actually
hitting or the base tag will point at the wrong place.

## 3. Pretty URLs (`/blog`, `/blog/<slug>`) need a router shim

PHP's built-in server **ignores `.htaccess`** entirely. The real rewrite
rules live in `english-key/.htaccess`. To exercise `/blog` and
`/blog/<slug>` locally, pass a router script mimicking them:

```php
<?php
$root = '/absolute/path/to/english-key';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/blog/?$#', $uri)) {
    chdir($root); require $root . '/blog.php'; return true;
}
if (preg_match('#^/blog/([A-Za-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    chdir($root); require $root . '/blog-post.php'; return true;
}
return false;
```

**Gotcha:** when this router returns `false` (no match), PHP's built-in
server does NOT 404 the way Apache would — it falls back to executing
`index.php` in the docroot for *any* unmatched path (confirmed by direct
test: a garbage path returned HTTP 200 with the homepage's content, and
`db.php` errors surfaced when no DB env vars were set for that instance).
Don't mistake that fallback for a routing bug — it's dev-server-only
behavior. Apache with the project's actual `.htaccess` (which has no
catch-all rule) will 404 real 404s for unmatched paths. Test SQL-injection-
style/malformed slugs via the `blog-post.php?slug=...` query-string form
instead, which exercises the real prepared-statement code path without this
router artifact in the way.

## 4. Stale background servers

`php -S` processes started in earlier turns of a long session can survive
and keep old ports bound with stale code/config. Before trusting a
"success", check `ss -ltnp | grep <port>` and the PID's start time/cmdline —
a mismatch (e.g. missing your new env vars, or no router arg) means you're
talking to a leftover process, not your current one. Kill it and restart.

## 5. Visual checks

No browser UI available; use headless Chrome (`google-chrome` is installed):

```bash
google-chrome --headless --disable-gpu --no-sandbox \
  --virtual-time-budget=3000 --run-all-compositor-stages-before-draw \
  --screenshot=/path/out.png --window-size=1280,900 "http://127.0.0.1:PORT/path"
```

`--virtual-time-budget` matters — the site fades in `.reveal` elements via
JS/IntersectionObserver, and a screenshot taken without it can catch
mid-transition (near-blank) frames that look like missing content but
aren't.
