<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$siteName = getSetting('site_name', 'EnglishKeys Academy');
$logoPath = getSetting('logo_path');
$accentColor = getSetting('accent_color', '#E56A19');
$whatsapp = getSetting('whatsapp_number');
$currentPage = basename($_SERVER['SCRIPT_NAME']);

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
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<style>:root { --orange: <?= e($accentColor) ?>; }</style>
</head>
<body>
<script>document.documentElement.classList.add('js');</script>
<header class="nav">
  <div class="nav-in">
    <a href="index.php">
      <?php if ($logoPath): ?>
        <img class="logo-img" src="<?= e($logoPath) ?>" alt="<?= e($siteName) ?>">
      <?php else: ?>
        <span style="font-family:'Manrope',sans-serif;font-weight:800;color:var(--navy)"><?= e($siteName) ?></span>
      <?php endif; ?>
    </a>
    <nav class="nl">
      <?php foreach ($navItems as $file => $label): ?>
        <a href="<?= e($file) ?>" class="<?= $currentPage === $file ? 'on' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div style="display:flex;gap:12px;align-items:center">
      <a class="ncta" href="enroll.php">Enrol Now</a>
      <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
<div class="mm" id="mm">
  <?php foreach ($navItems as $file => $label): ?>
    <a href="<?= e($file) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
  <a class="btn btn-o" href="enroll.php">Enrol Now</a>
</div>
<?php if ($flash): ?>
  <div class="wrap">
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  </div>
<?php endif; ?>
