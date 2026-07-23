<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;
$validTabs = ['stories', 'pending'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $returnTab = in_array($_POST['tab'] ?? '', $validTabs, true) ? $_POST['tab'] : 'stories';

    if ($action === 'delete') {
        $oldPhoto = $db->prepare('SELECT photo FROM alumni WHERE id = ?');
        $oldPhoto->execute([(int)$_POST['id']]);
        $oldPhoto = $oldPhoto->fetchColumn();
        $db->prepare('DELETE FROM alumni WHERE id = ?')->execute([(int)$_POST['id']]);
        if ($oldPhoto && !in_array($oldPhoto, STOCK_AVATARS, true)) {
            deleteUploadedImage($oldPhoto);
        }
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Story deleted.');
    }

    if ($action === 'approve') {
        $db->prepare("UPDATE alumni SET status = 'approved', is_active = 1 WHERE id = ?")->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Story approved and published.');
    }

    if ($action === 'reject') {
        $db->prepare("UPDATE alumni SET status = 'rejected', is_active = 0 WHERE id = ?")->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Story rejected.');
    }

    if ($action === 'hide') {
        $db->prepare('UPDATE alumni SET is_active = 0 WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Story hidden from the public site.');
    }

    if ($action === 'show') {
        // Only an approved row can be shown - keeps a pending/rejected story
        // from becoming visible through this shortcut.
        $db->prepare("UPDATE alumni SET is_active = 1 WHERE id = ? AND status = 'approved'")->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Story is now visible on the public site.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $batchInfo = trim($_POST['batch_info'] ?? '');
        $achievement = trim($_POST['achievement'] ?? '');
        $story = trim($_POST['story'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['pending', 'approved', 'rejected'], true) ? $_POST['status'] : 'approved';
        // A story can only ever be publicly visible while it is approved,
        // no matter what the "Visible on site" checkbox was submitted as.
        $isActive = ($status === 'approved' && isset($_POST['is_active'])) ? 1 : 0;

        if ($name === '') {
            redirectWithMessage('alumni.php?tab=stories', 'Name is required.', 'error');
        }
        if ($story === '') {
            redirectWithMessage('alumni.php?tab=stories', 'Story text is required.', 'error');
        }

        try {
            $photo = resolvePhotoChoice('photo_choice', 'photo', 'gallery');
        } catch (RuntimeException $e) {
            redirectWithMessage('alumni.php?tab=stories', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo !== null) {
                $oldPhoto = $db->prepare('SELECT photo FROM alumni WHERE id = ?');
                $oldPhoto->execute([$id]);
                $oldPhoto = $oldPhoto->fetchColumn();
                $newPhoto = $photo === '' ? null : $photo;

                $db->prepare('UPDATE alumni SET name=?, achievement=?, batch_info=?, photo=?, story=?, contact=?, sort_order=?, is_active=?, status=? WHERE id=?')
                   ->execute([$name, $achievement, $batchInfo, $newPhoto, $story, $contact, $sortOrder, $isActive, $status, $id]);

                if ($oldPhoto && $oldPhoto !== $newPhoto && !in_array($oldPhoto, STOCK_AVATARS, true)) {
                    deleteUploadedImage($oldPhoto);
                }
            } else {
                $db->prepare('UPDATE alumni SET name=?, achievement=?, batch_info=?, story=?, contact=?, sort_order=?, is_active=?, status=? WHERE id=?')
                   ->execute([$name, $achievement, $batchInfo, $story, $contact, $sortOrder, $isActive, $status, $id]);
            }
            redirectWithMessage('alumni.php?tab=stories', 'Story updated.');
        } else {
            $db->prepare('INSERT INTO alumni (name, achievement, batch_info, photo, story, contact, sort_order, is_active, status) VALUES (?,?,?,?,?,?,?,?,?)')
               ->execute([$name, $achievement, $batchInfo, $photo ?: null, $story, $contact, $sortOrder, $isActive, $status]);
            redirectWithMessage('alumni.php?tab=stories', 'Story added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM alumni WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$activeTab = $editing ? 'stories' : (in_array($_GET['tab'] ?? '', $validTabs, true) ? $_GET['tab'] : 'stories');

$storiesReviewed = $db->query("SELECT * FROM alumni WHERE status IN ('approved','rejected') ORDER BY sort_order, id")->fetchAll();
$pending = $db->query("SELECT * FROM alumni WHERE status = 'pending' ORDER BY id DESC")->fetchAll();

$tabLabels = [
    'stories' => 'Alumni Stories',
    'pending' => 'Pending Review',
];
$tabCounts = [
    'stories' => count($storiesReviewed),
    'pending' => count($pending),
];
?>
<h1>Alumni Stories</h1>
<p class="admin-page-intro">The story wall on the public Alumni page, plus submissions from its "Share Your Story" form. Public submissions land in Pending Review and only appear on the site once approved here. The dark achiever band at the top of the Alumni page is managed under <a href="home-track-record.php">Results &amp; Toppers</a>.</p>

<div class="admin-tabs">
  <?php foreach ($tabLabels as $key => $label): ?>
    <button type="button" class="admin-tab-btn<?= $activeTab === $key ? ' active' : '' ?>" data-tab="<?= e($key) ?>">
      <?= e($label) ?> (<?= $tabCounts[$key] ?>)
    </button>
  <?php endforeach; ?>
</div>

<?php /* ============================== ALUMNI STORIES ============================== */ ?>
<div data-tab-panel="stories"<?= $activeTab === 'stories' ? '' : ' hidden' ?>>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

    <h2><?= $editing ? 'Edit Story' : 'Add Story' ?></h2>

    <label>Student Name
      <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
    </label>
    <label>Batch (e.g. "Class of 2024")
      <input type="text" name="batch_info" value="<?= e($editing['batch_info'] ?? '') ?>">
    </label>
    <label>Result / Current Position (e.g. "1st Year MBBS")
      <input type="text" name="achievement" value="<?= e($editing['achievement'] ?? '') ?>">
    </label>
    <label>Story
      <textarea name="story" rows="6" required><?= e($editing['story'] ?? '') ?></textarea>
    </label>
    <label>Private Contact (internal only, never shown publicly)
      <input type="text" name="contact" value="<?= e($editing['contact'] ?? '') ?>">
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
    </label>
    <label>Publish Status
      <select name="status">
        <option value="pending" <?= ($editing && $editing['status'] === 'pending') ? 'selected' : '' ?>>Pending Review</option>
        <option value="approved" <?= (!$editing || $editing['status'] === 'approved') ? 'selected' : '' ?>>Approved &amp; Published</option>
        <option value="rejected" <?= ($editing && $editing['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
      </select>
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$editing || $editing['is_active']) ? 'checked' : '' ?>>
      Visible on site (only takes effect while Publish Status is Approved)
    </label>

    <?= renderPhotoChoiceField('photo_choice', 'photo', $editing['photo'] ?? null, 'Profile Photo (upload a real photo, or pick an avatar if none is available)') ?>

    <button type="submit"><?= $editing ? 'Update Story' : 'Add Story' ?></button>
    <?php if ($editing): ?><a href="alumni.php?tab=stories" class="button-secondary">Cancel</a><?php endif; ?>
  </form>

  <table class="admin-table">
    <thead>
      <tr><th>Photo</th><th>Name</th><th>Batch</th><th>Story</th><th class="col-center">Sort</th><th class="col-center">Publish Status</th><th class="col-center">Visibility</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($storiesReviewed as $s): ?>
        <tr>
          <td>
            <?php if (!empty($s['photo'])): ?>
              <img src="../<?= e($s['photo']) ?>" alt="" class="table-thumb">
            <?php else: ?>
              <span class="table-thumb-placeholder">No photo</span>
            <?php endif; ?>
          </td>
          <td class="cell-title"><?= e($s['name']) ?></td>
          <td><?= e($s['batch_info']) ?></td>
          <td><?= e(mb_strimwidth((string)$s['story'], 0, 90, '...')) ?></td>
          <td class="col-center"><?= (int)$s['sort_order'] ?></td>
          <td class="col-center">
            <span class="status-badge <?= $s['status'] === 'approved' ? 'status-published' : 'status-rejected' ?>"><?= $s['status'] === 'approved' ? 'Approved' : 'Rejected' ?></span>
          </td>
          <td class="col-center">
            <span class="status-badge <?= $s['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $s['is_active'] ? 'Visible' : 'Hidden' ?></span>
          </td>
          <td class="actions-cell">
            <a href="alumni.php?tab=stories&edit=<?= (int)$s['id'] ?>">Edit</a>
            <?php if ($s['status'] === 'approved'): ?>
              <form method="post" class="inline-form">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="<?= $s['is_active'] ? 'hide' : 'show' ?>">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <input type="hidden" name="tab" value="stories">
                <button type="submit" class="link-button"><?= $s['is_active'] ? 'Hide' : 'Show' ?></button>
              </form>
              <form method="post" class="inline-form" onsubmit="return confirm('Reject this story? It will be removed from the public site.');">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <input type="hidden" name="tab" value="stories">
                <button type="submit" class="link-button link-button-danger">Reject</button>
              </form>
            <?php else: ?>
              <form method="post" class="inline-form">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <input type="hidden" name="tab" value="stories">
                <button type="submit" class="link-button">Approve</button>
              </form>
            <?php endif; ?>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this story permanently?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
              <input type="hidden" name="tab" value="stories">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$storiesReviewed): ?><tr><td colspan="8" class="admin-table-empty">No reviewed stories yet — approve one from Pending Review, or add one using the form above.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php /* ============================== PENDING REVIEW ============================== */ ?>
<div data-tab-panel="pending"<?= $activeTab === 'pending' ? '' : ' hidden' ?>>
  <p>Stories submitted through the public "Share Your Story" form land here first. Review the full story and private contact info, then approve, reject, or edit before publishing.</p>
  <table class="admin-table">
    <thead>
      <tr><th>Photo</th><th>Name</th><th>Batch</th><th>Story</th><th>Private Contact</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($pending as $p): ?>
        <tr class="unread-row">
          <td>
            <?php if (!empty($p['photo'])): ?>
              <img src="../<?= e($p['photo']) ?>" alt="" class="table-thumb">
            <?php else: ?>
              <span class="table-thumb-placeholder">No photo</span>
            <?php endif; ?>
          </td>
          <td class="cell-title"><?= e($p['name']) ?></td>
          <td><?= e($p['batch_info']) ?></td>
          <td style="white-space:pre-wrap;max-width:360px"><?= e($p['story']) ?></td>
          <td><strong>Private:</strong> <?= $p['contact'] ? e($p['contact']) : '—' ?></td>
          <td class="actions-cell">
            <form method="post" class="inline-form">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="tab" value="pending">
              <button type="submit" class="link-button">Approve &amp; Publish</button>
            </form>
            <form method="post" class="inline-form" onsubmit="return confirm('Reject this submission?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="tab" value="pending">
              <button type="submit" class="link-button link-button-danger">Reject</button>
            </form>
            <a href="alumni.php?tab=stories&edit=<?= (int)$p['id'] ?>">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this submission?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="tab" value="pending">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$pending): ?><tr><td colspan="6" class="admin-table-empty">No pending submissions right now.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<script>
(function () {
  var buttons = document.querySelectorAll('.admin-tab-btn');
  var panels = document.querySelectorAll('[data-tab-panel]');
  var showTab = function (tab) {
    buttons.forEach(function (btn) {
      btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    panels.forEach(function (panel) {
      panel.hidden = panel.getAttribute('data-tab-panel') !== tab;
    });
  };
  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () { showTab(btn.dataset.tab); });
  });
})();
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
