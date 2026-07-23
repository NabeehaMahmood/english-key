<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM home_stats WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('home-stats.php#all-stats', 'Stat deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $value = trim($_POST['value'] ?? '');
        $label = trim($_POST['label'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($value === '' || $label === '') {
            redirectWithMessage('home-stats.php#add-stat', 'Value and label are required.', 'error');
        }

        if ($id > 0) {
            $db->prepare('UPDATE home_stats SET value=?, label=?, sort_order=?, is_active=? WHERE id=?')
               ->execute([$value, $label, $sortOrder, $isActive, $id]);
            redirectWithMessage('home-stats.php#all-stats', 'Stat updated.');
        } else {
            $db->prepare('INSERT INTO home_stats (value, label, sort_order, is_active) VALUES (?,?,?,?)')
               ->execute([$value, $label, $sortOrder, $isActive]);
            redirectWithMessage('home-stats.php#all-stats', 'Stat added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM home_stats WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$stats = $db->query('SELECT * FROM home_stats ORDER BY sort_order, id')->fetchAll();
?>
<div class="admin-tabs-page">
<h1>Homepage Stats</h1>
<p class="admin-page-intro">The stat cards shown in the dark band below the hero (e.g. "210K+ / Learners in our community"). This is the single source for that band on the Home page, the About page, and any future page - add, edit, delete, reorder with Sort Order, and hide with Visible.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Homepage Stats sections">
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="add-stat" role="tab" aria-selected="false"><?= icon('plus', 'tab-icon') ?> <?= $editing ? 'Edit Stat' : 'Add Stat' ?></button>
  <button type="button" class="admin-tab active" data-tab-group="main" data-tab-target="all-stats" role="tab" aria-selected="true"><?= icon('target', 'tab-icon') ?> All Stats <span class="tab-count"><?= count($stats) ?></span></button>
</nav>

<div class="admin-tabpanel" id="add-stat" data-tab-group="main" data-tab-id="add-stat" hidden>
  <form method="post" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

    <h2><?= $editing ? 'Edit Stat' : 'Add Stat' ?></h2>

    <label>Value (e.g. "210K+", "3×", "5 yrs", "2012")
      <input type="text" name="value" value="<?= e($editing['value'] ?? '') ?>" required>
    </label>
    <label>Label (e.g. "Learners in our community")
      <input type="text" name="label" value="<?= e($editing['label'] ?? '') ?>" required>
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>

    <button type="submit"><?= $editing ? 'Update' : 'Add Stat' ?></button>
    <?php if ($editing): ?><a href="home-stats.php#all-stats" class="button-secondary">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="admin-tabpanel" id="all-stats" data-tab-group="main" data-tab-id="all-stats">
  <h2>All Stats</h2>
  <table class="admin-table">
    <thead>
      <tr><th>Value</th><th>Label</th><th>Sort Order</th><th>Visible</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($stats as $stat): ?>
        <tr>
          <td><?= e($stat['value']) ?></td>
          <td><?= e($stat['label']) ?></td>
          <td><?= (int)$stat['sort_order'] ?></td>
          <td><?= $stat['is_active'] ? 'Yes' : 'No' ?></td>
          <td>
            <a href="home-stats.php?edit=<?= (int)$stat['id'] ?>#add-stat">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this stat?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$stat['id'] ?>">
              <button type="submit" class="link-button">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
