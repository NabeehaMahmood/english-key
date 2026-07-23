<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Brand-level settings only. Contact/payment details live under
// Contact Info & Payments; footer text and socials live under Footer.
$fields = [
    'site_name' => 'Site Name',
    'tagline' => 'Tagline (shown in the header title and footer)',
    'accent_color' => 'Accent Color (used site-wide for buttons and highlights)',
    'founded_date' => 'Founded Date (e.g. 18 July 2020)',
    'youtube_subscribers' => 'YouTube Subscriber Count (e.g. 147K+)',
    'google_reviews_url' => 'Google Reviews URL',
    'google_rating' => 'Google Rating (e.g. 4.8)',
    'google_review_count' => 'Google Review Count (e.g. 708)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $stmt = $db->prepare('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?');
    foreach ($fields as $key => $label) {
        $stmt->execute([trim($_POST[$key] ?? ''), $key]);
    }

    try {
        $logoPath = handleImageUpload('logo', 'logo');
        if ($logoPath) {
            $db->prepare('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?')
               ->execute([$logoPath, 'logo_path']);
        }
        redirectWithMessage('settings.php', 'Settings updated.');
    } catch (RuntimeException $e) {
        redirectWithMessage('settings.php', $e->getMessage(), 'error');
    }
}

$stmt = $db->query('SELECT setting_key, setting_value FROM site_settings');
$settings = [];
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<h1>Site Settings</h1>
<p class="admin-page-intro">The site's identity: name, tagline, logo, accent colour and public ratings. Phone numbers, email and payment accounts are under <a href="contact-settings.php">Contact Info &amp; Payments</a>; footer text and social links are under <a href="footer-settings.php">Footer</a>.</p>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>

  <?php foreach ($fields as $key => $label): ?>
    <label><?= e($label) ?>
      <?php if ($key === 'accent_color'): ?>
        <input type="color" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '#E56A19') ?>">
      <?php else: ?>
        <input type="text" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>">
      <?php endif; ?>
    </label>
  <?php endforeach; ?>

  <label>Logo
    <?php if (!empty($settings['logo_path'])): ?>
      <div><img src="../<?= e($settings['logo_path']) ?>" alt="Current logo" style="max-height:60px;"></div>
    <?php endif; ?>
    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit">Save Settings</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
