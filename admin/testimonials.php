<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM testimonials WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('testimonials.php', 'Testimonial deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $quote = trim($_POST['quote'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $sourceLabel = trim($_POST['source_label'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $quote === '') {
            redirectWithMessage('testimonials.php', 'Name and quote are required.', 'error');
        }

        try {
            $photo = handleImageUpload('photo', 'gallery');
        } catch (RuntimeException $e) {
            redirectWithMessage('testimonials.php', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo) {
                $db->prepare('UPDATE testimonials SET name=?, photo=?, quote=?, category=?, source_label=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $photo, $quote, $category, $sourceLabel, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE testimonials SET name=?, quote=?, category=?, source_label=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $quote, $category, $sourceLabel, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('testimonials.php', 'Testimonial updated.');
        } else {
            $db->prepare('INSERT INTO testimonials (name, photo, quote, category, source_label, sort_order, is_active) VALUES (?,?,?,?,?,?,?)')
               ->execute([$name, $photo, $quote, $category, $sourceLabel, $sortOrder, $isActive]);
            redirectWithMessage('testimonials.php', 'Testimonial added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$testimonials = $db->query('SELECT * FROM testimonials ORDER BY sort_order, id')->fetchAll();
?>
<h1>Testimonials</h1>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Testimonial' : 'Add Testimonial' ?></h2>

  <label>Name
    <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
  </label>
  <label>Quote
    <textarea name="quote" rows="3" required><?= e($editing['quote'] ?? '') ?></textarea>
  </label>
  <label>Category (e.g. English, Urdu, Islamiat & TQ, Crash Course, Bootcamp, Parent)
    <input type="text" name="category" value="<?= e($editing['category'] ?? '') ?>">
  </label>
  <label>Source Label (e.g. "Verified Google Review", "Class 11 Urdu")
    <input type="text" name="source_label" value="<?= e($editing['source_label'] ?? '') ?>">
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>
  <label>Photo
    <?php if (!empty($editing['photo'])): ?>
      <div><img src="../<?= e($editing['photo']) ?>" style="max-height:80px;"></div>
    <?php endif; ?>
    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit"><?= $editing ? 'Update' : 'Add Testimonial' ?></button>
  <?php if ($editing): ?><a href="testimonials.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Name</th><th>Category</th><th>Quote</th><th>Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($testimonials as $t): ?>
      <tr>
        <td><?= e($t['name']) ?></td>
        <td><?= e($t['category']) ?></td>
        <td><?= e(mb_strimwidth($t['quote'], 0, 60, '...')) ?></td>
        <td><?= $t['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="testimonials.php?edit=<?= (int)$t['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this testimonial?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
