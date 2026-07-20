<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$counts = [
    'Courses' => $db->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
    'Team Members' => $db->query('SELECT COUNT(*) FROM teachers')->fetchColumn(),
    'Blog Posts' => $db->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn(),
    'Notes' => $db->query('SELECT COUNT(*) FROM notes')->fetchColumn(),
    'Pending Alumni Stories' => $db->query("SELECT COUNT(*) FROM alumni WHERE type = 'story' AND status = 'pending'")->fetchColumn(),
    'Unread Messages' => $db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn(),
    'New Enrollments' => $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'new'")->fetchColumn(),
];
?>
<h1>Dashboard</h1>
<p>Welcome back, <?= e($_SESSION['admin_username'] ?? 'Admin') ?>.</p>

<div class="stat-grid">
  <?php foreach ($counts as $label => $value): ?>
    <div class="stat-card">
      <div class="stat-value"><?= (int)$value ?></div>
      <div class="stat-label"><?= e($label) ?></div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
