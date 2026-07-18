<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$contentTypes = ['prose' => 'Prose', 'poetry' => 'Poetry', 'other' => 'Notes'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_sample') {
        $db->prepare('DELETE FROM note_samples WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('notes.php?tab=list', 'Sample deleted.');
    }

    if ($action === 'toggle_sample') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT status FROM note_samples WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            redirectWithMessage('notes.php?tab=list', 'Sample not found.', 'error');
        }

        $newStatus = $row['status'] === 'published' ? 'draft' : 'published';
        $db->prepare('UPDATE note_samples SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
        redirectWithMessage('notes.php?tab=list', $newStatus === 'published' ? 'Sample published.' : 'Sample unpublished and moved to drafts.');
    }

    if ($action === 'save_sample') {
        $id = (int)($_POST['id'] ?? 0);
        $backTo = 'notes.php' . ($id ? '?edit=' . $id : '');

        $classLevel = (int)($_POST['class_level'] ?? 0);
        if (!in_array($classLevel, [9, 10, 11, 12], true)) {
            redirectWithMessage($backTo, 'Choose a valid class (9-12).', 'error');
        }

        $subjectId = (int)($_POST['subject_id'] ?? 0);
        $stmt = $db->prepare('SELECT id FROM note_subjects WHERE id = ?');
        $stmt->execute([$subjectId]);
        if (!$stmt->fetch()) {
            redirectWithMessage($backTo, 'Choose a valid subject (add one under Manage Subjects first if the list is empty).', 'error');
        }

        $title = trim($_POST['title'] ?? '');
        $chapterLabel = trim($_POST['chapter_label'] ?? '');
        $contentType = array_key_exists($_POST['content_type'] ?? '', $contentTypes) ? $_POST['content_type'] : 'other';
        $description = trim($_POST['description'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';

        if ($title === '') {
            redirectWithMessage($backTo, 'Title is required.', 'error');
        }

        try {
            $filePath = handlePdfUpload('pdf_file', 'notes');
        } catch (RuntimeException $e) {
            redirectWithMessage($backTo, $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($filePath) {
                $db->prepare('UPDATE note_samples SET class_level=?, subject_id=?, title=?, chapter_label=?, content_type=?, description=?, file_path=?, sort_order=?, status=? WHERE id=?')
                   ->execute([$classLevel, $subjectId, $title, $chapterLabel, $contentType, $description, $filePath, $sortOrder, $status, $id]);
            } else {
                $db->prepare('UPDATE note_samples SET class_level=?, subject_id=?, title=?, chapter_label=?, content_type=?, description=?, sort_order=?, status=? WHERE id=?')
                   ->execute([$classLevel, $subjectId, $title, $chapterLabel, $contentType, $description, $sortOrder, $status, $id]);
            }
            redirectWithMessage('notes.php?tab=list', 'Sample updated.');
        } else {
            if (!$filePath) {
                redirectWithMessage($backTo, 'A PDF file is required for a new sample.', 'error');
            }
            $db->prepare('INSERT INTO note_samples (class_level, subject_id, title, chapter_label, content_type, description, file_path, sort_order, status) VALUES (?,?,?,?,?,?,?,?,?)')
               ->execute([$classLevel, $subjectId, $title, $chapterLabel, $contentType, $description, $filePath, $sortOrder, $status]);
            redirectWithMessage('notes.php?tab=list', 'Sample added.');
        }
    }

    if ($action === 'delete_subject') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT COUNT(*) FROM note_samples WHERE subject_id = ?');
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            redirectWithMessage('notes.php?tab=subjects&subtab=list', 'This subject still has samples under it. Delete or reassign those samples first.', 'error');
        }
        $db->prepare('DELETE FROM note_subjects WHERE id = ?')->execute([$id]);
        redirectWithMessage('notes.php?tab=subjects&subtab=list', 'Subject deleted.');
    }

    if ($action === 'toggle_subject') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT is_active FROM note_subjects WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            redirectWithMessage('notes.php?tab=subjects&subtab=list', 'Subject not found.', 'error');
        }

        $newState = $row['is_active'] ? 0 : 1;
        $db->prepare('UPDATE note_subjects SET is_active = ? WHERE id = ?')->execute([$newState, $id]);
        redirectWithMessage('notes.php?tab=subjects&subtab=list', $newState ? 'Subject shown on the site.' : 'Subject hidden from the site.');
    }

    if ($action === 'save_subject') {
        $id = (int)($_POST['id'] ?? 0);
        $backTo = 'notes.php?tab=subjects&subtab=add' . ($id ? '&edit_subject=' . $id : '');

        $name = trim($_POST['name'] ?? '');
        $accentColor = trim($_POST['accent_color'] ?? '') ?: '#1F2B54';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            redirectWithMessage($backTo, 'Subject name is required.', 'error');
        }

        $baseSlug = slugify($name);
        if ($baseSlug === '') {
            $baseSlug = 'subject-' . ($id > 0 ? $id : time());
        }
        $slug = uniqueSubjectSlug($baseSlug, $id);

        if ($id > 0) {
            $db->prepare('UPDATE note_subjects SET name=?, slug=?, accent_color=?, sort_order=?, is_active=? WHERE id=?')
               ->execute([$name, $slug, $accentColor, $sortOrder, $isActive, $id]);
            redirectWithMessage('notes.php?tab=subjects&subtab=list', 'Subject updated.');
        } else {
            $db->prepare('INSERT INTO note_subjects (name, slug, accent_color, sort_order, is_active) VALUES (?,?,?,?,?)')
               ->execute([$name, $slug, $accentColor, $sortOrder, $isActive]);
            redirectWithMessage('notes.php?tab=subjects&subtab=list', 'Subject added.');
        }
    }
}

