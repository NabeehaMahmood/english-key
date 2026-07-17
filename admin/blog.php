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

    if ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT status, published_at FROM blog_posts WHERE id = ?');
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if (!$post) {
            redirectWithMessage('blog.php', 'Post not found.', 'error');
        }

        if ($post['status'] === 'published') {
            $db->prepare('UPDATE blog_posts SET status = ? WHERE id = ?')->execute(['draft', $id]);
            redirectWithMessage('blog.php', 'Post unpublished and moved to drafts.');
        }

        if ($post['published_at']) {
            $db->prepare('UPDATE blog_posts SET status = ? WHERE id = ?')->execute(['published', $id]);
        } else {
            $db->prepare('UPDATE blog_posts SET status = ?, published_at = NOW() WHERE id = ?')->execute(['published', $id]);
        }
        redirectWithMessage('blog.php', 'Post published.');
    }

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $backTo = 'blog.php' . ($id ? '?edit=' . $id : '');

        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $metaDescription = trim($_POST['meta_description'] ?? '');
        $primaryKeyword = trim($_POST['primary_keyword'] ?? '');
        $secondaryKeywords = trim($_POST['secondary_keywords'] ?? '');
        $targetAudience = trim($_POST['target_audience'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = sanitizeBlogHtml($_POST['content'] ?? '');
        $status = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';

        // URL slug: use the admin-provided slug if given (still normalized
        // and de-duplicated), otherwise derive it from the title, so every
        // post gets a clean, unique URL either way. slugify() only keeps
        // a-z0-9, so a non-Latin title (e.g. an Urdu-only post) with no
        // manual slug would otherwise collapse to '' — an unreachable post.
        $slugInput = trim($_POST['slug'] ?? '');
        $baseSlug = slugify($slugInput !== '' ? $slugInput : $title);
        if ($baseSlug === '') {
            $baseSlug = 'post-' . ($id > 0 ? $id : time());
        }
        $slug = uniqueBlogSlug($baseSlug, $id);

        if ($title === '') {
            redirectWithMessage($backTo, 'Title is required.', 'error');
        }

        if ($metaDescription === '') {
            $metaDescription = mb_strimwidth(trim(strip_tags($content)), 0, 160, '...');
        }

        if ($excerpt === '') {
            $excerpt = mb_strimwidth(trim(strip_tags($content)), 0, 200, '...');
        }

        // published_at is set once, the first time a post is published, and
        // is never overwritten by later edits — including a later edit that
        // unpublishes it back to a draft — so it keeps reflecting the
        // original publish date rather than the last-saved date.
        $existingPublishedAt = null;
        if ($id > 0) {
            $stmt = $db->prepare('SELECT published_at FROM blog_posts WHERE id = ?');
            $stmt->execute([$id]);
            $existingPublishedAt = $stmt->fetchColumn() ?: null;
        }
        $publishedAt = $status === 'published'
            ? ($existingPublishedAt ?: date('Y-m-d H:i:s'))
            : $existingPublishedAt;

        if ($id > 0) {
            $db->prepare('UPDATE blog_posts SET title=?, slug=?, category=?, meta_description=?, primary_keyword=?, secondary_keywords=?, target_audience=?, excerpt=?, content=?, status=?, published_at=? WHERE id=?')
               ->execute([$title, $slug, $category, $metaDescription, $primaryKeyword, $secondaryKeywords, $targetAudience, $excerpt, $content, $status, $publishedAt, $id]);
            redirectWithMessage('blog.php', 'Blog post updated.');
        } else {
            $db->prepare('INSERT INTO blog_posts (title, slug, category, meta_description, primary_keyword, secondary_keywords, target_audience, excerpt, content, status, published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$title, $slug, $category, $metaDescription, $primaryKeyword, $secondaryKeywords, $targetAudience, $excerpt, $content, $status, $publishedAt]);
            redirectWithMessage('blog.php', 'Blog post added.');
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$posts = $db->query('SELECT * FROM blog_posts ORDER BY COALESCE(published_at, created_at) DESC')->fetchAll();
?>
<h1>Blog</h1>

<div class="admin-tabs">
  <button type="button" class="admin-tab-btn active" data-tab="add">Add Post</button>
  <button type="button" class="admin-tab-btn" data-tab="list">All Posts (<?= count($posts) ?>)</button>
</div>

<div data-tab-panel="add">
<?php
// SEO fields are secondary to writing the post itself, so they're tucked into
// a collapsed-by-default panel — but auto-expand it when editing a post that
// already has any of them filled in, so nothing looks "lost".
$hasSeoData = !empty($editing['meta_description']) || !empty($editing['primary_keyword'])
    || !empty($editing['secondary_keywords']) || !empty($editing['target_audience']);
?>
<form method="post" class="admin-form admin-form-wide blog-editor">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">

  <div class="blog-editor-main">
    <h2><?= $editing ? 'Edit Post' : 'Add Post' ?></h2>

    <label>Title
      <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
    </label>
    <label>Content (type directly, or paste text from elsewhere)
      <textarea name="content" id="blog-content" rows="16"><?= e($editing['content'] ?? '') ?></textarea>
    </label>
  </div>

  <aside class="blog-editor-sidebar">
    <div class="blog-editor-panel">
      <div class="admin-form-actions">
        <button type="submit" name="status" value="draft" class="btn-draft">Save Draft</button>
        <button type="submit" name="status" value="published">Publish</button>
        <?php if ($editing): ?><a href="blog.php" class="button-secondary">Cancel</a><?php endif; ?>
      </div>
    </div>

    <div class="blog-editor-panel">
      <label>Category (e.g. Exam Technique, Grammar, Urdu, Board Updates)
        <input type="text" name="category" value="<?= e($editing['category'] ?? '') ?>">
      </label>
      <label>URL Slug (leave blank to generate from the title)
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="e.g. full-marks-paragraph-writing-fbise">
      </label>
      <p class="hint">Becomes the page address: /blog/your-slug-here.</p>
      <label>Excerpt (shown on the blog listing card &mdash; leave blank to auto-generate)
        <textarea name="excerpt" rows="3"><?= e($editing['excerpt'] ?? '') ?></textarea>
      </label>
    </div>

    <details class="blog-editor-panel blog-seo-details"<?= $hasSeoData ? ' open' : '' ?>>
      <summary>SEO details <span class="hint">(optional)</span></summary>
      <label>Meta Description (Google search snippet &mdash; leave blank to auto-generate)
        <textarea name="meta_description" rows="2" maxlength="300"><?= e($editing['meta_description'] ?? '') ?></textarea>
      </label>
      <p class="hint">Aim for about 150&ndash;160 characters.</p>
      <label>Primary Keyword
        <input type="text" name="primary_keyword" value="<?= e($editing['primary_keyword'] ?? '') ?>" placeholder="the one phrase this post should rank for">
      </label>
      <label>Secondary Keywords (comma-separated)
        <input type="text" name="secondary_keywords" value="<?= e($editing['secondary_keywords'] ?? '') ?>" placeholder="e.g. FBISE paragraph writing, SLO English marks">
      </label>
      <label>Target Audience
        <input type="text" name="target_audience" value="<?= e($editing['target_audience'] ?? '') ?>" placeholder="e.g. FBISE SSC-I students preparing for boards">
      </label>
    </details>
  </aside>
</form>
</div>

<div data-tab-panel="list" hidden>
<table class="admin-table">
  <thead>
    <tr><th>Title</th><th>Slug</th><th>Category</th><th>Status</th><th>Published</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($posts as $post): ?>
      <tr>
        <td><?= e($post['title']) ?></td>
        <td>/blog/<?= e($post['slug']) ?></td>
        <td><?= e($post['category']) ?></td>
        <td><span class="status-badge status-<?= e($post['status']) ?>"><?= $post['status'] === 'published' ? 'Published' : 'Draft' ?></span></td>
        <td><?= $post['published_at'] ? e(date('M j, Y', strtotime($post['published_at']))) : '&mdash;' ?></td>
        <td class="actions-cell">
          <a href="blog.php?edit=<?= (int)$post['id'] ?>">Edit</a>
          <form method="post" class="inline-form">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
            <button type="submit" class="link-button"><?= $post['status'] === 'published' ? 'Unpublish' : 'Publish' ?></button>
          </form>
          <form method="post" class="inline-form" onsubmit="return confirm('Delete this post?');">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
            <button type="submit" class="link-button link-button-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$posts): ?><tr><td colspan="6">No blog posts yet.</td></tr><?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#blog-content',
  // Grows with the content instead of scrolling internally in a fixed-size
  // box — the page itself is the only scroll region while writing, and
  // Save Draft/Publish stay reachable via the sticky sidebar regardless of
  // how long the post gets.
  min_height: 320,
  autoresize_bottom_margin: 24,
  menubar: false,
  branding: false,
  promotion: false,
  plugins: 'lists link image table code autoresize',
  toolbar: 'blocks | bold italic underline | bullist numlist | blockquote link image | alignleft aligncenter alignright | removeformat | code',
  block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Quote=blockquote',
  valid_elements: 'p,br,strong/b,em/i,u,h2,h3,h4,blockquote,ul,ol,li,a[href|target],img[src|alt|width|height],table,thead,tbody,tr,th,td,hr',
  content_css: '../assets/css/style.css',
  body_class: 'article-body',
  setup: function (editor) {
    // Google Docs/Word/LibreOffice don't tag quoted paragraphs as
    // <blockquote> — they just apply an indent (margin-left) and/or a left
    // border via inline styles. Those styles get stripped by valid_elements,
    // so the quote look is lost unless we convert them to real <blockquote>
    // tags before that happens. (TinyMCE 6 exposes this as the
    // PastePreProcess editor event, not an init-time paste_preprocess option.)
    editor.on('PastePreProcess', function (args) {
      var wrapper = document.createElement('div');
      wrapper.innerHTML = args.content;

      wrapper.querySelectorAll('p, div').forEach(function (el) {
        var style = el.getAttribute('style') || '';
        var margin = style.match(/(?:^|;)\s*margin-left\s*:\s*([\d.]+)(px|pt|in)/i);
        var hasBorder = /border-left\s*:\s*[^;]*[1-9]/i.test(style);
        var indentPx = 0;
        if (margin) {
          var val = parseFloat(margin[1]);
          indentPx = margin[2] === 'pt' ? val * 1.333 : margin[2] === 'in' ? val * 96 : val;
        }
        if (hasBorder || indentPx >= 24) {
          var bq = document.createElement('blockquote');
          bq.innerHTML = el.innerHTML;
          el.replaceWith(bq);
        }
      });

      args.content = wrapper.innerHTML;
    });
  }
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
