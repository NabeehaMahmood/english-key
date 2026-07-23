<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$siteName = getSetting('site_name', 'EnglishKeys Academy');
$logoPath = getSetting('logo_path');
$accentColor = getSetting('accent_color', '#EA6C1F');
$whatsapp = getSetting('whatsapp_number');
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Root-absolute base for static assets, taken from the ACTUAL request path
// (the running script's directory) rather than SITE_URL — so CSS/JS/logo load
// correctly however the site is served: at the domain root, in a subfolder
// (XAMPP /english-key, /academy, ...), or via `php -S`, with zero config and
// no dependency on SITE_URL being set right. Absolute "/..." paths also work
// on deep pretty URLs like /blog/<slug> without a page-level <base> tag (which
// would hijack the in-page #anchor jump-navs on Home/Courses).
$assetBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';
// Cache-buster so a browser never serves a stale style.css / site.js after edits.
$assetVer = static function (string $rel) use ($assetBase): string {
    $full = __DIR__ . '/../' . $rel;
    return $assetBase . $rel . '?v=' . (is_file($full) ? filemtime($full) : '1');
};

$navItems = [
    'index.php' => 'Home',
    'courses.php' => 'Courses',
    'notes.php' => 'Notes',
    'blog.php' => 'Blog',
    'testimonials.php' => 'Testimonials',
    'alumni.php' => 'Alumni',
    'about.php' => 'About',
    'contact.php' => 'Contact',
];

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) && $pageTitle !== '' ? e($pageTitle) . ' - ' . e($siteName) : e($siteName) . ' - ' . e(getSetting('tagline', 'Where Words Build Futures')) ?></title>
<?php if (!empty($pageDescription)): ?><meta name="description" content="<?= e($pageDescription) ?>"><?php endif; ?>
<?php if (!empty($pageKeywords)): ?><meta name="keywords" content="<?= e($pageKeywords) ?>"><?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e($assetVer('assets/css/style.css')) ?>">
<style>:root { --orange: <?= e($accentColor) ?>; }</style>
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs><linearGradient id="ekaGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" style="stop-color:var(--navy)"/><stop offset="100%" style="stop-color:var(--orange)"/></linearGradient></defs></svg>
<script>document.documentElement.classList.add('js');</script>
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs><linearGradient id="ekaGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1E2A66"/><stop offset="100%" stop-color="#E56A19"/></linearGradient></defs></svg>
<header class="site nav">
  <div class="wrap nav-in">
    <a class="logo" href="index.php">
      <?php if ($logoPath): ?>
        <img class="logo-img" src="<?= e($assetBase . $logoPath) ?>" alt="<?= e($siteName) ?>">
      <?php else: ?>
        <span style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;color:var(--navy)"><?= e($siteName) ?></span>
      <?php endif; ?>
    </a>
    <nav class="main nl">
      <?php foreach ($navItems as $file => $label): ?>
        <a href="<?= e($file) ?>" class="<?= $currentPage === $file ? 'active on' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="hactions">
      <a class="btn btn-hero-cta" href="enroll.php">Enrol Now</a>
      <button class="burger" id="burger" type="button" aria-label="Menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
<div class="mm" id="mm">
  <?php foreach ($navItems as $file => $label): ?>
    <a href="<?= e($file) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
  <a class="btn btn-hero-cta" href="enroll.php">Enrol Now</a>
</div>
<?php if ($flash): ?>
  <div class="wrap">
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  </div>
<?php endif; ?>
