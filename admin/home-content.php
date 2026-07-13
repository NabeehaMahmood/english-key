<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $stmt = $db->prepare('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?');
    $stmt->execute([trim($_POST['hero_title'] ?? ''), 'hero_title']);
    $stmt->execute([trim($_POST['hero_subtitle'] ?? ''), 'hero_subtitle']);
    $stmt->execute([trim($_POST['hero_micro'] ?? ''), 'hero_micro']);

    try {
        $heroImage = handleImageUpload('hero_image', 'logo');
        if ($heroImage) {
            $stmt->execute([$heroImage, 'hero_image']);
        }
        redirectWithMessage('home-content.php', 'Homepage content updated.');
    } catch (RuntimeException $e) {
        redirectWithMessage('home-content.php', $e->getMessage(), 'error');
    }
}

$stmt = $db->query('SELECT setting_key, setting_value FROM site_settings');
$settings = [];
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<h1>Homepage Content</h1>
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>

  <label>Hero Title
    <input type="text" name="hero_title" value="<?= e($settings['hero_title'] ?? '') ?>">
  </label>

  <label>Hero Subtitle
    <textarea name="hero_subtitle" rows="2"><?= e($settings['hero_subtitle'] ?? '') ?></textarea>
  </label>

  <label>Hero Micro Line (small line under the CTA buttons)
    <input type="text" name="hero_micro" value="<?= e($settings['hero_micro'] ?? '') ?>">
  </label>

  <label>Hero Image
    <?php if (!empty($settings['hero_image'])): ?>
      <div><img src="../<?= e($settings['hero_image']) ?>" alt="Current hero image" style="max-height:120px;"></div>
    <?php endif; ?>
    <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit">Save</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
