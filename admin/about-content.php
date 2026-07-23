<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $db->prepare(
        'INSERT INTO content_blocks (page_slug, block_key, content) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE content = VALUES(content)'
    )->execute(['about', 'method_steps', trim($_POST['method_steps'] ?? '')]);
    redirectWithMessage('about-content.php', 'About page content updated.');
}

$methodSteps = getContentBlock('about', 'method_steps')['content'] ?? '';
?>
<h1>About Page</h1>
<p class="admin-page-intro">The "Our Method" section on the public About page.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <label>Method Steps (one per line, format: Title|Description)
    <textarea name="method_steps" rows="8"><?= e($methodSteps) ?></textarea>
  </label>
  <button type="submit">Save</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
