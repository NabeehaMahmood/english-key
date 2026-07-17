<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;
$iconOptions = ['compass' => 'Compass', 'book-open' => 'Open Book', 'bookmark' => 'Bookmark', 'folder' => 'Folder'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM programme_groups WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('programme-groups.php', 'Programme group deleted. Courses in it moved to "Other Programmes".');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $dateRange = trim($_POST['date_range'] ?? '');
        $iconKey = in_array($_POST['icon_key'] ?? '', array_keys($iconOptions), true) ? $_POST['icon_key'] : 'compass';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            redirectWithMessage('programme-groups.php', 'Name is required.', 'error');
        }

        if ($id > 0) {
            $db->prepare('UPDATE programme_groups SET name=?, description=?, date_range=?, icon_key=?, sort_order=?, is_active=? WHERE id=?')
               ->execute([$name, $description, $dateRange, $iconKey, $sortOrder, $isActive, $id]);
            redirectWithMessage('programme-groups.php', 'Programme group updated.');
        } else {
            $db->prepare('INSERT INTO programme_groups (name, description, date_range, icon_key, sort_order, is_active) VALUES (?,?,?,?,?,?)')
               ->execute([$name, $description, $dateRange, $iconKey, $sortOrder, $isActive]);
            redirectWithMessage('programme-groups.php', 'Programme group added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM programme_groups WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$groups = $db->query('
    SELECT g.*, (SELECT COUNT(*) FROM courses c WHERE c.programme_group_id = g.id) AS course_count
    FROM programme_groups g
    ORDER BY g.sort_order, g.name
')->fetchAll();
?>
<h1>Programme Groups</h1>
<p>Collapsible sections that group Seasonal Programmes on the Courses page (e.g. "Full-Syllabus Bootcamps"). Assign a course to a group from the <a href="courses.php">Courses</a> screen.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Group' : 'Add Group' ?></h2>

  <label>Name
    <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
  </label>
  <label>Description (one sentence shown under the group name)
    <textarea name="description" rows="2"><?= e($editing['description'] ?? '') ?></textarea>
  </label>
  <label>Date Range Label
    <input type="text" name="date_range" value="<?= e($editing['date_range'] ?? '') ?>" placeholder="e.g. Aug 2026 - Jan 2027">
  </label>
  <label>Icon
    <select name="icon_key">
      <?php foreach ($iconOptions as $val => $label): ?>
        <option value="<?= e($val) ?>" <?= ($editing['icon_key'] ?? 'compass') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>

  <button type="submit"><?= $editing ? 'Update Group' : 'Add Group' ?></button>
  <?php if ($editing): ?><a href="programme-groups.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Name</th><th>Date Range</th><th>Programmes</th><th>Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($groups as $group): ?>
      <tr>
        <td><?= e($group['name']) ?></td>
        <td><?= e($group['date_range']) ?></td>
        <td><?= (int)$group['course_count'] ?></td>
        <td><?= $group['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="programme-groups.php?edit=<?= (int)$group['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this group? Its programmes will move to Other Programmes, not be deleted.');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$group['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
