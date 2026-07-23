<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;
$cardStyles = ['standard' => 'Standard (stars only)', 'marks' => 'Marks badge (orange)', 'parent' => 'Parent quote (left border)', 'tag' => 'Subject/course badge (navy)'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $sectionForm = $_POST['section_form'] ?? 'testimonial';
    $action = $_POST['action'] ?? '';

    if ($sectionForm === 'category') {
        if ($action === 'delete') {
            $db->prepare('DELETE FROM testimonial_categories WHERE id = ?')->execute([(int)$_POST['id']]);
            $db->prepare('UPDATE testimonials SET category_id = NULL WHERE category_id = ?')->execute([(int)$_POST['id']]);
            redirectWithMessage('testimonials.php#categories-all', 'Category deleted.');
        }

        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $cardStyle = in_array($_POST['card_style'] ?? '', array_keys($cardStyles), true) ? $_POST['card_style'] : 'standard';
            $heading = trim($_POST['heading'] ?? '') ?: null;
            $subText = trim($_POST['sub_text'] ?? '') ?: null;
            $ctaLabel = trim($_POST['cta_label'] ?? '') ?: null;
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($name === '') {
                redirectWithMessage('testimonials.php#categories-add', 'Name is required.', 'error');
            }

            if ($id > 0) {
                $db->prepare('UPDATE testimonial_categories SET name=?, card_style=?, heading=?, sub_text=?, cta_label=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $cardStyle, $heading, $subText, $ctaLabel, $sortOrder, $isActive, $id]);
                redirectWithMessage('testimonials.php#categories-all', 'Category updated.');
            } else {
                $db->prepare('INSERT INTO testimonial_categories (name, card_style, heading, sub_text, cta_label, sort_order, is_active) VALUES (?,?,?,?,?,?,?)')
                   ->execute([$name, $cardStyle, $heading, $subText, $ctaLabel, $sortOrder, $isActive]);
                redirectWithMessage('testimonials.php#categories-all', 'Category added.');
            }
        }
    } else {
        if ($action === 'delete') {
            $db->prepare('DELETE FROM testimonials WHERE id = ?')->execute([(int)$_POST['id']]);
            redirectWithMessage('testimonials.php#all-testimonials', 'Testimonial deleted.');
        }

        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $quote = trim($_POST['quote'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
            $course = trim($_POST['course'] ?? '') ?: null;
            $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $sourceLabel = trim($_POST['source_label'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($name === '' || $quote === '') {
                redirectWithMessage('testimonials.php#add-testimonial', 'Name and quote are required.', 'error');
            }

            try {
                $photo = handleImageUpload('photo', 'gallery');
            } catch (RuntimeException $e) {
                redirectWithMessage('testimonials.php#add-testimonial', $e->getMessage(), 'error');
            }

            if ($id > 0) {
                if ($photo) {
                    $db->prepare('UPDATE testimonials SET name=?, photo=?, quote=?, category_id=?, course=?, rating=?, is_featured=?, source_label=?, sort_order=?, is_active=? WHERE id=?')
                       ->execute([$name, $photo, $quote, $categoryId, $course, $rating, $isFeatured, $sourceLabel, $sortOrder, $isActive, $id]);
                } else {
                    $db->prepare('UPDATE testimonials SET name=?, quote=?, category_id=?, course=?, rating=?, is_featured=?, source_label=?, sort_order=?, is_active=? WHERE id=?')
                       ->execute([$name, $quote, $categoryId, $course, $rating, $isFeatured, $sourceLabel, $sortOrder, $isActive, $id]);
                }
                redirectWithMessage('testimonials.php#all-testimonials', 'Testimonial updated.');
            } else {
                $db->prepare('INSERT INTO testimonials (name, photo, quote, category_id, course, rating, is_featured, source_label, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)')
                   ->execute([$name, $photo, $quote, $categoryId, $course, $rating, $isFeatured, $sourceLabel, $sortOrder, $isActive]);
                redirectWithMessage('testimonials.php#all-testimonials', 'Testimonial added.');
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$editingCategory = null;
if (isset($_GET['edit_category'])) {
    $catStmt = $db->prepare('SELECT * FROM testimonial_categories WHERE id = ?');
    $catStmt->execute([(int)$_GET['edit_category']]);
    $editingCategory = $catStmt->fetch();
}

$categories = $db->query('SELECT * FROM testimonial_categories ORDER BY sort_order, id')->fetchAll();
$categoryNames = [];
foreach ($categories as $c) {
    $categoryNames[$c['id']] = $c['name'];
}

$testimonials = $db->query('SELECT * FROM testimonials ORDER BY sort_order, id')->fetchAll();
?>
<div class="admin-tabs-page">
<h1>Testimonials</h1>
<p class="admin-page-intro">The reviews shown across the site, and the category tabs used to filter them on the Testimonials page.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Testimonials sections">
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="add-testimonial" role="tab" aria-selected="false"><?= icon('plus', 'tab-icon') ?> <?= $editing ? 'Edit Testimonial' : 'Add Testimonial' ?></button>
  <button type="button" class="admin-tab active" data-tab-group="main" data-tab-target="all-testimonials" role="tab" aria-selected="true"><?= icon('chat', 'tab-icon') ?> All Testimonials <span class="tab-count"><?= count($testimonials) ?></span></button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="categories" role="tab" aria-selected="false"><?= icon('ticket', 'tab-icon') ?> Categories <span class="tab-count"><?= count($categories) ?></span></button>
</nav>

<div class="admin-tabpanel" id="add-testimonial" data-tab-group="main" data-tab-id="add-testimonial" hidden>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="section_form" value="testimonial">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

    <h2><?= $editing ? 'Edit Testimonial' : 'Add Testimonial' ?></h2>

    <label>Name
      <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
    </label>
    <label>Quote
      <textarea name="quote" rows="3" required><?= e($editing['quote'] ?? '') ?></textarea>
    </label>
    <label>Category
      <select name="category_id">
        <option value="">— None —</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>" <?= (int)($editing['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Course / Highlight badge (subject, course type, or a marks callout like "98 in pre-board", depending on the category's card style)
      <input type="text" name="course" value="<?= e($editing['course'] ?? '') ?>">
    </label>
    <label>Rating (1-5 stars, used on Featured-style cards)
      <input type="number" name="rating" min="1" max="5" value="<?= (int)($editing['rating'] ?? 5) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_featured" <?= !empty($editing['is_featured']) ? 'checked' : '' ?>>
      Featured badge
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
    <?php if ($editing): ?><a href="testimonials.php#all-testimonials" class="button-secondary">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="admin-tabpanel" id="all-testimonials" data-tab-group="main" data-tab-id="all-testimonials">
  <h2>All Testimonials</h2>
  <table class="admin-table">
    <thead>
      <tr><th>Name</th><th>Category</th><th>Course</th><th>Rating</th><th>Featured</th><th>Quote</th><th>Visible</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($testimonials as $t): ?>
        <tr>
          <td><?= e($t['name']) ?></td>
          <td><?= e($categoryNames[$t['category_id']] ?? '—') ?></td>
          <td><?= e($t['course']) ?></td>
          <td><?= (int)$t['rating'] ?></td>
          <td><?= $t['is_featured'] ? 'Yes' : 'No' ?></td>
          <td><?= e(mb_strimwidth($t['quote'], 0, 60, '...')) ?></td>
          <td><?= $t['is_active'] ? 'Yes' : 'No' ?></td>
          <td class="actions-cell">
            <a href="testimonials.php?edit=<?= (int)$t['id'] ?>#add-testimonial">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this testimonial?');">
              <?= csrfField() ?>
              <input type="hidden" name="section_form" value="testimonial">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="admin-tabpanel admin-section-card" id="categories" data-tab-group="main" data-tab-id="categories" hidden>
  <h2>Testimonial Categories</h2>
  <div class="admin-note">
    <?= icon('ticket', 'note-icon') ?>
    <p>The filter tabs shown on the Testimonials page (e.g. Featured, Results &amp; Marks, From Parents). Each review in <strong>All Testimonials</strong> is assigned to one category.</p>
  </div>

  <nav class="admin-tabbar admin-tabbar-nested" role="tablist" aria-label="Testimonial Categories sections">
    <button type="button" class="admin-tab" data-tab-group="categories" data-tab-target="categories-add" role="tab" aria-selected="false"><?= icon('plus', 'tab-icon') ?> <?= $editingCategory ? 'Edit Category' : 'Add Category' ?></button>
    <button type="button" class="admin-tab active" data-tab-group="categories" data-tab-target="categories-all" role="tab" aria-selected="true"><?= icon('ticket', 'tab-icon') ?> All Categories <span class="tab-count"><?= count($categories) ?></span></button>
  </nav>

  <div class="admin-tabpanel-nested" id="categories-add" data-tab-group="categories" data-tab-id="categories-add" hidden>
    <form method="post" class="admin-form">
      <?= csrfField() ?>
      <input type="hidden" name="section_form" value="category">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editingCategory['id'] ?? 0) ?>">

      <label>Name (shown as the tab label)
        <input type="text" name="name" value="<?= e($editingCategory['name'] ?? '') ?>" required>
      </label>
      <label>Card Style
        <select name="card_style">
          <?php foreach ($cardStyles as $val => $label): ?>
            <option value="<?= e($val) ?>" <?= ($editingCategory['card_style'] ?? 'standard') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Heading (shown above the review cards for this tab)
        <input type="text" name="heading" value="<?= e($editingCategory['heading'] ?? '') ?>">
      </label>
      <label>Sub-text (optional, small line under the heading)
        <input type="text" name="sub_text" value="<?= e($editingCategory['sub_text'] ?? '') ?>">
      </label>
      <label>Button label (optional; if set, shows a "Google reviews" button under the cards using the Google Reviews URL from Site Settings)
        <input type="text" name="cta_label" value="<?= e($editingCategory['cta_label'] ?? '') ?>">
      </label>
      <label>Sort Order (also controls tab order; the first tab is shown open by default)
        <input type="number" name="sort_order" value="<?= (int)($editingCategory['sort_order'] ?? 0) ?>">
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= (!isset($editingCategory) || $editingCategory['is_active']) ? 'checked' : '' ?>>
        Visible on site
      </label>

      <button type="submit"><?= $editingCategory ? 'Update' : 'Add Category' ?></button>
      <?php if ($editingCategory): ?><a href="testimonials.php#categories-all" class="button-secondary">Cancel</a><?php endif; ?>
    </form>
  </div>

  <div class="admin-tabpanel-nested" id="categories-all" data-tab-group="categories" data-tab-id="categories-all">
    <table class="admin-table">
      <thead>
        <tr><th>Name</th><th>Card Style</th><th>Sort Order</th><th>Visible</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?= e($cat['name']) ?></td>
            <td><?= e($cardStyles[$cat['card_style']] ?? $cat['card_style']) ?></td>
            <td><?= (int)$cat['sort_order'] ?></td>
            <td><?= $cat['is_active'] ? 'Yes' : 'No' ?></td>
            <td class="actions-cell">
              <a href="testimonials.php?edit_category=<?= (int)$cat['id'] ?>#categories-add">Edit</a>
              <form method="post" class="inline-form" onsubmit="return confirm('Delete this category? Reviews in it will become uncategorised.');">
                <?= csrfField() ?>
                <input type="hidden" name="section_form" value="category">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                <button type="submit" class="link-button link-button-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
