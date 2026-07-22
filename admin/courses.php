<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;
$categories = ['subject' => 'Core Subject', 'featured' => 'Featured Course', 'programme' => 'Seasonal Programme'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM courses WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('courses.php', 'Course deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = in_array($_POST['category'] ?? '', array_keys($categories), true) ? $_POST['category'] : 'programme';
        $tagLine = trim($_POST['tag_line'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $eligibility = trim($_POST['eligibility'] ?? '');
        $mode = trim($_POST['mode'] ?? '');
        $scheduleInfo = trim($_POST['schedule_info'] ?? '');
        $highlights = trim($_POST['highlights'] ?? '');
        $modules = trim($_POST['modules'] ?? '');
        $programmeGroup = trim($_POST['programme_group'] ?? '');
        $seatsInfo = trim($_POST['seats_info'] ?? '');
        $accentColor = trim($_POST['accent_color'] ?? '');
        $programmeGroupId = ($category === 'programme' && !empty($_POST['programme_group_id'])) ? (int)$_POST['programme_group_id'] : null;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $slug = slugify($title);

        if ($title === '') {
            redirectWithMessage('courses.php', 'Title is required.', 'error');
        }

        try {
            $image = handleImageUpload('image', 'courses');
        } catch (RuntimeException $e) {
            redirectWithMessage('courses.php', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($image) {
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, programme_group=?, tag_line=?, description=?, image=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, modules=?, seats_info=?, accent_color=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $programmeGroup, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $modules, $seatsInfo, $accentColor, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, programme_group=?, tag_line=?, description=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, modules=?, seats_info=?, accent_color=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $programmeGroup, $tagLine, $description, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $modules, $seatsInfo, $accentColor, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('courses.php', 'Course updated.');
        } else {
            $db->prepare('INSERT INTO courses (title, slug, category, programme_group, tag_line, description, image, duration, level, price, eligibility, mode, schedule_info, highlights, modules, seats_info, accent_color, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$title, $slug, $category, $programmeGroup, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $modules, $seatsInfo, $accentColor, $sortOrder, $isActive]);
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, tag_line=?, description=?, image=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, seats_info=?, accent_color=?, programme_group_id=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE courses SET title=?, slug=?, category=?, tag_line=?, description=?, duration=?, level=?, price=?, eligibility=?, mode=?, schedule_info=?, highlights=?, seats_info=?, accent_color=?, programme_group_id=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $tagLine, $description, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('courses.php', 'Course updated.');
        } else {
            $db->prepare('INSERT INTO courses (title, slug, category, tag_line, description, image, duration, level, price, eligibility, mode, schedule_info, highlights, seats_info, accent_color, programme_group_id, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$title, $slug, $category, $tagLine, $description, $image, $duration, $level, $price, $eligibility, $mode, $scheduleInfo, $highlights, $seatsInfo, $accentColor, $programmeGroupId, $sortOrder, $isActive]);
            redirectWithMessage('courses.php', 'Course added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$courses = $db->query('
    SELECT c.*, g.name AS group_name
    FROM courses c
    LEFT JOIN programme_groups g ON g.id = c.programme_group_id
    ORDER BY c.category, c.sort_order, c.id
')->fetchAll();
$programmeGroups = $db->query('SELECT id, name FROM programme_groups ORDER BY sort_order, name')->fetchAll();
?>
<h1>Courses</h1>
<p>One flexible list covers the 4 core subjects, the featured enrolling-now course, and seasonal programmes, filter each by category.</p>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Course' : 'Add Course' ?></h2>

  <label>Category
    <select name="category">
      <?php foreach ($categories as $val => $label): ?>
        <option value="<?= e($val) ?>" <?= ($editing['category'] ?? 'programme') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Title
    <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
  </label>
  <label>Programme Group (Seasonal Programme only - groups courses into an accordion on the Courses page, e.g. "Full Syllabus", "Exam Prep"; leave blank to fall back to a single "Programmes" group)
    <input type="text" name="programme_group" value="<?= e($editing['programme_group'] ?? '') ?>">
  </label>
  <label>Tag Line (short label under the title, e.g. tags separated by " - ", or a subtitle)
    <input type="text" name="tag_line" value="<?= e($editing['tag_line'] ?? '') ?>">
  </label>
  <label>Description
    <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
  </label>
  <label>Duration
    <input type="text" name="duration" value="<?= e($editing['duration'] ?? '') ?>" placeholder="e.g. 8 weeks">
  </label>
  <label>Level / Eligibility label
    <input type="text" name="level" value="<?= e($editing['level'] ?? '') ?>" placeholder="e.g. Classes 9-12">
  </label>
  <label>Price / Fee
    <input type="text" name="price" value="<?= e($editing['price'] ?? '') ?>" placeholder="e.g. Rs. 5,000">
  </label>
  <label>Eligibility
    <input type="text" name="eligibility" value="<?= e($editing['eligibility'] ?? '') ?>">
  </label>
  <label>Mode
    <input type="text" name="mode" value="<?= e($editing['mode'] ?? '') ?>" placeholder="e.g. Online via Zoom">
  </label>
  <label>Schedule Info (Featured course detail grid - "Label:Value" pairs separated by "|", e.g. "Starts:06 July 2026|Ends:31 July 2026|Schedule:Monday-Friday|Time:07:00-09:00 PM (PKT)|Sessions:20 live, 2 hours each". Plain free text still works and shows as a single cell.)
    <input type="text" name="schedule_info" value="<?= e($editing['schedule_info'] ?? '') ?>">
  </label>
  <label>Highlights (one bullet per line)
    <textarea name="highlights" rows="4"><?= e($editing['highlights'] ?? '') ?></textarea>
  </label>
  <label>Curriculum Modules (Featured course only, optional - blocks of "Label|Title|bullet one&#10;bullet two", separate blocks with a line containing only "---")
    <textarea name="modules" rows="5"><?= e($editing['modules'] ?? '') ?></textarea>
  </label>
  <label>Seats Info
    <input type="text" name="seats_info" value="<?= e($editing['seats_info'] ?? '') ?>" placeholder="e.g. Limited seats">
  </label>
  <label>Programme Group (Seasonal Programme only, the collapsible section it appears under on the courses page. Leave as "None" to fall under "Other Programmes". Manage groups on the <a href="programme-groups.php">Programme Groups</a> screen)
    <select name="programme_group_id">
      <option value="">None (Other Programmes)</option>
      <?php foreach ($programmeGroups as $g): ?>
        <option value="<?= (int)$g['id'] ?>" <?= (int)($editing['programme_group_id'] ?? 0) === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Accent Color (hex, used for subject cards)
    <input type="color" name="accent_color" value="<?= e($editing['accent_color'] ?? '#E56A19') ?>">
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>
  <label>Image
    <?php if (!empty($editing['image'])): ?>
      <div><img src="../<?= e($editing['image']) ?>" style="max-height:80px;"></div>
    <?php endif; ?>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit"><?= $editing ? 'Update Course' : 'Add Course' ?></button>
  <?php if ($editing): ?><a href="courses.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Category</th><th>Group</th><th>Price/Fee</th><th>Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($courses as $course): ?>
      <tr>
        <td><?= e($course['title']) ?></td>
        <td><?= e($categories[$course['category']] ?? $course['category']) ?></td>
        <td><?= e($course['group_name'] ?: '') ?></td>
        <td><?= e($course['price']) ?></td>
        <td><?= $course['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="courses.php?edit=<?= (int)$course['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