$editingSample = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM note_samples WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editingSample = $stmt->fetch() ?: null;
}

$editingSubject = null;
if (isset($_GET['edit_subject'])) {
    $stmt = $db->prepare('SELECT * FROM note_subjects WHERE id = ?');
    $stmt->execute([(int)$_GET['edit_subject']]);
    $editingSubject = $stmt->fetch() ?: null;
}

$activeTab = in_array($_GET['tab'] ?? '', ['add', 'list', 'subjects'], true) ? $_GET['tab'] : 'add';
$activeSubTab = in_array($_GET['subtab'] ?? '', ['add', 'list'], true) ? $_GET['subtab'] : 'add';

$allSubjects = $db->query('SELECT * FROM note_subjects ORDER BY sort_order, name')->fetchAll();
$samples = $db->query(
    "SELECT ns.*, sub.name AS subject_name FROM note_samples ns
     JOIN note_subjects sub ON sub.id = ns.subject_id
     ORDER BY ns.class_level, sub.sort_order, ns.sort_order, ns.id"
)->fetchAll();
?>
<h1>Notes</h1>
<p>Manage the free sample PDFs shown on the public Notes page, and the subject tabs (English, Urdu, Islamiat, Tarjuma-tul-Quran, ...) used to filter them. Class tabs (9-12) are fixed.</p>

<div class="admin-tabs">
  <button type="button" class="admin-tab-btn<?= $activeTab === 'add' ? ' active' : '' ?>" data-tab="add"><?= $editingSample ? 'Edit Sample' : 'Add Sample' ?></button>
  <button type="button" class="admin-tab-btn<?= $activeTab === 'list' ? ' active' : '' ?>" data-tab="list">All Samples (<?= count($samples) ?>)</button>
  <button type="button" class="admin-tab-btn<?= $activeTab === 'subjects' ? ' active' : '' ?>" data-tab="subjects">Manage Subjects (<?= count($allSubjects) ?>)</button>
</div>

<div data-tab-panel="add"<?= $activeTab === 'add' ? '' : ' hidden' ?>>
<?php if (!$allSubjects): ?>
  <p class="hint">Add at least one subject under "Manage Subjects" before adding a sample.</p>
