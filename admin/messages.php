<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'mark_read') {
        $db->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$id]);
        redirectWithMessage('messages.php', 'Marked as read.');
    }

    if ($action === 'delete') {
        $db->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
        redirectWithMessage('messages.php', 'Message deleted.');
    }
}

$messages = $db->query('SELECT * FROM contact_messages ORDER BY submitted_at DESC')->fetchAll();
?>
<h1>Contact Messages</h1>

<table class="admin-table">
  <thead>
    <tr><th>Name</th><th>Contact</th><th>Subject</th><th>Message</th><th>Submitted</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($messages as $msg): ?>
      <tr class="<?= $msg['is_read'] ? '' : 'unread-row' ?>">
        <td><?= e($msg['name']) ?></td>
        <td><?= e($msg['email']) ?><br><?= e($msg['phone']) ?></td>
        <td><?= e($msg['subject']) ?></td>
        <td><?= nl2br(e($msg['message'])) ?></td>
        <td><?= e(date('M j, Y g:ia', strtotime($msg['submitted_at']))) ?></td>
        <td><?= $msg['is_read'] ? 'Read' : 'Unread' ?></td>
        <td class="actions-cell">
          <?php if (!$msg['is_read']): ?>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="mark_read">
            <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
            <button type="submit" class="link-button">Mark Read</button>
          </form>
          <?php endif; ?>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this message?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$messages): ?>
      <tr><td colspan="7">No messages yet.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
