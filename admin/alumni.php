<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM alumni WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php', 'Entry deleted.');
    }

    if ($action === 'approve') {
        $db->prepare('UPDATE alumni SET is_active = 1 WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php', 'Story approved and published.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $achievement = trim($_POST['achievement'] ?? '');
        $batchInfo = trim($_POST['batch_info'] ?? '');
        $story = trim($_POST['story'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            redirectWithMessage('alumni.php', 'Name is required.', 'error');
        }

        try {
            $photo = handleImageUpload('photo', 'gallery');
        } catch (RuntimeException $e) {
            redirectWithMessage('alumni.php', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo) {
                $db->prepare('UPDATE alumni SET name=?, achievement=?, batch_info=?, photo=?, story=?, contact=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $achievement, $batchInfo, $photo, $story, $contact, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE alumni SET name=?, achievement=?, batch_info=?, story=?, contact=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $achievement, $batchInfo, $story, $contact, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('alumni.php', 'Entry updated.');
        } else {
            $db->prepare('INSERT INTO alumni (name, achievement, batch_info, photo, story, contact, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?)')
               ->execute([$name, $achievement, $batchInfo, $photo, $story, $contact, $sortOrder, $isActive]);
            redirectWithMessage('alumni.php', 'Entry added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM alumni WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$pending = $db->query("SELECT * FROM alumni WHERE is_active = 0 ORDER BY id DESC")->fetchAll();
$published = $db->query("SELECT * FROM alumni WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();
?>
<h1>Alumni</h1>
<p>Achievers (Hat-Trick band) and student-submitted stories. Public story submissions land here as pending, unpublished entries.</p>

<?php if ($pending): ?>
<h2>Pending Story Submissions</h2>
<table class="admin-table">
  <thead><tr><th>Name</th><th>Batch</th><th>Story</th><th>Private Contact</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($pending as $p): ?>
      <tr class="unread-row">
        <td><?= e($p['name']) ?></td>
        <td><?= e($p['batch_info']) ?></td>
        <td><?= e(mb_strimwidth((string)$p['story'], 0, 100, '...')) ?></td>
        <td><?= e($p['contact']) ?></td>
        <td>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button type="submit" class="link-button">Approve &amp; Publish</button>
          </form>
          <a href="alumni.php?edit=<?= (int)$p['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this submission?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Entry' : 'Add Achiever' ?></h2>

  <label>Name
    <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
  </label>
  <label>Achievement (e.g. "HSSC 1st Position - Federal Board")
    <input type="text" name="achievement" value="<?= e($editing['achievement'] ?? '') ?>">
  </label>
  <label>Batch / Class (e.g. "Class of 2025")
    <input type="text" name="batch_info" value="<?= e($editing['batch_info'] ?? '') ?>">
  </label>
  <label>Story (leave blank for achiever band entries; fill in for alumni story wall entries)
    <textarea name="story" rows="5"><?= e($editing['story'] ?? '') ?></textarea>
  </label>
  <label>Private Contact (not shown publicly)
    <input type="text" name="contact" value="<?= e($editing['contact'] ?? '') ?>">
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Published
  </label>
  <label>Photo
    <?php if (!empty($editing['photo'])): ?>
      <div><img src="../<?= e($editing['photo']) ?>" style="max-height:80px;"></div>
    <?php endif; ?>
    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit"><?= $editing ? 'Update' : 'Add Entry' ?></button>
  <?php if ($editing): ?><a href="alumni.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<h2>Published</h2>
<table class="admin-table">
  <thead><tr><th>Name</th><th>Batch</th><th>Type</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($published as $p): ?>
      <tr>
        <td><?= e($p['name']) ?></td>
        <td><?= e($p['batch_info']) ?></td>
        <td><?= $p['story'] ? 'Story' : 'Achiever' ?></td>
        <td>
          <a href="alumni.php?edit=<?= (int)$p['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this entry?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
