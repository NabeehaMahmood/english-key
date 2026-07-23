<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$counts = [
    'Active courses' => (int)$db->query('SELECT COUNT(*) FROM courses WHERE is_active = 1')->fetchColumn(),
    'Published blog posts' => (int)$db->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn(),
    'Published note samples' => (int)$db->query("SELECT COUNT(*) FROM note_samples WHERE status = 'published'")->fetchColumn(),
    'Testimonials' => (int)$db->query('SELECT COUNT(*) FROM testimonials WHERE is_active = 1')->fetchColumn(),
    'Alumni stories (published)' => (int)$db->query("SELECT COUNT(*) FROM alumni WHERE status = 'approved' AND is_active = 1")->fetchColumn(),
    'Results & toppers' => (int)$db->query('SELECT COUNT(*) FROM track_records WHERE is_active = 1')->fetchColumn(),
];
?>
<h1>Dashboard</h1>
<p class="admin-page-intro">Welcome back, <?= e($_SESSION['admin_username'] ?? 'admin') ?>. Everything on the public site is edited from the sections in the sidebar &mdash; each section is named after the part of the site it controls.</p>

<?php if ($pendingStories || $unreadMessages): ?>
<div class="admin-note">
  <?= icon('mail', 'note-icon') ?>
  <p>
    Needs attention:
    <?php if ($unreadMessages): ?><a href="messages.php"><?= $unreadMessages ?> unread message<?= $unreadMessages === 1 ? '' : 's' ?></a> &middot;<?php endif; ?>
    <?php if ($pendingStories): ?><a href="alumni.php?tab=pending"><?= $pendingStories ?> alumni stor<?= $pendingStories === 1 ? 'y' : 'ies' ?> awaiting review</a><?php endif; ?>
  </p>
</div>
<?php endif; ?>

<div class="dash-grid">
  <?php foreach ($counts as $label => $value): ?>
    <div class="dash-card"><b><?= $value ?></b><span><?= e($label) ?></span></div>
  <?php endforeach; ?>
</div>

<h2 style="margin-top:28px">Where do I edit&hellip;?</h2>
<table class="admin-table">
  <thead><tr><th>On the public site</th><th>Edit it under</th></tr></thead>
  <tbody>
    <tr><td>Home page hero (title, photo, buttons), Founders' Vision, Why EnglishKeys, popup</td><td><a href="home-content.php">Home Sections</a></td></tr>
    <tr><td>The dark stats band (210K+ learners, 3&times; positions, &hellip;)</td><td><a href="home-stats.php">Home Stats Band</a></td></tr>
    <tr><td>Topper cards on Home, Testimonials &amp; Alumni (photo or avatar per student)</td><td><a href="home-track-record.php">Results &amp; Toppers</a></td></tr>
    <tr><td>Courses, subjects and seasonal programmes (Home + Courses pages)</td><td><a href="courses.php">Courses</a></td></tr>
    <tr><td>Banner headings of inner pages (About, Courses, Notes, &hellip;)</td><td><a href="page-heroes.php">Page Banners</a></td></tr>
    <tr><td>Footer text and social icons (every page)</td><td><a href="footer-settings.php">Footer</a></td></tr>
    <tr><td>Phone, WhatsApp, email, bank &amp; EasyPaisa details (every page)</td><td><a href="contact-settings.php">Contact Info &amp; Payments</a></td></tr>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
