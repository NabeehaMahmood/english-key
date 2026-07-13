<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'update_status') {
        $status = $_POST['status'] ?? 'new';
        $allowed = ['new', 'contacted', 'enrolled', 'declined'];
        if (in_array($status, $allowed, true)) {
            $db->prepare('UPDATE enrollments SET status = ? WHERE id = ?')->execute([$status, $id]);
        }
        redirectWithMessage('enrollments.php', 'Enrollment updated.');
    }

    if ($action === 'delete') {
        $db->prepare('DELETE FROM enrollments WHERE id = ?')->execute([$id]);
        redirectWithMessage('enrollments.php', 'Enrollment deleted.');
    }
}

$enrollments = $db->query('SELECT * FROM enrollments ORDER BY submitted_at DESC')->fetchAll();
?>
<h1>Enrollments</h1>

<table class="admin-table">
  <thead>
    <tr><th>Student</th><th>Contact</th><th>Class</th><th>Subjects</th><th>Programme</th><th>Submitted</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($enrollments as $en): ?>
      <tr>
        <td><?= e($en['student_name']) ?><?php if ($en['guardian_name']): ?><br><small>Guardian: <?= e($en['guardian_name']) ?></small><?php endif; ?></td>
        <td><?= e($en['phone']) ?><?php if ($en['whatsapp']): ?><br>WA: <?= e($en['whatsapp']) ?><?php endif; ?><?php if ($en['email']): ?><br><?= e($en['email']) ?><?php endif; ?></td>
        <td><?= e($en['class_level']) ?><?php if ($en['board']): ?><br><small><?= e($en['board']) ?></small><?php endif; ?></td>
        <td><?= e($en['subjects']) ?></td>
        <td><?= e($en['programme']) ?><?php if ($en['preferred_start']): ?><br><small><?= e($en['preferred_start']) ?></small><?php endif; ?></td>
        <td><?= e(date('M j, Y g:ia', strtotime($en['submitted_at']))) ?></td>
        <td>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= (int)$en['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <?php foreach (['new', 'contacted', 'enrolled', 'declined'] as $status): ?>
                <option value="<?= e($status) ?>" <?= $en['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
        <td>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this enrollment?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$en['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$enrollments): ?>
      <tr><td colspan="8">No enrollments yet.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
