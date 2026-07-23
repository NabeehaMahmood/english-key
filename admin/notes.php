<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$contentTypes = ['prose' => 'Prose', 'poetry' => 'Poetry', 'other' => 'Notes'];
$iconOptions = ['list' => 'List', 'lightning' => 'Lightning', 'compass' => 'Compass', 'document' => 'Document', 'book' => 'Book', 'target' => 'Target', 'star-badge' => 'Star Badge', 'folder' => 'Folder'];
$classLevels = array_column($db->query('SELECT class_level FROM note_classes ORDER BY sort_order, class_level')->fetchAll(), 'class_level');
$classLevels = array_map('intval', $classLevels);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_sample') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare('SELECT file_path FROM note_samples WHERE id = ?');
        $stmt->execute([$id]);
        $filePath = $stmt->fetchColumn();

        $db->prepare('DELETE FROM note_samples WHERE id = ?')->execute([$id]);
        if ($filePath) {
            @unlink(__DIR__ . '/../' . $filePath);
        }
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
        $stmt = $db->prepare('SELECT id FROM note_classes WHERE class_level = ?');
        $stmt->execute([$classLevel]);
        if (!$stmt->fetch()) {
            redirectWithMessage($backTo, 'Choose a valid class (add one under Manage Classes first if the list is empty).', 'error');
        }

        // Subject is optional -- leave it unset for classes that don't use
        // subjects (MDCAT, Others, ...); pick one for classes that do.
        $subjectId = null;
        if (!empty($_POST['subject_id'])) {
            $subjectId = (int)$_POST['subject_id'];
            $stmt = $db->prepare('SELECT id FROM note_subjects WHERE id = ?');
            $stmt->execute([$subjectId]);
            if (!$stmt->fetch()) {
                redirectWithMessage($backTo, 'Choose a valid subject (add one under Manage Subjects first if the list is empty).', 'error');
            }
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
                $oldFileStmt = $db->prepare('SELECT file_path FROM note_samples WHERE id = ?');
                $oldFileStmt->execute([$id]);
                $oldFilePath = $oldFileStmt->fetchColumn();

                $db->prepare('UPDATE note_samples SET class_level=?, subject_id=?, title=?, chapter_label=?, content_type=?, description=?, file_path=?, sort_order=?, status=? WHERE id=?')
                   ->execute([$classLevel, $subjectId, $title, $chapterLabel, $contentType, $description, $filePath, $sortOrder, $status, $id]);

                if ($oldFilePath && $oldFilePath !== $filePath) {
                    @unlink(__DIR__ . '/../' . $oldFilePath);
                }
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

    if ($action === 'save_class_subjects') {
        $validSubjectIds = array_column($db->query('SELECT id FROM note_subjects')->fetchAll(), 'id');

        $db->beginTransaction();
        foreach ($classLevels as $c) {
            $selected = array_map('intval', $_POST['class_subjects'][$c] ?? []);
            $selected = array_values(array_intersect($selected, $validSubjectIds));

            $db->prepare('DELETE FROM note_class_subjects WHERE class_level = ?')->execute([$c]);
            if ($selected) {
                $stmt = $db->prepare('INSERT INTO note_class_subjects (class_level, subject_id) VALUES (?, ?)');
                foreach ($selected as $subjectId) {
                    $stmt->execute([$c, $subjectId]);
                }
            }
        }
        $db->commit();
        redirectWithMessage('notes.php?tab=class_subjects', 'Class subjects updated.');
    }

    if ($action === 'delete_class') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT class_level FROM note_classes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            redirectWithMessage('notes.php?tab=classes&subtab=list', 'Class not found.', 'error');
        }
        $cl = (int)$row['class_level'];
        $stmt = $db->prepare('SELECT COUNT(*) FROM note_samples WHERE class_level = ?');
        $stmt->execute([$cl]);
        $inUse = (int)$stmt->fetchColumn() > 0;
        if (!$inUse) {
            $stmt = $db->prepare('SELECT COUNT(*) FROM note_class_subjects WHERE class_level = ?');
            $stmt->execute([$cl]);
            $inUse = (int)$stmt->fetchColumn() > 0;
        }
        if ($inUse) {
            redirectWithMessage('notes.php?tab=classes&subtab=list', 'This class still has samples or subject assignments. Remove those first, or just hide the class instead of deleting it.', 'error');
        }
        $db->prepare('DELETE FROM note_classes WHERE id = ?')->execute([$id]);
        redirectWithMessage('notes.php?tab=classes&subtab=list', 'Class deleted.');
    }

    if ($action === 'toggle_class') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT is_active FROM note_classes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            redirectWithMessage('notes.php?tab=classes&subtab=list', 'Class not found.', 'error');
        }
        $newState = $row['is_active'] ? 0 : 1;
        $db->prepare('UPDATE note_classes SET is_active = ? WHERE id = ?')->execute([$newState, $id]);
        redirectWithMessage('notes.php?tab=classes&subtab=list', $newState ? 'Class shown on the site.' : 'Class hidden from the site.');
    }

    if ($action === 'save_class') {
        $id = (int)($_POST['id'] ?? 0);
        $backTo = 'notes.php?tab=classes&subtab=add' . ($id ? '&edit_class=' . $id : '');

        $label = trim($_POST['label'] ?? '');
        $examLabel = trim($_POST['exam_label'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $iconKey = in_array($_POST['icon_key'] ?? '', array_keys($iconOptions), true) ? $_POST['icon_key'] : 'document';
        $ctaLabel = trim($_POST['cta_label'] ?? '') ?: 'Enroll & Get Complete Notes';
        $ctaLink = trim($_POST['cta_link'] ?? '') ?: 'courses.php';
        $accentColor = trim($_POST['accent_color'] ?? '') ?: '#1E2A66';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($label === '') {
            redirectWithMessage($backTo, 'Label is required.', 'error');
        }

        if ($id > 0) {
            // class_level itself isn't editable once created -- note_samples
            // and note_class_subjects reference it directly, so changing it
            // here would silently orphan existing data.
            $db->prepare('UPDATE note_classes SET label=?, exam_label=?, description=?, icon_key=?, cta_label=?, cta_link=?, accent_color=?, sort_order=?, is_active=? WHERE id=?')
               ->execute([$label, $examLabel, $description, $iconKey, $ctaLabel, $ctaLink, $accentColor, $sortOrder, $isActive, $id]);
            redirectWithMessage('notes.php?tab=classes&subtab=list', 'Class updated.');
        } else {
            $classLevel = (int)($_POST['class_level'] ?? 0);
            if ($classLevel < 1) {
                redirectWithMessage($backTo, 'Class level must be a positive number.', 'error');
            }
            $stmt = $db->prepare('SELECT id FROM note_classes WHERE class_level = ?');
            $stmt->execute([$classLevel]);
            if ($stmt->fetch()) {
                redirectWithMessage($backTo, "Class level $classLevel already exists.", 'error');
            }
            $db->prepare('INSERT INTO note_classes (class_level, label, exam_label, description, icon_key, cta_label, cta_link, accent_color, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)')
               ->execute([$classLevel, $label, $examLabel, $description, $iconKey, $ctaLabel, $ctaLink, $accentColor, $sortOrder, $isActive]);
            redirectWithMessage('notes.php?tab=classes&subtab=list', 'Class added.');
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

$editingClass = null;
if (isset($_GET['edit_class'])) {
    $stmt = $db->prepare('SELECT * FROM note_classes WHERE id = ?');
    $stmt->execute([(int)$_GET['edit_class']]);
    $editingClass = $stmt->fetch() ?: null;
}

$activeTab = in_array($_GET['tab'] ?? '', ['classes', 'subjects', 'class_subjects', 'add', 'list'], true) ? $_GET['tab'] : 'classes';
$activeSubTab = in_array($_GET['subtab'] ?? '', ['add', 'list'], true) ? $_GET['subtab'] : 'add';

$allClasses = $db->query('SELECT * FROM note_classes ORDER BY sort_order, class_level')->fetchAll();
$classLabelByLevel = [];
foreach ($allClasses as $cl) {
    $classLabelByLevel[(int)$cl['class_level']] = $cl['label'];
}

$allSubjects = $db->query('SELECT * FROM note_subjects ORDER BY sort_order, name')->fetchAll();
$classSubjectIds = [];
foreach ($classLevels as $c) {
    $classSubjectIds[$c] = [];
}
foreach ($db->query('SELECT class_level, subject_id FROM note_class_subjects')->fetchAll() as $row) {
    $classSubjectIds[(int)$row['class_level']][] = (int)$row['subject_id'];
}

$samples = $db->query(
    "SELECT ns.*, sub.name AS subject_name FROM note_samples ns
     LEFT JOIN note_subjects sub ON sub.id = ns.subject_id
     ORDER BY ns.class_level, sub.sort_order, ns.sort_order, ns.id"
)->fetchAll();
?>
<h1>Notes</h1>
<p class="admin-page-intro">Manage the free sample PDFs shown on the public Notes page, the classes in its primary nav (numbered classes like "Class 9" and flat classes like "MDCAT English Prep" or "Others"), the subject list, and which subjects apply to which class.</p>

<div class="cms-admin">
<div class="cms-tabbar">
  <button type="button" class="cms-tab<?= $activeTab === 'classes' ? ' active' : '' ?>" data-tab="classes">Manage Classes (<?= count($allClasses) ?>)</button>
  <button type="button" class="cms-tab<?= $activeTab === 'subjects' ? ' active' : '' ?>" data-tab="subjects">Manage Subjects (<?= count($allSubjects) ?>)</button>
  <button type="button" class="cms-tab<?= $activeTab === 'class_subjects' ? ' active' : '' ?>" data-tab="class_subjects">Class Subjects</button>
  <button type="button" class="cms-tab<?= $activeTab === 'add' ? ' active' : '' ?>" data-tab="add"><?= $editingSample ? 'Edit Sample' : 'Add Sample' ?></button>
  <button type="button" class="cms-tab<?= $activeTab === 'list' ? ' active' : '' ?>" data-tab="list">All Samples (<?= count($samples) ?>)</button>
</div>

<div data-tab-panel="classes"<?= $activeTab === 'classes' ? '' : ' hidden' ?>>

<div class="cms-tabbar cms-tabbar-nested">
  <button type="button" class="cms-tab<?= $activeSubTab === 'add' ? ' active' : '' ?>" data-subtab="add"><?= $editingClass ? 'Edit Class' : 'Add Class' ?></button>
  <button type="button" class="cms-tab<?= $activeSubTab === 'list' ? ' active' : '' ?>" data-subtab="list">All Classes (<?= count($allClasses) ?>)</button>
</div>

<div data-subtab-panel="add"<?= $activeSubTab === 'add' ? '' : ' hidden' ?>>
<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_class">
  <input type="hidden" name="id" value="<?= (int)($editingClass['id'] ?? 0) ?>">

  <h2><?= $editingClass ? 'Edit Class' : 'Add Class' ?></h2>
  <p class="hint">A class is one button in the primary nav on the public Notes page -- "Class 9", "MDCAT English Prep", "Others", all the same thing. Assign it subjects under <a href="notes.php?tab=class_subjects">Class Subjects</a> if it needs them (like Class 9-12); leave it without any and it just lists its own samples directly, no subject breakdown.</p>

  <?php if ($editingClass): ?>
    <label>Class Level
      <input type="text" value="<?= (int)$editingClass['class_level'] ?>" disabled>
    </label>
    <p class="hint" style="margin-top:-10px">Class level can't be changed once created -- existing samples and subject assignments reference it directly. Delete and re-add if you really need a different number (only works while nothing uses it yet).</p>
  <?php else: ?>
    <label>Class Level (a number -- 9-12 for academic classes, or any unused number for anything else)
      <input type="number" name="class_level" min="1" required>
    </label>
  <?php endif; ?>
  <label>Label (shown in the nav and section headings, e.g. "Class 9" or "MDCAT English Prep")
    <input type="text" name="label" value="<?= e($editingClass['label'] ?? '') ?>" required>
  </label>
  <label>Exam Label (shown as a small badge, e.g. "SSC-I", optional)
    <input type="text" name="exam_label" value="<?= e($editingClass['exam_label'] ?? '') ?>">
  </label>
  <label>Description (one sentence shown under the class name, optional)
    <textarea name="description" rows="2"><?= e($editingClass['description'] ?? '') ?></textarea>
  </label>
  <label>Icon (only used if this class has no subjects assigned)
    <select name="icon_key">
      <?php foreach ($iconOptions as $val => $label): ?>
        <option value="<?= e($val) ?>" <?= ($editingClass['icon_key'] ?? 'document') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>CTA Button Text
    <input type="text" name="cta_label" value="<?= e($editingClass['cta_label'] ?? 'Enroll & Get Complete Notes') ?>">
  </label>
  <label>CTA Button Link (e.g. courses.php, or courses.php#programmes)
    <input type="text" name="cta_link" value="<?= e($editingClass['cta_link'] ?? 'courses.php') ?>">
  </label>
  <label>Accent Color
    <input type="color" name="accent_color" value="<?= e($editingClass['accent_color'] ?? '#1E2A66') ?>">
  </label>
  <label>Sort Order (this class's position relative to other classes, both in page content and in the primary nav -- e.g. raise this to push "Others" further right)
    <input type="number" name="sort_order" value="<?= (int)($editingClass['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!$editingClass || $editingClass['is_active']) ? 'checked' : '' ?>>
    Shown on site
  </label>

  <div class="admin-form-actions">
    <button type="submit"><?= $editingClass ? 'Update Class' : 'Add Class' ?></button>
    <?php if ($editingClass): ?><a href="notes.php?tab=classes&subtab=list" class="button-secondary">Cancel</a><?php endif; ?>
  </div>
</form>
</div>

<div data-subtab-panel="list"<?= $activeSubTab === 'list' ? '' : ' hidden' ?>>
<div class="cms-table-scroll">
<table class="admin-table">
  <thead>
    <tr><th>Label</th><th>Subjects</th><th>Sort</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($allClasses as $cl): $subjCount = count($classSubjectIds[(int)$cl['class_level']] ?? []); ?>
      <tr>
        <td><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:<?= e($cl['accent_color']) ?>;vertical-align:-1px;margin-right:6px;border:1px solid #ccc"></span><?= e($cl['label']) ?></td>
        <td><?= $subjCount ?: '—' ?></td>
        <td><?= (int)$cl['sort_order'] ?></td>
        <td><span class="status-badge status-<?= $cl['is_active'] ? 'published' : 'draft' ?>"><?= $cl['is_active'] ? 'Shown' : 'Hidden' ?></span></td>
        <td class="actions-cell">
          <a href="notes.php?tab=classes&subtab=add&edit_class=<?= (int)$cl['id'] ?>">Edit</a>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle_class">
            <input type="hidden" name="id" value="<?= (int)$cl['id'] ?>">
            <button type="submit" class="link-button"><?= $cl['is_active'] ? 'Hide' : 'Show' ?></button>
          </form>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this class? This only works if it has no samples or subject assignments.');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete_class">
            <input type="hidden" name="id" value="<?= (int)$cl['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$allClasses): ?><tr><td colspan="6">No classes yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>
</div>
</div>

<div data-tab-panel="subjects"<?= $activeTab === 'subjects' ? '' : ' hidden' ?>>

<div class="cms-tabbar cms-tabbar-nested">
  <button type="button" class="cms-tab<?= $activeSubTab === 'add' ? ' active' : '' ?>" data-subtab="add"><?= $editingSubject ? 'Edit Subject' : 'Add Subject' ?></button>
  <button type="button" class="cms-tab<?= $activeSubTab === 'list' ? ' active' : '' ?>" data-subtab="list">All Subjects (<?= count($allSubjects) ?>)</button>
</div>

<div data-subtab-panel="add"<?= $activeSubTab === 'add' ? '' : ' hidden' ?>>
<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_subject">
  <input type="hidden" name="id" value="<?= (int)($editingSubject['id'] ?? 0) ?>">

  <h2><?= $editingSubject ? 'Edit Subject' : 'Add Subject' ?></h2>
  <p class="hint">Subjects appear as tabs under every has-subjects class on the public Notes page (e.g. English, Urdu, Islamiat, Tarjuma-tul-Quran).</p>

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
<div class="cms-table-scroll">
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
</div>

<div data-tab-panel="class_subjects"<?= $activeTab === 'class_subjects' ? '' : ' hidden' ?>>
<?php if (!$allSubjects || !$allClasses): ?>
  <p class="hint">Add at least one class under "Manage Classes" and one subject under "Manage Subjects" before assigning subjects to a class.</p>
<?php else: ?>
<form method="post" class="admin-form cms-matrix-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_class_subjects">

  <h2>Class Subjects</h2>
  <p class="hint">Check a box where a subject applies to a class, e.g. Islamiat for Classes 9 &amp; 11, Pakistan Studies for 10 &amp; 12. Unchecking a box doesn't delete any samples, it just stops showing that class+subject combo on the site. A class with nothing checked just lists its samples directly on the public page instead, no subject sub-nav -- check a box for it any time to give it one.</p>

  <div class="cs-matrix-wrap">
    <table class="cs-matrix">
      <thead>
        <tr>
          <th class="cs-matrix-corner">Subject</th>
          <?php foreach ($allClasses as $cl): ?>
            <th style="--c:<?= e($cl['accent_color']) ?>">
              <?= e($cl['label']) ?>
              <?= $cl['is_active'] ? '' : '<br><small>(hidden)</small>' ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($allSubjects as $s): ?>
          <tr>
            <th class="cs-matrix-row-label" style="--c:<?= e($s['accent_color']) ?>"><?= e($s['name']) ?><?= $s['is_active'] ? '' : ' (hidden)' ?></th>
            <?php foreach ($allClasses as $cl): $c = (int)$cl['class_level']; ?>
              <td class="cs-matrix-cell">
                <input type="checkbox" name="class_subjects[<?= $c ?>][]" value="<?= (int)$s['id'] ?>" style="accent-color:<?= e($s['accent_color']) ?>" <?= in_array((int)$s['id'], $classSubjectIds[$c] ?? [], true) ? 'checked' : '' ?> aria-label="<?= e($s['name'] . ' for ' . $cl['label']) ?>">
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="admin-form-actions">
    <button type="submit">Save Class Subjects</button>
  </div>
</form>
<?php endif; ?>
</div>

<div data-tab-panel="add"<?= $activeTab === 'add' ? '' : ' hidden' ?>>
<?php if (!$allClasses): ?>
  <p class="hint">Add at least one class under "Manage Classes" before adding a sample.</p>
<?php else: ?>
<form method="post" enctype="multipart/form-data" class="admin-form" id="sampleForm">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save_sample">
  <input type="hidden" name="id" value="<?= (int)($editingSample['id'] ?? 0) ?>">

  <h2><?= $editingSample ? 'Edit Sample' : 'Add Sample' ?></h2>
  <p class="hint">A free sample PDF for one class. Pick a subject too if that class uses subjects. Shown as a Preview card on the public Notes page.</p>

  <label>Class
    <select name="class_level" id="sampleClassSelect" required>
      <option value="">Select class</option>
      <?php foreach ($allClasses as $cl): ?>
        <option value="<?= (int)$cl['class_level'] ?>" <?= (int)($editingSample['class_level'] ?? 0) === (int)$cl['class_level'] ? 'selected' : '' ?>><?= e($cl['label']) ?><?= $cl['is_active'] ? '' : ' (hidden)' ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label id="sampleSubjectField">
    Subject (optional)
    <select name="subject_id">
      <option value="">— None —</option>
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
<div class="cms-table-scroll">
<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Class</th><th>Subject</th><th>Type</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($samples as $sample): ?>
      <tr>
        <td><?= e($sample['title']) ?><?= $sample['chapter_label'] ? ' <span style="color:#888">(' . e($sample['chapter_label']) . ')</span>' : '' ?></td>
        <td><?= e($classLabelByLevel[(int)$sample['class_level']] ?? ('Class ' . (int)$sample['class_level'])) ?></td>
        <td><?= e($sample['subject_name'] ?? '—') ?></td>
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
</div>
</div><!-- /.cms-admin -->

<script>
(function () {
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

  wireTabs('.cms-tab[data-tab]', '[data-tab-panel]', 'tab', 'data-tab-panel');
  wireTabs('.cms-tab[data-subtab]', '[data-subtab-panel]', 'subtab', 'data-subtab-panel');

  // Landing directly on e.g. "All Samples" (via a saved link, or after a
  // form redirect) can leave that tab scrolled out of view in the
  // horizontally-scrolling bar, with no visible indicator of which tab is
  // actually active until the admin scrolls it into view themselves.
  document.querySelectorAll('.cms-tabbar').forEach(function (bar) {
    var active = bar.querySelector('.cms-tab.active');
    if (active) active.scrollIntoView({ inline: 'nearest', block: 'nearest' });
  });
})();
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
