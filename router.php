<?php
/**
 * PHP built-in server ignores .htaccess entirely, so /blog and /blog/<slug>
 * 404 (or worse, silently fall back to index.php) without this. Mirrors the
 * two RewriteRules in .htaccess so local testing matches real Apache
 * behaviour. Not needed on Apache/XAMPP or the Docker setup (both use
 * .htaccess directly) — only used when running via `php -S ... router.php`.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/blog/?$#', $uri)) {
    require __DIR__ . '/blog.php';
    return true;
}

if (preg_match('#^/blog/([A-Za-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/blog-post.php';
    return true;
}

return false;
