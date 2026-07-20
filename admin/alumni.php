<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;
$validTabs = ['achievers', 'stories', 'pending'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $returnTab = in_array($_POST['tab'] ?? '', $validTabs, true) ? $_POST['tab'] : 'achievers';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM alumni WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Entry deleted.');
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
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Entry hidden from the public site.');
    }

    if ($action === 'show') {
        // Only an approved row can be shown - keeps a pending/rejected story
        // from becoming visible through this shortcut.
        $db->prepare("UPDATE alumni SET is_active = 1 WHERE id = ? AND status = 'approved'")->execute([(int)$_POST['id']]);
        redirectWithMessage('alumni.php?tab=' . $returnTab, 'Entry is now visible on the public site.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $type = ($_POST['type'] ?? '') === 'story' ? 'story' : 'achiever';
        $name = trim($_POST['name'] ?? '');
        $batchInfo = trim($_POST['batch_info'] ?? '');
        $achievement = trim($_POST['achievement'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $tabForRedirect = $type === 'story' ? 'stories' : 'achievers';

        if ($name === '') {
            redirectWithMessage('alumni.php?tab=' . $tabForRedirect, 'Name is required.', 'error');
        }

        if ($type === 'achiever') {
            $passingYear = trim($_POST['passing_year'] ?? '');
            $story = null;
            $contact = null;
            $status = 'approved';
            $isActive = isset($_POST['is_active']) ? 1 : 0;
        } else {
            $passingYear = null;
            $story = trim($_POST['story'] ?? '');
            $contact = trim($_POST['contact'] ?? '');
            $status = in_array($_POST['status'] ?? '', ['pending', 'approved', 'rejected'], true) ? $_POST['status'] : 'approved';
            // A story can only ever be publicly visible while it is approved,
            // no matter what the "Visible on site" checkbox was submitted as.
            $isActive = ($status === 'approved' && isset($_POST['is_active'])) ? 1 : 0;

            if ($story === '') {
                redirectWithMessage('alumni.php?tab=stories', 'Story text is required.', 'error');
            }
        }

        try {
            $photo = handleImageUpload('photo', 'gallery');
        } catch (RuntimeException $e) {
            redirectWithMessage('alumni.php?tab=' . $tabForRedirect, $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo) {
                $db->prepare('UPDATE alumni SET name=?, type=?, achievement=?, batch_info=?, passing_year=?, photo=?, story=?, contact=?, sort_order=?, is_active=?, status=? WHERE id=?')
                   ->execute([$name, $type, $achievement, $batchInfo, $passingYear, $photo, $story, $contact, $sortOrder, $isActive, $status, $id]);
            } else {
                $db->prepare('UPDATE alumni SET name=?, type=?, achievement=?, batch_info=?, passing_year=?, story=?, contact=?, sort_order=?, is_active=?, status=? WHERE id=?')
                   ->execute([$name, $type, $achievement, $batchInfo, $passingYear, $story, $contact, $sortOrder, $isActive, $status, $id]);
            }
            redirectWithMessage('alumni.php?tab=' . $tabForRedirect, 'Entry updated.');
        } else {
            $db->prepare('INSERT INTO alumni (name, type, achievement, batch_info, passing_year, photo, story, contact, sort_order, is_active, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$name, $type, $achievement, $batchInfo, $passingYear, $photo, $story, $contact, $sortOrder, $isActive, $status]);
            redirectWithMessage('alumni.php?tab=' . $tabForRedirect, 'Entry added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM alumni WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

// Editing a row always wins the tab (so an Edit link never lands on the
// wrong panel); otherwise fall back to ?tab= from a tab click, then
// default to the first tab. Mirrors admin/courses.php's category logic.
if ($editing) {
    $activeTab = $editing['type'] === 'story' ? 'stories' : 'achievers';
} elseif (in_array($_GET['tab'] ?? '', $validTabs, true)) {
    $activeTab = $_GET['tab'];
} else {
    $activeTab = 'achievers';
}

$achievers = $db->query("SELECT * FROM alumni WHERE type = 'achiever' ORDER BY sort_order, id")->fetchAll();
$storiesReviewed = $db->query("SELECT * FROM alumni WHERE type = 'story' AND status IN ('approved','rejected') ORDER BY sort_order, id")->fetchAll();
$pending = $db->query("SELECT * FROM alumni WHERE type = 'story' AND status = 'pending' ORDER BY id DESC")->fetchAll();

$tabLabels = [
    'achievers' => 'Alumni Achievers',
    'stories'   => 'Alumni Stories',
    'pending'   => 'Pending Review',
];
$tabCounts = [
    'achievers' => count($achievers),
    'stories'   => count($storiesReviewed),
    'pending'   => count($pending),
];
?>
<h1>Alumni</h1>
<p>Manage the Alumni Achievers band, the Alumni Stories wall, and story submissions from the public "Share Your Story" form. Public submissions land in Pending Review and only appear on the site once approved here.</p>

<div class="admin-tabs">
  <?php foreach ($tabLabels as $key => $label): ?>
    <button type="button" class="admin-tab-btn<?= $activeTab === $key ? ' active' : '' ?>" data-tab="<?= e($key) ?>">
      <?= e($label) ?> (<?= $tabCounts[$key] ?>)
    </button>
  <?php endforeach; ?>
</div>

<?php /* ============================== ALUMNI ACHIEVERS ============================== */ ?>
<div data-tab-panel="achievers"<?= $activeTab === 'achievers' ? '' : ' hidden' ?>>
  <?php $aEditing = ($editing && $editing['type'] === 'achiever') ? $editing : null; ?>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($aEditing['id'] ?? 0) ?>">
    <input type="hidden" name="type" value="achiever">

    <h2><?= $aEditing ? 'Edit Achiever' : 'Add Achiever' ?></h2>

    <label>Full Name
      <input type="text" name="name" value="<?= e($aEditing['name'] ?? '') ?>" required>
    </label>
    <label>Batch / Class (e.g. "Class of 2025")
      <input type="text" name="batch_info" value="<?= e($aEditing['batch_info'] ?? '') ?>">
    </label>
    <label>Passing Year (e.g. "2025")
      <input type="text" name="passing_year" value="<?= e($aEditing['passing_year'] ?? '') ?>" maxlength="20">
    </label>
    <label>Achievement (e.g. "HSSC 1st Position - Federal Board")
      <input type="text" name="achievement" value="<?= e($aEditing['achievement'] ?? '') ?>">
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($aEditing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$aEditing || $aEditing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>
    <label>Profile Photo
      <?php if (!empty($aEditing['photo'])): ?>
        <div><img src="../<?= e($aEditing['photo']) ?>" style="max-height:80px;"></div>
      <?php endif; ?>
      <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
    </label>

    <button type="submit"><?= $aEditing ? 'Update Achiever' : 'Add Achiever' ?></button>
    <?php if ($aEditing): ?><a href="alumni.php?tab=achievers" class="button-secondary">Cancel</a><?php endif; ?>
  </form>

  <table class="admin-table">
    <thead>
      <tr><th>Photo</th><th>Name</th><th>Batch / Class</th><th>Passing Year</th><th>Achievement</th><th class="col-center">Sort</th><th class="col-center">Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($achievers as $a): ?>
        <tr>
          <td>
            <?php if (!empty($a['photo'])): ?>
              <img src="../<?= e($a['photo']) ?>" alt="" class="table-thumb">
            <?php else: ?>
              <span class="table-thumb-placeholder">No photo</span>
            <?php endif; ?>
          </td>
          <td class="cell-title"><?= e($a['name']) ?></td>
          <td><?= e($a['batch_info']) ?></td>
          <td><?= e($a['passing_year']) ?></td>
          <td><?= e($a['achievement']) ?></td>
          <td class="col-center"><?= (int)$a['sort_order'] ?></td>
          <td class="col-center">
            <span class="status-badge <?= $a['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $a['is_active'] ? 'Visible' : 'Hidden' ?></span>
          </td>
          <td class="actions-cell">
            <a href="alumni.php?tab=achievers&edit=<?= (int)$a['id'] ?>">Edit</a>
            <form method="post" class="inline-form">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="<?= $a['is_active'] ? 'hide' : 'show' ?>">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <input type="hidden" name="tab" value="achievers">
              <button type="submit" class="link-button"><?= $a['is_active'] ? 'Hide' : 'Show' ?></button>
            </form>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this achiever?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <input type="hidden" name="tab" value="achievers">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$achievers): ?><tr><td colspan="8" class="admin-table-empty">No achievers yet — add one using the form above.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php /* ============================== ALUMNI STORIES ============================== */ ?>
<div data-tab-panel="stories"<?= $activeTab === 'stories' ? '' : ' hidden' ?>>
  <?php $sEditing = ($editing && $editing['type'] === 'story') ? $editing : null; ?>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($sEditing['id'] ?? 0) ?>">
    <input type="hidden" name="type" value="story">

    <h2><?= $sEditing ? 'Edit Story' : 'Add Story' ?></h2>

    <label>Student Name
      <input type="text" name="name" value="<?= e($sEditing['name'] ?? '') ?>" required>
    </label>
    <label>Batch (e.g. "Class of 2024")
      <input type="text" name="batch_info" value="<?= e($sEditing['batch_info'] ?? '') ?>">
    </label>
    <label>Current Position / Badge (e.g. "1st Year MBBS")
      <input type="text" name="achievement" value="<?= e($sEditing['achievement'] ?? '') ?>">
    </label>
    <label>Story
      <textarea name="story" rows="6" required><?= e($sEditing['story'] ?? '') ?></textarea>
    </label>
    <label>Private Contact (internal only, never shown publicly)
      <input type="text" name="contact" value="<?= e($sEditing['contact'] ?? '') ?>">
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($sEditing['sort_order'] ?? 0) ?>">
    </label>
    <label>Publish Status
      <select name="status">
        <option value="pending" <?= ($sEditing && $sEditing['status'] === 'pending') ? 'selected' : '' ?>>Pending Review</option>
        <option value="approved" <?= (!$sEditing || $sEditing['status'] === 'approved') ? 'selected' : '' ?>>Approved &amp; Published</option>
        <option value="rejected" <?= ($sEditing && $sEditing['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
      </select>
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$sEditing || $sEditing['is_active']) ? 'checked' : '' ?>>
      Visible on site (only takes effect while Publish Status is Approved)
    </label>
    <label>Profile Photo
      <?php if (!empty($sEditing['photo'])): ?>
        <div><img src="../<?= e($sEditing['photo']) ?>" style="max-height:80px;"></div>
      <?php endif; ?>
      <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
    </label>

    <button type="submit"><?= $sEditing ? 'Update Story' : 'Add Story' ?></button>
    <?php if ($sEditing): ?><a href="alumni.php?tab=stories" class="button-secondary">Cancel</a><?php endif; ?>
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
