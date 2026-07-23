<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$flash = getFlashMessage();
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Inbox counts shown as badges in the sidebar so new items are never missed.
$adminDb = getDb();
$pendingStories = (int)$adminDb->query("SELECT COUNT(*) FROM alumni WHERE status = 'pending'")->fetchColumn();
$unreadMessages = (int)$adminDb->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();

/**
 * Sidebar structure: each group is a labelled set of screens, named after
 * the part of the public site it edits (not after internal file names).
 */
$navGroups = [
    '' => [
        'index.php' => ['Dashboard', 0],
    ],
    'Site-wide' => [
        'settings.php' => ['Site Settings', 0],
        'footer-settings.php' => ['Footer', 0],
        'page-heroes.php' => ['Page Banners', 0],
    ],
    'Home Page' => [
        'home-content.php' => ['Home Sections', 0],
        'home-stats.php' => ['Home Stats Band', 0],
        'home-track-record.php' => ['Results & Toppers', 0],
    ],
    'Content' => [
        'about-content.php' => ['About Page', 0],
        'courses.php' => ['Courses', 0],
        'programme-groups.php' => ['Programme Groups', 0],
        'faqs.php' => ['Enroll FAQs', 0],
        'teachers.php' => ['Our Team', 0],
        'testimonials.php' => ['Testimonials', 0],
        'blog.php' => ['Blog', 0],
        'notes.php' => ['Notes Library', 0],
        'alumni.php' => ['Alumni Stories', $pendingStories],
        'contact-settings.php' => ['Contact Info & Payments', 0],
    ],
    'Inbox' => [
        'messages.php' => ['Contact Messages', $unreadMessages],
    ],
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
      <?php foreach ($navGroups as $groupLabel => $items): ?>
        <?php if ($groupLabel !== ''): ?><div class="nav-group-label"><?= e($groupLabel) ?></div><?php endif; ?>
        <ul>
          <?php foreach ($items as $file => [$label, $badge]): ?>
            <li>
              <a href="<?= e($file) ?>" class="<?= $currentPage === $file ? 'active' : '' ?>">
                <?= e($label) ?><?php if ($badge > 0): ?> <span class="nav-badge"><?= (int)$badge ?></span><?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    </nav>
    <a href="../index.php" class="logout-link" target="_blank" rel="noopener">View Site</a>
    <a href="logout.php" class="logout-link">Log Out</a>
  </aside>
  <div class="admin-content<?= in_array($currentPage, ['blog.php', 'notes.php'], true) ? ' admin-content-wide' : '' ?>">
    <?php if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
