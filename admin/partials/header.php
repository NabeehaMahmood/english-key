<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$flash = getFlashMessage();
$currentPage = basename($_SERVER['SCRIPT_NAME']);

$navItems = [
    'index.php' => 'Dashboard',
    'settings.php' => 'Site Settings',
    'home-content.php' => 'Homepage Content',
    'home-stats.php' => 'Homepage Stats',
    'home-track-record.php' => 'Homepage Track Record',
    'page-heroes.php' => 'Page Heroes',
    'page-content.php' => 'Page Content',
    'courses.php' => 'Courses',
    'programme-groups.php' => 'Programme Groups',
    'teachers.php' => 'Our Team',
    'testimonials.php' => 'Testimonials',
    'blog.php' => 'Blog',
    'notes.php' => 'Notes',
    'alumni.php' => 'Alumni',
    'enrollments.php' => 'Enrollments',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - <?= e(getSetting('site_name', 'EnglishKeys Academy')) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <h2>Admin</h2>
    <nav>
      <ul>
        <?php foreach ($navItems as $file => $label): ?>
          <li><a href="<?= e($file) ?>" class="<?= $currentPage === $file ? 'active' : '' ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <a href="logout.php" class="logout-link">Log Out</a>
  </aside>
  <div class="admin-content<?= in_array($currentPage, ['blog.php', 'notes.php'], true) ? ' admin-content-wide' : '' ?>">
    <?php if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
