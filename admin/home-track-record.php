<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $oldImage = $db->prepare('SELECT image FROM track_records WHERE id = ?');
        $oldImage->execute([(int)$_POST['id']]);
        $oldImage = $oldImage->fetchColumn();
        $db->prepare('DELETE FROM track_records WHERE id = ?')->execute([(int)$_POST['id']]);
        if ($oldImage && !in_array($oldImage, STOCK_AVATARS, true)) {
            deleteUploadedImage($oldImage);
        }
        redirectWithMessage('home-track-record.php#all-records', 'Record deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $year = trim($_POST['year'] ?? '');
        $positionBadge = trim($_POST['position_badge'] ?? '');
        $studentName = trim($_POST['student_name'] ?? '');
        $achievementTitle = trim($_POST['achievement_title'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($year === '' || $positionBadge === '' || $studentName === '' || $achievementTitle === '') {
            redirectWithMessage('home-track-record.php#add-record', 'Year, position badge, student name and achievement title are required.', 'error');
        }

        // Photo choice: real upload, male/female stock avatar, or none.
        try {
            $image = resolvePhotoChoice('photo_choice', 'image', 'gallery');
        } catch (RuntimeException $e) {
            redirectWithMessage('home-track-record.php#add-record', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($image !== null) {
                $oldImage = $db->prepare('SELECT image FROM track_records WHERE id = ?');
                $oldImage->execute([$id]);
                $oldImage = $oldImage->fetchColumn();

                $newImage = $image === '' ? null : $image;

                $db->prepare('UPDATE track_records SET year=?, position_badge=?, student_name=?, achievement_title=?, description=?, image=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$year, $positionBadge, $studentName, $achievementTitle, $description, $newImage, $sortOrder, $isActive, $id]);

                // Stock avatars are shared assets, never deleted from disk.
                if ($oldImage && $oldImage !== $newImage && !in_array($oldImage, STOCK_AVATARS, true)) {
                    deleteUploadedImage($oldImage);
                }
            } else {
                $db->prepare('UPDATE track_records SET year=?, position_badge=?, student_name=?, achievement_title=?, description=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$year, $positionBadge, $studentName, $achievementTitle, $description, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('home-track-record.php#all-records', 'Record updated.');
        } else {
            $db->prepare('INSERT INTO track_records (year, position_badge, student_name, achievement_title, description, image, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?)')
               ->execute([$year, $positionBadge, $studentName, $achievementTitle, $description, $image ?: null, $sortOrder, $isActive]);
            redirectWithMessage('home-track-record.php#all-records', 'Record added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM track_records WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$records = $db->query('SELECT * FROM track_records ORDER BY sort_order, id')->fetchAll();
?>
<div class="admin-tabs-page">
<h1>Results &amp; Toppers</h1>
<p class="admin-page-intro">The "Proven Track Record" achiever cards, shown on the Home page ("Proven Track Record"), the Testimonials page ("Alumnus Corner") and the Alumni page (top band). One shared list &mdash; edit here and every page stays in sync automatically. Each record can have a real photo, a male/female stock avatar, or no photo at all.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Results sections">
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="add-record" role="tab" aria-selected="false"><?= icon('plus', 'tab-icon') ?> <?= $editing ? 'Edit Record' : 'Add Record' ?></button>
  <button type="button" class="admin-tab active" data-tab-group="main" data-tab-target="all-records" role="tab" aria-selected="true"><?= icon('trophy', 'tab-icon') ?> All Records <span class="tab-count"><?= count($records) ?></span></button>
</nav>

<div class="admin-tabpanel" id="add-record" data-tab-group="main" data-tab-id="add-record" hidden>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

    <h2><?= $editing ? 'Edit Record' : 'Add Record' ?></h2>

    <label>Year (e.g. "2025")
      <input type="text" name="year" value="<?= e($editing['year'] ?? '') ?>" required>
    </label>
    <label>Position Badge (e.g. "1st Position")
      <input type="text" name="position_badge" value="<?= e($editing['position_badge'] ?? '1st Position') ?>" required>
    </label>
    <label>Student Name
      <input type="text" name="student_name" value="<?= e($editing['student_name'] ?? '') ?>" required>
    </label>
    <label>Achievement Title (e.g. "HSSC 1st Position - Federal Board")
      <input type="text" name="achievement_title" value="<?= e($editing['achievement_title'] ?? '') ?>" required>
    </label>
    <label>Description (optional, not shown on the current card design; reserved for future use)
      <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>

    <?= renderPhotoChoiceField('photo_choice', 'image', $editing['image'] ?? null, 'Photo (upload a real photo, or pick an avatar if none is available)') ?>

    <button type="submit"><?= $editing ? 'Update' : 'Add Record' ?></button>
    <?php if ($editing): ?><a href="home-track-record.php#all-records" class="button-secondary">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="admin-tabpanel" id="all-records" data-tab-group="main" data-tab-id="all-records">
  <h2>All Records</h2>
  <table class="admin-table">
    <thead>
      <tr><th>Photo</th><th>Year</th><th>Badge</th><th>Student</th><th>Achievement</th><th>Sort Order</th><th>Visible</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($records as $r): ?>
        <tr>
          <td><?php if ($r['image']): ?><img src="../<?= e($r['image']) ?>" alt="" style="max-height:40px;max-width:40px;border-radius:6px;object-fit:cover"><?php else: ?>&mdash;<?php endif; ?></td>
          <td><?= e($r['year']) ?></td>
          <td><?= e($r['position_badge']) ?></td>
          <td><?= e($r['student_name']) ?></td>
          <td><?= e($r['achievement_title']) ?></td>
          <td><?= (int)$r['sort_order'] ?></td>
          <td><?= $r['is_active'] ? 'Yes' : 'No' ?></td>
          <td class="actions-cell">
            <a href="home-track-record.php?edit=<?= (int)$r['id'] ?>#add-record">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this record?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
