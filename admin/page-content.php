<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Editable text blocks for fixed pages that aren't otherwise covered by a
// dedicated CRUD screen (Home hero lives in home-content.php).
$blocks = [
    ['about', 'quote', 'About - Founders Quote'],
    ['about', 'method_steps', "About - Method Steps (one per line, format: Title|Description)"],
    ['courses', 'how_to_enrol_steps', "Courses - How to Enrol Steps (one per line, format: 01. Title|Description)"],
    ['courses', 'terms_conditions', 'Courses - Terms & Conditions (one per line)'],
    ['contact', 'intro', 'Contact - Introduction'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $stmt = $db->prepare(
        'INSERT INTO content_blocks (page_slug, block_key, content) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE content = VALUES(content)'
    );
    foreach ($blocks as [$page, $key, $label]) {
        $fieldName = $page . '__' . $key;
        $stmt->execute([$page, $key, trim($_POST[$fieldName] ?? '')]);
    }
    redirectWithMessage('page-content.php', 'Page content updated.');
}

$current = [];
foreach ($blocks as [$page, $key, $label]) {
    $stmt = $db->prepare('SELECT content FROM content_blocks WHERE page_slug = ? AND block_key = ?');
    $stmt->execute([$page, $key]);
    $row = $stmt->fetch();
    $current[$page . '__' . $key] = $row['content'] ?? '';
}
?>
<h1>Page Content</h1>
<p>Edit the text blocks used on the About, Courses, and Contact pages.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <?php foreach ($blocks as [$page, $key, $label]): $fieldName = $page . '__' . $key; ?>
    <label><?= e($label) ?>
      <textarea name="<?= e($fieldName) ?>" rows="5"><?= e($current[$fieldName]) ?></textarea>
    </label>
  <?php endforeach; ?>
  <button type="submit">Save Page Content</button>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
