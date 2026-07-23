<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Fixed set of inner pages that use the shared Hero component (see
// includes/hero.php). Not a free CRUD list - the set of pages is fixed
// in code, so rows are only ever updated here, never added/removed.
$pages = [
    'courses' => 'Courses',
    'about' => 'About',
    'testimonials' => 'Testimonials',
    'alumni' => 'Alumni',
    'blog' => 'Blog',
    'notes' => 'Notes',
    'contact' => 'Contact',
    'enroll' => 'Enroll',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $slug = $_POST['page_slug'] ?? '';

    if (!array_key_exists($slug, $pages)) {
        redirectWithMessage('page-heroes.php', 'Unknown page.', 'error');
    }

    $kicker = trim($_POST['kicker'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $titleHighlight = trim($_POST['title_highlight'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $breadcrumb = trim($_POST['breadcrumb'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $showDescription = isset($_POST['show_description']) ? 1 : 0;
    $removeBg = isset($_POST['background_image_remove']);

    if ($title === '') {
        redirectWithMessage('page-heroes.php#' . $slug, 'Title is required.', 'error');
    }

    try {
        $backgroundImage = handleImageUpload('background_image', 'heroes');
    } catch (RuntimeException $e) {
        redirectWithMessage('page-heroes.php#' . $slug, $e->getMessage(), 'error');
    }

    $oldImage = $db->prepare('SELECT background_image FROM page_heroes WHERE page_slug = ?');
    $oldImage->execute([$slug]);
    $oldImage = $oldImage->fetchColumn();

    // A new upload wins over "remove", which wins over keeping the old image.
    $finalImage = $backgroundImage ?: ($removeBg ? '' : $oldImage);
    if ($oldImage && $oldImage !== $finalImage) {
        deleteUploadedImage($oldImage);
    }

    $stmt = $db->prepare(
        'INSERT INTO page_heroes (page_slug, kicker, title, title_highlight, subtitle, breadcrumb, description, show_description, background_image)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE kicker = VALUES(kicker), title = VALUES(title), title_highlight = VALUES(title_highlight),
           subtitle = VALUES(subtitle), breadcrumb = VALUES(breadcrumb), description = VALUES(description),
           show_description = VALUES(show_description), background_image = VALUES(background_image)'
    );
    $stmt->execute([$slug, $kicker, $title, $titleHighlight, $subtitle, $breadcrumb, $description, $showDescription, $finalImage]);

    redirectWithMessage('page-heroes.php#' . $slug, ucfirst($slug) . ' hero updated.');
}

$rows = $db->query('SELECT * FROM page_heroes')->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
?>
<div class="admin-tabs-page">
<h1>Page Heroes</h1>
<p class="admin-page-intro">The banner shown at the top of every inner page (About, Courses, Testimonials, Alumni, Blog, Notes, Contact, Enroll), rendered by one shared component so they all look the same. The Home page's own hero is managed separately under Homepage Content and is not affected here.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Page Hero sections">
  <?php foreach ($pages as $slug => $label): ?>
    <button type="button" class="admin-tab<?= $slug === 'courses' ? ' active' : '' ?>" data-tab-group="heroes" data-tab-target="<?= e($slug) ?>" role="tab" aria-selected="<?= $slug === 'courses' ? 'true' : 'false' ?>"><?= e($label) ?></button>
  <?php endforeach; ?>
</nav>

<?php foreach ($pages as $slug => $label): $row = $rows[$slug] ?? []; ?>
<div class="admin-tabpanel" id="<?= e($slug) ?>" data-tab-group="heroes" data-tab-id="<?= e($slug) ?>"<?= $slug === 'courses' ? '' : ' hidden' ?>>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="page_slug" value="<?= e($slug) ?>">

    <h2><?= e($label) ?> Hero</h2>

    <label>Kicker (small label above the title)
      <input type="text" name="kicker" value="<?= e($row['kicker'] ?? '') ?>">
    </label>
    <label>Title
      <input type="text" name="title" value="<?= e($row['title'] ?? '') ?>" required>
    </label>
    <label>Title Highlight (trailing phrase shown in orange, optional)
      <input type="text" name="title_highlight" value="<?= e($row['title_highlight'] ?? '') ?>">
    </label>
    <label>Subtitle
      <textarea name="subtitle" rows="3"><?= e($row['subtitle'] ?? '') ?></textarea>
    </label>
    <label>Breadcrumb (optional, hidden when blank)
      <input type="text" name="breadcrumb" value="<?= e($row['breadcrumb'] ?? '') ?>" placeholder="e.g. Home / About">
    </label>
    <label>Description (optional secondary paragraph, only shown when enabled below)
      <textarea name="description" rows="3"><?= e($row['description'] ?? '') ?></textarea>
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="show_description" <?= !empty($row['show_description']) ? 'checked' : '' ?>>
      Show description on the page
    </label>
    <label>Background Image (optional, overlays the default dark gradient)
      <?php if (!empty($row['background_image'])): ?>
        <div><img src="../<?= e($row['background_image']) ?>" style="max-height:80px;"></div>
        <label class="checkbox-label"><input type="checkbox" name="background_image_remove"> Remove current image</label>
      <?php endif; ?>
      <input type="file" name="background_image" accept=".jpg,.jpeg,.png,.webp">
    </label>

    <button type="submit">Save <?= e($label) ?> Hero</button>
  </form>
</div>
<?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