<?php else: ?>
<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_sample">
  <input type="hidden" name="id" value="<?= (int)($editingSample['id'] ?? 0) ?>">

  <h2><?= $editingSample ? 'Edit Sample' : 'Add Sample' ?></h2>
  <p class="hint">A free sample chapter/paper for one class + subject. Shown as a Preview card on the public Notes page.</p>

  <label>Class
    <select name="class_level" required>
      <option value="">Select class</option>
      <?php foreach ([9, 10, 11, 12] as $c): ?>
        <option value="<?= $c ?>" <?= (int)($editingSample['class_level'] ?? 0) === $c ? 'selected' : '' ?>>Class <?= $c ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Subject
    <select name="subject_id" required>
      <option value="">Select subject</option>
      <?php foreach ($allSubjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= (int)($editingSample['subject_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?><?= $s['is_active'] ? '' : ' (hidden)' ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Title
    <input type="text" name="title" value="<?= e($editingSample['title'] ?? '') ?>" required>
  </label>
  <label>Chapter / Label (e.g. "Ch 01", optional)
    <input type="text" name="chapter_label" value="<?= e($editingSample['chapter_label'] ?? '') ?>">
  </label>
  <label>Type
    <select name="content_type">
      <?php foreach ($contentTypes as $val => $label): ?>
        <option value="<?= e($val) ?>" <?= ($editingSample['content_type'] ?? 'other') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Description
    <textarea name="description" rows="3"><?= e($editingSample['description'] ?? '') ?></textarea>
  </label>
  <label>Sort Order (lower numbers show first)
    <input type="number" name="sort_order" value="<?= (int)($editingSample['sort_order'] ?? 0) ?>">
  </label>
  <label>PDF File<?= $editingSample ? ' (leave empty to keep the current file)' : '' ?>
    <?php if (!empty($editingSample['file_path'])): ?>
      <div><a href="../<?= e($editingSample['file_path']) ?>" target="_blank" rel="noopener">View current PDF</a></div>
    <?php endif; ?>
    <input type="file" name="pdf_file" accept="application/pdf,.pdf" <?= $editingSample ? '' : 'required' ?>>
  </label>

  <div class="admin-form-actions">
    <button type="submit" name="status" value="draft" class="btn-draft">Save Draft</button>
    <button type="submit" name="status" value="published"><?= ($editingSample['status'] ?? '') === 'published' ? 'Update & Keep Published' : 'Publish' ?></button>
    <?php if ($editingSample): ?><a href="notes.php?tab=list" class="button-secondary">Cancel</a><?php endif; ?>
  </div>
</form>
<?php endif; ?>
</div>

<div data-tab-panel="list"<?= $activeTab === 'list' ? '' : ' hidden' ?>>
<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Class</th><th>Subject</th><th>Type</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($samples as $sample): ?>
      <tr>
        <td><?= e($sample['title']) ?><?= $sample['chapter_label'] ? ' <span style="color:#888">(' . e($sample['chapter_label']) . ')</span>' : '' ?></td>
        <td>Class <?= (int)$sample['class_level'] ?></td>
        <td><?= e($sample['subject_name']) ?></td>
        <td><?= e($contentTypes[$sample['content_type']] ?? $sample['content_type']) ?></td>
        <td><span class="status-badge status-<?= $sample['status'] === 'published' ? 'published' : 'draft' ?>"><?= $sample['status'] === 'published' ? 'Published' : 'Draft' ?></span></td>
        <td class="actions-cell">
          <a href="../<?= e($sample['file_path']) ?>" target="_blank" rel="noopener">View PDF</a>
          <a href="notes.php?edit=<?= (int)$sample['id'] ?>">Edit</a>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle_sample">
            <input type="hidden" name="id" value="<?= (int)$sample['id'] ?>">
            <button type="submit" class="link-button"><?= $sample['status'] === 'published' ? 'Unpublish' : 'Publish' ?></button>
          </form>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this sample?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete_sample">
            <input type="hidden" name="id" value="<?= (int)$sample['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$samples): ?><tr><td colspan="6">No samples yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<div data-tab-panel="subjects"<?= $activeTab === 'subjects' ? '' : ' hidden' ?>>

<div class="admin-tabs">
  <button type="button" class="admin-tab-btn<?= $activeSubTab === 'add' ? ' active' : '' ?>" data-subtab="add"><?= $editingSubject ? 'Edit Subject' : 'Add Subject' ?></button>
  <button type="button" class="admin-tab-btn<?= $activeSubTab === 'list' ? ' active' : '' ?>" data-subtab="list">All Subjects (<?= count($allSubjects) ?>)</button>
</div>

<div data-subtab-panel="add"<?= $activeSubTab === 'add' ? '' : ' hidden' ?>>
<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_subject">
  <input type="hidden" name="id" value="<?= (int)($editingSubject['id'] ?? 0) ?>">

  <h2><?= $editingSubject ? 'Edit Subject' : 'Add Subject' ?></h2>
  <p class="hint">Subjects appear as tabs under every class on the public Notes page (e.g. English, Urdu, Islamiat, Tarjuma-tul-Quran).</p>

  <label>Name
    <input type="text" name="name" value="<?= e($editingSubject['name'] ?? '') ?>" required>
  </label>
  <label>Accent Color (shown as a swatch in the list below, for your own reference)
    <input type="color" name="accent_color" value="<?= e($editingSubject['accent_color'] ?? '#1F2B54') ?>">
  </label>
  <label>Sort Order (lower numbers show first)
    <input type="number" name="sort_order" value="<?= (int)($editingSubject['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!$editingSubject || $editingSubject['is_active']) ? 'checked' : '' ?>>
    Shown on site
  </label>

  <div class="admin-form-actions">
    <button type="submit"><?= $editingSubject ? 'Update Subject' : 'Add Subject' ?></button>
    <?php if ($editingSubject): ?><a href="notes.php?tab=subjects&subtab=list" class="button-secondary">Cancel</a><?php endif; ?>
  </div>
</form>
</div>

<div data-subtab-panel="list"<?= $activeSubTab === 'list' ? '' : ' hidden' ?>>
<table class="admin-table">
  <thead>
    <tr><th>Name</th><th>Color</th><th>Sort</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($allSubjects as $s): ?>
      <tr>
        <td><?= e($s['name']) ?></td>
        <td><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:<?= e($s['accent_color']) ?>;vertical-align:-2px;border:1px solid #ccc"></span></td>
        <td><?= (int)$s['sort_order'] ?></td>
        <td><span class="status-badge status-<?= $s['is_active'] ? 'published' : 'draft' ?>"><?= $s['is_active'] ? 'Shown' : 'Hidden' ?></span></td>
        <td class="actions-cell">
          <a href="notes.php?tab=subjects&subtab=add&edit_subject=<?= (int)$s['id'] ?>">Edit</a>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle_subject">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button type="submit" class="link-button"><?= $s['is_active'] ? 'Hide' : 'Show' ?></button>
          </form>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this subject? This only works if it has no samples.');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete_subject">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$allSubjects): ?><tr><td colspan="5">No subjects yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>
</div>

<script>
(function () {
  // Attribute-qualified selectors so the outer (Add/List/Subjects) tabs and
  // the nested Manage Subjects (Add Subject/All Subjects) tabs -- which
  // share the same .admin-tab-btn styling -- don't pick up each other's
  // buttons/panels.
  function wireTabs(btnSelector, panelSelector, dataKey, panelDataAttr) {
    var buttons = document.querySelectorAll(btnSelector);
    var panels = document.querySelectorAll(panelSelector);
    if (!buttons.length) return;
    var showTab = function (tab) {
      buttons.forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset[dataKey] === tab);
      });
      panels.forEach(function (panel) {
        panel.hidden = panel.getAttribute(panelDataAttr) !== tab;
      });
    };
    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () { showTab(btn.dataset[dataKey]); });
    });
  }

  wireTabs('.admin-tab-btn[data-tab]', '[data-tab-panel]', 'tab', 'data-tab-panel');
  wireTabs('.admin-tab-btn[data-subtab]', '[data-subtab-panel]', 'subtab', 'data-subtab-panel');
})();
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
