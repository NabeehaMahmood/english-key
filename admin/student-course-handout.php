<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Only one handout row is ever kept - "replace" means updating this same
// row (and swapping its file on disk) rather than inserting a new one.
$fetchHandout = static function () use ($db): ?array {
    return $db->query('SELECT * FROM course_handouts ORDER BY id DESC LIMIT 1')->fetch() ?: null;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $existing = $fetchHandout();

    if ($action === 'delete') {
        if ($existing) {
            $oldPath = __DIR__ . '/../' . $existing['file_path'];
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
            $db->prepare('DELETE FROM course_handouts WHERE id = ?')->execute([$existing['id']]);
        }
        redirectWithMessage('student-course-handout.php', 'Handout deleted.');
    }

    if ($action === 'toggle') {
        if ($existing) {
            $db->prepare('UPDATE course_handouts SET is_active = ? WHERE id = ?')
               ->execute([$existing['is_active'] ? 0 : 1, $existing['id']]);
            redirectWithMessage('student-course-handout.php', $existing['is_active'] ? 'Handout disabled.' : 'Handout enabled.');
        }
        redirectWithMessage('student-course-handout.php', 'No handout to update.', 'error');
    }

    if ($action === 'save') {
        $title = trim($_POST['title'] ?? '') ?: 'Course Handout';
        $description = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        try {
            $upload = handlePdfUpload('handout_file', 'handouts');
        } catch (RuntimeException $e) {
            redirectWithMessage('student-course-handout.php', $e->getMessage(), 'error');
        }

        if (!$existing && !$upload) {
            redirectWithMessage('student-course-handout.php', 'Please choose a PDF file to upload.', 'error');
        }

        if ($existing) {
            if ($upload) {
                $oldPath = __DIR__ . '/../' . $existing['file_path'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
                $db->prepare('UPDATE course_handouts SET title=?, description=?, file_path=?, original_filename=?, file_size=?, is_active=? WHERE id=?')
                   ->execute([$title, $description, $upload['path'], $upload['original_filename'], $upload['size'], $isActive, $existing['id']]);
                redirectWithMessage('student-course-handout.php', 'Handout replaced.');
            } else {
                $db->prepare('UPDATE course_handouts SET title=?, description=?, is_active=? WHERE id=?')
                   ->execute([$title, $description, $isActive, $existing['id']]);
                redirectWithMessage('student-course-handout.php', 'Handout updated.');
            }
        } else {
            $db->prepare('INSERT INTO course_handouts (title, description, file_path, original_filename, file_size, is_active) VALUES (?,?,?,?,?,?)')
               ->execute([$title, $description, $upload['path'], $upload['original_filename'], $upload['size'], $isActive]);
            redirectWithMessage('student-course-handout.php', 'Handout uploaded.');
        }
    }
}

$handout = $fetchHandout();
?>
<h1>Student Course Handout</h1>
<p>Manage the single PDF course outline/handout linked from the "View Course Outline" and "Download Course Outline" buttons on the public Courses page. Uploading a new file automatically replaces the current one.</p>

<form method="post" enctype="multipart/form-data" class="admin-form" id="handout-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">

  <h2><?= $handout ? 'Edit Handout' : 'Upload Handout' ?></h2>

  <label>Title
    <input type="text" name="title" value="<?= e($handout['title'] ?? 'Course Handout') ?>" required>
  </label>
  <label>Short Description
    <textarea name="description" rows="3" placeholder="e.g. Download or view the latest detailed course outline, syllabus and study structure."><?= e($handout['description'] ?? '') ?></textarea>
  </label>
  <label id="handout-file">PDF File<?= $handout ? ' (leave empty to keep the current file)' : '' ?>
    <?php if ($handout): ?>
      <span class="hint">Current file: <?= e($handout['original_filename']) ?> (<?= formatFileSize((int)$handout['file_size']) ?>)</span>
    <?php endif; ?>
    <input type="file" name="handout_file" accept="application/pdf,.pdf" <?= $handout ? '' : 'required' ?>>
    <span class="hint">PDF only, max 20MB.</span>
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!$handout || $handout['is_active']) ? 'checked' : '' ?>>
    Enabled (visible on the Courses page)
  </label>

  <button type="submit"><?= $handout ? 'Save Changes' : 'Upload Handout' ?></button>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Description</th><th>Filename</th><th>Size</th><th class="col-center">Status</th><th>Updated</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php if ($handout): ?>
      <tr>
        <td class="cell-title"><?= e($handout['title']) ?></td>
        <td><?= e($handout['description']) ?></td>
        <td><?= e($handout['original_filename']) ?></td>
        <td><?= formatFileSize((int)$handout['file_size']) ?></td>
        <td class="col-center">
          <span class="status-badge <?= $handout['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $handout['is_active'] ? 'Enabled' : 'Disabled' ?></span>
        </td>
        <td><?= e(date('d M Y, H:i', strtotime($handout['updated_at']))) ?></td>
        <td class="actions-cell">
          <a href="../<?= e($handout['file_path']) ?>" target="_blank" rel="noopener">View</a>
          <a href="../<?= e($handout['file_path']) ?>" download="<?= e($handout['original_filename']) ?>">Download</a>
          <a href="student-course-handout.php#handout-form">Edit</a>
          <a href="student-course-handout.php#handout-file">Replace</a>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle">
            <button type="submit" class="link-button"><?= $handout['is_active'] ? 'Disable' : 'Enable' ?></button>
          </form>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this handout? This cannot be undone.');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php else: ?>
      <tr><td colspan="7" class="admin-table-empty">No handout uploaded yet — upload one using the form above.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
