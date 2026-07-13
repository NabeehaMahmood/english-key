<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$fields = [
    'site_name' => 'Site Name',
    'tagline' => 'Tagline',
    'kicker' => 'Hero Eyebrow Label',
    'phone' => 'Phone',
    'phone_2' => 'Phone (second number)',
    'whatsapp_number' => 'WhatsApp Number (digits only, e.g. 923111537563)',
    'email' => 'Email',
    'address' => 'Address',
    'office_hours' => 'Office Hours',
    'social_facebook' => 'Facebook URL',
    'social_instagram' => 'Instagram URL',
    'social_youtube' => 'YouTube URL',
    'social_linkedin' => 'LinkedIn URL',
    'youtube_subscribers' => 'YouTube Subscriber Count (e.g. 147K+)',
    'google_reviews_url' => 'Google Reviews URL',
    'google_rating' => 'Google Rating (e.g. 4.8)',
    'google_review_count' => 'Google Review Count',
    'stat_learners' => 'Stat: Learners (e.g. 210K+)',
    'stat_positions' => 'Stat: 1st Positions (e.g. 3x)',
    'stat_years' => 'Stat: Years Teaching FBISE',
    'stat_since' => 'Stat: Teaching Since (year)',
    'stat_youtube_subs' => 'Stat: YouTube Subscribers',
    'founded_date' => 'Founded Date',
    'bank_name' => 'Bank Name',
    'bank_title' => 'Bank Account Title',
    'bank_iban' => 'Bank IBAN',
    'easypaisa_name' => 'EasyPaisa Name',
    'easypaisa_number' => 'EasyPaisa Number',
    'footer_text' => 'Footer Text',
    'footer_note' => 'Footer Note (e.g. board/class/timezone line)',
    'accent_color' => 'Accent Color (hex)',
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
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>

  <?php foreach ($fields as $key => $label): ?>
    <label><?= e($label) ?>
      <?php if ($key === 'footer_text' || $key === 'address'): ?>
        <textarea name="<?= e($key) ?>" rows="2"><?= e($settings[$key] ?? '') ?></textarea>
      <?php elseif ($key === 'accent_color'): ?>
        <input type="color" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '#2f6f4f') ?>">
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
