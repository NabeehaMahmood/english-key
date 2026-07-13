<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM notes WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('notes.php', 'Note deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subjectTag = trim($_POST['subject_tag'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '') {
            redirectWithMessage('notes.php', 'Title is required.', 'error');
        }

        if ($id > 0) {
            $db->prepare('UPDATE notes SET title=?, subject_tag=?, description=?, link=?, sort_order=?, is_active=? WHERE id=?')
               ->execute([$title, $subjectTag, $description, $link, $sortOrder, $isActive, $id]);
            redirectWithMessage('notes.php', 'Note updated.');
        } else {
            $db->prepare('INSERT INTO notes (title, subject_tag, description, link, sort_order, is_active) VALUES (?,?,?,?,?,?)')
               ->execute([$title, $subjectTag, $description, $link, $sortOrder, $isActive]);
            redirectWithMessage('notes.php', 'Note added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM notes WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$notes = $db->query('SELECT * FROM notes ORDER BY sort_order, id')->fetchAll();
?>
<h1>Notes</h1>
<p>Free downloadable/requestable resources shown on the Notes page.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Note' : 'Add Note' ?></h2>

  <label>Title
    <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
  </label>
  <label>Subject Tag (e.g. "Class 9 - Islamiat")
    <input type="text" name="subject_tag" value="<?= e($editing['subject_tag'] ?? '') ?>">
  </label>
  <label>Description
    <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
  </label>
  <label>Link (WhatsApp request link, file URL, etc.)
    <input type="text" name="link" value="<?= e($editing['link'] ?? '') ?>">
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>

  <button type="submit"><?= $editing ? 'Update Note' : 'Add Note' ?></button>
  <?php if ($editing): ?><a href="notes.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Subject Tag</th><th>Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($notes as $n): ?>
      <tr>
        <td><?= e($n['title']) ?></td>
        <td><?= e($n['subject_tag']) ?></td>
        <td><?= $n['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="notes.php?edit=<?= (int)$n['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this note?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
