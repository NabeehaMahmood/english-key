<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

// Tab key => display label. Order here drives the nav order.
$categoryTabs = [
    'featured'  => 'Featured Courses',
    'subject'   => 'Core Subjects',
    'programme' => 'Programmes',
];
$validCategories = array_keys($categoryTabs);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $redirectCategory = in_array($_POST['category'] ?? '', $validCategories, true) ? $_POST['category'] : 'featured';
        $db->prepare('DELETE FROM courses WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('courses.php?category=' . $redirectCategory, 'Course deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = in_array($_POST['category'] ?? '', $validCategories, true) ? $_POST['category'] : 'programme';
        $tagLine = trim($_POST['tag_line'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $eligibility = trim($_POST['eligibility'] ?? '');
        $mode = trim($_POST['mode'] ?? '');
        $scheduleInfo = trim($_POST['schedule_info'] ?? '');
        $highlights = trim($_POST['highlights'] ?? '');
        $seatsInfo = trim($_POST['seats_info'] ?? '');
        $accentColor = trim($_POST['accent_color'] ?? '');
        $programmeGroupId = ($category === 'programme' && !empty($_POST['programme_group_id'])) ? (int)$_POST['programme_group_id'] : null;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $slug = slugify($title);

        if ($title === '') {
            redirectWithMessage('courses.php?category=' . $category, 'Title is required.', 'error');
        }

        try {
            $image = handleImageUpload('image', 'courses');
        } catch (RuntimeException $e) {
            redirectWithMessage('courses.php?category=' . $category, $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($image) {
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, tag_line=?, description=?, image=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, seats_info=?, accent_color=?, programme_group_id=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, tag_line=?, description=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, seats_info=?, accent_color=?, programme_group_id=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $tagLine, $description, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('courses.php?category=' . $category, 'Course updated.');
        } else {
            $db->prepare('INSERT INTO courses (title, slug, category, tag_line, description, image, duration, level, price, eligibility, mode, schedule_info, highlights, seats_info, accent_color, programme_group_id, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$title, $slug, $category, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive]);
            redirectWithMessage('courses.php?category=' . $category, 'Course added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

// The edit target's own category always wins the tab, so opening an edit
// link never lands on the wrong panel; otherwise fall back to ?category=
// from a tab click, then default to the first tab.
if ($editing && in_array($editing['category'], $validCategories, true)) {
    $activeCategory = $editing['category'];
} elseif (in_array($_GET['category'] ?? '', $validCategories, true)) {
    $activeCategory = $_GET['category'];
} else {
    $activeCategory = 'featured';
}

$courses = $db->query('
    SELECT c.*, g.name AS group_name
    FROM courses c
    LEFT JOIN programme_groups g ON g.id = c.programme_group_id
    ORDER BY c.category, c.sort_order, c.id
')->fetchAll();
$programmeGroups = $db->query('SELECT id, name FROM programme_groups ORDER BY sort_order, name')->fetchAll();

$coursesByCategory = ['featured' => [], 'subject' => [], 'programme' => []];
foreach ($courses as $c) {
    if (isset($coursesByCategory[$c['category']])) {
        $coursesByCategory[$c['category']][] = $c;
    }
}
?>
<h1>Courses</h1>
<p>Featured course, the 4 core subjects, and seasonal programmes each keep their own form, showing only the fields that actually appear for that type on the public Courses page.</p>

<div class="admin-tabs">
  <?php foreach ($categoryTabs as $cat => $label): ?>
    <button type="button" class="admin-tab-btn<?= $activeCategory === $cat ? ' active' : '' ?>" data-tab="<?= e($cat) ?>">
      <?= e($label) ?> (<?= count($coursesByCategory[$cat]) ?>)
    </button>
  <?php endforeach; ?>
</div>

<?php /* ============================== FEATURED COURSES ============================== */ ?>
<div data-tab-panel="featured"<?= $activeCategory === 'featured' ? '' : ' hidden' ?>>
  <?php $fEditing = ($editing && $editing['category'] === 'featured') ? $editing : null; ?>
  <form method="post" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($fEditing['id'] ?? 0) ?>">
    <input type="hidden" name="category" value="featured">
    <input type="hidden" name="level" value="<?= e($fEditing['level'] ?? '') ?>">

    <h2><?= $fEditing ? 'Edit Featured Course' : 'Add Featured Course' ?></h2>

    <label>Title
      <input type="text" name="title" value="<?= e($fEditing['title'] ?? '') ?>" required>
    </label>
    <label>Tag Line (short subtitle shown next to the title)
      <input type="text" name="tag_line" value="<?= e($fEditing['tag_line'] ?? '') ?>">
    </label>
    <label>Description
      <textarea name="description" rows="3"><?= e($fEditing['description'] ?? '') ?></textarea>
    </label>
    <label>Duration
      <input type="text" name="duration" value="<?= e($fEditing['duration'] ?? '') ?>" placeholder="e.g. 20 live sessions - 2 hours each">
    </label>
    <label>Eligibility / Level
      <input type="text" name="eligibility" value="<?= e($fEditing['eligibility'] ?? '') ?>" placeholder="e.g. All boards, Class 8th onwards">
    </label>
    <label>Mode
      <input type="text" name="mode" value="<?= e($fEditing['mode'] ?? '') ?>" placeholder="e.g. Online via Zoom">
    </label>
    <label>Price / Fee
      <input type="text" name="price" value="<?= e($fEditing['price'] ?? '') ?>" placeholder="e.g. Rs. 5,000">
    </label>
    <label>Seats Info
      <input type="text" name="seats_info" value="<?= e($fEditing['seats_info'] ?? '') ?>" placeholder="e.g. Seats are strictly limited">
    </label>
    <label>Schedule Info (shown as separate chips, split by " - ")
      <input type="text" name="schedule_info" value="<?= e($fEditing['schedule_info'] ?? '') ?>" placeholder="Starts ... - Ends ... - Mon-Fri - 7-9 PM">
    </label>
    <label>Highlights (one bullet per line)
      <textarea name="highlights" rows="4"><?= e($fEditing['highlights'] ?? '') ?></textarea>
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($fEditing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$fEditing || $fEditing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>

    <button type="submit"><?= $fEditing ? 'Update Course' : 'Add Course' ?></button>
    <?php if ($fEditing): ?><a href="courses.php?category=featured" class="button-secondary">Cancel</a><?php endif; ?>
  </form>

  <table class="admin-table">
    <thead>
      <tr><th>Title</th><th>Tag Line</th><th>Price/Fee</th><th class="col-center">Sort</th><th class="col-center">Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($coursesByCategory['featured'] as $course): ?>
        <tr>
          <td class="cell-title"><?= e($course['title']) ?></td>
          <td><?= e($course['tag_line']) ?></td>
          <td><?= e($course['price']) ?></td>
          <td class="col-center"><?= (int)$course['sort_order'] ?></td>
          <td class="col-center">
            <span class="status-badge <?= $course['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $course['is_active'] ? 'Visible' : 'Hidden' ?></span>
          </td>
          <td class="actions-cell">
            <a href="courses.php?category=featured&edit=<?= (int)$course['id'] ?>">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
              <input type="hidden" name="category" value="featured">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$coursesByCategory['featured']): ?><tr><td colspan="6" class="admin-table-empty">No featured courses yet — add one using the form above.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php /* ============================== CORE SUBJECTS ============================== */ ?>
<div data-tab-panel="subject"<?= $activeCategory === 'subject' ? '' : ' hidden' ?>>
  <?php $sEditing = ($editing && $editing['category'] === 'subject') ? $editing : null; ?>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($sEditing['id'] ?? 0) ?>">
    <input type="hidden" name="category" value="subject">

    <h2><?= $sEditing ? 'Edit Core Subject' : 'Add Core Subject' ?></h2>

    <label>Title
      <input type="text" name="title" value="<?= e($sEditing['title'] ?? '') ?>" required>
    </label>
    <label>Tag Line (tags shown as pills, separated by " - ")
      <input type="text" name="tag_line" value="<?= e($sEditing['tag_line'] ?? '') ?>" placeholder="e.g. Live Classes - Smart Notes - Model Papers">
    </label>
    <label>Description
      <textarea name="description" rows="3"><?= e($sEditing['description'] ?? '') ?></textarea>
    </label>
    <label>Level
      <input type="text" name="level" value="<?= e($sEditing['level'] ?? '') ?>" placeholder="e.g. Classes 9-12">
    </label>
    <label>Accent Color (used for this subject's card)
      <input type="color" name="accent_color" value="<?= e($sEditing['accent_color'] ?? '#EA6C1F') ?>">
    </label>
    <label>Image
      <?php if (!empty($sEditing['image'])): ?>
        <div><img src="../<?= e($sEditing['image']) ?>" style="max-height:80px;"></div>
      <?php endif; ?>
      <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($sEditing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$sEditing || $sEditing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>

    <button type="submit"><?= $sEditing ? 'Update Subject' : 'Add Subject' ?></button>
    <?php if ($sEditing): ?><a href="courses.php?category=subject" class="button-secondary">Cancel</a><?php endif; ?>
  </form>

  <table class="admin-table">
    <thead>
      <tr><th>Thumbnail</th><th>Title</th><th>Level</th><th>Accent</th><th class="col-center">Sort</th><th class="col-center">Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($coursesByCategory['subject'] as $course): ?>
        <tr>
          <td>
            <?php if (!empty($course['image'])): ?>
              <img src="../<?= e($course['image']) ?>" alt="" class="table-thumb">
            <?php else: ?>
              <span class="table-thumb-placeholder">No image</span>
            <?php endif; ?>
          </td>
          <td class="cell-title"><?= e($course['title']) ?></td>
          <td><?= e($course['level']) ?></td>
          <td><span style="display:inline-block;width:14px;height:14px;border-radius:4px;vertical-align:-2px;background:<?= e($course['accent_color'] ?: '#ccc') ?>"></span> <?= e($course['accent_color']) ?></td>
          <td class="col-center"><?= (int)$course['sort_order'] ?></td>
          <td class="col-center">
            <span class="status-badge <?= $course['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $course['is_active'] ? 'Visible' : 'Hidden' ?></span>
          </td>
          <td class="actions-cell">
            <a href="courses.php?category=subject&edit=<?= (int)$course['id'] ?>">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
              <input type="hidden" name="category" value="subject">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$coursesByCategory['subject']): ?><tr><td colspan="7" class="admin-table-empty">No core subjects yet — add one using the form above.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php /* ============================== PROGRAMMES ============================== */ ?>
<div data-tab-panel="programme"<?= $activeCategory === 'programme' ? '' : ' hidden' ?>>
  <?php $pEditing = ($editing && $editing['category'] === 'programme') ? $editing : null; ?>
  <form method="post" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($pEditing['id'] ?? 0) ?>">
    <input type="hidden" name="category" value="programme">
    <input type="hidden" name="level" value="<?= e($pEditing['level'] ?? '') ?>">

    <h2><?= $pEditing ? 'Edit Programme' : 'Add Programme' ?></h2>

    <label>Title
      <input type="text" name="title" value="<?= e($pEditing['title'] ?? '') ?>" required>
    </label>
    <label>Tag Line (short label shown on the card)
      <input type="text" name="tag_line" value="<?= e($pEditing['tag_line'] ?? '') ?>" placeholder="e.g. Jul 2026 - All boards">
    </label>
    <label>Description
      <textarea name="description" rows="3"><?= e($pEditing['description'] ?? '') ?></textarea>
    </label>
    <label>Eligibility / Level
      <input type="text" name="eligibility" value="<?= e($pEditing['eligibility'] ?? '') ?>" placeholder="e.g. All boards">
    </label>
    <label>Duration
      <input type="text" name="duration" value="<?= e($pEditing['duration'] ?? '') ?>" placeholder="e.g. 6-31 Jul 2026">
    </label>
    <label>Price / Fee
      <input type="text" name="price" value="<?= e($pEditing['price'] ?? '') ?>" placeholder="e.g. Rs. 5,000">
    </label>
    <label>Seats Info
      <input type="text" name="seats_info" value="<?= e($pEditing['seats_info'] ?? '') ?>" placeholder="e.g. Limited seats">
    </label>
    <label>Highlights (one bullet per line)
      <textarea name="highlights" rows="4"><?= e($pEditing['highlights'] ?? '') ?></textarea>
    </label>
    <label>Programme Group (the collapsible section this appears under. Leave as "None" to fall under "Other Programmes". Manage groups on the <a href="programme-groups.php">Programme Groups</a> screen)
      <select name="programme_group_id">
        <option value="">None (Other Programmes)</option>
        <?php foreach ($programmeGroups as $g): ?>
          <option value="<?= (int)$g['id'] ?>" <?= (int)($pEditing['programme_group_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Sort Order
      <input type="number" name="sort_order" value="<?= (int)($pEditing['sort_order'] ?? 0) ?>">
    </label>
    <label class="checkbox-label">
      <input type="checkbox" name="is_active" <?= (!$pEditing || $pEditing['is_active']) ? 'checked' : '' ?>>
      Visible on site
    </label>

    <button type="submit"><?= $pEditing ? 'Update Programme' : 'Add Programme' ?></button>
    <?php if ($pEditing): ?><a href="courses.php?category=programme" class="button-secondary">Cancel</a><?php endif; ?>
  </form>

  <table class="admin-table">
    <thead>
      <tr><th>Title</th><th>Group</th><th>Price/Fee</th><th class="col-center">Sort</th><th class="col-center">Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($coursesByCategory['programme'] as $course): ?>
        <tr>
          <td class="cell-title"><?= e($course['title']) ?></td>
          <td><?= e($course['group_name'] ?: '—') ?></td>
          <td><?= e($course['price']) ?></td>
          <td class="col-center"><?= (int)$course['sort_order'] ?></td>
          <td class="col-center">
            <span class="status-badge <?= $course['is_active'] ? 'status-published' : 'status-draft' ?>"><?= $course['is_active'] ? 'Visible' : 'Hidden' ?></span>
          </td>
          <td class="actions-cell">
            <a href="courses.php?category=programme&edit=<?= (int)$course['id'] ?>">Edit</a>
            <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
              <input type="hidden" name="category" value="programme">
              <button type="submit" class="link-button link-button-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$coursesByCategory['programme']): ?><tr><td colspan="6" class="admin-table-empty">No programmes yet — add one using the form above.</td></tr><?php endif; ?>
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
