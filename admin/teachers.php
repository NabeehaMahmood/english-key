<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM teachers WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('teachers.php', 'Team member deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $roleTitle = trim($_POST['role_title'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $detailBio = trim($_POST['detail_bio'] ?? '');
        $credentials = trim($_POST['credentials'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '') {
            redirectWithMessage('teachers.php', 'Name is required.', 'error');
        }

        try {
            $photo = handleImageUpload('photo', 'teachers');
        } catch (RuntimeException $e) {
            redirectWithMessage('teachers.php', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo) {
                $db->prepare('UPDATE teachers SET name=?, photo=?, role_title=?, subject=?, bio=?, detail_bio=?, credentials=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $photo, $roleTitle, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive, $id]);
            } else {
                $db->prepare('UPDATE teachers SET name=?, role_title=?, subject=?, bio=?, detail_bio=?, credentials=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $roleTitle, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('teachers.php', 'Team member updated.');
        } else {
            $db->prepare('INSERT INTO teachers (name, photo, role_title, subject, bio, detail_bio, credentials, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?)')
               ->execute([$name, $photo, $roleTitle, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive]);
            redirectWithMessage('teachers.php', 'Team member added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM teachers WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$teachers = $db->query('SELECT * FROM teachers ORDER BY sort_order, id')->fetchAll();
?>
<h1>Our Team</h1>
<p>Founders and instructors shown on the About page (team grid + individual bio sections) and Home page.</p>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Team Member' : 'Add Team Member' ?></h2>

  <label>Name
    <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
  </label>
  <label>Role Title (e.g. "Founder and CEO")
    <input type="text" name="role_title" value="<?= e($editing['role_title'] ?? '') ?>">
  </label>
  <label>Subject / Short Role (shown on Home page card)
    <input type="text" name="subject" value="<?= e($editing['subject'] ?? '') ?>">
  </label>
  <label>Short Bio (used on the Home page teaser card)
    <textarea name="bio" rows="2"><?= e($editing['bio'] ?? '') ?></textarea>
  </label>
  <label>Detailed Bio (used on the About page, separate paragraphs on blank lines)
    <textarea name="detail_bio" rows="6"><?= e($editing['detail_bio'] ?? '') ?></textarea>
  </label>
  <label>Credentials (one per line, shown as a portfolio list)
    <textarea name="credentials" rows="5"><?= e($editing['credentials'] ?? '') ?></textarea>
  </label>
  <label>Sort Order
    <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Visible on site
  </label>
  <label>Photo
    <?php if (!empty($editing['photo'])): ?>
      <div><img src="../<?= e($editing['photo']) ?>" style="max-height:80px;"></div>
    <?php endif; ?>
    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit"><?= $editing ? 'Update' : 'Add Team Member' ?></button>
  <?php if ($editing): ?><a href="teachers.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Name</th><th>Role</th><th>Visible</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($teachers as $teacher): ?>
      <tr>
        <td><?= e($teacher['name']) ?></td>
        <td><?= e($teacher['role_title']) ?></td>
        <td><?= $teacher['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="teachers.php?edit=<?= (int)$teacher['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this team member?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$teacher['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
