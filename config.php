<?php
/**
 * Central configuration. On Hostinger, replace the DB_* and MAIL_* values
 * with the credentials shown in hPanel (Databases section) and a real
 * mailbox on your domain, then re-upload this file.
 */

// --- Database ---
// Falls back to XAMPP's local defaults, but picks up Railway's (or any host's)
// injected MYSQL* environment variables automatically when present, so the
// same codebase deploys without editing this file per environment.
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'academy');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// --- Site ---
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/academy'); // no trailing slash
define('SITE_TIMEZONE', 'UTC');

// --- Mail (used by includes/functions.php -> notifyAdmin()) ---
define('MAIL_TO', 'admin@example.com');       // where form notifications are sent
define('MAIL_FROM', 'no-reply@example.com');  // must be a real mailbox on your domain once deployed

// --- Uploads ---
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp']);

date_default_timezone_set(SITE_TIMEZONE);

// Surface errors during local/staging testing. Set APP_DEBUG=false as an
// environment variable (or edit this line) before going live on Hostinger,
// so raw errors are never shown to site visitors.
define('APP_DEBUG', getenv('APP_DEBUG') !== 'false');
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
