<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM teachers WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('teachers.php#faculty', 'Team member deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $roleTitle = trim($_POST['role_title'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $detailBio = trim($_POST['detail_bio'] ?? '');
        $credentials = trim($_POST['credentials'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $returnTab = in_array($_POST['return_tab'] ?? '', ['founder', 'cofounder', 'faculty'], true) ? $_POST['return_tab'] : 'faculty';

        if ($name === '') {
            redirectWithMessage('teachers.php#' . $returnTab, 'Name is required.', 'error');
        }

        try {
            $photo = handleImageUpload('photo', 'teachers');
        } catch (RuntimeException $e) {
            redirectWithMessage('teachers.php#' . $returnTab, $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($photo) {
                $oldPhoto = $db->prepare('SELECT photo FROM teachers WHERE id = ?');
                $oldPhoto->execute([$id]);
                $oldPhoto = $oldPhoto->fetchColumn();

                $db->prepare('UPDATE teachers SET name=?, photo=?, role_title=?, qualification=?, subject=?, bio=?, detail_bio=?, credentials=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $photo, $roleTitle, $qualification, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive, $id]);

                if ($oldPhoto && $oldPhoto !== $photo) {
                    deleteUploadedImage($oldPhoto);
                }
            } else {
                $db->prepare('UPDATE teachers SET name=?, role_title=?, qualification=?, subject=?, bio=?, detail_bio=?, credentials=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$name, $roleTitle, $qualification, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive, $id]);
            }
            redirectWithMessage('teachers.php#' . $returnTab, 'Team member updated.');
        } else {
            $db->prepare('INSERT INTO teachers (name, photo, role_title, qualification, subject, bio, detail_bio, credentials, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)')
               ->execute([$name, $photo, $roleTitle, $qualification, $subject, $bio, $detailBio, $credentials, $sortOrder, $isActive]);
            redirectWithMessage('teachers.php#' . $returnTab, 'Team member added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM teachers WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$teachers = $db->query('SELECT * FROM teachers ORDER BY sort_order, id')->fetchAll();
['founder' => $founder, 'cofounder' => $cofounder] = getFounderAndCofounder($db);

/**
 * Shared add/edit form for Founder, Co-Founder and Faculty - all three
 * edit the same `teachers` row/columns, so one form covers all of them.
 * $returnTab controls which tab the page lands back on after saving.
 */
function renderTeacherForm(?array $row, string $heading, string $returnTab, bool $allowCancel = false): void
{
    ?>
    <form method="post" enctype="multipart/form-data" class="admin-form">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($row['id'] ?? 0) ?>">
      <input type="hidden" name="return_tab" value="<?= e($returnTab) ?>">

      <h2><?= e($heading) ?></h2>

      <label>Name
        <input type="text" name="name" value="<?= e($row['name'] ?? '') ?>" required>
      </label>
      <label>Role Title (e.g. "Founder and CEO", "Co-Founder, Director and Lead Instructor")
        <input type="text" name="role_title" value="<?= e($row['role_title'] ?? '') ?>">
      </label>
      <label>Qualification (shown on the About page Faculty card, e.g. "M.Phil. English Linguistics")
        <input type="text" name="qualification" value="<?= e($row['qualification'] ?? '') ?>">
      </label>
      <label>Subject / Short Role (shown on Home page card)
        <input type="text" name="subject" value="<?= e($row['subject'] ?? '') ?>">
      </label>
      <label>Short Bio (used on the Home page teaser card, and the About page Faculty card)
        <textarea name="bio" rows="2"><?= e($row['bio'] ?? '') ?></textarea>
      </label>
      <label>Biography (used on the About page profile section, separate paragraphs on blank lines)
        <textarea name="detail_bio" rows="6"><?= e($row['detail_bio'] ?? '') ?></textarea>
      </label>
      <label>Portfolio &amp; Qualifications (one per line, shown as a list on the About page profile section)
        <textarea name="credentials" rows="5"><?= e($row['credentials'] ?? '') ?></textarea>
      </label>
      <label>Sort Order
        <input type="number" name="sort_order" value="<?= (int)($row['sort_order'] ?? 0) ?>">
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= (!$row || $row['is_active']) ? 'checked' : '' ?>>
        Visible on site
      </label>
      <label>Photo
        <?php if (!empty($row['photo'])): ?>
          <div><img src="../<?= e($row['photo']) ?>" style="max-height:80px;"></div>
        <?php endif; ?>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
      </label>

      <button type="submit"><?= $row ? 'Update' : 'Add Team Member' ?></button>
      <?php if ($allowCancel): ?><a href="teachers.php#<?= e($returnTab) ?>" class="button-secondary">Cancel</a><?php endif; ?>
    </form>
    <?php
}
?>
<div class="admin-tabs-page">
<h1>Our Team</h1>
<p class="admin-page-intro">Founder and Co-Founder each get a dedicated tab for quick edits - which teacher fills each role is picked automatically from their Role Title ("CEO" for Founder, "Co-Founder" for Co-Founder), the same designation the About page uses. All team members, including the Founder and Co-Founder, are managed together on the Faculty tab (add, edit, delete, photo, visibility, sort order); the About page's Faculty grid excludes whoever is currently Founder/Co-Founder.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Our Team sections">
  <button type="button" class="admin-tab active" data-tab-group="team" data-tab-target="founder" role="tab" aria-selected="true">Founder</button>
  <button type="button" class="admin-tab" data-tab-group="team" data-tab-target="cofounder" role="tab" aria-selected="false">Co-Founder</button>
  <button type="button" class="admin-tab" data-tab-group="team" data-tab-target="faculty" role="tab" aria-selected="false">Faculty <span class="tab-count"><?= count($teachers) ?></span></button>
</nav>

<div class="admin-tabpanel" id="founder" data-tab-group="team" data-tab-id="founder">
  <?php if ($founder): ?>
    <?php renderTeacherForm($founder, 'Founder', 'founder'); ?>
  <?php else: ?>
    <p>No teacher is currently designated as Founder. Give someone a Role Title that includes "CEO" (e.g. "Founder and CEO") on the Faculty tab to assign this.</p>
  <?php endif; ?>
</div>

<div class="admin-tabpanel" id="cofounder" data-tab-group="team" data-tab-id="cofounder" hidden>
  <?php if ($cofounder): ?>
    <?php renderTeacherForm($cofounder, 'Co-Founder', 'cofounder'); ?>
  <?php else: ?>
    <p>No teacher is currently designated as Co-Founder. Give someone a Role Title that includes "Co-Founder" on the Faculty tab to assign this.</p>
  <?php endif; ?>
</div>

<div class="admin-tabpanel" id="faculty" data-tab-group="team" data-tab-id="faculty" hidden>
  <?php renderTeacherForm($editing, $editing ? 'Edit Team Member' : 'Add Team Member', 'faculty', (bool)$editing); ?>

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
            <a href="teachers.php?edit=<?= (int)$teacher['id'] ?>#faculty">Edit</a>
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
</div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
