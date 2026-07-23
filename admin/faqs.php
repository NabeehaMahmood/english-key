<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM faqs WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('faqs.php', 'FAQ deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($question === '' || $answer === '') {
            redirectWithMessage('faqs.php', 'Both the question and the answer are required.', 'error');
        }

        if ($id > 0) {
            $db->prepare("UPDATE faqs SET question=?, answer=?, sort_order=?, is_active=? WHERE id=?")
               ->execute([$question, $answer, $sortOrder, $isActive, $id]);
            redirectWithMessage('faqs.php', 'FAQ updated.');
        } else {
            $db->prepare("INSERT INTO faqs (page_slug, question, answer, sort_order, is_active) VALUES ('courses',?,?,?,?)")
               ->execute([$question, $answer, $sortOrder, $isActive]);
            redirectWithMessage('faqs.php', 'FAQ added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM faqs WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$faqs = $db->query("SELECT * FROM faqs WHERE page_slug = 'courses' ORDER BY sort_order, id")->fetchAll();
?>
<h1>Enroll Page FAQs</h1>
<p class="admin-page-intro">The Frequently Asked Questions accordion on the public Enroll page. Add, edit, reorder with Sort Order, and hide with Visible.</p>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit FAQ' : 'Add FAQ' ?></h2>

  <label>Question
    <input type="text" name="question" value="<?= e($editing['question'] ?? '') ?>" required>
  </label>
  <label>Answer
    <textarea name="answer" rows="4" required><?= e($editing['answer'] ?? '') ?></textarea>
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!$editing || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>

  <button type="submit"><?= $editing ? 'Update FAQ' : 'Add FAQ' ?></button>
  <?php if ($editing): ?><a href="faqs.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Question</th><th>Answer</th><th class="col-center">Sort</th><th class="col-center">Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($faqs as $f): ?>
      <tr>
        <td class="cell-title"><?= e($f['question']) ?></td>
        <td><?= e(mb_strimwidth($f['answer'], 0, 110, '...')) ?></td>
        <td class="col-center"><?= (int)$f['sort_order'] ?></td>
        <td class="col-center"><?= $f['is_active'] ? 'Yes' : 'No' ?></td>
        <td class="actions-cell">
          <a href="faqs.php?edit=<?= (int)$f['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this FAQ?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$faqs): ?><tr><td colspan="5" class="admin-table-empty">No FAQs yet — add one using the form above.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
