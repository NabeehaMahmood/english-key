<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([(int)$_POST['id']]);
        redirectWithMessage('blog.php', 'Blog post deleted.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $slug = slugify($title);

        if ($title === '') {
            redirectWithMessage('blog.php', 'Title is required.', 'error');
        }

        try {
            $image = handleImageUpload('image', 'news');
        } catch (RuntimeException $e) {
            redirectWithMessage('blog.php', $e->getMessage(), 'error');
        }

        if ($id > 0) {
            if ($image) {
                $db->prepare('UPDATE blog_posts SET title=?, slug=?, category=?, content=?, image=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $content, $image, $isActive, $id]);
            } else {
                $db->prepare('UPDATE blog_posts SET title=?, slug=?, category=?, content=?, is_active=? WHERE id=?')
                   ->execute([$title, $slug, $category, $content, $isActive, $id]);
            }
            redirectWithMessage('blog.php', 'Blog post updated.');
        } else {
            $db->prepare('INSERT INTO blog_posts (title, slug, category, content, image, is_active) VALUES (?,?,?,?,?,?)')
               ->execute([$title, $slug, $category, $content, $image, $isActive]);
            redirectWithMessage('blog.php', 'Blog post added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$posts = $db->query('SELECT * FROM blog_posts ORDER BY published_at DESC')->fetchAll();
?>
<h1>Blog</h1>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <h2><?= $editing ? 'Edit Post' : 'Add Post' ?></h2>

  <label>Title
    <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
  </label>
  <label>Category (e.g. Exam Technique, Grammar, Urdu, Board Updates)
    <input type="text" name="category" value="<?= e($editing['category'] ?? '') ?>">
  </label>
  <label>Content
    <textarea name="content" rows="6"><?= e($editing['content'] ?? '') ?></textarea>
  </label>
  <label class="checkbox-label">
    <input type="checkbox" name="is_active" <?= (!isset($editing) || $editing['is_active']) ? 'checked' : '' ?>>
    Published
  </label>
  <label>Image
    <?php if (!empty($editing['image'])): ?>
      <div><img src="../<?= e($editing['image']) ?>" style="max-height:80px;"></div>
    <?php endif; ?>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
  </label>

  <button type="submit"><?= $editing ? 'Update Post' : 'Add Post' ?></button>
  <?php if ($editing): ?><a href="blog.php" class="button-secondary">Cancel</a><?php endif; ?>
</form>

<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Category</th><th>Published</th><th>Live</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($posts as $post): ?>
      <tr>
        <td><?= e($post['title']) ?></td>
        <td><?= e($post['category']) ?></td>
        <td><?= e(date('M j, Y', strtotime($post['published_at']))) ?></td>
        <td><?= $post['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="blog.php?edit=<?= (int)$post['id'] ?>">Edit</a>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this post?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
            <button type="submit" class="link-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
