<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// The footer appears on every page of the site (includes/footer.php).
// Phone/email in the footer's "Get in Touch" column come from Contact Info
// & Payments; the "Programs" column lists live active programmes from
// Courses automatically.
$fields = [
    'footer_description' => 'Footer Description (the paragraph under the logo)',
    'footer_text' => 'Copyright Line (after the site name, e.g. "All rights reserved.")',
    'footer_note' => 'Bottom Note (e.g. FBISE - Classes 9-12 - Online, Pakistan Standard Time)',
    'social_facebook' => 'Facebook URL',
    'social_instagram' => 'Instagram URL',
    'social_youtube' => 'YouTube URL',
    'social_linkedin' => 'LinkedIn URL',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $stmt = $db->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    foreach ($fields as $key => $label) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }
    redirectWithMessage('footer-settings.php', 'Footer updated.');
}

$settings = [];
foreach ($db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<h1>Footer</h1>
<p class="admin-page-intro">The footer shown on every page of the site. Leave a social link empty to hide its icon. The "Get in Touch" column reads from <a href="contact-settings.php">Contact Info &amp; Payments</a>, and the "Programs" column automatically lists the active programmes from <a href="courses.php">Courses</a>.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>

  <?php foreach ($fields as $key => $label): ?>
    <label><?= e($label) ?>
      <?php if ($key === 'footer_description'): ?>
        <textarea name="<?= e($key) ?>" rows="3"><?= e($settings[$key] ?? '') ?></textarea>
      <?php else: ?>
        <input type="text" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
      <?php endif; ?>
    </label>
  <?php endforeach; ?>

  <button type="submit">Save Footer</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
